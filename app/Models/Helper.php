<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Helper extends Model
{
    // Use default primary key `id`
    protected $fillable = ['name', 'email', 'status', 'availability_status', 'profile_photo'];

    // app/Models/Helper.php
    public function trips()
    {
        return $this->belongsToMany(\App\Models\DispatchTrip::class, 'dispatch_trip_helpers', 'helper_id', 'dispatch_trip_id')->withTimestamps();
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? asset('storage/' . $this->profile_photo) : asset('assets/images/page-img/14.png');
    }

    public function documents()
    {
        return $this->hasMany(HelperDocument::class);
    }
}
