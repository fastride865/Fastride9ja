<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailBooking extends Model
{
    protected $guarded = [];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }

    public function ServiceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function VehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function deliveryType()
    {
        return $this->belongsTo(DeliveryType :: class);
    }

    public function Merchant()
    {
        return $this->belongsTo(Merchant :: class);
    }

}
