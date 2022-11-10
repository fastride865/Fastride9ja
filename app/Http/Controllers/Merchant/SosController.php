<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use App\Models\LanguageSos;
use Auth;
use App;
use App\Models\Sos;
use App\Traits\SosTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Traits\MerchantTrait;

class SosController extends Controller
{
    use SosTrait,MerchantTrait;

    public function index()
    {
        $checkPermission =  check_permission(1,'view_sos_number');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $Sos = Sos::where([['merchant_id', '=', $merchant_id]])->paginate(25);
        $info_setting = InfoSetting::where('slug', 'SOS_NUMBER')->first();
        return view('merchant.sos.index', compact('Sos','info_setting'));
    }

    public function SearchSos(Request $request)
    {
        $merchant_id = get_merchant_id();
        $query = Sos::where([['merchant_id', '=', $merchant_id]]);
        if ($request->name) {
            $keyword = $request->name;
            $query->WhereHas('LanguageSingle', function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%");
            });
        }
        if ($request->number) {
            $query->where('number', 'LIKE', "%$request->number%");
        }
        $Sos = $query->paginate(25);
        $info_setting = InfoSetting::where('slug', 'SOS_NUMBER')->first();
        return view('merchant.sos.index', compact('Sos','info_setting'));
    }

    public function create()
    {
        $checkPermission =  check_permission(1,'create_sos_number');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $info_setting = InfoSetting::where('slug', 'SOS_NUMBER')->first();
        return view('merchant.sos.create', compact('info_setting'));
    }

    public function store(Request $request)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $locale = App::getLocale();
        $request->validate([
            'name' => ['required', 'max:255',
                Rule::unique('language_sos')->where(function ($query) use ($merchant_id, &$locale) {
                    $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', $locale]]);
                })],
            'number' => 'required',
            'application' => 'required',
        ]);
        $sos = Sos::create([
            'merchant_id' => $merchant_id,
            'number' => $request->number,
            'application' => $request->application,
        ]);
        $this->SaveLanguageSos($merchant_id, $sos->id, $request->name);
        return redirect()->route("sos.index")->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function show($id)
    {

    }

    public function edit($id)
    {
        $checkPermission =  check_permission(1,'edit_sos_number');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $sos = Sos::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
        $info_setting = InfoSetting::where('slug', 'SOS_NUMBER')->first();
        return view('merchant.sos.edit', compact('sos','info_setting'));
    }

    public function update(Request $request, $id)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $locale = App::getLocale();
        $request->validate([
            'number' => 'required',
            'name' => ['required', 'max:255',
                Rule::unique('language_sos')->where(function ($query) use ($merchant_id, &$locale, &$id) {
                    $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', $locale], ['sos_id', '!=', $id]]);
                })],
        ]);
        $sos = Sos::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $sos->number = $request->number;
        $sos->save();
        $this->SaveLanguageSos($merchant_id, $sos->id, $request->name);
        return redirect()->route("sos.index")->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function SaveLanguageSos($merchant_id, $sos_id, $name)
    {
        LanguageSos::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'sos_id' => $sos_id
        ], [
            'name' => $name,
        ]);
    }

    public function destroy($id)
    {
        $checkPermission =  check_permission(1,'delete_sos_number');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $sos = Sos::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $sos->delete();
        return redirect()->back()->with('success', 'SOS Deleted Successfully');
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
        $sos = Sos::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $sos->sosStatus = $status;
        $sos->save();
        return redirect()->back()->with('success', 'Status Updated');
    }

    public function SosRequest()
    {
        $checkPermission =  check_permission(1,'view_sos_request');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $sosRequests = $this->getAllSosRequest();
        $data = [];
        $info_setting = InfoSetting::where('slug', 'SOS_REQUEST')->first();
        return view('merchant.sos.request', compact('sosRequests','data','info_setting'));
    }

    public function SercahSosRequest(Request $request)
    {
        $checkPermission =  check_permission(1,'view_sos_request');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $query = $this->getAllSosRequest(false);
        if ($request->application) {
            $query->where('application', $request->application);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('Booking', function ($q) use ($keyword) {
                $q->WhereHas('User', function ($qu) use ($keyword) {
                    $qu->where('UserName', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
                });
            });
        }
        if ($request->driver) {
            $driverKeyword = $request->driver;
            $query->WhereHas('Booking', function ($q) use ($driverKeyword) {
                $q->WhereHas('Driver', function ($qu) use ($driverKeyword) {
                    $qu->where('fullName', 'LIKE', "%$driverKeyword%")->orWhere('email', 'LIKE', "%$driverKeyword%")->orWhere('phoneNumber', 'LIKE', "%$driverKeyword%");
                });
            });
        }
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }
        $sosRequests = $query->paginate(25);
        $data = $request->all();
        $info_setting = InfoSetting::where('slug', 'SOS_REQUEST')->first();
        return view('merchant.sos.request', compact('sosRequests','data','info_setting'));
    }
}
