<?php

namespace App\Http\Controllers\Version2;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\DispatchDestinationTrip;
use App\Models\TripPayroll;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $company = null)
    {
        $from = $request->from ? Carbon::parse($request->from) : now()->startOfWeek();
        $to = $request->to ? Carbon::parse($request->to) : now()->endOfWeek();
        $dispatchPayroll = TripPayroll::select('trip_payrolls.employee_id') // Added prefix here too
            ->selectRaw('COUNT(trip_payrolls.dispatch_id) as total_dispatches')
            ->selectRaw('SUM(trip_payrolls.gross_amount) as total_gross_pay')
            ->selectRaw('SUM(trip_payrolls.allowance) as total_allowance')
            ->selectRaw('SUM(trip_payrolls.gross_amount + trip_payrolls.allowance) as total_combined_payout')
            ->join('dispatch_destination_trips', 'dispatch_destination_trips.id', 'trip_payrolls.dispatch_id')
            ->join('employees', 'employees.id', 'trip_payrolls.employee_id')
            ->where('trip_payrolls.is_deleted', 0)
            ->whereBetween('trip_payrolls.created_at', [$from, $to])
            ->whereNull('trip_payrolls.paid_at')
            ->where('dispatch_destination_trips.dispatch_status', 'Completed')
            ->when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('dispatch_destination_trips.company_id', $companyModel->id);
                }
            })
            ->groupBy('trip_payrolls.employee_id')
            ->orderByRaw('SUM(trip_payrolls.gross_amount) DESC'); // Fixed: Uses orderByRaw with table prefix
        $driversPayroll = (clone $dispatchPayroll)->where('employees.type', 'driver')->get();
        $helpersPayroll = (clone $dispatchPayroll)->where('employees.type', 'helper')->get();
        $employeeQuery = TripPayroll::select('trip_payrolls.employee_id')
            ->selectRaw('COUNT(trip_payrolls.dispatch_id) as total_dispatches')
            ->selectRaw('SUM(trip_payrolls.gross_amount) as total_gross_pay')
            ->selectRaw('SUM(trip_payrolls.allowance) as total_allowance')
            ->selectRaw('SUM(trip_payrolls.gross_amount + trip_payrolls.allowance) as total_combined_payout')
            ->join('dispatch_destination_trips', 'dispatch_destination_trips.id', '=', 'trip_payrolls.dispatch_id')
            ->join('employees', 'employees.id', '=', 'trip_payrolls.employee_id')
            ->whereBetween('dispatch_destination_trips.dispatch_date', [$from, $to])
            //->whereBetween('trip_payrolls.created_at', [$from, $to])
            ->where('trip_payrolls.is_deleted', 0)
            ->whereNull('trip_payrolls.paid_at')
            ->where('dispatch_destination_trips.dispatch_status', 'Completed')
            ->when($company, function ($query) use ($company) {
                $companyModel = Company::where('name', $company)->first();

                if ($companyModel) {
                    $query->where('dispatch_destination_trips.company_id', $companyModel->id);
                }
            })
            ->with('employee')
            ->orderBy('employees.type')
            ->orderBy('employees.name')
            ->groupBy('trip_payrolls.employee_id');
        $driversPayroll = (clone $employeeQuery)->where('employees.type', 'driver')->get();
        $helpersPayroll = (clone $employeeQuery)->where('employees.type', 'helper')->get();
        $employeePayroll = (clone $employeeQuery)->get();

        $employeeSalary = [];

        foreach ($employeePayroll as $employeePayrollItem) {

            $employee = $employeePayrollItem->employee;

            $dispatchList = $employee->payrollList([$from, $to])
                ->with([
                    'dispatch.company',
                    'dispatch.destination',
                ])
                ->get();

            $dispatchLists = [];

            foreach ($dispatchList as $index => $payroll) {

                $dispatchLists[] = [
                    'tripNo'       => $index + 1,
                    'dispatchCode'   => $payroll->dispatch->trip_ticket_no ?? 'N/A',
                    'dispatchDate' => Carbon::parse($payroll->dispatch->dispatch_date)->format('F d, Y'),
                    'company'      => $payroll->dispatch->company->name,
                    'destination'  => $payroll->dispatch->destination->name(),
                    'amountRate'   => $payroll->dispatch->destination->rate,
                    'payRate'      => $payroll->gross_amount,
                    'allowance'    => $payroll->allowance,
                    'totalPay'     => $payroll->gross_amount + $payroll->allowance,
                    'payStatus'    => $payroll->paid_at,
                ];
            }

            $employeeSalary[] = [
                'employee_id' => $employee->id,
                'name'          => $employee->name,
                'type'          => $employee->type,
                'totalSalary'   => $employeePayrollItem->total_combined_payout,
                'totalDispatch' => $employeePayrollItem->total_dispatches,
                'dispatchList'  => $dispatchLists,
                'status'        => $dispatchList->whereNull('paid_at')->isNotEmpty()
                    ? 'UNPAID'
                    : 'PAID',
            ];
        }

        $weekStart = $from;
        $weekEnd = $to;
        //return $employeeSalary;
        //return compact('employeeSalary', 'from', 'to', 'weekStart', 'weekEnd', 'driversPayroll', 'helpersPayroll');
        return view('version2.payroll.index', compact('employeeSalary', 'from', 'to', 'weekStart', 'weekEnd', 'driversPayroll', 'helpersPayroll'));
    }
    public function dashboard(Request $request, $company = null)
    {
        $from = $request->from ? Carbon::parse($request->from) : now()->startOfWeek();
        $to = $request->to ? Carbon::parse($request->to) : now()->endOfWeek();
        $pendingTrips = DispatchDestinationTrip::with(['driver', 'destination'])
            ->where('dispatch_status', 'Completed')
            ->whereBetween('dispatch_date', [$from, $to])
            ->when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('company_id', $companyModel->id);
                }
            })
            ->get();
        $employeeQuery = TripPayroll::select('trip_payrolls.employee_id')
            ->selectRaw('COUNT(trip_payrolls.dispatch_id) as total_dispatches')
            ->selectRaw('SUM(trip_payrolls.gross_amount) as total_gross_pay')
            ->selectRaw('SUM(trip_payrolls.allowance) as total_allowance')
            ->selectRaw('SUM(trip_payrolls.gross_amount + trip_payrolls.allowance) as total_combined_payout')
            ->join('dispatch_destination_trips', 'dispatch_destination_trips.id', '=', 'trip_payrolls.dispatch_id')
            ->join('employees', 'employees.id', '=', 'trip_payrolls.employee_id')
            ->whereBetween('dispatch_destination_trips.dispatch_date', [$from, $to])
            ->where('trip_payrolls.is_deleted', 0)
            ->whereNull('trip_payrolls.paid_at')
            ->where('dispatch_destination_trips.dispatch_status', 'Completed')
            ->when($company, function ($query) use ($company) {
                $companyModel = Company::where('name', $company)->first();

                if ($companyModel) {
                    $query->where('dispatch_destination_trips.company_id', $companyModel->id);
                }
            })
            ->with('employee')
            ->orderBy('employees.type')
            ->orderBy('employees.name')
            ->groupBy('trip_payrolls.employee_id');
        $drivers  = (clone $employeeQuery)->where('employees.type', 'driver')->get();
        $helpersPayroll = (clone $employeeQuery)->where('employees.type', 'helper')->get();
        $employeePayroll = (clone $employeeQuery)->get();

        $employeeSalary = [];

        foreach ($employeePayroll as $employeePayrollItem) {

            $employee = $employeePayrollItem->employee;

            $dispatchList = $employee->payrollList([$from, $to])
                ->with([
                    'dispatch.company',
                    'dispatch.destination',
                ])
                ->get();

            $dispatchLists = [];

            foreach ($dispatchList as $index => $payroll) {

                $dispatchLists[] = [
                    'tripNo'       => $index + 1,
                    'dispatchCode'   => $payroll->dispatch->trip_ticket_no ?? 'N/A',
                    'dispatchDate' => Carbon::parse($payroll->dispatch->dispatched_at)->format('F d, Y'),
                    'company'      => $payroll->dispatch->company->name,
                    'destination'  => $payroll->dispatch->destination->name(),
                    'amountRate'   => $payroll->dispatch->destination->rate,
                    'payRate'      => $payroll->gross_amount,
                    'allowance'    => $payroll->allowance,
                    'totalPay'     => $payroll->gross_amount + $payroll->allowance,
                    'payStatus'    => $payroll->paid_at,
                ];
            }

            $employeeSalary[] = [
                'employee_id' => $employee->id,
                'name'          => $employee->name,
                'type'          => $employee->type,
                'totalSalary'   => $employeePayrollItem->total_combined_payout,
                'totalDispatch' => $employeePayrollItem->total_dispatches,
                'dispatchList'  => $dispatchLists,
                'status'        => $dispatchList->whereNull('paid_at')->isNotEmpty()
                    ? 'UNPAID'
                    : 'PAID',
            ];
        }

        //  return $employeeSalary;
        return view('version2.payroll.dashboard', compact('from', 'to', 'pendingTrips', 'drivers', 'employeeSalary'));

        // Query for Dispatch
        $dispatchQuery = DispatchDestinationTrip::whereBetween('dispatched_at', [$from, $to])
            ->where('dispatch_status', 'Completed');

        // Query for Dispatch Payroll
        $dispatchPayroll = TripPayroll::select('trip_payrolls.employee_id') // Added prefix here too
            ->selectRaw('COUNT(trip_payrolls.dispatch_id) as total_dispatches')
            ->selectRaw('SUM(trip_payrolls.gross_amount) as total_gross_pay')
            ->selectRaw('SUM(trip_payrolls.allowance) as total_allowance')
            ->selectRaw('SUM(trip_payrolls.gross_amount + trip_payrolls.allowance) as total_combined_payout')
            ->join('dispatch_destination_trips', 'dispatch_destination_trips.id', 'trip_payrolls.dispatch_id')
            ->where('trip_payrolls.is_deleted', 0)
            ->whereBetween('trip_payrolls.created_at', [$from, $to])
            ->whereNull('trip_payrolls.paid_at')
            ->where('dispatch_destination_trips.dispatch_status', 'Completed')
            ->when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('dispatch_destination_trips.company_id', $companyModel->id);
                }
            })
            ->groupBy('trip_payrolls.employee_id')
            ->orderByRaw('SUM(trip_payrolls.gross_amount) DESC') // Fixed: Uses orderByRaw with table prefix
            ->get();

        // Fixed: The collection key name matches the clean alias from above
        $totalPayroll = $dispatchPayroll->sum('total_combined_payout');
        // Employee Payroll Dashboard
        // First Count the Driver not paid

        // ✅ ONLY COMPLETED TRIPS
        $trips = DispatchDestinationTrip::with(['driver', 'destination'])
            ->where('dispatch_status', 'Completed')
            ->whereBetween('dispatch_date', [$from, $to])
            ->when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('company_id', $companyModel->id);
                }
            })
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
        $trips2 = DispatchDestinationTrip::with([
            'driver',
            'truck',
            'destination'
        ])
            ->where('dispatch_status', 'Completed')
            ->whereBetween('dispatch_date', [$from, $to])
            ->get();
        $helpersPayroll = $trips2
            ->groupBy('helper_id')
            ->map(function ($group) {

                $helper = optional($group->first()->helper);

                return [
                    'name' => $helper->helper->name ?? 'N/A',
                    'trips' => $group->count(),
                    'amount' => $group->sum(function ($trip) {
                        if (!$trip->truck) {
                            return 0;
                        }
                        return $trip->truck->truck_type === 'L300'
                            ? 400
                            : (($trip->destination->rate ?? 0) * 0.8);
                    }),
                    'status' => $group->every(fn($trip) => $trip->payment_status === 'Paid')
                        ? 'PAID'
                        : 'UNPAID',
                ];
            })
            ->values();
        // return $helpersPayroll;
        // return compact('driversPayroll', 'helpersPayroll', 'queue', 'totalPayroll', 'from', 'to');
        return view('version2.payroll.dashboard', [
            'driversPayroll' => $driversPayroll,
            'helpersPayroll' => $helpersPayroll,
            'queue' => $queue,
            'total' => $totalPayroll,
            'from' => $from,
            'to' => $to,
            'drivers' => $queue->count(), // mas tama ito
            'pendingTrips' => $trips->count(),
            'dispatchPayroll' => $dispatchPayroll
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
