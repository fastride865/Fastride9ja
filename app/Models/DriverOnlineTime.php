<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverOnlineTime extends Model
{
    protected $guarded = [];

    protected $casts = [
        'time_intervals' => 'array',
    ];

    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
