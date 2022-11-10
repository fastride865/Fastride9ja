<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentPriceCardDetail extends Model
{
    public $fillable = ['segment_price_card_id', 'service_type_id','amount','status'];
    public function ServiceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function SegmentPriceCard()
    {
        return $this->belongsTo(SegmentPriceCard::class);
    }
}
