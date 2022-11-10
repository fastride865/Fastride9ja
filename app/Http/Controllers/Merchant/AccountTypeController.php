<?php

namespace App\Http\Controllers\Merchant;

use App\Models\AccountType;
use App\Models\InfoSetting;
use App\Models\LangName;
use App\Models\VersionManagement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\MerchantTrait;

class AccountTypeController extends Controller
{
    use MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','ACCOUNT_TYPE')->first();
        view()->share('info_setting', $info_setting);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checkPermission = check_permission(1, 'view-account-types');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $account_types = AccountType::where([['admin_delete',0], ['merchant_id', $merchant_id]])->latest()->paginate(25);
        $string_file = $this->getStringFile($merchant_id);
        return view('merchant.account_types.index', compact('account_types','string_file'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkPermission = check_permission(1, 'create-account-types');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        return view('merchant.account_types.create',compact('string_file'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $checkPermission = check_permission(1, 'create-account-types');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        Validator::make($request->all(),[
            'name'=>'required',
            'status' => 'integer|required|between:0,1'
        ],[
            'status.between' => trans('admin.invalid_status'),
        ])->validate();

        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $request->request->add(['merchant_id'=>$merchant_id]);
        $data = $request->except('_token','_method','name');
        $lang_data = $request->only(['name']);
        $store = new AccountType($data);
        if($store->save()):
            $this->saveLangAccountTypes(collect($lang_data), $store);
            VersionManagement::updateVersion($merchant_id);
            request()->session()->flash('message', trans("$string_file.account_type_saved_successfully"));
            return redirect()->route('account-types.index');
        else:
            request()->session()->flash('error', trans("$string_file.some_thing_went_wrong"));
            return redirect()->route('account-types.index');
        endif;
    }

    private function saveLangAccountTypes(Collection $collection, AccountType $model_data)
    {
        $collect_lang_data = $collection->toArray();
        $update_lang_pro = $model_data->LangAccountTypeSingle;
        if(!empty($update_lang_pro)){
            $update_lang_pro['name'] = $collect_lang_data['name'];
            $update_lang_pro->save();
        }else{
            $language_data = new LangName([
                'merchant_id' => $model_data->merchant_id,
                'locale' => \App::getLocale(),
                'name' => $collect_lang_data['name'],
            ]);
            $saved_lang_data = $model_data->LangAccountTypes()->save($language_data);
        }
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
        $checkPermission = check_permission(1, 'edit-account-types');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $edit = AccountType::where([['admin_delete',0]])->FindorFail($id);
        $string_file = $this->getStringFile($merchant_id);
        return view('merchant.account_types.edit', compact('edit','string_file'));
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
        $checkPermission = check_permission(1, 'edit-account-types');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->request->add(['id'=>$id]);
        Validator::make($request->all(),[
            'id'=>['required',
                Rule::exists('account_types', 'id')->where(function ($query) use(&$merchant_id){
                    $query->where([['admin_delete','=',0]]);
                })],
            'name'=>'required',
            'status' => 'integer|required|between:0,1'
        ],[
            'status.between' => trans('admin.invalid_status'),
            'id.exists' => trans('admin.account_type_addederror'),
        ])->validate();

        $update = AccountType::where([['merchant_id',$merchant_id],['admin_delete',0]])->findorfail($id);
        $data = $request->except('_token','_method','id','name');
        $update->status = $data['status'];
        $lang_data = $request->only(['name']);
        if($update->save()):
            $this->saveLangAccountTypes(collect($lang_data), $update);
            VersionManagement::updateVersion($merchant_id);
            request()->session()->flash('message', trans('admin.account_type_updated'));
            return redirect()->route('account-types.index');
        else:
            request()->session()->flash('error', trans('admin.account_type_addederror'));
            return redirect()->route('account-types.index');
        endif;

    }

    public function Change_Status(Request $request, $id = null , $status = null)
    {
        $request->request->add(['status'=>$status,'id'=>$id]);
        Validator::make($request->all(),[
            'id'=>'required|exists:account_types,id',
            'status' => 'integer|required|between:0,1'
        ],[
            'status.between' => trans('admin.invalid_status'),
            'id.exists' => trans('admin.account_type_addederror'),
        ])->validate();
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $change = AccountType::where([['admin_delete',0],['merchant_id',$merchant_id]])->FindorFail($id);
        $change->status = $status;
        $change->save();
        if ($status == 1)
        {
            request()->session()->flash('message', trans('admin.account_type_activated'));
        } else {
            request()->session()->flash('error', trans('admin.account_type_deactivated'));
        }
        return redirect()->route('account-types.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $checkPermission = check_permission(1, 'delete-account-types');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $delete = AccountType::where([['merchant_id',$merchant_id],['admin_delete',0]])->findorfail($id);
        $delete->status = 0;
        $delete->admin_delete = 1;
        $delete->save();
        request()->session()->flash('error', trans('admin.account_type_deleted'));
        echo trans('admin.account_type_deleted');
        //return redirect()->route('account-types.index');
    }
}
