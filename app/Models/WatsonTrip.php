<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatsonTrip extends Model
{
    protected $table = 'watson_trips';

    protected $fillable = [
        'dispatch_date',
        'destination_id',
        'truck_id',
        'driver_id',
        'trip_number',
        'remarks',
        'status',
        'trip_ticket_no',
        'payment_status',
        'billing_status',
        'check_release_date',
        'bank_name',
        'check_number',
        'is_removed'
    ];

    protected $casts = [
        'dispatch_date' => 'date',
        'check_release_date' => 'date',
        'assigned_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function destination()
    {
        return $this->belongsTo(WatsonDestination::class, 'destination_id');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
    function helper()
    {
        return $this->hasOne(WatsonTripHelpers::class, 'dispatch_trip_id');
    }


    function tripPayroll()
    {
        return $this->hasOne(WatsonTripPayroll::class, 'dispatch_trip_id');
    }
}
