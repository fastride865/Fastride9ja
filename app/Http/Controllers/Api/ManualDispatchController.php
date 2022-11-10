<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\PolygenController;
use App\Http\Controllers\Helper\PriceController;
use App\Http\Controllers\Helper\SmsController;
use App\Models\Booking;
use App\Models\BookingCheckout;
use App\Models\BookingConfiguration;
use App\Models\BookingDetail;
use App\Models\Configuration;
use App\Models\CountryArea;
use App\Models\DriverVehicle;
use App\Models\Onesignal;
use App\Models\Outstanding;
use App\Models\PriceCard;
use App\Models\SmsConfiguration;
use App\Models\User;
use App\Models\UserDevice;
use App\Traits\ApiResponseTrait;
use App\Traits\BookingTrait;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use DB;
use App\Http\Controllers\Helper\Merchant;


class ManualDispatchController extends Controller
{
    use ApiResponseTrait,BookingTrait,MerchantTrait;

    public function confirmBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkout_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $driver = $request->user('api-driver');
            $string_file = $this->getStringFile(NULL,$driver->Merchant);
            if ($driver->free_busy == 1) {
                return $this->failedResponse(trans("$string_file.user_running_ride"));
            }
            $merchant_id = $driver->merchant_id;

            $driver_Vehicle = DriverVehicle::whereHas('Drivers', function ($q) use ($driver) {
                $q->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 1]]);
            })->with(['Drivers' => function ($q) use ($driver) {
                $q->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 1]]);
            }])->first();

            $checkOut = BookingCheckout::find($request->checkout_id);
            $Bookingdata = $checkOut->toArray();
            unset($Bookingdata['id']);
            unset($Bookingdata['user']);
            unset($Bookingdata['created_at']);
            unset($Bookingdata['updated_at']);
            // unset($Bookingdata['bill_details']);
            $Bookingdata['booking_timestamp'] = time();
            $Bookingdata['booking_status'] = 1004;
            $Bookingdata['driver_id'] = $driver->id;
            $Bookingdata['driver_vehicle_id'] = $driver_Vehicle->id;
            $Bookingdata['segment_id'] = 1;

            $booking = Booking::create($Bookingdata);
            BookingDetail::create([
                'booking_id' => $booking->id,
                //'bill_details' => $bill_details,
                'accept_timestamp' => strtotime('now'),
                'accept_latitude' => $booking->pickup_latitude,
                'accept_longitude' => $booking->pickup_longitude,
                'accuracy_at_accept' => $booking->accuracy,
                'arrive_timestamp' => strtotime('now'),
                'arrive_latitude' => $booking->latitude,
                'arrive_longitude' => $booking->longitude,
                'accuracy_at_arrive' => $booking->accuracy,
                'start_timestamp' => strtotime('now'),
                'start_latitude' => $booking->pickup_latitude,
                'start_longitude' => $booking->pickup_longitude,
                'accuracy_at_start' => $booking->accuracy,
                'start_location' => $booking->pickup_location,
                'wait_time' => 0,
            ]);

            $driver->free_busy = 1;
            $driver->save();

            $bookingData = new BookingDataController();
            $bookingObj = $bookingData->driverBookingDetails($booking, false,$request);
            if (isset($booking->user_id)) {
                setLocal($booking->User->language);
                $data = $bookingData->BookingNotification($booking);
                //send notification to user
                $message = trans("$string_file.driver_ride_started");
//                $message = $bookingData->LanguageData($booking->merchant_id, 32);
                $arr_param = array(
                    'user_id' => $booking->User->id,
                    'data'=>$data,
                    'message'=>$message,
                    'merchant_id'=>$booking->merchant_id,
                    'title' => trans('api.message14')
                );
                Onesignal::UserPushMessage($arr_param);
                setLocal();
                // $booking = $bookingData->DriverBookingDetails($booking->id, $merchant_id);
                $SmsConfiguration = SmsConfiguration::select('ride_start_enable', 'ride_start_msg')->where([['merchant_id', '=', $merchant_id]])->first();
                if ($SmsConfiguration && $SmsConfiguration->ride_start_enable == 1) {
                    $sms = new SmsController();
                    $phone = $booking->User->UserPhone;
                    $sms->SendSms($merchant_id, $phone, null, 'RIDE_START',$booking->User->email);
                }
            }
            $generalConfiguration = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
            $bookingObj['highlights']['manual_toll_enable'] = false;
            if (isset($generalConfiguration->toll_api) && $generalConfiguration->toll_api == 2) {
                $bookingObj['highlights']['manual_toll_enable'] = true;
                $bookingObj['highlights']['manual_toll_price'] = $driver->CountryArea->manual_toll_price;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans('api.message14'), $bookingObj);
    }

    public function checkoutBooking(Request $request)
    {
        // we are changing some flow here means estimate and drop location will be checked in checkout
        $validator = Validator::make($request->all(), [
            'pickup_latitude' => 'required',
            'pickup_longitude' => 'required',
            'pickup_location' => 'required',
            'drop_location' => 'required',
            'country_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'accuracy' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $driver = $request->user('api-driver');
        $county_area = CountryArea::where('id',$driver->country_area_id)->first();
        $drop = json_decode($request->drop_location,true);
        $merchant_id = $driver->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $drop_latitude = $drop[0]['drop_latitude'];
        $drop_longitude = $drop[0]['drop_longitude'];
        $ploygon = new PolygenController();
        $checkArea = $ploygon->CheckArea($drop_latitude, $drop_longitude, $county_area->AreaCoordinates);
        if (!$checkArea) {
                return $this->failedResponse(trans("$string_file.drop_location_out"));
        }
        DB::beginTransaction();
        try {


//            $county_area = CountryArea::find($driver->country_area_id);
//            if (!empty($request->phone)) {
            $user = $this->User($request, $merchant_id);
//            } else {
//                $user = $this->ManualUser($merchant_id);
//            }
            $customer_details = array(
                'name' => $user->first_name.' '.$user->last_name,
                'phone' => $user->UserPhone,
                'email' => $user->emaill
            );
            $driver_Vehicle = DriverVehicle::whereHas('Drivers', function ($q) use ($driver) {
                $q->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 1]]);
            })->with(['Drivers' => function ($q) use ($driver) {
                $q->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 1]]);
            }])->first();

            if (empty($driver_Vehicle)) {

                return $this->failedResponse(trans("$string_file.inactive_vehicle_error"));
            }
            $drop_locationArray = json_decode($request->drop_location, true);
            // dd($county_area->id,$merchant_id,$driver_Vehicle->vehicle_type_id);
            $pricecards = PriceCard::where([['country_area_id', '=', $county_area->id], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', 1], ['vehicle_type_id', '=', $driver_Vehicle->vehicle_type_id]])->first();
            if(empty($pricecards)){
                return $this->failedResponse(trans("$string_file.services_price_card"));
            }
            
            $configuration = BookingConfiguration::select('google_key','otp_manual_dispatch')->where([['merchant_id', '=', $merchant_id]])->first();
            $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $configuration->google_key,"",$string_file);

            // Generate bill details
            $estimatePrice = new PriceController();
            $outstanding_amount = 0.00;
            $newBookingData = new BookingDataController();
            $from = $request->pickup_latitude . "," . $request->pickup_longitude;
            $to = "";
            if (!empty($drop_locationArray)) {
                $lastLocation = $newBookingData->wayPoints($drop_locationArray);
                $to = $lastLocation['last_location']['drop_latitude'] . "," . $lastLocation['last_location']['drop_longitude'];
            }
            $fare = $estimatePrice->BillAmount([
                'price_card_id' => $pricecards->id,
                'merchant_id' => $merchant_id,
                'distance' => $googleArray['total_distance'],
                'time' => $googleArray['total_time_minutes'],
                'booking_id' => 0,
                'user_id' => 0,
                'booking_time' => date('H:i'),
                'outstanding_amount' => $outstanding_amount,
                'units' => $county_area->Country['distance_unit'],
                'from' => $from,
                'to' => $to,
            ]);
            $bill_details = json_encode($fare['bill_details'], true);

            $merchant = new Merchant();
            $estimate_bill = $merchant->FinalAmountCal($fare['amount'], $driver->merchant_id);

            $booking = new BookingCheckout();

//            $estimate_bill = $pricecards->CountryArea->Country->isoCode . sprintf("%0.2f", $fare['amount']);
//            $estimate_bill = "";
//            if (isset($request->estimate_bill)) {
//                $estimate_bill = str_replace($county_area->Country->isoCode, '', $request->estimate_bill);
//            }
            $booking->merchant_id = $merchant_id;
            $booking->user_id = $user->id;
            //$booking->driver_id = $driver->id;
            $booking->country_area_id = $driver->country_area_id;
            $booking->service_type_id = 1;
            $booking->vehicle_type_id = $driver_Vehicle->vehicle_type_id;
            $booking->service_package_id = null;
            $booking->price_card_id = $pricecards->id;
            $booking->total_drop_location = 1;
            $booking->auto_upgradetion = 2;
            $booking->payment_method_id = 1;
            $booking->pickup_latitude = $request->pickup_latitude;
            $booking->pickup_longitude = $request->pickup_longitude;
            $booking->pickup_location = $request->pickup_location;
            $booking->drop_latitude = $drop_locationArray[0]['drop_latitude'];
            $booking->drop_longitude = $drop_locationArray[0]['drop_longitude'];
            $booking->drop_location = $drop_locationArray[0]['drop_location'];
            $booking->booking_type = 1;
            $booking->waypoints = null;
            $booking->map_image = $googleArray['image'];

//            $booking->estimate_distance = isset($request->estimate_distance) ? $request->estimate_distance : "";
//            $booking->estimate_time = isset($request->estimate_time) ? $request->estimate_time : "";
            $booking->estimate_distance = $googleArray['total_distance_text'];
            $booking->estimate_time = $googleArray['total_time_text'];
            $booking->estimate_bill = $estimate_bill;
            $booking->promo_code = null;
            $booking->estimate_driver_distnace = "";
            $booking->estimate_driver_time = "";
            $booking->bill_details = $bill_details;
            $booking->save();
            $otp_manual_dispatch = $configuration->otp_manual_dispatch;
            $config = Configuration::select('sms_gateway')->where([['merchant_id', '=', $merchant_id]])->first();
            $otp = "2020";
            if ($otp_manual_dispatch == 1 && $config->sms_gateway == 1) {
                $sms = new SmsController();
                $otp = mt_rand(1111, 9999);
                $sms->SendSms($merchant_id, $request->phone, $otp);
            }
            $bookingData = new BookingDataController();
            $result = $bookingData->CheckOut($booking);
            $result['otp'] = $otp;
            $result['customer_details'] = $customer_details;
            $result['estimate_distance'] = $booking->estimate_distance;
            $result['estimate_time'] = $booking->estimate_time;
            $estimates_arrive_header_text = trans("$string_file.distance_and_time");
            $result['estimates_arrive_header_text'] = $estimates_arrive_header_text;
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.ready_for_ride"),$result);
    }


    // checkout booking without drop location
    //make booking image optional in checkout and booking tables
//   public function checkoutBooking(Request $request)
//    {
//        // we are changing some flow here means estimate and drop location will be checked in checkout
//        $validator = Validator::make($request->all(), [
//            'pickup_latitude' => 'required',
//            'pickup_longitude' => 'required',
//            'pickup_location' => 'required',
//           // 'drop_location' => 'required',
//            'country_id' => 'required',
//            'first_name' => 'required',
//            'last_name' => 'required',
//            'phone' => 'required',
//            'accuracy' => 'required',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return $this->failedResponse($errors[0]);
//        }
//        $driver = $request->user('api-driver');
//        $county_area = CountryArea::where('id',$driver->country_area_id)->first();
//        $merchant_id = $driver->merchant_id;
//        $string_file = $this->getStringFile($merchant_id);
//        $drop_latitude = "";
//        $drop_longitude = "";
//        $drop_locationArray = [];
//        if(!empty($drop))
//        {
//            $drop = json_decode($request->drop_location,true);
//            $drop_locationArray = $drop;
//            $drop_latitude = $drop[0]['drop_latitude'];
//            $drop_longitude = $drop[0]['drop_longitude'];
//            $ploygon = new PolygenController();
//            $checkArea = $ploygon->CheckArea($drop_latitude, $drop_longitude, $county_area->AreaCoordinates);
//            if (!$checkArea) {
//                    return $this->failedResponse(trans("$string_file.drop_location_out"));
//            }
//        }
//        DB::beginTransaction();
//        try {
//
//
////            $county_area = CountryArea::find($driver->country_area_id);
////            if (!empty($request->phone)) {
//            $user = $this->User($request, $merchant_id);
////            } else {
////                $user = $this->ManualUser($merchant_id);
////            }
//            $customer_details = array(
//                'name' => $user->first_name.' '.$user->last_name,
//                'phone' => $user->UserPhone,
//                'email' => $user->emaill
//            );
//            $driver_Vehicle = DriverVehicle::whereHas('Drivers', function ($q) use ($driver) {
//                $q->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 1]]);
//            })->with(['Drivers' => function ($q) use ($driver) {
//                $q->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 1]]);
//            }])->first();
//
//            if (empty($driver_Vehicle)) {
//
//                return $this->failedResponse(trans("$string_file.inactive_vehicle_error"));
//            }
//          //  $drop_locationArray = json_decode($request->drop_location, true);
//            $pricecards = PriceCard::where([['country_area_id', '=', $county_area->id], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', 1], ['vehicle_type_id', '=', $driver_Vehicle->vehicle_type_id]])->first();
//            $configuration = BookingConfiguration::select('google_key','otp_manual_dispatch')->where([['merchant_id', '=', $merchant_id]])->first();
//            $googleArray = [];
//            if(count($drop_locationArray) > 0)
//            {
//                $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $configuration->google_key,"",$string_file);
//            }
//
//            // Generate bill details
//            $estimatePrice = new PriceController();
//            $outstanding_amount = 0.00;
//            $newBookingData = new BookingDataController();
//            $from = $request->pickup_latitude . "," . $request->pickup_longitude;
//            $to = "";
//            if (!empty($drop_locationArray)) {
//                $lastLocation = $newBookingData->wayPoints($drop_locationArray);
//                $to = $lastLocation['last_location']['drop_latitude'] . "," . $lastLocation['last_location']['drop_longitude'];
//            }
//            $fare = $estimatePrice->BillAmount([
//                'price_card_id' => $pricecards->id,
//                'merchant_id' => $merchant_id,
//                'distance' => isset($googleArray['total_distance']) ? $googleArray['total_distance'] : 0,
//                'time' => isset($googleArray['total_time_minutes']) ? $googleArray['total_time_minutes'] : 0,
//                'booking_id' => 0,
//                'user_id' => 0,
//                'booking_time' => date('H:i'),
//                'outstanding_amount' => $outstanding_amount,
//                'units' => $county_area->Country['distance_unit'],
//                'from' => $from,
//                'to' => $to,
//            ]);
//         // p($fare);
//            $bill_details = json_encode($fare['bill_details'], true);
//
//            $merchant = new Merchant();
//            $estimate_bill = $merchant->FinalAmountCal($fare['amount'], $driver->merchant_id);
//
//            $booking = new BookingCheckout();
//
////            $estimate_bill = $pricecards->CountryArea->Country->isoCode . sprintf("%0.2f", $fare['amount']);
////            $estimate_bill = "";
////            if (isset($request->estimate_bill)) {
////                $estimate_bill = str_replace($county_area->Country->isoCode, '', $request->estimate_bill);
////            }
//            $booking->merchant_id = $merchant_id;
//            $booking->user_id = $user->id;
//            //$booking->driver_id = $driver->id;
//            $booking->country_area_id = $driver->country_area_id;
//            $booking->service_type_id = 1;
//            $booking->vehicle_type_id = $driver_Vehicle->vehicle_type_id;
//            $booking->service_package_id = null;
//            $booking->price_card_id = $pricecards->id;
//            $booking->total_drop_location = 1;
//            $booking->auto_upgradetion = 2;
//            $booking->payment_method_id = 1;
//            $booking->pickup_latitude = $request->pickup_latitude;
//            $booking->pickup_longitude = $request->pickup_longitude;
//            $booking->pickup_location = $request->pickup_location;
//            $booking->drop_latitude = isset($drop_locationArray[0]['drop_latitude']) ? $drop_locationArray[0]['drop_latitude'] : "";
//            $booking->drop_longitude = isset($drop_locationArray[0]['drop_longitude']) ? $drop_locationArray[0]['drop_longitude']: "";
//            $booking->drop_location = isset($drop_locationArray[0]['drop_location']) ? $drop_locationArray[0]['drop_location'] : "";
//            $booking->booking_type = 1;
//            $booking->waypoints = null;
//            $booking->map_image = isset($googleArray['image']) ? $googleArray['image'] : NULL;
//
////            $booking->estimate_distance = isset($request->estimate_distance) ? $request->estimate_distance : "";
////            $booking->estimate_time = isset($request->estimate_time) ? $request->estimate_time : "";
//            $booking->estimate_distance = isset($googleArray['total_distance_text']) ? $googleArray['total_distance_text'] : 0;
//            $booking->estimate_time = isset($googleArray['total_time_text']) ? $googleArray['total_time_text'] : 0;
//            $booking->estimate_bill = $estimate_bill;
//            $booking->promo_code = null;
//            $booking->estimate_driver_distnace = "";
//            $booking->estimate_driver_time = "";
//            $booking->bill_details = $bill_details;
//            $booking->save();
//            $otp_manual_dispatch = $configuration->otp_manual_dispatch;
//            $config = Configuration::select('sms_gateway')->where([['merchant_id', '=', $merchant_id]])->first();
//            $otp = "2020";
//            if ($otp_manual_dispatch == 1 && $config->sms_gateway == 1) {
//                $sms = new SmsController();
//                $otp = mt_rand(1111, 9999);
//                $sms->SendSms($merchant_id, $request->phone, $otp);
//            }
//            $bookingData = new BookingDataController();
//            $result = $bookingData->CheckOut($booking);
//            $result['otp'] = $otp;
//            $result['customer_details'] = $customer_details;
//            $result['estimate_distance'] = $booking->estimate_distance;
//            $result['estimate_time'] = $booking->estimate_time;
//            $estimates_arrive_header_text = trans("$string_file.distance_and_time");
//            $result['estimates_arrive_header_text'] = $estimates_arrive_header_text;
//        } catch (\Exception $e) {
//            DB::rollback();
//            return $this->failedResponse($e->getMessage());
//        }
//        DB::commit();
//        return $this->successResponse(trans("$string_file.ready_for_ride"),$result);
//    }


    public function User($data, $merchant_id)
    {
        $user = User::where([['merchant_id', '=', $merchant_id], ['UserPhone', '=', $data->phone]])->first();
        if (!empty($user)) {
            return $user;
        }
        $first_name = ($data->first_name == null) ? "Manual" : $data->first_name;
        $last_name = ($data->last_name == null) ? "User" : $data->last_name;
        $email = ($data->email == null) ? 'Manual User' : $data->email;
        $user = new User();
        $user->merchant_id = $merchant_id;
        $user->country_id = $data->country_id;
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->UserPhone = $data->phone;
        $user->email = $email;
        $user->password = "";
        $user->UserSignupType = 1;
        $user->UserSignupFrom = 1;
        $user->ReferralCode = $user->GenrateReferCode();
        $user->user_type = 2;
        $user->UserProfileImage = null;
        $user->save();
        return $user;
    }

//    public function ManualUser($merchant_id)
//    {
//        $user = User::where([['merchant_id', '=', $merchant_id], ['manual_user', '=', 1]])->first();
//        if (!empty($user)) {
//            return $user;
//        }
//        $user = new User();
//        $user->merchant_id = $merchant_id;
//        $user->first_name = "Manual";
//        $user->last_name = "User";
//        $user->UserPhone = "Manual User";
//        $user->email = "Manual User";
//        $user->password = "Manual User";
//        $user->UserSignupType = 1;
//        $user->UserSignupFrom = 1;
//        $user->ReferralCode = $user->GenrateReferCode();
//        $user->user_type = 2;
//        $user->manual_user = 1;
//        $user->UserProfileImage = null;
//        $user->save();
//        return $user;
//    }
}