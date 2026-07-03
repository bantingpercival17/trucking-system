<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashDestination extends Model
{
    protected $table = 'flash_destinations';

    protected $fillable = [
        'hub_code',
        'area',
        'rate',
        'remarks',
    ];
}