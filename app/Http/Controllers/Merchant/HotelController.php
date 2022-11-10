<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Requests\HotelRequest;
use App\Models\Country;
use App\Models\HotelWalletTransaction;
use App\Models\Merchant;
use Auth;
use App\Models\Hotel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Traits\ImageTrait;
use App\Traits\MerchantTrait;

class HotelController extends Controller
{
    use ImageTrait,MerchantTrait;
    public function index()
    {
        $merchant_id = get_merchant_id();
        $hotels = Hotel::where([['merchant_id', '=', $merchant_id]])->latest()->paginate(25);
        $string_file = $this->getStringFile($merchant_id);
        return view('merchant.hotel.index', compact('hotels','string_file'));
    }

    public function add(Request $request, $id = NULL)
    {
        $config = get_merchant_id(false);
        $merchant_id = $config->id;
        $hotel = NULL;
        if(!empty($id))
        {
            $hotel = Hotel::Find($id);
        }
        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
        $account_types = $config->AccountType->where('admin_delete', '!=', 1);
        $string_file = $this->getStringFile($merchant_id);
        if($account_types->count() <= 0){
            return redirect()->back()->withErrors(trans($string_file.'.create_account_type_first'));
        }
        return view('merchant.hotel.create', compact('countries','account_types','string_file','hotel'));
    }

    public function save(Request $request, $id = NULL)
    {
        $merchant_id = get_merchant_id();
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => ['required',
                Rule::unique('hotels')->where(function($query)use($merchant_id,$id){
                    $query->where([['merchant_id','=',$merchant_id]])->where([['id','!=',$id]]);
//                    $query->where([['id','!=',$id]]);
                })],
            'phone' => ['required',
                Rule::unique('hotels')->where(function($query)use($merchant_id,$id){
                    $query->where([['merchant_id','=',$merchant_id]])->where([['id','!=',$id]]);
//                    $query->where([['id','!=',$id]]);
                })],
            'password' => 'required_without:id',
            'address' => 'required',
            'bank_name' => 'required',
            'account_holder_name' => 'required',
            'account_number' => 'required',
            'online_transaction' => 'required',
            'account_type' => 'required',
            'hotel_logo' => 'required_without:id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput()->withErrors($errors);
        }
        // Begin Transaction
        DB::beginTransaction();
        try
        {
//            $country = explode("|", $request->country);
//            Hotel::create([
//                'merchant_id' => $merchant_id,
//                'country_id' => $country[0],
//                'name' => $request->name,
//                'alias' => $request->alias,
//                'email' => $request->email,
//                'phone' => $request->phone,
//                'address' => $request->address,
//                'password' => Hash::make($request->password),
//                'bank_name' => $request->bank_name,
//                'account_holder_name' => $request->account_holder_name,
//                'account_number' => $request->account_number,
//                'online_transaction' => $request->online_transaction,
//                'account_type_id' => $request->account_type,
//                'hotel_logo' => $this->uploadImage('hotel_logo','hotel_logo'),
//            ]);


            if(!empty($id))
            {
            $hotel = Hotel::findOrFail($id);
            }
            else
            {
                $hotel = new Hotel;
                $hotel->alias = str_slug($request->name);
                $hotel->merchant_id = $merchant_id;
            }

            if($request->hasFile('hotel_logo')){
                $hotel->hotel_logo = $this->uploadImage('hotel_logo','hotel_logo');
            }
            $country = explode("|", $request->country);
            $hotel->phone = $country[1] . $request->phone;
            $hotel->name = $request->name;
            $hotel->country_id = $country[0];
            $hotel->email = $request->email;
            $hotel->address = $request->address;
            if (!empty($request->password)) {
                $password = Hash::make($request->password);
                $hotel->password = $password;
            }
            $hotel->bank_name = $request->bank_name;
            $hotel->account_holder_name = $request->account_holder_name;
            $hotel->account_number = $request->account_number;
            $hotel->online_transaction = $request->online_transaction;
            $hotel->account_type_id = $request->account_type;
            $hotel->save();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            DB::rollback();
            p($message);
            return redirect()->back()->withErrors($message);
        }
        // Commit Transaction
        DB::commit();
        $string_file = $this->getStringFile($merchant_id);
        return redirect()->route('hotels.index')->withSuccess(trans($string_file.".hotel_saved_successfully"));
    }

//    public function edit($id)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $hotel = Hotel::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
//        $config = Merchant::find($merchant_id);
//        $account_types = $config->AccountType->where('admin_delete', '!=', 1);
//        if($account_types->count() <= 0){
//            return redirect()->back()->with('error',trans('admin.create_account_type_first'));
//        }
//        return view('merchant.hotel.edit', compact('hotel','account_types'));
//    }

//    public function update(Request $request, $id)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $request->validate([
//            'name' => "required",
//            'email' => ['required', 'email', 'max:255',
//                Rule::unique('hotels', 'email')->where(function ($query) use ($merchant_id) {
//                    $query->where([['merchant_id', '=', $merchant_id]]);
//                })->ignore($id)],
//            'phone' => 'required',
//            'address' => 'required',
//            'password' => 'required_if:edit_password,1',
//            'bank_name' => 'required',
//            'account_holder_name' => 'required',
//            'account_number' => 'required',
//            'online_transaction' => 'required',
//            'account_type' => 'required',
//        ]);
//        DB::beginTransaction();
//        try
//        {
//            $hotel = Hotel::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
//            if($request->hasFile('hotel_logo')){
//                $hotel->hotel_logo = $this->uploadImage('hotel_logo','hotel_logo');
//            }
//            $hotel->name = $request->name;
//            $hotel->phone = $request->phone;
//            $hotel->email = $request->email;
//            $hotel->address = $request->address;
//            if ($request->edit_password == 1) {
//                $password = Hash::make($request->password);
//                $hotel->password = $password;
//            }
//            $hotel->bank_name = $request->bank_name;
//            $hotel->account_holder_name = $request->account_holder_name;
//            $hotel->account_number = $request->account_number;
//            $hotel->online_transaction = $request->online_transaction;
//            $hotel->account_type_id = $request->account_type;
//            $hotel->save();
//        } catch (\Exception $e) {
//            $message = $e->getMessage();
//            // Rollback Transaction
//            DB::rollback();
//            return redirect()->route('hotels.index')->withErrors($message[0]);
//        }
//        // Commit Transaction
//        DB::commit();
//        return redirect()->route('hotels.index')->with('success', trans('admin.message558'));
//    }

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
        $user = Hotel::findOrFail($id);
        $user->status = $status;
        $user->save();
        return redirect()->route('hotels.index')->withSuccess(trans("$string_file.status_updated"));
    }

    public function AddMoney(Request $request)
    {
        $merchant_id = get_merchant_id();
        $request->validate([
            'payment_method_id' => 'required|integer|between:1,2',
            'receipt_number' => 'required|string',
            'amount' => 'required|numeric',
            'hotel_id' => 'required|exists:hotels,id'
        ]);
        $newAmount = new \App\Http\Controllers\Helper\Merchant();
        DB::beginTransaction();
        try{
            HotelWalletTransaction::create([
                'merchant_id' => $merchant_id,
                'hotel_id' => $request->hotel_id,
                'transaction_type' => 1, // Credit
                'payment_method' => $request->payment_method_id,
                'receipt_number' => $request->receipt_number,
                'amount' => sprintf("%0.2f", $request->amount),
                'platform' => 1,
                'description' => $request->description,
            ]);
            $hotel = Hotel::find($request->hotel_id);
            $wallet_money = $hotel->wallet_money + $request->amount;
            $hotel->wallet_money = $newAmount->TripCalculation($wallet_money, $merchant_id);
            $hotel->save();
        }catch(\Exception $e){
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return success_response(trans("$string_file.money_money"));
    }

    public function Wallet($id)
    {
        $hotel = Hotel::findOrFail($id);
        $wallet_transactions = HotelWalletTransaction::where([['hotel_id', '=', $hotel->id]])->paginate(25);
        return view('merchant.hotel.wallet', compact('wallet_transactions', 'hotel'));
    }
}
