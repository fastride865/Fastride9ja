<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Helper\PolygenController;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\BookingTransaction;
use App\Models\BusinessSegment\BusinessSegmentCashout;
use App\Models\BusinessSegment\Order;
use App\Models\BusinessSegment\Product;
use App\Models\CountryArea;
use App\Models\DriverAgency\DriverAgency;
use App\Models\InfoSetting;
use App\Models\Merchant;
use App\Models\BusinessSegment\BusinessSegment;
use App\Models\Country;
use App\Models\Segment;
use App\Traits\AreaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;
use DB;
use App\Traits\ImageTrait;
//use App\Traits\MerchantTrait;
use App\Traits\OrderTrait;
use App\Models\BusinessSegment\BusinessSegmentConfigurations;
use App\Models\BusinessSegment\BusinessSegmentOnesignal;
use App\Models\Onesignal;
use View;

class  BusinessSegmentController extends Controller
{
    use ImageTrait,OrderTrait, AreaTrait;

    public function searchView($request,$arr_list = [])
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $data['arr_search'] = $request->all();
        $data['arr_area'] = $this->getMerchantCountryArea($arr_list,0,0,$string_file);
        $search = View::make('merchant.business-segment.search')->with($data)->render();
        return $search;
    }
    public function index(Request $request,$slug)
    {
        $checkPermission = check_permission(1, 'view_business_segment_'.$slug);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile($merchant_id);
        $title = "";
        if($slug == 'FOOD')
        {
            $title = trans($string_file.'.restaurants');
        }
        elseif($slug == 'GROCERY')
        {
            $title = trans($string_file.'.stores');
        }
        else
        {
            $title = trans($string_file.'.stores');
        }
        $title = $title.' '.trans("$string_file.list");

        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }

        $business_segment['data'] = BusinessSegment::whereHas('Segment',function($q) use($slug){
            $q->where('slag',$slug);
        })
            ->with('Merchant')
            ->where([['merchant_id', '=', $merchant_id]])
            ->orderBy('created_at','DESC')
            ->where(function($q) use ($request, $permission_area_ids) {
             if(!empty($request->country_area_id))
             {
                 $q->where('country_area_id',$request->country_area_id);
             }
            if(!empty($request->full_name))
            {
                $q->where('full_name','LIKE','%'.$request->full_name.'%');
            }
            if(!empty($request->email))
            {
                $q->where('email',$request->email);
            }
            if(!empty($request->phone_number))
            {
                $q->where('phone_number',$request->phone_number);
            }
            if(!empty($permission_area_ids)){
                $q->whereIn("country_area_id",$permission_area_ids);
            }
            })
            ->paginate(25);
        $business_segment['slug'] = $slug;
        $business_segment['title'] = $title;
        $business_segment['arr_search'] = $request->all();
        $request->request->add(['search_route'=>route('merchant.business-segment',$slug),'url_slug'=>$slug]);
        $business_segment['search_view'] = $this->searchView($request,$merchant->CountryArea);
        $info_setting = InfoSetting::where('slug','BUSINESS_SEGMENT')->first();
        $business_segment['info_setting'] = $info_setting;
        return view('merchant.business-segment.index')->with($business_segment);
    }

    public function add(Request $request,$slug, $id = NULL)
    {
        $merchant = get_merchant_id(false);
        $checkPermission = check_permission(1, 'create_business_segment_'.$slug);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        /*declaration part*/
        $business_segment = NULL;
        $merchant_id = $merchant->id;
        $is_demo = false;
        $string_file = $this->getStringFile($merchant_id);
        if($slug == 'FOOD')
        {
            $title = trans($string_file.'.restaurant');
        }
        elseif($slug == 'GROCERY')
        {
            $title = trans($string_file.'.store');
        }
        else
        {
            $title = trans($string_file.'.store');
        }

        $save_url = route('merchant.business-segment.save',['slug'=>$slug]);
        $prefix = trans("$string_file.add");
        $arr_agency_id = [];
        if(!empty($id))
        {
            $business_segment = BusinessSegment::Find($id);
            if(empty($business_segment->id))
            {
                return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
            }
            if($business_segment->delivery_service == 2)
            {
              $arr_agency_id = $business_segment->DriverAgency->pluck('id')->toArray();
            }
            $prefix = trans("$string_file.edit");
            $save_url = route('merchant.business-segment.save',['slug'=>$slug,'id'=>$id]);

//            !empty($id) && in_array($id,[6,11,1211,1212,1213])
            if($merchant->demo == 1 && $business_segment->country_area_id == 3)
            {
                $is_demo = true;
            }
        }
        $arr_segment = get_merchant_segment(false);
//        $arr_country = $this->getMerchantCountry();
        $arr_country = $merchant->Country;
        $arr_day = get_days($string_file);
        $info_setting = InfoSetting::where('slug','BUSINESS_SEGMENT')->first();
        $data['data']= [
            'arr_day'=>$arr_day,
            'slug'=>$slug,
            'countries'=>$arr_country,
            'save_url'=>$save_url,
            'title'=>$prefix.' '.$title,
            'business_segment'=>$business_segment,
            'segments'=>$arr_segment,
            'request_receiver'=>request_receiver($string_file),
            'arr_status'=>get_active_status("web",$string_file),
            'is_popular'=>get_status(true,$string_file),//\Config::get('custom.document_status'),
        ];
        $data['info_setting'] = $info_setting;
        $data['is_demo'] = $is_demo;
        $onesignal_config=BusinessSegmentOnesignal::where('business_segment_id',$id)->first();
        $data['onesignal_config']=$onesignal_config;
        $driver_agency_config = !empty($merchant->Configuration->driver_agency) ? $merchant->Configuration->driver_agency : 0;
        $data['driver_agency_config']=$driver_agency_config;
        
        $arr_agencies = [];
        if($driver_agency_config == 1)
        {
            $driver_agencies = DriverAgency::where('merchant_id',$merchant_id)->where('status',1)->get();
            foreach ($driver_agencies as $agency)
            {
                $arr_agencies[$agency->id] = $agency->name;
            }
        }
        $data['arr_agencies']=$arr_agencies;
        $data['arr_agency_id']=$arr_agency_id;

        return view('merchant.business-segment.form')->with($data);
    }

    /*Save or Update*/
    public function save(Request $request,$slug, $id = NULL)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $validator = Validator::make($request->all(), [
            'full_name' => 'required |unique:business_segments,full_name,'.$id.',id,merchant_id,'.$merchant_id,
            'email' =>'required|email|unique:business_segments,email,'.$id.',id,merchant_id,'.$merchant_id,
            'phone_number' =>'required|unique:business_segments,phone_number,'.$id.',id,merchant_id,'.$merchant_id,
            'password' => 'required_without:id',
            'business_logo' => 'required_without:id|mimes:jpeg,jpg,png',
            'login_background_image' => 'mimes:jpeg,png,jpg,gif,svg',
            'country_id' => 'required',
//            'segment_id' => 'required',
            'address' => 'required',
            'landmark' => 'required',
            'open_time' => 'required',
            'close_time' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'order_request_receiver' => 'required',
//            'commission_type' => 'required',
            'commission_method' => 'required',
            'commission' => 'required',
//            'delivery_service' => 'required',
//            'minimum_amount' => 'required_if:slug,==,FOOD',
            'delivery_time' => 'required_if:slug,==,FOOD',
//            'minimum_amount_for' => 'required_if:slug,==,FOOD',
            'rating' => 'required',
            'business_profile_image' => 'mimes:jpeg,png,jpg,gif,svg',
           // 'delivery_service' => 'required',
            'driver_agency_id' => 'required_if:delivery_service,==,2',
            //dimensions:width=800,height=230',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        $country_area_id = NULL;
        $arr_country_area = $merchant->CountryArea->where('country_id',$request->country_id)->where('status',1);
//        p($arr_country_area);
        foreach($arr_country_area as $country_area){
            $country_area_id = NULL;
            $ploygon = new PolygenController();
            $checkArea = $ploygon->CheckArea($request->latitude, $request->longitude, $country_area->AreaCoordinates);
            if(!empty($checkArea)){
                $country_area_id = $country_area->id;
                break;
            }
        }
        if (empty($country_area_id)) {
            $errors = trans("$string_file.no_service_area");
            return redirect()->back()->withErrors($errors);
        }
        // Begin Transaction
        DB::beginTransaction();
        try
        {
            $alias_name = str_slug($request->input('full_name'));
            if(!empty($id))
            {
                $business_segment = BusinessSegment::Find($id);
            }
            else
            {
                $segment = $this->getSegment($slug);
                if(empty($segment->id))
                {
                    $errors = [trans("$string_file.invalid_segment")];
                    return redirect()->back()->withInput($request->input())->withErrors($errors);
                }
                $business_segment = new BusinessSegment();
                $business_segment->alias_name = $alias_name;
                $business_segment->segment_id = $segment->id;
                $business_segment->merchant_id = $merchant_id;
                // $business_segment->delivery_service = 2;
            }

            $business_segment->country_id = $request->country_id;
            $business_segment->full_name = $request->full_name;
            $business_segment->phone_number = $request->phone_number;
            $business_segment->email = $request->email;
            $business_segment->address = $request->address;
            $business_segment->landmark = $request->landmark;
            $business_segment->open_time = json_encode($request->open_time);
            $business_segment->close_time = json_encode($request->close_time);
            $business_segment->status = $request->status;
            $business_segment->latitude = $request->latitude;
            $business_segment->longitude = $request->longitude;
            $business_segment->is_popular = $request->is_popular;
            $business_segment->country_area_id = $country_area_id;
//            $business_segment->commission_type = $request->commission_type;
            $business_segment->commission_method = $request->commission_method;
            $business_segment->commission = $request->commission;
//            $business_segment->delivery_service = $request->delivery_service;
            $business_segment->order_request_receiver = $request->order_request_receiver;
            $business_segment->rating = $request->rating;

            if($slug == 'FOOD')
            {
                $business_segment->delivery_time = $request->delivery_time;
                $business_segment->minimum_amount = $request->minimum_amount;
                $business_segment->minimum_amount_for = $request->minimum_amount_for;
            }

            if(!empty($request->password))
            {
                $business_segment->password = Hash::make($request->password);
            }
            if(!empty($request->hasFile('business_logo')))
            {
                $business_segment->business_logo = $this->uploadImage('business_logo','business_logo');
            }
            if(!empty($request->hasFile('login_background_image')))
            {
                $business_segment->login_background_image = $this->uploadImage('login_background_image','business_login_background_image');
            }

            if(!empty($request->hasFile('business_profile_image')))
            {
                $business_segment->business_profile_image = $this->uploadImage('business_profile_image','business_profile_image');
            }
            $bank_details = [
                'bank_name'=>$request->bank_name,
                'account_holder_name'=>$request->account_holder_name,
                'bank_code'=>$request->bank_code,
                'account_number'=>$request->account_number,
            ];
            $business_segment->bank_details = json_encode($bank_details);
             $business_segment->delivery_service = !empty($request->delivery_service) ? $request->delivery_service : 2;
//            p($business_segment->bank_details);
            $business_segment->save();
            $arr_agencies = $request->delivery_service == 2 ? $request->driver_agency_id : [];
            $business_segment->DriverAgency()->sync($arr_agencies);
//            p($business_segment);
            //create cofigurations for business segment
            $config=BusinessSegmentConfigurations::where('business_segment_id',  $business_segment->id)->first();
            if(empty($config)){
                $config = new BusinessSegmentConfigurations;
                $config->business_segment_id=$business_segment->id;
                $config->save();
            }

            //create onesignal cofigurations for business segment
            $onesignal_config=BusinessSegmentOnesignal::where('business_segment_id',  $business_segment->id)->first();
            if(empty($onesignal_config)){
                $onesignal_config = new BusinessSegmentOnesignal;
                $onesignal_config->business_segment_id=$business_segment->id;
            }
            if(!empty($request->application_key)){
                $onesignal_config->application_key=$request->application_key;
            }
            else{
                $merchant_onesignal = Onesignal::where([['merchant_id', '=', $merchant_id]])->first();
                $onesignal_config->application_key=$merchant_onesignal->web_application_key;
            }
            $onesignal_config->save();
            
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return redirect()->back()->withErrors($message);
            // Rollback Transaction
            DB::rollback();
        }
        // Commit Transaction
        DB::commit();
        return redirect()->route('merchant.business-segment',$slug)->with('success', trans("$string_file.added_successfully"));
    }

    function getMerchantCountry()
    {
        $merchant_id = get_merchant_id();
        $countries = Country::select('id','phonecode')->where('merchant_id',$merchant_id)->get()->toArray();
        $arr_country = [];
        foreach ($countries as $country)
        {
            $arr_country[$country['id']] = $country['phonecode'];
        }
        return $arr_country;
    }

    function getSegment($slug)
    {
        return Segment::select('id')->where('slag',$slug)->first();
    }

    public function statistics(Request $request, $slug, $id = NULL)
    {
        $checkPermission = check_permission(1, 'order_statistics_'.$slug);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $data = [];
        $order = new Order;
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $segment = Segment::where('slag',$slug)->first();
        $business_seg_list = BusinessSegment::where('segment_id',$segment->id)->where('merchant_id',$merchant_id)->pluck('full_name','id')->toArray();
        $business_seg = [];
        $data['business_summary'] = [];
        $data['summary'] = [];

        $merchant_name = $merchant->BusinessName;
        $segment_id = $segment->id;
        $currency = "";
        $request->request->add(['merchant_id'=>$merchant_id,'segment_id'=>$segment_id]);
        if($id != NULL){
            $business_seg = BusinessSegment::Find($id);
            $request->request->add(['business_segment_id'=>$id]);
            $business_income = BookingTransaction::select(DB::raw('SUM(customer_paid_amount) as order_amount'),DB::raw('SUM(company_earning) as merchant_earning'),DB::raw('SUM(driver_earning) as driver_earning'),DB::raw('SUM(business_segment_earning) as store_earning'))
                ->with(['Order'=>function($q) use($merchant_id,$segment_id,$id) {
                    $q->where([['merchant_id','=',$merchant_id],['segment_id','=',$segment_id],['business_segment_id','=',$id]])->get();
                }])
                ->whereHas('Order',function($q) use($merchant_id,$segment_id,$id) {
                    $q->where([['merchant_id','=',$merchant_id],['segment_id','=',$segment_id],['business_segment_id','=',$id]]);
                })->where('order_id','!=',NULL)
                ->first();
            $business_orders = Order::where([['business_segment_id','=',$id]])->count();
            $data['business_summary'] = [
                'products'=> !empty($business_seg) ? $business_seg->Product->count() : '---',
                'orders'=> !empty($business_orders) ? $business_orders: '---',
                'income'=> $business_income,
            ];
            $currency = $business_seg->Country->isoCode;
//            $merchant_id = $business_seg->merchant_id;
        }
//        else
//        {
//            $merchant = get_merchant_id(false);
//            $merchant_id = $merchant->id;
//            $merchant_name = $merchant->BusinessName;

//        }
        // summery of merchant
        $segment_id = $segment->id;
        $merchant_products = Product::where([['merchant_id','=',$merchant_id],['segment_id','=',$segment_id]])->count();
        $business_orders = Order::where([['merchant_id','=',$merchant_id],['segment_id','=',$segment_id]])->count();
        $income = BookingTransaction::select(DB::raw('SUM(customer_paid_amount) as order_amount'),DB::raw('SUM(company_earning) as merchant_earning'),DB::raw('SUM(driver_earning) as driver_earning'),DB::raw('SUM(business_segment_earning) as store_earning'))
            ->with(['Order'=>function($q) use($merchant_id,$segment_id) {
                $q->where([['merchant_id','=',$merchant_id],['segment_id','=',$segment_id]])->get();
            }])
            ->whereHas('Order',function($q) use($merchant_id,$segment_id) {
                $q->where([['merchant_id','=',$merchant_id],['segment_id','=',$segment_id]]);
            })->where('order_id','!=',NULL)
            ->first();
            $data['summary'] = [
                'products'=> $merchant_products,
                'orders'=> !empty($business_orders) ? $business_orders : 0,
                'income'=> $income,
            ];
        $data['currency'] = $currency;
        $all_orders = $order->getOrders($request,true);
        $request->request->add(['id'=>$id]);
        $data['arr_orders'] = $all_orders;
        $req_param['merchant_id'] =  $merchant_id;
        $data['arr_status'] = $this->getOrderStatus($req_param);
        $data['title'] =  !empty($business_seg) ? $business_seg->full_name : '---';
        $data['id'] =  !empty($business_seg) ? $business_seg->id :NULL;
        $data['slug'] =  !empty($business_seg) ? $business_seg->Segment->slag : $segment->slag;
        $data['business_seg_list'] = $business_seg_list;
        $data['merchant_name'] = $merchant_name;
//        p($data['summary']);
        $data['info_setting'] = InfoSetting::where('slug','ORDER')->first();
        return view('merchant.business-segment.statistics')->with($data);
    }

    public function cashoutRequest(Request $request){
        try{
            $merchant_id = get_merchant_id();
            $permission_segments = get_permission_segments(1,true);
            $cashout_requests = BusinessSegmentCashout::whereHas('BusinessSegment', function($query) use($permission_segments){
                $query->whereHas('Segment',function($query) use($permission_segments){
                    $query->whereIn('slag',$permission_segments);
                });
            })->where('merchant_id',$merchant_id)->latest()->paginate(20);
            $info_setting = InfoSetting::where('slug', 'BUSINESS_SEGMENT_CASHOUT')->first();
            return view('merchant.business-segment.cashout.index',compact('cashout_requests','info_setting'));
        }catch (\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function cashoutChangeStatus(Request $request, $id){
        try{
            $merchant_id = get_merchant_id();
            $cashout_request = BusinessSegmentCashout::with('BusinessSegment')->where('merchant_id',$merchant_id)->find($id);
            $info_setting = InfoSetting::where('slug', 'BUSINESS_SEGMENT_CASHOUT')->first();
            return view('merchant.business-segment.cashout.edit',compact('cashout_request','info_setting'));
        }catch (\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function cashoutChangeStatusUpdate(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'cashout_status' => 'required',
            'action_by' => 'required',
            'transaction_id' => 'required',
            'comment' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withErrors($errors[0]);
        }
        DB::beginTransaction();
        try{
            $merchant_id = get_merchant_id();
            $string_file = $this->getStringFile($merchant_id);
            $cashout_request = BusinessSegmentCashout::where('merchant_id',$merchant_id)->find($id);
            if($request->cashout_status == 2){
                $paramArray = array(
                    'business_segment_id' => $cashout_request->business_segment_id,
                    'order_id' => NULL,
                    'amount' => $cashout_request->amount,
                    'narration' => 5,
                );
                WalletTransaction::BusinessSegmntWalletCredit($paramArray);
            }
            $cashout_request->cashout_status = $request->cashout_status;
            $cashout_request->action_by = $request->action_by;
            $cashout_request->transaction_id = $request->transaction_id;
            $cashout_request->comment = $request->comment;
            $cashout_request->save();
            DB::commit();
            $return_message = "";
            if($request->cashout_status == 0)
            {
                $return_message = trans("$string_file.cashout_request_pending");
            }
            elseif($request->cashout_status == 1)
            {
                $return_message = trans("$string_file.cashout_request_successfully");
            }
            elseif($request->cashout_status == 2)
            {
                $return_message = trans("$string_file.cashout_request_rejected_refund_amount");
            }
            return redirect()->route('merchant.business-segment.cashout_request')->withSuccess($return_message);
        }catch (\Exception $e){
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function orderDetail(Request $request,$id)
    {
        $order_obj = new Order;
        $request->request->add(['id'=>$id]);
        $order = $order_obj->getOrders($request);
        $business_segment = $order->BusinessSegment;
        $req_param['merchant_id'] = $order->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $hide_user_info_from_store = $order->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $info_setting = InfoSetting::where('slug','ORDER')->first();
        return view('merchant.business-segment.order-details',compact('order','arr_status','business_segment','hide_user_info_from_store','info_setting'));
    }


    // list all orders for merchant panel
    public function orders(Request $request, $slug, $id = NULL)
    {
    
        $checkPermission = check_permission(1, 'order_statistics_'.$slug);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $data = [];
        $order = new Order;
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $segment = Segment::where('slag',$slug)->first();
        $business_seg_list = BusinessSegment::where('segment_id',$segment->id)->where('merchant_id',$merchant_id)->pluck('full_name','id')->toArray();
        $business_seg = [];
        $data['business_summary'] = [];
        $data['summary'] = [];

        $merchant_name = $merchant->BusinessName;
        $segment_id = $segment->id;
        $currency = "";
        $request->request->add(['merchant_id'=>$merchant_id,'segment_id'=>$segment_id,'id'=>$id]);

        $all_orders = $order->getOrders($request,true);
        $data['arr_orders'] = $all_orders;
        $req_param['merchant_id'] =  $merchant_id;
        $data['arr_status'] = $this->getOrderStatus($req_param);
        $data['title'] =  !empty($business_seg) ? $business_seg->full_name : '---';
        $data['id'] =  !empty($business_seg) ? $business_seg->id :NULL;
        $data['slug'] =  !empty($business_seg) ? $business_seg->Segment->slag : $segment->slag;
//        $data['business_seg_list'] = $business_seg_list;
//        $data['merchant_name'] = $merchant_name;
//        p($data['summary']);
        $data['arr_search'] = $request->all();
        $request->request->add(['search_route'=>route('merchant.business-segment.orders',$slug),'url_slug'=>$slug,'arr_bs'=>$business_seg_list]);
        $data['info_setting'] = InfoSetting::where('slug','ORDER')->first();
        $data['search_view'] = $this->orderSearchView($request,$merchant->CountryArea);
//        $data['search_view']['arr_segment'] = $business_seg_list;
        return view('merchant.business-segment.orders')->with($data);
    }

    public function orderSearchView($request,$arr_list = [],$string_file = "")
    {
//        $string_file = $this->getStringFile(NULL,$merchant);
        $data['arr_search'] = $request->all();
        $data['arr_area'] = $this->getMerchantCountryArea($arr_list,0,0,$string_file);

        $search = View::make('business-segment.order.order-search')->with($data)->render();
//        p($search);
        return $search;

    }
}
