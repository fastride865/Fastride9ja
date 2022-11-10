<?php

namespace App\Http\Middleware;

use App\Models\Merchant;
use Closure;

class DriverApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $public_key = $request->header('publicKey');
        $secret_key = $request->header('secretKey');
        $AccessToken = $request->header('Authorization');
        if (!empty($public_key) && !empty($secret_key)) {
            $clientDetail = Merchant::where([['merchantPublicKey', '=', $public_key], ['merchantSecretKey', '=', $secret_key], ['merchantStatus', '=', 1]])->first();
            if (empty($clientDetail)) {
                return response()->json(['version' => 'NA','result' => "0", 'message' => trans("api.merchant_not_found"), 'data' => []]);
            }
            if (empty($clientDetail->ApplicationConfiguration)) {
                return response()->json(['version' => 'NA','result' => "0", 'message' => trans('api.application_not_found'), 'data' => []]);
            }
            if (empty($clientDetail->ApplicationTheme)) {
                return response()->json(['version' => 'NA','result' => "0", 'message' => trans('api.application_theme_found'), 'data' => []]);
            }
            if (empty($clientDetail->BookingConfiguration)) {
                return response()->json(['version' => 'NA','result' => "0", 'message' => trans('api.booking_config_found'), 'data' => []]);
            }
            $request->request->add([
                'merchant_id' => $clientDetail->id,
                'gender' => $clientDetail->ApplicationConfiguration->gender,
                'smoker' => $clientDetail->ApplicationConfiguration->smoker,
//                'driver_email_enable' => $clientDetail->ApplicationConfiguration->driver_email,
//                'driver_phone_enable' => $clientDetail->ApplicationConfiguration->driver_phone,
                'driver_login_type' => $clientDetail->ApplicationConfiguration->driver_login,
                'driver_commission_choice' => $clientDetail->ApplicationConfiguration->driver_commission_choice,
                'driver_address_enable' => $clientDetail->Configuration->driver_Address,
                'referral_code_mandatory_driver_signup' => $clientDetail->Configuration->referral_code_mandatory_driver_signup,
            ]);
        } elseif (!empty($AccessToken)) {
            $user = $request->user('api-driver');
            if (empty($user)) {
                return response()->json(['version' => 'NA','result' => "999", 'message' => "unauthorised request", 'data' => []]);
            }
//            if(!empty($request->user('api-driver')->CountryArea)){
//                date_default_timezone_set($request->user('api-driver')->CountryArea->timezone);
//            }
            $request->request->add([
                'merchant_id' => $user->merchant_id,
                'gender' => $user->Merchant->ApplicationConfiguration->gender,
                'smoker' => $user->Merchant->ApplicationConfiguration->smoker,
                'driver_email_enable' => $user->Merchant->ApplicationConfiguration->driver_email,
                'driver_phone_enable' => $user->Merchant->ApplicationConfiguration->driver_phone,
                'driver_login_type' => $user->Merchant->ApplicationConfiguration->driver_login,
                'driver_commission_choice' => $user->Merchant->ApplicationConfiguration->driver_commission_choice,
                'referral_code_mandatory_user_signup' => $user->Merchant->Configuration->referral_code_mandatory_user_signup,
            ]);
        } else {
            return response()->json(['version' => 'NA','result' => "0", 'message' => "unauthorised request", 'data' => []]);
        }
        return $next($request);
    }
}
