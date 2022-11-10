<?php

namespace App\Models;

use App\Models\BusinessSegment\BusinessSegment;
use Illuminate\Database\Eloquent\Model;

class AdvertisementBanner extends Model
{
    //
    protected $guarded = [];
    public function BusinessSegment(){
        return $this->belongsTo(BusinessSegment::class);
    }

    public function Merchant(){
        return $this->belongsTo(Merchant::class);
    }

    public function Segment(){
        return $this->belongsTo(Segment::class);
    }
}
