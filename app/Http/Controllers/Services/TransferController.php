<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\DistanceController;
use App\Http\Controllers\Helper\FindDriverController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\PriceController;
use App\Models\Booking;
use App\Models\BookingConfiguration;
use App\Models\Configuration;
use App\Models\Driver;
use App\Models\Outstanding;
use App\Models\PriceCard;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{
//    public function CurrentBookingCheckout($request)
//    {
//        $validator = Validator::make($request->all(), [
//            'service_package_id' => 'required|integer|exists:service_packages,id',
//            'area' => 'required|integer',
//            'service_type' => 'required|integer',
//            'vehicle_type' => 'required|integer',
//            'pickup_latitude' => 'required',
//            'pickup_longitude' => 'required',
//            'pick_up_locaion' => 'required',
//            'booking_type' => 'required|integer|in:1',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return ['result' => "0", 'message' => $errors[0], 'data' => []];
//        }
//        $merchant_id = $request->user('api')->merchant_id;
//        $pricecards = PriceCard::where([['price_card_status', '=', 1],['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type], ['package_id', '=', $request->package_id]])->first();
//        $units = ( CountryArea::find($request->area)->Country['distance_unit'] == 1 ) ? 'metric' : 'imperial';
//        $newBookingData = new BookingDataController();
//        if (empty($pricecards)) {
//            $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 1);
//            return ['result' => "0", 'message' => trans("$string_file.no_price_card_for_area"), 'data' => []];
//        }
//        $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
//
////        $findDriver = new FindDriverController();
////        $drivers = $findDriver->GetAllNearestDriver($request->area, $request->pickup_latitude, $request->pickup_longitude, $configuration->transfer_ride_now_radius, $configuration->transfer_ride_now_request_driver, $request->vehicle_type, $request->service_type, '', '', $user_gender, $configuration->driver_request_timeout);
//        $drivers = Driver::GetNearestDriver([
//            'area'=>$request->area,
//            'latitude'=>$request->pickup_latitude,
//            'longitude'=>$request->pickup_longitude,
//            'distance'=>$configuration->transfer_ride_now_radius,
//            'limit'=>$configuration->transfer_ride_now_request_driver,
//            'service_type'=>$request->service_type,
//            'vehicle_type'=>$request->vehicle_type,
//        ]);
//
//        if (empty($drivers)) {
//            $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 2);
////            return ['result' => "0", 'message' => trans("$string_file.no_driver_available"), 'data' => []];
//        }
//        $from = $request->pickup_latitude . "," . $request->pickup_longitude;
//        $current_latitude = $drivers['0']->current_latitude;
//        $current_longitude = $drivers['0']->current_longitude;
//        $driverLatLong = $current_latitude . "," . $current_longitude;
//        $nearDriver = DistanceController::DistanceAndTime($from, $driverLatLong, $configuration->google_key, $units);
//        $estimate_driver_distnace = $nearDriver['distance'];
//        $estimate_driver_time = $nearDriver['time'];
//        $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, [], $configuration->google_key, $units);
//        if (empty($googleArray)) {
//            return ['result' => "0", 'message' => trans("$string_file.google_key_not_working"), 'data' => []];
//        }
//        $time = $googleArray['total_time_text'];
//        $timeSmall = $googleArray['total_time_minutes'];
//        $distance = $googleArray['total_distance_text'];
//        $distanceSmall = $googleArray['total_distance'];
//        $image = $googleArray['image'];
//        $bill_details = "";
//        $outstanding_amount = Outstanding::where(['user_id' => $request->user('api')->id,'reason' => 1,'pay_status' => 0])->sum('amount');
//        switch ($pricecards->pricing_type) {
//            case "1":
//            case "2":
//                $estimatePrice = new PriceController();
//                $fare = $estimatePrice->BillAmount([
//                    'price_card_id' => $pricecards->id,
//                    'merchant_id' => $merchant_id,
//                    'distance' => $distanceSmall,
//                    'time' => $timeSmall,
//                    'booking_id' => 0,
//                    'user_id' => $request->user('api')->id,
//                    'outstanding_amount' => $outstanding_amount,
//                    'booking_time' => date('H:i'),
//                ]);
//                $amount = $fare['amount'];
//                $bill_details = json_encode($fare['bill_details']);
//                break;
//            case "3":
//                $amount = trans('api.message62');;
//                break;
//        }
//        $rideData = array(
//            'distance' => $distance,
//            'time' => $time,
//            'bill_details' => $bill_details,
//            'amount' => $amount,
//            'estimate_driver_distnace' => $estimate_driver_distnace,
//            'estimate_driver_time' => $estimate_driver_time,
//            'auto_upgradetion' => 2
//        );
//        $result = $newBookingData->CreateCheckout($request, $request->user('api')->id, $merchant_id, $pricecards->id, $image, $rideData, []);
////        return ['result' => "1", 'message' => trans("$string_file.ready_for_ride"), 'data' => $result];
//    }

//    public function LaterBookingCheckout($request)
//    {
//        $validator = Validator::make($request->all(), [
//            'package_id' => 'required|integer|exists:packages,id',
//            'area' => 'required|integer',
//            'service_type' => 'required|integer',
//            'vehicle_type' => 'required|integer',
//            'pickup_latitude' => 'required',
//            'pickup_longitude' => 'required',
//            'pick_up_locaion' => 'required',
//            'booking_type' => 'required|integer|in:2',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return ['result' => "0", 'message' => $errors[0], 'data' => []];
//        }
//        $merchant_id = $request->user('api')->merchant_id;
//        $pricecards = PriceCard::where([['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type], ['package_id', '=', $request->package_id]])->first();
//        $units = ( CountryArea::find($request->area)->Country['distance_unit'] == 1 ) ? 'metric' : 'imperial';
//        $newBookingData = new BookingDataController();
//        if (empty($pricecards)) {
//            $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 1);
//            return ['result' => "0", 'message' => trans("$string_file.no_price_card_for_area"), 'data' => []];
//        }
//        $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
//        $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, [], $configuration->google_key, $units);
//        if (empty($googleArray)) {
//            return ['result' => "0", 'message' => trans("$string_file.google_key_not_working"), 'data' => []];
//        }
//        $time = $googleArray['total_time_text'];
//        $timeSmall = $googleArray['total_time_minutes'];
//        $distance = $googleArray['total_distance_text'];
//        $distanceSmall = $googleArray['total_distance'];
//        $image = $googleArray['image'];
//        $bill_details = "";
//        $outstanding_amount = Outstanding::where(['user_id' => $request->user('api')->id,'reason' => 1,'pay_status' => 0])->sum('amount');
//        switch ($pricecards->pricing_type) {
//            case "1":
//            case "2":
//                $estimatePrice = new PriceController();
//                $fare = $estimatePrice->BillAmount([
//                    'price_card_id' => $pricecards->id,
//                    'merchant_id' => $merchant_id,
//                    'distance' => $distanceSmall,
//                    'time' => $timeSmall,
//                    'booking_id' => 0,
//                    'user_id' => $request->user('api')->id,
//                    'outstanding_amount' => $outstanding_amount,
//                    'booking_time' => $request->later_time,
//                ]);
//                $amount = $fare['amount'];
//                $bill_details = json_encode($fare['bill_details']);
//                break;
//            case "3":
//                $amount = trans('api.message62');
//                break;
//        }
//        $rideData = array(
//            'distance' => $distance,
//            'time' => $time,
//            'amount' => $amount,
//            'bill_details' => $bill_details,
//            'estimate_driver_distnace' => "",
//            'estimate_driver_time' => "",
//            'auto_upgradetion' => 2
//        );
//        $result = $newBookingData->CreateCheckout($request, $request->user('api')->id, $merchant_id, $pricecards->id, $image, $rideData, []);
////        return ['result' => "1", 'message' => trans("$string_file.ready_for_ride"), 'data' => $result];
//    }

//    public function CurrentBookingAssign($checkOut)
//    {
//        $user_gender = $checkOut->gender;
////        $checkOut->additional_notes = $additional_notes;
//        $findDriver = new FindDriverController();
//        $configuration = BookingConfiguration::where([['merchant_id', '=', $checkOut->merchant_id]])->first();
//
////        $drivers = $findDriver->GetAllNearestDriver($checkOut->country_area_id, $checkOut->pickup_latitude, $checkOut->pickup_longitude, $configuration->transfer_ride_now_radius, $configuration->transfer_ride_now_request_driver, $checkOut->vehicle_type_id, $checkOut->service_type_id, $checkOut->drop_latitude, $checkOut->drop_longitude, $user_gender, $configuration->driver_request_timeout);
//
//        $drivers = Driver::GetNearestDriver([
//            'area'=>$checkOut->country_area_id,
//            'latitude'=>$checkOut->pickup_latitude,
//            'longitude'=>$checkOut->pickup_longitude,
//            'distance'=>$configuration->transfer_ride_now_radius,
//            'limit'=>$configuration->transfer_ride_now_request_driver,
//            'service_type'=>$checkOut->service_type_id,
//            'vehicle_type'=>$checkOut->vehicle_type_id,
//            'user_gender'=>$user_gender,
//        ]);
//        if (empty($drivers)) {
//            return ['result' => "0", 'message' => trans("$string_file.no_driver_available"), 'data' => []];
//        }
//        $Bookingdata = $checkOut->toArray();
//        unset($Bookingdata['id']);
//        // unset($Bookingdata['bill_details']);
//        unset($Bookingdata['user']);
//        unset($Bookingdata['created_at']);
//        unset($Bookingdata['updated_at']);
//        $Bookingdata['booking_timestamp'] = time();
//        $Bookingdata['booking_status'] = 1001;
//        $Bookingdata['insurnce'] = request()->insurnce;
//        $booking = Booking::create($Bookingdata);
//        $findDriver->AssignRequest($drivers, $booking->id);
//        $bookingData = new BookingDataController();
//        $message = $bookingData->LanguageData($booking->merchant_id, 25);
//        $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
//        return ['result' => "1", 'message' => trans("$string_file.ride_booked"), 'data' => $booking];
//    }

//    public function LeterBookingAssign($checkOut)
//    {
//        $user_gender = $checkOut->gender;
////        $checkOut->additional_notes = $additional_notes;
//        $Bookingdata = $checkOut->toArray();
//        unset($Bookingdata['id']);
//        // unset($Bookingdata['bill_details']);
//        unset($Bookingdata['user']);
//        unset($Bookingdata['created_at']);
//        unset($Bookingdata['updated_at']);
//        $Bookingdata['booking_timestamp'] = time();
//        $Bookingdata['booking_status'] = 1001;
//        $Bookingdata['insurnce'] = request()->insurnce;
//        $booking = Booking::create($Bookingdata);
//        $configuration = BookingConfiguration::where([['merchant_id', '=', $checkOut->merchant_id]])->first();
//        if ($configuration->transfer_ride_later_request_type == 1) {
////            $findDriver = new FindDriverController();
////            $drivers = $findDriver->GetAllNearestDriver($checkOut->country_area_id, $checkOut->pickup_latitude, $checkOut->pickup_longitude, $configuration->transfer_ride_later_radius, $configuration->transfer_ride_later_request_driver, $checkOut->vehicle_type_id, $checkOut->service_type_id, '', '', $user_gender, $configuration->driver_request_timeout);
//
//            $drivers = Driver::GetNearestDriver([
//                'area'=>$checkOut->country_area_id,
//                'latitude'=>$checkOut->pickup_latitude,
//                'longitude'=>$checkOut->pickup_longitude,
//                'distance'=>$configuration->transfer_ride_now_radius,
//                'limit'=>$configuration->transfer_ride_now_request_driver,
//                'service_type'=>$checkOut->service_type_id,
//                'vehicle_type'=>$checkOut->vehicle_type_id,
//                'user_gender'=>$user_gender,
//            ]);
//            if (!empty($drivers)) {
//                $bookingData = new BookingDataController();
//                $message = $bookingData->LanguageData($booking->merchant_id, 26);
//                $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
//            }
//        }
////        return ['result' => "1", 'message' => trans("$string_file.ride_booked"), 'data' => $booking];
//    }
}
