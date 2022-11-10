<?php

namespace App\Models;
use DB;
use App;
use Illuminate\Database\Eloquent\Model;
use App\Models\BusinessSegment\BusinessSegment;

class MercadoToken extends Model
{
    protected $guarded = [];


    public function Merchant()
    {
       return $this->belongsTo(Merchant::class,'merchant_id');
    }

    public function Driver()
    {
        return $this->belongsTo(Driver::class,'driver_id');
    }
    public function BusinessSegment()
    {
        return $this->belongsTo(BussinessSegment::class,'business_segment_id');
    }

}
