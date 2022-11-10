<?php

namespace App\Http\Middleware;

use Closure;

class ValidBusinessSegmentMiddleware
{
    public function handle($request, Closure $next)
    {

        $bs = $request->user('business-segment-api');
        if (empty($bs) || ($bs->status == 2 || $bs->login == 2)) {
            return response()->json(['version' => 'NA','result' => "999", 'message' => "unauthorised request", 'data' => []]);
        }
//        if (in_array($bs->CountryArea->timezone, \DateTimeZone::listIdentifiers())) {
//            date_default_timezone_set($bs->CountryArea->timezone);
//        }
        $request->request->add([
            'merchant_id' => $bs->merchant_id,
        ]);
        return $next($request);
    }
}
