<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceCardValue extends Model
{
    protected $guarded = [];
    protected $hidden = ['PricingParameter'];

    public function PricingParameter()
    {
        return $this->belongsTo(PricingParameter::class);
    }
    public function PriceCard()
    {
        return $this->belongsTo(PriceCard::class);
    }
}
