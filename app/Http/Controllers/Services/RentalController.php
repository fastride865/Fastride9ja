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
use App\Models\CountryArea;
use App\Http\Controllers\Helper\Merchant;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;

class RentalController extends Controller
{
    use ApiResponseTrait,MerchantTrait;
    public function CurrentBookingCheckout($request)
    {
        $validator = Validator::make($request->all(), [
            'service_package_id' => 'required|integer|exists:service_packages,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new \Exception($errors[0]);
        }
        $user = $request->user('api');
        $merchant_id = $user->merchant_id;
        $merchant = $user->Merchant;
        $string_file = $this->getStringFile(NULL,$merchant);
        $pricecards = PriceCard::where([['status', '=', 1],['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type], ['service_package_id', '=', $request->service_package_id]])->first();
        $newBookingData = new BookingDataController();
        if (empty($pricecards)) {
            $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 1);
            throw new \Exception(trans("$string_file.no_price_card_for_area"));
        }
        $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        // get nearest driver
        $drivers = Driver::GetNearestDriver([
            'area'=>$request->area,
            'segment_id'=>$request->segment_id,
            'latitude'=>$request->pickup_latitude,
            'longitude'=>$request->pickup_longitude,
            'distance'=>$configuration->rental_ride_now_radius,
            'limit'=>$configuration->rental_ride_now_request_driver,
            'service_type'=>$request->service_type,
            'vehicle_type'=>$request->vehicle_type,
        ]);

        if (empty($drivers) || $drivers->count() == 0) {
            $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 2);
            throw new \Exception(trans("$string_file.no_driver_available"));
        }
        $from = $request->pickup_latitude . "," . $request->pickup_longitude;
        $current_latitude = isset($drivers['0']) ? $drivers['0']->current_latitude : "";
        $current_longitude = isset($drivers['0']) ? $drivers['0']->current_longitude : "";
        $driverLatLong = $current_latitude . "," . $current_longitude;
        $units = ( CountryArea::find($request->area)->Country['distance_unit'] == 1 ) ? 'metric' : 'imperial';
        $nearDriver = DistanceController::DistanceAndTime($from, $driverLatLong, $configuration->google_key, $units);
        $estimate_driver_distnace = $nearDriver['distance'];
        $estimate_driver_time = $nearDriver['time'];
        $drop_locationArray = json_decode($request->drop_location, true);
        $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $configuration->google_key, $units,$string_file);
        if (empty($googleArray))
        {
            throw new \Exception(trans("$string_file.google_key_not_working"));
        }
        $lastLocation = $newBookingData->wayPoints($drop_locationArray);
        $time = $googleArray['total_time_text'];
        $timeSmall = $googleArray['total_time_minutes'];
        $distance = $googleArray['total_distance_text'];
        $distanceSmall = $googleArray['total_distance'];
        $image = $googleArray['image'];
        $bill_details = "";
        $outstanding_amount = Outstanding::where(['user_id' => $request->user('api')->id,'reason' => 1,'pay_status' => 0])->sum('amount');
        $merchant = new Merchant();
        switch ($pricecards->pricing_type) {
            case "1":
            case "2":
                $estimatePrice = new PriceController();
                $fare = $estimatePrice->BillAmount([
                    'price_card_id' => $pricecards->id,
                    'merchant_id' => $merchant_id,
                    'distance' => $distanceSmall,
                    'time' => $timeSmall,
                    'booking_id' => NULL,
                    'user_id' => $request->user('api')->id,
                    'outstanding_amount' => $outstanding_amount,
                    'booking_time' => date('H:i'),
                ]);
                $amount = $merchant->FinalAmountCal($fare['amount'],$merchant_id);
                $bill_details = json_encode($fare['bill_details']);
                break;
            case "3":
                $amount = trans('api.message62');;
                break;
        }
        $rideData = array(
            'distance' => $distance,
            'time' => $time,
            'bill_details' => $bill_details,
            'amount' => $amount,
            'estimate_driver_distnace' => $estimate_driver_distnace,
            'estimate_driver_time' => $estimate_driver_time,
            'estimate_distance' => $distance,
            'estimate_time' => $time,
            'auto_upgradetion' => 2
        );
        $result = $newBookingData->CreateCheckout($request, $request->user('api')->id, $merchant_id, $pricecards->id, $image, $rideData, $lastLocation);
        return ['message' => trans("$string_file.ready_for_ride"), 'data' => $result];
    }

    public function LaterBookingCheckout($request)
    {
        $validator = Validator::make($request->all(), [
            'service_package_id' => 'required|integer|exists:service_packages,id',
            'booking_type' => 'required|integer|in:2',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new \Exception($errors[0]);

        }
        $user = $request->user('api');
        $merchant_id = $user->merchant_id;

        $pricecards = PriceCard::where([['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type], ['service_package_id', '=', $request->service_package_id]])->first();
        $newBookingData = new BookingDataController();
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        if (empty($pricecards)) {
            $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 1);
            throw new \Exception(trans("$string_file.no_price_card_for_area"));
        }
        $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $drop_locationArray = json_decode($request->drop_location, true);
        $units = ( CountryArea::find($request->area)->Country['distance_unit'] == 1 ) ? 'metric' : 'imperial';
        $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $configuration->google_key, $units,$string_file);
        if (empty($googleArray)) {
            throw new \Exception(trans("$string_file.google_key_not_working"));
           // return ['result' => "0", 'message' => trans("$string_file.google_key_not_working"), 'data' => []];
        }
        $lastLocation = $newBookingData->wayPoints($drop_locationArray);
        $time = $googleArray['total_time_text'];
        $timeSmall = $googleArray['total_time_minutes'];
        $distance = $googleArray['total_distance_text'];
        $distanceSmall = $googleArray['total_distance'];
        $image = $googleArray['image'];
        $bill_details = "";
        $outstanding_amount = Outstanding::where(['user_id' => $request->user('api')->id,'reason' => 1,'pay_status' => 0])->sum('amount');
        switch ($pricecards->pricing_type) {
            case "1":
            case "2":
                $estimatePrice = new PriceController();
                $fare = $estimatePrice->BillAmount([
                    'price_card_id' => $pricecards->id,
                    'merchant_id' => $merchant_id,
                    'distance' => $distanceSmall,
                    'time' => $timeSmall,
                    'booking_id' => 0,
                    'user_id' => $request->user('api')->id,
                    'outstanding_amount' => $outstanding_amount,
                    'booking_time' => $request->later_time,
                ]);
                $amount = $fare['amount'];
                $bill_details = json_encode($fare['bill_details']);
                break;
            case "3":
                $amount = trans('api.message62');
                break;
        }
        $rideData = array(
            'distance' => $distance,
            'time' => $time,
            'bill_details' => $bill_details,
            'amount' => $amount,
            'estimate_driver_distnace' => $distance,
            'estimate_driver_time' => $time,
            'estimate_distance' => $distance,
            'estimate_time' => $time,
            'auto_upgradetion' => 2
        );
        $result = $newBookingData->CreateCheckout($request, $request->user('api')->id, $merchant_id, $pricecards->id, $image, $rideData, $lastLocation);
        return ['message' => trans("$string_file.ready_for_ride"), 'data' => $result];
    }

    public function currentBookingAssign($checkOut)
    {
        try {

            $user_gender = $checkOut->gender;
            $findDriver = new FindDriverController();
            $merchant_id = $checkOut->merchant_id;
            $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
            $drivers = Driver::GetNearestDriver([
                'area'=>$checkOut->country_area_id,
                'segment_id'=>$checkOut->segment_id,
                'latitude'=>$checkOut->pickup_latitude,
                'longitude'=>$checkOut->pickup_longitude,
                'distance'=>$configuration->rental_ride_now_radius,
                'limit'=>$configuration->rental_ride_now_request_driver,
                'service_type'=>$checkOut->service_type_id,
                'vehicle_type'=>$checkOut->vehicle_type_id,
                'payment_method_id'=>$checkOut->payment_method_id,
                'estimate_bill'=>$checkOut->estimate_bill,
                'user_gender'=>$user_gender,
            ]);

            $string_file = $this->getStringFile($merchant_id);
            if (empty($drivers)) {
                throw new \Exception(trans("$string_file.no_driver_available"));
            }
            $Bookingdata = $checkOut->toArray();
            unset($Bookingdata['id']);
            unset($Bookingdata['user']);
            unset($Bookingdata['created_at']);
            unset($Bookingdata['updated_at']);
            unset($Bookingdata['service_type']);
            $Bookingdata['booking_timestamp'] = time();
            $Bookingdata['booking_status'] = 1001;
            $Bookingdata['insurnce'] = request()->insurnce;
            $booking = Booking::create($Bookingdata);
            $findDriver->AssignRequest($drivers, $booking->id);
            $bookingData = new BookingDataController();
            $bookingData->SendNotificationToDrivers($booking, $drivers);
            return ['message' => trans("$string_file.ride_booked"), 'data' => $booking];

        }catch (\Exception $e)
        {
           throw new \Exception($e->getMessage());
        }
    }

    public function laterBookingAssign($checkOut)
    {
        try {
            $user_gender = $checkOut->gender;
            $Bookingdata = $checkOut->toArray();
            unset($Bookingdata['id']);
            unset($Bookingdata['user']);
            unset($Bookingdata['created_at']);
            unset($Bookingdata['updated_at']);
            unset($Bookingdata['service_type']);
            $Bookingdata['booking_timestamp'] = time();
            $Bookingdata['booking_status'] = 1001;
            $Bookingdata['insurnce'] = request()->insurnce;
            $booking = Booking::create($Bookingdata);
            $merchant_id  = $checkOut->merchant_id;
            $configuration = BookingConfiguration::where([['merchant_id', '=',$merchant_id ]])->first();
            if ($configuration->rental_ride_later_request_type == 1) {
                $drivers = Driver::GetNearestDriver([
                    'area'=>$checkOut->country_area_id,
                    'segment_id'=>$checkOut->segment_id,
                    'latitude'=>$checkOut->pickup_latitude,
                    'longitude'=>$checkOut->pickup_longitude,
                    'distance'=>$configuration->rental_ride_now_radius,
                    'limit'=>$configuration->rental_ride_now_request_driver,
                    'service_type'=>$checkOut->service_type_id,
                    'vehicle_type'=>$checkOut->vehicle_type_id,
                    'payment_method_id'=>$checkOut->payment_method_id,
                    'estimate_bill'=>$checkOut->estimate_bill,
                    'user_gender'=>$user_gender,
                ]);

                if (!empty($drivers)) {
                    $bookingData = new BookingDataController();
                    $bookingData->SendNotificationToDrivers($booking, $drivers);
                }
            }
            $string_file = $this->getStringFile($merchant_id);
            return ['message' => trans("$string_file.ride_booked"), 'data' => $booking];
        }catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }
}
