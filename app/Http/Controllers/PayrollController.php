<?php

namespace App\Http\Controllers;

use App\Models\DispatchTrip;
use App\Models\PayrollPersonLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PayrollPayment;
use App\Models\AllowanceRange;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function update(Request $request)
    {
        $personType = $request->person_type;

        foreach ($request->rows as $tripId => $rowData) {
            $trip = DispatchTrip::with('helpers')->findOrFail($tripId);

            $trip->manual_amount = $rowData['amount'];

            if ($personType === 'helper') {
                $helperCount = max($trip->helpers->count(), 1);

                $trip->manual_allowance = $rowData['allowance'] * $helperCount;
            } else {
                $trip->manual_allowance = $rowData['allowance'];
            }

            $trip->save();
        }

        return redirect()
            ->route('owner.payroll.index', [
                'from' => $request->from,
                'to' => $request->to,
                'active_tab' => $request->active_tab,
            ])
            ->with('success', 'Payroll updated successfully.');
    }

    public function edit(Request $request)
    {
        $weekStart = $request->week_start;
        $weekEnd = $request->week_end;

        if ($request->person_type === 'driver') {
            $rows = DispatchTrip::with('destination')
                ->where('driver_id', $request->person_id)
                ->whereBetween('dispatch_date', [$weekStart, $weekEnd])
                ->get();
        } else {
            $rows = DispatchTrip::with(['destination', 'helpers'])
                ->whereBetween('dispatch_date', [$weekStart, $weekEnd])
                ->get()
                ->filter(fn($trip) => $trip->helpers->contains('id', $request->person_id));
        }

        return view('owner.payroll.edit', [
            'rows' => $rows,
            'personType' => $request->person_type,
            'personId' => $request->person_id,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
        ]);
    }

    public function downloadPDF($type, $id)
    {
        $weekStart = request('week_start');
        $weekEnd = request('week_end');

        if ($type === 'driver') {
            $data = $this->getDriverPayroll($id, $weekStart, $weekEnd);
        } else {
            $data = $this->getHelperPayroll($id, $weekStart, $weekEnd);
        }

        $data['person_type'] = $type;

        // ✅ GET PAYMENT RECORD
        $payment = PayrollPayment::where('person_type', $type)->where('person_id', $id)->whereDate('week_start', $weekStart)->whereDate('week_end', $weekEnd)->first();

        // ✅ SAFE DEDUCTIONS
        $deductions = [
            'last_balance' => $payment ? $payment->advance_amount ?? 0 : 0,
            'advance_deducted' => $payment ? $payment->advance_deducted ?? 0 : 0,
            'remaining_balance' => $payment ? $payment->balance_advance ?? 0 : 0,
        ];

        $netPay = $payment ? $payment->final_amount ?? 0 : $data['payroll_total'];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.myfile', [
            'p' => $data,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'deductions' => $deductions,
            'netPay' => $netPay,
        ]);

        return $pdf->stream($data['name'] . '-payslip.pdf');
    }

    public function getLastOdometer($plate)
    {
        $last = DB::table('expenses')->where('plate_number', $plate)->where('type', 'fuel')->whereNotNull('odometer')->orderByDesc('date')->orderByDesc('id')->first();

        return response()->json([
            'odometer' => $last?->odometer,
        ]);
    }

    public function updateExpense(Request $request)
    {
        // ======================
        // VALIDATION
        // ======================
        $data = $request->validate([
            'id' => 'required|integer',
            'liters' => 'required|numeric|min:0.01',
            'debit' => 'required|numeric',
            'start_odometer' => ['nullable', 'integer', 'min:0'],
            'odometer' => ['nullable', 'integer', 'min:0'],
            'receipt_surrendered' => 'nullable|in:YES,NO',
            'remarks' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        // ======================
        // VALIDATE ODOMETER LOGIC
        // ======================
        if (!is_null($data['start_odometer']) && !is_null($data['odometer']) && $data['odometer'] < $data['start_odometer']) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'End odometer must be greater than or equal to start odometer.',
                ],
                422,
            );
        }

        // ======================
        // COMPUTE DISTANCE + KM/L
        // ======================
        $distance = null;
        $kmPerLiter = null;

        if (!is_null($data['start_odometer']) && !is_null($data['odometer'])) {
            $distance = $data['odometer'] - $data['start_odometer'];

            if (!is_null($data['liters']) && $data['liters'] > 0) {
                $kmPerLiter = round($distance / $data['liters'], 2);
            }
        }

        // ======================
        // BUILD UPDATE DATA
        // ======================
        $updateData = [
            'liters' => $data['liters'],
            'debit' => $data['debit'],

            'start_odometer' => $data['start_odometer'],
            'odometer' => $data['odometer'],

            'distance' => $distance,
            'km_per_liter' => $kmPerLiter,

            'receipt_surrendered' => $data['receipt_surrendered'],
            'remarks' => $data['remarks'],

            'updated_at' => now(),
        ];

        // ======================
        // HANDLE NEW RECEIPT IMAGE
        // ======================
        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')->store('receipts', 'public');
            $updateData['receipt_image'] = $path;
        }

        // ======================
        // UPDATE RECORD
        // ======================
        DB::table('expenses')->where('id', $data['id'])->update($updateData);

        return response()->json([
            'success' => true,
        ]);
    }

    public function updateLoadExpense(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'plate_number' => 'required|string',
            'date' => 'required|date',
            'time' => 'nullable',
            'debit' => 'required|numeric',
            'receipt_surrendered' => 'nullable|in:YES,NO',
            'remarks' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'plate_number' => $data['plate_number'],
            'date' => $data['date'],
            'time' => $data['time'],
            'debit' => $data['debit'],
            'receipt_surrendered' => $data['receipt_surrendered'],
            'remarks' => $data['remarks'],
            'updated_at' => now(),
        ];

        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')->store('receipts', 'public');
            $updateData['receipt_image'] = $path;
        }

        DB::table('expenses')->where('id', $data['id'])->update($updateData);

        return response()->json([
            'success' => true,
        ]);
    }

    public function complete($id)
    {
        $trip = DispatchTrip::findOrFail($id);

        $trip->billing_status = 'Billed';

        $trip->save();

        return back()->with('success', 'Trip marked as billed.');
    }

    public function updateBilling(Request $request, $id)
    {
        $trip = DispatchTrip::findOrFail($id);

        $trip->check_release_date = $request->check_release_date;
        $trip->bank_name = $request->bank_name;
        $trip->check_number = $request->check_number;

        /* If all billing details are provided → Billed, otherwise Pending */
        if ($request->filled('check_release_date') && $request->filled('bank_name') && $request->filled('check_number')) {
            $trip->billing_status = 'Billed';
        } elseif ($request->filled('check_release_date') || $request->filled('bank_name') || $request->filled('check_number')) {
            $trip->billing_status = 'Pending';
        }

        $trip->save();

        return back()->with('success', 'Billing details updated.');
    }

    public function billing(Request $request)
    {
        $q = $request->get('q');

        $sort = $request->get('sort', 'dispatch_date');
        $dir = $request->get('dir', 'desc');

        $allowedSorts = ['trip_ticket_no', 'dispatch_date', 'status', 'dispatched_at', 'id'];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'dispatch_date';
        }

        if (!in_array($dir, ['asc', 'desc'], true)) {
            $dir = 'desc';
        }

        $tripsQuery = DispatchTrip::with(['destination', 'truck', 'driver', 'helpers'])
            ->where('status', 'Completed')
            ->where(function ($q) {
                $q->whereNull('billing_status')->orWhereIn('billing_status', ['Unbilled', 'Pending']);
            });

        $checkDate = $request->get('check_date');

        if ($checkDate) {
            $tripsQuery->whereDate('check_release_date', $checkDate);
        }

        $totalBillingAmount = (clone $tripsQuery)->sum('rate_snapshot');

        // SEARCH
        if ($q) {
            $tripsQuery->where(function ($w) use ($q) {
                $w->where('trip_ticket_no', 'like', "%{$q}%")

                    ->orWhereHas('destination', function ($d) use ($q) {
                        $d->where('store_name', 'like', "%{$q}%")->orWhere('store_code', 'like', "%{$q}%");
                    })

                    ->orWhereHas('truck', function ($t) use ($q) {
                        $t->where('plate_number', 'like', "%{$q}%");
                    })

                    ->orWhereHas('driver', function ($dr) use ($q) {
                        $dr->where('name', 'like', "%{$q}%");
                    })

                    ->orWhereHas('helpers', function ($h) use ($q) {
                        $h->where('name', 'like', "%{$q}%");
                    });
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $trips = $tripsQuery->orderBy($sort, $dir)->paginate($perPage)->withQueryString();

        return view('owner.payroll.billing', compact('trips', 'q', 'sort', 'dir', 'totalBillingAmount'));
    }

    public function updateAllowances(Request $request)
    {
        $rateFrom = $request->input('rate_from', []);
        $rateTo = $request->input('rate_to', []);
        $allowance = $request->input('allowance', []);

        // clear existing
        AllowanceRange::truncate();

        foreach ($rateFrom as $i => $from) {
            if (!$from || !isset($rateTo[$i]) || !isset($allowance[$i])) {
                continue;
            }

            AllowanceRange::create([
                'rate_from' => $from,
                'rate_to' => $rateTo[$i],
                'allowance' => $allowance[$i],
            ]);
        }

        return back()->with('success', 'Allowance rates updated.');
    }

    public function dashboard(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from)->toDateString() : now()->startOfWeek(Carbon::MONDAY)->toDateString();

        $to = $request->to ? Carbon::parse($request->to)->toDateString() : now()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $trips = DispatchTrip::with(['destination', 'truck', 'driver', 'helpers'])
            ->where('status', 'Completed')
            ->whereBetween('dispatch_date', [$from, $to])
            ->get();

        $queue = collect();

        // =========================
        // 🚛 DRIVER QUEUE + PAYROLL
        // =========================

        $platePayroll = $trips->groupBy(function ($t) {
            return optional($t->truck)->plate_number ?? 'NO PLATE';
        });

        $driversPayroll = $platePayroll
            ->map(function ($group, $plate) {
                $rows = $group->map(function ($t) {
                    $rate = (float) ($t->rate_snapshot ?? 0);

                    $helperCount = $t->helpers->count();

                    $driverRatePercent = 0.11;

                    $amount = $t->manual_amount ?? round($rate * $driverRatePercent, 2);
                    $allowance = $t->manual_allowance ?? $this->allowanceFromRate($rate);

                    return [
                        'date' => Carbon::parse($t->dispatch_date)->format('Y-m-d'),
                        'location' => $t->destination->store_name ?? '-',
                        'driver' => optional($t->driver)->name ?? 'N/A',
                        'rate' => $rate,
                        'amount' => $amount,
                        'allowance' => $allowance,
                        'total_salary' => round($amount + $allowance, 2),
                    ];
                });

                return [
                    'plate' => $plate,

                    // ✅ ADD THIS LINE (FIX)
                    'truck_type' => optional($group->first()->truck)->truck_type ?? 'N/A',

                    'rows' => $rows,
                    'payroll_total' => round($rows->sum('total_salary'), 2),
                ];
            })
            ->values();

        // =========================
        // 👷 HELPER PAYROLL
        // =========================

        $helpersByPlate = $trips->groupBy(function ($t) {
            return optional($t->truck)->plate_number ?? 'NO PLATE';
        });

        $helpersPayroll = $helpersByPlate
            ->map(function ($group, $plate) {
                $rows = collect();

                foreach ($group as $t) {
                    foreach ($t->helpers as $h) {
                        $rate = (float) ($t->rate_snapshot ?? 0);
                        $count = max($t->helpers->count(), 1);

                        $amount = $t->manual_amount ?? round(($rate * 0.08) / $count, 2);

                        if ($t->manual_allowance !== null) {
                            $allowance = round($t->manual_allowance / $count, 2);
                        } else {
                            $allowance = round($this->allowanceFromRate($rate) / $count, 2);
                        }

                        $rows->push([
                            'helper' => $h->name,
                            'date' => Carbon::parse($t->dispatch_date)->format('Y-m-d'),
                            'location' => $t->destination->store_name ?? '-',
                            'rate' => $rate,
                            'amount' => $amount,
                            'allowance' => $allowance,
                            'total_salary' => round($amount + $allowance, 2),
                        ]);
                    }
                }

                return [
                    'plate' => $plate,
                    'truck_type' => optional($group->first()->truck)->truck_type ?? 'N/A',
                    'rows' => $rows,
                    'payroll_total' => round($rows->sum('total_salary'), 2),
                ];
            })
            ->values();

        // =========================
        // 📊 QUEUE SUMMARY
        // =========================
        foreach ($driversPayroll as $d) {
            $driverNames = collect($d['rows'])->pluck('driver')->unique()->implode(', ');

            $queue->push([
                'name' => $driverNames ?: 'Unknown Driver',
                'role' => 'Driver',
                'trips' => count($d['rows']),
                'amount' => $d['payroll_total'],
                'status' => 'Pending',
            ]);
        }

        foreach ($helpersPayroll as $h) {
            $helperNames = collect($h['rows'])->pluck('helper')->unique()->implode(', ');

            $queue->push([
                'name' => $helperNames ?: 'Unknown Helper',
                'role' => 'Helper',
                'trips' => count($h['rows']),
                'amount' => $h['payroll_total'],
                'status' => 'Pending',
            ]);
        }

        $pendingTrips = $trips->count();
        $drivers = $driversPayroll->count();
        $helpers = $helpersPayroll->count();
        $overallTotal = $queue->sum('amount');

        $ranges = AllowanceRange::orderBy('rate_from')->get();

        return view('owner.payroll.dashboard', [
            'pendingTrips' => $pendingTrips,
            'drivers' => $drivers,
            'helpers' => $helpers,
            'queue' => $queue,
            'overallTotal' => $overallTotal,
            'from' => Carbon::parse($from),
            'to' => Carbon::parse($to),
            'ranges' => $ranges,
            'driversPayroll' => $driversPayroll,
            'helpersPayroll' => $helpersPayroll,
        ]);
    }

    public function finalizeWeek(Request $request)
    {
        $weekStart = $request->week_start;
        $weekEnd = $request->week_end;

        // drivers
        $drivers = \App\Models\Driver::all();

        foreach ($drivers as $driver) {
            PayrollPersonLedger::updateOrCreate(
                [
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'person_type' => 'driver',
                    'person_id' => $driver->id,
                ],
                [
                    'updated_by' => auth()->id(),
                ],
            );
        }

        // helpers
        $helpers = \App\Models\Helper::all();

        foreach ($helpers as $helper) {
            PayrollPersonLedger::updateOrCreate(
                [
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'person_type' => 'helper',
                    'person_id' => $helper->id,
                ],
                [
                    'updated_by' => auth()->id(),
                ],
            );
        }

        return redirect()->route('owner.payroll.history')->with('success', 'Payroll finalized and moved to history.');
    }

    public function show($type, $id)
    {
        $weekStart = request('from') ? Carbon::parse(request('from')) : now()->startOfWeek();

        $weekEnd = request('to') ? Carbon::parse(request('to')) : now()->endOfWeek();

        if ($type === 'driver') {
            $payroll = $this->getDriverPayroll($id, $weekStart, $weekEnd);
        } else {
            $payroll = $this->getHelperPayroll($id, $weekStart, $weekEnd);
        }

        return view('owner.payroll.show', [
            'p' => $payroll,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'type' => $type,
        ]);
    }

    private function getDriverPayroll($driverId, $weekStart, $weekEnd)
    {
        $trips = DispatchTrip::with(['destination', 'truck', 'driver', 'helpers'])
            ->where('status', 'Completed')
            ->whereDate('dispatch_date', '>=', $weekStart)
            ->whereDate('dispatch_date', '<=', $weekEnd)
            ->where('driver_id', $driverId)
            ->get();

        $rows = $trips->map(function ($t) {
            $rate = (float) ($t->rate_snapshot ?? 0);

            $helperCount = $t->helpers->count();

            $driverRatePercent = 0.11;

            $computedAmount = round($rate * $driverRatePercent, 2);
            $computedAllowance = $this->allowanceFromRate($rate);

            $amount = $t->manual_amount ?? $computedAmount;
            $allowance = $t->manual_allowance ?? $computedAllowance;

            $totalSalary = round($amount + $allowance, 2);

            return [
                'id' => $t->id,
                'date' => Carbon::parse($t->dispatch_date)->format('Y-m-d'),
                'location' => $t->destination->store_name ?? '-',
                'rate' => $t->rate_snapshot,
                'amount' => $amount,
                'allowance' => $allowance,
                'total_salary' => $totalSalary,
            ];
        });

        $payrollTotal = round($rows->sum('total_salary'), 2);

        $status = $trips->every(fn($t) => $t->payment_status === 'Paid') ? 'PAID' : 'UNPAID';

        return [
            'person_id' => $driverId,
            'name' => optional($trips->first()->driver)->name ?? 'Driver',
            'rows' => $rows,
            'payroll_total' => $payrollTotal,
            'status' => $status,
        ];
    }

    private function getHelperPayroll($helperId, $weekStart, $weekEnd)
    {
        $trips = DispatchTrip::with(['destination', 'truck', 'helpers'])
            ->where('status', 'Completed')
            ->whereDate('dispatch_date', '>=', $weekStart)
            ->whereDate('dispatch_date', '<=', $weekEnd)
            ->get();

        $rows = collect();

        foreach ($trips as $t) {
            foreach ($t->helpers as $h) {
                if ($h->id != $helperId) {
                    continue;
                }

                $rate = (float) ($t->rate_snapshot ?? 0);

                $helperCount = max($t->helpers->count(), 1);

                $computedAmount = round(($rate * 0.08) / $helperCount, 2);
                $computedAllowance = round($this->allowanceFromRate($rate) / $helperCount, 2);

                $amount = $t->manual_amount ?? $computedAmount;
                if ($t->manual_allowance !== null) {
                    $allowance = round($t->manual_allowance / $helperCount, 2);
                } else {
                    $allowance = $computedAllowance;
                }

                $totalSalary = round($amount + $allowance, 2);

                $rows->push([
                    'id' => $t->id,
                    'date' => Carbon::parse($t->dispatch_date)->format('Y-m-d'),
                    'location' => $t->destination->store_name ?? '-',
                    'rate' => $t->rate_snapshot,
                    'amount' => $amount,
                    'allowance' => $allowance,
                    'total_salary' => $totalSalary,
                ]);
            }
        }

        $payrollTotal = round($rows->sum('total_salary'), 2);

        $status = $trips->every(fn($t) => $t->payment_status === 'Paid') ? 'PAID' : 'UNPAID';

        return [
            'person_id' => $helperId,
            'name' => optional($trips->flatMap->helpers->firstWhere('id', $helperId))->name ?? 'Helper',
            'rows' => $rows,
            'payroll_total' => $payrollTotal,
            'status' => $status,
        ];
    }

    private function allowanceFromRate(float $rate): float
    {
        if ($rate <= 0) {
            return 0;
        }

        $range = AllowanceRange::where('rate_from', '<=', $rate)->where('rate_to', '>=', $rate)->first();

        return $range ? (float) $range->allowance : 0;
    }

    private function buildKey(string $type, int $id): string
    {
        return $type . ':' . $id;
    }

    public function index(Request $request)
    {
        // default: current week Mon-Sun
        $from = $request->query('from', now()->startOfWeek(Carbon::MONDAY)->toDateString());
        $to = $request->query('to', now()->endOfWeek(Carbon::SUNDAY)->toDateString());

        // Force the "ledger week" to be Mon-Sun of the selected range
        $weekStart = Carbon::parse($from)->startOfWeek(Carbon::MONDAY);
        $weekEnd = Carbon::parse($to)->endOfWeek(Carbon::SUNDAY);

        // trips in selected range
        $trips = DispatchTrip::with(['destination', 'truck', 'driver', 'helpers'])
            ->where('status', 'Completed')
            ->whereDate('dispatch_date', '>=', $from)
            ->whereDate('dispatch_date', '<=', $to)
            ->orderBy('dispatch_date')
            ->get();

        // load ledgers for this WEEK only
        $ledgers = PayrollPersonLedger::whereDate('week_start', $weekStart->toDateString())->whereDate('week_end', $weekEnd->toDateString())->get()->keyBy(fn($l) => $this->buildKey($l->person_type, (int) $l->person_id));

        // DRIVERS
        $driversPayroll = $trips
            ->groupBy('driver_id')
            ->map(function ($group) use ($ledgers, $weekStart, $weekEnd) {
                $driverId = (int) $group->first()->driver_id;
                $latestBalanceAdvance = PayrollPersonLedger::where('person_type', 'driver')->where('person_id', $driverId)->latest('week_end')->value('advance_amount') ?? 0;
                $name = optional($group->first()->driver)->name ?? 'Unknown Driver';

                $rows = $group->map(function ($t) {
                    $rate = (float) ($t->rate_snapshot ?? 0);

                    $helperCount = $t->helpers->count();

                    $driverRatePercent = 0.11;

                    $computedAmount = round($rate * $driverRatePercent, 2);
                    $computedAllowance = $this->allowanceFromRate($rate);

                    $amount = $t->manual_amount ?? $computedAmount;
                    $allowance = $t->manual_allowance ?? $computedAllowance;

                    $totalSalary = round($amount + $allowance, 2);

                    return [
                        'id' => $t->id,
                        'date' => Carbon::parse($t->dispatch_date)->format('F d, Y'),
                        'location' => $t->destination->store_name . " - " . $t->trip_ticket_no ?? '-',
                        'rate' => $rate,
                        'amount' => $amount,
                        'allowance' => $allowance,
                        'total_salary' => $totalSalary,
                    ];
                });

                $payrollTotal = round($rows->sum('total_salary'), 2);

                $ledger = $ledgers->get($this->buildKey('driver', $driverId));
                $paid = (float) ($ledger->paid_amount ?? 0);
                $advance = (float) ($ledger->advance_amount ?? 0);
                $balance = round($payrollTotal - $paid, 2);

                $alreadyPaid = PayrollPayment::where('person_type', 'driver')->where('person_id', $driverId)->whereDate('week_start', $weekStart)->whereDate('week_end', $weekEnd)->exists();

                $status = $alreadyPaid ? 'PAID' : 'UNPAID';

                return [
                    'week_start' => $weekStart->toDateString(),
                    'week_end' => $weekEnd->toDateString(),

                    'person_id' => $driverId,
                    'person_type' => 'driver',
                    'name' => $name,
                    'rows' => $rows,

                    'latest_balance_advance' => $latestBalanceAdvance,

                    'payroll_total' => $payrollTotal,
                    'paid_amount' => $paid,
                    'advance_amount' => $advance,
                    'balance' => $balance,
                    'status' => $status,
                    'notes' => $ledger->notes ?? '',
                ];
            })
            ->filter(fn($p) => $p['status'] !== 'PAID')
            ->values();

        // HELPERS (only trips with helper)
        $helpersRows = $trips->flatMap(function ($t) {
            return $t->helpers->map(function ($h) use ($t) {
                $rate = (float) ($t->rate_snapshot ?? 0);

                $helperCount = max($t->helpers->count(), 1);

                $computedAmount = round(($rate * 0.08) / $helperCount, 2);
                $computedAllowance = round($this->allowanceFromRate($rate) / $helperCount, 2);

                $amount = $t->manual_amount ?? $computedAmount;
                if ($t->manual_allowance !== null) {
                    $allowance = round($t->manual_allowance / $helperCount, 2);
                } else {
                    $allowance = $computedAllowance;
                }

                $totalSalary = round($amount + $allowance, 2);

                return [
                    'id' => $t->id,
                    'helper_id' => $h->id,
                    'helper_name' => $h->name,
                    'date' => Carbon::parse($t->dispatch_date)->format('F d, Y'),
                    'location' => $t->destination->store_name ?? '-',
                    'rate' => $rate,
                    'amount' => $amount,
                    'allowance' => $allowance,
                    'total_salary' => $totalSalary,
                ];
            });
        });

        $helpersPayroll = $helpersRows
            ->groupBy('helper_id')
            ->map(function ($rows, $helperId) use ($ledgers, $weekStart, $weekEnd) {
                $name = $rows->first()['helper_name'] ?? 'Unknown Helper';
                $latestBalanceAdvance = PayrollPersonLedger::where('person_type', 'helper')->where('person_id', $helperId)->latest('week_end')->value('advance_amount') ?? 0;
                $rowsOnly = $rows->map(
                    fn($r) => [
                        'id' => $r['id'],
                        'date' => $r['date'],
                        'location' => $r['location'],
                        'rate' => $r['rate'],
                        'amount' => $r['amount'],
                        'allowance' => $r['allowance'],
                        'total_salary' => $r['total_salary'],
                    ],
                );

                $payrollTotal = round($rowsOnly->sum('total_salary'), 2);

                $ledger = $ledgers->get('helper:' . $helperId);
                $paid = (float) ($ledger->paid_amount ?? 0);
                $advance = (float) ($ledger->advance_amount ?? 0);
                $balance = round($payrollTotal - $paid, 2);

                $alreadyPaid = PayrollPayment::where('person_type', 'helper')->where('person_id', $helperId)->whereDate('week_start', $weekStart)->whereDate('week_end', $weekEnd)->exists();

                $status = $alreadyPaid ? 'PAID' : 'UNPAID';

                return [
                    'person_id' => (int) $helperId,
                    'name' => $name,
                    'rows' => $rowsOnly,
                    'latest_balance_advance' => $latestBalanceAdvance,
                    'payroll_total' => $payrollTotal,
                    'paid_amount' => $paid,
                    'advance_amount' => $advance,
                    'balance' => $balance,
                    'status' => $status,
                    'notes' => $ledger->notes ?? null,
                ];
            })
            ->filter(fn($p) => $p['status'] !== 'PAID')
            ->values();

        $driversTotal = round($driversPayroll->sum('payroll_total'), 2);
        $helpersTotal = round($helpersPayroll->sum('payroll_total'), 2);
        $grandTotal = round($driversTotal + $helpersTotal, 2);

        return view('owner.payroll.index', compact('from', 'to', 'weekStart', 'weekEnd', 'driversPayroll', 'helpersPayroll', 'driversTotal', 'helpersTotal', 'grandTotal'));
    }

    public function saveLedger(Request $request)
    {
        $data = $request->validate([
            'week_start' => 'required|date',
            'week_end' => 'required|date',
            'person_type' => 'required|in:driver,helper',
            'person_id' => 'required|integer',
            'paid_amount' => 'nullable|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        PayrollPersonLedger::updateOrCreate(
            [
                'week_start' => $data['week_start'],
                'person_type' => $data['person_type'],
                'person_id' => $data['person_id'],
            ],
            [
                'week_end' => $data['week_end'], // ✅ FIX: required by DB
                'paid_amount' => (float) ($data['paid_amount'] ?? 0),
                'advance_amount' => (float) ($data['advance_amount'] ?? 0),
                'notes' => $data['notes'] ?? null,
                'updated_by' => auth()->id(),
            ],
        );

        //Mail::to($person->email)->send(new PayrollReadyMail($person, $rows, $amount, $weekStart, $weekEnd));

        return back()->with('success', 'Payment/advance saved. This week will now appear in History.');
    }

    public function allowances()
    {
        $ranges = AllowanceRange::orderBy('rate_from')->get();

        return view('owner.payroll.dashboard', compact('ranges'));
    }

    public function history(Request $request)
    {
        $from = $request->query('from', now()->subWeeks(8)->startOfWeek(Carbon::MONDAY)->toDateString());
        $to = $request->query('to', now()->endOfWeek(Carbon::SUNDAY)->toDateString());

        $filterStart = Carbon::parse($from)->startOfWeek(Carbon::MONDAY)->toDateString();
        $filterEnd = Carbon::parse($to)->endOfWeek(Carbon::SUNDAY)->toDateString();

        // ✅ SOURCE OF TRUTH (PAYMENTS)
        $payments = PayrollPayment::whereDate('week_start', '>=', $filterStart)->whereDate('week_end', '<=', $filterEnd)->orderByDesc('week_start')->get();

        $weeks = $payments
            ->groupBy(fn($p) => $p->week_start . '|' . $p->week_end)
            ->map(function ($group) {
                $weekStart = $group->first()->week_start;
                $weekEnd = $group->first()->week_end;

                // 🔥 REUSE SAME PAYROLL LOGIC
                $buildRows = function ($personId, $type) use ($weekStart, $weekEnd) {
                    $trips = DispatchTrip::with(['destination', 'helpers'])
                        ->where('status', 'Completed')
                        ->whereDate('dispatch_date', '>=', $weekStart)
                        ->whereDate('dispatch_date', '<=', $weekEnd)
                        ->get();

                    $rows = collect();
                    // DRIVER
                    foreach ($trips as $t) {
                        if ($type === 'driver' && $t->driver_id == $personId) {
                            $rate = (float) ($t->rate_snapshot ?? 0);

                            $helperCount = $t->helpers->count();

                            $driverRatePercent = 0.11;

                            $computedAmount = round($rate * $driverRatePercent, 2);
                            $computedAllowance = $this->allowanceFromRate($rate);

                            $amount = $t->manual_amount ?? $computedAmount;
                            $allowance = $t->manual_allowance ?? $computedAllowance;

                            $rows->push([
                                'date' => Carbon::parse($t->dispatch_date)->format('Y-m-d'),
                                'location' => $t->destination->store_name ?? '-',
                                'rate' => $rate,
                                'amount' => $amount,
                                'allowance' => $allowance,
                                'total_salary' => $amount + $allowance,
                            ]);
                        }

                        // HELPER
                        if ($type === 'helper') {
                            foreach ($t->helpers as $h) {
                                if ($h->id != $personId) {
                                    continue;
                                }

                                $rate = (float) ($t->rate_snapshot ?? 0);

                                $helperCount = max($t->helpers->count(), 1);

                                $computedAmount = round(($rate * 0.08) / $helperCount, 2);
                                $computedAllowance = round($this->allowanceFromRate($rate) / $helperCount, 2);

                                $amount = $t->manual_amount ?? $computedAmount;
                                if ($t->manual_allowance !== null) {
                                    $allowance = round($t->manual_allowance / $helperCount, 2);
                                } else {
                                    $allowance = $computedAllowance;
                                }

                                $rows->push([
                                    'date' => Carbon::parse($t->dispatch_date)->format('Y-m-d'),
                                    'location' => $t->destination->store_name ?? '-',
                                    'rate' => $rate,
                                    'amount' => $amount,
                                    'allowance' => $allowance,
                                    'total_salary' => $amount + $allowance,
                                ]);
                            }
                        }
                    }

                    return $rows;
                };

                // =========================
                // 🚛 DRIVERS
                // =========================
                $driversPayroll = $group
                    ->where('person_type', 'driver')
                    ->map(function ($p) use ($buildRows) {
                        $rows = $buildRows($p->person_id, 'driver');

                        $advanceAmount = (float) ($p->advance_amount ?? 0);
                        $advanceDeducted = (float) ($p->advance_deducted ?? 0);
                        $balanceAdvance = (float) ($p->balance_advance ?? 0);

                        return [
                            'person_id' => $p->person_id,
                            'person_type' => 'driver',
                            'name' => optional(\App\Models\Driver::find($p->person_id))->name ?? 'Driver',
                            'rows' => $rows,
                            'payment_id' => $p->id,
                            'payroll_total' => $p->amount,
                            'paid_amount' => $p->final_amount,

                            // ✅ DEDUCTIONS
                            'advance_amount' => $advanceAmount,
                            'advance' => $advanceDeducted,
                            'balance_advance_remaining' => $balanceAdvance,
                            'net_pay' => (float) ($p->final_amount ?? 0),

                            'status' => 'PAID',
                        ];
                    })
                    ->values();

                // =========================
                // 👷 HELPERS
                // =========================
                $helpersPayroll = $group
                    ->where('person_type', 'helper')
                    ->map(function ($p) use ($buildRows) {
                        $rows = $buildRows($p->person_id, 'helper');

                        $advanceAmount = (float) ($p->advance_amount ?? 0);
                        $advanceDeducted = (float) ($p->advance_deducted ?? 0);
                        $balanceAdvance = (float) ($p->balance_advance ?? 0);

                        return [
                            'person_id' => $p->person_id,
                            'person_type' => 'helper',
                            'name' => optional(\App\Models\Helper::find($p->person_id))->name ?? 'Helper',
                            'rows' => $rows,
                            'payment_id' => $p->id,
                            'payroll_total' => $p->amount,
                            'paid_amount' => $p->final_amount,

                            // ✅ DEDUCTIONS
                            'advance_amount' => $advanceAmount,
                            'advance' => $advanceDeducted,
                            'balance_advance_remaining' => $balanceAdvance,
                            'net_pay' => (float) ($p->final_amount ?? 0),

                            'status' => 'PAID',
                        ];
                    })
                    ->values();

                $driversTotal = $driversPayroll->sum('paid_amount');
                $helpersTotal = $helpersPayroll->sum('paid_amount');

                return [
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'driversPayroll' => $driversPayroll,
                    'helpersPayroll' => $helpersPayroll,
                    'driversTotal' => $driversTotal,
                    'helpersTotal' => $helpersTotal,
                    'grandTotal' => $driversTotal + $helpersTotal,
                ];
            })
            ->values();

        return view('owner.payroll.history', compact('from', 'to', 'weeks'));
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $payment = PayrollPayment::findOrFail($id);

            $ledger = PayrollPersonLedger::where([
                'person_type' => $payment->person_type,
                'person_id' => $payment->person_id,
                'week_start' => $payment->week_start,
                'week_end' => $payment->week_end,
            ])->first();

            if ($ledger) {
                $ledger->paid_amount -= $payment->final_amount;
                $ledger->advance_amount = $payment->advance_amount;
                $ledger->save();
            }

            $tripIds = DB::table('payroll_payment_trips')->where('payroll_payment_id', $payment->id)->pluck('dispatch_trip_id');

            DB::table('payroll_payment_trips')->where('payroll_payment_id', $payment->id)->delete();

            $payment->delete();

            foreach ($tripIds as $tripId) {
                $trip = DispatchTrip::with('helpers')->find($tripId);

                if ($trip) {
                    $this->updateTripPaymentStatus($trip);
                }
            }
        });

        return redirect()
            ->route('owner.payroll.history', [
                'from' => request('from'),
                'to' => request('to'),
                'active_tab' => request('active_tab'),
            ])
            ->with('success', 'Payroll payment deleted successfully.');
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

    public function expenses(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $expensesQuery = DB::table('expenses');
        $creditsQuery = DB::table('credits');
        $deductionsQuery = DB::table('deductions'); // ✅ ADD THIS

        // if ($month && $year) {
        //     $expensesQuery->whereYear('date', $year)->whereMonth('date', $month);

        //     $creditsQuery->whereYear('date', $year)->whereMonth('date', $month);

        //     // ✅ FILTER DEDUCTIONS TOO
        //     $deductionsQuery->whereYear('date_paid', $year)->whereMonth('date_paid', $month);
        // }

        $expensesQuery->whereYear('date', $year)->whereMonth('date', $month);

        $creditsQuery->whereYear('date', $year)->whereMonth('date', $month);

        $deductionsQuery->whereYear('date_paid', $year)->whereMonth('date_paid', $month);

        $allExpenses = $expensesQuery->orderBy('plate_number')->orderBy('date')->orderBy('time')->get();

        // 👉 Separate expense types
        $loadExpenses = $allExpenses->where('type', 'load')->values();
        $fuelExpenses = $allExpenses->where('type', 'fuel')->values();

        // Credits
        $credits = $creditsQuery
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'credits_page')
            ->withQueryString();

        // ✅ DEDUCTIONS
        $deductions = $deductionsQuery->orderByDesc('date_paid')->get();

        $totalDebit = (float) $expensesQuery->sum('debit');
        $totalCredit = (float) $creditsQuery->sum('amount');
        $balance = $totalCredit - $totalDebit;

        $trucks = \App\Models\Truck::orderBy('plate_number')->get();

        // 🔥 COMPUTE FUEL CONSUMPTION
        $grouped = $allExpenses->groupBy('plate_number');

        $finalExpenses = collect();

        // =========================
        // GLOBAL TOTALS (NO FILTER)
        // =========================

        $totalCreditAll = DB::table('credits')->sum('amount');

        $totalFuelAll = DB::table('expenses')->where('type', 'fuel')->sum('debit');

        $totalLoadAll = DB::table('expenses')->where('type', 'load')->sum('debit');

        $totalBenefitsAll = DB::table('deductions')->sum('amount');

        $totalDebitAll = $totalFuelAll + $totalLoadAll + $totalBenefitsAll;

        $recentTrips = DispatchTrip::with('destination')
            ->where('status', 'Completed')
            ->latest()
            ->paginate(7, ['*'], 'trips_page')
            ->withQueryString();

        foreach ($grouped as $plate => $items) {
            $items = $items->sortBy('date')->values();

            foreach ($items as $index => $curr) {
                // Optional: only compute fuel logic for fuel rows
                if ($curr->type !== 'fuel') {
                    $finalExpenses->push($curr);
                    continue;
                }

                if ($index == 0) {
                    $curr->start_odometer = $curr->odometer;
                    $curr->distance = null;
                    $curr->km_per_liter = null;
                } else {
                    $prev = $items[$index - 1];

                    if (!is_null($curr->odometer) && !is_null($prev->odometer) && $curr->liters > 0) {
                        $curr->start_odometer = $prev->odometer;

                        $distance = $curr->odometer - $prev->odometer;

                        if ($distance > 0) {
                            $curr->distance = $distance;
                            $curr->km_per_liter = round($distance / $curr->liters, 2);
                        } else {
                            $curr->distance = null;
                            $curr->km_per_liter = null;
                        }
                    } else {
                        $curr->distance = null;
                        $curr->km_per_liter = null;
                    }
                }

                $finalExpenses->push($curr);
            }
        }

        // 🔥 AVG KM/L
        $totalDistance = 0;
        $totalLiters = 0;

        foreach ($finalExpenses as $e) {
            if ($e->type === 'fuel' && !is_null($e->distance) && $e->liters > 0) {
                $totalDistance += $e->distance;
                $totalLiters += $e->liters;
            }
        }

        $avgKmPerLiter = $totalLiters > 0 ? round($totalDistance / $totalLiters, 2) : 0;

        return view('owner.payroll.expenses', [
            'expenses' => $finalExpenses,
            'loadExpenses' => $loadExpenses,
            'fuelExpenses' => $fuelExpenses,
            'credits' => $credits,
            'deductions' => $deductions, // ✅ IMPORTANT
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'balance' => $balance,
            'trucks' => $trucks,
            'avgKmPerLiter' => $avgKmPerLiter,
            'recentTrips' => $recentTrips,
            'totalCreditAll' => $totalCreditAll,
            'totalDebitAll' => $totalDebitAll,
        ]);
    }

    public function storeExpense(Request $request)
    {
        try {
            // ======================
            // CONDITIONAL VALIDATION
            // ======================
            if ($request->type === 'fuel') {
                $data = $request->validate([
                    'type' => 'required|in:fuel,load',
                    'plate_number' => 'required|string',
                    'date' => 'required|date',
                    'liters' => 'required|numeric|min:0.01',
                    'debit' => 'required|numeric',
                    'start_odometer' => 'nullable|integer|min:0',
                    'odometer' => 'required|integer|min:0',
                    'receipt_surrendered' => 'nullable|in:YES,NO',
                    'remarks' => 'nullable|string',
                    'receipt_image' => 'nullable|image|max:2048',
                ]);
            } else {
                $data = $request->validate([
                    'type' => 'required|in:fuel,load',
                    'plate_number' => 'required|string',
                    'date' => 'required|date',
                    'time' => 'required|date_format:H:i',
                    'debit' => 'required|numeric',
                    'receipt_surrendered' => 'nullable|in:YES,NO',
                    'remarks' => 'nullable|string',
                    'receipt_image' => 'nullable|image|max:2048',
                ]);
            }

            // ======================
            // DEFAULT NULLS FOR LOAD
            // ======================
            if ($request->type !== 'fuel') {
                $data['liters'] = null;
                $data['start_odometer'] = null;
                $data['odometer'] = null;
                $distance = null;
                $kmPerLiter = null;
            }

            // ======================
            // FUEL AUTO START ODOMETER LOGIC
            // ======================
            if ($request->type === 'fuel') {
                // Get last fuel record for SAME plate only
                $lastFuel = DB::table('expenses')->where('plate_number', $data['plate_number'])->where('type', 'fuel')->whereNotNull('odometer')->orderByDesc('date')->orderByDesc('id')->first();

                /**
                 * FIRST ENTRY:
                 * Use manual input
                 *
                 * NEXT ENTRY:
                 * Use previous end odometer automatically
                 */
                $manualStart = isset($data['start_odometer']) ? (int) $data['start_odometer'] : null;

                $startOdometer = $lastFuel ? (int) $lastFuel->odometer : $manualStart;
                $endOdometer = $data['odometer'];

                // Prevent invalid lower odometer
                if (!is_null($startOdometer) && $endOdometer < $startOdometer) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'End odometer must be greater than or equal to start odometer.',
                        ],
                        422,
                    );
                }

                // Compute distance
                $distance = null;
                $kmPerLiter = null;

                if (!is_null($startOdometer) && !is_null($endOdometer)) {
                    $distance = $endOdometer - $startOdometer;

                    if (!is_null($data['liters']) && $data['liters'] > 0) {
                        $kmPerLiter = round($distance / $data['liters'], 2);
                    }
                }

                // Override values
                $data['start_odometer'] = $startOdometer;
                $data['odometer'] = $endOdometer;
            }

            // ======================
            // HANDLE RECEIPT IMAGE
            // ======================
            if ($request->hasFile('receipt_image')) {
                $data['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
            } else {
                $data['receipt_image'] = null;
            }

            // ======================
            // INSERT
            // ======================
            DB::table('expenses')->insert([
                'date' => $data['date'],
                'time' => $data['time'] ?? null,
                'type' => $data['type'],
                'plate_number' => $data['plate_number'],
                'liters' => $data['liters'] ?? null,

                'start_odometer' => $data['start_odometer'] ?? null,
                'odometer' => $data['odometer'] ?? null,

                'distance' => $distance,
                'km_per_liter' => $kmPerLiter,

                'receipt_surrendered' => $data['receipt_surrendered'] ?? null,
                'debit' => $data['debit'],
                'remarks' => $data['remarks'] ?? null,
                'billed' => false,
                'receipt_image' => $data['receipt_image'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ],
                422,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function updateBilled(Request $request, $id)
    {
        DB::table('expenses')
            ->where('id', $id)
            ->update([
                'billed' => $request->billed,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    public function storeCredit(Request $request)
    {
        try {
            $data = $request->validate([
                'amount' => ['required', 'numeric', 'min:0.01'],
                'date' => ['required', 'date'], // ✅ ADD
                'remarks' => ['nullable', 'string'],
            ]);

            DB::table('credits')->insert([
                'amount' => $data['amount'],
                'date' => $data['date'], // ✅ ADD
                'remarks' => $data['remarks'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateCredit(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'required|integer',
                'amount' => 'required|numeric|min:0.01',
                'date' => 'required|date',
            ]);

            DB::table('credits')
                ->where('id', $data['id'])
                ->update([
                    'amount' => $data['amount'],
                    'date' => $data['date'],
                    'updated_at' => now(),
                ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function deleteCredit($id)
    {
        try {
            DB::table('credits')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function deleteExpense($id)
    {
        try {
            DB::table('expenses')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function creditsHistory()
    {
        $credits = DB::table('credits')->orderByDesc('id')->paginate(20);
        return view('owner.payroll.credits_history', compact('credits'));
    }
}
