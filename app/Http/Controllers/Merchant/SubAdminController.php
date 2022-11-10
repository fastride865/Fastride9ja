<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use Auth;
use App\Models\Merchant;
use App\Traits\AreaTrait;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\MerchantTrait;


class SubAdminController extends Controller
{
    use AreaTrait,MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'SUB_ADMIN')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $checkPermission =  check_permission(1,'view_admin');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $subadmins = Merchant::where([['parent_id', '=', $merchant_id]])->latest()->paginate(25);
        return view('merchant.subadmin.index', compact('subadmins'));
    }

    public function create()
    {
        $checkPermission =  check_permission(1,'create_admin');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $string_file = $this->getStringFile($merchant_id);
        // @Bhuvanesh
        // Role list without super admin
        $roles = Role::where([['merchant_id', '=', $merchant_id],['name', '!=', "Super Admin" . $merchant_id]])->get();
        if($roles->count() == 0){
            return redirect()->back()->withErrors(trans("$string_file.create_role_for_sub_admin"));
        }
        $area = $this->getAreaList(false);
        $areas = $area->get();
        return view('merchant.subadmin.create', compact('roles', 'areas'));
    }

    public function store(Request $request)
    {
        $merchant_id = get_merchant_id();
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required|regex:/^[0-9+]+$/',
            'password' => 'required',
            'admin_type' => 'required|integer|between:1,2',
            'area_list' => 'required_if:admin_type,==,2',
            'email' => ['required', 'email',
                Rule::unique('merchants', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where('parent_id', '=', $merchant_id)->orWhere('id', '=', $merchant_id);
                })],
            'role_id' => 'required|exists:roles,id'
        ]);
        $merchant = Merchant::find($merchant_id);
        $string_file = $this->getStringFile(NULL,$merchant);
        $demo = $merchant->demo;
        $subAdmin = Merchant::create([
            'parent_id' => $merchant_id,
            'BusinessName' => $merchant->BusinessName,
            'BusinessLogo' => $merchant->BusinessLogo,
            'alias_name' => $merchant->alias_name,
            'page_color' => $merchant->page_color,
            'header_color' => $merchant->header_color,
            'sidebar_color' => $merchant->sidebar_color,
            'footer_color' => $merchant->footer_color,
            'merchantFirstName' => $request->first_name,
            'merchantLastName' => $request->last_name,
            'merchantPhone' => $request->phone_number,
            'merchantAddress' => $merchant->merchantAddress,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'demo'=>$demo,
        ]);
        $subAdmin->assignRole($request->role_id);
        if ($request->admin_type == 2) {
            $subAdmin->role_areas = implode(",",$request->area_list);
            $subAdmin->save();
//            $subAdmin->CountryArea()->sync($request->area_list);
        }else{
            $subAdmin->role_areas = null;
            $subAdmin->save();
        }
        return redirect()->back()->withSuccess(trans("$string_file.added_successfully"));
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
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $subAdmin = Merchant::where([['parent_id', '=', $merchant_id]])->findOrFail($id);
        $subAdmin->merchantStatus = $status;
        $subAdmin->save();
        return redirect()->back()->withSuccess(trans("$string_file.status_updated"));
    }

    public function edit($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $subadmin = Merchant::with('CountryArea')->find($id);
        $roles = Role::where([['merchant_id', '=', $merchant_id]])->get();
        $area = $this->getAreaList(false);
        $areas = $area->get();
        $permission_area = explode(",",$subadmin->role_areas);
        return view('merchant.subadmin.edit', compact('roles', 'areas','subadmin','permission_area'));
    }

    public function update(Request $request, $id)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required|regex:/^[0-9+]+$/',
            'password' => 'required_if:edit_password,1',
            'admin_type' => 'required|integer|between:1,2',
            'area_list' => 'required_if:admin_type,2',
            'email' => ['required', 'email',
                Rule::unique('merchants', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where('parent_id', '=', $merchant_id)->orWhere('id', '=', $merchant_id);
                })->ignore($id)],
            'role_id' => 'required|exists:roles,id'
        ]);
        $subAdmin = Merchant::where([['parent_id', '=', $merchant_id]])->findOrFail($id);
        $subAdmin->merchantFirstName = $request->first_name;
        $subAdmin->merchantLastName = $request->last_name;
        $subAdmin->merchantPhone = $request->phone_number;
        $subAdmin->email = $request->email;
        if ($request->edit_password == 1) {
            $subAdmin->password = Hash::make($request->password);
        }
        $subAdmin->save();
        $subAdmin->syncRoles($request->role_id);
        if ($request->admin_type == 2) {
            $subAdmin->role_areas = implode(",",$request->area_list);
            $subAdmin->save();
//            $subAdmin->GetCountryArea()->sync($request->area_list);
        }else{
            $subAdmin->role_areas = null;
            $subAdmin->save();
        }
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

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
}
