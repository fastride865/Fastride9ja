<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingParameterValue extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function Parameter()
    {
        return $this->belongsTo(PricingParameter::class);
    }
}
