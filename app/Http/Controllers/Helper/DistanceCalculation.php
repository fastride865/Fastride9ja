<?php
/**
 * Created by PhpStorm.
 * User: aamirbrar
 * Date: 2019-01-24
 * Time: 10:56
 */

namespace App\Http\Controllers\Helper;


use App\Models\DistanceSetting;

class DistanceCalculation
{
    public function distance($start_lat_long, $end_lat_long, $pick, $drop, $latlong, $merchant_id, $key,$calling_from = "",$string_file ="")
    {
        $newArray = json_decode($latlong, true);
        $settings = DistanceSetting::where([['merchant_id', '=', $merchant_id]])->oldest()->first();
        if (!empty($settings) && !empty($newArray) && count($newArray) > 2) {
            $distance_methods = json_decode($settings->distance_methods, true);
            foreach ($distance_methods as $value) {
                $method_id = $value['method_id'];
                switch ($method_id) {
                    case "1":
                        $response = $this->SnapToRoadWayPoint($newArray, $value['last_timestamp_difference'], $value['maximum_timestamp_difference'], $value['min_speed'], $value['max_speed'], $key);
                        if ($response != false) {
                            return $response;
                        }
                        break;
                    case "2":
                        $response = $this->SnapToRoadAerial($newArray, $value['last_timestamp_difference'], $value['maximum_timestamp_difference'], $value['min_speed'], $value['max_speed'], $key);
                        if ($response != false) {
                            return $response;
                        }
                        break;
                    case "3":
                        $response = $this->OnlyAerial($newArray, $value['last_timestamp_difference'], $value['maximum_timestamp_difference'], $value['min_speed'], $value['max_speed']);
                        if ($response != false) {
                            return $response;
                        }
                        break;
                    case "5":
                        $distance = GoogleController::GoogleShortestPathDistance($start_lat_long, $end_lat_long, $key,'metric',$calling_from,$string_file);
                        if ($distance != false) {
                            return $distance;
                        }
                        break;
                    case "6":
                        $distance = GoogleController::GoogleShortestPathDistance($pick, $drop, $key,'metric',$calling_from,$string_file);
                        if ($distance != false) {
                            return $distance;
                        }
                        break;
                }
            }
        }
        $distance = GoogleController::GoogleShortestPathDistance($start_lat_long, $end_lat_long, $key,'metric',$calling_from);
        return $distance;
    }

    public function OnlyAerial($newArray, $last_timestamp_difference, $maximum_timestamp_difference, $min_speed, $max_speed)
    {

//        if (!empty($maximum_timestamp_difference)) {
//            $timediffernce = $this->CheckTimeDiffernce($newArray, $maximum_timestamp_difference);
//            if ($timediffernce == false) {
//                return false;
//            }
//        }

        $distance = $this->Aerial($newArray);
        if ($distance == false) {
            return false;
        }
        if (!empty($min_speed) && !empty($max_speed) && !is_null($min_speed) && !is_null($max_speed)) {
            $time = 10;
            $speed = $distance / $time;
            if ($speed < $min_speed || $speed > $max_speed) {
                return false;
            }
        }
        return $distance;
    }

    public function SnapToRoadAerial($newArray, $last_timestamp_difference, $maximum_timestamp_difference, $min_speed, $max_speed, $key)
    {
//        if (!empty($maximum_timestamp_difference)) {
//            $timediffernce = $this->CheckTimeDiffernce($newArray, $maximum_timestamp_difference);
//            if ($timediffernce == false) {
//                return false;
//            }
//        }
        $googleServices = new GoogleController();
        $snapToRoad = $googleServices->SnapToRoad($newArray, $key);
        if ($snapToRoad == false) {
            return false;
        } else {
            foreach ($snapToRoad as $value) {
                $latlong = explode(',', $value);
                $AerialArray[] = array('latitude' => $latlong[0], 'longitude' => $latlong[1]);
            }
        }
        $distance = $this->Aerial($AerialArray);
        if ($distance == false) {
            return false;
        }
        if (!empty($min_speed) && !empty($max_speed) && !is_null($min_speed) && !is_null($max_speed)) {
            $time = 10;
            $speed = $distance / $time;
            if ($speed < $min_speed || $speed > $max_speed) {
                return false;
            }
        }
        return $distance;
    }

    public function Aerial($snapToRoad)
    {
        $dist1 = 0;
        if(count($snapToRoad) > 2)
        {
            for ($i = 0; $i < (count($snapToRoad) - 1); $i++) {
            $driver_lat_first = $snapToRoad[$i]['latitude'];
            $driver_long_first = $snapToRoad[$i]['longitude'];
            $driver_lat_second = $snapToRoad[$i + 1]['latitude'];
            $driver_long_second = $snapToRoad[$i + 1]['longitude'];
            $theta = $driver_long_first - $driver_long_second;
            $dist = sin(deg2rad($driver_lat_first)) * sin(deg2rad($driver_lat_second)) + cos(deg2rad($driver_lat_first)) * cos(deg2rad($driver_lat_second)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $km = $miles * 1.609344;
            $km = round($km, 2);
            $dist2[] = $km;
        }
        $dist1 = round(array_sum($dist2), 2);
        }
        return $dist1;
    }

    public function SnapToRoadWayPoint($newArray, $last_timestamp_difference, $maximum_timestamp_difference, $min_speed, $max_speed, $key)
    {

//        if (!empty($maximum_timestamp_difference) && !is_null($maximum_timestamp_difference)) {
//            $timediffernce = $this->CheckTimeDiffernce($newArray, $maximum_timestamp_difference);
//            if ($timediffernce == false) {
//                return false;
//            }
//        }
        $googleServices = new GoogleController();
        $snapToRoad = $googleServices->SnapToRoad($newArray, $key);
        if ($snapToRoad == false) {
            return false;
        }
        $distance = $googleServices->WayPointDistance($snapToRoad, $key);
        if ($distance == false) {
            return false;
        }
        if (!empty($min_speed) && !empty($max_speed) && !is_null($min_speed) && !is_null($max_speed)) {
            $time = 10;
            $speed = $distance / $time;
            if ($speed < $min_speed || $speed > $max_speed) {
                return false;
            }
        }
        return $distance;
    }

    public function CheckTimeDiffernce($newArray, $maximum_timestamp_difference)
    {
        foreach ($newArray as $value) {
            $timeStamp = $value['timeStamp'];
            $time = true;
            if ($timeStamp > $maximum_timestamp_difference) {
                return false;
            }
        }
        return $time;
    }
}