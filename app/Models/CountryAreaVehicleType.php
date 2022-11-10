<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryAreaVehicleType extends Model
{
    protected $guarded = [];

    public function ServiceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function VehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function Package()
    {
        return $this->belongsTo(Package::class);
    }
}
