<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\ReferralController;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Http\Controllers\PaymentMethods\Payment;
use App\Models\Booking;
use App\Models\BookingRating;
use App\Models\BookingRequestDriver;
use App\Models\BookingTransaction;
use App\Models\BusinessSegment\BusinessSegment;
use App\Models\BusinessSegment\Order;
use App\Models\Driver;
use App\Models\PriceCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Onesignal;
use App\Models\CancelReason;
use DB;
use App\Http\Controllers\Helper\GoogleController;
use App\Traits\OrderTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Models\EmailConfig;
use App\Models\EmailTemplate;
use mysql_xdevapi\Exception;
use View;
use DateTime;
use DateInterval;

class OrderController extends Controller
{
    // response of order api
    use OrderTrait,ApiResponseTrait;
    public function orderInfoResponse(Request $request, $order)
    {
        $icon = "";
        $order_description = "";
        $button_text = "";
        $path_type = "STILL";
        $string_file = $this->getStringFile(NULL,$order->Merchant);
        $user_address = $order->drop_location;
        $business_segment_address = $order->BusinessSegment->address;
        $business_segment_name = $order->BusinessSegment->full_name;
        $drop_location = [];
        $dummy_google_result = ['total_distance' => "0", 'total_distance_text' => "", 'total_time' => "0", 'total_time_minutes' => "", 'total_time_text' => "", 'image' => "","poly_points"=>""];
        $call_google_api = false;
        if(in_array($order->order_status,  [5,6]))
        {
            $call_google_api = true;
            $drop_location[0] = [
                'drop_latitude'=>$order->BusinessSegment->latitude,
                'drop_longitude'=>$order->BusinessSegment->longitude,
                'drop_location'=>$business_segment_address,
            ];
        }
        else
        {
            $call_google_api = true;
            $drop_location[0] = [
                'drop_latitude'=>$order->drop_latitude,
                'drop_longitude'=>$order->drop_longitude,
                'drop_location'=>$user_address,
            ];

        }
        if($call_google_api == true)
        {
//            "AIzaSyC7lIIgBajzx409vxmmY_CJPcRvDb114w4";//
            $google_key = $order->Merchant->BookingConfiguration->google_key;
            $google_result = GoogleController::GoogleStaticImageAndDistance($request->latitude, $request->longitude, $drop_location, $google_key,"",$string_file );
            if (empty($google_result) && $order->User->login_type != 1) {
                $message = "Sorry order can't be placed because, delivery address is out of service area";//trans("$string_file.google_key_not_working");
//                $message = trans("$string_file.google_key_not_working");
                throw new \Exception($message);
            }

            if($order->order_status == 6)
            {
                // in case of demo request
                if(empty($google_result) || !is_array($google_result))
                {
                    $google_result = $dummy_google_result;
                }
                $order->estimate_driver_distance = $google_result['total_distance_text'];
                $order->estimate_driver_time = $google_result['total_time_text'];
                //$order_description = $google_result['total_distance_text'].' | '.$google_result['total_time_text'];
                $order->save();
            }
            $order_description = $google_result['total_distance_text'].' | '.$google_result['total_time_text'];
        }

        if(empty($google_result) || !is_array($google_result))
        {
            $google_result = $dummy_google_result;
        }

        $cancel_able = false;
        if($order->order_status < 7 )
        {
            $cancel_able = true;
        }

        $merchant_id = $order->merchant_id;
        $order_details = [
            'order_name'=>$order->Segment->Name($merchant_id) .' '.trans($string_file.".order"),
            'order_id'=>$order->id,
            'order_number'=>$order->merchant_order_id,
            'segment_id'=>$order->segment_id,
            'segment_group_id'=>$order->Segment->segment_group_id,
            'segment_sub_group'=>$order->Segment->sub_group_for_app,
            'payment_mode' => $order->PaymentMethod->MethodName($merchant_id) ? $order->PaymentMethod->MethodName($merchant_id) : $order->PaymentMethod->payment_method,
//            'payment_mode'=>$order->PaymentMethod->payment_method,
            'order_description'=>$order_description,
            'order_price'=>$order->CountryArea->Country->isoCode .' '.$order->final_amount_paid,
            'cancel_able'=>$cancel_able,
            'otp_for_pickup' => !empty($order->otp_for_pickup) ? $order->otp_for_pickup : "",
            'confirmed_otp_for_pickup' => $order->confirmed_otp_for_pickup == 1 ? true : false,
//            'widget_image'=>isset($order->Segment->Merchant[0]['pivot']->icon) && !empty($order->Segment->Merchant[0]['pivot']->icon) ? get_image($order->Segment->Merchant[0]['pivot']->icon, 'segment', $order->merchant_id, true) :
//                get_image($order->Segment->icon, 'segment_super_admin', NULL, false)
            'widget_image'=>isset($order->BusinessSegment->business_logo) && !empty($order->BusinessSegment->business_logo) ? get_image($order->BusinessSegment->business_logo, 'business_logo', $order->merchant_id, true) :
                get_image($order->Segment->icon, 'segment_super_admin', NULL, false)
        ];
        // its object in case of taxi and delivery, but array in case of food and grocery because of app ui
        $customer_details[] = [
            'customer_name'=>$order->User->first_name .' '.$order->User->last_name,
            'customer_image'=>get_image($order->User->UserProfileImage,'user',$order->merchant_id,true,false),
            'customer_phone'=>$order->User->UserPhone,
        ];
        $user_name = $order->User->first_name .' '.$order->User->last_name;

        /*******Some strings translations ********/
        $sos_string = trans("$string_file.sos");
        $order_string = trans($string_file.".order");
        $pick_order_string = trans($string_file.".pick").' '.$order_string;
        $moving_to_pickup_string = trans($string_file.".moving_towards_pickup").' '.$business_segment_name;
        $goto_pickup_string = trans("$string_file.arrived_at_pickup").' '.$business_segment_name;

        $action_buttons[] = [
            'button_icon'=>$user_name,
            'button_text'=>trans("$string_file.customer_support"),
            'button_text_colour'=>"FFFFFF",
            'button_action'=>$sos_string,
        ];

        // $order_status_action =  [6,7,10,11];
        // if($order->Segment->slag == "FOOD")
        // {
        $order_status_action =  [6,7,9,10,11];
        // }

        $product_list = $order->OrderDetail;
        $product_data =[];
        foreach($product_list as $product)
        {
            $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
            $product_data[] =['value'=> $product->quantity.' X '.$product->Product->Name($order->merchant_id) .'('.$product->ProductVariant->weight .' '.$unit.')'];
        }
        $order_status_holders= [];
        $req_param['string_file'] = $string_file;
        $config_status = $this->getOrderStatus($req_param);
        $pending_icon = view_config_image("static-images/inactive-status.png");
        $completed_icon = view_config_image("static-images/tic-with-white-back.png");
        $current_icon = view_config_image("static-images/working-on.png");

        $order_status = [];
        $arr_completed_order = [];
        if(!empty($order->order_status_history))
        {
            $status_completed = json_decode($order->order_status_history,true);
            $status_completed =  array_column($status_completed,NULL,'order_status');
            $arr_completed_order =array_keys($status_completed);
        }

        $arr_completed_steps =$status_completed;
//            array_column(json_decode($order->order_status_history,true),'order_status');
        $api_to_call = "";
        foreach($order_status_action as $order_status)
        {
            $status_description = [];
            $completed_time = "";
//            $api_to_call = "";
            $moving_to_pickup = [];
            if($order_status == 6)
            {
                if(in_array($order_status,$arr_completed_order))
                {
                    $completed_time = $status_completed[$order_status]['order_timestamp'];
                    $icon = $completed_icon;
                    $api_to_call="ARRIVE_AT_STORE";

                }
                if($order->order_status == $order_status)
                {
                    $descriptive_text = !empty($business_segment_address) ? [['value'=>$business_segment_address]] : [];
                    $status_description = [
                        "highlighted_text"=>$business_segment_name,
                        "descriptive_text"=>$descriptive_text,
                        "navigation_visibility"=>true,
                    ];

                    $moving_to_pickup = [
                        'status_time'=>"",
                        'tick_icon'=>$current_icon,
                        'status_text'=>$moving_to_pickup_string,
                        'status_description'=>!empty($status_description) ? [$status_description] : $status_description,
                    ];

                    $status_description = [];
                    $button_text= $goto_pickup_string;
                }


            }
            elseif($order_status == 7)
            {
                //p($order->order_status);
                if($order->order_status == $order_status)
                {
                    $status_description= [
                        "highlighted_text"=>$business_segment_name,
                        "descriptive_text"=>$product_data,
                        "navigation_visibility"=>false,
                    ];
                    $path_type = "ANIMATED";
                    $icon = $current_icon;
                    $button_text=$pick_order_string ;
                    $api_to_call="PICK_ORDER";
                }
                elseif(in_array($order_status,$arr_completed_order))
                {
                    $completed_time = $status_completed[$order_status]['order_timestamp'];
                    $icon = $completed_icon;

                }
                else
                {
                    $icon = $pending_icon;
                }

            }
            elseif($order_status == 9)
            {
                if($order->order_status == $order_status)
                {
                    $path_type = "ANIMATED";
                    $status_description= [
                        "highlighted_text"=>$business_segment_name,
                        "descriptive_text"=>$product_data,
                        "navigation_visibility"=>false,
                    ];
                    $icon = $current_icon;
                    if(in_array(7,array_keys($arr_completed_steps)))
                    {
                        $button_text= $pick_order_string;
                        $api_to_call="PICK_ORDER";

                    }
                    else
                    {
                        $button_text= $goto_pickup_string;
                        $api_to_call="ARRIVE_AT_STORE";
                    }
                }
                elseif(in_array($order_status,$arr_completed_order))
                {
                    $completed_time = $status_completed[$order_status]['order_timestamp'];
                    $icon = $completed_icon;

                }
                else
                {
                    $icon = $pending_icon;
                }
            }
            elseif($order_status == 10 )
            {
                if($order->order_status == $order_status)
                {
                    $path_type="STILL";
                    $descriptive_text = !empty($user_address) ? [['value'=>$user_address]] : [];
                    $status_description= [
                        "highlighted_text"=>$user_name,
                        "descriptive_text"=>$descriptive_text,
                        "navigation_visibility"=>true,
                    ];
                    $icon = $current_icon;
                    $button_text = trans("$string_file.deliver").' '.$order_string;
                    $api_to_call="DELIVER_ORDER";
                }

                else if(in_array($order_status,$arr_completed_order))
                {
                    $completed_time = $status_completed[$order_status]['order_timestamp'];
                    $icon = $completed_icon;

                }
                else
                {
                    $icon = $pending_icon;
                }

            }
            elseif($order_status == 11 )
            {
                if(in_array($order_status,$arr_completed_order))
                {
                    $completed_time = $status_completed[$order_status]['order_timestamp'];
                    $icon = $completed_icon;
                }
                elseif($order->order_status == $order_status)
                {
                    $icon = $current_icon;
                }
                else
                {
                    $icon = $pending_icon;
                }
            }

            $status_text =  $config_status[$order_status];
            $order_status_holders[] = [
                'status_time'=>$completed_time,
                'tick_icon'=>$icon,
                'status_text'=>$status_text,
                'status_description'=>!empty($status_description) ? [$status_description] : $status_description,
            ];
            if($order->order_status == $order_status && $order_status == 6 && !empty($moving_to_pickup))
            {
                array_push($order_status_holders,$moving_to_pickup);
            }


        }

//        if($order->order_status <= 7)
        if(!in_array(7, $arr_completed_order))
        {
            $destination_latitude = $order->BusinessSegment->latitude;
            $destination_longitude = $order->BusinessSegment->longitude;
        }
        else
        {
            $destination_latitude = $order->drop_latitude;
            $destination_longitude = $order->drop_longitude;
        }
        $return_data = [
            'order_details'=> $order_details,
            'customer_details'=> $customer_details,
            'order_status_holders'=> $order_status_holders,
            'order_current_status'=> $order->order_status,
            'button_text'=>$button_text,
            'api_to_call'=>$api_to_call,
            'status_button_type'=> "STRICT", //SLIDER
            'destination_location'=> [
                'lat'=>$destination_latitude,
                'lng'=>$destination_longitude,
            ],
            'path_type'=>$path_type, //NA,ANIMATED,STILL
            'poly_line'=> $google_result['poly_points'], //NA,ANIMATED,STILL
            "action_buttons"=>$action_buttons,
        ];
        return $return_data;
    }

    // get order information
    public function getOrderInformation(Request $request)
    {
        try {
            $order_obj = new Order;
            $order = $order_obj->getOrderInfo($request);
            $return_data = $this->orderInfoResponse($request,$order);
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \Exception($message);
        }
        return $return_data;
    }

    // order accept/ reject api
    public function orderAcceptReject(Request $request)
    {

        $driver =$request->user('api-driver');
        $string_file = $this->getStringFile($driver->merchant_id);
        $driver_id = $request->id;
        $order_id = $request->booking_order_id;


        DB::beginTransaction();
        try {
            $request_status = $request->status;
//            $order = Order::Find($request->id);
            $order = Order::sharedLock()->Find($request->id);

            if(empty($order))
            {
                $message = trans("$string_file.order_not_found");
                throw new \Exception($message);
            }

            if ($order->order_status == 6) {
                throw new \Exception(trans("$string_file.order_already_accepted"));
            }

            //p($driver->id);
            $booking_request =  BookingRequestDriver::where([['order_id',"=",$order->id],['driver_id',"=",$driver->id]])->first();
            // p($booking_request);
            $driver_request_status = null;
            $message = "";
            $order_update = false;
            $data = [];
            if($order->order_status == 1  && !empty($order->id))
            {
                if($request_status == "REJECT") // rejecting or by passing  order request
                {
                    $message = trans("$string_file.rejected");
                    $driver_request_status = 3; //rejecting request

                }
                elseif ($request_status == "ACCEPT") // accepting order request
                {
                    // p($request->all(),0);
                    $order_status = 6; // accepted by driver
//                    $status_history[] = [
//                        'order_status'=>$order_status,
//                        'order_timestamp'=>time(),
//                        'latitude'=>$request->latitude,
//                        'longitude'=>$request->longitude,
//                    ];

                    // this message is not not using anywhere
                    $message = trans("$string_file.accepted");
                    $driver_request_status = 2; // accepting request
                    $order->order_status = $order_status;
                    $order->driver_id = $driver->id;

                    // change driver status
                    $driver->free_busy = 1;
                    $driver->save();

                    $order->driver_vehicle_id = $request->driver_vehicle_id;

                    // driver bill details, if driver is of merchant
//                    if($order->BusinessSegment->delivery_service == 2)
//                    {
                        $user_drop_location[0] = [
                            'drop_latitude'=>$order->drop_latitude,
                            'drop_longitude'=>$order->drop_longitude,
                            'drop_location'=>"",
                        ];
                        $google_key = $order->Merchant->BookingConfiguration->google_key;

                        // distance b/w store and user,
                        // FOR GOOGLE ERROR HANDLING if google api will not work for demo then we will calculate distance b/w user and driver for distance calculation
                        $store_lat = $order->BusinessSegment->latitude;
                        $store_long = $order->BusinessSegment->longitude;
                        $dummy_google_result = ['total_distance' => "0", 'total_distance_text' => "", 'total_time' => "0", 'total_time_minutes' => "", 'total_time_text' => "", 'image' => "","poly_points"=>""];
                        $user_distance = GoogleController::GoogleStaticImageAndDistance($store_lat, $store_long, $user_drop_location, $google_key,"",$string_file);
//                        if (empty($user_distance) && $order->User->login_type !=1) {
//                            $message = "Sorry order can't be placed because, delivery address is out of service area";//trans("$string_file.google_key_not_working");
//                            throw new \Exception($message);
//                        }
                        if(empty($user_distance))
                        {
                            $user_distance = $dummy_google_result;
                        }
                        $order->estimate_distance = $user_distance['total_distance_text'];
                        $order->estimate_time = $user_distance['total_time_text'];

                        $order->travel_distance = $user_distance['total_distance_text'];
                        $order->travel_time = $user_distance['total_time_text'];

                        // get driver price card
                        $price_card = PriceCard::where([['status', '=', 1], ['country_area_id', '=', $order->country_area_id], ['merchant_id', '=', $order->merchant_id], ['service_type_id', '=', $order->service_type_id], ['segment_id', '=', $order->segment_id],['price_card_for','=',1]])->first();
                        $driver_distance = $user_distance['total_distance']; // distance in meter
                        if(empty($price_card))
                        {
                            throw new \Exception(trans("$string_file.no_price_card_for_area"));
                        }
                        $distance_charges = 0;
                        $price_card_detail_id = NULL;
                        $delivery_charge_slabs = $price_card->PriceCardDetail->toArray();

                        $request->request->add(['for'=>1,'distance'=>$driver_distance,'cart_amount'=>NULL]);
                        $slab = $this->getDistanceSlab($request,$delivery_charge_slabs);
                        if(isset($slab['id']) && isset($slab['slab_amount']))
                        {
                            $distance_charges = $slab['slab_amount'];
                            $price_card_detail_id = $slab['id'];
                        }

                        $bill_details  = json_decode($order->bill_details,true);
                        $driver_bill = ['price_card_detail_id'=>$price_card_detail_id,'slab_amount'=>$distance_charges,'distance'=>$order->travel_distance,'pick_up_fee'=>$price_card->pick_up_fee,'drop_off_fee'=>$price_card->drop_off_fee];
                        $bill_details['driver'] = $driver_bill;
                        $order->bill_details = json_encode($bill_details);
                        $order->save();
//                    }
                    // entry in booking transaction
                    $this->orderTransaction($request,$order);
                    // save status history
                    $this->saveOrderStatusHistory($request,$order);

                    // send already order accepted notification to other drivers
                    //in some cases same player  id will be used for driver accepted or rejected then it has to be exclude from list
                    $ongoing_request_drivers = BookingRequestDriver::select('driver_id')
                        ->with(['Driver' => function ($q) use($driver) {
                                  return $q->where('player_id','!=',$driver->player_id);
                         }])
                        ->whereHas('Driver' , function ($q) use($driver) {
                            return $q->where('player_id','!=',$driver->player_id);
                        })
                        ->where([['order_id', '=', $order->id], ['request_status', '=', 1]])->get();
                    $ids = array_pluck($ongoing_request_drivers, 'driver_id');
                    if(!empty($ids))
                    {
                        $request->request->add(['notification_type'=>'BOOKING_ACCEPTED_BY_OTHER_DRIVER']);
                        $this->sendNotificationToDriver($request,$ids,$order);
                    }

                    //send onesignal message to restro
                    $data = array('booking_id' => $order->id, 'notification_type' => 'ORDER_ACCEPTED', 'segment_type' => "ORDER_ACCEPTED",'segment_data' => time(),'notification_gen_time' => time());
                    $arr_param = array(
                        'business_segment_id' => $order->business_segment_id,
                        'data'=>$data,
                        'message'=>trans('food.order').''.trans('food.accepted'),
                        'merchant_id'=>$order->merchant_id,
                        'title' => trans("common.order").' '.trans("common.accepted")
                    );
                    Onesignal::BusinessSegmentPushMessage($arr_param);
                }
                if(!empty($booking_request))
                {
                    $booking_request->request_status = $driver_request_status;
                    $booking_request->save();
                }

                $order_obj = new Order;
                $order = $order_obj->getOrderInfo($request);
                $data = $this->orderInfoResponse($request,$order);
            }
            else
            {
                $message = trans("$string_file.order_already_accepted");
                throw new \Exception($message);
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
        // mark reject for all drivers where
        BookingRequestDriver::where('order_id',$request->id)->whereIn('driver_id',[$driver->id])->update(['request_status'=>3]);
        DB::commit();
        /**send notification to user*/
        $this->sendNotificationToUser($order,$message);
        $return_data['message'] = $message;
        $return_data['data'] = $data;
        return $return_data;
    }

    // get list of ongoing orders of driver
    public function getOngoingOrders(Request $request)
    {
        $data = [];
        try {
            $driver =$request->user('api-driver');
            $merchant_id = $driver->merchant_id;
            $string_file = $this->getStringFile($merchant_id,$driver->Merchant);
            $order_string = trans("$string_file.order");
            $order_obj = new Order;
            $orders = $order_obj->getDriverOngoingOrders($request);
            foreach ($orders as $order)
            {
                $merchant_segment =  $order->Segment->Merchant->where('id',$order->merchant_id);
                $merchant_segment = collect($merchant_segment->values());
                $order_info = [
                    'id'=>$order->id,
                    'status'=>$order->order_status,
                    'segment_name'=>$order->Segment->Name($order->merchant_id) .' '.$order_string,
                    'segment_slug'=>$order->Segment->slag,
                    'segment_group_id'=>$order->Segment->segment_group_id,
                    'segment_sub_group'=>$order->Segment->sub_group_for_app,
                    'number'=>$order->merchant_order_id,
                    'segment_service'=>$order->ServiceType->ServiceName($order->merchant_id),//"Normal Food Delivery",
                    'time'=>$order->order_timestamp,
                    'segment_image'=>isset($merchant_segment[0]['pivot']->segment_icon) && !empty($merchant_segment[0]['pivot']->segment_icon) ? get_image($merchant_segment[0]['pivot']->segment_icon, 'segment', $order->merchant_id, true,false) :
                        get_image($order->Segment->icon, 'segment_super_admin', NULL, false,false)
                ];
                $user_info = [
                    'user_name'=>$order->User->first_name .' '.$order->User->last_name,
                    'user_image'=>get_image($order->User->UserProfileImage,'user',$order->merchant_id),
                    'user_phone'=>$order->User->UserPhone,
                    'user_rating'=>$order->User->rating,
                ];
                $pickup = $order->BusinessSegment;
                $pick_details = [
                    'lat'=>$pickup->latitude,
                    'lng'=>$pickup->longitude,
                    'address'=>$pickup->address,
                    'icon'=>view_config_image("static-images/pick-icon.png"),

                ];
                $drop_details = [
                    'lat'=>$order->drop_latitude,
                    'lng'=>$order->drop_longitude,
                    'address'=>$order->drop_location,
                    'icon'=>view_config_image("static-images/drop-icon.png"),
                ];
                $payment_details = [
                    'payment_mode' => $order->PaymentMethod->MethodName($merchant_id) ? $order->PaymentMethod->MethodName($merchant_id) : $order->PaymentMethod->payment_method,
//                    'payment_mode'=>$order->PaymentMethod->payment_method,
                    'amount'=>$order->CountryArea->Country->isoCode .' '.$order->final_amount_paid,
                    'paid'=>$order->payment_status == 1 ? true : false,
                ];
                $data[] = [
                    'info'=> $order_info,
                    'user_info'=> $user_info,
                    'pick_details'=> $pick_details,
                    'drop_details'=> $drop_details,
                    'payment_details'=> $payment_details,
                ];
            }
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            return $message;
        }
        return $data;
    }

    // driver arrived at pickup location
    public function arrivedAtPickup(Request $request)
    {
        DB::beginTransaction();
        try {
            $request_status = 7; //$request->status;
            $order = Order::Find($request->id);
//            either order accepted or processed
            if(in_array($order->order_status,[6,9])  && !empty($order->id)) {
                $order_id_verification = $order->Merchant->Configuration->order_id_verification;
                $message = trans('api.reached_at_pickup'); // not using
                $order->order_status = $request_status;
                $order->otp_for_pickup = $order_id_verification == 1 ? $order->merchant_order_id : rand(1000,9999);
                $order->save();

                // save status history
                $this->saveOrderStatusHistory($request,$order);

                $order_obj = new Order;
                $order = $order_obj->getOrderInfo($request);
                $data = $this->orderInfoResponse($request,$order);
            }
            else
            {
                $string_file = $this->getStringFile($order->merchant_id,$order->Merchant);
                $message = trans("$string_file.order_not_found");
                throw new \Exception($message);
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
        DB::commit();
        /**send notification to user*/
        $this->sendNotificationToUser($order,$message);
        $return_data['message'] = $message;
        $return_data['data'] = $data;
        return $return_data;
    }

    // order in kitchen
    public function orderInProcess(Request $request)
    {
        DB::beginTransaction();
        try {
            $request_status = $request->status;
            $order = Order::Find($request->id);
            $string_file = $this->getStringFile($order->merchant_id);

            if($order->order_status == 7  && !empty($order->id)) {
//                $status_history = json_decode($order->order_status_history, true);
//
//                $new_status = [
//                    'order_status' => $request_status,
//                    'order_timestamp' => time(),
//                    'latitude' => $request->latitude,
//                    'longitude' => $request->longitude,
//                ];
//                array_push($status_history, $new_status);
                $message = trans('api.order_in_process');
                $order->order_status = $request_status;
//                $order->order_status_history = json_encode($status_history);
                $order->save();

                // save status history
                $this->saveOrderStatusHistory($request,$order);



                $order_obj = new Order;
                $order = $order_obj->getOrderInfo($request);
                $data = $this->orderInfoResponse($request,$order);
            }
            else
            {
                $message = trans("$string_file.order_not_found");
                throw new \Exception($message);
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
        DB::commit();
        /**send notification to user*/
        $this->sendNotificationToUser($order,$message);
        $return_data['message'] = $message;
        $return_data['data'] = $data;
        return $return_data;
    }

    // order picked from store or restaurant
    public function pickedOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            $request_status = $request->status;
            $order = Order::Find($request->id);
            $string_file = $this->getStringFile($order->merchant_id);

            if(in_array($order->order_status,[7,9]) && !empty($order->id) && $order->confirmed_otp_for_pickup == 1) {
//                $message = trans('api.order_picked');
                $order->order_status = $request_status;
//                $order->order_status_history = json_encode($status_history);
                $order->save();

                // save status history
                $this->saveOrderStatusHistory($request,$order);


                $order_obj = new Order;
                $order = $order_obj->getOrderInfo($request);
                $data = $this->orderInfoResponse($request,$order);
            }
            else
            {
                $message = trans("$string_file.order_not_found");
                throw new \Exception($message);
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
        DB::commit();
        /**send notification to user*/
        $this->sendNotificationToUser($order);
        $return_data['message'] = trans("$string_file.success");
        $return_data['data'] = $data;
        return $return_data;
    }

    // order delivered to user
    public function deliverOrder(Request $request)
    {
        DB::beginTransaction();
        try {
//            $request_status = $request->status;
            $order = Order::Find($request->id);
            $string_file = $this->getStringFile($order->merchant_id);
            if(($order->order_status == 10) && !empty($order->id) && $order->is_order_completed !=1) {
                $message = trans('api.order_delivered');
                $order->order_status = 11;
                $order->save();

                // save status history
                $this->saveOrderStatusHistory($request,$order);

                // send completed mail to user
                $data['order'] = $order;
                $temp = EmailTemplate::where('merchant_id', '=', $order->merchant_id)->where('template_name', '=', "invoice")->first();
                $data['temp'] = $temp;
                $invoice_html = View::make('mail.order-invoice')->with($data)->render();
                $configuration = EmailConfig::where('merchant_id', '=', $order->merchant_id)->first();
                $response = $this->sendMail($configuration, $order->User->email, $invoice_html, 'order_invoice', $order->Merchant->BusinessName,NULL,$order->Merchant->email,$string_file);

                // update transaction table
                $order_transaction = $this->orderTransaction($request,$order);

                $order = $order->fresh();

                //Referral Calculation
                $ref = new ReferralController();
                $arr_params = array(
                    "segment_id" => $order->segment_id,
                    "driver_id" => $order->driver_id,
                    "user_id" => $order->user_id,
                    "order_id" => $order->id,
                    "user_paid_amount" => $order->final_amount_paid,
                    "driver_paid_amount" => $order->final_amount_paid,
                    "check_referral_at" => "OTHER"
                );
                $ref->checkReferral($arr_params);

                // Make the payment of driver instant
//                if ($order->payment_status == 1) {
//                    $array_param = array(
//                        'order_id' => $order->id,
//                        'driver_id' => $order->driver_id,
//                        'amount' => $order_transaction->driver_earning,
//                        'payment_method_type' => $order->PaymentMethod->payment_method_type,
//                        'discount_amount' => $order_transaction->discount_amount,
//                    );
//                    $driverPayment = new CommonController();
//                    $driverPayment->DriverRideAmountCredit($array_param);
//
//                    if($order->payment_method_id == 1){
//                        $paramArray = array(
//                            'driver_id' => $order->driver_id,
//                            'order_id' => $order->id,
//                            'amount' => $order_transaction->customer_paid_amount,
//                            'narration' => 13,
//                        );
//                        WalletTransaction::WalletDeduct($paramArray);
//                    }
//                }

                $order_obj = new Order;
                $order = $order_obj->getOrderInfo($request);
                $data = $this->orderInfoResponse($request,$order);

                //send onesignal message to restro
                $data = array('order_id' => $order->id, 'notification_type' => 'ORDER_DELIVERED', 'segment_type' => $order->Segment->slag,'segment_data' => []);
                $arr_param = array(
                    'business_segment_id' => $order->business_segment_id,
                    'data'=>$data,
                    'message'=>trans("$string_file.order_delivered_message"),
                    'merchant_id'=>$order->merchant_id,
                    'title' => trans("$string_file.order_delivered")
                );
                Onesignal::BusinessSegmentPushMessage($arr_param);

            }
            else
            {
                $message = trans("$string_file.order_not_found");
                throw new \Exception($message);
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
        DB::commit();
        /**send notification to user*/
        $this->sendNotificationToUser($order,$message);
        $return_data['message'] = $message;
        $return_data['data'] = $data;
        return $return_data;
    }

    // get payment and rating order info
    public function orderPaymentInfo(Request $request)
    {
        try {
            $order = Order::Find($request->id);
            if(($order->order_status == 11) && !empty($order->id) && $order->is_order_completed !=1) {

                $product_list = $order->OrderDetail;
                $product_data =[];
                foreach($product_list as $product)
                {
                    $unit = isset($product->weight_unit_id) ?  $product->WeightUnit->WeightUnitName : "";
                    $variant_name =  $product->ProductVariant->Name($order->merchant_id);
                    $variant = "";
                    if(!empty($variant_name))
                    {
                        $variant = '('.$variant_name.')';
                    }
                    $product_data[] =['value'=> $product->quantity.' X '.$product->Product->Name($order->merchant_id) .' ('.$product->ProductVariant->weight .' '.$unit.') '.$variant];
                }
                $order_details = [
                    'order_id'=>$order->id,
                    'order_number'=>$order->merchant_order_id,
                    'order_items'=>$product_data,
                ];
                $payment_details = [
                    'payment_method_id'=>$order->PaymentMethod->id,
                    'payment_mode' => $order->PaymentMethod->MethodName($order->merchant_id) ? $order->PaymentMethod->MethodName($order->merchant_id) : $order->PaymentMethod->payment_method,
//                    'payment_mode'=>$order->PaymentMethod->payment_method,
                    'payment_status'=>$order->payment_status == 1 ? true : false,
                    'order_price'=>$order->CountryArea->Country->isoCode .' '.$order->final_amount_paid,
                ];
                $customer_details[] = [
                    'customer_name'=>$order->User->first_name .' '.$order->User->last_name,
                    'customer_image'=>get_image($order->User->UserProfileImage,'user',$order->merchant_id),
                    'customer_phone'=>$order->User->UserPhone,
                ];
                $address_details[] = [
                    'value'=>$order->drop_location,
                ];

                $data = [
                    'order_details'=>$order_details,
                    'payment_details'=>$payment_details,
                    'customer_details'=>$customer_details,
                    'address_details'=>$address_details,
                    'rating_mandatory'=>false,
                ];
            }
            else
            {
                $string_file = $this->getStringFile($order->merchant_id);
                $message = trans("$string_file.order_not_found");
                throw new \Exception($message);
            }
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        $return_data['data'] = $data;
        return $return_data;
    }

    // update order payment info
    public function updateOrderPaymentStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $request_status = $request->payment_status;
            $order = Order::Find($request->id);
            if($order->order_status == 11  && !empty($order->payment_status != 1))
            {
                $order->payment_status = $request_status;
                $order->save();
                $order_transaction = $order->OrderTransaction;
                // In case of Cash Payment Method, Do the payment here
                if($order->payment_method_id == 1){
                    $payment = new Payment();
//                    $currency = $order->User->Country->isoCode;
                    $currency = $order->CountryArea->Country->isoCode;
                    $array_param = array(
                        'order_id' => $order->id,
                        'payment_method_id' => $order->payment_method_id,
                        'amount' => $order->final_amount_paid,
                        'user_id' => $order->user_id,
                        'card_id' => $order->card_id,
                        'quantity' => $order->quantity,
                        'order_name' => $order->merchant_order_id,
                        'currency' => $currency,
                        'booking_transaction' => $order_transaction,
                        'driver_sc_account_id' => $order->Driver->sc_account_id
                    );
                    $payment->MakePayment($array_param);
                }
            }
            else
            {
                $string_file = $this->getStringFile($order->merchant_id);
                $message = trans("$string_file.order_not_found");
                throw new \Exception($message);
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
        DB::commit();
//        $return_data['message'] = $message;
//        $return_data['data'] = $data;
        return true;
    }

// order delivered to user
    public function completeOrder(Request $request)
    {
        $data= [];
        DB::beginTransaction();
        try {
            $order = Order::Find($request->id);
            $string_file = $this->getStringFile($order->merchant_id);
            if(($order->order_status == 11) && !empty($order->id) && $order->payment_status == 1) {
                $order->is_order_completed = 1;
                $order->save();
                // change driver status
                $driver = $request->user('api-driver');
                $driver->free_busy = 2; // driver is free now
                $driver->save();

                $message = trans('api.order_completed');

                // rate to user by driver
                $rating = BookingRating::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'driver_rating_points' => $request->rating,
                        'driver_comment' => $request->comment
                    ]
                );
                $user_id = $order->user_id;
                $avg = BookingRating::whereHas('Order', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                })->avg('driver_rating_points');
                $user = $order->User;
                $user->rating = round($avg, 2);
                $user->save();

                $this->orderSettlement($order);
            }
            else
            {

                $message = trans("$string_file.order_not_found");
                throw new \Exception($message);
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
        DB::commit();
        $return_data['message'] = $message;
        $return_data['data'] = $data;
        return $return_data;
    }


    //cancel order by driver
    public function cancelOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            $request_status = $request->status;
            $order = Order::Find($request->id);
            $driver =$request->user('api-driver');
            $string_file = $this->getStringFile($order->merchant_id);
            if($order->order_status < 10  && !empty($order->id))
            {
//                $status_history = json_decode($order->order_status_history, true);
//                $new_status = [
//                    'order_status' => $request_status,
//                    'order_timestamp' => time(),
//                    'latitude' => $request->latitude,
//                    'longitude' => $request->longitude,
//                ];
//                    array_push($status_history, $new_status);
                $booking_request = BookingRequestDriver::where(['driver_id'=>$driver->id,'order_id'=>$request->id])->first();
                $booking_request->request_status = 4;
                $booking_request->save();

                $message = trans('api.order_cancelled');
                $order->order_status = $request_status;
                $order->cancel_reason_id = $request->cancel_reason_id;
//                    $order->order_status_history = json_encode($status_history);
                $order->save();

                // save status history
                $this->saveOrderStatusHistory($request,$order);

                // change driver status
                $driver->free_busy = 2; // driver is free now
                $driver->save();

                // refund amount o user wallet if payment was online/wallet
                if(!empty($order->payment_method_id) && in_array($order->payment_method_id,[2,4,3]))
                {
                    $user = $order->User;
                    $user->wallet_balance = $user->wallet_balance + $order->final_paid_amount;
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
                    // p($paramArray);
                    WalletTransaction::UserWalletCredit($paramArray);
                }

                //send onesignal message to restro
                $data = array('order_id' => $order->id, 'notification_type' => 'ORDER_CANCELLED', 'segment_type' => $order->Segment->slag,'segment_data' => []);
                $arr_param = array(
                    'business_segment_id' => $order->business_segment_id,
                    'data'=>$data,
                    'message'=>trans("$string_file.order_cancelled_message"),
                    'merchant_id'=>$order->merchant_id,
                    'title' => trans("$string_file.order_cancelled")
                );
                Onesignal::BusinessSegmentPushMessage($arr_param);
            }
            else
            {
                $message = trans("$string_file.order_not_found");
                throw new \Exception($message);
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
        DB::commit();
        /**send notification to user*/
        $this->sendNotificationToUser($order,$message);
        $return_data['message'] = $message;
        $return_data['data'] = [];
        return $return_data;
    }

    // get list of active orders of driver
    public function getActiveOrders(Request $request)
    {
        $return_data = [];
        $data = [];
        try {
            $order_obj = new Order;
            $orders = $order_obj->getDriverOngoingOrders($request);

            $driver =$request->user('api-driver');
            $string_file = $this->getStringFile($driver->merchant_id);
            $req_param = ['string_file'=>$string_file];
            $config_status = $this->getOrderStatus($req_param);
            foreach ($orders as $order)
            {
                $order_history = json_decode($order->order_status_history,true);
                $order_history = array_column($order_history,NULL,'order_status');
                $merchant_segment =  $order->Segment->Merchant->where('id',$order->merchant_id);
                $merchant_segment = collect($merchant_segment->values());
                $order_details = [
                    'id'=>$order->id,
                    'number'=>$order->merchant_order_id,
                    'segment_slug'=>$order->Segment->slag,
                    'name'=>$order->Segment->Name($order->merchant_id) .' '.trans("$string_file.order"),
                    'segment_image'=>isset($merchant_segment[0]['pivot']->segment_icon) && !empty($merchant_segment[0]['pivot']->segment_icon) ? get_image($merchant_segment[0]['pivot']->segment_icon, 'segment', $order->merchant_id, true,false) :
                        get_image($order->Segment->icon, 'segment_super_admin', NULL, false,false),
                    'status'=>$order->order_status,
                    'status_text'=>$config_status[$order->order_status],
                    'updated_timestamp'=>$order_history[$order->order_status]['order_timestamp'],
                    'status_description'=>$order->ServiceType->serviceName,

                ];
                $pickup = $order->BusinessSegment;
                $pick_details = [
                    'pick_image'=>"",
                    'pick_lat'=>$pickup->latitude,
                    'pick_lng'=>$pickup->longitude,
                    'pick_address'=>$pickup->address,

                ];
                $drop_details = [
                    'drop_lat'=>$order->drop_latitude,
                    'drop_lng'=>$order->drop_longitude,
                    'drop_address'=>$order->drop_location,
                    'drop_image'=>"",
                ];

                $order_vehicle = Booking::VehicleDetail($order);
                $data[] = [
                    'details'=> $order_details,
                    'pick_details'=> $pick_details,
                    'drop_details'=> $drop_details,
                    'vehicle_details'=> [$order_vehicle],
                ];
            }
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \Exception($message);
        }
        return $data;
    }

    // get list of past orders of driver
    public function getPastOrders(Request $request)
    {
        $data = [];
        try {
            $order_obj = new Order;
            $merchant_id = $request->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
            $request->request->add(['pagination' => true]);
            $orders = $order_obj->getDriverPastOrders($request);
            $driver = $request->user('api-driver');
            //p($driver->id);
            if(!empty($orders) && $orders->count() == 0)
            {
                throw new \Exception(trans("$string_file.no_past_orders"));
            }
            $req_param['string_file'] = $string_file;
            $config_status = $this->getOrderStatus($req_param);
//                \Config::get('custom.order_status');
            foreach ($orders as $order)
            {
                $order_history = json_decode($order->order_status_history,true);
                $order_history = array_column($order_history,NULL,'order_status');
                $order_details = [
                    'id'=>$order->id,
                    'number'=>$order->merchant_order_id,
                    'segment_slug'=>$order->Segment->slag,
                    'name'=>$order->Segment->Name($order->merchant_id) .' '.trans("$string_file.order"),
                    'segment_image'=>isset($order->Segment->Merchant[0]['pivot']->icon) && !empty($order->Segment->Merchant[0]['pivot']->icon) ? get_image($order->Segment->Merchant[0]['pivot']->icon, 'segment', $order->merchant_id, true) :
                        get_image($order->Segment->icon, 'segment_super_admin', NULL, false),
                    'status'=>$order->order_status,
                     'status_text'=>($order->driver_id == $driver->id) ? $config_status[$order->order_status] : trans("$string_file.order_completed_by_other_delivery_boy"),
                    'updated_timestamp'=>$order_history[$order->order_status]['order_timestamp'],
                    'status_description'=>$order->ServiceType->serviceName,
                ];
                $pickup = $order->BusinessSegment;
                $pick_details = [
                    'pick_image'=>"",
                    'pick_lat'=>$pickup->latitude,
                    'pick_lng'=>$pickup->longitude,
                    'pick_address'=>$pickup->address,

                ];
                $drop_details = [
                    'drop_lat'=>$order->drop_latitude,
                    'drop_lng'=>$order->drop_longitude,
                    'drop_address'=>$order->drop_location,
                    'drop_image'=>"",
                ];

                $order_vehicle = Booking::VehicleDetail($order);
                $data[] = [
                    'details'=> $order_details,
                    'pick_details'=> $pick_details,
                    'drop_details'=> $drop_details,
                    'vehicle_details'=>[$order_vehicle],
                ];
            }

            $orders = $orders->toArray();
            $next_page_url = isset($orders['next_page_url']) && !empty($orders['next_page_url']) ? $orders['next_page_url'] : "";
            $current_page = isset($orders['current_page']) && !empty($orders['current_page']) ? $orders['current_page'] : 0;

            $response['data'] =[
                'current_page'=>$current_page,
                'next_page_url'=>$next_page_url,
                'response_data'=>$data
            ];
            $response['message'] = trans("$string_file.data_found");
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \Exception($message);
        }
        return $response;
    }

    // get  order details of driver
    public function getOrderDetails(Request $request)
    {
        $data = [];
        try {
            $order_obj = new Order;
            $order = $order_obj->getOrderInfo($request);
            $string_file = $this->getStringFile($order->merchant_id);
            $req_param = ['string_file'=>$string_file];
            $config_status = $this->getOrderStatus($req_param);

            $business_segment = $order->BusinessSegment->full_name;
            $currency = $order->CountryArea->Country->isoCode;
            $order_history = json_decode($order->order_status_history,true);
            $order_history = array_column($order_history,NULL,'order_status');
            $map_image = "";
            if(!empty($order->Merchant->ApplicationConfiguration->map_on_order_details) && $order->Merchant->ApplicationConfiguration->map_on_order_details == 1)
            {
                $map_image = "https://maps.googleapis.com/maps/api/staticmap?center=".$order->BusinessSegment->address."&size=600x400&maptype=roadmap&markers=color:green%7Clabel:G%7C".$order->BusinessSegment->latitude.','.$order->BusinessSegment->longitude."&markers=color:red%7Clabel:C%7C".$order->drop_latitude.','.$order->drop_longitude."&key=".$order->Merchant->BookingConfiguration->google_key;
            }

            $order_details = [
                'id'=>$order->id,
                'number'=>$order->merchant_order_id,
                'segment_slug'=>$order->Segment->slag,
                'segment_group_id'=>$order->Segment->segment_group_id,
                'segment_sub_group'=>$order->Segment->sub_group_for_app,
                'segment_id'=>$order->segment_id,
                'status'=>$order->order_status,
                'status_text'=>$config_status[$order->order_status],
                'map_image'=>$map_image,
                'pickup_location'=>$order->BusinessSegment->address,
                'drop_location'=>$order->drop_location,
            ];
            $trip_details = [];
            foreach ($order_history as $status)
            {
                $trip_details[] = [
                    'status_time'=>date('H:i a',$status['order_timestamp']),
                    'status_value'=>$config_status[$status['order_status']],
                    'location' => '',
                ];
            }
            $vehicle_details[] = [
                'vehicle_type_image'=>get_image($order->DriverVehicle->VehicleType->vehicleTypeImage,'vehicle',$order->merchant_id),
                'vehicle_type'=>$order->DriverVehicle->VehicleType->VehicleTypeName,
                'vehicle_model'=>$order->DriverVehicle->VehicleModel->VehicleModelName,
                'vehicle_number'=>$order->DriverVehicle->vehicle_number,
            ];

            $product_list = $order->OrderDetail;
            $bill_details =[];
            foreach($product_list as $product)
            {
                $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
                $variant = "";
                if(!empty($product->ProductVariant->Name($order->merchant_id)))
                {
                    $variant =  '('.$product->ProductVariant->Name($order->merchant_id).')';
                }
                // check options
                $arr_option = json_decode($product->options,true);
                $option_amount  = 0;
                $option_name  = "";
                if(!empty($arr_option))
                {
                  $option_name = implode(',',array_column($arr_option,'option_name'));
                  $option_name = ' + '.$option_name;
                  $option_amount = array_sum(array_column($arr_option,'amount'));
                }
                $item_text = $product->quantity .' '.$unit;
                if(isset($product->ProductVariant->weight) && $product->ProductVariant->weight != ""){
                    $item_text = "(".$product->quantity ." * ".$product->ProductVariant->weight." $unit)";
                }
                $bill_details[] = [
                    'name'=> $item_text.' '.$product->Product->Name($order->merchant_id).$variant.$option_name,
                    'value'=> round_number($product->price + $option_amount),
                    'bold'=> false,
                ];
//
            }
            $delivery_charges = [
                'name'=>trans("$string_file.delivery_charge"),
                'value'=> $order->delivery_amount,
                'bold'=> false,
            ];
            array_push($bill_details,$delivery_charges);

            $tax = [
                'name'=>trans("$string_file.tax"),
                'value'=> $order->tax,
                'bold'=> false,
            ];
            array_push($bill_details,$tax);

//            $cart_amount = [
//                'name'=> trans("$string_file.cart_amount"),
//                'value'=> $order->cart_amount,
//                'bold'=> false,
//            ];
//            array_push($bill_details,$cart_amount);
            $tip_amount = [
                'name'=> trans("$string_file.tip"),
                'value'=> !empty($order->tip_amount) ? $order->tip_amount : "0.0",
                'bold'=> false,
            ];
            array_push($bill_details,$tip_amount);
             $discount_amount = [
                'name'=> trans("$string_file.discount_amount"),
                'value'=> $order->discount_amount,
                'bold'=> false,
            ];
            array_push($bill_details,$discount_amount);

            $final_amount = [
                'name'=> trans("$string_file.grand_total"),
                'value'=> $currency.$order->final_amount_paid,
                'bold'=> true,
            ];
            array_push($bill_details,$final_amount);
            $distance1 = trans("$string_file.accepted_location_to").' '.$business_segment;
            $distance2 = $business_segment.' '.trans("$string_file.to_user");
            $distance_details = [
                ['name'=>trans("$string_file.distance").' 1 : '.$distance1,'value'=>$order->estimate_driver_distance,'bold'=>false],
                ['name'=>trans("$string_file.distance").' 2 : '.$distance2,'value'=>$order->estimate_distance,'bold'=>false],
//                    ['name'=>trans('api.travelled_distance'),'value'=>$order->travel_distance,'bold'=>true]
            ];

            $payment_details = [
                'paid_status'=>$order->payment_status == 1 ? true : false,
                'payment_mode' => $order->PaymentMethod->MethodName($order->merchant_id) ? $order->PaymentMethod->MethodName($order->merchant_id) : $order->PaymentMethod->payment_method,
//                'payment_mode'=>$order->PaymentMethod->payment_method,
                'amount'=>$currency.$order->final_amount_paid,
                'currency'=>$currency,
            ];
            $hide_user_info_from_driver = $order->Merchant->ApplicationConfiguration->hide_user_info_from_driver;
            if($hide_user_info_from_driver == 1)
            {
                $default_string = "*****";
                $customer_details = [
                    'customer_name'=>$default_string,
                    'customer_image'=>$default_string,
                    'customer_phone'=>$default_string,
                    'rating'=>$order->User->rating,
                ];
            }
            else
            {
                $customer_details = [
                    'customer_name'=>$order->User->first_name .' '.$order->User->last_name,
                    'customer_image'=>get_image($order->User->UserProfileImage,'user',$order->merchant_id),
                    'customer_phone'=>$order->User->UserPhone,
                    'rating'=>$order->User->rating,
                ];
            }

            $action =  $order->is_order_completed == 1 || in_array($order->order_status,[2,3,5,8]) ? false : true;
            $arr_action= [];
            if($action == true)
            {
                if($order->order_status == 11)
                {
                    $action = "COMPLETE";
                    $message = trans("$string_file.complete_order");
                }
                else
                {
                    $action = "TRACK";
                    $message = trans("$string_file.track_order");
                }
                $arr_action[] = [
                    'button_type'=>"FULL_WIDTH",
                    'action'=>$action,
                    'icon'=>"",
                    'text'=>$message,
                    'color'=>"3498DB",
                ];
            }
            $data = [
                'details'=> $order_details,
                'trip_details'=> $trip_details,
                'vehicle_details'=> $vehicle_details,
                'bill_details'=> $bill_details,
                'meter_details'=> $distance_details,
                'payment_details'=> $payment_details,
                'customer_details'=> $customer_details,
                'action_buttons'=>$arr_action,
                'middle_drop' => []
            ];

        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \Exception($message);
        }
        return $data;
    }

    // order receipt
    public function orderReceipt(Request $request)
    {
        $request_fields = [
            'order_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        try{
            $order_id = $request->order_id;
            $order = Order::find($order_id);
            $string_file = $this->getStringFile($order->merchant_id);
            if($order->order_status !=11)
            {

                return $this->failedResponse(trans("$string_file.order_not_found"));
            }

            $tip_status = false;
            if($order->Merchant->ApplicationConfiguration->tip_status == 1)
            {
                $tip_status = true;
                if($order->tip_amount > 0)
                {
                    $tip_status = false;
                }
            }

            $time_charges_enable = false;
            $time_charges = "";
            $time_charges_placeholder = "";
            if(isset($order->Merchant->Configuration->user_time_charges) && $order->Merchant->Configuration->user_time_charges == 1){
                $time_charges_enable = true;
            }
            if($time_charges_enable == true && !empty($order->time_charges)){
                $time_charges = $order->time_charges;
                $time_charges_enable = true;
                $time_charges_details = json_decode($order->PriceCard->time_charges_details,true);
                $time_charges_placeholder = $time_charges_details['charge_parameter'];
            }
            else
            {
                $time_charges_enable = false;
            }

            $data_receipt['highlights'] = [
                'order_id'=>$order->id,
                'segment'=>$order->Segment->Name($order->merchant_id),
                'currency'=>$order->CountryArea->Country->isoCode,
                'payment_mode' => $order->PaymentMethod->MethodName($order->merchant_id) ? $order->PaymentMethod->MethodName($order->merchant_id) : $order->PaymentMethod->payment_method,
//                'payment_mode'=>$order->PaymentMethod->payment_method,
                'tip_status'=>$tip_status,
                'time_charges_enable'=>$time_charges_enable,
                'time_charges_placeholder'=>$time_charges_placeholder,
            ];
            $data_receipt['summary'] = [
                'cart_amount'=>!empty($order->cart_amount) ? $order->cart_amount : "0.0",
                'delivery_charge'=>!empty($order->delivery_amount) ? $order->delivery_amount : "0.0",
                'tax_amount'=>!empty($order->tax) ? $order->tax : "0.0",
                'discount_amount'=>!empty($order->discount_amount) ? $order->discount_amount : "0.0",
                'tip_amount'=>!empty($order->tip_amount) ? $order->tip_amount : "0.0",
                'final_amount_paid'=>$order->final_amount_paid,
                'time_charges'=>$time_charges,
            ];
            $order_details = $order->OrderDetail;
            foreach ($order_details as $detail)
            {
                $arr_products[] = [
                    'product_name'=>$detail->Product->Name($order->merchant_id),
                    'variant_name'=>$detail->ProductVariant->Name($order->merchant_id),
                    'product_id'=>$detail->product_id,
                    'quantity'=>$detail->quantity,
                    'price'=>$detail->price,
                    'total_amount'=>$detail->total_amount,
                    'arr_option'=>!empty($detail->options) ? json_decode($detail->options,true) : [],
                ];
            }
            $data_receipt['details'] = $arr_products;

        }catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans("$string_file.data_found"),$data_receipt);
//        return  ['booking_order_id'=>$order_id];
    }
    // booking rating by user to driver
    public function orderRating(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                'integer',
                Rule::exists('orders', 'id'),
            ],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new \Exception($errors[0]);
        }

        // transaction is not working
        // DB::beginTransaction();
        try{
            $order_id = $request->id;
            $order = Order::select('id','driver_id','order_status')->find($order_id);
            if($order->order_status !=11)
            {
                $string_file = $this->getStringFile($order->merchant_id);
                throw new \Exception(trans("$string_file.order_not_found"));
            }
            // rating is done by user
            $rating = BookingRating::updateOrCreate(
                ['order_id' => $order_id],
                [
                    'user_rating_points' => $request->rating,
                    'user_comment' => $request->comment,
                ]
            );
            $avg = BookingRating::whereHas('Order', function ($q) use ($order) {
                $q->where('driver_id', $order->driver_id);
            })->avg('user_rating_points');
            $driver = $order->Driver;
            $driver->rating = round($avg, 2);
            $driver->save();


            // add tip from rating screen
            $this->manageTip($request);
        }
        catch (\Exception $e)
        {
//            DB::rollBack();
        }
//        DB::commit();
        return  ['booking_order_id'=>$order_id];
    }
    // order amount settlement
    public function orderSettlement(Order $order){
        DB::beginTransaction();
        try{
            // If payment done
            if($order->payment_status == 1){
                $order_transaction = BookingTransaction::where('order_id',$order->id)->first();
                $customer_paid_amount = $order_transaction->customer_paid_amount;
//                $driver_commission = $order_transaction->driver_earning;
                $driver_commission = $order_transaction->driver_total_payout_amount;
                $driver_agency_commission = $order_transaction->driver_agency_total_payout_amount;
//                $bs_cart_commission_amount = $order_transaction->business_segment_earning;
                $bs_cart_commission_amount = $order_transaction->business_segment_total_payout_amount;
                $array_param = array(
                    'order_id' => $order->id,
                    'driver_id' => $order->driver_id,
                    'driver_agency_id'=>$order->Driver->driver_agency_id,
                    'payment_method_type' => $order->PaymentMethod->payment_method_type,
                );
                if($order->payment_method_id == 1 || $order->payment_method_id == 5) // cash or swipe card payment
                {
                    $array_param['amount'] = ($customer_paid_amount - $driver_commission);
                    $array_param['wallet_status'] = 'DEBIT';
                    $array_param['narration'] = 13;
                }
                else // online payment like card, payment gateway, wallet.
                {
                    $array_param['amount'] = $driver_commission;
                    $array_param['wallet_status'] = 'CREDIT';
                    $array_param['narration'] = 14;
                }
                // p($array_param);
                $driverPayment = new CommonController();
                if(!empty($order->Driver->driver_agency_id))
                {
                    $array_param['amount'] = ($customer_paid_amount - $driver_agency_commission);
                    $driverPayment->DriverAgencyWalletAmount($array_param);
                }
                else{
                    $driverPayment->DriverRideAmountCredit($array_param);
                }


                $paramArray = array(
                    'business_segment_id' => $order->business_segment_id,
                    'order_id' => $order->id,
                    'amount' => $bs_cart_commission_amount,
                    'narration' => 2,
                );
                WalletTransaction::BusinessSegmntWalletCredit($paramArray);
            }else{
                throw new \Exception(trans('api.message143'));
            }
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        DB::commit();
    }

    // add tip after order delivered and payment completed
    public function manageTip($request)
    {
        $validator = Validator::make($request->all(), [
//            'id' => [
//                'required',
//                'integer',
//                Rule::exists('orders', 'id'),
//            ],
            'tip_amount' => 'required|numeric|min:1',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new \Exception($errors[0]);
        }
        DB::beginTransaction();
        try {
            $tip_amount = $request->tip_amount;
            $order = Order::select("id","country_area_id","user_id","driver_id","order_status","payment_status","final_amount_paid","segment_id","merchant_id")->Find($request->id);
            $string_file = $this->getStringFile($order->merchant_id);
            $order->tip_amount = $tip_amount;
            $order->final_amount_paid  = $order->final_amount_paid + $tip_amount;
            $order->save();
            // p($order);
//            if($order->payment_status == 1)
//            {
            $tip_amount = $request->tip_amount;
            // make payment calls if payment method is not cash
            if($order->payment_method_id != 1)
            {
                // make payment
//                $currency = $order->User->Country->isoCode;
                $currency = $order->CountryArea->Country->isoCode;
                $array_param = array(
                    'booking_id' => NULL, // we don't want to update any status thats why booking id is going as null
                    'payment_method_id' => $order->payment_method_id,
                    'amount' => $tip_amount,
                    'user_id' => $order->user_id,
                    'card_id' => $order->card_id,
                    'currency' => $currency,
                    'quantity' => 1,
                    'order_name' => $order->merchant_booking_id,
                    'driver_sc_account_id' => $order->Driver->sc_account_id
                );

                $payment = new Payment();
                $payment->MakePayment($array_param);
            }

            // update total amount of driver in transaction
            $booking_transaction = BookingTransaction::where('order_id',$order->id)->first();

            $driver_existing_amount =   $booking_transaction->driver_total_payout_amount;
            $existing_booking_amount =   $booking_transaction->customer_paid_amount;

            $booking_transaction->tip = $tip_amount;
            $booking_transaction->driver_total_payout_amount = $driver_existing_amount + $tip_amount;
            $booking_transaction->customer_paid_amount = $existing_booking_amount + $tip_amount;
            $booking_transaction->save();
            // p($booking_transaction);

            // credit driver wallet
            $paramArray = array(
                'driver_id' => $order->driver_id,
                'booking_id' => NULL,
                'order_id' => $order->id,
                'handyman_order_id' => NULL,
                'amount' => $tip_amount,
                'narration' => 16,
            );
            WalletTransaction::WalletCredit($paramArray);

            // tip credited notification
            setLocal($order->Driver->language);
            $data = array('notification_type' => 'TIP_ADDED', 'segment_type' =>$order->Segment->slag,'segment_data' => []);
            $arr_param = array(
                'driver_id' => $order->driver_id,
                'data'=>$data,
                'message'=>trans("$string_file.tip_credited_to_driver"),
                'merchant_id'=>$order->merchant_id,
                'title' => trans("$string_file.tip_credited")
            );
            Onesignal::DriverPushMessage($arr_param);
            setLocal();
//            }
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        DB::commit();
        //p(Order::select('tip_amount')->find($request->id));
        // p(BookingTransaction::where('order_id',$order->id)->first());
        return trans("$string_file.tip_message");
    }

    // user tracking code
    function trackOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders', 'id'),
            ],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $order = Order::select("id","driver_id")->Find($request->order_id);
            $string_file = $this->getStringFile($order->merchant_id);
            $arr = [
                'lat'=> (!empty($order->Driver)?$order->Driver->current_latitude:''),
                'lng'=> (!empty($order->Driver)?$order->Driver->current_longitude:'')
            ];
            return $this->successResponse(trans("$string_file.success"),$arr);
        }
        catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }
    }
// user tracking details
    function trackOrderDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders', 'id'),
            ],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $order = Order::find($request->order_id);
            $order_number = $order->merchant_order_id;
            $business_segment = $order->BusinessSegment->full_name;
            $merchant_id = $order->merchant_id;
            $string_file = $this->getStringFile($merchant_id,$order->Merchant);
            $order_details = [
                'order_id'=>$order->id,
                'order_number'=>$order_number,
                'segment_id'=>$order->segment_id,
                'payment_mode'=> $order->PaymentMethod->MethodName($merchant_id) ? $order->PaymentMethod->MethodName($merchant_id) : $order->PaymentMethod->payment_method,
                'time'=>$order->estimate_time,
                'distance'=>$order->estimate_distance,
                'order_price'=>$order->CountryArea->Country->isoCode .' '.$order->final_amount_paid,
            ];

            if(!empty($order->driver_id)){
                $driver_name = $order->Driver->first_name .' '.$order->Driver->last_name;
                $driver_details = [
                    'driver_name'=>$driver_name,
                    'ats_id'=>$order->Driver->ats_id,
                    'profile_image'=>get_image($order->Driver->profile_image,'driver',$order->merchant_id,true,false),
                    'phone_number'=>$order->Driver->phoneNumber,
                ];
            }
            else{
                $driver_details=[];
            }


            /*******Some strings translations ********/
            $message = "";
            $order_status_action =  [6,7,9,10,11];
            $order_status_holders= [];
            $req_param['string_file'] = $string_file;
            $config_status = $this->getOrderStatus($req_param);
            $pending_icon = view_config_image("static-images/inactive-status.png");
            $completed_icon = view_config_image("static-images/tic-with-white-back.png");
            $current_icon = view_config_image("static-images/working-on.png");

            $status_completed = [];
            $arr_completed_order = [];
            if(!empty($order->order_status_history))
            {
                $status_completed = json_decode($order->order_status_history,true);
                $status_completed =  array_column($status_completed,NULL,'order_status');
                $arr_completed_order =array_keys($status_completed);
            }

            foreach($order_status_action as $order_status)
            {
                $completed_time = "";
                $icon = "";
                if($order_status == 6)
                {
                    if(in_array($order_status,$arr_completed_order))
                    {
                        $completed_time = $status_completed[$order_status]['order_timestamp'];
                        $icon = $completed_icon;
                    }
                }
                elseif($order_status == 7)
                {
                    //p($order->order_status);
                    if($order->order_status == $order_status)
                    {
                        $icon = $current_icon;
                        $message = trans_choice("$string_file.arrived_at_pickup_message", 3, ['store' => $business_segment]);
                    }
                    elseif(in_array($order_status,$arr_completed_order))
                    {
                        $completed_time = $status_completed[$order_status]['order_timestamp'];
                        $icon = $completed_icon;
                    }
                    else
                    {
                        $icon = $pending_icon;
                    }
                }
                elseif($order_status == 9)
                {
                    if($order->order_status == $order_status)
                    {
                        $icon = $current_icon;
                        $message = trans_choice("$string_file.order_in_process_message", 3, ['ID' => $order_number]);
                    }
                    elseif(in_array($order_status,$arr_completed_order))
                    {
                        $completed_time = $status_completed[$order_status]['order_timestamp'];
                        $icon = $completed_icon;
                    }
                    else
                    {
                        $icon = $pending_icon;
                    }
                }
                elseif($order_status == 10 )
                {
                    if($order->order_status == $order_status)
                    {
                        $icon = $current_icon;
                        $message = trans_choice("$string_file.order_picked_message", 3, ['successfully' => $business_segment]);
                    }

                    else if(in_array($order_status,$arr_completed_order))
                    {
                        $completed_time = $status_completed[$order_status]['order_timestamp'];
                        $icon = $completed_icon;
                    }
                    else
                    {
                        $icon = $pending_icon;
                    }
                }
                elseif($order_status == 11)
                {

                    if(in_array($order_status,$arr_completed_order))
                    {
                        $completed_time = $status_completed[$order_status]['order_timestamp'];
                        $icon = $completed_icon;
                        $message = trans("$string_file.order_delivered_message");
                    }
                    elseif($order->order_status == $order_status)
                    {
                        $icon = $current_icon;
                        $message = trans("$string_file.order_delivered_message");
                    }
                    else
                    {
                        $icon = $pending_icon;
                    }
                }

                $status_text =  $config_status[$order_status];
                $order_status_holders[] = [
                    'status_time'=>"$completed_time",
                    'tick_icon'=>$icon,
                    'status_text'=>$status_text,
                ];
            }
            if(!in_array(7,array_keys($status_completed)))
            {
                $destination_latitude = $order->BusinessSegment->latitude;
                $destination_longitude = $order->BusinessSegment->longitude;
            }
            else
            {
                $destination_latitude = $order->drop_latitude;
                $destination_longitude = $order->drop_longitude;
            }

            if($order->order_status == 5)
            {
                $message = trans_choice("$string_file.order_cancelled_by_message", 3, ['ID' => $order_number, '.' => $driver_name]);
            }
            elseif($order->order_status == 6)
            {
                $message = trans_choice("$string_file.order_accepted_by_driver", 3, ['ID' => $order_number, 'delivery' => $driver_name]);
            }
            elseif($order->order_status == 8)
            {
                $message = trans_choice("$string_file.order_cancelled_by_message", 3, ['ID' => $order_number, '.' => $business_segment]);
            }

            $tip_status = false;
            if($order->Merchant->ApplicationConfiguration->tip_status == 1)
            {
                $tip_status = true;
                if($order->tip_amount > 0)
                {
                    $tip_status = false;
                }
            }

            $return_data = [
                'order_details'=> $order_details,
                'driver_details'=> $driver_details,
                'order_status_holders'=> $order_status_holders,
                'order_current_status'=> $order->order_status,
                'message'=> $message,
                'tip_status'=> $tip_status,

                'destination_location'=> [
                    'lat'=>$destination_latitude,
                    'lng'=>$destination_longitude,
                ],
                'driver_location'=> [
                    'lat'=> (!empty($order->Driver)?$order->Driver->current_latitude:''),
                    'lng'=> (!empty($order->Driver)?$order->Driver->current_longitude:''),
                    'bearing'=> (!empty($order->Driver)?$order->Driver->bearing:''),
                    'accuracy'=> (!empty($order->Driver)?$order->Driver->accuracy:''),
                ],
                "action_buttons"=>[],
            ];
            return $this->successResponse(trans("$string_file.success"),$return_data);
        }
        catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }
    }

    // order tip from tracking screen
    public function orderTip(Request $request)
    {
        try{
            $message = $this->manageTip($request);
            return $message;
        }
        catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    public function userCancelOrder(Request $request){
        $user = $request->user('api');
        $request_fields = [
            'order_id' => ['required', 'integer', Rule::exists('orders', 'id')->where(function ($query) {
            }),],
            'latitude' => 'required',
            'longitude' => 'required',
            'cancel_reason_id' => 'required'
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $string_file = $this->getStringFile($user->merchant_id);
       DB::beginTransaction();
        try {
            $order = Order::find($request->order_id);
            if($order->payment_method_id == 1){
                $price_card = $order->PriceCard;
                $cancel_charges_amount = 0;
                if($price_card->cancel_charges == 1){
                    $order_eligible_for_cancel = [1,6,7];
                    $cancel_charges_amount = $price_card->cancel_amount;
                    $status_history = json_decode($order->order_status_history, true);
                    $exist_order_nine = array_search('9', array_column($status_history, 'order_status'));
                    if(in_array($order->order_status, $order_eligible_for_cancel) && empty($exist_order_nine) && $order->order_status != 2){
                        $order_status_timestamp = "";
                        foreach($status_history as $status_hst){
                            if($status_hst['order_status'] == 1){
                                $order_status_timestamp = $status_hst['order_timestamp'];
                                break;
                            }
                        }
                        if(!empty($order_status_timestamp)){
                            $till_cancel_time = date("Y-m-d H:i:s",$order_status_timestamp);
                            $till_cancel_time = new DateTime($till_cancel_time);
                            $till_cancel_time->add(new DateInterval('PT' . $order->PriceCard->cancel_time . 'M'));
                            $till_cancel_time = $till_cancel_time->format('Y-m-d H:i:s');
                            $till_cancel_time = convertTimeToUSERzone($till_cancel_time, $order->CountryArea->timezone, null, $order->Merchant, 1,1);
                        }else{
                            $till_cancel_time = convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone, null, $order->Merchant, 1,1);
                        }
                        $order->order_status = 2;
                        $order->cancel_reason_id = $request->cancel_reason_id;
                        $order->save();
                        $this->saveOrderStatusHistory($request,$order);

                        // Send notification to restro
                        $data = array('order_id' => $order->id, 'notification_type' => 'ORDER_CANCELLED', 'segment_type' => $order->Segment->slag);
                        $arr_param = array(
                            'business_segment_id' => $order->business_segment_id,
                            'data'=>$data,
                            'message'=>trans("$string_file.order_cancelled_message"),
                            'merchant_id'=>$order->merchant_id,
                            'title' => trans("$string_file.order_cancelled")
                        );
                        Onesignal::BusinessSegmentPushMessage($arr_param);

                        if (!empty($order->driver_id)) {
                            $order->Driver->free_busy = 2;
                            $order->Driver->save();
                            // Send notification to driver
                            $request->request->add(['notification_type'=>'ORDER_CANCELLED']);
                            $arr_driver_id = [$order->driver_id];
                            $this->sendNotificationToDriver($request,$arr_driver_id,$order);
                        }

                        $current_time = convertTimeToUSERzone(date("Y-m-d H:i:s"), $order->CountryArea->timezone, null, $order->Merchant, 1,1);
                        $apply_cancel_charges = false;
                        $driver_received_amount = 0;
                        $business_segment_received_amount = 0;
                        if($current_time > $till_cancel_time){
                            $apply_cancel_charges = true;
                            $business_seg = BusinessSegment::select('id', 'order_request_receiver', 'segment_id', 'merchant_id', 'latitude', 'longitude')->Find($order->business_segment_id);
                            $paramArray = array(
                                'user_id' => $user->id,
                                'order_id' => $order->id,
                                'amount' => $cancel_charges_amount,
                                'narration' => 15,
                                'platform' => 2,
                                'payment_method' => 1,
                            );
                            WalletTransaction::UserWalletDebit($paramArray);
                            if (!empty($business_seg->order_request_receiver) && $business_seg->order_request_receiver == 2 && !empty($order->driver_id)) {
                                $paramArray = array(
                                    'driver_id' => $order->driver_id,
                                    'order_id' => $order->id,
                                    'amount' => $cancel_charges_amount,
                                    'narration' => 19
                                );
                                WalletTransaction::WalletCredit($paramArray);
                                $driver_received_amount = $cancel_charges_amount;
                            }else{
                                $paramArray = array(
                                    'business_segment_id' => $order->business_segment_id,
                                    'order_id' => $order->id,
                                    'amount' => $cancel_charges_amount,
                                    'narration' => 2,
                                );
                                WalletTransaction::BusinessSegmntWalletCredit($paramArray);
                                $business_segment_received_amount = $cancel_charges_amount;
                            }
                        }
                        $merchant = new \App\Http\Controllers\Helper\Merchant();
                        BookingTransaction::updateOrCreate([
                            'order_id' => $order->id,
                        ],
                            [
                            'date_time_details' => date('Y-m-d H:i:s'),
                            'sub_total_before_discount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'surge_amount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'extra_charges' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'discount_amount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'tax_amount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'tip' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'insurance_amount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'cancellation_charge_received' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'cancellation_charge_applied' => $merchant->TripCalculation(($apply_cancel_charges) ? $cancel_charges_amount : "0.0", $order->merchant_id),
                            'toll_amount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'cash_payment' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'online_payment' => $merchant->TripCalculation(($apply_cancel_charges) ? $cancel_charges_amount : "0.0", $order->merchant_id),
                            'customer_paid_amount' => $merchant->TripCalculation(($apply_cancel_charges) ? $cancel_charges_amount : "0.0", $order->merchant_id),
                            'company_earning' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'driver_earning' => $merchant->TripCalculation(($apply_cancel_charges) ? $driver_received_amount : "0.0", $order->merchant_id),
                            'amount_deducted_from_driver_wallet' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'driver_total_payout_amount' => $merchant->TripCalculation(($apply_cancel_charges) ? $driver_received_amount : "0.0", $order->merchant_id),
                            'trip_outstanding_amount' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'business_segment_earning' => $merchant->TripCalculation(($apply_cancel_charges) ? $business_segment_received_amount : "0.0", $order->merchant_id),
                            'business_segment_total_payout_amount' => $merchant->TripCalculation(($apply_cancel_charges) ? $business_segment_received_amount : "0.0", $order->merchant_id),
                            'company_gross_total' => $merchant->TripCalculation('0.0', $order->merchant_id),
                            'merchant_id'=>$order->merchant_id
                        ]);
                    }else{
                        // Not able to cancel
                        throw new \Exception(trans("$string_file.your_order_in_progress_can_not_cancel"));
                    }
                }else{
                    // Cancel order is disable
                    throw new \Exception(trans("$string_file.your_order_in_progress_can_not_cancel"));
                }
            }else{
                // Other than cash payment method
                throw new \Exception(trans("$string_file.for_selected_payment_method_cancellation_is_not_applicable"));
            }
        }catch (\Exception $e) {
            DB::rollback();
            // p($e->getTraceAsString());
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.order")." ".trans("$string_file.cancelled")." ".trans("$string_file.successfully"));
    }
}
