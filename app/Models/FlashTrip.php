<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashTrip extends Model
{
    protected $fillable = ['dispatch_date', 'destination_id', 'truck_id', 'driver_id', 'remarks', 'trip_number', 'status', 'trip_ticket_no'];

    public function destination()
    {
        return $this->belongsTo(FlashDestination::class, 'destination_id');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
