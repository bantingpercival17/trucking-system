<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TruckDestination extends Model
{
    protected $fillable = ['company_id', 'destination_code', 'store_name',  'area', 'truck_type', 'rate'];
    function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function destinationName()
    {
        $parts = [];

        if ($this->destination_code) {
            $parts[] = $this->destination_code;
        }

        if ($this->store_name) {
            $parts[] = $this->store_name;
        }

        $parts[] = $this->area;
        $parts[] = $this->truck_type;
        $parts[] = '₱' . number_format($this->rate, 2);

        return implode(' - ', $parts);
    }
    public function name()
    {
        $parts = [];

        if ($this->store_name) {
            $parts[] = $this->store_name;
        }

        $parts[] = $this->area;

        return implode(' - ', $parts);
    }
}
