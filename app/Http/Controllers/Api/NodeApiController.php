<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\FindDriverController;
use App\Models\Booking;
use App\Models\BookingConfiguration;

use App\Models\BookingCoordinate;
use App\Models\BookingRequestDriver;
use App\Models\Configuration;
use App\Models\CountryArea;
use App\Models\PriceCard;
use App\Models\Driver;
use DB;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class NodeApiController extends Controller
{
    use ImageTrait;
  // node api for get driver
    public function userHomeScreenDriver(Request $request)
    {

        $merchant_id = $request->input('merchant_id');
        $service_type_id = $request->input('service_type');
        $vehicle_type_id = $request->input('vehicle_type');
        $country_area_id = $request->input('area');
        if(empty($merchant_id) && empty($service_type_id) && empty($country_area_id))
        {
            return 'mandatory fields can not be empty';
        }
        $driver_area_notification = Configuration::select('driver_area_notification')->where([['merchant_id', '=', $merchant_id]])->first();
        $are_config = $driver_area_notification->driver_area_notification;
        $config = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();

        $drivers = Driver::select('drivers.id')
            ->join('driver_vehicles', 'drivers.id', '=', 'driver_vehicles.driver_id')
            ->join('driver_vehicle_service_type', 'driver_vehicles.id', '=', 'driver_vehicle_service_type.driver_vehicle_id')
            ->where('drivers.merchant_id',$merchant_id)
            ->where('drivers.free_busy',2)
            ->where('drivers.player_id', "!=", NULL)
            ->where('drivers.online_offline',1)
            ->where('drivers.login_logout',1)
            ->where('driver_vehicles.vehicle_active_status',1)
            ->where('driver_vehicle_service_type.service_type_id',$service_type_id)
            ->where(function($q) use ($vehicle_type_id){
            if(!empty($vehicle_type_id))
                {
                  $q->where('driver_vehicles.vehicle_type_id',$vehicle_type_id);
                }
                })
            ->where(function($q) use ($are_config,$country_area_id){
                if ($are_config != 1) {
                    // if  $driver_area_notification->driver_area_notification is disabled
                    $q->where('drivers.country_area_id',$country_area_id);
                }
            })
            ->where(function($q) use ($service_type_id,$config){
                if ($service_type_id == 5) {
                    $q->where([['drivers.status_for_pool', '!=', 2008],
                        ['drivers.pool_ride_active', '=', 1],
                        ['drivers.avail_seats', '>=', $config->number_of_driver_user_map],]);
                }
                else
                {
                    $current_date = date('Y-m-d H:i:s');
                    $q->whereRaw('( is_suspended is NULL or is_suspended < "'.$current_date.'" )');
                }
            })
//            ->toSql();
            ->get()->toArray();
//           $drivers = Driver::select('id')->get()->toArray();
           $arr_drivers = array_column($drivers,'id');
//        if (empty($arr_drivers)) {
            return response()->json($arr_drivers);
//        }
    }


    public function Drivers(Request $request)
    {
        $merchant_id = $request->input('merchant_id');
        $service_type = $request->input('service_type');
        $vehicle_type = $request->input('vehicle_type');
        $area = $request->input('area');
//        $merchant_id = $request->input('merchant_id');
//        $validator = Validator::make($request->all(), [
//            'area' => [
//                'required',
//                'integer',
//                Rule::exists('country_areas', 'id')->where(function ($query) use ($merchant_id) {
//                    $query->where('merchant_id', $merchant_id);
//                }),
//            ],
//            'service_type' => 'required|integer|exists:service_types,id',
//            'vehicle_type' => 'required_if:service_type,1,2,3,4',
//            'distance' => 'required|integer',
//            'latitude' => 'required|string',
//            'longitude' => 'required|string',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
//        }
//        $vehicle = VehicleType::find($request->vehicle_type);
//        if(isset($vehicle->map_icon)){
//            $vehicle->map_icon = explode_image_path($vehicle->map_icon);
//        }
        $config = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        switch ($request->input('service_type')) {
            case "1":
                $distance = $config->normal_ride_now_radius;
                break;
            case "2":
                $distance = $config->rental_ride_now_radius;
                break;
            case "3":
                $distance = $config->transfer_ride_now_radius;
                break;
            case "4":
                $distance = $config->outstation_radius;
                break;
            case "5":
                $distance = $config->pool_radius;
                break;
        }
        if ($request->service_type == 5) {
            $pricecards = PriceCard::where([['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type]])->get();
            $vehicle_type_id = array_pluck($pricecards, 'vehicle_type_id');
            $findDriver = new FindDriverController();
            $drivers = $findDriver->checkPoolDriver($request->area, $request->latitude, $request->longitude, $distance, $config->number_of_driver_user_map, 1, $vehicle_type_id);
            if (!empty($drivers)) {
                $vehicle = VehicleType::find($drivers[0]->vehicle_type_id);
            }
        } else {
            $drivers = Driver::GetNearestDriver($request->area, $request->latitude, $request->longitude, $distance, $config->number_of_driver_user_map, $request->vehicle_type, $request->service_type);
        }
        if (empty($drivers)) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }
        return response()->json(['result' => "1", 'message' => trans('api.message5'), 'term_status' => $request->user('api')->term_status, 'data' => $drivers, 'vehicle' => $vehicle]);
    }


//    // driver current lat long
//    public function driverLatLong(Request $request)
//    {
//        $data = [
//            'latitude' => $request->input('latitude'),
//            'longitude' => $request->input('longitude'),
//            'bearing' => $request->input('bearing'),
//            'accuracy' => $request->input('accuracy'),
//            'driver_id' => $request->input('driver_id'),
//            'timestamp' => time()
//        ];
//
//        if (empty($data['latitude']) && empty($data['longitude']) && empty($data['driver_id'])) {
//
//            return response()->json(['result' => "0", 'message' => 'mandatory fields can not be empty', 'data' => []]);
//        }
//        $mytime = \Carbon\Carbon::now();
//        $driver = Driver::Find($data['driver_id']);
//        $driver->current_latitude = $data['latitude'];
//        $driver->current_longitude = $data['longitude'];
//        $driver->bearing = $data['bearing'];
//        $driver->accuracy = $data['accuracy'];
//        $driver->last_location_update_time = $mytime->toDateTimeString();
//        $driver->save();
//        if ($driver->free_busy == 1) {
//            $bookings = Booking::where([['booking_status', '=', 1004], ['driver_id', '=', $driver->id], ['service_type_id', '=', 1]])->first();
//            if (!empty($bookings)) {
//                $driverlocation = $data;
//                $locationArray = [];
//                $getLocation = BookingCoordinate::where([['booking_id', '=', $bookings->id]])->first();
//                if (!empty($getLocation)) {
//                    $locationArray = json_decode($getLocation->coordinates, true);
//                }
//                array_push($locationArray, $driverlocation);
//                $locationArray = json_encode($locationArray);
//                BookingCoordinate::updateOrCreate(
//                    ['booking_id' => $bookings->id],
//                    [
//                        'coordinates' => $locationArray,
//                    ]
//                );
//            }
//        }
//        return response()->json(['result' => "1", 'message' => trans('api.locationupdate')]);
//    }
}
