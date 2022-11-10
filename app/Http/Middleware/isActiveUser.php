<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class isActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
//    public function handle($request, Closure $next)
//    {
//        $user = Auth::user('merchant');
//        if($user->merchantStatus == 2){
//            $alias_name = $user->alias_name;
//            Auth::logout();
//            Session::flush(); //clears out all the exisiting sessions
//            return Redirect::route('merchant.login', $alias_name);
//        }
//        return $next($request);
//    }

    public function handle($request, Closure $next)
    {
        $user = Auth::user('merchant');
        if($user->merchantStatus == 2){
            $alias_name = $user->alias_name;
            Auth::logout();
            Session::flush(); //clears out all the existing sessions
            return Redirect::route('merchant.login', $alias_name);
        }
//        elseif(isset($user->version) && $user->version != 22){
//            $alias_name = $user->alias_name;
//            Auth::logout();
//            Session::flush(); //clears out all the existing sessions
//            return Redirect::route('merchant.login', $alias_name);
//        }
        return $next($request);
    }
}
