<?php

namespace App\Models\BusinessSegment;

use App\Models\Merchant;
use Illuminate\Database\Eloquent\Model;

class BusinessSegmentCashout extends Model
{
    protected $guarded = [];

    public function BusinessSegment(){
        return $this->belongsTo(BusinessSegment::class);
    }

    public function Merchant(){
        return $this->belongsTo(Merchant::class);
    }
}
