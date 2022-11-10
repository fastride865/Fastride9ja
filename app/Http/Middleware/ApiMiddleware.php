<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Helper\PolygenController;
use App\Models\Merchant;
use Closure;

class ApiMiddleware
{
    public function handle($request, Closure $next)
    {
        // these keys will be used to get merchant information/authentication
        $public_key = $request->header('publicKey');
        $secret_key = $request->header('secretKey');

        $country_area_id = null;
        $AccessToken = $request->header('Authorization');
        if (!empty($public_key) && !empty($secret_key)) {
            $clientDetail = Merchant::where([['merchantPublicKey', '=', $public_key], ['merchantSecretKey', '=', $secret_key], ['merchantStatus', '=', 1]])->first();
            if (empty($clientDetail)) {
                return response()->json(['version' => 'NA','result' => "0", 'message' => trans("common.merchant_not_found"), 'data' => []]);
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
                'user_email_enable' => $clientDetail->ApplicationConfiguration->user_email,
                'user_phone_enable' => $clientDetail->ApplicationConfiguration->user_phone,
                'user_cpf_enable' => $clientDetail->ApplicationConfiguration->user_cpf_number_enable,
                'login_type' => $clientDetail->ApplicationConfiguration->user_login,
                'referral_code_mandatory_user_signup' => $clientDetail->Configuration->referral_code_mandatory_user_signup,
            ]);
        } elseif (!empty($AccessToken)) {
            $user = $request->user('api');
            // user status 1 means active else deactivate
            if (empty($user) || $user->UserStatus == 2) {
                return response()->json(['result' => "0", 'message' => "unauthorised request", 'data' => []]);
            }
            $request->request->add([
                'merchant_id' => $user->merchant_id,
                'gender' => $user->Merchant->ApplicationConfiguration->gender,
                'smoker' => $user->Merchant->ApplicationConfiguration->smoker,
                'user_email_enable' => $user->Merchant->ApplicationConfiguration->user_email,
                'user_phone_enable' => $user->Merchant->ApplicationConfiguration->user_phone,
                'login_type' => $user->Merchant->ApplicationConfiguration->user_login,
                'referral_code_mandatory_user_signup' => $user->Merchant->Configuration->referral_code_mandatory_user_signup,
            ]);
        } else {
            return response()->json(['version' => 'NA','result' => "0", 'message' => "unauthorised request", 'data' => []]);
        }
        return $next($request);
    }
}
