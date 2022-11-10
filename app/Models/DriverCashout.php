<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverCashout extends Model
{
    protected $guarded = [];

    public function Driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
