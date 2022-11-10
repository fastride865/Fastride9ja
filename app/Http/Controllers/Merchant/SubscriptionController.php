<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use App\Models\LangSubscriptionPack;
use App\Traits\MerchantTrait;
use App\Traits\SubscriptionPackageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    use MerchantTrait, SubscriptionPackageTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'SUBSCRIPTION_PACKAGE')->first();
        view()->share('info_setting', $info_setting);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packages = $this->getAllPackages(true);
        return view('merchant.subscriptionpack.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function create()
//    {
//        $all_services = $this->getAllMerchantServices(false);
//        $all_durations = $this->getPackagesDuration(false)->get();
//        $all_areas = $this->getAllMerchantAreas(false)->get();
//        $package_type = \Config::get('custom.package_type');
//        return view('merchant.subscriptionpack.create', compact('all_services', 'all_durations','all_areas','package_type'));
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    public function store(Request $request)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $request->validate([
//            'name' => ['required', 'max:255',
//                /*Rule::unique('lang_subscription_packs')->where(function ($query) use ($merchant_id) {
//                    $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', \App::getLocale()]]);
//                })*/
//                ],
//            'description' => 'required',
//            'price' => 'required',
//            'max_trip' => 'required',
//            'services' => 'required|exists:service_types,id',
//            'areas' => 'required|exists:country_areas,id',
//            'package_duration' => ['required',
//                Rule::exists('package_durations', 'id')
//                /*->where(function ($query) use (&$merchant_id){
//                    return $query->where('merchant_id', $merchant_id);
//                })*/
//                ],
//            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|',
//            'package_type' => 'required',
//        ]);
//        $return = $this->SavePackage($request);
//
//        if($return):
//            request()->session()->flash('message', trans('admin.message103'));
//            return redirect()->back();
//        else:
//            request()->session()->flash('error', trans('admin.subspack_addederror'));
//            return redirect()->back();
//        endif;
//    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id)
//    {
//        $package_edit = $this->getAllPackages(false)->FindorFail($id);
//        $all_services = $this->getAllMerchantServices(false);
//        $all_areas = $this->getAllMerchantAreas(false)->get();
//        $all_durations = $this->getPackagesDuration(false)->get();
//        $selected_services = $package_edit->ServiceType()->pluck('service_type_id')->all();
//        $selected_areas = $package_edit->CountryArea()->pluck('country_area_id')->all();
//        return view('merchant.subscriptionpack.edit', [ 'all_services'=>$all_services,'all_durations'=>$all_durations,
//                                                        'all_areas'=>$all_areas,'selected_areas'=>$selected_areas,
//                                                            'package_edit'=>$package_edit,'selected_services'=>$selected_services]);
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, $id)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $request->validate([
//            'name' => ['required', 'max:255',
//                /*Rule::unique('lang_subscription_packs')->where(function ($query) use ($merchant_id,&$ignore_id) {
//                    $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', \App::getlocale()]]);
//                })->ignore(SubscriptionPackage::with('LangSubscriptionPackageSingle')->FindorFail($id)->LangSubscriptionPackageSingle['id']
//                )*/
//                ],
//            'description' => 'required',
//            'price' => 'required',
//            'max_trip' => 'required',
//            'services' => 'required|exists:service_types,id',
//            'areas' => 'required|exists:country_areas,id',
//            'package_duration' => ['required',
//                Rule::exists('package_durations', 'id')
//                /*->where(function ($query) use (&$merchant_id){
//                    return $query->where('merchant_id', $merchant_id);
//                })*/
//                ],
//            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|',
//        ]);
//        $return  = $this->UpdatePackage($request,$id);
//        if($return):
//            request()->session()->flash('message', trans('admin.subspack_updated'));
//            return redirect()->back()->with('packageupdated', 'Package Updated');
//        else:
//            request()->session()->flash('error', trans('admin.subspack_addederror'));
//            return redirect()->back();
//        endif;
//    }


    public function Change_Status($id = null , $status = null)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $change = $this->getAllPackages(false)->FindorFail($id);
        $change->status = $status;
        $change->save();
//        if ($status == 1)
//        {
//            request()->session()->flash('message', 'Package has been Activated!');
//        } else {
//            request()->session()->flash('error', 'Package has been Deactivated!');
//        }
        return redirect()->route('subscription.index')->withSuccess(trans("$string_file.status_updated"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $delete = $this->getAllPackages(false)->FindorFail($id);
        $delete->status = 0;
        $delete->admin_delete = 1;
        $delete->save();
        request()->session()->flash('error', trans('admin.subspack_deleted'));
        echo trans("$string_file.deleted");
//        return redirect('merchant/admin/subscription');
    }

    /**
     * Add Edit form of duration
     */
    public function add(Request $request, $id = null)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $data = [];
        $arr_duration = [];
        $arr_area = [];
        $selected_services = [];
        $all_services = $this->getAllMerchantServices(false);
        $all_durations = $this->getPackagesDuration(false)->get();
        foreach ($all_durations  as $durations)
        {
            $arr_duration[$durations->id] = $durations->NameAccMerchant;
        }
        $all_areas = $this->getAllMerchantAreas(false)->get();
        foreach ($all_areas  as $areas)
        {
            $arr_area[$areas->id] = $areas->CountryAreaName;
        }
        $package_type = \Config::get('custom.package_type');

        if(!empty($id))
        {
            $data = SubscriptionPackage::findorfail($id);
            if($data->status != 1)
            {
                request()->session()->flash('error', trans('admin.deactivated_package'));
                return redirect()->route('subscription.index');
            }
            $selected_services = $data->ServiceType()->pluck('service_type_id')->all();
            $pre_title = trans("$string_file.edit");
            $submit_button = trans("$string_file.save");
        }
        else
        {
            $pre_title = trans("$string_file.add");
            $submit_button = trans("$string_file.update");
        }
        $title = $pre_title.' '.trans("$string_file.package");
        $return = [
            'package_edit'=>$data,
            'submit_url'=>url('merchant/admin/subscription/save/'.$id),
            'title'=>$title,
            'package_type'=>$package_type,
            'submit_button'=>$submit_button,
            'arr_area'=>$arr_area,
            'all_durations'=>add_blank_option($arr_duration,trans("$string_file.select")),
            'all_services'=>$all_services,
            'selected_services'=>$selected_services,
        ];
        return view('merchant.subscriptionpack.form')->with($return);
    }
    /***
     * Save/update function of duration
     */
    public function save(Request $request, $id = NULL)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $request->validate([
            'name' => ['required', 'max:255',
                ],
            'description' => 'required',
            'price' => 'required_if:package_type,==,2',
            'max_trip' => 'required',
            'services' => 'required|exists:service_types,id',
            'areas' => 'required|exists:country_areas,id',
            'package_duration' => ['required',
                Rule::exists('package_durations', 'id')
                ],
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|',
            'package_type' => 'required',
        ]);
            $request->id = $id;
            $return = $this->SavePackage($request);
            if($return):
                return redirect()->route('subscription.index')->withSuccess(trans("$string_file.package_saved_successfully"));
            else:
                return redirect()->route('subscription.index')->withErrors(trans("$string_file.some_thing_went_wrong"));
            endif;
    }
}
