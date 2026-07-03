<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WatsonTrip;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\WatsonDestination;
use App\Models\WatsonPayrollPayment;

class WatsonDashboardController extends Controller
{
    public function index()
    {
        // =========================
        // GAINS BREAKDOWN
        // =========================

        $trips = WatsonTrip::with('destination')->where('status', 'Completed')->get();

        $gains_billed = $trips->where('billing_status', 'Billed')->sum(fn($t) => $t->destination->rate ?? 0);

        $gains_pending = $trips->where('billing_status', 'Pending')->sum(fn($t) => $t->destination->rate ?? 0);

        $gains_unbilled = $trips->whereNull('billing_status')->sum(fn($t) => $t->destination->rate ?? 0);

        // total gains
        $gains = $gains_billed + $gains_pending + $gains_unbilled;

        // =========================
        // EXPENSES (PAYROLL ONLY)
        // =========================

        $payroll = WatsonPayrollPayment::sum('final_amount');

        // =========================
        // PROFIT
        // =========================

        $profit = $gains - $payroll;

        return view('watson.dashboard', [
            'trucksStats' => [
                'total' => Truck::count(),
                'active' => Truck::where('status', 'Active')->count(),
            ],

            'driversStats' => [
                'total' => Driver::count(),
                'active' => Driver::where('status', 'Active')->count(),
            ],

            'destinationsStats' => [
                'total' => WatsonDestination::count(),
                'avg_rate' => WatsonDestination::avg('rate') ?? 0,
            ],

            'tripsStats' => [
                'total' => WatsonTrip::count(),
                'completed' => WatsonTrip::where('status', 'Completed')->count(),
            ],

            'todayData' => [
                'dispatched' => WatsonTrip::whereDate('dispatch_date', now())->count(),
                'profit' => WatsonTrip::with('destination')->whereDate('dispatch_date', now())->get()->sum(fn($t) => $t->destination->rate ?? 0),
            ],

            'weekData' => [
                'dispatched' => WatsonTrip::whereBetween('dispatch_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'gains' => WatsonTrip::with('destination')
                    ->whereBetween('dispatch_date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->get()
                    ->sum(fn($t) => $t->destination->rate ?? 0),
            ],

            'activeTrips' => WatsonTrip::with(['driver', 'destination', 'truck'])
                ->where('status', 'Dispatched')
                ->latest()
                ->take(6)
                ->get(),

            'topDestinations' => WatsonDestination::orderByDesc('rate')->take(6)->get(),

            'recentTrips' => WatsonTrip::with('destination')->latest()->take(5)->get(),

            'financialData' => [
                'gains' => $gains,
                'gains_billed' => $gains_billed,
                'gains_pending' => $gains_pending,
                'gains_unbilled' => $gains_unbilled,

                'expenses' => $payroll,
                'payroll' => $payroll,

                'profit' => $profit,
            ],
        ]);
    }
}
