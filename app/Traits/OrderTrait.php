<?php

namespace App\Traits;

use App\Events\SendNewOrderRequestMailEvent;
use App\Http\Controllers\Helper\FindDriverController;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\BookingTransaction;
use App\Models\BusinessSegment\Order;
use App\Models\Driver;
use App\Models\EmailConfig;
use App\Models\EmailTemplate;
use App\Models\Onesignal;
use App\Models\User;
use App\Models\ServiceTimeSlotDetail;
use App\Models\PromoCode;
use Auth;
use Illuminate\Validation\Rule;
use Matrix\Exception;
use Validator;
use DB;
use App\Traits\MailTrait;
use App\Traits\MerchantTrait;
use View;

trait OrderTrait
{

    // send notification to driver
    use MailTrait,MerchantTrait;
    public function orderAcceptNotification($request, $order)
    {
        $order_id = $order->id;
        $string_file = $this->getStringFile(NULL,$order->Merchant);
        $request->request->add(['user_id' => $order->user_id, 'service_type_id' => $order->service_type_id, 'driver_vehicle_id' => $order->driver_vehicle_id]);

        if(!empty($order->User->login_type) && $order->User->login_type == 1 )
        {
            $latitude = $order->drop_latitude;
            $longitude = $order->drop_longitude;
            $request->request->add(['latitude'=>$latitude,'longitude'=>$longitude]);
        }

        $arr_driver = Driver::getDeliveryCandidate($request);
        $arr_driver_id = array_pluck($arr_driver, 'id');
        if (!empty($arr_driver_id)) {
            $request->request->add(['id' => $order_id, 'notification_type' => "NEW_ORDER"]);
            $this->sendNotificationToDriver($request, $arr_driver_id, $order);

            // entry in request table
            $findDriver = new FindDriverController();
            $findDriver->AssignRequest($arr_driver, null, $order_id);
            return trans("$string_file.order_assign_request");
        } else {
            throw new \Exception(trans("$string_file.seems_drivers_not_ready"));
        }
    }

    // send notification to user
    public function sendNotificationToUser($order, $message = '')
    {
        $order_status = $order->order_status;
        $user_id = $order->user_id;
        $data['notification_type'] = "ORDER";
        $data['segment_type'] = $order->Segment->slag;
        $data['segment_sub_group'] = $order->Segment->sub_group_for_app; // grocery
        $data['segment_group_id'] = $order->Segment->segment_group_id; //
        $data['segment_data'] = [
            'order_id' => $order->id,
            'order_status' => $order_status,
            'type' => in_array($order_status, [1, 6, 7, 9, 10]) ? 2 : 3, // 3 means past 2 means active
        ];
        $merchant_id = $order->merchant_id;
        $business_segment = $order->BusinessSegment->full_name;
        $driver_name = "";
        if (!empty($order->driver_id)) {
            $driver_name = $order->Driver->first_name . $order->Driver->last_name;
        }
        $order_number = $order->merchant_order_id;
        $item = $order->Segment;
        $large_icon = isset($item->Merchant[0]['pivot']->segment_icon) && !empty($item->Merchant[0]['pivot']->segment_icon) ? get_image($item->Merchant[0]['pivot']->segment_icon, 'segment', $merchant_id, true) :
            get_image($item->icon, 'segment_super_admin', NULL, false);

        $user = User::find($user_id);
        // p($user);
        setLocal($user->language);

        $segment_name = !empty($item->Name($merchant_id)) ? $item->Name($merchant_id) : $item->slag;
       // get string file
        $string_file = $this->getStringFile($merchant_id);

        $lang_order = trans("$string_file.order");

        $order_delivered = false;
        $title = "";
        $message = "";
        // title and message of notification based on order status
        switch ($order_status) {
            case "3":
                $title = $segment_name . ' ' . $lang_order . ' ' . trans("$string_file.rejected");
                $message = trans_choice("$string_file.order_rejected_user_message", 3, ['ID' => $order_number, '.' => $business_segment]);
                break;
            case "6":
                $title = $segment_name . ' ' . $lang_order . ' ' . trans("$string_file.accepted");
                $message = trans_choice("$string_file.order_accepted_by_driver", 3, ['ID' => $order_number, 'delivery' => $driver_name]);

                break;
            case "5":
                $title = $segment_name . ' ' . $lang_order . ' ' . trans("$string_file.cancelled");
                $message = trans_choice("$string_file.order_cancelled_by_message", 3, ['ID' => $order_number, '.' => $driver_name]);
                break;
            case "8":
                $title = $segment_name . ' ' . $lang_order . ' ' . trans("$string_file.cancelled");
                $message = trans_choice("$string_file.order_cancelled_by_message", 3, ['ID' => $order_number, '.' => $business_segment]);
                break;
            case "7":
                $title = trans("$string_file.arrived_at_pickup");
                $message = trans_choice("$string_file.arrived_at_pickup_message", 3, ['store' => $business_segment]);
                break;
            case "9": // order in process
                $title = $segment_name . ' ' .$lang_order . ' ' . trans("$string_file.in").' ' .trans("$string_file.process");
                $message = trans_choice("$string_file.order_in_process_message", 3, ['ID' => $order_number]);
                break;
            case "10": // order picked
                $title = $segment_name . ' ' .$lang_order . ' '.trans("$string_file.picked");
                $message = trans_choice("$string_file.order_picked_message", 3, ['successfully' => $business_segment]);
                break;
            case "11": // order delivered
                $title = $segment_name . ' ' . $lang_order . ' ' . trans("$string_file.delivered");
                $message = trans("$string_file.order_delivered_message");
                $order_delivered = true;
                break;
            case "12": // order auto Expired
                // order cancelled
                $title = $segment_name . ' ' . $lang_order . '  #' . $order_number . ' ' . trans("$string_file.cancelled");
                $message = trans_choice("$string_file.order_request_expired_message", 3, ['ID' => $order_number]);
                break;
        }
        $data['segment_data']['order_delivered'] = $order_delivered;
        $arr_param['user_id'] = $user_id;
        $arr_param['data'] = $data;
        $arr_param['message'] = $message;
        $arr_param['merchant_id'] = $merchant_id;
        $arr_param['title'] = $title; // notification title
        $arr_param['large_icon'] = $large_icon;
        Onesignal::UserPushMessage($arr_param);
        setLocal();
    }

    // send notification to driver
    public function sendNotificationToDriver($request, $arr_driver_id, $order)
    {
        $data['notification_type'] = $request->notification_type;
        $data['segment_type'] = $order->Segment->slag;
        $data['segment_sub_group'] = $order->Segment->sub_group_for_app; // its segment sub group for app
        $data['segment_group_id'] = $order->Segment->segment_group_id; // for handyman
        $order_status = $order->order_status;
        $item = $order->Segment;

        $business_segment = $order->BusinessSegment->full_name;
        $order_number = $order->merchant_order_id;
        $merchant_id = $order->merchant_id;
        $time_format = $order->Merchant->Configuration->time_format;

        $large_icon = isset($item->Merchant[0]['pivot']->segment_icon) && !empty($item->Merchant[0]['pivot']->segment_icon) ? get_image($item->Merchant[0]['pivot']->segment_icon, 'segment', $merchant_id, true) :
            get_image($item->icon, 'segment_super_admin', NULL, false);
        $segment_name = !empty($item->Name($merchant_id)) ? $item->Name($merchant_id) : $item->slag;

        $segment_data = [];
        $time = "";
        $service_time_slot_detail = ServiceTimeSlotDetail::find($order->service_time_slot_detail_id);
        if(!empty($service_time_slot_detail))
        {
            $start = strtotime($service_time_slot_detail->from_time);
            $start = $time_format == 2  ? date("H:i",$start) : date("h:i a",$start);
            $end = strtotime($service_time_slot_detail->to_time);
            $end =  $time_format == 2  ? date("H:i",$end) : date("h:i a",$end);
            $time = $start."-".$end;
        }

        $title = "";
        $message = "";
        if(!is_array($arr_driver_id)){
            $arr_driver_id = [$arr_driver_id];
        }
        // p($arr_driver_id);
        foreach($arr_driver_id as $driver_id){
            $driver = Driver::find($driver_id);
            setLocal($driver->language);

            // get string file
            $string_file = $this->getStringFile($merchant_id);
            $lang_order = trans("$string_file.order");
            $order_title = $order->Segment->Name($order->merchant_id) . ' ' . $lang_order;
            if ($order_status != 6) {
                $segment_data = [
                    "id" => $order->id,
                    "generated_time" => $order->order_timestamp,
                    "highlights" => [
                        'name' => $order_title,
                        'number' => $order_number,
                        'payment_mode' => $order->PaymentMethod->MethodName($merchant_id) ? $order->PaymentMethod->MethodName($merchant_id) : $order->PaymentMethod->payment_method,
                        //                    'payment_mode' => $order->PaymentMethod->payment_method,
                        'description' => trans("$string_file.total") . ' ' . $order->quantity . ' ' . trans("$string_file.items"),
                        'price' => $order->CountryArea->Country->isoCode . ' ' . $order->final_amount_paid,
                    ],
                    "pickup_details" => [
                        "header" => $order->BusinessSegment->full_name,
                        "locations" => [[
                            "lat" => $order->BusinessSegment->latitude,
                            "lng" => $order->BusinessSegment->longitude,
                            "address" => $order->BusinessSegment->address,
                        ]],
                    ],
                    "drop_details" => [
                        "header" => $order->User->first_name . ' ' . $order->User->last_name,
                        "locations" => [[
                            "lat" => $order->drop_latitude,
                            "lng" => $order->drop_longitude,
                            "address" => $order->drop_location,
                        ]],
                    ],
                    "timer" => $order->Merchant->BookingConfiguration->driver_request_timeout * 1000,
                    "cancel_able" => true,
                    "status" => $order_status,
                    'customer_details' => [],
                    'package_details' => (object)[],
                    'segment_type' => $order->Segment->slag,
                    'timing' => date(('d/M (D)'),strtotime($order->order_date))." ".$time,
                ]; // notification data
            }

            if (!empty($request->status_based) && $request->status_based == "NO") {
                $title = $segment_name . ' ' . $lang_order.' '.trans("$string_file.otp_verified");
                $message = trans_choice("$string_file.order_ready_to_pick_message", 3, ['ID' => $order_number]);
            } else {
                // title and message of notification based on order status
                switch ($order_status) {
                    case "1":
                        $title = trans("$string_file.new") . ' ' . $segment_name . ' ' . $lang_order;
                        $message = trans("$string_file.new_order_driver_message");
                        break;
                    case "2":
                        $title = $segment_name . ' ' . $lang_order . ' ' . trans("$string_file.cancelled");
                        $message = trans_choice("$string_file.order_cancelled_by_message", 3, ['ID' => $order_number, '.' => trans("$string_file.user")]);
                        break;
                    case "8":
                        $title = $segment_name . ' ' . $lang_order . ' ' . trans("$string_file.cancelled");
                        $message = trans_choice("$string_file.order_cancelled_by_message", 3, ['ID' => $order_number, '.' => $business_segment]);
                        break;
                    case "9":
                        $title = $segment_name . ' ' . $lang_order;
                        $message = trans_choice("$string_file.order_in_process_driver_message", 3, ['ID' => $order_number, '.' => $business_segment]);
                        break;
                    case "4":
                        break;
                    case "6":
                        // send order expired status to other driver, except order accepted
                        $title = $segment_name . ' ' . $lang_order.' '.trans("$string_file.expired");
                        $message = trans("$string_file.order_already_accepted");
                        break;
                    case "5":
                        break;
                }
            }
            $data['segment_data'] = $segment_data;
            $arr_param = ['driver_id' => $driver_id, 'data' => $data, 'message' => $message, 'merchant_id' => $merchant_id, 'title' => $title, 'large_icon' => $large_icon];
            Onesignal::DriverPushMessage($arr_param);
        }
        setLocal();
        return true;
    }

    function saveOrderStatusHistory($request, $order_obj, $order_id = NULL)
    {
        if (!empty($order_obj->id)) {
            $order = $order_obj;
        } else {
            $order = Order::select('id', 'order_status', 'order_status_history')->Find($order_id);
        }
        if (!empty($order->id)) {
            $new_status = [
                'order_status' => $order->order_status,
                'order_timestamp' => time(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ];
            if (empty($order->order_status_history)) {
                $order->order_status_history = json_encode([$new_status]);
                $order->save();
            } else {
                $status_history = json_decode($order->order_status_history, true);
                array_push($status_history, $new_status);
                $order->order_status_history = json_encode($status_history);
                $order->save();
            }
        }
        return true;
    }


    // send notification to driver
    public function sendPushNotificationToWeb($request, $order)
    {
        $business_seg = $order->BusinessSegment;
        $merchant_id = $business_seg->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $lang_order = trans("$string_file.order");
        $onesignal_redirect_url = route('business-segment.today-order');
        // upcoming orders
        $order_date = $order->order_date;
        if(!empty($order_date) && $order_date > date("Y-m-d"))
        {
            $onesignal_redirect_url = route('business-segment.upcoming-order');
        }
        $player_id = array_pluck($business_seg->webOneSignalPlayerId->where('status', 1), 'player_id');
        $segment_name = !empty($order->Segment->Name($merchant_id)) ? $order->Segment->Name($merchant_id) : $order->Segment->slag;
        $title = trans("$string_file.new") . ' ' . $segment_name . ' ' . $lang_order;
        $message = trans("$string_file.new_order_driver_message");
        $data = ['id'=>$order->id];
        Onesignal::MerchantWebPushMessage($player_id, $data, $message, $title, $merchant_id, $onesignal_redirect_url);
        return true;
    }

    public function getDistanceSlab($request, $delivery_charge_slabs)
    {
        $for = $request->for;
        $distance = $request->distance;
        if (!empty($distance)) {
            $total_cart_amount = $request->cart_amount;
            $user_distance = $distance / 1000; //convert m into km
            $arr_condition = \Config::get('custom.condition');
            foreach ($delivery_charge_slabs as $slab) {
                if ($user_distance >= $slab['distance_from'] && $user_distance <= $slab['distance_to']) {
                    if ($for == 1) // for driver
                    {
                        return $slab;
                    } else   // for user
                    {
                        switch($slab['condition']){
                            case "1":
                                if($total_cart_amount < $slab['cart_amount']){
                                    return $slab;
                                }
                                break;
                            case "2":
                                if($total_cart_amount == $slab['cart_amount']){
                                    return $slab;
                                }
                                break;
                            case "3":
                                if($total_cart_amount > $slab['cart_amount']){
                                    return $slab;
                                }
                                break;
                            case "4":
                                if($total_cart_amount <= $slab['cart_amount']){
                                    return $slab;
                                }
                                break;
                            case "5":
                                if($total_cart_amount >= $slab['cart_amount']){
                                    return $slab;
                                }
                                break;
                            default:
                                return [];
                                break;
                        }
                    }
                }
            }
        }
        return [];
    }

    public function orderTransaction($request, $order)
    {
        if (!empty($order->id)) {
            $order_transaction = BookingTransaction::where([['order_id', '=', $order->id]])->first();
            if (empty($order_transaction)) {
                $order_transaction = new BookingTransaction;
                $order_transaction->merchant_id = $order->merchant_id;
            }
            if ($order->order_status == 6) {
                $before_discount = $order->cart_amount + $order->delivery_amount + $order->tax;
                $order_transaction->order_id = $order->id;
                $order_transaction->date_time_details = date('Y-m-d H:i:s');
                $order_transaction->sub_total_before_discount = $before_discount;
                $order_transaction->discount_amount = $order->discount_amount;
                $order_transaction->tax_amount = $order->tax;
                $order_transaction->booking_fee = $order->cart_amount;
                $order_transaction->tip = $order->tip_amount;
                $order_transaction->cash_payment = $order->final_amount_paid;
                $order_transaction->customer_paid_amount = $order->final_amount_paid;
                $order_transaction->commission_type = $order->BusinessSegment->commission_type;

            } elseif ($order->order_status == 11) {
                // merchant vs restaurant commission
                $commission = $order->BusinessSegment->commission;
                $cart_amount = $order->cart_amount;

                // merchant commission amount
                // Flat Commission
                $merchant_cart_commission_amount = 0;
                if($order->BusinessSegment->commission_method == 1){
                    if($cart_amount >= $commission){
                        $merchant_cart_commission_amount = $commission;
                    }else{
                        $merchant_cart_commission_amount = $cart_amount;
                    }
                }elseif($order->BusinessSegment->commission_method == 2){
                    // Percentage Commission
                    $merchant_cart_commission_amount = ($commission * $cart_amount) / 100;
                }

                // business segment commission amount
                $bs_cart_commission_amount = $cart_amount - $merchant_cart_commission_amount;

                $merchant_total_commission = (($merchant_cart_commission_amount + $order->delivery_amount) - $order->discount_amount);

                $delivery_service = $order->BusinessSegment->delivery_service;
                // driver calculation
                $driver_commission = 0;
//                if ($delivery_service == 2) {
                    $bill_details = json_decode($order->bill_details, true);
                    $driver_bill = isset($bill_details['driver']) ? $bill_details['driver'] : [];
                    if (!empty($driver_bill)) {
                        $driver_commission = $driver_bill['pick_up_fee'] + $driver_bill['drop_off_fee'] + $driver_bill['slab_amount'];
                    }
//                }
                $order_transaction->company_earning = $merchant_cart_commission_amount;
                $order_transaction->driver_earning = $driver_commission;
                $order_transaction->business_segment_earning = $bs_cart_commission_amount;

                // total paid amount to driver merchant and business segment
                $order_transaction->company_gross_total = ($merchant_total_commission); // including delivery charge
                $delivery_boy_total_commission = $driver_commission + $order->tip_amount + $order->discount_amount;
                if(!empty($order->Driver->driver_agency_id))
                {
                    $order_transaction->driver_agency_total_payout_amount = $delivery_boy_total_commission;
                }
                else
                {
                    $order_transaction->driver_total_payout_amount = $delivery_boy_total_commission;
                }

                $order_transaction->business_segment_total_payout_amount = $bs_cart_commission_amount + $order->tax;
            }
            $order_transaction->save();

            return $order_transaction;
        }
        return false;
    }

  public Function getOrderStatus($req_param)
    {
        $string_file="";$slug="";$bs_name = "";$for = "";
        if(isset($req_param['string_file']))
        {
            $string_file =  $req_param['string_file'];
        }
        else
        {
            $merchant_id = $req_param['merchant_id'];
            $string_file =  $this->getStringFile($merchant_id);
        }

      if(isset($req_param['slug']) && $req_param['slug'] == "FOOD")
        {
            $store_string = trans("$string_file.restaurant");
        }
      else
      {
          $store_string = trans("$string_file.store");
      }

        $order_string = trans("$string_file.order");
        $cancelled_string = trans("$string_file.cancelled");
        $rejected_string = trans("$string_file.rejected");
        $accepted_string = trans("$string_file.accepted");
        $arrived_string = trans("$string_file.arrived");
        $picked_string = trans("$string_file.picked");
        $completed_string = trans("$string_file.delivered");
        $by_string = trans("$string_file.by");
        $user_string = trans("$string_file.user");
        $driver_string = trans("$string_file.driver");
        $auto_string = trans("$string_file.auto");
        $expired_string = trans("$string_file.expired");
        $at_string = trans("$string_file.at");
        $in_string = trans("$string_file.in");
        $process_string = trans("$string_file.process");
        $admin_string = trans("$string_file.admin");
      return   array(
        '1' =>trans("$string_file.new").' '.$order_string,
        '2' =>$cancelled_string.' '.$by_string.' '.$user_string,
        '3' =>$rejected_string.' '.$by_string.' '.$store_string,
        '4' =>$accepted_string.' '.$by_string.' '.$store_string,
        '12' =>$auto_string.' '.$expired_string, // Order expired because no one(either restaurant or driver) has taken action
        '6' =>$accepted_string.' '.$by_string.' '.$driver_string,
        '7' =>$arrived_string.' '.$at_string.' '.$store_string,
        '9' =>$order_string.' '.$in_string.' '.$process_string, //Queue//Kitchen
        '10' =>$order_string.' '.$picked_string, //picked from store
        '11' =>$order_string.' '.$completed_string,
        '5' =>$cancelled_string.' '.$by_string.' '.$driver_string,
        '8' =>$cancelled_string.' '.$by_string.' '.$admin_string, // here admin means Restaurant/Store
        );
    }

    public function cancelOrderByBusinessSegment($request, $business_seg)
    {
        DB::beginTransaction();
        try {
            $order = Order::Find($request->order_id);
            $string_file = $this->getStringFile(NULL,$order->Merchant);
            if(!empty($order->id))
            {
            $merchant_id = $business_seg->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
            $request->request->add(['id'=>$order->id,'notification_type'=>"CANCEL_ORDER",'latitude'=>$business_seg->latitude,'longitude'=>$business_seg->longitude]);
            $order->order_status = 8;
            /**
             * if payment done when order placed then credit to user wallet
            **/
            if($order->payment_status == 1)
            {
                $amount = $order->final_amount_paid;
                $order->refund = 1;
                $paramArray = [
                    'order_id'=> $order->id,
                    'amount'=> $amount,
                    'user_id'=> $order->user_id,
                    'narration'=> $amount,
                ];
                WalletTransaction::UserWalletCredit($paramArray);
            }
            $order->save();

            // save status history
            $this->saveOrderStatusHistory($request,$order);

            if(!empty($order->driver_id))
            {
                $driver = $order->Driver;
                $driver->free_busy = 2; // driver is free now
                $driver->save();

                $arr_driver_id = [$order->driver_id];
                $this->sendNotificationToDriver($request,$arr_driver_id,$order);
            }
        }
            else
            {
                $message = trans("$string_file.not_found");
                throw new \Exception($message);
            }
        }catch(\Exception $e)
        {
            DB::rollback();
          throw new \Exception($e->getMessage());
        }
        DB::commit();
        /**send notification to user*/
        $this->sendNotificationToUser($order);
        return  trans_choice("$string_file.order_cancelled_by_message",['ID'=>$order->merchant_order_id,'.'=>$order->BusinessSegment->full_name]);
    }

    public function rejectOrderByBusinessSegment($request, $business_seg)
    {
        try {
            $order = Order::Find($request->order_id);
            $string_file = $this->getStringFile(NULL,$order->Merchant);
            if(!empty($order) && $order->order_status == 1)
            {
                $request->request->add(['id'=>$order->id,'latitude'=>$business_seg->latitude,'longitude'=>$business_seg->longitude]);
                $order->order_status = 3;
                $order->save();
                if($order->payment_status == 1)
                {
                    $amount = $order->final_amount_paid;
                    $order->refund = 1;
                    $paramArray = [
                        'order_id'=> $order->id,
                        'amount'=> $amount,
                        'user_id'=> $order->user_id,
                        'narration'=> $amount,
                    ];
                    WalletTransaction::UserWalletCredit($paramArray);
                }
                // save status history
                $this->saveOrderStatusHistory($request,$order);

                /**send notification to user*/
                $this->sendNotificationToUser($order);
            }
            else
            {
                $message = trans("$string_file.not_found");
                throw new \Exception($message);
            }
        }catch(\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
        return true;
    }

    // order pickup otp verification
    public function orderOTPVerification($request)
    {
        $validator = Validator::make($request->all(),[
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders', 'id')->where(function ($query) {
                    $query->whereIn('order_status',[7,9]);
                }),
            ],
            'otp' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new \Exception($errors[0]);
        }
        DB::beginTransaction();
        try{
            $order = Order::find($request->order_id);
            $string_file = $this->getStringFile(NULL,$order->Merchant);
            if(!empty($order))
            {
                $order_history = array_column(json_decode($order->order_status_history,true),'order_status');
                $merchant_id = $order->merchant_id;
                $string_file = $this->getStringFile($merchant_id);
                if(in_array(9,$order_history))
                {
                    if($order->confirmed_otp_for_pickup == 2){
                        if($order->otp_for_pickup == $request->otp){
                            $order->confirmed_otp_for_pickup = 1;
                            $order->otp_for_pickup = NULL;
                            $order->save();
                            $request->request->add(['notification_type'=>"READY_FOR_PICKUP",'status_based'=>"NO"]);
                            $arr_driver_id = $order->driver_id;
                            $this->sendNotificationToDriver($request,$arr_driver_id,$order);
                        }else{
                            throw new \Exception(trans("$string_file.invalid_otp_try_again"));
                        }
                    }else{
                        throw new \Exception(trans("$string_file.otp_already_verify"));
                    }
                }
                else
                {
                    throw new \Exception(trans("$string_file.process_order_before_verification"));
                }
            }
            else
            {
                $message = trans("$string_file.not_found");
                throw new \Exception($message);
            }
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
        DB::commit();
        return trans("$string_file.otp_verified");
    }

    // order start order processing
    public function orderProcessing($request,$business_seg)
    {
        $validator = Validator::make($request->all(),[
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders', 'id')->where(function ($query) {
                    $query->whereIn('order_status',[6,7]);
                }),
            ],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new \Exception($errors[0]);
        }
        DB::beginTransaction();
        try{
            $order = Order::Find($request->order_id);
            $merchant_id = $order->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
            if(!empty($order))
            {
            $order_number = $order->merchant_order_id;
            $business_segment_name = $business_seg->full_name;
            $request_status = 9;
                if(($order->order_status == 6 || $order->order_status == 7)  && !empty($order->id)) {
                    $request->request->add(['id'=>$order->id,'notification_type'=>"ORDER_PROCESS_START",'latitude'=>$business_seg->latitude,'longitude'=>$business_seg->longitude]);
                    $order->order_status = $request_status;
                    $order->save();

                    // save status history
                    $this->saveOrderStatusHistory($request,$order);


                    if(!empty($order->driver_id))
                    {
                        $arr_driver_id = [$order->driver_id];
                        $this->sendNotificationToDriver($request,$arr_driver_id,$order);
                    }
                }
                else
                {
                    $message = trans("$string_file.order_not_found");
                    throw new \Exception($message);
                }
            }
            else
            {
                $message = trans("$string_file.not_found");
                throw new \Exception($message);
            }
        }catch (\Exception $e){
            DB::rollback();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        /**send notification to user*/
        $this->sendNotificationToUser($order);
        $success_message = trans_choice("$string_file.order_in_process_driver_message", 3, ['ID' => $order_number, '.' => $business_segment_name]);
        return $success_message;
    }

    function sendNewOrderMail($order, $order_from = 'FOOD')
    {
        event(new SendNewOrderRequestMailEvent($order));
//        $data['order'] = $order;
//        $temp = EmailTemplate::where('merchant_id', '=', $order->merchant_id)->where('template_name', '=', "invoice")->first();
//        $data['temp'] = $temp;
//        $order_request = View::make('mail.new-order-request')->with($data)->render();
//        $configuration = EmailConfig::where('merchant_id', '=', $order->merchant_id)->first();
//        $response = $this->sendMail($configuration, $order->BusinessSegment->email, $order_request, 'new_order', $order->Merchant->BusinessName,NULL,$order->Merchant->email);
        return true;
    }

//    public function checkPromoCode($request)
//    {
//        $user = $request->user('api');
//        $user_id = $user->id;
//        $promo_code = $request->promo_code;
//        $merchant_id = $request->merchant_id;
//        $promocode = PromoCode::where([['segment_id','=',$request->segment_id],['promoCode', '=', $promo_code], ['merchant_id', '=', $merchant_id], ['promo_code_status', '=', 1]])->whereNull('deleted')->first();
//        // p($promocode);
//        if (empty($promocode)) {
//            throw new \Exception (trans("$string_file.invalid_promo_code"));
//            // return $this->failedResponse(trans("$string_file.invalid_promo_code"));
//        }
//        $validity = $promocode->promo_code_validity;
//        $start_date = $promocode->start_date;
//        $end_date = $promocode->end_date;
//        $currentDate = date("Y-m-d");
//        if ($validity == 2 && ($currentDate < $start_date || $currentDate > $end_date)) {
//            throw new \Exception (trans("$string_file.promo_code_expired_message"));
//            // return $this->failedResponse(trans("$string_file.promo_code_expired_message"));
//        }
//        $promo_code_limit = $promocode->promo_code_limit;
//        $total_usage = Order::select('id','promo_code_id','user_id')->where([['promo_code_id', '=', $promocode->id]])
//            ->whereIn('order_status',[1,6,7,9,10,11])->get();
//        $all_uses = !empty($total_usage) ? $total_usage->count() : 0;
//        if (!empty($all_uses)) {
//            if ($all_uses >= $promo_code_limit) {
//                throw new \Exception (trans("$string_file.user_limit_promo_code_expired"));
//            }
//            $promo_code_limit_per_user = $promocode->promo_code_limit_per_user;
//            $used_by_user = $total_usage->where('user_id', $user_id)->count();
//            if ($used_by_user >= $promo_code_limit_per_user) {
//                throw new \Exception (trans("$string_file.user_limit_promo_code_expired"));
//            }
//        }
//        $applicable_for = $promocode->applicable_for;
//        if ($applicable_for == 2 && $user->created_at < $promocode->updated_at)
//        {
//            throw new \Exception (trans("$string_file.promo_code_for_new_user"));
//            // return $this->failedResponse(trans("$string_file.promo_code_for_new_user"));
//        }
//        $order_minimum_amount = $promocode->order_minimum_amount;
//        if (!empty($request->order_amount) && $request->order_amount < $order_minimum_amount) {
//            $message = trans_choice("$string_file.promo_code_order_value", 3, ['AMOUNT' => $order_minimum_amount]);
//            throw new \Exception ($message);
//        }
//        return array('status' => true, 'promo_code' => $promocode);
//    }
}
