<?php

namespace App\Http\Controllers;

use App\Models\DispatchTrip;
use App\Models\Destination;
use App\Models\Truck;
use App\Models\Driver;
use App\Models\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\TripAssignedMail;

class DispatchTripController extends Controller
{
    public function assign($dispatch_trip_id)
    {
        $trip = DispatchTrip::with(['destination', 'truck', 'driver', 'helpers'])->findOrFail($dispatch_trip_id);

        if ($trip->status !== 'Draft') {
            return back()->with('error', 'Only Draft trips can be assigned.');
        }

        $trip->status = 'Assigned';
        $trip->assigned_at = now();
        $trip->save();

        $driver = Driver::find($trip->driver_id);

        $company = 'Chamonix';

        if ($driver && $driver->email) {
            Mail::to($driver->email)->send(new TripAssignedMail($trip, $driver, $company));
        }

        foreach ($trip->helpers as $helper) {
            if ($helper->email) {
                Mail::to($helper->email)->send(new TripAssignedMail($trip, $helper, $company));
            }
        }


        return back()->with('success', 'Trip assigned and email sent.');
    }

    public function index(Request $request)
    {
        $q = $request->get('q'); // search text
        $sort = $request->get('sort', 'dispatch_date');
        $dir = $request->get('dir', 'desc');

        // ✅ allowlist sorting columns (safe)
        $allowedSorts = ['trip_ticket_no', 'dispatch_date', 'status', 'dispatched_at', 'id'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'dispatch_date';
        }
        if (!in_array($dir, ['asc', 'desc'], true)) {
            $dir = 'desc';
        }

        // ✅ Trips query
        $tripsQuery = DispatchTrip::with(['destination', 'truck', 'driver', 'helpers'])->whereNotIn('status', ['Completed']);

        // ✅ Search (adjust fields if needed)
        if ($q) {
            $tripsQuery->where(function ($w) use ($q) {
                $w->where('trip_ticket_no', 'like', "%{$q}%")
                    ->orWhere('status', 'like', "%{$q}%")
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

        // ✅ Sort + Pagination (10 per page)

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        // SEARCH
        if ($request->filled('q')) {
            $q = $request->q;
            $tripsQuery->where(function ($qq) use ($q) {
                $qq->where('trip_ticket_no', 'like', "%{$q}%");
                // add more fields here if needed
            });
        }

        // SORT (your existing logic)
        $trips = $tripsQuery
            ->orderByRaw(
                "
                    CASE
                WHEN status = 'Draft' THEN 1
                WHEN status = 'Dispatched' THEN 2
                WHEN status = 'Completed' THEN 3
                WHEN status = 'Cancelled' THEN 4
                    END
                    ",
            )
            ->orderByDesc('dispatch_date')
            ->paginate($perPage)
            ->withQueryString();

        // ✅ Data for "Create Trip" modal (your existing)
        $destinations = Destination::orderBy('store_name')->get();
        $trucks = Truck::where('status', 'active')->get();
        $drivers = Driver::where('status', 'active')->get();
        $helpers = Helper::where('status', 'active')->get();

        // ✅ OPTIONAL: for the “Available” cards list
        // (Change logic depending on your meaning of "available")
        $availableTrucks = Truck::where('status', 'active')->get();
        $availableDrivers = Driver::where('availability_status', 'available')->get();
        $availableHelpers = Helper::where('availability_status', 'available')->get();

        return view('owner.trips.index', compact('trips', 'destinations', 'trucks', 'drivers', 'helpers', 'q', 'sort', 'dir', 'availableTrucks', 'availableDrivers', 'availableHelpers'));
    }

    public function create()
    {
        $destinations = Destination::orderBy('store_name')->get();
        $trucks = Truck::where('status', 'active')->orderBy('plate_number')->get();
        $drivers = Driver::where('availability_status', 'available')->orderBy('name')->get();
        $helpers = Helper::where('availability_status', 'available')->orderBy('name')->get();

        return view('dispatch_trips.create', compact('destinations', 'trucks', 'drivers', 'helpers'));
    }

    public function update(Request $request, $dispatch_trip_id)
    {
        $trip = DispatchTrip::findOrFail($dispatch_trip_id);

        if ($trip->status !== 'Draft') {
            return back()->with('error', 'Only Draft trips can be edited.');
        }

        $trip->update([
            'trip_ticket_no' => $request->trip_ticket_no,
            'dispatch_date' => $request->dispatch_date,
            'truck_id' => $request->truck_id,
            'driver_id' => $request->driver_id,
            'remarks' => $request->remarks,
        ]);

        // ✅ FIX: update helpers
        $helperIds = [];

        if ($request->helper_1_id) {
            $helperIds[] = $request->helper_1_id;
        }

        if ($request->helper_2_id) {
            $helperIds[] = $request->helper_2_id;
        }

        $helperIds = array_unique($helperIds);

        // 🔥 IMPORTANT: sync para ma-update pivot table
        $trip->helpers()->sync($helperIds);

        return back()->with('success', 'Trip updated.');
    }

    public function edit($dispatch_trip_id)
    {
        $trip = DispatchTrip::findOrFail($dispatch_trip_id);

        $destinations = Destination::orderBy('store_name')->get();
        $trucks = Truck::where('status', 'active')->get();
        $drivers = Driver::where('availability_status', 'available')->get();
        $helpers = Helper::where('availability_status', 'available')->get();

        return view('owner.trips.edit', compact('trip', 'destinations', 'trucks', 'drivers', 'helpers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dispatch_date' => 'required|date',
            'destination_id' => 'required|exists:destinations,id',
            'truck_id' => 'required|exists:trucks,id',
            'driver_id' => 'required|exists:drivers,id',
            'helper_ids' => 'nullable|array|max:2',
            'helper_ids.*' => 'exists:helpers,id',
            'remarks' => 'nullable',
        ]);

        $destination = Destination::find($data['destination_id']);
        $rate = $destination?->rate ?? null;

        $trip = DispatchTrip::create([
            'dispatch_date' => $data['dispatch_date'],
            'destination_id' => $data['destination_id'],
            'truck_id' => $data['truck_id'],
            'driver_id' => $data['driver_id'],
            'remarks' => $data['remarks'] ?? null,
            'rate_snapshot' => $rate,
            'status' => 'Draft',
        ]);
        $helperIds = [];

        if ($request->helper_1_id) {
            $helperIds[] = $request->helper_1_id;
        }

        if ($request->helper_2_id) {
            $helperIds[] = $request->helper_2_id;
        }

        $helperIds = array_unique($helperIds);
        $trip->helpers()->sync($helperIds);
        // Audit log
        return back()->with('success', 'Trip saved as Draft');
    }

    public function show($dispatch_trip_id)
    {
        $trip = DispatchTrip::with(['destination', 'truck', 'driver', 'helpers'])
            ->where('id', $dispatch_trip_id)
            ->firstOrFail();

        return view('dispatch_trips.show', compact('trip'));
    }

    public function dispatch(Request $request, $dispatch_trip_id)
    {
        $request->validate([
            'trip_ticket_no' => 'required|string|max:50|unique:dispatch_trips,trip_ticket_no',
        ]);

        $trip = DispatchTrip::findOrFail($dispatch_trip_id);

        if ($trip->status !== 'Assigned') {
            return back()->with('error', 'Trip not ready to dispatch.');
        }

        DB::transaction(function () use ($trip, $request) {
            $trip->trip_ticket_no = $request->trip_ticket_no;
            $trip->status = 'Dispatched';
            $trip->dispatched_at = now();
            $trip->save();

            $trip->truck->update(['status' => 'on_trip']);

            $table = (new Driver())->getTable();

            if (Schema::hasColumn($table, 'availability_status')) {
                $trip->driver->update([
                    'availability_status' => 'on_trip',
                ]);
            } else {
                $trip->driver->update([
                    'status' => 'on_trip',
                ]);
            }

            foreach ($trip->helpers as $h) {
                if (Schema::hasColumn((new Helper())->getTable(), 'availability_status')) {
                    $h->update(['availability_status' => 'on_trip']);
                } else {
                    $h->update(['status' => 'on_trip']);
                }
            }
        });

        return back()->with('success', 'Trip dispatched.');
    }

    public function deliver($dispatch_trip_id)
    {
        $trip = DispatchTrip::where('id', $dispatch_trip_id)->firstOrFail();
        if ($trip->status !== 'Dispatched') {
            return back()->with('error', 'Only Dispatched trips can be completed.');
        }
        DB::transaction(function () use ($trip) {
            $truck = Truck::where('id', $trip->truck_id)->lockForUpdate()->firstOrFail();
            $driver = Driver::where('id', $trip->driver_id)->lockForUpdate()->firstOrFail();
            $helperIds = $trip->helpers()->pluck('helpers.id')->toArray();
            $helpers = Helper::whereIn('id', $helperIds)->lockForUpdate()->get();
            $trip->status = 'Completed';
            $trip->completed_at = now();
            $trip->save();
            $truck->status = 'active';
            $truck->save();

            if (Schema::hasColumn($driver->getTable(), 'availability_status')) {
                $driver->availability_status = 'available';
                $driver->status = 'active';
            } else {
                $driver->status = 'active';
            }
            $driver->save();

            foreach ($helpers as $h) {
                if (Schema::hasColumn($h->getTable(), 'availability_status')) {
                    $h->availability_status = 'available';
                    $h->status = 'active';
                } else {
                    $h->status = 'active';
                }
                $h->save();
            }
        });
        // Audit log
        return back()->with('success', 'Trip completed.');
    }

    public function cancel($dispatch_trip_id)
    {
        $trip = DispatchTrip::where('id', $dispatch_trip_id)->firstOrFail();
        if (!in_array($trip->status, ['Draft', 'Dispatched'], true)) {
            return back()->with('error', 'Trip can only be cancelled from Draft or Dispatched.');
        }
        DB::transaction(function () use ($trip) {
            $truck = Truck::where('id', $trip->truck_id)->lockForUpdate()->firstOrFail();
            $driver = Driver::where('id', $trip->driver_id)->lockForUpdate()->firstOrFail();
            $helperIds = $trip->helpers()->pluck('helpers.id')->toArray();
            $helpers = Helper::whereIn('id', $helperIds)->lockForUpdate()->get();
            $trip->status = 'Cancelled';
            $trip->save();
            // Release if they are On Trip
            if ($truck->status === 'on_trip') {
                $truck->status = 'active';
                $truck->save();
            }
            if ($driver->status === 'on_trip' || Schema::hasColumn($driver->getTable(), 'availability_status')) {
                if (Schema::hasColumn($driver->getTable(), 'availability_status')) {
                    $driver->availability_status = 'available';
                }
                $driver->status = 'active';
                $driver->save();
            }
            foreach ($helpers as $h) {
                if ($h->status === 'on_trip' || Schema::hasColumn($h->getTable(), 'availability_status')) {
                    if (Schema::hasColumn($h->getTable(), 'availability_status')) {
                        $h->availability_status = 'available';
                    }
                    $h->status = 'active';
                    $h->save();
                }
            }
        });
        // Audit log
        return back()->with('success', 'Trip cancelled.');
    }

    public function destroy($dispatch_trip_id)
    {
        if (!in_array(auth()->user()->role, ['owner', 'it', 'secretary'])) {
            abort(403);
        }

        $trip = DispatchTrip::with(['truck', 'driver', 'helpers'])->findOrFail($dispatch_trip_id);

        DB::transaction(function () use ($trip) {
            // ✅ IF naka on_trip (Dispatched), ibalik sila sa available
            if ($trip->status === 'Dispatched') {
                // 🚚 Truck
                if ($trip->truck && $trip->truck->status === 'on_trip') {
                    $trip->truck->update(['status' => 'active']);
                }

                // 👤 Driver
                if ($trip->driver) {
                    if (Schema::hasColumn($trip->driver->getTable(), 'availability_status')) {
                        $trip->driver->update([
                            'availability_status' => 'available',
                            'status' => 'active',
                        ]);
                    } else {
                        $trip->driver->update(['status' => 'active']);
                    }
                }

                // 👷 Helpers
                foreach ($trip->helpers as $h) {
                    if (Schema::hasColumn($h->getTable(), 'availability_status')) {
                        $h->update([
                            'availability_status' => 'available',
                            'status' => 'active',
                        ]);
                    } else {
                        $h->update(['status' => 'active']);
                    }
                }
            }

            // ✅ DELETE TRIP
            $trip->delete();
        });

        return back()->with('success', 'Trip deleted and resources released.');
    }

    public function toggleBilling(Request $request, $dispatch_trip_id)
    {
        $trip = DispatchTrip::where('id', $dispatch_trip_id)->firstOrFail();

        $trip->billing_status = $trip->billing_status === 'Billed' ? 'Burn' : 'Billed';
        $trip->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['billing_status' => $trip->billing_status]);
        }

        return back();
    }

    public function history(Request $request)
    {
        $q = $request->get('q');

        $tripsQuery = DispatchTrip::with(['destination', 'truck', 'driver', 'helpers'])->whereIn('status', ['Completed', 'Cancelled']);

        // Billing filter
        $billingStatus = $request->get('billing_status');

        if (!empty($billingStatus)) {
            if ($billingStatus === 'Unbilled') {
                $tripsQuery->where(function ($q) {
                    $q->whereNull('billing_status')->orWhere('billing_status', 'Unbilled');
                });
            } else {
                $tripsQuery->where('billing_status', $billingStatus);
            }
        }

        // Search
        if ($q) {
            $tripsQuery->where(function ($w) use ($q) {
                $w->where('trip_ticket_no', 'like', "%{$q}%")

                    ->orWhereDate('check_release_date', $q)

                    ->orWhere('check_release_date', 'like', "%{$q}%")

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

        // Page rows
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        // Unbilled first
        $trips = $tripsQuery
            ->orderByRaw(
                "
            CASE
                WHEN billing_status IS NULL THEN 1
                WHEN billing_status = 'Unbilled' THEN 1
                WHEN billing_status = 'Pending' THEN 2
                WHEN billing_status = 'Billed' THEN 3
                ELSE 4
            END
        ",
            )
            ->orderByDesc('dispatch_date')
            ->paginate($perPage)
            ->withQueryString();

        foreach ($trips as $trip) {
            $driverPaid = DB::table('payroll_payments')->where('person_type', 'driver')->where('person_id', $trip->driver_id)->whereDate('week_start', '<=', $trip->dispatch_date)->whereDate('week_end', '>=', $trip->dispatch_date)->exists();

            $helperIds = $trip->helpers->pluck('id');

            $helpersPaidCount = DB::table('payroll_payments')->where('person_type', 'helper')->whereIn('person_id', $helperIds)->whereDate('week_start', '<=', $trip->dispatch_date)->whereDate('week_end', '>=', $trip->dispatch_date)->distinct('person_id')->count('person_id');

            $helpersTotal = $helperIds->count();

            if ($helpersTotal === 0) {
                $trip->computed_payment_status = $driverPaid ? 'Paid' : 'Unpaid';
            } else {
                if ($driverPaid && $helpersPaidCount === $helpersTotal) {
                    $trip->computed_payment_status = 'Paid';
                } elseif ($driverPaid || $helpersPaidCount > 0) {
                    $trip->computed_payment_status = 'Partial';
                } else {
                    $trip->computed_payment_status = 'Unpaid';
                }
            }
        }

        return view('owner.trips.history', compact('trips'));
    }

    public function complete(Request $request, $dispatch_trip_id)
    {
        $request->validate([
            'check_release_date' => 'nullable|date',
            'bank_name' => 'nullable|string|max:100',
            'check_number' => 'nullable|string|max:100',
        ]);

        $trip = DispatchTrip::findOrFail($dispatch_trip_id);

        $trip->billing_status = 'Billed';

        // Only set check details if provided
        if ($request->filled('check_release_date')) {
            $trip->check_release_date = $request->check_release_date;
        }
        if ($request->filled('bank_name')) {
            $trip->bank_name = $request->bank_name;
        }
        if ($request->filled('check_number')) {
            $trip->check_number = $request->check_number;
        }

        $trip->save();

        return redirect()->back()->with('success', 'Trip marked as billed.');
    }
}
