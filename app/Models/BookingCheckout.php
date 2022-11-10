<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingCheckout extends Model
{
    protected $guarded = [];
    protected $hidden = ['VehicleType', 'CountryArea', 'PaymentMethod', 'User'];

    public function VehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function PaymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function PromoCode()
    {
        return $this->belongsTo(PromoCode::class, 'promo_code');
    }

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }

    public function PriceCard()
    {
        return $this->belongsTo(PriceCard::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function packages()
    {
        return $this->hasMany(BookingCheckoutPackage::class);
    }

    public function Merchant(){
        return $this->belongsTo(Merchant::class);
    }
    public function ServiceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
    public function Segment()
    {
        return $this->belongsTo(Segment::class);
    }

    public function DeliveryCheckoutDetail()
    {
        return $this->hasMany(DeliveryCheckoutDetail::class);
    }
}
