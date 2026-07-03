<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TruckDocument extends Model
{
    protected $guarded = [];

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function personalVehicle()
    {
        return $this->belongsTo(PersonalVehicle::class, 'personal_vehicle_id');
    }
}
