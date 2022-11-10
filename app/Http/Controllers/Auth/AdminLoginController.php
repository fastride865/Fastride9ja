<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
class AdminLoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }


    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        // $captech = $_POST['g-recaptcha-response'];  
        // $secret = '6LfDC5gUAAAAAH2U2BiLaW7QV6WxfGqDp6AR-99Z';
        // $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$captech);
        // $responseData = json_decode($verifyResponse);
        // if($responseData->success) {
            $this->validate($request,[
                'email'=>'required|email',
                'password'=>'required',
                
            ]);
            if(Auth::guard('admin')->attempt(['email'=>$request->email,'password'=>$request->password],$request->remember)){
                return redirect()->route('admin.dashboard');
            }
        // }
        // else
        // {
        //     return redirect()->route('admin.login');
        // }
        
        $this->sendFailedLoginResponse($request);
    }


    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
