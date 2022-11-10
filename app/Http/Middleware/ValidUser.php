<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Helper\PolygenController;
use Closure;

class ValidUser
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
        $user = $request->user('api');
        $country_area_id = NULL;
        if ($user->UserStatus == 2 || $user->user_delete == 1) {
            return response()->json(['version' => 'NA','result' => "999", 'message' => "unauthorised request", 'data' => []]);
        }
//        if (!empty($request->user('api')->CountryArea) && in_array($request->user('api')->CountryArea->timezone, \DateTimeZone::listIdentifiers())) {
//            date_default_timezone_set($request->user('api')->CountryArea->timezone);
//        }
        $request->request->add([
            'merchant_id' => $user->merchant_id,
        ]);
        return $next($request);
    }
}
