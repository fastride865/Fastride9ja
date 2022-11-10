<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Helper\GetString;
use App\Models\Booking;
use App\Models\BusinessSegment\BusinessSegment;
use App\Models\BusinessSegment\BusinessSegmentWalletTransaction;
use App\Models\BusinessSegment\Order;
use App\Models\Corporate;
use App\Models\CorporateWalletTransaction;
use App\Models\Driver;
use App\Models\DriverAgency\DriverAgency;
use App\Models\DriverVehicle;
use App\Models\DriverWalletTransaction;
use App\Models\HandymanOrder;
use App\Models\Hotel;
use App\Models\HotelWalletTransaction;
use App\Models\Onesignal;
use App\Models\TaxiCompaniesWalletTransaction;
use App\Models\TaxiCompany;
use App\Models\User;
use App\Models\UserWalletTransaction;
use App\Traits\MerchantTrait;
use App\Models\DriverAgency\DriverAgencyWalletTransaction;

class WalletTransaction {
    use MerchantTrait;
//    public static function WalletDeduct($driver_id, $booking_id, $amount, $narration, $platform = 2, $payment_method = 1, $receipt = null,$transaction_id = '',$notification_type = 3)
    public static function WalletDeduct($paramArray)
    {
        $driver_id = isset($paramArray['driver_id']) ? $paramArray['driver_id'] : NULL;
        $booking_id = isset($paramArray['booking_id']) ? $paramArray['booking_id'] : NULL;
        $order_id = isset($paramArray['order_id']) ? $paramArray['order_id'] : NULL;
        $handyman_order_id = isset($paramArray['handyman_order_id']) ? $paramArray['handyman_order_id'] : NULL;
        $amount = isset($paramArray['amount']) ? $paramArray['amount'] : NULL;
        $narration = isset($paramArray['narration']) ? $paramArray['narration'] : NULL;
        $platform = isset($paramArray['platform']) ? $paramArray['platform'] : 2;
        $payment_method = isset($paramArray['payment_method']) ? $paramArray['payment_method'] : 1;
        $receipt = isset($paramArray['receipt']) ? $paramArray['receipt'] : null;
        $transaction_id = isset($paramArray['transaction_id']) ? $paramArray['transaction_id'] : NULL;
        $notification_type = isset($paramArray['notification_type']) ? $paramArray['notification_type'] : 3 ;
        $action_merchant_id = isset($paramArray['action_merchant_id']) ? $paramArray['action_merchant_id'] : NULL ;
        /**
         * narration 1-Wallet recharged by Admin,
         *           2-Wallet recharged by Driver,
         *           3-Company commission by Booking id,
         *           4-For Subscription Pack Activation,
         *           5-Money Added Successfully Cashback,
         *           6-Ride Amount Credited for Booking id,
         *           7-Send money to User,
         *           8-Cancel Ride Amount Deducted,
         *           9-Reward point redeem Credited
         *          10-Cahout request rejected, amount refund by admin
         *          11-Ride Cancel Amount Received
         *          12-User Old Outstanding Deducted
         *          13-Order amount Deducted
         *          14-Order amount received
         *          15-Cashout request rejected, refund amount.
         *          17-Tax Amount Deducted.
         * platform  1-Admin
         *           2-Application
         *           3-Web
         * transaction_type 1-Credit
         *                  2-Debit
         * payment_method 1-Cash
         *                2-Non Cash
         *                3-Cashback
         */
        $subscription_package_id = NULL;
        $id = NULL;
        if(!empty($booking_id))
        {
            $obj = Booking::select('id','merchant_booking_id','segment_id','merchant_id')->where('id',$booking_id)->first();
            $id = $obj->merchant_booking_id;
        }
        elseif(!empty($order_id))
        {
            $obj = Order::select('id','merchant_order_id','segment_id','merchant_id')->where('id',$order_id)->first();
            $id = $obj->merchant_order_id;
        }
        elseif(!empty($handyman_order_id))
        {
            $obj = HandymanOrder::select('id','merchant_order_id','segment_id','merchant_id')->where('id',$handyman_order_id)->first();
            $id = $obj->merchant_order_id;
        }
        $driver = Driver::find($driver_id);
        $get_string = new GetString($driver->merchant_id);
        $string_file = $get_string->getStringFileText();
        setLocal($driver->language);
        $description = get_narration_value("DRIVER",$narration,$driver->merchant_id,$id,$receipt);
        DriverWalletTransaction::create([
            'merchant_id' => $driver->merchant_id,
            'driver_id' => $driver->id,
            'amount' => round_number($amount),
            'booking_id' => $booking_id,
            'order_id' => $order_id,
            'handyman_order_id' => $handyman_order_id,
            'payment_method' => $payment_method,
            'narration' => $narration,
            'platform' => $platform,
            'transaction_type' => 2,
            'description' => $description,
            'subscription_package_id' => $subscription_package_id,
            'receipt_number' => ($receipt != NULL) ? $receipt : $description,
            'transaction_id' => $transaction_id,
            'action_merchant_id' => $action_merchant_id,
        ]);
        $wallet_money = $driver->wallet_money;
        $outstanding = $wallet_money - $amount;
        $driver->wallet_money = round_number($outstanding);
        $message = $description;
        $minimum_balance = 0;
        if(!empty($driver->CountryArea)){
            $minimum_balance = $driver->CountryArea->minimum_wallet_amount;
        }
        if ($minimum_balance > $outstanding) {
            // $driver->online_offline = 2;
            // $data = array('booking_id' => $booking_id, 'notification_type' => 'ONLINE_OFFLINE', 'segment_type' => "ONLINE_OFFLINE",'segment_data' => 1,'notification_gen_time' => time());
            // $arr_param = array(
            //     'driver_id' => $driver->id,
            //     'data'=>$data,
            //     'message'=>trans('api.message35'),
            //     'merchant_id'=>$driver->merchant_id,
            //     'title' => trans('api.offlineNoe')
            // );
            // Onesignal::DriverPushMessage($arr_param);
        }
        $data = array('booking_id' => $booking_id, 'notification_type' => 'WALLET_UPDATE', 'segment_type' => "WALLET_UPDATE",'segment_data' => [],'notification_gen_time' => time());

        $arr_param = array(
            'driver_id' => $driver->id,
            'data'=>$data,
            'message'=>$message,
            'merchant_id'=>$driver->merchant_id,
            'title' => trans("$string_file.wallet_debited"),
            'large_icon' => ""
        );
        Onesignal::DriverPushMessage($arr_param);
        setLocal();
        $driver->save();
        $config = $driver->Merchant->Configuration;
        if (($config->existing_vehicle_enable == 1 && $config->demo != 1) || ($config->add_multiple_vehicle == 1 && $config->demo != 1)) {
            $driverVehicleDetails = DriverVehicle::with(['Drivers' => function ($q) use ($driver) {
                $q->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 1]]);
            }])->whereHas('Drivers', function ($query) use ($driver) {
                $query->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 1]]);
            })->first();
            if (!empty($driverVehicleDetails)) {
                $drivers = $driverVehicleDetails->Drivers;
                $vehicleActiveStatus = array();
                foreach ($drivers as $driverData) {
                    $vehicleActiveStatus[] = $driverData->online_offline == 1 ? 1 : 2;
                }
                if (!in_array(1, $vehicleActiveStatus)) {
                    $driverVehicleDetails->Drivers()->updateExistingPivot($driver->id, ['vehicle_active_status' => 2]);
//                    $driverVehicleDetails->vehicle_active_status = 2;
//                    $driverVehicleDetails->save();
                }
            }
        }
    }



//    public static function WalletCredit($driver_id, $booking_id, $amount, $narration, $platform = 1, $payment_method = 1, $receipt = null, $transaction_id = '', $notification_type = 3)
    public static function WalletCredit($paramArray)
    {
//        $paramArray = array(
//            'driver_id' => $driver_id,
//            'booking_id' => $booking_id,
//            'amount' => $amount,
//            'narration' => $narration,
//            'platform' => 1,
//            'payment_method' => 1,
//            'receipt' => null,
//            'transaction_id' => '',
//            'notification_type' => 3
//        );
        $driver_id = isset($paramArray['driver_id']) ? $paramArray['driver_id'] : NULL;
        $booking_id = isset($paramArray['booking_id']) ? $paramArray['booking_id'] : NULL;
        $order_id = isset($paramArray['order_id']) ? $paramArray['order_id'] : NULL;
        $handyman_order_id = isset($paramArray['handyman_order_id']) ? $paramArray['handyman_order_id'] : NULL;
        $amount = isset($paramArray['amount']) ? $paramArray['amount'] : NULL;
        $narration = isset($paramArray['narration']) ? $paramArray['narration'] : NULL;
        $platform = isset($paramArray['platform']) ? $paramArray['platform'] : 1;
        $payment_method = isset($paramArray['payment_method']) ? $paramArray['payment_method'] : 1;
        $receipt = isset($paramArray['receipt']) ? $paramArray['receipt'] : null;
        $transaction_id = isset($paramArray['transaction_id']) ? $paramArray['transaction_id'] : NULL;
        $notification_type = isset($paramArray['notification_type']) ? $paramArray['notification_type'] : 3 ;
        $action_merchant_id = isset($paramArray['action_merchant_id']) ? $paramArray['action_merchant_id'] : NULL ;
        $sender = isset($paramArray['sender']) ? $paramArray['sender'] : null;
        /**
         * narration 1-Wallet recharged by Admin,
         *           2-Wallet recharged by Driver,
         *           3-Company commission by Booking id,
         *           4-For Subscription Pack Activation,
         *           5-Money Added Successfully Cashback,
         *           6-Ride Amount Credited for Booking id,
         *           7-Send money to User,
         *           8-Cancel Ride Amount Deducted,
         *           9-Reward point redeem Credited
         *          10-Cahout request rejected, amount refund by admin
         *          11-Ride Cancel Amount Received
         *          12-User Old Outstanding Deducted
         *          13-Order amount Deducted
         *          14-Order amount received
         *          15-Cashout request rejected, refund amount.
         *          17-Tax Amount Deducted.
         * platform  1-Admin
         *           2-Application
         *           3-Web
         * transaction_type 1-Credit
         *                  2-Debit
         * payment_method 1-Cash
         *                2-Non Cash
         *                3-Cashback
         */
        $driver = Driver::find($driver_id);
        $get_string = new GetString($driver->merchant_id);
        $string_file = $get_string->getStringFileText();
        $id = NULL;
        if(!empty($booking_id))
        {
            $obj = Booking::select('id','merchant_booking_id','segment_id','merchant_id')->Find($booking_id);
            $id = $obj->merchant_booking_id;
        }
        elseif(!empty($order_id))
        {
            $obj = Order::select('id','merchant_order_id','segment_id','merchant_id')->Find($order_id);
            $id = $obj->merchant_order_id;
        }
        elseif(!empty($handyman_order_id))
        {
            $obj = HandymanOrder::select('id','merchant_order_id','segment_id','merchant_id')->Find($handyman_order_id);
            $id = $obj->merchant_order_id;
        }
        setLocal($driver->language);
        $description = get_narration_value("DRIVER",$narration,$driver->merchant_id,$id,$receipt,$amount,$sender);
        $subscription_package_id = NULL;

        DriverWalletTransaction::create([
            'merchant_id' => $driver->merchant_id,
            'driver_id' => $driver->id,
            'amount' => round_number($amount),
            'booking_id' => $booking_id,
            'order_id' => $order_id,
            'handyman_order_id' => $handyman_order_id,
            'payment_method' => $payment_method,
            'narration' => $narration,
            'platform' => $platform,
            'transaction_type' => 1,
            'description' => $description,
            'subscription_package_id' => $subscription_package_id,
            'receipt_number' => ($receipt != NULL) ? time().'_'.$receipt : $description,
            'transaction_id' => $transaction_id,
            'action_merchant_id' => $action_merchant_id,
        ]);
        $wallet_money = $driver->wallet_money + $amount;
        $driver->wallet_money = round_number($wallet_money);
        $driver->save();
        $data = array('notification_type' => 'WALLET_UPDATE', 'segment_type' => "WALLET_UPDATE",'segment_data' => [],'notification_gen_time' => time());
        $arr_param = array(
            'driver_id' => $driver->id,
            'large_icon' => "",
            'data'=>$data,
            'message'=>$description,
            'merchant_id'=>$driver->merchant_id,
            'title' => trans("$string_file.wallet_credited"),
        );
        Onesignal::DriverPushMessage($arr_param);
        setLocal();
//        Onesignal::DriverPushMessage($driver->id, $data, $description, $notification_type, $driver->merchant_id);
    }

//    public static function UserWalletDebit($user_id, $booking_id, $amount, $narration, $platform = 2, $payment_method = 1, $receipt = null,$transaction_id = null,$notification_type = 3)
    public static function UserWalletDebit($paramArray)
    {
//        $paramArray = array(
//            'user_id' => $user_id,
//            'booking_id' => $booking_id,
//            'amount' => $amount,
//            'narration' => $narration,
//            'platform' => 2,
//            'payment_method' => 1,
//            'receipt' => null,
//            'transaction_id' => '',
//            'notification_type' => 3
//        );
        $user_id = isset($paramArray['user_id']) ? $paramArray['user_id'] : NULL;
        $booking_id = isset($paramArray['booking_id']) ? $paramArray['booking_id'] : NULL;
        $order_id = isset($paramArray['order_id']) ? $paramArray['order_id'] : NULL;
        $handyman_order_id = isset($paramArray['handyman_order_id']) ? $paramArray['handyman_order_id'] : NULL;
        $amount = isset($paramArray['amount']) ? $paramArray['amount'] : NULL;
        $narration = isset($paramArray['narration']) ? $paramArray['narration'] : NULL;
        $platform = isset($paramArray['platform']) ? $paramArray['platform'] : 2;
        $payment_method = isset($paramArray['payment_method']) ? $paramArray['payment_method'] : 1;
        $receipt = isset($paramArray['receipt']) ? $paramArray['receipt'] : null;
        $transaction_id = isset($paramArray['transaction_id']) ? $paramArray['transaction_id'] : NULL;
        $notification_type = isset($paramArray['notification_type']) ? $paramArray['notification_type'] : 3 ;
        $transaction_type = isset($paramArray['transaction_type']) ? $paramArray['transaction_type'] : 2 ;
        $receiver = isset($paramArray['receiver']) ? $paramArray['receiver'] : NULL;
        $action_merchant_id = isset($paramArray['action_merchant_id']) ? $paramArray['action_merchant_id'] : NULL ;
        $wallet_transfer_id = isset($paramArray['wallet_transfer_id']) ? $paramArray['wallet_transfer_id'] : NULL ;

        /**
         * narration 1-Wallet recharged by Admin
         *           2-Wallet recharged by User
         *           3-Wallet money added with Coupon
         *           4-Auto Deduct At Ride End
         *           5-Cancel Ride Amount Deducted,
         *           6-Money received from Driver
         *           7-Cashback
         *           8. Money Transfered to another user
         *           9. Money Transfered from another user   
         * platform  1-Money Added By Admin
         *           2-Money Added
         * transaction_type 1-Credit
         *                  2-Debit
         *                  3-Transfered
         * payment_method 1-Cash
         *                2-Non Cash
         *                3-Cashback
         */
//        switch ($narration) {
//            case "1":
//                $description = trans('api.message44');
//                break;
//            case "2":
//                $description = trans('api.wallet_recharge_by_user');
//                break;
//            case "3":
//                $description = trans('api.wallet_money_added_with_coupon') .' '. $receipt;
//                break;
//            case "4":
//                $description = trans('api.message54') .' '. $booking_id;
//                break;
//            case "5":
//                $description = trans('api.ride_cancel_amout_debit') .' '. $booking_id;
//                break;
//            case "6":
//                $description = $receipt;
//                break;
//            case "7":
//                $description = $booking_id;
//                $booking_id = NULL;
//                break;
//            default:
//                $description = 'No Description Found.';
//        }
        $user = User::find($user_id);
        $string_file = $user->Merchant->string_file;
        $segment_name = "";
        $id = NULL;
        if(!empty($booking_id))
        {
            $data = Booking::select('id','merchant_booking_id','segment_id','merchant_id')->Find($booking_id);
            $segment_name = $data->Segment->Name($data->merchant_id);
            $segment_name = $segment_name.' '.trans("$string_file.ride");
            $id = $data->merchant_booking_id;
        }
        elseif(!empty($order_id))
        {
            $data = Order::select('id','merchant_order_id','segment_id','merchant_id')->Find($order_id);
            $segment_name = $data->Segment->Name($data->merchant_id);
            $segment_name = $segment_name.' '.trans("$string_file.order");
            $id = $data->merchant_order_id;
        }
        elseif(!empty($handyman_order_id))
        {
            $data = HandymanOrder::select('id','merchant_order_id','segment_id','merchant_id')->Find($handyman_order_id);
            $segment_name = $data->Segment->Name($data->merchant_id);
            $segment_name = $segment_name.' '.trans("$string_file.booking");
            $id = $data->merchant_order_id;
        }
        if(!empty($segment_name))
        {
            $segment_name = trans("$string_file.for").' '.$segment_name;
        }
        setLocal($user->language);
        $description = get_narration_value("USER",$narration,$user->merchant_id,$id,$receipt,$amount,$receiver);
        UserWalletTransaction::create([
            'merchant_id' => $user->merchant_id,
            'user_id' => $user->id,
            'narration' => $narration,
            'amount' => round_number($amount),
            'booking_id' => $booking_id,
            'order_id' => $order_id,
            'handyman_order_id' => $handyman_order_id,
            'payment_method' => $payment_method,
            'platfrom' => $platform,
            'type' => $transaction_type,
            'description' => $description,
            'receipt_number' => ($receipt != NULL) ? $receipt : $description,
            'transaction_id' => ($transaction_id != null) ? $transaction_id : $booking_id,
            'action_merchant_id' => $action_merchant_id,
            'wallet_transfer_id' => $wallet_transfer_id,
        ]);
        $user->wallet_balance = round_number(($user->wallet_balance - $amount));
        $user->save();
        $data = array('notification_type' => 'WALLET_UPDATE','segment_type' => 'WALLET_UPDATE','segment_data'=>[]);
        $arr_param = array(
            'user_id' => $user->id,
            'data'=>$data,
            'message'=>$description,
            'merchant_id'=>$user->merchant_id,
            'title' => trans("$string_file.wallet_debited").' '.$segment_name
        );
        Onesignal::UserPushMessage($arr_param);
        setLocal();
//        Onesignal::UserPushMessage($user->id, $data, $description, 3, $user->merchant_id);
    }

//    public static function UserWalletCredit($user_id, $booking_id, $amount, $narration, $platform = 1, $payment_method = 1, $receipt = null, $transaction_id = null, $notification_type = 3)
    public static function UserWalletCredit($paramArray)
    {
//        $paramArray = array(
//            'user_id' => $user_id,
//            'booking_id' => $booking_id,
//            'amount' => $amount,
//            'narration' => $narration,
//            'platform' => 1,
//            'payment_method' => 1,
//            'receipt' => null,
//            'transaction_id' => '',
//            'notification_type' => 3
//        );
        $user_id = isset($paramArray['user_id']) ? $paramArray['user_id'] : NULL;
        $booking_id = isset($paramArray['booking_id']) ? $paramArray['booking_id'] : NULL;
        $order_id = isset($paramArray['order_id']) ? $paramArray['order_id'] : NULL;
        $handyman_order_id = isset($paramArray['handyman_order_id']) ? $paramArray['handyman_order_id'] : NULL;
        $amount = isset($paramArray['amount']) ? $paramArray['amount'] : NULL;
        $narration = isset($paramArray['narration']) ? $paramArray['narration'] : NULL;
        $platform = isset($paramArray['platform']) ? $paramArray['platform'] : 1;
        $payment_method = isset($paramArray['payment_method']) ? $paramArray['payment_method'] : 1;
        $payment_option_id = isset($paramArray['payment_option_id']) ? $paramArray['payment_option_id'] : 1;
        $receipt = isset($paramArray['receipt']) ? $paramArray['receipt'] : null;
        $transaction_id = isset($paramArray['transaction_id']) ? $paramArray['transaction_id'] : NULL;
        $notification_type = isset($paramArray['notification_type']) ? $paramArray['notification_type'] : 3 ;
        $sender = isset($paramArray['sender']) ? $paramArray['sender'] : NULL;
        $action_merchant_id = isset($paramArray['action_merchant_id']) ? $paramArray['action_merchant_id'] : NULL ;
        $wallet_transfer_id = isset($paramArray['wallet_transfer_id']) ? $paramArray['wallet_transfer_id'] : NULL ;
        /**
         * narration 1-Wallet recharged by Admin
         *           2-Wallet recharged by User
         *           3-Wallet money added with Coupon
         *           4-Auto Deduct At Ride End
         *           5-Cancel Ride Amount Deducted,
         *           6-Money received from Driver
         *           7-Cashback
         *           8. Money Transfered to another user
         *           9. Money Transfered from another user
         * platform  1-Money Added By Admin
         *           2-Money Added
         * transaction_type 1-Credit
         *                  2-Debit
         *                  3-Transfered
         * payment_method 1-Cash
         *                2-Non Cash
         *                3-Cashback
         */
//        switch ($narration) {
//            case "1":
//                $description = trans('api.message44');
//                $transaction_id = $receipt;
//                break;
//            case "2":
//                $description = trans('api.wallet_recharge_by_user');
//                break;
//            case "3":
//                $description = trans('api.wallet_money_added_with_coupon') .' '. $receipt;
//                break;
//            case "4":
//                $description = trans('api.message54') .' '. $booking_id;
//                break;
//            case "5":
//                $description = trans('api.ride_cancel_amout_debit') .' '. $booking_id;
//                break;
//            case "6":
//                $description = $receipt;
//                break;
//            case "7":
//                $description = $booking_id;
//                $booking_id = NULL;
//                break;
//            default:
//                $description = 'No Description Found.';
//        }
        $id = NULL;
        if(!empty($booking_id))
        {
            $obj = Booking::select('id','merchant_booking_id','segment_id','merchant_id')->where('id',$booking_id)->first();
            $id = $obj->merchant_booking_id;
        }
        elseif(!empty($order_id))
        {
            $obj = Order::select('id','merchant_order_id','segment_id','merchant_id')->where('id',$order_id)->first();
            $id = $obj->merchant_order_id;
        }
        elseif(!empty($handyman_order_id))
        {
            $obj = HandymanOrder::select('id','merchant_order_id','segment_id','merchant_id')->where('id',$handyman_order_id)->first();
            $id = $obj->merchant_order_id;
        }
        $user = User::find($user_id);
        $get_string = new GetString($user->merchant_id);
        $string_file = $get_string->getStringFileText();
//        $description = self::getNarrationValue("USER",$narration,$user->merchant_id,$booking_id,$receipt);
        setLocal($user->language);
        $description = get_narration_value("USER",$narration,$user->merchant_id,$id,$receipt,$amount,$sender);
        UserWalletTransaction::create([
            'merchant_id' => $user->merchant_id,
            'user_id' => $user->id,
            'narration' => $narration,
            'amount' => round_number($amount),
            'booking_id' => $booking_id,
            'order_id' => $order_id,
            'handyman_order_id' => $handyman_order_id,
            'payment_method' => $payment_method,
            'platfrom' => $platform,
            'type' => 1,
            'description' => $description,
            'receipt_number' => ($receipt != NULL) ? time().'_'.$receipt : $description,
            'transaction_id' => ($transaction_id != null) ? $transaction_id : $booking_id,
            'payment_option_id' => $payment_option_id,
            'action_merchant_id' => $action_merchant_id,
            'wallet_transfer_id' => $wallet_transfer_id,
        ]);
        $wallet_money = $user->wallet_balance + $amount;
        $user->wallet_balance = round_number($wallet_money);
        $user->save();
        $data = array(
            'notification_type' => 'WALLET_UPDATE',
            'segment_type' => "WALLET_UPDATE",
            'segment_data' => [],
//            'notification_gen_time' => time(),
        );
        $large_icon = "";
        $arr_param = array(
            'user_id' => $user->id,
            'data'=>$data,
            'message'=>$description,
            'merchant_id'=>$user->merchant_id,
            'title' => trans("$string_file.wallet_credited"),
            'large_icon'=>$large_icon
        );
        Onesignal::UserPushMessage($arr_param);
        setLocal();
//        Onesignal::DriverPushMessage($user->id, $data, $description, $notification_type, $user->merchant_id);
    }

    public static function TaxiComapnyWalletDeduct($taxi_company_id, $booking_id = null, $amount, $payment_method_id = null, $receipt_number = null, $description = NULL)
    {
        $taxi_company = TaxiCompany::find($taxi_company_id);
        TaxiCompaniesWalletTransaction::create([
            'merchant_id' => $taxi_company->merchant_id,
            'taxi_company_id' => $taxi_company->id,
            'transaction_type' => 2,
            'payment_method' => $payment_method_id,
            'receipt_number' => time().'_'.$receipt_number,
            'amount' => $amount,
            'platform' => 2,
            'description' => ($description != null) ? $description : trans('api.message54'),
            'booking_id' => $booking_id,
            'narration' => 3,
        ]);
        $wallet_money = $taxi_company->wallet_money;
        $outstanding = $wallet_money - $amount;
        $taxi_company->wallet_money = round($outstanding, 2);
        $taxi_company->save();
    }

    public static function TaxiComapnyWalletCredit($taxi_company_id, $amount, $payment_method_id, $receipt_number, $description = NULL)
    {
        $newAmount = new \App\Http\Controllers\Helper\Merchant();
        $taxi_company = TaxiCompany::find($taxi_company_id);
        TaxiCompaniesWalletTransaction::create([
            'merchant_id' => $taxi_company->merchant_id,
            'taxi_company_id' => $taxi_company->id,
            'transaction_type' => 1, // Credit
            'payment_method' => $payment_method_id,
            'receipt_number' => time().'_'.$receipt_number,
            'amount' => sprintf("%0.2f", $amount),
            'platform' => 1,
            'description' => $description,
        ]);
        $wallet_money = $taxi_company->wallet_money + $amount;
        $taxi_company->wallet_money = $newAmount->TripCalculation($wallet_money, $taxi_company->merchant_id);
        $taxi_company->save();
    }

    public static function HotelWalletAdded($hotel_id, $booking_id, $amount, $receipt_number, $description)
    {
        $hotel = Hotel::find($hotel_id);
        HotelWalletTransaction::create([
            'merchant_id' => $hotel->merchant_id,
            'hotel_id' => $hotel->id,
            'transaction_type' => 1,
            'payment_method' => 1,
            'receipt_number' => time().'_'.$receipt_number,
            'amount' => $amount,
            'platform' => 2,
            'description' => $description,
            'booking_id' => $booking_id,
            'narration' => 3,
        ]);
        $wallet_money = $hotel->wallet_money;
        $outstanding = $wallet_money + $amount;
        $hotel->wallet_money = round($outstanding, 2);
        $hotel->save();
    }

    public static function HotelWalletDeduct($hotel_id, $booking_id, $amount, $receipt_number, $description)
    {
        $hotel = Hotel::find($hotel_id);
        HotelWalletTransaction::create([
            'merchant_id' => $hotel->merchant_id,
            'hotel_id' => $hotel->id,
            'transaction_type' => 2,
            'payment_method' => 1,
            'receipt_number' => time().'_'.$receipt_number,
            'amount' => $amount,
            'platform' => 2,
            'description' => $description,
            'booking_id' => $booking_id,
            'narration' => 3,
        ]);
        $wallet_money = $hotel->wallet_money;
        $outstanding = $wallet_money - $amount;
        $hotel->wallet_money = round($outstanding, 2);
        $hotel->save();
    }

    public static function BusinessSegmntWalletDebit($paramArray)
    {
//        $paramArray = array(
//            'business_segment_id' => $business_segment_id,
//            'order_id' => $order_id,
//            'amount' => $amount,
//            'narration' => $narration,
//            'platform' => 2,
//            'payment_method' => 1,
//            'receipt' => null,
//            'transaction_id' => '',
//        );
        $business_segment_id = isset($paramArray['business_segment_id']) ? $paramArray['business_segment_id'] : NULL;
        $order_id = isset($paramArray['order_id']) ? $paramArray['order_id'] : NULL;
        $amount = isset($paramArray['amount']) ? $paramArray['amount'] : NULL;
        $narration = isset($paramArray['narration']) ? $paramArray['narration'] : NULL;
        $platform = isset($paramArray['platform']) ? $paramArray['platform'] : 2;
        $payment_method = isset($paramArray['payment_method']) ? $paramArray['payment_method'] : 1;
        $receipt = isset($paramArray['receipt']) ? $paramArray['receipt'] : null;
        $transaction_id = isset($paramArray['transaction_id']) ? $paramArray['transaction_id'] : NULL;
        $action_merchant_id = isset($paramArray['action_merchant_id']) ? $paramArray['action_merchant_id'] : NULL ;
        /**
         * narration 1-Wallet recharged by Admin
         *           2-Order amount added by Admin
         *           3-Order commission deducted
         *           4-Cashout amount deducted
         *           5-Cashout request rejected, refund amount.
         * platform  1-Admin
         *           2-Application
         *           3-Web
         * transaction_type 1-Credit
         *                  2-Debit
         * payment_method 1-Cash
         *                2-Non Cash
         *                3-Cashback
         */
//        switch ($narration) {
//            case "1":
//                $description = trans('api.message44');
//                break;
//            case "2":
//                $description = trans('api.order_amount_added_by_Admin');
//                break;
//            case "3":
//                $description = trans('api.order_commission_deducted');
//                break;
//            case "4":
//                $description = trans('api.cashout_amount_deducted');
//                break;
//            case "5":
//                $description = trans('api.cashout_request_rejected_refund_amount');
//                break;
//            default:
//                $description = 'No Description Found.';
//        }
        $business_segment = BusinessSegment::find($business_segment_id);
        $merchant_id = $business_segment->merchant_id;
        $merchant_order_id = $business_segment->merchant_order_id;
//        $description = self::getNarrationValue("BUSINESS_SEGMENT",$narration,$merchant_id,$merchant_order_id,$receipt);
        $description = get_narration_value("BUSINESS_SEGMENT",$narration,$merchant_id,$merchant_order_id,$receipt);
        BusinessSegmentWalletTransaction::create([
            'merchant_id' => $business_segment->merchant_id,
            'business_segment_id' => $business_segment->id,
            'amount' => round_number($amount),
            'order_id' => $order_id,
            'payment_method' => $payment_method,
            'platform' => $platform,
            'narration' => $narration,
            'transaction_type' => 2,
            'description' => $description,
            'receipt_number' => ($receipt != NULL) ? $receipt : $description,
            'transaction_id' => ($transaction_id != null) ? $transaction_id : $order_id,
            'action_merchant_id' => $action_merchant_id,
        ]);
        $business_segment->wallet_amount = round_number(($business_segment->wallet_amount - $amount));
        $business_segment->save();
    }

    public static function BusinessSegmntWalletCredit($paramArray)
    {
//        $paramArray = array(
//            'business_segment_id' => $business_segment_id,
//            'order_id' => $order_id,
//            'amount' => $amount,
//            'narration' => $narration,
//            'platform' => 2,
//            'payment_method' => 1,
//            'receipt' => null,
//            'transaction_id' => '',
//        );
        $business_segment_id = isset($paramArray['business_segment_id']) ? $paramArray['business_segment_id'] : NULL;
        $order_id = isset($paramArray['order_id']) ? $paramArray['order_id'] : NULL;
        $amount = isset($paramArray['amount']) ? $paramArray['amount'] : NULL;
        $narration = isset($paramArray['narration']) ? $paramArray['narration'] : NULL;
        $platform = isset($paramArray['platform']) ? $paramArray['platform'] : 1;
        $payment_method = isset($paramArray['payment_method']) ? $paramArray['payment_method'] : 1;
        $receipt = isset($paramArray['receipt']) ? $paramArray['receipt'] : null;
        $transaction_id = isset($paramArray['transaction_id']) ? $paramArray['transaction_id'] : NULL;
        $action_merchant_id = isset($paramArray['action_merchant_id']) ? $paramArray['action_merchant_id'] : NULL ;
        /**
         * narration 1-Wallet recharged by Admin
         *           2-Order amount added by Admin
         *           3-Order commission deducted
         *           4-Cashout amount deducted
         *           5-Cashout request rejected, refund amount.
         * platform  1-Admin
         *           2-Application
         *           3-Web
         * transaction_type 1-Credit
         *                  2-Debit
         * payment_method 1-Cash
         *                2-Non Cash
         *                3-Cashback
         */
//        switch ($narration) {
//            case "1":
//                $description = trans('api.message44');
//                break;
//            case "2":
//                $description = trans('api.order_amount_added_by_Admin');
//                break;
//            case "3":
//                $description = trans('api.order_commission_deducted');
//                break;
//            case "4":
//                $description = trans('api.cashout_amount_deducted');
//                break;
//            case "5":
//                $description = trans('api.cashout_request_rejected_refund_amount');
//                break;
//            default:
//                $description = 'No Description Found.';
//        }
        $business_segment = BusinessSegment::find($business_segment_id);
        $merchant_id = $business_segment->merchant_id;
//        $description = self::getNarrationValue("BUSINESS_SEGMENT",$narration,$merchant_id,"",$receipt);
        $description = get_narration_value("BUSINESS_SEGMENT",$narration,$merchant_id,"",$receipt);
        BusinessSegmentWalletTransaction::create([
            'merchant_id' => $merchant_id,
            'business_segment_id' => $business_segment->id,
            'amount' => round_number($amount),
            'order_id' => $order_id,
            'payment_method' => $payment_method,
            'platform' => $platform,
            'transaction_type' => 1,
            'narration' => $narration,
            'description' => $description,
            'receipt_number' => ($receipt != NULL) ? time().'_'.$receipt : $description,
            'transaction_id' => ($transaction_id != null) ? $transaction_id : $order_id,
            'action_merchant_id' => $action_merchant_id,
        ]);
        $wallet_money = $business_segment->wallet_amount + $amount;
        $business_segment->wallet_amount = round_number($wallet_money);
        $business_segment->save();
    }

    public static function CorporateWaletCredit($corporate_id, $amount, $payment_method, $receipt_number, $description){
        $corporate = Corporate::find($corporate_id);
        $newAmount = new \App\Http\Controllers\Helper\Merchant();
        CorporateWalletTransaction::create([
            'merchant_id' => $corporate->merchant_id,
            'corporate_id' => $corporate->id,
            'transaction_type' => 1,
            'payment_method' => $payment_method,
            'receipt_number' => time().'_'.$receipt_number,
            'amount' => $amount,
            'platform' => 1,
            'description' => $description,
            'narration' => 1,
        ]);
        $wallet_money = $corporate->wallet_balance + $amount;
        $corporate->wallet_balance = $newAmount->TripCalculation($wallet_money, $corporate->merchant_id);
        $corporate->save();
    }

    public static function CorporateWaletDebit($corporate_id, $amount, $payment_method, $receipt_number, $description){
        $corporate = Corporate::find($corporate_id);
        $newAmount = new \App\Http\Controllers\Helper\Merchant();
        CorporateWalletTransaction::create([
            'merchant_id' => $corporate->merchant_id,
            'corporate_id' => $corporate->id,
            'transaction_type' => 2,
            'payment_method' => $payment_method,
            'receipt_number' => time().'_'.$receipt_number,
            'amount' => $amount,
            'platform' => 1,
            'description' => $description,
            'narration' => 1,
        ]);
        $wallet_money = $corporate->wallet_balance - $amount;
        $corporate->wallet_balance = $newAmount->TripCalculation($wallet_money, $corporate->merchant_id);
        $corporate->save();
    }

//    public static function getNarrationValue($narration_for,$narration,$merchant_id,$id,$receipt)
//    {
//        $get_string = new GetString($merchant_id);
//        $string_file = $get_string->getStringFileText();
//        $description = "";
//        // common strings
//        $no_description =trans("$string_file.no_description");
//        $description_admin =trans("$string_file.wallet_recharged_by_admin");
//        $description_self_credit =  trans("$string_file.wallet_recharged_successfully");
//        switch ($narration_for){
//            case "DRIVER":
//                switch ($narration) {
//                    case "1":
//                        $description = $description_admin;
////                    trans('api.message44');
//                        break;
//                    case "2":
//                        $description = $description_self_credit;
////                $description = trans('api.message45');
//                        break;
//                    case "3":
//                        $description = trans("$string_file.company_commission_of_ride_id").' #'.$id;
////                $description = trans('api.message46') .' '. $booking_id;
//                        break;
//                    case "4":
//                        // In this cash, booking id is package id
//                        $subscription_package_id = $id;
//                        $booking_id = NULL;
//                        $description = trans("$string_file.bought_subscription_package") .' '.$subscription_package_id;
//                        break;
//                    case "5":
//                        $description = trans("$string_file.money_added_in_wallet") . '(' . trans("$string_file.cashback") . ')';
//                        break;
//                    case "6":
//                        $description = trans("$string_file.ride_amount_credited").$id;
//                        break;
//                    case "7":
//                        $description = $receipt;
//                        break;
//                    case "8":
//                        $description = trans("$string_file.cancelled_ride_amount_debited").$id;
//                        break;
//                    case "9":
//                        $description = trans('api.reward_point_redeem_credit');
//                        break;
//                    case "10":
//                        $description = trans("$string_file.cashout_amount_deducted");
//                        break;
//                    case "11":
//                        $description = trans("$string_file.cancelled_ride_amount_credited").$id;
//                        break;
//                    case "12":
//                        $description = trans("$string_file.user_old_outstanding_deducted");
//                        break;
//                    case "13":
//                        $description = trans("$string_file.order_amount_deducted");
//                        break;
//                    case "14":
//                        $description = trans("$string_file.order_amount_received");
//                        break;
//                    case "15":
//                        $description = trans("$string_file.cashout_request_rejected_refund_amount");
//                        break;
//                    default:
//                        $description =$no_description;
//                }
//                break;
//            case "USER":
//                switch ($narration) {
//                    case "1":
//                        $description = trans("$string_file.wallet_recharged_by_admin");
//                        break;
//                    case "2":
//                        $description = trans("$string_file.wallet_recharged_successfully");
//                        break;
//                    case "3":
//                        $description = trans("$string_file.wallet_money_added_with_coupon") .' '. $receipt;
//                        break;
//                    case "4":
//                        $description = trans("$string_file.ride_amount_debited") .' '. $id;
//                        break;
//                    case "5":
//                        $description = trans("$string_file.cancelled_ride_amount_debited") .' '. $id;
//                        break;
//                    case "6":
//                        $description = $receipt;
//                        break;
//                    case "7":
//                        $description = $id;
//                        $booking_id = NULL;
//                        break;
//                    default:
//                        $description =$no_description;
//                }
//                break;
//            case "TAXI_COMPANY":
//                break;
//            case "HOTEL":
//                break;
//            case "BUSINESS_SEGMENT":
//                switch ($narration) {
//                    case "1":
//                        $description = $description_admin;
//                        break;
//                    case "2":
//                        $description = trans("$string_file.order_amount_added_by_admin");
//                        break;
//                    case "3":
//                        $description = trans("$string_file.order_commission_deducted").$id;
//                        break;
//                    case "4":
//                        $description = trans("$string_file.cashout_amount_deducted");
//                        break;
//                    case "5":
//                        $description = trans("$string_file.cashout_request_rejected_refund_amount");
//                        break;
//                    default:
//                        $description = $no_description;
//                }
//                break;
//        }
//        return $description;
//    }

   // wallet credit functionality
    public static function driverAgencyWalletCredit($arr_data)
    {
        $driver_agency_id = isset($arr_data['driver_agency_id']) ? $arr_data['driver_agency_id'] : NULL;
        $payment_method_id = isset($arr_data['payment_method_id']) ? $arr_data['payment_method_id'] : NULL;
        $receipt_number = isset($arr_data['receipt_number']) ? $arr_data['receipt_number']: NULL;
        $amount = isset($arr_data['amount']) ? $arr_data['amount']: NULL;
        $description = isset($arr_data['description']) ? $arr_data['description']: NULL;
        $narration = isset($arr_data['narration']) ? $arr_data['narration']: NULL;
        $newAmount = new \App\Http\Controllers\Helper\Merchant();
        $driver_agency = DriverAgency::find($driver_agency_id);
        DriverAgencyWalletTransaction::create([
            'merchant_id' => $driver_agency->merchant_id,
            'driver_agency_id' => $driver_agency->id,
            'transaction_type' => 1, // Credit
            'payment_method' => $payment_method_id,
            'receipt_number' => time().'_'.$receipt_number,
            'amount' => sprintf("%0.2f", $amount),
            'platform' => 1,
            'narration' => $narration,
            'description' => $description,
        ]);
        $wallet_money = $driver_agency->wallet_balance + $amount;
        $driver_agency->wallet_balance = $newAmount->TripCalculation($wallet_money, $driver_agency->merchant_id);
        $driver_agency->save();
        return true;
    }

    // wallet credit functionality
    public static function driverAgencyWalletDebit($arr_data)
    {
        $driver_agency_id = isset($arr_data['driver_agency_id']) ? $arr_data['driver_agency_id'] : NULL;

        $payment_method_id = isset($arr_data['payment_method_id']) ? $arr_data['payment_method_id'] : NULL;
        $receipt_number = isset($arr_data['receipt_number']) ? $arr_data['receipt_number']: NULL;
        $amount = isset($arr_data['amount']) ? $arr_data['amount']: NULL;
        $description = isset($arr_data['description']) ? $arr_data['description']: NULL;
        $narration = isset($arr_data['narration']) ? $arr_data['narration']: NULL;
        $newAmount = new \App\Http\Controllers\Helper\Merchant();
        $driver_agency = DriverAgency::find($driver_agency_id);
        // p([
        //     'merchant_id' => $driver_agency->merchant_id,
        //     'driver_agency_id' => $driver_agency->id,
        //     'transaction_type' => 2, // Debit
        //     'payment_method' => $payment_method_id,
        //     'receipt_number' => time().'_'.$receipt_number,
        //     'amount' => sprintf("%0.2f", $amount),
        //     'platform' => 1,
        //     'narration' => $narration,
        //     'description' => $description,
        // ]);
        // p(sprintf("%0.2f", $amount));
        DriverAgencyWalletTransaction::create([
            'merchant_id' => $driver_agency->merchant_id,
            'driver_agency_id' => $driver_agency->id,
            'transaction_type' => 2, // Debit
            'payment_method' => $payment_method_id,
            'receipt_number' => time().'_'.$receipt_number,
            'amount' => sprintf("%0.2f", $amount),
            'platform' => 1,
            'narration' => $narration,
            'description' => $description,
        ]);
        $wallet_money = !empty($driver_agency->wallet_balance) ?  $driver_agency->wallet_balance- $amount : 0-$amount;
        $driver_agency->wallet_balance = $newAmount->TripCalculation($wallet_money, $driver_agency->merchant_id);
        $driver_agency->save();
        return true;
    }
}