<?php

namespace App\Traits;

use App\Models\Configuration;
use Auth;
use App\Models\PriceCard;

trait PriceTrait
{
    public function getPriceList($pagination = true, $delivery = false,$area_id = null)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $config = Configuration::where('merchant_id',$merchant_id)->first();
        $query = PriceCard::where([['merchant_id', '=', $merchant_id]])->latest();
//        if ($delivery == false) {
//            $query->where([['merchant_id', '=', $merchant_id], ['delivery_type_id', '=', null]]);
//        } else {
//            $query->where([['merchant_id', '=', $merchant_id], ['service_type_id', '=', null]]);
//        }
        if(isset($config->geofence_module) && $config->geofence_module == 1){
            $query->whereHas('CountryArea',function($q) use($permission_area_ids){
                $q->whereIn('is_geofence', [1,2]);
                if(!empty($permission_area_ids)){
                    $q->whereIn("id",$permission_area_ids);
                }
            });
        }else{
            $query->whereHas('CountryArea',function($q) use($permission_area_ids){
                $q->where('is_geofence', 2);
                if(!empty($permission_area_ids)){
                    $q->whereIn("id",$permission_area_ids);
                }
            });
        }
        if(!empty($area_id)){
            $query->whereHas('CountryArea',function($q)use($area_id){
                $q->where('country_area_id', $area_id);
            });
        }
        $query->whereHas('Segment',function($d){
            $arr = [];
            if(Auth::user('merchant')->can('price_card_TAXI')){
                array_push($arr,'TAXI','TOWING');
            }
            if(Auth::user('merchant')->can('price_card_DELIVERY')){
                array_push($arr,'DELIVERY');
            }
            if(!empty($arr)){
                $d->whereIn('slag',$arr);
            }
            $d->where('segment_group_id',1);
        });
//        p($query->toSql());
        $aeraList = $pagination == true ? $query->paginate(25) : $query;
//        p($aeraList);
        return $aeraList;
    }
}
