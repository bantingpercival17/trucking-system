<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceRange extends Model
{
    protected $fillable = ['rate_from', 'rate_to', 'allowance'];
}
