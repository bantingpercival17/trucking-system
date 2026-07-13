<?php

namespace App\Http\Controllers\Version2;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\DispatchDestinationTrip;
use App\Models\Driver;
use App\Models\Employee;
use App\Models\Truck;
use App\Models\TruckDestination;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexAdmin(Request $request)
    {

        $data =  $this->dashboard($request, null);
        // /return $profits;
        return view('version2.dashboard', $data);
    }
    public function indexCompany(Request $request, $company = null)
    {
        $data =  $this->dashboard($request, $company);
        return view('version2.dashboard', $data);
    }
    function dashboard($request, $company = null)
    {
        $trucksStats = [
            'total' => Truck::count(),
            'active' => Truck::where('status', 'active')->count(),
        ];

        $employeeStatus = [
            'total' => Employee::count(),
            'active' => Employee::where('is_active', true)->count(),
        ];

        $destinationsStats = [
            'total' => TruckDestination::when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('company_id', $companyModel->id);
                }
            })->count(),
            'avg_rate' => (float) (TruckDestination::when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('company_id', $companyModel->id);
                }
            })->avg('rate') ?? 0),
        ];

        $tripsStats = [
            'total' => DispatchDestinationTrip::when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('company_id', $companyModel->id);
                }
            })->count(),
            'completed' => DispatchDestinationTrip::where('dispatch_status', 'Completed')->when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('company_id', $companyModel->id);
                }
            })->count(),
        ];
        $profits = $this->profits($company);
        $activeTrips = DispatchDestinationTrip::with(['destination', 'truck', 'driver'])
            ->where('dispatch_status', 'Dispatched')
            ->orderByDesc('dispatch_date')
            ->take(6)
            ->get();

        $recentTrips = DispatchDestinationTrip::with(['destination'])
            ->orderByDesc('dispatch_date')
            ->take(6)
            ->get();
        $financialData =   $this->breakdown($company);
        $topDestinations = [];
        return  compact('trucksStats', 'employeeStatus', 'destinationsStats', 'tripsStats', 'profits', 'activeTrips', 'recentTrips', 'financialData', 'topDestinations');
    }
    function profits($company = null)
    {
        // ----------------
        // DATES
        // ----------------
        $today = Carbon::today();
        $startWeek = Carbon::now()->startOfWeek();
        $endWeek = Carbon::now()->endOfWeek();

        // ----------------
        // TODAY DATA (FIXED)
        // ----------------

        // ✅ TODAY GAINS
        // ✅ TODAY GAINS
        $todayGains = (float)DispatchDestinationTrip::query()
            ->join('truck_destinations', 'dispatch_destination_trips.truck_id', '=', 'truck_destinations.id')
            ->whereDate('dispatch_destination_trips.dispatch_date', [$today])
            ->whereIn('dispatch_destination_trips.dispatch_status', ['Dispatched', 'Completed'])
            ->sum('truck_destinations.rate');
        /*  DispatchDestinationTrip::whereDate('dispatch_date', $today)
            ->whereIn('status', ['Dispatched', 'Completed'])
            ->sum('rate_snapshot'); */

        // ✅ TODAY FUEL
        $todayFuel = (float) DB::table('expenses')->whereDate('date', $today)->sum('debit');

        // ✅ TODAY PAYROLL (FIXED)
        $todayPayroll = (float) DB::table('payroll_payments')->whereDate('paid_at', $today)->sum('amount');

        // ✅ TOTAL EXPENSES
        $todayExpenses = $todayFuel + $todayPayroll;

        // ✅ PROFIT
        $todayProfit = $todayGains - $todayExpenses;

        $todayData = [
            'dispatched' => DispatchDestinationTrip::whereDate('dispatch_date', $today)
                ->whereIn('dispatch_status', ['Dispatched', 'Completed'])
                ->count(),

            'gains' => $todayGains,
            'profit' => $todayProfit,
        ];

        // ----------------
        // WEEK DATA
        // ----------------
        $dispatchDestination =  DispatchDestinationTrip::whereBetween('dispatch_destination_trips.dispatch_date', [$startWeek, $endWeek])
            ->where('dispatch_destination_trips.is_deleted', false)
            ->when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('dispatch_destination_trips.company_id', $companyModel->id);
                }
            });
        $gains =   (clone $dispatchDestination)->join('truck_destinations', 'truck_destinations.id', '=', 'dispatch_destination_trips.destination_id')
            ->select('truck_destinations.rate')
            ->whereIn('dispatch_destination_trips.dispatch_status', ['Dispatched', 'Completed'])
            ->sum('truck_destinations.rate');
        $dispatched = (clone $dispatchDestination)->where('dispatch_destination_trips.dispatch_status', 'Dispatched')
            ->count();
        $weekData = compact('gains', 'dispatched');
        return compact('todayData', 'weekData');
    }
    function breakdown($company)
    {
        $dispatchDestination =  DispatchDestinationTrip::join('truck_destinations', 'truck_destinations.id', '=', 'dispatch_destination_trips.destination_id')
            ->select('truck_destinations.rate')
            ->where('dispatch_destination_trips.is_deleted', false)
            ->when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('dispatch_destination_trips.company_id', $companyModel->id);
                }
            });
        // Profit Status
        $gains = (float) $dispatchDestination->whereIn('dispatch_status', ['Dispatched', 'Completed'])->sum('truck_destinations.rate');
        $gainsBilled = (float) $dispatchDestination->where('billing_status', 'Billed')->sum('truck_destinations.rate');
        $gainsUnbilled = (float) $dispatchDestination->where('billing_status', 'Unbilled')->sum('truck_destinations.rate');
        $gainsPending = (float) $dispatchDestination->where('billing_status', 'Pending')->sum('truck_destinations.rate');

        // ==============================
        // EXPENSE BREAKDOWN
        // ==============================
        // FUEL ONLY
        $fuelExpenses = (float) DB::table('expenses')->where('type', 'fuel')->sum('debit');
        // LOAD ONLY
        $loadExpenses = (float) DB::table('expenses')->where('type', 'load')->sum('debit');
        // DEDUCTIONS (SSS/PAGIBIG/PHILHEALTH)
        $deductionExpenses = (float) DB::table('deductions')->sum('amount');
        // PAYROLL / PASAHOD
        $payrollExpenses = (float) DB::table('payroll_payments')->sum('amount');
        // ✅ TOTAL EXPENSES
        $expenses = $fuelExpenses + $loadExpenses + $deductionExpenses + $payrollExpenses;
        // ✅ PROFIT
        $profit = $gains - $expenses;

        return compact('gains', 'gainsBilled', 'gainsUnbilled', 'gainsPending', 'profit', 'expenses', 'fuelExpenses', 'loadExpenses', 'deductionExpenses', 'payrollExpenses');
        /* 
        
          $financialData = [
            'gains' => $gains,
            'expenses' => $expenses,
            'profit' => $profit,

            // EXPENSE BREAKDOWN
            'fuel' => $fuelExpenses,
            'load' => $loadExpenses,
            'deductions' => $deductionExpenses,
            'payroll' => $payrollExpenses,

            // GAINS BREAKDOWN
            'gains_billed' => $gainsBilled,
            'gains_unbilled' => $gainsUnbilled,
            'gains_pending' => $gainsPending,
        ];
        */
    }
}
