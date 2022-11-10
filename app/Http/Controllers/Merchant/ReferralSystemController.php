<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Country;
use App\Models\Driver;
use App\Models\InfoSetting;
use App\Models\ReferralDiscount;
use App\Models\ReferralDriverDiscount;
use App\Models\ReferralSystem;
use App\Models\ReferralUserDiscount;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\MerchantTrait;

class ReferralSystemController extends Controller
{
    use MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'REFERRAL_SYSTEM')->first();
        view()->share('info_setting', $info_setting);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checkPermission = check_permission(1, 'view_refer');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $this->checkExpireReferralSystem($merchant_id);
        $referral_systems = ReferralSystem::where([['merchant_id', '=', $merchant_id],['status', '!=', 4]])->orderBy('id','DESC')->paginate(10);
        return view('merchant.referral_system.index', compact('referral_systems'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = NULL)
    {
        $checkPermission = check_permission(1, 'create_refer');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get()->pluck("CountryName", "id")->toArray();
        $referral_system = [];
        $referral_system_segments = [];
        if (!empty($id)) {
            $referral_system = ReferralSystem::where("merchant_id", $merchant_id)->findOrFail($id);
            $referral_system_segments = $referral_system->Segment;
            $referral_system_segments = $referral_system_segments->map(function ($item) {
                $name = !empty($item->Name()) ? $item->Name() : $item->slag;
                return $name;
            });
        }
        if (!empty($id)) {
            return view('merchant.referral_system.edit', compact('referral_system', 'referral_system_segments'));
        } else {
            return view('merchant.referral_system.create', compact('countries', 'referral_system', 'referral_system_segments'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = NULL)
    {
        if (!empty($id)) {
            $validation = [
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ];
        } else {
            $validation = [
                'country_id' => 'required|integer|exists:countries,id',
                'country_area_id' => 'required|integer|exists:country_areas,id',
                'segment_id.*' => "required|string|distinct|min:1",
                'application' => 'required|integer',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'offer_applicable' => 'required|integer',
                'offer_type' => 'required',
                'offer_value' => 'required_if:offer_type,[1,2]',
                'offer_condition' => 'required|integer',
            ];
        }
        $validator = Validator::make($request->toArray(), $validation);
        if ($validator->fails()) {
            $msg = $validator->messages()->all();
            return redirect()->back()->with('error', $msg[0]);
        }
        DB::beginTransaction();
        try {
            $merchant = get_merchant_id(false);
            $merchant_id = $merchant->id;
            $string_file = $this->getStringFile(NULL, $merchant);
            if (!empty($id)) {
                $referral = ReferralSystem::where("merchant_id", $merchant_id)->find($id);
                $referral->end_date = $request->end_date;
                $referral->save();
            } else {
                $referral = ReferralSystem::where([
                    ["merchant_id", "=", $merchant_id],
                    ["country_id", "=", $request->country_id],
                    ["country_area_id", "=", $request->country_area_id],
                    ["application", "=", $request->application],
                ])->whereIn("status", [1, 2])->first();
                if (empty($referral)) {
                    $offer_condition_data = [];
                    switch ($request->offer_condition) {
                        case 1:
                            $offer_condition_data['day_limit'] = $request->day_limit;
                            $offer_condition_data['day_count'] = $request->day_count;
                            $offer_condition_data['limit_usage'] = $request->limit_usage;
                            break;
                        case 4:
                            $offer_condition_data['conditional_no_driver'] = $request->conditional_no_driver;
                            $offer_condition_data['conditional_no_services'] = $request->conditional_no_services;
                            $offer_condition_data['conditional_driver_rule'] = $request->conditional_driver_rule;
                            break;
                    }
                    $referral = new ReferralSystem();
                    $referral->merchant_id = $merchant_id;
                    $referral->country_id = $request->country_id;
                    $referral->country_area_id = $request->country_area_id;
                    $referral->application = $request->application;
                    $referral->start_date = $request->start_date;
                    $referral->end_date = $request->end_date;
                    $referral->offer_applicable = $request->offer_applicable;
                    $referral->offer_type = $request->offer_type;
                    $referral->offer_value = $request->offer_value;
                    $referral->maximum_offer_amount = ($request->offer_type == 2) ? $request->maximum_offer_amount : null;
                    $referral->offer_condition = $request->offer_condition;
                    $referral->offer_condition_data = json_encode($offer_condition_data);
                    $referral->save();
                    $referral->segment()->sync($request->segment_id);
                } else {
                    return redirect()->back()->withInput()->withErrors(trans("$string_file.referral_system_already_exist_for_this"));
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
        DB::commit();
        return redirect()->route('referral-system')->with("success", trans("$string_file.saved_successfully"));
    }

    public function deleteReferral(Request $request)
    {
        $request->validate([
            'referral_system_id' => 'required'
        ]);
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $referral = ReferralSystem::where([['merchant_id', '=', $merchant_id]])->findOrFail($request->referral_system_id);
        $referral->status = 4;
        $referral->save();
        return redirect()->back()->with('success', trans("$string_file.deleted_successfully"));
    }

    public function ChangeStatus($id, $status)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
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
        $refer = ReferralSystem::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $refer->status = $status;
        $refer->save();
        return redirect()->back()->with('success', trans("$string_file.status") . " " . trans("$string_file.changed") . " " . trans("$string_file.successfully"));
    }

    public function checkExpireReferralSystem($merchant_id = null)
    {
        $exipre_ids = ReferralSystem::where(function ($query) use ($merchant_id) {
            if ($merchant_id != null) {
                $query->where("merchant_id", $merchant_id);
            }
        })->whereIn("status", [1, 2])->whereDate('end_date', '<', date("Y-m-d"))->get()->pluck("id")->toArray();
        if (!empty($exipre_ids)) {
            ReferralSystem::whereIn('id', $exipre_ids)->update(array('status' => 3));
        }
    }

    public function checkReferralSystem(Request $request)
    {
        $validator = Validator::make($request->toArray(), [
            'country_id' => 'required|integer|exists:countries,id',
            'country_area_id' => 'required|integer|exists:country_areas,id',
            'application' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $msg = $validator->messages()->all();
            return array("status" => "error", "message" => $msg[0]);
        }
        try {
            $merchant_id = get_merchant_id();
            $country = Country::find($request->country_id);
            $referral_system_count = ReferralSystem::where([
                ["merchant_id", "=", $merchant_id],
                ["country_id", "=", $request->country_id],
                ["country_area_id", "=", $request->country_area_id],
                ["application", "=", $request->application],
            ])->whereIn("status", [1, 2])->get()->count();  // Select only active or inactive
            $data = array("status" => "success", "message" => "Not exist", "currency" => $country->isoCode);
            if ($referral_system_count > 0) {
                $data = array("status" => "error", "message" => "Exist");
            }
        } catch (\Exception $e) {
            $data = array("status" => "error", "message" => $e->getMessage());
        }
        return $data;
    }

    public function referralReport(Request $request)
    {
        try {
            $merchant_id = get_merchant_id();
            $where_date = function ($q) use ($request) {
                if ($request->start) {
                    $start_date = date('Y-m-d', strtotime($request->start));
                    $end_date = date('Y-m-d ', strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
                }
            };
            $referral_details = ReferralDiscount::where($where_date)->where([['merchant_id', '=', $merchant_id]])->groupBy('sender_type')->groupBy('sender_id')->latest()->paginate(15);
            foreach ($referral_details as $key => $referral_detail) {
                $senderDetails = $referral_detail->sender_type == "USER" ? User::find($referral_detail->sender_id) : Driver::find($referral_detail->sender_id);
                if (!empty($senderDetails)) {
                    $phone = $referral_detail->sender_type == "USER" ? $senderDetails->UserPhone : $senderDetails->phoneNumber;
                    $senderType = $referral_detail->sender_type == "USER" ? 'User' : 'Driver';
                    $referral_detail->sender_details = array(
                        'id' => $senderDetails->id,
                        'name' => $senderDetails->first_name . ' ' . $senderDetails->last_name,
                        'phone' => $phone,
                        'email' => $senderDetails->email,
                        'type' => $senderType);
                    $referReceivers = ReferralDiscount::where([['merchant_id', '=', $merchant_id], ['sender_id', '=', $referral_detail->sender_id], ['sender_type', '=', $referral_detail->sender_type]])->latest()->get();
                    $receiverBasic = array();
                    foreach ($referReceivers as $referReceiver) {
                        $receiverDetails = $referReceiver->receiver_type == "USER" ? User::find($referReceiver->receiver_id) : Driver::find($referReceiver->receiver_id);
                        if (!empty($receiverDetails)) {
                            $phone = $referReceiver->receiver_type == "USER" ? $receiverDetails->UserPhone : $receiverDetails->phoneNumber;
                            $receiverType = $referReceiver->receiver_type == "USER" ? 'User' : 'Driver';
                            $receiverBasic[] = array(
                                'id' => $receiverDetails->id,
                                'name' => $receiverDetails->first_name . ' ' . $receiverDetails->last_name,
                                'phone' => $phone,
                                'email' => $receiverDetails->email,
                                'date' => $referReceiver->created_at,
                                'type' => $receiverType
                            );
                        }
                    }
                    $referral_detail->receiver_details = $receiverBasic;
                }
            }
            // p($referral_details);
            $arr_search = $request->all();
            $states_data['user_referral'] = ReferralDiscount::where($where_date)->where([["sender_type", "=", "USER"], ["merchant_id", '=', $merchant_id]])->get()->count();
            $states_data['driver_referral'] = ReferralDiscount::where($where_date)->where([["sender_type", "=", "DRIVER"], ["merchant_id", '=', $merchant_id]])->get()->count();;
            $states_data['user_referral_amount'] = ReferralUserDiscount::where($where_date)->where([["merchant_id", '=', $merchant_id]])->get()->sum('amount');;
            $states_data['driver_referral_amount'] = ReferralDriverDiscount::where($where_date)->where([["payment_status", "=", 1], ["merchant_id", '=', $merchant_id]])->get()->sum('amount');;
            return view('merchant.report.referral', compact('referral_details', 'arr_search', 'states_data'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function getReferralReceiverDetails(Request $request)
    {
        $validator = Validator::make($request->toArray(), [
            'referral_discount_id' => 'required|integer|exists:referral_discounts,id',
        ]);
        if ($validator->fails()) {
            $msg = $validator->messages()->all();
            return array("status" => "error", "message" => $msg[0]);
        }
        try {
            $merchant_id = get_merchant_id();
            $receiverBasic = array();
            $name = "";
            $html_view = "";
            $string_file = $this->getStringFile($merchant_id);
            $referral_detail = ReferralDiscount::where([['merchant_id', '=', $merchant_id]])->find($request->referral_discount_id);
            if (!empty($referral_detail)) {
                $senderDetails = $referral_detail->sender_type == "USER" ? User::find($referral_detail->sender_id) : Driver::find($referral_detail->sender_id);
                if (!empty($senderDetails)) {
                    $name = $senderDetails->first_name . ' ' . $senderDetails->last_name;
                    $referReceivers = ReferralDiscount::where([['merchant_id', '=', $merchant_id], ['sender_id', '=', $referral_detail->sender_id], ['sender_type', '=', $referral_detail->sender_type]])->latest()->get();
                    foreach ($referReceivers as $referReceiver) {
                        $receiverDetails = $referReceiver->receiver_type == "USER" ? User::find($referReceiver->receiver_id) : Driver::find($referReceiver->receiver_id);
                        if (!empty($receiverDetails)) {
                            $phone = $referReceiver->receiver_type == "USER" ? $receiverDetails->UserPhone : $receiverDetails->phoneNumber;
                            $receiverType = $referReceiver->receiver_type == "USER" ? 'User' : 'Driver';
                            $receiverBasic[] = array(
                                'id' => $receiverDetails->id,
                                'name' => $receiverDetails->first_name . ' ' . $receiverDetails->last_name,
                                'phone' => $phone,
                                'email' => $receiverDetails->email,
                                'date' => $referReceiver->created_at,
                                'type' => $receiverType,
                                'referral_available' => $referReceiver->referral_available,
                                'offer_type' => $referReceiver->offer_type,
                                'offer_value' => $referReceiver->offer_value,
                                'currency' => $referReceiver->getReferralSystem->Country->isoCode,
                            );
                        }
                    }
                    $data['string_file'] = $string_file;
                    $data['receiverBasic'] = $receiverBasic;
                    $data['referral_detail'] = $referral_detail;
                    $html_view = \Illuminate\Support\Facades\View::make('merchant.report.referral-receiver-table')->with($data)->render();
                }else{
                    return array("status" => "error", "message" => trans("$string_file.data_not_found"));
                }
            } else {
                return array("status" => "error", "message" => trans("$string_file.data_not_found"));
            }
            return array("status" => "success", "message" => "", "data" => array("name" => $name, "view" => $html_view));
        } catch (\Exception $e) {
            return array("status" => "error", "message" => $e->getMessage());
        }
    }
}