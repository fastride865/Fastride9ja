<?php

namespace App\Http\Controllers\Api;

use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\BannerTrait;
use App\Traits\ApiResponseTrait;
use App\Models\PromoCode;
use App\Models\BusinessSegment\BusinessSegment;
use App\Traits\AreaTrait;
use App\Models\Merchant;
use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\GoogleController;

class MainScreenController extends Controller
{
    use BannerTrait,MerchantTrait,ApiResponseTrait,AreaTrait;
    // get home screen data of food app
    public function mainScreenSegments(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $merchant = Merchant::find($merchant_id);
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $return_data = [];
        $banner_res['cell_title'] = "BANNERS";
        $user = $request->user('api');

        // Set language for notification
        $commonObj = new CommonController();
        $commonObj->setLanguage($user->id,1);

        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $banner_res['cell_title_text'] = trans("$string_file.banner");
        $banner_res['cell_contents'] = [];
        $recent_services['cell_title'] = "RECENTS";
        $recent_services['cell_title_text'] = trans("$string_file.recent");
        $recent_services['cell_contents'] = [];
        $merchant_services_res['cell_title'] = "ALL SERVICES";
        $merchant_services_res['cell_title_text'] = trans("$string_file.all_services");
        $merchant_services_res['cell_contents'] = [];
        $merchant_segment_count = $user->Merchant->Segment->count();
        if($merchant_segment_count > 1 || $merchant_segment_count == 0)
        {
            try{
                // call area trait to get id of area
                $this->getAreaByLatLong($request,$string_file);
            }catch(\Exception $e)
            {
                array_push($return_data,$banner_res,$recent_services,$merchant_services_res);
                return $this->failedResponse($e->getMessage(),$return_data);
            }
        }

        try{
            $country_area_id = $request->area;
            $request->request->add(['merchant_id'=>$merchant_id,'home_screen'=>1,'segment_id'=>NULL,'banner_for'=>1]);
            $arr_banner = $this->getMerchantBanner($request);
            $banner_res['cell_contents'] = $arr_banner->map(function ($item, $key) use($merchant_id){
                return array(
                    'id' => $item->id,
                    'business_segment_id' => $item->business_segment_id,
                    'title' => $item->banner_name,
                    'image' => get_image($item->banner_images,'banners',$merchant_id),
                    'redirect_url' => !empty($item->redirect_url) ? $item->redirect_url : "",
                );
            });
            array_push($return_data,$banner_res);

            $arr_services = $this->getMerchantSegmentServices($merchant_id,'api',NULL,[],$country_area_id);
            $arr_services = collect($arr_services);
            
            if($merchant->ApplicationConfiguration->recent_services_enable=="1"){
                $recent_services['cell_contents'] = $arr_services->map(function ($item, $key) use($merchant_id){
                    return array(
                        'id' => $item['segment_id'],
                        'title' =>$item['slag'],
                        'is_coming_soon' =>$item['is_coming_soon'],
                        'segment_group_id' => $item['segment_group_id'],
                        'segment_sub_group' => $item['sub_group_for_app'],
                        'name' =>$item['name'],
                        'price_card_owner' =>$item['price_card_owner'],
                        'image' => $item['segment_icon'],
                    );
                });
                array_push($return_data,$recent_services);    
            }

            $merchant_services_res['cell_contents'] = $arr_services->map(function ($item, $key) use($merchant_id){
                $multi_store = false;
                if($item['slag'] == 'GROCERY')
                {
                   $store_count =   $store_count = BusinessSegment::where('merchant_id',$merchant_id)->where('segment_id',$item['segment_id'])->count();
                   $multi_store = $store_count > 1 ? true : false;
                }
                return array(
                    'id' =>$item['segment_id'],
                    'title' =>$item['slag'],
                    'is_coming_soon' =>$item['is_coming_soon'],
                    'segment_group_id' => $item['segment_group_id'],
                    'segment_sub_group' => $item['sub_group_for_app'],
                    'price_card_owner' => $item['price_card_owner'],
                    'name' =>$item['name'],
                    'multi_store' =>$multi_store,
                    'image' =>$item['segment_icon'],
                );
            });
            array_push($return_data,$merchant_services_res);
                 // array_push($return_data,$banner_res,$recent_services,$merchant_services_res);
                 
                 // popular restaurants ans stores
                 
            // popular restaurant and store data
            $popular_restaurant_res['cell_title'] = "POPULAR_RESTAURANT";
            $popular_restaurant_res['cell_title_text'] = trans("$string_file.popular_restaurants");

            $popular_store_res['cell_title'] = "POPULAR_STORE";
            $popular_store_res['cell_title_text'] = trans("$string_file.popular_stores");
            $distance = $user->Merchant->BookingConfiguration->store_radius_from_user;
            $request->request->add(['distance'=>$distance]);
            $arr_popular_business_segment = $this->getMerchantPopularBusinessSegment($request);
// p($arr_popular_business_segment);

            $arr_restaurants = [];
            $arr_stores = [];
            $user_lat = $request->latitude;
            $user_long = $request->longitude;
            $google_key = $user->Merchant->BookingConfiguration->google_key;
            foreach ($arr_popular_business_segment as $item)
            {


                $store_lat = $item->latitude;
                $store_long = $item->longitude;

                // $distance_from_user = $google->arialDistance($user_lat, $user_long, $store_lat, $store_long,$unit,$string_file);
                // calculate distance from google direction api
                $user_drop_location[0] = [
                    'drop_latitude'=>$user_lat,
                    'drop_longitude'=>$user_long,
                    'drop_location'=>""
                ];
                $distance_from_user = GoogleController::GoogleStaticImageAndDistance($store_lat, $store_long, $user_drop_location, $google_key,"",$string_file);
                $distance_from_user = isset($distance_from_user['total_distance_text']) ? $distance_from_user['total_distance_text'] : "";


                // p(round($item->distance,1));
                // only setting timezone
                date_default_timezone_set($item->CountryArea['timezone']);
                $current_time = date('H:i');
                $is_business_segment_open = false;
                $current_day = date('w');
                $arr_open_time = json_decode($item->open_time,true);
                $arr_close_time = json_decode($item->close_time,true);
                $open_time = isset($arr_open_time[$current_day]) ? $arr_open_time[$current_day] : NULL;
                $close_time = isset($arr_close_time[$current_day]) ? $arr_close_time[$current_day] : NULL;
                if ($open_time < $current_time && $close_time > $current_time) {
                    $is_business_segment_open = true;
                } else {
                    $is_business_segment_open = false;
                }
                $arr_style =   $item->StyleManagement->map(function ($style) use ($merchant_id,$string_file){
                    return ['style_name' => $style->Name($merchant_id)];
                });
//                $m_amount = $item->minimum_amount;
//                $m_amount_for = $item->minimum_amount_for;
                $business_segment =  array(
                    'business_segment_id' => $item->id,
                    'id' => $item->segment_id, // segment id of business segment
                    'name' => $item->full_name,
                    'title' => $item->Segment->slag,

                    'time' => $item->Segment->sub_group_for_app == 1 ? "$item->delivery_time " . trans("$string_file.minute") : "",
                    'distance' =>$distance_from_user,
//                    'amount' => !empty($item->minimum_amount) ? "$m_amount" : "",
//                    'amount_for' => !empty($item->minimum_amount_for) ? "$m_amount_for" : "",
//                    'currency' => $item->Country->isoCode,
//                    'style' => array_pluck($arr_style,'style_name'),//array_pluck($item->StyleManagement, 'style_name'),
//                    'open_time' => $open_time,
//                    'close_time' => $close_time,
//                    'rating' => !empty($item->rating) ? $item->rating : "2.5",
                    'image' => get_image($item->business_logo, 'business_logo', $merchant_id),
                    'is_business_segment_open' => $is_business_segment_open,
                );

                if($item->Segment->sub_group_for_app == 1)
                {
                    $arr_restaurants [] = $business_segment;
                }
                else
                {
                    $arr_stores [] = $business_segment;
                }
            }

            $popular_restaurant_res['cell_contents'] = $arr_restaurants;
            $popular_store_res['cell_contents'] = $arr_stores;
            if(count($arr_restaurants) > 0)
            {
            array_push($return_data,$popular_restaurant_res);
            }
            if(count($arr_stores) > 0)
            {
                array_push($return_data, $popular_store_res);
            }
                 
                 
                 
        }
        catch(\Exception $e)
        {
           return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans("$string_file.success"),$return_data);
    }


    // public function getPromoCodeList(Request $request)
    // {
    //     $merchant_id = $request->merchant_id;
    //     $user = $request->user('api');
    //     $string_file = $this->getStringFile(NULL,$user->Merchant);
    //     $validator = Validator::make($request->all(), [
    //         'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')],
    //     ]);
    //     if ($validator->fails()) {
    //         $errors = $validator->messages()->all();
    //         $this->failedResponse($errors[0]);
    //     }
    //     $arr_promo_code_list = PromoCode::select('promoCode','promo_code_value','promo_code_description','order_minimum_amount','promo_code_value_type','promo_percentage_maximum_discount')->where([['deleted','=',NULL], ['merchant_id','=',$merchant_id]
    //     ])->orderBy('promo_code_value')->get()->toArray();
    //     return $this->successResponse(trans("$string_file.data_found"), $arr_promo_code_list);
    // }
    
    
    // updated code for country are wise promo code list
    public function getPromoCodeList(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $user = $request->user('api');
        // call area trait to get id of area
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $area_id = NULL;
        if(!empty($request->latitude) && !empty($request->longitude))
        {
            $this->getAreaByLatLong($request,$string_file);
            $area_id = $request->area;
        }
        $validator = Validator::make($request->all(), [
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            $this->failedResponse($errors[0]);
        }
        $arr_promo_code_list = PromoCode::select('promoCode','promo_code_value','promo_code_description','order_minimum_amount','promo_code_value_type','promo_percentage_maximum_discount')->where([['deleted','=',NULL], ['merchant_id','=',$merchant_id]
        ])->where(function($q) use ($area_id){
          if(!empty($area_id))
          {
              $q->where('country_area_id',$area_id);
          }
        })->orderBy('promo_code_value')->get()->toArray();
        return $this->successResponse(trans("$string_file.data_found"), $arr_promo_code_list);
    }
}
