<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function reports(Request $request)
    {
        // Maintenance summary
        $maintenanceCount = \DB::table('maintenance')->count();
        $lastMaintenance = \DB::table('maintenance')->orderByDesc('date')->value('date');
        // Trips summary
        $tripCount = \DB::table('dispatch_trips')->count();
        $lastTripDate = \DB::table('dispatch_trips')->orderByDesc('dispatch_date')->value('dispatch_date');
        // Fuel logs summary
        $fuelLogCount = \DB::table('expenses')->count();
        $lastFuelEntry = \DB::table('expenses')->orderByDesc('date')->value('date');

        // Plate numbers
        $plateNumbers = \DB::table('trucks')->pluck('plate_number')->toArray();

        // Vehicle Report
        $vehicleQuery = \DB::table('trucks')->select('plate_number')->get();
        $vehicleReports = [];
        foreach ($vehicleQuery as $truck) {
            $plate = $truck->plate_number;
            $vehicleReports[] = [
                'plate_number' => $plate,
                'total_trips' => \DB::table('dispatch_trips')->join('trucks', 'dispatch_trips.truck_id', '=', 'trucks.id')->where('trucks.plate_number', $plate)->count(),
                'total_fuel_cost' => \DB::table('expenses')->where('plate_number', $plate)->sum('debit'),
                'total_fuel_liters' => \DB::table('expenses')->where('plate_number', $plate)->sum('liters'),
                'total_maintenance_cost' => \DB::table('maintenance')->where('plate_number', $plate)->sum('cost'),
            ];
        }

        // Trip Report
        $tripQuery = \DB::table('dispatch_trips')
            ->join('trucks', 'dispatch_trips.truck_id', '=', 'trucks.id')
            ->join('drivers', 'dispatch_trips.driver_id', '=', 'drivers.id')
            ->join('destinations', 'dispatch_trips.destination_id', '=', 'destinations.id')
            ->select('dispatch_trips.*', 'trucks.plate_number', 'drivers.name as driver_name', 'destinations.store_name as destination_name')
            ->when($request->input('trip_plate_number'), function ($q) use ($request) {
                $q->where('trucks.plate_number', $request->input('trip_plate_number'));
            })
            ->when($request->input('trip_from'), function ($q) use ($request) {
                $q->where('dispatch_trips.dispatch_date', '>=', $request->input('trip_from'));
            })
            ->when($request->input('trip_to'), function ($q) use ($request) {
                $q->where('dispatch_trips.dispatch_date', '<=', $request->input('trip_to'));
            })
            ->get();
        $tripReports = [];
        foreach ($tripQuery as $trip) {
            $tripReports[] = [
                'plate_number' => $trip->plate_number,
                'driver_name' => $trip->driver_name ?? '',
                'trip_date' => $trip->dispatch_date,
                'destination' => $trip->destination_name ?? '',
            ];
        }

        // Fuel Consumption Report
        $fuelQuery = \DB::table('expenses')
            ->when($request->input('fuel_plate_number'), function ($q) use ($request) {
                $q->where('plate_number', $request->input('fuel_plate_number'));
            })
            ->when($request->input('fuel_from'), function ($q) use ($request) {
                $q->where('date', '>=', $request->input('fuel_from'));
            })
            ->when($request->input('fuel_to'), function ($q) use ($request) {
                $q->where('date', '<=', $request->input('fuel_to'));
            })
            ->get();
        $fuelReports = [];
        $totalFuelCost = [];
        $totalLiters = [];
        $totalOdo = [];
        foreach ($fuelQuery as $fuel) {
            $fuelReports[] = [
                'plate_number' => $fuel->plate_number,
                'fuel_date' => $fuel->date,
                'liters' => $fuel->liters,
                'cost' => $fuel->debit,
                'odometer' => $fuel->odometer,
            ];
            $totalFuelCost[$fuel->plate_number] = ($totalFuelCost[$fuel->plate_number] ?? 0) + $fuel->debit;
            $totalLiters[$fuel->plate_number] = ($totalLiters[$fuel->plate_number] ?? 0) + $fuel->liters;
            $totalOdo[$fuel->plate_number] = ($totalOdo[$fuel->plate_number] ?? 0) + ($fuel->odometer ?? 0);
        }
        // Simple average consumption (liters per odometer diff)
        $averageConsumption = null;
        if (count($totalLiters) && count($totalOdo)) {
            $averageConsumption = 'Avg: ' . round(array_sum($totalLiters) / max(array_sum($totalOdo), 1), 4) . ' liters/km';
        }

        return view('users.reports', compact('maintenanceCount', 'lastMaintenance', 'tripCount', 'lastTripDate', 'fuelLogCount', 'lastFuelEntry', 'vehicleReports', 'tripReports', 'fuelReports', 'plateNumbers', 'totalFuelCost', 'averageConsumption'));
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        })
            ->latest()
            ->get();

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:owner,admin,secretary,it'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('owner.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:owner,admin,secretary,it'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // ✅ GET OLD DATA (ADD THIS)

        if ($validated['role'] === 'owner' && User::where('role', 'owner')->where('id', '!=', $user->id)->exists()) {
            return back()
                ->withErrors(['role' => 'Owner account already exists.'])
                ->withInput();
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // ✅ REMOVE PASSWORD FROM NEW DATA (optional but clean)

        return redirect()->route('owner.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'owner') {
            return back()->withErrors(['role' => 'Owner account cannot be deleted.']);
        }
        
        $user->delete();

        return redirect()->route('owner.users.index')->with('success', 'User deleted successfully.');
    }
}
