<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchDestinationTrip extends Model
{

    protected $fillable = [
        'company_id',
        'destination_id',
        'driver_id',
        'helper_first_id',
        'helper_second_id',
        'dispatch_date',
        'truck_id',
        'assigned_at',
        'dispatch_status',
        'dispatched_at',
        'trip_ticket_no',
        'is_deleted',
        'completed_at',
        'billing_status',
        'check_release_date'
    ];
    protected $casts = [
        'dispatch_date' => 'date',
        'assigned_at' => 'date',
        'dispatched_at' => 'date',
        'completed_at' => 'date',
    ];
    function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    function destination()
    {
        return $this->belongsTo(TruckDestination::class, 'destination_id');
    }
    function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }
    function helper1()
    {
        return $this->belongsTo(Employee::class, 'helper_first_id');
    }
    function helper2()
    {
        return $this->belongsTo(Employee::class, 'helper_second_id');
    }
    function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }
}
