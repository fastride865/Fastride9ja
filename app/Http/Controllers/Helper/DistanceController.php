<?php

namespace App\Http\Controllers\Helper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DistanceController extends Controller
{
    public function AddSeries($driver_id,$latitude,$longitude)
    {

    }

//    public static function GoogleDistance($from, $to, $key, $units = 'metric')
//    {
//        $routes = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/directions/json?units='.$units.'&origin=' . $from . '&destination=' . $to . '&alternatives=true&key=' . $key))->routes;
//        usort($routes, create_function('$a,$b', 'return intval($a->legs[0]->distance->value) - intval($b->legs[0]->distance->value);'));
//        return $dist_inval = $routes[0]->legs[0]->distance->value;
//    }

    public static function DistanceAndTime($from, $to, $key, $units = 'metric')
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://maps.googleapis.com/maps/api/directions/json?units=%22.$units.%22&origin=$from&destination=$to&mode=driving&key=$key",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
                CURLOPT_HTTPHEADER => array(
                    "Postman-Token: a2bb7a65-3762-4af6-8889-ae151d4940c9",
                    "cache-control: no-cache"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            // $data = file_get_contents("https://maps.googleapis.com/maps/api/directions/json?units=".$units."&origin=$from&destination=$to&mode=driving&key=$key");
            $data = json_decode($response, true);
            $status = $data['status'];
            if ($status != "OK") {
                $message = !empty($data['error_message']) ?  $data['error_message'] : trans("common.google_key_not_working");
                throw new \Exception($message);
//                return array('time' => "", 'distance' => "");
            } else {
                $time = $data['routes'][0]['legs'][0]['duration']['text'];
                $distance = $data['routes'][0]['legs'][0]['distance']['text'];
                return array('time' => $time, 'distance' => $distance);
            }

        }catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }

    }

    public function AerialDistanceBetweenTwoPoints($from_lat = null, $from_long = null, $to_lat = null, $to_long = null)
    {
        $theta = $from_long - $to_long;
        $dist = sin(deg2rad($from_lat)) * sin(deg2rad($to_lat)) + cos(deg2rad($from_lat)) * cos(deg2rad($to_lat)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        return round($dist * 60 * 1.1515, 2);
    }
}
