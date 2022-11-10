<?php

namespace App\Models;

use Auth;
use App;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $hidden = array('pivot');

    public function VehicleType()
    {
        return $this->belongsToMany(VehicleType::class);
    }

    public function CountryAreaVehicleType()
    {
        return $this->hasMany(CountryAreaVehicleType::class);
    }

    public function PriceCard()
    {
        return $this->hasMany(PriceCard::class);
    }

    public function DriverOnline()
    {
        return $this->belongsToMany(Driver:: class,'driver_online','service_type_id')->withPivot('segment_id','driver_vehicle_id');
    }

    public function ServiceTranslation()
    {
        $locale = App::getLocale();
        return $this->hasOne(ServiceTranslation::class, 'service_type_id')
            ->where(function ($q) use ($locale) {
                $q->where('locale', $locale);
                $q->orWhere('locale', '!=', NULL);
            });
    }

    // using in home Controller API
    public function ServiceApplication($merchant_id)
    {
        $service = $this->hasOne(ServiceTranslation::class, 'service_type_id')->where([['locale', '=', App::getLocale()], ['merchant_id', '=', $merchant_id]]);
        if (!empty($service->first())) {

            return $service->first()->name;
        }
    }

    public function ServiceTypeConfiguratoin($merchant_id)
    {
        return $this->hasOne(merchantServiceTypeConfiguration::class)->where([['merchant_id', '=', $merchant_id]])->first();
    }

    public function ServiceName($merchant_id = NULL)
    {
        $locale = App::getLocale();
        $service = $this->hasOne(ServiceTranslation::class, 'service_type_id')
            ->where(function ($q) use ($locale) {
                $q->where('locale', $locale);
//                $q->orWhere('locale', '!=', NULL);
            })
            ->where([['merchant_id', '=', $merchant_id]])->first();
        if(empty($service))
        {
            $service = $this->hasOne(ServiceTranslation::class, 'service_type_id')
                ->where(function ($q) use ($locale) {
//                    $q->where('locale', $locale);
                    $q->where('locale', '!=', NULL);
                })
                ->where([['merchant_id', '=', $merchant_id]])->first();
        }
        if (!empty($service)) {
            return $service->name;
        }
        return $this->serviceName;
    }

    public function ServiceDescription($merchant_id = NULL)
    {
        $locale = App::getLocale();
        $service = $this->hasOne(ServiceTranslation::class, 'service_type_id')
            ->where(function ($q) use ($locale) {
                $q->where('locale', $locale);
//                $q->orWhere('locale', '!=', NULL);
            })
            ->where([['merchant_id', '=', $merchant_id]])->first();
        if(empty($service))
        {
            $service = $this->hasOne(ServiceTranslation::class, 'service_type_id')
                ->where(function ($q) use ($locale) {
//                    $q->where('locale', $locale);
                    $q->where('locale', '!=', NULL);
                })
                ->where([['merchant_id', '=', $merchant_id]])->first();
        }
        if (!empty($service)) {
            return $service->description;
        }
        return "";
    }

    public function Driver()
    {
        return $this->belongsToMany(Driver:: class,'driver_service_type','service_type_id')->withPivot('segment_id');
    }

    public function SegmentPriceCardDetail()
    {
        return $this->hasOne(SegmentPriceCardDetail::class);
    }
    public function MerchantServiceType()
    {
        return $this->belongsToMany(Merchant::class,'merchant_service_type','service_type_id')->withPivot('segment_id','sequence','service_icon')->orderBy('segment_id');
    }

    public function Merchant()
    {
        return $this->belongsToMany(Merchant::class,'merchant_service_type','service_type_id')->withPivot('segment_id','sequence','service_icon')->orderBy('segment_id');
    }

    public function CountryArea()
    {
        return $this->belongsToMany(ServiceType::class,'country_area_service_type','service_type_id')->withPivot('segment_id');
    }

    public function Segment()
    {
        return $this->belongsTo(Segment::class);
    }
}
