<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatsonTripHelpers extends Model
{
    protected $table = 'watson_trip_helpers';

    protected $fillable = [
        'dispatch_trip_id',
        'helper_id',
        'is_completed',
    ];

    public function dispatchTrip()
    {
        return $this->belongsTo(WatsonTrip::class, 'dispatch_trip_id');
    }

    public function helper()
    {
        return $this->belongsTo(Helper::class, 'helper_id');
    }
}
