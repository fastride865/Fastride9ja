<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverCard extends Model
{
    protected $guarded = [];
    
     public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function PaymentOption()
    {
        return $this->belongsTo(PaymentOption::class);
    }
}
