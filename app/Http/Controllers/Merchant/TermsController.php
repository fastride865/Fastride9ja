<?php

namespace App\Http\Controllers\Merchant;

use App\Models\LanguageCmsPage;
use Auth;
use App;
use App\Models\Page;
use App\Models\User;
use App\Models\Driver;
use App\Models\CmsPage;
use App\Models\Country;
use App\Models\Onesignal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TermsController extends Controller
{
    /******
     * BHUVANESH :  --
     * THERE IS NO USE OF THIS CONTROLLER , I HAVE MERGED THIS WITH CMS PAGES
     */
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $checkPermission =  check_permission(1,'view_cms');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $countries = Country::where([['country_status', '=', 1], ['merchant_id', '=', $merchant_id]])->get();
        $cmspages = CmsPage::where([['merchant_id', '=', $merchant_id], ['slug', '=', 'terms_and_Conditions']])->latest()->paginate(25);
        return view('merchant.terms.index', compact('cmspages','countries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $checkPermission =  check_permission(1,'create_cms');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $countries = Country::where([['country_status', '=', 1], ['merchant_id', '=', $merchant_id]])->get();
        $pages = Page::where('id', 1)->get();
        return view('merchant.terms.create', compact('pages', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'application' => 'required|integer|between:1,2',
            'page' => 'required|exists:pages,slug',
            'title' => 'required',
            'description' => 'required',
        ]);
        $cmsPage = CmsPage::where(['merchant_id' => $merchant_id, 'application' => $request->application, 'country_id' => $request->country, 'slug' => $request->page])->first();
        if(!empty($cmsPage)){
            return redirect()->route('terms.index')->with('info',trans('admin.terms_condition_exist'));
        }else{
            $cmsPage = CmsPage::create(
                ['merchant_id' => $merchant_id, 'application' => $request->application, 'country_id' => $request->country, 'slug' => $request->page,'status' => 1]
            );
        }
        $country_id = $request->country;
        if ($request->application != 1) {
            $users = User::whereHas('UserDevice')->with('UserDevice')->where(['merchant_id' => $merchant_id, 'country_id' => $request->country])->get();
            $ids = $users->pluck('id');
            for($i=0;$i<count($ids);$i++)
            {
                User::where('id', $ids[$i])->update(['term_status' => 1]);
            }
                if(count($ids) > 0)
                {
                    Onesignal::UserPushMessage($ids, [], $request->title, 7, $merchant_id);
                }
            } else {
            $drivers = Driver::whereHas('CountryArea', function ($query) use ($country_id) {
                $query->where([['country_id', '=', $country_id]]);
            })->where(['merchant_id' => $merchant_id])->get();
            $ids = $drivers->pluck('id');
            for($i=0;$i<count($ids);$i++)
            {
                Driver::where('id', $ids[$i])->update(['term_status' => 1]);
            }
            if(count($ids) > 0)
            {
                Onesignal::DriverPushMessage($ids, [], $request->title, 7, $merchant_id);
            }
            }
        $this->SaveLanguageCms($merchant_id, $cmsPage->id, $request->title, $request->description);
        return redirect()->route('terms.index')->with('success',trans('admin.terms_condition_added'));
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

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $checkPermission =  check_permission(1,'edit_cms');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $cmspage = CmsPage::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
        return view('merchant.terms.edit', compact('cmspage'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
     
    public function update(Request $request, $id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
        $cmspage = CmsPage::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
        $country_id = $cmspage->country_id;
        if ($cmspage->application == 1) {
            $users = User::whereHas('UserDevice')->with('UserDevice')->where(['merchant_id' => $merchant_id, 'country_id' => $country_id])->get();
            $ids = $users->pluck('id');
            for($i=0;$i<count($ids);$i++)
            {
                User::where('id', $ids[$i])->update(['term_status' => 1]);
            }
            if(count($ids) > 0) {
                Onesignal::UserPushMessage($ids, [], $request->title, 7, $merchant_id);
            }
        } else {

            $drivers = Driver::whereHas('CountryArea', function ($query) use ($country_id) {
                $query->where([['country_id', '=', $country_id]]);
            })->where(['merchant_id' => $merchant_id])->get();
            $ids = $drivers->pluck('id');
            for($i=0;$i<count($ids);$i++)
            {
                Driver::where('id', $ids[$i])->update(['term_status' => 1]);
            }
            if(count($ids) > 0)
            {
            Onesignal::DriverPushMessage($ids, [], $request->title, 7, $merchant_id);
            }
        }

        $this->SaveLanguageCms($merchant_id, $cmspage->id, $request->title, $request->description);
        return redirect()->back()->with('cmsupdate', 'Terms Update');
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
        $cmspages = $query->get();
        return view('merchant.terms.index', compact('cmspages'));
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
