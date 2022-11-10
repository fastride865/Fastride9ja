<?php

namespace App\Http\Controllers\Merchant;

use App\Events\SendUserHandymanInvoiceMailEvent;
use App\Models\BookingTransaction;
use App\Models\InfoSetting;
use App\Models\Segment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HandymanOrder;
use App\Traits\HandymanTrait;
use App\Traits\MerchantTrait;
use DB;
use Illuminate\Support\Facades\Auth;
use View;

class HandymanOrderController extends Controller
{
    // all orders
    use HandymanTrait,MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','HANDYMAN_ORDER')->first();
        view()->share('info_setting', $info_setting);
    }

    public function bookingSearchView($request)
    {
        $data['arr_search'] = $request->all();
        $booking_search = View::make('merchant.handyman-order.booking-search')->with($data)->render();
        return $booking_search;
    }

    public function orders(Request $request)
    {
        $checkPermission = check_permission(1, 'booking_management_HANDYMAN');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $handyman = new HandymanOrder;
        $arr_orders = $handyman->getSegmentOrders($request);
        $merchant_id = get_merchant_id();
        $req_param['merchant_id'] = $merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $arr_status = $this->getHandymanBookingStatus($req_param,$string_file);
        $segment_list = get_merchant_segment(false,$merchant_id,2);
        $data = $request->all();
        $search_route =  route('handyman.orders');
        $arr_search = $request->all();
        $request->request->add(['search_route'=>route("handyman.orders")]);
        $search_view = $this->bookingSearchView($request);
        return view('merchant.handyman-order.index',compact('arr_orders','data','arr_status','search_route','segment_list','arr_search','search_view'));
    }

    public function orderDetail(Request $request, $order_id)
    {
        $checkPermission = check_permission(1, 'booking_management_HANDYMAN');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $order_obj = new HandymanOrder;
        $request->request->add(['order_id'=>$order_id]);
        $order = $order_obj->getOrder($request);
        $req_param['merchant_id'] = $order->merchant_id;
        $string_file = $this->getStringFile($order->merchant_id);
        $arr_status = $this->getHandymanBookingStatus($req_param,$string_file);
        return view('merchant.handyman-order.order-detail',compact('order','arr_status'));
    }

    // Taxi based services Earning
    public function handymanServicesEarning(Request $request)
    {
        $checkPermission =  check_permission(1,'view_reports_charts');
        if ($checkPermission['isRedirect'])
        {
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $handyman = new HandymanOrder;
        $request->request->add(['merchant_id'=>$merchant_id,'status'=>'COMPLETED']);
        $arr_bookings = $handyman->getSegmentOrders($request);
//        $arr_booking_id = array_pluck($arr_bookings,'id');
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $query = BookingTransaction::select(DB::raw('SUM(customer_paid_amount) as booking_amount'),DB::raw('SUM(company_earning) as merchant_earning'),DB::raw('SUM(driver_earning) as driver_earning'),DB::raw('SUM(tip) as tip'),DB::raw('SUM(tax_amount) as tax'),DB::raw('SUM(discount_amount) as discount'))
            ->with(['HandymanOrder'=>function($q) use($request,$merchant_id){
                $q->where([['order_status','=',7],['merchant_id','=',$merchant_id]]);
                if (!empty($request->order_id) && $request->order_id) {
                    $q->where('merchant_order_id', $request->order_id);
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
            ->whereHas('HandymanOrder',function($q) use($request,$merchant_id, $permission_area_ids){
                $q->where([['order_status','=',7],['merchant_id','=',$merchant_id]]);
                if (!empty($request->booking_id) && $request->booking_id) {
                    $q->where('merchant_booking_id', $request->booking_id);
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
            });
        $earning_summary = $query->first();
        if(!empty($earning_summary->booking_amount))
        {
            $earning_summary['merchant_total'] = $earning_summary['merchant_earning'] + $earning_summary['tax'] - $earning_summary['discount'];
            $earning_summary['driver_total'] = $earning_summary['driver_earning'] + $earning_summary['tip'];
        }
        $arr_segment = get_merchant_segment(true,$merchant_id,2);
        $request->request->add(['request_from'=>"booking_earning","arr_segment"=>$arr_segment]);
        $arr_search = $request->all();
        $total_bookings = $arr_bookings->total();
        $currency = "";
        $request->request->add(['search_route'=>route("merchant.handyman-services-report")]);
        $search_view = $this->bookingSearchView($request);
        $info_setting = InfoSetting::where('slug', 'HANDYMAN_SERVICE_EARNING')->first();
        return view('merchant.report.handyman-services.earning', compact('arr_bookings','arr_search','earning_summary','total_bookings','currency','search_view','info_setting'));
    }
    
    
    public function sendInvoice(Request $request, $id)
    {
        $order = HandymanOrder::find($id);
        $string_file = $this->getStringFile($order->merchant_id);
        event(new SendUserHandymanInvoiceMailEvent($order));
        return redirect()->route('merchant.handyman.order.detail',$order->id)->withSuccess(trans("$string_file.email_sent_successfully"));
    }
}
