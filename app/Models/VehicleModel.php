<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    protected $guarded = [];

    public function VehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function VehicleMake()
    {
        return $this->belongsTo(VehicleMake::class);
    }

    public function LanguageVehicleModel()
    {
        return $this->hasMany(LanguageVehicleModel::class);
    }

    public function LanguageVehicleModelAny()
    {
        return $this->hasOne(LanguageVehicleModel::class);
    }

    public function LanguageVehicleModelSingle()
    {
        return $this->hasOne(LanguageVehicleModel::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getVehicleModelNameAttribute()
    {
        if (empty($this->LanguageVehicleModelSingle)) {
            return $this->LanguageVehicleModelAny->vehicleModelName;
        }
        return $this->LanguageVehicleModelSingle->vehicleModelName;
    }

    public function getVehicleModelDescriptionAttribute()
    {
        if (empty($this->LanguageVehicleModelSingle)) {
            return $this->LanguageVehicleModelAny->vehicleModelDescription;
        }
        return $this->LanguageVehicleModelSingle->vehicleModelDescription;
    }
}
