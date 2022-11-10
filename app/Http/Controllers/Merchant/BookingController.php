<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\HolderController;
use App\Models\BookingRating;
use App\Models\BookingRequestDriver;
use App\Models\BookingTransaction;
use App\Models\EmailConfig;
use App\Models\EmailTemplate;
use App\Models\FailBooking;
use App\Models\InfoSetting;
use App\Models\Onesignal;
use App\Models\Outstanding;
use App\Models\PriceCard;
use App\Models\PriceCardValue;
use App\Models\PricingParameter;
use Auth;
use App\Models\Driver;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\BookingConfiguration;
use App\Traits\BookingTrait;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Events\SendUserInvoiceMailEvent;
use View;
use App\Traits\MerchantTrait;
use DB;

class BookingController extends Controller
{
    use BookingTrait,MerchantTrait;
    // common search blade
    public function __construct()
    {
//        $query_string = \Request::getRequestUri();
//        $query_string_arr = explode('/',$query_string);
//        if (in_array("DELIVERY", $query_string_arr)) {
//            $info_setting = InfoSetting::where('slug','DELIVERY_RIDE')->first();
//        }else {
//            $info_setting = InfoSetting::where('slug','TAXI_RIDE')->first();
//        }
//        view()->share('info_setting', $info_setting);
    }

    public function orderSearchView($request)
    {
        $data['arr_search'] = $request->all();
        $order_search = View::make('merchant.booking.ride-search')->with($data)->render();
        return $order_search;
    }

    // active bookings
    public function index(Request $request,$url_slug)
    {
        $checkPermission =  check_permission(1,"ride_management_$url_slug");
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $request->request->add(['search_route'=>route('merchant.activeride',$url_slug),'request_from'=>"ACTIVE",'url_slug'=>$url_slug]);
//        $all_bookings = $this->ActiveBooking(true,'MERCHANT',$request);
        $all_bookings = $this->getBookings($request,$pagination = true, 'MERCHANT');
//        if (!empty($all_bookings)){
//            foreach ($all_bookings as $booking){
//                if (!empty($booking->driver_id)){
//                    $timezone = $booking->Driver->CountryArea->timezone;
//                }else{
//                    if(isset($booking->User->Country->CountryArea[0]->timezone)){
//                        $timezone = $booking->User->Country->CountryArea[0]->timezone;
//                    }else{
//                        $timezone = 'Asia/Kolkata';
//                    }
//                }
//                date_default_timezone_set($timezone);
//                $date = date('Y-m-d');
//                $currenct_time = date('H:i');
//                $later_booking_date = set_date($booking->later_booking_date);
//                if ($booking->booking_type == 1 && $date > $booking->updated_at){
//                    Booking::where('id','=',$booking->id)->update(['booking_status'=> 1016]);
//                }
//                if ($booking->booking_type == 2 && ($date > $later_booking_date || ($date == $later_booking_date && $currenct_time > $booking->later_booking_time))){
//                    Booking::where('id','=',$booking->id)->update(['booking_status'=> 1016]);
//                }
//            }
//        }
//        $bookings = $all_bookings;
//        $bookings = $this->ActiveBookingNow();
//        $later_bookings = $this->ActiveBookingLater();
        $cancelreasons = $this->CancelReason();
        $bookingConfig = BookingConfiguration::select('ride_otp')->where([['merchant_id','=',$merchant_id]])->first();

        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $string_file = $this->getStringFile($merchant_id);
        $arr_booking_status = $this->getBookingStatus($string_file);
        if ($url_slug == "DELIVERY") {
            $info_setting = InfoSetting::where('slug','DELIVERY_RIDE')->first();
        }else {
            $info_setting = InfoSetting::where('slug','TAXI_RIDE')->first();
        }
        return view('merchant.booking.active', compact('all_bookings', 'cancelreasons','bookingConfig','search_view','arr_search','arr_booking_status','info_setting'));
    }

    public function AutoCancel(Request $request,$url_slug)
    {
        $arr_search = $request->all();
        $request->request->add(['search_route'=>route('merchant.autocancel',$url_slug),'request_from'=>'AUTO_CANCEL','url_slug'=>$url_slug]);
        $bookings = $this->getBookings($request,$pagination = true, 'MERCHANT');
        //$bookings = $this->bookings(true, [1016], 'MERCHANT',$url_slug);
        $data = [];
        if ($url_slug == "DELIVERY") {
            $info_setting = InfoSetting::where('slug','AUTO_CANCELLED_DELIVERY_RIDE')->first();
        }else {
            $info_setting = InfoSetting::where('slug','AUTO_CANCELLED_TAXI_RIDE')->first();
        }
        return view('merchant.booking.auto-cancel', compact('bookings','data', 'url_slug','arr_search','info_setting'));
    }

    public function SearchForAutoCancel(Request $request, $url_slug)
    {
        $query = $this->bookings(false, [1016], 'MERCHANT',$url_slug);
        if ($request->booking_id) {
            $query->where('merchant_booking_id', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->date) {
            $query->whereDate('created_at', '=', $request->date);
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $arr_search = $request->all();
        $bookings = $query->paginate(25);
        $data = $request->all();
        return view('merchant.booking.auto-cancel', compact('bookings','data','url_slug','arr_search'));
    }

    public function AllRides(Request $request,$url_slug)
    {
//        $bookings = $this->bookings(true, [1001, 1012, 1002, 1003, 1004, 1005, 1006, 1007, 1008, 1016]) ;
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $arr_booking_status = $this->getBookingStatus($string_file);
        $request->request->add(['search_route'=>route('merchant.all.ride',$url_slug),'request_from'=>"ALL",'arr_booking_status'=>$arr_booking_status,'url_slug'=>$url_slug]);
        $bookings = $this->getBookings($request,$pagination = true, 'MERCHANT');
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        if ($url_slug == "DELIVERY") {
            $info_setting = InfoSetting::where('slug','ALL_DELIVERY_RIDE')->first();
        }else {
            $info_setting = InfoSetting::where('slug','ALL_TAXI_RIDE')->first();
        }
        return view('merchant.booking.all-ride', compact('bookings','search_view','arr_search','arr_booking_status','info_setting'));
    }

//    public function SearchForAllRides(Request $request, $url_slug)
//    {
//        $query = $this->bookings(false, [1001, 1012, 1002, 1003, 1004, 1005, 1006, 1007, 1008, 1016],'MERCHANT',$url_slug);
//        if ($request->booking_id) {
//            $query->where('merchant_booking_id', $request->booking_id);
//        }
//        if ($request->booking_status) {
//            $query->where('booking_status', $request->booking_status);
//        }
//        if ($request->rider) {
//            $keyword = $request->rider;
//            $query->WhereHas('User', function ($q) use ($keyword) {
//                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
//            });
//        }
//        if ($request->date) {
//            $query->whereDate('created_at', '=', $request->date);
//        }
//        if ($request->driver) {
//            $keyword = $request->driver;
//            $query->WhereHas('Driver', function ($q) use ($keyword) {
//                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
//            });
//        }
//        $bookings = $query->paginate(25);
//        $data = $request->all();
//        return view('merchant.booking.all-ride', compact('bookings','data', 'url_slug'));
//    }

    public function CancelBooking(Request $request,$url_slug)
    {
        $checkPermission =  check_permission(1,"ride_management_$url_slug");
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
//        $bookings = $this->bookings(true, [1006, 1007, 1008]);
        $request->request->add(['search_route'=>route('merchant.cancelride',$url_slug),'request_from'=>"CANCEL",'url_slug'=>$url_slug]);
        $bookings = $this->getBookings($request,$pagination = true, 'MERCHANT');
//        $bookings = $this->bookings(true, [1005]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        $string_file = $this->getStringFile($merchant_id);
        $arr_booking_status = $this->getBookingStatus($string_file);
        if ($url_slug == "DELIVERY") {
            $info_setting = InfoSetting::where('slug','CANCELLED_DELIVERY_RIDE')->first();
        }else {
            $info_setting = InfoSetting::where('slug','CANCELLED_TAXI_RIDE')->first();
        }
        return view('merchant.booking.cancel', compact('bookings','search_view','arr_search','arr_booking_status','info_setting'));
    }

//    public function SearchCancelBooking(Request $request)
//    {
//        $query = $this->bookings(false, [1006, 1007, 1008]);
//        if ($request->booking_id) {
//            $query->where('merchant_booking_id', $request->booking_id);
//        }
//        if ($request->rider) {
//            $keyword = $request->rider;
//            $query->WhereHas('User', function ($q) use ($keyword) {
//                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
//            });
//        }
//        if ($request->date) {
//            $query->whereDate('created_at', '=', $request->date);
//        }
//        if ($request->driver) {
//            $keyword = $request->driver;
//            $query->WhereHas('Driver', function ($q) use ($keyword) {
//                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
//            });
//        }
//        $bookings = $query->paginate(25);
//        $data = $request->all();
//        return view('merchant.booking.cancel', compact('bookings','data'));
//    }

    public function CompleteBooking(Request $request,$url_slug)
    {
        $checkPermission =  check_permission(1,"ride_management_$url_slug");
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $request->request->add(['search_route'=>route('merchant.completeride',$url_slug),'request_from'=>"COMPLETE",'url_slug'=>$url_slug]);
        $bookings = $this->getBookings($request,$pagination = true, 'MERCHANT');
//        $bookings = $this->bookings(true, [1005]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();
        if ($url_slug == "DELIVERY") {
            $info_setting = InfoSetting::where('slug','COMPLETED_DELIVERY_RIDE')->first();
        }else {
            $info_setting = InfoSetting::where('slug','COMPLETED_TAXI_RIDE')->first();
        }
        return view('merchant.booking.complete', compact('bookings','search_view','arr_search','info_setting'));
    }

//    public function SerachCompleteBooking(Request $request)
//    {
//        $query = $this->bookings(false, [1005]);
//        if ($request->booking_id) {
//            $query->where('merchant_booking_id', $request->booking_id);
//        }
////        if ($request->date) {
////            $query->whereDate('created_at', '=', $request->date);
////        }
//
//        if ($request->date) {
//            $query->whereDate('created_at', '>=', $request->date);
//        }
//        if ($request->date1) {
//            $query->whereDate('created_at', '<=', $request->date1);
//        }
//
//        if ($request->rider) {
//            $keyword = $request->rider;
//            $query->WhereHas('User', function ($q) use ($keyword) {
//                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
//            });
//        }
//        if ($request->driver) {
//            $keyword = $request->driver;
//            $query->WhereHas('Driver', function ($q) use ($keyword) {
//                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
//            });
//        }
//        $bookings = $query->paginate(25);
//        $data = $request->all();
//        return view('merchant.booking.complete', compact('bookings','data'));
//    }

    public function FailedBooking($url_slug)
    {
        $checkPermission =  check_permission(1,"ride_management_$url_slug");
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $bookings = $this->failsBookings(true,'MERCHANT',$url_slug);
        $data = [];
        if ($url_slug == "DELIVERY") {
            $info_setting = InfoSetting::where('slug','FAILED_DELIVERY_RIDE')->first();
        }else {
            $info_setting = InfoSetting::where('slug','FAILED_TAXI_RIDE')->first();
        }
        return view('merchant.booking.fail', compact('bookings','data', 'url_slug','info_setting'));
    }

    public function SearchFailedBooking(Request $request, $url_slug)
    {
        $query = $this->failsBookings(false,'MERCHANT',$url_slug);
        if ($request->booking_id) {
            $query->where('id', $request->booking_id);
        }
        if ($request->date) {
            $query->whereDate('created_at', '=', $request->date);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        $bookings = $query->paginate(25);
        $data = $request->all();
        return view('merchant.booking.fail', compact('bookings','data', 'url_slug'));
    }

    public function CancelBookingAdmin(Request $request)
    {
        $request->validate([
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->whereIn('booking_status', [1001, 1012, 1002, 1003, 1004]);
                }),
            ],
            'cancel_reason_id' => 'required|integer',
        ]);
        DB::beginTransaction();
        try {
            $booking = Booking::find($request->booking_id);
            $booking->cancel_reason_id = $request->cancel_reason_id;
            $booking->additional_notes = $request->description;
            $booking->booking_status = 1008;
            $booking->save();
            BookingRequestDriver::where("booking_id",$request->booking_id)->update(["request_status" => 4]);
            $bookingData = new BookingDataController;
            $bookingData->bookingNotificationForUser($booking,"CANCEL_RIDE");
            $driver_id = $booking->driver_id;
            if (!empty($driver_id)) {
                $Driver = Driver::select('id','free_busy')->where([['id', '=', $driver_id]])->first();
                $Driver->free_busy = 2;
                $Driver->save();
                $bookingData->SendNotificationToDrivers($booking);
            }
        }catch (\Exception $e)
        {
          DB::rollBack();
          return redirect()->route('merchant.activeride',$booking->Segment->slag)->withErrors($e->getMessage());
        }
        DB::commit();
        $string_file = $this->getStringFile($booking->merchant_id);
        return redirect()->route('merchant.activeride',$booking->Segment->slag)->withSuccess(trans("$string_file.ride_cancelled"));
    }

    public function CompleteBookingAdmin(Request $request)
    {
        $request->validate([
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where('booking_status', 1004);
                }),
            ]
        ]);
        DB::beginTransaction();
        try {
            $booking = Booking::find($request->booking_id);
            $bookingDetails = $booking->BookingDetail;
            $booking_data = new \App\Http\Controllers\Api\BookingController();
            $BillDetails = $booking_data->EstimateBillDetailsBreakup($booking->bill_details, $booking->estimate_bill);
            $amount = $booking->estimate_bill;
            $total_payable = $amount;
            Outstanding::where(['user_id' => $booking->user_id, 'reason' => '1', 'pay_status' => 0])->update(['pay_status' => 1]);
            $bookingFee = $BillDetails['booking_fee'];
            $amount_for_commission = $BillDetails['subTotalWithoutDiscount'] - $bookingFee;
            $billDetails = json_encode($BillDetails['bill_details'], true);

            $amount = $amount + $bookingDetails->tip_amount;

            $booking_transaction_submit = BookingTransaction::updateOrCreate([
                'booking_id' => $booking->id,
            ], [
                'date_time_details' => date('Y-m-d H:i:s'),
                'sub_total_before_discount' => $amount_for_commission,
                'surge_amount' => $BillDetails['surge'],
                'extra_charges' => $BillDetails['extracharge'],
                'discount_amount' => $BillDetails['promo'],
                'tax_amount' => $BillDetails['total_tax'],
                'tip' => $bookingDetails->tip_amount,
                'insurance_amount' => $BillDetails['insurnce_amount'],
                'cancellation_charge_received' => $BillDetails['cancellation_amount_received'],
                'cancellation_charge_applied' => '0.0',
                'toll_amount' => $BillDetails['toolCharge'],
                'booking_fee' => $BillDetails['booking_fee'],
                'cash_payment' => ($booking->PaymentMethod->payment_method_type == 1) ? $total_payable : '0.0',
                'online_payment' => ($booking->PaymentMethod->payment_method_type == 1) ? '0.0' : $total_payable,
                'customer_paid_amount' => $total_payable,
                'rounded_amount' => round_number(($total_payable - $amount))
            ]);

            $bookingDetails->total_amount = $amount;

            // company earning will deduct from driver account
            $commission_data = CommonController::NewCommission($booking->id, $booking_transaction_submit['sub_total_before_discount']);

            $booking_transaction_submit->company_earning = $commission_data['company_cut'];
            $booking_transaction_submit->driver_earning = $commission_data['driver_cut'];

            // revenue of driver
            // Driver Commission + Discount Amt + tip + toll
            $booking_transaction_submit->driver_total_payout_amount = $commission_data['driver_cut'] + $booking_transaction_submit->tip + $booking_transaction_submit->toll_amount + $booking_transaction_submit->discount_amount;

            // revenue of merchant
            // Company Commission + Tax Amt - Discount + Insurance Amt
            $booking_transaction_submit->company_gross_total = $commission_data['company_cut'] + $booking_transaction_submit->tax_amount - $booking_transaction_submit->discount_amount + $booking_transaction_submit->insurance_amount + $booking_transaction_submit['cancellation_charge_received'];

            // $booking_transaction_submit->trip_outstanding_amount = $merchant->TripCalculation(($booking_transaction_submit->driver_total_payout_amount + $booking_transaction_submit->amount_deducted_from_driver_wallet - $booking_transaction_submit->cash_payment), $merchant_id);
            $booking_transaction_submit->trip_outstanding_amount = 0;
            $booking_transaction_submit->commission_type = $commission_data['commission_type'];
            if ($booking->hotel_id != '') {
                $booking_transaction_submit->hotel_earning = $commission_data['hotel_cut'];
            }
            // $booking_transaction_submit->amount_deducted_from_driver_wallet = ($commission_data['commission_type'] == 1) ? $commission_data['company_cut'] : $merchant->TripCalculation('0.0', $merchant_id);     //Commission Type: 1:Prepaid (==OR==) 2:Postpaid
//                    $booking_transaction_submit->amount_deducted_from_driver_wallet = $commission_data['company_cut'];     //Commission Type: 1:Prepaid (==OR==) 2:Postpaid
            $booking_transaction_submit->amount_deducted_from_driver_wallet = $booking_transaction_submit->company_gross_total;

            // Instant settlement For Stripe Connect
            if ($booking->payment_method_id == 2 && isset($booking->Driver->sc_account_status) && $booking->Driver->sc_account_status == 'active') {
                $booking_transaction_submit->instant_settlement = 1;
            } else {
                $booking_transaction_submit->instant_settlement = 1;
            }
            $booking_transaction_submit->save();

            //Referral Calculation
//                    $billDetails = self::checkReferral($booking, $billDetails, $amount);

            $bookingDetails->bill_details = $billDetails;
            $bookingDetails->save();


//            $booking->cancel_reason_id = $request->cancel_reason_id;
//            $booking->additional_notes = $request->description;
            $booking->final_amount_paid = $booking->estimate_bill;
            $booking->payment_status = 1;
            $booking->booking_status = 1005;
            $booking->bill_details = $billDetails;
            $booking->save();
//            BookingRequestDriver::where("booking_id",$request->booking_id)->update(["request_status" => 4]);
            $bookingData = new BookingDataController;
            $bookingData->bookingNotificationForUser($booking,"COMPLETE_RIDE");

            $user = User::find($booking->user_id);
            $user->total_trips = $user->total_trips + 1;
            $user->save();

            $driver_id = $booking->driver_id;
            if (!empty($driver_id)) {
                $Driver = Driver::select('id','free_busy','total_trips')->where([['id', '=', $driver_id]])->first();
                $Driver->total_trips = $Driver->total_trips + 1;
                $Driver->free_busy = 2;
                $Driver->save();
                $bookingData->SendNotificationToDrivers($booking);
            }
            $booking = $booking->fresh();
            $booking_data->updateRideAmountInDriverWallet($booking, $booking_transaction_submit, $booking->id);
        }catch (\Exception $e)
        {
            DB::rollBack();
            return redirect()->route('merchant.activeride',$booking->Segment->slag)->withErrors($e->getMessage());
        }
        DB::commit();
        $string_file = $this->getStringFile($booking->merchant_id);
        return redirect()->route('merchant.activeride',$booking->Segment->slag)->withSuccess(trans("$string_file.ride_completed"));
    }

    public function requestRides($id)
    {
        $booking = Booking::find($id);
        $time = BookingConfiguration::where([['merchant_id', $booking->merchant_id]])->first();
        $time = $time->driver_request_timeout * 100 / 3;
        return view('merchant.manual.loader', compact('time', 'id'));
    }

    public function checkBookingStatusWaiting(Request $request)
    {
        $booking = Booking::find($request->booking_id);
        if (!empty($booking)) {
            if ($booking->booking_status == 1002) {
                return redirect()->route('merchant.ride-requests', $request->booking_id);
            } else {
                $time = BookingConfiguration::where([['merchant_id', $booking->merchant_id]])->first();
                $time = ($time->driver_request_timeout * 1000) / 60;
                $id = $request->booking_id;
                $time_check = session('timer_no');
                $time_check = $time_check + 1;
                $request->session()->put('timer_no', $time_check);
                $request->session()->save();
                if ($time_check == 4) {
                    $request->session()->put('timer_no', 0);
                    $request->session()->save();
                    return redirect()->route('merchant.ride-requests', $request->booking_id)->with('success', 'NO Drivers Accepted');
                } else {
                    return view('merchant.manual.loader', compact('time', 'id'));
                }
            }
        }
    }

    public function DriverRequest($id)
    {
        $booking = Booking::with(['BookingRequestDriver' => function ($query) {
            $query->with('Driver');
        }])->with('OneSignalLog')->findOrFail($id);
        return view('merchant.booking.request', compact('booking'));
    }

    public function BookingDetails(Request $request, $id)
    {
        $booking = Booking::with('User')->findOrFail($id);
        $string_file = $this->getStringFile($booking->merchant_id);
        $arr_booking_status = $this->getBookingStatus($string_file);
        $booking->map_image = $booking->map_image . "&zoom=12&size=600x300";
        $final_bill_calculation = $booking->Merchant->BookingConfiguration->final_bill_calculation;
        if($booking->family_member_id != ''){
            $booking->FamilyMember = FamilyMember::find($booking->family_member_id);
        }
        return view('merchant.booking.detail', compact('booking','arr_booking_status','final_bill_calculation'));
    }

    public function Invoice(Request $request, $id)
    {
        $booking_data = new BookingDataController;
        $request->request->add(['booking_id'=>$id]);
        $data = $booking_data->bookingReceiptForDriver($request);
//        p($data);
        $merchant = get_merchant_id(false);
        $booking = Booking::with('User', 'BookingDetail')->findOrFail($id);
        $price = json_decode($booking->BookingDetail->bill_details);
//        p($price);
        $holder = HolderController::PriceDetailHolder($price, $booking->id);
//        array_shift($holder);
        $booking->holder = $holder;
        $final_bill_calculation = $merchant->BookingConfiguration->final_bill_calculation;
        $booking->map_image = $booking->map_image . "&zoom=12&key=".$booking->Merchant->BookingConfiguration->google_key_admin.'&size=600x300';
        return view('merchant.booking.invoice', compact('booking','final_bill_calculation'));
    }

    public function bookingInvoiceSend(Request $request, $id)
    {
        $booking = Booking::where([['id', '=', $id]])->first();
        $string_file = $this->getStringFile(NULL,$booking->Merchant);
        $template = EmailConfig::where('merchant_id', '=', $booking->Merchant->id)->first();
        $temp = EmailTemplate::where('merchant_id', '=', $booking->Merchant->id)->where('template_name', '=', 'invoice')->first();
        if (!empty($template) && !empty($temp)){
            event(new SendUserInvoiceMailEvent($booking, 'invoice'));
            return redirect()->back()->withSuccess(trans("$string_file.email_sent_successfully"));
        }else{
            return redirect()->back()->withErrors(trans("$string_file.some_thing_went_wrong"));
        }
    }
    
    public function ActiveBookingTrack($id){
        $booking = Booking::where([['id', '=', $id]])->first();
        return view('merchant.booking.track', compact('booking'));
    }

    // Taxi based services Earning
    public function taxiServicesEarning(Request $request)
    {
        $checkPermission =  check_permission(1,'view_reports_charts');
        if ($checkPermission['isRedirect'])
        {
            return  $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $cancel_charges = $merchant->cancel_charges;
        // get segment list
        $arr_segment = get_merchant_segment(true,$merchant_id,1,1);
        $arr_segment_list = $arr_segment;
        $arr_segment = count($arr_segment) > 1 ? $arr_segment : [];

        //get all parameters of price cards
        $arr_parameter = PricingParameter::select('id','parameterType')
            ->whereHas('PriceCardValue',function($q) use($merchant_id,$request){
            $q->whereHas('PriceCard',function($q) use($merchant_id,$request){
                $q->where('merchant_id',$merchant_id);
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
            });
            $q->distinct('pricing_parameter_id');
        })->get();
        $arr_parameter = $arr_parameter->map(function($item){
           return [
               'id'=>$item->id,
               'parameterType'=>$item->parameterType,
               'name'=>$item->ParameterName,
               ];
        });
//        p($arr_parameter);
        $request->request->add(['search_route'=>route('merchant.taxi-services-report'),'request_from'=>"COMPLETE",'merchant_id'=>$merchant_id]);
        $arr_rides = $this->getBookings($request,$pagination = true, 'MERCHANT');
        // total fun  don't work after modification in collection
        $total_rides = $arr_rides->total();
        $arr_rides_details = $arr_rides;
        $arr_rides_details = $arr_rides_details->map(function($item) {
          $bill_details = json_decode($item->BookingDetail->bill_details);
          $invoice_data = HolderController::PriceDetailHolder($bill_details,null, NULL,'driver',$item->segment_id);
//          p($invoice_data);
          $item->invoice = $invoice_data;
            return $item;
        });
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $query = BookingTransaction::select(DB::raw('SUM(customer_paid_amount) as ride_amount'),DB::raw('SUM(company_earning) as merchant_earning'),DB::raw('SUM(driver_earning) as driver_earning'),DB::raw('SUM(business_segment_earning) as store_earning'))
            ->with(['Booking'=>function($q) use($request,$merchant_id){
             $q->where([['booking_status','=',1005],['merchant_id','=',$merchant_id]]);

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
            ->whereHas('Booking',function($q) use($request, $merchant_id, $permission_area_ids){
                $q->where([['booking_status','=',1005],['merchant_id','=',$merchant_id]]);
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
//            ->whereIn('booking_id',$arr_booking_id);
        $earning_summary = $query->first();
        $request->request->add(['request_from'=>"ride_earning","arr_segment"=>$arr_segment]);
        $search_view = $this->orderSearchView($request);
        $arr_search = $request->all();

        $currency = "";
        $info_setting = InfoSetting::where('slug', 'TAXI_LOGISTICS_SERVICE_EARNING')->first();
        return view('merchant.report.taxi-services.earning', compact('arr_rides','search_view','arr_search','earning_summary','total_rides','currency','info_setting','arr_parameter','arr_rides_details','cancel_charges','arr_segment_list'));
    }

    public function rateBooking(Request $request){
        DB::beginTransaction();
        try{
            $booking = Booking::find($request->rating_booking_id);
            $string_file = $this->getStringFile($booking->merchant_id);
            if ($booking->payment_status == 1) {
                $booking->booking_closure = 1;
                $booking->save();
            } else {
                throw new \Exception(trans("$string_file.payment_pending"));
            }
            BookingRating::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'driver_rating_points' => $request->rating,
                    'driver_comment' => $request->comment
                ]
            );
            $avg = BookingRating::whereHas('Booking', function ($q) use ($booking) {
                $q->where('user_id', $booking->user_id);
            })->avg('driver_rating_points');
            $user = $booking->User;
            $user->rating = round($avg, 2);
            $user->save();
        }catch (\Exception $e){
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
        DB::commit();
        return redirect()->back()->with('success',trans("$string_file.rating_thanks"));
    }
}
