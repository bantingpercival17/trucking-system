<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Truck;

class TruckController extends Controller
{
    private function isFlash()
    {
        return request()->routeIs('flash.*');
    }

    private function isWatson()
    {
        return request()->routeIs('watson.*');
    }

    private function companyPrefix()
    {
        if ($this->isWatson()) {
            return 'watson';
        }
        if ($this->isFlash()) {
            return 'flash';
        }
        return 'owner';
    }

    private function routeName($name)
    {
        return "{$this->companyPrefix()}.$name";
    }

    private function viewName($name)
    {
        return "{$this->companyPrefix()}.$name";
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:trucks,plate_number',
            'truck_type' => 'required|in:L300,6W,4W,AUV',
            'status' => 'required|in:active,inactive',
            'company_number' => 'nullable|string|max:20',
        ]);

        Truck::create($validated);

        $route = $this->routeName('trucks.index');

        return redirect()->route($route)->with('success', 'Truck added successfully.');
    }

    public function index()
    {
        $trucks = \App\Models\Truck::latest()->get();

        $stats = [
            'total' => Truck::count(),
            'available' => Truck::where('status', 'active')->count(),
            'on_trip' => Truck::where('status', 'on_trip')->count(),
            'maintenance' => 0,
            'out_of_service' => Truck::where('status', 'inactive')->count(),
        ];

        // separate paginators (10 each)
        $l300Trucks = Truck::where('truck_type', 'L300')
            ->orderBy('plate_number')
            ->paginate(10, ['*'], 'l300_page');

        $sixWTrucks = Truck::where('truck_type', '6W')
            ->orderBy('plate_number')
            ->paginate(10, ['*'], 'sixw_page');

        $view = $this->viewName('trucks.index');

        return view($view, compact('stats', 'l300Trucks', 'sixWTrucks'));
    }

    public function sidebar($id)
    {
        $truck = \App\Models\Truck::with(['driver'])->findOrFail($id);
        $driver = $truck->driver;
        $fuelTotal = $truck
            ->expenses()
            ->where('date', '>=', now()->subWeek())
            ->sum('liters');
        $fuelAmount = $truck
            ->expenses()
            ->where('date', '>=', now()->subWeek())
            ->sum('debit');
        $trips = $truck
            ->dispatchTrips()
            ->where('dispatch_date', '>=', now()->subWeek())
            ->get();

        $view = $this->viewName('trucks._sidebar');

        return view($view, compact('truck', 'driver', 'fuelTotal', 'fuelAmount', 'trips'))->render();
    }

    public function destroy(Truck $truck)
    {
        $truck->delete();

        $route = $this->routeName('trucks.index');

        return redirect()->route($route)->with('success', 'Truck deleted successfully.');
    }

    public function update(Request $request, Truck $truck)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:trucks,plate_number,' . $truck->id,
            'truck_type' => 'required|in:L300,6W,4W,AUV',
            'status' => 'required|in:active,inactive',
            'company_number' => 'nullable|string|max:20',
        ]);

        $truck->update($validated);

        $route = $this->routeName('trucks.index');

        return redirect()->route($route)->with('success', 'Truck updated successfully.');
    }

    public function destroyAll(Request $request)
    {
        $type = $request->input('truck_type');

        // ✅ MOBILE: delete ALL trucks
        if (!$type) {
            \App\Models\Truck::query()->delete();

            return back()->with('success', 'All trucks deleted successfully.');
        }

        // ✅ DESKTOP: delete by type
        if (!in_array($type, ['L300', '6W'], true)) {
            return back()->with('error', 'Invalid truck type.');
        }

        \App\Models\Truck::where('truck_type', $type)->delete();

        return back()->with('success', "All {$type} trucks deleted successfully.");
    }

    public function detailsModal($plate)
    {
        $truck = Truck::where('plate_number', $plate)->first();
        if (!$truck) {
            return response('<div class="text-danger">Truck not found.</div>', 404);
        }
        // Get driver (latest dispatched trip in last week)
        $driverName =
            DispatchTrip::where('truck_id', $truck->id)
                ->where('dispatch_date', '>=', now()->subWeek())
                ->with('driver')
                ->orderByDesc('dispatch_date')
                ->first()?->driver?->name ?? '—';

        // Fuel consumption (last week)
        $fuelConsumption = Expense::where('plate_number', $plate)
            ->where('date', '>=', now()->subWeek())
            ->sum('liters');

        // Trips (last week)
        $trips = DispatchTrip::where('truck_id', $truck->id)
            ->where('dispatch_date', '>=', now()->subWeek())
            ->with('destination')
            ->orderByDesc('dispatch_date')
            ->get();

        $view = $this->viewName('trucks.modal');

        $html = view($view, [
            'truck' => $truck,
            'driverName' => $driverName,
            'fuelConsumption' => $fuelConsumption,
            'trips' => $trips,
        ])->render();
        return response($html);
    }
}
