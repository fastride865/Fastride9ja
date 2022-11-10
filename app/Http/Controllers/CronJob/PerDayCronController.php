<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Merchant\ExpireDocumentController;
use App\Http\Controllers\Merchant\ReferralSystemController;
use App\Models\Configuration;
use App\Models\Driver;
use App\Models\DriverDocument;
use App\Models\DriverSubscriptionRecord;
use App\Models\DriverVehicle;
use App\Models\DriverVehicleDocument;
use App\Models\HandymanOrder;
use App\Models\Merchant;
use App\Models\Onesignal;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ExpireDocument;
use App\Models\BusinessSegment\Order;
use DB;
use App\Models\User;
use App\Http\Controllers\Helper\WalletTransaction;
//use App\Traits\HandymanTrait;
use App\Traits\OrderTrait;

class PerDayCronController extends Controller
{
    /*************** Document expire cron start **************/
    // call cron function
    use ExpireDocument, MerchantTrait,OrderTrait;

    public function document()
    {
//        $this->checkPersonalDocument();
//        $this->checkVehicleDocument();
        $this->getExpiredDocument();
        $this->getDocumentExpireReminder();
        $this->reminderNotificationForOrderDelivery(); //today's delivery reminder
        $this->expireAcceptedOrders(); //expire accepted orders which were not delivered on delivery date

    }

    public function getExpiredDocument()
    {
        $drivers = $this->getAllExpireDriversDocument(NULL, 2, false);
        if (!empty($drivers) && $drivers->count() > 0) {
            foreach ($drivers as $driver) {
                // personal document case
                $notification_status = false;
                if ($driver->DriverDocument->count() > 0) {
                    $notification_status = false;
                    foreach ($driver->DriverDocument as $driverDoc) {
                        if ($driverDoc->temp_document_file != null && ($driverDoc->temp_doc_verification_status == 1 || $driverDoc->temp_doc_verification_status == 2)) {
                            if($driverDoc->temp_doc_verification_status == 1) {
                                $driver->signupStep = 8; // pending mode when admin did not either approve or reject
                                $driver->save();
                            }
                            $driverDoc->document_file = $driverDoc->temp_document_file;
                            $driverDoc->expire_date = $driverDoc->temp_expire_date;
                            $driverDoc->document_verification_status = $driverDoc->temp_doc_verification_status;
                            $driverDoc->temp_document_file = null;
                            $driverDoc->temp_expire_date = null;
                            $driverDoc->temp_doc_verification_status = null;
                            $driverDoc->save();
                        } else {
                            $notification_status = true;
                            $driverDoc->document_verification_status = 4;
                            $driverDoc->save();
                            $driver->online_offline = 2;
                            $driver->save();
                        }
                    }
                }

                if ($driver->segment_group_id == 2) {
                    // segment document case
                    if ($driver->DriverSegmentDocument->count() > 0) {
                        $notification_status = false;
                        foreach ($driver->DriverSegmentDocument as $driverDoc) {
                            if ($driverDoc->temp_document_file != null && ($driverDoc->temp_doc_verification_status == 1 || $driverDoc->temp_doc_verification_status == 2)) {
                                if($driverDoc->temp_doc_verification_status == 1) {
                                    $driver->signupStep = 8; // pending mode when admin did not either approve or reject
                                    $driver->save();
                                }
                                $driverDoc->document_file = $driverDoc->temp_document_file;
                                $driverDoc->expire_date = $driverDoc->temp_expire_date;
                                $driverDoc->document_verification_status = $driverDoc->temp_doc_verification_status;
                                $driverDoc->temp_document_file = null;
                                $driverDoc->temp_expire_date = null;
                                $driverDoc->temp_doc_verification_status = null;
                                $driverDoc->save();
                            } else {
                                $notification_status = true;
                                $driverDoc->document_verification_status = 4;
                                $driverDoc->save();
                                $driver->online_offline = 2;
                                $driver->save();
                            }
                        }
                    }
                } else {
                    // vehicle document
                    if ($driver->DriverVehicles->count() > 0) {
                        $notification_status = false;
                        foreach ($driver->DriverVehicles as $driverVehicle) {
                            foreach ($driverVehicle->DriverVehicleDocument as $driverDoc) {
                                if ($driverDoc->temp_document_file != null && ($driverDoc->temp_doc_verification_status == 1 || $driverDoc->temp_doc_verification_status == 2)) {
                                    if($driverDoc->temp_doc_verification_status == 1) {
                                        $driver->signupStep = 8; // pending mode when admin did not either approve or reject
                                        $driver->save();
                                    }
                                    $driverDoc->document = $driverDoc->temp_document_file;
                                    $driverDoc->expire_date = $driverDoc->temp_expire_date;
                                    $driverDoc->document_verification_status = $driverDoc->temp_doc_verification_status;
                                    $driverDoc->temp_document_file = null;
                                    $driverDoc->temp_expire_date = null;
                                    $driverDoc->temp_doc_verification_status = null;
                                    $driverDoc->save();
                                } else {
                                    $notification_status = true;
                                    $driverDoc->document_verification_status = 4;
                                    $driverDoc->save();
                                    $driver->online_offline = 2;
                                    $driver->save();
                                }
                            }
                        }
                    }
                }

                if ($notification_status == true) {
                    $string_file = $this->getStringFile($driver->merchant_id);
                    setLocal($driver->language);
                    $data['notification_type'] = "DOCUMENT_EXPIRED";
                    $data['segment_sub_group'] = NULL;
                    $data['segment_group_id'] = NULL;
                    $data['segment_type'] = "";
                    $data['segment_data'] = [];
                    $arr_param = ['driver_id' => $driver->id, 'data' => $data, 'message' => trans("$string_file.document_expired_error"), 'merchant_id' => $driver->merchant_id, 'title' => trans("$string_file.document_expired")];
                    $a = Onesignal::DriverPushMessage($arr_param);
                }
            }
        }
    }

    public function getDocumentExpireReminder()
    {
        $currentDate = date('Y-m-d');
        $merchants = Merchant::where('parent_id', '=', 0)->get();
        $expire_class = new ExpireDocumentController;
        foreach ($merchants as $merchant) {
            $reminder_days = Configuration::where('merchant_id', '=', $merchant->id)->select('reminder_doc_expire')->first();
            $reminder_last_date = date('Y-m-d', strtotime('+' . $reminder_days->reminder_doc_expire . ' days'));
            $drivers = $expire_class->getDocumentGoingToExpire($currentDate, $reminder_last_date, $merchant->id)->get();
            $Ids = array();
            foreach ($drivers as $driver) {
                if (!empty($driver->player_id) && $driver->player_id != null) {
                    $Ids[] = $driver->id; // send driver id
                }
            }
            if (count($Ids) > 0) {
                $string_file = $this->getStringFile($merchant->id);
                foreach($Ids as $id){
                    setLocal($driver->language);
                    $data['notification_type'] = "DOCUMENT_EXPIRE_REMINDER";
                    $data['segment_sub_group'] = NULL;
                    $data['segment_group_id'] = NULL;
                    $data['segment_type'] = "";
                    $data['segment_data'] = [];
                    $arr_param = ['driver_id' => $id, 'data' => $data, 'message' => trans("$string_file.document_expire_warning"), 'merchant_id' => $merchant->id, 'title' => trans("$string_file.document_expire")];
                    Onesignal::DriverPushMessage($arr_param);
                }
            }
        }
    }
    /*************** Document expire cron end **************/

    /*************** Subscription Package expire cron start **************/
    public function subscriptionPackage()
    {
        $this->ExpireSubscriptionPackage();
    }

    public function ExpireSubscriptionPackage()
    {
//        $active_packages = DriverSubscriptionRecord::select('id')->where([['status', '!=', 3], ['end_date_time', '<', date('Y-m-d H:i:s')]])->get();
//        if ($active_packages->isNotEmpty()):
//            DriverSubscriptionRecord::whereIn('id', $active_packages->toArray())
//                ->update([
//                    'status' => 3, // package expired
//                ]);
//        endif;
    }

//    public function checkPersonalDocument()
//    {
//        $drivers = $this->getPersonalDocExpireAllDriver();
//        if (!empty($drivers)){
//            foreach ($drivers as $value) {
//                $notification_status = false;
//                foreach ($value->DriverDocument as $driverDoc){
//                    if ($driverDoc->temp_document_file != null){
//                        $driverDoc->document_file = $driverDoc->temp_document_file;
//                        $driverDoc->expire_date = $driverDoc->temp_expire_date;
//                        $driverDoc->document_verification_status = $driverDoc->temp_doc_verification_status;
//                        $driverDoc->temp_document_file = null;
//                        $driverDoc->temp_expire_date = null;
//                        $driverDoc->temp_doc_verification_status = null;
//                        $driverDoc->save();
//                    }else{
//                        $notification_status = true;
//                        DriverDocument::where('id', $driverDoc->id)->update(['document_verification_status' => 4]);
//                    }
//                }
//                // code for driver notification
//                if ($notification_status) {
//                    $merchant_id = $value->merchant_id;
//                    $value->online_offline = 2;
//                    $value->save();
//
//                    $merchant = Merchant::find($merchant_id);
//                    $config = $merchant->Configuration;
//                    if (($config->existing_vehicle_enable == 1 && $merchant->demo != 1) || ($config->add_multiple_vehicle == 1 && $merchant->demo != 1)) {
//                        $driverVehicleDetails = DriverVehicle::with('Drivers')->whereHas('Drivers', function ($query) use ($value){
//                            $query->where('id',$value->id);
//                        })->where('vehicle_active_status',1)->first();
//                        if (!empty($driverVehicleDetails)){
//                            $drivers = $driverVehicleDetails->Drivers;
//                            $vehicleActiveStatus = array();
//                            foreach($drivers as $driverData){
//                                $vehicleActiveStatus[] = $driverData->online_offline == 1 ? 1 : 2;
//                            }
//                            if(!in_array(1,$vehicleActiveStatus)){
//                                $driverVehicleDetails->vehicle_active_status = 2;
//                                $driverVehicleDetails->save();
//                            }
//                        }
//                    }
//                    Onesignal::DriverPushMessage($value->id, [], trans('api.personal_document_expired'), 11, $merchant_id);
//                }
//            }
//        }
//    }
//
//    public function checkVehicleDocument()
//    {
//        $drivers = $this->getVehicleDocExpireAllDriver();
//        foreach ($drivers as $value) {
//            $notification_status = false;
//            foreach ($value->DriverVehicleDocument as $driverVehicleDocs){
//                if ($driverVehicleDocs->temp_document_file != null){
//                    $driverVehicleDocs->document = $driverVehicleDocs->temp_document_file;
//                    $driverVehicleDocs->expire_date = $driverVehicleDocs->temp_expire_date;
//                    $driverVehicleDocs->document_verification_status = $driverVehicleDocs->temp_doc_verification_status;
//                    $driverVehicleDocs->temp_document_file = null;
//                    $driverVehicleDocs->temp_expire_date = null;
//                    $driverVehicleDocs->temp_doc_verification_status = null;
//                    $driverVehicleDocs->save();
//                }else{
//                    $notification_status = true;
//                    DriverVehicleDocument::where('id','=', $driverVehicleDocs->id)->update(['document_verification_status' => 4]);
//                }
//            }
//            if ($notification_status){
//                $merchant_id = $value->merchant_id;
////                $value->save();
////                $driverdata = Driver::findorfail($value->owner_id);
////                if (!empty($driverdata)) {
////                    Onesignal::DriverPushMessage($driverdata->id, [], trans('api.vehicle_document_expired'), 12, $merchant_id);
////                }
//
//                $data['notification_type'] = "DOCUMENT_EXPIRED";
//                $data['segment_sub_group'] = NULL;
//                $data['segment_group_id'] = NULL;
//                $data['segment_type'] = "";
//                $data['segment_data'] = [];
//                $arr_param = ['driver_id' => $value->id, 'data' => $data, 'message' => trans("$string_file.document_expire_warning"), 'merchant_id' => $value->id, 'title' => trans("$string_file.personal_document_expired")];
//                Onesignal::DriverPushMessage($arr_param);
//
//            }
//        }
////        return redirect()->back()->with('PersonalExpire', trans('admin.vehicle_doc_expire_alert'));
//    }
//
//    public function getPersonalDocExpireAllDriver()
//    {
//        $expiry_date = date('Y-m-d');
//        $drivers = Driver::with(['DriverDocument' => function ($q) {
//            $q->where([['expire_date', '<', date('Y-m-d')],['document_verification_status','=',2]]);
//            }])
//            ->whereHas('DriverDocument', function ($query) use ($expiry_date) {
//            $query->where([['expire_date', '<', $expiry_date],['document_verification_status','=',2]]);
//             })
//            ->where([['driver_delete', '=', NULL]])->orderBy('id','DESC')->get();
//        return $drivers;
//    }
//
//    public function getVehicleDocExpireAllDriver()
//    {
//        $expiry_date = date('Y-m-d');
//        $expire_vehicles = DriverVehicle::with(['DriverVehicleDocument' => function ($query) use ($expiry_date) {
//            $query->where([['expire_date', '<', $expiry_date],['document_verification_status','=',2]]);
//            }])
//            ->whereHas('DriverVehicleDocument', function ($q) {
//            $q->where([['expire_date', '<', date('Y-m-d')],['document_verification_status','=',2]]);
//        })->orderBy('id','DESC')->get();
//
////        $expire_vehicles = $vehcileArray->map(function ($item, $key) {
////            $item->total_expire_document = count($item->DriverVehicleDocument);
////            return $item;
////        });
//        return $expire_vehicles;
//    }

    /*************** Subscription Package expire cron end **************/

    public function expireHandymanOrder()
    {
        $merchants = Merchant::whereHas("Segment", function ($query) {
            $query->where('segment_group_id', 2);
        })->get();
        if (!empty($merchants)) {
            foreach ($merchants as $merchant) {
                $handyman_orders = HandymanOrder::where([['merchant_id', '=', $merchant->id], ['booking_date', '<',date('Y-m-d')]])->whereIn('order_status',[1,4])->get();
                if (!empty($handyman_orders)) {
                    HandymanOrder::where([['merchant_id', '=', $merchant->id], ['booking_date', '<', date('Y-m-d')]])->whereIn('order_status',[1,4])->update(array('order_status' => 8));
                }
            }
        }
    }

    /*************** Referral System expire cron end **************/

    public function expireReferralSystem()
    {
        $ref_controller = new ReferralSystemController();
        $ref_controller->checkExpireReferralSystem();
    }

    /*************** Referral System expire cron end **************/


    // order delivery reminder for the day
    public function reminderNotificationForOrderDelivery()
    {
        DB::beginTransaction();
        try {
            $current_date = date('Y-m-d');
            $yesterday_date = date('Y-m-d',strtotime("-1 days"));
            $all_orders = Order::select('id','business_segment_id','merchant_id','service_time_slot_detail_id','order_timestamp','country_area_id','driver_id','order_status','segment_id','user_id','order_date','created_at')
                ->whereIn('order_status', [6,7,9,10])
                ->where(function($e) use($current_date,$yesterday_date){
                    $e->where('order_date','=',$current_date)
                    ->orWhere('order_date','=',$yesterday_date);
                })
                ->where('is_order_completed','!=',1)
//                ->where('segment_id','!=',3)
                ->get();
//            p($all_orders);
            if ($all_orders->isNotEmpty())
            {
                    foreach ($all_orders as $order)
                    {
                        $string_file = $this->getStringFile($order->merchant_id);
                        // reminder notification driver
                        $segment_data = [
                            'id'=>$order->id,
                            'order_status'=>$order->order_status,
                        ];
                        $data = array('order_id' => $order->id, 'notification_type' => 'ORDER_DELIVERY_REMINDER', 'segment_type' => $order->Segment->slag,'segment_data' => $segment_data);
                        $arr_param = array(
                            'driver_id' => $order->driver_id,
                            'data'=>$data,
                            'message'=>trans("$string_file.today_order_delivery_title"),
                            'merchant_id'=>$order->merchant_id,
                            'title' => trans("$string_file.today_order_delivery_message").' '.'#'.$order->merchant_order_id,
                        );
//                        p($arr_param);
                        Onesignal::DriverPushMessage($arr_param);
                    }
            }
            DB::commit();
        }catch (\Exception $e)
        {
            $message = $e->getMessage();
            DB::rollBack();
        }
    }

     // expire accepted orders which were not delivered on time
    public function expireAcceptedOrders()
    {
        DB::beginTransaction();
        try {
            $all_orders = Order::select('id','business_segment_id','merchant_id','service_time_slot_detail_id','order_timestamp','country_area_id','driver_id','order_status','segment_id','user_id','order_date','created_at')
                ->whereIn('order_status', [6,7,9,10])->get();
                // p($all_orders);
            if ($all_orders->isNotEmpty())
            {
                $order_ids = $all_orders->map(function ($item, $key) {
                    if($item->segment_id == 3) { // for food
                        $current_date =  date('Y-m-d');
                        $order_date = date('Y-m-d',strtotime($item->created_at));
                    }
                    else{
                        $current_date =  date('Y-m-d');
                        $order_date = $item->order_date;
                        // next date of order delivery date
                        $order_date = date("Y-m-d", strtotime("+1 day",strtotime($order_date)));
                    }
                    return ($current_date > $order_date  ) ? $item->id : null;
                })->filter()->values();
                // p($order_ids->toArray());
                if($order_ids->count() > 0)
                {
                    $log_data =[
                        'order_id'=>$order_ids->toArray(),
                        'request_type'=>"ongoing order expire"
                    ];
                    \Log::channel('per_day_cron_log')->emergency($log_data);

                    Order::whereIn('id', $order_ids->toArray())
                        ->update([
                            'order_status' => '12', //auto expired
                        ]);
                     $arr_orders = Order::
//                    select('id','merchant_id','driver_id','merchant_order_id','order_status','segment_id','user_id','payment_method_id','payment_option_id')->
                    whereIn('id', $order_ids->toArray())->get();
                    // p($arr_orders);
                    foreach ($arr_orders as $order)
                    {

                        // send notification to user like your order has been expired
                        $this->sendNotificationToUser($order);

                        // refund credit to user wallet if payment done while placing order
                        if(!empty($order->payment_method_id) && in_array($order->payment_method_id,[2,4,3]))
                        {
                            $user = User::select('wallet_balance','merchant_id','id')->where('id',$order->user_id)->first();
                            // p($user);
                            $user->wallet_balance = $user->wallet_balance + $order->final_amount_paid;
                            $user->save();
                            // send wallet credit notification
                            $paramArray = array(
                                'user_id' => $user->id,
                                'merchant_id' => $user->merchant_id,
                                'booking_id' => NULL,
                                'amount' => $order->final_amount_paid,
                                'order_id' => $order->id,
                                'narration' => 11,
                                'platform' => 2,
                                'payment_method' => $order->payment_method_id,
                                'payment_option_id' => $order->payment_option_id,
                                'transaction_id' => NULL
                            );

                           WalletTransaction::UserWalletCredit($paramArray);
                        }
                        // make driver free once order expired
                        $driver = $order->Driver;
                        $driver->free_busy = 2;
                        $driver->save();
                        // p($driver);
                    }
                }
            }
            DB::commit();
        }catch (\Exception $e)
        {
            $message = $e->getMessage();
            DB::rollBack();
        }
    }
}
