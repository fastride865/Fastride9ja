<?php

namespace App\Http\Controllers\Auth;

use App\Models\Franchisee;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class FranchiseLoginController extends Controller
{
    use AuthenticatesUsers;

    public function showLoginForm($merchant_alias_name = null, $alias = null)
    {
        $franchise = Franchisee::where([['alias', '=', $alias]])->first();
        $merchant = Merchant::where([['alias_name', '=', $merchant_alias_name]])->first();
        if (!empty($franchise) && !empty($merchant))
            return view('franchise.login', compact('merchant', 'franchise'));
        else
            abort(404);
    }

    public function login(Request $request, $alias_name)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);
        $url_pattern = array_slice(explode('/', \URL::previous()), -3, 1);
        $merchant_info = Merchant::where([['alias_name', '=', $url_pattern[0]]])->first();

        // if below guards are opened in same tab then first logout them
        if(Auth::guard('merchant')->check())
        {
            throw ValidationException::withMessages([
                $this->username() => [trans("auth.merchant_guard_conflict")],
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
        elseif(Auth::guard('business-segment')->check())
        {
            throw ValidationException::withMessages([
                $this->username() => [trans("auth.bs_guard_conflict")],
            ]);
        }

        if (Auth::guard('franchise')->attempt(['email' => $request->email, 'password' => $request->password, 'alias' => $alias_name, 'merchant_id' => $merchant_info->id, 'status' => 1], $request->remember)) {
            return redirect()->route('franchise.dashboard');
        }
        $this->sendFailedLoginResponse($request);
    }

    public function logout()
    {
        // clear locale sessions
        Session::flush();
        $alias = Auth::user('franchise')->alias;
        $alias_name = Auth::user('franchise')->Merchant->alias_name;
        Auth::guard('hotel')->logout();
        return redirect()->route('franchise.login', ['merchant_alias_name' => $alias_name, 'alias' => $alias]);
    }
}
