<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helper\DriverRecords;
use App\Http\Controllers\Helper\Merchant;
use App\Http\Controllers\Helper\PriceController;
use App\Http\Controllers\Helper\ReferralController;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\BillPeriodCountryArea;
use App\Models\BookingRequestDriver;
use App\Models\BusinessSegment\Order;
use App\Models\Driver;
use App\Models\DriverAccount;
use App\Models\HandymanOrder;
use App\Models\Segment;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use DB;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\Booking;
use App\Models\DriverWalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class DriverEarningController extends Controller
{
    use ApiResponseTrait, MerchantTrait;
    public function AddMoney(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'payment_method' => 'required|integer|between:1,2',
            'receipt_number' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $driver = $request->user('api-driver');
            $string_file = $this->getStringFile(NULL, $driver->Merchant);
            $paramArray = array(
                'driver_id' => $driver->id,
                'booking_id' => NULL,
                'amount' => $request->amount,
                'narration' => 2,
                'platform' => 2,
                'payment_method' => $request->payment_method,
                'receipt' => $request->receipt_number,
            );
            WalletTransaction::WalletCredit($paramArray);
            $money = $driver->wallet_money;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans("$string_file.money_added_in_wallet"));
    }

    public function WalletTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'filter' => 'required|integer|between:1,3',
            'duration'=>'required',
            'from'=>'required_if:duration,custom',
            'to'=>'required_if:duration,custom',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            if ($request->filter == 3) {
                $filter = array(1, 2);
            } else {
                $filter = array($request->filter);
            }
            $driver = $request->user('api-driver');
            $string_file = $this->getStringFile(NULL, $driver->Merchant);
            $currency = $driver->CountryArea->Country->isoCode;
            $driver_id = $driver->id;
            // $transaction = DriverWalletTransaction::where([['driver_id', '=', $driver_id]])->whereIn('transaction_type', $filter)->latest()->paginate(10);
            $query = DriverWalletTransaction::where([['driver_id', '=', $driver_id]])->whereIn('transaction_type', $filter);
            $current_date=date('Y-m-d');
            if($request->duration=='today'){
                $query->whereDate('created_at','=',$current_date);
            }
            elseif($request->duration=='yesterday'){
                $query->whereDate('created_at','=',date('Y-m-d',strtotime("-1 day",strtotime($current_date))));
            }
            elseif($request->duration=='one_week'){
                $from=date('Y-m-d',strtotime("-7 day",strtotime($current_date)));
                $to=date('Y-m-d');
                $query->whereBetween('created_at',[$from,$to]);
            }
            elseif($request->duration=='one_month'){
                $from=date('Y-m-d',strtotime("-1 month",strtotime($current_date)));
                $to=date('Y-m-d');
                $query->whereBetween('created_at',[$from,$to]);
            }
            elseif($request->duration=='three_month'){
                $from=date('Y-m-d',strtotime("-3 month",strtotime($current_date)));
                $to=date('Y-m-d');
                $query->whereBetween('created_at',[$from,$to]);
            }
            elseif($request->duration=='six_month'){
                $from = date('Y-m-d',strtotime("-6 month",strtotime($current_date)));
                $to=date('Y-m-d');
                $query->whereBetween('created_at',[$from,$to]);
            }
            elseif($request->duration=='one_year'){
                $from=date('Y-m-d',strtotime("-1 year",strtotime($current_date)));
                $to=date('Y-m-d');
                $query->whereBetween('created_at',[$from,$to]);
            }elseif($request->duration=='custom'){
                $from=date($request->from." 00:00:00");
                $to=date($request->to." 23:59:59");
                $query->whereBetween('created_at',[$from,$to]);
            }
            $transaction=$query->latest()->paginate(10);
            $newArray = $transaction->toArray();
            $data = array();
            $wallet_money = !empty($driver->wallet_money) ? $currency . " " . $driver->wallet_money : $currency . " 0.00";
//            $wallet_money = $currency . " " . $driver->wallet_money;
            $next_page_url = "";
            if (!empty($newArray['data'])) {
                $next_page_url = $newArray['next_page_url'];
                $next_page_url = $next_page_url == "" ? "" : $next_page_url;
                foreach ($newArray['data'] as $value) {
                    $id = "";
                    if(!empty($value['booking_id'])){
                        $booking = Booking::select("merchant_booking_id")->find($value['booking_id']);
                        $id = $booking->merchant_booking_id;
                    }else{
                        if(!empty($value['order_id'])){
                            $order = Order::select("merchant_order_id")->find($value['order_id']);
                            $id = $order->merchant_order_id;
                        }elseif(!empty($value['handyman_order_id'])){
                            $order = HandymanOrder::select("merchant_order_id")->find($value['handyman_order_id']);
                            $id = $order->merchant_order_id;
                        }
                    }
                    $transaction_type = $value['transaction_type'];
                    $payment_method = $value['payment_method'];
                    $platform = $value['platform'];
                    $narration = $value['narration'];
                    switch ($transaction_type) {
                        case "1":
                            $transaction_value = "Credit"; // at app end there are some condition on
                            // $transaction_value text
//                                trans("$string_file.credit");
                            $value_color = "2ecc71";
                            $image = view_config_image("static-images/cash_in.png");
                            break;
                        case "2":
                            $transaction_value =  "Debit";
//                                trans("$string_file.debit");
                            $value_color = "e74c3c";
                            $image = view_config_image("static-images/cash_out.png");
                            break;
                    }
                    switch ($payment_method) {
                        case "1":
                            $payment_method = trans("$string_file.cash");
                            break;
                        case "2":
                            $payment_method = trans("$string_file.non_cash");
                            break;
                    }
                    switch ($platform) {
                        case "1":
                            $platform = trans("$string_file.admin");
                            break;
                        case "2":
                            $platform = trans("$string_file.application");
                            break;
                        case "3":
                            $platform = trans("$string_file.web");
                            break;
                    }
//
                    $narration = get_narration_value("DRIVER",$narration,$driver->merchant_id,$id,"");
                    $data[] = array(
                        'driver_id' => $value['driver_id'],
                        'transaction_type' => $transaction_value,
                        'payment_method' => $payment_method,
                        'amount' => $currency . " " . $value['amount'],
                        'platform' => $platform,
                        'date' => date('d M, D H:i a',strtotime(convertTimeToUSERzone($value['created_at'],
                            $driver->CountryArea->timezone,
                            null,$driver->Merchant))),
                        'description' => $value['description'],
                        'narration' => $narration,
                        'value_color' => $value_color,
                        'icon' => $image,
                       'date_timestamp' =>strtotime($value['created_at']),
                    );
                }
            }
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
        $result = array(
            'wallet_money' => $wallet_money,
            'recent_transactions' => $data,
            'total_pages' => $newArray['last_page'],
            'next_page_url' => $next_page_url,
            'current_page' => $newArray['current_page'],
        );
        return $this->successResponse(trans("$string_file.wallet_transaction"), $result);
    }



    public function NewGenerateBill(Driver $driver_obj, $request, $timePeriod)
    {
        try {

            $bookings = Booking::where([['booking_status', '=', '1006']])->whereBetween('updated_at', [$timePeriod['from'], $timePeriod['to']])->where([['driver_id', '=', $driver_obj->id]])
                ->orWhere([['booking_closure', '=', '1']])->whereBetween('updated_at', [$timePeriod['from'], $timePeriod['to']])->where([['driver_id', '=', $driver_obj->id]])
                ->with(['BookingTransaction'])
                ->whereHas('Segment', function ($q) use ($request) {
                    $q->where('slag', $request->segment_slug);
                })
                ->get();
            if ($bookings->isEmpty()):
                return [];
            endif;
            $transaction_collection = $bookings->map(function ($item, $key) {
                return $item['BookingTransaction'];
            })->filter()->values();
            if ($transaction_collection->isEmpty()):
                return [];
            endif;
            $merchant = new Merchant();
            //$driver_config = $driver_obj->Merchant->DriverConfiguration;
            $trips = $bookings->filter(function ($item, $key) {
                return $item['booking_status'] != 1006 && !empty($item['BookingTransaction']);
            })->count();

            $data_with_transactions = $bookings->filter(function ($item, $key) {
                return !empty($item['BookingTransaction']);
            });

            $rides_data = array();
            foreach ($data_with_transactions as $value) {
                $rides_data[] = array('rides_color' => 'bbbbbb', 'ride_name' => 'CRN # ' . $value['id'], 'ride_id' => $value['id'], 'ride_earning' => $driver_obj->CountryArea->Country->isoCode . " " . sprintf("%0.2f", $value['BookingTransaction']['driver_total_payout_amount']));
            }
            $referral_controller = new ReferralController();
            $driver_refer_amount = $referral_controller->getDriverReferEarning($driver_obj->merchant_id, $driver_obj->id, $timePeriod['from'], $timePeriod['to']);

            $fare_amount_sum = $transaction_collection->sum('sub_total_before_discount');
            $company_commission_sum = $transaction_collection->sum('company_earning');
            $toll_amount_sum = $transaction_collection->sum('toll_amount');
            $tip_amount_sum = $transaction_collection->sum('tip');
            $cancellation_charges_sum = $transaction_collection->sum('cancellation_charge_applied');
//        $referral_amount_sum = $merchant->TripCalculation($transaction_collection->sum('referral_amount'), $driver_obj->merchant_id);
            $referral_amount_sum = $driver_refer_amount;
            $cash_payment_sum = $transaction_collection->sum('cash_payment');
            $online_payment_sum = $transaction_collection->sum('online_payment');
            $amount_deducted_from_driver_wallet = $transaction_collection->sum('amount_deducted_from_driver_wallet');
            $tax_amount = $transaction_collection->sum('tax_amount');
            $trips_outstanding_sum = $transaction_collection->sum('trip_outstanding_amount');
            $discount_amount = $transaction_collection->sum('discount_amount');
            $round_off_amount = round($transaction_collection->sum('rounded_amount'), 2);
            $old_outstanding_received = $transaction_collection->sum('cancellation_charge_received');

            $total_trips_till_now = $driver_obj->total_trips;
            // $amount = $fare_amount_sum + $toll_amount_sum + $tip_amount_sum + $cancellation_charges_sum + $referral_amount_sum - $company_commission_sum - $cash_payment_sum + $amount_deducted_from_driver_wallet;

            $driver_total_payout_amount = $merchant->TripCalculation($transaction_collection->sum('driver_total_payout_amount'), $driver_obj->merchant_id);


            $amount = $driver_total_payout_amount - $cash_payment_sum + $amount_deducted_from_driver_wallet;
            return [
                'printable' => array(
                    'net_earnings' => $fare_amount_sum + $round_off_amount - $company_commission_sum + $cancellation_charges_sum,
                    'trips' => $trips,
                    'cash_payment_sum' => $cash_payment_sum,
                    'online_payment_sum' => $online_payment_sum + $discount_amount + $cancellation_charges_sum,
                    'amount' => $amount,
                    'rides_data' => $rides_data,
                    'fare_amount_sum' => $fare_amount_sum + $tax_amount - $discount_amount,
                ),
                'holder' => array(
                    'fare_amount_sum' => $fare_amount_sum + $tax_amount - $discount_amount,
                    'company_commission_sum' => $company_commission_sum,
                    'tax' => $tax_amount,
//                    'toll_amount_sum' => $toll_amount_sum,
                    'tip_amount_sum' => $tip_amount_sum,
//                    'cancellation_charges_sum' => $cancellation_charges_sum,
                    'referral_amount_sum' => $referral_amount_sum,
                    'round_off_amount' => $round_off_amount,
                    'old_outstanding_received' => $old_outstanding_received,
                    // 'net_earnings' => $amount+$cash_payment_sum,
                    //'net_earnings' => $amount+$cash_payment_sum-$tax_amount,
                ),
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function DriverBookingAccountEarnings(Request $request)
    {
        try {
            $date = date_create($request->date . '00:00:00');
            $date = \Carbon\Carbon::parse(date_format($date, 'Y-m-d H:i:s'));
            $date->setISODate(date_format($date, 'Y'), date_format($date, 'W'));
            $start_date = $date->startOfWeek()->format('Y-m-d H:i:s');
            $end_date = $date->endOfWeek()->format('Y-m-d H:i:s');

            $driver = $request->user('api-driver');
            $currency = $driver->CountryArea->Country->isoCode;

            $bill_timeperiod = array('from' => $start_date, 'to' => $end_date);

            $data = $this->NewGenerateBill($driver, $request, $bill_timeperiod);

            $string_file = $this->getStringFile($driver->merchant_id);

            $holder_data = !empty($data['holder']) ? $data['holder'] : [];
            $printable = !empty($data['printable']) ? $data['printable'] : [];
            $net_earnings = isset($printable['net_earnings']) ? round($printable['net_earnings'], 2) : 0;
            $trips = isset($printable['trips']) ? $printable['trips'] : 0;
            $fare_amount_sum = isset($printable['fare_amount_sum']) ? $printable['fare_amount_sum'] : 0;
            $cash_payout_sum = isset($printable['cash_payment_sum']) ? $printable['cash_payment_sum'] : 0;
            $online_payment_sum = isset($printable['online_payment_sum']) ? $printable['online_payment_sum'] : 0;

            $holder = [];
            if (!empty($holder_data)):
                foreach ($holder_data as $key => $value):
                    $type = '';
                    if ($key == 'tax' || $key == 'company_commission_sum') {
                        $type = ' - ';
                    } else if ($key == 'fare_amount_sum' || $key == 'cash_payment_sum') {
                        $type = '';
                    } else {
                        $type = ' + ';
                    }
                    $holder[] = array(
                        "name" => $this->getBillDetailKey($string_file,$key), // trans('api.' . $key),
                        "value" => $type . $currency . ' ' . round_number($value),
                        "bold" => false,
                    );
                endforeach;
            endif;
            // push total earning array to holder
           $total_earning =  array(
                "name" => trans("$string_file.total_earning"),
                "value" => $currency . ' ' . $net_earnings,
                "bold" => true,
            );
            array_push($holder, $total_earning);
            $newArray['total_earnings'] = $currency . ' ' . $net_earnings;
            $newArray['received_cash'] = $currency . ' ' . $cash_payout_sum;
            $newArray['received_in_wallet'] = $currency . ' ' . $online_payment_sum;
            $newArray['from_timestamp'] = strtotime($bill_timeperiod['from']);
            $newArray['to_timestamp'] = strtotime($bill_timeperiod['to']);
            $newArray['wallet_balance'] = $currency . ' ' . $driver->wallet_money;
            $newArray['holder_data'] = $holder;

            $date = $request->date;
            $ts = strtotime($date);
            $dow = date('w', $ts);
            $offset = $dow - 1;
            if ($offset < 0) {
                $offset = 6;
            }
            $ts = $ts - $offset * 86400;
            for ($i = 0; $i < 7; $i++, $ts += 86400) {
                $a[] = date("Y-m-d l", $ts);
            }
            foreach ($a as $v) {
                $date = explode(" ", $v);
                $b[] = array('date' => $date[0], 'day' => $date[1]);
            }
            $merchant_segment = $this->getMerchantSegmentDetails($driver->merchant_id);
            $trips_data = array();
            foreach ($b as $key => $value) {
                $weekly_day_timestamp = strtotime($value['date']);
                $weekly_day_rides = 0;
                $weekly_day_earning = $currency . ' 0.0';
                $weekly_day_rating = 0.0;
                $weekly_day_trips = [];
                $date = $value['date'];
                $bookingObj = Booking::select('id', 'merchant_booking_id', 'segment_id', 'country_area_id', 'company_cut', 'driver_cut', 'created_at')
                    ->where([['driver_id','=', $driver->id], ['booking_closure' ,'=',  1], ['booking_status' ,'=',  1005]])
                    ->whereDate('created_at', '=', $date)
                    ->whereHas('Segment', function ($q) use ($request) {
                        $q->where('slag', $request->segment_slug);
                    })
                    ->orderBy('created_at','DESC')
                    ->get();
                if ($bookingObj->count() > 0) {
                    $data = $bookingObj->toArray();
                    $weekly_day_trips = $bookingObj->map(function ($item, $key) use ($merchant_segment, $currency) {
                        $time_of_booking = convertTimeToUSERzone($item->created_at,$item->CountryArea->timezone,$item->merchant_id,null,1,1);
                        $segment = $merchant_segment->where('segment_id', $item->segment_id)->first();
                        return array(
                            'order_no' => $item->merchant_booking_id,
                            'order_id' => $item->id,
                            'time_of_booking' => strtotime($time_of_booking), //strtotime($item->created_at),
                            'order_name' => $segment['name'],
                            'segment_image' => $segment['segment_icon'],
                            'segment_slug' => $segment['slag'],
                            'sub_group_for_app' => $segment['sub_group_for_app'], // 1 for food, 2 grocery, 3 taxi 4 delivery else handyman
                            'amount' => $currency . ' ' . $item->driver_cut,
                        );
                    });
                    $weekly_day_rides = count($data);
                    $weekly_day_earning = $currency . ' ' . array_sum(array_column($data, 'driver_cut'));
                }
                $weekly_data['timestamp'] = $weekly_day_timestamp;
                $weekly_data['completed_rides'] = $weekly_day_rides;
                $weekly_data['day_earning'] = $weekly_day_earning;
                $weekly_data['day_rating'] = $weekly_day_rating;
                $weekly_data['trips'] = $weekly_day_trips;
                if (count($weekly_day_trips) > 0) {
                    $trips_data[] = $weekly_data;
                }
            }
            $trips_details['total_trips_in_week'] = $trips;
            $trips_details['overall_rating_in_week'] = 0.0;
            $trips_details['trips_data'] = $trips_data;
            $newArray['total_billed_to_consumer'] = $currency . ' ' . $fare_amount_sum;
            $newArray['trips_details'] = $trips_details;
            if (empty($trips_data)) {
                throw new \Exception(trans("$string_file.data_not_found"));
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $newArray;
    }

    public function DriverOrderAccountEarnings(Request $request)
    {
        try {
            $date = date_create($request->date . '00:00:00');
            $date = \Carbon\Carbon::parse(date_format($date, 'Y-m-d H:i:s'));
            $date->setISODate(date_format($date, 'Y'), date_format($date, 'W'));
            $start_date = $date->startOfWeek()->format('Y-m-d H:i:s');
            $end_date = $date->endOfWeek()->format('Y-m-d H:i:s');

            $driver = $request->user('api-driver');
            $currency = $driver->CountryArea->Country->isoCode;

            $bill_timeperiod = array('from' => $start_date, 'to' => $end_date);

            $data = $this->OrderGenerateBill($driver, $request, $bill_timeperiod);

            $string_file = $this->getStringFile($driver->merchant_id);

            $holder_data = !empty($data['holder']) ? $data['holder'] : [];
            $printable = !empty($data['printable']) ? $data['printable'] : [];
            $net_earnings = isset($printable['net_earnings']) ? round($printable['net_earnings'], 2) : 0;
            $trips = isset($printable['trips']) ? $printable['trips'] : 0;
            $fare_amount_sum = isset($printable['fare_amount_sum']) ? $printable['fare_amount_sum'] : 0;
            $cash_payout_sum = isset($printable['cash_payment_sum']) ? $printable['cash_payment_sum'] : 0;
            $online_payment_sum = isset($printable['online_payment_sum']) ? $printable['online_payment_sum'] : 0;

            $holder = [];
            if (!empty($holder_data)):
                foreach ($holder_data as $key => $value):
                    $type = '';
                    if ($key == 'tax' || $key == 'company_commission_sum') {
                        $type = ' - ';
                    } else if ($key == 'fare_amount_sum' || $key == 'cash_payment_sum') {
                        $type = '';
                    } else {
                        $type = ' + ';
                    }
                    $holder[] = array(
                        "name" => $this->getBillDetailKey($string_file,$key), //trans('api.' . $key),
                        "value" => $type . $currency . ' ' . round_number($value),
                        "bold" => false,
                    );
                endforeach;
            endif;
            // push total earning array to holder
            $total_earning =  array(
                "name" => trans("$string_file.total_earning"),
                "value" => $currency . ' ' . $net_earnings,
                "bold" => true,
            );
            array_push($holder, $total_earning);
            $newArray['total_earnings'] = $currency . ' ' . $net_earnings;
            $newArray['received_cash'] = $currency . ' ' . $cash_payout_sum;
            $newArray['received_in_wallet'] = $currency . ' ' . $online_payment_sum;
            $newArray['from_timestamp'] = strtotime($bill_timeperiod['from']);
            $newArray['to_timestamp'] = strtotime($bill_timeperiod['to']);
            $newArray['wallet_balance'] = $currency . ' ' . $driver->wallet_money;
            $newArray['holder_data'] = $holder;

            $date = $request->date;
            $ts = strtotime($date);
            $dow = date('w', $ts);
            $offset = $dow - 1;
            if ($offset < 0) {
                $offset = 6;
            }
            $ts = $ts - $offset * 86400;
            for ($i = 0; $i < 7; $i++, $ts += 86400) {
                $a[] = date("Y-m-d l", $ts);
            }
            foreach ($a as $v) {
                $date = explode(" ", $v);
                $b[] = array('date' => $date[0], 'day' => $date[1]);
            }
            $merchant_segment = $this->getMerchantSegmentDetails($driver->merchant_id);
            $trips_data = array();
            foreach ($b as $key => $value) {
                $weekly_day_timestamp = strtotime($value['date']);
                $weekly_day_rides = 0;
                $weekly_day_earning = $currency . ' 0.0';
                $weekly_day_rating = 0.0;
                $weekly_day_trips = [];
                $date = $value['date'];
                $orderObj = Order::select('id', 'merchant_order_id', 'segment_id', 'created_at')
                    ->where([['driver_id' ,'=',  $driver->id], ['order_status' ,'=',  11]])
                    ->whereDate('created_at', '=', $date)
                    ->with(['OrderTransaction'])
                    ->whereHas('Segment', function ($q) use ($request) {
                        $q->where('slag', $request->segment_slug);
                    })
                    ->get();
//                $bookingObj = Booking::select('id', 'merchant_booking_id', 'segment_id', 'company_cut', 'driver_cut', 'created_at')
//                    ->where(['driver_id' => $driver->id, 'booking_closure' => 1])
//                    ->whereDate('created_at', '=', $date)
//                    ->get();
                if ($orderObj->count() > 0) {
                    $data = $orderObj->toArray();
                    $weekly_day_trips = $orderObj->map(function ($item, $key) use ($merchant_segment, $currency) {
                        $segment = $merchant_segment->where('segment_id', $item->segment_id)->first();
                        return array(
                            'order_no' => $item->merchant_order_id,
                            'order_id' => $item->id,
                            'time_of_booking' => strtotime($item->created_at),
                            'order_name' => $segment['name'],
                            'segment_image' => $segment['segment_icon'],
                            'segment_slug' => $segment['slag'],
                            'sub_group_for_app' => $segment['sub_group_for_app'], // 1 for food, 2 grocery, 3 taxi 4 delivery else handyman
                            'amount' => $currency . ' ' . $item->OrderTransaction->driver_earning,
                        );
                    });
                    $weekly_day_rides = count($data);
                    $weekly_day_earning = 0;
                    foreach ($orderObj as $key => $value) {
                        $weekly_day_earning += $value->OrderTransaction->driver_earning;
                    }
//                    $weekly_day_earning = $currency . ' ' . array_sum(array_column($data, 'driver_cut'));
                }
                $weekly_data['timestamp'] = $weekly_day_timestamp;
                $weekly_data['completed_rides'] = $weekly_day_rides;
                $weekly_data['day_earning'] = $weekly_day_earning;
                $weekly_data['day_rating'] = $weekly_day_rating;
                $weekly_data['trips'] = $weekly_day_trips;
                if (count($weekly_day_trips) > 0) {
                    $trips_data[] = $weekly_data;
                }
            }
            $trips_details['total_trips_in_week'] = $trips;
            $trips_details['overall_rating_in_week'] = 0.0;
            $trips_details['trips_data'] = $trips_data;
            $newArray['total_billed_to_consumer'] = $currency . ' ' . $fare_amount_sum;
            $newArray['trips_details'] = $trips_details;
            if (empty($trips_data)) {
                throw new \Exception(trans("$string_file.data_not_found"));
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $newArray;
    }

    public function OrderGenerateBill(Driver $driver_obj, $request, $timePeriod)
    {
        try {
            /*return [
                'fare_amount_sum' => 90.00,
                'company_commission_sum' => 90.00,
                'toll_amount_sum' => 90.00,
                'tip_amount_sum' => 90.00,
                'cancellation_charges_sum' => 90.00,
                'referral_amount_sum' => 0.00,
                'net_earnings' => 90.00,
                'cash_payment_sum' => 90.00,
                'amount' => 90.00,
                'trips' => 5,
            ];*/

            $orders = Order::
            // where([['order_status', '=', 11]])->whereBetween('updated_at', [$timePeriod['from'], $timePeriod['to']])->where([['driver_id', '=', $driver_obj->id]])
            where([['order_status', '=', 11], ['payment_status', '=', '1'], ['is_order_completed', '=', 1]])->whereBetween('updated_at', [$timePeriod['from'], $timePeriod['to']])->where([['driver_id', '=', $driver_obj->id]])
                ->with(['OrderTransaction'])
                ->whereHas('Segment', function ($q) use ($request) {
                    $q->where('slag', $request->segment_slug);
                })
                ->get();
//        $bookings = Booking::where([['booking_status', '=', '1006']])->whereBetween('updated_at', [$timePeriod['from'], $timePeriod['to']])->where([['driver_id', '=', $driver_obj->id]])
//            ->orWhere([['booking_closure', '=', '1']])->whereBetween('updated_at', [$timePeriod['from'], $timePeriod['to']])->where([['driver_id', '=', $driver_obj->id]])
//            ->with(['BookingTransaction'])
//            ->whereHas('Segment',function($q) use($request){
//                $q->where('slag',$request->segment_slug);
//            })
//            ->get();
            if ($orders->isEmpty()):
                return [];
            endif;
            $transaction_collection = $orders->map(function ($item, $key) {
                return $item['OrderTransaction'];
            })->filter()->values();
            if ($transaction_collection->isEmpty()):
                return [];
            endif;
            $merchant = new Merchant();
            //$driver_config = $driver_obj->Merchant->DriverConfiguration;
            $trips = $orders->filter(function ($item, $key) {
                return $item['order_status'] != 11 && !empty($item['OrderTransaction']);
            })->count();

            $data_with_transactions = $orders->filter(function ($item, $key) {
                return !empty($item['OrderTransaction']);
            });

            $rides_data = array();
            foreach ($data_with_transactions as $value) {
                $rides_data[] = array('rides_color' => 'bbbbbb', 'ride_name' => 'CRN # ' . $value['id'], 'ride_id' => $value['id'], 'ride_earning' => $driver_obj->CountryArea->Country->isoCode . " " . sprintf("%0.2f", $value['OrderTransaction']['driver_total_payout_amount']));
            }
            $referral_controller = new ReferralController();
            $driver_refer_amount = $referral_controller->getDriverReferEarning($driver_obj->merchant_id, $driver_obj->id, $timePeriod['from'], $timePeriod['to']);

            $fare_amount_sum = $transaction_collection->sum('sub_total_before_discount');
            $company_commission_sum = $transaction_collection->sum('company_earning');
            $toll_amount_sum = $transaction_collection->sum('toll_amount');
            $tip_amount_sum = $transaction_collection->sum('tip');
            $cancellation_charges_sum = $transaction_collection->sum('cancellation_charge_applied');
//        $referral_amount_sum = $merchant->TripCalculation($transaction_collection->sum('referral_amount'), $driver_obj->merchant_id);
            $referral_amount_sum = $driver_refer_amount;
            $cash_payment_sum = $transaction_collection->sum('cash_payment');
            $online_payment_sum = $transaction_collection->sum('online_payment');
            $amount_deducted_from_driver_wallet = $transaction_collection->sum('amount_deducted_from_driver_wallet');
            $tax_amount = $transaction_collection->sum('tax_amount');
            $trips_outstanding_sum = $transaction_collection->sum('trip_outstanding_amount');
            $discount_amount = $transaction_collection->sum('discount_amount');
            $round_off_amount = round($transaction_collection->sum('rounded_amount'), 2);
            $old_outstanding_received = $transaction_collection->sum('cancellation_charge_received');

            $driver_earning = $transaction_collection->sum('driver_earning');

            $total_trips_till_now = $driver_obj->total_trips;
            // $amount = $fare_amount_sum + $toll_amount_sum + $tip_amount_sum + $cancellation_charges_sum + $referral_amount_sum - $company_commission_sum - $cash_payment_sum + $amount_deducted_from_driver_wallet;

            $driver_total_payout_amount = $merchant->TripCalculation($transaction_collection->sum('driver_total_payout_amount'), $driver_obj->merchant_id);


            $amount = $driver_total_payout_amount - $cash_payment_sum + $amount_deducted_from_driver_wallet;
            $return_data =  [
                'printable' => array(
                    'net_earnings' => $driver_earning,
                    'trips' => $trips,
                    'cash_payment_sum' => $cash_payment_sum,
                    'online_payment_sum' => $online_payment_sum + $discount_amount + $cancellation_charges_sum,
                    'amount' => $amount,
                    'rides_data' => $rides_data,
                    'fare_amount_sum' => $fare_amount_sum + $tax_amount - $discount_amount,
                ),
                'holder' => array(
                    'delivery_charges' => $driver_earning,
                    // 'fare_amount_sum' => $fare_amount_sum + $tax_amount - $discount_amount,
                    // 'company_commission_sum' => $company_commission_sum,
//                     'tax' => $tax_amount,
                    // 'toll_amount_sum' => $toll_amount_sum,
                    // 'tip_amount_sum' => $tip_amount_sum,
                    // 'cancellation_charges_sum' => $cancellation_charges_sum,
                    // 'referral_amount_sum' => $referral_amount_sum,
                    // 'round_off_amount' => $round_off_amount,
                    // 'old_outstanding_received' => $old_outstanding_received,

                    // 'net_earnings' => $amount+$cash_payment_sum,
                    //'net_earnings' => $amount+$cash_payment_sum-$tax_amount,
                ),
            ];
            // p($return_data);
            return $return_data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function DriverHandymanAccountEarnings($request){
        try {
            $date = date_create($request->date . '00:00:00');
            $date = \Carbon\Carbon::parse(date_format($date, 'Y-m-d H:i:s'));
            $date->setISODate(date_format($date, 'Y'), date_format($date, 'W'));
            $start_date = $date->startOfWeek()->format('Y-m-d H:i:s');
            $end_date = $date->endOfWeek()->format('Y-m-d H:i:s');

            $driver = $request->user('api-driver');
            $currency = $driver->CountryArea->Country->isoCode;

            $bill_timeperiod = array('from' => $start_date, 'to' => $end_date);

            $data = $this->HandymanOrderGenerateBill($driver, $request, $bill_timeperiod);

            $string_file = $this->getStringFile($driver->merchant_id);
            $order_string = trans("$string_file.order");

            $holder_data = !empty($data['holder']) ? $data['holder'] : [];
            $printable = !empty($data['printable']) ? $data['printable'] : [];
            $net_earnings = isset($printable['net_earnings']) ? round($printable['net_earnings'], 2) : 0;
            $trips = isset($printable['trips']) ? $printable['trips'] : 0;
            $fare_amount_sum = isset($printable['fare_amount_sum']) ? $printable['fare_amount_sum'] : 0;
            $cash_payout_sum = isset($printable['cash_payment_sum']) ? $printable['cash_payment_sum'] : 0;
            $online_payment_sum = isset($printable['online_payment_sum']) ? $printable['online_payment_sum'] : 0;

            $holder = [];
            if (!empty($holder_data)):
                foreach ($holder_data as $key => $value):
                    $type = '';
                    if ($key == 'tax' || $key == 'company_commission_sum' || $key == "total_company_fees") {
                        $type = ' - ';
                    } else if ($key == 'fare_amount_sum' || $key == 'cash_payment_sum') {
                        $type = '';
                    } else {
                        $type = ' + ';
                    }
                    $holder[] = array(
                        "name" => $this->getBillDetailKey($string_file,$key), // trans('api.' . $key),
                        "value" => $type . $currency . ' ' . round_number($value),
                        "bold" => false,
                    );
                endforeach;
            endif;
            // push total earning array to holder
            $total_earning =  array(
                "name" => trans("$string_file.total_earning"),
                "value" => $currency . ' ' . $net_earnings,
                "bold" => true,
            );
            array_push($holder, $total_earning);
            $newArray['total_earnings'] = $currency . ' ' . $net_earnings;
            $newArray['received_cash'] = $currency . ' ' . $cash_payout_sum;
            $newArray['received_in_wallet'] = $currency . ' ' . $online_payment_sum;
            $newArray['from_timestamp'] = strtotime($bill_timeperiod['from']);
            $newArray['to_timestamp'] = strtotime($bill_timeperiod['to']);
            $newArray['wallet_balance'] = $currency . ' ' . $driver->wallet_money;
            $newArray['holder_data'] = $holder;

            $date = $request->date;
            $ts = strtotime($date);
            $dow = date('w', $ts);
            $offset = $dow - 1;
            if ($offset < 0) {
                $offset = 6;
            }
            $ts = $ts - $offset * 86400;
            for ($i = 0; $i < 7; $i++, $ts += 86400) {
                $a[] = date("Y-m-d l", $ts);
            }
            foreach ($a as $v) {
                $date = explode(" ", $v);
                $b[] = array('date' => $date[0], 'day' => $date[1]);
            }
            $merchant_segment = $this->getMerchantSegmentDetails($driver->merchant_id);
            $trips_data = array();
            foreach ($b as $key => $value) {
                $weekly_day_timestamp = strtotime($value['date']);
                $weekly_day_rides = 0;
                $weekly_day_earning = $currency . ' 0.0';
                $weekly_day_rating = 0.0;
                $weekly_day_trips = [];
                $date = $value['date'];
                $orderObj = HandymanOrder::select('id', 'merchant_order_id', 'segment_id', 'created_at')
                    ->where([['driver_id' ,'=',  $driver->id],[ 'order_status' ,'=',  7]])
                    ->whereDate('created_at', '=', $date)
                    ->with('HandymanOrderTransaction')
                    ->whereHas('Segment', function ($q) use ($request) {
                        $q->where('slag', $request->segment_slug);
                    })
                    ->get();
//                $bookingObj = Booking::select('id', 'merchant_booking_id', 'segment_id', 'company_cut', 'driver_cut', 'created_at')
//                    ->where(['driver_id' => $driver->id, 'booking_closure' => 1])
//                    ->whereDate('created_at', '=', $date)
//                    ->get();
                if ($orderObj->count() > 0) {
                    $data = $orderObj->toArray();
                    $weekly_day_trips = $orderObj->map(function ($item, $key) use ($merchant_segment, $currency) {
                        $segment = $merchant_segment->where('segment_id', $item->segment_id)->first();
                        return array(
                            'order_no' => $item->merchant_order_id,
                            'order_id' => $item->id,
                            'time_of_booking' => strtotime($item->created_at),
                            'order_name' => $segment['name'],
                            'segment_image' => $segment['segment_icon'],
                            'segment_slug' => $segment['slag'],
                            'sub_group_for_app' => $segment['sub_group_for_app'], // 1 for food, 2 grocery, 3 taxi, 4 delivery ,5 handyman
                            'amount' => $currency . ' ' . $item->HandymanOrderTransaction->driver_earning,
                        );
                    });
                    $weekly_day_rides = count($data);
                    $weekly_day_earning = 0;
                    foreach ($orderObj as $key => $value) {
                        $weekly_day_earning += $value->HandymanOrderTransaction->driver_earning;
                    }
//                    $weekly_day_earning = $currency . ' ' . array_sum(array_column($data, 'driver_cut'));
                }
                $weekly_data['timestamp'] = $weekly_day_timestamp;
                $weekly_data['completed_rides'] = $weekly_day_rides;
                $weekly_data['day_earning'] = $weekly_day_earning;
                $weekly_data['day_rating'] = $weekly_day_rating;
                $weekly_data['trips'] = $weekly_day_trips;
                if (count($weekly_day_trips) > 0) {
                    $trips_data[] = $weekly_data;
                }
            }
            $trips_details['total_trips_in_week'] = $trips;
            $trips_details['overall_rating_in_week'] = 0.0;
            $trips_details['trips_data'] = $trips_data;
            $newArray['total_billed_to_consumer'] = $currency . ' ' . $fare_amount_sum;
            $newArray['trips_details'] = $trips_details;
            if (empty($trips_data)) {
                throw new \Exception(trans("$string_file.data_not_found"));
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $newArray;
    }



    public function HandymanOrderGenerateBill(Driver $driver_obj, $request, $timePeriod)
    {
        try {
            /*return [
                'fare_amount_sum' => 90.00,
                'company_commission_sum' => 90.00,
                'toll_amount_sum' => 90.00,
                'tip_amount_sum' => 90.00,
                'cancellation_charges_sum' => 90.00,
                'referral_amount_sum' => 0.00,
                'net_earnings' => 90.00,
                'cash_payment_sum' => 90.00,
                'amount' => 90.00,
                'trips' => 5,
            ];*/
            $orders = HandymanOrder::
            // where([['order_status', '=', 11]])->whereBetween('updated_at', [$timePeriod['from'], $timePeriod['to']])->where([['driver_id', '=', $driver_obj->id]])
            where([['order_status', '=', 7], ['payment_status', '=', '1'], ['is_order_completed', '=', 1]])->whereBetween('updated_at', [$timePeriod['from'], $timePeriod['to']])->where([['driver_id', '=', $driver_obj->id]])
                ->with(['HandymanOrderTransaction'])
                ->whereHas('Segment', function ($q) use ($request) {
                    $q->where('slag', $request->segment_slug);
                })
                ->get();
            if ($orders->isEmpty()):
                return [];
            endif;
            $transaction_collection = $orders->map(function ($item, $key) {
                return $item['HandymanOrderTransaction'];
            })->filter()->values();
            if ($transaction_collection->isEmpty()):
                return [];
            endif;
            $merchant = new Merchant();
            //$driver_config = $driver_obj->Merchant->DriverConfiguration;
            $trips = $orders->filter(function ($item, $key) {
                return $item['booking_status'] != 1006 && !empty($item['HandymanOrderTransaction']);
            })->count();

            $data_with_transactions = $orders->filter(function ($item, $key) {
                return !empty($item['HandymanOrderTransaction']);
            });

            $rides_data = array();
            foreach ($data_with_transactions as $value) {
                $rides_data[] = array('rides_color' => 'bbbbbb', 'ride_name' => 'CRN # ' . $value['id'], 'ride_id' => $value['id'], 'ride_earning' => $driver_obj->CountryArea->Country->isoCode . " " . sprintf("%0.2f", $value['HandymanOrderTransaction']['driver_total_payout_amount']));
            }
            $referral_controller = new ReferralController();
            $driver_refer_amount = $referral_controller->getDriverReferEarning($driver_obj->merchant_id, $driver_obj->id, $timePeriod['from'], $timePeriod['to']);

            $fare_amount_sum = $transaction_collection->sum('sub_total_before_discount');
            $company_commission_sum = $transaction_collection->sum('company_earning');
            $toll_amount_sum = $transaction_collection->sum('toll_amount');
            $tip_amount_sum = $transaction_collection->sum('tip');
            $cancellation_charges_sum = $transaction_collection->sum('cancellation_charge_applied');
//        $referral_amount_sum = $merchant->TripCalculation($transaction_collection->sum('referral_amount'), $driver_obj->merchant_id);
            $referral_amount_sum = $driver_refer_amount;
            $cash_payment_sum = $transaction_collection->sum('cash_payment');
            $online_payment_sum = $transaction_collection->sum('online_payment');
            $amount_deducted_from_driver_wallet = $transaction_collection->sum('amount_deducted_from_driver_wallet');
            $tax_amount = $transaction_collection->sum('tax_amount');
            $trips_outstanding_sum = $transaction_collection->sum('trip_outstanding_amount');
            $discount_amount = $transaction_collection->sum('discount_amount');
            $round_off_amount = round($transaction_collection->sum('rounded_amount'), 2);
            $old_outstanding_received = $transaction_collection->sum('cancellation_charge_received');

            $total_trips_till_now = $driver_obj->total_trips;
            // $amount = $fare_amount_sum + $toll_amount_sum + $tip_amount_sum + $cancellation_charges_sum + $referral_amount_sum - $company_commission_sum - $cash_payment_sum + $amount_deducted_from_driver_wallet;

            $driver_total_payout_amount = $merchant->TripCalculation($transaction_collection->sum('driver_total_payout_amount'), $driver_obj->merchant_id);


            $amount = $driver_total_payout_amount - $cash_payment_sum + $amount_deducted_from_driver_wallet;
            return [
                'printable' => array(
                    'net_earnings' => $fare_amount_sum + $round_off_amount - $company_commission_sum + $cancellation_charges_sum,
                    'trips' => $trips,
                    'cash_payment_sum' => $cash_payment_sum,
                    'online_payment_sum' => $online_payment_sum + $discount_amount + $cancellation_charges_sum,
                    'amount' => $amount,
                    'rides_data' => $rides_data,
                    'fare_amount_sum' => $fare_amount_sum + $tax_amount - $discount_amount,
                ),
                'holder' => array(
                    'total_service_charges' => $fare_amount_sum + $tax_amount - $discount_amount,
                    'total_company_fees' => $company_commission_sum - $tax_amount, // tax is already included in company earning
                    'tax' => $tax_amount,
//                    'toll_amount_sum' => $toll_amount_sum,
//                    'tip_amount_sum' => $tip_amount_sum,
//                    'cancellation_charges_sum' => $cancellation_charges_sum,
                    'referral_amount_sum' => $referral_amount_sum,
                    'round_off_amount' => $round_off_amount,
                    'old_outstanding_received' => $old_outstanding_received,
                    // 'net_earnings' => $amount+$cash_payment_sum,
                    //'net_earnings' => $amount+$cash_payment_sum-$tax_amount,
                ),
            ];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getBillDetailKey($string_file, $key){
        $name = $key;
        if($key == 'fare_amount_sum'){
            $name = trans("$string_file.fare_of_rides");
        }
        elseif($key == 'company_commission_sum'){
            $name = trans("$string_file.company_commission_of_rides");
        }
        elseif($key == 'tax'){
            $name = trans("$string_file.tax");
        }
        elseif($key == 'toll_amount_sum'){
            $name = trans("$string_file.toll_amount");
        }
        elseif($key == 'tip_amount_sum'){
            $name = trans("$string_file.tip");
        }
        elseif($key == 'cancellation_charges_sum'){
            $name = trans("$string_file.cancellation_charges");
        }
        elseif($key == 'referral_amount_sum'){
            $name = trans("$string_file.referral_amount");
        }
        elseif($key == 'round_off_amount'){
            $name = trans("$string_file.round_off");
        }
        elseif($key == 'old_outstanding_received'){
            $name = trans("$string_file.user_outstanding_received");
        }
        elseif($key == 'total_service_charges'){
            $name = trans("$string_file.total_service_charges");
        }
        elseif($key == 'total_company_fees'){
            $name = trans("$string_file.total_company_fees");
        }
        elseif($key == 'delivery_charges'){
            $name = trans("$string_file.delivery_charge");
        }
        return $name;
    }
}