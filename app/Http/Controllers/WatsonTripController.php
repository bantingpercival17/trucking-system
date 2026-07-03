<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WatsonTrip;
use App\Models\WatsonDestination;
use App\Models\Truck;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TripAssignedMail;
use App\Models\Helper;

class WatsonTripController extends Controller
{
    public function index(Request $request)
    {
        $trips = WatsonTrip::with(['destination', 'truck', 'driver'])
            ->where('status', '!=', 'Completed')
            ->where('is_removed', 0) // 🔥 THIS LINE
            ->latest()
            ->paginate(10);

        $destinations = WatsonDestination::orderBy('area')->get();
        $trucks = Truck::where('status', 'active')->get();
        $drivers = Driver::where('availability_status', 'available')->orderBy('name')->get();
        $helpers = Helper::where('status', 'active')->orderBy('name')->get();
        $availableTrucks = Truck::where('status', 'active')->get();
        $availableDrivers = Driver::where('availability_status', 'available')->orderBy('name')->get();
        $availableHelpers = Helper::where('status', 'active')->orderBy('name')->get();
        $availableDestinations = WatsonDestination::select('destination_name', 'area')
            ->distinct()
            ->orderBy('destination_name')
            ->get();

        return view('watson.trips.index', compact('trips', 'destinations', 'trucks', 'drivers', 'helpers', 'availableTrucks', 'availableDrivers', 'availableHelpers', 'availableDestinations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dispatch_date' => 'required|date',
            'destination_id' => 'required|exists:watson_destinations,id',
            'truck_id' => 'required|exists:trucks,id',
            'driver_id' => 'required|exists:drivers,id',
            'trip_number' => 'required|integer|min:1|max:3',
            'remarks' => 'nullable',
        ]);

        $trip =  WatsonTrip::create([
            'dispatch_date' => $data['dispatch_date'],
            'destination_id' => $data['destination_id'],
            'truck_id' => $data['truck_id'],
            'driver_id' => $data['driver_id'],
            'trip_number' => $data['trip_number'], // 👈 ADD
            'remarks' => $data['remarks'] ?? null,
            'status' => 'Draft',
        ]);

        // Update truck, helper and driver status
        //$trip->truck->update(['status' => 'on_trip']);
        //$trip->driver->update(['availability_status' => 'on_trip']);
        if ($request->input('helper_1_id')) {
            $trip->helper()->createMany([
                ['helper_id' => $request->input('helper_1_id'), 'is_completed' => false],
            ]);
            //$trip->helper->update(['status' => 'on_trip']);
        }

        return back()->with('success', 'Trip created.');
    }

    public function assign($id)
    {
        $trip = WatsonTrip::with(['destination', 'truck', 'driver'])->findOrFail($id);

        if ($trip->status !== 'Draft') {
            return back()->with('error', 'Invalid status.');
        }

        $trip->update([
            'status' => 'Assigned',
            'assigned_at' => now(),
        ]);

        $company = 'Watsons';

        if ($trip->driver && $trip->driver->email) {
            // Mail::to($trip->driver->email)->send(new TripAssignedMail($trip, $trip->driver, $company));
        }

        return back()->with('success', 'Trip assigned and email sent.');
    }
    public function dispatch(Request $request, $id)
    {
        $request->validate([
            'trip_ticket_no' => 'required|unique:watson_trips,trip_ticket_no',
        ]);

        $trip = WatsonTrip::findOrFail($id);

        if ($trip->status !== 'Assigned') {
            return back()->with('error', 'Not ready.');
        }

        DB::transaction(function () use ($trip, $request) {
            $trip->update([
                'trip_ticket_no' => $request->trip_ticket_no,
                'status' => 'Dispatched',
                'dispatched_at' => now(),
            ]);

            $trip->truck->update(['status' => 'on_trip']);
            Driver::where('id', $trip->driver_id)->update(['availability_status' => 'on_trip']);
            // $trip->driver->update(['availability_status' => 'on_trip']);
            // Check if helper exists before updating status
            if ($trip->helper) {
                Helper::where('id', $trip->helper->helper_id)->update(['status' => 'on_trip']);
                // $trip->helper->update(['status' => 'on_trip']);
            }
        });

        return back()->with('success', 'Dispatched.');
    }

    public function deliver($id)
    {
        $trip = WatsonTrip::findOrFail($id);

        if ($trip->status !== 'Dispatched') {
            return back()->with('error', 'Invalid.');
        }

        DB::transaction(function () use ($trip) {
            $trip->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);

            $trip->truck->update(['status' => 'active']);
            $trip->driver->update(['availability_status' => 'available']);
        });

        return back()->with('success', 'Completed.');
    }

    public function update(Request $request, $id)
    {
        $trip = WatsonTrip::findOrFail($id);

        $data = $request->validate([
            'dispatch_date' => 'required|date',
            'destination_id' => 'required|exists:watson_destinations,id',
            'truck_id' => 'required|exists:trucks,id',
            'driver_id' => 'required|exists:drivers,id',
            'remarks' => 'nullable',
            'trip_number' => 'required|integer|min:1|max:3',
        ]);

        $trip->update($data);
        $trip->helper()->delete(); // Delete existing helpers
        $trip->helper()->createMany([
            ['helper_id' => $request->input('helper_1_id'), 'is_completed' => false],
        ]);
        return back()->with('success', 'Trip updated.');
    }

    public function destroy($id)
    {
        $trip = WatsonTrip::findOrFail($id);
        $trip->is_removed = true;
        $trip->save();
        return back()->with('success', 'Trip deleted successfully.');
    }

    public function destroyAll()
    {
        // optional: wag isama completed
        WatsonTrip::where('status', '!=', 'Completed')->where('is_removed', false)->update(['is_removed' => true]);

        return back()->with('success', 'All trips deleted.');
    }

    public function history(Request $request)
    {
        $q = $request->q;

        $trips = WatsonTrip::with(['driver', 'destination', 'truck'])->whereIn('status', ['Completed', 'Cancelled']);

        if ($q) {
            $trips->where(function ($w) use ($q) {
                $w->where('trip_ticket_no', 'like', "%$q%")
                    ->orWhereHas('driver', fn($d) => $d->where('name', 'like', "%$q%"))
                    ->orWhereHas('destination', fn($d) => $d->where('area', 'like', "%$q%"))
                    ->orWhereHas('truck', fn($t) => $t->where('plate_number', 'like', "%$q%"));
            });
        }

        $trips = $trips->latest()->paginate(10);

        return view('watson.trips.history', compact('trips'));
    }
}
