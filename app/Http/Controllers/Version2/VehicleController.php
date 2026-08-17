<?php

namespace App\Http\Controllers\Version2;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    function index(Request $request)
    {
        $truckTypes = ['4W', '6W', 'AUV', '4WCV', 'L300'];
        $vehicles = Truck::when($request->filled('q'), function ($query) use ($request) {
            $q = $request->q;
            $query->where(function ($query) use ($q) {
                $query->where('plate_number', 'like', "%{$q}%")
                    ->orWhere('company_number', 'like', "%{$q}%");
            });
        })
            ->when($request->filled('type'), function ($query) use ($request, $truckTypes) {
                $type = strtoupper($request->type);
                if (in_array($type, $truckTypes)) {
                    $query->where('truck_type', $type);
                }
            })
            ->when($request->filled('sort'), function ($query) use ($request) {
                $sort = $request->sort;
                $direction = $request->get('direction', 'asc');
                if (in_array($sort, ['plate_number', 'company_number', 'truck_type'])) {
                    $query->orderBy($sort, $direction);
                }
            }, function ($query) {
                $query->orderBy('plate_number', 'asc');
            })
            ->select('id', 'plate_number', 'company_number', 'truck_type', 'status')
            ->paginate(15)->appends($request->all());
        $counts = Truck::select('truck_type', DB::raw('COUNT(*) as total'))->groupBy('truck_type')->pluck('total', 'truck_type');
        // Ensure every truck type exists, even if count is 0
        $counts = collect($truckTypes)->mapWithKeys(function ($type) use ($counts) {
            return [$type => $counts[$type] ?? 0];
        });
        //  return  compact('vehicles', 'truckTypes', 'counts');
        return view('version2.vehicles.index', compact('vehicles', 'truckTypes', 'counts'));
    }
    function store(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string|max:255|unique:trucks,plate_number',
            'company_number' => 'nullable|string|max:255',
            'truck_type' => 'required|string|in:4W,6W,AUV,4WCV,L300',
            'status' => 'required|string|in:active,inactive',
        ]);
        try {
            DB::table('trucks')->insert([
                'plate_number' => strtoupper($request->plate_number),
                'company_number' => $request->company_number,
                'truck_type' => strtoupper($request->truck_type),
                'status' => $request->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return back()->with('success', 'Truck/Vehicle added successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    function update($id, Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string|max:255|unique:trucks,plate_number,' . $id,
            'company_number' => 'nullable|string|max:255',
            'truck_type' => 'required|string|in:4W,6W,AUV,4WCV,L300',
            'status' => 'required|string|in:active,inactive',
        ]);
        try {
            DB::table('trucks')->where('id', $id)->update([
                'plate_number' => strtoupper($request->plate_number),
                'company_number' => $request->company_number,
                'truck_type' => strtoupper($request->truck_type),
                'status' => $request->status,
                'updated_at' => now(),
            ]);
            return back()->with('success', 'Truck/Vehicle updated successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
