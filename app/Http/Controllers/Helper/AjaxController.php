<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\DriverAgency\DriverController;
use App\Http\Controllers\Taxicompany\DriverController as TaxiCompanyDriver;
use App\Http\Controllers\Merchant\DriverController as MerchantCompanyDriver;
use App\Models\ServicePackage;
use Auth;
use App\Models\CountryArea;
use App\Models\PaymentOption;
use App\Models\SmsGateways;
use App\Models\OutstationPackage;
use App\Models\PriceCard;
use App\Models\ServiceType;
use App\Models\Driver;
use App\Models\State;
use App\Models\Town;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use DB;
use App\Traits\MerchantTrait;

class AjaxController extends Controller
{
    use MerchantTrait;

//    public function GetPriceCard(Request $request)
//    {
//        $area_id = $request->area_id;
//        $priceCards = PriceCard::select('id', 'price_card_name')->where([['country_area_id', '=', $area_id]])->get();
//        if (empty($priceCards->toArray())) {
//            echo "<option value=''>" . trans("$string_file.data_not_found") . "</option>";
//        } else {
//            foreach ($priceCards as $value) {
//                echo "<option value='" . $value->id . "'>" . $value->price_card_name . "</option>";
//            }
//        }
//    }

    public function AreaList(Request $request)
    {
        $taxi_company = get_taxicompany();
        $hotel = get_hotel();
        if (!empty($taxi_company)) {
            $merchant_id = $taxi_company->merchant_id;
        } elseif (!empty($hotel)) {
            $merchant_id = $hotel->merchant_id;
        } else {
            $merchant_id = get_merchant_id();
        }
        $string_file = $this->getStringFile($merchant_id);
        $id = $request->id;
        $areaList = CountryArea::whereHas('Country', function ($query) use ($id) {
            $query->where([['phonecode', '=', $id]]);
        })->where([['merchant_id', '=', $merchant_id], ['status', '=', 1]])->get();
        if (empty($areaList->toArray())) {
           echo "<option value=''>" . trans("$string_file.no_service_area") . "</option>";
        } else {
            echo "<option value=''>" . trans("$string_file.area") . "</option>";
            foreach ($areaList as $value) {
                echo "<option id='" . $value->CountryAreaName . "' value='" . $value->id . "'>" . $value->CountryAreaName . "</option>";
            }
        }
    }

    public function VehicleServices(Request $request)
    {
        $validator = Validator::make($request->all(), [
//            'area_id' => 'required',
            'vehicle' => 'required',
            'driver_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        $driver = Driver::Find($request->driver_id);
//        $driver_segment = array_pluck($driver->Segment,'id');
        $vehicle_type = $request->vehicle;
        $area_id = $driver->country_area_id;
        if(!empty($driver->taxi_company_id))
        {
            $driver_obj = new TaxiCompanyDriver;
            echo $vehicle_doc_segment = $driver_obj->vehicleDocSegment($area_id, $driver, $vehicle_type);
        }
        elseif(!empty($driver->driver_agency_id))
        {
            $driver_obj = new DriverController;
            echo $vehicle_doc_segment = $driver_obj->vehicleDocSegment($area_id, $driver, $vehicle_type);
        }
         else
        {
            $driver_obj = new MerchantCompanyDriver;
            echo $vehicle_doc_segment = $driver_obj->vehicleDocSegment($area_id, $driver, $vehicle_type);
        }


    }

    public function PriceCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area' => 'required',
            'vehicle_type' => 'required',
            'service' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        $pricecard = PriceCard::where(function ($query) use ($request) {
            if (isset($request->package_id) && !empty($request->package_id)) {
                $query->where(['package_id', '=', $request->package_id]);
            }
        })->where([['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type]])->first();
        if (!empty($pricecard)) {
            return array('result' => 1, 'message' => "Price Card Added");
        } else {
            return array('result' => 0, 'message' => trans("$string_file.no_price_card_for_area"));
        }
    }

    public function ServiceType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_id' => 'required',
            'segment_id' => 'required',
            'segment_group' => 'required',
            'vehicle_type_id' => 'required_if:segment_group,==,1',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        $area_id = $request->area_id;
        $segment_id = $request->segment_id;
        $vehicle_type_id = $request->vehicle_type_id;
        $segment_group = $request->segment_group;
        $merchant_id = !empty($request->merchant_id) ? $request->merchant_id : get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $service_type = $this->getMerchantServicesByArea($area_id, $segment_id, $vehicle_type_id, '', $segment_group);
        echo "<option value=''>" . trans("$string_file.select") . "</option>";
        if (!empty($service_type)) {
            foreach ($service_type as $value) {
                echo "<option value='" . $value->id . "' additional_support='" . $value->additional_support . "'>" .
                    $value->serviceName($merchant_id) . "</option>";
            }
        }
    }

    public function VehicleTypeCashBack(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_id' => 'required|exists:country_areas,id',
            'service_id' => 'required|exists:service_types,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        $area_id = $request->area_id;
        $areas = CountryArea::with(['ServiceTypes'])
            ->with(['VehicleType'=>function($q){
                $q->where('admin_delete',NULL);
            }])
            ->where([['id', '=', $area_id]])->first();
        $service_ids = [$request->service_id];
        $vehiclesList = $areas->VehicleType->filter(function ($item) use ($service_ids) {
            return in_array($item->pivot->service_type_id, $service_ids);
        });
        $service_with_vehicles = $areas->ServiceTypes->where('id', $request->service_id)->map(function ($item, $key) use ($vehiclesList) {
            $item->vehicle = $vehiclesList->filter(function ($vehicle) use ($item) {
                return $vehicle->pivot->service_type_id == $item->id;
            });
            return $item;
        });

        echo "<div class='col-md-6' id='services-delete-$request->service_id'>
            <div class='form-group p-1' id='vehicletype'>
                <label for='emailAddress5'>
                    " . trans('admin.select-vehicles', $service_with_vehicles->pluck('serviceName')->toArray()) . "
                    <span class='text-danger'>*</span>
                </label><br>
                <select class='select2me'
                        name='" . $service_with_vehicles->pluck('serviceName')->toArray()['0'] . "[]'
                        id='vehicle'
                        data-placeholder=''
                        multiple >
                        <option value=''>" .
            trans('admin.select-vehicles', $service_with_vehicles->pluck('serviceName')->toArray()) .
            trans('admin.select-vehicles', $service_with_vehicles->pluck('serviceName')->toArray())
            . "</option>";
        foreach ($vehiclesList as $vehicles):
            echo "<option id='vehicle_$vehicles->id'
                                value='$vehicles->id'>
                            $vehicles->vehicleTypeName
                        </option>";
        endforeach;
        echo "</select>
            </div>
        </div>";
    }

    public function ServiceTypeCashBack(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_id' => 'required|exists:country_areas,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        $merchant = \Auth::user('merchant');
        $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
        $area_id = $request->area_id;
        $areas = CountryArea::with(['ServiceTypes'])
            ->with(['VehicleType',function($q){
                $q->where('admin_delete',NULL);
            }])
            ->where([['id', '=', $area_id]])->first();
        $service_ids = $areas->ServiceTypes->pluck('id')->toArray();
        $vehiclesList = $areas->VehicleType->filter(function ($item) use ($service_ids) {
            return in_array($item->pivot->service_type_id, $service_ids);
        });
        $service_with_vehicles = $areas->ServiceTypes->map(function ($item, $key) use ($vehiclesList) {
            $item->vehicle = $vehiclesList->filter(function ($vehicle) use ($item) {
                return $vehicle->pivot->service_type_id == $item->id;
            });
            return $item;
        });

        $array = [];
        $i = 0;
        foreach ($areas->ServiceTypes as $all_service):
            echo "<div class='custom-control custom-checkbox mr-1'>";
            echo "<input type='checkbox' onclick='getvehicles(this)' data-id='$all_service->id' class='custom-control-input all_services' name='services[]' id='service-$all_service->id' value='$all_service->id' required>";
            echo "<label class='custom-control-label' for='service-$all_service->id'>$all_service->serviceName</label>";
            echo "</div>";

            /*echo "<li>";
            echo "<div class='checkbox'>";
              echo "<label class='checkbox-inline'>";
                       echo"<input type='checkbox'
                                    name='services[]'
                                    class='category'
                                    value='$all_service->id'>";
                           echo"$all_service->serviceName";
               echo"</label>";
            echo"</div>";
        echo"</li>";*/
        endforeach;

    }

//    public function get_states(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'country_id' => 'required|exists:countries,id',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return $errors;
//            exit();
//        }
//        $val = $request->country_id;
//        $merchant_id = \Auth::user('merchant')->parent_id != 0 ? \Auth::user('merchant')->parent_id : \Auth::user('merchant')->id;
//        $state = State::where([['merchant_id', $merchant_id], ['country_id', $val], ['status', true]])->get();
//        if ($state->isNotEmpty()):
//            echo "<option value=>" . trans('admin.select_state') . "</option>";
//            foreach ($state as $key => $raw):
//                /*if(empty($raw->langstateoneview)) :
//                    $raw['name'] = $raw->langstateanyviews['name'];
//                else :
//                    $raw['name'] = $raw->langstateoneview['name'];
//                endif;*/
//                echo "<option value=" . $raw['id'] . ">" . $raw->Name . "</option>";
//            endforeach;
//        else:
//            echo "<option value=>" . trans('admin.no_state_found') . "</option>";
//        endif;
//    }

//    public function get_cities(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'state_id' => 'required|exists:states,id',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return $errors;
//            exit();
//        }
//        $val = $request->state_id;
//        $merchant_id = \Auth::user('merchant')->parent_id != 0 ? \Auth::user('merchant')->parent_id : \Auth::user('merchant')->id;
//        $city = Town::where([['merchant_id', $merchant_id], ['state_id', $val], ['status', true]])->get();
//        if ($city->isNotEmpty()):
//            echo "<option value=>" . trans('admin.select_town') . "</option>";
//            foreach ($city as $key => $raw):
//                /*if(empty($raw->langstateoneview)) :
//                    $raw['name'] = $raw->langstateanyviews['name'];
//                else :
//                    $raw['name'] = $raw->langstateoneview['name'];
//                endif;*/
//                echo "<option value=" . $raw['id'] . ">" . $raw->Name . "</option>";
//            endforeach;
//        else:
//            echo "<option value=>" . trans('admin.no_town_found') . "</option>";
//        endif;
//    }

    public function CheckPool(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        $area_id = $request->area_id;
        $areas = CountryArea::whereHas('ServiceTypes', function ($query) {
            $query->where([['service_type_id', '=', 5]]);
        })->with(['ServiceTypes' => function ($query) {
            $query->where([['service_type_id', '=', 5]]);
        }])->where([['id', '=', $area_id]])->first();
        echo "<option value=''>" . trans("$string_file.select") . "</option>";
        if (!empty($areas->ServiceTypes)) {
            foreach ($areas->ServiceTypes as $value) {
                echo "<option value='" . $value->id . "'>" . $value->serviceName . "</option>";
            }
        }

    }

    public function VehicleType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        $data = CountryArea::with(['VehicleType' => function ($query) {
            $query->where('admin_delete',NULL);
        }])
            ->find($request->area_id);
        $merchant = $data->Merchant;
        $string_file = $this->getStringFile(NULL, $merchant);
        echo "<option value=''>" . trans("$string_file.select") . "</option>";
        $arr_vehicle = $data->VehicleType->unique();
        foreach ($arr_vehicle as $vehicle) {
            echo "<option value='" . $vehicle->id . "'>" . $vehicle->VehicleTypeName . "</option>";
        }
    }

    public function VehicleSegment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_id' => 'required',
            'vehicle_type_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        $vehicle_type_id = $request->vehicle_type_id;
        $sub_group_for_admin = $request->sub_group_for_admin;
        $data = CountryArea::with(['Segment' => function ($q) use ($vehicle_type_id, $sub_group_for_admin) {
            $q->join('country_area_vehicle_type as cavt', 'cavt.segment_id', '=', 'segments.id');
            $q->where('cavt.vehicle_type_id', $vehicle_type_id);
            if (!empty($sub_group_for_admin)) {
                $q->where('segments.sub_group_for_admin', $sub_group_for_admin);
            }
        }])
            ->find($request->area_id);

        $merchant = $data->Merchant;
        $string_file = $this->getStringFile(NULL, $merchant);
        echo "<option value=''>" . trans("$string_file.select") . "</option>";
        $arr_segment = $data->Segment->unique();
        $permission_segments = get_permission_segments(1, true);
        foreach ($arr_segment as $value) {
            if (in_array($value->slag, $permission_segments)) {
                $name = !empty($value->Name()) ? $value->Name() : $value->slag;
                echo "<option value='" . $value->id . "'>" . $name . "</option>";
            }
        }
    }


    public function VehicleModel(Request $request)
    {
        $request->validate([
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'vehicle_make_id' => 'required|exists:vehicle_makes,id',
        ]);
        $data = VehicleModel::where([['vehicle_type_id', '=', $request->vehicle_type_id], ['vehicle_make_id', '=', $request->vehicle_make_id],['admin_delete', '=', NULL]])->get();
        foreach ($data as $vehicle) {
            if (!empty($vehicle->LanguageVehicleModelSingle)) {
                $name = $vehicle->LanguageVehicleModelSingle->vehicleModelName;
            } else {
                $name = $vehicle->LanguageVehicleModelAny->vehicleModelName;
            }
            echo "<option value='" . $vehicle->id . "'>" . $name . "</option>";
        }
    }

//    public function VehicleConfig(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'manual_area' => 'required',
//            'service' => 'required',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return $errors;
//            exit();
//        }
//        $manual_area = $request->manual_area;
//        $area = CountryArea::find($manual_area);
//        $merchant_id = $area->merchant_id;
//        $areaName = $area->CountryAreaName;
//        $service = $request->service;
////        switch ($service) {
////            case "1":
////                $data = CountryArea::with(['VehicleType' => function ($query) use ($service) {
////                    $query->where([['service_type_id', '=', $service]]);
////                }])->find($manual_area);
////                echo "<option value=''>" . trans("$string_file.select") . "</option>";
////                foreach ($data->VehicleType as $vehicle) {
////                    echo "<option value='" . $vehicle->id . "'>" . $vehicle->VehicleTypeName . "</option>";
////                }
////                break;
////            case "2":
////            case "3":
////                $data = CountryArea::with(['Package' => function ($query) use ($service) {
////                    $query->where([['country_area_package.service_type_id', '=', $service]]);
////                }])->find($manual_area);
////                echo "<option value=''>" . trans('admin.message538') . "</option>";
////                foreach ($data->Package as $package) {
////                    echo "<option value='" . $package->id . "'>" . $package->PackageName . "</option>";
////                }
////                break;
////            case "4":
////                $data = OutstationPackage::where([['merchant_id', '=', $area->merchant_id]])->get();
////                echo "<option value=''>" . trans('admin.message539') . "</option>";
////                foreach ($data as $package) {
////                    echo "<option value='" . $package->id . "'>" . $areaName . " -> " . $package->PackageName . "</option>";
////                }
////                break;
////            case "5":
////                $data = VehicleType::where([['merchant_id', '=', $merchant_id], ['pool_enable', '=', 1]])->get();
////                echo "<option value=''>" . trans("$string_file.select") . "</option>";
////                foreach ($data as $vehicle) {
////                    echo "<option value='" . $vehicle->id . "'>" . $vehicle->VehicleTypeName . "</option>";
////                }
////                break;
////            default :
////                $data = CountryArea::with(['VehicleType' => function ($query) use ($service) {
////                    $query->where([['service_type_id', '=', $service]]);
////                }])->find($manual_area);
////                echo "<option value=''>" . trans("$string_file.select") . "</option>";
////                foreach ($data->VehicleType as $vehicle) {
////                    echo "<option value='" . $vehicle->id . "'>" . $vehicle->VehicleTypeName . "</option>";
////                }
////                break;
////        }
//    }

    public function ServiceConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merchant_id' => 'required',
            'service_type_id' => 'required',
            'additional_support' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }

        $merchant = \App\Models\Merchant::find($request->merchant_id);
        $additional_support = $request->additional_support;
        $service_type_id = $request->service_type_id;
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL, $merchant);
        switch ($additional_support) {
            case "1":
                $arr_package = ServicePackage::where([['service_type_id', '=', $service_type_id], ['merchant_id', '=', $merchant_id]])->get();
                echo "<option value=''>" . trans("$string_file.select") . "</option>";
                foreach ($arr_package as $package) {
                    echo "<option value='" . $package->id . "'>" . $package->PackageName . "</option>";
                }
                break;
            case "2":
                $data = OutstationPackage::where([['service_type_id', '=', $service_type_id], ['merchant_id', '=', $merchant_id]])->get();
                echo "<option value=''>" . trans("$string_file.select") . "</option>";
                foreach ($data as $package) {
                    echo "<option value='" . $package->id . "'>" . $package->PackageName . "</option>";
                }
                break;
        }
    }


    public function SmsGatewayParams(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'smsgateway_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        $params = SmsGateways::where('id', '=', $request->smsgateway_id)->value('params');
        $jsonparams = json_decode($params, true);
        $html = '';
        foreach ($jsonparams as $i => $v) {
            $html .= '<div class="form-group"><label>' . $v . '</label><input type="text" name="' . $i . '" placeholder="Enter ' . $v . '" class="form-control" required ></div>';
        }
        echo $html;
    }


    public function PaymentGatewayParams(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_option_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        $params = PaymentOption::where('id', '=', $request->payment_option_id)->value('params');
        $jsonparams = json_decode($params, true);
        $html = '';
        foreach ($jsonparams as $i => $v) {
            $html .= '<div class="form-group"><label>' . $v . '</label><input type="text" name="' . $i . '" placeholder="Enter ' . $v . '" class="form-control" required ></div>';
        }
        echo $html;
    }

    /*
     * Code merged by @Amba
     * Delivery code
     * */


//    public function getVehicleTypesByDelivery(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'delivery_type' => 'required',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return $errors;
//            exit();
//        }
//
//        $vehicle_types = VehicleType:: get();
//        echo "<option value=''>" . trans("$string_file.select") . "</option>";
//        foreach ($vehicle_types as $vehicle) {
//            $selected = ($request->vehicle_type == $vehicle->id) ? "selected" : "";
//            echo "<option " . $selected . " value='" . $vehicle->id . "'>" . $vehicle->VehicleTypeName . "</option>";
//        }
//
//
//    }

//    public function getDeliveryTypes(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'area_id' => 'required',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return $errors;
//            exit();
//        }
//        $area_id = $request->area_id;
//        $areas = CountryArea::with(['deliveryTypes'])->where([['id', '=', $area_id],['status', '=', 1]])->first();
//        echo "<option value=''>" . trans('admin.delivery_type_select') . "</option>";
//        foreach ($areas->deliveryTypes as $value) {
//            $selected = ($request->delivery_type == $value->id) ? 'selected' : '';
//            echo "<option " . $selected . " value='" . $value->id . "'>" . $value->name . "</option>";
//        }
//    }

    public function countryAreaSegment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $errors;
            exit();
        }
        $permission_segments = get_permission_segments(1, true);
        $arr_segment = $this->getCountryAreaSegment($request);
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL, $merchant);
        $option_type = isset($request->option_type) ? $request->option_type : "SELECT-BOX";
        $output = "";
        if(!empty($arr_segment)){
            switch ($option_type) {
                case "CHECK-BOX":  // For segment checkbox and select multiple
                    foreach ($arr_segment as $value) {
                        if (in_array($value->slag, $permission_segments)) {
                            $name = !empty($value->Name()) ? $value->Name() : $value->slag;
                            $output .= "<div class='col-md-2'><div class='checkbox-custom checkbox-primary'><input type='checkbox' name='segment_id[]' id='segment_id_$value->id' value='$value->id'><label for='segment_id_$value->id'>$name</label></div></div>";
                        }
                    }
                    break;
                case "SELECT-BOX":
                default:
                    $output = "<option value=''>" . trans("$string_file.select") . "</option>";
                    foreach ($arr_segment as $value) {
                        if (in_array($value->slag, $permission_segments)) {
                            $name = !empty($value->Name()) ? $value->Name() : $value->slag;
                            $output .= "<option value='" . $value->id . "'>" . $name . "</option>";
                        }
                    }
            }
        }else{
            $output = trans("$string_file.data_not_found");
        }
        echo $output;
    }

    function getCountryAreaSegment($request, $return = '')
    {
        if (!empty($request->area_id)) {
            $segment_group_id = $request->segment_group_id;
            $merchant_id = $request->merchant_id;
            if (empty($merchant_id)) {
                $merchant_id = get_merchant_id();
            }
            $sub_group_for_admin = $request->sub_group_for_admin;
            $sub_group_for_app = $request->sub_group_for_app;
            $check_where_or = isset($request->check_where_or) ? $request->check_where_or : false;
            $data = CountryArea::with(['Segment' => function ($q) use ($segment_group_id, $sub_group_for_admin, $merchant_id, $sub_group_for_app, $check_where_or) {
                if($check_where_or){
                    if (!empty($segment_group_id)) {
                        $q->where('segment_group_id', $segment_group_id);
                    }
                    if (!empty($sub_group_for_admin)) {
                        $q->orWhere('sub_group_for_admin', $sub_group_for_admin);
                    }
                    if (!empty($sub_group_for_app)) {
                        $q->orWhere('sub_group_for_app', $sub_group_for_app);
                    }
                }else{
                    if (!empty($segment_group_id)) {
                        $q->where('segment_group_id', $segment_group_id);
                    }
                    if (!empty($sub_group_for_admin)) {
                        $q->where('sub_group_for_admin', $sub_group_for_admin);
                    }
                    if (!empty($sub_group_for_app)) {
                        $q->where('sub_group_for_app', $sub_group_for_app);
                    }
                }
                $q->whereHas('Merchant', function ($q) use ($merchant_id) {
                    $q->where('merchant_id', $merchant_id);
                });
            }])
                ->where('status', 1)
            ->find($request->area_id);
            if (!empty($data))
            {
                $arr_segment = $data->Segment->unique();
                if ($return == 'dropdown') {
                    $arr_return = [];
                    foreach ($arr_segment as $value) {
                        $name = !empty($value->Name($merchant_id)) ? $value->Name($merchant_id) : $value->slag;
                        $arr_return[$value->id] = $name;
                    }
                    return $arr_return;
                }
                return $arr_segment;
            }
        }
        return [];
    }
}
