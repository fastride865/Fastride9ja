<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\AccountType;
use App\Models\Country;
use App\Models\DriverAgency\DriverAgencyWalletTransaction;
use App\Models\DriverAgency\DriverAgency;
use App\Models\Merchant;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;
use App\Traits\MerchantTrait;

class DriverAgencyController extends Controller
{
   use ImageTrait,MerchantTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $checkPermission =  check_permission(1,'driver_agency');
//        if ($checkPermission['isRedirect']){
//            return  $checkPermission['redirectBack'];
//        }

        $merchant_id = get_merchant_id();
        $merchant = Merchant::find($merchant_id);
        $account_types = $merchant->AccountType;
        $driver_agency = DriverAgency::where([['merchant_id', '=', $merchant_id]])->latest()->paginate(10);
        $string_file = $this->getStringFile($merchant_id);
        return view('merchant.driver-agency.index',compact('driver_agency','merchant', 'account_types','string_file'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request , $id = NULL)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $config = $merchant;
        $agency = null;
        if(!empty($id))
        {
            $agency = DriverAgency::Find($id);
        }
        $countries = $merchant->Country;
        $account_types = $config->AccountType->where('admin_delete', '!=', 1);
        $string_file = $this->getStringFile($merchant_id);
        if($account_types->count() <= 0){
            return redirect()->back()->withErrors(trans($string_file.'.create_account_type_first'));
        }
        return view('merchant.driver-agency.create',compact('countries', 'account_types','agency'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request, $id=NULL)
    {
        $merchant_id = get_merchant_id();
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => ['required',
                Rule::unique('driver_agencies')->where(function($query)use($merchant_id,$id){
                    $query->where([['merchant_id','=',$merchant_id]]);
                    $query->where([['id','!=',$id]]);
                })],
            'phone' => ['required',
                Rule::unique('driver_agencies')->where(function($query)use($merchant_id,$id){
                    $query->where([['merchant_id','=',$merchant_id]]);
                    $query->where([['id','!=',$id]]);
                })],
            'password' => 'required_without:id',
            'logo' => 'required_without:id',
            'country' => 'required',
            'address' => 'required',
            'bank_name' => 'required',
            'account_holder_name' => 'required',
            'account_number' => 'required',
            'online_transaction' => 'required',
            'account_types' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput()->withErrors($errors);
        }
        // Begin Transaction
        DB::beginTransaction();
        try
        {
            $data = $request->except('_token', '_method');
           if(!empty($id))
           {
               $agency = DriverAgency::find($id);
           }
           else
           {
               $agency = new DriverAgency;
               $agency->alias_name = str_slug($request->input('name'));
               $agency->merchant_id = $merchant_id;
           }

            if($request->password){
                $password = Hash::make($request->password);
                $agency->password = $password;
            }
            if($request->hasFile('logo')){
                $agency->logo = $this->uploadImage('logo','agency_logo');
            }
            $agency->name = $request->name;
            $agency->phone = $request->phone;
            $agency->email = $request->email;
            $agency->country_id = $request->country;
            $agency->address = $request->address;
            $agency->bank_name = $request->bank_name;
            $agency->account_holder_name = $request->account_holder_name;
            $agency->account_number = $request->account_number;
            $agency->online_transaction = $request->online_transaction;
            $agency->account_type_id = $request->account_types;
            $agency->save();

        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            DB::rollback();
            return redirect()->back()->withErrors($message);
        }
        // Commit Transaction
        DB::commit();
        $string_file = $this->getStringFile($merchant_id);
        return redirect()->route('merchant.driver-agency')->withSuccess(trans($string_file.".saved_successfully"));
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

    // update status
    public function StatusUpdate(Request $request,$id)
    {
        $agency = DriverAgency::find($id);
        $agency->status = $request->status;
        $agency->save();
        $string_file = $this->getStringFile($agency->merhcant_id);
        return redirect()->back()->withSuccess(trans("$string_file.status_updated"));
    }

    public function AddMoney(Request $request)
    {
        $merchant_id = get_merchant_id();
        $request->validate([
            'payment_method_id' => 'required|integer|between:1,2',
            'receipt_number' => 'required|string',
            'amount' => 'required|numeric',
            'driver_agency_id' => 'required|exists:driver_agencies,id'
        ]);

        DB::beginTransaction();
        try{
            $arr_data = [
                'driver_agency_id'=>$request->driver_agency_id,
                'amount'=>$request->amount,
                'payment_method_id'=>$request->payment_method_id,
                'receipt_number'=>$request->receipt_number,
                'description'=>$request->description,
            ];
            WalletTransaction::driverAgencyWalletCredit($arr_data);
        }catch(\Exception $e){
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return success_response("success");
    }

    public function Wallet($id)
    {
        $driver_agency = DriverAgency::findOrFail($id);
        $wallet_transactions = DriverAgencyWalletTransaction::where([['driver_agency_id', '=', $driver_agency->id]])->paginate(25);
        $string_file = $this->getStringFile($driver_agency->merchant_id);
        return view('merchant.driver-agency.wallet', compact('wallet_transactions', 'driver_agency','string_file'));
    }
}
