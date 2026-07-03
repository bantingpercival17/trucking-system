<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalVehicle extends Model
{
    protected $fillable = [
        'plate_number',
        'vehicle_name'
    ];

    public function documents()
    {
        return $this->hasMany(TruckDocument::class, 'personal_vehicle_id');
    }
}