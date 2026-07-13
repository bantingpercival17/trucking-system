<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripPayroll extends Model
{
    protected $fillable = ['dispatch_id', 'employee_id', 'gross_amount', 'allowance', 'deduction', 'net_amount', 'approved_at', 'paid_at'];

    function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    function dispatch()
    {
        return $this->belongsTo(DispatchDestinationTrip::class, 'dispatch_id');
    }
}
