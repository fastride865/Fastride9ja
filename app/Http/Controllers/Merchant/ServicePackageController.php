<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use Auth;
use App;
use App\Models\ServicePackage;
use App\Models\LanguageServicePackage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\MerchantTrait;
use DB;

class ServicePackageController extends Controller
{
    use MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','RENTAL_PACKAGE_MANAGEMENT')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $packages = ServicePackage::with('ServiceType')->where([['merchant_id', '=', $merchant_id]])->paginate(25);
        $arr_services = $this->getAdditionalSupportServices(null,1);
        return view('merchant.service-package.index', compact('packages','arr_services'));
    }

    public function create()
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        return view('merchant.service-package.create');
    }

    public function store(Request $request)
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $service_type_id = $request->service_type_id;
        $locale = App::getLocale();
        $request->validate([
            'name' => ['required', 'max:255',
                Rule::unique('language_service_packages')->where(function ($query) use ($merchant_id, &$locale,$service_type_id) {
                    $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', $locale], ['service_type_id', '=', $service_type_id]]);
                })],
            'description' => 'required',
            'terms_conditions' => 'required',
            'service_type_id' => 'required',
        ]);
        DB::beginTransaction();
        try {
        $package = ServicePackage::create([
            'merchant_id' => $merchant_id,
            'service_type_id' => $service_type_id,
        ]);
        $this->SaveLanguagePackage($merchant_id, $package->id, $request->name, $request->description, $request->terms_conditions, $service_type_id);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // p($message);
            return redirect()->back()->withErrors( $message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function SaveLanguagePackage($merchant_id, $package_id, $name, $description, $terms_conditions, $service_type_id)
    {
        LanguageServicePackage::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'service_package_id' => $package_id, 'service_type_id' => $service_type_id
        ], [
            'name' => $name,
            'description' => $description,
            'terms_conditions' => $terms_conditions,
        ]);
    }

    public function edit($id)
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $is_demo = $merchant->demo == 1 ? true : false;
        $package = ServicePackage::find($id);
        return view('merchant.service-package.edit', compact('package','is_demo'));
    }

    public function update(Request $request, $id)
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $service_type_id = $request->service_type_id;
        $locale = App::getLocale();
        $request->validate([
            'name' => ['required', 'max:255',
                Rule::unique('language_service_packages')->where(function ($query) use ($merchant_id, &$locale, &$id,$service_type_id) {
                    $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', $locale], ['service_package_id', '!=', $id], ['service_type_id', '=', $service_type_id]]);
                })],
            'description' => 'required',
            'terms_conditions' => 'required',
        ]);
        $package = ServicePackage::findorFail($id);
        $this->SaveLanguagePackage($merchant_id, $package->id, $request->name, $request->description, $request->terms_conditions, $service_type_id);
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
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        if($merchant->demo == 1)
        {
            return redirect()->back()->withErrors(trans("$string_file.demo_warning_message"));
        }
        $package = ServicePackage::findOrFail($id);
        $package->packageStatus = $status;
        $package->save();
        return redirect()->back()->withSuccess(trans("$string_file.status_updated"));
    }

}
