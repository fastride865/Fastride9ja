<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoConfiguration extends Model
{
    protected $guarded = [];

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }

    public function VehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function VehicleMake()
    {
        return $this->belongsTo(VehicleMake::class);
    }

    public function VehicleModel()
    {
        return $this->belongsTo(VehicleModel::class);
    }
}
