<?php

namespace App\Traits;

use App\Http\Controllers\Helper\PolygenController;
use App\Models\Configuration;
use Auth;
use App\Models\CountryArea;
use App\Models\AdvertisementBanner;

trait BannerTrait{

    public function getMerchantBanner($request)
    {
        $query = AdvertisementBanner::with('BusinessSegment')->select('name as banner_name','id','image as banner_images','business_segment_id','redirect_url')
            ->where([['merchant_id','=',$request->merchant_id],
                ['status','=',1]])
            ->orderBy('sequence')
            ->whereIn('banner_for',[$request->banner_for,4])
            ->orderBy('updated_at')
            ->where('is_deleted', NULL)
            ->where(function ($q) use ($request) {
                if($request->home_screen == 1)
                {
                    $q->where('home_screen',1);
                }
                elseif(!empty($request->segment_id)){
                    $q->where('segment_id',$request->segment_id);
                }
                if(isset($request->business_segment_id) && !empty($request->business_segment_id)){
                    if(is_array($request->business_segment_id)){
                        $q->whereIn('business_segment_id',$request->business_segment_id);
                    }else{
                        $q->where('business_segment_id',$request->business_segment_id);
                    }
                }
            })
            ->where(function ($q) use ($request) {
                $q->where([['activate_date','=',NULL]]);
                $q->orWhere('activate_date','<=',date('Y-m-d'));
            })
            ->where(function ($q) use ($request) {
                $q->where([['expire_date','>=',date('Y-m-d')],['validity','=',2]]);
                $q->orWhere([['expire_date','=',NULL],['validity','=',1]]);

            });
        $arr_banner = $query->get();
        return $arr_banner;
    }

    public function getBusinessSegmentBanner($request, $filtered = true)
    {
        $query = AdvertisementBanner::with('BusinessSegment')->select('name as banner_name','id','image as banner_images','business_segment_id','redirect_url')
            ->where([
                ['business_segment_id','=',$request->business_segment_id],
                ['merchant_id','=',$request->merchant_id],
                ['status','=',1]
            ])
            ->orderBy('sequence')
            ->whereIn('banner_for',[$request->banner_for,4])
            ->orderBy('updated_at')
            ->where('is_deleted', NULL)
            ->where(function ($q) use ($request) {
                if($request->home_screen == 1)
                {
                    $q->where('home_screen',1);
                }else{
                    $q->where('home_screen',"!=",1);
                }
                if(!empty($request->segment_id)){
                    $q->where('segment_id',$request->segment_id);
                }
            })
            ->where(function ($q) use ($request) {
                $q->where([['activate_date','=',NULL]]);
                $q->orWhere('activate_date','<=',date('Y-m-d'));
            })
            ->where(function ($q) use ($request) {
                $q->where([['expire_date','>=',date('Y-m-d')],['validity','=',2]]);
                $q->orWhere([['expire_date','=',NULL],['validity','=',1]]);

            });
        $arr_banner = $query->get();
        $return_banners = [];
        if($filtered){
            $string_file = $this->getStringFile($request->merchant_id);
            $return_banners = $arr_banner->map(function ($item, $key) use ($request, $string_file) {
                $return = array(
                    'id' => $item->id,
                    'business_segment_id' => $item->business_segment_id,
                    'title' => $item->banner_name,
                    'image' => get_image($item->banner_images, 'banners', $request->merchant_id,true,false),
                );
                return $return;
            });
        }else{
            $return_banners = $arr_banner;
        }
        return $return_banners;
    }
}
