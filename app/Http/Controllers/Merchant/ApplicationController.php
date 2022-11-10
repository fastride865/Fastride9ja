<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use App\Models\VersionManagement;
use Auth;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\MerchantTrait;

class ApplicationController extends Controller
{
    use MerchantTrait;
    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'APPLICATION_URL')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $is_demo = $merchant->demo == 1 ? true : false;
        $application = Application::where([['merchant_id', '=', $merchant_id]])->first();
        return view('merchant.application.index', compact('application','is_demo'));
    }

    public function store(Request $request)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $request->validate([
            'ios_user_link' => 'required',
            'ios_driver_link' => 'required',
            'android_user_link' => 'required',
            'android_driver_link' => 'required'
        ]);
        Application::updateOrCreate(
            ['merchant_id' => $merchant_id],
            [
                'ios_user_link' => $request->ios_user_link,
                'ios_driver_link' => $request->ios_driver_link,
                'android_user_link' => $request->android_user_link,
                'android_driver_link' => $request->android_driver_link,
                'ios_user_appid' => $request->ios_user_appid,
                'ios_driver_appid' => $request->ios_driver_appid,
            ]
        );
        VersionManagement::updateVersion($merchant_id);
        return redirect()->back()->withSuccess(trans("$string_file.added_successfully"));
    }
}
