<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashback extends Model
{
    protected $guarded = [];
    protected $hidden = [];

    public function CashBackVehicles()
    {
        return $this->belongsToMany(VehicleType::class, 'cashback_details')->withPivot('service_type_id');
    }

    public function CashBackServices()
    {
        return $this->belongsToMany(ServiceType::class, 'cashback_details')->withPivot('vehicle_type_id');
    }

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }

    public function LangCashbackAnyUser()
    {
        return $this->hasOne(LangCashback::class)->where('type',1);

    }

    public function LangCashbackAnyDriver()
    {
        return $this->hasOne(LangCashback::class)->where('type',2);

    }

    public function LangCashbackSingleUser()
    {
        return $this->hasOne(LangCashback::class)->where([['locale', '=', \App::getLocale()],['type',1]]);
    }

    public function LangCashbackSingleDriver()
    {
        return $this->hasOne(LangCashback::class)->where([['locale', '=', \App::getLocale()],['type',2]]);
    }

    public function LangCashbacks()
    {
        return $this->hasMany(LangSubscriptionPack::class);
    }

    public function getUserMessageAttribute()
    {
        if (empty($this->LangCashbackSingleUser)) {
            return $this->LangCashbackAnyUser->app_message;
        }
        return $this->LangCashbackSingleUser->app_message;
    }

    public function getDriverMessageAttribute()
    {
        if (empty($this->LangCashbackSingleDriver)) {
            return $this->LangCashbackAnyDriver->app_message;
        }
        return $this->LangCashbackSingleDriver->app_message;
    }
}
