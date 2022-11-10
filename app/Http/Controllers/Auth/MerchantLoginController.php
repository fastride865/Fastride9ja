<?php

namespace App\Http\Controllers\Auth;

use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
//use Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;

class MerchantLoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest:merchant')->except('logout');;
    }

    public function showLoginForm($name = null)
    {
        $merchant = Merchant::where([['alias_name', '=', $name], ['merchantStatus', '=', 1]])->first();
        if (!empty($merchant)) {
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            return view('merchant.login', compact('merchant'));
        } else {
            return view('apporio');
        }
    }

    public function login(Request $request, $alias_name)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:5',
//            'g-recaptcha-response' => 'required',
        ]);
//        $captcha = $_POST['g-recaptcha-response'];
//        $secretKey = '6LcXDdUUAAAAACbckpUpfPCZ5ZYmdO_lTDnWbmlP';
//        $ip = $_SERVER['REMOTE_ADDR'];
//        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
//        $responseKeys = json_decode($response,true);
//
//        if(intval($responseKeys["success"]) !== 1) {
//            p('failed');
//            echo '<p class="alert alert-warning">Please check the the captcha form.</p>';
//        }
        // if below guards are opened in same tab then first logout them
        if(Auth::guard('business-segment')->check())
        {
            throw ValidationException::withMessages([
                $this->username() => [trans("auth.bs_guard_conflict")],
            ]);
        }
        elseif(Auth::guard('taxicompany')->check())
        {
            throw ValidationException::withMessages([
                $this->username() => [trans("auth.company_guard_conflict")],
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

        if (Auth::guard('merchant')->attempt(['email' => $request->email, 'password' => $request->password, 'alias_name' => $alias_name, 'merchantStatus' => 1], $request->remember)) {
            // logout
            return redirect()->route('merchant.dashboard');
        }
        $this->sendFailedLoginResponse($request);
    }

    public function logout()
    {
        // clear locale sessions
        Session::flush();
        $alias_name = Auth::user('merchant')->alias_name;
        Auth::guard('merchant')->logout();
        return redirect()->route('merchant.login', $alias_name);
    }


    // set custom guard
    protected function guard()
    {
        return Auth::guard('merchant');
    }
}
