<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//
//// tutu changes
class DriverReferralDiscount extends Model
{
  protected $guarded = [];
////  public $timestamps = false;
//
//  public function Driver()
//  {
//    return $this->belongsTo(Driver::class, 'driver_id');
//  }
//
////  public function SenderDriver()
////  {
////    return $this->belongsTo(Driver::class, 'referral_sender_id');
////  }
//
////  public function getSenderDetails($driver_id)
////  {
////    $refer = DriverReferralDiscount::where([['driver_id', '=', $driver_id], ['referral_sender_id', '!=', 0]])->get();
////    return $refer;
////  }
////
////  public function getSenderCount($driver_id)
////  {
////    $refer = DriverReferralDiscount::where([['driver_id', '=', $driver_id], ['referral_sender_id', '!=', 0]])->count();
////    return $refer;
////  }
}
