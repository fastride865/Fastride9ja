<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $guarded = [];

    protected $hidden = ['LanguageVehicleTypeSingle', 'LanguageVehicleTypeAny', 'pivot'];

//    public function Country_Area_Vehicle_Type()
//    {
//        return $this->hasMany(CountryAreaVehicleType::class);
//    }

    public function PriceCard()
    {
        return $this->hasMany(PriceCard::class);
    }

//    public function CountryAreaVehicleType()
//    {
//        return $this->belongsToMany(CountryAreaVehicleType::class,'country_area_vehicle_type','vehicle_type_id')->withPivot('area_id','service_type_id','segment_id')->orderBy('sequence', 'asc');
//        //return $this->hasMany(CountryAreaVehicleType::class);
//    }

    public function LanguageVehicleTypeAny()
    {
        return $this->hasOne(LanguageVehicleType::class);
    }

    public function LanguageVehicleTypeSingle()
    {
        return $this->hasOne(LanguageVehicleType::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getVehicleTypeNameAttribute()
    {
        if (empty($this->LanguageVehicleTypeSingle)) {
            return $this->LanguageVehicleTypeAny->vehicleTypeName;
        }
        return $this->LanguageVehicleTypeSingle->vehicleTypeName;
    }

    public function getVehicleTypeDescriptionAttribute()
    {
        if (empty($this->LanguageVehicleTypeSingle)) {
            return $this->LanguageVehicleTypeAny->vehicleTypeDescription;
        }
        return $this->LanguageVehicleTypeSingle->vehicleTypeDescription;
    }

    public function getVehicleLinkAttribute()
    {
        return DriverVehicle::where([['vehicle_type_id', '=', $this->id]])->count();
    }

//    public function DeliveryType()
//    {
//        return $this->belongsTo(DeliveryType::class);
//    }

    public function CountryArea()
    {
        return $this->belongsToMany(CountryArea::class,'country_area_vehicle_type','vehicle_type_id');
    }
    public function Category()
    {
        return $this->belongsToMany(Category::class)->withPivot('country_area_id','service_type_id','segment_id');
    }
}
