<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchTrip extends Model
{
    protected $table = 'dispatch_trips';
    // Use default primary key `id`

    protected $fillable = ['trip_ticket_no', 'dispatch_date', 'dispatch_time', 'destination_id', 'truck_id', 'driver_id', 'helper_id', 'rate_snapshot', 'status', 'remarks', 'dispatched_at', 'dispatched_by', 'completed_at', 'completed_by', 'billing_status', 'payment_status'];

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id', 'id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id');
    }

    public function helper()
    {
        return $this->belongsTo(Helper::class, 'helper_id', 'id');
    }
    protected $casts = [
        'dispatch_date' => 'date',
        'check_release_date' => 'date',
        'dispatched_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Billing status accessor (defaults to 'Unbilled').
    // If you later add a `billing_status` column or a relation to invoices,
    // you can update this accessor to return the real value.
    public function getBillingStatusAttribute()
    {
        $val = $this->attributes['billing_status'] ?? null;
        if (empty($val)) {
            return 'Burn';
        }
        return ucfirst($val);
    }

    // Payment status accessor (defaults to 'Unpaid').
    public function getPaymentStatusAttribute()
    {
        $val = $this->attributes['payment_status'] ?? null;
        if (empty($val)) {
            return 'Unpaid';
        }
        return ucfirst($val);
    }

    // app/Models/DispatchTrip.php
    // app/Models/DispatchTrip.php
    public function helpers()
    {
        return $this->belongsToMany(
            \App\Models\Helper::class,
            'dispatch_trip_helpers', // pivot table name
            'dispatch_trip_id', // pivot FK to trips
            'helper_id', // pivot FK to helpers
        )->withTimestamps();
    }

    public function destroyAll()
    {
        // optional: only allow owner/admin middleware already
        \App\Models\DispatchTrip::query()->delete();

        return redirect()->route('owner.trips.index')->with('success', 'All trips deleted successfully.');
    }
}
