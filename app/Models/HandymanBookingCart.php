<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BusinessSegment\BusinessSegment;

class HandymanBookingCart extends Model
{
    //
    public function SegmentPriceCard()
    {
        return $this->belongsTo(SegmentPriceCard::class);
    }
    public function User()
    {
        return $this->belongsTo(User::class);
    }
    public function Segment()
    {
        return $this->belongsTo(Segment::class);
    }
    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
