<?php

namespace App\Http\Controllers\Helper;

class RewardPoint
{

    //Referral Module

    public static function giveReferralReward($sender, $type)
    {
//        if ($type == 1){
//            $reward_point_data = \App\Models\RewardPoint::where('merchant_id' , $sender->merchant_id)
//                ->where('country_area_id' , $sender->country_area_id)
//                ->where('active' , 1)->first();
//            if ($reward_point_data && $reward_point_data->referral_enable == 1) {
//                $sender->reward_points = $sender->reward_points + $reward_point_data->user_referral_reward;
//                $sender->save();
//            }
//        }
//        if ($type ==  2){
//            $reward_point_data = \App\Models\RewardPoint::where('merchant_id' , $sender->merchant_id)
//                ->where('country_area_id' , $sender->country_area_id)
//                ->where('active' , 1)->first();
//            if ($reward_point_data && $reward_point_data->referral_enable == 1) {
//                $sender->reward_points = $sender->reward_points + $reward_point_data->driver_referral_reward;
//                $sender->save();
//            }
//        }
        $reward_point_data = \App\Models\RewardPoint::where('merchant_id', $sender->merchant_id)
            ->where('country_area_id', $sender->country_area_id)
            ->where('active', 1)->first();
        if ($reward_point_data && $reward_point_data->referral_enable == 1) {
            if ($type == 1) {
                $sender->reward_points = $sender->reward_points + $reward_point_data->user_referral_reward;
                $sender->save();
            }
            if ($type == 2) {
                $sender->reward_points = $sender->reward_points + $reward_point_data->driver_referral_reward;
                $sender->save();
            }
        }
    }


    // tutu changes
    public static function giveUserReferralReward($user)
    {
        $reward_point_data = \App\Models\RewardPoint::where('merchant_id', $user->merchant_id)
            ->where('country_area_id', $user->country_area_id)
            ->where('active', 1)->first();
        if ($reward_point_data && $reward_point_data->referral_enable == 1) {
            $user->reward_points = $user->reward_points + $reward_point_data->user_referral_reward;
            $user->save();
        }
    }


    public static function giveDriverReferralReward($driver)
    {
        $reward_point_data = \App\Models\RewardPoint::where('merchant_id', $driver->merchant_id)
            ->where('country_area_id', $driver->country_area_id)
            ->where('active', 1)->first();
        if ($reward_point_data && $reward_point_data->referral_enable == 1) {
            $driver->reward_points = $driver->reward_points + $reward_point_data->driver_referral_reward;
            $driver->save();
        }
    }

    public static function giveUserRegistrationReward($user)
    {
        $reward_point_data = \App\Models\RewardPoint::where('merchant_id', $user->merchant_id)
            ->where('country_area_id', $user->country_area_id)
            ->where('active', 1)->first();
        if ($reward_point_data && $reward_point_data->registration_enable == 1) {
            $user->reward_points = $user->reward_points + $reward_point_data->user_registration_reward;
            $user->save();
        }
    }

    public static function incrementUserTripCount($user)
    {
        $reward_point_data = \App\Models\RewardPoint::where('merchant_id', $user->merchant_id)
            ->where('country_area_id', $user->country_area_id)
            ->where('active', 1)->first();
        if ($reward_point_data) {
            $user->use_reward_trip_count = $user->use_reward_trip_count + 1;
            if ($user->use_reward_trip_count == $reward_point_data->trips_count) {
//        $user->use_reward_count = $user->use_reward_count + 1;
                $user->usable_reward_points = $user->usable_reward_points + $reward_point_data->max_redeem;
                $user->use_reward_trip_count = 0;
            }
            $user->save();
        }
    }


    public static function giveDriverRegistrationReward($driver)
    {
        $reward_point_data = \App\Models\RewardPoint::where('merchant_id', $driver->merchant_id)
            ->where('country_area_id', $driver->country_area_id)
            ->where('active', 1)->first();
        if ($reward_point_data && $reward_point_data->registration_enable == 1) {
            $driver->reward_points = $driver->reward_points + $reward_point_data->driver_registration_reward;
            $driver->save();
        }
    }

//    public function getOfferDetails($referral_code,$merchant_id,$country_id, $type){
//        $where1 = ['country_id','=',$country_id];
//        $where2 = ['merchant_id','=',$merchant_id];
//        $where3 = ['delete_status','=',NULL];
//        $where4 = ['status', '=', 1];
//        // Type 1 for user and 2 for driver
//        switch ($type){
//            case '1':
//                if (ReferralSystem::where([['code_name','=',$referral_code],$where1,$where2,$where3])->exists()){
//                    $offer_details = ReferralSystem::where([['code_name','=',$referral_code],$where1,$where2,$where3,$where4])->whereIn('application',array(1,2,3))->first();
//                    $senderType = 0;
//                }elseif (User::where([['ReferralCode','=',$referral_code],$where1,$where2,['user_delete', '=', NULL]])->exists()) {
//                    $offer_details = ReferralSystem::where([['default_code', '=', 0], $where1,$where2,$where3,$where4])->whereIn('application', array(1, 3))->latest()->first();
//                    $senderType = 1;
//                }else{
//                    return false;
//                }
//               break;
//            case '2':
//                if (ReferralSystem::where([['code_name','=',$referral_code],$where1,$where2,$where3])->exists()){
//                    $offer_details = ReferralSystem::where([['code_name','=',$referral_code],$where1,$where2,$where3,$where4])->whereIn('application',array(1,2,3))->first();
//                    $senderType = 0;
//                }elseif (Driver::where([['driver_referralcode','=',$referral_code],['merchant_id','=',$merchant_id]])->exists()){
//                    $offer_details = ReferralSystem::where([['default_code','=',0],$where1,$where2,$where3,$where4])->whereIn('application',array(2,3))->latest()->first();
//                    $senderType = 2;
//                }else{
//                    return false;
//                }
//                break;
//        }
//        return array($offer_details,$senderType);
//    }
//
//    public function getSenderDetails($sender,$code,$country_id,$merchant_id){
//        switch ($sender){
//            case 1:
//                $sender_details = User::where([['ReferralCode','=',$code],['country_id','=',$country_id],['merchant_id','=',$merchant_id],['user_delete', '=', NULL]])->first();
//                return $sender_details;
//                break;
//            case 2:
//                $sender_details = Driver::where([['driver_referralcode','=',$code],['merchant_id','=',$merchant_id],['driver_delete','=',NULL]])->first();
//                return $sender_details;
//                break;
//            default:
//                return false;
//                break;
//        }
//    }
//
//    public function ReferralOffer($referOffer,$receiver_type,$refer_id,$sender_type,$refer_sender_id,$merchant_id)
//    {
//        $this->AddDiscount($merchant_id,$referOffer->id,$refer_id,$receiver_type,$refer_sender_id,$sender_type,$referOffer->offer_type, $referOffer->offer_value, 1,$referOffer->limit,$referOffer->no_of_limit,$referOffer->no_of_day,$referOffer->day_count,$referOffer->start_date,$referOffer->end_date,$referOffer->offer_applicable);
//    }
//
//    public function AddDiscount($merchant_id,$referral_offer_id,$user_id, $receiver_type, $sender_id, $sender_type, $referral_offer, $referral_offer_value, $referral_available,$limit,$limit_usage,$no_of_day,$day_count,$start_date,$end_date,$offer_applicable)
//    {
//        ReferralDiscount::create([
//            'referral_system_id' => $referral_offer_id,
//            'merchant_id' => $merchant_id,
//            'receiver_id' => $user_id,
//            'receiver_type' => $receiver_type,
//            'sender_id' => $sender_id,
//            'sender_type' => $sender_type,
//            'limit' => $limit,
//            'limit_usage' => $limit_usage,
//            'no_of_day' => $no_of_day,
//            'day_count' => $day_count,
//            'start_date' => $start_date,
//            'end_date' => $end_date,
//            'offer_applicable' => $offer_applicable,
//            'offer_type' => $referral_offer,
//            'offer_value' => $referral_offer_value,
//            'referral_available' => $referral_available,
//        ]);
//    }
}
