<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    protected $fillable = ['plate_number', 'deduction_type', 'date_paid', 'amount', 'receipt_image', 'remarks'];
}
