<?php

namespace App\Traits;

use App\Models\Driver;
use App\Models\HandymanOrder;
use App\Models\Onesignal;
use Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Traits\MerchantTrait;
use App\Traits\DriverTrait;

trait HandymanTrait
{
    use DriverTrait;

    public function getHandymanOrders($request, $string_file = "")
    {
        $driver = $request->user('api-driver');
        $driver_id = $driver->id;
        $merchant_id = $driver->merchant_id;
        $area_id = $driver->country_area_id;
        $segment_id = [];
        $online_work_set = $this->getDriverOnlineConfig($driver, 'all');
        if ($online_work_set['status'] == 1) {
            $segment_id = array_unique($online_work_set['segment_id']);
        }
        //p($online_work_set);
        $query = HandymanOrder::select('id', 'merchant_id', 'segment_id', 'drop_location', 'user_id', 'handyman_orders.driver_id', 'handyman_orders.driver_id', 'payment_method_id', 'quantity', 'final_amount_paid', 'order_status', 'booking_date', 'booking_timestamp', 'service_time_slot_detail_id');

        if (($request->type == 'PENDING' || $request->type == 'ALL') && !empty($driver->ActiveAddress->id)) {
            $distance_unit = 1;
            $address = $driver->WorkShopArea; // workshop area of driver
            $distance = $address->radius;
            $latitude = $address->latitude;
            $longitude = $address->longitude;
            //p($driver->ActiveAddress);
            $radius = $distance_unit == 2 ? 3958.756 : 6367;
            $query->addSelect(DB::raw('( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( drop_latitude ) ) * cos( radians( drop_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( drop_latitude ) ) ) ) AS distance'))
                ->having('distance', '<', $distance)
                ->orderBy('distance');

//            $query->whereNOTIn('handyman_orders.id', function ($q) use ($driver_id) {
//                $q->select('brd.handyman_order_id')
//                    ->from('booking_request_drivers as brd')
//                    ->join('handyman_orders as ho', 'brd.handyman_order_id', '=', 'ho.id')
//                    //->where('o.order_status', '=', 1)
//                    ->where(function ($p) use ($driver_id) {
//                        $p->where([['brd.driver_id', $driver_id], ['brd.request_status', 3]])->orWhere([['brd.request_status', 1]]);
//                    });
//            });
        }
        $query->with(['User' => function ($q) {
            $q->addSelect('id', 'first_name', 'last_name');

        }])
            ->with(['HandymanOrderDetail' => function ($q) {
                $q->addSelect('handyman_order_id', 'service_type_id', 'segment_price_card_id');

            }])
            ->with(['HandymanOrderDetail.ServiceType' => function ($q) {
                $q->addSelect('service_types.id', 'service_types.serviceName');

            }])
            ->with(['ServiceTimeSlotDetail' => function ($q) {
                $q->addSelect('id', 'to_time', 'from_time');
            }])
            ->leftJoin(DB::raw('(SELECT dsr.driver_id, CAST(AVG (dsr.rating) AS DECIMAL (2,1)) AS rating FROM `driver_segment_ratings` as dsr GROUP BY dsr.driver_id) dsr'), 'handyman_orders.driver_id', '=', 'dsr.driver_id')
            ->where([['merchant_id', '=', $merchant_id]])
            ->whereIn('segment_id', $segment_id)
            ->where(function ($q) use ($driver_id, $request, $driver) {
                if ($request->type == 'COMPLETED') {
                    $q->where([['order_status', '=', 7], ['is_order_completed', '=', 1], ['payment_status', '=', 1], ['handyman_orders.driver_id', '=', $driver_id]]);
                } elseif ($request->type == 'CANCELLED') {
                    $q->where([['handyman_orders.driver_id', '=', $driver_id]]);
                    $q->whereIn('order_status', [2, 5]);
                } elseif ($request->type == 'REJECTED') {
                    $q->where([['order_status', '=', 3], ['handyman_orders.driver_id', '=', $driver_id]]);
                } elseif ($request->type == 'PENDING') {
                    $q->whereDate('booking_date', '>=', date('Y-m-d'));
                    $q->where([['order_status', '=', 1], ['handyman_orders.driver_id', '=', NULL]]);
                    $q->whereNOTIn('id', function ($query) use ($driver_id) {
                        $query->select('handyman_order_id')
                            ->from('booking_request_drivers as brd')
                            ->where('request_status', '=', 3)->where('driver_id', $driver_id);
                    });
                } elseif ($request->type == 'ALL') {
                    $q->where(function ($q) {
                        $q->where([['booking_date', '>=', date('Y-m-d')], ['order_status', '=', 1]])
                            ->orWhere([['order_status', '=', 6], ['booking_date', '!=', '']])
                            ->orWhere([['order_status', '=', 4], ['booking_date', '>=', date('Y-m-d')]])
                            ->orWhere([['order_status', '=', 7], ['is_order_completed', '!=', 1]])
                        ;
                    });
                    $q->where([['is_order_completed', '!=', 1]]);
                    $q->whereNotIn('order_status', [3, 2, 5]);
                    //$q->orderBy(DB::raw('FIELD(order_status,6,4,1))'));
                    $q->where(function ($qq) use ($driver_id, $request) {
                        $qq->where([['order_status', '=', 1], ['handyman_orders.driver_id', '=', NULL]]);
                        $qq->orWhere([['handyman_orders.driver_id', '=', $driver_id]]);
                    });
                    $q->whereNOTIn('id', function ($query) use ($driver_id) {
                        $query->select('handyman_order_id')
                            ->from('booking_request_drivers as brd')
                            ->where('request_status', '=', 3)->where('driver_id', $driver_id);
                    });
                } elseif ($request->type == 'TODAY') {
                    $order_date = date('Y-m-d');
                    $q->where([['order_status', '=', 4], ['booking_date', '=', $order_date]]);
                    $q->where([['handyman_orders.driver_id', '=', $driver_id]]);
                } elseif ($request->type == 'TOMORROW') {
                    $datetime = new DateTime();
                    $datetime->modify('+1 day');
                    $order_date = $datetime->format('Y-m-d');
                    $q->where([['order_status', '=', 4], ['booking_date', '=', $order_date]]);
                    $q->where([['handyman_orders.driver_id', '=', $driver_id]]);
                } elseif ($request->type == 'THIS_WEEK') {
                    $order_from_date = date('Y-m-d');
                    $order_to_date = date('Y-m-d', strtotime('Saturday'));
                    $q->where([['order_status', '=', 4]]);
                    $q->whereBetween('booking_date', [$order_from_date, $order_to_date]);
                    $q->where([['handyman_orders.driver_id', '=', $driver_id]]);
                }
//                elseif ($request->type == 'ONGOING') {
//                    $q->where([['order_status', '=', 6]]);
//                    $q->orWhere(function($qqq){
//                        $qqq->where([['order_status', '=', 7],['is_order_completed', '=', 2]]);
//                    });
//                    $q->where([['handyman_orders.driver_id', '=', $driver_id]]);
//                }
            });
        $query->orderBy('order_status', 'DESC');
        $query->where('country_area_id', $area_id);
        $arr_orders = $query->get();
        return $arr_orders;
    }

    // send notification to driver
    public function sendNotificationToProvider($request, $arr_driver_id, $order, $string_file = "")
    {
        $order_status = $order->order_status;
        $merchant_id = $order->merchant_id;
        $data['notification_type'] = $request->notification_type;
        $data['segment_type'] = "HANDYMAN";//$order->Segment->slag;
        $data['segment_sub_group'] = $order->Segment->sub_group_for_app; // for handyman
        $data['segment_group_id'] = $order->Segment->segment_group_id; // for handyman
        $segment_name = $order->Segment->Name($merchant_id);
        $item = $order->Segment;
        $large_icon = $large_icon = isset($item->Merchant[0]['pivot']->segment_icon) && !empty($item->Merchant[0]['pivot']->segment_icon) ? get_image($item->Merchant[0]['pivot']->segment_icon, 'segment', $merchant_id, true) :
            get_image($item->icon, 'segment_super_admin', NULL, false);
        $data['segment_data'] = [
            "order_id" => $order->id,
        ]; // notification data

//        $string_file = $this->getStringFile($merchant_id);
        $order_number = $order->merchant_order_id;
        $booking_string = trans("$string_file.booking");
        // title and message of notification based on order status
        if (!is_array($arr_driver_id)) {
            $arr_driver_id = [$arr_driver_id];
        }
        foreach ($arr_driver_id as $driver_id) {
            $driver = Driver::find($driver_id);
            setLocal($driver->language);
            switch ($order_status) {
                case "1":
                    $title = $segment_name . ' ' . trans("$string_file.new") . ' ' . $booking_string;
                    $message = trans("$string_file.new_booking_driver_message");
                    break;
                case "2":
                    $user_name = $order->User->first_name . ' ' . $order->User->last_name;
                    $title = $segment_name . ' ' . $booking_string . ' ' . trans("$string_file.cancelled");
                    $message = trans_choice("$string_file.booking_cancelled_by", 3, ['ID' => $order_number, '.' => $user_name]);
                    break;
            }
            $arr_param = ['driver_id' => $driver_id, 'data' => $data, 'message' => $message, 'merchant_id' => $merchant_id, 'title' => $title, 'large_icon' => $large_icon];
            Onesignal::DriverPushMessage($arr_param);
        }
        setLocal();
        return true;
    }

    // send notification to user
    public function sendHandymanNotificationToUser($request, $order, $message = "", $string_file = "")
    {
        $order_status = $order->order_status;
        $merchant_id = $order->merchant_id;
        $data['notification_type'] = $request->notification_type;
        $data['segment_id'] = $order->segment_id;
        $data['segment_type'] = $order->Segment->slag;
        $data['segment_sub_group'] = $order->Segment->sub_group_for_app; // for handyman
        $data['segment_group_id'] = $order->Segment->segment_group_id; // for handyman
        $user_id = $order->user_id;
        setLocal($order->User->language);
        $segment_name = $order->Segment->Name($merchant_id);
        $item = $order->Segment;
        $large_icon = "";
        $type = NULL;
        if (in_array($order->order_status, [1, 4])) {
            $type = 1; // schedule
        } elseif (in_array($order->order_status, [6]) || ($order->order_status == 7 && $order->payment_status != 1)) {
            $type = 2; // 2 ongoing
        } elseif (in_array($order->order_status, [2, 5]) || ($order->order_status == 7 && $order->payment_status == 1)) {
            $type = 3; // past
        }
        $data['segment_data'] = [
            "order_id" => $order->id,
            "order_status" => $order->order_status,
            "type" => $type,
        ]; // notification data

//        $string_file = $this->getStringFile($merchant_id);
        $booking_string = trans("$string_file.booking");
        $order_number = $order->merchant_order_id;

        $title = "";
        if($request->notification_type == "PENDING_PAYMENT")
        {
            $title = trans("$string_file.pending_amount_booking") .' '.$request->pending_amount;
            $message = trans("$string_file.pending_amount_booking_message");
        }
        if($request->notification_type == "ADDITIONAL_CHARGES_APPLIED")
        {
            $title = trans("$string_file.additional_charges_booking") .' '.$request->additional_chagres;
            $message = trans("$string_file.additional_chagres_booking_message");
        }
        if ($request->notification_type == "ORDER_OTP") {
            $title = $segment_name . ' ' . $booking_string . ' ' . trans("$string_file.otp");
//            $message = $message;
        } else {
            // title and message of notification based on order status
            switch ($order_status) {
                case "4":
                    $title = $segment_name . ' ' . $booking_string . ' ' . trans("$string_file.accepted");
                    $message = trans("$string_file.booking_accepted_successfully");
                    break;

                case "5":
                    $driver_name = $order->Driver->first_name . ' ' . $order->Driver->last_name;
                    $title = $segment_name . ' ' . $booking_string . ' ' . trans("$string_file.cancelled");
                    $message = trans_choice("$string_file.booking_cancelled_by", 3, ['ID' => $order_number, '.' => $driver_name]);
                    break;

                case "6":
                    $driver_name = $order->Driver->first_name . ' ' . $order->Driver->last_name;
                    $title = $segment_name . ' ' . $booking_string . ' ' . trans("$string_file.started");
                    $message = trans_choice("$string_file.booking_started_by", 3, ['ID' => $order_number, '.' => $driver_name]);
                    break;
                case "7":
                    $title = $segment_name . ' '. trans("$string_file.booking_completed");
                    $message = trans("$string_file.booking_completed_successfully");
                    break;
                case "8":
                    $title = $segment_name . ' ' . trans("$string_file.booking_expired");
                    $message = trans_choice("$string_file.booking_request_expired_message", 3, ['ID' => $order_number]);
                    break;
            }
        }
        $arr_param = ['user_id' => $user_id, 'data' => $data, 'message' => $message, 'merchant_id' => $merchant_id, 'title' => $title, 'large_icon' => $large_icon];
        Onesignal::UserPushMessage($arr_param);
        setLocal();
        return true;
    }

    public function calculateExpireTime($start_time, $order_crate_timestamp, $calling_from = 'API', $string_file = "")
    {
        $result = '';
        if (strtotime($start_time) > $order_crate_timestamp) {
            $percetile = .25;
            $start = strtotime($start_time);
            $book_before = ($start - $order_crate_timestamp) * $percetile;
            if ($calling_from == 'CRON') {
                if ($book_before > 0) {
                    $order_date = date('Y-m-d',$order_crate_timestamp);
                    $slot_date = date('Y-m-d',strtotime($start_time));
                    if($order_date < $slot_date){
                        $result = true;
                    }else{
                        $result = false;
                    }
                } else {
                    $result = true;
                }
            } else {
                $result = date('H', $book_before) . ' hour ' . date('i', $book_before) . ' min';
            }
        } else {
            if ($calling_from == 'CRON') {
                $result = false;
            } else {
                $result = date('H', $order_crate_timestamp) . ' hour ' . date('i', $order_crate_timestamp) . ' min';
            }
        }
        return $result;
    }

    public function getHandymanBookingStatus($req_param = [], $string_file = "")
    {
        if (isset($req_param['string_file'])) {
            $string_file = $req_param['string_file'];
        } else {
            $merchant_id = $req_param['merchant_id'];
//            $string_file =  $this->getStringFile($merchant_id);
        }
//       $booking_string = trans("$string_file.booking");
        return array(
            '1' => trans("$string_file.pending"),//'Booking Pending',//Order placed
            '2' => trans("$string_file.ride_cancelled_by_user"),//
            '3' => trans("$string_file.rejected"),
            '4' => trans("$string_file.accepted"),
            '5' => trans("$string_file.ride_cancelled_by_driver"),////'Cancelled by Provider',
            '6' => trans("$string_file.started"),//'Booking Started ',
            '7' => trans("$string_file.completed"),//'Booking Finished',
            '8' => trans("$string_file.expired"),//'Booking Expired',
        );
    }
}
