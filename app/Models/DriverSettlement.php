<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverSettlement extends Model
{
    protected $guarded = [];

    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
