<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPackage extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $hidden = ['LangSubscriptionPackageAny','LangSubscriptionPackageSingle','PackageDuration'];

    public function LangSubscriptionPackageAny()
    {
        return $this->hasOne(LangSubscriptionPack::class);

    }

    public function LangSubscriptionPackageSingle()
    {
        return $this->hasOne(LangSubscriptionPack::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function LangPackages()
    {
        return $this->hasMany(LangSubscriptionPack::class);
    }

    public function PackageDuration()
    {
        return $this->BelongsTo(PackageDuration::class)->with('LangPackageDurationSingleApi');
    }

    public function ServiceType()
    {
        return $this->belongsToMany(ServiceType::class,null,'subscription_pack_id');
    }
    
    public function CountryArea()
    {
        return $this->belongsToMany(CountryArea::class,null,'subscription_pack_id');
    }
    
    public function getNameAttribute()
    {
        if (empty($this->LangSubscriptionPackageSingle)) {
            return $this->LangSubscriptionPackageAny->name;
        }
        return $this->LangSubscriptionPackageSingle->name;
    }

    public function getDescriptionAttribute()
    {
        if (empty($this->LangSubscriptionPackageSingle)) {
            return $this->LangSubscriptionPackageAny->description;
        }
        return $this->LangSubscriptionPackageSingle->description;
    }

    public function PackageServiceType()
    {
        return $this->belongsToMany(ServiceType::class,'service_type_subscription_package','subscription_pack_id');
    }

    public function PackageCountryArea()
    {
        return $this->belongsToMany(CountryArea::class,'country_area_subscription_package','subscription_pack_id');
    }

    public function Merchant()
    {
        return $this->BelongsTo(Merchant::class);
    }
}
