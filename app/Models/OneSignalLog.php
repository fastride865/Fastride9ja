<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OneSignalLog extends Model
{
    protected $fillable = ['booking_id,merchant_id,total_request_sent,recipients,failed_driver_id,success_driver_id'];
    public function booking()
    {
        return $this->belongsTo(Booking :: class);
    }
    public function merchant()
    {
        return $this->belongsTo(Merchant :: class);
    }
}


