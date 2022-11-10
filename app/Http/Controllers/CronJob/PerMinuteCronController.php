<?php

namespace App\Http\Controllers\CronJob;

use App\Http\Controllers\Helper\WalletTransaction;
use App\Http\Controllers\Merchant\WhatsappController;
use App\Models\Booking;
use App\Http\Controllers\Helper\BookingDataController;
use App\Models\Driver;
use App\Models\HandymanOrder;
use App\Models\Onesignal;
use App\Models\BusinessSegment\Order;
use App\Models\User;
use App\Traits\HandymanTrait;
use App\Traits\OrderTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\MerchantTrait;
use App\Models\BusinessSegment\BusinessSegmentConfigurations;
use App\Models\BookingRequestDriver;

class PerMinuteCronController extends Controller
{
    use OrderTrait,HandymanTrait;
//    use HandymanTrait;
    /********** Expire old Booking's cron start ***************/
    public function booking()
    {
        // its for taxi and delivery
        $this->expireOldAndNotifyScheduledBooking();
        // expired partial accepted ride and send notification
        $this->expireOldAcceptedBookings();

        $this->expireNotAcceptedHandymanOrders();
        //  order of food and grocery
        $this->expirePlacedOrders();
        $this->expireNewRequestedRides(); //rides booked from whatsapp
        $this->rejectOrderRequest(); //reject order request
    }
    /********** Expire old Booking's cron end ***************/
    // expired all booked rides which are not accepted yet.
    public function expireOldAndNotifyScheduledBooking()
    {
//        $string_file = "";
        $bookings = Booking::select('id','user_id','segment_id','merchant_id','driver_id','merchant_booking_id','country_area_id','booking_type','later_booking_date','later_booking_time','booking_status')->where([['booking_status', '=', 1001], ['booking_type', '=', 2]])->get();
        if (!empty($bookings->toArray())) {
            $minutes = 30; //minutes
//            $message_expired = trans('admin.booking_expired');
//            $message_upcoming = trans('admin.booking_upcoming');
            foreach ($bookings as $booking):
                $string_file = $this->getStringFile($booking->merchant_id);
                date_default_timezone_set($booking->CountryArea['timezone']);
                $booking_time = $booking->later_booking_date . ' ' . $booking->later_booking_time;
                $current_date_time = date('Y-m-d H:i');
                $date1=date_create($booking_time);
                $date2=date_create($current_date_time);
                $diff=date_diff($date1,$date2);
                $diff_minute = $diff->i;
                if ($current_date_time > $booking_time) {
                    // p($booking->id);
//                        if (!empty($booking->driver_id))
//                        {
//
////                            $message = $message_expired.' '.trans("$string_file.id").' #'.$booking->merchant_booking_id.' '.trans("$string_file.date").' '.$booking->later_booking_date . ' ' . $booking->later_booking_time;
////                            Onesignal::DriverPushMessage($booking->driver_id, [], $message, 13, $booking->merchant_id);
//                        }
                    $booking->booking_status = 1018;  // expired booking
                    $booking->save();
                    // p($booking);
                    // send notification to user to inform that his ride has expired

                    $segment_data = [
                        'id'=>$booking->id,
                        'booking_status'=>$booking->booking_status,
                    ];
                    $data = array('notification_type' => 'RIDE_EXPIRED','segment_type' => $booking->Segment->slag,'segment_data'=>$segment_data);
                    $arr_param = array(
                        'user_id' => $booking->user_id,
                        'data'=>$data,
                        'message'=>trans("$string_file.ride_expired"),
                        'merchant_id'=>$booking->merchant_id,
                        'title' => '#'.$booking->merchant_booking_id .' '.trans("$string_file.ride_expired")
                    );
                    Onesignal::UserPushMessage($arr_param);

                }
//                    elseif($diff_minute == $minutes)
//                    {
//                        if (!empty($booking->driver_id))
//                        {
////                            $message = $message_upcoming . ' ' . trans("$string_file.id") . ' #' . $booking->merchant_booking_id . ' ' . trans("$string_file.date") . ' ' . $booking->later_booking_date . ' ' . $booking->later_booking_time;
////                            Onesignal::DriverPushMessage($booking->driver_id, [], $message, 13, $booking->merchant_id);
//                        }
//                    }
            endforeach;
        }
    }


    // expire old  accepted booking
    public function expireOldAcceptedBookings()
    {
        $all_bookings = Booking::select('id','merchant_id','segment_id','later_booking_date','later_booking_time','country_area_id','driver_id','booking_status','segment_id','user_id')
//            ->whereHas('Merchant', function ($q) {
//                $q->whereHas('BookingConfiguration', function ($query) {
//                    $query->where('auto_cancel_expired_rides', 1);
//                });
//            })
            ->where([['booking_type', 2]])->whereIn('booking_status', [1012])->get();

//        if ($all_bookings->isNotEmpty()):
//            $booking_ids = $all_bookings->map(function ($item, $key) {
//                date_default_timezone_set($item->CountryArea['timezone']);
//                $now = new \DateTime();
//                $booking_time = new \DateTime($item->later_booking_date . ' ' . $item->later_booking_time);
//                return ($now > $booking_time) ? $item->id : null;
//            })->filter()->values();
//            if ($booking_ids->isNotEmpty()):
//                Booking::whereIn('id', $booking_ids->toArray())
//                    ->update([
//                        'booking_status' => '1018', // Add as many as you need
//                    ]);
//                $all_bookings = Booking::whereIn("id",$booking_ids->toArray())->get();
//                $bookingData = new BookingDataController();
//                foreach ($all_bookings as $booking)
//                {
//                    $bookingData->SendNotificationToDrivers($booking);
//                }
//            endif;
//        endif;

        foreach ($all_bookings as $booking):
            $string_file = $this->getStringFile($booking->merchant_id);
            $minutes = 30; //minutes
            date_default_timezone_set($booking->CountryArea['timezone']);
            $booking_time = $booking->later_booking_date . ' ' . $booking->later_booking_time;
            $current_date_time = date('Y-m-d H:i');
            $date1=date_create($booking_time);
            $date2=date_create($current_date_time);
            $diff=date_diff($date1,$date2);
            $diff_minute = $diff->i;
            if ($current_date_time > $booking_time) {
                $segment_data = [
                    'id'=>$booking->id,
                    'booking_status'=>$booking->booking_status,
                ];
                if (!empty($booking->driver_id))
                {
                    $data = array('booking_id' => $booking->id, 'notification_type' => 'RIDE_EXPIRED', 'segment_type' => $booking->Segment->slag,'segment_data' => $segment_data);
                    $arr_param = array(
                        'driver_id' => $booking->driver_id,
                        'data'=>$data,
                        'message'=>trans("$string_file.ride_expired"),
                        'merchant_id'=>$booking->merchant_id,
                        'title' => '#'.$booking->merchant_booking_id .' '.trans("$string_file.ride_expired_title"),
                    );
                    Onesignal::DriverPushMessage($arr_param);
                }

                $data = array('notification_type' => 'RIDE_EXPIRED','segment_type' => $booking->Segment->slag,'segment_data'=>$segment_data);
                $arr_param = array(
                    'user_id' => $booking->user_id,
                    'data'=>$data,
                    'message'=>trans("$string_file.ride_expired"),
                    'merchant_id'=>$booking->merchant_id,
                    'title' => '#'.$booking->merchant_booking_id .' '.trans("$string_file.ride_expired_title")
                );
                Onesignal::UserPushMessage($arr_param);
                $booking->booking_status = 1018;  // expired booking
                $booking->save();

            }
            elseif($diff_minute == $minutes)
            {
                if (!empty($booking->driver_id))
                {
                    $segment_data = [
                        'id'=>$booking->id,
                        'booking_status'=>$booking->booking_status,
                    ];
                    $data = array('booking_id' => $booking->id, 'notification_type' => 'UPCOMING_RIDE', 'segment_type' => $booking->Segment->slag,'segment_data' => $segment_data);
                    $arr_param = array(
                        'driver_id' => $booking->driver_id,
                        'data'=>$data,
                        'message'=>trans("$string_file.upcoming_ride_at").' '.$booking_time,
                        'merchant_id'=>$booking->merchant_id,
                        'title' => '#'.$booking->merchant_booking_id .' '.trans("$string_file.new_upcoming_ride"),
                    );
                    Onesignal::DriverPushMessage($arr_param);
                }
            }
        endforeach;

    }

    // expire not accepted handyman orders
    public function expireNotAcceptedHandymanOrders()
    {
        $query = HandymanOrder::select('id', 'merchant_id', 'segment_id', 'drop_location', 'user_id', 'booking_date', 'booking_timestamp', 'service_time_slot_detail_id','country_area_id','order_status')
            ->with(['ServiceTimeSlotDetail' => function ($q) {
                $q->addSelect('id', 'to_time','from_time');
            }])->whereIn('order_status',[1,2])
            ->where([['created_at', '<=', date('Y-m-d')]]);
        $arr_orders = $query->get();
        if ($arr_orders->isNotEmpty()):
            $arr_orders = $arr_orders->map(function ($item, $key) {
                date_default_timezone_set($item->CountryArea['timezone']);
                $job_expire_status = $this->calculateExpireTime($item->ServiceTimeSlotDetail->from_time,$item->booking_timestamp,'CRON');
                return $job_expire_status ? $item->id : NULL;
            })->filter()->values();
            if ($arr_orders->isNotEmpty()):

                $log_data =[
                    'handyman_order_id'=>$arr_orders->toArray(),
                    'request_type'=>"placed handyman order expire request"
                ];
                \Log::channel('per_minute_cron_log')->emergency($log_data);

                HandymanOrder::whereIn('id', $arr_orders->toArray())
                    ->update([
                        'order_status' => '8',
                    ]);
            endif;
        endif;
        $handman_orders = HandymanOrder::whereIn('id', $arr_orders->toArray())->get();
        foreach ($handman_orders as $order)
        {
            // $request = (object)array('notification_type' => 'EXPIRE_ORDER');
            $this->sendNotificationToUser($order);
        }
    }

    // expire placed orders
    public function expirePlacedOrders()
    {

         DB::beginTransaction();
        try {
               $all_orders = Order::select('id','business_segment_id','merchant_id','service_time_slot_detail_id','order_timestamp','country_area_id','driver_id','order_status','segment_id','user_id','order_date','order_type')
            ->whereIn('order_status', [1])->get();
            // p($all_orders);
        $current_time_stamp = NULL;
        if ($all_orders->isNotEmpty())
        {
            $order_ids = $all_orders->map(function ($item, $key) {
                if($item->segment_id == 3) { // for food
                    $current_time_stamp =  time();
                    $config = BusinessSegmentConfigurations::where('business_segment_id', $item->business_segment_id)->first();
                    if (!empty($config)) {
                        $minute = $config->order_expire_time * 60;
                    } else {
                        $minute = 10 * 60;
                    }
                    $order_time = $item->order_timestamp;
                    $expire_order_time = $order_time + $minute;// 5 minutes after older placed
                }
                else{

                    $current_time_stamp  = NULL;
                    $expire_order_time = "";
                    if(!empty($item->ServiceTimeSlotDetail) && $item->order_type == 2)
                    {
                        date_default_timezone_set($item->CountryArea['timezone']);
                        $current_time_stamp =  time();

                        $slot_time = $item->ServiceTimeSlotDetail->to_time;
                        $expire_order_time = strtotime($item->order_date.' '.$slot_time);
                        // p($item->id.' '.$item->order_date.' '.$slot_time,0);
                    }
                     elseif($item->order_type == 1)
                    {
                        $current_time_stamp =  time();
                        $config = BusinessSegmentConfigurations::where('business_segment_id', $item->business_segment_id)->first();
                        if (!empty($config)) {
                            $minute = $config->order_expire_time * 60;
                        } else {
                            $minute = 10 * 60;
                        }
                        $order_time = $item->order_timestamp;
                        $expire_order_time = $order_time + $minute;// 5 minutes after older placed
                        
                    }
                }
                return ($current_time_stamp > $expire_order_time  ) ? $item->id : null;
            })->filter()->values();
            // p($order_ids);
            // p('end');
            if($order_ids->count() > 0)
            {
                $log_data =[
                    'order_id'=>$order_ids->toArray(),
                    'request_type'=>"placed order expire request"
                ];
                \Log::channel('per_minute_cron_log')->emergency($log_data);

                Order::whereIn('id', $order_ids->toArray())
                    ->update([
                        'order_status' => '12', //auto expired
                    ]);
                $arr_orders = Order::
                    // select('id','merchant_id','merchant_order_id','order_status','segment_id','user_id','payment_method_id','payment_option_id')->
                    whereIn('id', $order_ids->toArray())->get();
            //   p($arr_orders);
                foreach ($arr_orders as $order)
                {
                    // p($order);
                    $this->sendNotificationToUser($order);
                    // p($order);
                    // refund credit to user wallet if payment done while placing order
                    // var_dump((!empty($order->payment_method_id) && in_array($order->payment_method_id,[2,4,3])));
                    // p('end');
                    if(!empty($order->payment_method_id) && in_array($order->payment_method_id,[2,4,3]))
                    {
                       $user = User::select('wallet_balance','id','merchant_id')->where('id',$order->user_id)->first();
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



    public function testCron(){
        \Log::channel('onesignal')->emergency(array(
            "text" => "Hello",
            "message" => "Your most welcome!"
        ));
    }

    // ride quest from whatsapp
    public function expireNewRequestedRides()
    {
        $bookings = Booking::select('id','merchant_id','driver_id','merchant_booking_id','country_area_id','booking_status','created_at')->where([['booking_status', '=', 1001], ['platform', '=', 3]])->orWhere([['booking_status', '=', 1001], ['booking_type', '=', 1]])->get();
        if (!empty($bookings->toArray())) {
            $minutes = 1; //minutes
            $log_data =[
                'booking_id'=>$bookings->toArray(),
                'request_type'=>"whatsapp booked ride expire"
            ];
            \Log::channel('per_minute_cron_log')->emergency($log_data);
            foreach ($bookings as $booking):
                $string_file = $this->getStringFile($booking->merchant_id);
//                date_default_timezone_set($booking->CountryArea['timezone']);
                $booking_time = $booking->created_at;
                $current_date_time = date('Y-m-d H:i:s');
                $date1=date_create($booking_time);
                $date2=date_create($current_date_time);
                $diff=date_diff($date1,$date2);
                $diff_minute = $diff->i;
                if ($diff_minute > $minutes) {
                    if (!empty($booking->driver_id))
                    {
                        $message = trans("$string_file.no_driver_found");
                        $whatsApp = new WhatsappController;
                        if(!empty($booking->User)){
                            $whatsApp->sendWhatsApp($booking->User->UserPhone,$message,$booking->merchant_id);
                        }
                    }
                    $booking->booking_status = 1016;  //  auto expired booking
                    $booking->save();
                }
            endforeach;
        }
    }

    public function rejectOrderRequest()
    {
        $all_orders = BookingRequestDriver::select('id','order_id','request_status','driver_id','created_at')->where('order_id','!=',NULL)
            ->where('request_status', 1)->get();
        // p($all_orders);
        if($all_orders->isNotEmpty())
        {
                $order_ids = $all_orders->map(function ($item, $key) {
                $current_time_stamp = time();
                $expire_order_time = (strtotime($item->created_at) + 60); // after 60 sec
                return ($current_time_stamp > $expire_order_time  ) ? $item->id : null;
            })->filter()->values();
            // p($order_ids);
            if($order_ids->count() > 0)
            {
                $log_data =[
                    'order_id'=>$order_ids->toArray(),
                    'request_type'=>"order request reject"
                ];
                \Log::channel('per_minute_cron_log')->emergency($log_data);
                BookingRequestDriver::whereIn('id', $order_ids->toArray())
                    ->update([
                        'request_status' => 3, //request expired or rejected automatically
                    ]);
            }
        }
    }
    
    public function checkCron(){
        $this->expireNotAcceptedHandymanOrders();
    }
}
