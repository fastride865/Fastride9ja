<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverActivePack extends Model
{
    protected $guarded = [];
    protected $hidden =['SubscriptionPackage'];
    
//    public function Driver()
//    {
//        return $this->belongsTo(Driver::class);
//    }
//
//    public function PaymentMethod()
//    {
//        return $this->belongsTo(PaymentMethod::class);
//    }
//
//    public function SubscriptionPackage()
//    {
//        return $this->belongsTo(SubscriptionPackage::class,'subscription_pack_id');
//    }
}
