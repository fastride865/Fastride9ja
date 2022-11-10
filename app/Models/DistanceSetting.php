<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistanceSetting extends Model
{
    protected $guarded = [];

    public function Method()
    {
        return $this->belongsTo(DistanceMethod::class);
    }

    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}