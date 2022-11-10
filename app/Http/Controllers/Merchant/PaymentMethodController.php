<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Merchant;
use App\Models\PaymentMethod;
use App;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\MerchantTrait;
use App\Traits\ImageTrait;
use DB;

class PaymentMethodController extends Controller
{
    use ImageTrait,MerchantTrait;
    public function __construct()
    {
        $info_setting = App\Models\InfoSetting::where('slug', 'PAYMENT_METHOD')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $merchant = get_merchant_id(false);
        $payment = $merchant->PaymentMethod;
        return view('merchant.payment_methods.index', compact('payment','merchant'));
    }

    public function edit($id)
    {
        $merchant = get_merchant_id(false);
        $payment = PaymentMethod::where('id',$id)->first();
        $icon = get_image($payment->payment_icon,'payment_icon',$merchant->id,false);
        $merchant_payment = $payment->Merchant->where('id',$merchant->id);
        $merchant_payment = collect($merchant_payment->values());
        if(isset($merchant_payment) && !empty($merchant_payment[0]->pivot['icon']))
        {
            $icon = get_image($merchant_payment[0]->pivot['icon'],'p_icon',$merchant->id);
        }
        return view('merchant.payment_methods.edit', compact('payment','merchant','icon'));
    }

    public function update(Request $request, $id)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $request->validate([
            'payment_name' => 'required'
        ]);
        if($request->hasFile('p_icon_image'))
        {
            $p_icon = $this->uploadImage('p_icon_image','p_icon',$merchant_id);
            DB::table('merchant_payment_method')->where([['payment_method_id','=',$id],['merchant_id','=',$merchant_id]])->update(['icon'=>$p_icon]);
        }
        App\Models\PaymentMethodTranslation::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'payment_method_id' => $id
        ], [
            'name' => $request->payment_name,
        ]);
        
        return redirect()->route('merchant.paymentMethod.index')->withSuccess(trans("$string_file.saved_successfully"));
    }
}
