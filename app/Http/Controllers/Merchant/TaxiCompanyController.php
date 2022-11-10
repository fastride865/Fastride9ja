<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\AccountType;
use App\Models\Country;
use App\Models\TaxiCompaniesWalletTransaction;
use App\Models\TaxiCompany;
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

class TaxiCompanyController extends Controller
{
   use ImageTrait,MerchantTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checkPermission =  check_permission(1,'taxi_company');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }

        $merchant_id = get_merchant_id();
        $merchant = Merchant::find($merchant_id);
        $account_types = $merchant->AccountType;
        $taxi_company = TaxiCompany::where([['merchant_id', '=', $merchant_id]])->latest()->paginate(10);
        $string_file = $this->getStringFile($merchant_id);
        return view('merchant.taxicompany.index',compact('taxi_company','merchant', 'account_types','string_file'));
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
        $company = null;
        if(!empty($id))
        {
            $company = TaxiCompany::Find($id);
        }
        $countries = $merchant->Country;
        $account_types = $config->AccountType->where('admin_delete', '!=', 1);
        $string_file = $this->getStringFile($merchant_id);
        if($account_types->count() <= 0){
            return redirect()->back()->withErrors(trans($string_file.'.create_account_type_first'));
        }
        return view('merchant.taxicompany.create',compact('countries', 'account_types','company'));
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
                Rule::unique('taxi_companies')->where(function($query)use($merchant_id,$id){
                    $query->where([['merchant_id','=',$merchant_id]]);
                    $query->where([['id','!=',$id]]);
                })],
            'phone' => ['required',
                Rule::unique('taxi_companies')->where(function($query)use($merchant_id,$id){
                    $query->where([['merchant_id','=',$merchant_id]]);
                    $query->where([['id','!=',$id]]);
                })],
            'password' => 'required_without:id',
            'company_logo' => 'required_without:id',
            'country' => 'required',
            'contact_person' => 'required',
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
               $company = TaxiCompany::find($id);
           }
           else
           {
               $company = new TaxiCompany;
               $company->alias_name = str_slug($request->input('name'));
               $company->merchant_id = $merchant_id;
           }

            if($request->password){
                $password = Hash::make($request->password);
                $company->password = $password;
            }
            if($request->hasFile('company_logo')){
                $company->company_image = $this->uploadImage('company_logo','company_logo');
            }
            $company->name = $request->name;
            $company->phone = $request->phone;
            $company->email = $request->email;
            $company->contact_person = $request->contact_person;
            $company->country_id = $request->country;
            $company->address = $request->address;
            $company->bank_name = $request->bank_name;
            $company->account_holder_name = $request->account_holder_name;
            $company->account_number = $request->account_number;
            $company->online_transaction = $request->online_transaction;
            $company->account_type_id = $request->account_types;
            $company->save();

//            TaxiCompany::create([
//                'merchant_id' => $merchant_id,
//                'name' => $request->name,
//                'alias_name' => $data['alias_name'],
//                'email' => $request->email,
//                'password' => $password,
//                'company_image' => $this->uploadImage('company_logo','company_logo'),
//                'phone' => $request->phone,
//                'country_id' => $request->country,
//                'contact_person' => $request->contact_person,
//                'address' => $request->address,
//                'bank_name' => $request->bank_name,
//                'account_holder_name' => $request->account_holder_name,
//                'account_number' => $request->account_number,
//                'online_transaction' => $request->online_transaction,
//                'account_type_id' => $request->account_types,
//            ]);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            DB::rollback();
            return redirect()->back()->withErrors($message);
        }
        // Commit Transaction
        DB::commit();
        $string_file = $this->getStringFile($merchant_id);
        return redirect()->route('merchant.taxi-company')->withSuccess(trans($string_file.".saved_successfully"));
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
//    public function edit($id)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $countries = Country::where([['merchant_id', '=', $merchant_id]])->latest()->paginate(25);
//        $company = TaxiCompany::find($id);
//        $config = Merchant::find($merchant_id);
//        $account_types = $config->AccountType->where('admin_delete', '!=', 1);
//        if($account_types->count() <= 0){
//            return redirect()->back()->with('error',trans('admin.create_account_type_first'));
//        }
//        return view('merchant.taxicompany.edit',compact('company','countries','account_types'));
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, $id)
//    {
//        $merchant_id = get_merchant_id();
//        $validator = Validator::make($request->all(), [
//            'name' => 'required|alpha',
//            'email' => ['required',
//                Rule::unique('taxi_companies')->where(function($query)use($merchant_id, $id){
//                    $query->where([['merchant_id','=',$merchant_id]]);
//                })->ignore($id)
//            ],
//            'phone' => ['required',
//                Rule::unique('taxi_companies')->where(function($query)use($merchant_id,$id){
//                    $query->where([['merchant_id','=',$merchant_id]]);
//                })->ignore($id)
//            ],
//            'contact_person' => 'required',
//            'address' => 'required',
//            'bank_name' => 'required',
//            'account_holder_name' => 'required',
//            'account_number' => 'required',
//            'online_transaction' => 'required',
//            'account_type' => 'required',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return redirect()->back()->with('error',$errors[0]);
//        }
//        DB::beginTransaction();
//        try
//        {
//            $company = TaxiCompany::find($id);
//            if($request->password){
//                $password = Hash::make($request->password);
//                $company->password = $password;
//            }
//            if($request->hasFile('company_logo')){
//                $company->company_image = $this->uploadImage('company_logo','company_logo');
//            }
//            $company->name = $request->name;
//            $company->phone = $request->phone;
//            $company->email = $request->email;
//            $company->contact_person = $request->contact_person;
//            $company->country_id = $request->country;
//            $company->address = $request->address;
//            $company->bank_name = $request->bank_name;
//            $company->account_holder_name = $request->account_holder_name;
//            $company->account_number = $request->account_number;
//            $company->online_transaction = $request->online_transaction;
//            $company->account_type_id = $request->account_types;
//            $company->save();
//        } catch (\Exception $e) {
//            $message = $e->getMessage();
//            // Rollback Transaction
//            DB::rollback();
//            return redirect()->route('taxicompany.index')->withErrors($message[0]);
//        }
//        // Commit Transaction
//        DB::commit();
//        return redirect()->route('taxicompany.index')->with('success', 'Company Updated');
//    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    public function StatusUpdate(Request $request,$id)
    {
        $company = TaxiCompany::find($id);
        $company->status = $request->status;
        $string_file = $this->getStringFile($company->merchant_id);
        $company->save();
        return redirect()->back()->withSuccess(trans("$string_file.status_updated"));
    }

    public function AddMoney(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'payment_method_id' => 'required|integer|between:1,2',
            'receipt_number' => 'required|string',
            'amount' => 'required|numeric',
            'taxi_company_id' => 'required|exists:taxi_companies,id'
        ]);
//        $newAmount = new \App\Http\Controllers\Helper\Merchant();
        DB::beginTransaction();
        try{
            WalletTransaction::TaxiComapnyWalletCredit($request->taxi_company_id,$request->amount,$request->payment_method_id,$request->receipt_number,$request->description);
//            TaxiCompaniesWalletTransaction::create([
//                'merchant_id' => $merchant_id,
//                'taxi_company_id' => $request->taxi_company_id,
//                'transaction_type' => 1, // Credit
//                'payment_method' => $request->payment_method_id,
//                'receipt_number' => $request->receipt_number,
//                'amount' => sprintf("%0.2f", $request->amount),
//                'platform' => 1,
//                'description' => $request->description,
//            ]);
//            $taxi_company = TaxiCompany::find($request->taxi_company_id);
//            $wallet_money = $taxi_company->wallet_money + $request->amount;
//            $taxi_company->wallet_money = $newAmount->TripCalculation($wallet_money, $merchant_id);
//            $taxi_company->save();
        }catch(\Exception $e){
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return success_response(trans('admin.message207'));
    }

    public function Wallet($id)
    {
        $merchant_id = get_merchant_id();
        $taxi_company = TaxiCompany::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $wallet_transactions = TaxiCompaniesWalletTransaction::where([['taxi_company_id', '=', $taxi_company->id]])->paginate(25);
        $string_file = $this->getStringFile($merchant_id);
        return view('merchant.taxicompany.wallet', compact('wallet_transactions', 'taxi_company','string_file'));
    }
}
