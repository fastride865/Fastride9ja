<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverSegmentRating extends Model
{
    //
    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
