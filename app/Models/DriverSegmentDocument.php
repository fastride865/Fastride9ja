<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverSegmentDocument extends Model
{
    //
    public function Document()
    {
        return $this->belongsTo(Document::class);
    }
    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function Segment()
    {
        return $this->belongsTo(Segment::class);
    }
}
