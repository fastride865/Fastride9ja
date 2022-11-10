<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HandymanCommission extends Model
{
    //
    public function Segment()
    {
        return $this->belongsTo(Segment:: class);
    }
    public function CountryArea()
    {
        return $this->belongsTo(CountryArea:: class);
    }
    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
