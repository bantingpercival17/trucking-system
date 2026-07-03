<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    // Use default primary key `id`
    protected $fillable = ['name',
    'email',
    'status',
    'profile_photo',
    'birthday',
    'contact_number',
    'address',
    'emergency_contact_person',
    'emergency_contact_number','availability_status'];

     protected $casts = [
        'birthday' => 'date',
    ];

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? asset('storage/' . $this->profile_photo) : asset('assets/images/page-img/14.png');
    }

    public function documents()
    {
        return $this->hasMany(DriverDocument::class);
    }
}
