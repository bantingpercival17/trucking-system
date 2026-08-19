<?php

namespace App\Http\Controllers\Version2;

use App\Http\Controllers\Controller;
use App\Models\AllowanceRange;
use App\Models\Company;
use App\Models\DispatchDestinationTrip;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\WatsonTrip;
use App\Models\WatsonDestination;
use App\Models\Truck;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use App\Models\Helper;
use App\Models\TripPayroll;
use App\Models\TruckDestination;

class DispatchTripController extends Controller
{
    public function index($company, Request $request)
    {
        $dispatchList = DispatchDestinationTrip::where('is_deleted', false)
            ->when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('company_id', $companyModel->id);
                }
            })
            ->whereNull('completed_at')
            ->latest()
            ->paginate(10);
        // Monitoring and for Dispatch Forms
        $trucks = Truck::where('status', 'active')->get();
        $helpers = Employee::where('type', 'helper')->where('is_available', true)->orderBy('name')->get();
        $drivers = Employee::where('type', 'driver')->where('is_available', true)->orderBy('name')->get();
        $destinations = TruckDestination::select('id', 'destination_code',  'store_name', 'truck_type', 'area', 'rate')
            ->when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('company_id', $companyModel->id);
                }
            })
            ->distinct()
            ->orderBy('area')
            ->get();
        $company = Company::all();
        return view('version2.dispatch-trip.index', compact('dispatchList', 'trucks', 'drivers', 'helpers', 'destinations', 'company'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "company" => 'required',
            "dispatch_date" => "required",
            "destination" => "required",
            "truck" => "required",
            "driver" => "required",
            "helper1" => "nullable",
            "helper2" => "nullable",
            "remarks" => 'nullable'
        ]);
        try {
            //code...
            DispatchDestinationTrip::create([
                'company_id' => $data['company'],
                'destination_id' => $data['destination'],
                'truck_id' => $data['truck'],
                'driver_id' => $data['driver'],
                'helper_first_id' => $data['helper1'],
                'helper_second_id' => $data['helper2'],
                'dispatch_date' => $data['dispatch_date'],
                'dispatch_status' => 'Draft'
            ]);
            return back()->with('success', 'Dispatch Trip created.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function assign($id)
    {
        try {
            $dispatch = DispatchDestinationTrip::findOrFail($id);
            // Check Driver Available Alert
            $dispatch->update([
                'dispatch_status' => 'Assigned',
                'assigned_at' => now(),
            ]);
            return back()->with('success', 'Trip assigned and email sent.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    public function dispatch(Request $request, $id)
    {
        $request->validate([
            'trip_ticket_no' => 'required',
        ]);

        try {
            $dispatch = DispatchDestinationTrip::findOrFail($id);
            if ($dispatch->dispatch_status !== 'Assigned') {
                return back()->with('error', 'Not ready.');
            }
            // Check Driver Available Alert

            // Truck Status
            $dispatch->truck->update(['status' => 'on_trip']);
            // Driver Status and Payroll
            $dispatch->driver->update(['is_available' => false]);
            $this->employeeDispatchPay($dispatch->driver, $dispatch);
            // Helpers Status and Payroll
            if (!empty($dispatch->helper1)) {
                $dispatch->helper1->update(['is_available' => false]);
                $this->employeeDispatchPay($dispatch->helper1, $dispatch);
            }

            if (!empty($dispatch->helper2)) {
                $dispatch->helper2->update(['is_available' => false]);
                $this->employeeDispatchPay($dispatch->helper2, $dispatch);
            }
            $dispatch->update([
                'trip_ticket_no' => $request->trip_ticket_no,
                'dispatch_status' => 'Dispatched',
                'dispatched_at' => now(),
            ]);
            return back()->with('success', 'Trip assigned and email sent.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }

        return back()->with('success', 'Dispatched.');
    }
    function employeeDispatchPay($employee, $dispatch)
    {
        // Allowance
        $destinationRate = $dispatch->destination->rate;
        $allowanceRecord = AllowanceRange::where('rate_from', '<=', $destinationRate)
            ->where('rate_to', '>=', $destinationRate)
            ->first();

        // Fallback to 0 (or a default value) if the rate doesn't match any database ranges
        $allowance = $allowanceRecord ? $allowanceRecord->allowance : 0;
        $percentageRate = [.11, .08];
        $fixRate = [700, 500, 1200];
        // Flash no Helper
        $rate = 0;
        $helperCount = collect([$dispatch->helper_first_id, $dispatch->helper_second_id])
            ->filter() // Removes null, 0, false, or empty strings
            ->count();
        if ($employee->type === 'driver') {
            // Driver Rate Calculation
            //  no Helper
            if ($dispatch->company_id == 3) {
                $rate = $helperCount > 0 ? $fixRate[0] : $fixRate[2];
            } else {
                if ($dispatch->company_id == 2) {
                    $rate = $dispatch->destination->rate;
                } else {
                    $rate = $dispatch->destination->rate * $percentageRate[0];
                }
            }
        } else {
            // Helper Rate Calculation
            if ($dispatch->company_id == 3) {
                $rate = $fixRate[1];
            } else {
                $rate = $dispatch->destination->rate * $percentageRate[1];
            }
            $rate = $helperCount > 1 ? ($rate / 2) : $rate;
            $allowance = $helperCount > 1 ? ($allowance / 2) : $allowance;
        }
        TripPayroll::firstOrCreate([
            'dispatch_id' => $dispatch->id,
            'employee_id' => $employee->id
        ], [
            'gross_amount' => $rate,
            'allowance' => $dispatch->company_id != 2 ? $allowance : 0,
        ]);
    }
    public function deliver($id)
    {
        try {
            $dispatch = DispatchDestinationTrip::findOrFail($id);
            if ($dispatch->dispatch_status !== 'Dispatched') {
                return back()->with('error', 'Not ready.');
            }
            // Truck Status
            $dispatch->truck->update(['status' => 'active']);
            // Driver Status and Payroll
            $dispatch->driver->update(['is_available' => true]);
            // Helpers Status and Payroll
            if (!empty($dispatch->helper1)) {
                $dispatch->helper1->update(['is_available' => false]);
            }
            if (!empty($dispatch->helper2)) {
                $dispatch->helper2->update(['is_available' => false]);
            }
            $dispatch->update([
                'dispatch_status' => 'Completed',
                'completed_at' => now(),
            ]);
            return back()->with('success', 'Trip Delivered Completed');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }

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
        ///*  */ return $request;
        $data = $request->validate([
            "company" => 'required',
            "dispatch_date" => "required",
            "destination" => "required",
            "truck" => "required",
            "driver" => "required",
            "helper1" => "nullable",
            "helper2" => "nullable",
            "trip_number" => "required",
            "remarks" => 'nullable'
        ]);
        try {
            $dispatch = DispatchDestinationTrip::findOrFail($id);
            $dispatch->update([
                'company_id' => $data['company'],
                'destination_id' => $data['destination'],
                'truck_id' => $data['truck'],
                'driver_id' => $data['driver'],
                'helper_first_id' => $data['helper1'] ?? null,
                'helper_second_id' => $data['helper2'] ?? null,
                'dispatch_date' => $data['dispatch_date'],
                'dispatch_status' => 'Draft'
            ]);
            return back()->with('success', 'Dispatch Trip Updated.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($id)
    {
        $trip = DispatchDestinationTrip::findOrFail($id);
        $trip->is_deleted = true;
        $trip->save();
        return back()->with('success', 'Dispatch Trip Deleted Successfully.');
    }

    public function destroyAll()
    {
        // optional: wag isama completed
        DispatchDestinationTrip::where('dispatch_status', '!=', 'Completed')->where('is_deleted', false)->update(['is_deleted' => true]);

        return back()->with('success', 'All trips deleted.');
    }
    public function show($company, Request $request)
    {

        $dispatchList = DispatchDestinationTrip::where('is_deleted', false)
            ->when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();
                if ($companyModel) {
                    $query->where('company_id', $companyModel->id);
                }
            })
            ->whereIn('dispatch_status', ['Completed', 'Cancelled'])
            ->latest()
            ->paginate(10);
        //return $dispatchList;
        $trips = DispatchDestinationTrip::whereIn('dispatch_status', ['Completed', 'Cancelled']);
        /*  return $trips;
        if ($q) {
            $trips->where(function ($w) use ($q) {
                $w->where('trip_ticket_no', 'like', "%$q%")
                    ->orWhereHas('driver', fn($d) => $d->where('name', 'like', "%$q%"))
                    ->orWhereHas('destination', fn($d) => $d->where('area', 'like', "%$q%"))
                    ->orWhereHas('truck', fn($t) => $t->where('plate_number', 'like', "%$q%"));
            });
        } */

        $trips = $trips->latest()->paginate(10);
        return view('version2.dispatch-trip.history', compact('trips', 'dispatchList'));
    }
    public function updateBilling(Request $request, $id)
    {
        $trip = DispatchDestinationTrip::findOrFail($id);

        $trip->check_release_date = $request->check_release_date;
        $trip->bank_name = $request->bank_name;
        $trip->check_number = $request->check_number;

        if ($request->filled('check_release_date') && $request->filled('bank_name') && $request->filled('check_number')) {
            $trip->billing_status = true;
        } elseif ($request->filled('check_release_date') || $request->filled('bank_name') || $request->filled('check_number')) {
            $trip->billing_status = false;
        }
        $trip->save();
        return back()->with('success', 'Billing updated.');
    }
    public function showBilling($company, Request $request)
    {
        $query = DispatchDestinationTrip::where('is_deleted', false)
            ->when($company, function ($query, $company) {
                $companyModel = Company::where('name', $company)->first();

                if ($companyModel) {
                    $query->where('company_id', $companyModel->id);
                }
            })
            ->whereIn('dispatch_status', ['Completed']);

        $dispatchList = (clone $query)
            ->when($request->q, function ($query, $code) {
                $query->where('trip_ticket_no', $code);
            })
            ->when($request->status, function ($query, $status) {

                if ($status !== 'Unbilled') {
                    $status = [
                        'Billed' => 1,
                        'Pending' => 0,
                    ][$status] ?? null;
                    $query->where('billing_status', $status);
                } else {
                    $query->whereNull('billing_status');
                }
            })
            ->when($request->check_date, function ($query, $check_date) {
                $query->where('check_release_date', $check_date);
            })
            ->latest()
            ->paginate($request->per_page ?? 10)
            ->withQueryString();


        $billingQuery = (clone $query)->where('billing_status', 1);
        $pendingQuery = (clone $query)->where('billing_status', 0);
        $unbillingQuery = (clone $query)->whereNull('billing_status');
        $dashboardData = [
            'billedTrips' => $billingQuery->get()->sum(function ($trip) {
                return $trip->destination->rate ?? 0;
            }),
            'billedTripsCount' => $billingQuery->count(),
            'pendingTrips' => $pendingQuery->get()->sum(function ($trip) {
                return $trip->destination->rate ?? 0;
            }),
            'pendingTripsCount' => $pendingQuery->count(),
            'unbilledTrips' => $unbillingQuery->get()->sum(function ($trip) {
                return $trip->destination->rate ?? 0;
            }),
            'unbilledTripsCount' => $unbillingQuery->count(),
        ];
        $dashboardData['totalTrips'] = (clone $query)->count();
        $dashboardData['totalBilledAmount'] = (clone $query)->where('billing_status', 1)->get()->sum(function ($trip) {
            return $trip->destination->rate ?? 0;
        });

        //return compact('dispatchList', 'dashboardData');
        return view('version2.dispatch-trip.billing', compact('dispatchList', 'dashboardData'));
    }
}
