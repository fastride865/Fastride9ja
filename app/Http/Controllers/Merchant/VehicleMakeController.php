<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use App\Models\VehicleMake;
use Auth;
use App;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Traits\ImageTrait;
use App\Traits\MerchantTrait;

class VehicleMakeController extends Controller
{
    use ImageTrait,MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','VEHICLE_MAKE')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index(Request $request)
    {
        $merchant_id = get_merchant_id();
        $checkPermission =  check_permission(1,'view_vehicle_make');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $vehicle_make = $request->vehicle_make;

        $query = VehicleMake::where([['merchant_id', '=', $merchant_id],['admin_delete', '=', NULL]]);
        if(!empty($vehicle_make))
        {
            $query->with(['LanguageVehicleMakeSingle'=>function($q) use($vehicle_make,$merchant_id){
                $q->where('vehicleMakeName',$vehicle_make)->where('merchant_id',$merchant_id);
            }])->whereHas('LanguageVehicleMakeSingle',function($q) use($vehicle_make,$merchant_id){
                $q->where('vehicleMakeName',$vehicle_make)->where('merchant_id',$merchant_id);
            });
        }

        $vehiclemakes =$query->paginate(10);
        $arr_vehicle_make['search_route'] = route('vehiclemake.index');
        $arr_vehicle_make['arr_search'] = $request->all();
        $arr_vehicle_make['merchant_id'] = $merchant_id;
        $arr_vehicle_make['vehicle_make'] = $vehicle_make;

        return view('merchant.vehiclemake.index', compact('vehiclemakes','arr_vehicle_make'));
    }

    public function store(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $this->validate($request, [
            'vehicle_make' => ['required',
                Rule::unique('language_vehicle_makes', 'vehicleMakeName')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', App::getLocale()]]);
                })],
            'vehicle_make_logo' => "Required",
            'description' => 'Required'
        ]);
        DB::beginTransaction();
        try {
            $vehicle_make = VehicleMake::create([
                'merchant_id' => $merchant_id,
                'vehicleMakeLogo' => $this->uploadImage('vehicle_make_logo', 'vehicle'),
            ]);
            $this->SaveLanguageVehicle($merchant_id, $vehicle_make->id, $request->vehicle_make, $request->description);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->back()->with('vehiclemakeadded', 'Make added');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $checkPermission =  check_permission(1,'edit_vehicle_type');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $is_demo = $merchant->demo == 1 ? true : false;
        $merchant_id = $merchant->id;
        $vehiclemake = VehicleMake::where([['merchant_id', '=', $merchant_id]])->find($id);
        return view('merchant.vehiclemake.edit', compact('vehiclemake','is_demo'));
    }

    public function update(Request $request, $id)
    {
        $locale = App::getLocale();
        $merchant_id = get_merchant_id();
        $request->validate([
            'vehicle_make' => ['required', 'max:255',
                Rule::unique('language_vehicle_makes', 'vehicleMakeName')->where(function ($query) use ($merchant_id, &$locale, &$id) {
                    $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', $locale], ['vehicle_make_id', '!=', $id]]);
                })],
            'description' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $vehicleMake = VehicleMake::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
            if ($request->hasFile('vehicleMakeLogo')) {
                $vehicleMake->vehicleMakeLogo = $this->uploadImage('vehicleMakeLogo', 'vehicle');
                $vehicleMake->save();
            }
            $this->SaveLanguageVehicle($merchant_id, $id, $request->vehicle_make, $request->description);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->back()->with('vehiclemakeadded', 'Make added');
    }

    public function SaveLanguageVehicle($merchant_id, $vehicle_make_id, $name, $description)
    {
        App\Models\LanguageVehicleMake::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'vehicle_make_id' => $vehicle_make_id
        ], [
            'vehicleMakeName' => $name,
            'vehicleMakeDescription' => $description,
        ]);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $delete = VehicleMake::FindorFail($id);
        $string_file = $this->getStringFile(NULL, $delete->Merchant);
        if (!empty($delete->id)):
            $delete->admin_delete = 1;
            $delete->save();
            echo trans("$string_file.data_deleted_successfully");
        else:
            echo trans("$string_file.some_thing_went_wrong");
        endif;
    }
}
