<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use App\Models\LanguageVehicleType;
use Auth;
use App;
use DB;
use App\Models\VehicleType;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Http\Requests\VehicleTypeRequest;
use App\Models\DriverVehicle;
use App\Traits\MerchantTrait;
use App\Traits\ImageTrait;

class VehicleTypeController extends Controller
{

   use MerchantTrait,ImageTrait;
    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','VEHICLE_TYPE')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index(Request $request)
    {
        $checkPermission =  check_permission(1,'view_vehicle_type');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $arr_vehicle_type = [];
        $vehicle_type = $request->vehicle_type;
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $vehicle_model_expire = $merchant->Configuration->vehicle_model_expire;
        $query = VehicleType::where([['merchant_id', '=', $merchant_id],['admin_delete', '=', NULL]]);
        if(!empty($vehicle_type))
        {
            $query->with(['LanguageVehicleTypeSingle'=>function($q) use($vehicle_type,$merchant_id){
                $q->where('vehicleTypeName',$vehicle_type)->where('merchant_id',$merchant_id);
            }])->whereHas('LanguageVehicleTypeSingle',function($q) use($vehicle_type,$merchant_id){
                $q->where('vehicleTypeName',$vehicle_type)->where('merchant_id',$merchant_id);
            });
        }

        $vehicles =  $query->latest()->paginate(10);
        $segment = array_pluck($merchant->Segment, 'slag');
        $one_vehicle_segment = array('2' => 'FOOD','3' => 'GROCERY');
        sort($segment);
        sort($one_vehicle_segment);
        $one_type_check = false;
        if ($segment == $one_vehicle_segment){
            $one_type_check = true;
        }

        $arr_vehicle_type['search_route'] = route('vehicletype.index');
        $arr_vehicle_type['arr_search'] = $request->all();
        $arr_vehicle_type['merchant_id'] = $merchant_id;
        $arr_vehicle_type['vehicle_type'] = $vehicle_type;
//p($arr_vehicle_type);
       // $delivery_types = DeliveryType::where('merchant_id' , $merchant_id)->get();
        return view('merchant.vehicletype.index', compact('vehicles', 'merchant', 'segment', 'one_type_check','vehicle_model_expire','arr_vehicle_type'));
    }

    public function store(VehicleTypeRequest $request)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        DB::beginTransaction();
        try {
            $vehicle_type = VehicleType::create([
                'merchant_id' => $merchant_id,
                'vehicleTypeImage' => $this->uploadImage('vehicle_image', 'vehicle'),
                'vehicleTypeDeselectImage' => "",
                'vehicleTypeMapImage' => $request->vehicle_map_image,
                'vehicleTypeRank' => $request->vehicle_rank,
                'pool_enable' => $request->pool_enable,
                'sequence' => $request->sequence,
                'ride_now' => $request->ride_now,
                'ride_later' => $request->ride_later,
                'model_expire_year' => $request->model_expire_year,
//                'delivery_type_id' => $request->delivery_name
            ]);
            $this->SaveLanguageVehicle($merchant_id, $vehicle_type->id, $request->vehicle_name, $request->description);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
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
        $merchant_id = $merchant->id;
        $vehicle = VehicleType::where([['merchant_id', '=', $merchant_id]])->find($id);
        $segment = array_pluck($merchant->Segment, 'slag');
        $vehicle_model_expire = $merchant->Configuration->vehicle_model_expire;
        $is_demo = $merchant->demo == 1 ? true : false;
       // $delivery_types = DeliveryType::where('merchant_id' , $merchant_id)->get();
        return view('merchant.vehicletype.edit', compact('vehicle', 'merchant', 'segment','vehicle_model_expire','is_demo'));
    }

    public function update(Request $request, $id)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $locale = App::getLocale();
        $request->validate([
            'vehicle_name' => ['required', 'max:255',
                Rule::unique('language_vehicle_types', 'vehicleTypeName')->where(function ($query) use ($merchant_id, &$locale, &$id) {
                    $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', $locale], ['vehicle_type_id', '!=', $id]]);
                })],
            'vehicle_rank' => 'required|integer',
            'description' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $vehicle = VehicleType::where([['merchant_id', '=', $merchant_id]])->find($id);
            if ($vehicle->pool_enable == 1 && empty($request->pool_enable)) {

                $getDriverIds = DriverVehicle::whereHas('Drivers', function ($q) {
                    $q->where([['vehicle_active_status', '=', 1]]);
                })->with(['Driver' => function ($q) {
                    $q->where([['pool_ride_active', '=', 1]]);
                }])->where([['vehicle_type_id', '=', $id]])->get();

                if (!empty($getDriverIds->toArray())) {
                    App\Models\Driver::whereIn('id', array_pluck($getDriverIds, 'driver_id'))->update(['pool_ride_active' => 2]);
                }
            }
            $vehicle->vehicleTypeStatus = 1;
            $vehicle->pool_enable = $request->pool_enable;
            $vehicle->ride_now = $request->ride_now;
            $vehicle->ride_later = $request->ride_later;
            $vehicle->vehicleTypeRank = $request->vehicle_rank;
            $vehicle->model_expire_year = $request->model_expire_year;
            if ($request->hasFile('vehicle_image')) {
                $vehicle->vehicleTypeImage = $this->uploadImage('vehicle_image', 'vehicle');
            }
            if ($request->hasFile('vehicle_deselected_image')) {
                $vehicle->vehicleTypeDeselectImage = $this->uploadImage('vehicle_deselected_image', 'vehicle');
            }
            $vehicle->vehicleTypeMapImage = $request->vehicleTypeMapImage;
            $vehicle->sequence = $request->sequence;
            //$vehicle->delivery_type_id = $request->delivery_name;

            $vehicle->save();
            $this->SaveLanguageVehicle($merchant_id, $id, $request->vehicle_name, $request->description);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function SaveLanguageVehicle($merchant_id, $vehicle_type_id, $name, $description)
    {
        LanguageVehicleType::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'vehicle_type_id' => $vehicle_type_id
        ], [
            'vehicleTypeName' => $name,
            'vehicleTypeDescription' => $description,
        ]);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $delete = VehicleType::FindorFail($id);
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
