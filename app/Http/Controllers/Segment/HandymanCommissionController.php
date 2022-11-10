<?php

namespace App\Http\Controllers\Segment;

use App\Http\Controllers\Helper\AjaxController;
use App\Models\HandymanCommission;
use App\Models\InfoSetting;
use App\Traits\AreaTrait;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HandymanCommissionController extends Controller
{
    use AreaTrait,MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','HANDYMAN_DRIVER_SERVICE_PRICE_CARD')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index(Request $request)
    {
        $checkPermission = check_permission(1, 'price_card_HANDYMAN');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $segment_list = get_merchant_segment(false,$merchant_id,2);
        $arr_commission = HandymanCommission::with('CountryArea','Segment')->where([['merchant_id', '=', $merchant_id]])
            ->where(function ($q) use ($request){
                if(!empty($request->country_area_id))
                {
                    $q->where('country_area_id',$request->country_area_id);
                }
                if(!empty($request->segment_id))
                {
                    $q->where('segment_id',$request->segment_id);
                }
            })
            ->whereHas('CountryArea',function($q) use($permission_area_ids){
                if(!empty($permission_area_ids)){
                    $q->whereIn("id",$permission_area_ids);
                }
            })
            ->paginate(25);
        $search_route =  route('merchant.segment.commission');
        $arr_search = $request->all();
        $country_area = $this->getMerchantCountryArea($merchant->CountryArea);
        return view('merchant.segment-commission.index', compact('arr_commission','segment_list','search_route','arr_search','country_area'));
    }

    public function add(Request $request, $id = null)
    {
        $checkPermission = check_permission(1, 'price_card_HANDYMAN');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $data = [];
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $commission = '';
        $segment_group_id = 2;
        $area_id = NULL;
        $is_demo = false;
        if(!empty($id))
        {
//            $commission = HandymanCommission::where([['status',true]])->findorfail($id);
            $commission = HandymanCommission::findorfail($id);
            $pre_title = trans("$string_file.edit");
            $submit_button = trans("$string_file.update");
            $area_id = $commission->country_area_id;
            $is_demo = $merchant->demo == 1 && $commission->country_area_id == 3 ? true : false;
        }
        else
        {
            $pre_title = trans("$string_file.add");
            $submit_button = trans("$string_file.save");
        }
        $title = $pre_title.' '.trans("$string_file.commission");
        $areas = $this->getMerchantCountryArea($this->getAreaList(false,false)->get());
        $ajax = new AjaxController;
        $request->request->add(['area_id'=>$area_id,'segment_group_id'=>$segment_group_id]);
        $arr_segment = $ajax->getCountryAreaSegment($request,'dropdown');
        $data = [
            'commission'=>$commission,
            'title'=>$title,
            'submit_button'=>$submit_button,
            'arr_areas'=>$areas,
            'arr_segment'=>$arr_segment,
            'arr_status'=>get_active_status("web",$string_file),
            'is_demo'=>$is_demo,
        ];
        return view('merchant.segment-commission.form',compact('merchant','data'));
    }
    public function save(Request $request, $id = NULL)
    {
        $checkPermission = check_permission(1, 'price_card_HANDYMAN');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $segment_id = $request->segment_id;
        $country_area_id = $request->country_area_id;
        $validator = Validator::make($request->all(), [
            'country_area_id' => 'required|integer',
            'segment_id' => 'required|unique:handyman_commissions,segment_id,'.$id.',id,merchant_id,'.$merchant_id.',country_area_id,'.$country_area_id,
            'commission_method' => 'required',
            'commission' => 'required',
            'status' => 'required',
            'tax' => 'nullable',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            if(!empty($id))
            {
                $commission = HandymanCommission::Find($id);
            }
            else{
                $commission = new HandymanCommission;
            }
            $commission->merchant_id = $merchant_id;
            $commission->segment_id = $segment_id;
            $commission->country_area_id = $country_area_id;
            $commission->commission_method = $request->commission_method;
            $commission->commission = $request->commission;
            $commission->tax = $request->tax;
            $commission->status = $request->status;
            $commission->save();

        } catch (\Exception $e) {
            $message = $e->getMessage();
//            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->route('merchant.segment.commission')->withSuccess(trans("$string_file.saved_successfully"));
    }
}
