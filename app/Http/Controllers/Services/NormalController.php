<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Helper\Merchant;
use App\Http\Controllers\Helper\PolygenController;
use App\Models\Driver;
use App\Models\Booking;
use App\Models\BookingConfiguration;
use App\Models\CountryArea;
use App\Models\Outstanding;
use App\Models\PriceCard;
use App\Models\Configuration;
use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\PriceController;
use App\Http\Controllers\Helper\DistanceController;
use App\Http\Controllers\Helper\BookingDataController;
use App\Traits\AreaTrait;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;

class NormalController extends Controller
{
    use AreaTrait,ApiResponseTrait,MerchantTrait;
    public function CurrentBookingCheckout($request)
    {
        try{
            $drop_locationArray = json_decode($request->drop_location, true);
            $dropLocation = isset($drop_locationArray[0]) ? $drop_locationArray[0] : '';
            $drop_lat = isset($dropLocation['drop_latitude']) ? $dropLocation['drop_latitude'] : '';
            $drop_long = isset($dropLocation['drop_longitude']) ? $dropLocation['drop_longitude'] :'';

            $merchant_id = $request->user('api')->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
            $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
            $countryArea = CountryArea::find($request->area);
//            date_default_timezone_set($countryArea->timezone);
            if(isset($config->geofence_module) && $config->geofence_module == 1){
                $dropCountryArea = $this->checkGeofenceArea($drop_lat,$drop_long,'drop',$merchant_id);
                if($countryArea->is_geofence == 1){
                    $pricecards = PriceCard::where([['status', '=', 1], ['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type]])->first();
                    if(empty($pricecards) && !empty($dropCountryArea)){
                        $pricecards = PriceCard::where([['status', '=', 1], ['country_area_id', '=', $dropCountryArea['id']], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type]])->first();
                    }else{
                        $request->is_geofence = 1;
                        $request->base_area_id = $request->area;
                    }
                }elseif(!empty($dropCountryArea) && $dropCountryArea->is_geofence == 1){
                    $pricecards = PriceCard::where([['status', '=', 1], ['country_area_id', '=', $dropCountryArea['id']], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type]])->first();
                    if(empty($pricecards)){
                        $pricecards = PriceCard::where([['status', '=', 1], ['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type]])->first();
                    }else{
                        $request->is_geofence = 1;
                        $request->base_area_id = $request->area;
                    }
                }else{
                    $pricecards = PriceCard::where([['status', '=', 1], ['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type]])->first();
                }
            }else{
                $where = [['status', '=', 1], ['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type]];
                if($config->drop_outside_area == 1 && $config->outside_area_ratecard == 1){
                    $ploygon = new PolygenController();
                    $checkArea = $ploygon->CheckArea($drop_lat, $drop_long, $countryArea->AreaCoordinates);
                    if(!$checkArea){
                        array_push($where,['rate_card_scope','=',2]);
                    }
                }
                $pricecards = PriceCard::where($where)->first();
            }

            $newBookingData = new BookingDataController();
            if (empty($pricecards)) {
                $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 1);
                throw new \Exception(trans("$string_file.no_price_card_for_area"));
            }else{
                $county_area = CountryArea::find($pricecards->country_area_id);
                $request->is_geofence = ($county_area->is_geofence == 1) ? 1 : 0;
                $request->base_area_id = $pricecards->country_area_id;
            }

            $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();

            if (!empty($configuration->driver_ride_radius_request)) {
                $remain_ride_radius_slot = json_decode($configuration->driver_ride_radius_request, true);
            } else {
                $remain_ride_radius_slot = array();
            }

            $req_parameter = [
                'area' => $request->area,
                'segment_id' => $request->segment_id,
                'latitude' => $request->pickup_latitude,
                'longitude' => $request->pickup_longitude,
                'distance' => !empty($remain_ride_radius_slot) ? $remain_ride_radius_slot[0] : $configuration->normal_ride_now_radius,
                'limit' => $configuration->normal_ride_now_request_driver,
                'service_type' => $request->service_type,
                'vehicle_type' => $request->vehicle_type,
                'drop_lat' => $drop_lat,
                'drop_long' => $drop_long,
                'user_gender' => $request->user('api')->user_gender
            ];

            // get nearest drivers
            $drivers = Driver::GetNearestDriver($req_parameter);
            if(is_array($remain_ride_radius_slot) && ($remain_ride_radius_slot[1] != null) || ($remain_ride_radius_slot[2] != null) && empty($drivers)){
                $req_parameter['distance'] = $remain_ride_radius_slot[1];
                $drivers = Driver::GetNearestDriver($req_parameter);
                if(empty($drivers)){
                    $req_parameter['distance'] = $remain_ride_radius_slot[2];
                    $drivers = Driver::GetNearestDriver($req_parameter);
                }
            }

            if (empty($drivers) || $drivers->count() == 0) {
                if ($config->no_driver_availabe_enable == 1) {
                    $areaDetails = CountryArea::select('id', 'auto_upgradetion')->find($request->area);
                    if (isset($areaDetails) && $areaDetails->count() > 0 && $areaDetails->auto_upgradetion == 1) {
                        $vehicleDetail = VehicleType::select('id', 'vehicleTypeRank')->find($request->vehicle_type);

                        $req_parameter['vehicleTypeRank'] = $vehicleDetail->vehicleTypeRank;
                        $drivers = Driver::GetNearestDriver($req_parameter);
                        if (empty($drivers)|| $drivers->count() == 0) {
                            $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 2);
                            throw new \Exception(trans("$string_file.no_driver_available"));
//                        return $this->failedResponse(trans("$string_file.no_driver_available"));
                        }
                        $auto_upgradetion = 1;
                    } else {
                        $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 2);
                        throw new \Exception(trans("$string_file.no_driver_available"));
                        //return $this->failedResponse(trans("$string_file.no_driver_available"));
                    }
                } else {
                    $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 2);
                    throw new \Exception(trans("$string_file.no_driver_available"));
//                return $this->failedResponse(trans("$string_file.no_driver_available"));
                }
            }
            $auto_upgradetion = isset($auto_upgradetion) ? $auto_upgradetion : 2;
            $from = $request->pickup_latitude . "," . $request->pickup_longitude;
            $current_latitude = !empty($drivers[0]) ? $drivers[0]->current_latitude : '';
            $current_longitude = !empty($drivers[0]) ? $drivers[0]->current_longitude : '';
            $driverLatLong = $current_latitude . "," . $current_longitude;
            $units = (CountryArea::find($request->area)->Country['distance_unit'] == 1) ? 'metric' : 'imperial';
            $nearDriver = DistanceController::DistanceAndTime($from, $driverLatLong, $configuration->google_key, $units);
            $estimate_driver_distnace = $nearDriver['distance'];
            $estimate_driver_time = $nearDriver['time'];

            $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $configuration->google_key, $units,$string_file);
            $to = "";
            $lastLocation = "";
            if (!empty($drop_locationArray)) {
                $lastLocation = $newBookingData->wayPoints($drop_locationArray);
                $to = $lastLocation['last_location']['drop_latitude'] . "," . $lastLocation['last_location']['drop_longitude'];
            }
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
                        'booking_id' => 0,
                        'user_id' => $request->user('api')->id,
//                        'booking_time' => date('H:i'),
                        'outstanding_amount' => $outstanding_amount,
                        'units' => CountryArea::find($request->area)->Country['distance_unit'],
                        'from' => $from,
                        'to' => $to,
                    ]);
                    $amount = $merchant->FinalAmountCal($fare['amount'], $merchant_id);
                    $bill_details = json_encode($fare['bill_details']);
                    break;
                case "3":
                    // @Bhuvanesh
                    // In case of Input by driver, all parameters amount will be 0, and will be calculate at the end of booking. - booking_close api.
                    $estimatePrice = new PriceController();
                    $fare = $estimatePrice->BillAmount([
                        'price_card_id' => $pricecards->id,
                        'merchant_id' => $merchant_id,
                        'distance' => $distanceSmall,
                        'time' => $timeSmall,
                        'booking_id' => 0,
                        'user_id' => $request->user('api')->id,
//                        'booking_time' => date('H:i'),
                        'outstanding_amount' => $outstanding_amount,
                        'units' => CountryArea::find($request->area)->Country['distance_unit'],
                        'from' => $from,
                        'to' => $to,
                    ]);
                    $amount = trans('api.message62');
                    $bill_details = json_encode($fare['bill_details']);
                    break;
            }

            $rideData = array(
                'distance' => $distance,
                'bill_details' => $bill_details,
                'time' => $time,
                'amount' => $amount,
                'estimate_driver_distnace' => $estimate_driver_distnace,
                'estimate_driver_time' => $estimate_driver_time,
                'estimate_distance' => $distance,
                'estimate_time' => $time,
                'auto_upgradetion' => $auto_upgradetion
            );
            $result = $newBookingData->CreateCheckout($request, $request->user('api')->id, $merchant_id, $pricecards->id, $image, $rideData, $lastLocation, $request->user('api')->corporate_id);
            return ['message'=>trans("$string_file.ready_for_ride"),'data'=>$result];

        }catch(\Exception $e)
        {
          throw new \Exception($e->getMessage());
        }
    }

    public function LaterBookingCheckout($request)
    {

        try{
            $user = $request->user('api');
            $merchant_id = $user->merchant_id;
            $string_file = $this->getStringFile(NULL,$user->Merchant);
            $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
//
            $pricecards = PriceCard::where([['status', '=', 1], ['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $request->vehicle_type]])->first();
            $newBookingData = new BookingDataController();
            if (empty($pricecards)) {
                $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 1);
                throw new \Exception(trans("$string_file.no_price_card_for_area"));
//            return $this->failedResponse(trans("$string_file.no_price_card_for_area"));
            }
            $from = $request->pickup_latitude . "," . $request->pickup_longitude;
            $drop_locationArray = json_decode($request->drop_location, true);
            $units = (CountryArea::find($request->area)->Country['distance_unit'] == 1) ? 'metric' : 'imperial';
            $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $configuration->google_key, $units,$string_file);

            // $lastLocation = $newBookingData->wayPoints($drop_locationArray);
            // $to = $lastLocation['last_location']['drop_latitude'] . "," . $lastLocation['last_location']['drop_longitude'];
            $to = "";
            $lastLocation = "";
            if (!empty($drop_locationArray)) {
                $lastLocation = $newBookingData->wayPoints($drop_locationArray);
                $to = $lastLocation['last_location']['drop_latitude'] . "," . $lastLocation['last_location']['drop_longitude'];
            }
            $time = $googleArray['total_time_text'];
            $timeSmall = $googleArray['total_time_minutes'];
            $distance = $googleArray['total_distance_text'];
            $distanceSmall = $googleArray['total_distance'];
            if(!empty($drop_locationArray) && count($drop_locationArray) > 1){
                $distance_unit = ($units == 'metric') ? ' km' : ' m';
                $distance = $googleArray['total_distance_text'].$distance_unit;
            }
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
                        'booking_id' => 0,
                        'user_id' => $request->user('api')->id,
                        'booking_time' => $request->later_time,
                        'booking_date' => $request->later_date,
                        'outstanding_amount' => $outstanding_amount,
                        'units' => CountryArea::find($request->area)->Country['distance_unit'],
                        'from' => $from,
                        'to' => $to,
                    ]);
                    $amount = $merchant->FinalAmountCal($fare['amount'], $merchant_id);
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
                'estimate_driver_distnace' => '',
                'estimate_driver_time' => '',
                'estimate_distance' => $distance,
                'estimate_time' => $time,
                'auto_upgradetion' => 2
            );
            $result = $newBookingData->CreateCheckout($request, $request->user('api')->id, $merchant_id, $pricecards->id, $image, $rideData, $lastLocation, $request->user('api')->corporate_id);

            return ['message' => trans("$string_file.ready_for_ride"), 'data' => $result];
        }
        catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    public function currentBookingAssign($checkOut)
    {
        try {
            $bookingData = new BookingDataController();
            $result = $bookingData->sendRequestToNextDrivers($checkOut->id,2);
            return $result;
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
            unset($Bookingdata['user']);
            unset($Bookingdata['id']);
            unset($Bookingdata['created_at']);
            unset($Bookingdata['updated_at']);
            unset($Bookingdata['service_type']);
            $Bookingdata['booking_timestamp'] = time();
            $Bookingdata['booking_status'] = 1001;
            $Bookingdata['insurnce'] = request()->insurnce;
            $booking = Booking::create($Bookingdata);
            $merchant_id = $checkOut->merchant_id;
            $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
            if ($configuration->normal_ride_later_request_type == 1) {
//            $findDriver = new FindDriverController();
                $drivers = Driver::GetNearestDriver([
                    'area'=>$checkOut->country_area_id,
                    'segment_id'=>$checkOut->segment_id,
                    'latitude'=>$checkOut->pickup_latitude,
                    'longitude'=>$checkOut->pickup_longitude,
                    'distance'=>$configuration->normal_ride_later_radius,
                    'limit'=>$configuration->normal_ride_later_request_driver,
                    'service_type'=>$checkOut->service_type_id,
                    'vehicle_type'=>$checkOut->vehicle_type_id,
                    'payment_method_id'=>$checkOut->payment_method_id,
                    'estimate_bill'=>$checkOut->estimate_bill,
                    'user_gender'=>$user_gender,
                ]);
                if (!empty($drivers) && $drivers->count() > 0) {
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
