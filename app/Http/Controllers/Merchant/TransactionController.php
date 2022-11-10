<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\BusinessSegment\OrderController;
use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\BookingTransaction;
use App\Models\BusinessSegment\BusinessSegment;
use App\Models\BusinessSegment\BusinessSegmentWalletTransaction;
use App\Models\Configuration;
use App\Models\Corporate;
use App\Models\DriverCard;
use App\Models\DriverAgency\DriverAgency;
use App\Models\Hotel;
use App\Models\InfoSetting;
use App\Models\Merchant;
use App\Models\PricingParameter;
use App\Models\TaxiCompany;
use App\Models\ReferralCompanyDiscount;
use App\Models\ReferralDriverDiscount;
use App\Models\ReferralUserDiscount;
use App\Models\User;
use App\Models\Driver;
use App\Models\Onesignal;
use App\Models\DriverWalletTransaction;
use App\Models\UserCard;
use App\Models\UserWalletTransaction;
use App\Models\UserDevice;
use Auth;
use App\Traits\BookingTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\MerchantTrait;
use DB;
use App\Models\BusinessSegment\Order;
use App\Models\HandymanOrder;
use App\Models\Transaction;

class TransactionController extends Controller
{
    use BookingTrait,MerchantTrait;

    public function index()
    {
        $checkPermission =  check_permission(1,'view_transactions');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }

        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $merchant = Merchant::find($merchant_id);
        $transactions = $this->getAllTransaction();
        foreach ($transactions as $transaction){
            $referAmount = 0;
            $companyDiscount = ReferralCompanyDiscount::where('booking_id',$transaction->id)->first();
            if (!empty($companyDiscount)){
                $referAmount = $referAmount+$companyDiscount->amount;
            }

            $driverDiscount = ReferralDriverDiscount::where('booking_id',$transaction->id)->sum('amount');
            if (!empty($driverDiscount)){
                $referAmount = $referAmount+$driverDiscount;
            }

            $userDiscount = ReferralUserDiscount::where('booking_id',$transaction->id)->sum('amount');
            if (!empty($userDiscount)){
                $referAmount = $referAmount+$userDiscount;
            }
            $transaction->referral_discount = $referAmount;
        }
        $data = [];
        $data['merchant_id'] = $merchant_id;
        return view('merchant.transaction.index', compact('transactions','merchant','data'));
    }

    public function Search(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $merchant = Merchant::find($merchant_id);

        $query = $this->getAllTransaction(false);
        if ($request->date) {
            $query->whereDate('created_at', '>=', $request->date);
        }
        if ($request->date1) {
            $query->whereDate('created_at', '<=', $request->date1);
        }
        if ($request->booking_id) {
            $query->where('id', '=', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where(\DB::raw("concat(`first_name`,' ', `last_name`)"), 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where(\DB::raw("concat(`first_name`,' ', `last_name`)"), 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $data = $request->all();
        $data['merchant_id'] = $merchant_id;
        $transactions = $query->paginate(25);
        return view('merchant.transaction.index', compact('transactions', 'merchant','data'));
    }

    public function GetBillDetails(Request $request)
    {
        $bookingDetails = BookingDetail::where([['booking_id', '=', $request->booking_id]])->first();
        $newArray = [];
        if(!empty($bookingDetails)):
            $bill_details = json_decode($bookingDetails->bill_details, true);
            if(!empty($bill_details)) {
                foreach ($bill_details as $value) {
                    $parameter = $value['parameter'];
                    $parameterDetails = PricingParameter::find($parameter);
                    if (!empty($parameterDetails)):
                        $parameterName = $parameterDetails['ParameterApplication'];
                    else:
                        $parameterName = $value['parameter'];
                    endif;
                    $newArray[] = array('name' => $parameterName, 'amount' => $value['amount']);
                }
                $booking = Booking::find($request->booking_id);
                if(isset($booking->Merchant->BookingConfiguration->final_amount_to_be_shown)){
                    $rounded_amount = isset($booking->BookingTransaction->rounded_amount) ? number_format($booking->BookingTransaction->rounded_amount,2) : 0;
                    array_push($newArray, [
                        'name' => trans("common.round").' '.trans("common.off"),
                        "amount" => $booking->CountryArea->Country->isoCode . " " . $rounded_amount,
                    ]);
                }
            }
        endif;
        echo json_encode($newArray, true);
    }
    
    public function wallet(){
        $merchant = get_merchant_id(false);
        $config = Configuration::where([['merchant_id', '=', $merchant->id]])->first();
        $receiver = [];
        $string_file = $this->getStringFile($merchant->id);
        if($config->driver_wallet_status == 1){
            $receiver = array_merge($receiver,array('DRIVER' => trans($string_file.".driver")));
        }
        if($config->user_wallet_status == 1){
            $receiver = array_merge($receiver,array('USER' => trans("$string_file.user")));
        }
        $business_segment_type_count = $merchant->Segment->where('sub_group_for_admin',2)->count();
        if($business_segment_type_count > 0){
            $receiver = array_merge($receiver,array('BUSINESS_SEGMENT' => trans($string_file.'.business_segment')));
        }
        if($config->company_admin == 1){
            $receiver = array_merge($receiver,array('TAXI_COMPANY' => trans($string_file.'.taxi_company')));
        }
        if($merchant->hotel_active == 1){
            $receiver = array_merge($receiver,array('HOTEL' => trans($string_file.'.hotel')));
        }
        if($config->corporate_admin == 1){
            $receiver = array_merge($receiver,array('CORPORATE' => trans($string_file.'.corporate')));
        }
        $no_wallet_exist = false;
        if(empty($receiver)){
            $no_wallet_exist = true;
        }
        $receiver = add_blank_option($receiver,trans("$string_file.select"));
        $info_setting = InfoSetting::where('slug', 'WALLET_RECHARGE')->first();
        return view('merchant.random.wallet_recharge',compact('config','receiver','info_setting'));
    }

    public function getWalletReceiver(Request $request){
        $validator = Validator::make($request->all(), ['application' => 'required']);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return array('result' => 'success', 'data' => $errors[0]);
        }
        try{
            $merchant = get_merchant_id(false);
            $string_file = $this->getStringFile($merchant->id);
            $receivers = "<option>".trans("$string_file.select")."</option>";
            switch ($request->application){
                case "DRIVER":
                    $drivers = Driver::where([['merchant_id', '=', $merchant->id],['taxi_company_id', '=', NULL], ['driver_delete', '=', NULL], ['reject_driver','!=',2]])->where('signupStep','=',9)->orderBy('first_name')->get();
                    foreach($drivers as $key => $value){
                        $phone = $value->phoneNumber;
                        if($merchant->demo == 1){
                            $phone = "**********".substr($phone,-3);
                        }
                        $receivers .= "<option value='".$value->id."'>".$value->fullName." (".$phone.")</option>";
                    }
                    break;
                case "USER":
                    $users = User::where([['merchant_id', '=', $merchant->id],['taxi_company_id', '=', NULL], ['user_delete', '=', NULL]])->orderBy('first_name')->get();
                    foreach($users as $key => $value){
                        $phone = $value->UserPhone;
                        if($merchant->demo == 1){
                            $phone = "**********".substr($phone,-3);
                        }
                        $receivers .= "<option value='".$value->id."'>".$value->UserName." (".$phone.")</option>";
                    }
                    break;
                case "BUSINESS_SEGMENT":
                    $business_segments = BusinessSegment::where([['merchant_id', '=', $merchant->id]])->orderBy('full_name')->get();
                    foreach($business_segments as $key => $value){
                        $phone = $value->phone_number;
//                        if($merchant->demo == 1){
//                            $phone = "**********".substr($phone,-3);
//                        }
                        $receivers .= "<option value='".$value->id."'>".$value->full_name." (".$phone.")</option>";
                    }
                    break;
                case "TAXI_COMPANY":
                    $taxi_companies = TaxiCompany::where([['merchant_id', '=', $merchant->id]])->orderBy('name')->get();
                    foreach($taxi_companies as $key => $value){
                        $phone = $value->phone;
                        if($merchant->demo == 1){
                            $phone = "**********".substr($phone,-3);
                        }
                        $receivers .= "<option value='".$value->id."'>".$value->name." (".$phone.")</option>";
                    }
                    break;
                case "HOTEL":
                    $hotels = Hotel::where([['merchant_id', '=', $merchant->id]])->orderBy('name')->get();
                    foreach($hotels as $key => $value){
                        $phone = $value->phone;
                        if($merchant->demo == 1){
                            $phone = "**********".substr($phone,-3);
                        }
                        $receivers .= "<option value='".$value->id."'>".$value->name." (".$phone.")</option>";
                    }
                    break;
                case "CORPORATE":
                    $corporates = Corporate::where([['merchant_id', '=', $merchant->id]])->orderBy('corporate_name')->get();
                    foreach($corporates as $key => $value){
                        $phone = $value->corporate_phone;
                        if($merchant->demo == 1){
                            $phone = "**********".substr($phone,-3);
                        }
                        $receivers .= "<option value='".$value->id."'>".$value->corporate_name." (".$phone.")</option>";
                    }
                    break;
                default:
                    $receivers = [];

            }
            return array('result' => 'success', 'data' => $receivers);
        }catch (\Exception $e){
            return array('result' => 'error', 'data' => $e->getMessage()) ;
        }
    }
    
    public function getDetails(Request $request){
        $validator = Validator::make($request->all(), ['application' => 'required', 'receiver_id' => 'required']);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return array('result' => 'success', 'data' => $errors[0]);
        }
        try{
            $merchant = get_merchant_id(false);
            $receiver_account = [];
            switch ($request->application){
                case "DRIVER":
                    $driver = Driver::find($request->receiver_id);
                    $iso_code = isset($driver->CountryArea->Country->isoCode) ? $driver->CountryArea->Country->isoCode : "";
                    $receiver_account = array(
                        'id' => $driver->id,
                        'full_name' => $driver->fullName,
                        'phone' => ($merchant->demo == 1) ? "**********".substr($driver->phoneNumber,-3) : $driver->phoneNumber,
                        'email' => ($merchant->demo == 1) ? "**********".substr($driver->email,-3) : $driver->email,
                        'wallet' => ($driver->wallet_money > 0) ? $iso_code." ".$driver->wallet_money : $iso_code." 0.0",
                    );
                    break;
                case "USER":
                    $user = User::find($request->receiver_id);
                    $iso_code = isset($user->CountryArea->Country->isoCode) ? $user->CountryArea->Country->isoCode : "";
                    $receiver_account = array(
                        'id' => $user->id,
                        'full_name' => $user->UserName,
                        'phone' => ($merchant->demo == 1) ? "**********".substr($user->UserPhone,-3) : $user->UserPhone,
                        'email' => ($merchant->demo == 1) ? "**********".substr($user->email,-3) : $user->email,
                        'wallet' => ($user->wallet_balance > 0) ? $iso_code." ". $user->wallet_balance : $iso_code." 0.0",
                    );
                    break;
                case "BUSINESS_SEGMENT":
                    $business_segment = BusinessSegment::find($request->receiver_id);
                    $receiver_account = array(
                        'id' => $business_segment->id,
                        'full_name' => $business_segment->full_name,
                        'phone' => ($merchant->demo == 1) ? "**********".substr($business_segment->phone_number,-3) : $business_segment->phone_number,
                        'email' => ($merchant->demo == 1) ? "**********".substr($business_segment->email,-3) : $business_segment->email,
                        'wallet' => ($business_segment->wallet_amount > 0) ? $business_segment->Country->isoCode." ".$business_segment->wallet_amount : $business_segment->Country->isoCode." 0.0",
                    );
                    break;
                case "TAXI_COMPANY":
                    $taxi_company = TaxiCompany::find($request->receiver_id);
                    $receiver_account = array(
                        'id' => $taxi_company->id,
                        'full_name' => $taxi_company->name,
                        'phone' => ($merchant->demo == 1) ? "**********".substr($taxi_company->phone,-3) : $taxi_company->phone,
                        'email' => ($merchant->demo == 1) ? "**********".substr($taxi_company->email,-3) : $taxi_company->email,
                        'wallet' => ($taxi_company->wallet_money > 0) ? $taxi_company->Country->isoCode." ". $taxi_company->wallet_money : $taxi_company->Country->isoCode." 0.0",
                    );
                    break;
                case "HOTEL":
                    $hotel = Hotel::find($request->receiver_id);
                    $receiver_account = array(
                        'id' => $hotel->id,
                        'full_name' => $hotel->name,
                        'phone' => ($merchant->demo == 1) ? "**********".substr($hotel->phone,-3) : $hotel->phone,
                        'email' => ($merchant->demo == 1) ? "**********".substr($hotel->email,-3) : $hotel->email,
                        'wallet' => ($hotel->wallet_money > 0) ? $hotel->Country->isoCode." ". $hotel->wallet_money : $hotel->Country->isoCode." 0.0",
                    );
                    break;
                case "CORPORATE":
                    $corporate = Corporate::find($request->receiver_id);
                    $receiver_account = array(
                        'id' => $corporate->id,
                        'full_name' => $corporate->corporate_name,
                        'phone' => ($merchant->demo == 1) ? "**********".substr($corporate->corporate_phone,-3) : $corporate->corporate_phone,
                        'email' => ($merchant->demo == 1) ? "**********".substr($corporate->email,-3) : $corporate->email,
                        'wallet' => ($corporate->wallet_balance > 0) ? $corporate->Country->isoCode." ". $corporate->wallet_balance : $corporate->Country->isoCode." 0.0",
                    );
                    break;
                default:
                    return array('result' => 'error', 'data' => 'Invalid receiver type');
            }
            return array('result' => 'success', 'data' => $receiver_account);
        }catch (\Exception $e){
            return array('result' => 'error', 'data' => $e->getMessage()) ;
        }

    }
    
    public function walletRecharge(Request $request){
        $merchant_id = get_merchant_id();
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|integer|between:1,2',
            'receipt_number' => 'required|string',
            'transaction_type' => 'required',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
            'application' => 'required',
            'receiver_id' => 'required'
        ]);
        // $validator=$request->validate([
        //     'payment_method' => 'required|integer|between:1,2',
        //     'receipt_number' => 'required|string',
        //     'amount' => 'required|numeric',
        //     'description' => 'required|string',
        //     'application' => 'required',
        //     'receiver_id' => 'required'
        // ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        $string_file = $this->getStringFile($merchant_id);
        try{
            switch ($request->application){
                case "DRIVER":
                    $driver = Driver::find($request->receiver_id);
                    $paramArray = array(
                        'driver_id' => $driver->id,
                        'booking_id' => NULL,
                        'amount' => $request->amount,
                        'narration' => 1,
                        'platform' => 1,
                        'payment_method' => $request->payment_method,
                        'receipt' => $request->receipt_number,
                        'action_merchant_id' => Auth::user('merchant')->id
                    );
                    if($request->transaction_type == 1){
                        WalletTransaction::WalletCredit($paramArray);
                    }else{
                        $paramArray['narration'] = 18;
                        WalletTransaction::WalletDeduct($paramArray);
                    }
                    break;
                case "USER":
                    $user = User::find($request->receiver_id);
                    $paramArray = array(
                        'user_id' => $user->id,
                        'booking_id' => NULL,
                        'amount' => $request->amount,
                        'narration' => 1,
                        'platform' => 1,
                        'payment_method' => $request->payment_method,
                        'receipt' => $request->receipt_number,
                        'action_merchant_id' => Auth::user('merchant')->id
                    );
                    if($request->transaction_type == 1){
                        WalletTransaction::UserWalletCredit($paramArray);
                    }else{
                        $paramArray['narration'] = 14;
                        WalletTransaction::UserWalletDebit($paramArray);
                    }
                    break;
                case "BUSINESS_SEGMENT":
                    $business_segment = BusinessSegment::find($request->receiver_id);
                    $paramArray = array(
                        'business_segment_id' => $business_segment->id,
                        'order_id' => NULL,
                        'amount' => $request->amount,
                        'narration' => 1,
                        'platform' => 1,
                        'payment_method' => $request->payment_method,
                        'receipt' => $request->receipt_number,
                        'action_merchant_id' => Auth::user('merchant')->id
                    );
                    if($request->transaction_type == 1){
                        WalletTransaction::BusinessSegmntWalletCredit($paramArray);
                    }else{
                        $paramArray['narration'] = 6;
                        WalletTransaction::BusinessSegmntWalletDebit($paramArray);
                    }
                    break;
                case "TAXI_COMPANY":
                    $taxi_company = TaxiCompany::find($request->receiver_id);
                    if($request->transaction_type == 1){
                        WalletTransaction::TaxiComapnyWalletCredit($taxi_company->id,$request->amount,$request->payment_method,$request->receipt_number);
                    }else{
                        $description = trans("$string_file.amount_debited_by_admin");
                        WalletTransaction::TaxiComapnyWalletDeduct($taxi_company->id,null,$request->amount,$request->payment_method,$request->receipt_number,$description);
                    }
                    break;
                case "HOTEL":
                    $hotel = Hotel::find($request->receiver_id);
                    if($request->transaction_type == 1){
                        WalletTransaction::HotelWalletAdded($hotel->id,NULL,$request->amount,$request->receipt_number,$request->description);
                    }else{
                        $description = trans("$string_file.amount_debited_by_admin");
                        WalletTransaction::HotelWalletDeduct($hotel->id,NULL,$request->amount,$request->receipt_number,$description);
                    }
                    break;
                case "CORPORATE":
                    $corporate = Corporate::find($request->receiver_id);
                    if($request->transaction_type == 1){
                        WalletTransaction::CorporateWaletCredit($corporate->id,$request->amount,$request->payment_method,$request->receipt_number,$request->description);
                    }else{
                        $description = trans("$string_file.amount_debited_by_admin");
                        WalletTransaction::CorporateWaletDebit($corporate->id,$request->amount,$request->payment_method,$request->receipt_number,$description);
                    }
                    break;
                default:
                    return array('result' => 'error', 'data' => 'Invalid receiver type');
            }
        }catch (\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
        }
        if($request->transaction_type == 1){
            $transaction_type = trans("$string_file.credited");
        }else{
            $transaction_type = trans("$string_file.debited");
        }
        return redirect()->back()->withSuccess(trans("$string_file.money_added_in_wallet"));
    }

    public function TaxiCompanyTransaction($id = NULL)
    {
//        $checkPermission =  check_permission(1,'view_transactions');
//        if ($checkPermission['isRedirect']){
//            return  $checkPermission['redirectBack'];
//        }
        if($id != NULL){
            $merchant = get_merchant_id(false);
            $merchant_id = $merchant->id;
            $string_file = $this->getStringFile(NULL,$merchant);
            $taxi_company = TaxiCompany::find($id);
            if(!empty($taxi_company)){
                $where = [['merchant_id', '=', $merchant_id],['taxi_company_id', '=', $taxi_company->id],['booking_closure', '=', 1]];
                $query = Booking::where($where)->latest();
                if (!empty($merchant->CountryArea->toArray())) {
                    $area_ids = array_pluck($merchant->CountryArea, 'id');
                    $query->whereIn('country_area_id', $area_ids);
                }
                $transactions = $query->paginate(25);
                $string_file = $this->getStringFile($merchant_id);
                return view('merchant.transaction.taxi-company', compact('transactions','merchant','taxi_company','string_file'));
            }else{
                return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
            }
        }else {
            return redirect()->back();
        }
    }

    public function TaxiCompanySearch(Request $request, $id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $merchant = Merchant::find($merchant_id);
        $taxi_company = TaxiCompany::find($id);

        $where = [['merchant_id', '=', $merchant_id],['taxi_company_id', '=', $id],['booking_closure', '=', 1]];
        $query = Booking::where($where)->latest();
        if (!empty($merchant->CountryArea->toArray())) {
            $area_ids = array_pluck($merchant->CountryArea, 'id');
            $query->whereIn('country_area_id', $area_ids);
        }
        $query->paginate(25);
        if ($request->date) {
            $query->whereDate('created_at', '>=', $request->date);
        }
        if ($request->date1) {
            $query->whereDate('created_at', '<=', $request->date1);
        }
        if ($request->booking_id) {
            $query->where('id', '=', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where(\DB::raw('concat(`first_name`, `last_name`)'), 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where(\DB::raw('concat(`first_name`, `last_name`)'), 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $transactions = $query->paginate(25);
        return view('merchant.transaction.taxi-company', compact('transactions', 'merchant', 'taxi_company'));
    }

    public function HotelTransaction($id = NULL)
    {
        $checkPermission =  check_permission(1,'view_transactions');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        if($id != NULL){
            $merchant = get_merchant_id(false);
            $merchant_id = $merchant->id;
            $hotel = Hotel::find($id);
            if(!empty($hotel)){
                $where = [['merchant_id', '=', $merchant_id],['hotel_id', '=', $hotel->id],['booking_closure', '=', 1]];
                $query = Booking::where($where)->latest();
                $transactions = $query->paginate(25);
                return view('merchant.transaction.hotel', compact('transactions','merchant','hotel'));
            }
        }else {
            return redirect()->back();
        }
    }

    public function HotelSearch(Request $request, $id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $merchant = Merchant::find($merchant_id);
        $hotel = Hotel::find($id);

        $where = [['merchant_id', '=', $merchant_id],['hotel_id', '=', $id],['booking_closure', '=', 1]];
        $query = Booking::where($where)->latest();
        if (!empty($merchant->CountryArea->toArray())) {
            $area_ids = array_pluck($merchant->CountryArea, 'id');
            $query->whereIn('country_area_id', $area_ids);
        }
        $query->paginate(25);
        if ($request->date) {
            $query->whereDate('created_at', '>=', $request->date);
        }
        if ($request->date1) {
            $query->whereDate('created_at', '<=', $request->date1);
        }
        if ($request->booking_id) {
            $query->where('id', '=', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where(\DB::raw('concat(`first_name`, `last_name`)'), 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where(\DB::raw('concat(`first_name`, `last_name`)'), 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $transactions = $query->paginate(25);
        return view('merchant.transaction.hotel', compact('transactions', 'merchant', 'hotel'));
    }

    public function walletReport(Request $request, $slug){
        $checkPermission =  check_permission(1,'view_reports_charts');
        if ($checkPermission['isRedirect'])
        {
            return  $checkPermission['redirectBack'];
        }
        try{
            $merchant = get_merchant_id(false);
            $merchant_id = $merchant->id;
            $merchant_name = $merchant->merchantFirstName." ".$merchant->merchantLastName;
            $string_file = $this->getStringFile($merchant_id);
            $wallet_transactions = [];
            $page_title = "";
            switch ($slug){
                case "USER":
                    $wallet_transactions = UserWalletTransaction::where(function($query) use($request){
                        if(isset($request->start) && !empty($request->start)){
                            $start_date = date('Y-m-d',strtotime($request->start));
                            $end_date = date('Y-m-d ',strtotime($request->end));
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                        }
                    })->where("merchant_id",$merchant_id)->whereHas("User")->latest()->paginate(10);
                    foreach($wallet_transactions as $transaction){
                            $id = NULL;
                            if(!empty($transaction->booking_id))
                            {
                                $booking = Booking::select('merchant_booking_id')->find($transaction->booking_id);
                                $id = $booking->merchant_booking_id;
                            }
                            elseif(!empty($transaction->order_id))
                            {
                                $order = Order::select('merchant_order_id')->find($transaction->order_id);
                                $id = $order->merchant_order_id;
                            }
                            elseif(!empty($transaction->handyman_order_id))
                            {
                                $order = HandymanOrder::select('merchant_order_id')->find($transaction->handyman_order_id);
                                $id = $order->merchant_order_id;
                            }
                            $transaction->platform = ($transaction->platfrom == 1) ? trans("$string_file.by")." ".trans("$string_file.admin") : trans("$string_file.by")." ".trans("$string_file.app");
                            $transaction->payment_method = ($transaction->payment_method == 1) ? trans("$string_file.cash") : trans("$string_file.non_cash");
                            $transaction->transaction_type = ($transaction->type == 1) ? trans("$string_file.credit") : trans("$string_file.debit");
                            $transaction->user_name = $transaction->User->UserName;
                            $transaction->user_phone = $transaction->User->UserPhone;
                            $transaction->user_email = $transaction->User->email;
                            $transaction->action_merchant_name = isset($transaction->ActionMerchant) ? $transaction->ActionMerchant->merchantFirstName." ".$transaction->ActionMerchant->merchantLastName : $merchant_name;
                            $transaction->narration = isset($transaction->narration) ? get_narration_value("USER",$transaction->narration,$transaction->merchant_id,$id) : "";
                    }
                    $page_title = trans("$string_file.user");
                    break;
                case "DRIVER":
                    $permission_area_ids = [];
                    if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
                        $permission_area_ids = explode(",",Auth::user()->role_areas);
                    }
                    $wallet_transactions = DriverWalletTransaction::where(function($query) use($request){
                        if(isset($request->start) && !empty($request->start)){
                            $start_date = date('Y-m-d',strtotime($request->start));
                            $end_date = date('Y-m-d ',strtotime($request->end));
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                        }
                    })->where("merchant_id",$merchant_id)->whereHas("Driver",function ($q) use($permission_area_ids){
                        if(!empty($permission_area_ids)){
                            $q->whereIn("country_area_id",$permission_area_ids);
                        }
                    })->latest()->paginate(10);
                    foreach($wallet_transactions as $transaction){
                        $id = NULL;
                        if(!empty($transaction->booking_id))
                        {
                            $booking = Booking::select('merchant_booking_id')->find($transaction->booking_id);
                            $id = $booking->merchant_booking_id;
                        }
                        elseif(!empty($transaction->order_id))
                        {
                            $order = Order::select('merchant_order_id')->find($transaction->order_id);
                            $id = $order->merchant_order_id;
                        }
                        elseif(!empty($transaction->handyman_order_id))
                        {
                            $order = HandymanOrder::select('merchant_order_id')->find($transaction->order_id);
                            $id = $order->merchant_order_id;
                        }
                        $transaction->platform = ($transaction->platform == 1) ? trans("$string_file.by")." ".trans("$string_file.admin") : trans("$string_file.by")." ".trans("$string_file.app");
                        $transaction->payment_method = ($transaction->payment_method == 1) ? trans("$string_file.cash") : trans("$string_file.non_cash");
                        $transaction->transaction_type = ($transaction->transaction_type == 1) ? trans("$string_file.credit") : trans("$string_file.debit");
                        $transaction->user_name = $transaction->Driver->fullName;
                        $transaction->user_phone = $transaction->Driver->phoneNumber;
                        $transaction->user_email = $transaction->Driver->email;
                        $transaction->action_merchant_name = isset($transaction->ActionMerchant) ? $transaction->ActionMerchant->merchantFirstName." ".$transaction->ActionMerchant->merchantLastName : $merchant_name;
                        $transaction->narration = isset($transaction->narration) ? get_narration_value("DRIVER",$transaction->narration,$transaction->merchant_id,$id) : "";

                    }
                    $page_title = trans("$string_file.driver");
                    break;
                case "BUSINESS-SEGMENT":
                    $wallet_transactions = BusinessSegmentWalletTransaction::where(function($query) use($request){
                        if(isset($request->start) && !empty($request->start)){
                            $start_date = date('Y-m-d',strtotime($request->start));
                            $end_date = date('Y-m-d ',strtotime($request->end));
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                        }
                    })->where("merchant_id",$merchant_id)->whereHas("BusinessSegment")->latest()->paginate(10);
                    foreach($wallet_transactions as $transaction){
                        $id = NULL;
                        if(!empty($transaction->order_id))
                        {
                            $order = Order::select('merchant_order_id')->find($transaction->order_id);
                            $id = $order->merchant_order_id;
                        }
                        $transaction->platform = ($transaction->platform == 1) ? trans("$string_file.by")." ".trans("$string_file.admin") : trans("$string_file.by")." ".trans("$string_file.app");
                        $transaction->payment_method = ($transaction->payment_method == 1) ? trans("$string_file.cash") : trans("$string_file.non_cash");
                        $transaction->transaction_type = ($transaction->transaction_type == 1) ? trans("$string_file.credit") : trans("$string_file.debit");
                        $transaction->user_name = $transaction->BusinessSegment->full_name;
                        $transaction->user_phone = $transaction->BusinessSegment->phone_number;
                        $transaction->user_email = $transaction->BusinessSegment->email;
                        $transaction->action_merchant_name = isset($transaction->ActionMerchant) ? $transaction->ActionMerchant->merchantFirstName." ".$transaction->ActionMerchant->merchantLastName : $merchant_name;
                        $transaction->narration = isset($transaction->narration) ? get_narration_value('BUSINESS_SEGMENT',$transaction->narration,$transaction->merchant_id,$id,NULL) : "";

                    }
                    $page_title = trans("$string_file.business_segment");
                    break;
                default:
                    return redirect()->back()->withErrors(trans("$string_file.invalid")." ".trans("$string_file.slug"));
            }
            $page_title = $page_title.' '.trans("$string_file.wallet")." ".trans("$string_file.transaction");
            $data = $request->all();
            $data['slug'] = $slug;
            return view('merchant.report.wallet_report', compact('wallet_transactions', 'page_title','data','slug'));
        }catch (\Exception $exception){
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }

    public function walletReportExport(Request $request){
        try{
            $merchant = get_merchant_id(false);
            $merchant_id = $merchant->id;
            $merchant_name = $merchant->merchantFirstName." ".$merchant->merchantLastName;
            $string_file = $this->getStringFile($merchant_id);
            $wallet_transactions = [];
            switch ($request->slug){
                case "USER":
                    $wallet_transactions = UserWalletTransaction::where(function($query) use($request){
                        if(isset($request->start) && !empty($request->start)){
                            $start_date = date('Y-m-d',strtotime($request->start));
                            $end_date = date('Y-m-d ',strtotime($request->end));
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                        }
                    })->where("merchant_id",$merchant_id)->whereHas("User")->get();
                    foreach($wallet_transactions as $transaction){
                        $transaction->platform = ($transaction->platfrom == 1) ? trans("$string_file.by")." ".trans("$string_file.admin") : trans("$string_file.by")." ".trans("$string_file.app");
                        $transaction->payment_method = ($transaction->payment_method == 1) ? trans("$string_file.cash") : trans("$string_file.non_cash");
                        $transaction->transaction_type = ($transaction->type == 1) ? trans("$string_file.credit") : trans("$string_file.debit");
                        $transaction->user_name = $transaction->User->UserName;
                        $transaction->user_phone = $transaction->User->UserPhone;
                        $transaction->user_email = $transaction->User->email;
                        $transaction->action_merchant_name = isset($transaction->ActionMerchant) ? $transaction->ActionMerchant->merchantFirstName." ".$transaction->ActionMerchant->merchantLastName : $merchant_name;
                    }
                    break;
                case "DRIVER":
                    $wallet_transactions = DriverWalletTransaction::where(function($query) use($request){
                        if(isset($request->start) && !empty($request->start)){
                            $start_date = date('Y-m-d',strtotime($request->start));
                            $end_date = date('Y-m-d ',strtotime($request->end));
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                        }
                    })->where("merchant_id",$merchant_id)->whereHas("Driver")->get();
                    foreach($wallet_transactions as $transaction){
                        $transaction->platform = ($transaction->platform == 1) ? trans("$string_file.by")." ".trans("$string_file.admin") : trans("$string_file.by")." ".trans("$string_file.app");
                        $transaction->payment_method = ($transaction->payment_method == 1) ? trans("$string_file.cash") : trans("$string_file.non_cash");
                        $transaction->transaction_type = ($transaction->transaction_type == 1) ? trans("$string_file.credit") : trans("$string_file.debit");
                        $transaction->user_name = $transaction->Driver->fullName;
                        $transaction->user_phone = $transaction->Driver->phoneNumber;
                        $transaction->user_email = $transaction->Driver->email;
                        $transaction->action_merchant_name = isset($transaction->ActionMerchant) ? $transaction->ActionMerchant->merchantFirstName." ".$transaction->ActionMerchant->merchantLastName : $merchant_name;
                    }
                    break;
                case "BUSINESS-SEGMENT":
                    $wallet_transactions = BusinessSegmentWalletTransaction::where(function($query) use($request){
                        if(isset($request->start) && !empty($request->start)){
                            $start_date = date('Y-m-d',strtotime($request->start));
                            $end_date = date('Y-m-d ',strtotime($request->end));
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                        }
                    })->where("merchant_id",$merchant_id)->whereHas("BusinessSegment")->get();
                    foreach($wallet_transactions as $transaction){
                        $transaction->platform = ($transaction->platform == 1) ? trans("$string_file.by")." ".trans("$string_file.admin") : trans("$string_file.by")." ".trans("$string_file.app");
                        $transaction->payment_method = ($transaction->payment_method == 1) ? trans("$string_file.cash") : trans("$string_file.non_cash");
                        $transaction->transaction_type = ($transaction->transaction_type == 1) ? trans("$string_file.credit") : trans("$string_file.debit");
                        $transaction->user_name = $transaction->BusinessSegment->full_name;
                        $transaction->user_phone = $transaction->BusinessSegment->phone_number;
                        $transaction->user_email = $transaction->BusinessSegment->email;
                        $transaction->action_merchant_name = isset($transaction->ActionMerchant) ? $transaction->ActionMerchant->merchantFirstName." ".$transaction->ActionMerchant->merchantLastName : $merchant_name;
                    }
                    break;
                default:
                    return redirect()->back()->withErrors(trans("$string_file.invalid")." ".trans("$string_file.slug"));
            }
            $data = $request->all();
//            p($wallet_transactions);
            $csvExporter = new \Laracsv\Export();
            $csvExporter->beforeEach(function ($wallet_transactions) {
                $wallet_transactions->name = $wallet_transactions->user_name;
                $wallet_transactions->phone = $wallet_transactions->user_phone;
                $wallet_transactions->email = $wallet_transactions->user_email;
                $wallet_transactions->transaction_date = date('H:i',strtotime($wallet_transactions->created_at)).', '.date_format($wallet_transactions->created_at,'D, M d, Y');
                $wallet_transactions->transaction_from = $wallet_transactions->platfrom;
                $wallet_transactions->transaction_by = $wallet_transactions->action_merchant_name;
            });

            $csvExporter->build($wallet_transactions,
                [
                    'id' => trans("$string_file.id"),
                    'name' => trans("$string_file.receiver_name"),
                    'phone' => trans("$string_file.receiver_phone"),
                    'email' => trans("$string_file.receiver_email"),
                    'amount' => trans("$string_file.amount"),
                    'transaction_type' => trans("$string_file.transaction_type"),
                    'transaction_date' => trans("$string_file.date"),
                    'transaction_from' => trans("$string_file.transaction_from"),
                    'transaction_by' => trans("$string_file.transaction_by"),
                ]
            )->download($request->slug.'wallet-transaction' . time() . '.csv');

        }catch (\Exception $exception){
            p($exception->getMessage());
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }


    // net earning of merchant
    public function merchantNetEarning(Request $request){

        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $all_transactions = BookingTransaction::
        where('merchant_id',$merchant_id)->
        latest()->paginate(25);
        $currency = "";
        $data['merchant_id'] = $merchant_id;
        $data['currency'] = $currency;
        $data['all_transactions'] = $all_transactions;
        $data['arr_search'] = [];
        $request->request->add(['search_route'=>route('merchant.net.earning')]);
        $arr_bs = [];
        $request->request->add(['calling_view'=>"earning","arr_segment"=>[],"arr_bs"=>$arr_bs]);
        $data['info_setting'] = InfoSetting::where('slug', 'DELIVERY_SERVICE_EARNING')->first();
        return view('merchant.report.merchant-net-earning')->with($data);
    }

    public function PaymentGatewayTransactions(Request $request)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $transactions = Transaction::where('merchant_id',$merchant_id)->orderBy('id','DESC')->paginate(25);
        return view('merchant.payment_transaction.index', compact('transactions'));
    }

    public function GetCardDetails(Request $request)
    {
        $merchant_id = get_merchant_id(true);
        $string_file = $this->getStringFile($merchant_id);
        try {
            $is_user = $request->is_user;
            $card_id = $request->card_id;
            $card = $is_user ? UserCard::find($card_id) : DriverCard::find($card_id);
            if(!empty($card)) {
                echo '<div>'.'************'.substr($card->card_number, -4).'<br>'.$card->exp_month.'/'.$card->exp_year.(isset($card->card_type) ? ' ('.$card->card_type.')' : '').'</div>';
            } else {
                echo trans("$string_file.no_card");
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function DriverAgencyTransaction($id = NULL)
    {
        if($id != NULL){
            $merchant = get_merchant_id(false);
            $merchant_id = $merchant->id;
            $string_file = $this->getStringFile(NULL,$merchant);
            $driver_agency = DriverAgency::find($id);
            if(!empty($driver_agency)){
//                $where = [['merchant_id', '=', $merchant_id],['driver_agency_id', '=', $driver_agency->id],['booking_closure', '=', 1]];
//                $query = Booking::where($where)->latest();
//                if (!empty($merchant->CountryArea->toArray())) {
//                    $area_ids = array_pluck($merchant->CountryArea, 'id');
//                    $query->whereIn('country_area_id', $area_ids);
//                }
                $transactions = [];
//                    $query->paginate(25);
                $string_file = $this->getStringFile($merchant_id);
                return view('merchant.transaction.driver-agency', compact('transactions','merchant','driver_agency','string_file'));
            }else{
                return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
            }
        }else {
            return redirect()->back();
        }
    }
}
