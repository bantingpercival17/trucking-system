<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WatsonTrip;
use App\Models\Driver;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PayrollReadyMail;

class WatsonPayrollController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from) : now()->startOfWeek();
        $to = $request->to ? Carbon::parse($request->to) : now()->endOfWeek();

        $trips = WatsonTrip::with(['driver', 'destination', 'truck'])
            ->where('status', 'Completed')
            ->whereNotIn('id', function ($q) {
                $q->select('watson_trip_id')->from('watson_payroll_payment_trips');
            })
            ->whereBetween('dispatch_date', [$from, $to])
            ->orderBy('dispatch_date')
            ->get();

        $driversPayroll = $trips
            ->groupBy('driver_id')
            ->map(function ($group) {
                $driver = optional($group->first()->driver);

                $rows = $group->map(function ($t) {
                    // Check truck status and get rate
                    $rate = 0;
                    if ($t->truck) {
                        if ($t->truck->truck_type == 'L300') {
                            $rate = 700;
                            if (!$t->helper) {
                                $rate = 1200;
                            }
                        } else {
                            $rate = $t->destination->rate * .11;
                        }
                    }
                    return [
                        'id' => $t->id,
                        'date' => Carbon::parse($t->dispatch_date)->format('Y-m-d'),
                        'destination' => $t->destination->destination_name . ' (' . ($t->truck->truck_type ?? '-') . ')',
                        'trip_number' => $t->trip_number,
                        'rate' => $rate,
                        'amount' => $rate * $t->trip_number,
                        'total_salary' => $rate,
                    ];
                });

                // ✅ DITO MO ILALAGAY
                $status = $group->every(fn($t) => $t->payment_status === 'Paid') ? 'PAID' : 'UNPAID';

                return [
                    'person_id' => $driver->id,
                    'name' => $driver->name ?? 'N/A',
                    'plate' => optional($group->first()->truck)->plate_number ?? 'N/A',
                    'rows' => $rows,
                    'payroll_total' => $rows->sum('total_salary'),
                    'status' => $status, // ✅ gamitin mo na
                ];
            })
            ->values();

        return view('watson.payroll.index', [
            'from' => $from,
            'to' => $to,
            'weekStart' => $from,
            'weekEnd' => $to,
            'driversPayroll' => $driversPayroll,
            'driversTotal' => $driversPayroll->sum('payroll_total'),
            'grandTotal' => $driversPayroll->sum('payroll_total'),
        ]);
    }

    public function dashboard(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from) : now()->startOfWeek();

        $to = $request->to ? Carbon::parse($request->to) : now()->endOfWeek();

        // ✅ ONLY COMPLETED TRIPS
        $trips = WatsonTrip::with(['driver', 'destination'])
            ->where('status', 'Completed')
            ->whereBetween('dispatch_date', [$from, $to])
            ->get();

        // =========================
        // DRIVER PAYROLL ONLY
        // =========================
        $driversPayroll = $trips
            ->groupBy('truck_id')
            ->map(function ($group) {
                $truck = optional($group->first()->truck); // ✅ FIX

                $rows = $group->map(function ($t) {
                    return [
                        'date' => $t->dispatch_date,
                        'destination' => $t->destination->destination_name ?? '-',
                        'driver' => optional($t->driver)->name ?? 'N/A', // ✅ per row
                        'amount' => $t->destination->rate ?? 0,
                    ];
                });

                return [
                    'plate' => $truck->plate_number ?? 'N/A', // ✅ PER TRUCK
                    'rows' => $rows,
                    'total' => $rows->sum('amount'),
                ];
            })
            ->values();

        // =========================
        // QUEUE (NO ROLE)
        // =========================
        $queue = $trips
            ->where('status', 'Completed')
            ->groupBy('driver_id')
            ->map(function ($group) {
                $driver = optional($group->first()->driver);

                return [
                    'name' => $driver->name ?? 'N/A', // ✅ DRIVER NAME NA
                    'trips' => $group->count(),
                    'amount' => $group->sum(function ($t) {
                        $rate = 0;
                        if ($t->truck) {
                            if ($t->truck->truck_type == 'L300') {
                                $rate = 700;
                                if (!$t->helper) {
                                    $rate = 1200;
                                }
                            } else {
                                $rate = $t->destination->rate * .11;
                            }
                        }
                        return $rate ?? 0;
                    }),
                    'status' => $group->every(fn($t) => $t->payment_status === 'Paid') ? 'PAID' : 'UNPAID', // ✅ STATUS PER DRIVER
                ];
            })
            ->values();

        return view('watson.payroll.dashboard', [
            'driversPayroll' => $driversPayroll,
            'queue' => $queue,
            'total' => $queue->sum('amount'),
            'from' => $from,
            'to' => $to,
            'drivers' => $queue->count(), // mas tama ito
            'pendingTrips' => $trips->count(),
        ]);
    }

    public function history(Request $request)
    {
        $from = $request->query('from', now()->subWeeks(4)->startOfWeek());
        $to = $request->query('to', now()->endOfWeek());

        $payments = \App\Models\WatsonPayrollPayment::whereBetween('week_start', [$from, $to])
            ->orderByDesc('week_start')
            ->get();

        $weeks = $payments
            ->groupBy(fn($p) => $p->week_start . '|' . $p->week_end)
            ->map(function ($group) {
                $weekStart = $group->first()->week_start;
                $weekEnd = $group->first()->week_end;

                $driversPayroll = $group->map(function ($p) {
                    // ✅ kunin trips via pivot
                    $tripIds = \DB::table('watson_payroll_payment_trips')->where('watson_payroll_payment_id', $p->id)->pluck('watson_trip_id');

                    $trips = \App\Models\WatsonTrip::with('destination')->whereIn('id', $tripIds)->get();

                    $rows = $trips->map(function ($t) {
                        $rate = $t->destination->rate ?? 0;

                        return [
                            'date' => \Carbon\Carbon::parse($t->dispatch_date)->format('Y-m-d'),
                            'location' => $t->destination->destination_name ?? '-',
                            'rate' => $rate,
                            'amount' => $rate,
                            'total_salary' => $rate,
                        ];
                    });

                    return [
                        'person_id' => $p->driver_id,
                        'name' => optional(\App\Models\Driver::find($p->driver_id))->name ?? 'Driver',
                        'rows' => $rows,
                        'payment_id' => $p->id,

                        'payroll_total' => $p->amount,
                        'net_pay' => $p->final_amount,

                        // deductions (optional)
                        'advance_amount' => $p->advance_amount,
                        'advance' => $p->advance_deducted,
                        'balance_advance_remaining' => $p->balance_advance,

                        'status' => 'PAID',
                    ];
                });

                return [
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'driversPayroll' => $driversPayroll,
                    'driversTotal' => $driversPayroll->sum('net_pay'),
                    'helpersPayroll' => [], // ❌ wala tayo helpers
                    'helpersTotal' => 0,
                    'grandTotal' => $driversPayroll->sum('net_pay'),
                ];
            })
            ->values();

        return view('watson.payroll.history', compact('weeks', 'from', 'to'));
    }

    public function downloadPDF($id)
    {
        $payment = \App\Models\WatsonPayrollPayment::findOrFail($id);

        $driver = \App\Models\Driver::find($payment->driver_id);

        $tripIds = \DB::table('watson_payroll_payment_trips')->where('watson_payroll_payment_id', $id)->pluck('watson_trip_id');

        $trips = \App\Models\WatsonTrip::with('destination')->whereIn('id', $tripIds)->get();

        $rows = $trips->map(function ($t) {
            return [
                'date' => \Carbon\Carbon::parse($t->dispatch_date)->format('Y-m-d'),
                'location' => $t->destination->destination_name ?? '-',
                'rate' => $t->destination->rate ?? 0,
                'amount' => $t->destination->rate ?? 0,
                'total_salary' => $t->destination->rate ?? 0,
            ];
        });

        $pdf = Pdf::loadView('watson.pdf', [
            'rows' => $rows,
            'payment' => $payment,
            'driver' => $driver,
        ]);

        return $pdf->stream('watson-payroll.pdf');
    }

    public function destroy($id)
    {
        \DB::transaction(function () use ($id) {
            $payment = \App\Models\WatsonPayrollPayment::findOrFail($id);

            // kunin trips
            $tripIds = \DB::table('watson_payroll_payment_trips')->where('watson_payroll_payment_id', $id)->pluck('watson_trip_id');

            // delete pivot
            \DB::table('watson_payroll_payment_trips')->where('watson_payroll_payment_id', $id)->delete();

            // ibalik sa unpaid
            \App\Models\WatsonTrip::whereIn('id', $tripIds)->update(['payment_status' => 'Unpaid']);

            // delete payment
            $payment->delete();
        });

        return back()->with('success', 'Payment deleted!');
    }

    public function billing(Request $request)
    {
        $checkDate = $request->get('check_date');

        $trips = WatsonTrip::with(['destination', 'truck', 'driver'])
            ->where('status', 'Completed')
            ->where(function ($q) {
                $q->whereNull('billing_status')->orWhereIn('billing_status', ['Unbilled', 'Pending']);
            });

        if ($checkDate) {
            $trips->whereDate('check_release_date', $checkDate);
        }

        $trips = $trips->orderByDesc('dispatch_date')->paginate(10)->withQueryString();

        return view('watson.payroll.billing', compact('trips'));
    }

    public function complete($id)
    {
        $trip = WatsonTrip::findOrFail($id);

        $trip->billing_status = 'Billed';
        $trip->save();

        return back()->with('success', 'Trip marked as billed.');
    }

    public function updateBilling(Request $request, $id)
    {
        $trip = WatsonTrip::findOrFail($id);

        $trip->check_release_date = $request->check_release_date;
        $trip->bank_name = $request->bank_name;
        $trip->check_number = $request->check_number;

        if ($request->filled('check_release_date') && $request->filled('bank_name') && $request->filled('check_number')) {
            $trip->billing_status = 'Billed';
        } elseif ($request->filled('check_release_date') || $request->filled('bank_name') || $request->filled('check_number')) {
            $trip->billing_status = 'Pending';
        }

        $trip->save();

        return back()->with('success', 'Billing updated.');
    }

    public function finalize(Request $request)
    {
        // sample logic
        return back()->with('success', 'Payroll finalized!');
    }
}
