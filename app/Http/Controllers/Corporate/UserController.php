<?php

namespace App\Http\Controllers\Corporate;

use App\Imports\UsersImport;
use App\Models\Booking;
use App\Models\Configuration;
use App\Models\Country;
use App\Models\EmployeeDesignation;
use App\Models\ImportUserFail;
use App\Models\Merchant;
use App\Models\Onesignal;
use App\Models\User;
use App\Models\UserDevice;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\MerchantTrait;

class UserController extends Controller
{
    use ImageTrait,MerchantTrait;

    public function index(){

        $corporate = Auth::user('corporate');
        $merchant = Merchant::find($corporate->merchant_id);
        $config = $merchant->Configuration;
        $countries = $merchant->Country;
        $appconfig = $merchant->ApplicationConfiguration;
        $config->gender = $appconfig->gender;
        $users = User::where([['merchant_id', '=', $merchant->id],['corporate_id','=', $corporate->id], ['user_delete', '=', NULL]])->latest()->paginate(10);
        $failImport = ImportUserFail::where([['merchant_id',$corporate->merchant_id],['corporate_id',$corporate->id]])->count();
        return view('corporate.user.index', compact('users', 'config', 'countries', 'merchant','failImport'));
    }

    public function create()
    {
        $corporate = Auth::user('corporate');
        $merchant = Merchant::find($corporate->merchant_id);
        $appConfig = $merchant->ApplicationConfiguration;
        $countries = Country::where([['merchant_id', '=', $merchant->id]])->get();
        $config = Configuration::where([['merchant_id', '=', $merchant->id]])->first();
        $designations = EmployeeDesignation::where([['merchant_id', '=', $merchant->id],['corporate_id', '=', $corporate->id],['delete_status','=',null]])->get();
        return view('corporate.user.create', compact('appConfig', 'countries', 'config','designations'));
    }

    public function store(Request $request)
    {
        $corporate = Auth::user('corporate');
        $request->validate([
            'user_phone' => 'required|regex:/^[0-9]+$/'
        ]);
        $user_phone = $request->isd.$request->user_phone;
        $request->merge([
            'phone' => $user_phone,
        ]);
        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => ['required',
                Rule::unique('users', 'UserPhone')->where(function ($query) use ($corporate) {
                    return $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $corporate->merchant_id]]);
                })],
            'user_email' => ['required', 'email',
                Rule::unique('users', 'email')->where(function ($query) use ($corporate) {
                    return $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $corporate->merchant_id]]);
                })],
            'password' => "required|min:6|max:8",
            'profile' => 'required|file'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->with('error',$errors[0]);
        }

//        dd($request->all());
        DB::beginTransaction();
        try
        {
            $merchant_id = $corporate->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
            User::create([
                'merchant_id' => $merchant_id,
                'country_id' => explode('|',$request->country)[0],
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'UserPhone' => $request->phone,
                'email' => $request->user_email,
                'password' => Hash::make($request->password),
                'UserSignupType' => 1,
                'UserSignupFrom' => 2,
                'UserProfileImage' => $this->uploadImage('profile','corporate_user',$merchant_id),
                'user_type' => 1,
                'user_gender' => $request->user_gender,
                'corporate_id' => $corporate->id,
                'designation_id' => $request->designationId
            ]);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        // Commit Transaction
        DB::commit();

        //event(new UserSignupWelcome($user->id, $merchant_id, 'welcome'));
        return redirect()->route('user.index')->withSuccess(trans("$string_file.user_added_successfully"));
    }

    public function edit($id)
    {
        $corporate = Auth::user('corporate');
        $merchant = Merchant::find($corporate->merchant_id);
        $config = Configuration::where([['merchant_id', '=', $corporate->merchant_id]])->first();
        $user = User::where([['merchant_id', '=', $corporate->merchant_id],['corporate_id', '=', $corporate->id], ['user_delete', '=', NULL]])->findOrFail($id);
        $designations = EmployeeDesignation::where([['merchant_id', '=', $merchant->id],['corporate_id', '=', $corporate->id],['delete_status','=',null]])->get();
        return view('corporate.user.edit', compact('user', 'config','merchant','designations'));
    }

    public function update(Request $request, $id)
    {
        $corporate = Auth::user('corporate');
        $request->validate([
            'first_name' => "required",
            'user_email' => ['required', 'email', 'max:255',
                Rule::unique('users', 'email')->where(function ($query) use ($corporate) {
                    $query->where([['merchant_id', '=', $corporate->merchant_id],['corporate_id','=',$corporate->id]]);
                })->ignore($id)],
            'user_phone' => ['required', 'regex:/^[0-9+]+$/',
                Rule::unique('users', 'UserPhone')->where(function ($query) use ($corporate) {
                    $query->where([['merchant_id', '=', $corporate->merchant_id],['corporate_id','=',$corporate->id]]);
                })->ignore($id)],
            'password' => 'required_if:edit_password,1'
        ]);

        DB::beginTransaction();

        try
        {
            $string_file = $this->getStringFile($corporate->merchant_id);
            $user = User::where([['merchant_id', '=', $corporate->merchant_id],['corporate_id','=',$corporate->id]])->findOrFail($id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->UserPhone = $request->user_phone;
            $user->email = $request->user_email;
            $user->user_gender = $request->user_gender;
            $user->designation_id = $request->designationId;
            if ($request->edit_password == 1) {
                $user->password = Hash::make($request->password);
            }
            if ($request->hasFile('profile')) {
                $user->UserProfileImage = $this->uploadImage('profile','corporate_user');
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
        return redirect()->route('user.index')->withSuccess(trans("$string_file.user_saved_successfully"));
    }

    public function show($id)
    {
        $corporate = Auth::user('corporate');
        $user = User::where([['merchant_id', '=', $corporate->merchant_id],['corporate_id','=',$corporate->id]])->findOrFail($id);
        $bookings = Booking::where([['user_id', '=', $id]])->whereIn('booking_status', [1005])->paginate(5);
        return view('corporate.user.show', compact('user', 'bookings'));
    }

    public function FavouriteLocation($id)
    {
        $corporate = Auth::user('corporate');
        $user = User::with('UserAddress')->where([['merchant_id', '=', $corporate->merchant_id],['corporate_id','=',$corporate->id]])->findOrFail($id);
        return view('corporate.user.favourite', compact('user'));
    }

    public function FavouriteDriver($id)
    {
        $corporate = Auth::user('corporate');
        $user = User::with(['FavouriteDriver' => function ($query) {
            $query->with(['Driver' => function ($q) {
                $q->with('CountryArea');
            }]);
        }])->where([['merchant_id', '=', $corporate->merchant_id],['corporate_id','=',$corporate->id]])->findOrFail($id);
        return view('corporate.user.favourite-drivers', compact('user'));
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
            $msg = $validator->messages()->all();
            return redirect()->back()->with('error',$msg[0]);
        }

        $corporate = Auth::user('corporate');
        $merchant_id = $corporate->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $user = User::where([['merchant_id', '=', $corporate->merchant_id],['corporate_id','=',$corporate->id]])->findOrFail($id);
        //p($user);
        $user->UserStatus = $status;
        $user->save();
//        $userdevices = UserDevice::where([['user_id', '=', $id]])->get();
//        $playerids = array_pluck($userdevices, 'player_id');
//        $data = ['status' => $status];
//        $message = $status == 2 ? trans('admin.message296') : trans('admin.message297');
//        $type = 20; // 20 value set according to vishal's suggestion {done by @amba}
//        Onesignal::UserPushMessage($id, $data, $message, $type, $corporate->merchant_id);
        $data = ['status' => $status];
        setLocal($user->language);
        $message = $status == 2 ? trans("$string_file.account_has_been_inactivated_by_admin") : trans("$string_file.account_has_been_activated_by_admin");
        $title = trans("$string_file.account_inactivated");
        $data['notification_type'] ="ACCOUNT_INACTIVATED";
        $data['segment_type'] = "";
        $data['segment_data'] = ['user_id'=>$id];
        $arr_param = ['user_id'=>$id,'data'=>$data,'message'=>$message,'merchant_id'=>$merchant_id,'title'=>$title,'large_icon'=>''];
        Onesignal::UserPushMessage($arr_param);
        setLocal();
        return redirect()->route('user.index')->withSuccess(trans("$string_file.status_updated"));
    }

    public function destroy(Request $request)
    {
        $corporate = Auth::user('corporate');

        $bookings = Booking::whereIn('booking_status', array(1001, 1012, 1002, 1003, 1004))->where([['user_id', '=', $request->id],['merchant_id','=',$corporate->merchant_id]])->first();
        if (empty($bookings)):
            $bookings = Booking::where([['user_id', '=', $request->id],['merchant_id','=',$corporate->merchant_id]])->first();
            $user = User::where([['merchant_id', '=', $corporate->merchant_id],['corporate_id','=',$corporate->id]])->FindorFail($request->id);
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
            $id = $user->id;
            $merchant_id = $corporate->merchant_id;
            setLocal($user->language);
            $string_file = $this->getStringFile($merchant_id);
            $message = trans("$string_file.account_has_been_deleted_by_admin");
            $title = trans("$string_file.account_inactivated");
            $data['notification_type'] ="ACCOUNT_DELETED";
            $data['segment_type'] = "";
            $data['segment_data'] = ['user_id'=>$user->id];
            $arr_param = ['user_id'=>$id,'data'=>$data,'message'=>$message,'merchant_id'=>$merchant_id,'title'=>$title,'large_icon'=>''];
            Onesignal::UserPushMessage($arr_param);
            setLocal();
//            $data = ['booking_status' => '999'];
//            $message = trans('admin.message728');
//            Onesignal::UserPushMessage($request->id, $data, $message, 6, $corporate->merchant_id);
            echo trans('admin.message693');
        else:
            echo trans('admin.message694');
        endif;
    }

    public function Search(Request $request)
    {
        $corporate = Auth::user('corporate');
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
        $query = User::where([['merchant_id', '=', $corporate->merchant_id],['corporate_id','=',$corporate->id], ['user_delete', '=', NULL]]);
        if ($request->keyword) {
            $query->where($parameter, 'like', '%' . $request->keyword . '%');
        }
        if ($request->country_id) {
            $query->where('country_id', '=', $request->country_id);
        }
        $users = $query->paginate(25);
        $merchant = Merchant::find($corporate->merchant_id);
        $config = $merchant->Configuration;
        $countries = $merchant->Country;
        $failImport = ImportUserFail::where([['merchant_id',$corporate->merchant_id],['corporate_id',$corporate->id]])->count();
        return view('corporate.user.index', compact('countries', 'users', 'config', 'merchant','failImport'));
    }

    public function ImportUserData(Request $request){
        $validator = Validator::make($request->all(),
            ['import_data'  => 'required|mimes:xls,xlsx']
        );

        if ($validator->fails()){
            $msg = $validator->messages()->all();
            return redirect()->back()->with('error',$msg[0]);
        }
        $corporate = Auth::user('corporate');

        //$path = $request->file('import_data')->getRealPath();

        $path1 = $request->file('import_data')->store('temp');
        $path = storage_path('app').'/'.$path1;
        Excel::import(new UsersImport,$path);
        $failUser = ImportUserFail::where([['merchant_id',$corporate->merchant_id],['corporate_id',$corporate->id]])->count();
        $msg = $failUser > 0 ? trans('admin.import_fail_user',['count' => $failUser]) : trans('admin.file_import');
        $action = $failUser > 0 ? 'error' : 'success';
        return redirect()->back()->with($action,$msg);
    }

    public function FailImports(){
        $corporate = Auth::user('corporate');
        $users = ImportUserFail::where([['merchant_id',$corporate->merchant_id],['corporate_id',$corporate->id]])->paginate(20);
        return view('corporate.user.fail_import',compact('users'));
    }

    public function FailImportDelete(Request $request){
        $failImport = ImportUserFail::find($request->userId);
        $failImport->delete();
        return redirect()->back()->with('success',trans('admin.message693'));
    }
}
