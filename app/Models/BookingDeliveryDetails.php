<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingDeliveryDetails extends Model
{
    protected $guarded = [];

    public function Booking()
    {
        return $this->belongsTo(Booking::class);
    }

}
