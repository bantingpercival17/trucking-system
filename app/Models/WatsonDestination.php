<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatsonDestination extends Model
{
    protected $table = 'watson_destinations';

    protected $fillable = [
        'origin',
        'destination_name',
        'area',
        'truck_type',
        'rate',
        'remarks',
    ];
}
