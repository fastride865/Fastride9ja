<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralDiscount extends Model
{
    protected $guarded = [];

//    public function User()
//    {
//        return $this->belongsTo(User::class, 'referral_user_id');
//    }

//    public function Sender($type)
//    {
//        if ($type == 1){
//            return $this->belongsTo(User::class, 'sender_id');
//        }else{
//            return $this->belongsTo(Driver::class, 'sender_id');
//        }
//    }

//    public function getSenderDetails($user_id)
//    {
//        $refer = ReferralDiscount::where([['referral_user_id', '=', $user_id], ['referral_sender_id', '!=', 0]])->get();
//        return $refer;
//    }
//
//    public function getSenderCount($user_id)
//    {
//        $refer = ReferralDiscount::where([['referral_user_id', '=', $user_id], ['referral_sender_id', '!=', 0]])->count();
//        return $refer;
//    }

    public function getReferralSystem(){
        return $this->belongsTo(ReferralSystem::class,'referral_system_id');
    }
}
