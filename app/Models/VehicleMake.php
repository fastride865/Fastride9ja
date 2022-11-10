<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class VehicleMake extends Model
{
    protected $guarded = [];

    protected $hidden = ['LanguageVehicleMakeAny', 'LanguageVehicleMakeSingle'];

    public function LanguageVehicleMakeAny()
    {
        return $this->hasOne(LanguageVehicleMake::class);
    }

    public function LanguageVehicleMakeSingle()
    {
        return $this->hasOne(LanguageVehicleMake::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getVehicleMakeNameAttribute()
    {
        if (empty($this->LanguageVehicleMakeSingle)) {
            return $this->LanguageVehicleMakeAny->vehicleMakeName;
        }
        return $this->LanguageVehicleMakeSingle->vehicleMakeName;
    }

    public function getVehicleMakeDescriptionAttribute()
    {
        if (empty($this->LanguageVehicleMakeSingle)) {
            return $this->LanguageVehicleMakeAny->vehicleMakeDescription;
        }
        return $this->LanguageVehicleMakeSingle->vehicleMakeDescription;
    }

    public function getVehicleLinkAttribute()
    {
        return DriverVehicle::where([['vehicle_make_id', '=', $this->id]])->count();
    }
}
