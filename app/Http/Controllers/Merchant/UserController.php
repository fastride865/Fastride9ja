<?php

namespace App\Http\Controllers\Merchant;

use App\Events\UserSignupWelcome;
use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Http\Requests\UserRequest;
use App\Models\CountryArea;
use App\Traits\BookingTrait;
use App\Traits\MerchantTrait;
use App\Models\ApplicationConfiguration;
use App\Models\Booking;
use App\Models\Configuration;
use App\Models\Corporate;
use App\Models\Country;
use App\Models\Driver;
use App\Models\InfoSetting;
use App\Models\Merchant;
use App\Models\Onesignal;
use App\Models\RejectReason;
use App\Models\TaxiCompany;
use App\Models\UserDocument;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserWalletTransaction;
use App\Models\ReferralDiscount;
use App\Traits\ImageTrait;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use DB;
use Session;


class UserController extends Controller
{
    use ImageTrait, MerchantTrait, BookingTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'USER')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $checkPermission =  check_permission(1,'view_rider');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $merchant = Merchant::find($merchant_id);
        $config = $merchant->Configuration;
        $countries = $merchant->Country;
        $config->user_document = $merchant->ApplicationConfiguration->user_document;
        $appconfig = $merchant->ApplicationConfiguration;
        $config->gender = $appconfig->gender;
        $permission_country_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
            if(!empty($permission_area_ids)){
                $permission_country_ids = CountryArea::whereIn("id",$permission_area_ids)->get()->pluck("country_id")->toArray();
                if(!empty($permission_country_ids)){
                    $permission_country_ids = array_unique($permission_country_ids);
                }
            }
        }
        $users = User::where([['merchant_id', '=', $merchant_id], ['taxi_company_id', '=', NULL], ['user_delete', '=', NULL]])->where(function ($q) use($permission_country_ids){
            if(!empty($permission_country_ids)){
                $q->where("country_id",$permission_country_ids);
            }
        })->latest()->paginate(10);
        $pending_user = User::where([['merchant_id', '=', $merchant_id], ['taxi_company_id', '=', NULL], ['user_delete', '=', NULL], ['signup_status', '=', 1]])->count();
        $data = [];
        $data['export_search'] = [];
        $data['merchant_id'] = $merchant_id;
        return view('merchant.user.index', compact('users', 'config', 'pending_user', 'countries', 'merchant','data'));
    }

    public function create()
    {
        $checkPermission =  check_permission(1,'create_rider');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
        $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $corporates = Corporate::where([['merchant_id', '=', $merchant_id]])->get();
        $appConfig = ApplicationConfiguration::where('merchant_id','=',$merchant_id)->first();
        return view('merchant.user.create', compact('corporates', 'countries', 'config','appConfig'));
    }

    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $merchant = get_merchant_id(false);
            $string_file = $this->getStringFile(NULL,$merchant);
            $merchant_id = $merchant->id;
            $country = explode("|", $request->country);
            $user = new User();
            $user = User::create([
                'merchant_id' => $merchant_id,
                'country_id' => $country[0],
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'UserPhone' => $request->phone,
                'email' => $request->user_email,
                'password' => Hash::make($request->password),
                'UserSignupType' => 1,
                'UserSignupFrom' => 2,
                'ReferralCode' => $user->GenrateReferCode(),
                'UserProfileImage' => $this->uploadImage('profile','user'),
                'user_type' => $request->rider_type,
                'user_gender' => $request->user_gender,
                'corporate_id' => $request->corporate_id,
                'corporate_email' => $request->corporate_email,
                'smoker_type' => $request->smoker_type,
                'allow_other_smoker' => $request->allow_other_smoker,
            ]);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        // Commit Transaction
        DB::commit();
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function show($id)
    {
        $merchant_id = get_merchant_id();
        $user = User::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $bookings = Booking::where([['user_id', '=', $id]])->whereIn('booking_status', [1005])->orderBy("created_at",'desc')->paginate(5);
        $appConfig = ApplicationConfiguration::where('merchant_id','=',$merchant_id)->first();
        $string_file = $this->getStringFile($merchant_id);
        $arr_booking_status = $this->getBookingStatus($string_file);
        return view('merchant.user.show', compact('user', 'bookings','appConfig','arr_booking_status'));
    }

    public function FavouriteLocation($id)
    {
        $merchant_id = get_merchant_id();
        $user = User::where([['merchant_id', '=', $merchant_id]])->with('UserAddress')->findOrFail($id);
        return view('merchant.user.favourite', compact('user'));
    }

    public function FavouriteDriver($id)
    {
        $merchant_id = get_merchant_id();
        $user = User::where([['merchant_id', '=', $merchant_id]])->with(['FavouriteDriver' => function ($query) {
            $query->with(['Driver' => function ($q) {
                $q->where(function ($qq) {
                    $qq->where('driver_delete','=',NULL);
                    $qq->where('driver_admin_status','=',1);
                });
                $q->with('CountryArea');
            }]);
            $query->whereHas('Driver', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('driver_delete','=',NULL);
                    $qq->where('driver_admin_status','=',1);
                });
            });
        }])->findOrFail($id);
        return view('merchant.user.favourite-drivers', compact('user'));
    }

    public function edit($id)
    {
        $checkPermission =  check_permission(1,'edit_rider');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $user = User::where([['merchant_id', '=', $merchant_id], ['user_delete', '=', NULL]])->findOrFail($id);
        $appConfig = ApplicationConfiguration::where('merchant_id','=',$merchant_id)->first();
        return view('merchant.user.edit', compact('user', 'config','appConfig'));
    }

    public function update(Request $request, $id)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $request->validate([
            'first_name' => "required",
            'user_email' => "required|email|max:255|unique:users,email,".$id.",id,merchant_id,".$merchant_id.",user_delete,NULL",
            'user_phone' => "required|regex:/^[0-9+]+$/|unique:users,UserPhone,".$id.",id,merchant_id,".$merchant_id.",user_delete,NULL",
            'password' => 'required_if:edit_password,1'
        ]);

        DB::beginTransaction();

        try
        {
        $user = User::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->UserPhone = $request->isd.$request->user_phone;
        $user->email = $request->user_email;
        $user->user_gender = $request->user_gender;
        $user->smoker_type = $request->smoker_type;
        $user->allow_other_smoker = $request->allow_other_smoker;
        if ($request->edit_password == 1) {
            $user->password = Hash::make($request->password);
        }
        if ($request->hasFile('profile')) {
            $user->UserProfileImage = $this->uploadImage('profile','user');
        }
        $user->save();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        // Commit Transaction
        DB::commit();
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
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

        $user = User::findOrFail($id);
        $merchant = $user->Merchant;
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $user->UserStatus = $status;
        $user->save();
        $data = ['status' => $status];
        setLocal($user->language);
        $message = $status == 2 ? trans("$string_file.account_has_been_inactivated") : trans("$string_file.account_has_been_activated");
        $title = trans("$string_file.account_inactivated");
        $data['notification_type'] ="ACCOUNT_INACTIVATED";
        $data['segment_type'] = "";
        $data['segment_data'] = ['user_id'=>$id];
        $arr_param = ['user_id'=>$id,'data'=>$data,'message'=>$message,'merchant_id'=>$merchant_id,'title'=>$title,'large_icon'=>''];
        Onesignal::UserPushMessage($arr_param);
        setLocal();
        return redirect()->route('users.index')->withSuccess(trans("$string_file.status_updated"));
    }

    public function Wallet($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $user = User::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $wallet_transactions = UserWalletTransaction::where([['user_id', '=', $id]])->paginate(25);
        return view('merchant.user.wallet', compact('wallet_transactions', 'user'));
    }

    public function AddWalletMoney(Request $request)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $validator = Validator::make($request->all(), [
            'add_money_user_id' => 'required',
            'transaction_type' => 'required',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|integer|between:1,2',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        // $request->validate([
        //     'add_money_user_id' => 'required',
        //     'amount' => 'required|numeric',
        //     'payment_method' => 'required|integer|between:1,2',
        // ]);
//        $amount = number_format((float)$request->amount, 2, '.', '');
        $paramArray = array(
            'user_id' => $request->add_money_user_id,
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
//        CommonController::UserWalletCredit($request->add_money_user_id,NULL,$request->amount,1,1,$request->payment_method,$request->receipt_number);
//        $user = User::findOrFail($request->add_money_user_id);
//        $wallet = $user->wallet_balance;
//        $total = $wallet + $amount;
//        $user->wallet_balance = number_format((float)$total, 2, '.', '');;
//        $user->save();
//        UserWalletTransaction::create([
//            'merchant_id' => $merchant_id,
//            'user_id' => $request->add_money_user_id,
//            'platfrom' => 1,
//            'amount' => sprintf("%0.2f", $request->amount),
//            'payment_method' => $request->payment_method,
//            'receipt_number' => $request->receipt_number,
//            'description' => $request->description,
//            'type' => 1,
//        ]);
//        $message = trans('api.money');
//        $data = ['message' => $message];
//        Onesignal::UserPushMessage($request->add_money_user_id, $data, $message, 3, $merchant_id);
        return redirect()->route('users.index')->withSuccess(trans("$string_file.money_added_successfully"));
    }

    public function Serach(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        switch ($request->parameter) {
            case "1":
                $parameter = "first_name";
                break;
            case "2":
                $parameter = "email";
                break;
            case "3":
                $parameter = "UserPhone";
                break;
        }
        $query = User::where([['merchant_id', '=', $merchant_id], ['user_delete', '=', NULL]]);
        if ($request->keyword) {
            $query->where($parameter, 'like', '%' . $request->keyword . '%');
        }
        if ($request->country_id) {
            $query->where('country_id', '=', $request->country_id);
        }
        $users = $query->paginate(25);
        $merchant = Merchant::find($merchant_id);
        $config = $merchant->Configuration;
        $countries = $merchant->Country;
        $config->user_document = $merchant->ApplicationConfiguration->user_document;
        $pending_user = User::where([['merchant_id', '=', $merchant_id], ['user_delete', '=', NULL], ['signup_status', '=', 1]])->count();
        $data = $request->all();
        $data['merchant_id'] = $merchant_id;
        $data['export_search'] = $request->all();
        return view('merchant.user.index', compact('countries', 'users', 'config', 'pending_user', 'merchant','data'));
    }

    public function destroy($id)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $bookings = Booking::whereIn('booking_status', array(1001, 1012, 1002, 1003, 1004))->where([['user_id', '=', $id]])->first();
        if (empty($bookings)):
            $bookings = Booking::where([['user_id', '=', $id]])->first();
            $user = User::where([['merchant_id', '=', $merchant_id]])->FindorFail($id);
//            $playerids = $user->UserDevice()->get()->pluck('player_id')->toArray();
            if (empty($bookings)):
                if (!empty($user->UserDevice())) {
                    $user->UserDevice()->delete();
                }
                $user->delete();
            else:
                $user->user_delete = 1;
                $user->save();
            endif;
            setLocal($user->language);
            $message = trans("$string_file.account_has_been_deleted");
            $title = trans("$string_file.account_deleted");
            $data['notification_type'] ="ACCOUNT_DELETED";
            $data['segment_type'] = "";
            $data['segment_data'] = ['user_id'=>$id];
            $arr_param = ['user_id'=>$id,'data'=>$data,'message'=>$message,'merchant_id'=>$merchant_id,'title'=>$title,'large_icon'=>''];
            Onesignal::UserPushMessage($arr_param);
            setLocal();
//            $data = ['booking_status' => '999'];
//            $message = trans('admin.message728');
//            Onesignal::UserPushMessage($id, $data, $message, 6, $merchant_id);
            echo $message;
        else:
            echo trans('admin.message694');
        endif;
    }

    public function showDocuments($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $user = User::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $rejectReasons = RejectReason::where([['merchant_id', '=', $merchant_id], ['status', '=', 1]])->get();
        return view('merchant.user.document', compact('user', 'rejectReasons'));
    }

    public function ChangeDocumentStatus(Request $request)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $request->validate([
            'id' => 'required',
            'status' => 'required',
            'reject_reason_id' => 'required_if:status,3'
        ]);
        $userdocument = UserDocument:: findorfail($request->id);
        // status 2 for approved documents 3 for reject
        $bool = UserDocument:: where('id', $request->id)->update([
            'document_verification_status' => $request->status,
            'reject_reason_id' => $request->reject_reason_id
        ]);
        if ($request->status == 2 && $bool) {
            $user = User::findorfail($userdocument->user_id);
            $compare = (int)$user->approved_document + 1;
            if ($user->total_document == $compare) {
                $user->signup_status = 2;
            }
            $user->approved_document = $user->approved_document + 1;
            $user->save();
        }

        if ($bool) {
            return redirect()->back()->withSuccess(trans("$string_file.added_successfully"));
        }
        return redirect()->back()->withErrors(trans("$string_file.some_thing_went_wrong"));
    }

    public function AlldocumentStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => 'required',
            'reject_reason_id' => 'required_if:status,3'
        ]);

        $userdocument = UserDocument::where('user_id', '=', $request->id)->update(['document_verification_status' => $request->status]);

        $user = User::findorfail($request->id);
        $user->signup_status = 2;
        $user->approved_document = $user->total_document;
        $user->save();


        if ($userdocument) {
            return redirect()->back()->with('document-message', trans('admin.documentAdded'));
        }
        return redirect()->back()->with('document-message', trans('admin.documentNotAdded'));
    }

    public function UserRefer($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $user = User::find($id);
        $referral_details = ReferralDiscount::where([['sender_id', '=', $id],['sender_type','=',"USER"],['merchant_id','=',$merchant_id]])->latest()->paginate(10);
        foreach($referral_details as $refer){
            $receiverDetails = $refer->receiver_type == "USER" ? User::find($refer->receiver_id) : Driver::find($refer->receiver_id);
            $phone = $refer->receiver_type == "USER" ? $receiverDetails->UserPhone : $receiverDetails->phoneNumber;
            $receiverType = $refer->receiver_type == "USER" ? 'User' : 'Driver';
            $refer->receiver_details = array(
                'id' => $receiverDetails->id,
                'name' => $receiverDetails->first_name.' '.$receiverDetails->last_name,
                'phone' => $phone,
                'email' => $receiverDetails->email );
            $refer->receiverType = $receiverType;
        }
        return view('merchant.user.user_refer', compact('referral_details', 'user'));
    }

    public function PendingRiderList()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $merchant = Merchant::find($merchant_id);
        $config = $merchant->Configuration;
        $config->user_document = $merchant->ApplicationConfiguration->user_document;
        $appconfig = $merchant->ApplicationConfiguration;
        $config->gender = $appconfig->gender;
        $users = User::where([['merchant_id', '=', $merchant_id], ['user_delete', '=', NULL], ['signup_status', '=', 1]])->latest()->paginate(10);
        return view('merchant.user.pending_rider', compact('users', 'config'));
    }

    public function PendingSearch(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'keyword' => "required",
            'parameter' => "required|integer|between:1,3",
        ]);
        switch ($request->parameter) {
            case "1":
                $parameter = "first_name";
                break;
            case "2":
                $parameter = "email";
                break;
            case "3":
                $parameter = "UserPhone";
                break;
        }
        $query = User::where([['merchant_id', '=', $merchant_id], ['user_delete', '=', NULL], ['signup_status', '=', 1]]);
        if ($request->keyword) {
            $query->where($parameter, 'like', '%' . $request->keyword . '%');
        }
        $users = $query->paginate(25);
        $merchant = Merchant::find($merchant_id);
        $config = $merchant->Configuration;
        $config->user_document = $merchant->ApplicationConfiguration->user_document;

        return view('merchant.user.pending_rider', compact('users', 'config'));

    }

//    public function UserList()
//    {
//        if(Auth::user()->demo == 1)
//        {
//            $otp = 436158961;//getRandomCode(10);
//            Session::put('demo_otp',$otp);
//            $drivers = [];
//            return view('merchant.user.user-list', compact('drivers'));
//        }
//        return redirect()->route('merchant.dashboard')->withErrors(trans("$string_file.permission_denied"));
//    }
//    public function verfiyOtp(Request $request)
//    {
//        if(Auth::user()->demo == 1)
//        {
//            $session_otp = Session::get('demo_otp');
//            $session_post = $request->otp;
//            $merchant_id = Auth::user()->id;
//            if ($session_otp == $session_post) {
//                $drivers = User::where([['merchant_id', '=', $merchant_id], ['taxi_company_id', '=', NULL], ['user_delete', '=', NULL]])->orderBy('created_at','DESC')->get();
//                return view('merchant.user.user-list', compact('drivers'));
//            }
//        }
//        return redirect()->route('merchant.dashboard')->withErrors(trans("$string_file.permission_denied"));
//    }
}
