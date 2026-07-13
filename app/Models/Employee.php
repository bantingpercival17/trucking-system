<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'email', 'type', 'phone', 'address', 'date_of_birth', 'license_number', 'profile_picture', 'is_available', 'is_active'];

    public function payrollList(array $data)
    {
        return $this->hasMany(TripPayroll::class, 'employee_id')
            ->select('trip_payrolls.*')
            ->join('dispatch_destination_trips', 'dispatch_destination_trips.id', 'trip_payrolls.dispatch_id')
            ->whereBetween('trip_payrolls.created_at', $data)
            ->where('trip_payrolls.is_deleted', 0)
            ->whereNull('trip_payrolls.paid_at')
            ->where('dispatch_destination_trips.dispatch_status', 'Completed')
            ->orderBy('dispatched_at', 'asc');
    }
}
