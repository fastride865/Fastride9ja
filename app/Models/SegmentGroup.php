<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentGroup extends Model
{
    public function Segment()
    {
        return $this->hasMany(Segment::class);
    }
}
