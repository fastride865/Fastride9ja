<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoolRideList extends Model
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
}
