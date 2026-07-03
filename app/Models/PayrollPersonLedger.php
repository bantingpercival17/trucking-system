<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPersonLedger extends Model
{
    protected $fillable = [
        'week_start',
        'week_end',
        'person_type',
        'person_id',
        'paid_amount',
        'advance_amount',
        'notes',
        'updated_by',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date',
        'paid_amount' => 'decimal:2',
        'advance_amount' => 'decimal:2',
    ];
}
