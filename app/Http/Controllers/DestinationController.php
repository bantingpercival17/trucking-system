<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $q   = $request->get('q');
        $tab = $request->get('tab', '6w'); // '6w' or 'l300'

        $destinationsQuery = Destination::query();

        // ✅ Search (store_code, store_name, area, remarks)
        if ($q) {
            $destinationsQuery->where(function ($w) use ($q) {
                $w->where('store_code', 'like', "%{$q}%")
                    ->orWhere('store_name', 'like', "%{$q}%")
                    ->orWhere('area', 'like', "%{$q}%")
                    ->orWhere('remarks', 'like', "%{$q}%");
            });
        }

        // ✅ Pagination (separate page keys per tab)
        $perPage = 25; // adjust if you want

        $destinations6w = (clone $destinationsQuery)
            ->where('truck_type', '6W')
            ->orderBy('store_name')
            ->paginate($perPage, ['*'], 'page6w')
            ->withQueryString();

        $destinationsL300 = (clone $destinationsQuery)
            ->where('truck_type', 'L300')
            ->orderBy('store_name')
            ->paginate($perPage, ['*'], 'pageL300')
            ->withQueryString();

        return view('owner.destinations.index', compact(
            'destinations6w',
            'destinationsL300',
            'q',
            'tab'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_code' => ['required', 'string', 'max:50'],
            'store_name' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'truck_type' => ['required', 'in:6W,L300'],
            'rate' => ['required', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        Destination::create($data);

        return back()->with('success', 'Destination added!');
    }

    public function destroy(Destination $destination)
    {
        $destination->delete();

        return back()->with('success', 'Destination deleted.');
    }

    public function update(Request $request, Destination $destination)
    {
        $data = $request->validate([
            'store_code' => ['required', 'string', 'max:50'],
            'store_name' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'truck_type' => ['required', 'in:6W,L300'],
            'rate' => ['required', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
        ]);

        $destination->update($data);

        return back()->with('success', 'Destination updated.');
    }
}