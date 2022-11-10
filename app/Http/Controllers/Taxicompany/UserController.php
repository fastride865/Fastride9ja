<?php

namespace App\Http\Controllers\Taxicompany;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\ApplicationConfiguration;
use App\Models\Booking;
use App\Models\Configuration;
use App\Models\Country;
use App\Models\Merchant;
use App\Models\Onesignal;
use App\Models\TaxiCompany;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserWalletTransaction;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use DB;
use App\Traits\ImageTrait;

class UserController extends Controller
{
    use ImageTrait;
    public function index()
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $merchant = Merchant::find($merchant_id);
        $config = $merchant->Configuration;
        $countries = $merchant->Country;
        $config->user_document = $merchant->ApplicationConfiguration->user_document;
        $appconfig = $merchant->ApplicationConfiguration;
        $config->gender = $appconfig->gender;
        $users = User::where([['taxi_company_id', $taxicompany->id], ['user_delete', '=', NULL]])->latest()->paginate(10);
        $config = Configuration::where('merchant_id', $taxicompany->merchant_id)->first();
        return view('taxicompany.user.index', compact('users', 'config', 'countries', 'merchant'));
    }

    public function create()
    {
        $taxicompany = get_taxicompany();
        $countries = Country::where([['merchant_id', $taxicompany->merchant_id]])->get();
        return view('taxicompany.user.create', compact('countries'));
    }

    public function store(UserRequest $request)
    {
        $taxicompany = get_taxicompany();
        $country = explode("|", $request->country);
        DB::beginTransaction();
        try
        {
            $user = new User();
            $user = User::create([
                'merchant_id' => $taxicompany->merchant_id,
                'taxi_company_id' => $taxicompany->id,
                'country_id' => $country[0],
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'UserPhone' => $request->phone,
                'email' => $request->user_email,
                'password' => Hash::make($request->password),
                'UserSignupType' => 1,
                'UserSignupFrom' => 2,
                'ReferralCode' => $user->GenrateReferCode(),
                'UserProfileImage' => $this->uploadImage('profile','user',$taxicompany->merchant_id),
                'user_type' => "Retail",
                'user_gender' => $request->user_gender,
                'smoker_type' => $request->smoker_type,
                'allow_other_smoker' => $request->allow_other_smoker,
            ]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            DB::rollback();
            return redirect()->back()->with('rideradded', $message);
        }
        // Commit Transaction
        DB::commit();
        return redirect()->back()->with('rideradded', 'Rider Added');
    }

    public function show($id)
    {
        $taxi_company = get_taxicompany();
        $merchant_id = $taxi_company->merchant_id;
        $user = User::where([['merchant_id', '=', $merchant_id],['taxi_company_id', '=', $taxi_company->id]])->findOrFail($id);
        $bookings = Booking::where([['user_id', '=', $id],['taxi_company_id', '=', $taxi_company->id]])->whereIn('booking_status', [1005])->paginate(5);
        $appConfig = ApplicationConfiguration::where('merchant_id','=',$merchant_id)->first();
        return view('taxicompany.user.show', compact('user', 'bookings','appConfig'));
    }

    public function edit($id)
    {
        $taxi_company = get_taxicompany();
        $merchant_id = $taxi_company->merchant_id;
        $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $user = User::where([['taxi_company_id', '=', $taxi_company->id], ['user_delete', '=', NULL]])->with('Country')->findOrFail($id);
        return view('taxicompany.user.edit', compact('user', 'config'));
    }

    public function update(Request $request, $id)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $request->request->add(['user_phone' => $request->phoneCode . $request->user_phone]);
        $request->validate([
            'first_name' => "required",
            'user_email' => ['required', 'email', 'max:255',
                Rule::unique('users', 'email')->where(function ($query) use ($merchant_id) {
                    $query->where([['merchant_id', '=', $merchant_id]]);
                })->ignore($id)],
            'user_phone' => ['required', 'regex:/^[0-9+]+$/',
                Rule::unique('users', 'UserPhone')->where(function ($query) use ($merchant_id) {
                    $query->where([['merchant_id', '=', $merchant_id]]);
                })->ignore($id)],
            'password' => '
            required_if:edit_password,1'
        ]);
        $user = User::where([['taxi_company_id', '=', $taxicompany->id]])->findOrFail($id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->UserPhone = $request->user_phone;
        $user->email = $request->user_email;
        $user->user_gender = $request->user_gender;
        $user->smoker_type = $request->smoker_type;
        $user->allow_other_smoker = $request->allow_other_smoker;
        if ($request->edit_password == 1) {
            $password = Hash::make($request->password);
            $user->password = $password;
        }
        if ($request->hasFile('profile')) {
            $profile_image = $this->uploadImage('profile','user',$taxicompany->merchant_id);
            $user->Userprofile_image = $profile_image;
        }
        $user->save();
        return redirect()->back()->with('rideradded', 'Rider Added');
    }

    public function destroy(Request $request, $id)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $bookings = Booking::whereIn('booking_status', array(1001, 1012, 1002, 1003, 1004))->where([['user_id', '=', $id]])->first();
        if (empty($bookings)):
            $bookings = Booking::where([['user_id', '=', $id]])->first();
            $user = User::where([['taxi_company_id', '=', $taxicompany->id]])->FindorFail($id);
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
            $data = ['booking_status' => '999'];
            $message = trans('admin.message728');
            Onesignal::UserPushMessage($user->id, $data, $message, 6, $merchant_id);
            echo trans('admin.message693');
        else:
            echo trans('admin.message694');
        endif;
    }

    public function search(Request $request)
    {
        $taxicompany_id = get_taxicompany(true);
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $merchant = Merchant::find($merchant_id);
        $config = $merchant->Configuration;
        $countries = $merchant->Country;
        $config->user_document = $merchant->ApplicationConfiguration->user_document;
        $appconfig = $merchant->ApplicationConfiguration;
        $config->gender = $appconfig->gender;
        $users = User::where([['taxi_company_id', $taxicompany->id], ['user_delete', '=', NULL]])->latest()->paginate(10);
        $config = Configuration::where('merchant_id', $taxicompany->merchant_id)->first();
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
        $query = User::where([['taxi_company_id', '=', $taxicompany_id], ['user_delete', '=', NULL]]);
        if ($request->keyword) {
            $query->where($parameter, 'like', '%' . $request->keyword . '%');
        }
        $users = $query->paginate(25);
        return view('taxicompany.user.index', compact('users','config'));
    }

    public function Wallet($id)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $user = User::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $wallet_transactions = UserWalletTransaction::where([['user_id', '=', $id]])->paginate(25);
        return view('taxicompany.user.wallet', compact('wallet_transactions', 'user'));
    }

    public function AddWalletMoney(Request $request)
    {
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $request->validate([
            'add_money_user_id' => 'required',
            'amount' => 'required|numeric',
            'payment_method' => 'required|integer|between:1,2',
        ]);
        $amount = number_format((float)$request->amount, 2, '.', '');
        $user = User::findOrFail($request->add_money_user_id);
        $wallet = $user->wallet_balance;
        $total = $wallet + $amount;
        $user->wallet_balance = number_format((float)$total, 2, '.', '');;
        $user->save();
        UserWalletTransaction::create([
            'merchant_id' => $merchant_id,
            'user_id' => $request->add_money_user_id,
            'platfrom' => 1,
            'amount' => sprintf("%0.2f", $request->amount),
            'payment_method' => $request->payment_method,
            'receipt_number' => $request->receipt_number,
            'description' => $request->description,
            'type' => 1,
        ]);
//        $userdevices = UserDevice::where([['user_id', '=', $request->add_money_user_id]])->get();
//        $playerids = array_pluck($userdevices, 'player_id');
        $message = trans('api.money');
        $data = ['message' => $message];
        Onesignal::UserPushMessage($request->add_money_user_id, $data, $message, 3, $merchant_id);
        return redirect()->route('taxicompany.users.index')->with('moneyAdded', 'Money Added In Driver Wallet');
    }
}