<?php

namespace App\Http\Controllers\Version2;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Truck;
use App\Models\TruckDestination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($company, Request $request)
    {
        $company = Company::where('name', $company)->first();
        // return $company;
        $query = TruckDestination::query();
        $truckTypes = ['4W', '6W', 'AUV', '4WCV', 'L300'];
        // Search ( area, origin)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($query) use ($q) {
                $query->where('area', 'like', "%{$q}%")
                    ->orWhere('store_name', 'like', "%{$q}%")
                    ->orWhere('destination_code', 'like', "%{$q}%");
            });
        }
        // Truck-type tab (6W / 4W / AUV / all)
        $type = $request->get('type');
        if ($type && in_array(strtoupper($type), $truckTypes)) {
            $query->where('truck_type', strtoupper($type));
        }

        // Sorting
        $sort = $request->get('sort');
        $direction = $request->get('direction');

        if ($sort && in_array($sort, ['area', 'truck_type', 'rate'])) {
            $query->orderBy($sort, $direction ?? 'asc');
        } else {
            $query->orderBy('area');
        }

        if ($company) {
            $query->where('company_id', $company->id);
        }
        $destinations = $query->where('is_deleted', false)->orderBy('updated_at', 'desc')->paginate(15)->appends($request->all());

        $query = TruckDestination::whereIn('truck_type', $truckTypes)
            ->selectRaw('truck_type, COUNT(*) as total');
        if ($company) {
            $query->where('company_id', $company->id);
        }
        $counts = $query->groupBy('truck_type')
            ->pluck('total', 'truck_type');
        // Ensure every truck type exists, even if count is 0
        $counts = collect($truckTypes)->mapWithKeys(function ($type) use ($counts) {
            return [$type => $counts[$type] ?? 0];
        });
        $companyList = Company::all();
        return view('version2.destinations.index', compact('destinations', 'type', 'counts', 'truckTypes', 'companyList'));
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
        $data = $request->validate([
            'company_id' => 'nullable|max:100',
            'destination_code' => 'nullable|string|max:100',
            'store_name' => 'nullable|string|max:100',
            'area' => 'required|string|max:255',
            'truck_type' => 'required',
            'rate' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);
        try {
            //return $data;
            $data = array_map(function ($value) {
                return is_string($value) ? strtoupper($value) : $value;
            }, $data);
            TruckDestination::create($data);
            return back()->with('success', 'Destination added successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
            return $th->getMessage();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id, int $destination, Request $request)
    {

        try {
            $data = $request->validate([
                'company_id' => 'nullable|max:100',
                'destination_code' => 'nullable|string|max:100',
                'store_name' => 'nullable|string|max:100',
                'area' => 'required|string|max:255',
                'truck_type' => 'required',
                'rate' => 'required|numeric|min:0',
                'remarks' => 'nullable|string',
            ]);
            $destination = TruckDestination::findOrFail($destination);
            $data = array_map(function ($value) {
                return is_string($value) ? strtoupper($value) : $value;
            }, $data);
            $destination->update($data);
            return back()->with('success', 'Destination updated successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Update Destination: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, $destination)
    {
        // return $destination;
        try {
            $destination = TruckDestination::findOrFail($destination);
            $destination->is_deleted = true;
            $destination->save();
            return back()->with('success', 'Destination deleted successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    function destinationList($data)
    {
        $data = TruckDestination::where('company_id', $data)
            ->get()
            ->map(function ($destination) {
                return [
                    'id' => $destination->id,
                    'name' => $destination->destinationName(),
                ];
            });

        return response()->json(compact('data'));
    }
    function truckList($data)
    {
        $destination = TruckDestination::find($data);
        //return $destination;
        $data = Truck::where('status', 'active')
            ->when($destination->truck_type === 'AUV', function ($query) {
                $query->whereIn('truck_type', ['AUV', 'L300']);
            }, function ($query) use ($destination) {
                $query->where('truck_type', $destination->truck_type);
            })
            ->get()
            ->map(function ($destination) {
                return [
                    'id' => $destination->id,
                    'name' => strtoupper($destination->plate_number . ' - ' . $destination->truck_type),
                ];
            });

        return response()->json(compact('data'));
    }
}
