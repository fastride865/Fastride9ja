<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosRequest extends Model
{
    protected $guarded = [];

    public function Booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }
}
