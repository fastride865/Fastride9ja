<?php

namespace App\Http\Middleware;

use Closure;

class Timezone
{
    public function handle($request, Closure $next)
    {
        $user = $request->user('api-driver');
        if ($request->user('api-driver')->driver_admin_status == 2 || $request->user('api-driver')->driver_delete == 1 || $request->user('api-driver')->login_logout == 2) {
            return response()->json(['version' => 'NA','result' => "999", 'message' => "unauthorised request", 'data' => []]);
        }
//        if (in_array($request->user('api-driver')->CountryArea->timezone, \DateTimeZone::listIdentifiers())) {
//            date_default_timezone_set($request->user('api-driver')->CountryArea->timezone);
//        }
        $request->request->add([
            'merchant_id' => $user->merchant_id,
        ]);
        return $next($request);
    }
}
