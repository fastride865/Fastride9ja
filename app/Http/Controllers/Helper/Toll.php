<?php

namespace App\Http\Controllers\Helper;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;

class Toll
{
    use ApiResponseTrait,MerchantTrait;
    public function checkToll($tollapi, $from, $to, $waypoints, $key, $path = null)
    {
        $method = 1; // 1 - OriginDestination 2 - GPS Coordinates
        $wayPoint = "";
        if (!empty($waypoints) && $method == 1) {
            $waypoints = json_decode($waypoints, true);
            if (is_array($waypoints) && !empty($waypoints)) {
                foreach ($waypoints as $login) {
                    if(isset($login['drop_latitude']))
                    {
                    $combine[] = $login['drop_latitude'] . "," . $login['drop_longitude'];
                    }
                    else
                    {
                    $combine[] = $login['latitude'] . "," . $login['longitude'];
                    }
                }
                $wayPoint = implode("|", $combine);
            }
        }
        $tollPrice = "";
        switch ($tollapi) {
            case 1;
                if($method == 1){
                    $tollPrice = $this->tollguru($from, $to, $wayPoint, $key);
                }elseif($method == 2){
                    if(!empty($waypoints)){
                        $tollPrice = $this->tollguruGPS($path, $key, $waypoints);
                    }
                }
                break;
        }
        return $tollPrice;
    }

    public function tollguru($from, $to, $wayoints, $key)
    {
        $wayoints = $wayoints ? $wayoints : $from;
        $vehicletype = "2AxlesAuto";
        $city_id = "24";
        $currency = 'USD';
        $headers = array();
        $headers[] = 'X-Api-Key: ' . $key;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://dev.tollguru.com/v1/calc/gmaps');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n    \"from\": {\n      \"address\": \"$from\"\n    },\n    \"to\": {\n      \"address\": \"$to\"\n    },\n    \"waypoints\": [\n      { \"address\": \"$wayoints\" }\n    ],\n    \"vehicleType\": \"$vehicletype\",\n    \"fuelPrice\": \"3\",\n    \"fuelPriceCurrency\": \"$currency\",\n    \"fuelEfficiency\": {\n      \"city\": $city_id,\n      \"hwy\": 30,\n      \"units\": \"mpg\"\n    }\n  }");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);
        if (empty($result) || !array_key_exists('routes', $result)) {
            return [];
        }
        $istoll = $result['routes'][0]['summary']['hasTolls'];
        $cost = $istoll ? $result['routes'][0]['costs']['cash'] : "";
        $content = array("istoll" => $istoll, "cost" => $cost);
        return $content;
    }

    public function tollguruGPS($path, $key, $waypoints)
    {
        $headers = array();
        $headers[] = 'X-Api-Key: ' . $key;
        $headers[] = 'Content-Type: application/javascript';
        $url = "https://dev.tollguru.com/v1/calc/route/upload?vehicleType=2AxlesAuto";

        $curl = curl_init();
        $file = $this->create_csv($waypoints);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $file,
            CURLOPT_HTTPHEADER => $headers,
        ));
        $response = curl_exec($curl);
        $response = json_decode($response, true);
        curl_close($curl);
        if (empty($response) || !array_key_exists('route', $response)) {
            return [];
        }
        $istoll = $response['route']['hasTolls'];
        $cost = $istoll ? array_sum(array_column($response['route']['tolls'], 'tagCost')) : "";
        $content = array("istoll" => $istoll, "cost" => $cost);
        return $content;
    }

    public function create_csv($coordinates)
    {
        $file_name = 'xyz.csv';
        $columns = array('Latitude', 'Longitude', 'Timestamp', 'Speed_mph', 'Heading');
        $coordinates = json_decode($coordinates, true);

        $file = fopen($file_name, 'w');
        fputcsv($file, $columns);
        if(!empty($coordinates)){
            foreach($coordinates as $coordinate) {
                $timestamp = date('Y-m-d', $coordinate['timestamp']).'T'.date('H:s:i', $coordinate['timestamp']).'Z';
                fputcsv($file, array($coordinate['latitude'], $coordinate['longitude'], $timestamp,'',''));
            }
        }
        $file_content = file_get_contents($file_name);
        fclose($file);
        return $file_content;
    }

    // toll api for upgirl
    public function peajeTollApi(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
//            'toll_type' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
            // return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $booking = Booking::select('pickup_latitude','pickup_longitude','drop_latitude','drop_longitude','vehicle_type_id','merchant_id')->where('id',$request->booking_id)->first();
        $string_file = $this->getStringFile(NULL,$booking->Merchant);
        $pickup = "$booking->pickup_latitude,$booking->pickup_longitude";
        $drop = "$booking->drop_latitude,$booking->drop_longitude";
//        $pickup = "-35.4230842,-71.7186925";
//        $drop = "-33.04703699999998,-71.62998";
//        $token = "";
        $vehicle_type="car";
        $token = "eWOG8OdJ1vetJ7c4MLvrLCh5Kas2";
        $headers = array();
        $headers[] = 'Content-Type: application/javascript';
        $url = "https://us-central1-peaje-148206.cloudfunctions.net/calcularruta_api?token=".$token."&origin=".$pickup."&destination=".$drop."&car=".$vehicle_type;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            //CURLOPT_POSTFIELDS => $file,
            CURLOPT_HTTPHEADER => $headers,
        ));
        $response = curl_exec($curl);
        $decoded_response = json_decode($response, true);

        curl_close($curl);

        if (empty($decoded_response))
        {
            if(!empty($response));
            {
                return $this->failedResponse($response);
            }
            return $this->failedResponse(trans("$string_file.toll_api_response"));
        }
        else
        {
            $manula_toll = $decoded_response['costoTotalPeajes'];
            $online_toll = $decoded_response['costoTotalTags'];
            $toll_total_cost = $decoded_response['costoTotal'];
            $content = array("toll_cost" => "$toll_total_cost");
            return $this->successResponse(trans("$string_file.toll_amount"),$content);
        }
    }
}