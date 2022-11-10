<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\CorporateWalletTransaction;
use App\Models\Country;
use App\Models\Merchant;
use Auth;
use App\Models\Corporate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\ImageTrait;
use App\Traits\MerchantTrait;
use DB;

class CorporateController extends Controller
{
    use ImageTrait,MerchantTrait;

    public function index()
    {
        $checkPermission =  check_permission(1,'corporate');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $corporates = Corporate::where([['merchant_id', '=', $merchant_id]])->paginate(25);
        return view('merchant.corporate.index', compact('corporates','merchant'));
    }

    public function add(Request $request, $id = NULL)
    {
        $checkPermission =  check_permission(1,'corporate');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
        $corporate = NULL;
        if(!empty($id))
        {
            $corporate = Corporate::findOrFail($id);
        }
        return view('merchant.corporate.create', compact('countries','corporate'));
    }


    public function save(Request $request, $id = NULL)
    {
        $merchant_id = get_merchant_id();
        $validator = Validator::make($request->all(), [
            'country' => 'required|integer',
            'corporate_name' => 'required',
            'email' => ['required', 'email',
                Rule::unique('corporates')->where(function ($query) use ($merchant_id,$id) {
                     $query->where('merchant_id', $merchant_id);
                     $query->where('id','!=',$id);
                })],
            'phone' => ['required','regex:/^[0-9]+$/',
                Rule::unique('corporates', 'corporate_phone')->where(function ($query) use ($merchant_id,$id) {
                    $query->where('merchant_id', $merchant_id);
                    $query->where('id','!=',$id);
                })],
            'address' => 'required',
            'password' => 'required_without:id|confirmed',
            'corporate_logo' =>'required_without:id'
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
//            p($errors);
            return redirect()->back()->withInput()->withErrors($errors);
        }

        DB::beginTransaction();
        try{
            $data = $request->except('_token', '_method');
            $alias_name = str_slug($request->input('corporate_name'));
//            $password = Hash::make($request->password);
//            $country = Country::find($request->country);
//            $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//            Corporate::create([
//                'merchant_id' => $merchant_id,
//                'country_id' => $request->country,
//                'corporate_name' => $request->corporate_name,
//                'alias_name' => $data['alias_name'],
//                'email' => $request->email,
//                'corporate_phone' => $country->phonecode . $request->phone,
//                'corporate_address' => $request->address,
//                'corporate_logo' => $this->uploadImage('corporate_logo','corporate_logo'),
//                'password' => $password
//            ]);

            if(!empty($id))
            {
                $corporate = Corporate::findOrFail($id);
            }
            else
            {
                $corporate = new Corporate;
                $corporate->alias_name = $alias_name;
            }

            $country = Country::find($request->country);
            $corporate->merchant_id = $merchant_id;
            $corporate->corporate_name = $request->corporate_name;
            $corporate->country_id = $request->country;
            $corporate->corporate_phone = $country->phonecode . $request->phone;
            $corporate->email = $request->email;
            $corporate->corporate_address = $request->address;
            if($request->hasFile('corporate_logo')){
                $corporate->corporate_logo = $this->uploadImage('corporate_logo','corporate_logo');
            }
            if($request->password){
                $password = Hash::make($request->password);
                $corporate->password = $password;
            }
            $corporate->save();
        }catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();

        }
        // Commit Transaction
        DB::commit();
        $string_file = $this->getStringFile($merchant_id);
        return redirect()->route('corporate.index')->withSuccess(trans($string_file.".added_successfully"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $corporate = Corporate::findOrFail($id);
//        $corporate->corporate_phone = substr($corporate->corporate_phone, strlen($corporate->Country->phonecode));
//        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
//        return view('merchant.corporate.edit', compact('countries','corporate'));
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, $id)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $this->validate($request, [
//            'country' => 'required|integer',
//            'corporate_name' => 'required',
//            'email' => ['required','email',
//                Rule::unique('corporates', 'email')->where(function ($query) use ($merchant_id) {
//                    return $query->where([['merchant_id', '=', $merchant_id]]);
//                })->ignore($id)],
//            'phone' => ['required','regex:/^[0-9]+$/',
//                Rule::unique('corporates', 'corporate_phone')->where(function ($query) use ($merchant_id) {
//                    return $query->where([['merchant_id', '=', $merchant_id]]);
//                })->ignore($id)],
//            'address' => 'required',
//        ]);
//
//        DB::beginTransaction();
//        try{
//            $country = Country::find($request->country);
//            $corporate = Corporate::findOrFail($id);
//            $corporate->corporate_name = $request->corporate_name;
//            $corporate->country_id = $request->country;
//            $corporate->corporate_phone = $country->phonecode . $request->phone;
//            $corporate->email = $request->email;
//            $corporate->corporate_address = $request->address;
//            if($request->hasFile('corporate_logo')){
//                $corporate->corporate_logo = $this->uploadImage('corporate_logo','corporate_logo');
//            }
//            $corporate->save();
//        }catch (\Exception $e) {
//            $message = $e->getMessage();
//            p($message);
//            // Rollback Transaction
//            DB::rollback();
//        }
//        // Commit Transaction
//        DB::commit();
//        return redirect()->route('corporate.index')->with('success', trans('admin.corporate_update'));
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function ChangeStatus($id, $status)
    {
        $validator = Validator::make(
            [
                'id' => $id,
                'status' => $status,
            ],
            [
                'id' => ['required'],
                'status' => ['required', 'integer', 'between:1,2'],
            ]);
        if ($validator->fails()) {
            return redirect()->back();
        }
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $corporate = Corporate::findOrFail($id);
        $string_file = $this->getStringFile($corporate->merchant_id);
        $corporate->status = $status;
        $corporate->save();
        return redirect()->back()->withSuccess(trans("$string_file.status_updated"));
    }

    public function AddMoney(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'payment_method' => 'required|integer|between:1,2',
            'receipt_number' => 'required|string',
            'amount' => 'required|numeric',
            'add_money_driver_id' => 'required|exists:corporates,id'
        ]);
//        $newAmount = new \App\Http\Controllers\Helper\Merchant();
//        CorporateWalletTransaction::create([
//            'merchant_id' => $merchant_id,
//            'corporate_id' => $request->add_money_driver_id,
//            'transaction_type' => 1,
//            'payment_method' => $request->payment_method,
//            'receipt_number' => $request->receipt_number,
//            'amount' => sprintf("%0.2f", $request->amount),
//            'platform' => 1,
//            'description' => $request->description,
//            'narration' => 1,
//        ]);
//        $corporate = Corporate::find($request->add_money_driver_id);
//        $wallet_money = $corporate->wallet_balance + $request->amount;
//        $corporate->wallet_balance = $newAmount->TripCalculation($wallet_money, $merchant_id);
//        $corporate->save();
        $string_file = $this->getStringFile($merchant_id);
        WalletTransaction::CorporateWaletCredit($request->add_money_driver_id,$request->amount,$request->payment_method,$request->receipt_number,$request->description);
        return redirect()->back()->withSuccess(trans("$string_file.money_added_successfully"));
    }

    public function Wallet($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $corporate = Corporate::select('corporate_name')->where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $wallet_transactions = CorporateWalletTransaction::where([['merchant_id','=',$merchant_id],['corporate_id', '=', $id]])->paginate(25);
        return view('merchant.corporate.wallet', compact('wallet_transactions', 'corporate'));
    }

}
