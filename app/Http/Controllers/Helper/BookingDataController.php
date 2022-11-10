<?php

namespace App\Http\Controllers\Helper;

use App\Events\SendNewRideRequestMailEvent;
use App\Events\WebPushNotificationEvent;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\PoolController;
use App\Http\Resources\DeliveryCheckoutResource;
use App\Models\Booking;
use App\Models\BookingCheckout;
use App\Models\BookingCheckoutPackage;
use App\Models\BookingConfiguration;
use App\Models\BookingDeliveryDetails;
use App\Models\BookingRequestDriver;
use App\Models\CancelReason;
use App\Models\Configuration;
use App\Models\Corporate;
use App\Models\CountryArea;
use App\Models\DeliveryCheckoutDetail;
use App\Models\Driver;
use App\Models\EmailConfig;
use App\Models\EmailTemplate;
use App\Models\FailBooking;
use App\Models\LanguageString;
use App\Models\LanguageStringTranslation;
use App\Models\Onesignal;
use App\Models\Outstanding;
use App\Models\QuestionUser;
use App\Models\Sos;
use App\Models\VehicleType;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Traits\BookingTrait;
use App\Traits\MerchantTrait;
use App\Traits\MailTrait;
use View;
use App\Http\Controllers\Merchant\WhatsappController;

class BookingDataController extends Controller
{

    use BookingTrait,MerchantTrait,MailTrait;

    public function MakePolyLine($from, $booking_id, $key)
    {
        $booking = Booking::find($booking_id);
        $string_file = $this->getStringFile($booking->merchant_id);
        $ployline = "";
        $to = '';
        if (!empty($booking->id) && $booking->Merchant->BookingConfiguration->polyline == 1) {
            switch ($booking->booking_status) {
                case "1002":
                    if (!empty($booking->pickup_latitude) && !empty($booking->pickup_longitude)) {
                        $to = "$booking->pickup_latitude,$booking->pickup_longitude";
                    }
                    break;
                case "1003":
                case "1004":
                    $dropLocation = $this->NextLocation($booking->waypoints,$string_file);
                    if (!empty($dropLocation) && $dropLocation['last_location'] == 1) {
                        $drop_latitude = $dropLocation['drop_latitude'];
                        $drop_longitude = $dropLocation['drop_longitude'];
                    } else {
                        $drop_latitude = $booking->drop_latitude;
                        $drop_longitude = $booking->drop_longitude;
                    }
                    if (!empty($drop_latitude) && !empty($drop_longitude)) {
                        $to = "$drop_latitude,$drop_longitude";
                    }
                    break;
            }
            if (!empty($to) && in_array($booking->service_type_id, [1, 5])) {
                $ployline = GoogleController::PolyLine($from, $to, $key,"MakePolyLine$booking_id");
            }
            $booking->ploy_points = $ployline;
            $booking->save();
        }
        return $ployline;
    }

    public function SendNotificationToDrivers($booking, $drivers = [], $message ="", $notification_type = "")
    {
        try {
            $booking_status = $booking->booking_status;
            $merchant_id = $booking->merchant_id;
//            $ids = [];
            if(!empty($drivers))
            {
                $ids = array_pluck($drivers, 'driver_id');
            }
            else
            {
                $ids = [$booking->driver_id];
            }
            $data = [];
            if(!empty($ids))
            {
                foreach($ids as $id){
                    $driver = Driver::find($id);
                    setLocal($driver->language);
                    $notification_data['segment_type'] = $booking->Segment->slag;
                    $segment_name = !empty($booking->Segment->Name($merchant_id)) ? $booking->Segment->Name($merchant_id) : $booking->Segment->slag;
                    $string_file = $this->getStringFile($merchant_id);
                    $ride_string = trans("$string_file.ride");
                    $title = $title = trans("$string_file.new").' '.$segment_name .' '.$ride_string;
                    $large_icon = "";
                    $segment_data = [];
//            $large_icon = $this->getNotificationLargeIconForBooking($booking);
                    switch ($booking_status) {
                        case "1001":
                            if($booking->booking_type == 1)
                            {
                                $notification_type = "NEW_BOOKING";
                                $title= trans("$string_file.new").' '.$segment_name.' '.$ride_string;
                                $data = $this->BookingNotification($booking);
                                $segment_data = $data;
                                $message = trans("$string_file.new_ride");
//                            $message = $this->LanguageData($booking->merchant_id, 25);
                            }
                            else{
                                $notification_type = "UPCOMING_BOOKING";
//                                $segment_data = [
//                                    'id'=>$booking->id,
//                                    'status'=>$booking->booking_status,
//                                ];
                                $data = $this->BookingNotification($booking);
                                $segment_data = $data;
                                $title= trans("$string_file.upcoming").' '.$segment_name.' '.$ride_string;
                                $message = trans("$string_file.new_upcoming_ride");
//                            $message = $this->LanguageData($booking->merchant_id,26);
                            }
                            break;
                        case "1002": // send ride expired notification to other driver except booking accepted driver
                            $notification_type= "BOOKING_ACCEPTED_BY_OTHER_DRIVER";
                            $title = $ride_string.' '.trans("$string_file.expired");
                            $message = $ride_string.' '.trans("$string_file.expired");
                            $segment_data = [];
                            break;
                        case "1006":
                            $notification_type= "CANCEL_BOOKING";
                            $title = $ride_string.' '.trans("$string_file.cancelled");
                            $message = $ride_string.' '.trans("$string_file.ride_cancelled_by_user");
                            $data = $this->BookingNotification($booking);
                            $segment_data = $data;
                            break;
                        case "1008": // cancelled by admin
                            $notification_type= "CANCEL_BOOKING";
                            $title = $ride_string.' '.trans("$string_file.cancelled");
                            $message = $ride_string.' '.trans("$string_file.ride_cancelled_by_admin");
                            $data = $this->BookingNotification($booking);
                            $segment_data = $data;
                            break;
                        case "1016": // UserAutoCancel
                            $notification_type= "BOOKING_ACCEPTED_BY_OTHER_DRIVER";
                            $title = $ride_string.' '.trans("$string_file.expired");
                            $message = $ride_string.' '.trans("$string_file.expired");
                            $segment_data = [];
                            break;
                        case "1018": // UserAutoCancel
                            $notification_type= "BOOKING_EXPIRED";
                            $title = $ride_string.' '.trans("$string_file.expired");
                            $message = $ride_string.' '.trans("$string_file.expired");
                            $segment_data = [
                                'id'=>$booking->id,
                                'status'=>$booking->booking_status,
                            ];
                            break;
                    }
                    $notification_data['notification_type'] =$notification_type;
                    $notification_data['segment_data']= $segment_data;
                    $notification_data['segment_sub_group'] = $booking->Segment->sub_group_for_app; // its segment sub group for app
                    $notification_data['segment_group_id'] = $booking->Segment->segment_group_id; // its segment group
                    $arr_param = ['driver_id'=>$id,'data'=>$notification_data,'message'=>$message,'merchant_id'=>$merchant_id,'title'=>$title,'large_icon'=>$large_icon];
                    $a = Onesignal::DriverPushMessage($arr_param);
                }
                setLocal();
                event(new WebPushNotificationEvent($booking->merchant_id, $data, 1, $booking->service_type_id, $booking,$string_file)); //type defines situation,like 1: New Ride Booking
            }
        }catch (\Exception $e)
        {
          throw new \Exception($e->getMessage());
        }
        // event(new WebPushNotificationEvent($booking->merchant_id, $data, 1, $booking->service_type_id, $booking)); //type defines situation,like 1: New Ride Booking
    }

    public function PaymentOption($data, $userId, $currency = null, $min_wallet_bal = null, $ride_amount = null)
    {
        $user = User::select('merchant_id', 'corporate_id', 'wallet_balance')->find($userId);
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $return_option_list = [];
        foreach ($data as $paymentMethod) {
            $options = [];
            $icon = get_image($paymentMethod->payment_icon,'payment_icon',$user->merchant_id,false);
            $merchant_payment = $paymentMethod->Merchant->where('id',$user->merchant_id);
            $merchant_payment = collect($merchant_payment->values());
            if(isset($merchant_payment) && !empty($merchant_payment[0]->pivot['icon']))
            {
                $icon = get_image($merchant_payment[0]->pivot['icon'],'p_icon',$user->merchant_id);
            }
            switch ($paymentMethod->id) {
                case "1":
                    $options = array(
                        'id' => $paymentMethod->id,
                        'name' => $paymentMethod->MethodName($user->merchant_id) ? $paymentMethod->MethodName($user->merchant_id) : $paymentMethod->payment_method,
//                        'icon' => get_image($paymentMethod->payment_icon, 'payment_icon', null, false,false),
                        'action' => true,
                        'icon' => $icon,
                        'message' => '',
                        'card_id' => "",
                    );
                    break;
                case "2":
                    $options = array(
                        'id' => $paymentMethod->id,
                        'name'=>$paymentMethod->MethodName($user->merchant_id) ? $paymentMethod->MethodName($user->merchant_id) : $paymentMethod->payment_method,
//                        'icon' => get_image($paymentMethod->payment_icon, 'payment_icon', null, false,false),
                        'action' => true,
                        'icon' => $icon,
                        'message' => ''
                    );
                    $cardObj = new CardController();
                    $cardList = $cardObj->getUserAllCards($userId);
                    $arr_card = [];
                    if (!empty($cardList)) {
                        foreach ($cardList as $value) {
                            $arr_card[] = array(
                                'name' => "************" . $value['card_number'],
                                'card_id' => array_key_exists('id', $value) ? (string)$value['id'] : (string)$value['card_id'],
//                                'icon' => get_image($paymentMethod->payment_icon, 'payment_icon', null, false,false),
                                'action' => true,
                                'icon' => $icon,
                                'message' => ''
                            );
                        }
                    }
                    $options['arr_card'] = $arr_card;
                    break;
                case "3":
                    $config = Configuration::select('user_wallet_status')->where('merchant_id',$user->merchant_id)->first();
                    $action = false;
                    $msg = "";
                    $wallet = NULL;
                        if(!empty($ride_amount))
                        {
                            $wallet = $user->wallet_balance ? $user->wallet_balance : '0.00';
                            $msg = trans("$string_file.low_wallet_warning");
                            if(ceil($user->wallet_balance) >= ceil($ride_amount)){
                                $action = true;


                            }
                        }
                        else
                        {
                            if(!empty($config) && $config->user_wallet_status == 1)
                            {
                                if ($user->corporate_id != null) {
                                    $corporate = Corporate::find($user->corporate_id);
                                    $action = $corporate->wallet_balance >= $min_wallet_bal ? true : false;
                                    $wallet = $corporate->wallet_balance ? $corporate->wallet_balance : '0.00';
                                    $msg = trans("$string_file.user_unauthorized_msg");
//                            $msg = sprintf($this->LanguageData($user->merchant_id, 36));
                                } else {
                                    $action = $user->wallet_balance >= $min_wallet_bal ? true : false;
                                    $wallet = $user->wallet_balance ? $user->wallet_balance : '0.00';
                                    $msg = trans("$string_file.low_wallet_warning");
//                            $msg = sprintf($this->LanguageData($user->merchant_id, 35), $min_wallet_bal);
                                }
                            }
                        }
                            $paymentOPtion = $paymentMethod->MethodName($user->merchant_id) ? $paymentMethod->MethodName($user->merchant_id) : $paymentMethod->payment_method;
                            $options = array(
                                'id' => $paymentMethod->id,
                                'name' => !empty($wallet)  ? $paymentOPtion . " (" . $currency . " " . $wallet . ")" : $paymentOPtion,
                                'card_id' => "",
//                                'icon' => get_image($paymentMethod->payment_icon,'payment_icon',null,false,false),
                                'action' => $action,
                                'icon' => $icon,
                                'message' =>$msg
                            );
                    break;
                case "4":
                    $options = array(
                        'id' => $paymentMethod->id,
                        'name' => $paymentMethod->MethodName($user->merchant_id) ? $paymentMethod->MethodName($user->merchant_id) : $paymentMethod->payment_method,
                        'card_id' => "",
                        'icon' => get_image($paymentMethod->payment_icon, 'payment_icon', null, false,false),
                        'action' => true,
                        'message' => ''
                    );
                    break;
                case "5":
                    $options = array(
                        'id' => $paymentMethod->id,
                        'name' => $paymentMethod->MethodName($user->merchant_id) ? $paymentMethod->MethodName($user->merchant_id) : $paymentMethod->payment_method,
                        'card_id' => "",
//                        'icon' => get_image($paymentMethod->payment_icon, 'payment_icon', null, false,false),
                        'action' => true,
                        'icon' => $icon,
                        'message' => ''
                    );
                    break;
                case "6":
                    $options = array(
                        'id' => $paymentMethod->id,
                        'name' => $paymentMethod->MethodName($user->merchant_id) ? $paymentMethod->MethodName($user->merchant_id) : $paymentMethod->payment_method,
                        'card_id' => "",
//                        'icon' => get_image($paymentMethod->payment_icon, 'payment_icon', null, false,false),
                        'action' => true,
                        'icon' => $icon,
                        'message' => ''
                    );
                    break;
            }
            if(!empty($options))
            {
                $return_option_list [] = $options;
            }
        }
        return $return_option_list;
    }

    public function PaymentMethod($checkOut_id, $string_file)
    {
        $booking = BookingCheckout::find($checkOut_id);
        $id = NULL;
        $paymentName = trans("$string_file.payment_method");
        $card_id = NULL;
        if ($booking->payment_method_id != 0) {
            $id = (string)$booking->payment_method_id;
            $card_id = NULL;
            if (!empty($booking->card_id)) {
                $card_id = $booking->card_id;
                $cardDetails = new CardController();
                $card = $cardDetails->CardDetails($card_id);
                if (!empty($card)) {
                    $paymentName = $card['card_number'];
                } else {
                    $id = NULL;
                    $paymentName = trans("$string_file.payment_method");
                    $card_id = NULL;
                }

            } else {
                $paymentName = $booking->PaymentMethod->MethodName($booking->merchant_id) ? $booking->PaymentMethod->MethodName($booking->merchant_id) : $booking->PaymentMethod->payment_method;
            }
        }
        return array(
            'id' => $id,
            'name' => $paymentName,
            'card_id' => $card_id,
        );
    }

    public function DefaultPaymentMethod($user_id, $area_id,$string_file = "")
    {
        $lastBooking = Booking::where([['user_id', '=', $user_id]])->latest()->first();
        $id = NULL;
        $paymentName = trans("$string_file.payment_method");
        $card_id = NULL;
        if (!empty($lastBooking)) {
            $payment_id = $lastBooking->payment_method_id;
            $methods = CountryArea::select('id')->with(['PaymentMethod' => function ($query) use ($payment_id) {
                $query->where('payment_method_id', $payment_id);
            }])->find($area_id);
            if (!empty($methods->PaymentMethod->toArray())) {
                $id = $payment_id;
                $paymentName = $lastBooking->PaymentMethod->MethodName($lastBooking->merchant_id) ? $lastBooking->PaymentMethod->MethodName($lastBooking->merchant_id) : $lastBooking->PaymentMethod->payment_method;
                if (!empty($lastBooking->card_id)) {
                    $card_id = $lastBooking->card_id;
                    $cardDetails = new CardController();
                    $card = $cardDetails->CardDetails($lastBooking->card_id);
                    if (!empty($card)) {
                        $paymentName = $card['card_number'];
                    } else {
                        $id = NULL;
                        $paymentName = trans("$string_file.payment_method");
                        $card_id = NULL;
                    }
                }
            }
        }
        return array(
            'id' => $id,
            'name' => $paymentName,
            'card_id' => $card_id,
        );
    }

    public function failbooking($data, $merchant_id, $user_id, $reason)
    {
        FailBooking::create([
            'user_id' => $user_id,
            'merchant_id' => $merchant_id,
            'country_area_id' => $data->area,
            'service_type_id' => $data->service_type,
            'vehicle_type_id' => $data->vehicle_type,
            // 'package_id' => $data->package_id,
            'pickup_latitude' => $data->pickup_latitude,
            'pickup_longitude' => $data->pickup_longitude,
            'booking_type' => $data->booking_type,
            'drop_location' => $data->drop_location,
            'pickup_location' => $data->pick_up_location ?? $data->pick_up_locaion,
            'failreason' => $reason
        ]);
    }


    public function CreateCheckout($data, $user_id, $merchant_id, $price_card_id, $image = null, $rideData, $lastLocation, $corporate_id = null)
    {
        $drop_latitude = "";
        $drop_longitude = "";
        $drop_location = "";
        $waypont = "";
        if (!empty($lastLocation)) {
            $drop_latitude = $lastLocation['last_location']['drop_latitude'];
            $drop_longitude = $lastLocation['last_location']['drop_longitude'];
            $drop_location = $lastLocation['last_location']['drop_location'];
            $waypont = json_encode($lastLocation['waypont']);
        }
        $payment = $this->DefaultPaymentMethod($user_id, $data->area);
        $Checkout = BookingCheckout::updateOrCreate(
            ['user_id' => $user_id],
            [
                'merchant_id' => $merchant_id,
                'segment_id' => $data->segment_id,
                'corporate_id' => $corporate_id,
                'country_area_id' => $data->area,
                'service_type_id' => $data->service_type,
                'vehicle_type_id' => $data->vehicle_type,
                'service_package_id' => $data->service_package_id,
                'price_card_id' => $price_card_id,
                'is_geofence' => isset($data->is_geofence) ? $data->is_geofence : 0,
                'base_area_id' => isset($data->base_area_id) ? $data->base_area_id : null,
                'pickup_latitude' => $data->pickup_latitude,
                'pickup_longitude' => $data->pickup_longitude,
                'booking_type' => $data->booking_type,
                'total_drop_location' => $data->total_drop_location,
                'map_image' => $image,
                'drop_latitude' => $drop_latitude,
                'drop_longitude' => $drop_longitude,
                'drop_location' => $drop_location,
                'waypoints' => $waypont,
                'pickup_location' => $data->pick_up_locaion,
                'estimate_distance' => $rideData['distance'],
                'estimate_time' => $rideData['time'],
                'payment_method_id' => $payment['id'],
                'card_id' => $payment['card_id'],
                'estimate_bill' => $rideData['amount'],
                'auto_upgradetion' => $rideData['auto_upgradetion'],
                'later_booking_date' => set_date($data->later_date),
                'later_booking_time' => trim($data->later_time),
                'return_date' => $data->return_date,
                'return_time' => $data->return_time,
                'number_of_rider' => $data->number_of_rider,
                'bill_details' => $rideData['bill_details'],
                'promo_code' => null,
                'estimate_driver_distnace' => $rideData['estimate_driver_distnace'],
                'estimate_driver_time' => $rideData['estimate_driver_time'],
                'ac_nonac'=>NULL,
                'wheel_chair_enable'=>NULL,
                'baby_seat_enable'=>NULL,
            ]
        );
        $checkOut = $this->CheckOut($Checkout);
        return $checkOut;
    }

    public function NextLocation($drop_location = null, $string_file)
    {
        $multiple_location = json_decode($drop_location, true);
        $upcoming_stop = NULL;
        if (!empty($multiple_location)) {
            foreach ($multiple_location as $key => $location) {
                if ($location['status'] == 1) {
                    $lastLocation = 1;
                    $stop = $location['stop'];
                    $upcoming_stop = $stop;
                    switch ($stop) {
                        case "1":
                            $text = trans("$string_file.reached_at_first_drop_location");
                            break;
                        case "2":
                            $text = trans("$string_file.reached_at_second_drop_location");
                            break;
                        case "3":
                            $text = trans("$string_file.reached_at_third_drop_location");
                            break;
                        default:
                            $text = trans("$string_file.reached_at_drop_location");
                    }
                    $multiple_location[$key]['last_location'] = $lastLocation;
                    $multiple_location[$key]['text'] = $text;
                    $multiple_location[$key]['upcoming_stop'] = $upcoming_stop;
                    return $multiple_location[$key];
                }
            }
        }
        return [];
    }

    public function LastLocation($drop_location = null)
    {
        $drop = json_decode($drop_location, true);
        if (!empty($drop)) {
            $end = array_pop($drop);
            return $end;
        } else {
            return ['stop' => '', 'status' => '', 'drop_latitude' => '', 'drop_longitude' => '', 'drop_location' => ''];
        }
    }

    public function wayPoints($drops = null)
    {
        if (!empty($drops)) {
            // @Bhuvanesh
            $last = [];
            $drop_list = $drops;
            if(isset($drops[0])){
                $last = $drops[0];
                unset($drops[0]);
                $drop_list = [];
                foreach ($drops as $drop){
                    array_push($drop_list, $drop);
                }
            }else{
                $last = array_pop($drop_list);
            }
            $location = array('last_location' => $last, 'waypont' => $drop_list);
            return $location;
        } else {
            return [];
        }
    }

    public function BookingNotificationForUser($booking,$notification_type="",$message_otp = "")
    {
        $user = User::find($booking->user_id);
        setLocal($user->language);
        $booking_status = $booking->booking_status;
        $merchant_id = $booking->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $ride_string = trans("$string_file.ride");
        $item = $booking->Segment;
        $segment_name = !empty($item->Name($merchant_id)) ? $item->Name($merchant_id) : $item->slag;
        $large_icon = isset($item->Merchant[0]['pivot']->segment_icon) && !empty($item->Merchant[0]['pivot']->segment_icon) ? get_image($item->Merchant[0]['pivot']->segment_icon, 'segment', $merchant_id, true,false) :
            get_image($item->icon, 'segment_super_admin', NULL, false,false);
        $notification_data['notification_type'] =$notification_type;
        $notification_data['segment_type'] = $booking->Segment->slag;
        $notification_data['segment_group_id'] = $booking->Segment->segment_group_id;
        $notification_data['segment_sub_group'] = $booking->Segment->sub_group_for_app; // its segment sub group for app
        $notification_data['segment_data']= [
            'booking_id'=>$booking->id,
            'booking_status'=>$booking_status,
            'booking_type'=>$booking->booking_type,
            'username'=>$booking->User->UserName,
            'email'=>isset($booking->User->email) ? $booking->User->email : "",
            'user_image'=>get_image($booking->User->Userprofile_image,'user',$booking->merchant_id,true,false),
            'phone'=>$booking->User->UserPhone,
        ];

        switch ($booking_status) {
            case "1001":
                $title = "";
                $message = "";
                break;
            case "1002":
                $booking_status_history = json_decode($booking->booking_status_history,true);
                $booking_status_history = array_column($booking_status_history,'booking_status');
                $message = in_array(1012, $booking_status_history) ? trans("$string_file.driver_started_pickup_location") : trans("$string_file.ride_has_been_assigned");
//                $message = in_array(1012, $booking_status_history) ? $this->LanguageData($booking->merchant_id, 28) : $this->LanguageData($booking->merchant_id, 27);
//                $message = $this->LanguageData($booking->merchant_id, 27);
                $title = $segment_name .' '.$ride_string.' '.trans("$string_file.accepted");
                break;
            case "1003":
                $title = trans("$string_file.driver_arrived_at_pickup");
                $message = trans("$string_file.arrived_pickup");
//                $message = $this->LanguageData($merchant_id, 31);
                break;
            case "1004":
                if($notification_type == "REACH_AT_DROP"){
                    $message = trans("$string_file.reached_at_drop");
//                    $message = $this->LanguageData($merchant_id, 37);
                    $title = $ride_string.' '.trans("$string_file.started");
                }else{
                    $message = trans("$string_file.driver_ride_started");
//                    $message = $this->LanguageData($merchant_id, 32);
                    $title = $ride_string.' '.trans("$string_file.reached_at_drop_location");
                }
                break;
            case "1005":
                $title = $ride_string.' '.trans("$string_file.completed");
                $message = trans("$string_file.driver_ride_completed");
//                $message = $this->LanguageData($merchant_id, 34);
                break;
            case "1006":
                $title = "";
                $message = "";
                break;
            case "1007":
                $title = $ride_string.' '.trans("$string_file.cancelled");
                $message =$ride_string.' '.trans("$string_file.ride_cancelled_by_driver");
                break;
            case "1008":
                $title = $ride_string.' '.trans("$string_file.cancelled");
                $message =$ride_string.' '.trans("$string_file.ride_cancelled_by_admin");
                break;
            case "1012":
                $message = trans("$string_file.ride_has_been_assigned");
//                $message = $this->LanguageData($merchant_id, 30);
                $title = $segment_name .' '.$ride_string.' '.trans("$string_file.assigned");
                break;
        }
        $arr_param = ['user_id'=>$booking->user_id,'data'=>$notification_data,'message'=>$message,'merchant_id'=>$merchant_id,'title'=>$title,'large_icon'=>$large_icon];
        Onesignal::UserPushMessage($arr_param);
        // send whatsApp notification
        if($booking->platform == 3)
        {
            $whatsApp = new WhatsappController;
            $whatsApp->sendWhatsApp($booking->User->UserPhone,$message.' '.$message_otp,$booking->merchant_id);
        }
        setLocal();
        return true;
    }
    // send new booking notification to driver
    public function BookingNotification($details)
    {
        $additional_notes = !empty($details->additional_notes) ? $details->additional_notes : "";
        $additional_information = !empty($details->additional_information) ? $details->additional_information :"";
        $vehicle = $details->VehicleType;
        $vehicleTypeName = $vehicle->VehicleTypeName;
        $driver_request_timeout = 0;
        $mulple_stop = false;
        $dropCount = "";
        $stops = [];
        if ($details->booking_status == 1001) {
            $config = BookingConfiguration::select('driver_request_timeout')->where([['merchant_id', '=', $details->merchant_id]])->first();
            $driver_request_timeout = $config->driver_request_timeout * 1000;
            $mulple_stop = (!empty($details->waypoints) && count(json_decode($details->waypoints, true)) > 0) ? true : false;
            $count = (!empty($details->waypoints) && count(json_decode($details->waypoints, true)) > 0) ? count(json_decode($details->waypoints, true)) : 0;
            $dropCount = trans('multipleStop', ['number' => $count]);
            $stops = json_decode($details->waypoints, true);
        }

        $receiver_details = [];
        if (!empty($details->receiver_details)){
            $receiver_details = [json_decode($details->receiver_details,true)];
        }

        $productDetails = [];
        if (!empty($details->DeliveryPackage)){
            $deliveryPackages = $details->DeliveryPackage;
            foreach ($deliveryPackages as $deliveryPackage){
                $productDetails[] = array(
                    'id' => $deliveryPackage->id,
                    'merchant_id' => $deliveryPackage->merchant_id,
                    'product_name' => $deliveryPackage->DeliveryProduct->ProductName,
                    'weight_unit' => $deliveryPackage->DeliveryProduct->WeightUnit->WeightUnitName,
                    'quantity' => $deliveryPackage->quantity
                );
            }
        }

        if(!empty($productDetails))
        {
            $arr_packages = [];
            $arr_packages['items'] = $productDetails;
        }
        else
        {
            $arr_packages = (object)[];
        }
        
        $estimate_bill = $details->CountryArea->Country->isoCode.' '.$details->estimate_bill;
        if($details->Merchant->Configuration->homescreen_estimate_fare == 2){
            $estimate_bill = $this->getPriceRange($details->estimate_bill,$details->CountryArea->Country->isoCode);
        }

        $merchant_id = $details->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $description = $details->estimate_distance.' '. $details->estimate_time;
//        .' '.$details->estimate_bill;
        $return = [
            'timer'=> ($driver_request_timeout > 0) ? $driver_request_timeout : 60000,
            'cancel_able'=>true,
            'id'=>$details->id,
            'status'=>$details->booking_status,
            'later_booking_date'=>$details->later_booking_date,
            'later_booking_time'=>$details->later_booking_time,
            'generated_time'=>$details->booking_timestamp,
            'segment_type' => $details->Segment->slag,
            'highlights'=>[
                'number'=>$details->merchant_booking_id,
                'price'=>$estimate_bill,
                'name'=>$details->Segment->Name($details->merchant_id) .' '.trans("$string_file.ride"),
                'payment_mode'=>$details->PaymentMethod->MethodName($details->merchant_id) ? $details->PaymentMethod->MethodName($details->merchant_id) : $details->PaymentMethod->payment_method,
                'description'=>$description,
            ],
            'pickup_details'=>[
                'header'=>trans("$string_file.pickup_location"),
                'locations'=>[
                    [
                        'address'=>$details->pickup_location,
                        'lat'=>(string)$details->pickup_latitude,
                        'lng'=>(string)$details->pickup_longitude,
                    ]
                ],
            ],
            'drop_details'=>[
                'header'=>trans("$string_file.drop_off_location"),
                'locations'=>[
                    [
                        'address'=>$details->drop_location,
                        'lat'=>(string)$details->drop_latitude,
                        'lng'=>(string)$details->drop_longitude,
                    ]
                ],
            ],
            'customer_details'=>[
                [ "name" => $details->User->UserName,
                    "email" => isset($details->User->email) ? $details->User->email : "",
                    "phone" => $details->User->UserPhone,
                    "image" => !empty($details->User->UserProfileImage) ? get_image($details->User->UserProfileImage,'user',$merchant_id,true,false) : "",
                ]
            ],
            'receiver_details'=>$receiver_details,
            'package_details'=>$arr_packages,
            'additional_notes'=>!empty($additional_notes) ? [$additional_notes] : [],
            'additional_information'=>!empty($additional_information) ? [json_decode($additional_information,true)] : [],
        ];

        return $return;
    }

    public function CheckOut($Checkout)
    {
        $string_file = $this->getStringFile(NULL,$Checkout->Merchant);
        $questions = QuestionUser::where([['user_id', '=', $Checkout->user_id]])->inRandomOrder()->first();
        if (!empty($questions)) {
            $questions->question = $questions->Question->question;
        }
        $currency = $Checkout->CountryArea->Country->isoCode;
        $pricing_type = $Checkout->PriceCard->pricing_type;
        $insurance_enable = $Checkout->PriceCard->insurnce_enable == 1 ? true : false;
        $insurance_price = "";
        if ($insurance_enable == true) {
            // correct message according to requirement
//            $insurance_price = $Checkout->PriceCard->insurnce_type == 1 ? trans($string_file., ['amount' => $currency
//                . $Checkout->PriceCard->insurnce_value]) : trans('api.Pactage', ['amount' => $Checkout->PriceCard->insurnce_value]);
        }
        
        if($Checkout->Merchant->Configuration->homescreen_estimate_fare == 2){
            $estimate_bill = $this->getPriceRange($Checkout->estimate_bill,$currency);
        }else{
            $estimate_bill = $pricing_type == 3 ? trans("$string_file.fare_will_be_confirmed") : $currency . " " . $Checkout->estimate_bill;    
        }
        
        $promo_heading = trans("$string_file.apply_coupon");
        $promoCode = "";
        $discounted_amout = "";
        if (!empty($Checkout->promo_code)) {
            $promoCode = $Checkout->PromoCode->promoCode;
            $promo_heading = trans("$string_file.coupon_applied");
            $discounted_amout = $currency . " " . isset($Checkout->discounted_amount) ? $Checkout->discounted_amount : "";
        }
        $estimate_receipt = [];
        if (!empty($Checkout->bill_details)) {
            $price = json_decode($Checkout->bill_details, true);
            $estimate_receipt = HolderController::PriceDetailHolder($price, null, $currency,'user',
                $Checkout->segment_id,"",$Checkout->merchant_id);
        }
        $name = $Checkout->VehicleType->VehicleTypeName;

        $outstanding_amount = Outstanding::where(['user_id' => $Checkout->user_id, 'reason' => 1, 'pay_status' => 0])->sum('amount');
        $outstandAmount = $outstanding_amount ? $outstanding_amount : '0.00';
        $outstandShow = $outstanding_amount ? true : false;
//        $outstandAmount = $Checkout->User->outstanding_amount ? $Checkout->User->outstanding_amount : '0.00';
//        $outstandShow = $Checkout->User->outstanding_amount ? true : false;

        $vehicleTypeImage = get_image($Checkout->VehicleType->vehicleTypeImage, 'vehicle', $Checkout->merchant_id,true,false);
        $Checkout->waypoints = json_decode($Checkout->waypoints, true);
        $Checkout->vehicleTypeName = $name;
        $Checkout->service_package_id = $Checkout->service_package_id ? $Checkout->service_package_id : null;
        $Checkout->later_booking_date = $Checkout->later_booking_date ? $Checkout->later_booking_date : "";
        $Checkout->later_booking_time = $Checkout->later_booking_time ? $Checkout->later_booking_time : "";
        $Checkout->return_date = $Checkout->return_date ? $Checkout->return_date : "";
        $Checkout->return_time = $Checkout->return_time ? $Checkout->return_time : "";
        $Checkout->additional_notes = $Checkout->additional_notes ? $Checkout->additional_notes : "";
        $Checkout->vehicleTypeImage = $vehicleTypeImage;
        $booking_type = $Checkout->booking_type;
        $string_file = $this->getStringFile($Checkout->merchant_id);
        $Checkout->SelectedPaymentMethod = $this->PaymentMethod($Checkout->id, $string_file);
        $Checkout->payment_method_id = (string)$Checkout->payment_method_id;
        $newArray = $Checkout->toArray();
        $estimates_arrive_header_text = trans("$string_file.ride_estimate");
        $estimates_header_text = $newArray['drop_location'] ? trans("$string_file.ride_estimate") : trans("$string_file.minimum_fare");
        if ($booking_type == 1) {
//            $newArray['estimate_distance'] = $newArray['estimate_driver_distnace'];
//            $newArray['estimate_time'] = $newArray['estimate_driver_time'];
            $estimates_arrive_header_text = trans("$string_file.arrive_in");
        }else{

            $estimates_arrive_header_text = trans("$string_file.distance_and_time");
        }
        $newArray['promo_code'] = $promoCode;
        $newArray['estimate_receipt'] = $estimate_receipt;
        $newArray['estimates_arrive_header_text'] = $estimates_arrive_header_text;
        $newArray['promo_heading'] = $promo_heading;
        $newArray['discounted_amout'] = $discounted_amout;
        $newArray['estimates_header_text'] = $estimates_header_text;
        $newArray['estimate_bill'] = $estimate_bill;
        $newArray['outstandAmount'] = (string)$outstandAmount;
        $newArray['outstandShow'] = $outstandShow;
        $newArray['question'] = $questions;
        $newArray['insurance_enable'] = $insurance_enable;
        $newArray['insurance_price'] = $insurance_price;
        // unwanted params from response
        unset($newArray['price_card']);
        unset($newArray['created_at']);
        unset($newArray['updated_at']);
        unset($newArray['merchant_id']);
        unset($newArray['corporate_id']);
        unset($newArray['country_area_id']);
        unset($newArray['service_package_id']);
        unset($newArray['is_geofence']);
        unset($newArray['base_area_id']);
        unset($newArray['price_card_id']);
        unset($newArray['auto_upgradetion']);
        unset($newArray['no_of_person']);
        unset($newArray['later_booking_date']);
        unset($newArray['later_booking_time']);
        unset($newArray['return_date']);
        unset($newArray['return_time']);
        unset($newArray['additional_notes']);
        unset($newArray['additional_information']);
        unset($newArray['baby_seat_enable']);
        unset($newArray['wheel_chair_enable']);
        unset($newArray['no_of_children']);
        unset($newArray['no_of_bags']);
        unset($newArray['manual_dispatch_ride']);
        unset($newArray['gender']);
        if(!empty($Checkout->Merchant->BookingConfiguration) && $Checkout->Merchant->BookingConfiguration->static_map == 1)
        {
            $newArray['map_image'] = $newArray['map_image'].'&key='.$Checkout->Merchant->BookingConfiguration->google_key;
        }
        else
        {
            $newArray['map_image'] = NULL;
        }
         unset($newArray['merchant']);
        return $newArray;
    }


    public function driverBookingDetails($booking, $call_by_id = false,$request = NULL)
    {
        $additional_notes = "";
        try {
            if($call_by_id == true)
            {
                $booking_obj = new Booking;
                $booking = $booking_obj->getBooking($booking);
            }
            $price_card_id = $booking->price_card_id;
            $booking_status = $booking->booking_status;
            $service_type_id = $booking->service_type_id;
            $string_file = $this->getStringFile($booking->merchant_id);
            $dropLocation = $this->NextLocation($booking->waypoints, $string_file);
            if (!empty($dropLocation) && $dropLocation['last_location'] == 1) {
                $drop_location = $dropLocation['drop_location'] ? $dropLocation['drop_location'] : "";
                $drop_latitude = $dropLocation['drop_latitude'];
                $drop_longitude = $dropLocation['drop_longitude'];
            } else {
                $drop_location = $booking->drop_location ? $booking->drop_location : "";
                $drop_latitude = $booking->drop_latitude;
                $drop_longitude = $booking->drop_longitude;
            }
            $merchant = $booking->Merchant;
            $merchant_id=$merchant->id;
            $app_config = $merchant->ApplicationConfiguration;
            $marker_lat = $booking->pickup_latitude;
            $marker_long = $booking->pickup_longitude;

            $trip_status_text = "";
            $location = "";
            $cancel = false;
            $location_action = false;
            $sos_visibility = false;
            $location_editable = false;
            $send_meter_image = false;
            $send_meter_value = false;
            $marker_color = "";
            $is_it_first_stop = false;
            switch ($booking_status) {
                case "1001":
                    $trip_status_text = trans("$string_file.accept_ride");
//                    $trip_status_text = $this->LanguageData($merchant_id, 1);
                    $location = $booking->pickup_location;
                    $cancel = true;
                    break;
                case "1012":
                    $trip_status_text = trans("$string_file.start_to_pickup");
//                    $trip_status_text = $this->LanguageData($merchant_id, 4);
                    $location = $booking->pickup_location;
                    $cancel = true;
                    break;
                case "1002":
                    if ($service_type_id == 5) {
                        $poolRide = new PoolController();
                        $poolDetails = $poolRide->DecideForPickOrDrop($booking->driver_id);
                        $trip_status_text = $poolDetails['status'];
                        $location = $poolDetails['location'];
                        $booking->id = $poolDetails['booking_id'];
                        $booking->booking_status = $poolDetails['booking_status'];
                    } else {
                        $trip_status_text = trans("$string_file.arrive");
//                        $trip_status_text = $this->LanguageData($merchant_id, 5);
                        $location = $booking->pickup_location;
                    }
                    $cancel = true;
                    break;
                case "1003":
                    $trip_status_text = trans("$string_file.start_ride");
//                        $this->LanguageData($merchant_id, 8);
                    $location = $drop_location;
                    $cancel = true;
                    $location_action = false;
                    $sos_visibility = $app_config->sos_user_driver == 1 ? true : false;
                    $location_editable = ($service_type_id == 1 && $booking->total_drop_location <= 1) ? true : false;
                    if ($service_type_id == null || $service_type_id == 0 || $service_type_id == 1 || $service_type_id == 5) {
                        $send_meter_image = false;
                        $send_meter_value = false;
                    } else {
                        $send_meter_image = true;
                        $send_meter_value = true;
                    }
//
                    $marker_lat = $drop_latitude;
                    $marker_long = $drop_longitude;
                    break;
                case "1004":
                    $location_editable = ($service_type_id == 1 && $booking->total_drop_location <= 1) ? true : false;
                    if ($service_type_id == 5) {
                        $poolRide = new PoolController();
                        $poolDetails = $poolRide->DecideForPickOrDrop($booking->driver_id);
                        $trip_status_text = $poolDetails['status'];
                        $location = $poolDetails['location'];
                        $poolBookingDetials = Booking::with(['User' => function ($query) {
                            $query->select('id', 'first_name', 'last_name', 'UserPhone', 'email', 'rating', 'UserProfileImage');
                        }])->find($poolDetails['booking_id']);
                        $booking->id = $poolDetails['booking_id'];
                        $booking->booking_status = $poolDetails['booking_status'];
                        $booking->User = $poolBookingDetials->User;
                        $booking->User->UserName = $booking->User->UserName;
                    } else {
                        $is_it_first_stop = isset($dropLocation['upcoming_stop']) && $dropLocation['upcoming_stop'] == 1 ? true : false;
                        if (!empty($dropLocation) && $dropLocation['last_location'] == 1) {
                            $location = $dropLocation['drop_location'];
                            $trip_status_text = $dropLocation['text'];;
                            $location_action = true;
                        } else {
                            $location = $drop_location;
                            $trip_status_text = trans("$string_file.end_ride");
//                            $trip_status_text = $this->LanguageData($merchant_id, 9);
                        }
                    }
                    $sos_visibility = $app_config->sos_user_driver == 1 ? true : false;
                    if ($service_type_id == null || $service_type_id == 0 || $service_type_id == 1 || $service_type_id == 5) {
                        $send_meter_image = false;
                        $send_meter_value = false;
                    } else {
                        $send_meter_image = true;
                        $send_meter_value = true;
                    }

                    $marker_color = "E74C3C";
                    $marker_lat = $drop_latitude;
                    $marker_long = $drop_longitude;
                    break;
                case "1005":
                    $trip_status_text = trans("$string_file.end_ride");
//                    $trip_status_text = $this->LanguageData($merchant_id, 9);
                    $location = $drop_location;
                    $marker_lat = $drop_latitude;
                    $marker_long = $drop_longitude;
                    break;
                case "1006":
                    $trip_status_text = trans("$string_file.user_cancel");
//                    $trip_status_text = $this->LanguageData($merchant_id, 2);
                    $location = $drop_location;
                    break;
                case "1007":
                    $trip_status_text = trans("$string_file.driver_cancel");
//                    $trip_status_text = $this->LanguageData($merchant_id, 10);
                    $location = $drop_location;
                    break;
                case "1008":
                    $trip_status_text = trans("$string_file.admin_cancel");
//                    $trip_status_text = $this->LanguageData($merchant_id, 11);
                    $location = $drop_location;
                    break;
            }

            $generalConfiguration = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
            $toll_enable = $generalConfiguration->toll_api;
            //            $toll_enable = "";
//            $booking['manual_toll_enable'] = false;
//            if(isset($generalConfiguration->toll_api) && $generalConfiguration->toll_api == 2){
//                $booking['manual_toll_enable'] = true;
//                $toll_enable = 2;
//                $booking['manual_toll_price'] = $booking->Driver->CountryArea->manual_toll_price;
//            }
//            elseif(isset($generalConfiguration->toll_api) && $generalConfiguration->toll_api == 3){
//                $toll_enable = 3;
//            }

            $booking->send_meter_image = $send_meter_image;
            $booking->send_meter_value = $send_meter_value;

            $booking->sos = Sos::AllSosList($merchant_id, 2, $booking->driver_id);
            if ($merchant->Configuration->without_country_code_sos == 1) {
                $phoneCode = $booking->CountryArea->Country->phonecode;
                $booking->Driver->phoneNumber = str_replace($phoneCode, '', $booking->driver->phoneNumber);
                $booking->User->UserPhone = str_replace($phoneCode, '', $booking->User->UserPhone);
            }

            $arr_packages = []; // in case of delivery
            $booing_config = BookingConfiguration::where('merchant_id',$booking->merchant_id)->first();
            $arr_action = [];
            if($sos_visibility == true)
            {
                $sos = [
                    'icon' => view_config_image("static-images/sos.png"),
                    'name' => trans("$string_file.sos"),
                    'action' => 'SOS',
                ];
                array_push($arr_action,$sos);
            }
            $product_loaded_images = [];
            $get_first_stop_detail = false; // this is used only in delivery case
            if($booking->Segment->slag == 'DELIVERY')
            {
                $package = [
                    'icon' => view_config_image("static-images/package_details.png"),
                    'name' => trans("$string_file.packages"),
                    'action' => 'PACKAGE_DETAILS',
                ];
                array_push($arr_action,$package);

                $productImageData = [];
//                if (!empty($booking->product_images)){
//                    $productImages = json_decode($booking->product_images,true);
//                    foreach ($productImages as $productImage){
//                        $productImageData[] = get_image($productImage,'product_image',$merchant_id,true);
//                    }
//                }
                if (!empty($booking->BookingDetail->product_loaded_images)){
                    $productImages = json_decode($booking->BookingDetail->product_loaded_images,true);
                    foreach ($productImages as $productImage){
                        $product_loaded_images[] = get_image($productImage,'product_loaded_images',$merchant_id,true);
                    }
                }
                $productDetails = [];
                if (!empty($booking->DeliveryPackage)){
                    $deliveryPackages = $booking->DeliveryPackage;
                    foreach ($deliveryPackages as $deliveryPackage){
                        $productDetails[] = array(
                            'id' => $deliveryPackage->id,
//                        'merchant_id' => $deliveryPackage->merchant_id,
                            'product_name' => $deliveryPackage->DeliveryProduct->ProductName,
                            'weight_unit' => $deliveryPackage->DeliveryProduct->WeightUnit->WeightUnitName,
                            'quantity' => $deliveryPackage->quantity
                        );
                    }
                }

//                when stops are multiple than reachedAtMultiDrop fun is calling and adding addition_notes in request
                if(!empty($booking->waypoints) && count(json_decode($booking->waypoints)) > 0)
                {
                        if($is_it_first_stop)
                        {
                            // if multiple stop and calling ride start  or booking-order-picked api is calling then display first stop details
                            $get_first_stop_detail = true;
                        }
                        else {
                            // when reached at drop location api called
                            $additional_notes = !empty($request->additional_notes) ? $request->additional_notes : "";
                            if (!empty($request->product_image_one)) {
                                $image1 = get_image($request->product_image_one, 'product_image', $merchant_id, true);
                                array_push($productImageData, $image1);
                            }
                            if (!empty($request->product_image_two))
                            {
                                $image2 = get_image($request->product_image_two, 'product_image', $merchant_id, true);
                            array_push($productImageData, $image2);
                            }
                        }
                }
                else
                {
                    // if single stop then get by first stop details
                    $get_first_stop_detail = true;
                }

                if($get_first_stop_detail == true)
                {
                    if (!empty($booking->BookingDeliveryDetails)){
                        $additional_notes = !empty($booking->BookingDeliveryDetails->additional_notes) ? $booking->BookingDeliveryDetails->additional_notes : "";
                        $productImageData = [];
                        if(!empty($booking->BookingDeliveryDetails))
                        {
                            if(!empty($booking->BookingDeliveryDetails->product_image_one))
                            {
                            $image1 = get_image($booking->BookingDeliveryDetails->product_image_one,'product_image',$merchant_id,true);
                            array_push($productImageData,$image1);
                            }
                        }
                        if(!empty($booking->BookingDeliveryDetails))
                        {
                            if(!empty($booking->BookingDeliveryDetails->product_image_two))
                            {
                                $image2 = get_image($booking->BookingDeliveryDetails->product_image_two, 'product_image', $merchant_id, true);
                                array_push($productImageData, $image2);
                            }
                        }
                    }
                }
            }
            else
            {
               $additional_notes =  !empty($booking->additional_notes) ? $booking->additional_notes : "";
            }


            if(!empty($productDetails) || !empty($productImageData))
            {
                $packages['images'] = $productImageData;
                $packages['items'] = $productDetails;
                $arr_packages[] = $packages;
            }

            $phone =
                [
                    'icon'=>view_config_image("static-images/phone.png"),
                    'name'=>trans("$string_file.call"),
                    'action'=>'PHONE',
                ];
            array_push($arr_action,$phone);

            if(!empty($booking->drop_location))
            {
                $navigate = [
                    'icon'=>view_config_image("static-images/navigation.png"),
                    'name'=>trans("$string_file.navigate"),
                    'action'=>'NAVIGATE',
                ];
                array_push($arr_action,$navigate);
            }

            $price_card = DB::table('price_card_values as pvc')->join('pricing_parameters as pp','pvc.pricing_parameter_id','=','pp.id')->where([['pvc.price_card_id','=',$price_card_id],['pp.parameterType','=',18]])->get();
            if(isset($merchant->Configuration->onride_waiting_button) && $merchant->Configuration->onride_waiting_button == 1 && count($price_card) > 0){
                $start_wait = [
                    'icon'=>'',
                    'name'=>trans("$string_file.start_wait"),
                    'action'=>'WAIT',
                ];
                array_push($arr_action,$start_wait);
            }

            // google api polyline
            $path_type = "STILL";
            if(!empty($request->latitude) && !empty($request->longitude))
            {
                // p($booking);
                if(in_array($booking->booking_status,[1001,1002,1012]))
                {
                    $status_drop_latitude = $booking->pickup_latitude;
                    $status_drop_longitude = $booking->pickup_longitude;
                }
                else
                {
                    $status_drop_latitude = $booking->drop_latitude;
                    $status_drop_longitude = $booking->drop_longitude;
                }
                $google_result = [];
                if(!empty($status_drop_latitude) && !empty($status_drop_longitude) && $booing_config->polyline == 1)
                {
                    $status_drop_location[0] = ['drop_latitude'=>$status_drop_latitude,'drop_longitude'=>$status_drop_longitude];
                    $google_key = $booking->Merchant->BookingConfiguration->google_key;
                    $google_result = GoogleController::GoogleStaticImageAndDistance($request->latitude, $request->longitude, $status_drop_location, $google_key,"",$string_file);

                    $booking_data = Booking::select('id','ploy_points')->find($booking->id);
                    $booking_data->ploy_points = isset($google_result['poly_points']) ? $google_result['poly_points'] : "";
                    $booking_data->save();
                }
//                if (empty($google_result)) {
//                    $message = "Sorry order can't be placed because, delivery address is out of service area";//trans("$string_file.google_key_not_working");
//                    //                $message = trans("$string_file.google_key_not_working");
//                    throw new \Exception($message);
//                }

                if($booking->booking_status == 1003)
                {
                    $path_type ="ANIMATED";
                }
            }

            $merchant_segment =  $booking->Segment->Merchant->where('id',$booking->merchant_id);
            $merchant_segment = collect($merchant_segment->values());

            $currency = $booking->CountryArea->Country->isoCode;
            $segment_slug = $booking->Segment->slag;

            $drop_off_details = [
                'location'=>$booking->drop_location,
                'lat'=>$booking->drop_latitude,
                'lng'=>$booking->drop_longitude,
                'receiver_details'=>[
                    'receiver_name'=>"",
                    'receiver_phone'=>"",
                ]
            ];
            if($segment_slug == "DELIVERY"){
                $booking_delivery_details = BookingDeliveryDetails::where([["booking_id","=",$booking->id],["drop_status","=",0]])->orderBy("stop_no")->first();
                if(!empty($booking_delivery_details)){
                    $drop_off_details = [
                        'location'=>$booking_delivery_details->drop_location,
                        'lat'=>$booking_delivery_details->drop_latitude,
                        'lng'=>$booking_delivery_details->drop_longitude,
                        'receiver_details'=>[
                            'receiver_name'=>$booking_delivery_details->receiver_name,
                            'receiver_phone'=>$booking_delivery_details->receiver_phone,
                        ],
                    ];
                }
            }
            $userPhone = $booking->User->UserPhone;
            if ($merchant->Configuration->twilio_call_masking == 1) {
                $userPhone = $booking->user_masked_number ?? '';
            }

            $return_data = [
                'highlights'=>[
                    'id'=>$booking->id,
                    'number' => $booking->merchant_booking_id,
                    'segment_id'=>$booking->segment_id,
                    'segment_name'=>$booking->Segment->Name($booking->merchant_id),
                    'segment_group_id'=>$booking->Segment->segment_group_id,
                    'segment_sub_group'=>$booking->Segment->sub_group_for_app,
                    'service_type'=>$booking->ServiceType->type,
                    'status'=>$booking->booking_status,
                    'status_text'=>$trip_status_text,
                    'cancel_able'=>$cancel, // ride start otp will be used for taxi as well as delivery
                    'ride_start_otp'=> (!empty($merchant->BookingConfiguration) && !empty($merchant->BookingConfiguration->ride_otp) && $booking->platform == 1) ? true : false, // normal ride
//                    'ride_start_otp'=> !empty($merchant->BookingConfiguration) && !empty($merchant->BookingConfiguration->ride_otp) ? true : false, // manual dispatch ride
                    'chat_enable'=> !empty($booing_config->chat) && $booing_config->chat == 1 ? true : false,
                    'reached_at_multi_drop'=>$location_action, //check multiple drop exist or not
                    'segment_slug'=>$segment_slug,
                    'delivery_drop_otp'=> ($segment_slug == "DELIVERY" && $booing_config->delivery_drop_otp == 1) ?  true : false ,
                    // If delivery_drop_otp value is 2 means qr code is enable for delivery
                    'delivery_drop_qr'=> ($segment_slug == "DELIVERY" && $booing_config->delivery_drop_otp == 2) ?  true : false ,
                    'widget_image'=>isset($merchant_segment[0]['pivot']->segment_icon) && !empty($merchant_segment[0]['pivot']->segment_icon) ? get_image($merchant_segment[0]['pivot']->segment_icon, 'segment', $booking->merchant_id, true,false) :
                        get_image($booking->Segment->icon, 'segment_super_admin', NULL, false,false),
                    "additional_notes"=>$additional_notes, // in case of taxi additional notes will come from booking table and for delivery it comes according to stop points
                    "toll_enable" =>$booking->booking_status == 1004 ? "$toll_enable" : "0",
                ],
                // its object in case of taxi and delivery, but array in case of food and grocery because of app ui
                'customer_details'=>[
                    'id'=>$booking->User->id,
                    'first_name'=>$booking->User->first_name,
                    'last_name'=>$booking->User->last_name,
                    'phone_number'=>$userPhone,
                    'email'=>$booking->User->email,
                    'profile_image'=>get_image($booking->User->UserProfileImage, 'user', $merchant_id,true,false),
                    "rating"=>$booking->User->rating,
                ],
                'payment_details'=>[
                    'id'=>$booking->PaymentMethod->id,
                    'status'=>$booking->payment_status == 1? true : false,
                    'amount'=>$booking->estimate_bill,
                    'currency'=>$currency,
                    'payment_method'=>$booking->PaymentMethod->MethodName($merchant_id) ? $booking->PaymentMethod->MethodName($merchant_id) : $booking->PaymentMethod->payment_method,
                ],
                'current_destination_details'=>[
                    'markers'=>[[
                        'lat'=>$marker_lat,
                        'long'=>$marker_long,
                        'color'=>$marker_color,
                    ]],
                    'address_text'=>$location,//
                    'editable'=>$location_editable,//
                ],
                'pick_up_details'=>[[
                    'location'=>$booking->pickup_location,
                    'lat'=>$booking->pickup_latitude,
                    'lng'=>$booking->pickup_longitude,
                ]],
                'drop_off_details'=>[$drop_off_details],
                'action_buttons'=>$arr_action,
                'packages'=>$arr_packages,
                'driver_clicked_images'=>[], // key is not using
                'product_loaded_images'=>$product_loaded_images,
                'path_type'=>$path_type, //NA,ANIMATED,STILL
                'pic_uploadable'=>$booking->booking_status == 3 ? true : false, //NA,ANIMATED,STILL
                'poly_line'=> isset($google_result['poly_points']) ? $google_result['poly_points'] : "", //NA,ANIMATED,STILL
            ];
            return $return_data;
        }catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

//    public function DeliveryCheckOut($Checkout)
//    {
//        $currency = $Checkout->CountryArea->Country->isoCode;
//        $estimate_bill = $currency . " " . $Checkout->estimate_bill;
//        $SelectedPaymentMethod = $Checkout->SelectedPaymentMethod = $this->PaymentMethod($Checkout->id);
//
//        $estimate_receipt = [];
//        if (!empty($Checkout->bill_details)) {
//            $price = json_decode($Checkout->bill_details, true);
//            $estimate_receipt = HolderController::PriceDetailHolder($price, null, $currency,'user',$Checkout->segment_id);
//        }
//
//        $return_array = [];
//        $return_array['id'] = $Checkout->id;
//        $return_array['estimate_bill'] = $estimate_bill;
//        $return_array['estimate_receipt'] = $estimate_receipt;
//        $return_array['SelectedPaymentMethod'] = $SelectedPaymentMethod;
//        $return_array['vehicle_details']['id'] = $Checkout->VehicleType->id;
//        $return_array['vehicle_details']['name'] = $Checkout->VehicleType->VehicleTypeName;
//        $return_array['vehicle_details']['weight'] = '';
//        $return_array['vehicle_details']['icon'] = get_image($Checkout->VehicleType->vehicleTypeImage, 'vehicle', $Checkout->merchant_id,true,false);
//
//        $return_array['request_type']['type'] = ((int)$Checkout->booking_type == 1) ? trans("$string_file.request_normal") : trans("$string_file.request_later");
//        $return_array['request_type']['time'] = ($Checkout->booking_type == 1) ? '' : $Checkout->later_booking_time;
//        $return_array['request_type']['date'] = ($Checkout->booking_type == 1) ? '' : $Checkout->later_booking_date;
//
//        $return_array['location']['pickup']['visible'] = true;
//        $return_array['location']['pickup']['address']['name'] = $Checkout->pickup_location;
//        $return_array['location']['pickup']['address']['latitude'] = $Checkout->pickup_latitude;
//        $return_array['location']['pickup']['address']['longitude'] = $Checkout->pickup_longitude;
//
//        $return_array['location']['drop']['visible'] = ($Checkout->drop_latitude) ? true : false;
//        $return_array['location']['drop']['address']['name'] = $Checkout->drop_location;
//        $return_array['location']['drop']['address']['latitude'] = (string)$Checkout->drop_latitude;
//        $return_array['location']['drop']['address']['longitude'] = (string)$Checkout->drop_longitude;
//
//        $return_array['packages'] = [];
//        $return_array['additional_mover_charge'] = !empty($Checkout->PriceCard->additional_mover_charge) ? $Checkout->PriceCard->additional_mover_charge : 0;
//        // iterate packages
//        $count = 0;
////        foreach ($Checkout->packages()->with('unit')->with('deliveryType')->with('good')->get() as $package) {
////            $return_array['packages'][$count]['id'] = $package->good_id;
////            $return_array['packages'][$count]['good_name'] = $package->good->GoodName;
////            $return_array['packages'][$count]['category_id'] = $package->delivery_type_id;
////            $return_array['packages'][$count]['category_name'] = $package->deliveryType->name;
////            $return_array['packages'][$count]['type'] = $package->type;
////            $return_array['packages'][$count]['qty'] = $package->qty;
////            $return_array['packages'][$count]['unit']['id'] = (string)$package->weight_unit_id;
////            $return_array['packages'][$count++]['unit']['name'] = ($package->unit) ? $package->unit->WeightUnitName : '';
////
////        }
//        return $return_array;
//    }


    public function CreateDeliveryCheckout($data, $user_id, $merchant_id, $price_card_id, $image = null, $rideData, $lastLocation)
    {
        $drop_latitude = "";
        $drop_longitude = "";
        $drop_location = "";
        $waypont = "";
        if (!empty($lastLocation)) {
            $drop_latitude = $lastLocation['last_location']['drop_latitude'];
            $drop_longitude = $lastLocation['last_location']['drop_longitude'];
            $drop_location = $lastLocation['last_location']['drop_location'];
            $waypont = json_encode($lastLocation['waypont']);
        }
        $payment = $this->DefaultPaymentMethod($user_id, $data->area);

        $Checkout = BookingCheckout::updateOrCreate(
            ['user_id' => $user_id],
            [
                'merchant_id' => $merchant_id,
                'segment_id' => $data->segment_id,
                'country_area_id' => $data->area,
                'service_type_id' => $data->service_type_id,
                'vehicle_type_id' => $data->vehicle_type,
                'service_package_id' => $data->service_package_id,
                'price_card_id' => $price_card_id,
                'pickup_latitude' => $data->pickup_latitude,
                'pickup_longitude' => $data->pickup_longitude,
                'booking_type' => $data->booking_type,
                'total_drop_location' => $data->total_drop_location,
                'map_image' => $image,
                'drop_latitude' => $drop_latitude,
                'drop_longitude' => $drop_longitude,
                'drop_location' => $drop_location,
                'waypoints' => $waypont,
                'pickup_location' => $data->pick_up_locaion,
                'estimate_distance' => $rideData['distance'],
                'estimate_time' => $rideData['time'],
                'payment_method_id' => $payment['id'],
                'card_id' => $payment['card_id'],
                'estimate_bill' => $rideData['amount'],
                'auto_upgradetion' => $rideData['auto_upgradetion'],
                'later_booking_date' => $data->later_date,
                'later_booking_time' => $data->later_time,
                'return_date' => $data->return_date,
                'return_time' => $data->return_time,
                'number_of_rider' => $data->number_of_rider,
                'bill_details' => $rideData['bill_details'],
                'promo_code' => NULL,
                'estimate_driver_distnace' => $rideData['estimate_driver_distnace'],
                'estimate_driver_time' => $rideData['estimate_driver_time'],
            ]
        );

        // Deleting existing drop details, and create new drop details
        DeliveryCheckoutDetail::where('booking_checkout_id',$Checkout->id)->delete();
        if(!empty($data->drop_location)){
            $drop_points = json_decode($data->drop_location, true);
            $last_location = $drop_points[0];
            unset($drop_points[0]);
            $point = 1;
            if(!empty($drop_points)){
                foreach($drop_points as $key => $drop_point){
                    $delivery_checkout_detail = new DeliveryCheckoutDetail;
                    $delivery_checkout_detail->booking_checkout_id = $Checkout->id;
                    $delivery_checkout_detail->stop_no = $point++;
                    $delivery_checkout_detail->drop_latitude = $drop_point['drop_latitude'];
                    $delivery_checkout_detail->drop_longitude = $drop_point['drop_longitude'];
                    $delivery_checkout_detail->drop_location = $drop_point['drop_location'];
                    $delivery_checkout_detail->save();
                }
            }
            $delivery_checkout_detail = new DeliveryCheckoutDetail;
            $delivery_checkout_detail->booking_checkout_id = $Checkout->id;
            $delivery_checkout_detail->stop_no = $point;
            $delivery_checkout_detail->drop_latitude = $last_location['drop_latitude'];
            $delivery_checkout_detail->drop_longitude = $last_location['drop_longitude'];
            $delivery_checkout_detail->drop_location = $last_location['drop_location'];
            $delivery_checkout_detail->save();
        }

//        // generate packages array
//        $insert_array = [];
//        $package_array = json_decode($data->package_array);
//        foreach ($package_array as $pkg) {
//            $insert_array[] = [
//                'good_id' => $pkg->id,
//                'delivery_type_id' => $pkg->category_id,
//                'type' => $pkg->type,
//                'weight_unit_id' => isset($pkg->unit_id) ? ($pkg->unit_id > 0) ? $pkg->unit_id : null : null,
//                'qty' => isset($pkg->quantity) ? $pkg->quantity : 0
//            ];
//        }
//        // dellete previous checkout data
//        BookingCheckoutPackage:: where('booking_checkout_id', $Checkout->id)->delete();
//        // add packages to booking checkout packages
//        $Checkout->packages()->createMany($insert_array);
        $checkOut = new DeliveryCheckoutResource($Checkout);
//        $checkOut = $this->DeliveryCheckOut($Checkout);
        return $checkOut;
    }

//    public function SendNotificationToDriversDelivery($booking, $drivers, $message)
//    {
//        $data = $this->BookingNotification($booking);
//        $ids = array_pluck($drivers, 'driver_id');
//        Onesignal::DriverPushMessage($ids, $data, $message, 1, $booking->merchant_id);
//        event(new WebPushNotificationEvent($booking->merchant_id, $data, 1, $booking->delivery_type_id, $booking)); //type defines situation,like 1: New Ride Booking
//    }

    public function sendRequestToNextDrivers($booking_id,$type = 1, $calling_for = "")
    {
        // ride later case
        try {
            if($type == 1){
                $booking = Booking::select('id','merchant_booking_id','payment_method_id','vehicle_type_id','driver_id','merchant_id','segment_id','country_area_id','driver_id','user_id','service_package_id','service_type_id','is_geofence','base_area_id','auto_upgradetion','number_of_rider','total_drop_location','price_card_id','driver_vehicle_id','family_member_id','pickup_latitude','pickup_longitude','pickup_location','drop_latitude','drop_longitude','drop_location','booking_type','estimate_bill','additional_information','additional_notes','waypoints','promo_code')->
                where([['booking_status', 1001], ['id', $booking_id]])
//                ->with(['Segment'=>function($q){
//                    $q->addSelect('id','slag','name','icon');
//                    $q->with(['Merchant'=>function($q){
//                    }]);
//                }])
                    ->first();
            }else{
                $booking = BookingCheckout::select('id','merchant_id','payment_method_id','card_id','segment_id','vehicle_type_id','country_area_id','user_id','service_package_id','service_type_id','is_geofence','base_area_id','auto_upgradetion','number_of_rider','total_drop_location','price_card_id','pickup_latitude','pickup_longitude','pickup_location','drop_latitude','drop_longitude','drop_location','booking_type','estimate_bill','additional_information','additional_notes','waypoints','estimate_distance','estimate_time','map_image','promo_code','wheel_chair_enable','baby_seat_enable','ac_nonac','bill_details')
                    ->find($booking_id);
            }
            $string_file = $this->getStringFile($booking->merchant_id);
            if (!empty($booking)){
                $driver_id = isset($booking) ? $booking->driver_id : null;
                if (empty($driver_id)) {
                    $limit = getSendDriverRequestLimit($booking);
                    $booking_config = BookingConfiguration::where('merchant_id', $booking->merchant_id)->latest()->first();
                    if (!empty($booking_config->driver_ride_radius_request)) {
                        $ride_radius = json_decode($booking_config->driver_ride_radius_request, true);
                        if ($limit == 1){
                            if (!empty($booking->ride_radius)) {
                                $booking_ride_radius = explode(',', $booking->ride_radius);
                                $remain_ride_radius_slot[] = $booking_ride_radius[0];
                            } else {
                                $remain_ride_radius_slot = $ride_radius;
                            }
                        }elseif ($limit > 1){
                            if (!empty($booking->ride_radius)) {
                                $booking_ride_radius = explode(',', $booking->ride_radius);
                                $remain_ride_radius = array_diff($ride_radius, $booking_ride_radius);
                                $remain_ride_radius_slot = array_values($remain_ride_radius);
                            } else {
                                $remain_ride_radius_slot = $ride_radius;
                            }
                        }
                    } else {
                        $remain_ride_radius_slot = array();
                    }

                    $bookingId = $type == 1 ? $booking->id : null;
                    $req_parameter = [
                        'area' => isset($booking->country_area_id) ? $booking->country_area_id : null,
                        'segment_id' => isset($booking->segment_id) ? $booking->segment_id : null,
                        'latitude' => isset($booking->pickup_latitude) ? $booking->pickup_latitude : null,
                        'longitude' => isset($booking->pickup_longitude) ? $booking->pickup_longitude : null,
                        //'distance' => !empty($remain_ride_radius_slot) ? $remain_ride_radius_slot[0] : isset($booking_config->normal_ride_now_radius) ? $booking_config->normal_ride_now_radius : null,
                        'distance' => !empty($remain_ride_radius_slot) ? $remain_ride_radius_slot[0] : null,
                        'limit' => isset($booking_config->normal_ride_now_request_driver) ? $booking_config->normal_ride_now_request_driver : null,
                        'service_type' => isset($booking->service_type_id) ? $booking->service_type_id : null,
                        'vehicle_type' => isset($booking->vehicle_type_id) ? $booking->vehicle_type_id : null,
                        'baby_seat' => isset($booking->baby_seat_enable) ? $booking->baby_seat_enable : null,
                        'user_gender' => isset($booking->gender) ? $booking->gender : null,
                        'drop_lat' => isset($booking->drop_latitude) ? $booking->drop_latitude : null,
                        'drop_long' => isset($booking->drop_longitude) ? $booking->drop_longitude : null,
                        'booking_id' => isset($bookingId) ? $bookingId : null,
                        'wheel_chair' => isset($booking->wheel_chair_enable) ? $booking->wheel_chair_enable : null,
                        'payment_method_id' => isset($booking->payment_method_id) ? $booking->payment_method_id : null,
                        'estimate_bill' => isset($booking->estimate_bill) ? $booking->estimate_bill : null,
                        'ac_nonac' => isset($booking->ac_nonac) ? $booking->ac_nonac : null,
                        'string_file' => $string_file,
                    ];

                    $drivers = Driver::GetNearestDriver($req_parameter);
                    $auto_upgrade = 2;

                    if (empty($drivers) || (!empty($drivers) && $drivers->count() == 0)) {
                        if ($booking->Merchant->Configuration->no_driver_availabe_enable == 1) {
                            if ($booking->auto_upgradetion == 1) {
                                $auto_upgrade = 1;
                                $vehicleDetail = VehicleType::select('id', 'vehicleTypeRank')->find($booking->vehicle_type_id);
                                $req_parameter['vehicleTypeRank'] = $vehicleDetail->vehicleTypeRank;
                                $drivers = Driver::GetNearestDriver($req_parameter);
                                if (empty($drivers)) {
                                    if($calling_for =="auto_upgrade")
                                    {
                                        return true;
                                    }
                                    else
                                    {
                                        throw new \Exception(trans("$string_file.no_driver_available"));
                                    }
                                }
                            } else {
//                            throw new \Exception(trans("$string_file.no_driver_available"));
                                if($calling_for =="auto_upgrade")
                                {
                                    return true;
                                }
                                else
                                {
                                    throw new \Exception(trans("$string_file.no_driver_available"));
                                }
                            }
                        } else {
//                        throw new \Exception(trans("$string_file.no_driver_available"));
                            if($calling_for =="auto_upgrade")
                            {
                                return true;
                            }
                            else
                            {
                                throw new \Exception(trans("$string_file.no_driver_available"));
                            }
                        }
                    }

                    if (!empty($remain_ride_radius_slot)){
                        if ($limit == 1){
                            $booking->ride_radius = $remain_ride_radius_slot[0];
                        }elseif ($limit > 1){
                            $booking_ride_radius = $booking->ride_radius;
                            $booking_ride_radius = str_replace(' ', '', $booking_ride_radius);
                            $booking_ride_radius = empty($booking_ride_radius) ? array() : explode(',', $booking_ride_radius);
                            array_push($booking_ride_radius, $remain_ride_radius_slot[0]);
                            $booking->ride_radius = implode(',', $booking_ride_radius);
                        }
                    }

                    $booking->auto_upgradetion = $auto_upgrade;
                    if($type == 1){
                        $booking->booking_timestamp = time();
                        $booking->booking_status = 1001;
                        $booking->save();
                    }else{
                        $Bookingdata = $booking->toArray();
                        if (isset($Bookingdata['merchant'])){
                            unset($Bookingdata['merchant']);
                        }
                        unset($Bookingdata['id']);
                        unset($Bookingdata['user']);
                        unset($Bookingdata['created_at']);
                        unset($Bookingdata['updated_at']);
                        $Bookingdata['booking_timestamp'] = time();
                        $Bookingdata['booking_status'] = 1001;
                        $Bookingdata['insurnce'] = request()->insurnce;

                        // Amba don't set time zone
//                        date_default_timezone_set($booking->PriceCard->CountryArea->timezone);
                        $booking = Booking::create($Bookingdata);
                    }

                    //\Log::channel('driverRequest')->emergency($drivers->toArray());
                    // $old_driver_request = BookingRequestDriver::where('booking_id', $booking_id)->whereIn('request_status', [1, 3])->get();
                    // $old_driver_ids = array_pluck($old_driver_request, 'driver_id');
                    // $new_driver_ids = array_pluck($drivers, 'driver_id');
                    // $remainDriver = array_diff($new_driver_ids, $old_driver_ids);
                    // \Log::channel('RemainDriver')->emergency($remainDriver);
                    // $drivers = Driver::whereIn('id', $remainDriver)->get();
                    
                    // send booking request to driver entry
                    $findDriver = new FindDriverController();
                    $findDriver->AssignRequest($drivers, $booking->id);
                    $bookingData = new BookingDataController();
//                $message = $bookingData->LanguageData($booking->merchant_id, 25);
//                $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
                    $bookingData->SendNotificationToDrivers($booking, $drivers);
                    $data = $type == 1 ? [] : $booking;
                    return ['message' => trans("$string_file.ride_booked"), 'data' => $data];
                } else {
                    throw new \Exception(trans("$string_file.ride_already"));
                }
            }else{
                throw new \Exception(trans("$string_file.data_not_found"));
            }
        }catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    public function checkPriceForRide($booking,$billDetails){
        switch ($booking->price_for_ride){
            case '2':
                $amount = $booking->price_for_ride_amount;
                $billDetails = $this->checkPrice($billDetails,$amount);
                break;
            case '3':
                $maxFare = $booking->price_for_ride_amount;
                if($billDetails['amount'] > $maxFare){
                    $billDetails = $this->checkPrice($billDetails,$maxFare);
                }else{
                    $billDetails['subTotalWithoutSpecial'] = $billDetails['amount'];
                    $billDetails['subTotalWithoutDiscount'] = $billDetails['amount'];
                }
                break;
            default :
                return $billDetails;
        }
        return $billDetails;
    }

    public function checkPrice($billDetails,$amount){
        $billData = array();
        $toll_charges = $billDetails['toolCharge'];
        foreach($billDetails['bill_details'] as $billDetail){
            if(array_key_exists('amount',$billDetail)){
                if(array_key_exists('parameterType',$billDetail)){
                    if($billDetail['parameterType'] == 10 ){
                        $billDetail['amount'] = $amount-$billDetails['booking_fee']+$toll_charges;
                    }elseif($billDetail['parameterType'] == 17){
                        $billDetail['amount'] = $billDetails['booking_fee'];
                    }else{
                        $billDetail['amount'] = "0.00";
                    }
                }
            }
            if(isset($billDetail['subTotal'])){
                $billDetail['subTotal'] = $amount;
            }
            $billData[]=$billDetail;
        }
        $billDetails['bill_details'] = $billData;
        $billDetails['amount'] = $amount+$toll_charges;
        $billDetails['subTotalWithoutSpecial'] = $amount;
        $billDetails['subTotalWithoutDiscount'] = $amount;
        return $billDetails;
    }

    public function bookingReceiptForDriver($request)
    {
        $action= [];
        $booking = Booking::with(['BookingDetail', 'BookingRating', 'PriceCard' => function ($query) {
            $query->with(['PriceCardValues' => function ($q) {
                $q->with('PricingParameter');
            }]);
        }])->find($request->booking_id);
        $string_file = $this->getStringFile($booking->merchant_id);
        $multi_destination = $booking->Merchant->BookingConfiguration->multi_destination == 1 ? true : false;
        $arr_ride_location = [];
        $start =  [
            'color'=>'2ECC71',
            'address'=>$booking->BookingDetail->start_location,
            'latitude'=>$booking->BookingDetail->start_latitude,
            'longitude'=>$booking->BookingDetail->start_longitude,
        ];
        array_push($arr_ride_location,$start);
        if($multi_destination)
        {
            $drop_location = $booking->waypoints;
            $multiple_location = json_decode($drop_location, true);
            if(!empty($multiple_location))
            {
                foreach ($multiple_location as $location)
                {
                    // currently end location value doesn't exist and to get that we have to run google api
                    $mid_drop =  [
                        'color'=>'F7B500',
                        'address'=>$location['drop_location'],
                        'latitude'=>$location['end_latitude'],
                        'longitude'=>$location['end_longitude'],
                    ];
                    array_push($arr_ride_location,$mid_drop);
                }
            }
        }
        $end = [
            'color'=>'E74C3C',
            'address'=>$booking->BookingDetail->end_location,
            'latitude'=>$booking->BookingDetail->end_latitude,
            'longitude'=>$booking->BookingDetail->end_longitude,
        ];
        array_push($arr_ride_location,$end);

        if ($booking->BookingDetail->bill_details) {
            if ($booking->payment_status == 1) {
                // total received amount
                    $payment_text = trans("$string_file.total_to_be_collected");
                    } else {
                // total pending amount
                        $payment_text = trans("$string_file.cash_to_be_collected");
                    }
//            switch ($booking->payment_method_id) {
//                case "1":
//                    $payment_pending = trans("$string_file.payment_pending");
//                    $payment_text = trans("$string_file.cash_to_be_collected");
//                    $amount = $booking->BookingDetail->pending_amount;
//                    break;
//                case "3":
//                    if ($booking->payment_status == 1) {
//                        $payment_pending = trans('api.message162');
//                        $payment_text = trans("$string_file.total_to_be_collected");
//                        $amount = "";
//                    } else {
//                        $payment_pending = trans("$string_file.payment_pending");
//                        $payment_text = trans("$string_file.payment_pending");
//                        $amount = $booking->BookingDetail->pending_amount;
//                    }
//                    break;
//                default:
//                    if ($booking->payment_status == 1) {
//                        $payment_pending = trans('api.message161');
//                        $payment_text = trans("$string_file.cash_to_be_collected");
//                        $amount = "";
//                    } else {
//                        $payment_pending = trans("$string_file.payment_pending");
//                        $payment_text = trans("$string_file.payment_pending");
//                        $amount = $booking->BookingDetail->pending_amount;
//                    }
//            }
        } else {
            $payment_pending = "";
            $payment_text = "";
            $amount = "";
        }

        $currency = $booking->CountryArea->Country->isoCode;
        $string_file = $this->getStringFile($booking->merchant_id);
        $bottom_button_action = "RATE_USER";
        $bottom_text = trans("$string_file.rate_user_and_complete");
        $text_back_ground_Color = "e67e22";
        if (!empty($booking->BookingRating) && $booking->payment_status == 1) {
            if (!empty($booking->BookingRating->driver_rating_points)) {
                $bottom_button_action = "COMPLETE";
                $text_back_ground_Color = "#2ECC71";
                $bottom_text = trans("$string_file.complete");
            }
        }
        if ($booking->BookingDetail->bill_details) {
            $price = json_decode($booking->BookingDetail->bill_details);
            $holder = HolderController::PriceDetailHolder($price, $request->booking_id,NULL,'driver');
        } else {

            $bottom_text = trans("$string_file.submit_fare");
            $text_back_ground_Color = "0091FF";
            $bottom_button_action = "INPUT_PRICES";
        }

        // Incase booking complete in ride end. and driver want to rate user
        if($booking->booking_closure !=1 || (empty($booking->BookingRating) || empty($booking->BookingRating->driver_rating_points)))
        {
            $bottom_button=['action_name'=>$bottom_text,'action'=>$bottom_button_action,'color'=>$text_back_ground_Color];
            array_push($action,$bottom_button);
        }

        if($booking->payment_status !=1)
        {
            $holder_driver_ride_payment = [
                'action_name'=>trans("$string_file.have_you_received_cash"),
                'action'=>'CASH_CONFIRM',
                'color'=>'0091FF',
            ];
            array_push($action,$holder_driver_ride_payment);
        }else{

        }

        $merchant_id = $booking->merchant_id;
        $return = [
            'highlights'=>[
                'id'=>$booking->id,
                'number' => $booking->merchant_booking_id,
                'status'=>$booking->booking_status,
                'segment_slug'=>$booking->Segment->slag,
                'rating_mandatory'=>false,
            ],
            'customer_details'=>[
                'id'=>$booking->User->id,
                'first_name'=>$booking->User->first_name,
                'last_name'=>$booking->User->last_name,
                'phone_number'=>$booking->User->UserPhone,
                'email'=>$booking->User->email,
                'profile_image'=>get_image($booking->User->UserProfileImage, 'user', $merchant_id,true,false),
            ],
            'payment_info'=>[
                'id'=>$booking->PaymentMethod->id,
                'status'=>$booking->payment_status == 1? true : false,
                'amount'=>$booking->final_amount_paid,
//                'amount'=>$booking->BookingDetail->pending_amount,
                'currency'=>$currency,
                'payment_text'=>$payment_text,
                'payment_method'=>$booking->PaymentMethod->MethodName($merchant_id) ? $booking->PaymentMethod->MethodName($merchant_id) : $booking->PaymentMethod->payment_method,
            ],
            'address_details'=>$arr_ride_location,
            'bill_details'=>$holder,
            'action_buttons'=>$action,
        ];
        return $return;
    }

    function sendBookingMail($booking)
    {
        event(new SendNewRideRequestMailEvent($booking));
//        $data['booking'] = $booking;
//        $temp = EmailTemplate::where('merchant_id', '=', $booking->merchant_id)->where('template_name', '=', "invoice")->first();
//        $data['temp'] = $temp;
//        $order_request = View::make('mail.new-ride-request')->with($data)->render();
//        $configuration = EmailConfig::where('merchant_id', '=', $booking->merchant_id)->first();
//        $response = $this->sendMail($configuration, $booking->Merchant->email, $order_request, 'new_ride', $booking->Merchant->BusinessName);
        return true;
    }

    function feedPromoCodeValue($checkout){
        $promoCode = $checkout->PromoCode->promoCode;
        // for flat discount
        if ($checkout->PromoCode->promo_code_value_type == 1) {
            $parameterAmount = $checkout->PromoCode->promo_code_value;
        } else {
            $promoMaxAmount = !empty($checkout->PromoCode->promo_percentage_maximum_discount) ? $checkout->PromoCode->promo_percentage_maximum_discount : 0;
            $parameterAmount = ($checkout->estimate_bill * $checkout->PromoCode->promo_code_value) / 100;
            $parameterAmount = (($parameterAmount > $promoMaxAmount) && ($promoMaxAmount > 0)) ? $promoMaxAmount : $parameterAmount;
        }

        $parameterForDiscount = array('subTotal' => $checkout->PromoCode->id,
            'price_card_id' => $checkout->price_card_id,
            'booking_id' => 0,
            'parameter' => "promo_code",
//                'parameter' => $promoCode,
            'parameterType' => "PROMO CODE",
            'amount' => (string)$parameterAmount,
            'type' => "DEBIT",
            'code' => $promoCode,
            'freeValue' => $checkout->PromoCode->promo_code_value);

        $bill_details = json_decode($checkout->bill_details, true);
        $promo_code_already_applied = false;
        if(!empty($bill_details)){
            foreach($bill_details as $bill_detail){
                if($bill_detail['code'] == $promoCode){
                    $promo_code_already_applied = true;
                    break;
                }
            }
        }
        if(!$promo_code_already_applied){
            array_push($bill_details, $parameterForDiscount);
            $checkout->bill_details = json_encode($bill_details);
            $checkout->save();
        }
        $discounted_amount = sprintf("%0.2f", $checkout->estimate_bill - $parameterAmount);
        return array('discounted_amount' => $discounted_amount);
    }
    
    public function getPriceRange($amount,$currency){
        if(empty($amount)){
            return '';
        }
        $amount = trim(str_replace($currency,'',$amount));
        if($amount < 500){
            $price_range = $currency." 100 - 500";
        }elseif($amount >= 500 && $amount < 1000){
            $price_range = $currency." 500 - 1000";
        }elseif($amount >= 1000 && $amount < 1500){
            $price_range = $currency." 1000 - 1500";
        }elseif($amount >= 1500 && $amount < 2000){
            $price_range = $currency." 1500 - 2000";
        }elseif($amount >= 2000 && $amount < 2500){
            $price_range = $currency." 2000 - 2500";
        }elseif($amount >= 2500 && $amount < 3000){
            $price_range = $currency." 2500 - 3000";
        }elseif($amount >= 3000 && $amount < 3500){
            $price_range = $currency." 3000 - 3500";
        }elseif($amount >= 3500 && $amount < 4000){
            $price_range = $currency." 3500 - 4000";
        }elseif($amount >= 4000 && $amount < 4500){
            $price_range = $currency." 4000 - 4500";
        }elseif($amount >= 4500 && $amount < 5000){
            $price_range = $currency." 4500 - 5000";
        }else{
            $price_range = "More Than 5000";
        }
        return $price_range;
    }
}