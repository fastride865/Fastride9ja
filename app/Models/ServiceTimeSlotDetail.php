<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTimeSlotDetail extends Model
{
    function ServiceTimeSlot()
    {
        return $this->belongsTo(ServiceTimeSlot::class);
    }
    function HandymanOrder()
    {
        return $this->hasMany(HandymanOrder::class);
    }
    function Driver()
    {
        return $this->belongsToMany(Driver::class,'driver_service_time_slot_detail','service_time_slot_detail_id')->withPivot('segment_id');
    }
}
