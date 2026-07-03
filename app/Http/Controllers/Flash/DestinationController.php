<?php

namespace App\Http\Controllers\Flash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlashDestination;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $query = FlashDestination::query();

        // 🔍 Search
        if ($request->filled('q')) {
            $q = $request->q;

            $query->where(function ($query) use ($q) {
                $query->where('hub_code', 'like', "%{$q}%")->orWhere('area', 'like', "%{$q}%");
            });
        }

        // 🔽 SORTING (ADD THIS)
        $sort = $request->get('sort');
        $direction = $request->get('direction');

        if ($sort && in_array($sort, ['hub_code', 'area', 'rate'])) {
            $query->orderBy($sort, $direction ?? 'asc');
        } else {
            $query->latest(); // default state
        }

        $destinations = $query->paginate(10)->appends($request->all());

        return view('flash.destinations.index', compact('destinations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hub_code' => 'required',
            'area' => 'nullable',
            'rate' => 'required|numeric',
            'remarks' => 'nullable',
        ]);

        FlashDestination::create([
            'hub_code' => $request->hub_code,
            'area' => $request->area,
            'rate' => $request->rate,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('flash.destinations.index')->with('success', 'Destination added successfully.');
    }

    public function update(Request $request, $id)
    {
        $destination = FlashDestination::findOrFail($id);

        $request->validate([
            'hub_code' => 'required',
            'area' => 'nullable',
            'rate' => 'required|numeric',
            'remarks' => 'nullable',
        ]);

        $destination->update([
            'hub_code' => $request->hub_code,
            'area' => $request->area,
            'rate' => $request->rate,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('flash.destinations.index')->with('success', 'Destination updated successfully.');
    }

    public function destroy($id)
    {
        $destination = FlashDestination::findOrFail($id);
        $destination->delete();

        return redirect()->route('flash.destinations.index')->with('success', 'Destination deleted successfully.');
    }
}
