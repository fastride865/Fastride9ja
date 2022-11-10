<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Merchant;
use App\Models\TaxiCompany;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use Lang;
use URL;
use Illuminate\Support\Facades\Session;

class TaxicompanyLoginController extends Controller
{
    use AuthenticatesUsers;  // USE it to use sendFailedLoginResponse method

    //

    public function __construct()
    {
        $this->middleware('guest:taxicompany')->except('logout');;
    }

    public function showLoginForm($merchant_alias = null, $taxicompany_alias = null)
    {
        $valid_taxicompany = TaxiCompany::where([['alias_name', '=', $taxicompany_alias], ['status', TRUE]])->count();
        $valid_merchant = Merchant::where([['alias_name', '=', $merchant_alias], ['merchantStatus', TRUE]])->first();
        if ($valid_taxicompany && !empty($valid_merchant))
            return view('taxicompany.login', ['merchant' => $valid_merchant]);
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
        elseif(Auth::guard('driver-agency')->check())
        {
            throw ValidationException::withMessages([
                $this->username() => [trans("auth.driver_agency_guard_conflict")],
            ]);
        }

        //Attempt the user log in
        if (Auth::guard('taxicompany')->attempt(['email' => $request->email, 'password' => $request->password, 'merchant_id' => $merchant_info->id, 'status' => 1], $request->remember)) {
            return redirect()->intended(route('taxicompany.dashboard'));
            //$this->sendLoginResponse($request);
        }
        //return redirect()->back()->withInput($request->only('email','remember'));
        $this->sendFailedLoginResponse($request);
    }

    public function logout()
    {
        // clear locale sessions
        Session::flush();
        $taxi_alias_name = Auth::user('taxicompany')->alias_name;
        $merchant_alias_name = Auth::user('taxicompany')->merchant->alias_name;
        Auth::guard('taxicompany')->logout();
        return redirect()->route('taxicompany.login', [$merchant_alias_name, $taxi_alias_name]);
    }
}
