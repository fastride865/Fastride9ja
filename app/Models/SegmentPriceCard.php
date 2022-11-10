<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentPriceCard extends Model
{
    public $fillable = ['driver_id', 'merchant_id', 'segment_id', 'service_type_id','country_area_id','amount','price_type','minimum_booking_amount','status'];
    public function ServiceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }
    public function Segment()
    {
        return $this->belongsTo(Segment::class);
    }
    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function SegmentPriceCardDetail()
    {
        return $this->hasMany(SegmentPriceCardDetail::class);
    }
}
