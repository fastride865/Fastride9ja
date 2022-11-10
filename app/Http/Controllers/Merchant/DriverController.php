<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\SubscriptionPackageController;
use App\Http\Controllers\BusinessSegment\OrderController;
use App\Http\Controllers\Helper\HolderController;
use App\Http\Controllers\Helper\Merchant;
use App\Http\Controllers\Helper\ReferralController;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Http\Controllers\PaymentMethods\Paystack\Paystack;
use App\Http\Controllers\PaymentSplit\StripeConnect;
use App\Models\Booking;
use App\Models\Document;
use App\Models\DriverAccount;
use App\Models\ApplicationConfiguration;
use App\Models\Configuration;
use App\Models\Country;
use App\Models\CountryArea;
use App\Models\DriverConfiguration;
use App\Models\DriverDocument;
use App\Models\DriverRideConfig;
use App\Models\DriverSegmentDocument;
use App\Models\DriverSubscriptionRecord;
use App\Models\DriverVehicle;
use App\Models\DriverVehicleDocument;
use App\Models\DriverWalletTransaction;
use App\Models\HandymanOrder;
use App\Models\InfoSetting;
use App\Models\MerchantStripeConnect;
use App\Models\Onesignal;
use App\Models\ReferralDiscount;
use App\Models\ReferralDriverDiscount;
use App\Models\RejectReason;
use App\Models\ServiceTimeSlot;
use App\Models\ServiceType;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Models\VehicleMake;
use App\Models\VehicleType;
use App\Traits\AreaTrait;
use Auth;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Traits\ImageTrait;
use DB;
use App\Traits\DriverVehicleTrait;
use App\Models\BookingConfiguration;
use App\Models\Segment;
use App\Models\BookingTransaction;
use Session;
use App\Traits\HandymanTrait;
use App\Traits\OrderTrait;
use App\Traits\BookingTrait;
use App\Http\Controllers\Helper\Merchant as MerchantHelper;
use View;
use App\Models\BusinessSegment\Order;
use DateTime;
use DateTimeZone;
use App\Models\EmailConfig;
use App\Models\EmailTemplate;

class DriverController extends Controller
{
    /**NOTE driver trait is already included in handyman trait thats why we didn't driver trait here**/
    use HandymanTrait, AreaTrait, ImageTrait, DriverVehicleTrait, OrderTrait, BookingTrait;

    // display all drivers
    public function index(Request $request)
    {
        $checkPermission = check_permission(1, 'view_drivers');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $request->request->add(['merchant_id' => $merchant_id]);
        $drivers = $this->getAllDriver(true, $request);
        $driver_summary = $this->getDriverSummary($request);
        $pendingdrivers = $driver_summary->pending;
        $rejecteddrivers = $driver_summary->rejected;
        $basicDriver = $driver_summary->basic_signup;
        $config = $merchant;
//            Merchant::find($merchant_id);
        $config->driver_wallet_status = $config->Configuration->driver_wallet_status;
        $config->subscription_package = $config->Configuration->subscription_package;
        $config->gender = $config->ApplicationConfiguration->gender;
        $config->smoker = $config->ApplicationConfiguration->smoker;
        $config->driver_commission_choice = $config->ApplicationConfiguration->driver_commission_choice;
        $config->stripe_connect_enable = $config->Configuration->stripe_connect_enable;
        $tempDocUploaded = $this->getAllTempDocUploaded(false)->count();
        $arr_search = $request->all();
        $request->request->add(['search_route' => route('driver.index')]);
        $search_view = $this->driverSearchView($request);
        $custom_segment = \Config::get('custom.segment_sub_group');
        $booking_segment = $custom_segment['booking'];
        $order_segment = $custom_segment['order'];
        $socket_enable = $merchant->Configuration->lat_long_storing_at == 2 ? true : false;
        $info_setting = InfoSetting::where('slug', 'DRIVER')->first();
        return view('merchant.driver.index', compact('booking_segment', 'order_segment', 'drivers', 'rejecteddrivers', 'pendingdrivers', 'basicDriver', 'config', 'tempDocUploaded', 'search_view', 'arr_search', 'socket_enable', 'info_setting'));
    }

    public function driverSearchView($request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $areas = $this->getMerchantCountryArea($this->getAreaList(false)->get());
        $countries = Country::where([['merchant_id','=',$merchant_id],['country_status','=',1]])->get();
        $countries = $this->getMerchantCountry($countries);
        $arr_segment = get_merchant_segment();
        $search_param = array(
            '1' => trans("$string_file.name"),
            '2' => trans("$string_file.email"),
            '3' => trans("$string_file.phone"),
            '4' => trans($string_file . ".vehicle_number"),
        );
        $data['areas'] = $areas;
        $data['countries'] = $countries;
        $data['arr_segment'] = $arr_segment;
        $data['arr_search'] = $request->all();
        $data['search_param'] = $search_param;
        $vehicle_doc_segment = View::make('merchant.driver.driver-search')->with($data)->render();
        return $vehicle_doc_segment;
    }

    // create driver
    public function add(Request $request, $id = NULL)
    {
        $checkPermission = check_permission(1, 'create_drivers');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $driver = NULL;
        $country_area_id = NULL;
        $driver_additional_data = NULL;
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL, $merchant);
        $areas = add_blank_option([], trans("$string_file.area"));
        if (!empty($id)) {
            $driver = Driver::Find($id);
            $country_area_id = $driver->country_area_id;
            if (!empty($driver->driver_additional_data)) {
                $driver_additional_data = (object)json_decode($driver->driver_additional_data, true);
            }
            $pre_title = trans("$string_file.edit");
            $areas = $this->getMerchantCountryArea($this->getAreaList(false, true, [], $driver->country_id)->get());
            $areas = add_blank_option($areas, trans("$string_file.area"));
        } else {
            $pre_title = trans("$string_file.add");
        }
        $merchant = get_merchant_id(false);
        $title = $pre_title . ' ' . trans($string_file . '.driver');

       $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $merchant_obj = new Merchant;
        $countries = $merchant_obj->CountryList($merchant,$permission_area_ids);
//        $area = $this->getAreaList(false);
//        $areas = $area->get();
        $config = $merchant;
        $group = $this->segmentGroup($merchant->id, "drop_down", $string_file);
        $config->bank_details = $config->Configuration->bank_details_enable;
        $config->stripe_connect_enable = isset($config->Configuration->stripe_connect_enable) ? $config->Configuration->stripe_connect_enable : null;
        $config->paystack_split_payment_enable = isset($config->Configuration->paystack_split_payment_enable) ? $config->Configuration->paystack_split_payment_enable : null;
        $config->driver_wallet_status = $config->Configuration->driver_wallet_status;
        $config->driver_address = $config->Configuration->driver_address;
        $config->gender = $config->ApplicationConfiguration->gender;
        $config->smoker = $config->ApplicationConfiguration->smoker;
        $config->driver_email_enable = $config->ApplicationConfiguration->driver_email;
        $account_types = $config->AccountType;
//        $arr_commission_choice = $this->getDriverCommissionChoices($config);
        $personal_document = $this->personalDocument($id, $country_area_id);
        $info_setting = InfoSetting::where('slug', 'DRIVER')->first();
        $paystack_bank_codes = [];
        if($config->Configuration->paystack_split_payment_enable == 1){
            $crdController = new CardController();
            $paystack_bank_codes = $crdController->getPaystackBankCodesForMerchant($merchant->id);
        }
        return view('merchant.driver.create', compact('driver', 'areas', 'countries', 'group', 'config', 'account_types', 'driver_additional_data', 'personal_document', 'title', 'info_setting', 'paystack_bank_codes'));
    }

    // save driver
    public function save(Request $request, $id = NULL)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $only_num = $request->phone;
        $string_file = $this->getStringFile(NULL, $merchant);
        $request->request->add(['phone' => $request->isd . $request->phone]);
        $driver =  Driver::select('id')->where([['phoneNumber', '=', $request->phone],['driver_delete', '=', NULL],['email', '!=', NULL], ['merchant_id', '=', $merchant_id], ['id', '!=', $id]])->first();
       if(!empty($driver->id))
       {
           return redirect()->back()->withInput($request->input())->withErrors(trans("$string_file.number_already_used"));
       }
        $validator_array = [
            'first_name' => 'required',
            'country' => 'required_without:id',
            'email' => ['required_if:driver_email_enable,1',
                Rule::unique('drivers', 'email')->where(function ($query) use ($merchant_id, $id) {
                    return $query->where([['driver_delete', '=', NULL],['email', '!=', NULL], ['merchant_id', '=', $merchant_id], ['id', '!=', $id]]);
                })],
            'phone' => ['required',
                // Rule::unique('drivers', 'phoneNumber')->where(function ($query) use ($merchant_id, $id) {
                //     return $query->where([['driver_delete', '=', NULL], ['merchant_id', '=', $merchant_id], ['id', '!=', $id]]);
                // })
                ],
            'password' => 'required_without:id|confirmed',
            'area' => 'required_without:id',
            'image' => 'required_without:id|file',
//            'address_line_1' => 'required',
//            'city_name' => 'required',
//            'address_postal_code' => 'required',
        ];
        $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $stripe_connect_enable = (isset($config->stripe_connect_enable) && $config->stripe_connect_enable == 1) ? true : false;
        $paystack_split_payment_enable = (isset($config->paystack_split_payment_enable) && $config->paystack_split_payment_enable == 1) ? true : false;
        if ($stripe_connect_enable) {
            $validator_array = array_merge($validator_array, [
                'address_province' => 'required',
                'account_holder_name' => 'required',
                'account_number' => 'required',
                'bsb_routing_number' => 'required',
                'abn_number' => 'required',
                'dob' => 'required'
            ]);
        }elseif($paystack_split_payment_enable){
            $validator_array = array_merge($validator_array, [
                'bank_name' => 'required',
                'bank_code' => 'required',
                'account_holder_name' => 'required',
                'account_number' => 'required',
            ]);
        }
       $validator = Validator::make($request->all(), $validator_array);
        // removed code  from phone number because it's creating an issue while updating driver profile
        $request->request->add(['phone' => $only_num]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        // again appending iso code with phone number
        $request->request->add(['phone' => $request->isd . $request->phone]);
        DB::beginTransaction();
        try {
            $driver_additional_data = NULL;
            if (!empty($id)) {
                $driver = Driver::Find($id);
            } else {
                $driver = new Driver();
            }

//            if ($config->driver_address == 1) {
            $driver_additional_data = array("pincode" => $request->address_postal_code, "address_line_1" => $request->address_line_1, "city_name" => $request->city_name);
            if ($stripe_connect_enable) {
                $driver_additional_data['province'] = $request->address_province;
                $driver_additional_data['subhurb'] = $request->address_suburb;
                $driver_additional_data['address_line_2'] = $request->address_line_2;
            }
            $driver_additional_data = json_encode($driver_additional_data, true);
//            }

            $driver_store_data = [
                'merchant_id' => $merchant_id,
//                'country_id' => $request->country,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phoneNumber' => $request->phone,
                'driver_gender' => $request->driver_gender,
//                'country_area_id' => $request->area,
//                'password' => Hash::make($request->password),
//                'profile_image' => $this->uploadImage('image', 'driver'),
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'account_type_id' => $request->account_types,
                'online_code' => isset($request->bank_code) && !empty($request->bank_code) ? $request->bank_code : $request->online_transaction,
//                'term_status' => 1,
                'last_ride_request_timestamp' => date("Y-m-d H:i:s"),
                'driver_referralcode' => $driver->GenrateReferCode(),
                'driver_additional_data' => $driver_additional_data,
//                'signupStep' => 3, //[basic info + country area + segment group]
                'pay_mode' => isset($request->pay_mode) ? $request->pay_mode : 2,// default commission based

            ];

            if (empty($id)) {
                $driver_store_data['country_id'] = $request->country;
//                $driver_store_data['country_area_id'] =  $request->area;
                $driver_store_data['term_status'] = 1;
                $driver_store_data['segment_group_id'] = isset($request->segment_group_id) ? $request->segment_group_id : NULL;
                $driver_store_data['signupStep'] = 4;
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
            if ((!empty($driver->id) && empty($driver->segment_group_id))) {
                $driver_store_data['segment_group_id'] = isset($request->segment_group_id) ? $request->segment_group_id : NULL;
            }
            if (!empty($request->password)) {
                $driver_store_data['password'] = Hash::make($request->password);
            }
            if (!empty($request->hasFile('image'))) {
                $driver_store_data['profile_image'] = $this->uploadImage('image', 'driver');
            }
            if ($stripe_connect_enable) {
                $driver_store_data['routing_number'] = $request->bsb_routing_number;
                $driver_store_data['bsb_number'] = $request->bsb_routing_number;
                $driver_store_data['sc_address_status'] = 'pending';
                $driver_store_data['dob'] = $request->dob;
                $driver_store_data['device_ip'] = $request->ip();
                $driver_store_data['abn_number'] = $request->abn_number;
            }

            $driver = Driver::updateOrCreate(['id' => $id], $driver_store_data);
            //send mail
            if (empty($id)) {
                $temp = EmailTemplate::where('merchant_id', '=', $merchant_id)->where('template_name', '=', "welcome")->first();
                $merchant = get_merchant_id(false);
                $data['temp'] = $temp;
                $data['merchant'] = $merchant;
                $data['driver'] = $driver;
                $email_html = View::make('mail.driver-welcome')->with($data)->render();
                $configuration = EmailConfig::where('merchant_id', '=', $merchant_id)->first();
                $response = $this->sendMail($configuration, $driver->email, $email_html, 'welcome_email', $merchant->BusinessName, NULL, $merchant->email, $string_file);
            }

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
            if($paystack_split_payment_enable && $driver->paystack_account_status != "active"){
                $request_data = array(
                    'business_name' => $request->account_holder_name,
                    'bank_code' => $request->bank_code,
                    'account_number' => $request->account_number,
                    'percentage_charge' => 0.2
                );

                $crdController = new CardController();
                $paystack_account_id = $crdController->registerPaystackDriverForMerchant($merchant->id, $request_data, $driver->paystack_account_id);

                $driver->paystack_account_id = $paystack_account_id;
                $driver->paystack_account_status = "active";
                $driver->save();
            }
        } catch (\Exception $e) {
            DB::rollback();
//            p($e->getTraceAsString());
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
        DB::commit();
        $message = trans("$string_file.saved_successfully");
//            !empty($id) ? trans("$string_file.profile_updated") : trans("$string_file.basic_signup_completed");
        if ($driver->segment_group_id == 1) {
            $vehicle_id = isset($driver->DriverVehicles[0]) ? $driver->DriverVehicles[0]->id : NULL;
            return redirect()->route('merchant.driver.vehicle.create', [$driver->id, $vehicle_id])->withSuccess($message);
        } else {
            return redirect()->route('merchant.driver.handyman.segment', [$driver->id])->withSuccess($message);
        }
//        return redirect()->route('merchant.driver.personal.document.show', [$driver->id]);
        //return redirect()->route('merchant.driver.vehicle.create', [$driver->id]);
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
            $personal_doc = View::make('merchant.driver.personal-document')->with($data)->render();
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

    //  add handyman segment configuration for driver
    public function addHandymanSegment(Request $request, $id)
    {
        $driver = Driver::select('id', 'first_name', 'last_name', 'merchant_id', 'country_area_id')->Find($id);
        $area = $driver->CountryArea;
        $merchant = $driver->Merchant;
        $merchant_id = $merchant->id;
        $segment_group_id = 2;
        $arr_segment_services = $this->getMerchantSegmentServices($merchant_id, '', $segment_group_id);
        $documents = $area->SegmentDocument;
//        p($documents);

        /************* for group 2 segments start **************/
        $arr_selected_segment_service = [];
        $arr_segment_selected_document = [];
        $selected_segment_document = $driver->DriverSegmentDocument;
        $selected_segment_services = $driver->ServiceType;

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
            $arr_segment_selected_document[$segment_document->segment_id][$segment_document->document_id] = $segment_document;
        }

        /************* for group 2 segments end **************/

        return view('merchant.driver.handyman-segment', compact('merchant', 'documents', 'driver',
            'arr_segment_services', 'arr_selected_segment_service', 'arr_segment_selected_document'));
    }

    // save handyman segment configuration for driver
    public function saveHandymanSegment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'segment_service_type' => 'required',
        ], [
            'segment_service_type.required' => 'You have to choose at least one service type of segment(s)',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {

            $segment_group_id = 2;
            $driver = Driver::Find($id);
            $merchant_id = $driver->merchant_id;
            if ($driver->signupStep == 4) {
                $driver->signupStep = 7;
                $driver->save();
            }
            $driver_id = $driver->id;
            $arr_segment_service = $request->input('segment_service_type');
            $segment_document_id = $request->input('segment_document_id');
            // its for group = 2 segments [Plumber, Car Painting, Cleaning]
//            p($request->all());

            $driver->Segment()->detach();
            $driver->ServiceType()->detach();
//p($request->all());
            if (!empty($arr_segment_service)) {
//                p($arr_segment_service);
                foreach ($arr_segment_service as $segment => $segment_services) {
                    if (!empty($segment_services)) {
                        // add segment services
                        foreach ($segment_services as $service) {
                            $driver->ServiceType()->attach($service, ['segment_id' => $segment]);
                        }
                    }
//                    p($segment);
                    // add segment document
                    $all_doc = isset($segment_document_id[$segment]) ? $segment_document_id[$segment] : [];
//                    p($all_doc);
                    if (!empty($all_doc)) {
                        $document_number = $request->input('document_number');
                        $document_number = isset($document_number[$segment]) ? $document_number[$segment] : [];
                        $doc_expire_date = $request->input('expire_date');
                        $doc_expire_date = isset($doc_expire_date[$segment]) ? $doc_expire_date[$segment] : [];
                        $segment_document = $request->file('segment_document');
                        $arr_doc_file = isset($segment_document[$segment]) ? $segment_document[$segment] : [];
                        $custom_document_key = "segment_document";
                        $this->uploadDocument($driver_id, $custom_document_key, $all_doc, $arr_doc_file, $doc_expire_date, $document_number, $segment);
                    }
                    $driver->Segment()->attach($segment);
                }
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
//            p($message);
            // Rollback Transaction
            DB::rollback();
            return redirect()->back()->withErrors($message);
        }
        DB::commit();
//        p('end');
        // redirect on segment time slot screen
        $string_file = $this->getStringFile($merchant_id);
        return redirect()->route('merchant.driver.segment.time-slot', $id)->withSuccess(trans("$string_file.handyman_segment_service"));
    }

    // add handyman segment time slot
    public function addSegmentTimeSlot(Request $request, $driver_id)
    {
        $slot_type = "all";
        $driver = Driver::select('first_name', 'last_name', 'id', 'country_area_id', 'merchant_id')->Find($driver_id);
        $string_file = $this->getStringFile(NULL, $driver->Merchant);
        $arr_segment = $driver->Segment;
        $merchant_id = $driver->merchant_id;
        $arr_segment_id = array_pluck($arr_segment, 'id');
        $request->request->add(['slot_type' => $slot_type, 'driver_id' => $driver_id, 'calling_from' => 'admin', 'segment_id' => $arr_segment_id, 'driver' => $driver]);
        $segment_time_slot = ServiceTimeSlot::getServiceTimeSlot($request, $string_file);
//        p($segment_time_slot);
        return view('merchant.driver.segment-time-slot', compact(
            'arr_segment', 'segment_time_slot', 'driver', 'merchant_id'));
    }

    // save handyman segment time slot
    public function saveSegmentTimeSlot(Request $request, $id)
    {
//        p($request->all());
        $validator = Validator::make($request->all(), [
            'arr_time_slot' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            $driver = Driver::Find($id);
            if ($driver->signupStep == 7) {
                $signup_status = 9;
                $driver->signupStep = $signup_status;
                $driver->save();
            }
            $arr_segment_time_slot = $request->input('arr_time_slot');
            foreach ($arr_segment_time_slot as $segment_id => $arr_slot_details_id) {
                $driver->ServiceTimeSlotDetail()->wherePivot('segment_id', $segment_id)->detach();
                foreach ($arr_slot_details_id as $slot_details_id) {
                    $driver->ServiceTimeSlotDetail()->attach($slot_details_id, ['segment_id' => $segment_id]);
                }
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
//        p('end');
        // redirect on index page
        $string_file = $this->getStringFile(NULL, $driver->Merchant);
        return redirect()->route('driver.index')->withSuccess(trans("$string_file.saved_successfully"));
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
            ->where([['merchant_id', '=', $merchant_id],['admin_delete', '=', NULL]])->get();
        $vehiclemakes = VehicleMake::where([['merchant_id', '=', $merchant_id],['admin_delete', '=', NULL]])->get();
        $driver_config = DriverConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $appConfig = ApplicationConfiguration::where('merchant_id', '=', $merchant_id)->first();

        // driver vehicle
        $vehicle_details = NULL;
        if (!empty($driver_vehicle_id)) {
            $vehicle_details = DriverVehicle::Find($driver_vehicle_id);
        }
//        else{
//            // get driver's first vehicle
//            $vehicle_details =  isset($driver->DriverVehicles[0]) ? $driver->DriverVehicles[0] : NULL;
//        }

        $vehicle_type = isset($vehicle_details->vehicle_type_id) ? $vehicle_details->vehicle_type_id : NULL;
        $vehicle_doc_segment = $this->vehicleDocSegment($country_area_id, $driver, $vehicle_type, $driver_vehicle_id);
//        p($vehicle_doc_segment);
        $request_from = $calling_from == "d-list" ? "vehicle_list" : "driver_list";
        $baby_seat_enable = $driver->Merchant->BookingConfiguration->baby_seat_enable == 1 ? true : false;
        $wheel_chair_enable = $driver->Merchant->BookingConfiguration->wheel_chair_enable == 1 ? true : false;
        $vehicle_ac_enable = $driver->Merchant->Configuration->vehicle_ac_enable == 1 ? true : false;
//        request()->session()->flash('message', trans('admin.driverAuto'));
        $info_setting = InfoSetting::where('slug', 'DRIVER_VEHICLE')->first();
        return view('merchant.driver.create_vehicle', compact('driver', 'vehicletypes', 'vehiclemakes', 'vehicle_doc_segment', 'appConfig', 'driver_config', 'vehicle_details', 'request_from', 'baby_seat_enable', 'wheel_chair_enable', 'vehicle_ac_enable', 'info_setting', 'vehicle_model_expire'));
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
//            p($vehicle_details->DriverVehicleDocument);
//            else{
//                // get driver's first vehicle
//                $vehicle_details =  isset($driver->DriverVehicles[0]) ? $driver->DriverVehicles[0] : NULL;
//            }
//            p($vehicle_details->ServiceTypes);
            $data['selected_services'] = isset($vehicle_details->ServiceTypes) ? array_pluck($vehicle_details->ServiceTypes, 'id') : [];
            $merchant_id = $driver->merchant_id;
//            p($data['selected_services']);
            $arr_segment_services = $this->getMerchantSegmentServices($merchant_id, '', 1, [], $country_area_id, false, $arr_services);
            $data['arr_segment_services'] = $arr_segment_services;
            $data['vehicle_details'] = $vehicle_details;
            $vehicle_doc_segment = View::make('merchant.driver.vehicle-document-segment')->with($data)->render();
        } else {
            $vehicle_doc_segment = "";
        }
        return $vehicle_doc_segment;
    }

    // save vehicle
    public function saveVehicle(Request $request, $driver_id, $vehicle_id = NULL)
    {
        $merchant_id = get_merchant_id();
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
//            'document' => 'required',
//            'document.*' => 'image|mimes:jpeg,jpg,png',
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
//            $vehicle_active_status = get_driver_multi_existing_vehicle_status($id);
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
//                    'vehicle_active_status' => 1, // be default vehicle will be active
                    'vehicle_verification_status' => 2, // means verified
                ];
            }
            $arr_data = array_merge($arr_data1, $arr_data2);
            if (!empty($request->file('car_image'))) {
                $arr_data['vehicle_image'] = $this->uploadImage('car_image', 'vehicle_document');
            }
            if (!empty($request->file('car_number_plate_image'))) {
                $arr_data['vehicle_number_plate_image'] = $this->uploadImage('car_number_plate_image', 'vehicle_document');
            }

            $vehicle = DriverVehicle::updateOrCreate(['id' => $vehicle_id, 'driver_id' => $driver_id], $arr_data);
//            $vehicle->ServiceTypes()->sync([1,2]);
            if (!empty($vehicle_id)) {
                DB::table('driver_driver_vehicle')->where([['driver_vehicle_id', "=", $vehicle_id], ['driver_id', "=", $driver_id]])->delete();
            }

            $vehicle_active_status = 2;
            if($driver->signupStep == 9)
            {
                $vehicle_active_status = 1;
            }
            $vehicle->Drivers()->attach($driver_id, ['vehicle_active_status' => $vehicle_active_status]);

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
//           // segments for driver
//                $driver->Segment()->attach($segment_id);
                foreach ($segment_services as $service_type_id) {
                    // services for vehicle
//                    $driver->ServiceType()->attach($service_type_id, ['segment_id' => $segment_id]);
                    // services for vehicle
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
            return redirect()->route('merchant.driver.allvehicles')->withSuccess($v_message);
        } else {
            $message = $temp_step == 9 ? $v_message : trans("$string_file.driver_registered");
            return redirect()->route('driver.index')->withSuccess($message);
        }
        //return redirect()->route('merchant.driver.document.show', [$driver->id]);
    }

    public function driverJobs(Request $request, $job_type, $id)
    {
        $driver = Driver::find($id);
        $segment_group_id = $driver->segment_group_id;
        $bookings = [];
        $food_grocery_orders = [];
        $handyman_orders = [];
        if (!in_array($job_type, ['booking', 'order', 'handyman-order'])) {
            return redirect()->back()->withErrors(trans('admin.invalid_request'));
        }

        if ($segment_group_id == 1) {
            if ($job_type == "booking") {
                $bookings = Booking::where([['driver_id', '=', $id]])->paginate(20);
            } elseif ($job_type == "order") {
                $food_grocery_orders = Order:: where([['driver_id', '=', $id]])->paginate(20);
            }
        } else {
            $handyman_orders = HandymanOrder::where([['driver_id', '=', $id]])->paginate(20);
        }

        $string_file = $this->getStringFile($driver->merchant_id);
        $req_param['string_file'] = $string_file;
        $arr_status = $this->getOrderStatus($req_param);
        $booking_status = $this->getBookingStatus($string_file);
        $handyman_status = $this->getHandymanBookingStatus($req_param, $string_file);
        return view('merchant.driver.jobs', compact('bookings', 'booking_status', 'driver', 'food_grocery_orders', 'arr_status', 'handyman_orders', 'handyman_status', 'job_type'));
    }

    public function driver_location(Request $request)
    {
        $driver_id = $request->driver_id;
        $driver = Driver::select('current_latitude', 'current_longitude')->find($driver_id);
        return $driver;
    }

    public function EditDocument(Request $request, $id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $driver = Driver::with('DriverDocument')->where([['merchant_id', '=', $merchant_id], ['driver_delete', '=', NULL]])->findOrFail($id);
        return view('merchant.driver.edit-doc', compact('driver'));
    }

    public function basicSignupDriver(Request $request)
    {
        $request->request->add(['search_route' => route('merchant.driver.basic'), 'request_from' => "basic_signup"]);
        $drivers = $this->getAllDriver(true, $request);
        $search_view = $this->driverSearchView($request);
        $arr_search = $request->all();
        $info_setting = InfoSetting::where('slug', 'DRIVER_BASIC_SIGNUP')->first();
        return view('merchant.driver.basic', compact('drivers', 'search_view', 'arr_search', 'info_setting'));
    }


    public function pendingDriver(Request $request)
    {
//        $checkPermission = check_permission(1, 'pending_driver');
//        if ($checkPermission['isRedirect']) {
//            return $checkPermission['redirectBack'];
//        }
        $checkPermission = check_permission(1, 'pending_drivers_approval');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
//        $drivers = $this->getAllPendingDriver();
        $request->request->add(['search_route' => route('merchant.driver.pending.show'), 'request_from' => "pending_approval"]);
        $drivers = $this->getAllDriver(true, $request);
        $search_view = $this->driverSearchView($request);
        $arr_search = $request->all();
        $info_setting = InfoSetting::where('slug', 'DRIVER_PENDING_APPROVAL')->first();
        return view('merchant.driver.pending', compact('drivers', 'search_view', 'arr_search', 'info_setting'));
    }

    public function tempDocApprovalPending(Request $request)
    {
        $request->request->add(['search_route' => route('merchant.driver.temp-doc-pending.show'), 'request_from' => "pending_approval"]);
        $drivers = $this->getAllTempDocUploaded();
        $search_view = $this->driverSearchView($request);
        $arr_search = $request->all();
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL, $merchant);
        $page_title_prefix = trans("$string_file.temp_doc_approve");
        return view('merchant.driver.pending', compact('drivers', 'search_view', 'arr_search', 'page_title_prefix'));
    }

    public function AllVehicle(Request $request)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $vehicles = VehicleType::where([['merchant_id', '=', $merchant_id],['admin_delete', '=', NULL]])->get();
        $request->request->add(['verification_status' => 'verified']);
        $driver_vehicles = $this->getAllVehicles(true, $request);
        $areas = $this->getMerchantCountryArea($this->getAreaList(false)->get());
        $arr_search = $request->all();
//        $arr_search['merchant_id'] = $merchant_id;
        $info_setting = InfoSetting::where('slug', 'DRIVER_VEHICLE')->first();
        $vehicle_model_expire = $merchant->Configuration->vehicle_model_expire;
        return view('merchant.drivervehicles.all_vehicles', compact('driver_vehicles', 'areas', 'vehicles', 'arr_search', 'info_setting', 'vehicle_model_expire'));
    }


    public function PendingVehicle(Request $request)
    {
        $checkPermission = check_permission(1, 'view_pending_vehicle_apporvels');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $vehicles = VehicleType::where([['merchant_id', '=', $merchant_id],['admin_delete', '=', NULL]])->get();
        $request->request->add(['verification_status' => 'pending']); // get pending vehicle
        $driver_vehicles = $this->getAllVehicles(true, $request);
        $areas = $this->getMerchantCountryArea($this->getAreaList(false)->get());
        $arr_search = $request->all();
        $info_setting = InfoSetting::where('slug', 'DRIVER_VEHICLE_PENDING_APPROVAL')->first();
        return view('merchant.drivervehicles.pending_vehicles', compact('driver_vehicles', 'areas', 'vehicles', 'arr_search', 'info_setting'));
    }

    public function StoreEdit(Request $request, $id)
    {
        $request->validate([
//            'document' => 'required'
        ]);
        try {
            $doc_expire_date = $request->expiredate;
            $arr_doc_file = $request->file('document');
            $all_doc = $request->input('all_doc');
            $document_number = $request->document_number;
            $custom_document_key = "driver_document";
            $this->uploadDocument($id, $custom_document_key, $all_doc, $arr_doc_file, $doc_expire_date, $document_number);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
        return redirect()->route('driver.index')->withSuccess(trans('admin.editDocSucess'));
    }

    public function uploadDocument($driver_id, $custom_document_key, $all_doc_id, $arr_doc_file, $doc_expire_date, $document_number, $segment_id = NULL, $driver_vehicle_id = NULL)
    {
//        p($segment_id);
        $merchant_id = get_merchant_id();
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
                    $driver_document->document = $this->uploadImage($image, $custom_document_key, NULL, 'multiple');
                }
            } else {
                $driver_document->driver_id = $driver_id;
                if (!empty($image)) {
                    $driver_document->document_file = $this->uploadImage($image, $custom_document_key, NULL, 'multiple');
                }
            }
            $driver_document->save();
//            p($driver_document);
        }
        return true;
    }

    public function EditVehicleDocument(Request $request, $id)
    {
        $drivervehicle = DriverVehicle::find($id);
        $documents = $drivervehicle->Driver->CountryArea->VehicleDocuments;
        return view('merchant.drivervehicles.edit-doc', compact('drivervehicle', 'documents'));
    }

    public function UpdateVehicleDocument(Request $request, $id)
    {
        $request->validate([
            'document' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $images = $request->file('document');
            $expiredate = $request->expiredate;
            foreach ($images as $key => $image) {
                $document_id = $key;
                $expiry_date = isset($expiredate[$key]) ? $expiredate[$key] : NULL;
                DriverVehicleDocument::updateOrCreate([
                    'driver_vehicle_id' => $id,
                    'document_id' => $document_id,
                ], [
                    'document' => $this->uploadImage($image, 'vehicle_document', NULL, 'multiple'),
                    'expire_date' => $expiry_date,
                    'document_verification_status' => 2,
                ]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->route('merchant.driver.allvehicles')->with('vehcile', trans('admin.editvehicleSucess'));
    }

    public function show($id)
    {
        // $driver_vehicle_document = DriverVehicle::with('DriverVehicleDocument')->where('driver_id',$driver->id)->orderBy('id','ASC')->count();
        $merchant_id = get_merchant_id();
        $vehicle_details = NULL;
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
//        $result = check_driver_document($driver->id);
        $tempDocUploaded = $this->getAllTempDocUploaded(false, $driver->id)->count();
        if ($driver->segment_group_id == 1) {
            $vehicle_details = isset($driver->DriverVehicles[0]) ? $driver->DriverVehicles[0] : NULL;
        } else {
            $arr_segment = Segment::select('id', 'name', 'slag')->whereHas('Driver', function ($q) use ($driver_id) {
                $q->where('driver_id', $driver_id);
            })
                ->with(['ServiceType' => function ($qq) use ($driver_id) {
                    $qq->select('segment_id', 'id', 'serviceName', 'type');
                    $qq->whereHas('Driver', function ($qqq) use ($driver_id) {
                        $qqq->where('driver_id', $driver_id);
                    });
                }])
                ->whereHas('ServiceType.Driver', function ($qqq) use ($driver_id) {
                    $qqq->where('driver_id', $driver_id);
                })->with(['ServiceTimeSlot' => function ($qq) use ($driver_id) {
//                 $qq->select('segment_id', 'id', 'day');
                    $qq->with(['ServiceTimeSlotDetail' => function ($q) use ($driver_id) {
//                     $q->select('slot_time_text', 'id');
                        $q->whereHas('Driver', function ($qq) use ($driver_id) {
                            $qq->where('driver_id', $driver_id);
                        });
                    }]);
                    $qq->whereHas('ServiceTimeSlotDetail', function ($q) use ($driver_id) {
                        $q->whereHas('Driver', function ($qq) use ($driver_id) {
                            $qq->where('driver_id', $driver_id);
                        });
                    });
                }])
                ->with(['DriverSegmentDocument' => function ($qq) use ($driver_id) {
//                    $qq->select('id', 'document_id', 'segment_id', 'document_file', 'expire_date', 'document_number', 'document_verification_status');
                    $qq->where('driver_id', $driver_id);
                }])
//             ->whereHas('ServiceTimeSlot.ServiceTimeSlotDetail',function($qqq) use($driver_id){
//             })
                ->get();
        }
        $package_name = trans('admin.no_package_found');
        if (isset($driver->pay_mode) && $driver->pay_mode == 1) {
            $package = DriverSubscriptionRecord::where([['driver_id', '=', $driver->id], 'status' => 2])->with('SubscriptionPackage')->first();
            if (!empty($package->SubscriptionPackage)) {
                $package_name = $package->SubscriptionPackage->Name;
            }
        }
        return view('merchant.driver.show', compact('driver', 'rejectreasons', 'config', 'driver_wallet', 'driver_config', 'tempDocUploaded', 'package_name', 'vehicle_details', 'arr_segment'));
    }

    public function Wallet($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $driver = Driver::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $wallet_transactions = DriverWalletTransaction::where([['driver_id', '=', $id]])->orderBy("created_at")->paginate(25);
        return view('merchant.driver.wallet', compact('wallet_transactions', 'driver'));
    }

    public function VerifyDocument(Request $request, $id, $status)
    {
        $document = DriverDocument::findOrFail($id);
        $document->document_verification_status = $status;
        $document->save();
        return redirect()->back();
    }

    public function Reject(Request $request)
    {
        $request->validate([
            'reject_id' => 'required',
            'doc_id' => 'required',
        ]);
        $doc = DriverDocument::find($request->doc_id);
        $doc->document_verification_status = 3;
        $doc->reject_reason_id = $request->reject_id;
        $doc->save();
        return redirect()->back();
    }

    public function Vehicles($id)
    {
        $driver = Driver::with(['DriverVehicles' => function ($query) {
            $query->with('VehicleType', 'ServiceTypes');
        }])->findOrFail($id);
        $vehicle_model_expire = $driver->Merchant->Configuration->vehicle_model_expire;
        return view('merchant.driver.vehicle', compact('driver', 'vehicle_model_expire'));
    }

    public function EditVehicle($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $Newconfig = Configuration::select('vehicle_ac_enable')->where([['merchant_id', '=', $merchant_id]])->first();
        $config = BookingConfiguration::select('baby_seat_enable', 'wheel_chair_enable')->where([['merchant_id', '=', $merchant_id]])->first();
        $config->vehicle_ac_enable = $Newconfig->vehicle_ac_enable;
        $vehicle = DriverVehicle::findOrFail($id);
        $service_types = ServiceType::where('serviceStatus', 1)->get();
        $driver_config = DriverConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        return view('merchant.driver.edit-vehicle', compact('vehicle', 'config', 'service_types', 'driver_config'));
    }

    public function UpdateVehicle(Request $request, $id = NULL)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'vehicle_number' => 'required',
            'vehicle_color' => 'required',
            'vehicle_number_plate' => 'mimes:jpeg,jpg,png',
            'vehicle_document' => 'mimes:jpeg,jpg,png',
        ]);

        DB::beginTransaction();
        try {
            $Newconfig = Configuration::select('vehicle_ac_enable')->where([['merchant_id', '=', $merchant_id]])->first();
            $config = BookingConfiguration::select('baby_seat_enable', 'wheel_chair_enable')->where([['merchant_id', '=', $merchant_id]])->first();

            $driver_vehicle = DriverVehicle::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);

            $driver_vehicle->vehicle_number = $request->vehicle_number;
            $driver_vehicle->vehicle_color = $request->vehicle_color;

            if ($request->hasFile('vehicle_number_plate')) {
                $driver_vehicle->vehicle_number_plate_image = $this->uploadImage('vehicle_number_plate', 'vehicle_document');
            }

            if ($request->hasFile('vehicle_image')) {
                $driver_vehicle->vehicle_image = $this->uploadImage('vehicle_image', 'vehicle_document');
            }

            if ($request->service_types) {
                $old_service = array_pluck($driver_vehicle->ServiceTypes, 'id');
                $allService = array_merge($old_service, $request->service_types);
                $driver_vehicle->ServiceTypes()->sync($allService);
            }

            if ($Newconfig->vehicle_ac_enable == 1) {
                $driver_vehicle->ac_nonac = $request->ac_nonac;
            }
            if ($config->baby_seat_enable == 1) {
                $driver_vehicle->baby_seat = $request->baby_seat;
            }
            if ($config->wheel_chair_enable == 1) {
                $driver_vehicle->wheel_chair = $request->wheel_chair;
            }
            $driver_vehicle->vehicle_register_date = isset($request->vehicle_register_date) ? $request->vehicle_register_date : NULL;
            $driver_vehicle->save();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->route('driver.index')->with('success', trans('admin.vehicle_updated_successfully'));
    }

    public function verifyDriver($id, $status)
    {
//        $status = 1 means approve driver profile
//        $status = 2 means approve vehicle profile
        try {
            if ($status == 1) {
                $driver = Driver::findOrFail($id);
                $pending_status = $driver->signupStep;
                DriverDocument::where('driver_id', $driver->id)->update(['document_verification_status' => 2]);
                $driver->signupStep = 9; // Approve driver
                $driver->save();

                $ref = new ReferralController();
                $arr_params = array(
                    "driver_id" => $driver->id,
                    "check_referral_at" => "COMPLETE-SIGNUP"
                );
                $ref->checkReferral($arr_params);

                $config = Configuration::where('merchant_id', $driver->merchant_id)->first();
                if (isset($config->stripe_connect_enable) && $config->stripe_connect_enable == 1) {
                    StripeConnect::sync_account($driver);
                }
                // if driver is  from segment group 1 then approve vehicle
                if ($driver->segment_group_id == 1) {
                    if ($driver->FirstVehicle && ($pending_status == 8)) {
                        $driver->FirstVehicle->vehicle_verification_status = 2;
//                        $driver->FirstVehicle->vehicle_active_status = 2;
                        $driver->FirstVehicle->save();
                        DriverVehicleDocument::where('driver_vehicle_id', $driver->FirstVehicle->id)->update(['document_verification_status' => 2]);
                    }
                } else {
                    // approve document of handyman segments
                    DriverSegmentDocument::where('driver_id', $driver->id)->update(['document_verification_status' => 2]);
                }

                $ids = $driver->id;
                $merchant_id = $driver->merchant_id;
            } else {
                $vehicle = DriverVehicle::find($id);
                $vehicle->vehicle_verification_status = 2;
                $vehicle->save();
                DriverVehicleDocument::where('driver_vehicle_id', $vehicle->id)->update(['document_verification_status' => 2]);
                $ids = $vehicle->Driver->id;
                $merchant_id = $vehicle->Driver->merchant_id;
            }
            // send notification to driver
            if (!empty($ids)) {
                $string_file = $this->getStringFile($merchant_id);
                $driver = Driver::find($ids);
                setLocal($driver->language);
                $msg = $status == 1 ? trans("$string_file.account_has_been_approved_successfully") : trans("$string_file.vehicle_approved", ['number' => $vehicle->vehicle_number]);
                $title = $status == 1 ? trans("$string_file.account_approved") : trans("$string_file.vehicle_approved");
                $data['notification_type'] = $status == 1 ? "DRIVER_APPROVED" : "VEHICLE_APPROVED";
                $data['segment_type'] = "";
                $data['segment_data'] = [];
                $arr_param = ['driver_id' => $ids, 'data' => $data, 'message' => $msg, 'merchant_id' => $merchant_id, 'title' => $title, 'large_icon' => ''];
                Onesignal::DriverPushMessage($arr_param);
                setLocal();
            }
            return redirect()->route('driver.index')->withSuccess($msg);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function rejectDriver(Request $request)
    {
//        p($request->all());
        // this function is called when click on reject button of either driver profile
        // or vehicle reject screen
        $request->validate([
            'driver_id' => 'required',
            'comment' => 'required',
            'request_from' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $request_from = $request->request_from == "driver_profile" ? 1 : 2;
            $driver = Driver::find($request->driver_id);
            if ($request_from == 1) {
                $driver->signupStep = 8; // move to pending
                $driver->reject_driver = 2; // means reject the driver
                $driver->admin_msg = $request->comment;
                $driver->save();

                if (!empty($request->document_id)) {
                    DriverDocument::whereIn('id', $request->document_id)->update(['document_verification_status' => 3]);
                }

                // driver vehicle from profile screen
                if (!empty($request->driver_vehicle_id) && !empty($request->vehicle_documents)) {
                    $vehicle = DriverVehicle::findOrFail($request->driver_vehicle_id);
                    // if driver is owner of that vehicle then reject that driver when click on driver profile
                    if ($vehicle->owner_id == $request->driver_id) {
                        $vehicle->vehicle_verification_status = 3;
                        $vehicle->save();
                        if (!empty($request->vehicle_documents)) {
                            DriverVehicleDocument::whereIn('id', $request->vehicle_documents)->update(['document_verification_status' => 3]);
                        }
                    }
                }
                if (!empty($request->segment_documents)) {
                    DriverSegmentDocument::whereIn('id', $request->segment_documents)->update(['document_verification_status' => 3]);
                }

            } else {
                if ($request->driver_vehicle_id) {
                    $vehicle = DriverVehicle::findOrFail($request->driver_vehicle_id);
                    // if driver is owner of that vehicle then reject that driver when click on driver profile
                    if ($vehicle->owner_id == $request->driver_id) {
                        $vehicle->vehicle_verification_status = 3;
                        $vehicle->save();
                        if (!empty($request->vehicle_documents)) {
                            DriverVehicleDocument::whereIn('id', $request->vehicle_documents)->update(['document_verification_status' => 3]);
                        }
                    }
                }
            }
            // send reject notification
            $string_file = $this->getStringFile($driver->merchant_id);
            setLocal($driver->language);
            $msg = $request_from == 1 ? trans($string_file . '.driver') . ' ' . trans("$string_file.rejected") : trans($string_file . '.vehicle') . ' ' . trans("$string_file.rejected");
            $admin_panel_msg = $request_from == 1 ? trans($string_file . '.driver') . ' ' . trans("$string_file.rejected") : trans($string_file . '.vehicle') . ' ' . trans("$string_file.rejected");
            $title = $request_from == 1 ? trans($string_file . '.driver') . ' ' . trans("$string_file.rejected") : trans($string_file . '.vehicle') . '# ' . $vehicle->vehicle_number . ' ' . trans("$string_file.rejected");
            $data['notification_type'] = $request_from == 1 ? "DRIVER_REJECTED" : "VEHICLE_REJECTED";
            $data['segment_type'] = "";
            $data['segment_data'] = array("comment" => $request->comment);
            if(!empty($request->comment)){
                $msg = $request->comment;
            }
            $arr_param = ['driver_id' => $driver->id, 'data' => $data, 'message' => $msg, 'merchant_id' => $driver->merchant_id, 'title' => $title, 'large_icon' => ''];
            Onesignal::DriverPushMessage($arr_param);
            setLocal();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
        DB::commit();
//        p($request_from);
        if ($request_from == 1) {
            return redirect()->route('merchant.driver.rejected')->withSuccess($admin_panel_msg);
        } else {
            return redirect()->route('merchant.vehicle.rejected')->withSuccess($admin_panel_msg);
        }
    }

    public function TempDocumentVerify($id, $status)
    {
        /*$driver = Driver::find($id);
        if (count($driver) > 0) {
            $driverDocs = $driver->DriverDocument;
            foreach ($driverDocs as $driverDoc) {
                if ($driverDoc->temp_document_file != null) {
                    $driverDoc->temp_doc_verification_status = 2;
                    $driverDoc->save();
                }
            }
            $driverVehicleDocs = $driver->DriverVehicle[0]->DriverVehicleDocument;
            foreach ($driverVehicleDocs as $driverVehicleDoc) {
                if ($driverVehicleDoc->temp_document_file != null) {
                    $driverVehicleDoc->temp_doc_verification_status = 2;
                    $driverVehicleDoc->save();
                }
            }
//            $playerId[] = $driver->player_id;
            $merchant_id = $driver->merchant_id;
            $msg = trans('admin.temp_doc_approved');
            Onesignal::DriverPushMessage($driver->id, [], $msg, 13, $merchant_id, 1);
        }
        return redirect()->back()->with('success', trans('admin.doc_approved'));*/

        try {
            if ($status == 1) {
                $driver = Driver::findOrFail($id);
                DriverDocument::where('driver_id', $driver->id)->update(['temp_doc_verification_status' => 2]);

                // if driver is  from segment group 1 then approve vehicle
                if ($driver->segment_group_id == 1) {
                    if ($driver->FirstVehicle) {
                        DriverVehicleDocument::where('driver_vehicle_id', $driver->FirstVehicle->id)->update(['temp_doc_verification_status' => 2]);
                    }
                } else {
                    // approve document of handyman segments
                    DriverSegmentDocument::where('driver_id', $driver->id)->update(['temp_doc_verification_status' => 2]);
                }

                $ids = $driver->id;
                $merchant_id = $driver->merchant_id;
            } else {
                $vehicle = DriverVehicle::find($id);
                DriverVehicleDocument::where('driver_vehicle_id', $vehicle->id)->update(['temp_doc_verification_status' => 2]);
                $ids = $vehicle->Driver->id;
                $merchant_id = $vehicle->Driver->merchant_id;
            }
            // send notification to driver
            if (!empty($ids)) {
                $string_file = $this->getStringFile($merchant_id);
                $driver = Driver::find($ids);
                setLocal($driver->language);
                $msg = $status == 1 ? trans("$string_file.account_has_been_approved_successfully") : trans("$string_file.vehicle_approved", ['number' => $vehicle->vehicle_number]);
                $title = $status == 1 ? trans("$string_file.account_approved") : trans("$string_file.vehicle_approved");
                $data['notification_type'] = $status == 1 ? "DRIVER_APPROVED" : "VEHICLE_APPROVED";
                $data['segment_type'] = "";
                $data['segment_data'] = [];
                $arr_param = ['driver_id' => $ids, 'data' => $data, 'message' => $msg, 'merchant_id' => $merchant_id, 'title' => $title, 'large_icon' => ''];
                Onesignal::DriverPushMessage($arr_param);
                setLocal();
            }
            return redirect()->route('driver.index')->withSuccess($msg);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function rejectTempDoc(Request $request)
    {
        $request->validate([
            'driver_id' => 'required',
            'comment' => 'required',
            'request_from' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $request_from = $request->request_from == "driver_profile" ? 1 : 2;
            $driver = Driver::find($request->driver_id);
            $driver->temp_admin_msg = $request->comment;
            $driver->save();

            if (!empty($request->document_id)) {
                DriverDocument::whereIn('id', $request->document_id)->update(['temp_doc_verification_status' => 3]);
            }
            if (!empty($request->driver_vehicle_id)) {
                $vehicle = DriverVehicle::findOrFail($request->driver_vehicle_id);
                // if driver is owner of that vehicle then reject that driver when click on driver profile
                if ($vehicle->owner_id == $request->driver_id) {
                    if (!empty($request->vehicle_documents)) {
                        DriverVehicleDocument::whereIn('id', $request->vehicle_documents)->update(['temp_doc_verification_status' => 3]);
                    }
                }
            }
            if (!empty($request->segment_documents)) {
                DriverSegmentDocument::whereIn('id', $request->segment_documents)->update(['temp_doc_verification_status' => 3]);
            }
            // send reject notification
            $string_file = $this->getStringFile($driver->merchant_id);
            setLocal($driver->language);
            $msg = $request_from == 1 ? trans($string_file . '.driver') . ' ' . trans("$string_file.rejected") : trans($string_file . '.vehicle') . ' ' . trans("$string_file.rejected");
            $admin_panel_msg = $request_from == 1 ? trans($string_file . '.driver') . ' ' . trans("$string_file.rejected") : trans($string_file . '.vehicle') . ' ' . trans("$string_file.rejected");
            $title = $request_from == 1 ? trans($string_file . '.driver') . ' ' . trans("$string_file.rejected") : trans($string_file . '.vehicle') . '# ' . $vehicle->vehicle_number . ' ' . trans("$string_file.rejected");
            $data['notification_type'] = "TEMP_DOC_REJECTED";
            $data['segment_type'] = "";
            $data['segment_data'] = array("comment" => $request->comment);
            if(!empty($request->comment)){
                $msg = $request->comment;
            }
            $arr_param = ['driver_id' => $driver->id, 'data' => $data, 'message' => $msg, 'merchant_id' => $driver->merchant_id, 'title' => $title, 'large_icon' => ''];
            Onesignal::DriverPushMessage($arr_param);
            setLocal();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
        DB::commit();
//        p($request_from);
        if ($request_from == 1) {
            return redirect()->route('merchant.driver.rejected.temporary')->withSuccess($admin_panel_msg);
        } else {
            return redirect()->route('merchant.vehicle.rejected.temporary')->withSuccess($admin_panel_msg);
        }
    }

    public function VehiclesDocumentReject(Request $request)
    {
        $request->validate([
            'reject_id' => 'required',
            'doc_id' => 'required',
        ]);
        $document = DriverVehicleDocument::findOrFail($request->doc_id);
        $document->document_verification_status = 3;
        $document->reject_reason_id = $request->reject_id;
        $document->save();
        return redirect()->back();
    }

    public function VehiclesDetail($id)
    {
        $vehicle = DriverVehicle::with(['DriverVehicleDocument'])->findOrFail($id);
        $driver = $vehicle->Driver->id;
        $baby_seat_enable = $vehicle->Driver->Merchant->BookingConfiguration->baby_seat_enable == 1 ? true : false;
        $wheel_chair_enable = $vehicle->Driver->Merchant->BookingConfiguration->wheel_chair_enable == 1 ? true : false;
        $vehicle_ac_enable = $vehicle->Driver->Merchant->Configuration->vehicle_ac_enable == 1 ? true : false;
        $vehicle_model_expire = $vehicle->Driver->Merchant->Configuration->vehicle_model_expire;
        $result = check_driver_document($driver, $type = 'vehicle', $id);
        return view('merchant.drivervehicles.vehicle-details', compact('vehicle', 'result', 'baby_seat_enable', 'wheel_chair_enable', 'vehicle_ac_enable', 'vehicle_model_expire'));
    }

    public function VehiclesDocumentVerify($id, $status)
    {
        $document = DriverVehicleDocument::findOrFail($id);
        $document->document_verification_status = $status;
        $document->save();
        return redirect()->back();
    }

    public function AddMoney(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|integer|between:1,2',
            'receipt_number' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'transaction_type' => 'required',
            'driver_id' => 'required|exists:drivers,id'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return error_response($errors);
        }

        // $request->validate([
        //     'payment_method_id' => 'required|integer|between:1,2',
        //     'receipt_number' => 'required|string',
        //     'amount' => 'required|numeric',
        //     'driver_id' => 'required|exists:drivers,id'
        // ]);
//        $newAmount = new \App\Http\Controllers\Helper\Merchant();
        // set time zone, debit is doing according to driver service area
        $driver = Driver::select('country_area_id', 'id')->find($request->driver_id);
//        date_default_timezone_set($driver->CountryArea->timezone);
        $paramArray = array(
            'driver_id' => $request->driver_id,
            'booking_id' => NULL,
            'amount' => $request->amount,
            'narration' => 1,
            'platform' => 1,
            'payment_method' => $request->payment_method_id,
            'action_merchant_id' => Auth::user('merchant')->id
        );
        if ($request->transaction_type == 1) {
            WalletTransaction::WalletCredit($paramArray);
        } else {
            $paramArray['narration'] = 18;
            WalletTransaction::WalletDeduct($paramArray);
        }
        return success_response(trans('admin.message207'));
    }

    public function ShowSubscriptionPacks(Request $request, $driver_id = null)
    {
        $driver = Driver::findorfail($driver_id);
        $packages = SubscriptionPackage::where([['merchant_id', $driver->merchant_id], ['status', true]])
            ->whereHas('CountryArea', function ($query) use (&$driver) {
                $query->where('country_area_id', '=', $driver->CountryArea->id);
            })
            ->whereNotIn('id', function ($query) use ($driver_id) {
                $query->select('subscription_pack_id')->where('driver_id', $driver_id)->where('package_type', 1)->from('driver_subscription_records');
            })
            ->select(['id', 'package_duration_id', 'max_trip', 'image', 'price', 'package_type'])
            ->get();
        $packages = $this->PaginateCollection($packages);

        return view('merchant.driver.show_subscription_packs', compact('driver', 'packages'));
    }

//    public function Activated_Subscription(Request $request, $driver_id = null)
//    {
//        $driver = Driver::findorfail($driver_id);
//        if ($driver->Merchant->Configuration->subscription_package != 1) {
//            return redirect()->route('merchant.dashboard')->withErrors(trans("$string_file.permission_denied"));
//        }
//
//        $driver_all_packs = $driver->DriverSubscriptionRecord->sortByDesc('id');
//        $driver_all_packs = $this->PaginateCollection($driver_all_packs);
//        return view('merchant.driver.activated_subscription', compact('driver', 'driver_all_packs'));
//    }
//
//    public function Activate_Subscription_Wallet(Request $request, $id = null)
//    {
//        $driver = Driver::findorfail($id);
//        $package = SubscriptionPackage::findorfail($request->package);
//        if ($driver->wallet_money < $package->price):
//            request()->session()->flash('error', trans('admin.driver_low_wallet'));
//            return redirect()->route('driver.activated_subscription', $driver->id);
//        endif;
//        //        $driver->wallet_money = ($driver->wallet_money - $package->price);
////        $driver->save();
//        $paramArray = array(
//            'driver_id' => $driver->id,
//            'booking_id' => $package->id,
//            'amount' => $package->price,
//            'narration' => 4,
//            'platform' => 2,
//            'payment_method' => 3,
//            'receipt' => rand(1111, 983939),
//        );
//        WalletTransaction::WalletDeduct($paramArray);
////        CommonController::WalletDeduct($driver->id,$package->id,$package->price,4,2,3,rand(1111, 983939));
////        DriverWalletTransaction::create([
////            'merchant_id' => $driver->merchant_id,
////            'driver_id' => $driver->id,
////            'transaction_type' => 2,
////            'payment_method' => 3,
////            'receipt_number' => rand(1111, 983939),
////            'amount' => $package->price,
////            'platform' => 2,
////            'subscription_package_id' => $package->id,
////            'description' => 'On Activation of Subscription Pack',
////            'narration' => 4,
////        ]);
//        $request->request->add(['payment_method_id' => 3,
//            'subscription_package_id' => $request->package, 'driver_id' => $id]);
//        $package = new SubscriptionPackageController();
//        $package->SavePackageDetails($request, true);
//        request()->session()->flash('message', trans('admin.subspack_added'));
//        return redirect()->route('driver.activated_subscription', $driver->id);
//    }
//
//    public function Activate_Subscription_Cash(Request $request, $id = null)
//    {
//        $request->request->add(['payment_method_id' => 1,
//            'subscription_package_id' => $request->package, 'driver_id' => $id]);
//        $package = new SubscriptionPackageController();
//        $package->SavePackageDetails($request, true);
//        request()->session()->flash('message', trans('admin.subspack_added'));
//        return redirect()->route('driver.activated_subscription', $id);
//    }

    public function PaginateCollection($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items); // Check Collection, or make Array into Collection
        return new LengthAwarePaginator($items->forPage($page, $perPage)->values(), $items->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
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
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $driver = Driver::findOrFail($id);
        $merchant_id = $driver->merchant_id;
        $string_file = $this->getStringFile(NULL, $driver->Merchant);
        if ($status == 2) {
//            $booking = Booking::where('driver_id', $driver->id)->whereIn('booking_status', [1002, 1003, 1004])->latest()->first();
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

    public function Logout($id)
    {
//        $merchant_id = get_merchant_id();
        $driver = Driver::findOrFail($id);
        $merchant_id = $driver->merchant_id;
        $string_file = $this->getStringFile(NULL, $driver->Merchant);
        $booking = Booking::where('driver_id', $driver->id)->whereIn('booking_status', [1002, 1003, 1004])->latest()->first();
        if (empty($booking)) {
            $driver->online_offline = 2;
            $driver->login_logout = 2;
            $driver->free_busy = 2;
            $driver->save();
            $config = $driver->Merchant->Configuration;
            $data = [];
            setLocal($driver->language);
            $title = trans("$string_file.logout");
            $message = trans("$string_file.account_has_been_logout_by_admin");
            $data['notification_type'] = "LOGOUT";
            $data['segment_type'] = "";
            $data['segment_data'] = [];
            $arr_param = ['driver_id' => $driver->id, 'data' => $data, 'message' => $message, 'merchant_id' => $merchant_id, 'title' => $title, 'large_icon' => NULL];
            Onesignal::DriverPushMessage($arr_param);
            setLocal();
            $action = 'success';

        } else {
            $action = 'error';
            $message = trans("$string_file.running_job_config_error");
        }
        return redirect()->back()->with($action, $message);
    }

    public function PersonalDocExpire()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $currentDate = date("Y-m-d");
        $doc = DriverDocument::whereHas('Driver', function ($query) use ($merchant_id) {
            $query->where([['merchant_id', '=', $merchant_id]]);
        })->whereDate('expire_date', '<', $currentDate)->get();
        if (empty($doc->toArray())) {
            return redirect()->back();
        }
        $docment_ids = array_pluck($doc, 'id');
        $driver_ids = array_pluck($doc, 'driver_id');
        DriverDocument::whereIn('id', $docment_ids)->update(['document_verification_status' => 4]);
        $message = trans('admin.message359');
        $data = ['title' => $message];
        Onesignal::DriverPushMessage($driver_ids, $data, $message, 7, $merchant_id);
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $request_from = isset($request->request_from) ? $request->request_from : NULL;
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
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
                
                 // make document inactive
                DriverDocument::where([['driver_id', '=', $delete->id]])->update(['status' => 2]);
                DriverVehicleDocument::whereHas('DriverVehicle',function($q) use ($id){
                    $q->where('driver_id',$id);
                })->update(['status' => 2]);
                
                DriverVehicle::where([['owner_id', '=', $delete->id], ['driver_id', '=', $delete->id]])->update(['vehicle_delete' => 1]);
            }
            setLocal($delete->language);
            $data = ['booking_status' => '999'];
            $message = trans("$string_file.account_has_been_deleted");
            $title = trans("$string_file.account_deleted");
            $arr_param = ['driver_id' => $delete->id, 'data' => $data, 'message' => $message, 'merchant_id' => $merchant_id, 'title' => $title];
            Onesignal::DriverPushMessage($arr_param);
            setLocal();
            echo trans("$string_file.data_deleted_successfully");
        else:
            echo trans("$string_file.some_thing_went_wrong");
        endif;
    }

    public function DeletePendingVehicle(Request $request, $id)
    {
        $vehicle_rides = Booking::where([['driver_vehicle_id', '=', $id]])->count();
        if ($vehicle_rides > 0) {
            $vehicle = DriverVehicle::find($id);
            $vehicle->vehicle_delete = 1;
            $vehicle->save();
            // return redirect()->back()->with('vehcile', trans('Vehicle Deleted Successfully'));
            echo trans('Vehicle Deleted Successfully');
        } else {
            $vehicle_docs = DriverVehicleDocument::where([['driver_vehicle_id', '=', $id]])->get();
            foreach ($vehicle_docs as $vehicle_doc) {
                $image_path = $vehicle_doc->document;
                if (File::exists($image_path)) {
                    File::delete($image_path);
                }
                $vehicle_doc->delete();
            }
            DriverVehicle::where([['id', '=', $id]])->delete();
            // return redirect()->back()->with('vehcile', trans('Vehicle Deleted Successfully'));
            echo trans('Vehicle Deleted Successfully');
        }
    }

    // get rejected driver list
    public function rejectedDriver(Request $request)
    {
//        $drivers = $this->getAllRejectedDrivers();
        $request->request->add(['search_route' => route('merchant.driver.rejected'), 'request_from' => "rejected_driver"]);
        $drivers = $this->getAllDriver(true, $request);
        $search_view = $this->driverSearchView($request);
        $arr_search = $request->all();
        $info_setting = InfoSetting::where('slug', 'DRIVER')->first();
        return view('merchant.driver.rejected', compact('drivers', 'search_view', 'arr_search', 'info_setting'));
    }

    // get rejected driver list
    public function rejectedDriverTemporary(Request $request)
    {
        $request->request->add(['search_route' => route('merchant.driver.rejected.temporary'), 'request_from' => "rejected_driver_temporary"]);
        $drivers = $this->getAllDriver(true, $request);
        $search_view = $this->driverSearchView($request);
        $arr_search = $request->all();
        $info_setting = InfoSetting::where('slug', 'DRIVER')->first();
        return view('merchant.driver.rejected-temp', compact('drivers', 'search_view', 'arr_search', 'info_setting'));
    }

//    public function DisapproveDriver($id)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $driver = Driver::find($id);
//        $driver->signupStep = 4;
//        $driver->save();
////        $playerids = array($driver->player_id);
//        $data = [];
//        $message = trans('admin.admin_disapprove_message');
//        Onesignal::DriverPushMessage($driver->id, $data, $message, 6, $merchant_id);
//        return redirect()->back();
//    }

    public function ApproveDriver($id)
    {
        DB::beginTransaction();
        try {
            $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
            $driver = Driver::find($id);
            $driver->signupStep = 3;
            $driver->save();
            $playerids = array($driver->player_id);
            $data = [];
            $vehicle = DriverVehicle::where([['owner_id', '=', $id]])->first();
            if (!empty($vehicle)) {
                $vehicle->vehicle_verification_status = 1;
//                $vehicle->vehicle_active_status = 1;
                $vehicle->save();
            }
            $config = Configuration::where('merchant_id', $merchant_id)->first();
            if (isset($config->stripe_connect_enable) && $config->stripe_connect_enable == 1) {
                StripeConnect::sync_account($driver);
            }
            $message = trans('admin.admin_approve_message');
            Onesignal::DriverPushMessage($driver->id, $data, $message, 6, $merchant_id);
            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        $delete = Driver::find($request->id);
        $delete->delete();
        echo trans('admin.message697');
    }

    public function BlockDrivers(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $drivers = Driver::where([['merchant_id', '=', $merchant_id], ['driver_block_status', '=', 1]])->paginate(10);
        return view('merchant.driver.block_drivers', compact('drivers'));
    }

    public function driver_unblock(Request $request)
    {
        $driver = Driver::find($request->id);
        $driver->driver_block_status = 0;
        $driver->save();
        echo trans('admin.unblocked');
    }

//    public function ExpireDocuments(Request $request)
//    {
//        return view('merchant.driver.expire_document');
//    }

    public function Cronjob_DriverBlock(Request $request)
    {
        $driver = DriverAccount::with('Driver')->get();
        $driver = $driver->toArray();
        $driver_det = array();
        $id = array();
        $now_date = date('Y-m-d');
        foreach ($driver as $value) {
            $block_date = explode(' ', $value['block_date']);
            $block_date_new = $block_date[0];
            if ($now_date == $block_date_new) {
                $merchant_id = $value['merchant_id'];
                $driver_det = $value['driver_id'];
                $driver_id = $value['driver_id'];
                $details = Driver::find($driver_id);
                $details->driver_block_status = 1;
                $details->save();
                $id[] = $value['driver']['id'];
            }
        }
        if (!empty($driver_det)) {
            $data = array();
            $message = "Driver Blocked";
            $type = 190;
            Onesignal::DriverPushMessage($id, $data, $message, $type, $merchant_id);
            return redirect()->route('merchant.driver.block')->with('moneyAdded', 'Driver Block.');
        } else {
            return redirect()->route('merchant.driver.block')->with('moneyAdded', 'Driver not block.');
        }
    }

    public function editDriverVehicleDocument($id, $vehicle_id)
    {
        $merchant_id = get_merchant_id();
        $driver = Driver::with('DriverVehicle.DriverVehicleDocument')->where([['merchant_id', '=', $merchant_id], ['driver_delete', '=', NULL]])->findOrFail($id);
        return view('merchant.driver.edit-vehicle-doc', compact('driver', 'vehicle_id'));
    }

    public function storeDriverVehicleDocument(Request $request, $id, $vehicle_id)
    {
        $expiredate = $request->expiredate;
        $images = $request->file('document');
        $all_doc = $request->input('all_doc');
        foreach ($all_doc as $document_id) {
            $image = isset($images[$document_id]) ? $images[$document_id] : null;
            $expiry_date = isset($expiredate[$document_id]) && !empty($expiredate[$document_id]) ? $expiredate[$document_id] : NULL;
            $driver_v_document = DriverVehicleDocument::where([['driver_vehicle_id', $vehicle_id], ['document_id', $document_id]])->first();
            if (empty($driver_v_document->id)) {
                $driver_v_document = new DriverVehicleDocument;
            }
            $driver_v_document->driver_vehicle_id = $vehicle_id;
            $driver_v_document->document_id = $document_id;
            $driver_v_document->expire_date = $expiry_date;
            $driver_v_document->document_verification_status = 2;
            if (!empty($image)) {
                $driver_v_document->document = $this->uploadImage($image, 'vehicle_document', NULL, 'multiple');
            }
            $driver_v_document->save();
        }
        return redirect()->route('driver.show', $id)->withSuccess(trans('admin.editDocSucess'));
    }

    public function referralEarning($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $driver = Driver::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $referralEarning = ReferralDriverDiscount::where(['merchant_id' => $merchant_id, 'driver_id' => $driver->id], ['expire_status', '!=', 1], ['payment_status', '!=', 1])->paginate(25);
        return view('merchant.driver.referral-earning', compact('referralEarning', 'driver'));
    }

    public function DriverRefer($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $driver = Driver::find($id);
        $referral_details = ReferralDiscount::where([['sender_id', '=', $id], ['sender_type', '=', "DRIVER"], ['merchant_id', '=', $merchant_id]])->latest()->paginate(10);
        foreach ($referral_details as $refer) {
            $receiverDetails = $refer->receiver_type == "USER" ? User::find($refer->receiver_id) : Driver::find($refer->receiver_id);
            $phone = $refer->receiver_type == "USER" ? $receiverDetails->UserPhone : $receiverDetails->phoneNumber;
            $receiverType = $refer->receiver_type == "USER" ? 'User' : 'Driver';
            $refer->receiver_details = array(
                'id' => $receiverDetails->id,
                'name' => $receiverDetails->first_name . ' ' . $receiverDetails->last_name,
                'phone' => $phone,
                'email' => $receiverDetails->email);
            $refer->receiverType = $receiverType;
        }
        return view('merchant.driver.driver_refer', compact('referral_details', 'driver'));
    }

    // move driver to pending list from rejected list
    public function MoveToPending(Request $request)
    {
        $driver = Driver::find($request->driver_id);
        $string_file = $this->getStringFile(NULL, $driver->Merchant);
        if (!empty($driver)) {
            $driver->signupStep = 8;
            $driver->reject_driver = 1;
            $driver->save();
            DriverDocument::where('driver_id', $driver->id)->update(['document_verification_status' => 1]);

            if ($driver->segment_group_id == 1) {
                $driver_id = $driver->id;
                DriverVehicleDocument::where('driver_vehicle_id', $driver_id)->update(['document_verification_status' => 1]);
                DriverVehicle::whereHas('Driver', function ($q) use ($driver_id) {
                    $q->where('driver_id', $driver_id);
                })
                    ->update(['vehicle_verification_status' => 1]);
            } else {
                DriverSegmentDocument::where('driver_id', $driver->id)->update(['document_verification_status' => 1]);
            }
//            return redirect()->route('driver.pending.show')->withSucess(trans('admin.move_to_pending'));
            return redirect()->route('driver.show', $driver->id)->withSucess(trans("$string_file.move_to_pending"));
        } else {
            return redirect()->back()->withErrors(trans('admin.no_driver'));
        }
    }

    // assign
    public function AssignFreeSubscription(Request $request, $id = null)
    {
        request()->session()->flash('message', trans('admin.subspack_added'));
        // driver subscription code for free package assignment
        $existing_package = DriverSubscriptionRecord::
        where('package_type', 1)
            ->where(function ($q) {
                $q->where([['end_date_time', '>=', date('Y-m-d H:i:s')], ['status', 1]]);
                $q->orWhere([['end_date_time', NULL], ['start_date_time', NULL]]);
            })
            ->orderBy('id', 'DESC')
            ->first();
//             p($existing_package);
        if ((!empty($existing_package->id) && ($request->package != $existing_package->subscription_pack_id) || $request->package == null)) {
            $existing_package->status = 0; // make previous package disable
            $existing_package->save();
        }
        $merchant_id = get_merchant_id();
        if (!empty($request->package)) {
            $driver_pack = new DriverSubscriptionRecord;
            $pack = SubscriptionPackage::Find($request->package);
            $driver_pack->subscription_pack_id = $request->package;
            $driver_pack->driver_id = $id;
            $driver_pack->payment_method_id = null;
            $driver_pack->package_duration_id = $pack->package_duration_id;
            $driver_pack->price = $pack->price;
            $driver_pack->package_total_trips = $pack->max_trip;
            $driver_pack->package_type = $pack->package_type;
            $driver_pack->used_trips = 0;
            $driver_pack->start_date_time = NULL; //when driver make is active
            $driver_pack->end_date_time = NULL; //when driver make is active
            $driver_pack->status = 1;  // assigned
            $driver_pack->save();

            $msg = trans('api.free_subscription_notify_driver');
            $type = 17;
            Onesignal::DriverPushMessage($id, [], $msg, $type, $merchant_id, 1);
        }
        return redirect()->route('driver.activated_subscription', $id);
    }

    public function RejectedVehicle()
    {
//        if (!Auth::user('merchant')->can('view_rejected_vehicle')) {
//            abort(404, 'Unauthorized action.');
//        }
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $merchant_id = get_merchant_id();
        $query = DriverVehicle::with('Driver')->where([['merchant_id', '=', $merchant_id], ['vehicle_verification_status', 3]]);
        if(!empty($permission_area_ids))
        {
            $query->whereHas("Driver",function($q) use($permission_area_ids){
                $q->whereIn('country_area_id',$permission_area_ids);
            });
        }
        $query->orderBy('id', 'DESC');
        $vehicles = $query->paginate(20);
//        p($vehicles);
        $info_setting = InfoSetting::where('slug', 'DRIVER_VEHICLE_REJECTED')->first();
        return view('merchant.drivervehicles.vehicle-rejected', compact('vehicles', 'info_setting'));
    }

    public function FindDriverLocationNotUpdate()
    {
        $merchant_id = get_merchant_id();
        $countryAreas = CountryArea::where([['merchant_id', $merchant_id], ['status', 1]])->get();
        $drivers = array();
        return view('merchant.driver.find_loc_not_update', compact('countryAreas', 'drivers'));
    }

    public function SearchDriverLocationNotUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_area' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'time' => 'required'
        ]);

        if ($validator->fails()) {
            $msg = $validator->messages()->all();
            return redirect()->back()->withErrors($msg[0]);
        }

        $data = $request->all();
        $merchant_id = get_merchant_id();
        $countryArea = CountryArea::where([['merchant_id', $merchant_id], ['id', $request->country_area]])->first();
        $timeZone = $countryArea->timezone ? $countryArea->timezone : "Asia/Kolkata";
//        date_default_timezone_set($timeZone);
        $startDate = $request->to_date . ' ' . date('H:i:s');
        $endDate = $request->from_date . ' ' . $request->time . ':00';
        $drivers = Driver::where([['last_location_update_time', '>=', $endDate], ['last_location_update_time', '<=', $startDate]])->orderBy('last_location_update_time', 'desc')->paginate(15);
        $countryAreas = CountryArea::where([['merchant_id', $merchant_id], ['status', 1]])->get();
        return view('merchant.driver.find_loc_not_update', compact('drivers', 'countryAreas', 'data'));
    }

//    public function DetailList()
//    {
//        if (Auth::user()->demo == 1) {
//            $otp = 4194568863;//getRandomCode(10);
//            Session::put('demo_otp', $otp);
//            $drivers = [];
//            return view('merchant.driver.driver-list', compact('drivers'));
//        }
//        return redirect()->route('merchant.dashboard')->withErrors(trans("$string_file.permission_denied"));
//    }

//    public function verfiyOtp(Request $request)
//    {
//        if (Auth::user()->demo == 1) {
//            $session_otp = Session::get('demo_otp');
//            $session_post = $request->otp;
//            if ($session_otp == $session_post) {
//                $merchant_id = Auth::user()->id;
//                $drivers = Driver::where([['merchant_id', '=', $merchant_id], ['taxi_company_id', '=', NULL], ['signupStep', '=', 3], ['driver_delete', '=', NULL]])->orderBy('created_at', 'DESC')->get();
//                return view('merchant.driver.driver-list', compact('drivers'));
//            }
//        }
//        return redirect()->route('merchant.dashboard')->withErrors(trans("$string_file.permission_denied"));
//
//    }

//    public function send_custom_mail()
//    {
//        $otp = 1234;
//        $reciever_email = \Config::get('custom.static_email');
//        $mail = new PHPMailer(true);
//        $mail->SMTPDebug = 0;
//        $mail->Host = 'smtp.gmail.com';
//        $mail->SMTPAuth = true;
//        $mail->Username = 'demo6619@gmail.com';
//        $mail->Password = 'apporio95605';
//        $mail->SMTPSecure = 'ssl';
//        $mail->Port =465;
//        //Recipients
//        $mail->setFrom('demo6619@gmail.com');
//        $mail->addAddress($reciever_email);
//        //Content
//        $mail->Subject = 'Driver/User list Otp';
//        $mail->Body = $otp;
//        $mail->AltBody = 'Alt Body';
//        $mail->send();
//    }

//    public function getDriverCommissionChoices($config)
//    {
//        $arr_choice = [];
//        $config->subscription_module = ($config->Configuration->subscription_package == 1 && $config->ApplicationConfiguration->driver_commission_choice == 1) ? true : false;
//        if ($config->subscription_module == true) {
//            $merchant_helper = new MerchantHelper;
//            $arr_choice_data = $merchant_helper->DriverCommissionChoices($config); // send merchant
//            foreach ($arr_choice_data as $choice) {
//                $arr_choice[$choice['id']] = $choice['lang_data'];
//            }
//            $arr_choice = add_blank_option($arr_choice, trans("$string_file.select"));
//        }
//        return $arr_choice;
//    }

    public function driverStripeConnect(Request $request, $id)
    {
        try {
            $merchant_id = get_merchant_id();
            $driver = Driver::with('DriverDocument')->where([['merchant_id', '=', $merchant_id]])->find($id);
            $configuration = Configuration::select('stripe_connect_enable')->where('merchant_id', $merchant_id)->first();
            if ($configuration->stripe_connect_enable != 1) {
                return redirect()->back();
            }
            $merchant_stripe_config = MerchantStripeConnect::where('merchant_id', $merchant_id)->first();
            $stripe_docs_list = self::getStripeRelatedDocumentList($merchant_stripe_config, $driver);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
        return view('merchant.driver.stripe_connect', compact('id', 'driver', 'merchant_stripe_config', 'stripe_docs_list'));
    }

    public function driverStripeConnectStore(Request $request, $id)
    {
        try {
            $merchant_id = get_merchant_id();
            $driver = Driver::findorFail($id);
            $merchant_stripe_config = MerchantStripeConnect::where('merchant_id', $merchant_id)->first();
            $stripe_docs_list = self::getStripeRelatedDocuments($merchant_stripe_config, $driver);
            $driver->sc_identity_photo = $stripe_docs_list['personal_document']['image_name'];
            $driver->sc_identity_photo_status = 'pending';
            $driver->ssn = $stripe_docs_list['personal_document']['doc_number'];
            $driver->save();
            $personal_id = StripeConnect::upload_file($stripe_docs_list['personal_document']['image'], $driver->merchant_id, 'customer_signature');
            $photo_front_id = StripeConnect::upload_file($stripe_docs_list['photo_front_document']['image'], $driver->merchant_id, 'identity_document');
            $photo_back_id = StripeConnect::upload_file($stripe_docs_list['photo_back_document']['image'], $driver->merchant_id, 'identity_document');
            $additional_id = StripeConnect::upload_file($stripe_docs_list['additional_document']['image'], $driver->merchant_id, 'additional_verification');
            $verification_details = [
                'personal_id' => $personal_id->id,
                'photo_front_id' => $photo_front_id->id,
                'photo_back_id' => $photo_back_id->id,
                'additional_id' => $additional_id->id
            ];
            if (!empty($driver->sc_account_id)) {
                $driver = StripeConnect::update_driver_account($driver, $verification_details);
            } else {
                $driver = StripeConnect::create_driver_account($driver, $verification_details);
            }
            return redirect()->back()->withInput()->with('success', trans('admin.stripe_registration_successfully_done'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function driverStripeConnectSync(Request $request, $id)
    {
        try {
            $merchant_id = get_merchant_id();
            $driver = Driver::findorFail($id);
            $driver = StripeConnect::sync_account($driver);
            if (isset($driver->sc_due_list) && $driver->sc_due_list != NULL) {
                $ss = json_decode($driver->sc_due_list, true);
                return redirect()->back()->withInput()->withErrors($ss);
            } else {
                return redirect()->back()->withInput()->with('success', trans('admin.stripe_connect_successfully_sync'));
            }
            return redirect()->back()->withInput()->withErrors('Invalid Sync');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function driverStripeConnectDelete(Request $request, $id)
    {
        try {
            $merchant = get_merchant_id(false);
            $driver = Driver::findorFail($id);
            if (isset($driver->sc_account_id) && $driver->sc_account_id != NULL) {
                $result = StripeConnect::delete_account($driver);
                return redirect()->back()->withInput()->with('success', trans('admin.stripe_connect_deleted_successfully'));
            } else {
                return redirect()->back()->withInput()->with('error', 'Account not created');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }

    // get driver details
    public function orderDetail(Request $request, $id)
    {
        $order_obj = new Order;
        $request->request->add(['id' => $id]);
        $order = $order_obj->getOrders($request);
        $string_file = $this->getStringFile($order->merchant_id);
        if (!empty($order->id)) {
            $order = $order_obj->getOrders($request);
            $business_segment = $order->BusinessSegment;
            $arr_status = $this->getOrderStatus(['merchant_id' => $order->merchant_id]);
            $title = trans('admin.orders');
            $redirect_route = "";
            if (!empty($order->driver_id)) {
                $redirect_route = route('merchant.driver.jobs', ['order', $order->driver_id]);
            }
            $cancel_receipt = HolderController::userOrderCancelHolder($order, $string_file);
            return view('merchant.driver.order-detail', compact('order', 'arr_status', 'title', 'business_segment', 'redirect_route', 'cancel_receipt'));
        }
        return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
    }


    public function earningSummary(Request $request)
    {
        $checkPermission = check_permission(1, 'view_drivers');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $request->request->add(['merchant_id' => $merchant_id]);

        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }

        $query = Driver::select('id', 'merchant_driver_id', 'first_name', 'phoneNumber', 'email', 'last_name', 'merchant_id', 'country_area_id', 'segment_group_id')
            ->where([['merchant_id', '=', $merchant_id], ['taxi_company_id', '=', NULL], ['driver_delete', '=', NULL], ['signupStep', '=', 9], ['driver_admin_status', '=', 1]])
            ->orderBy('created_at', 'DESC');
        if (!empty($request->area_id) || !empty($request->parameter) || !empty($request->segment_id)) {
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
            $query->with(['Booking' => function ($qq) use ($merchant_id) {
                $qq->select('id', 'driver_id', 'booking_status', 'merchant_id');
                $qq->where('booking_status', 1005);
                $qq->where('merchant_id', $merchant_id);
            }])
                ->with(['Order' => function ($qq) use ($merchant_id) {
                    $qq->select('id', 'driver_id', 'order_status', 'merchant_id');
                    $qq->where('order_status', 11);
                    $qq->where('merchant_id', $merchant_id);
                }])
                ->with(['HandymanOrder' => function ($qq) use ($merchant_id) {
                    $qq->select('id', 'driver_id', 'order_status', 'merchant_id');
                    $qq->where('order_status', 11);
                    $qq->where('merchant_id', $merchant_id);
                }]);

            if ($request->keyword) {
                $query->where($parameter, 'like', '%' . $request->keyword . '%');
            }
            if ($request->area_id) {
                $query->where('country_area_id', '=', $request->area_id);
            }
            if (!empty($request->segment_id)) {
                $arr_segment_id = $request->segment_id;
                $query->whereHas('Segment', function ($q) use ($arr_segment_id) {
                    $q->whereIn('segment_id', $arr_segment_id);
                });
            }
        }
        if(!empty($permission_area_ids)){
            $query->whereIn("country_area_id",$permission_area_ids);
        }
        $drivers = $query->paginate(25);
        $drivers->map(function ($driver) use ($merchant_id) {
            $ride_amount = 0;
            $total_completed_rides = 0;
            if (!empty($driver->Booking) && $driver->Booking->count() > 0) {
                $arr_all_rides =  $driver->Booking->where('booking_status',1005);
                $arr_ride_id = array_pluck($arr_all_rides, 'id');
//                $ride_amount = BookingTransaction::select(DB::raw("sum('driver_earning') as driver_earning"))->whereIn("booking_id",$arr_ride_id)->first();
//                $ride_amount = $ride_amount->driver_earning;
                $ride_amount = BookingTransaction::whereIn("booking_id", $arr_ride_id)->get()->sum("driver_earning");
                $total_completed_rides = count($arr_ride_id);
            }
            $order_amount = 0;
            $total_completed_orders = 0;
            if (!empty($driver->Order) && $driver->Order->count() > 0) {
                $arr_order_id = array_pluck($driver->Order, 'id');
//                $order_amount = BookingTransaction::select(DB::raw("sum('driver_earning') as driver_earning"))->whereIn("order_id", $arr_order_id)->first();
//                $order_amount = $order_amount->driver_earning;
                $order_amount = BookingTransaction::whereIn("order_id", $arr_order_id)->get()->sum("driver_earning");
                $total_completed_orders = count($arr_order_id);
            }
            $booking_amount = 0;
            $total_completed_bookings = 0;
            if (!empty($driver->HandymanOrder) && $driver->HandymanOrder->count() > 0) {
                $arr_booking_id = array_pluck($driver->HandymanOrder, 'id');
//                $booking_amount = BookingTransaction::select(DB::raw("sum('driver_earning') as driver_earning"))->whereIn("handyman_order_id", $arr_booking_id)->first();
//                $booking_amount = $booking_amount->driver_earning;
                $booking_amount = BookingTransaction::whereIn("handyman_order_id", $arr_booking_id)->get()->sum("driver_earning");
                $total_completed_bookings = count($arr_booking_id);
            }
            $driver['order_earning'] = $order_amount;
            $driver['total_orders'] = $total_completed_orders;
            $driver['ride_earning'] = $ride_amount;
            $driver['total_rides'] = $total_completed_rides;
            $driver['booking_earning'] = $booking_amount;
            $driver['total_bookings'] = $total_completed_bookings;
            return $driver;
        });
        $request->request->add(['search_route' => route('merchant.driver.earning')]);
        $search_view = $this->driverSearchView($request);
        $arr_search = $request->all();
        $info_setting = InfoSetting::where('slug', 'INDIVIDUAL_ORDER_EARNING')->first();
        return view('merchant.report.driver-earning', compact('drivers', 'search_view', 'arr_search', 'info_setting'));
    }

    // driver earning details
    public function driverRideEarning(Request $request)
    {
        $id = $request->driver_id;
        $driver = Driver::select('id', 'first_name', 'last_name', 'phoneNumber', 'merchant_id')->find($id);
        $checkPermission = check_permission(1, 'view_reports_charts');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $request->request->add(['search_route' => route('merchant.driver-taxi-services-report', ['driver_id' => $id]), 'request_from' => "COMPLETE"]);
        $arr_rides = $this->getBookings($request, $pagination = true, 'MERCHANT');
        $query = BookingTransaction::select(DB::raw('SUM(customer_paid_amount) as ride_amount'), DB::raw('SUM(company_earning) as merchant_earning'), DB::raw('SUM(driver_earning) as driver_earning'), DB::raw('SUM(business_segment_earning) as store_earning'))
            ->with(['Booking' => function ($q) use ($request, $merchant_id) {
                $q->where([['booking_status', '=', 1005], ['merchant_id', '=', $merchant_id]]);

                if (!empty($request->booking_id) && $request->booking_id) {
                    $q->where('merchant_booking_id', $request->booking_id);
                }
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
                if (!empty($request->driver_id)) {
                    $q->where('driver_id', $request->driver_id);
                }
                if ($request->start) {
                    $start_date = date('Y-m-d', strtotime($request->start));
                    $end_date = date('Y-m-d ', strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
                }
            }])
            ->whereHas('Booking', function ($q) use ($request, $merchant_id) {
                $q->where([['booking_status', '=', 1005], ['merchant_id', '=', $merchant_id]]);
                if (!empty($request->booking_id) && $request->booking_id) {
                    $q->where('merchant_booking_id', $request->booking_id);
                }
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
                if (!empty($request->driver_id)) {
                    $q->where('driver_id', $request->driver_id);
                }
                if ($request->start) {
                    $start_date = date('Y-m-d', strtotime($request->start));
                    $end_date = date('Y-m-d ', strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
                }
            });
        $earning_summary = $query->first();
        $arr_segment = get_merchant_segment(true, $merchant_id, 1, 1);
        $arr_segment = count($arr_segment) > 1 ? $arr_segment : [];
        $request->request->add(['request_from' => "ride_earning", "arr_segment" => $arr_segment]);
        $ride_obj = new BookingController;
        $search_view = $ride_obj->orderSearchView($request);
        $arr_search = $request->all();
        $total_rides = $arr_rides->count();
        $currency = "";
        return view('merchant.report.taxi-services.driver', compact('arr_rides', 'search_view', 'arr_search', 'earning_summary', 'total_rides', 'currency', 'driver'));

    }

    public function driverOrderEarning(Request $request)
    {
        $data = [];
        $driver_id = $request->driver_id;
        $driver = Driver::select('id', 'first_name', 'last_name', 'phoneNumber', 'merchant_id')->find($driver_id);
        $order = new Order;
        $merchant_id = get_merchant_id();
        $data['business_summary'] = [];
        $segment_id = $request->segment_id;
        $request->request->add(['status' => 'COMPLETED']);
        $all_orders = $order->getOrders($request, true);
        $request->request->add(['merchant_id' => $merchant_id, 'segment_id' => $segment_id, 'driver_id' => $driver_id]);
        $query = BookingTransaction::select(DB::raw('SUM(customer_paid_amount) as order_amount'), DB::raw('SUM(company_earning) as merchant_earning'), DB::raw('SUM(driver_earning) as driver_earning'), DB::raw('SUM(business_segment_earning) as store_earning'))
            ->with(['Order' => function ($q) use ($request, $merchant_id) {
                $q->where([['order_status', '=', 11], ['merchant_id', '=', $merchant_id]]);

                if (!empty($request->booking_id) && $request->booking_id) {
                    $q->where('merchant_booking_id', $request->booking_id);
                }
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
                if (!empty($request->driver_id)) {
                    $q->where('driver_id', $request->driver_id);
                }
                if ($request->start) {
                    $start_date = date('Y-m-d', strtotime($request->start));
                    $end_date = date('Y-m-d ', strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
                }
            }])
            ->whereHas('Order', function ($q) use ($request, $merchant_id) {
                $q->where([['order_status', '=', 11], ['merchant_id', '=', $merchant_id]]);
                if (!empty($request->booking_id) && $request->booking_id) {
                    $q->where('merchant_booking_id', $request->booking_id);
                }
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
                if (!empty($request->driver_id)) {
                    $q->where('driver_id', $request->driver_id);
                }
                if ($request->start) {
                    $start_date = date('Y-m-d', strtotime($request->start));
                    $end_date = date('Y-m-d ', strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
                }
            });
        $business_income = $query->first();
        $data['business_summary'] = [
            'orders' => $all_orders->total(),
            'income' => $business_income,
        ];
        $currency = "";
        $data['currency'] = $currency;
        $data['arr_orders'] = $all_orders;
        $req_param['merchant_id'] = $merchant_id;
        $data['title'] = "";
        $data['merchant_name'] = "";
        $request->request->add(['search_route' => route('merchant.driver-delivery-services-report', ['driver_id' => $driver_id])]);
        $order_con = new OrderController;
        $arr_segment = get_merchant_segment(false, $merchant_id, 1, 2);
        $arr_segment = count($arr_segment) > 1 ? $arr_segment : [];
        $request->request->add(['calling_view' => "earning", "arr_segment" => $arr_segment]);
        $data['search_view'] = $order_con->orderSearchView($request);
        $data['arr_search'] = $request->all();
        $data['driver'] = $driver;
        return view('merchant.report.delivery-services.driver')->with($data);
    }

    // Taxi based services Earning
    public function driverHandymanServicesEarning(Request $request)
    {
        $checkPermission = check_permission(1, 'view_reports_charts');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $driver_id = $request->driver_id;
        $driver = Driver::select('id', 'first_name', 'last_name', 'phoneNumber', 'merchant_id')->find($driver_id);
        $merchant_id = get_merchant_id();
        $handyman = new HandymanOrder;
        $arr_bookings = $handyman->getSegmentOrders($request);
//        $arr_booking_id = array_pluck($arr_bookings,'id');
        $query = BookingTransaction::select(DB::raw('SUM(customer_paid_amount) as booking_amount'), DB::raw('SUM(company_earning) as merchant_earning'), DB::raw('SUM(driver_earning) as driver_earning'), DB::raw('SUM(business_segment_earning) as store_earning'))
            ->with(['HandymanOrder' => function ($q) use ($request, $merchant_id) {
                $q->where([['order_status', '=', 7], ['merchant_id', '=', $merchant_id]]);
                if (!empty($request->order_id) && $request->order_id) {
                    $q->where('merchant_order_id', $request->order_id);
                }
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
                if (!empty($request->driver_id)) {
                    $q->where('driver_id', $request->driver_id);
                }
                if ($request->start) {
                    $start_date = date('Y-m-d', strtotime($request->start));
                    $end_date = date('Y-m-d ', strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
                }
            }])
            ->whereHas('HandymanOrder', function ($q) use ($request, $merchant_id) {
                $q->where([['order_status', '=', 7], ['merchant_id', '=', $merchant_id]]);
                if (!empty($request->booking_id) && $request->booking_id) {
                    $q->where('merchant_booking_id', $request->booking_id);
                }
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
                if (!empty($request->driver_id)) {
                    $q->where('driver_id', $request->driver_id);
                }
                if ($request->start) {
                    $start_date = date('Y-m-d', strtotime($request->start));
                    $end_date = date('Y-m-d ', strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
                }
            });
        $earning_summary = $query->first();
        $arr_segment = get_merchant_segment(true, $merchant_id, 2);
        $request->request->add(['request_from' => "booking_earning", "arr_segment" => $arr_segment]);
        $arr_search = $request->all();
        $total_bookings = $arr_bookings->total();
        $currency = "";
        $request->request->add(['search_route' => route("merchant.driver-handyman-services-report", ["driver_id" => $driver_id])]);
        $handyman_obj = new HandymanOrderController;
        $search_view = $handyman_obj->bookingSearchView($request);
        return view('merchant.report.handyman-services.driver', compact('arr_bookings', 'arr_search', 'earning_summary', 'total_bookings', 'currency', 'search_view', 'driver'));
    }

    public function getLatLongFromNode(Request $request)
    {
        $data = ['ats_id' => $request->ats_id, 'developer_id' => "5e4e21de577d8b1accd4f76f"];
        $payload = json_encode($data);
        $ch = curl_init('http://68.183.85.170:3027/api/v1/ats/atsIdLocation');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

// Set HTTP Header for POST request
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload))
        );

        $return = "";
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        } else {
            $return_data = json_decode($result, true);
            if ($return_data['result'] == 1) {
                $coordinate = $return_data['response']['coordinates'];
                $lat_long = $coordinate['lat'] . ',' . $coordinate['lng'];
                $date = $return_data['response']['updatedAt'];
                $date_at = new DateTime($date);
                $date_at->setTimezone(new DateTimeZone($request->driver_timezone));
                $updated_at = $date_at->format(" g:i a, d M,Y ");
                $return = '<a class="map_address hyperLink " target="_blank" href="https://www.google.com/maps/place/' . $lat_long . '">' . $updated_at . '<br>' . $lat_long . '</a>';
            }
        }
        curl_close($ch);
        echo $return;
    }


    public function getDriverAgencyDrivers(Request $request)
    {
        $checkPermission = check_permission(1, 'driver_agency');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $request->request->add(['merchant_id' => $merchant_id,'request_from'=>"merchant_driver_agency"]);
        $drivers = $this->getAllDriver(true, $request);
        $config = $merchant;
        $config->driver_wallet_status = $config->Configuration->driver_wallet_status;
        $config->subscription_package = $config->Configuration->subscription_package;
        $config->gender = $config->ApplicationConfiguration->gender;
        $config->smoker = $config->ApplicationConfiguration->smoker;
        $config->driver_commission_choice = $config->ApplicationConfiguration->driver_commission_choice;
        $config->stripe_connect_enable = $config->Configuration->stripe_connect_enable;
        $arr_search = $request->all();
        $request->request->add(['search_route' => route('merchant.driver-agency.drivers')]);
        $search_view = $this->driverSearchView($request);
        $socket_enable = $merchant->Configuration->lat_long_storing_at == 2 ? true : false;
        $info_setting = InfoSetting::where('slug', 'DRIVER')->first();
        return view('merchant.driver.driver-agency-drivers', compact(  'drivers',  'config', 'search_view', 'arr_search', 'socket_enable', 'info_setting'));
    }
}