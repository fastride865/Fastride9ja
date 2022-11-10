<?php

namespace App\Http\Controllers\Segment;

use App\Http\Controllers\Helper\AjaxController;
use App\Models\CountryArea;
use App\Models\InfoSetting;
use App\Models\SegmentPriceCardDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SegmentPriceCard;
use App\Traits\AreaTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;
use App\Traits\MerchantTrait;
use View;

class SegmentPriceCardController extends Controller
{
    use AreaTrait,MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','HANDYMAN_USER_SERVICE_PRICE_CARD')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index(Request $request)
    {
        $checkPermission = check_permission(1, 'price_card_HANDYMAN');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $price_card_owner_config = $merchant->HandymanConfiguration->price_card_owner_config;
        $price_type_config = $merchant->HandymanConfiguration->price_type_config;
        $segment_list = get_merchant_segment(false,$merchant_id,2);
        $arr_price_card = SegmentPriceCard::with('ServiceType','CountryArea','Segment')
             ->with(['SegmentPriceCardDetail'=>function($s) use ($merchant_id)
             {
             $s->whereHas('ServiceType',function($q) use($merchant_id){
               $q->whereHas('Merchant',function($qqq) use($merchant_id){
                    $qqq->where('merchant_id',$merchant_id);
                    $qqq->orderBy('sequence','ASC');
                });
               });
             }])
            ->join('merchant_segment','segment_price_cards.segment_id','=','merchant_segment.segment_id')
            ->where([['segment_price_cards.merchant_id', '=', $merchant_id],['merchant_segment.merchant_id', '=', $merchant_id],['delete', '=', NULL]])
            ->where(function ($q) use ($request){
                if(!empty($request->country_area_id))
                {
                    $q->where('country_area_id',$request->country_area_id);
                }
                if(!empty($request->segment_id))
                {
                    $q->where('segment_price_cards.segment_id',$request->segment_id);
                }
            })
            ->whereHas('CountryArea',function($q) use($permission_area_ids){
                if(!empty($permission_area_ids)){
                    $q->whereIn("id",$permission_area_ids);
                }
            })
            ->paginate(25);
        $arr_price_type = add_blank_option(get_price_card_type('',$price_type_config,$string_file),'');
        $search_route =  route('merchant.segment.price_card');
        $arr_search = $request->all();
        $country_area = $this->getMerchantCountryArea($merchant->CountryArea);
        return view('merchant.segment-pricecard.index', compact('arr_price_card','arr_price_type','segment_list','search_route','arr_search','country_area','price_card_owner_config','price_type_config'));
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
        $price_card = '';
        $arr_services = "";
        $segment_group_id = 2;
        $area_id = NULL;
        $is_demo = false;
        if(!empty($id))
        {
//            $price_card = SegmentPriceCard::where([['status',true]])->findorfail($id);
            $price_card = SegmentPriceCard::findorfail($id);
            $pre_title = trans("$string_file.edit");
            $submit_button = trans("$string_file.update");
            $is_demo =  ($merchant->demo == 1 && $price_card->country_area_id == 3) ? true : false;
            if($price_card->price_type == 1)
            {
             $request->request->add(['area_id'=>$price_card->country_area_id,'segment_id'=>$price_card->segment_id,'calling_from'=>"controller","segment_price_card_id"=>$price_card->id,'merchant_id'=>$merchant->id]);
             $arr_services = $this->getSegmentPriceCardServices($request);
             if(empty($arr_services)){
                 $string_file = $this->getStringFile($merchant->id);
                 $service_not_found = trans("$string_file.either_service_or_segment_removed_from_service_area_please_check");
                 return redirect()->back()->withErrors($service_not_found);
             }
            }
            $area_id = $price_card->country_area_id;
        }
        else
        {
            $pre_title = trans("$string_file.add");
            $submit_button = trans("$string_file.save");
        }
        $title = $pre_title.' '.trans("$string_file.price_card");
        $areas = $this->getMerchantCountryArea($this->getAreaList(false,false)->get());
        $ajax = new AjaxController;
        $request->request->add(['area_id'=>$area_id,'segment_group_id'=>$segment_group_id]);
        $arr_segment = $ajax->getCountryAreaSegment($request,'dropdown');
        $price_type_config = $merchant->HandymanConfiguration->price_type_config;
        $data = [
            'price_card'=>$price_card,
            'title'=>$title,
            'submit_button'=>$submit_button,
            'arr_areas'=>$areas,
            'arr_segment'=>$arr_segment,
            'arr_services'=>$arr_services,
            'arr_status'=>get_active_status("web",$string_file),
            'arr_type'=>add_blank_option(get_price_card_type('',$price_type_config,$string_file),trans("$string_file.select")),
        ];
//        p($data);
        return view('merchant.segment-pricecard.form',compact('merchant','data','is_demo'));
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
        $price_type = $request->price_type;
        $country_area_id = $request->country_area_id;

        $validator = Validator::make($request->all(), [
            'segment_id' => 'required|unique:segment_price_cards,segment_id,'.$id.',id,merchant_id,'.$merchant_id.',price_type,'.$price_type.',country_area_id,'.$country_area_id,
//            'service_type_id' => 'required|unique:segment_price_cards,service_type_id,'.$id.',id,merchant_id,'.$merchant_id.',segment_id,'.$segment_id.',country_area_id,'.$country_area_id,
            'country_area_id' => 'required|integer',
            'status' => 'required',
            'price_type' => 'required',
            'minimum_booking_amount' => 'required',
            'fixed_amount' => 'required_if:price_type,1',
            'hourly_amount' => 'required_if:price_type,2',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            if(!empty($id))
            {
                $price_card = SegmentPriceCard::Find($id);
            }
            else{
                $price_card = new SegmentPriceCard;
            }
            $price_card->merchant_id = $merchant_id;
            $price_card->segment_id = $segment_id;
            $price_card->country_area_id = $country_area_id;
//            $price_card->service_type_id = $request->price_type == 1 ? $request->service_type_id : NULL;
            $price_card->minimum_booking_amount = $request->minimum_booking_amount;
            $price_card->status = $request->status;
            $price_card->price_type = $request->price_type;
            $price_card->amount = $request->price_type == 2 ? $request->hourly_amount : NULL;
//            p($price_card);
            $price_card->save();

            if($price_type == 1)
            {
                $arr_fixed_amount = $request->fixed_amount;
                $arr_detail = $request->detail_id;
                foreach ($arr_fixed_amount as $service_type_id => $fixed_amount)
                {
                    if(!empty($id))
                    {
                        $detail_id = isset($arr_detail[$service_type_id]) ? $arr_detail[$service_type_id] : NULL;
                        $price_card_detail = SegmentPriceCardDetail::Find($detail_id);
                        if(empty($price_card_detail)){
                            $price_card_detail = new SegmentPriceCardDetail;
                        }
                    }
                    else
                    {
                        $price_card_detail = new SegmentPriceCardDetail;
                    }
                    $price_card_detail->segment_price_card_id  = $price_card->id;
                    $price_card_detail->service_type_id  = $service_type_id;
                    $price_card_detail->amount  = $fixed_amount;
                    $price_card_detail->save();
                }
            }

        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
//        p($request->all());
        return redirect()->route('merchant.segment.price_card')->withSuccess(trans("$string_file.saved_successfully"));
    }
   public function getSegmentPriceCardServices(Request  $request)
   {
       $area_id = $request->area_id;
       $merchant_id = !empty($request->merchant_id) ? $request->merchant_id : get_merchant_id();
       $segment_id = $request->segment_id;
       $calling_from = $request->calling_from;
       $segment_price_card_id = $request->segment_price_card_id;
       $areas = CountryArea::select('id','merchant_id')
           ->with(['ServiceTypes'=>function($q) use($area_id,$segment_id,$segment_price_card_id,$merchant_id){
               $q->whereHas('Merchant',function($qqq) use($merchant_id){
                    $qqq->where('merchant_id',$merchant_id);
                    $qqq->orderBy('sequence','ASC');
                });
              $q->where('country_area_service_type.segment_id',$segment_id);
               $q->with(['SegmentPriceCardDetail'=>function($q) use($area_id,$segment_price_card_id){
                   $q->where('segment_price_card_id',$segment_price_card_id);
               }]);
            }])
           ->whereHas('ServiceTypes',function($q) use($area_id,$segment_id,$segment_price_card_id,$merchant_id){
               $q->whereHas('Merchant',function($qqq) use($merchant_id){
                    $qqq->where('merchant_id',$merchant_id);
                    $qqq->orderBy('sequence','ASC');
                });
               $q->where('country_area_service_type.segment_id',$segment_id);
               if(!empty($segment_price_card_id))
               {
               $q->whereHas('SegmentPriceCardDetail',function($q) use($area_id,$segment_price_card_id){
                   $q->where('segment_price_card_id',$segment_price_card_id);
               });
               }
           })
           ->where([['id', '=', $area_id]])->first();
       if(!empty($areas)){
           if(isset($areas->ServiceTypes) && !empty($areas->ServiceTypes)){
               $arr_data['arr_services'] = $areas->ServiceTypes;
           }else{
               $arr_data['arr_services'] = [];
           }
           $arr_data['merchant_id'] = $areas->merchant_id;
           $service_view = View::make('merchant.segment-pricecard.services-amount')->with($arr_data)->render();
           if($calling_from == "controller")
           {
               return $service_view;
           }
           // calling from ajax
           echo $service_view;
       }else{
           echo "";
       }
   }
}
