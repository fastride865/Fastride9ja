<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRequestDriver extends Model
{
    protected $guarded = [];

    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function Booking()
    {
        return $this->belongsTo(Booking::class);
    }
    public function Order()
    {
        return $this->belongsTo(Order::class);
    }
    public function HandymanOrder()
    {
        return $this->belongsTo(HandymanOrder::class);
    }
}
