<?php

namespace App\Http\Controllers\Helper;


use App\Models\BookingConfiguration;
use App\Models\CountryArea;
use App\Models\Driver;
use App\Models\PriceCard;
use App\Models\VehicleType;
use App\Traits\AreaTrait;
use App\Http\Controllers\Helper\ExtraCharges;

class Estimate
{
    use AreaTrait;
    public function Eta($merchant_id, $service_type, $area, $pickup_latitude, $pickup_longitude, $vehicle_type = null,$segment_id = NULL)
    {
        $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();

        if (!empty($configuration->driver_ride_radius_request)) {
            $ride_radius = json_decode($configuration->driver_ride_radius_request, true);
            $remain_ride_radius_slot = $ride_radius;
        } else {
            $remain_ride_radius_slot = array();
        }

        switch ($service_type) {
            case 1:
                $configuration->normal_ride_now_radius ? $configuration->normal_ride_now_radius : 0;
                $configuration->normal_ride_now_request_driver ? $configuration->normal_ride_now_request_driver : 0;
                break;
            case 2:
                $configuration->normal_ride_now_radius = $configuration->rental_ride_now_radius ? $configuration->rental_ride_now_radius : 0;
                $configuration->normal_ride_now_request_driver = $configuration->rental_ride_now_request_driver ? $configuration->rental_ride_now_request_driver : 0;
                break;
            case 3:
                $configuration->normal_ride_now_radius = $configuration->transfer_ride_now_radius ? $configuration->transfer_ride_now_radius : 0;
                $configuration->normal_ride_now_request_driver = $configuration->transfer_ride_now_request_driver ? $configuration->transfer_ride_now_request_driver : 0;
                break;
            case 4:
                $configuration->normal_ride_now_radius = $configuration->outstation_ride_now_radius ? $configuration->outstation_ride_now_radius : 0;
                $configuration->normal_ride_now_request_driver = $configuration->outstation_ride_now_request_driver ? $configuration->outstation_ride_now_request_driver : 0;
                break;
        }
        $eta = "";
        $user_gender = NULL;
        $distance = !empty($remain_ride_radius_slot) ? $remain_ride_radius_slot[0] : $configuration->normal_ride_now_radius;
        if ($service_type != 5) {
            $findDriver = new FindDriverController();
//            $drivers = $findDriver->GetAllNearestDriver($area, $pickup_latitude, $pickup_longitude, $distance, $configuration->normal_ride_now_request_driver, $vehicle_type, $service_type, '', '', $user_gender = null, $configuration->driver_request_timeout,$segment_id);
            $drivers = Driver::GetNearestDriver([
                'area'=>$area,
                'segment_id'=>$segment_id,
                'latitude'=>$pickup_latitude,
                'longitude'=>$pickup_longitude,
                'distance'=>$distance,
                'limit'=>$configuration->normal_ride_now_request_driver,
                'service_type'=>$service_type,
                'vehicle_type'=>$vehicle_type,
                'user_gender'=>$user_gender,
            ]);

            if (empty($drivers)) {
                $areaDetails = CountryArea::select('id', 'auto_upgradetion')->find($area);
                if ($areaDetails->auto_upgradetion != 1) {
                    return $eta;
                }
                $vehicleDetail = VehicleType::select('id', 'vehicleTypeRank')->find($vehicle_type);
                if(!empty($vehicleDetail) && $vehicleDetail->count() > 0){
                    $drivers = $findDriver->GetAutoUpgradeDriver($area, $pickup_latitude, $pickup_longitude, $configuration->normal_ride_now_radius, $configuration->normal_ride_now_request_driver, $service_type, $vehicleDetail->vehicleTypeRank, $user_gender, $configuration->driver_request_timeout);
                }
                //$drivers = $findDriver->GetAutoUpgradeDriver($vehicle_type, $pickup_latitude, $pickup_longitude, $configuration->normal_ride_now_radius, $configuration->normal_ride_now_request_driver, $service_type, $vehicleDetail->vehicleTypeRank, $user_gender, $configuration->driver_request_timeout);
            }
        }
        if ($service_type == 5) {
            if (empty($configuration->pool_radius)) {
                return $eta;
            }
            $pricecards = PriceCard::where([['country_area_id', '=', $area], ['service_type_id', '=', $service_type]])->get();
            if (empty($pricecards->toArray())) {
                return $eta;
            }
            $vehicle_type_id = array_pluck($pricecards, 'vehicle_type_id');
            $findDriver = new FindDriverController();
            $drivers = $findDriver->checkPoolDriver($area, $pickup_latitude, $pickup_longitude, $configuration->pool_radius, $configuration->pool_now_request_driver, 1, $vehicle_type_id, 1, $configuration->driver_request_timeout);
            if (empty($drivers)) {
                return $eta;
            }
        }
        if(empty($drivers) || $drivers->count() == 0)
        {
            return $eta;
        }
        $from = $pickup_latitude . "," . $pickup_longitude;
        $current_latitude = isset($drivers['0']) ? $drivers['0']->current_latitude : "";
        $current_longitude = isset($drivers['0']) ? $drivers['0']->current_longitude : "";
        $driverLatLong = $current_latitude . "," . $current_longitude;
        $nearDriver = DistanceController::DistanceAndTime($from, $driverLatLong, $configuration->google_key);
        // $estimate_driver_distnace = $nearDriver['distance'];
        $estimate_driver_time = $nearDriver['time'];
        return $estimate_driver_time;
    }

//    public function Estimate($merchant_id, $service_type, $area, $pickup_latitude, $pickup_longitude, $drop_location = null, $vehicle_type = null, $currency = null,$package_id= null)
//    {
////        p($package_id);
//        $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
//        $amount = $currency . " 0.00";
//        if ($service_type != 5) {
//            $pricecards = PriceCard::where([['country_area_id', '=', $area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $service_type], ['vehicle_type_id', '=', $vehicle_type]])
//                ->where(function($q) use ($package_id){
//                    if(!empty($package_id)){
//                        $q->where('package_id',$package_id);
//                    }
//                })
//                ->first();
////            p($pricecards->toArray());
//            if (empty($pricecards)) {
//                return $amount;
//            }
//        }
//        if ($service_type == 5) {
//            $pricecards = PriceCard::where([['country_area_id', '=', $area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $service_type]])->first();
//            if (empty($pricecards)) {
//                return $amount;
//            }
//        }
//        $drop_locationArray = [];
//        if (!empty($drop_location)) {
//            //$drop_locationArray[] = array('drop_latitude' => $drop_latitude, 'drop_longitude' => $drop_longitude);
//            $drop_locationArray = json_decode($drop_location, true);
//        }
//        $googleArray = GoogleController::GoogleStaticImageAndDistance($pickup_latitude, $pickup_longitude, $drop_locationArray, $configuration->google_key);
//        if (empty($googleArray)) {
//            return $amount;
//        }
//        $timeSmall = $googleArray['total_time_minutes'];
//        $distanceSmall = $googleArray['total_distance'];
//        $merchant = new Merchant();
//        switch ($pricecards->pricing_type) {
//            case "1":
//            case "2":
//                $estimatePrice = new PriceController();
//                $fare = $estimatePrice->BillAmount([
//                    'price_card_id' => $pricecards->id,
//                    'merchant_id' => $merchant_id,
//                    'distance' => $distanceSmall,
//                    'time' => $timeSmall,
//                    'booking_id' => NULL,
//                    'user_id' => isset(request()->user('api')->id) ? request()->user('api') : NULL,
//                    //'user_id' => request()->user('api')->id, // commented for static website
//                    'booking_time' => date('H:i'),
//                    'units' => CountryArea::find($area)->Country['distance_unit']
//                ]);
//                $amount = $currency . " " . $merchant->FinalAmountCal($fare['amount'],$merchant_id);
//                break;
//            case "3":
//                $amount = trans('api.message62');;
//                break;
//        }
//        return $amount;
//    }

    public function Estimate($merchant_id, $service_type, $area, $pickup_latitude, $pickup_longitude, $drop_location = null, $vehicle_type = null, $currency = null,$package_id= null,$googleArray = [],$drop_location_outside_area = null)
    {
        $amount = $currency . " 0.00";
        if ($service_type != 5) {
            $drop_lat = '';
            $drop_long = '';
            if(!empty($drop_location)){
                $drop_location_array = json_decode($drop_location, true);
                $drop_lat = isset($drop_location_array[0]['drop_latitude']) ? $drop_location_array[0]['drop_latitude'] : '';
                $drop_long = isset($drop_location_array[0]['drop_longitude']) ? $drop_location_array[0]['drop_longitude'] : '';
            }
            $merchant = \App\Models\Merchant::find($merchant_id);
            $countryArea = [];
            $dropCountryArea = [];
            $countryAreaGeofence = false;
            $dropCountryAreaGeofence = false;
            if(isset($merchant->Configuration->geofence_module) &&  $merchant->Configuration->geofence_module == 1 && $drop_lat != '' && $drop_long != ''){
                $countryArea = CountryArea::find($area);
                $countryAreaGeofence = ($countryArea->is_geofence == 1) ? true : false;
                $dropCountryArea = $this->checkGeofenceArea($drop_lat,$drop_long,'drop',$merchant_id);
                $dropCountryAreaGeofence = (!empty($dropCountryArea) && $dropCountryArea->is_geofence == 1) ? true : false;
            }

            if($countryAreaGeofence){
                $pricecards = PriceCard::where([['country_area_id', '=', $area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $service_type], ['vehicle_type_id', '=', $vehicle_type]])
                    ->where(function($q) use ($package_id){
                        if(!empty($package_id)){
                            $q->where('service_package_id',$package_id);
                        }
                    })
                    ->first();
            }elseif($dropCountryAreaGeofence){
                $pricecards = PriceCard::where([['country_area_id', '=', $dropCountryArea->id], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $service_type], ['vehicle_type_id', '=', $vehicle_type]])
                    ->where(function($q) use ($package_id){
                        if(!empty($package_id)){
                            $q->where('service_package_id',$package_id);
                        }
                    })
                    ->first();
                if(empty($pricecards)){
                    $pricecards = PriceCard::where([['country_area_id', '=', $area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $service_type], ['vehicle_type_id', '=', $vehicle_type]])
                    ->where(function($q) use ($package_id){
                        if(!empty($package_id)){
                            $q->where('service_package_id',$package_id);
                        }
                    })
                    ->first();
                }
            }else{
                $where = [['status', '=', 1],['country_area_id', '=', $area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $service_type], ['vehicle_type_id', '=', $vehicle_type]];
                if($merchant->Configuration->drop_outside_area == 1 && $merchant->Configuration->outside_area_ratecard == 1 && $drop_location_outside_area == 1){
                    array_push($where,['rate_card_scope','=',2]);
                }
                $pricecards = PriceCard::where($where)
                    ->where(function($q) use ($package_id){
                        if(!empty($package_id)){
                            $q->where('service_package_id',$package_id);
                        }
                    })
                    ->first();
            }
            if (empty($pricecards)) {
                return $amount;
            }
        }
        if ($service_type == 5) {
            $pricecards = PriceCard::where([['country_area_id', '=', $area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $service_type]])->first();
            if (empty($pricecards)) {
                return $amount;
            }
        }
//        $drop_locationArray = [];
//        if (!empty($drop_location)) {
//            //$drop_locationArray[] = array('drop_latitude' => $drop_latitude, 'drop_longitude' => $drop_longitude);
//            $drop_locationArray = json_decode($drop_location, true);
//        }
////        $googleArray = ;
        if (empty($googleArray)) {
            return $amount;
        }
        $timeSmall = $googleArray['total_time_minutes'];
        $distanceSmall = $googleArray['total_distance'];
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
                    'user_id' => isset(request()->user('api')->id) ? request()->user('api') : NULL,
                    //'user_id' => request()->user('api')->id, // commented for static website
//                    'booking_time' => date('H:i'),
                    'units' => CountryArea::find($area)->Country['distance_unit']
                ]);
                $amount = $currency . " " . $merchant->FinalAmountCal($fare['amount'],$merchant_id);
                break;
            case "3":
                $amount = trans('api.message62');;
                break;
        }
        return $amount;
    }

}