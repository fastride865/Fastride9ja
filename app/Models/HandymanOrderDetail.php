<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HandymanOrderDetail extends Model
{
    //
    public function SegmentPriceCard()
    {
        return $this->belongsTo(SegmentPriceCard::class);
    }
    public function ServiceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
    public function HandymanOrder()
    {
        return $this->belongsTo(HandymanOrder::class);
    }
}
