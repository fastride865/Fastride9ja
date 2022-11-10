<?php

namespace App\Http\Controllers\BusinessSegment;

use App\Http\Controllers\Helper\FindDriverController;
use App\Models\BookingTransaction;
use App\Models\BusinessSegment\BusinessSegment;
use App\Models\CancelReason;
use App\Models\InfoSetting;
use App\Models\Onesignal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusinessSegment\Order;
use App\Models\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Validator;
use DB;
use App\Traits\OrderTrait;
use App\Traits\MerchantTrait;
use View;
use DateTime;
use DateTimeZone;
use App\Models\BusinessSegment\BusinessSegmentConfigurations;
use App\Models\BookingConfiguration;
use PDF;

class OrderController extends Controller
{
    //
    // this function will be used for searching also
    use OrderTrait;
    public function orderSearchView($request)
    {
        $data['arr_search'] = $request->all();
        $order_search = View::make('business-segment.order.order-search')->with($data)->render();
        return $order_search;
    }
    public function index(Request $request)
    {
        $order = new Order;
        $arr_orders = $order->getOrders($request,true);
        $business_seg = get_business_segment(false);
        $hide_user_info_from_store = $business_seg->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $req_param['merchant_id'] = $business_seg->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $request->request->add(['search_route'=>route('business-segment.order')]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $business_seg->Merchant->Configuration->time_format;
        return view('business-segment.order.index',compact('arr_orders','arr_status','arr_search','search_view','hide_user_info_from_store','time_format'));
    }

    // this function will be used for searching also
//    public function newOrder(Request $request)
//    {
//        $business_segment = get_business_segment(false);
//        $order_request_receiver = $business_segment->order_request_receiver;
//        $hide_user_info_from_store = $business_segment->Merchant->ApplicationConfiguration->hide_user_info_from_store;
//        $order = new Order;
//        $request->request->add(['status'=>"NEW"]);
//        $arr_orders = $order->getOrders($request,true);
//        $business_seg = get_business_segment(false);
//        $req_param['merchant_id'] = $business_seg->merchant_id;
//        $arr_status = $this->getOrderStatus($req_param);
//        $search_route =  route('business-segment.new-order');
//        $request->request->add(['search_route'=>$search_route]);
//        $search_view = $this->orderSearchView($request);
//        $arr_search = $request->all();
//        $config = BusinessSegmentConfigurations::where('business_segment_id',  $business_seg->id)->first();
//        $booking_config=BookingConfiguration::where('merchant_id',  $business_seg->merchant_id)->first();
//        return view('business-segment.order.new-order',compact('arr_orders','arr_status','order_request_receiver','search_view','arr_search','hide_user_info_from_store','config','booking_config'));
//    }

    // this function will be used for searching also
    public function getCancelledOrder(Request $request)
    {
        $order = new Order;
        $request->request->add(['status'=>"CANCELLED"]);
        $arr_orders = $order->getOrders($request,true);
        $business_seg = get_business_segment(false);
        $hide_user_info_from_store = $business_seg->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $req_param['merchant_id'] = $business_seg->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $search_route =  route('business-segment.cancelled-order');
        $request->request->add(['search_route'=>$search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $business_seg->Merchant->Configuration->time_format;
        return view('business-segment.order.cancelled-order',compact('arr_orders','arr_status','search_view','arr_search','hide_user_info_from_store','time_format'));
    }

    // this function will be used for searching also
    public function getPendingProcessingOrder(Request $request)
    {
        //$business_segment = get_business_segment();
        $order = new Order;
        $request->request->add(['status'=>"PENDING_PROCESSING"]);
        $arr_orders = $order->getOrders($request,true);
        $business_seg = get_business_segment(false);
        $hide_user_info_from_store = $business_seg->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $req_param['merchant_id'] = $business_seg->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $search_route =  route('business-segment.pending-process-order');
        $request->request->add(['search_route'=>$search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $business_seg->Merchant->Configuration->time_format;
        return view('business-segment.order.pending-processing',compact('arr_orders','arr_status','search_view','arr_search','hide_user_info_from_store','time_format'));
    }

    // this function will be used for searching also
    public function getPickupVerificationOrder(Request $request)
    {
        //$business_segment = get_business_segment();
        $order = new Order;
        $request->request->add(['status'=>"PICKUP_VERIFICATION"]);
        $arr_orders = $order->getOrders($request,true);
        $business_seg = get_business_segment(false);
        $req_param['merchant_id'] = $business_seg->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $search_route =  route('business-segment.pending-pick-order-verification');
        $request->request->add(['search_route'=>$search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $business_seg->Merchant->Configuration->time_format;
        $hide_user_info_from_store = $business_seg->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $order_id_verification = $business_seg->Merchant->Configuration->order_id_verification;
        return view('business-segment.order.order-pickup-verification',compact('arr_orders','arr_status','search_view','arr_search','hide_user_info_from_store','time_format','order_id_verification'));
    }

    // this function will be used for searching also
    public function orderOntheWay(Request $request)
    {
        //$business_segment = get_business_segment();
        $order = new Order;
        $request->request->add(['status'=>"ONTHEWAY"]);
        $arr_orders = $order->getOrders($request,true);
        $business_seg = get_business_segment(false);
        $hide_user_info_from_store = $business_seg->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $req_param['merchant_id'] = $business_seg->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $search_route =  route('business-segment.order-ontheway');
        $request->request->add(['search_route'=>$search_route]);
        $search_view = $this->orderSearchView($request);
        $time_format = $business_seg->Merchant->Configuration->time_format;
        $arr_search = $request->all();
        return view('business-segment.order.order-ontheway',compact('arr_orders','arr_status','search_view','arr_search','hide_user_info_from_store','time_format'));
    }

    // this function will be used for searching also
    public function getDeliveredOrder(Request $request)
    {
        //$business_segment = get_business_segment();
        $order = new Order;
        $is_order_completed = "no";
        $request->request->add(['status'=>"DELIVERED",'is_order_completed'=>$is_order_completed]);
        $arr_orders = $order->getOrders($request,true);
        $business_seg = get_business_segment(false);
        $hide_user_info_from_store = $business_seg->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $arr_status = $this->getOrderStatus(['merchant_id'=>$business_seg->merchant_id]);
        $search_route =  route('business-segment.delivered-order.search');
        $request->request->add(['search_route'=>$search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $business_seg->Merchant->Configuration->time_format;
        return view('business-segment.order.delivered-order',compact('arr_orders','arr_status','search_view','arr_search','hide_user_info_from_store','time_format'));
    }

    // this function will be used for searching also
    public function getCompletedOrder(Request $request)
    {
        //$business_segment = get_business_segment();
        $order = new Order;
        $is_order_completed = "yes";
        $request->request->add(['arr_order_status'=>"COMPLETED",'is_order_completed'=>$is_order_completed]);
        $arr_orders = $order->getOrders($request,true);
        $business_seg = get_business_segment(false);
        $hide_user_info_from_store = $business_seg->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $req_param['merchant_id'] = $business_seg->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $search_route =  route('business-segment.completed-order');
        $request->request->add(['search_route'=>$search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $business_seg->Merchant->Configuration->time_format;
        return view('business-segment.order.completed-order',compact('arr_orders','arr_status','search_view','arr_search','hide_user_info_from_store','time_format'));
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
        $calling_from_bs = true;
        return view('business-segment.order.order-detail',compact('order','arr_status','business_segment','hide_user_info_from_store','calling_from_bs'));
    }

    public function orderInvoice(Request $request,$id)
    {
        $business_segment = get_business_segment(false);
        $order_obj = new Order;
        $request->request->add(['id'=>$id]);
        $order = $order_obj->getOrders($request);
        //p($order);
        $hide_user_info_from_store = $business_segment->Merchant->ApplicationConfiguration->hide_user_info_from_store;
//p($hide_user_info_from_store);
//p($order);
        $arr_status = $this->getOrderStatus(['merchant_id'=>$order->merchant_id]);
        $data = $request->all();
      //  p('inn');
        return view('business-segment.order.invoice',compact('order','arr_status','data','business_segment','hide_user_info_from_store'));
    }

    public function orderAssign(Request $request,$id)
    {
        $business_seg = get_business_segment(false);
        $order_obj  = new Order;
        $request->request->add(['id'=>$id]);
        $order = $order_obj->getOrderInfo($request);

        // if delivery person is already assigned then removed from list
        $driver_not = false;
        $arr_not_drivers = [];
        if(!empty($order->driver_id))
        {
            $driver_not = true;
            $arr_not_drivers = [$order->driver_id];
        }
        $arr_agency_id = []; // we can check
            $delivery_service = $business_seg->delivery_service;
            if($delivery_service == 2)
            {
                $arr_agency_id = $business_seg->DriverAgency->pluck('id')->toArray();
            }
        $request->request->add(['latitude'=>$business_seg->latitude,'longitude'=>$business_seg->longitude,
            'merchant_id'=>$business_seg->merchant_id,'segment_id'=>$business_seg->segment_id,'user_id'=>$order->user_id
            ,'service_type_id'=>$order->service_type_id,'driver_vehicle_id'=>$order->driver_vehicle_id,'driver_not'=>$driver_not,'arr_not_drivers'=>$arr_not_drivers,'arr_agency_id'=>$arr_agency_id
        ]);
        $arr_driver = Driver::getDeliveryCandidate($request);
        $req_param['merchant_id'] = $order->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $data = $request->all();
        return view('business-segment.order.assign',compact('order','arr_status','data','arr_driver','driver_not'));
    }

    // order reassign
    public function orderReassign(Request $request,$id)
    {
        $business_seg = get_business_segment(false);
        $order_obj  = new Order;
        $request->request->add(['id'=>$id]);
        $order = $order_obj->getOrderInfo($request);
        // if delivery person is already assigned then removed from list
        $driver_not = false;
        $arr_not_drivers = [];
        if(!empty($order->driver_id))
        {
            $driver_not = true;
            $arr_not_drivers = [$order->driver_id];
        }
        $request->request->add(['latitude'=>$business_seg->latitude,'longitude'=>$business_seg->longitude,
            'merchant_id'=>$business_seg->merchant_id,'segment_id'=>$business_seg->segment_id,'user_id'=>$order->user_id
            ,'service_type_id'=>$order->service_type_id,'driver_vehicle_id'=>$order->driver_vehicle_id,'driver_not'=>$driver_not,'arr_not_drivers'=>$arr_not_drivers
        ]);
        $arr_driver = Driver::getDeliveryCandidate($request);
        $req_param['merchant_id'] = $order->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $data = $request->all();
        return view('business-segment.order.reassign',compact('order','arr_status','data','arr_driver','driver_not'));
    }

    // send request to auto selecting drivers
    public function orderAutoAssign(Request $request)
    {
        try{
            $business_seg = get_business_segment(false);
            $request->request->add(['latitude'=>$business_seg->latitude,'longitude'=>$business_seg->longitude,
                'merchant_id'=>$business_seg->merchant_id,'segment_id'=>$business_seg->segment_id]);
            $message = $this->autoAssign($request,$business_seg);
            return redirect()->route('business-segment.today-order')->withSuccess($message);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            return redirect()->back()->withErrors($message);
        }
    }

    // send request to auto selecting drivers
    public function autoAssign($request,$business_seg)
    {
        $validator = Validator::make($request->all(), [
//            'order_id' => 'required',
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders', 'id')->where(function ($query) {
                    $query->whereIn('order_status',[1]);
                }),
            ]
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new \Exception($errors[0]);
        }
        try{
            $order_id = $request->order_id;
            $order_obj  = new Order;
            $arr_agency_id = []; // we can check
            $delivery_service = $business_seg->delivery_service;
            if($delivery_service == 2)
            {
                $arr_agency_id = $business_seg->DriverAgency->pluck('id')->toArray();
            }
            $request->request->add(['id'=>$order_id,'arr_agency_id'=>$arr_agency_id]);
            $order = $order_obj->getOrderInfo($request);
            return $this->orderAcceptNotification($request,$order);
//            return $message;
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            throw new \Exception($message);
        }
    }


    //send request to manual selected  drivers
    public function orderAssignToDriver(Request $request)
    {

        $business_seg = get_business_segment(false);
        try{
            $arr_driver_id = $request->driver_id;
            $request->request->add(['latitude'=>$business_seg->latitude,'longitude'=>$business_seg->longitude,
                'merchant_id'=>$business_seg->merchant_id,'segment_id'=>$business_seg->segment_id,'arr_id'=>$arr_driver_id]);
//            p('ss',0);
            $message = $this->manualAssign($request,$business_seg);
            return redirect()->route('business-segment.today-order')->withSuccess($message);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            return redirect()->back()->withErrors($message);
        }
    }

    public function manualAssign($request,$business_seg)
    {
        $merchant_id = $request->merchant_id;
        $string_file=$this->getStringFile($merchant_id);
//        $business_seg = get_business_segment(false);
        $validator = Validator::make($request->all(), [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders', 'id')->where(function ($query) {
                    $query->whereIn('order_status',[1]); // order can be assigned when status is 1
                }),
                'driver_id' => 'required',
            ]],
            [
            'driver_id.required' => trans_choice("$string_file.have_to_choose", 3, [ 'NUM' => trans("$string_file.one"),'OBJECT' => trans("$string_file.driver")]),
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new \Exception($errors[0]);
        }
        try{
            $order_id = $request->order_id;
            $order_obj  = new Order;
            $request->request->add(['id'=>$order_id]);
            $order = $order_obj->getOrderInfo($request);
            $arr_agency_id = []; // we can check
            $delivery_service = $business_seg->delivery_service;
            if($delivery_service == 2)
            {
                $arr_agency_id = $business_seg->DriverAgency->pluck('id')->toArray();
            }
            $request->request->add(['id'=>$order_id,'arr_agency_id'=>$arr_agency_id]);
//            $request->request->add(['latitude'=>$business_seg->latitude,'longitude'=>$business_seg->longitude,
//                'merchant_id'=>$merchant_id,'segment_id'=>$business_seg->segment_id,'arr_id'=>$arr_driver_id]);
            return $this->orderAcceptNotification($request,$order);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            throw new \Exception($message);
//            return redirect()->back()->withErrors($message);
        }
    }

    //reassign order to another driver
    public function reAssignToDriver(Request $request)
    {
        $order_id = $request->order_id;
        $order_obj  = new Order;
        $request->request->add(['id'=>$order_id]);
        $order = $order_obj->getOrderInfo($request);
        $merchant_id = $order->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $validator = Validator::make($request->all(), [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders', 'id')->where(function ($query) {
                    $query->whereIn('order_status',[6,7,9,10]); // order can be reassigned when status is 6,7,9,10
                }),
                'driver_id' => 'required',
            ]],
            [
                'driver_id.required' => trans_choice("$string_file.have_to_choose", 3, [ 'NUM' => trans("$string_file.one"),'OBJECT' => trans("$string_file.driver")]),
            ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new \Exception($errors[0]);
        }
        try{
            $driver_id = $request->driver_id;
            // make old driver free
            $order->reassign = 1;
            $order->reassign_reason = $request->reassign_reason;
            // free the old driver
            $order->old_driver_id = $order->driver_id;
            $driver = $order->Driver;
            $driver->free_busy = 2;
            $driver->save();

            // assign order to other driver and make him busy
            $order->driver_id = $driver_id;

            // new driver details
            $new_driver = Driver::select('id','free_busy')->find($driver_id);
            $new_driver->free_busy = 1;
            $new_driver->save();

            // save order details
            $order->save();

            $segment_data = [
                'id'=>$order->id,
                'status'=>$order->order_status,
            ];
            $data['notification_type'] = "REASSIGNED_ONGOING_ORDER";
            $data['segment_type'] = $order->Segment->slag;
            $data['segment_sub_group'] = $order->Segment->sub_group_for_app; // its segment sub group for app
            $data['segment_group_id'] = $order->Segment->segment_group_id; // for handyman
            $data['segment_data'] = $segment_data;
            $title = trans("$string_file.new_order_reassign");
            $message = trans("$string_file.new_order_reassign_message");
            $arr_param = ['driver_id' => $driver_id, 'data' => $data, 'message' => $message, 'merchant_id' => $merchant_id, 'title' => $title, 'large_icon' => ''];
            // send reassign notification to driver and user
            Onesignal::DriverPushMessage($arr_param);
            $arr_param = ['user_id' => $order->user_id, 'data' => $data, 'message' => $message, 'merchant_id' => $merchant_id, 'title' => $title, 'large_icon' => ''];
            Onesignal::UserPushMessage($arr_param);
            if($order->order_status == 10)
            {
             return redirect()->route('business-segment.order-ontheway')->withSuccess(trans("$string_file."));
            }
            elseif($order->order_status == 9)
            {
                return redirect()->route('business-segment.pending-pick-order-verification')->withSuccess(trans("$string_file."));
            }
            else
            {
                return redirect()->route('business-segment.pending-process-order')->withSuccess(trans("$string_file."));
            }
//            return $this->orderAcceptNotification($request,$order);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            throw new \Exception($message);
//            return redirect()->back()->withErrors($message);
        }
    }
    // order cancel
    public function orderCancel(Request $request,$id)
    {
        try {
        $business_seg = get_business_segment(false);
        $request->request->add(['order_id'=>$id]);
        $message =  $this->cancelOrderByBusinessSegment($request, $business_seg);
        }
        catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
        DB::commit();
        return redirect()->route('business-segment.cancelled-order')->withSuccess($message);
    }

    public function processOrder(Request $request,$id)
    {
        $business_segment = get_business_segment(false);
        $order_obj = new Order;
        $request->request->add(['id'=>$id]);
        $order = $order_obj->getOrders($request);
        $req_param['merchant_id'] = $order->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        return view('business-segment.order.process-order',compact('order','arr_status','business_segment'));
    }

    public function startOrderProcessing(Request $request,$id)
    {
        $business_segment = get_business_segment(false);
        $request->request->add(['order_id'=>$id]);
        try {
          $message =  $this->orderProcessing($request,$business_segment);
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            return redirect()->back()->withErrors($message);
        }
        return redirect()->route('business-segment.pending-pick-order-verification')->withSuccess($message);
    }

    public function orderPickupVerify(Request $request){
        try {
            $message = $this->orderOTPVerification($request);
        }catch (\Exception $e)
        {
            $message = $e->getMessage();
            return redirect()->route('business-segment.pending-pick-order-verification')->withErrors($message);
        }
        return redirect()->route('business-segment.pending-pick-order-verification')->withSuccess($message);
    }
     // rejected order by restaurant
    public function rejectOrder(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            $business_seg = get_business_segment(false);
            $request->request->add(['order_id'=>$id]);
            $this->rejectOrderByBusinessSegment($request, $business_seg);

            $merchant_id = $business_seg->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
        }
        catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
        DB::commit();
        $order_rejected_message = trans("$string_file.order_rejected_successfully");
        return redirect()->route('business-segment.rejected-order')->withSuccess($order_rejected_message);
    }
    // this function will be used for searching also
    public function getRejectedOrder(Request $request)
    {
        $order = new Order;
        $business_seg = get_business_segment(false);
        $hide_user_info_from_store = $business_seg->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $request->request->add(['status'=>"REJECTED"]);
        $arr_orders = $order->getOrders($request,true);
        $req_param['merchant_id'] = $business_seg->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $arr_search = $request->all();
        $search_route =  route('business-segment.rejected-order');
        $request->request->add(['search_route'=>$search_route]);
        $search_view = $this->orderSearchView($request);
        $time_format = $business_seg->Merchant->Configuration->time_format;
        return view('business-segment.order.rejected-order',compact('arr_orders','arr_status','arr_search','search_view','hide_user_info_from_store','time_format'));
    }

    // this function will be used for searching also
    public function getExpiredOrder(Request $request)
    {
        $order = new Order;
        $business_seg = get_business_segment(false);
        $hide_user_info_from_store = $business_seg->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $request->request->add(['status'=>"EXPIRED"]);
        $arr_orders = $order->getOrders($request,true);
        $req_param['merchant_id'] = $business_seg->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $arr_search = $request->all();
        $search_route =  route('business-segment.expired-order');
        $request->request->add(['search_route'=>$search_route]);
        $search_view = $this->orderSearchView($request);
        $time_format = $business_seg->Merchant->Configuration->time_format;
        return view('business-segment.order.rejected-order',compact('arr_orders','arr_status','arr_search','search_view','hide_user_info_from_store','time_format'));
    }

    public function orderEarningSummary(Request $request){
        $data = [];
        $order = new Order;
        $merchant_id = get_merchant_id();
        $id = $request->business_segment_id;
        $data['business_summary'] = [];
        $segment_id = $request->segment_id;
        $request->request->add(['status'=>'COMPLETED','merchant_id'=>$merchant_id]); // DELIVERED
        $all_orders = $order->getOrders($request,true);
        $arr_order_id = array_pluck($all_orders,'id');
        $request->request->add(['merchant_id'=>$merchant_id,'segment_id'=>$segment_id,'id'=>$id]);

        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }

        $query = BookingTransaction::select(DB::raw('SUM(customer_paid_amount) as order_amount'),DB::raw('SUM(company_gross_total) as merchant_earning'),DB::raw('SUM(driver_total_payout_amount) as driver_earning'),DB::raw('SUM(business_segment_total_payout_amount) as store_earning'))
            ->with(['Order'=>function($q) use($request,$merchant_id){
                $q->where([['order_status','=',11],['merchant_id','=',$merchant_id]]);

                if (!empty($request->booking_id) && $request->booking_id) {
                    $q->where('merchant_booking_id', $request->booking_id);
                }
                if (!empty($request->business_segment_id)) {
                    $q->where('business_segment_id', $request->business_segment_id);
                }
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
                if ($request->start) {
                    $start_date = date('Y-m-d',strtotime($request->start));
                    $end_date = date('Y-m-d ',strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                }
            }])
            ->whereHas('Order',function($q) use($request,$merchant_id, $permission_area_ids){
                $q->where([['order_status','=',11],['merchant_id','=',$merchant_id]]);
                if (!empty($request->order_id) && $request->order_id) {
                    $q->where('merchant_order_id', $request->order_id);
                }
                if (!empty($request->business_segment_id)) {
                    $q->where('business_segment_id', $request->business_segment_id);
                }
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
                if ($request->start) {
                    $start_date = date('Y-m-d',strtotime($request->start));
                    $end_date = date('Y-m-d ',strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                }
                if(!empty($permission_area_ids)){
                    $q->whereIn("country_area_id",$permission_area_ids);
                }
            })
        ;
        $business_income = $query->first();
        $data['business_summary'] = [
            'orders'=> $all_orders->total(),
            'income'=> $business_income,
        ];
        $currency = "";
        $data['merchant_id'] = $merchant_id;
        $data['currency'] = $currency;
        $data['arr_orders'] = $all_orders;
        $req_param['merchant_id'] = $merchant_id;
        $data['title'] =  "";
        $data['merchant_name'] =  "";
        $request->request->add(['search_route'=>route('merchant.delivery-services-report')]);
        $order_con = new OrderController;
        $arr_segment = get_merchant_segment(false,$merchant_id,1,2);
        $arr_segment = count($arr_segment) > 1 ? $arr_segment : [];
        $business_segment = BusinessSegment::select('id','full_name')
            ->with('Merchant')
            ->where([['merchant_id', '=', $merchant_id]])
            ->where(function($q) use ($request) {
                if(!empty($request->segment_id))
                {
                    $q->where('segment_id',$request->segment_id);
                }
            })
            ->get();
        $arr_bs = [];
        foreach ($business_segment as $bs)
        {
            $arr_bs[$bs->id] =  $bs->full_name;
        }
        $request->request->add(['calling_view'=>"earning","arr_segment"=>$arr_segment,"arr_bs"=>$arr_bs]);
        $data['search_view'] = $order_con->orderSearchView($request);
        $data['arr_search'] = $request->all();
        $data['info_setting'] = InfoSetting::where('slug', 'DELIVERY_SERVICE_EARNING')->first();
        return view('merchant.report.delivery-services.earning')->with($data);
    }

    public function generateInvoice(Request $request,$business_segment,$id){
        $order_obj = new Order;
        $request->request->add(['business_segment_id'=>$business_segment]);
        $request->request->add(['id'=>$id]);
        $order = $order_obj->getOrderInfo($request);
        $business_segment = BusinessSegment::find($order->business_segment_id);
        $hide_user_info_from_store = $business_segment->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $data=array(
            'business_segment'=>$business_segment,
            'order'=>$order,
            'hide_user_info_from_store'=>$hide_user_info_from_store,
        );
        $pdf = PDF::loadView('business-segment.order.pdf-invoice', $data);
        return $pdf->download('invoice.pdf');
    }

    // this function will be used for searching also
    public function todayOrder(Request $request)
    {
        $business_segment = get_business_segment(false);
        $order_request_receiver = $business_segment->order_request_receiver;
        $hide_user_info_from_store = $business_segment->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $order = new Order;
        $request->request->add(['status'=>"TODAY"]);
        $arr_orders = $order->getOrders($request,true);
        $business_seg = get_business_segment(false);
        $req_param['merchant_id'] = $business_seg->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $search_route =  route('business-segment.today-order');
        $request->request->add(['search_route'=>$search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $business_segment->Merchant->Configuration->time_format;
        $config = BusinessSegmentConfigurations::where('business_segment_id',  $business_seg->id)->first();
        $booking_config=BookingConfiguration::where('merchant_id',  $business_seg->merchant_id)->first();
        return view('business-segment.order.today-order',compact('arr_orders','arr_status','order_request_receiver','search_view','arr_search','hide_user_info_from_store','config','booking_config','time_format'));
    }

    // future date orders
    // this function will be used for searching also
    public function upcomingOrder(Request $request)
    {
        $business_segment = get_business_segment(false);
        $order_request_receiver = $business_segment->order_request_receiver;
        $hide_user_info_from_store = $business_segment->Merchant->ApplicationConfiguration->hide_user_info_from_store;
        $order = new Order;
        $request->request->add(['status'=>"UPCOMING"]);
        $arr_orders = $order->getOrders($request,true);
        $business_seg = get_business_segment(false);
        $req_param['merchant_id'] = $business_seg->merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $search_route =  route('business-segment.today-order');
        $request->request->add(['search_route'=>$search_route]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $time_format = $business_segment->Merchant->Configuration->time_format;
        $config = BusinessSegmentConfigurations::where('business_segment_id',  $business_seg->id)->first();
        $booking_config=BookingConfiguration::where('merchant_id',  $business_seg->merchant_id)->first();
        return view('business-segment.order.upcoming-order',compact('arr_orders','arr_status','order_request_receiver','search_view','arr_search','hide_user_info_from_store','config','booking_config','time_format'));
    }
}
