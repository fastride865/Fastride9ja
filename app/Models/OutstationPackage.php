<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class OutstationPackage extends Model
{
    protected $guarded = [];

    public function LanguageAny()
    {
        return $this->hasOne(OutstationPackageTranslation::class);
    }
    public function ServiceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(OutstationPackageTranslation::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getPackageNameAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->city;
        }
        return $this->LanguageSingle->city;
    }

    public function getPackageDescriptionAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->description;
        }
        return $this->LanguageSingle->description;
    }
}
