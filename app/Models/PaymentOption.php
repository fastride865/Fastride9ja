<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentOption extends Model
{
    protected $guarded = [];
    protected $hidden = array('pivot');

    public function PaymentOptionsConfiguration()
    {
        return $this->hasMany(PaymentOptionsConfiguration::class);
    }

    public function PaymentOptionConfiguration()
    {
        return $this->hasOne(PaymentOptionsConfiguration::class);
    }
}
