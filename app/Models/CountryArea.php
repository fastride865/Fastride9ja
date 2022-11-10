<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class CountryArea extends Model
{
    protected $hidden = ['pivot', 'VehicleType', 'ServiceTypes', 'Country', 'Package', 'LanguageAny', 'LanguageSingle', 'Category'];

    protected $guarded = [];

    public function BillPeriod()
    {
        return $this->belongsTo(BillPeriod::class);
    }

    public function Country()
    {
        return $this->belongsTo(Country::class);
    }

    public function State()
    {
        return $this->belongsTo(State::class);
    }

    public function Town()
    {
        return $this->belongsTo(Town::class);
    }

//    public function Country_id($country_area_id)
//    {
//        $country_id = \DB::table('country_areas')->where('id', $country_area_id)->first();
//        return $country_id;
//    }

    public function VehicleType()
    {
        return $this->belongsToMany(VehicleType::class,'country_area_vehicle_type','country_area_id')->withPivot('service_type_id','segment_id')->orderBy('sequence', 'asc');
    }

    public function Package()
    {
        return $this->belongsToMany(Package::class)->withPivot('service_type_id');
    }

    public function ServiceTypes()
    {
        return $this->belongsToMany(ServiceType::class,'country_area_service_type','country_area_id')->withPivot('segment_id');
    }

    public function Documents()
    {
        return $this->belongsToMany(Document::class);
    }

    public function VehicleDocuments()
    {
        return $this->belongsToMany(Document::class, 'country_area_vehicle_document')->withPivot('vehicle_type_id');
    }

    public function PriceCard()
    {
        return $this->hasMany(PriceCard::class);
    }

    public function LanguageAny()
    {
        return $this->hasOne(LanguageCountryArea::class);
    }

    public function DemoConfiguration()
    {
        return $this->hasOne(DemoConfiguration::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(LanguageCountryArea::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function Categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function Category()
    {
        return $this->hasMany(Category::class);
    }

    public function getCountryAreaNameAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->AreaName;
        }
        return $this->LanguageSingle->AreaName;
    }

//    public function DeliveryVehicle()
//    {
//        return $this->belongsToMany(VehicleType::class, 'vehicle_type_country_area');
//    }

//    public function deliveryTypes()
//    {
//        return $this->belongsToMany(DeliveryType :: class);
//    }

    public function RestrictedArea()
    {
        return $this->hasOne(RestrictedArea::class);
    }
    public  function GeofenceAreaQueue(){
        return $this->belongsToMany(GeofenceAreaQueue :: class);
    }

    public function Segment()
    {
        return $this->belongsToMany(Segment::class)->withPivot('segment_id');
    }

    // document of segment specially for group 2 segments
    public function SegmentDocument()
    {
        return $this->belongsToMany(Document::class, 'country_area_segment_document','country_area_id')->withPivot('segment_id');
    }

    // payment method according to area
    public function PaymentMethod()
    {
        return $this->belongsToMany(PaymentMethod::class);
    }

    // payment method according to area
    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
