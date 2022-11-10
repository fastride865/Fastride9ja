<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Country;
use App\Models\Driver;
use App\Models\LanguageCmsPage;
use App\Models\Onesignal;
use App\Models\User;
use Auth;
use App;
use App\Models\CmsPage;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\MerchantTrait;

class CmsPagesController extends Controller
{
    use MerchantTrait;
    public function __construct()
    {
        $info_setting = App\Models\InfoSetting::where('slug', 'CMS_PAGES')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $checkPermission = check_permission(1, 'view_cms');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $cmspages = CmsPage::where([['merchant_id', '=', $merchant_id], ['slug', '!=', 'terms_and_Conditions']])->latest()->paginate(25);
        $cmspages = CmsPage::with('Country')->where([['merchant_id', '=', $merchant_id]])->latest()->paginate(25);
        return view('merchant.cms.index', compact('cmspages'));
    }

    public function create()
    {
        $checkPermission = check_permission(1, 'create_cms');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $pages = Page::get();
        $merchant_id = get_merchant_id();
        $countries = Country::where([['country_status', '=', 1], ['merchant_id', '=', $merchant_id]])->get();
        return view('merchant.cms.create', compact('pages', 'countries'));
    }

    public function store(Request $request)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $request->validate([
            'application' => 'required|integer|between:1,2',
            'page' => 'required|exists:pages,slug',
            'title' => 'required',
            'description' => 'required',
            'country' => 'required_if:page,=,terms_and_Conditions'
        ]);
        $array = ['merchant_id' => $merchant_id, 'application' => $request->application, 'slug' => $request->page];
        if (isset($request->country) && $request->country != '') {
            $array = array_merge($array, ['country_id' => $request->country]);
        }
        $cmsPage = CmsPage::where($array)->first();
        if (empty($cmsPage)) {
            $cmsPage = new CmsPage;
            $cmsPage->merchant_id = $merchant_id;
            $cmsPage->application = $request->application;
            $cmsPage->slug = $request->page;
            $cmsPage->status = 1;
            if (isset($request->country) && $request->country != '') {
                $cmsPage->country_id = $request->country;
            }
            $cmsPage->save();
            $this->SaveLanguageCms($merchant_id, $cmsPage->id, $request->title, $request->description);
            if($request->page == 'terms_and_Conditions'){
                $request->request->add(['merchant_id'=>$merchant_id]);
                $this->updateToUserDriver($request);
            }
        } else {
            return redirect()->route('cms.index')->withErrors(trans("$string_file.data_already_exist"));
        }
        return redirect()->route('cms.index')->withSuccess(trans("$string_file.added_successfully"));
    }

    public function SaveLanguageCms($merchant_id, $cms_page_id, $title, $description)
    {
        LanguageCmsPage::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'cms_page_id' => $cms_page_id
        ], [
            'title' => $title,
            'description' => $description,
        ]);
    }


    public function show($id)
    {

    }

    public function edit($id)
    {
        $checkPermission = check_permission(1, 'edit_cms');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $cmspage = CmsPage::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
        return view('merchant.cms.edit', compact('cmspage'));
    }

    public function update(Request $request, $id)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $request->validate([
            'title' => 'required',
            'description' => 'required',
//            'country' => 'required_id:page,=,terms_and_Conditions'
        ]);
        $cmspage = CmsPage::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
        $this->SaveLanguageCms($merchant_id, $cmspage->id, $request->title, $request->description);
        if($cmspage->slug == 'terms_and_Conditions'){
            $request->request->add(['merchant_id'=>$merchant_id, 'country' => $cmspage->country_id]);
            $this->updateToUserDriver($request);
        }
        return redirect()->route('cms.index')->withSuccess(trans("$string_file.added_successfully"));
    }

    public function Search(Request $request)
    {
        $merchant_id = Auth::user()->id;
        $query = CmsPage::where([['merchant_id', '=', $merchant_id]]);
        if ($request->pagetitle) {
            $keyword = $request->pagetitle;
            $query->WhereHas('LanguageSingle', function ($q) use ($keyword) {
                $q->where('title', 'LIKE', "%$keyword%");
            });
        }
        $cmspages = $query->latest()->paginate(25);
        return view('merchant.cms.index', compact('cmspages'));
    }

    public function destroy($id)
    {
        //
    }

    public function updateToUserDriver($request)
    {
        if ($request->application == 1) {
            $users = User::whereHas('UserDevice')->with('UserDevice')->where(['merchant_id' => $request->merchant_id, 'country_id' => $request->country])->get();
            $ids = $users->pluck('id');
            for ($i = 0; $i < count($ids); $i++) {
                User::where('id', $ids[$i])->update(['term_status' => 1]);
            }
            if (count($ids) > 0) {
                $title = "Terms and Condition";
                $message = "Terms and condition has been updated by admin, You can review that.";
                $data = array('notification_type' => 'TERMS_AND_CONDITIONS');
                $arr_param = array(
                    'user_id' => $ids,
                    'data'=>$data,
                    'message'=>$message,
                    'merchant_id'=>$request->merchant_id,
                    'title' => $title
                );
                Onesignal::UserPushMessage($arr_param);
//                Onesignal::UserPushMessage($ids, [], $request->title, 7, $request->merchant_id);
            }
        } else {
            $drivers = Driver::whereHas('CountryArea', function ($query) use ($request) {
                $query->where([['country_id', '=', $request->country]]);
            })->where(['merchant_id' => $request->merchant_id])->get();
            $ids = $drivers->pluck('id');
            for ($i = 0; $i < count($ids); $i++) {
                Driver::where('id', $ids[$i])->update(['term_status' => 1]);
            }
            if (count($ids) > 0) {
                $data = array(
                    'notification_type' => "TERMS_AND_CONDITIONS",
                    'segment_type' => "TERMS_AND_CONDITIONS",
                    'segment_data' => time(),
                    'notification_gen_time' => time(),
                );
                $large_icon = "";
                $title = "Terms and Condition";
                $message = "Terms and condition has been updated by admin, You can review that.";
                $arr_param = ['driver_id'=>$ids->toArray(),'data'=>$data,'message'=>$message,'merchant_id'=>$request->merchant_id,'title'=>$title,'large_icon'=>$large_icon];
                Onesignal::DriverPushMessage($arr_param);
//                Onesignal::DriverPushMessage($ids, [], $request->title, 7, $request->merchant_id);
            }
        }
    }
}