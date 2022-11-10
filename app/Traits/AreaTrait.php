<?php

namespace App\Traits;

use App\Http\Controllers\Helper\PolygenController;
use App\Models\Configuration;
use App\Models\DemoConfiguration;
use Auth;
use App\Models\CountryArea;
use App\Models\GeofenceArea;
use Illuminate\Support\Facades\DB;
use Zend\Diactoros\Request;

trait AreaTrait{

    public function getAreaList($pagination = true,$allArea = false,$arr_segment = [],$country_id = null,$area_id = null, $for_taxi_company = false, $for_driver_agency = false)
    {
        if($for_taxi_company){
            $taxi_company = get_taxicompany(false);
            $merchant = $taxi_company->Merchant;
            $merchant_id = $taxi_company->id;
        }
        elseif($for_driver_agency){
            $driver_agency = get_driver_agency(false);
            $merchant = $driver_agency->Merchant;
            $merchant_id = $driver_agency->id;
        }
        else{
            $merchant = get_merchant_id(false);
            $merchant_id = $merchant->id;
        }
        $config = $merchant->Configuration;
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $query = CountryArea::where([['merchant_id', '=', $merchant_id]])->where(function($query) use ($permission_area_ids){
            if(!empty($permission_area_ids)){
                $query->whereIn("id",$permission_area_ids);
            }
        })->latest();
        if(isset($config->geofence_module) && $config->geofence_module == 1) {
            if($allArea){
                $query->whereIn('is_geofence',[1,2]);
            }else{
                $query->where('is_geofence',2);
            }
        }
        else{
            $query->where('is_geofence',2);
        }
        if(count($arr_segment) > 0)
        {
            $query->join('country_area_segment as cas', 'country_areas.id','=','cas.country_area_id')
            ->whereIn('cas.segment_id',$arr_segment);
        }
        if(!empty($country_id))
        {
            $query->where('country_areas.country_id',$country_id);
        }
        if(!empty($area_id))
        {
            $query->where('country_areas.id',$area_id);
        }
        $areaList = $pagination == true ? $query->paginate(25) : $query;
//        p($areaList);
        return $areaList;
    }

    public function getGeofenceAreaList($pagination = true, $merchant_id = NULL, $base_area_id = NULL)
    {
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $merchant_id = ($merchant_id == NULL) ? get_merchant_id() : $merchant_id;
        $query = CountryArea::select('country_areas.*')
            ->with('RestrictedArea')
            ->where(function($q) use($base_area_id, $permission_area_ids){
                if($base_area_id != NULL){
                    $q->whereHas('RestrictedArea',function($query) use($base_area_id){
                        $query->whereRaw(DB::raw("find_in_set($base_area_id,base_areas)"));
                    });
                }
                if(!empty($permission_area_ids)){
                    $q->whereIn("country_areas.id",$permission_area_ids);
                }
            })
            ->where([['is_geofence','=',1],['country_areas.merchant_id', '=', $merchant_id]])
            ->join('language_country_areas as lsa', 'country_areas.id','=','lsa.country_area_id')
            ->orderBy('lsa.AreaName')
            ->distinct('lsa.locale')->latest();
        $aeraList = $pagination == true ? $query->paginate(25) : $query;
        return $aeraList;
    }

    public function checkGeofenceArea($lat, $long, $checkFor, $merchant_id){
        //Get all geofence area, then check drop is allow or not
        $geofenceAreas = CountryArea::with('RestrictedArea')->where([['merchant_id','=',$merchant_id],['is_geofence','=','1']])->get();
        $found_area = [];
        if(!empty($geofenceAreas)){
            foreach($geofenceAreas as $geofenceArea){
                if(isset($geofenceArea->RestrictedArea->restrict_area)){
                    if($checkFor == 'pickup'){
                        switch($geofenceArea->RestrictedArea->restrict_area){
                            case 1:
                            case 3:
                                if($geofenceArea->RestrictedArea->restrict_type == 1){
//                                    p($geofenceArea->RestrictedArea);
                                    $ploygon = new PolygenController();
                                    $checkArea = $ploygon->CheckArea($lat, $long, $geofenceArea->AreaCoordinates);
                                    if(!empty($checkArea)){
                                        return $geofenceArea;
                                    }
                                }
                                break;
                        }
                    }elseif($checkFor == 'drop'){
                        switch($geofenceArea->RestrictedArea->restrict_area){
                            case 2:
                            case 3:
                                if($geofenceArea->RestrictedArea->restrict_type == 1){
                                    $ploygon = new PolygenController();
                                    $checkArea = $ploygon->CheckArea($lat, $long, $geofenceArea->AreaCoordinates);
                                    if(!empty($checkArea)){
                                        return $geofenceArea;
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        }
        return $found_area;
    }

    public static function GeofenceArea($lat, $long, $merchant_id, $check_area_id = null)
    {
        $areas = CountryArea::where(function($query) use($merchant_id, $check_area_id){
            $query->where([['merchant_id', '=', $merchant_id],['is_geofence','=',1]]);
            if($check_area_id != null){
                $query->where('id',$check_area_id);
            }
        })->get();
        if (!empty($areas->toArray())) {
            $areas = $areas->toArray();
            foreach ($areas as $area) {
                $polygon = array();
                $coordinates_JSON = $area['AreaCoordinates'];
                $coordinates = json_decode($coordinates_JSON, true);
                foreach ($coordinates as $coordinate) {
                    $latitude = $coordinate['latitude'];
                    $longitude = $coordinate['longitude'];
                    $polygon[] = new PolygenController($latitude, $longitude);
                }
                $polyObject = new PolygenController($lat, $long);
                $check = $polyObject->pointInPolygon($polyObject, $polygon);
                if (!empty($check)) {
                    return $area;
                }
            }
            return false;
        }
        return false;
    }

    function getMerchantCountryArea($arr_list,$geo_fence = 0,$option_group = 0,$string_file = "")
    {
        $arr_country_area = [];
        $arr_service_area = [];
        $arr_geo_area = [];

        foreach ($arr_list as $country_area) {
            if($country_area['status'] == 1)
            {
                if($geo_fence == 1 && $country_area['is_geofence'] == 1)
                {
                    $arr_geo_area[$country_area['id']] = $country_area['CountryAreaName'];
                }
                else
                {
                    $arr_service_area[$country_area['id']] = $country_area['CountryAreaName'];
                }
            }
        }
        if(!empty($arr_geo_area) && $option_group == 1)
        {
            $arr_country_area[trans("$string_file.service_area")] =$arr_service_area;
            $arr_country_area['Geofence '.trans("$string_file.area")] =$arr_geo_area;
        }
        else
        {
            $arr_country_area = $arr_service_area;
        }
        return $arr_country_area;
    }

    public function getAreaByLatLong($request, $string_file = null)
    {
        $user = $request->user('api');
        $merchant = $user->Merchant;
        $merchant_id = $user->merchant_id;
        if (!empty($request->user('api')->login_type) && $request->user('api')->login_type == 1 && $merchant->demo == 1) {
            $demo = DemoConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
            if (empty($demo)) {
                throw new \Exception("Demo configuration not found");
                // return $this->failedResponse('Demo configuration not found');
            }
            $area = $demo->CountryArea;
            if (empty($area)) {
                throw new \Exception(trans("$string_file.no_service_area"));
                // return $this->failedResponse(trans("$string_file.no_service_area"));
            }
        } else {

            // Check with Geofence
            $area = NULL;
            $geo_fence = $merchant->Configuration->geofence_module;
            if($geo_fence == 1)
            {
                $area = $this->checkGeofenceArea($request->latitude, $request->longitude, 'pickup', $merchant_id);
            }
            if(empty($area)){
                $area = PolygenController::Area($request->latitude, $request->longitude, $merchant_id);
                if (empty($area)) {
                    throw new \Exception(trans("$string_file.no_service_area"));
                    // return $this->failedResponse(trans("$string_file.no_service_area"));
                }
            }
        }
        $request->request->add([ 'area' => $area['id']]);
    }
    
    function getMerchantCountry($arr_list){
        $arr_country = [];
        foreach ($arr_list as $country) {
            $arr_country[$country['id']] = $country['CountryName'];
        }
        return $arr_country;
    }
}
