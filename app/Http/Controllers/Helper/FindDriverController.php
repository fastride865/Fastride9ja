<?php

namespace App\Http\Controllers\Helper;

use App\Models\BookingRequestDriver;
use App\Models\Driver;
use App\Http\Controllers\Controller;
use App\Models\PoolRideList;
use App\Models\CountryArea;


class FindDriverController extends Controller
{
    public function getAllDriverAccordingGender($area = null, $latitude = null, $longitude = null, $distance = 1, $limit = 1, $vehicle_type = null, $service_type = null, $drop_lat = null, $drop_long = null, $user_gender = null, $ac_nonac = null, $driver_request_timeout = 60)
    {
//        $units = (CountryArea::find($area)->Country['distance_unit'] == 1) ? 1 : 2;
        $newDriverSerach = new Driver();
//        $drivers = $newDriverSerach->getNearestDriverGender($area, $pickup_latitude, $pickup_longitude, $distance, $number, $vehicle_type, $service_type, $user_gender, $units, $ac_nonac, $driver_request_timeout);

        $drivers = $newDriverSerach->GetNearestDriver([
            'area'=>$area,
            'latitude'=>$latitude,
            'longitude'=>$longitude,
            'distance'=>$distance,
            'limit'=>$limit,
            'service_type'=>$service_type,
            'vehicle_type'=>$vehicle_type,
            'user_gender'=>$user_gender,
            'ac_nonac'=>$ac_nonac,
        ]);
        if (empty($drivers)) {
            return [];
        }
        $homelocation_driver_ids = array_pluck($drivers->where('home_location_active', 1)->toArray(), 'driver_id');
        if (!empty($homelocation_driver_ids) && !empty($drop_lat) && !empty($drop_long)) {
            $nearHomelocation = Driver::GetHomeLocationsNearestToDropLocation($homelocation_driver_ids, $drop_lat, $drop_long, $distance, $limit);
            if (!empty($nearHomelocation->toArray())) {
//                $homelocation_id = array_pluck($drivers, 'driver_id');
//                $newArray = array_diff($homelocation_driver_ids, $homelocation_id);
//                $drivers = $drivers->whereIn('driver_id', $newArray);

//                 above 3 lines commented by @amba.[gender case ride]
                $homelocation_id = array_pluck($nearHomelocation, 'driver_id');
                $drivers = $drivers->whereIn('driver_id', $homelocation_id);
                if (empty($drivers->toArray())) {
                    return [];
                }
            } else {
                $drivers = $drivers->whereIn('driver_id', $homelocation_driver_ids);
                if (empty($drivers->toArray())) {
                    return [];
                }
            }
        }
        return $drivers;
    }

    public function GetAllNearestDriver($area = null, $latitude = null, $longitude = null, $distance = 1, $limit = 1, $vehicle_type = null, $service_type = null, $drop_lat = null, $drop_long = null, $user_gender = null, $driver_request_timeout = 60,$segment_id = NULL)
    {
        $drivers = Driver::GetNearestDriver([
            'area'=>$area,
            'segment_id'=>$segment_id,
            'latitude'=>$latitude,
            'longitude'=>$longitude,
            'distance'=>$distance,
            'limit'=>$limit,
            'service_type'=>$service_type,
            'vehicle_type'=>$vehicle_type,
            'user_gender'=>$user_gender,
        ]);

        if (empty($drivers)) {
            return [];
        }
        return $drivers;
    }


    public function GetAutoUpgradeDriver($area = null, $latitude = null, $longitude = null, $distance = 1, $limit = 1, $service_type = null, $rank = 1, $user_gender = null, $driver_request_timeout = 60)
    {
//        $drivers = Driver::GetAllNearestDriver($area, $pickup_latitude, $pickup_longitude, $distance, $number, $service_type, $rank, $user_gender, $driver_request_timeout);
         $drivers = Driver::GetNearestDriver([
            'area'=>$area,
            'latitude'=>$latitude,
            'longitude'=>$longitude,
            'distance'=>$distance,
            'limit'=>$limit,
            'service_type'=>$service_type,
            'rank'=>$rank,
            'user_gender'=>$user_gender,
        ]);
        if (empty($drivers)) {
            return [];
        }
        return $drivers;
    }

    public function AssignRequest($drivers, $booking_id = null,$order_id = null,$handyman_order_id = null)
    {
        $driverRequest = array();
        foreach ($drivers as $value) {
            $driverRequest[] = array('booking_id' => $booking_id,'order_id' => $order_id,'handyman_order_id' => $handyman_order_id,
                'driver_id' => $value->driver_id ? $value->driver_id : $value->id,
                'distance_from_pickup' => $value->distance ? $value->distance : 0, 'request_status' => 1,
                'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s"));
            $driver = $value->driver_id ? Driver::find($value->driver_id) : Driver::find($value->id);
            $driver->last_ride_request_timestamp = date("Y-m-d H:i:s");
            $driver->save();
        }
        BookingRequestDriver::insert($driverRequest);
    }

    public function checkPoolDriver($area, $latitude, $longitude, $distance, $limit, $riders_num, $vehicle_type_id, $user_gender = null, $driver_request_timeout = 60)
    {

//        $drivers = Driver::GetNearestPoolDrivers($area, $latitude, $longitude, $distance, $limit, $riders_num, $vehicle_type_id, $user_gender, $driver_request_timeout);
        // call common function to get driver
         $drivers = Driver::GetNearestDriver([
            'area'=>$area,
            'latitude'=>$latitude,
            'longitude'=>$longitude,
            'distance'=>$distance,
            'limit'=>$limit,
            'riders_num'=>$riders_num,
            // 'vehicle_type'=>$vehicle_type_id,
            'service_type'=>5,
        ]);
        if (empty($drivers)) {
            return [];
        }
        return $drivers;
    }

    public function getPoolDriver($area, $latitude, $drop_lat, $drop_long, $longitude, $distance, $limit, $riders_num, $vehicle_type_id, $dropdistance, $user_gender = null, $driver_request_timeout = 60)
    {
//        $drivers = Driver::GetNearestPoolDrivers($area, $latitude, $longitude, $distance, $limit, $riders_num, $vehicle_type_id, $user_gender, $driver_request_timeout);
           $drivers = Driver::GetNearestDriver([
            'area'=>$area,
            'latitude'=>$latitude,
            'longitude'=>$longitude,
            'distance'=>$distance,
            'limit'=>$limit,
            'riders_num'=>$riders_num,
            // 'vehicle_type'=>$vehicle_type_id,
            'service_type'=>5,
            'dropdistance'=>$dropdistance,
        ]);

        if (empty($drivers)) {
            return [];
        }
        $drivers_pass = array();
        $drivers_fail = array();
        foreach ($drivers as $driver_data):
            $return_bool = $this->CheckForCurrentRiders($driver_data->driver_id, $drop_lat, $drop_long, $dropdistance);
            if ($return_bool):
                $drivers_pass[] = $driver_data;
            else:
                $drivers_fail[] = $driver_data;
            endif;
        endforeach;
        if (!empty($drivers_pass)):
            return $drivers_pass;
        else:
            return $drivers_fail;
        endif;
    }

    public function CheckForCurrentRiders($driver_id = null, $droplat = null, $droplong = null, $distance = null) //distance for area near to drop location of previous Riders
    {
        $data = PoolRideList::where([['driver_id', '=', $driver_id], ['dropped', '=', 0]])->oldest()->first(); // Data of Oldest Rider Currently Inside the Cab

        if (!empty($data)):  // When Driver has some Riders already, Check for New Drop Point Nearest To Drop Location
            $return_data = Driver::CheckNewDropPointNearestToDropLocation($data->id, $droplat, $droplong, $distance);
            if (!$return_data->isEmpty()):
                $passed_driver = $driver_id;
            endif;

        else:
            $passed_driver = $driver_id;
        endif;
        if (isset($passed_driver)):
            return true;
        else:
            return false;
        endif;

    }
/**
 * Code merged by @Amba
 * Code of delivey booking
*/
    public function GetAllNearestDriverDelivery($area = null, $pickup_latitude = null, $pickup_longitude = null, $distance = 1, $number = 1, $vehicle_type = null, $service_type = null, $drop_lat = null, $drop_long = null, $user_gender = null, $driver_request_timeout = 60)
    {
        $drivers = Driver::GetNearestDriverDelivery($area, $pickup_latitude, $pickup_longitude, $distance, $number, $vehicle_type, $service_type, $user_gender, $driver_request_timeout);
        if (empty($drivers->toArray())) {
            return [];
        }
        $homelocation_driver_ids = array_pluck($drivers->where('home_location_active', 1)->toArray(), 'driver_id');
        if (!empty($homelocation_driver_ids) && !empty($drop_lat) && !empty($drop_long)) {
            $nearHomelocation = Driver::GetHomeLocationsNearestToDropLocation($homelocation_driver_ids, $drop_lat, $drop_long, $distance, $number);
            if (!empty($nearHomelocation->toArray())) {
                $homelocation_id = array_pluck($drivers, 'driver_id');
                $newArray = array_diff($homelocation_driver_ids, $homelocation_id);
                $drivers = $drivers->whereIn('driver_id', $newArray);
                if (empty($drivers->toArray())) {
                    return [];
                }
            } else {
                $drivers = $drivers->whereIn('driver_id', $homelocation_driver_ids);
                if (empty($drivers->toArray())) {
                    return [];
                }
            }
        }
        return $drivers;
    }
}
