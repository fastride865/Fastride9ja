<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BusinessSegment\Order;

class BookingTransaction extends Model
{
    protected $hidden =[];
    protected $guarded =[];

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
