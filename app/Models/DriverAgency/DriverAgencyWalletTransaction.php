<?php

namespace App\Models\DriverAgency;

use Illuminate\Database\Eloquent\Model;

class DriverAgencyWalletTransaction extends Model
{
    protected $guarded = [];

    public function DriverAgency()
    {
        return $this->belongsTo(DriverAgency::class);
    }
}
