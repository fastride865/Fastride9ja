<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Auth;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Configuration;
use App\Models\Corporate;
use App\Models\WalletCouponCode;
use Illuminate\Validation\Rule;
//Mansu
class WalletCouponCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checkPermission =  check_permission(1,'view_wallet_promo_code');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $promocodes = WalletCouponCode::where([['merchant_id', '=', $merchant_id]])->paginate(10);
        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
        return view('merchant.wallet_promocode.index', compact('promocodes','countries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkPermission =  check_permission(1,'create_promo_code');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
        $corporates = Corporate::where([['merchant_id', '=', $merchant_id]])->get();
        $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        return view('merchant.wallet_promocode.create', compact('countries','corporates','config'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'country' => 'required|integer',
            'couponcode' => 'required',
            'amount' => 'required',
        ]);
        $promocode = WalletCouponCode::create([
            'merchant_id' => $merchant_id,
            'coupon_code' => $request->couponcode,
            'amount' => $request->amount,
            'country_id'=>$request->country
        ]);
        return redirect()->back()->with('couponcode', 'Coupon added');
    }
    
    public function bulk_code(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $coupon_quantity = $request->coupon_code_quantity;
        for($i =1; $i<=$coupon_quantity; $i++)
        {
            $code = $this->random_code();
            WalletCouponCode::create([
                'merchant_id' => $merchant_id,
                'coupon_code' => $code,
                'amount' => $request->amount,
                'country_id'=>$request->country
            ]);
        }
        return redirect()->back()->with('couponcode', 'Coupon added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $wallet_code = WalletCouponCode::where([['merchant_id', '=', $merchant_id]])->find($id);
        $corporates = Corporate::where([['merchant_id', '=', $merchant_id]])->get();
        $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
        return view('merchant.wallet_promocode.edit', compact('wallet_code', 'config', 'corporates','countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'country' => 'required|integer',
            'couponcode' => 'required',
            'amount' => 'required',
        ]);
        $wallet_code = WalletCouponCode::where([['merchant_id', '=', $merchant_id]])->find($id);
        $wallet_code->country_id = $request->country;
        $wallet_code->coupon_code = $request->couponcode;
        $wallet_code->amount = $request->amount;
        $wallet_code->save();
        return redirect()->back()->with('couponcode','couponcode');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function random_code()
    {
        $unique = false;
        do{
            $permitted_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = substr(str_shuffle($permitted_chars), 0, 8);
            $count = WalletCouponCode::where([['coupon_code','=',$code]])->count();
            if($count == 0){
                $unique = true;
            }
        }while(!$unique);

        return $code;
    }
}
