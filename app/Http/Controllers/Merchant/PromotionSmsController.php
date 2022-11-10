<?php

namespace App\Http\Controllers\Merchant;

use App\Models\CountryArea;
use App\Models\Driver;
use App\Models\User;
use App\Http\Controllers\Helper\SmsController;
use App\Models\Onesignal;
use App\Models\PromotionSms;
use App\Models\UserDevice;
use App\Models\Merchant;
use App\Models\Configuration;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Traits\MerchantTrait;

class PromotionSmsController extends Controller
{
    use MerchantTrait;
    public function index()
    {
        $checkPermission =  check_permission(1,'view_promotion');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $promotionsms = PromotionSms::where([['merchant_id', '=', $merchant_id]])->paginate(25);
        return view('merchant.promotionsms.index', compact('promotionsms'));
    }

    public function create()
    {
        $checkPermission =  check_permission(1,'create_promotion');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $areas = CountryArea::where([['merchant_id', '=', $merchant_id]])->get();
        return view('merchant.promotionsms.create', ['areas' => $areas]);
    }

    public function store(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'application' => 'required|integer|between:1,2',
            'message' => 'required|string',
        ]);
        $promotion = new PromotionSms();
        $promotion->merchant_id = $merchant_id;
        $promotion->application = $request->application;
        $promotion->message = $request->message;
        $promotion->save();
        if($request->application == 1)
        {
            $driver = Driver::where([['merchant_id', '=', $merchant_id]])->get(['phoneNumber'])->toArray();
            foreach($driver as $val)
            {
                $driver_imp[] = $val['phoneNumber'];
            }
            $driver_imp = implode(',',$driver_imp);
        }
        else
        {
            $driver = User::where([['merchant_id', '=', $merchant_id]])->get(['phoneNumber'])->toArray();
            foreach($driver as $val)
            {
                $driver_imp[] = $val['UserPhone'];
            }
            $driver_imp = implode(',',$driver_imp);   
        }
        $config = Configuration::select('sms_gateway', 'email_functionality')->where([['merchant_id', '=', $merchant_id]])->first();
        $message = $request->message;
        if ($config->sms_gateway == 1) {
            $sms = new SmsController();
            $otp = mt_rand(1111, 9999);
//            $sms->SendSms($merchant_id, $driver_imp, $message, 'PUSH_MSG');
            $auto_fill = false;
        }
        return redirect()->back()->withSuccess('Notification sent');
    }
    
    public function storeUserDriver(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'application' => 'required|integer|between:1,2',
            'message' => 'required|string',
        ]);
        $promotion = new PromotionSms();
        $promotion->merchant_id = $merchant_id;
        $promotion->application = $request->application;
        if($request->application == 1)
        {
            $promotion->driver_id = $request->user_driver_id;
            $driver = Driver::find($request->user_driver_id);
            $phone = $driver->phoneNumber;
        }
        else
        {
            $promotion->user_id = $request->user_driver_id;
            $user = User::find($request->user_driver_id);
            $phone = $user->UserPhone;
        }
        $promotion->message = $request->message;
        $promotion->save();
        
        $config = Configuration::select('sms_gateway', 'email_functionality')->where([['merchant_id', '=', $merchant_id]])->first();
        $message = $request->message;
        if ($config->sms_gateway == 1) {
            $sms = new SmsController();
//            $sms->SendSms($merchant_id, $phone, $message, 'PUSH_MSG');
            $auto_fill = false;
        }
        return redirect()->back()->withSuccess('Notification sent');
    }

    public function show(Request $request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        if($request->user_driver == 1)
        {   
            $details = Driver::where([['merchant_id', '=', $merchant_id]])->get();
            if (!empty($details->toArray())) {
            foreach ($details as $value) {
                echo "<option value='" . $value->id . "'>" . $value->first_name . ".($value->phoneNumber)</option>";
            }
            } else {
               echo "<option value=''>" . trans("$string_file.no_service_area") . "</option>";
            }
        }
        else
        {
            $details = User::where([['merchant_id', '=', $merchant_id]])->get();
            if (!empty($details->toArray())) {
            foreach ($details as $value) {
                echo "<option value='" . $value->id . "'>" . $value->first_name . ".($value->UserPhone)</option>";
            }
            } else {
                echo "<option value=''>" . trans('admin.message669') . "</option>";
            }
        }
    }
    
    public function destroy($id)
    {
        $checkPermission =  check_permission(1,'delete_promotion');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $promotions = PromotionSms::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $promotions->delete();
        return redirect()->back();
    }
}
