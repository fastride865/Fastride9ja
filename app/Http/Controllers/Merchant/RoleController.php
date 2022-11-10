<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Helper\CommonController;
use App\Models\InfoSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Traits\MerchantTrait;
use Auth;

class RoleController extends Controller
{
    use MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'SUB_ADMIN_ROLE')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $checkPermission =  check_permission(1,'view_role');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $roles = Role::where([['merchant_id', '=', $merchant_id]])->paginate(25);
        $string_file = $this->getStringFile($merchant_id);
        return view('merchant.role.index', ['roles' => $roles,'string_file'=>$string_file]);
    }

    public function create()
    {
        $checkPermission =  check_permission(1,'create_role');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $permissions = Permission::get()->toArray();
        $permissions = CommonController::buildTree($permissions);
        return view('merchant.role.create', ['permissions' => $permissions]);
    }

    public function store(Request $request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $name = $request->name;
        $request->request->add(['name' => $name . "_" . $merchant_id, 'displayName' => $name]);
        $request->validate([
            'name' => ['required',
                Rule::unique('roles', 'name')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', '=', $merchant_id);
                })],
            'description' => 'required',
            'permission' => 'required'
        ]);
        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->displayName,
            'merchant_id' => $merchant_id,
            'description' => $request->description,
            'guard_name' => 'merchant',
        ]);
        $role->givePermissionTo($request->permission);
        return redirect()->back()->withSuccess(trans("$string_file.added_successfully"));
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        $permission_array = array_pluck($role->getAllPermissions(),'id');
        $permissions = Permission::get()->toArray();
        $permissions = CommonController::buildTree($permissions);
        return view('merchant.role.show', ['permissions' => $permissions,'permission_array'=>$permission_array,'role'=>$role]);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permission_array = array_pluck($role->getAllPermissions(),'id');
        $permissions = Permission::get()->toArray();
        $permissions = CommonController::buildTree($permissions);
        return view('merchant.role.edit', ['permissions' => $permissions,'permission_array'=>$permission_array,'role'=>$role]);
    }

    public function update(Request $request, $id)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $name = $request->name;
        $request->request->add(['name' => $name . "_" . $merchant_id, 'displayName' => $name]);
        $request->validate([
            'name' => ['required',
                Rule::unique('roles', 'name')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', '=', $merchant_id);
                })->ignore($id)],
            'description' => 'required',
            'permission' => 'required'
        ]);
        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->display_name = $request->displayName;
        $role->description = $request->description;
        $role->save();
        $role->syncPermissions($request->permission);
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