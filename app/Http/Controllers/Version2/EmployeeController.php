<?php

namespace App\Http\Controllers\Version2;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::where('is_deleted', false)
            ->orderBy('name')
            ->get();

        $employeeDrivers = $employees->where('type', 'driver');
        $employeeHelpers = $employees->where('type', 'helper');

        $driver = [
            'total' => $employeeDrivers,
            'active' => $employeeDrivers->where('is_active', true),
            'inactive' => $employeeDrivers->where('is_active', false),
            'available' => $employeeDrivers->where('is_active', true)
                ->where('is_available', true),
            'onTrip' => $employeeDrivers->where('is_active', true)
                ->where('is_available', false),
        ];

        $helper = [
            'total' => $employeeHelpers,
            'active' => $employeeHelpers->where('is_active', true),
            'inactive' => $employeeHelpers->where('is_active', false),
            'available' => $employeeHelpers->where('is_active', true)
                ->where('is_available', true),
            'onTrip' => $employeeHelpers->where('is_active', true)
                ->where('is_available', false),
        ];
        // return compact('driver', 'helper');
        $companies = Company::all();
        return view('version2.employee.index', compact('employees', 'driver', 'helper'));
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees|max:255',
            'type' => 'required|string',
            'birthday' => 'nullable|date',
            'contact_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'emergency_contact_person' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:50',
        ]);
        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'type' => $request->type,
                'contact_number' => $request->contact_number,
                'address' => $request->address,
                'birthday' => $request->birthday,
            ];
            if ($request->hasFile('profile_photo')) {
                $data['profile_photo'] = $request->file('profile_photo')->store('drivers', 'public');
            }
            Employee::create($data);
            return back()->with('success', 'Driver added.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids ?? [];

        if (!empty($ids)) {
            Employee::whereIn('id', $ids)->update(['is_deleted' => false]);
        }

        return back()->with('success', 'Selected drivers deleted.');
    }
}
