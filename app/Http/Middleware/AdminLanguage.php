<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdminLanguage
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

       // if(!Session::has('locale') || (Session::has('locale') &&   empty(Session::get('locale'))))
         if(!Session::has('locale'))
        {
            $merchant_id = NULL;
            if (Auth::guard('merchant')->check()) {

                $merchant_id = Auth::user('merchant')->id;
            }
            elseif(Auth::guard('hotel')->check()) {
                $merchant_id = Auth::user('hotel')->merchant_id;
                }
            elseif(Auth::guard('franchise')->check()) {
                $merchant_id = Auth::user('franchise')->merchant_id;
                }
            elseif(Auth::guard('taxicompany')->check()) {
                $merchant_id = Auth::user('taxicompany')->merchant_id;
            }
            elseif(Auth::guard('corporate')->check()) {
                $merchant_id = Auth::user('corporate')->merchant_id;
            }
            elseif(Auth::guard('business-segment')->check()) {
                $merchant_id = Auth::user('business-segment')->merchant_id;
            }
            elseif(Auth::guard('driver-agency')->check()) {
                $merchant_id = Auth::user('driver-agency')->merchant_id;
            }
            if(!empty($merchant_id))
            {
               $default_locale =  App\Models\Configuration::select('default_language')->where('merchant_id',$merchant_id)
                    ->first();
                $locale = Session::get('locale', Config::get('app.locale'));
               if(!empty($default_locale->default_language))
               {
                   $locale = $default_locale->default_language;
               }
               App::SetLocale($locale);
            }
        }
        return $next($request);
    }
}
