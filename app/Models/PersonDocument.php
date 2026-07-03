<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonDocument extends Model
{
    protected $fillable = ['person_id', 'person_type', 'type', 'expiry_date', 'file_path'];
}
