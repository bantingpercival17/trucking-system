<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    protected $table = 'payroll_periods';

    protected $fillable = [
        'week_start',
        'week_end',
        'drivers_total',
        'helpers_total',
        'grand_total',
        'is_paid',
        'paid_at',
        'paid_by',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end'   => 'date',
        'paid_at'    => 'datetime',
        'is_paid'    => 'boolean',
    ];

    // Who marked it as paid
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by', 'id');
    }
}
