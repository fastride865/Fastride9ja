<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    protected $guarded = [];

    protected $hidden = ['LanguagePackageAny', 'LanguagePackageSingle','pivot'];

    public function PriceCard()
    {
        return $this->hasMany(PriceCard::class);
    }

    public function VehicleType()
    {
        return $this->hasMany(VehicleType::class);
    }

    public function ServiceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function LanguagePackageAny()
    {
        return $this->hasOne(LanguageServicePackage::class);
    }

    public function LanguagePackageSingle()
    {
        return $this->hasOne(LanguageServicePackage::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getPackageNameAttribute()
    {
        if (empty($this->LanguagePackageSingle)) {
            return $this->LanguagePackageAny->name;
        }
        return $this->LanguagePackageSingle->name;
    }

    public function getPackageDescriptionAttribute()
    {
        if (empty($this->LanguagePackageSingle)) {
            return $this->LanguagePackageAny->description;
        }
        return $this->LanguagePackageSingle->description;
    }

    public function getPackageTermsConditionsAttribute()
    {
        if (empty($this->LanguagePackageSingle)) {
            return $this->LanguagePackageAny->terms_conditions;
        }
        return $this->LanguagePackageSingle->terms_conditions;
    }
}
