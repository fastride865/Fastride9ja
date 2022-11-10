<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletCouponCode extends Model
{
    protected $guarded = [];

    public function Country()
    {
        return $this->belongsTo(Country::class);
    }
}
