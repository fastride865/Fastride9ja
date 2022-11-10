<?php

namespace App\Http\Controllers\Segment;

use App\Models\InfoSetting;
use Auth;
use App;
use App\Models\HandymanChargeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Traits\MerchantTrait;
use DB;

class HandymanChargeTypeController extends Controller
{
    use MerchantTrait;
    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'HANDYMAN_CHARGE_TYPE')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
        $all_segments = array_merge(['DELIVERY','TAXI','HANDYMAN'],$all_food_grocery_clone);
        $checkPermission = check_permission(1, $all_segments, true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $permission_segments = get_permission_segments(1,true);
        $handyman_charge_types = HandymanChargeType::whereHas('Segment',function($query) use($permission_segments){
            $query->whereIn('slag',$permission_segments);
        })->where([['merchant_id', '=', $merchant_id]])->paginate(15);
        $merchant_segments = get_permission_segments(1,false,get_merchant_segment());
        return view('merchant.handyman-charge-type.index', compact('handyman_charge_types','merchant_segments'));
    }

    public function add(Request $request, $id = null)
    {
        $all_grocery_clone = ['HANDYMAN'];
        $checkPermission = check_permission(1, $all_grocery_clone,true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $data = [];
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $segment_group_id = 2;
        $is_demo = false;
        $handyman_charge_type = NULL;
        if(!empty($id))
        {
            $handyman_charge_type = HandymanChargeType::findorfail($id);
            $submit_button = trans("$string_file.update");
        }
        else
        {
            $submit_button = trans("$string_file.save");
        }

        $arr_segment = get_merchant_segment(false,$merchant->id,$segment_group_id);
        $arr_segment = get_permission_segments(1,false,$arr_segment);
        $data = [
            'handyman_charge_type'=>$handyman_charge_type,
            'submit_button'=>$submit_button,
            'arr_segment'=>$arr_segment,
            'arr_status'=>get_active_status("web",$string_file),
        ];

        return view('merchant.handyman-charge-type.form',compact('merchant','data'));
    }

    public function save(Request $request, $id = NULL)
    {
        $all_grocery_clone = ['HANDYMAN'];
        $checkPermission = check_permission(1, $all_grocery_clone,true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $segment_id = $request->segment_id;
        $validator = Validator::make($request->all(), [
            'segment_id' => 'required',
            'status' => 'required',
            'charge_type' => 'required',
//            'maximum_amount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        //
        DB::beginTransaction();
        try {
            if(!empty($id))
            {
                $handyman_charge_type = HandymanChargeType::find($id);
            }
            else
            {
                $handyman_charge_type = new HandymanChargeType;
                $handyman_charge_type->merchant_id = $merchant_id;
            }

            $handyman_charge_type->segment_id = $segment_id;
            $handyman_charge_type->status = $request->status;
            $handyman_charge_type->maximum_amount = $request->maximum_amount;
            $handyman_charge_type->save();

            $this->SaveLanguageCancel($merchant_id, $handyman_charge_type->id, $request->charge_type,$request->charge_description);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->route('segment.handyman-charge-type')->withSuccess(trans("$string_file.added_successfully"));
    }


    public function SaveLanguageCancel($merchant_id, $handyman_charge_type_id, $charge_type,$charge_description)
    {
        App\Models\LanguageHandymanChargeType::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'handyman_charge_type_id' => $handyman_charge_type_id
        ], [
            'charge_type' => $charge_type,
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
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $HandymanChargeType = HandymanChargeType::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $HandymanChargeType->reason_status = $status;
        $HandymanChargeType->save();
        return redirect()->route('merchant.handyman-charge-type.index');
    }

//    public function edit($id)
//    {
//        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
//        $all_segments = array_merge(['DELIVERY','TAXI','HANDYMAN'],$all_food_grocery_clone);
//        $checkPermission = check_permission(1, $all_segments, true);
//        if ($checkPermission['isRedirect']) {
//            return $checkPermission['redirectBack'];
//        }
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $HandymanChargeType = HandymanChargeType::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
//        $merchant_segments = get_merchant_segment();
//        return view('merchant.handyman-charge-type.edit', compact('HandymanChargeType','merchant_segments'));
//    }
//
//    public function update(Request $request, $id)
//    {
//        $merchant = get_merchant_id(false);
//        $merchant_id = $merchant->id;
//        $string_file = $this->getStringFile(NULL,$merchant);
//        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
//        $all_segments = array_merge(['DELIVERY','TAXI','HANDYMAN'],$all_food_grocery_clone);
//        $checkPermission = check_permission(1, $all_segments, true);
//        if ($checkPermission['isRedirect']) {
//            return $checkPermission['redirectBack'];
//        }
//        $request->validate([
//            'reason' => 'required',
//        ]);
//        $handyman_charge_type = HandymanChargeType::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
//        $handyman_charge_type->segment_id = $request->segment_id;
//        $handyman_charge_type->save();
//        $this->SaveLanguageCancel($merchant_id, $handyman_charge_type->id, $request->reason);
//        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
//    }
//
//    public function Search(Request $request)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $query = HandymanChargeType::where([['merchant_id', '=', $merchant_id]]);
//        if ($request->reason) {
//            $keyword = $request->reason;
//            $query->WhereHas('LanguageSingle', function ($q) use ($keyword) {
//                $q->where('reason', 'LIKE', "%$keyword%");
//            });
//        }
//        if ($request->reason_for) {
//            $query->where('reason_type', $request->reason_for);
//        }
//        $handyman_charge_type = $query->paginate(25);
//        return view('merchant.handyman-charge-type.index', compact('handyman_charge_type'));
//    }
//    public function store(Request $request)
//    {
//        $merchant = get_merchant_id(false);
//        $merchant_id = $merchant->id;
//        $string_file = $this->getStringFile(NULL,$merchant);
//        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
//        $all_segments = array_merge(['DELIVERY','TAXI','HANDYMAN'],$all_food_grocery_clone);
//        $checkPermission = check_permission(1, $all_segments, true);
//        if ($checkPermission['isRedirect']) {
//            return $checkPermission['redirectBack'];
//        }
//        $request->validate([
//            'reason_for' => 'required|integer',
//            'reason' => 'required',
//        ]);
//        $cancel_reason = HandymanChargeType::create([
//            'merchant_id' => $merchant_id,
//            'segment_id' => $request->segment_id,
//            'reason_type' => $request->reason_for,
//            'reason_status' => 1,
//        ]);
//        $this->SaveLanguageCancel($merchant_id, $cancel_reason->id, $request->reason);
//        return redirect()->back()->withSuccess(trans("$string_file.added_successfully"));
//    }
}
