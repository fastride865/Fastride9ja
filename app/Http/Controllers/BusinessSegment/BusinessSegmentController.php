<?php

namespace App\Http\Controllers\BusinessSegment;
use App\Models\BookingTransaction;
use App\Models\BusinessSegment\BusinessSegment;
use App\Models\BusinessSegment\Order;
use App\Models\BusinessSegment\Product;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;
use DB;
use App\Traits\OrderTrait;

class BusinessSegmentController extends Controller
{
    use OrderTrait;
    public function __construct()
    {
        $this->middleware('auth:business-segment');
    }

    public function dashboard(Request $request)
    {
        $order = new Order;
        $bs = get_business_segment(false);
        // p($bs);
        $segment_slug = $bs->Segment->slag;
        // p($segment_slug);
        $arr_orders = $order->getOrders($request,false);
        // p($arr_orders);
        $all_orders_list = $arr_orders;
        $all_orders = $arr_orders->count();
        $arr_order_id = array_pluck($all_orders_list,'id');

        $total_expired_orders = $arr_orders->whereIn('order_status',[2,3,5,8,12])->count();

        $cancelled_orders = $arr_orders->whereIn('order_status',[2,5,8])->count();

        $rejected_orders = $arr_orders->whereIn('order_status',[3])->count();

        $auto_expired_orders = $arr_orders->whereIn('order_status',[12])->count();

        $history_orders = $arr_orders->where('order_status',11)->count();

        $completed_orders = $arr_orders->where('is_order_completed',1)->count();

        $delivered_orders = $history_orders - $completed_orders;

        $date = date('Y-m-d');
        $new_orders = Order::where([['order_status','=',1],['business_segment_id','=',$bs->id]])->whereDate('order_date','>=',$date)->count();
        $today_orders = Order::where([['order_status','=',1],['business_segment_id','=',$bs->id]])->whereDate('order_date',$date)->count();
        $upcoming_orders = $new_orders - $today_orders;

        $on_going_orders = $arr_orders->whereIn('order_status',[6,7,9,10])->count();
        $pending_process_orders = $arr_orders->whereIn('order_status',[6,7])->count();
        $pending_verification = $arr_orders->where('otp_for_pickup','!=', NULL)->where('confirmed_otp_for_pickup', 2)->count();
        $ontheway = $arr_orders->where('confirmed_otp_for_pickup', 1)->whereIn('order_status',[7,9,10])->count();
        $drivers = null;
        $wallet_money = $bs->Country->isoCode.' '.$bs->wallet_amount;
//        $products = $bs->Product->whereNull('deleted_at')->count();
        $products = Product::where("business_segment_id",$bs->id)->whereHas('Category',function($q) {
            $q->whereNull('delete');
        })->whereNull("delete")->count();
        $users = null;
        $bs_earning = BookingTransaction::select(DB::raw('SUM(business_segment_earning) as store_earning'))
           ->whereIn('order_id',$arr_order_id)->first();
        $earnings = $bs->Country->isoCode.' '.$bs_earning->store_earning;
        return view('business-segment.dashboard', compact('on_going_orders','pending_process_orders','ontheway','today_orders','pending_verification','rejected_orders','new_orders','total_expired_orders','auto_expired_orders','upcoming_orders','delivered_orders', 'all_orders', 'cancelled_orders','history_orders', 'users', 'drivers', 'earnings', 'completed_orders', 'wallet_money','products','segment_slug'));
    }

    public function statistics(Request $request, $id)
    {
        $data = [];
        $order = new Order;
        $business_seg = BusinessSegment::Find($id);
        $merchant_id = $business_seg->merchant_id;
        $data['business_summary'] = [];
        $segment_id = $business_seg->segment_id;
        $request->request->add(['merchant_id'=>$merchant_id,'segment_id'=>$segment_id,'id'=>$id]);
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

        $data['currency'] = $currency;
        $all_orders = $order->getOrders($request,true);
        $data['arr_orders'] = $all_orders;
        $req_param['merchant_id'] = $business_seg->merchant_id;
        $data['arr_status'] = $this->getOrderStatus($req_param);;
        $data['title'] =  !empty($business_seg) ? $business_seg->full_name : '---';
        $data['merchant_name'] =  !empty($business_seg) ? $business_seg->Merchant->BusinessName : '---';
        return view('business-segment.statics')->with($data);
    }

    public function walletTransaction(Request $request){
        $business_segment = get_business_segment();
        p('Inside wallet transaction');
    }
    public function earningSummary(Request $request){
        $data = [];
        $order = new Order;
        $business_seg = get_business_segment(false);
        $id = $business_seg->id;
        $merchant_id = $business_seg->merchant_id;
        $data['business_summary'] = [];
        $segment_id = $business_seg->segment_id;
        $request->request->add(['status'=>'COMPLETED','merchant_id'=>$merchant_id,'segment_id'=>$segment_id,'id'=>$id]);  // DELIVERED
        $all_orders = $order->getOrders($request,true);
        $query = BookingTransaction::select(DB::raw('SUM(customer_paid_amount) as order_amount'),DB::raw('SUM(company_earning) as merchant_earning'),DB::raw('SUM(driver_earning) as driver_earning'),DB::raw('SUM(business_segment_earning) as store_earning'))
            ->with(['Order'=>function($q) use($request,$id){
                $q->where([['order_status','=',11],['business_segment_id','=',$id]]);
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
            }])
            ->whereHas('Order',function($q) use($request,$id){
                $q->where([['order_status','=',11],['business_segment_id','=',$id]]);
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
            });
        $business_income = $query->first();
        $data['business_summary'] = [
            'orders'=> $all_orders->total(),
            'income'=> $business_income,
            'commission'=> $business_seg->commission,
        ];
        $currency = $business_seg->Country->isoCode;
        $data['currency'] = $currency;
        $data['arr_orders'] = $all_orders;
        $req_param['merchant_id'] = $business_seg->merchant_id;
        $data['title'] =  !empty($business_seg) ? $business_seg->full_name : '---';
        $data['merchant_name'] =  !empty($business_seg) ? $business_seg->Merchant->BusinessName : '---';
        $request->request->add(['search_route'=>route('business-segment.earning')]);
        $order_con = new OrderController;
        $request->request->add(['calling_view'=>'earning']);
        $data['search_view'] = $order_con->orderSearchView($request);
        $data['arr_search'] = $request->all();
        return view('business-segment.report.earning')->with($data);
    }
}
