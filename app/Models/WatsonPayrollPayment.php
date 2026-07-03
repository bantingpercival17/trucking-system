<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatsonPayrollPayment extends Model
{
    protected $table = 'watson_payroll_payments';

    protected $fillable = [
        'driver_id',
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
        'paid_at',
    ];
}
