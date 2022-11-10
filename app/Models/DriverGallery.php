<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverGallery extends Model
{
    //
    public function Driver()
    {
        return $this->belongsTo(Driver:: class);
    }
    public function Segment()
    {
        return $this->belongsTo(Segment:: class);
    }
    public function HandymanOrder()
    {
        return $this->belongsTo(HandymanOrder:: class);
    }
}
