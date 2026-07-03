<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollPayment;
use App\Models\DispatchTrip;
use App\Models\PayrollPersonLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\WatsonTrip;
use App\Models\WatsonPayrollPayment;
use Illuminate\Support\Facades\Mail;
use App\Mail\PayrollReadyMail;
use App\Models\Driver;
use Carbon\Carbon;

class WatsonPayrollPaymentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
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
            // ✅ prevent duplicate
            $exists = WatsonPayrollPayment::where([
                'driver_id' => $data['person_id'],
                'week_start' => $data['week_start'],
                'week_end' => $data['week_end'],
            ])->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'person_id' => 'Already paid for this week.',
                ]);
            }

            $advance = (float) ($data['balance_advance'] ?? 0);
            $deduct = (float) ($data['advance_deducted'] ?? 0);

            if ($deduct > $advance) {
                throw ValidationException::withMessages([
                    'advance_deducted' => 'Invalid deduction.',
                ]);
            }

            $remaining = max($advance - $deduct, 0);
            $final = max(0, $data['amount'] - $deduct);

            // ✅ create payment
            $payment = WatsonPayrollPayment::create([
                'driver_id' => $data['person_id'],
                'week_start' => $data['week_start'],
                'week_end' => $data['week_end'],
                'total_trips' => $data['total_trips'],
                'amount' => $data['amount'],
                'advance_amount' => $advance,
                'advance_deducted' => $deduct,
                'balance_advance' => $remaining,
                'final_amount' => $final,
                'payment_mode' => $data['payment_mode'],
                'transaction_id' => $data['transaction_id'],
                'paid_at' => now(),
            ]);

            // ✅ link trips
            $trips = WatsonTrip::where('driver_id', $data['person_id'])
                ->where('status', 'Completed')
                ->whereBetween('dispatch_date', [$data['week_start'], $data['week_end']])
                ->get();

            foreach ($trips as $trip) {
                DB::table('watson_payroll_payment_trips')->insert([
                    'watson_payroll_payment_id' => $payment->id,
                    'watson_trip_id' => $trip->id,
                ]);

                // optional: mark paid
                $trip->update(['payment_status' => 'Paid']);
            }
        });

        $company = 'Watsons';

        $person = Driver::find($data['person_id']);

        if ($person && $person->email) {
            $paymentTrips = WatsonTrip::with(['destination'])
                ->where('driver_id', $person->id)
                ->where('status', 'Completed')
                ->whereBetween('dispatch_date', [$data['week_start'], $data['week_end']])
                ->get();

            $rows = collect();

            foreach ($paymentTrips as $trip) {
                // Unlike Flash's flat rate, Watson rates vary per destination/truck type,
                // so pull the real rate and multiply by trip_number like the payroll index() does.
                $rate = $trip->destination->rate ?? 0;
                $amount = $rate * ($trip->trip_number ?? 1);
                $allowance = 0;

                $rows->push([
                    'date' => Carbon::parse($trip->dispatch_date)->format('Y-m-d'),
                    'location' => $trip->destination->destination_name ?? '-',
                    'amount' => $amount,
                    'allowance' => $allowance,
                    'incentive' => 0,
                    'total_salary' => $amount + $allowance,
                ]);
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
                    (float) ($data['balance_advance'] ?? 0),
                    (float) ($data['advance_deducted'] ?? 0),
                    (float) max(($data['balance_advance'] ?? 0) - ($data['advance_deducted'] ?? 0), 0),
                    (float) max($amount - ($data['advance_deducted'] ?? 0), 0),
                ),
            );
        }

        return back()->with('success', 'Watson payroll paid!');
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
