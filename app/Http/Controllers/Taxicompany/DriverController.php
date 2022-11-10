<?php

namespace App\Http\Controllers\Taxicompany;

use App\Models\Booking;
use App\Models\Document;
use App\Models\DriverConfiguration;
use App\Models\DriverSegmentDocument;
use App\Models\DriverSubscriptionRecord;
use App\Traits\DriverVehicleTrait;
use App\Traits\MerchantTrait;
use App\Models\ApplicationConfiguration;
use App\Models\Configuration;
use App\Models\Country;
use App\Models\CountryArea;
use App\Models\Driver;
use App\Models\DriverDocument;
use App\Models\DriverRideConfig;
use App\Models\DriverVehicle;
use App\Models\DriverVehicleDocument;
use App\Models\Merchant;
use App\Models\Onesignal;
use App\Models\PromotionNotification;
use App\Models\RejectReason;
use App\Models\VehicleMake;
use App\Models\VehicleType;
use App\Traits\AreaTrait;
use App\Traits\DriverTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\View;

class DriverController extends Controller
{
    use ImageTrait, DriverTrait, AreaTrait, MerchantTrait, DriverVehicleTrait;
    public function index(Request $request)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $drivers = $this->getAllDriver(true, $request, true);
        $basicDriver = Driver::where([['taxi_company_id', '=', $taxicompany->id], ['signupStep', '=', 1]])->latest()->count();
        $config = Merchant::find($merchant_id);
        $config->driver_wallet_status = $config->Configuration->driver_wallet_status;
        $config->subscription_package = $config->Configuration->subscription_package;
        $config->gender = $config->ApplicationConfiguration->gender;
        $config->smoker = $config->ApplicationConfiguration->smoker;
        $areas = CountryArea::where([['merchant_id', '=', $merchant_id]])->get();
        $request->request->add(['search_route' => route('taxicompany.driver.index')]);
        $search_view = $this->driverSearchView($request);
        $tempDocUploaded = $this->getAllTempDocUploaded(false)->count();
        return view('taxicompany.driver.index', compact('search_view','drivers', 'config','basicDriver', 'areas', 'tempDocUploaded'));
    }

    public function add(Request $request, $id = NULL)
    {
        $taxicompany = get_taxicompany();
        $merchant = $taxicompany->Merchant;
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL, $merchant);
        $areas = add_blank_option([], trans("$string_file.area"));
        $driver = NULL;
        $country_area_id = NULL;
        $driver_additional_data = NULL;
        if (!empty($id)) {
            $driver = Driver::where("taxi_company_id",$taxicompany->id)->Find($id);
            $country_area_id = $driver->country_area_id;
            if (!empty($driver->driver_additional_data)) {
                $driver_additional_data = (object)json_decode($driver->driver_additional_data, true);
            }
            $pre_title = trans("$string_file.edit");
            $areas = $this->getMerchantCountryArea($this->getAreaList(false, true, [], $driver->country_id, null,true)->get());
            $areas = add_blank_option($areas, trans("$string_file.area"));
        } else {
            $pre_title = trans("$string_file.add");
        }
        $title = $pre_title . ' ' . trans($string_file . '.driver');

        $merchant_obj = new \App\Http\Controllers\Helper\Merchant;
        $countries = $merchant_obj->CountryList($merchant);

        $config = $merchant;
        $group = $this->segmentGroup($merchant_id, "drop_down", $string_file);
        $config->bank_details = $config->Configuration->bank_details_enable;
        $config->driver_wallet_status = $config->Configuration->driver_wallet_status;
        $config->driver_address = $config->Configuration->driver_address;
        $config->gender = $config->ApplicationConfiguration->gender;
        $config->smoker = $config->ApplicationConfiguration->smoker;
        $account_types = $config->AccountType->where('admin_delete','!=',1);
        $personal_document = $this->personalDocument($id, $country_area_id);

        return view('taxicompany.driver.create', compact('driver', 'areas', 'countries', 'group', 'config', 'account_types', 'driver_additional_data', 'personal_document', 'title'));
    }

    public function save(Request $request, $id = NULL)
    {
//        p($request->all());
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $string_file = $this->getStringFile(NULL, $taxicompany->Merchant);
        $taxicompany_id = $taxicompany->id;
        $request->request->add(['phone' => $request->isd . $request->phone]);
        $validator_array = [
            'first_name' => 'required',
            'country' => 'required_without:id',
            'email' => ['required', 'email',
                Rule::unique('drivers', 'email')->where(function ($query) use ($merchant_id, $id) {
                    return $query->where([['driver_delete', '=', NULL], ['merchant_id', '=', $merchant_id], ['id', '!=', $id]]);
                })],
            'phone' => ['required',
                Rule::unique('drivers', 'phoneNumber')->where(function ($query) use ($merchant_id, $id) {
                    return $query->where([['driver_delete', '=', NULL], ['merchant_id', '=', $merchant_id], ['id', '!=', $id]]);
                })],
            'password' => 'required_without:id|confirmed',
            'area' => 'required_without:id',
            'image' => 'required_without:id|file',
            'address_line_1' => 'required',
            'city_name' => 'required',
            'address_postal_code' => 'required',
        ];
        $validator = Validator::make($request->all(), $validator_array);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            $driver_additional_data = NULL;
            if (!empty($id)) {
                $driver = Driver::Find($id);
            } else {
                $driver = new Driver();
            }
            $driver_additional_data = array("pincode" => $request->address_postal_code, "address_line_1" => $request->address_line_1, "city_name" => $request->city_name);
            $driver_additional_data = json_encode($driver_additional_data, true);

            $driver_store_data = [
                'merchant_id' => $merchant_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phoneNumber' => $request->phone,
                'driver_gender' => $request->driver_gender,
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'account_type_id' => $request->account_types,
                'online_code' => $request->online_transaction,
                'term_status' => 1,
                'last_ride_request_timestamp' => date("Y-m-d H:i:s"),
                'driver_referralcode' => $driver->GenrateReferCode(),
                'driver_additional_data' => $driver_additional_data,
                'pay_mode' => isset($request->pay_mode) ? $request->pay_mode : 2,// default commission based
            ];

            if (empty($id)) {
                $driver_store_data['taxi_company_id'] = $taxicompany->id;
                $driver_store_data['country_id'] = $request->country;
                $driver_store_data['term_status'] = 1;
                $driver_store_data['segment_group_id'] = isset($request->segment_group_id) ? $request->segment_group_id : NULL;
                $driver_store_data['signupStep'] = 4;
                $driver_store_data['segment_group_id'] = 1;
            } elseif (!empty($driver->id) && ($driver->signupStep == 1 || $driver->signupStep == 2 || $driver->signupStep == 3)) {
                $driver_store_data['signupStep'] = 4;
            }
            if (empty($id) || (!empty($driver->id) && empty($driver->country_area_id))) {
                if(!empty($request->area)){
                    $driver_store_data['country_area_id'] = $request->area;
                }else{
                    throw new \Exception(trans("The area field is required"));
                }
            }
            if (!empty($request->password)) {
                $driver_store_data['password'] = Hash::make($request->password);
            }
            if (!empty($request->hasFile('image'))) {
                $driver_store_data['profile_image'] = $this->uploadImage('image', 'driver', $merchant_id);
            }

            $driver = Driver::updateOrCreate(['id' => $id], $driver_store_data);

            DriverRideConfig::create(['driver_id' => $id], [
                'driver_id' => $driver->id,
                'smoker_type' => $request->smoker_type,
                'allow_other_smoker' => $request->allow_other_smoker,
            ]);
            // upload personal document of driver
            $all_doc = $request->input('all_doc');
            if (!empty($all_doc)) {
                $expiredate = $request->expiredate;
                $images = $request->file('document');
                $document_number = $request->document_number;
                $custom_document_key = "driver_document";
                $this->uploadDocument($driver->id, $custom_document_key, $all_doc, $images, $expiredate, $document_number);
            }
        } catch (\Exception $e) {
            DB::rollback();
            p($e->getTraceAsString());
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
        DB::commit();
        $message = trans("$string_file.saved_successfully");
        $vehicle_id = isset($driver->DriverVehicles[0]) ? $driver->DriverVehicles[0]->id : NULL;
        return redirect()->route('taxicompany.driver.vehicle.create', [$driver->id, $vehicle_id])->withSuccess($message);
    }

    public function show($id)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $handyman_segment = NULL;
        $arr_segment = [];
        $arr_days = [];
        $rejectreasons = RejectReason::where([['merchant_id', '=', $merchant_id], ['status', '=', 1]])->get();
        $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $driver = Driver::with(['DriverDocument' => function ($query) {
            $query->with('Document');
        }])->where('id', $id)->first();
        $driver_id = $driver->id;
        $driver_config = DriverRideConfig::select('latitude', 'longitude', 'radius')->where('driver_id', $driver->id)->first();
        $driver_wallet = DB::table('driver_wallet_transactions')->select(DB::raw('SUM(amount) as wallet_amount'))->where(['merchant_id' => $merchant_id, 'driver_id' => $driver->id])->first();
        $tempDocUploaded = $this->getAllTempDocUploaded(false, $driver->id)->count();
        $vehicle_details = isset($driver->DriverVehicles[0]) ? $driver->DriverVehicles[0] : NULL;
        $package_name = trans('admin.no_package_found');
        if (isset($driver->pay_mode) && $driver->pay_mode == 1) {
            $package = DriverSubscriptionRecord::where([['driver_id', '=', $driver->id], 'status' => 2])->with('SubscriptionPackage')->first();
            if (!empty($package->SubscriptionPackage)) {
                $package_name = $package->SubscriptionPackage->Name;
            }
        }
        return view('taxicompany.driver.show', compact('driver', 'rejectreasons', 'config', 'driver_wallet', 'driver_config', 'tempDocUploaded', 'package_name', 'vehicle_details', 'arr_segment'));
    }

    public function Vehicles($id)
    {
        $driver = Driver::with(['DriverVehicles' => function ($query) {
            $query->with('VehicleType', 'ServiceTypes');
        }])->findOrFail($id);
        return view('taxicompany.driver.vehicle', compact('driver'));
    }

    public function ChangeStatus($id, $status)
    {
        $validator = Validator::make(
            [
                'id' => $id,
                'status' => $status,
            ],
            [
                'id' => ['required'],
                'status' => ['required', 'integer', 'between:1,2'],
            ]);
        if ($validator->fails()) {
            return redirect()->back();
        }
        $driver = Driver::findOrFail($id);
        $merchant_id = $driver->merchant_id;
        $string_file = $this->getStringFile(NULL, $driver->Merchant);
        if ($status == 2) {
            if ($driver->free_busy == 2) {
                $driver->driver_admin_status = $status;
                $driver->online_offline = 2;
                $driver->login_logout = 2;
                $driver->save();
                $action = 'success';
                $msg = trans("$string_file.deactivated");
            } else {
                $action = 'error';
                $msg = trans("$string_file.running_job_error");
            }
        } else {
            $driver->driver_admin_status = $status;
            $driver->save();
            $action = 'success';
            $msg = trans("$string_file.activated");
        }
        setLocal($driver->language);
        $data = [];
        $pre_title = $status == 2 ? trans("$string_file.inactivated") : trans("$string_file.activated");
        $title = trans("$string_file.account") . ' ' . $pre_title;
        $message = trans("$string_file.account_has_been") . ' ' . $pre_title;

        $data['notification_type'] = $status == 1 ? "ACCOUNT_ACTIVATED" : "ACCOUNT_INACTIVATED";
        $data['segment_type'] = "";
        $data['segment_data'] = [];
        $arr_param = ['driver_id' => $driver->id, 'data' => $data, 'message' => $message, 'merchant_id' => $merchant_id, 'title' => $title, 'large_icon' => NULL];
        Onesignal::DriverPushMessage($arr_param);
        setLocal();
        return redirect()->back()->with($action, $msg);
    }

    public function edit($id)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $driver = Driver::where([['taxi_company_id', '=', $taxicompany->id], ['driver_delete', '=', NULL]])->findOrFail($id);
        $config = ApplicationConfiguration::select('gender', 'smoker')->where([['merchant_id', '=', $merchant_id]])->first();
        $configNew = Configuration::select('driver_wallet_status', 'driver_address','bank_details_enable')->where([['merchant_id', '=', $merchant_id]])->first();
        $config->driver_wallet_status = $configNew->driver_wallet_status;
        $config->driver_address = $configNew->driver_address;
        $config->bank_details = $configNew->bank_details_enable;
        $driver_additional_data = NULL;
        if($configNew->driver_address == 1 && $driver->driver_additional_data != ''){
            $driver_additional_data = (object)json_decode($driver->driver_additional_data, true);
        }
        return view('taxicompany.driver.edit', compact('driver', 'config', 'driver_additional_data'));
    }

    public function update(Request $request, $id)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $request->request->add(['phone' => $request->phoneCode . $request->phone]);
        $request->validate([
            'first_name' => 'required',
            'email' => ['required','email',
                Rule::unique('drivers', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where([['driver_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
                })->ignore($id)],
            'phone' => ['required',
                Rule::unique('drivers', 'phoneNumber')->where(function ($query) use ($merchant_id) {
                    return $query->where([['driver_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
                })->ignore($id)],
            'password' => 'required_if:edit_password,1'
        ]);
        DB::beginTransaction();
        try {
            $driver_additional_data = NULL;
            $appConfig = ApplicationConfiguration::select('gender', 'smoker')->where([['merchant_id', '=', $merchant_id]])->first();
            $config = Configuration::select('driver_address','bank_details_enable')->where([['merchant_id', '=', $merchant_id]])->first();

            $driver = Driver::where([['taxi_company_id', '=', $taxicompany->id]])->findOrFail($id);
            $driver->phoneNumber = $request->phone;
            $driver->first_name = $request->first_name;
            $driver->last_name = $request->last_name;
            $driver->email = $request->email;

            if ($request->hasFile('image')) {
                $driver->profile_image = $this->uploadImage('image','driver',$merchant_id);
            }
            if($appConfig->gender == 1){
                $driver->driver_gender = $request->driver_gender;
            }
            if($appConfig->smoker == 1){
                DriverRideConfig::updateOrCreate(
                    ['driver_id' => $driver->id],
                    [
                        'smoker_type' => $request->smoker_type,
                        'allow_other_smoker' => $request->allow_other_smoker
                    ]);
            }
            if($config->driver_address == 1){
                $driver_additional_data = array("pincode" => $request->address_postal_code,"address_line_1" => $request->address_line_1,"province" => $request->address_province,"subhurb" => $request->address_suburb);
                $driver_additional_data = json_encode($driver_additional_data, true);
                $driver->driver_additional_data = $driver_additional_data;
            }
            if($config->bank_details_enable == 1) {
                $driver->bank_name = $request->bank_name;
                $driver->account_holder_name = $request->account_holder_name;
                $driver->account_number = $request->account_number;
                $driver->online_code = $request->online_transaction;
            }
            if ($request->edit_password == 1) {
                $password = Hash::make($request->password);
                $driver->password = $password;
            }
            $driver->save();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->route('Taxicompany.drivers.index')->with('success',trans('admin.message181'));
    }

    public function Serach(Request $request)
    {
        switch ($request->parameter) {
            case "1":
                $parameter = "first_name";
                break;
            case "2":
                $parameter = "email";
                break;
            case "3":
                $parameter = "phoneNumber";
                break;
        }
        $taxicompany = Auth::user('taxicompany')->parent_id != 0 ? Auth::user('taxicompany')->parent_id : Auth::user('taxicompany');
        $merchant_id = $taxicompany->merchant_id;
        $query = Driver::where([['signupStep', '=', 3],['driver_delete','=',NULL],['taxi_company_id','=',$taxicompany->id]])->latest();
        if ($request->keyword) {
            $query->where($parameter, 'like', '%' . $request->keyword . '%');
        }
        if ($request->area_id) {
            $query->where('country_area_id', '=', $request->area_id);
        }
        $drivers = $query->paginate(25);
        $config = ApplicationConfiguration::select('gender')->where([['merchant_id', '=', $merchant_id]])->first();
        $configNew = Configuration::select('driver_wallet_status','subscription_package')->where([['merchant_id', '=', $merchant_id]])->first();
        $config->driver_wallet_status = $configNew->driver_wallet_status;
        $config->subscription_package = $configNew->subscription_package;
        $areas = CountryArea::where([['merchant_id', '=', $merchant_id]])->latest();
        return view('taxicompany.driver.index', compact('drivers', 'config', 'areas'));
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $request_from = isset($request->request_from) ? $request->request_from : NULL;
        $delete = Driver::FindorFail($id);
        $merchant_id = $delete->merchant_id;
        $string_file = $this->getStringFile(NULL, $delete->Merchant);
        $bookings = Booking::where([['driver_id', '=', $id], ['booking_status', '=', 1012]])->get();
        if ($delete->free_busy != 1 && empty($bookings->toArray())):
            if ($request_from == 'rejected') {
                $delete->delete();
            } else {
                $delete->driver_delete = 1;
                $delete->online_offline = 2;
                $delete->login_logout = 2;
                $delete->save();
                DriverVehicle::where([['owner_id', '=', $delete->id], ['driver_id', '=', $delete->id]])->update(['vehicle_delete' => 1]);
            }
            setLocal($delete->language);
            $data = ['booking_status' => '999'];
            $message = trans("$string_file.account_has_been_deleted");
            $title = trans("$string_file.account_has_been_deleted");
            $arr_param = ['driver_id' => $delete->id, 'data' => $data, 'message' => $message, 'merchant_id' => $merchant_id, 'title' => $title];
            Onesignal::DriverPushMessage($arr_param);
            setLocal();
            echo trans("$string_file.data_deleted_successfully");
        else:
            echo trans("$string_file.some_thing_went_wrong");
        endif;
    }

    public function AllVehicle(Request $request)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $merchant = $taxicompany->Merchant;
        $vehicles = VehicleType::where([['merchant_id', '=', $merchant_id]])->get();
        $request->request->add(['verification_status' => 'verified', 'for_taxi_company' => true]);
        $driver_vehicles = $this->getAllVehicles(true, $request);
        $areas = $this->getMerchantCountryArea($this->getAreaList(false,false,[],null,null, true)->get());
        $arr_search = $request->all();
        $vehicle_model_expire = $merchant->Configuration->vehicle_model_expire;
        return view('taxicompany.drivervehicles.all_vehicles', compact('driver_vehicles', 'areas', 'vehicles', 'arr_search', 'vehicle_model_expire'));
    }

    public function NewDriver()
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $drivers = Driver::where([['taxi_company_id', '=', $taxicompany->id], ['signupStep', '!=', 9]])->latest()
            ->paginate(20);
        $areas = CountryArea::where([['merchant_id', '=', $merchant_id]])->latest();
        return view('taxicompany.driver.basic', compact('drivers', 'areas'));
    }

    public function NewDriverSearch(Request $request)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $query = Driver::where([['taxi_company_id', '=', $taxicompany->id], ['signupStep', '=', 1]])->latest();
        switch ($request->parameter) {
            case "1":
                $parameter = "first_name";
                break;
            case "2":
                $parameter = "email";
                break;
            case "3":
                $parameter = "phoneNumber";
                break;
        }
        if ($request->keyword) {
            $query->where($parameter, 'like', '%' . $request->keyword . '%');
        }
        if ($request->area_id) {
            $query->where('country_area_id', '=', $request->area_id);
        }
        $drivers = $query->paginate(25);
        $areas = CountryArea::where([['merchant_id', '=', $merchant_id]])->latest();
        return view('taxicompany.driver.basic', compact('drivers', 'areas'));
    }

    public function AreaList(Request $request)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $area = CountryArea::where([['merchant_id', '=', $merchant_id]])->latest();
        $area->where([['country_id', '=', $request->country_id]]);
        $areas = $area->get();
        $string_file = $this->getStringFile($merchant_id);
        echo "<option value=''>" . trans("$string_file.select") . "</option>";
        if (!empty($areas->toArray())) {
            foreach ($areas as $value) {
                echo "<option value='" . $value->id . "'>" . $value->CountryAreaName . "</option>";
            }
        } else {
            echo "<option value=''>" . trans("$string_file.no_service_area") . "</option>";
        }
    }

    public function ShowDocument($id)
    {
        $taxicompany = get_taxicompany();
        $taxicompany_id = $taxicompany->id;
        $merchant_id = $taxicompany->merchant_id;
        $driver = Driver::where([['taxi_company_id', '=', $taxicompany_id]])->find($id)->toArray();
        $areas = CountryArea::with('Documents')->where('id', '=', $driver['country_area_id'])->first();
        return view('taxicompany.driver.create_document', compact('areas', 'id'));
    }

    public function StoreDocument(Request $request, $id)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $request->validate([
            'document' => 'required'
        ]);
        $driver = Driver::where([['merchant_id', '=', $merchant_id]])->find($id);
        $images = $request->file('document');
        $expiredate = $request->expiredate;
        foreach ($images as $key => $image) {
            $document_id = $key;
            $driverimage = $this->uploadImage($image,'driver_document',$merchant_id,'multiple');
            $expiry_date = isset($expiredate[$key]) ? $expiredate[$key] : NULL;
            DriverDocument::create([
                'driver_id' => $id,
                'document_id' => $document_id,
                'document_file' => $driverimage,
                'expire_date' => $expiry_date,
                'document_verification_status' => 2,
            ]);
        }
        $driver->signupStep = 1;
        $driver->save();
        return redirect()->route('taxicompany.driver.vehicle.create', [$id]);
    }

    public function CreateVehicle($id)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $driver = Driver::where([['taxi_company_id', '=', $taxicompany->id]])->find($id);
        $vehicletypes = VehicleType::where([['merchant_id', '=', $merchant_id]])->get();
        $vehiclemakes = VehicleMake::where([['merchant_id', '=', $merchant_id]])->get();
        $docs = CountryArea::with('VehicleDocuments')->where('id', $driver->country_area_id)->first();
        return view('taxicompany.driver.create_vehicle', compact('driver', 'vehicletypes', 'vehiclemakes', 'docs'));
    }

    public function StoreVehicle(Request $request, $id)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $taxicompany_id = $taxicompany->id;
        $request->validate([
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'vehicle_make_id' => 'required|exists:vehicle_makes,id',
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'vehicle_number' => ['required',
                Rule::unique('driver_vehicles', 'vehicle_number')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })],
            'vehicle_color' => 'required',
            'document' => 'required',
            'car_number_plate_image' => 'required',
            'car_image' => 'required',
            'services' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $image = $this->uploadImage('car_image', 'vehicle_document', $merchant_id);
            $image1 = $this->uploadImage('car_number_plate_image', 'vehicle_document', $merchant_id);
            $vehicle = DriverVehicle::create([
                'merchant_id' => $merchant_id,
                'driver_id' => $id,
                'owner_id' => $id,
                'vehicle_type_id' => $request->vehicle_type_id,
                'vehicle_make_id' => $request->vehicle_make_id,
                'vehicle_model_id' => $request->vehicle_model_id,
                'vehicle_number' => $request->vehicle_number,
                'vehicle_color' => $request->vehicle_color,
                'vehicle_image' => $image,
                'vehicle_number_plate_image' => $image1,
                'vehicle_active_status' => (has_driver_multiple_or_existing_vehicle($id)) ? 2 : 1,
                'vehicle_verification_status' => 1
            ]);
            $vehicle->ServiceTypes()->sync($request->services);
            $vehicle->Drivers()->sync($id);
            $images = $request->file('document');
            $expiredate = $request->expiredate;
            foreach ($images as $key => $image) {
                $document_id = $key;
                $vehicleDocumentImage = $this->uploadImage($image, 'vehicle_document', $merchant_id, 'multiple');
                $expiry_date = isset($expiredate[$key]) ? $expiredate[$key] : NULL;
                DriverVehicleDocument::create([
                    'driver_vehicle_id' => $vehicle->id,
                    'document_id' => $document_id,
                    'document' => $vehicleDocumentImage,
                    'expire_date' => $expiry_date,
                    'document_verification_status' => 2,
                ]);
            }
            $driver = Driver::where([['taxi_company_id', '=', $taxicompany_id]])->find($id);
            $driver->signupStep = 3;
            $driver->save();
        }catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->route('Taxicompany.drivers.index');
    }

    public function TempDocPending(){
        $drivers = $this->getAllTempDocUploaded();
        return view('taxicompany.driver.pending', compact('drivers'));
    }

    public function SendNotificationDriver(Request $request)
    {
        $taxi_company = get_taxicompany();
        $merchant_id = $taxi_company->merchant_id;
        $request->validate([
            'persion_id' => ['required',
                Rule::exists('drivers', 'id')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', $merchant_id);
                })],
            'title' => 'required|string',
            'message' => 'required|string',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif',
//            'date' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $promotion = new PromotionNotification();
            $promotion->merchant_id = $merchant_id;
            $promotion->title = $request->title;
            $promotion->application = 1;
            $promotion->message = $request->message;
            $promotion->driver_id = $request->persion_id;
            $promotion->url = $request->url;
            $promotion->show_promotion = 1;
            $promotion->expiry_date = !empty($request->date) ? $request->date : NULL;
            if ($request->hasFile('image')) {
                $promotion->image = $this->uploadImage('image', 'promotions',$merchant_id);
                $promotion->save();
            }
            $promotion->save();
            $promotion_data = array(
                'url' => isset($promotion->url) ? $promotion->url : "",
                'image' => isset($promotion->image) ? get_image($promotion->image,'promotions',$merchant_id) : ""
            );
            $data = array(
                'notification_type' => "NOTIFICATION",
                'segment_type' => "NOTIFICATION",
                'segment_data' => $promotion_data,
            );
            $large_icon = NULL;
            $arr_param = ['driver_id'=>$request->persion_id,'data'=>$data,'message'=>$request->message,'merchant_id'=>$merchant_id,'title'=>$request->title,'large_icon'=>$large_icon];
            Onesignal::DriverPushMessage($arr_param);
        } catch (\Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
            p($e->getTraceAsString());
            // Rollback Transaction
        }
        DB::commit();
        $string_file = $this->getStringFile($merchant_id);
        return redirect()->back()->withSuccess(trans("$string_file.sent_successfully"));
    }

    // driver personal document
    public function personalDocument($driver_id = NULL, $country_area_id = NULL)
    {
        $personal_doc = "";
        if (!empty($country_area_id)) {
            $driver = Driver::select('id', 'merchant_id', 'country_id', 'country_area_id', 'segment_group_id')->with('DriverDocument')->find($driver_id);
            if (!empty($driver)) {
                $driver = $driver->toArray();
            }
            $areas = CountryArea::with('Documents')->where('id', '=', $country_area_id)->first();
            $merchant_id = isset($driver['merchant_id']) ? $driver['merchant_id'] : get_merchant_id();
            $configuration = Configuration::select('stripe_connect_enable')->where('merchant_id', $merchant_id)->first();
            $data['areas'] = $areas;
            $data['driver'] = $driver;
            $data['configuration'] = $configuration;
            $personal_doc = View::make('taxicompany.driver.personal-document')->with($data)->render();
        }
        return $personal_doc;
    }

    public function getPersonalDocument(Request $request)
    {
        $personal_doc = "";
        $country_area_id = $request->area_id;
        if (!empty($country_area_id)) {
            $personal_doc = $this->personalDocument(NULL, $country_area_id);
        }
        echo $personal_doc;
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

    // add vehicle
    public function addVehicle(Request $request, $driver_id, $driver_vehicle_id = NULL, $calling_from = "")
    {
//        $merchant_id = get_merchant_id();
        $driver = Driver::find($driver_id);
        $merchant_id = $driver->merchant_id;
        $vehicle_model_expire = $driver->Merchant->Configuration->vehicle_model_expire;
        $country_area_id = $driver->country_area_id;
        $vehicletypes = VehicleType::whereHas('CountryArea', function ($q) use ($country_area_id) {
            $q->where([['country_area_id', '=', $country_area_id]]);
        })
            ->where([['merchant_id', '=', $merchant_id]])->get();
        $vehiclemakes = VehicleMake::where([['merchant_id', '=', $merchant_id]])->get();
        $driver_config = DriverConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $appConfig = ApplicationConfiguration::where('merchant_id', '=', $merchant_id)->first();

        // driver vehicle
        $vehicle_details = NULL;
        if (!empty($driver_vehicle_id)) {
            $vehicle_details = DriverVehicle::Find($driver_vehicle_id);
        }

        $vehicle_type = isset($vehicle_details->vehicle_type_id) ? $vehicle_details->vehicle_type_id : NULL;
        $vehicle_doc_segment = $this->vehicleDocSegment($country_area_id, $driver, $vehicle_type, $driver_vehicle_id);
        $request_from = $calling_from == "d-list" ? "vehicle_list" : "driver_list";
        $baby_seat_enable = $driver->Merchant->BookingConfiguration->baby_seat_enable == 1 ? true : false;
        $wheel_chair_enable = $driver->Merchant->BookingConfiguration->wheel_chair_enable == 1 ? true : false;
        $vehicle_ac_enable = $driver->Merchant->Configuration->vehicle_ac_enable == 1 ? true : false;
        return view('taxicompany.driver.create_vehicle', compact('driver', 'vehicletypes', 'vehiclemakes', 'vehicle_doc_segment', 'appConfig', 'driver_config', 'vehicle_details', 'request_from', 'baby_seat_enable', 'wheel_chair_enable', 'vehicle_ac_enable', 'vehicle_model_expire'));
    }

    public function vehicleDocSegment($country_area_id, $driver, $vehicle_type, $driver_vehicle_id = NULL)
    {
        if (!empty($vehicle_type)) {
            $docs = CountryArea::with(['VehicleDocuments' => function ($q) use ($vehicle_type) {
                $q->addSelect('documents.id', 'expire_date', 'documentNeed', 'document_number_required');
                $q->where('documentStatus', 1);
                $q->where('vehicle_type_id', $vehicle_type);
            }])
                ->where('id', $country_area_id)
                ->first();
//            p($docs);
            $area = CountryArea::with(['VehicleType' => function ($q) use ($vehicle_type, $country_area_id) {
                $q->where('country_area_id', $country_area_id);
                $q->where('vehicle_type_id', $vehicle_type);
            }])
                ->where('id', $country_area_id)
                ->first();
            $arr_services = $area->VehicleType->map(function ($item) {
                return $item['pivot']->service_type_id;
            });
            $arr_services = $arr_services->toArray();
            $data['driver'] = $driver;
            $data['docs'] = $docs;
            // driver vehicle
            $vehicle_details = NULL;
            if (!empty($driver_vehicle_id)) {
                $vehicle_details = DriverVehicle::Find($driver_vehicle_id);
            }
            $data['selected_services'] = isset($vehicle_details->ServiceTypes) ? array_pluck($vehicle_details->ServiceTypes, 'id') : [];
            $merchant_id = $driver->merchant_id;
            $arr_segment_services = $this->getMerchantSegmentServices($merchant_id, '', 1, [], $country_area_id, false, $arr_services);
            $data['arr_segment_services'] = $arr_segment_services;
            $data['vehicle_details'] = $vehicle_details;
            $vehicle_doc_segment = View::make('taxicompany.driver.vehicle-document-segment')->with($data)->render();
        } else {
            $vehicle_doc_segment = "";
        }
        return $vehicle_doc_segment;
    }

    // save vehicle
    public function saveVehicle(Request $request, $driver_id, $vehicle_id = NULL)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $vehicle_id = $request->input('vehicle_id');
        $request_fields = [
            'vehicle_type_id' => 'required_without:vehicle_id',
            'vehicle_make_id' => 'required_without:vehicle_id',
            'vehicle_model_id' => 'required_without:vehicle_id',
            'vehicle_register_date' => 'required_if:vehicle_model_expire,==,1',
            'vehicle_expire_date' => 'required_if:vehicle_model_expire,==,1',
            'vehicle_number' => ['required',
                Rule::unique('driver_vehicles', 'vehicle_number')->where(function ($query) use ($merchant_id, $vehicle_id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['id', '!=', $vehicle_id], ['vehicle_delete', '=', NULL]]);
                })],
            'vehicle_color' => 'required',
            'car_number_plate_image' => 'required_without:vehicle_id',
            'car_image' => 'required_without:vehicle_id',
            'segment_service_type' => 'required'
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        $string_file = $this->getStringFile($merchant_id);
        if ($request->vehicle_model_expire == 1) {
            if ($request->vehicle_expire_date < $request->vehicle_register_date) {
                return redirect()->back()->withErrors(trans("$string_file.model_expire_date_error"));
            }
            $model_age = date_diff(date_create($request->vehicle_expire_date), date_create($request->vehicle_register_date));
            if ($model_age->y == 0) {

                return redirect()->back()->withErrors(trans("$string_file.model_expire_date_diff"));
            }
        }
        DB::beginTransaction();
        try {
            $driver = Driver::find($driver_id);
            $temp_step = $driver->signupStep;
            if ($driver->signupStep == 4 || $driver->signupStep == 5 || $driver->signupStep == 6) {
                // account creating case
                $driver->signupStep = 9;
                $driver->save();
            }
            $arr_data2 = [];
            $arr_data1 = [
                'vehicle_number' => $request->vehicle_number,
                'vehicle_color' => $request->vehicle_color,
                'baby_seat' => $request->baby_seat,
                'wheel_chair' => $request->wheel_chair,
                'ac_nonac' => $request->ac_nonac,
                'vehicle_register_date' => isset($request->vehicle_register_date) ? $request->vehicle_register_date : NULL,
                'vehicle_expire_date' => isset($request->vehicle_expire_date) ? $request->vehicle_expire_date : NULL,
            ];
            if (empty($vehicle_id)) {
                $vehicleMake_id = $this->vehicleMake($request->vehicle_make_id, $merchant_id);
                $vehicle_seat = $request->vehicle_seat ? $request->vehicle_seat : 3;
                $vehicleModel_id = $this->vehicleModel($request->vehicle_model_id, $merchant_id, $vehicleMake_id, $request->vehicle_type, $vehicle_seat);
                $arr_data2 = [
                    'merchant_id' => $merchant_id,
                    'driver_id' => $driver_id,
                    'owner_id' => $driver_id,
                    'vehicle_type_id' => $request->vehicle_type_id,
                    'shareCode' => getRandomCode(10),
                    'vehicle_make_id' => $vehicleMake_id,
                    'vehicle_model_id' => $vehicleModel_id,
                    'vehicle_verification_status' => 2, // means verified
                ];
            }
            $arr_data = array_merge($arr_data1, $arr_data2);
            if (!empty($request->file('car_image'))) {
                $arr_data['vehicle_image'] = $this->uploadImage('car_image', 'vehicle_document',$merchant_id);
            }
            if (!empty($request->file('car_number_plate_image'))) {
                $arr_data['vehicle_number_plate_image'] = $this->uploadImage('car_number_plate_image', 'vehicle_document',$merchant_id);
            }

            $vehicle = DriverVehicle::updateOrCreate(['id' => $vehicle_id, 'driver_id' => $driver_id], $arr_data);
            if (!empty($vehicle_id)) {
                DB::table('driver_driver_vehicle')->where([['driver_vehicle_id', "=", $vehicle_id], ['driver_id', "=", $driver_id]])->delete();
            }
            $vehicle->Drivers()->attach($driver_id, ['vehicle_active_status' => 2]);

            $all_doc = $request->input('all_doc');
            if (!empty($all_doc)) {
                $images = $request->file('document');
                $expiredate = $request->expiredate;
                $document_number = $request->document_number;
                $custom_key = "vehicle_document";
                // upload document
                $this->uploadDocument($driver_id, $custom_key, $all_doc, $images, $expiredate, $document_number, NULL, $vehicle->id);
            }

            // sync services and segment
            $segment_service_type = $request->segment_service_type;

            // remove all segments of driver
            $driver->Segment()->detach();
            // remove all services of driver
            $driver->ServiceType()->detach();
            // services for vehicle
            $vehicle->ServiceTypes()->detach();

            foreach ($segment_service_type as $segment_id => $segment_services) {
                foreach ($segment_services as $service_type_id) {
                    $vehicle->ServiceTypes()->attach($service_type_id, ['segment_id' => $segment_id]);
                }
            }
            // insert services and segments of all vehicles to driver
            $arr_segment_services = DB::table('driver_vehicle_service_type as dvst')
                ->join('driver_vehicles as dv', 'dvst.driver_vehicle_id', '=', 'dv.id')
                ->where('dv.driver_id', $driver_id)
                ->select('dvst.segment_id', 'dvst.service_type_id')
                ->get();
            $arr_segment = array_unique(array_pluck($arr_segment_services, 'segment_id'));
            foreach ($arr_segment as $segment) {
                $driver->Segment()->attach($segment);
            }
            // insert services in driver service type
            $data = json_decode($arr_segment_services, true);
            $arr_services_data = array_column($data, NULL, 'service_type_id');
            $arr_services = array_unique(array_keys($arr_services_data));
            foreach ($arr_services as $service) {
                $driver->ServiceType()->attach($service, ['segment_id' => $arr_services_data[$service]['segment_id']]);
            }

        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            DB::rollback();
            return redirect()->back()->withErrors($error_message);
            // Rollback Transaction
        }
        DB::commit();
        $v_message = trans("$string_file.saved_successfully");
        if ($request->request_from == "vehicle_list") {
            // vehicle add/edit case
            return redirect()->route('taxicompany.driver.allvehicles')->withSuccess($v_message);
        } else {
            $message = $temp_step == 9 ? $v_message : trans("$string_file.driver_registered");
            return redirect()->route('taxicompany.driver.index')->withSuccess($message);
        }
    }

    public function uploadDocument($driver_id, $custom_document_key, $all_doc_id, $arr_doc_file, $doc_expire_date, $document_number, $segment_id = NULL, $driver_vehicle_id = NULL)
    {
//        p($segment_id);
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        foreach ($all_doc_id as $document_id) {
            $image = isset($arr_doc_file[$document_id]) ? $arr_doc_file[$document_id] : null;
            $expiry_date = isset($doc_expire_date[$document_id]) ? $doc_expire_date[$document_id] : NULL;
//            p($expiry_date);
            $doc_number = isset($document_number[$document_id]) ? $document_number[$document_id] : NULL;
            if ($custom_document_key == "segment_document") {
                $driver_document = DriverSegmentDocument::where([['driver_id', $driver_id], ['document_id', $document_id], ['segment_id', $segment_id]])->first();
//                p($driver_document);
                if (empty($driver_document->id)) {
                    $driver_document = new DriverSegmentDocument;
                }
                $unique_document = DriverSegmentDocument::where([['driver_id', '!=', $driver_id]])->where(function ($q) use ($doc_number, $document_id) {
                    $q->where('document_number', '=', $doc_number)->Where('document_number', '!=', '');
                })->count();
            } elseif ($custom_document_key == "driver_document") {
                $driver_document = DriverDocument::where([['driver_id', $driver_id], ['document_id', $document_id]])->first();
                if (empty($driver_document->id)) {
                    $driver_document = new DriverDocument;
                }
                $unique_document = DriverDocument::where([['driver_id', '!=', $driver_id]])->where(function ($q) use ($doc_number, $document_id) {
                    $q->where('document_number', '=', $doc_number)->Where('document_number', '!=', '');
                })->count();
            } elseif ($custom_document_key == "vehicle_document") {
//                p($custom_document_key);
                $driver_document = DriverVehicleDocument::where([['driver_vehicle_id', $driver_vehicle_id], ['document_id', $document_id]])->first();
                if (empty($driver_document->id)) {
                    $driver_document = new DriverVehicleDocument;
                }
                $unique_document = DriverVehicleDocument::where([['driver_vehicle_id', '!=', $driver_vehicle_id]])->where(function ($q) use ($doc_number, $document_id) {
                    $q->where('document_number', '=', $doc_number)->Where('document_number', '!=', '');
                })->count();
            }

            $doc_info = Document::find($document_id);
            $string_file = $this->getStringFile($doc_info->Merchant);
            $doc_name = $doc_info->DocumentName;
//            p($doc_info);
            // if required document not uploaded
            if ($doc_info->documentNeed == 1 && empty($image) && empty($driver_document->id)) {
                throw new \Exception(trans("$string_file.upload_document") . ' ' . $doc_name);
            }
            // if expire date is mandatory but not inserted
            if ($doc_info->expire_date == 1 && empty($expiry_date)) {
                throw new \Exception(trans("$string_file.select_expire_date_of") . ' ' . $doc_name);
            }
            // if document number is mandatory but not entered or duplicate
            if ($doc_info->document_number_required == 1) {
                if (!empty($doc_number)) {
                    if ($unique_document > 0) {
                        throw new \Exception('Document Number already exist');
//                        return redirect()->back()->withInput()->withErrors('Document Number already exist');
                    }
                } else {
                    throw new \Exception(trans("$string_file.please_enter_document_number") . $doc_name);
//                    return redirect()->back()->withInput()->withErrors('Invalid Document Number');
                }
                $driver_document->document_number = $document_number[$document_id];
            }

            $driver_document->document_id = $document_id;
            $driver_document->expire_date = $expiry_date;
            $driver_document->document_verification_status = 2;
//            p($driver_document);
            if ($custom_document_key == "segment_document") {
                $driver_document->segment_id = $segment_id;
            }
            if ($custom_document_key == "vehicle_document") {
                $driver_document->driver_vehicle_id = $driver_vehicle_id;
                if (!empty($image)) {
                    $driver_document->document = $this->uploadImage($image, $custom_document_key, $merchant_id, 'multiple');
                }
            } else {
                $driver_document->driver_id = $driver_id;
                if (!empty($image)) {
                    $driver_document->document_file = $this->uploadImage($image, $custom_document_key, $merchant_id, 'multiple');
                }
            }
            $driver_document->save();
//            p($driver_document);
        }
        return true;
    }

//    public function DeletePendingVehicle(Request $request, $id)
//    {
//        $vehicle_rides = Booking::where([['driver_vehicle_id', '=', $id]])->count();
//        if ($vehicle_rides > 0) {
//            $vehicle = DriverVehicle::find($id);
//            $vehicle->vehicle_delete = 1;
//            $vehicle->save();
//            // return redirect()->back()->with('vehcile', trans('Vehicle Deleted Successfully'));
//            echo trans('Vehicle Deleted Successfully');
//        } else {
//            $vehicle_docs = DriverVehicleDocument::where([['driver_vehicle_id', '=', $id]])->get();
//            foreach ($vehicle_docs as $vehicle_doc) {
//                $image_path = $vehicle_doc->document;
//                if (File::exists($image_path)) {
//                    File::delete($image_path);
//                }
//                $vehicle_doc->delete();
//            }
//            DriverVehicle::where([['id', '=', $id]])->delete();
//            // return redirect()->back()->with('vehcile', trans('Vehicle Deleted Successfully'));
//            echo trans('Vehicle Deleted Successfully');
//        }
//    }

    public function VehiclesDetail($id)
    {
        $vehicle = DriverVehicle::with(['DriverVehicleDocument'])->findOrFail($id);
        $driver = $vehicle->Driver->id;
        $baby_seat_enable = $vehicle->Driver->Merchant->BookingConfiguration->baby_seat_enable == 1 ? true : false;
        $wheel_chair_enable = $vehicle->Driver->Merchant->BookingConfiguration->wheel_chair_enable == 1 ? true : false;
        $vehicle_ac_enable = $vehicle->Driver->Merchant->Configuration->vehicle_ac_enable == 1 ? true : false;
        $vehicle_model_expire = $vehicle->Driver->Merchant->Configuration->vehicle_model_expire;
        $result = check_driver_document($driver, $type = 'vehicle', $id);
        $merchant_id = $vehicle->Driver->merchant_id;
        return view('taxicompany.drivervehicles.vehicle-details', compact('merchant_id','vehicle', 'result', 'baby_seat_enable', 'wheel_chair_enable', 'vehicle_ac_enable', 'vehicle_model_expire'));
    }

    public function driverSearchView($request)
    {
        $taxi_company = get_taxicompany();
        $merchant_id = $taxi_company->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $areas = $this->getMerchantCountryArea($this->getAreaList(false,false,[],null,null, true)->get());
        $arr_segment = get_merchant_segment();
        $search_param = array(
            '1' => trans("$string_file.name"),
            '2' => trans("$string_file.email"),
            '3' => trans("$string_file.phone"),
            '4' => trans($string_file . ".vehicle_number"),
        );
        $data['areas'] = $areas;
        $data['arr_segment'] = $arr_segment;
        $data['arr_search'] = $request->all();
        $data['search_param'] = $search_param;
        $vehicle_doc_segment = View::make('taxicompany.driver.driver-search')->with($data)->render();
        return $vehicle_doc_segment;
    }
}