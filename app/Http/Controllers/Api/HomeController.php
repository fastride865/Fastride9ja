<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helper\FindDriverController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\PolygenController;
use App\Http\Controllers\Helper\Estimate;
use App\Models\BookingConfiguration;
use App\Models\Configuration;
use App\Models\CountryArea;
use App\Models\ServiceType;
use App\Models\Driver;
use App\Models\Merchant;
use App\Models\PriceCard;
use App\Models\VehicleType;
use App\Models\RewardPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\AreaTrait;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Models\ServicePackage;
use DB;
use App\Models\Category;

class HomeController extends Controller
{
    use AreaTrait,ApiResponseTrait,MerchantTrait ;
    // to get home screen data of user app
    public function cars(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $area = PolygenController::Area($request->latitude, $request->longitude, $merchant_id);
        if (empty($area)) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.no_service_area"), 'data' => []]);
        }
//        $merchant = Merchant::find($merchant_id);
//        $home_screen = $merchant->ApplicationConfiguration->home_screen_view;
        $area_id = $area['id'];
        $areas = CountryArea::with('ServiceTypes')->find($area_id);
        $currency = $areas->Country->isoCode;
//        if ($home_screen == 1) {
//            $areas = $this->Category($areas);
//        } else {
        $areas = $this->ServiceType($areas, $merchant_id, $request,"",$string_file);
//        }
//        $areas->AreaCoordinatesIos = json_decode($areas->AreaCoordinates, true);
        //$areas->AreaCoordinatesIos = json_decode($areas->AreaCoordinates, true);
        if(isset($areas->AreaCoordinates))
        {
            unset($areas->AreaCoordinates);
        }
        return response()->json(['result' => "1", 'home_screen' => 2, 'message' => "cars", 'currency' => $currency, 'data' => $areas]);
    }

    // for vehicle based segment
    public function userHomeScreen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'segment_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            // call area trait to get id of area
            $merchant_id = $request->user('api')->merchant_id;

            $string_file = $this->getStringFile($merchant_id);
            $this->getAreaByLatLong($request,$string_file);
            $area_id = $request->area;
            $areas = CountryArea::select('id','country_id','merchant_id','AreaCoordinates')
                ->with(['ServiceTypes'=>function($q) use($merchant_id){
                }])
                ->find($area_id);
            $request->user('api')->country_area_id = $area_id;
            if($request->user('api')->first_reward_pending == 1) {
                $reward_point_data = RewardPoint:: where([
                    ['merchant_id' , '=' , $merchant_id],
                    ['country_area_id' , '=' , $area_id],
                ])->first();

                if ($reward_point_data && $reward_point_data->registration_enable == 1) {
                    $request->user('api')->reward_points = $reward_point_data->user_registration_reward;
                }
            }
            $request->user('api')->first_reward_pending = null;
            $request->user('api')->save();
            $currency = $areas->Country->isoCode;
            $areas = $this->ServiceType($areas, $merchant_id, $request, $currency,$string_file);
            $is_geofence = (isset($areas->is_geofence) && $areas->is_geofence == 1) ? true : false;
            $return['config_data'] = [
                'currency' => $currency,
                'is_geofence' => $is_geofence
            ];
            $return['response_data'] = $areas;

        }catch (\Exception $e)
        {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
       return $this->successResponse(trans("$string_file.data_found"),$return);
    }

    /*updated code*/
    public function ServiceType($areas, $merchant_id, $request, $currency = null,$string_file = "")
    {

        try {
            $service_types = $areas->ServiceTypes->where('segment_id',$request->segment_id)->sortBy('id');

            $pool = $service_types->filter(function ($service) {
                return $service->id == 5;
            });

            $service_types = $service_types->filter(function ($service) {
                return $service->id != 5;
            });

            $merchant = Merchant::find($merchant_id);
            $configuration = $merchant->Configuration;
            $app_configuration = $merchant->ApplicationConfiguration;

            if(count($service_types) > 0)
            {
                /**Get time according to pickup and drop location*/
                $drop_locationArray = [];
                if (!empty($request->drop_location)) {
                    $drop_locationArray = json_decode($request->drop_location, true);
                }

                if($configuration->drop_outside_area == 1 && $configuration->outside_area_ratecard == 1){
                    $dropLocation = isset($drop_locationArray[0]) ? $drop_locationArray[0] : '';
                    $drop_lat = isset($dropLocation['drop_latitude']) ? $dropLocation['drop_latitude'] : '';
                    $drop_long = isset($dropLocation['drop_longitude']) ? $dropLocation['drop_longitude'] :'';
                    $drop_location_outside_area = 2;
                    $ploygon = new PolygenController();
                    $checkArea = $ploygon->CheckArea($drop_lat, $drop_long, $areas->AreaCoordinates);
                    if(!$checkArea){
                        $drop_location_outside_area = 1;
                    }
                    $request->request->add(['drop_location_outside_area' => $drop_location_outside_area]);
                }

                $key = get_merchant_google_key($merchant_id,'api');
                $googleArray = GoogleController::GoogleStaticImageAndDistance($request->latitude, $request->longitude, $drop_locationArray, $key,"",$string_file);

                $service_type_id = array_column($service_types->toArray(),'id');
                if(!empty($pool) && !in_array(5,$service_type_id))
                {
                    $pool = NULL;
                }
                $vehicles = CountryArea::select('vt.id','vt.vehicleTypeImage','vt.ride_now','vt.ride_later')
                    ->addSelect('cavt.service_type_id','cavt.vehicle_type_id as id','country_areas.merchant_id','lvt.vehicleTypeName')
                    ->where('country_areas.merchant_id',$merchant_id)
                    ->where('country_areas.id',$areas->id)
                    ->join('country_area_vehicle_type as cavt','cavt.country_area_id','=','country_areas.id')
                    ->join('vehicle_types as vt','vt.id','=','cavt.vehicle_type_id')
                    ->join('language_vehicle_types as lvt','vt.id','=','lvt.vehicle_type_id')
                    ->join('price_cards as pc','vt.id','=','pc.vehicle_type_id')
                    ->whereIn('cavt.service_type_id',$service_type_id)
                    ->where('pc.country_area_id',$areas->id)
                    ->where('pc.merchant_id',$merchant_id)
                    ->whereIn('pc.service_type_id',$service_type_id)
                    ->groupBy('cavt.vehicle_type_id')
                    ->groupBy('cavt.service_type_id')
                    ->orderBy('vt.sequence')
                    ->get();

                if($vehicles->count() > 0){
                    // we are calling images here other aws will take more time to return image response
                    foreach($vehicles as $key => $value){
                        $value->vehicleTypeImage = get_image($value->vehicleTypeImage,'vehicle',$value->merchant_id);
                    }
                }

                foreach ($service_types as $key => $value) {
                    $serviceName = $value->ServiceName($merchant_id);
                    if ($request->segment_id == 1){
                        if($app_configuration->home_screen_view == 1)
                        {
                            $service_data = $this->vehicleForTaxiWithCategory($value,$vehicles,$request,$configuration,$merchant_id,$areas,$currency,$pool,$googleArray);
                        }
                        else
                        {
                            $service_data = $this->vehicleForTaxi($value,$vehicles,$request,$configuration,$merchant_id,$areas,$currency,$pool,$googleArray);
                        }
                    }else if($request->segment_id == 2){
                        $service_data = $this->vehicleForDelivery($value,$vehicles);
                    }

                    $value->serviceName = $serviceName;
                    unset($value->parent_id);
                    unset($value->segment_id);
                    unset($value->serviceStatus);
                    unset($value->additional_support);
                    unset($value->owner);
                    unset($value->owner_id);
                    unset($value->created_at);
                    unset($value->updated_at);
                    $service_types[$key] = $value;
                    if($app_configuration->home_screen_view == 1 && $request->segment_id == 1)
                    {
                        $service_types[$key]['arr_category'] = $service_data;
                    }
                    else
                    {
                        $service_types[$key]['vehicles'] = $service_data['vehicle'];
                        $service_types[$key]['package'] = $service_data['packages'];
                    }
                }
            }
            // we have to change key of  $service_types thats why executed extra loop
            $a = array();
//            foreach ($service_types as $value) {
//                // If vehicles or packages not exist for service then that will not show on app.
//                if(($app_configuration->home_screen_view == 2 && (!empty($value->vehicles) || !empty($value->package))) || ($app_configuration->home_screen_view == 1 && !empty($value->arr_category))){
//                    $a[] = $value;
//                }
//            }

            if($request->segment_id == 1)
            {
                foreach ($service_types as $value) {
                    // If vehicles or packages not exist for service then that will not show on app.
                    if(($app_configuration->home_screen_view == 2 && (!empty($value->vehicles) || !empty($value->package))) || ($app_configuration->home_screen_view == 1 && !empty($value->arr_category))){
                        $a[] = $value;
                    }
                }
            }
            elseif($request->segment_id == 2)
            {
                foreach ($service_types as $value) {
                    // If vehicles or packages not exist for service then that will not show on app.
                    if(!empty($value->vehicles)){
                        $a[] = $value;
                    }
                }
            }

            $areas->service_types = $a;
            return $areas;

        }catch(\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    public function Areas(Request $request)
    {
        $merchant_id = $request->user('api')->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $areas = CountryArea::select('id','country_id')->whereHas('PriceCard', function ($query) use ($merchant_id) {
            $query->where([['merchant_id', '=', $merchant_id]]);
            $query->where([['status', '=', 1]]);
        })->where([['merchant_id', '=', $merchant_id]])->get();
        if (empty($areas->toArray())) {
            return $this->failedResponse(trans("$string_file.data_not_found"));
        }
        foreach ($areas as $value) {
            $value->AreaName = $value->CountryAreaName;
        }
        return $this->successResponse(trans('api.message84'),$areas);
    }

    public function homeScreenDrivers(Request $request)
    {
        $user = $request->user('api');
        $merchant_id = $user->merchant_id;
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $validator = Validator::make($request->all(), [
            'area' => [
                'required',
                'integer',
                Rule::exists('country_areas', 'id')->where(function ($query) use ($merchant_id) {
                    $query->where('merchant_id', $merchant_id);
                }),
            ],
            'service_type' => 'required|integer|exists:service_types,id',
            'vehicle_type' => 'required_if:service_type,1,2,3,4',
            'distance' => 'required|integer',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'segment_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//            throw new \Exception($errors[0]);
        }
        try {
            $vehicle = VehicleType::find($request->vehicle_type);
            if(isset($vehicle->vehicleTypeMapImage)){
                $vehicle->vehicleTypeMapImage = explode_image_path($vehicle->vehicleTypeMapImage);
            }
            $config = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
            $service_type = ServiceType::select('id','type')->Find($request->service_type);
            switch ($service_type->type) {
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
            if ($service_type->type == 5) {
                $pricecards = PriceCard::where([['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type]])->get();
                $vehicle_type_id = array_pluck($pricecards, 'vehicle_type_id');
                $findDriver = new FindDriverController();
                $drivers = $findDriver->checkPoolDriver($request->area, $request->latitude, $request->longitude, $distance, $config->number_of_driver_user_map, 1, $vehicle_type_id);
                if (!empty($drivers)) {
                    $vehicle = VehicleType::find($drivers[0]->vehicle_type_id);
                }
            } else {
                $drivers = Driver::GetNearestDriver([
                    'area'=>$request->area,
                    'latitude'=>$request->latitude,
                    'longitude'=>$request->longitude,
                    'distance'=>$distance,
                    'limit'=>$config->number_of_driver_user_map,
                    'vehicle_type'=>$request->vehicle_type,
                    'segment_id'=>$request->segment_id,
                    'service_type'=>$request->service_type]);
            }
            if (count($drivers) == 0) {
                throw new \Exception(trans("$string_file.data_not_found"));
            }
        }catch (\Exception $e)
        {
         return $this->failedResponse($e->getMessage());
        }
        $response_data = ['term_status' => $request->user('api')->term_status, 'response_data' => $drivers, 'vehicle' => $vehicle];
        return $this->successResponse(trans("$string_file.success"),$response_data);
    }

    public function CheckDropLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_id' => 'required|exists:country_areas,id',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'service_type' => 'required|exists:service_types,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//                response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }

        $area = CountryArea::find($request->area_id);
        $merchant = Merchant::find($area->merchant_id);
        $string_file = $this->getStringFile($merchant->id);
        $message = trans("$string_file.drop_location_out");

        // Check for demo

        if (!empty($area->DemoConfiguration) || ($merchant->Configuration->drop_outside_area == 1 && in_array($request->service_type, [1, 2, 3, 5]))) {
            return $this->successResponse($message,[]);
//                response()->json(['result' => "1", 'message' => $message]);
        }

        if(isset($merchant->Configuration->geofence_module) &&  $merchant->Configuration->geofence_module == 1){
            $found_area = $this->checkGeofenceArea($request->latitude,$request->longitude,'drop',$area->merchant_id);
            if(!empty($found_area)){
                return response()->json(['result' => "1", 'message' => trans('api.message137'), 'drop_area_id' => $found_area->id]);
            }
        }

        if($area->is_geofence == 1){
            $base_areas = explode(',',$area->RestrictedArea->base_areas);
            $base_areas = CountryArea::with('RestrictedArea')->where([['merchant_id','=',$area->merchant_id]])->whereIn('id',$base_areas)->get();
            if(!empty($base_areas)){
                foreach($base_areas as $geofenceArea){
                    $ploygon = new PolygenController();
                    $checkArea = $ploygon->CheckArea($request->latitude, $request->longitude, $geofenceArea->AreaCoordinates);
                    if(!empty($checkArea)){
                        $found_area = $geofenceArea;
                        return response()->json(['result' => "1", 'message' => trans('api.message137'), 'drop_area_id' => $found_area->id]);
                        break;
                    }
                }
            }
        }

        $ploygon = new PolygenController();
        $checkArea = $ploygon->CheckArea($request->latitude, $request->longitude, $area->AreaCoordinates);

        if ($request->service_type == 4) {
            $checkArea = $checkArea ? false : true;
            $message = $checkArea == true ? trans("$string_file.success") : trans("$string_file.outstation_drop_location_error");
        }
        if ($checkArea) {
            return response()->json(['result' => "1", 'message' => trans("$string_file.success"), 'drop_area_id' => $request->area_id]);
        }
        return $this->failedResponse($message);
//            response()->json(['result' => "0", 'message' => $message]);
    }

    public function StaticVehicle($service, $merchant_id)
    {
        $serviceName = $service->ServiceApplication($merchant_id) ? $service->ServiceApplication($merchant_id) : $service->serviceName;
        $merchant = Merchant::find($merchant_id);
        $service = $merchant->ServiceType->where('id',$service->id)->first();
        return array(
            "id" => NULL,
            "vehicleTypeImage" => get_image($service['pivot']->service_icon,'service',$merchant_id),
            "vehicleTypeName" => $serviceName,
            'service_type_id' => $service->id,
            'surcharge' => "",
            'ride_now' => 1,
            'ride_later' => 0,
            'eta' => "",
            'estimate_fare' => "",
            'map_icon' => ""
        );
    }

    public function surCharge($country_area_id, $service_type_id, $vehicle_type_id, $currency = null)
    {
        $response = "";
        $surchargeData = PriceCard::where([['country_area_id', '=', $country_area_id], ['service_type_id', '=', $service_type_id], ['vehicle_type_id', '=', $vehicle_type_id]])->first();
        if (empty($surchargeData)) {
            return $response;
        }
        if ($surchargeData->sub_charge_status == 1) {
            $response = $surchargeData->sub_charge_type == 1 ? trans('api.sub_charge_type', ['amount' => $currency . " " . $surchargeData->sub_charge_value]) : trans('api.sub_charge_type2', ['amount' => $surchargeData->sub_charge_value]);
        }
        return $response;
    }

// get services vehicles when category view is enabled
    public function vehicleForTaxiWithCategory($serviceType,$vehicles,$request,$configuration,$merchant_id,$areas,$currency,$pool,$googleArray){
        $service_id = $serviceType->id;
        $type = $serviceType->type;
        $area_id = $areas->id;
        $segment_id = $request->segment_id;
        $vehicle = array();
        $packages = array();
        $merchant_id = $request->merchant_id;
        $estimateController = new Estimate();
        $eta = "";
        $arr_return_data = [];
        switch ($type) {
            case "1":  //Normal
                $arr_category = Category::
                with(['VehicleType'=>function($q) use ($merchant_id,$service_id,$segment_id,$area_id){
                    $q ->where([['service_type_id','=',$service_id],['country_area_id','=',$area_id],['merchant_id','=',$merchant_id],['segment_id','=',$segment_id]]);
                }])->whereHas('VehicleType',function($q) use ($merchant_id,$service_id,$segment_id,$area_id){
                    $q ->where([['service_type_id','=',$service_id],['country_area_id','=',$area_id],['merchant_id','=',$merchant_id],['segment_id','=',$segment_id]]);
                })->where([['merchant_id','=',$merchant_id],['delete','=',NULL],['status','=',1]])->get();

                foreach($arr_category as $category)
                {
                    $arr_vehicle = array_pluck($category->VehicleType,'id');
                    $vehiclesList = $vehicles->filter(function ($vehicle) use ($arr_vehicle,$service_id) {
                        return  in_array($vehicle->id,$arr_vehicle) && $vehicle->service_type_id == $service_id;
                    });
                    $vehicle = [];
                    $pool_vehicle = [];
                    foreach ($vehiclesList as $v) {
                        $vehicle_type_id = $v->id;
                        $eta = ($configuration->homescreen_eta == 1) ? $estimateController->Eta($merchant_id, $service_id, $areas->id, $request->latitude, $request->longitude, $vehicle_type_id,$segment_id) : "";
                        $estimate_fase = ($configuration->homescreen_estimate_fare == 1 && !empty($request->drop_location)) ? $estimateController->Estimate($merchant_id, $service_id, $areas->id, $request->latitude, $request->longitude, $request->drop_location, $vehicle_type_id, $currency,null,$googleArray) : "";

                        $v->service_type_id = $service_id;
                        $v->surcharge = $this->surCharge($areas->id, $service_id, $v->id, $currency);
                        $v->eta = $eta;
                        $v->estimate_fare = $estimate_fase;
                        $v->service_type_id = $service_id;
                        $v->map_icon = explode_image_path($v->vehicleTypeMapImage);
                        $vehicle[] = $v;
                    }
                    if (!empty($pool) && !empty($pool->toArray())) {
                        $pool_vehicle[] = $this->staticVehicle($pool->first(), $merchant_id);
                        array_splice($vehicle, $areas->pool_postion - 1, 0, $pool_vehicle);
                    }
                    $arr_return_data[] = ['category_id'=>$category->id,'name'=>$category->Name($merchant_id),'vehicle' => $vehicle,'packages' => $packages];
                }
                break;
            case "2":  //Rental

                $arr_category = Category::
                with(['VehicleType'=>function($q) use ($merchant_id,$service_id,$segment_id,$area_id){
                    $q ->where([['service_type_id','=',$service_id],['country_area_id','=',$area_id],['merchant_id','=',$merchant_id],['segment_id','=',$segment_id]]);
                }])->whereHas('VehicleType',function($q) use ($merchant_id,$service_id,$segment_id,$area_id){
                    $q ->where([['service_type_id','=',$service_id],['country_area_id','=',$area_id],['merchant_id','=',$merchant_id],['segment_id','=',$segment_id]]);
                })->where([['merchant_id','=',$merchant_id],['delete','=',NULL],['status','=',1]])->get();

                foreach($arr_category as $category) {
                    $arr_vehicle = array_pluck($category->VehicleType, 'id');
                    $packageList =[];
                    $packageList = ServicePackage :: select('id','service_type_id')->with(['PriceCard'=>function($q) use ($service_id,$areas,$request){
                        $q->select('service_type_id','vehicle_type_id','service_package_id','id')
                            ->where([['country_area_id','=',$areas->id],['service_type_id','=',$service_id],
                                ['service_package_id','!=',NULL],['outstation_type','=',NULL],['segment_id','=',$request->segment_id]]);
                    }])
                        ->whereHas('PriceCard',function($q) use ($service_id,$areas,$request,$arr_vehicle){
                            $q->select('service_type_id','vehicle_type_id','service_package_id','id')
                                ->where([['country_area_id','=',$areas->id],['service_type_id','=',$service_id],
                                    ['service_package_id','!=',NULL],['outstation_type','=',NULL],['segment_id','=',$request->segment_id]])
                                ->whereIn('vehicle_type_id',$arr_vehicle);
                        })
                        ->get();
                    $packages = [];
                    foreach ($packageList as $login) {
                        $package_id = $login->id;
                        $packagevehicle = array();
                        $login->name = $login->PackageName;
                        $vehicle_type_for_package = array_column($login->PriceCard->toArray(),'vehicle_type_id');
                        $vehiclesList = $vehicles->filter(function ($vehicle) use ($service_id,$arr_vehicle,$vehicle_type_for_package) {
//                                 check vehicle type for package from price card configuration
//                                && in_array($vehicle->id,$vehicle_type_for_package)
                            return in_array($vehicle->id,$arr_vehicle) && $vehicle->service_type_id == $service_id  && in_array($vehicle->id,$vehicle_type_for_package);
                        });
                        foreach ($vehiclesList as $item) {
                            unset($item->PriceCard);
                            $vehicle_type = $item->id;
                            $eta = ($configuration->homescreen_estimate_fare == 1 && !empty($request->drop_location)) ? $estimateController->Eta($merchant_id, $service_id, $areas->id, $request->latitude, $request->longitude,  null) : "";
                            $estimate_fase = $configuration->homescreen_estimate_fare == 1 ? $estimateController->Estimate($merchant_id, $service_id, $areas->id, $request->latitude, $request->longitude, $request->drop_location, $vehicle_type, $currency,$package_id,$googleArray) : "";

                            $temp_item = (object)[];
                            $temp_item->id = $item->id;
                            $temp_item->service_type_id = $item->service_type_id;
                            $temp_item->vehicleTypeImage = $item->vehicleTypeImage;
                            $temp_item->ride_now = $item->ride_now;
                            $temp_item->ride_later = $item->ride_later;
                            $temp_item->vehicleTypeName = $item->vehicleTypeName;
                            $temp_item->surcharge = $this->surCharge($areas->id, $service_id, $item->id, $currency);
                            $temp_item->eta = $eta;
                            $temp_item->estimate_fase = $estimate_fase;
                            $temp_item->map_icon = explode_image_path($item->vehicleTypeMapImage);
                            $packagevehicle[] = $temp_item;
                        }
                        $login->vehicles =[];
                        $login->vehicles = $packagevehicle;
                        unset($login->PriceCard);
                        $packages[] = $login;
                    }
                    $arr_return_data[] = ['category_id'=>$category->id,'name'=>$category->Name($merchant_id),'vehicle' => $vehicle,'packages' => $packages];
                }
                break;
            case "3": // Transfer service
                $vehicle[] = $this->StaticVehicle($serviceType, $merchant_id);
                $arr_return_data[] = ['category_id'=>NULL,'name'=>"",'vehicle' => $vehicle,'packages' => $packages];
                break;
            case "4": // outstation service
                $vehicle[] = $this->StaticVehicle($serviceType, $merchant_id);
                $arr_return_data[] = ['category_id'=>NULL,'name'=>"",'vehicle' => $vehicle,'packages' => $packages];
                break;
            default:
        }
        return $arr_return_data;
        // return array('vehicle' => $vehicle,'packages' => $packages);
    }
// get services vehicles when category view is disabled
    public function vehicleForTaxi($serviceType,$vehicles,$request,$configuration,$merchant_id,$areas,$currency,$pool,$googleArray){
        $service_id = $serviceType->id;
        $type = $serviceType->type;
        $vehicle = array();
        $packages = array();
        $estimateController = new Estimate();
        $eta = "";
        switch ($type) {
            case "1": //Normal
                $vehiclesList = $vehicles->filter(function ($vehicle) use ($service_id) {
                    return $vehicle->service_type_id == $service_id;
                });
                foreach ($vehiclesList as $v) {
                    $vehicle_type_id = $v->id;
                    $vehicle_type = VehicleType::find($vehicle_type_id);
                    $eta = ($configuration->homescreen_eta == 1) ? $estimateController->Eta($merchant_id, $service_id, $areas->id, $request->latitude, $request->longitude, $vehicle_type_id) : "";
                    $estimate_fase = "";
                    if($configuration->homescreen_estimate_fare == 1 && !empty($request->drop_location)){
                        $estimate_fase = $estimateController->Estimate($merchant_id, $service_id, $areas->id, $request->latitude, $request->longitude, $request->drop_location, $vehicle_type_id, $currency,null,$googleArray,$request->drop_location_outside_area);
                    }elseif ($configuration->homescreen_estimate_fare == 2 && !empty($request->drop_location)){
                        $fare =  $estimateController->Estimate($merchant_id, $service_id, $areas->id, $request->latitude, $request->longitude, $request->drop_location, $vehicle_type_id, $currency,null,$googleArray,$request->drop_location_outside_area);
                        $estimate_fase = $this->getPriceRange($fare,$currency);
                    }

                    $v->service_type_id = $service_id;
                    $v->surcharge = $this->surCharge($areas->id, $service_id, $v->id, $currency);
                    $v->eta = $eta;
                    $v->estimate_fare = $estimate_fase;
                    $v->service_type_id = $service_id;
                    $v->map_icon = explode_image_path($v->vehicleTypeMapImage);
                    $v->vehicleTypeName = $vehicle_type->VehicleTypeName;
                    $vehicle[] = $v;
                }
                if (!empty($pool) && !empty($pool->toArray())) {
                    $pool_vehicle[] = $this->staticVehicle($pool->first(), $merchant_id);
                    array_splice($vehicle, $areas->pool_postion - 1, 0, $pool_vehicle);
                }
                break;
            case "2": //Rental
                $packageList = ServicePackage :: select('id','service_type_id')->where('packageStatus',1)->with(['PriceCard'=>function($q) use ($service_id,$areas,$request){
                    $q->select('service_type_id','service_package_id','id','vehicle_type_id')
                        ->where([['country_area_id','=',$areas->id],['service_type_id','=',$service_id],
                            ['service_package_id','!=',NULL],['outstation_type','=',NULL],['segment_id','=',$request->segment_id]]);
                }])
                    ->whereHas('PriceCard',function($q) use ($service_id,$areas,$request){
                        $q->select('service_type_id','service_package_id','id')
                            ->where([['country_area_id','=',$areas->id],['service_type_id','=',$service_id],
                                ['service_package_id','!=',NULL],['outstation_type','=',NULL],['segment_id','=',
                                    $request->segment_id],['status','=',1]]);
                    })
                    ->get();
                $packages = [];
                foreach ($packageList as $login) {
                    $package_id = $login->id;
                    $packagevehicle = array();
                    $login->name = $login->PackageName;
                    $vehicle_type_for_package = array_column($login->PriceCard->toArray(),'vehicle_type_id');
                    $vehiclesList = $vehicles->filter(function ($vehicle) use ($service_id,$vehicle_type_for_package) {
//                                 check vehicle type for package from price card configuration
//                                && in_array($vehicle->id,$vehicle_type_for_package)
                        return $vehicle->service_type_id == $service_id && in_array($vehicle->id,$vehicle_type_for_package);
                    });
                    foreach ($vehiclesList as $item) {
                        unset($item->PriceCard);
                        $vehicle_type = $item->id;
                        $vehicle_type_obj = VehicleType::find($vehicle_type);
                        $eta = ($configuration->homescreen_estimate_fare == 1 && !empty($request->drop_location)) ? $estimateController->Eta($merchant_id, $service_id, $areas->id, $request->latitude, $request->longitude,  null) : "";
                        $estimate_fase = $configuration->homescreen_estimate_fare == 1 ? $estimateController->Estimate($merchant_id, $service_id, $areas->id, $request->latitude, $request->longitude, $request->drop_location, $vehicle_type, $currency,$package_id,$googleArray) : "";
                        $temp_item = (object)[];
                        $temp_item->id = $item->id;
                        $temp_item->service_type_id = $item->service_type_id;
                        $temp_item->vehicleTypeImage = $item->vehicleTypeImage;
                        $temp_item->ride_now = $item->ride_now;
                        $temp_item->ride_later = $item->ride_later;
                        $temp_item->vehicleTypeName = $item->vehicleTypeName;
                        $temp_item->surcharge = $this->surCharge($areas->id, $service_id, $item->id, $currency);
                        $temp_item->eta = $eta;
                        $temp_item->estimate_fase = $estimate_fase;
                        $temp_item->map_icon = explode_image_path($item->vehicleTypeMapImage);
                        $temp_item->vehicleTypeName = $vehicle_type_obj->VehicleTypeName;
                        $packagevehicle[] = $temp_item;
                    }
                    $login->vehicles =[];
                    $login->vehicles = $packagevehicle;
                    unset($login->PriceCard);
                    if(!empty($packagevehicle))
                    {
                     $packages[] = $login;
                    }
                }
                break;
            case "3": //transfer
                $vehicle[] = $this->StaticVehicle($serviceType, $merchant_id);
                break;
            case "4": //Outstation
                $vehicle[] = $this->StaticVehicle($serviceType, $merchant_id);
            default:
        }
        return array('vehicle' => $vehicle,'packages' => $packages);
    }
    public function vehicleForDelivery($serviceType,$vehicles){
        $service_id = $serviceType->id;
        $vehicle = array();
        $packages = array();

        $vehiclesList = $vehicles->filter(function ($vehicle) use ($service_id) {
            return $vehicle->service_type_id == $service_id;
        });

        foreach ($vehiclesList as $v) {
            $v->surcharge = '0.0';
            $v->eta = '';
            $v->estimate_fase = '';
            $v->service_type_id = $service_id;
            $v->map_icon = explode_image_path($v->vehicleTypeMapImage);
            $vehicle[] = $v;
        }
        return array('vehicle' => $vehicle,'packages' => $packages);
    }

    public function getPriceRange($amount,$currency){
        if(empty($amount)){
            return '';
        }
        $amount = trim(str_replace($currency,'',$amount));
        if($amount < 500){
            $price_range = $currency." 100 - 500";
        }elseif($amount >= 500 && $amount < 1000){
            $price_range = $currency." 500 - 1000";
        }elseif($amount >= 1000 && $amount < 1500){
            $price_range = $currency." 1000 - 1500";
        }elseif($amount >= 1500 && $amount < 2000){
            $price_range = $currency." 1500 - 2000";
        }elseif($amount >= 2000 && $amount < 2500){
            $price_range = $currency." 2000 - 2500";
        }elseif($amount >= 2500 && $amount < 3000){
            $price_range = $currency." 2500 - 3000";
        }elseif($amount >= 3000 && $amount < 3500){
            $price_range = $currency." 3000 - 3500";
        }elseif($amount >= 3500 && $amount < 4000){
            $price_range = $currency." 3500 - 4000";
        }elseif($amount >= 4000 && $amount < 4500){
            $price_range = $currency." 4000 - 4500";
        }elseif($amount >= 4500 && $amount < 5000){
            $price_range = $currency." 4500 - 5000";
        }else{
            $price_range = "More Than 5000";
        }
        return $price_range;
    }
}