<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverSubscriptionRecord extends Model
{
    protected $guarded = [];
    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function PackageDuration()
    {
        return $this->belongsTo(PackageDuration::class);
    }
    
     public function PaymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function SubscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class,'subscription_pack_id');
    }
}
