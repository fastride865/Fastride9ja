<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Country;
use App;
use App\Models\DeliveryType;
use App\Models\InfoSetting;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\PolygenController;
use App\Models\CountryArea;
use App\Models\Category;
use App\Models\VersionManagement;
use App\Traits\AreaTrait;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use View;
use Form;
use App\Traits\MerchantTrait;

class CountryAreaController extends Controller
{
    use AreaTrait, MerchantTrait;

    public function index(Request $request)
    {
        $checkPermission = check_permission(1, 'view_area');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $area_id = isset($request->area_id) ? $request->area_id : NULL;
        $area_query = $this->getAreaList(false,true);
        $arr_areas = $area_query->get();
        $areas =  $this->getAreaList(true,true,[],NULL,$area_id);
        $config = $merchant->Configuration;
        $segment_group_vehicle = false;
        $segment_group_handyman = false;
        $category_vehicle_type_module = $merchant->ApplicationConfiguration->home_screen_view;
        $merchant_segment_group = array_unique(array_pluck($merchant->Segment, 'segment_group_id'));
        if (in_array(1, $merchant_segment_group)) {
            $segment_group_vehicle = true;
        }
        if (in_array(2, $merchant_segment_group)) {
            $segment_group_handyman = true;
        }
        $arr_status = get_active_status("web",$string_file);
        $info_setting = InfoSetting::where('slug','COUNTRY_AREA')->first();
        return view('merchant.area.index', compact('areas', 'config', 'segment_group_vehicle', 'segment_group_handyman', 'area_id', 'category_vehicle_type_module','arr_status','info_setting','arr_areas'));
    }

    public function SaveLanguageArea($merchant_id, $country_area_id, $name)
    {
        App\Models\LanguageCountryArea::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'country_area_id' => $country_area_id
        ], [
            'AreaName' => $name,
        ]);
    }

    public function json_validate($string)
    {
        // decode the JSON data
        $result = json_decode($string);
        // switch and check possible JSON errors
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = ''; // JSON is valid // No error has occurred
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
            // PHP >= 5.3.3
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occured.';
                break;
        }
        if ($error !== '') {
            return $error;
        } else {
            return true;
        }
    }

    public function AreaList(Request $request)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $geo_fence = $request->geo_fence;
        $country_id = $request->country_id;
        $option_group = $request->option_group;
        $areas = $this->getMerchantCountryArea($this->getAreaList(false, true, [], $country_id)->get(), $geo_fence, $option_group,$string_file);
        if (count($areas) == 0) {
           $areas[] = trans("$string_file.no_service_area");
        } else {
            $areas = add_blank_option($areas, trans("$string_file.select"));
        }
        $select_box = Form::select("area", $areas, ['class' => 'form-control', 'id' => 'area', 'required' => true]);
        echo $select_box;
    }

    public function CountryConfig(Request $request)
    {
        $transaction_code = NULL;
        $country = Country::select('transaction_code')->find($request->id);
        if (!empty($country)) {
            $transaction_code = $country->transaction_code;
        }
        echo $transaction_code;
    }


    public function add(Request $request, $id = NULL)
    {
        $action_type = !empty($id) ? "edit_area" : "create_area";
        $checkPermission = check_permission(1, $action_type);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $is_demo = false;
        $arr_selected_segment = [];
        $selected_payment_method = [];
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $area = NULL;
        if (!empty($id)) {
            $area = CountryArea::with('ServiceTypes', 'VehicleType', 'Documents')->where([['merchant_id', '=', $merchant_id]])->find($id);
            $arr_selected_segment = array_pluck($area->Segment, 'id');
            $selected_payment_method = array_pluck($area->PaymentMethod, 'id');
            $is_demo = $merchant->demo == 1 && $area->id == 3 ?  true : false;
        }
        $countries = add_blank_option(get_merchant_country($merchant->Country), '--Select Country');
        $documents = get_merchant_document($merchant->Document);
        $allSegment = get_merchant_segment();
        $payment_method = $this->getMerchantPaymentMethod($merchant->PaymentMethod, $merchant_id);
        $timezones = \DateTimeZone::listIdentifiers();
        $config = $merchant->Configuration;
        $arr_enable = get_enable($string_file);
        $arr_status = get_active_status("web",$string_file);
        $info_setting = InfoSetting::where('slug','COUNTRY_AREA')->first();
//p($is_demo);
        return view(
            'merchant.area.form-step1',
            compact('merchant', 'config', 'timezones', 'countries', 'documents', 'allSegment', 'arr_enable', 'area', 'arr_selected_segment', 'payment_method', 'selected_payment_method','arr_status','info_setting','is_demo'));
    }

    public function save(Request $request, $id = NULL)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $validator = Validator::make($request->all(), [
            'country' => 'required_without:id',
            'name' => ['required',
                Rule::unique('language_country_areas', 'AreaName')->where(function ($query) use ($merchant_id, &$id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', \Config::get('app.locale')], ['country_area_id', '!=', $id]]);
                })],
            'driver_document' => 'required',
            'lat' => 'required_without:id',
//            'segment' => 'required',
            'timezone' => 'required|in:' . implode(',', \DateTimeZone::listIdentifiers())
        ],
            [
                'lat.required_without' => trans("$string_file.$string_file.draw_map"),
            ]
        );
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            $is_geofence = isset($request->is_geofence) ? $request->is_geofence : 2;
            if (empty($id)) {
                $latLOng = $request->lat ? $request->lat : $request->latlong;
                $lat_longs = $this->json_validate($latLOng);
                if ($lat_longs != true) {
                    return redirect()->back()->withErrors($lat_longs);
                }
                if (!json_decode($latLOng, true)) {
                    $message = trans("$string_file.invalid_json");
                    return redirect()->back()->withErrors($message);
                }
                $checkArea = PolygenController::DuplicateArea($latLOng, $merchant_id, $is_geofence);
                if (!empty($checkArea)) {
                    $area = CountryArea::find($checkArea['id']);
                    return redirect()->back()->withErrors(trans_choice("$string_file.intersecting_area",3, ['area' => $area->CountryAreaName]));
                }
            } else {
                if (!empty($request->lat)) {
                    $checkArea = PolygenController::DuplicateAreaEdit($request->lat, $merchant_id, $id);
                    if (!empty($checkArea)) {
                        $area = CountryArea::find($checkArea['id']);
                        return redirect()->back()->withErrors(trans_choice("$string_file.intersecting_area",3,['area' => $area->CountryAreaName]));
                    }
                }
            }
            ini_set('max_execution_time', '900');
//            $arr_segment = $request->input('segment');
            if (!empty($id)) {
                $area = CountryArea::Find($id);
                // existing driver document
                $existing_documents = array_pluck($area->Documents,'id');
                $new_document = $request->input('driver_document');
                $arr_removed_doc = array_diff($existing_documents,$new_document);
                if(!empty($arr_removed_doc))
                {
                    //make all document inactive which were removed
                    DB::table('driver_documents as dd')
                        ->join('drivers as d','dd.driver_id','=','d.id')
                        ->where('d.merchant_id',$merchant_id)
                        ->whereIn('dd.document_id',$arr_removed_doc)->delete();//update(['dd.status'=>2]);

                }
            } else {
                $area = new CountryArea;
                $area->merchant_id = $merchant_id;
            }
            $auto_upgradetion = 2;
            if ($request->auto_upgradetion != null) {
                $auto_upgradetion = $request->auto_upgradetion;
            }
            $manual_downgradation = 2;
            if ($request->manual_downgradation != null) {
                $manual_downgradation = $request->manual_downgradation;
            }
            $area->status = $request->status;
            $area->is_geofence = $is_geofence;
            $area->timezone = $request->timezone;
            $area->minimum_wallet_amount = $request->minimum_wallet_amount;
            $area->manual_downgradation = $manual_downgradation;
            $area->auto_upgradetion = $auto_upgradetion;
            $area->driver_cash_limit_amount = $request->driver_cash_limit_amount;

            if (!empty($request->lat)) {
                $area->AreaCoordinates = $request->lat;
            }
            if (!empty($request->country)) {
                $area->country_id = $request->country;
            }
            $area->save();
            $this->SaveLanguageArea($merchant_id, $area->id, $request->name);

            //$area->Segment()->sync($arr_segment);
            // personal document of driver for this area
            $area->Documents()->sync($request->input('driver_document'));
            // payment method for this area
            $area->PaymentMethod()->sync($request->input('payment_method'));
            VersionManagement::updateVersion($merchant_id);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            DB::rollback();
            p($message);
        }
        DB::commit();
        return redirect()->route('countryareas.add', $area->id)->withSuccess(trans("$string_file.saved_successfully"));
    }


    public function addStep2(Request $request, $area_id)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $area = CountryArea::with(['VehicleType'=>function($q) {
            $q->where('admin_delete',NULL);
        }])->select('id')->find($area_id);
        $string_file = $this->getStringFile(NULL, $area->Merchant);
        $is_demo = false;
        $segment_group_id = 1;
        $arr_segment_services = $this->getMerchantSegmentServices($merchant_id, '', $segment_group_id);

        $vehicles = get_merchant_vehicle($merchant->VehicleType);
//        $segment_group = get_segment_group();
        $documents = get_merchant_document($merchant->Document);
        $config = $merchant->Configuration;
        $arr_enable = get_enable($string_file);

        /************* for group 1 segments start **************/
        $arr_vehicle_selected_document = [];
        $arr_selected_vehicle_service = [];
        $selected_vehicle_document = $area->VehicleDocuments;
        $selected_vehicle_services = $area->VehicleType;

        // vehicle document
        foreach ($selected_vehicle_document as $document) {
            $arr_vehicle_selected_document[$document['pivot']->vehicle_type_id][] = $document['pivot']->document_id;
        }

        // vehicle's segment & services for group 1 segments
        foreach ($selected_vehicle_services as $service) {
//            if(isset($service['pivot']->pivotParent->Segment[0]->segment_group_id) && $service['pivot']->pivotParent->Segment[0]->segment_group_id == 1)
//            {
            $arr_selected_vehicle_service[$service['pivot']->vehicle_type_id][$service['pivot']->segment_id][] = $service['pivot']->service_type_id;
//            }
        }
        /************* for group 1 segments end **************/
        $info_setting = InfoSetting::where('slug','COUNTRY_AREA_CONFIGURATION')->first();
        return view('merchant.area.form-step2', compact('merchant', 'documents', 'config', 'vehicles', 'arr_enable', 'area',
            'arr_segment_services', 'arr_vehicle_selected_document', 'arr_selected_vehicle_service','info_setting','is_demo'));

    }

    public function vehicleTypeEdit(Request $request)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $area_id = $request->area_id;
        $vehicle_type_id = $request->vehicle_type_id;
        $is_demo = ($area_id == 3 && !empty($vehicle_type_id) && $merchant->demo == 1) ? true : false;
        $area = CountryArea::where(function ($q) {
            if (!empty($vehicle_type_id)) {
                $q->whereHas('VehicleType', function ($q) use ($vehicle_type_id) {
                    $q->where('vehicle_type_id', $vehicle_type_id);

                });
            }
        })->select('id')->where('id', $area_id)->first();
        $segment_group_id = 1;
//        $segment_exist = $this->checkAreaVehicleExist($area_id, $vehicle_type_id);
        $arr_segment_services = $this->getMerchantSegmentServices($merchant_id, '', $segment_group_id, [], NULL, true);

        if (!empty($vehicle_type_id)) {
            $area_added_vehicle = [$vehicle_type_id];
            $vehicle_list = $merchant->VehicleType->whereIn('id', $area_added_vehicle);
        } else {
            $area_added_vehicle = $area->VehicleType;
            $area_added_vehicle = array_unique(array_pluck($area_added_vehicle, 'id'));
            $vehicle_list = $merchant->VehicleType->whereNotIn('id', $area_added_vehicle);
        }

        $vehicles = get_merchant_vehicle($vehicle_list);
        $documents = get_merchant_document($merchant->Document);
        $config = $merchant->Configuration;
//        $arr_enable = get_enable();

        /************* for group 1 segments start **************/
        $arr_vehicle_selected_document = [];
        $arr_selected_vehicle_service = [];
        $selected_vehicle_document = $area->VehicleDocuments;
        $selected_vehicle_services = $area->VehicleType;

        // vehicle document
        foreach ($selected_vehicle_document as $document) {
            $arr_vehicle_selected_document[$document['pivot']->vehicle_type_id][] = $document['pivot']->document_id;
        }

        // vehicle's segment & services for group 1 segments
        foreach ($selected_vehicle_services as $service) {
//            if(isset($service['pivot']->pivotParent->Segment[0]->segment_group_id) && $service['pivot']->pivotParent->Segment[0]->segment_group_id == 1)
//            {
            $arr_selected_vehicle_service[$service['pivot']->vehicle_type_id][$service['pivot']->segment_id][] = $service['pivot']->service_type_id;
//            }
        }
        /************* for group 1 segments end **************/


        //$rentalpackages = get_merchant_package($merchant->Package->where('service_type_id', 2)->where('packageStatus',1));

        $vehicle_type_config = View::make('merchant.area.edit-vehicle-config', compact('merchant', 'documents', 'config', 'vehicles', 'area',
            'arr_segment_services', 'arr_vehicle_selected_document', 'arr_selected_vehicle_service', 'vehicle_type_id','is_demo'))->render();
        echo $vehicle_type_config;
    }

    public function syncCountryArea($area, $segment_group_id, $action, $step)
    {
        if ($action == 'before') {
            //delete all segments of segment group
            DB::table('country_area_segment as cas')
                ->join('segments as s', 's.id', '=', 'cas.segment_id')
                ->where('s.segment_group_id', $segment_group_id)
                ->where('cas.country_area_id', $area->id)
                ->delete();

            //delete all services of segment group
            DB::table('country_area_service_type as cast')
                ->join('service_types as st', 'st.id', '=', 'cast.service_type_id')
                ->join('segments as s', 's.id', '=', 'st.segment_id')
                ->where('s.segment_group_id', $segment_group_id)
                ->where('cast.country_area_id', $area->id)
                ->delete();
        } elseif ($action == 'after') {
            if ($step == 2) {
                // insert segments in country area segment
                $arr_segment_services = DB::table('country_area_vehicle_type as cavt')
                    ->where('cavt.country_area_id', $area->id)->get();
                $arr_segment = array_unique(array_pluck($arr_segment_services, 'segment_id'));
                foreach ($arr_segment as $segment) {
                    $area->Segment()->attach($segment);
                }
                $data = json_decode($arr_segment_services, true);
                $arr_services_data = array_column($data, NULL, 'service_type_id');
                $arr_services = array_unique(array_keys($arr_services_data));
                foreach ($arr_services as $service) {
                    $area->ServiceTypes()->attach($service, ['segment_id' => $arr_services_data[$service]['segment_id']]);
                }
            }
        }
    }

    public function saveStep2(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'required',
            'vehicle_service_type' => 'required',
//           'pool_position' => 'required_with:pool_enable',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            $segment_group_id = 1;
            $area = CountryArea::Find($id);
            $string_file = $this->getStringFile(NULL,$area->Merchant);
            $vehicle_type_id = $request->input('vehicle_type');
            $vehicle_document = $request->input('vehicle_document');
            $vehicle_service_type = $request->input('vehicle_service_type');

            // Get All the driver vehicle documents, which is different from saved documents
            $existing_vehicle_documents = $area->VehicleDocuments()->wherePivot("vehicle_type_id", $vehicle_type_id)->get()->pluck("id")->toArray();
            $except_vehicle_documents = array_diff($existing_vehicle_documents,$vehicle_document);
            $delete_vehicle_documents = [];
            if(!empty($except_vehicle_documents)){
                $delete_vehicle_documents = App\Models\DriverVehicleDocument::whereHas("DriverVehicle",function($q) use($area, $vehicle_type_id){
                    $q->whereHas("Driver",function($k) use($area){
                        $k->where([["country_area_id","=",$area->id]]);
                    })->where("merchant_id",$area->merchant_id)->where("vehicle_type_id",$vehicle_type_id);
                })->whereIn("document_id",$except_vehicle_documents)->get()->pluck("id")->toArray();
            }

            // its for group = 1 segments [taxi, food, grocery]
            $area->VehicleType()->detach($vehicle_type_id);
            $area->VehicleDocuments()->wherePivot('vehicle_type_id', $vehicle_type_id)->detach();
            $this->syncCountryArea($area, $segment_group_id, 'before', 2);

            $vehicle_segment = array_keys($vehicle_service_type);
            foreach ($vehicle_segment as $segment) {
                // attach vehicle and service type
                $segment_services = isset($vehicle_service_type[$segment]) ? $vehicle_service_type[$segment] : [];
                if (!empty($segment_services)) {
                    foreach ($segment_services as $service) {
                        $area->VehicleType()->attach($vehicle_type_id, ['service_type_id' => $service, 'segment_id' => $segment]);
                    }
                }
            }
            // save vehicle document
            $arr_vehicle_doc = !empty($vehicle_document) ? $vehicle_document : [];
            foreach ($arr_vehicle_doc as $vehicle_doc) {
                $area->VehicleDocuments()->attach($vehicle_doc, ['vehicle_type_id' => $vehicle_type_id]);
            }

            // delete driver vehicle documents, which is not configure in the service area
            if(!empty($delete_vehicle_documents)){
                App\Models\DriverVehicleDocument::whereIn('id',$delete_vehicle_documents)->delete();
            }

            $this->syncCountryArea($area, $segment_group_id, 'after', 2);
            VersionManagement::updateVersion($area->merchant_id);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
//        return redirect()->route('countryareas.index')->with('areaadded', trans('admin.configuration.added'));
    }

    public function deleteStep2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_type_id' => 'required',
            'country_area_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            $id = $request->country_area_id;
            $area = CountryArea::Find($id);
            $segment_group_id = 1;
            $vehicle_type_id = $request->input('vehicle_type_id');
            // its for group = 1 segments [taxi, food, grocery]
            // delete data form servcies and segment of country
            $this->syncCountryArea($area, $segment_group_id, 'before', 2);

            $area->VehicleType()->detach($vehicle_type_id);
            $area->VehicleDocuments()->wherePivot('vehicle_type_id', $vehicle_type_id)->detach();

            // update services and segment of country area table
            $this->syncCountryArea($area, $segment_group_id, 'after', 2);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            echo $message;
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        $string_file = $this->getStringFile(NULL,$area->Merchant);
        echo trans("$string_file.vehicle_deleted_from_area");
    }

    public function addStep3(Request $request, $area_id)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $area = CountryArea::select('id')->find($area_id);

        $is_demo = $merchant->demo == 1 && $area->id == 3 ?  true : false;
        $segment_group_id = 2;
        $arr_segment_services = $this->getMerchantSegmentServices($merchant_id, '', $segment_group_id);
        $documents = get_merchant_document($merchant->Document);
        $config = $merchant->Configuration;
//        $arr_enable = get_enable();

        /************* for group 2 segments start **************/
        $arr_selected_segment_service = [];
        $arr_segment_selected_document = [];
        $selected_segment_document = $area->SegmentDocument;
        $selected_segment_services = $area->ServiceTypes;

        // segment's services for group 2 segments
        foreach ($selected_segment_services as $segment_services) {
            $group_2_segment = $segment_services['pivot']->pivotParent->Segment->where('segment_group_id', 2);
            $group_2_segment = array_pluck($group_2_segment, 'id');
            if (in_array($segment_services['pivot']->segment_id, $group_2_segment)) {
                $arr_selected_segment_service[$segment_services['pivot']->segment_id][] = $segment_services['pivot']->service_type_id;
            }
        }
        // segment's document for group 2
        foreach ($selected_segment_document as $segment_document) {
            $arr_segment_selected_document[$segment_document['pivot']->segment_id][] = $segment_document['pivot']->document_id;
        }

        /************* for group 2 segments end **************/
        $info_setting = InfoSetting::where('slug','COUNTRY_AREA_CONFIGURATION')->first();
        return view('merchant.area.form-step3', compact('merchant', 'documents', 'config', 'area',
            'arr_segment_services', 'arr_selected_segment_service', 'arr_segment_selected_document','info_setting','is_demo'));
    }

    public function saveStep3(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
//            'segment_service_type' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {

            $segment_group_id = 2;
            $area = CountryArea::Find($id);
            $string_file = $this->getStringFile(NULL,$area->Merchant);

            $arr_segment_service = $request->input('segment_service_type');
            $segment_document = $request->input('segment_document');

            // its for group = 2 segments [Plumber, Car Painting, Cleaning]

            // delete documents of segment group 2's segments from country area
            $area->SegmentDocument()->detach();

            $this->syncCountryArea($area, $segment_group_id, 'before', 3);

            if (!empty($arr_segment_service)) {
                foreach ($arr_segment_service as $segment => $segment_services) {
                    if (!empty($segment_services)) {
                        // add segment services
                        foreach ($segment_services as $service) {
                            $area->ServiceTypes()->attach($service, ['segment_id' => $segment]);
                        }
                    }
                    // add segment document
                    $arr_segment_doc = isset($segment_document[$segment]) ? $segment_document[$segment] : [];
                    foreach ($arr_segment_doc as $segment_doc) {
                        $area->SegmentDocument()->attach($segment_doc, ['segment_id' => $segment]);
                    }

                    $area->Segment()->attach($segment);
                }
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function checkAreaVehicleExist($area_id, $vehicle_type_id = null)
    {
        $food_segment_id = App\Models\Segment::whereIn('slag', ['FOOD'])->pluck('id')->first();
        $exist_food_vehicle = App\Models\VehicleType::whereHas('CountryArea', function ($q) use ($food_segment_id, $area_id) {
            $q->where('segment_id', $food_segment_id);
            $q->where('country_area_id', $area_id);
        })->where(function ($q) use ($vehicle_type_id) {
            if (!empty($vehicle_type_id)) {
                $q->where('id', '!=', $vehicle_type_id);
            }
        })->count();
        $grocery_segment_id = App\Models\Segment::whereIn('slag', ['GROCERY'])->pluck('id')->first();
        $exist_grocery_vehicle = App\Models\VehicleType::whereHas('CountryArea', function ($q) use ($grocery_segment_id, $area_id) {
            $q->where('segment_id', $grocery_segment_id);
            $q->where('country_area_id', $area_id);
        })->where(function ($q) use ($vehicle_type_id) {
            if (!empty($vehicle_type_id)) {
                $q->where('id', '!=', $vehicle_type_id);
            }
        })->count();
        $exist_array = [];
        if ($exist_food_vehicle > 0) {
            array_push($exist_array, $food_segment_id);
        }
        if ($exist_grocery_vehicle > 0) {
            array_push($exist_array, $grocery_segment_id);
        }
        return $exist_array;
    }

    // vehicle type categorization means four step of area
    public function vehicleCategorization(Request $request, $area_id)
    {
        $segment_id = 1;
        $segment_group_id = 1;
        $merchant = get_merchant_id(false);
        // only in edit case
        $is_demo = $merchant->demo == 1 && $area_id == 3 ?  true : false;
        $merchant_id = $merchant->id;
        $arr_services = [1, 2]; // only for taxi segment with normal and rental service
        $arr_selected_vehicle = [];
        $area = CountryArea::select('id')
            ->with(['VehicleType' => function ($q) use ($merchant_id, $segment_id, $area_id, $arr_services) {
                $q->select('id', 'service_type_id', 'country_area_id', 'vehicle_type_id', 'segment_id');
                $q->where([['segment_id', '=', $segment_id], ['country_area_id', '=', $area_id]]);
                $q->whereIn('service_type_id', $arr_services);
            }])
            ->whereHas('VehicleType', function ($q) use ($merchant_id, $segment_id, $area_id, $arr_services) {
                $q->select('id', 'service_type_id', 'country_area_id', 'vehicle_type_id', 'segment_id');
                $q->where([['segment_id', '=', $segment_id], ['country_area_id', '=', $area_id]]);
                $q->whereIn('service_type_id', $arr_services);
            })
            ->find($area_id);

        $arr_category = Category::select('id','merchant_id')
            ->whereHas('Segment', function ($q) use ($merchant_id, $segment_id) {
                $q->where([['segment_id', '=', $segment_id]]);
            })
            ->where([['merchant_id', '=', $merchant_id], ['delete', '=', NULL], ['status', '=', 1]])->get();
        $arr_segment = [$segment_id]; // only for taxi segment

        $arr_segment_services = $this->getMerchantSegmentServices($merchant_id, '', $segment_group_id, $arr_segment, $area_id, false, $arr_services);
        $segment = $arr_segment_services[$segment_id];
        // merchant's enabled services
        $area_services = [];
        foreach($segment['arr_services'] as $key => $service) {
            $area_services[$key] = $service['id'];
        }

        foreach ($area_services as $service => $service_id) {
            $arr_vehicle[$service_id] = get_merchant_vehicle($area->VehicleType->where('service_type_id', $service_id));
            foreach ($arr_category as $category) {
                $check_data = DB:: table('category_vehicle_type')->select('vehicle_type_id')->where([['country_area_id', '=', $area_id], ['service_type_id', '=', $service_id], ['segment_id', '=', $segment_id], ['category_id', '=', $category->id]])->get()->toArray();
                $selected_vehicle = array_column($check_data, 'vehicle_type_id');
                $arr_selected_vehicle[$service_id][$category->id] = $selected_vehicle;
            }
        }
        $info_setting = InfoSetting::where('slug','COUNTRY_AREA_CONFIGURATION')->first();
        return view('merchant.area.form-step4', compact('area',
            'segment', 'arr_category', 'arr_vehicle', 'segment_id', 'arr_selected_vehicle','info_setting','is_demo'));
    }

    // save vehicle type categorization means four step of area
    public function saveVehicleCategorization(Request $request, $area_id)
    {
        $merchant_id = get_merchant_id();
        $validator = Validator::make($request->all(), [
                'segment_id' => 'required', // country area id
                'country_area_id' => 'required', // country area id
                'service_type_id' => 'required',
                'service_category' => 'required',
                'category_vehicle' => 'required',
            ]
        );
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            $string_file = $this->getStringFile($merchant_id);
            $segment_id = $request->input('segment_id');
            $country_area_id = $request->input('country_area_id');
            $arr_service_type = $request->input('service_type_id');
            $service_category = $request->input('service_category');
            $category_vehicle = $request->input('category_vehicle');
            DB::table('category_vehicle_type')->where('country_area_id', $country_area_id)
                ->whereIn('service_type_id', $arr_service_type)->delete();
            foreach ($arr_service_type as $service) {
                $arr_category = isset($service_category[$service]) ? $service_category[$service] : [];
                foreach ($arr_category as $category) {
                    $arr_vehicle = isset($category_vehicle[$service][$category]) ? $category_vehicle[$service][$category] : [];
                    foreach ($arr_vehicle as $vehicle_type) {
                        $check_data = DB:: table('category_vehicle_type')->where([['country_area_id', '=', $country_area_id], ['service_type_id', '=', $service], ['segment_id', '=', $segment_id], ['vehicle_type_id', '=', $vehicle_type]])->count();
                        if ($check_data == 0) {
                            $insert_array = ['country_area_id' => $country_area_id, "segment_id" => $segment_id, "category_id" => $category, "vehicle_type_id" => $vehicle_type, "service_type_id" => $service];
                            DB:: table('category_vehicle_type')->insert($insert_array);
                        } else {
                            return redirect()->back()->withErrors(trans("$string_file.duplicate_vehicle_type_in_category"));
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            DB::rollback();
            return redirect()->back()->withErrors($message);
        }
        DB::commit();
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }
}
