<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use App\Models\LanguageVehicleModel;
use App\Models\VehicleMake;
use App\Models\VehicleType;
use Auth;
use App;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use DB;
use App\Traits\MerchantTrait;

class VehicleModelController extends Controller
{
    use MerchantTrait;
    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','VEHICLE_MODEL')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index(Request $request)
    {
        $checkPermission =  check_permission(1,'view_vehicle_model');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $vehicle_model = $request->vehicle_model;
        $merchant_id = get_merchant_id();
        $query = VehicleModel::where([['merchant_id', '=', $merchant_id],['admin_delete', '=', NULL]])
            ->whereHas('VehicleType',function($q){
                $q->where('admin_delete',NULL);
            })
            ->whereHas('VehicleMake',function($q){
                $q->where('admin_delete',NULL);
            })
        ;
              if(!empty($vehicle_model))
              {
                  $query->with(['LanguageVehicleModelSingle'=>function($q) use($vehicle_model,$merchant_id){
                      $q->where('vehicleModelName',$vehicle_model)->where('merchant_id',$merchant_id);
                  }])->whereHas('LanguageVehicleModelSingle',function($q) use($vehicle_model,$merchant_id){
                      $q->where('vehicleModelName',$vehicle_model)->where('merchant_id',$merchant_id);
                  });
              }
        $vehicleModels =     $query->paginate(25);
        $vehicles = VehicleType::where([['merchant_id', '=', $merchant_id],['admin_delete', '=', NULL]])->get();
        $vehiclemakes = VehicleMake::where([['merchant_id', '=', $merchant_id],['admin_delete', '=', NULL]])->get();
        $arr_vehicle_model['search_route'] = route('vehiclemodel.index');
        $arr_vehicle_model['arr_search'] = $request->all();
        $arr_vehicle_model['merchant_id'] = $merchant_id;
        $arr_vehicle_model['vehicle_model'] = $vehicle_model;
        return view('merchant.vehiclemodel.index', compact('vehicleModels', 'vehiclemakes', 'vehicles','arr_vehicle_model'));
    }

    public function store(Request $request)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $locale = App::getLocale();
        $this->validate($request, [
            'vehicletype' => "required",
            'vehiclemake' => "required",
            'vehicle_model' => ['required', 'max:255'],
            'description' => "required",
            'vehicle_seat' => "required|integer",
        ]);
        $model = DB::table('language_vehicle_models')->select('vehicle_models.id')
            ->where(function ($query) use ($merchant_id, &$locale,$request) {
            $query->where([['language_vehicle_models.merchant_id', '=', $merchant_id], ['locale', '=', $locale],['vehicleModelName','=',$request->vehicle_model]]);
        })->join('vehicle_models','vehicle_models.id','=','language_vehicle_models.vehicle_model_id')
            ->where([['vehicle_models.vehicle_type_id', '=', $request->vehicletype]])
            ->where([['vehicle_models.vehicle_make_id', '=', $request->vehiclemake]])
            ->first();

        if(empty($model->id))
        {
            $vehicleModel = VehicleModel::create([
                'merchant_id' => $merchant_id,
                'vehicle_type_id' => $request->vehicletype,
                'vehicle_make_id' => $request->vehiclemake,
                'vehicle_seat' => $request->vehicle_seat,
            ]);
            $this->SaveLanguageVehicleModel($merchant_id, $vehicleModel->id, $request->vehicle_model, $request->description);
            return redirect()->route("vehiclemodel.index")->withSuccess(trans("$string_file.saved_successfully"));
        }
        else
        {
            return redirect()->back()->withErrors(trans("$string_file.vehicle_model_already_exist"));
        }
    }

    public function SaveLanguageVehicleModel($merchant_id, $vehicle_model_id, $name, $description)
    {
        LanguageVehicleModel::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'vehicle_model_id' => $vehicle_model_id
        ], [
            'vehicleModelName' => $name,
            'vehicleModelDescription' => $description,
        ]);
    }

    public function edit($id)
    {
        $checkPermission =  check_permission(1,'edit_vehicle_model');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $is_demo = $merchant->demo == 1 ? true: false;
        $merchant_id = $merchant->id;
        $vehicleModel = VehicleModel::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $vehicles = VehicleType::where([['merchant_id', '=', $merchant_id]])->get();
        $vehiclemakes = VehicleMake::where([['merchant_id', '=', $merchant_id]])->get();
        return view('merchant.vehiclemodel.edit', compact('vehicleModel', 'vehiclemakes', 'vehicles','is_demo'));
    }

    public function update(Request $request, $id)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $locale = App::getLocale();
        $request->validate([
            'vehicletype' => "required",
            'vehiclemake' => "required",
            'vehicle_seat' => "integer",
            'vehicle_model' => ['required', 'max:255'],
            'description' => "required"
        ]);
        $model = DB::table('language_vehicle_models')->select('vehicle_models.id')
            ->where(function ($query) use ($merchant_id, &$locale,$request) {
                $query->where([['language_vehicle_models.merchant_id', '=', $merchant_id], ['locale', '=', $locale],['vehicleModelName','=',$request->vehicle_model]]);
            })
            ->join('vehicle_models','vehicle_models.id','=','language_vehicle_models.vehicle_model_id')
            ->where([['vehicle_models.vehicle_type_id', '=', $request->vehicletype],['vehicle_models.id','!=',$id]])
            ->where([['vehicle_models.vehicle_make_id', '=', $request->vehiclemake]])
            ->first();

        if(empty($model->id))
        {
            $vehicleModel = VehicleModel::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
            $vehicleModel->vehicle_type_id = $request->vehicletype;
            $vehicleModel->vehicle_make_id = $request->vehiclemake;
            $vehicleModel->vehicle_seat = $request->vehicle_seat;
            $vehicleModel->save();
            $this->SaveLanguageVehicleModel($merchant_id, $vehicleModel->id, $request->vehicle_model, $request->description);
            return redirect()->route("vehiclemodel.index")->withSuccess(trans("$string_file.saved_successfully"));
        }
        else
        {
            return redirect()->back()->withErrors(trans("$string_file.already_exist"));
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $delete = VehicleModel::FindorFail($id);
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
