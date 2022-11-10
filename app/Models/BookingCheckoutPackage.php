<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingCheckoutPackage extends Model
{
    protected $guarded = [];
    protected $hidden = [];
    public $timestamps = false;

    public function unit()
    {
        return $this->belongsTo(WeightUnit :: class);
    }

    public function deliveryType()
    {
        return $this->belongsTo(DeliveryType :: class);
    }

    public function good()
    {
        return $this->belongsTo(Good :: class);
    }


}
