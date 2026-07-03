<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPayment extends Model
{
    protected $fillable = [
    'person_type',
    'person_id',
    'week_start',
    'week_end',
    'total_trips',

    'amount',

    'advance_amount',
    'advance_deducted',
    'balance_advance',

    'final_amount',
    'payment_mode',
    'transaction_id',
    'released_by',
    'paid_at',
];


}
