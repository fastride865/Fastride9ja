<?php

namespace App\Http\Controllers\Merchant;

use App\Models\WebSiteHomePage;
use App\Models\WebsiteFeature;
use App\Models\WebsiteFeatureTranslation;
use App\Models\WebSiteHomePageTranslation;
use App\Models\WebsiteFeaturesComponents;
use App\Models\WebsiteFeaturesComponentsTranslation;
use Illuminate\Http\Request;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\ImageTrait;
use App\Traits\MerchantTrait;

class WebsiteUserHomeController extends Controller
{
    use ImageTrait,MerchantTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $checkPermission =  check_permission(1,'website_user_home');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        if($features = WebsiteFeature::where([['merchant_id',$merchant_id],['application','USER']])->limit('3')->get()->isEmpty()):
            for($i = 0; $i<3; $i++){
                $update_create = WebsiteFeature::Create([
                    'merchant_id' => $merchant_id,
                    'application'=>'USER'
                ], [
                    'feature_image' => null,
                ]);
            }
        endif;
        if($features_component = WebsiteFeaturesComponents::where([['merchant_id',$merchant_id],['application','USER']])->limit('5')->get()->isEmpty()):
            for($i = 0; $i<5; $i++){
                $update_create = WebsiteFeaturesComponents::Create([
                    'merchant_id' => $merchant_id,
                    'application'=>'USER',
                    'position' => $i,
                ], [
                    'feature_image' => null,
                ]);
            }
        endif;
        $features_component = WebsiteFeaturesComponents::where([['merchant_id',$merchant_id],['application','USER']])->limit('5')->get();
        $details = WebSiteHomePage::where([['merchant_id',$merchant_id]])->first();
        $features = WebsiteFeature::where([['merchant_id',$merchant_id],['application','USER']])->limit('3')->get();
        // dd($features);
        $string_file = $this->getStringFile($merchant_id);
        return view('merchant.website.user_headings',compact('details','features','features_component',"string_file"));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $checkPermission =  check_permission(1,'website_user_home');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        //dd($request['data']);
        $locale = \App::getLocale();
        $merchant_id = get_merchant_id();

        Validator::make($request->all(),[
            'start_address_hint' => 'required',
            'end_address_hint' => 'required',
            'book_btn_title' => 'required',
            'estimate_btn_title' => 'required',
            'estimate_description' => 'required',
            /*'features.*' => ['required',
                Rule::exists('website_features','id')->where('merchant_id',$merchant_id)],*/
        ],[
            //'features.*.exists' => 'Some Data Invalid',
        ])->validate();

        $features_component = WebsiteFeaturesComponents::where([['merchant_id',$merchant_id]])->orderBy('position')->get();
        //dd($request['data']);
        foreach($request['data'] as $key => $image){
            if($image){
                if(isset($image['featre_compnt_image'])){
                    $app_image = $this->uploadImage($image['featre_compnt_image'],'website_images',$merchant_id,'multiple');
                }
                else{ $app_image = ''; }
                if($app_image != ""){ $app_image=$app_image; }else{ $app_image = $features_component[$key]['feature_image']; }
                $update_img = WebsiteFeaturesComponents::updateOrCreate(['merchant_id' => $merchant_id,'position'=>$key], ['feature_image' => $app_image,'application'=>'USER','align'=>1]);
                $inrt_id = $update_img->id;
                $locale = \App::getLocale();
                $update_detil = WebsiteFeaturesComponentsTranslation::updateOrCreate(['website_features_components_id' => $inrt_id,'locale'=>$locale], ['title' => $image['featre_compnt_title'],'description'=>$image['featre_compnt_description'],'locale'=>$locale]);
            }
        }

        $android_link = $request->android_link;
        $ios_link = $request->ios_link;
        $image = null;
        if($request->file('banner_image')):
            $image = $this->uploadImage('banner_image','website_images');
        else:
            $update = WebSiteHomePage::where([['merchant_id',$merchant_id]])->first();
            if(!empty($update)):
                $image = $update['user_banner_image'];
            endif;
        endif;
        if($request->hasFile('app_logo') && $request->file('app_logo')):
            $logo = $this->uploadImage('app_logo','website_images');
        else:
            $updatelogo = WebSiteHomePage::where([['merchant_id',$merchant_id]])->first();
            if(!empty($updatelogo)):
                $logo = $updatelogo['logo'];
            endif;
        endif;
        $websiteHomePage = WebSiteHomePage::where('merchant_id' , $merchant_id)->first();
        if ($request->estimate_image) {
            $estimate_image = $this->uploadImage('estimate_image' , 'website_images');
        }
        else {
            $estimate_image = $websiteHomePage['user_estimate_image'];
        }

        // featured component main image
        if ($request->featured_component_main_image) {
            $featured_component_main_image = $this->uploadImage('featured_component_main_image' , 'website_images');
        }
        else {
            $featured_component_main_image = $websiteHomePage['featured_component_main_image'];
        }

        // user login bg image
        if ($request->user_login_bg_image) {
            $user_login_bg_image = $this->uploadImage('user_login_bg_image' , 'website_images');
        }
        else {
            $user_login_bg_image = $websiteHomePage['user_login_bg_image'];
        }
        $update = WebSiteHomePage::updateOrCreate(['merchant_id' => $merchant_id,], [
            'user_banner_image' => $image,
            'logo'=>$logo,
            'android_link'=>$android_link,
            'ios_link'=>$ios_link,
            'user_estimate_image' => $estimate_image,
            'featured_component_main_image' => $featured_component_main_image,
            'footer_bgcolor' => $request->footer_bg_color,
            'footer_text_color' => $request->footer_text_color,
            'user_login_bg_image' => $user_login_bg_image
        ]);
        $image = null;
        foreach($request->features as $key => $value):
            $update_data = WebsiteFeature::findorfail($key);
            $update_lang_data = $value;
            $this->SaveLanguageHomeWebsiteFeatures(collect($update_lang_data), $update_data);
        endforeach;
        $update_lang_data = $request->only(['start_address_hint', 'end_address_hint', 'book_btn_title', 'estimate_btn_title', 'estimate_description']);
        $this->SaveLanguageHomePage(collect($update_lang_data), $update);
        return redirect()->back();
    }

    public function SaveLanguageHomeWebsiteFeatures(Collection $collection, WebsiteFeature $webfeature_data)
    {
        $collect_lang_data = $collection->toArray();
        WebsiteFeatureTranslation::updateOrCreate([
            'website_feature_id' => $webfeature_data['id'], 'locale' => \App::getLocale()
        ], [
            'title' => $collect_lang_data['title'],
            'description' => $collect_lang_data['description'],
        ]);
    }

    public function SaveLanguageHomePage(Collection $collection, WebSiteHomePage $webhome_data)
    {
        $collect_lang_data = $collection->toArray();
        WebSiteHomePageTranslation::updateOrCreate([
            'web_site_home_page_id' => $webhome_data['id'], 'locale' => \App::getLocale()
        ], [
            'start_address_hint' => $collect_lang_data['start_address_hint'],
            'end_address_hint' => $collect_lang_data['end_address_hint'],
            'book_btn_title' =>$collect_lang_data['book_btn_title'],
            'estimate_btn_title' =>$collect_lang_data['estimate_btn_title'],
            'estimate_description'=>$collect_lang_data['estimate_description'],
        ]);
    }

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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
