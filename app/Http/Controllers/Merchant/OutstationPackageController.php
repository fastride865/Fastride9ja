<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use App\Models\OutstationPackageTranslation;
use Auth;
use App;
use App\Models\OutstationPackage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\MerchantTrait;

class OutstationPackageController extends Controller
{
    use MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','OUTSTATION_PACKAGE_MANAGEMENT')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $packages = OutstationPackage::where([['merchant_id', '=', $merchant_id]])->paginate(25);
        return view('merchant.outstation.index', compact('packages'));
    }
    
    public function create()
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $arr_services = $this->getAdditionalSupportServices(null,2);
        return view('merchant.outstation.create',compact('arr_services'));
    }

    public function store(Request $request)
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $city = $request->city;
        $city = explode(",", $city);
        $request->request->add(['city' => $city[0]]);
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $service_type_id = $request->service_type_id;
        $validator = Validator::make($request->all(),[
            'city' => ['required',
                Rule::unique('outstation_package_translations', 'city')->where(function ($query) use ($merchant_id,$service_type_id) {
                    return $query->where('merchant_id', $merchant_id);
                    return $query->where('service_type_id', $service_type_id);
                })],
            'service_type_id' => 'required',
            'description' => 'required',
            'lat' => 'required',
        ],[
            'lat.required' => 'Draw Area On Map',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        $latLOng = $request->lat ? $request->lat : $request->latlong;
        if (!json_decode($latLOng, true)) {
            return redirect()->back();
        }
        $package = OutstationPackage::create([
            'merchant_id' => $merchant_id,
            'service_type_id' => $service_type_id,
            'area_coordinates' =>$latLOng
        ]);
        $this->SaveLanguagePackage($merchant_id, $package->id, $request->city, $request->description,$service_type_id);
        return redirect()->route('outstationpackage.index')->withSuccess(trans("$string_file.saved_successfully"));;
    }

    public function SaveLanguagePackage($merchant_id, $package_id, $name, $description,$service_type_id)
    {
        OutstationPackageTranslation::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'outstation_package_id' => $package_id, 'service_type_id' => $service_type_id
        ], [
            'city' => $name,
            'description' => $description,
        ]);
    }

    public function edit($id)
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $package = OutstationPackage::findOrFail($id);
        $merchant = get_merchant_id(false);
        $is_demo = $merchant->demo == 1 ? true : false;
        return view('merchant.outstation.edit', compact('package','is_demo'));
    }

    public function update(Request $request, $id)
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $lang_id = OutstationPackageTranslation::where([['locale',\App::getLocale()],['outstation_package_id',$id]])->first()['id'];
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $service_type_id = $request->service_type_id;
        $request->validate([
            'city' => ['required',
                Rule::unique('outstation_package_translations', 'city')->where(function ($query) use ($merchant_id,$service_type_id) {
                     $query->where('merchant_id', $merchant_id);
                     $query->where('service_type_id', $service_type_id);
                })->ignore($lang_id)],
            'description' => 'required',
        ]);
        $package = OutstationPackage::findorFail($id);
        if (!empty($request->lat)):
            $package->area_coordinates = $request->lat;
            $package->save();
        endif;
        $this->SaveLanguagePackage($merchant_id, $package->id, $request->city, $request->description,$service_type_id);
//        request()->session()->flash('message',trans('admin.packagedetial'));
        return redirect()->route('outstationpackage.index')->withSuccess(trans("$string_file.saved_successfully"));;
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
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        if($merchant->demo == 1)
        {
            return redirect()->back()->withErrors(trans("$string_file.demo_warning_message"));
        }
        $package = OutstationPackage::findOrFail($id);
        $package->status = $status;
        $package->save();
        return redirect()->back()->withSuccess(trans("$string_file.status_updated"));
    }
}
