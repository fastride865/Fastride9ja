<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Helper\PolygenController;
use App\Models\Merchant;
use Closure;

class BusinessSegmentMiddleware
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
                return response()->json(['version' => 'NA','result' => "0", 'message' => trans("$string_file.merchant_not_found"), 'data' => []]);
            }
            if (empty($clientDetail->ApplicationConfiguration)) {
                return response()->json(['version' => 'NA','result' => "0", 'message' => trans('api.application_not_found'), 'data' => []]);
            }
            if (empty($clientDetail->BookingConfiguration)) {
                return response()->json(['version' => 'NA','result' => "0", 'message' => trans('api.booking_config_found'), 'data' => []]);
            }
            $request->request->add([
                'merchant_id' => $clientDetail->id,
            ]);
        } elseif (!empty($AccessToken)) {
            $business_segment = $request->user('business-segment');
            // user status 1 means active else deactivate
            if (empty($business_segment) || $business_segment->status == 2) {
                return response()->json(['version' => 'NA','result' => "0", 'message' => "unauthorised request", 'data' => []]);
            }
            $request->request->add([
                'merchant_id' => $business_segment->merchant_id,
            ]);
        } else {
            return response()->json(['version' => 'NA','result' => "0", 'message' => "unauthorised request", 'data' => []]);
        }
        return $next($request);
    }
}
