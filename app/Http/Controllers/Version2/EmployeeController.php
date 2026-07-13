<?php

namespace App\Http\Controllers\Version2;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employeeQuery = Employee::orderBy('name')->where('is_deleted', false);
        $employees = (clone $employeeQuery)->get();
        $employeeDrivers = (clone $employeeQuery)->where('type', 'driver')->get();
        $employeeHelpers = (clone $employeeQuery)->where('type', 'helpers')->get();
        $availableDrivers = (clone $employeeQuery)->where('type', 'driver')->where('is_available', true)->get();
        $availableHelpers = (clone $employeeQuery)->where('type', 'helpers')->where('is_available', true)->get();
        return view('version2.employee.index', compact('employees', 'employeeDrivers', 'employeeHelpers', 'availableDrivers', 'availableHelpers'));
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
        //
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
}
