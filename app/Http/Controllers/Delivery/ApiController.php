<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Helper\PolygenController;
use App\Models\Category;
use App\Models\CountryArea;
use App\Models\DeliveryProduct;
use App\Models\DeliveryType;
use App\Models\DeliveryConfiguration;
use App\Models\DemoConfiguration;
use App\Models\PricingParameter;
use Illuminate\Validation\Rule;
use App\Models\PriceCard;
use App\Models\WeightUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\NormalController;

class ApiController extends Controller
{
    public function Checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area' => 'required|integer|exists:country_areas,id',
            'delivery_type_id' => 'required|integer|exists:delivery_types,id',
            'vehicle_type' => 'required|integer',
            'pickup_latitude' => 'required',
            'pickup_longitude' => 'required',
            'pick_up_locaion' => 'required',
            'booking_type' => 'required|integer|in:1,2',
            'package_array' => 'required|json',
            'later_date' => 'required_if:booking_type,2',
            'later_time' => 'required_if:booking_type,2',
            'total_drop_location' => 'required|integer|between:0,4',
            'drop_location' => 'required_if:total_drop_location,1,2,3,4',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }

        $normalBooking = new NormalController();
        if ($request->booking_type == 1) {
            $booking = $normalBooking->NormalDeliveryBookingCheckout($request);
        } else {
            $booking = $normalBooking->LaterDeliveryBookingCheckout($request);
        }

        return response()->json($booking);

    }

    public function HomeScreen(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $merchant_id = $request->user('api')->merchant_id;

        //Demo

        if (!empty($request->user('api')->unique_number)) {
            $demo = DemoConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
            if (empty($demo)) {
                return response()->json(['result' => "0", 'message' => "No Demo Area", 'data' => []]);
            }
            $area = CountryArea::where([['id', '=', $demo->country_area_id]])->first();
            if (empty($area)) {
                return response()->json(['result' => "0", 'message' => trans("$string_file.no_service_area"), 'data' => []]);
            }
        } else {
            $area = PolygenController::Area($request->latitude, $request->longitude, $merchant_id);
            if (empty($area)) {
                return response()->json(['result' => "0", 'message' => trans("$string_file.no_service_area"), 'data' => []]);
            }
        }

        $area_id = $area['id'];
        $area = CountryArea::find($area_id);
        if (empty($area->deliveryTypes->toArray())) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }
        $user = $request->user('api');
        $user->country_area_id = $area_id;
        $user->save();
        $categories = $area->deliveryTypes;
        unset($area->deliveryTypes);
        foreach ($categories as $values) {
            $values->name = $values->name;
            foreach ($values->goods as $value) {
                $value->name = $value->GoodName;
            }
        }
        $WeightUnit = WeightUnit::where([['merchant_id', '=', $merchant_id]])->get();
        foreach ($WeightUnit as $item) {
            $item->name = $item->WeightUnitName;
            $item->description = $item->WeightUnitDescription;
        }
        $area->categories = $categories;
        $area->unit = $WeightUnit;
        return response()->json(['result' => "1", 'message' => "category and Package", 'data' => $area]);
    }

    public function HomeScreenCopy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $merchant_id = $request->user('api')->merchant_id;
        $area = PolygenController::Area($request->latitude, $request->longitude, $merchant_id);
        if (empty($area)) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.no_service_area"), 'data' => []]);
        }
        $area_id = $area['id'];
        $area = CountryArea::find($area_id);
        if (empty($area->Categories->toArray())) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }
        $categories = $area->Categories;
        foreach ($categories as $values) {
            $values->name = $values->CategoryName;
            foreach ($values->Goods as $value) {
                $value->name = $value->GoodName;
            }
        }
        $WeightUnit = WeightUnit::where([['merchant_id', '=', $merchant_id]])->get();
        foreach ($WeightUnit as $item) {
            $item->name = $item->WeightUnitName;
            $item->description = $item->WeightUnitDescription;
        }
        $area->categories = $categories;
        $area->unit = $WeightUnit;
        return response()->json(['result' => "1", 'message' => "category and Package", 'data' => $area]);
    }

    public function VehicleType(Request $request)
    {
        $merchant_id = $request->user('api')->merchant_id;
        $validator = Validator::make($request->all(), [
            'packages' => 'required|json',
            'area_id' => [
                'required',
                'integer',
                Rule::exists('country_areas', 'id')->where(function ($query) use ($merchant_id) {
                    $query->where('merchant_id', $merchant_id);
                }),
            ],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $packages = json_decode($request->packages, true);
        $category_id = array_pluck($packages, 'category_id');

        if (empty($category_id)) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }
        $deliveryType = DeliveryType :: whereIn('id', $category_id)
                        ->whereHas('areas', function ($q) use ($request) {
                            $q->where('country_area_delivery_type.country_area_id', '=', $request->area_id);
                        })
                        ->orderBy('rank' , 'desc')
                        ->first();
        if (empty($deliveryType)) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }

        $vehicles = $deliveryType->vehicles;
        if (count($vehicles) == 0) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }

        $arr_vehicle = [];
        foreach ($vehicles as $value) {
            $value->name = $value->VehicleTypeName;
            $value->description = $value->VehicleTypeDescription;
            $value->vehicleTypeImage = get_image($value->vehicleTypeImage , 'vehicle',$merchant_id);
            $value->vehicleTypeDeselectImage = get_image($value->vehicleTypeDeselectImage , 'vehicle',$merchant_id);
            $value->vehicleTypeMapImage = view_config_image($value->vehicleTypeMapImage); // get images from main panel
            $arr_vehicle[] = $value;
        }
        return response()->json(['result' => "1", 'message' => "category and Package", 'data' => $arr_vehicle]);
    }



// commented by amba while string module
//    public function Pricecard(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'area' => 'required|integer|exists:country_areas,id',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
//        }
//
//        $area_id = $request->area;
//        $delivery_types = DeliveryType::with(['PriceCard' => function ($query) use ($area_id) {
//            $query->where([['country_area_id', '=', $area_id]]);
//        }])->whereHas('PriceCard', function ($q) use ($area_id) {
//            $q->where([['country_area_id', '=', $area_id]]);
//        })->get();
//        if (count($delivery_types) == 0) {
//            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
//        }
//        $area = CountryArea::find($area_id);
//        $currency = $area->Country->isoCode;
//        $merchant_id = $area->merchant_id;
//        if ($area->Country->distance_unit == 1) {
//            $distance_unit = trans("$string_file.km");
//        } else {
//            $distance_unit = trans("$string_file.miles");
//        }
//
//        foreach ($delivery_types as $key => $value) {
//            $value['serviceName'] = $value->name;
//            $value['serviceStatus'] = 1;
//            $vehicle_type = [];
//            foreach ($value->PriceCard as $login) {
//                $login->vehicleTypeName = $login->VehicleType->VehicleTypeName;
//                $login->vehicleTypeDescription = $login->VehicleType->VehicleTypeDescription;
//                $login->vehicleTypeImage = get_image($login->VehicleType->vehicleTypeImage,'vehicle',$merchant_id);
//                $price_card_values = $login->PriceCardValues;
//                foreach ($price_card_values as $values) {
//                    $parameter_price = $currency . " " . $values->parameter_price;
//                    $parameterType = $values->PricingParameter->parameterType;
//                    switch ($parameterType) {
//                        case "1":
//                            $description = trans('api.message78');
//                            break;
//                        case "2":
//                            $description = trans('api.message79');
//                            break;
//                        case "3":
//                            $description = trans('api.message80');
//                            break;
//                        case "4":
//                            $description = trans('api.message81');
//                            break;
//                        case "6":
//                            $description = trans('api.message81');
//                            break;
//                        case "7":
//                            $description = trans('admin.toll');
//                            break;
//                        case "8":
//                            $description = trans('admin.message4');
//                            break;
//                        case "9":
//                            $description = trans('admin.message219');
//                            break;
//                        case "10":
//                            $description = trans('admin.message220');
//                            break;
//                        default:
//                            $description = "";
//                    }
//                    $values->pricing_parameter = $values->PricingParameter->ParameterApplication;
//                    $values->parameter_price = $parameter_price;
//                    $values->description = $description;
//                }
//                $base_fare = $login->base_fare;
//                if (!empty($base_fare)) {
//                    $parameterBase = PricingParameter::where([['parameterType', '=', 10], ['merchant_id', '=', $merchant_id]])->first();
//                    $name = $parameterBase->ParameterApplication;
//                    $newBase = array(
//                        'id' => 0,
//                        'price_card_id' => 0,
//                        'pricing_parameter_id' => 0,
//                        'parameter_price' => $currency . " " . $login->base_fare,
//                        'pricing_parameter' => $name,
//                        'parameterType' => 10,
//                        "description" => "Free Distance " . $login->free_distance . " " . $distance_unit . " Free Time " . $login->free_time . " Mintues",
//                        'parameter_edit' => 0,
//                        'free_value' => 0,
//                        'created_at' => "",
//                        'updated_at' => "",
//                    );
//                    $price_card_values->push($newBase);
//                }
//                $login['price_card_values'] = $price_card_values;
//                $vehicle_type[] = $login;
//            }
//
//            unset($value->PriceCard);
//            $delivery_types[$key] = $value;
//            $delivery_types[$key]['vehicle_type'] = $vehicle_type;
//
//        }
//        return response()->json(['result' => "1", 'message' => trans('api.message3'), 'data' => $delivery_types]);
//    }

    public function getDeliveryProduct(Request $request){
        $user = $request->user('api');
        $string_file = $this->getStringFile(null,$user->Merchant);
        $products = DeliveryProduct::where([['merchant_id','=',$user->merchant_id],['status','=',1]])->get();
        $data = [];
        foreach ($products as $product){
            $data[] = array(
                'id' => $product->id,
                'segment_id' => $product->segment_id,
                'merchant_id' => $product->merchant_id,
                'product_name' => $product->ProductName,
                'weight_unit' => $product->WeightUnit->WeightUnitName
            );
        }
        return response()->json(['result' => "1", 'message' => __("$string_file.success"), 'data' => $data]);
    }
}
