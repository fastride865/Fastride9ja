<?php

namespace App\Http\Controllers\Services;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\FindDriverController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\Merchant;
use App\Http\Controllers\Helper\PolygenController;
use App\Http\Controllers\Helper\PriceController;
use App\Models\Booking;
use App\Models\BookingConfiguration;
use App\Models\CountryArea;
use App\Models\Configuration;
use App\Models\Driver;
use App\Models\Outstanding;
use App\Models\PriceCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;

class OutstationController extends Controller
{
    use ApiResponseTrait,MerchantTrait;
    public function CheckOut($request)
    {

        try{
            $user = $request->user('api');
            $merchant_id = $user->merchant_id;
            $string_file = $this->getStringFile(NULL,$user->Merchant);
            $query = PriceCard::where([['status', '=', 1],['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type]]);
            $newBookingData = new BookingDataController();
            $round = 2;
            if (!empty($request->service_package_id)) {
                $round = 1;
                $query->where([['service_package_id', '=', $request->service_package_id]]);
            } else {
                $query->whereNull('service_package_id');
            }
            $pricecards = $query->first();
            if (empty($pricecards)) {
                $pricecards = PriceCard::where([['status', '=', 1],['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type]])->first();
                if (empty($pricecards)) {
                    $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 1);
                    throw new \Exception(trans("$string_file.no_price_card_for_area"));
                }
            }
            $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
            $drop_locationArray = json_decode($request->drop_location, true);
            $units = ( CountryArea::find($request->area)->Country['distance_unit'] == 1 ) ? 'metric' : 'imperial';
            $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $configuration->google_key, $units,$string_file);
//        if (empty($googleArray)) {
//            throw new \Exception(trans("$string_file.google_key_not_working"));
//
//        }
            if (!empty($request->return_date)) {
                $datetime1 = strtotime("$request->later_date. $request->later_time");
                $datetime2 = strtotime("$request->return_date .$request->return_time");
                $interval = abs($datetime2 - $datetime1);
                $timeSmall = round($interval / 60);
                $time = trans("$string_file.online_time", ['hours' => intdiv($timeSmall, 60), 'min' => ($timeSmall % 60)]);
            } else {
                $time = $googleArray['total_time_text'];
                $timeSmall = $googleArray['total_time_minutes'] * $round;
            }
            $lastLocation = $newBookingData->wayPoints($drop_locationArray);
            $distance = $googleArray['total_distance_text'];
            $distanceSmall = $googleArray['total_distance'] * $round;
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
                        'booking_time' => date('H:i'),
                    ]);
                    $amount = $fare['amount'];
                    $bill_details = json_encode($fare['bill_details']);
                    break;
                case "3":
                    $amount = trans('api.message62');
                    break;
            }
            $merchant = new Merchant();
            $rideData = array(
                'distance' => $distance,
                'time' => $time,
                'amount' => $merchant->FinalAmountCal($amount,$merchant_id),
                'bill_details' => $bill_details,
                'estimate_driver_distnace' => $distance,
                'estimate_driver_time' => $time,
                'auto_upgradetion' => 2
            );
            $result = $newBookingData->CreateCheckout($request, $request->user('api')->id, $merchant_id, $pricecards->id, $image, $rideData, $lastLocation);
            return ['message' => trans("$string_file.ready_for_ride"), 'data' => $result];
        }catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }

    }

    public function bookingAssign($checkOut)
    {
        try {
            $user_gender = $checkOut->gender;
//        $checkOut->additional_notes = $additional_notes;
            $Bookingdata = $checkOut->toArray();
            unset($Bookingdata['id']);
            // unset($Bookingdata['bill_details']);
            unset($Bookingdata['user']);
            unset($Bookingdata['created_at']);
            unset($Bookingdata['updated_at']);
            unset($Bookingdata['service_type']);
            $Bookingdata['booking_timestamp'] = time();
            $Bookingdata['booking_status'] = 1001;
            $Bookingdata['insurnce'] = request()->insurnce;
            $merchant_id = $checkOut->merchant_id;
            $booking = Booking::create($Bookingdata);
            $configuration = BookingConfiguration::where([['merchant_id', '=',$merchant_id ]])->first();
            if ($configuration->outstation_request_type == 1) {
                $drivers = Driver::GetNearestDriver([
                    'area'=>$checkOut->country_area_id,
                    'segment_id'=>$checkOut->segment_id,
                    'latitude'=>$checkOut->pickup_latitude,
                    'longitude'=>$checkOut->pickup_longitude,
                    'distance'=>$configuration->outstation_radius,
                    'limit'=>$configuration->outstation_request_driver,
                    'vehicle_type'=>$checkOut->vehicle_type_id,
                    'service_type'=>$checkOut->service_type_id,
                    'payment_method_id'=>$checkOut->payment_method_id,
                    'estimate_bill'=>$checkOut->estimate_bill,
                    'user_gender'=>$user_gender,
                ]);
                unset($booking->map_image);
                if (!empty($drivers)) {
                    $bookingData = new BookingDataController();
                    $bookingData->SendNotificationToDrivers($booking, $drivers);
                }
            }
            $string_file = $this->getStringFile($merchant_id);
            return ['message' => trans("$string_file.ride_booked"), 'data' => $booking];
        }catch (\Exception $e)
        {

        }
    }

    public function currentBookingAssign($checkOut)
    {
        try {
            $user_gender = (isset($checkOut->gender)) ? $checkOut->gender : null;
            $findDriver = new FindDriverController();
            $merchant_id = $checkOut->merchant_id;
            $configuration = BookingConfiguration::select('id','merchant_id','outstation_ride_now_radius','outstaion_ride_now_request_driver')->where([['merchant_id', '=',$merchant_id ]])->first();

            $string_file = $this->getStringFile($merchant_id);
            if (isset($configuration->outstation_ride_now_radius) && isset($configuration->outstaion_ride_now_request_driver))
            {
                $drivers = Driver::GetNearestDriver([
                    'area'=>$checkOut->country_area_id,
                    'segment_id'=>$checkOut->segment_id,
                    'latitude'=>$checkOut->pickup_latitude,
                    'longitude'=>$checkOut->pickup_longitude,
                    'distance'=>$configuration->outstation_ride_now_radius,
                    'limit'=>$configuration->outstaion_ride_now_request_driver,
                    'vehicle_type'=>$checkOut->vehicle_type_id,
                    'service_type'=>$checkOut->service_type_id,
                    'payment_method_id'=>$checkOut->payment_method_id,
                    'estimate_bill'=>$checkOut->estimate_bill,
                    'user_gender'=>$user_gender,
                ]);
                if (empty($drivers)) {
                    throw new \Exception(trans("$string_file.no_driver_available"));
                }
                $Bookingdata = $checkOut->toArray();
                unset($Bookingdata['id']);
                //unset($Bookingdata['bill_details']);
                unset($Bookingdata['user']);
                unset($Bookingdata['created_at']);
                unset($Bookingdata['updated_at']);
                unset($Bookingdata['service_type']);
                $Bookingdata['booking_timestamp'] = time();
                $Bookingdata['booking_status'] = 1001;
                $booking = Booking::create($Bookingdata);
                $booking->trip_way = isset($booking->return_date) && isset($booking->return_time) ? trans("$string_file.round_trip") : trans("$string_file.one_way");
                $findDriver->AssignRequest($drivers, $booking->id);
                $bookingData = new BookingDataController();
                unset($booking->map_image_url);
//            $message = $bookingData->LanguageData($booking->merchant_id,25);
//            $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
                $bookingData->SendNotificationToDrivers($booking, $drivers);
                return ['message' => trans("$string_file.ride_booked"), 'data' => $booking];
            } else {
                throw new \Exception(trans("$string_file.configuration_not_found"));
                //return response()->json(['result' => "0", 'message' => trans("$string_file.configuration_not_found"), 'data' => []]);
            }
        }catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

//    public function outstationDetail(Request $request)
//    {
//        try {
//            $baseFare = 0;
//            $validator = Validator::make($request->all(), [
//                'area' => 'required',
//                'pickup_lat' => 'required|string',
//                'pickup_long' => 'required|string',
//                'drop_lat' => 'required|string',
//                'drop_long' => 'required|string',
//                'service_type' => 'required',
//                'segment_id' => 'required',
//            ]);
//            if ($validator->fails()) {
//                $errors = $validator->messages()->all();
//                return $this->failedResponse($errors[0]);
//                // return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
//            }
//            $merchant_id = $request->user('api')->merchant_id;
//            $string_file = $this->getStringFile($merchant_id);
//            $merchant = new Merchant();
//            $configuration = BookingConfiguration::where([['merchant_id', '=',$merchant_id]])->first();
//            $drop_locationArray[] = [
//                'drop_latitude' => $request->drop_lat,
//                'drop_longitude' => $request->drop_long,
//            ];
//            $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_lat, $request->pickup_long, $drop_locationArray, $configuration->google_key);
//            $distance = $googleArray['total_distance'] / 1000;
//            $timeSmall = (string)ceil($googleArray['total_time_minutes']);
//            $merchant_id = $request->user('api')->merchant_id;
//            $Droparea = PolygenController::OutstationArea($request->drop_lat, $request->drop_long, $merchant_id);
//            $area = CountryArea::find($request->area);
//            $packageRate = [];
//            if (!empty($Droparea)) {
//                $packageRate = PriceCard::
//                select('id', 'country_area_id', 'service_type_id', 'vehicle_type_id', 'segment_id', 'outstation_type', 'base_fare', 'free_distance', 'free_time', 'minimum_wallet_amount','service_package_id')->
//                where([['status', '=', 1], ['segment_id', '=', $request->segment_id], ['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service_type], ['service_package_id', '=', $Droparea['id']]])->get();
//                if (empty($packageRate->toArray())) {
//                    $packageRate = [];
//                }
//            }
//            $perKiloRate = PriceCard::select('id', 'country_area_id', 'service_type_id', 'vehicle_type_id', 'segment_id', 'outstation_type', 'base_fare', 'free_distance', 'free_time', 'minimum_wallet_amount','service_package_id')->
//            where([['status', '=', 1], ['segment_id', '=', $request->segment_id], ['outstation_max_distance', '>=', $distance], ['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service_type]])->whereNull('service_package_id')->get();
//            if (empty($packageRate) && empty($perKiloRate->toArray())) {
//                return $this->failedResponse(trans("$string_file.no_price_card_for_area"));
//            }
//            $currency = $area->Country->isoCode;
//            $distance_unit = $area->Country->distance_unit;
//            $distance_unit = $distance_unit == 1 ? trans("$string_file.km") : trans("$string_file.miles");
//            $distance_unit = trans("$string_file.per") . ' ' . $distance_unit;
//            foreach ($packageRate as $value) {
//                $priceCard = $value->PriceCardValues;
//                $filtered = $priceCard->filter(function ($value) {
//                    return $value->PricingParameter->parameterType == 1;
//                });
//                $Base = $value->base_fare;
//                if (!empty($filtered->toArray())) {
//                    $baseFare = $filtered->pluck('parameter_price');
//                    $baseFare = $baseFare[0];
//                    if ($distance > $value->free_distance) {
//                        $extra = $distance - $value->free_distance;
//                        $baseFare = $extra * $baseFare;
//                    }
//                }
//                $baseFare = $merchant->FinalAmountCal(($baseFare + $Base), $merchant_id);
//                $value->vechile_name = $value->VehicleType->VehicleTypeName;
//                $value->vechile_image = get_image($value->VehicleType->vehicleTypeImage, 'vehicle', $merchant_id, true, false);
//                $value->vechile_description = $value->VehicleType->VehicleTypeDescription;
//                $value->base_fare = $currency . " " . $baseFare;
//            }
//            $roundTrip = !empty($packageRate) ? 1 : 2;
//            foreach ($perKiloRate as $value) {
//                $priceCard = $value->PriceCardValues;
//                $filtered = $priceCard->filter(function ($value) {
//                    return $value->PricingParameter->parameterType == 1;
//                });
//                $Base = $value->base_fare;
//                if (!empty($filtered->toArray())) {
//                    $baseFare = $filtered->pluck('parameter_price');
//                    $baseFare = $baseFare[0];
//                    if ($distance > $value->free_distance) {
//                        $extra = $distance - $value->free_distance;
//                        $baseFare = $extra * $baseFare * $roundTrip;
//                    }
//                }
//                $baseFare = $merchant->FinalAmountCal(($baseFare + $Base), $merchant_id);
//                $value->base_fare = $currency . " " . $baseFare . " " . $distance_unit;
//                $value->vechile_name = $value->VehicleType->VehicleTypeName;
//                $value->vechile_image = get_image($value->VehicleType->vehicleTypeImage, 'vehicle', $merchant_id, true, false);
//                $value->vechile_description = $value->VehicleType->VehicleTypeDescription;
//            }
//            $data = array('single' => $packageRate, 'round' => $perKiloRate, 'return_time' => $timeSmall);
//            return $this->successResponse(trans("$string_file.outstation_one"), $data);
//        }
//        catch (\Exception $e)
//        {
//            return $this->failedResponse($e->getMessage());
//        }
//    }


    public function outstationDetail(Request $request)
    {
        try {
            $baseFare = 0;
            $validator = Validator::make($request->all(), [
                'area' => 'required',
                'pickup_lat' => 'required|string',
                'pickup_long' => 'required|string',
                'drop_lat' => 'required|string',
                'drop_long' => 'required|string',
                'service_type' => 'required',
                'segment_id' => 'required',
            ]);
            if ($validator->fails()) {
                $errors = $validator->messages()->all();
                return $this->failedResponse($errors[0]);
                // return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
            }
            $merchant_id = $request->user('api')->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
            $merchant = new Merchant();
            $configuration = BookingConfiguration::where([['merchant_id', '=',$merchant_id]])->first();
            $drop_locationArray[] = [
                'drop_latitude' => $request->drop_lat,
                'drop_longitude' => $request->drop_long,
            ];
            $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_lat, $request->pickup_long, $drop_locationArray, $configuration->google_key,$string_file);
            $distance = $googleArray['total_distance'] / 1000;
            $timeSmall = (string)ceil($googleArray['total_time_minutes']);
            $merchant_id = $request->user('api')->merchant_id;
            $Droparea = PolygenController::OutstationArea($request->drop_lat, $request->drop_long, $merchant_id);
            $area = CountryArea::find($request->area);
            $packageRate = [];
            if (!empty($Droparea)) {
                $packageRate = PriceCard::
                select('id', 'country_area_id', 'service_type_id', 'vehicle_type_id', 'segment_id', 'outstation_type', 'base_fare', 'free_distance', 'free_time', 'minimum_wallet_amount','service_package_id')->
                where([['status', '=', 1], ['segment_id', '=', $request->segment_id], ['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service_type], ['service_package_id', '=', $Droparea['id']]])->get();
                if (empty($packageRate->toArray())) {
                    $packageRate = [];
                }
            }
            $perKiloRate = PriceCard::select('id', 'country_area_id', 'service_type_id', 'vehicle_type_id', 'segment_id', 'outstation_type', 'base_fare', 'free_distance', 'free_time', 'minimum_wallet_amount','service_package_id')->
            where([['status', '=', 1], ['segment_id', '=', $request->segment_id], ['outstation_max_distance', '>=', $distance], ['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service_type]])->whereNull('service_package_id')->get();
            if (empty($packageRate) && empty($perKiloRate->toArray())) {
                return $this->failedResponse(trans("$string_file.no_price_card_for_area"));
            }
            $currency = $area->Country->isoCode;
            $distance_unit = $area->Country->distance_unit;
            $distance_unit = $distance_unit == 1 ? trans("$string_file.km") : trans("$string_file.miles");
            $distance_unit = trans("$string_file.per") . ' ' . $distance_unit;
            $outstanding_amount = Outstanding::where(['user_id' => $request->user('api')->id,'reason' => 1,'pay_status' => 0])->sum('amount');
            foreach ($packageRate as $value) {
//                $priceCard = $value->PriceCardValues;
//                $filtered = $priceCard->filter(function ($value) {
//                    return $value->PricingParameter->parameterType == 1;
//                });
//                $Base = $value->base_fare;
//                if (!empty($filtered->toArray())) {
//                    $baseFare = $filtered->pluck('parameter_price');
//                    $baseFare = $baseFare[0];
//                    if ($distance > $value->free_distance) {
//                        $extra = $distance - $value->free_distance;
//                        $baseFare = $extra * $baseFare;
//                    }
//                }
                $estimatePrice = new PriceController();
                $fare = $estimatePrice->BillAmount([
                    'price_card_id' => $value->id,
                    'merchant_id' => $merchant_id,
                    'distance' => $googleArray['total_distance'],
                    'time' => $timeSmall,
                    'booking_id' => 0,
                    'user_id' => $request->user('api')->id,
                    'outstanding_amount' => $outstanding_amount,
                    'booking_time' => date('H:i'),
                ]);
                $amount = $fare['amount'];
//                $baseFare = $merchant->FinalAmountCal(($baseFare + $Base), $merchant_id);
                $value->vechile_name = $value->VehicleType->VehicleTypeName;
                $value->vechile_image = get_image($value->VehicleType->vehicleTypeImage, 'vehicle', $merchant_id, true, false);
                $value->vechile_description = $value->VehicleType->VehicleTypeDescription;
                $value->base_fare = $currency . " " . $amount;
            }
            $roundTrip = 1;
            if (!empty($request->return_date)) {
                $roundTrip = 2;
                $datetime1 = strtotime("$request->later_date. $request->later_time");
                $datetime2 = strtotime("$request->return_date .$request->return_time");
                $interval = abs($datetime2 - $datetime1);
                $timeSmall = round($interval / 60);
            }
//            $distance = (2*$distance); // round trip logic
            foreach ($perKiloRate as $value) {
//                $priceCard = $value->PriceCardValues;
//                $filtered = $priceCard->filter(function ($value) {
//                    return $value->PricingParameter->parameterType == 1;
//                });
//                $Base = $value->base_fare;
//                if (!empty($filtered->toArray())) {
//                    $baseFare = $filtered->pluck('parameter_price');
//                    $baseFare = $baseFare[0];
//                    if ($distance > $value->free_distance) {
//                        $extra =   $distance - $value->free_distance;
//                        $baseFare = $extra * $baseFare;
//                    }
//                }
//                $baseFare = $merchant->FinalAmountCal(($baseFare + $Base), $merchant_id);

                $estimatePrice = new PriceController();
                $fare = $estimatePrice->BillAmount([
                    'price_card_id' => $value->id,
                    'merchant_id' => $merchant_id,
                    'distance' => $googleArray['total_distance'] * $roundTrip,
                    'time' => $timeSmall,
                    'booking_id' => 0,
                    'user_id' => $request->user('api')->id,
                    'outstanding_amount' => $outstanding_amount,
                    'booking_time' => date('H:i'),
                ]);
                $amount = $fare['amount'];

                $value->base_fare = $currency . " " . $amount;
                $value->vechile_name = $value->VehicleType->VehicleTypeName;
                $value->vechile_image = get_image($value->VehicleType->vehicleTypeImage, 'vehicle', $merchant_id, true, false);
                $value->vechile_description = $value->VehicleType->VehicleTypeDescription;
            }
            $data = array('single' => $packageRate, 'round' => $perKiloRate, 'return_time' => $timeSmall);
            return $this->successResponse(trans("$string_file.outstation_one"), $data);
        }
        catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }
    }
}
