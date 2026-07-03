<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashPayroll extends Model
{
    protected $fillable = ['driver_id', 'week_start', 'week_end', 'total_amount', 'paid_amount', 'status'];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
