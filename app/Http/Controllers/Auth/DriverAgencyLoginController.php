<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Merchant;
use App\Models\DriverAgency\DriverAgency;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use Lang;
use URL;
use Illuminate\Support\Facades\Session;

class DriverAgencyLoginController extends Controller
{
    use AuthenticatesUsers;                            // USE it to use sendFailedLoginResponse method

    //

    public function __construct()
    {
        $this->middleware('guest:driver-agency')->except('logout');;
    }

    public function showLoginForm($merchant_alias = null, $taxicompany_alias = null)
    {
        $valid_merchant = Merchant::where([['alias_name', '=', $merchant_alias], ['merchantStatus', TRUE]])->first();
        $valid_agency = DriverAgency::where([['alias_name', '=', $taxicompany_alias], ['status', TRUE],['merchant_id',$valid_merchant->id]])->first();
//      p($valid_agency);
        if ($valid_agency && !empty($valid_merchant))
            return view('driver-agency.login', ['merchant' => $valid_merchant,'agency' => $valid_agency]);
        else
            abort(404);
    }

    public function login(Request $request)
    {
        $url_pattern = array_slice(explode('/', URL::previous()), -3, 1);
        $merchant_info = Merchant::where([['alias_name', '=', $url_pattern[0]]])->first();
        //Validate the form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);

        // if below guards are opened in same tab then first logout them
        if(Auth::guard('merchant')->check())
        {
            throw ValidationException::withMessages([
                $this->username() => [trans("auth.merchant_guard_conflict")],
            ]);
        }
        elseif(Auth::guard('business-segment')->check())
        {
            throw ValidationException::withMessages([
                $this->username() => [trans("auth.bs_guard_conflict")],
            ]);
        }
        elseif(Auth::guard('corporate')->check())
        {
            throw ValidationException::withMessages([
                $this->username() => [trans("auth.corporate_guard_conflict")],
            ]);
        }
        elseif(Auth::guard('hotel')->check())
        {
            throw ValidationException::withMessages([
                $this->username() => [trans("auth.hotel_guard_conflict")],
            ]);
        }
        elseif(Auth::guard('taxicompany')->check())
        {
            throw ValidationException::withMessages([
                $this->username() => [trans("auth.taxicompany_guard_conflict")],
            ]);
        }

        //Attempt the user log in
        if (Auth::guard('driver-agency')->attempt(['email' => $request->email, 'password' => $request->password, 'merchant_id' => $merchant_info->id, 'status' => 1], $request->remember)) {
            return redirect()->intended(route('driver-agency.dashboard'));
            //$this->sendLoginResponse($request);
        }
        //return redirect()->back()->withInput($request->only('email','remember'));
        $this->sendFailedLoginResponse($request);
    }

    public function logout()
    {
        // clear locale sessions
        Session::flush();
        $taxi_alias_name = Auth::user('driver-agency')->alias_name;
        $merchant_alias_name = Auth::user('driver-agency')->Merchant->alias_name;
        Auth::guard('driver-agency')->logout();
        return redirect()->route('driver-agency.login', [$merchant_alias_name, $taxi_alias_name]);
    }
}
