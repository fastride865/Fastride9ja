<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BusinessSegment\BusinessSegment;
class ProductCart extends Model
{
    //
    public function PriceCard()
    {
        return $this->belongsTo(PriceCard::class);
    }
    public function User()
    {
        return $this->belongsTo(User::class);
    }
    public function BusinessSegment()
    {
        return $this->belongsTo(BusinessSegment::class);
    }
    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
    public function Segment()
    {
        return $this->belongsTo(Segment::class);
    }
}
