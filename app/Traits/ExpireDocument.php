<?php

namespace App\Traits;

use App\Models\Driver;
use App\Models\DriverVehicle;
use Auth;
use Illuminate\Database\Eloquent\Builder;

trait ExpireDocument
{
//    public function getExpireAllDriverPagination($pagination = true)
//    {
//        $merchant = Auth::user('merchant')->load('CountryArea');
//        $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
//
//        $query = Driver::has('DriverDocument')->with('DriverDocument')->where([['merchant_id', '=', $merchant_id], ['driver_delete', '=', NULL]])
//            ->orWhere([['merchant_id', '=', $merchant_id], ['driver_delete', '=', NULL]])->latest();
//        if (!empty($merchant->CountryArea->toArray())) {
//            $area_ids = array_pluck($merchant->CountryArea, 'id');
//            $query->whereIn('country_area_id', $area_ids);
//        }
//        $drivers = $pagination == true ? $query->paginate(8) : $query;
//        return $drivers;
//    }

//    public function getPesrsonalDocExpireAllDriver()
//    {
//        $expiry_date = date('Y-m-d');
//        $merchant = Auth::user('merchant')->load('CountryArea');
//        $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
//        $query = Driver::with(['DriverDocument' => function ($q) {
//            $q->where([['expire_date', '!=', null], ['expire_date', '!=', ''], ['expire_date', '<=', date('Y-m-d')]]);
//        }])->whereHas('DriverDocument', function ($query) use ($expiry_date) {
//            $query->where([['expire_date', '!=', null], ['expire_date', '!=', ''], ['expire_date', '<=', $expiry_date]]);
//        })->where([['merchant_id', '=', $merchant_id], ['signupStep', '=', 3], ['driver_delete', '=', NULL]])->latest();
//        if (!empty($merchant->CountryArea->toArray())) {
//            $area_ids = array_pluck($merchant->CountryArea, 'id');
//            $query->whereIn('country_area_id', $area_ids);
//        }
//        $drivers = $query->get();
//        if (empty($drivers->toArray())) {
//            return [];
//        }
//        $expire_drivers = $drivers->map(function ($item, $key) {
//            $item->total_expire_document = count($item->DriverDocument);
//            return $item;
//        });
//        return $expire_drivers;
//    }

//    public function getVehicleDocExpireAllDriver()
//    {
//        $expiry_date = date('Y-m-d');
//        $merchant = Auth::user('merchant')->load('CountryArea');
//        $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
//        $vehcileArray = DriverVehicle::with(['DriverVehicleDocument' => function ($query) use ($expiry_date) {
//            $query->where([['expire_date', '!=', null], ['expire_date', '!=', ''], ['expire_date', '<=', $expiry_date]]);
//        }])->whereHas('DriverVehicleDocument', function ($q) {
//            $q->where([['expire_date', '!=', null], ['expire_date', '!=', ''], ['expire_date', '<=', date('Y-m-d')]]);
//        })->where([['merchant_id', '=', $merchant_id]])->latest()->get();
//        if (empty($vehcileArray->toArray())) {
//            return [];
//        }
//        $expire_vehicles = $vehcileArray->map(function ($item, $key) {
//            $item->total_expire_document = count($item->DriverVehicleDocument);
//            return $item;
//        });
//        return $expire_vehicles;
//    }

    public function getAllExpireDriversDocument($merchant_id = NULL,$document_verification_status = 4,$pagination = true)
    {
        // this function is using for cron job and expired document on admin panel
        $currentDate = date('Y-m-d');
        $where = [['expire_date', '<', $currentDate],['document_verification_status', '=', $document_verification_status]];
        $driverVehicleDocumentWith = ['DriverVehicleDocument' => function ($o) use ($where) {
            $o->where($where);
        }];
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $query = Driver::select('id','first_name','merchant_driver_id','last_name','country_area_id','segment_group_id','email','phoneNumber','merchant_id')->where([['driver_delete', '=', NULL]])
            ->with([
            'DriverDocument' => function ($query) use ($where) {
                $query->where($where);
                $query->where('status',1);
                $query->whereHas('Document', function($q) use($where)
                {
                    $q->where('expire_date',1);
                });
            },
            'DriverVehicles' => function ($d_v) use ($where, $driverVehicleDocumentWith) {
                $d_v->with($driverVehicleDocumentWith)->whereHas('DriverVehicleDocument', function ($p) use ($where) {
                    $p->where($where);
                });
            },
            'DriverSegmentDocument' => function ($p) use ($where) {
                    $p->where($where);
                    $p->where('status',1);
                    $p->whereHas('Document', function($q) use($where)
                    {
                        $q->where('expire_date',1);
                    });
            },
            ])
            ->where(function ($q) use ($where, $driverVehicleDocumentWith, $permission_area_ids) {
                $q->whereHas('CountryArea', function ($q) use ($permission_area_ids) {
                    if(!empty($permission_area_ids)){
                        $q->whereIn("id",$permission_area_ids);
                    }
                });
                $q->whereHas('DriverDocument', function ($q) use ($where) {
                    $q->where($where);
                    $q->whereHas('Document', function($q) use($where)
                    {
                        $q->where('expire_date',1);
                    });
                })
                ->orWhereHas('DriverVehicles', function ($r) use ($where, $driverVehicleDocumentWith) {
                    $r->with($driverVehicleDocumentWith)
                        ->whereHas('DriverVehicleDocument', function ($s) use ($where) {
                        $s->where($where);
                            $s->where('status',1);
                            $s->whereHas('Document', function($q) use($where)
                            {
                                $q->where('expire_date',1);
                            });
                    });
                })
                ->orWhereHas('DriverSegmentDocument', function ($s) use ($where, $driverVehicleDocumentWith) {
                    $s->where($where);
                    $s->where('status',1);
                    $s->whereHas('Document', function($q) use($where)
                    {
                        $q->where('expire_date',1);
                    });
                });
            });
        if($merchant_id != NULL) {
            $query->where('merchant_id',$merchant_id);
        }
        if($pagination == false)
        {
            $drivers = $query->get();
        }
        else
        {
            $drivers = $query->latest()->paginate(10);
        }
        return $drivers;
    }
}
