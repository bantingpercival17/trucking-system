<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    // Use default primary key `id`
    protected $fillable = ['plate_number',  'company_number', 'truck_type', 'status'];
    public function driver()
    {
        return $this->hasOneThrough(
            Driver::class,
            DispatchTrip::class,
            'truck_id', // Foreign key on DispatchTrip
            'id', // Foreign key on Driver
            'id', // Local key on Truck
            'driver_id', // Local key on DispatchTrip
        )->latest('dispatch_date');
    }

    // Relationship: DispatchTrips for this truck
    public function dispatchTrips()
    {
        return $this->hasMany(DispatchTrip::class, 'truck_id', 'id');
    }

    // Relationship: Expenses for this truck (by plate_number)
    public function expenses()
    {
        return $this->hasMany(\App\Models\Expense::class, 'plate_number', 'plate_number');
    }

    public function truck()
    {
        return $this->belongsTo(\App\Models\Truck::class);
    }

    public function documents()
    {
        return $this->hasMany(TruckDocument::class);
    }
}
// Relationship: latest assigned driver (via DispatchTrip)
