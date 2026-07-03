<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    // Use default primary key `id`
    protected $fillable = ['store_code', 'store_name', 'area', 'truck_type', 'rate', 'remarks'];
    
}
