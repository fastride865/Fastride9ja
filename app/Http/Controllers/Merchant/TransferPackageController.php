<?php

namespace App\Http\Controllers\Merchant;

use Auth;
use App;
use App\Models\Package;
use App\Models\LanguagePackage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class TransferPackageController extends Controller
{

    public function index()
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $packages = Package::where([['merchant_id', '=', $merchant_id], ['service_type_id', '=', 3]])->paginate(25);
        return view('merchant.package.transfer_index', compact('packages'));
    }

    public function store(Request $request)
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $locale = App::getLocale();
        $request->validate([
            'name' => ['required', 'max:255',
                Rule::unique('language_packages')->where(function ($query) use ($merchant_id, &$locale) {
                    $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', $locale], ['service_type_id', '=', 3]]);
                })],
            'description' => 'required',
            'terms_conditions' => 'required',
        ]);
        $package = Package::create([
            'merchant_id' => $merchant_id,
            'service_type_id' => 3,
        ]);
        $this->SaveLanguagePackage($merchant_id, $package->id, $request->name, $request->description, $request->terms_conditions, 3);
        return redirect()->back()->with('package', 'Package Added');
    }

    public function SaveLanguagePackage($merchant_id, $package_id, $name, $description, $terms_conditions, $service_type_id)
    {
        LanguagePackage::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'package_id' => $package_id, 'service_type_id' => $service_type_id
        ], [
            'name' => $name,
            'description' => $description,
            'terms_conditions' => $terms_conditions,
        ]);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $package = Package::where([['merchant_id', '=', $merchant_id]])->find($id);
        return view('merchant.package.transfer_edit.blade.php', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $checkPermission = check_permission(1, ['TAXI','package']);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $locale = App::getLocale();
        $request->validate([
            'name' => ['required', 'max:255',
                Rule::unique('language_packages')->where(function ($query) use ($merchant_id, &$locale, &$id) {
                    $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', $locale], ['package_id', '!=', $id], ['service_type_id', '=', 3]]);
                })],
            'description' => 'required',
            'terms_conditions' => 'required',
        ]);
        $package = Package::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
        $this->SaveLanguagePackage($merchant_id, $package->id, $request->name, $request->description, $request->terms_conditions, 3);
        return redirect()->back()->with('package', 'Package Added');
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
