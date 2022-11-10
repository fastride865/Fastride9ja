<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use App\Models\LanguagePricingParameter;
use App\Models\PricingParameterValue;
use Auth;
use App;
use App\Models\PricingParameter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use DB;
use View;
use App\Traits\MerchantTrait;

class PricingParameterController extends Controller
{
    use MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','PRICING_PARAMETERS')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $checkPermission = check_permission(1, ['TAXI','DELIVERY'], true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $permission_segments = get_permission_segments(1,true);
        $parameters = PricingParameter::with(['Segment' => function($query) use($permission_segments){
            $query->whereIn('slag',$permission_segments);
        }])->where([['merchant_id', '=', $merchant_id],['deleted_at', '=', NULL]])->paginate(25);
        return view('merchant.pricingparameter.index', compact('parameters'));
    }

    /**
     * Add Edit form of duration
     */
    public function add(Request $request, $id = null)
    {
        $checkPermission = check_permission(1, ['TAXI','DELIVERY'], true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $data = [];
        $merchant = get_merchant_id(false);
        $is_demo = $merchant->demo == 1 ? true : false;
        $string_file = $this->getStringFile($merchant->id);
        $arr_selected_segment = [];
        if(!empty($id))
        {
            $data = PricingParameter::where([['parameterStatus',true]])->findorfail($id);
            $arr_selected_segment = array_pluck($data->Segment,'id');
            $pre_title = trans("$string_file.edit");
            $submit_button = trans("$string_file.save");
        }
        else
        {
            $pre_title = trans("$string_file.add");
            $submit_button = trans("$string_file.update");
        }
        $title = $pre_title.' '. trans("$string_file.pricing_parameter");
        $segment_group_id = 1;
        $sub_group_for_admin = 1;
        $allSegment = get_merchant_segment(true,$merchant->id,$segment_group_id,$sub_group_for_admin);
        $allSegment = get_permission_segments(1, false, $allSegment);
        $segment_data['arr_segment'] = $allSegment;
        $segment_data['selected'] = $arr_selected_segment;
        $segment_html = View::make('segment')->with($segment_data)->render();
        $data = [
            'pricing_parameter'=>$data,
            'title'=>$title,
            'submit_button'=>$submit_button,
            'segment_html'=>$segment_html,
        ];
        return view('merchant.pricingparameter.form',compact('merchant','data','is_demo'));
    }
    /***
     * Save/update function of duration
     */
    public function save(Request $request, $id = NULL)
    {
        $checkPermission = check_permission(1, ['TAXI','DELIVERY'], true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $validator = Validator::make($request->all(), [
                'parametername' => ['required',
                    Rule::unique('language_pricing_parameters', 'parameterName')->where(function ($query) use ($merchant_id,$id) {
                        return $query->where([['deleted_at', '=', NULL], ['merchant_id', '=', $merchant_id], ['locale', '=', \Config::get('app.locale')],['pricing_parameter_id','!=',$id]]);
                    })],
                'parameter_display_name' => ['required',
                    Rule::unique('language_pricing_parameters', 'parameterNameApplication')->where(function ($query) use ($merchant_id,$id) {
                        return $query->where([['deleted_at', '=', NULL], ['merchant_id', '=', $merchant_id], ['locale', '=', \Config::get('app.locale')],['pricing_parameter_id','!=',$id]]);
                    })],
                'parameterType' => 'required_without:id',
                'sequence_number' => 'required|integer',
                'price_type' => 'required_without:id',
                'segment' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            if(!empty($id))
            {
                $pricing_parameter = PricingParameter::Find($id);
            }
            else{
                $base_fare_type_pricing_parameter = PricingParameter::where("parameterType",10)->where('merchant_id',$merchant_id)->first();
                if($request->parameterType == 10 && !empty($base_fare_type_pricing_parameter)){
                    throw new \Exception(trans("$string_file.cant_create_two_base_fare_pricing_parameter"));
                }
                $pricing_parameter = new PricingParameter;
                $pricing_parameter->parameterType = $request->parameterType;
            }
            $pricing_parameter->merchant_id = get_merchant_id();
            $pricing_parameter->sequence_number = $request->sequence_number;
            $pricing_parameter->applicable = $request->tax_type;
            $pricing_parameter->save();
            $pricing_parameter->Segment()->sync($request->segment);
            $this->SaveLanguagePara($merchant_id, $pricing_parameter->id, $request->parametername, $request->parameter_display_name);
            if(!empty($request->price_type) && empty($id))
            {
                foreach ($request->price_type as $value)
                {
                    PricingParameterValue::create(['price_type' => $value, 'pricing_parameter_id' => $pricing_parameter->id]);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput($request->input())->withErrors($e->getMessage());

        }
        DB::commit();
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function SaveLanguagePara($merchant_id, $pricing_parameter_id, $name,$parameterNameApplication)
    {
        LanguagePricingParameter::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'pricing_parameter_id' => $pricing_parameter_id
        ], ['parameterName' => $name, 'parameterNameApplication' => $parameterNameApplication,
        ]);
    }
}
