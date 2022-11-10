<?php

namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\WebSiteHomePage;
use App\Models\WebsiteFeature;
use App\Models\WebsiteFeatureTranslation;
use App\Models\WebSiteHomePageTranslation;
use App\Models\WebsiteApplicationFeature;
use App\Models\WebsiteApplicationTranslation;
use Illuminate\Http\Request;
use App;
use App\Traits\ImageTrait;
use App\Traits\MerchantTrait;

class WebsiteDriverHomeController extends Controller
{
    use ImageTrait,MerchantTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $checkPermission =  check_permission(1,'website_driver_home');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $details = WebSiteHomePage::where([['merchant_id',$merchant_id]])->first();
        if($features = WebsiteFeature::where([['merchant_id',$merchant_id],['application','DRIVER']])->get()->isEmpty()):
            for($i = 0; $i<3; $i++){
                $update_create = WebsiteFeature::Create([
                    'merchant_id' => $merchant_id,
                    'application'=>'DRIVER'
                ], [
                    'feature_image' => null,
                ]);
            }
        endif;
        $app_detil = WebsiteApplicationFeature::where([['merchant_id',$merchant_id],['application',"DRIVER"]])->orderBy('position')->get();
        $details ? $details : $details = NULL;
        count($app_detil) > 0 ? $app_detil : $app_detil = NULL;
        $features = WebsiteFeature::where([['merchant_id',$merchant_id],['application','DRIVER']])->limit('3')->get();
        $string_file = $this->getStringFile($merchant_id);
        return view('merchant.website.driver_headings',compact('details','features','app_detil','string_file'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $checkPermission =  check_permission(1,'website_driver_home');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        Validator::make($request->all(),[
            'driver_heading' => 'required',
            'driver_sub_heading' => 'required',
            'driver_buttonText' => 'required',
            'footer_heading' => 'required',
            'footer_sub_heading' => 'required',
        ],[
            //'features.*.exists' => 'Some Data Invalid',
        ])->validate();
        $app_detil = WebsiteApplicationFeature::where([['merchant_id',$merchant_id]])->orderBy('position')->get();
        $key = 0;
        $app_image = '';
        if(!empty($request['data']))
        {
            foreach($request['data'] as  $image){
                if($image){
                    if(isset($image['image']))
                    {
                        $app_image = $this->uploadImage($image['image'],'website_images',$merchant_id,'multiple');
                    }
                    else
                        {
                            $app_image = '';
                        }
                    if(empty($app_image))
                    {
                        $app_image = isset($app_detil[$key]['image']) ? $app_detil[$key]['image'] : 0;
                    }
                    $update_img = WebsiteApplicationFeature::updateOrCreate(['merchant_id' => $merchant_id,'position'=>$key], ['image' => $app_image,'application'=>'DRIVER','align'=>1]);
                    $inrt_id = $update_img->id;
                    $locale = App::getLocale();
                    $update_detil = WebsiteApplicationTranslation::updateOrCreate(['website_application_feature_id' => $inrt_id,'locale'=>$locale],
                            ['title' => $image['title'],
                            'description'=>$image['description'],
                            'locale'=>$locale]);
                }
                $key++;
            }
        }

        $websiteHomePage = WebSiteHomePage::where('merchant_id' , $merchant_id)->first();

        //upload footer image

        if($request->driver_footer_image){
            $driver_footer_image = $this->uploadImage('driver_footer_image','website_images');
        }else{
            $driver_footer_image = $websiteHomePage['driver_footer_image'];
        }
        // upload driver login image
        if ($request->driver_login_bg_image) {
            $driver_login_bg_image = $this->uploadImage('driver_login_bg_image' , 'website_images');
        }
        else {
            $driver_login_bg_image = $websiteHomePage['driver_login_bg_image'];
        }

        
        $image = null;
        if($request->banner_image):
            $image = $this->uploadImage('banner_image','website_images');
        else:
            $update = WebSiteHomePage::where([['merchant_id',$merchant_id]])->first();
            if(!empty($update)):
                $image = $update['driver_banner_image'];
            endif;
        endif;

        $update = WebSiteHomePage::updateOrCreate(['merchant_id' => $merchant_id,], [
            'driver_banner_image' => $image,
            'driver_footer_image'=>$driver_footer_image ,
            'driver_login_bg_image' => $driver_login_bg_image
        ]);

        $image = null;
        foreach($request->features as $key => $value):
            //echo $key."<\br>";
            $update_data = WebsiteFeature::findorfail($key);
            $update_lang_data = $value;
            $this->SaveLanguageHomeWebsiteFeatures(collect($update_lang_data), $update_data);
        endforeach;
        $update_lang_data = $request->only(['driver_heading', 'driver_sub_heading', 'driver_buttonText','footer_heading','footer_sub_heading']);
        $this->SaveLanguageHomePage(collect($update_lang_data), $update);
        return redirect()->back();
    }
    
    public function SaveLanguageHomeWebsiteFeatures(Collection $collection, WebsiteFeature $webfeature_data)
    {
        $collect_lang_data = $collection->toArray();
        WebsiteFeatureTranslation::updateOrCreate(['website_feature_id' => $webfeature_data['id'], 'locale' => \App::getLocale()], ['title' => $collect_lang_data['title'],
            'description' => $collect_lang_data['description']]);
    }

    public function SaveLanguageHomePage(Collection $collection, WebSiteHomePage $webhome_data)
    {
        $collect_lang_data = $collection->toArray();
        WebSiteHomePageTranslation::updateOrCreate([
            'web_site_home_page_id' => $webhome_data['id'], 'locale' => \App::getLocale()
        ], [
            'driver_heading' => $collect_lang_data['driver_heading'],
            'driver_sub_heading' => $collect_lang_data['driver_sub_heading'],
            'driver_buttonText' =>$collect_lang_data['driver_buttonText'],
            'footer_heading' =>$collect_lang_data['footer_heading'],
            'footer_sub_heading' =>$collect_lang_data['footer_sub_heading'],
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
