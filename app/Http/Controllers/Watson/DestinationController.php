<?php

namespace App\Http\Controllers\Watson;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WatsonDestination;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $query = WatsonDestination::query();

        // Search (destination_name, area, origin)
        if ($request->filled('q')) {
            $q = $request->q;

            $query->where(function ($query) use ($q) {
                $query->where('destination_name', 'like', "%{$q}%")
                    ->orWhere('area', 'like', "%{$q}%")
                    ->orWhere('origin', 'like', "%{$q}%");
            });
        }

        // Truck-type tab (6W / 4W / AUV / all)
        $type = $request->get('type');
        if ($type && in_array(strtoupper($type), ['6W', '4W', 'AUV'])) {
            $query->where('truck_type', strtoupper($type));
        }

        // Sorting
        $sort = $request->get('sort');
        $direction = $request->get('direction');

        if ($sort && in_array($sort, ['destination_name', 'area', 'truck_type', 'rate'])) {
            $query->orderBy($sort, $direction ?? 'asc');
        } else {
            $query->orderBy('destination_name');
        }

        $destinations = $query->paginate(15)->appends($request->all());

        $counts = [
            '6W' => WatsonDestination::where('truck_type', '6W')->count(),
            '4W' => WatsonDestination::where('truck_type', '4W')->count(),
            'AUV' => WatsonDestination::where('truck_type', 'AUV')->count(),
        ];

        return view('watson.destinations.index', compact('destinations', 'type', 'counts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'origin' => 'nullable|string|max:100',
            'destination_name' => 'nullable|string|max:255',
            //  'area' => 'nullable|string|max:255',
            'truck_type' => 'required|in:6W,4W,AUV',
            'rate' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        WatsonDestination::create($data);

        return redirect()->route('watson.destinations.index')->with('success', 'Destination added successfully.');
    }

    public function update(Request $request, $id)
    {
        $destination = WatsonDestination::findOrFail($id);

        $data = $request->validate([
            'origin' => 'nullable|string|max:100',
            'destination_name' => 'required|string|max:255',
            'area' => 'nullable|string|max:255',
            'truck_type' => 'required|in:6W,4W,AUV',
            'rate' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $destination->update($data);

        return redirect()->route('watson.destinations.index')->with('success', 'Destination updated successfully.');
    }

    public function destroy($id)
    {
        $destination = WatsonDestination::findOrFail($id);
        $destination->delete();

        return redirect()->route('watson.destinations.index')->with('success', 'Destination deleted successfully.');
    }
}
