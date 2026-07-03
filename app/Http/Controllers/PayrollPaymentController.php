<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollPayment;
use App\Models\DispatchTrip;
use App\Models\PayrollPersonLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\PayrollReadyMail;
use App\Models\Driver;
use App\Models\Helper;
use Carbon\Carbon;
use App\Http\Controllers\FlashPayrollController;
use App\Http\Controllers\PayrollController;
use App\Models\AllowanceRange;

class PayrollPaymentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'person_type' => 'required|in:driver,helper',
            'person_id' => 'required|integer',
            'week_start' => 'required|date',
            'week_end' => 'required|date',
            'total_trips' => 'required|integer',
            'amount' => 'required|numeric',
            'payment_mode' => 'required|string',
            'transaction_id' => 'nullable|string',
            'balance_advance' => 'nullable|numeric|min:0',
            'advance_deducted' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $advanceDeducted = (float) ($data['advance_deducted'] ?? 0);

            /*
            |--------------------------------------------------------------------------
            | PREVENT DUPLICATE PAYROLL
            |--------------------------------------------------------------------------
            */
            $existing = PayrollPayment::where([
                'person_type' => $data['person_type'],
                'person_id' => $data['person_id'],
                'week_start' => $data['week_start'],
                'week_end' => $data['week_end'],
            ])->exists();

            if ($existing) {
                throw ValidationException::withMessages([
                    'person_id' => 'Payroll already paid for this week.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | FIND / CREATE LEDGER
            |--------------------------------------------------------------------------
            */
            $ledger = PayrollPersonLedger::firstOrCreate(
                [
                    'person_type' => $data['person_type'],
                    'person_id' => $data['person_id'],
                    'week_start' => $data['week_start'],
                    'week_end' => $data['week_end'],
                ],
                [
                    'paid_amount' => 0,
                    'advance_amount' => 0,
                    'notes' => null,
                    'updated_by' => Auth::id(),
                ],
            );

            /*
            |--------------------------------------------------------------------------
            | VALIDATE DEDUCTION
            |--------------------------------------------------------------------------
            */
            $advanceBalance = (float) ($data['balance_advance'] ?? 0);
            $advanceDeducted = (float) ($data['advance_deducted'] ?? 0);

            if ($advanceDeducted > $advanceBalance) {
                throw ValidationException::withMessages([
                    'advance_deducted' => 'Advance deduction cannot exceed advance balance.',
                ]);
            }

            $remainingAdvance = max($advanceBalance - $advanceDeducted, 0);

            /*
            |--------------------------------------------------------------------------
            | COMPUTE FINAL PAY
            |--------------------------------------------------------------------------
            */
            $finalAmount = max(0, $data['amount'] - $advanceDeducted);

            /*
            |--------------------------------------------------------------------------
            | UPDATE LEDGER
            |--------------------------------------------------------------------------
            */
            $ledger->advance_amount = $remainingAdvance;
            $ledger->paid_amount += $finalAmount;
            $ledger->updated_by = Auth::id();
            $ledger->save();

            /*
            |--------------------------------------------------------------------------
            | CREATE PAYMENT RECORD
            |--------------------------------------------------------------------------
            */
            $payment = PayrollPayment::create([
                'person_type' => $data['person_type'],
                'person_id' => $data['person_id'],
                'week_start' => $data['week_start'],
                'week_end' => $data['week_end'],
                'total_trips' => $data['total_trips'],
                'amount' => $data['amount'],
                'advance_amount' => $advanceBalance,
                'advance_deducted' => $advanceDeducted,
                'balance_advance' => $remainingAdvance,
                'final_amount' => $finalAmount,
                'payment_mode' => $data['payment_mode'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'released_by' => Auth::id(),
                'paid_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | LINK TRIPS TO PAYMENT
            |--------------------------------------------------------------------------
            */
            $trips = DispatchTrip::with(['helpers'])
                ->where('status', 'Completed')
                ->whereBetween('dispatch_date', [$data['week_start'], $data['week_end']])
                ->get();

            foreach ($trips as $trip) {
                if ($data['person_type'] === 'driver' && $trip->driver_id == $data['person_id']) {
                    DB::table('payroll_payment_trips')->insert([
                        'payroll_payment_id' => $payment->id,
                        'dispatch_trip_id' => $trip->id,
                    ]);
                }

                if ($data['person_type'] === 'helper') {
                    foreach ($trip->helpers as $h) {
                        if ($h->id == $data['person_id']) {
                            DB::table('payroll_payment_trips')->insert([
                                'payroll_payment_id' => $payment->id,
                                'dispatch_trip_id' => $trip->id,
                            ]);
                        }
                    }
                }

                $this->updateTripPaymentStatus($trip);
            }
        });

        $company = 'Monde de Parfum Logistics Services';

        if ($data['person_type'] === 'driver') {
            $person = Driver::find($data['person_id']);
        } else {
            $person = Helper::find($data['person_id']);
        }

        if ($person && $person->email) {
            $paymentTrips = DispatchTrip::with(['destination', 'helpers'])
                ->where('status', 'Completed')
                ->whereBetween('dispatch_date', [$data['week_start'], $data['week_end']])
                ->get();

            $rows = collect();

            foreach ($paymentTrips as $trip) {
                // DRIVER
                if ($data['person_type'] === 'driver' && $trip->driver_id == $person->id) {
                    $rate = (float) ($trip->rate_snapshot ?? 0);
                    $helperCount = $trip->helpers->count();
                    $driverRatePercent = $helperCount >= 2 ? 0.08 : 0.11;

                    $amount = $trip->manual_amount ?? round($rate * $driverRatePercent, 2);

                    $computedAllowance = 0;

                    if ($rate > 0) {
                        $range = AllowanceRange::where('rate_from', '<=', $rate)->where('rate_to', '>=', $rate)->first();

                        $computedAllowance = $range ? (float) $range->allowance : 0;
                    }

                    $allowance = $trip->manual_allowance ?? $computedAllowance;

                    $rows->push([
                        'date' => Carbon::parse($trip->dispatch_date)->format('Y-m-d'),
                        'location' => $trip->destination->store_name ?? '-',
                        'amount' => $amount,
                        'allowance' => $allowance,
                        'incentive' => 0,
                        'total_salary' => $amount + $allowance,
                    ]);
                }

                // HELPER
                if ($data['person_type'] === 'helper') {
                    foreach ($trip->helpers as $helper) {
                        if ($helper->id == $person->id) {
                            $rate = (float) ($trip->rate_snapshot ?? 0);
                            $helperCount = $trip->helpers->count();

                            $helperPool = $trip->manual_amount ?? $rate * 0.08;
                            $helperBase = $helperCount > 0 ? $helperPool / $helperCount : 0;

                            // SAME LOGIC AS PDF
                            $computedAllowance = 0;

                            if ($rate > 0) {
                                $range = AllowanceRange::where('rate_from', '<=', $rate)->where('rate_to', '>=', $rate)->first();

                                $computedAllowance = $range ? (float) $range->allowance : 0;
                            }

                            // Manual allowance = full trip allowance pool
                            $allowancePool = $trip->manual_allowance ?? $computedAllowance;

                            // Split among helpers
                            $splitAllowance = $helperCount > 0 ? $allowancePool / $helperCount : 0;
                            $rows->push([
                                'date' => Carbon::parse($trip->dispatch_date)->format('Y-m-d'),
                                'location' => $trip->destination->store_name ?? '-',
                                'amount' => round($helperBase, 2),
                                'allowance' => round($splitAllowance, 2),
                                'incentive' => 0,
                                'total_salary' => round($helperBase + $splitAllowance, 2),
                            ]);
                        }
                    }
                }
            }

            $amount = $rows->sum('total_salary');

            Mail::to($person->email)->send(
                new PayrollReadyMail(
                    $person,
                    $rows,
                    $amount,
                    $data['week_start'],
                    $data['week_end'],
                    $company,

                    // DEDUCTIONS
                    (float) ($data['balance_advance'] ?? 0), // Last Balance
                    (float) ($data['advance_deducted'] ?? 0), // Deducted
                    (float) max(($data['balance_advance'] ?? 0) - ($data['advance_deducted'] ?? 0), 0), // Remaining
                    (float) max($amount - ($data['advance_deducted'] ?? 0), 0), // Net Pay
                ),
            );
        }

        return back()->with('success', 'Payment recorded successfully.');
    }

    private function updateTripPaymentStatus($trip)
    {
        $driverPaid = DB::table('payroll_payments')->join('payroll_payment_trips', 'payroll_payments.id', '=', 'payroll_payment_trips.payroll_payment_id')->where('dispatch_trip_id', $trip->id)->where('person_type', 'driver')->exists();

        $helperIds = $trip->helpers->pluck('id');

        $helpersPaidCount = DB::table('payroll_payments')->join('payroll_payment_trips', 'payroll_payments.id', '=', 'payroll_payment_trips.payroll_payment_id')->where('dispatch_trip_id', $trip->id)->where('person_type', 'helper')->whereIn('person_id', $helperIds)->distinct()->count('person_id');

        $helpersTotal = $helperIds->count();

        $trip->update([
            'payment_status' => $driverPaid && $helpersPaidCount === $helpersTotal ? 'Paid' : 'Unpaid',
        ]);
    }
}
