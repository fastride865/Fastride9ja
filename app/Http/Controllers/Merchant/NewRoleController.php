<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\Merchant as helperMerchant;
use App\Models\InfoSetting;
use App\Models\Merchant;
use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Traits\MerchantTrait;
use Auth;
use DB;

class NewRoleController extends Controller
{
    use MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'SUB_ADMIN_ROLE')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $checkPermission = check_permission(1, 'view_role');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        if(Auth::user('merchant')->parent_id == 0){
            $roles = Role::where([['merchant_id', '=', $merchant_id]])->paginate(25);
        }else{
            $roles = Role::where([['merchant_id', '=', $merchant_id],['name', '!=', "Super Admin" . $merchant_id]])->paginate(25);
        }
        $string_file = $this->getStringFile($merchant_id);
        return view('merchant.new-role.index', ['roles' => $roles, 'string_file' => $string_file]);
    }

    public function create(Request  $request,$id = NULL)
    {

        $merchant = get_merchant_id(false);
        $is_demo = $merchant->demo == 1 ? true : false;
        $merchant_id = $merchant->id;
        $checkPermission = check_permission(1, 'create_role');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $role = [];
        $permission_array = [];
        if ($id != NULL) {
            $role = Role::findOrFail($id);
            $permission_array = array_pluck($role->getAllPermissions(), 'id');
        }
        $permissions = Permission::where("permission_type", 1)->get()->toArray();
        $permissions = CommonController::buildTree($permissions);
        $type_two_permissions = Permission::where("permission_type", 2)->get()->toArray();
        $type_two_permissions = CommonController::buildTree($type_two_permissions);
        $return = $this->getTypeTwoPermission($merchant_id, $type_two_permissions);
        if ($return) {
            $type_two_permissions = Permission::where("permission_type", 2)->get()->toArray();
            $type_two_permissions = CommonController::buildTree($type_two_permissions);
        }
        return view('merchant.new-role.create', ['role' => $role, 'permission_array' => $permission_array, 'permissions' => $permissions, 'type_two_permissions' => $type_two_permissions,'is_demo'=>$is_demo]);
    }

    public function store(Request $request, $id = NULL)
    {
//        p($request->all());
        DB::beginTransaction();
        try {
            $merchant_id = get_merchant_id();
            $string_file = $this->getStringFile($merchant_id);
            $name = $request->name;
            $request->request->add(['name' => $name.$merchant_id, 'displayName' => $name]);
            $request->validate([
                'name' => ['required',
                    Rule::unique('roles', 'name')->where(function ($query) use ($merchant_id, $id) {
                        return $query->where('merchant_id', '=', $merchant_id);
                    })->ignore($id)],
                'description' => 'required',
                'permission' => 'required'
            ]);
            if ($id != NULL) {
                $role = Role::find($id);
            } else {
                $role = new Role();
                $role->merchant_id = $merchant_id;
                $role->guard_name = 'merchant';
            }
            $role->name = $request->name;
            $role->display_name = $request->displayName;
            $role->description = $request->description;
            $role->save();
            if ($id != NULL) {
                $role->syncPermissions($request->permission);
                $message = trans("$string_file.saved_successfully");
            } else {
                $role->givePermissionTo($request->permission);
                $message = trans("$string_file.added_successfully");
            }
            DB::commit();
            return redirect()->back()->withSuccess($message);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function getTypeTwoPermission($merchant_id, $existing_permissions)
    {
        $store_permissions = [];
        $merchant = Merchant::find($merchant_id);
        $merchant_segment = helperMerchant::MerchantSegments(1);
        $merchant_segment_group = helperMerchant::MerchantSegments(2);
        $segments = $merchant->Segment->whereIn('sub_group_for_app', [1, 2]); // Food & Grocery and its clone
        foreach ($segments as $segment) {
            $status = array_search($segment->slag, array_column($existing_permissions, 'name'));
            if ($status === false || empty($existing_permissions)) {
                $type_segment = ($segment->sub_group_for_app == 2) ? 'GROCERY' : 'FOOD';
                $all_segment_permissions = $this->getPermissionsArr($type_segment, $segment->slag, $segment->name);
                $store_permissions = array_merge($store_permissions, $all_segment_permissions);
            }
        }
        if (in_array('TAXI', $merchant_segment)) {
            $status = array_search('TAXI', array_column($existing_permissions, 'name'));
            if ($status === false || empty($existing_permissions)) {
                $all_segment_permissions = $this->getPermissionsArr("TAXI", "TAXI", "Taxi");
                $store_permissions = array_merge($store_permissions, $all_segment_permissions);
            }
        }
        if (in_array('DELIVERY', $merchant_segment)) {
            $status = array_search('DELIVERY', array_column($existing_permissions, 'name'));
            if ($status === false || empty($existing_permissions)) {
                $all_segment_permissions = $this->getPermissionsArr('DELIVERY', 'DELIVERY', "Delivery");
                $store_permissions = array_merge($store_permissions, $all_segment_permissions);
            }
        }
        if (in_array(2, $merchant_segment_group)) {
            $status = array_search('HANDYMAN', array_column($existing_permissions, 'name'));
            if ($status === false || empty($existing_permissions)) {
                $all_segment_permissions = $this->getPermissionsArr('HANDYMAN', 'HANDYMAN', "Handyman");
                $store_permissions = array_merge($store_permissions, $all_segment_permissions);
            }
        }
        if (!empty($store_permissions)) {
            $this->storeNewSegmentPermission($store_permissions);
            return true;
        } else {
            return false;
        }
    }

    public function getPermissionsArr($permission_type, $slug, $name = "NA")
    {
        $business_segment_permissions = [];
        switch ($permission_type) {
            case "FOOD":
                $business_segment_permissions = array(
                    array('name' => $slug, 'special_permission' => '1', 'display_name' => $name . ' Segment', 'guard_name' => 'merchant', 'permission_type' => 2, 'children' => array(
                        array('name' => 'create_business_segment_' . $slug, 'special_permission' => '0', 'display_name' => 'Create Business Segment', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'view_business_segment_' . $slug, 'special_permission' => '0', 'display_name' => 'View Business Segment', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'order_statistics_' . $slug, 'special_permission' => '0', 'display_name' => 'View Order/Bookings statistics', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'order_management_' . $slug, 'special_permission' => '1', 'display_name' => 'Order Management', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'price_card_' . $slug, 'special_permission' => '1', 'display_name' => 'Price Card', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'promo_code_' . $slug, 'special_permission' => '0', 'display_name' => 'Promo Code', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'cancel_reason_' . $slug, 'special_permission' => '0', 'display_name' => 'Cancel Reason', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'business_segment_cash_out_' . $slug, 'special_permission' => '0', 'display_name' => 'Business Segment Cashout', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'weight_unit_' . $slug, 'special_permission' => '1', 'display_name' => 'Weight Unit', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'category_' . $slug, 'special_permission' => '1', 'display_name' => 'Category', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'style_management_' . $slug, 'special_permission' => '1', 'display_name' => 'Style Management', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'driver_agency_' . $slug, 'special_permission' => '1', 'display_name' => 'Driver Agency', 'guard_name' => 'merchant', 'permission_type' => 2),
                    )),
                );
                break;
            case "GROCERY":
                $business_segment_permissions = array(
                    array('name' => $slug, 'special_permission' => '1', 'display_name' => $name . ' Segment', 'guard_name' => 'merchant', 'permission_type' => 2, 'children' => array(
                        array('name' => 'create_business_segment_' . $slug, 'special_permission' => '0', 'display_name' => 'Create Business Segment', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'view_business_segment_' . $slug, 'special_permission' => '0', 'display_name' => 'View Business Segment', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'order_statistics_' . $slug, 'special_permission' => '0', 'display_name' => 'View Order/Bookings statistics', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'ride_management_' . $slug, 'special_permission' => '1', 'display_name' => 'Ride Management', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'price_card_' . $slug, 'special_permission' => '1', 'display_name' => 'Price Card', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'promo_code_' . $slug, 'special_permission' => '0', 'display_name' => 'Promo Code', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'cancel_reason_' . $slug, 'special_permission' => '0', 'display_name' => 'Cancel Reason', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'business_segment_cash_out_' . $slug, 'special_permission' => '0', 'display_name' => 'Business Segment Cashout', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'weight_unit_' . $slug, 'special_permission' => '1', 'display_name' => 'Weight Unit', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'category_' . $slug, 'special_permission' => '1', 'display_name' => 'Category', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'service_time_slot_' . $slug, 'special_permission' => '1', 'display_name' => 'Service Time Slot', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'driver_agency_' . $slug, 'special_permission' => '1', 'display_name' => 'Driver Agency', 'guard_name' => 'merchant', 'permission_type' => 2),
                    )),
                );
                break;
            case "HANDYMAN":
                $business_segment_permissions = array(
                    array('name' => $slug, 'special_permission' => '1', 'display_name' => $name . ' Segment', 'guard_name' => 'merchant', 'permission_type' => 2, 'children' => array(
                        array('name' => 'booking_management_' . $slug, 'special_permission' => '1', 'display_name' => 'Booking Management', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'price_card_' . $slug, 'special_permission' => '1', 'display_name' => 'Price Card', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'promo_code_' . $slug, 'special_permission' => '0', 'display_name' => 'Promo Code', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'cancel_reason_' . $slug, 'special_permission' => '0', 'display_name' => 'Cancel Reason', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'handyman_charge_type_' . $slug, 'special_permission' => '1', 'display_name' => 'Cancel Reason', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'service_time_slot_' . $slug, 'special_permission' => '1', 'display_name' => 'Service Time Slot', 'guard_name' => 'merchant', 'permission_type' => 2),
                    )),
                );
                break;
            case "DELIVERY":
                $business_segment_permissions = array(
                    array('name' => $slug, 'special_permission' => '1', 'display_name' => $name . ' Segment', 'guard_name' => 'merchant', 'permission_type' => 2, 'children' => array(
                        array('name' => 'ride_management_' . $slug, 'special_permission' => '1', 'display_name' => 'Ride Management', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'price_card_' . $slug, 'special_permission' => '1', 'display_name' => 'Price Card', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'promo_code_' . $slug, 'special_permission' => '0', 'display_name' => 'Promo Code', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'cancel_reason_' . $slug, 'special_permission' => '0', 'display_name' => 'Cancel Reason', 'guard_name' => 'merchant', 'permission_type' => 2),
                    )),
                );
                break;
            case "TAXI":
                $business_segment_permissions = array(
                    array('name' => $slug, 'special_permission' => '1', 'display_name' => $name . ' Segment', 'guard_name' => 'merchant', 'permission_type' => 2, 'children' => array(
                        array('name' => 'ride_management_' . $slug, 'special_permission' => '1', 'display_name' => 'Ride Management', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'price_card_' . $slug, 'special_permission' => '1', 'display_name' => 'Price Card', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'promo_code_' . $slug, 'special_permission' => '0', 'display_name' => 'Promo Code', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'cancel_reason_' . $slug, 'special_permission' => '0', 'display_name' => 'Cancel Reason', 'guard_name' => 'merchant', 'permission_type' => 2),
                        // Taxi Based
                        array('name' => 'manualdispach', 'special_permission' => '0', 'display_name' => 'Manual Dispach', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'corporate', 'special_permission' => '1', 'display_name' => 'Corporate', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'taxi_company', 'special_permission' => '1', 'display_name' => 'Taxi Company', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'franchisee', 'special_permission' => '1', 'display_name' => 'Franchisee', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'hotel', 'special_permission' => '1', 'display_name' => 'Hotel', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'security_question', 'special_permission' => '1', 'display_name' => 'Security Question', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'subscription_package', 'special_permission' => '1', 'display_name' => 'Subscription Package', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'reward_points', 'special_permission' => '1', 'display_name' => 'Reward Points', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'package', 'special_permission' => '1', 'display_name' => 'Packages', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'driver_commission_choices', 'special_permission' => '1', 'display_name' => 'Driver Commission Choices', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'navigation_drawers', 'special_permission' => '0', 'display_name' => 'Navigation Drawers', 'guard_name' => 'merchant', 'permission_type' => 2),
                        array('name' => 'surcharge', 'special_permission' => '1', 'display_name' => 'SurCharge', 'guard_name' => 'merchant', 'permission_type' => 2),
                    )),
                );
                break;
        }
        return $business_segment_permissions;
    }

    public function storeNewSegmentPermission($permissions)
    {
        DB::beginTransaction();
        try {
            foreach ($permissions as $permission) {
                $new_permission = new Permission();
                $new_permission->parent_id = 0;
                $new_permission->name = $permission['name'];
                $new_permission->special_permission = $permission['special_permission'];
                $new_permission->display_name = $permission['display_name'];
                $new_permission->guard_name = $permission['guard_name'];
                $new_permission->permission_type = $permission['permission_type'];
                $new_permission->save();
                if (!empty($permission['children'])) {
                    foreach ($permission['children'] as $child_permission) {
                        $new_child_permission = new Permission();
                        $new_child_permission->parent_id = $new_permission->id;
                        $new_child_permission->name = $child_permission['name'];
                        $new_child_permission->special_permission = $child_permission['special_permission'];
                        $new_child_permission->display_name = $child_permission['display_name'];
                        $new_child_permission->guard_name = $child_permission['guard_name'];
                        $new_child_permission->permission_type = $child_permission['permission_type'];
                        $new_child_permission->save();
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            p($e->getTraceAsString());
            p($e->getMessage());
        }
        DB::commit();
    }
}
