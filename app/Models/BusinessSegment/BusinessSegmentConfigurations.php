<?php

namespace App\Models\BusinessSegment;

use Illuminate\Database\Eloquent\Model;

class BusinessSegmentConfigurations extends Model
{
    protected $guarded = [];

    public function BusinessSegment(){
        return $this->belongsTo(BusinessSegment::class);
    }
}