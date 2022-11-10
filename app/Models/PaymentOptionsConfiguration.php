<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentOptionsConfiguration extends Model
{
    protected $guarded = [];

    public function PaymentOption()
    {
        return $this->belongsTo(PaymentOption::class);
    }
}
