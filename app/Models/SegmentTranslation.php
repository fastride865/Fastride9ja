<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentTranslation extends Model
{
    protected $fillable = ['merchant_id','segment_id','name','locale','created_at','updated_at'];
}

