<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlashTrip;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\FlashDestination;
use App\Models\FlashPayrollPayment;

class FlashDashboardController extends Controller
{
    public function index()
    {
        // =========================
        // GAINS BREAKDOWN
        // =========================

        $trips = FlashTrip::with('destination')->where('status', 'Completed')->get();

        $gains_billed = $trips->where('billing_status', 'Billed')->sum(fn($t) => $t->destination->rate ?? 0);

        $gains_pending = $trips->where('billing_status', 'Pending')->sum(fn($t) => $t->destination->rate ?? 0);

        $gains_unbilled = $trips->whereNull('billing_status')->sum(fn($t) => $t->destination->rate ?? 0);

        // total gains
        $gains = $gains_billed + $gains_pending + $gains_unbilled;

        // =========================
        // EXPENSES (PAYROLL ONLY)
        // =========================

        $payroll = FlashPayrollPayment::sum('final_amount');

        // =========================
        // PROFIT
        // =========================

        $profit = $gains - $payroll;

        return view('flash.dashboard', [
            'trucksStats' => [
                'total' => Truck::count(),
                'active' => Truck::where('status', 'Active')->count(),
            ],

            'driversStats' => [
                'total' => Driver::count(),
                'active' => Driver::where('status', 'Active')->count(),
            ],

            'destinationsStats' => [
                'total' => FlashDestination::count(),
                'avg_rate' => FlashDestination::avg('rate') ?? 0,
            ],

            'tripsStats' => [
                'total' => FlashTrip::count(),
                'completed' => FlashTrip::where('status', 'Completed')->count(),
            ],

            'todayData' => [
                'dispatched' => FlashTrip::whereDate('dispatch_date', now())->count(),
                'profit' => FlashTrip::with('destination')->whereDate('dispatch_date', now())->get()->sum(fn($t) => $t->destination->rate ?? 0),
            ],

            'weekData' => [
                'dispatched' => FlashTrip::whereBetween('dispatch_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'gains' => FlashTrip::with('destination')
                    ->whereBetween('dispatch_date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->get()
                    ->sum(fn($t) => $t->destination->rate ?? 0),
            ],

            'activeTrips' => FlashTrip::with(['driver', 'destination', 'truck'])
                ->where('status', 'Dispatched')
                ->latest()
                ->take(6)
                ->get(),

            'topDestinations' => FlashDestination::orderByDesc('rate')->take(6)->get(),

            'recentTrips' => FlashTrip::with('destination')->latest()->take(5)->get(),

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
