<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleOwnerDetail extends Model
{
    protected $guarded = [];
    
    public function DriverVehicleDetails()
    {
        return $this->belongsTo(DriverVehicle::class);
    }
}
