<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        switch ($guard) {
            case "admin":
                if (Auth::guard($guard)->check()) {
                    return redirect('admin');
                }
                break;
            case "merchant":
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('merchant.dashboard');
                }
                break;
            case "hotel":
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('hotel.dashboard');
                }
                break;
            case "franchise":
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('franchise.dashboard');
                }
                break;
             case "taxicompany":
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('taxicompany.dashboard');
                }
                break;
            case "corporate":
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('corporate.dashboard');
                }
                break;
            case "business-segment":
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('business-segment.dashboard');
                }
                break;
            case "driver-agency":
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('driver-agency.dashboard');
                }
                break;
            default:
                if (Auth::guard($guard)->check()) {
                    return redirect('/home');
                }
                break;
        }
        return $next($request);
    }
}
