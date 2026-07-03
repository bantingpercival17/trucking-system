<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expenses';
    protected $fillable = ['date', 'time', 'plate_number', 'debit', 'receipt_surrendered', 'remarks', 'liters', 'start_odometer', 'odometer', 'distance', 'km_per_liter', 'type', 'receipt_image', 'billed'];
    // Add casts if needed
    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'liters' => 'float',
        'start_odometer' => 'integer',
        'odometer' => 'integer',
        'distance' => 'float',
        'km_per_liter' => 'float',
    ];
}
