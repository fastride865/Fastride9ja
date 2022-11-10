<?php

namespace App\Traits;
use App\Models\BusinessSegment\BusinessSegment;
use App\Models\CountryArea;
use App\Models\SegmentGroup;
use DB;
use App\Models\Segment;
use App\Models\ServiceType;
use App\Models\Merchant;
use App\Models\Category;
use App;
use Illuminate\Http\Request;
use App\Models\Configuration;

trait MerchantTrait{

    public function getMerchantSegmentServices($merchant_id,$call_from = '',$segment_group_id = NULL,$arr_segment = [],$country_area_id = NULL, $arr_segment_not_in = false,$arr_services = [])
    {
        $query = Segment::select('id', 'name', 'slag', 'icon', 'segment_group_id','sub_group_for_app','sub_group_for_admin')
            ->with(['Merchant'=> function ($q) use ($merchant_id) {
                $q->where('id', $merchant_id);
                $q->select('id','sequence');
                $q->orderBy('sequence','ASC');
            }])
            ->whereHas('Merchant', function ($q) use ($merchant_id) {
                $q->select('merchants.id as merchant_id');
                $q->where('merchant_id', $merchant_id);
                $q->select('id','sequence');
                $q->orderBy('sequence','ASC');
                $q->where('is_coming_soon',2);
            })
            ->with(['ServiceType.Merchant' => function ($qq) use ($merchant_id,$arr_services) {
                // $qq->select('segment_id', 'id', 'serviceName', 'type', 'additional_support', 'owner');
                //  $qq->whereHas('Merchant',function($qqq) use($merchant_id){
                //     $qqq->where('merchant_id',$merchant_id);
                //     $qqq->orderBy('sequence','ASC');
                // });
                $qq->where('merchant_id',$merchant_id);
                  if(!empty($arr_services))
                  {
                  $qq->whereIn('service_type_id',$arr_services);
                  }
            }])
            ->whereHas('ServiceType.Merchant',function($qqq) use($merchant_id){
                // $qqq->whereHas('Merchant',function($qqq) use($merchant_id){
                    $qqq->where('merchant_id',$merchant_id);
//                    $qqq->orderBy('sequence','ASC');
                // });
//                $qqq->orderBy('sequence','ASC');
            });
            if(!empty($country_area_id))
            {
                $query->with(['ServiceType.CountryArea'=>function($qqq) use($country_area_id){
                    $qqq->where('country_area_id',$country_area_id);
                }]);
                $query->whereHas('ServiceType.CountryArea',function($qqq) use($country_area_id){
                  $qqq->where('country_area_id',$country_area_id);
                });
            }
        if(!empty($segment_group_id))
        {
            $query->where('segment_group_id',$segment_group_id);
        }
        if(!empty($arr_segment))
        {
            if($arr_segment_not_in == true){
                $query->whereNotIn('id',$arr_segment);
            }else{
                $query->whereIn('id',$arr_segment);
            }
        }
        $query->join('merchant_segment', 'merchant_segment.segment_id', '=', 'id');
        $query->where('merchant_segment.merchant_id',$merchant_id);
        $query->orderBy('merchant_segment.sequence');
//        $query->where('is_coming_soon','!=',1);
        $segment_services = $query->get();

        if($call_from == 'api' || $call_from == "service_type_screen")
        {
            // get coming soon segment of merchant
            $is_coming_query = Segment::select('id', 'name', 'slag', 'icon', 'segment_group_id', 'sub_group_for_app', 'sub_group_for_admin')
                ->with(['Merchant' => function ($q) use ($merchant_id) {
                    $q->where('id', $merchant_id);
                    $q->select('id')->where('is_coming_soon', 1);
                }])
                ->whereHas('Merchant', function ($q) use ($merchant_id) {
                    $q->where('merchant_id', $merchant_id);
                    $q->orderBy('sequence', 'ASC')->where('is_coming_soon', 1);
                })
                ->with(['ServiceType.Merchant' => function ($qq) use ($merchant_id,$arr_services) {
                    // $qq->select('segment_id', 'id', 'serviceName', 'type', 'additional_support', 'owner');
                    // $qq->whereHas('Merchant',function($qqq) use($merchant_id){
                        $qq->where('merchant_id',$merchant_id);
                    //     $qqq->orderBy('sequence','ASC');
                    // });
                    if(!empty($arr_services))
                    {
                        $qq->whereIn('id',$arr_services);
                    }
                }])
                ->whereHas('ServiceType.Merchant',function($qqq) use($merchant_id){
                    $qqq->where('merchant_id',$merchant_id);
                    $qqq->orderBy('sequence','ASC');
                })
                ->get();
            $segment_services = $segment_services->merge($is_coming_query);
        }

        // p($segment_services);
        $arr_segment = $segment_services->map(function ($item) use ($merchant_id,$call_from,$country_area_id) {
            return [
                'segment_id' => $item->id,
                'slag' => $item->slag,
                'is_coming_soon' => !empty($item->Merchant[0]['pivot']->is_coming_soon) ? $item->Merchant[0]['pivot']->is_coming_soon : 2,
                'sub_group_for_app' => $item->sub_group_for_app,
                'segment_group_id' => $item->segment_group_id,
                'price_card_owner' => $item->Merchant[0]['pivot']->price_card_owner, // 1 admin, 2  driver
                'name' => !empty($item->Name($merchant_id)) ? $item->Name($merchant_id) : $item->slag,
                'segment_icon' => isset($item->Merchant[0]['pivot']->segment_icon) && !empty($item->Merchant[0]['pivot']->segment_icon) ? get_image($item->Merchant[0]['pivot']->segment_icon, 'segment', $merchant_id, true) :
                    get_image($item->icon, 'segment_super_admin', NULL, false),
                'arr_services' => $this->serviceTypes($item->ServiceType,$call_from,$merchant_id,$country_area_id),
            ];
        });

        if($call_from == 'api')
        {
            return $arr_segment;
        }
        else
        {
            $arr_segment = $arr_segment->toArray();
            $arr_segment = array_column($arr_segment,NULL,'segment_id');
            return $arr_segment;
        }
    }

    public function serviceTypes($arr_data,$call_from,$merchant_id = NULL,$country_area_id = NULL)
    {

        $arr_service = [];
        $data_list =  [];
        foreach($arr_data as $data)
        {
          if(isset($data->Merchant[0]) && ($country_area_id  == NULL || (!empty($country_area_id) && $data->CountryArea->count() > 0)))
           {
            $data_list =  array(
                'id' => $data->id,
                'segment_id' => $data->segment_id,
                'serviceName' => $data->serviceName,
                'service_sequence' => isset($data->Merchant[0]['pivot']) ? $data->Merchant[0]['pivot']->sequence : 0,
                'service_icon' =>isset($data->Merchant[0]['pivot']) && !empty($data->Merchant[0]['pivot']->service_icon) ? get_image($data->Merchant[0]['pivot']->service_icon,'service',$merchant_id) : "",
                'locale_service_name' => !empty($data->ServiceName($merchant_id)) ? $data->ServiceName($merchant_id) : $data->serviceName,
                'locale_service_description' => !empty($data->ServiceDescription($merchant_id)) ? $data->ServiceDescription($merchant_id) : "",
            );
            if($call_from == 'api')
            {
                $arr_service[] = $data_list;
            }
            else
            {
                $arr_service[$data->id] = $data_list;
            }
          }
        }
        // sort the sequences of services
        array_multisort(array_column($arr_service, 'service_sequence'), SORT_ASC, $arr_service);
        return $arr_service;
    }

    public function getMerchantServicesByArea($area_id,$segment_id,$vehicle_type_id,$return_type='',$segment_group = null)
    {
        $arr_services = [];
        if($segment_group == 1)
        {
            $areas = CountryArea::with(['ServiceTypes'=>function($q) use($area_id,$segment_id,$vehicle_type_id){
                $q->join('country_area_vehicle_type as cavt','cavt.service_type_id','=','service_types.id');
                if(!empty($vehicle_type_id))
                {
                    // in case of food and grocery price card
                $q->where('cavt.vehicle_type_id',$vehicle_type_id);
                }
                $q->where('cavt.segment_id',$segment_id);
                $q->where('cavt.country_area_id',$area_id);
            }])->where([['id', '=', $area_id]])->first();
        }
        elseif($segment_group == 2)
        {
            $areas = CountryArea::with(['ServiceTypes'=>function($q) use($area_id,$segment_id){
                $q->where('country_area_service_type.segment_id',$segment_id);
            }])->where([['id', '=', $area_id]])->first();
        }
        if(!empty($areas->id))
        {
            $service_type = $areas->ServiceTypes;
            if($return_type == 'array')
            {
                foreach ($service_type as $value) {
                    $arr_services[$value->id ] = $value->serviceName;
                }
            }
            else
            {
                $arr_services = $service_type;
            }
        }
        return $arr_services;
    }

//    public function getSegmentServicesXXX($request)
//    {
//        $segment_id = $request->segment_id;
//        $merchant_id = $request->merchant_id;
//        $segment_group_id = $request->segment_group_id;
//        $segment_price_card_id = $request->segment_price_card_id;
//        // p($segment_price_card_id);
//        //p($segment_group_id);
//        $driver_id = $request->has('driver_id') ? $request->driver_id : NULL;
//        // p($driver_id);
//        $country_area_id = $request->area;
//        $price_card_owner = $request->has('price_card_owner') ?  $request->price_card_owner : 1;
//        // p($price_card_owner);
//        $query = ServiceType::select('service_types.id','service_types.serviceName')
//            ->with(['Merchant'=> function ($q) use ($segment_id,$merchant_id) {
//               $q->where('merchant_id', $merchant_id);
//               $q->orderBy('sequence');
//           }])
//            ->whereHas('Merchant', function ($q) use ($segment_id,$merchant_id) {
//                $q->where('merchant_id', $merchant_id);
//                $q->orderBy('sequence');
//            })
//            ->whereHas('CountryArea', function ($q) use ($segment_id,$merchant_id,$country_area_id) {
//                $q->where('country_area_id', $country_area_id);
//            })
//            ->with(['Driver'=> function ($q) use ($segment_id,$merchant_id,$driver_id) {
//                $q->where('driver_id', $driver_id);
//                $q->where('segment_id', $segment_id);
//            }])
////            ->whereHas('Driver', function ($q) use ($segment_id,$merchant_id,$driver_id) {
////                $q->where('driver_id', $driver_id);
////                $q->where('segment_id', $segment_id);
////            })
//            ->where('service_types.segment_id',$segment_id);
//        if($segment_group_id == 2)
//        {
//            $query->with(['SegmentPriceCardDetail'=>function($q) use($price_card_owner,$segment_price_card_id,$driver_id,$merchant_id,$country_area_id){
//
//                $q->addSelect('id','service_type_id','amount');
//                if(!empty($segment_price_card_id))
//                {
//                    $q->where('segment_price_card_id', $segment_price_card_id);
//                }
//                $q->whereHas('SegmentPriceCard', function ($qq) use ($segment_price_card_id,$driver_id,$price_card_owner,$merchant_id, $country_area_id) {
//                    $qq->where('merchant_id', $merchant_id);
//                    if(!empty($segment_price_card_id))
//                    {
//                        $qq->where('id', $segment_price_card_id);
//                    }
//                    if($price_card_owner == 2)
//                    {
//                        $qq->where('driver_id', $driver_id);
//                    }
//                    if(!empty($country_area_id))
//                    {
//                        $qq->where('country_area_id', $country_area_id);
//                    }
//                });
//            }]);
//            // if($price_card_owner == 1 ) {
//            //     $query->whereHas('SegmentPriceCardDetail', function ($q) use ($segment_price_card_id) {
//            //         $q->addSelect('id','service_type_id','amount');
//            //         $q->where('segment_price_card_id', $segment_price_card_id);
//            //     });
//            // }
//        }
//        $arr_services = $query->get();
//        if(count($arr_services)> 0)
//        {
//            $arr_services = $arr_services->map(function ($item, $key) use($merchant_id) {
//                // p($item->SegmentPriceCardDetail);
//                return array(
//                    'id' => $item->id,
//                    'name' => !empty($item->ServiceName($merchant_id)) ? $item->ServiceName($merchant_id) : $item->serviceName,
//                    'amount' =>isset($item->SegmentPriceCardDetail->amount) ? $item->SegmentPriceCardDetail->amount : 0,
//                    'segment_price_card_detail_id' => isset($item->SegmentPriceCardDetail->id)   ? $item->SegmentPriceCardDetail->id :null,
//                    'selected' => isset($item->Driver[0]->id)  && !empty($item->Driver[0]->id) ? true :false,
//                    'description' => !empty($item->ServiceDescription($merchant_id)) ? $item->ServiceDescription($merchant_id) : "",
//                    'service_icon' =>isset($data->Merchant[0]['pivot']) && !empty($data->Merchant[0]['pivot']->service_icon) ? get_image($data->Merchant[0]['pivot']->service_icon,'service',$merchant_id) : "",
//                );
//            });
//        }
//        return $arr_services;
//    }

    public function getSegmentServices($request)
   {
       $segment_id = $request->segment_id;
       $merchant_id = $request->merchant_id;
       $segment_group_id = $request->segment_group_id;
       $segment_price_card_id = $request->segment_price_card_id;
       // p($segment_price_card_id);
       //p($segment_group_id);
       $driver_id = $request->has('driver_id') ? $request->driver_id : NULL;
       // p($driver_id);
       $country_area_id = $request->area;
       $price_card_owner = $request->has('price_card_owner') ?  $request->price_card_owner : 1;
       // p($price_card_owner);
       // p($merchant_id);
       $query = ServiceType::select('service_types.id','service_types.serviceName')
           ->with(['Merchant'=> function ($q) use ($segment_id,$merchant_id) {
               $q->where('merchant_id', $merchant_id);
               $q->orderBy('sequence');
           }])
           ->whereHas('Merchant', function ($q) use ($segment_id,$merchant_id) {
               $q->where('merchant_id', $merchant_id);
               $q->orderBy('sequence');
           })
           ->whereHas('CountryArea', function ($q) use ($segment_id,$merchant_id,$country_area_id) {
               $q->where('country_area_id', $country_area_id);
           })
           ->with(['Driver'=> function ($q) use ($segment_id,$merchant_id,$driver_id) {
               $q->where('driver_id', $driver_id);
               $q->where('segment_id', $segment_id);
           }])
//            ->whereHas('Driver', function ($q) use ($segment_id,$merchant_id,$driver_id) {
//                $q->where('driver_id', $driver_id);
//                $q->where('segment_id', $segment_id);
//            })
           ->where('service_types.segment_id',$segment_id);
       if($segment_group_id == 2)
       {
           $query->with(['SegmentPriceCardDetail'=>function($q) use($price_card_owner,$segment_price_card_id,$driver_id,$merchant_id,$country_area_id){

               $q->addSelect('id','service_type_id','amount');
               if(!empty($segment_price_card_id))
               {
                   $q->where('segment_price_card_id', $segment_price_card_id);
               }
               $q->whereHas('SegmentPriceCard', function ($qq) use ($segment_price_card_id,$driver_id,$price_card_owner,$merchant_id, $country_area_id) {
                   $qq->where('merchant_id', $merchant_id);
                   if(!empty($segment_price_card_id))
                   {
                       $qq->where('id', $segment_price_card_id);
                   }
                   if($price_card_owner == 2)
                   {
                       $qq->where('driver_id', $driver_id);
                   }
                   if(!empty($country_area_id))
                   {
                       $qq->where('country_area_id', $country_area_id);
                   }
               });
           }]);
           // if($price_card_owner == 1 ) {
           //     $query->whereHas('SegmentPriceCardDetail', function ($q) use ($segment_price_card_id) {
           //         $q->addSelect('id','service_type_id','amount');
           //         $q->where('segment_price_card_id', $segment_price_card_id);
           //     });
           // }
       }
       $arr_services = $query->get();
       if(count($arr_services)> 0)
       {
           $arr_services = $arr_services->map(function ($item, $key) use($merchant_id) {
               // p($item->SegmentPriceCardDetail);
               // if($item->id == 11)
               // {
               //   //p($item->Merchant[0]);
               // }
               return array(
                   'id' => $item->id,
                   'name' => !empty($item->ServiceName($merchant_id)) ? $item->ServiceName($merchant_id) : $item->serviceName,
                   'amount' =>isset($item->SegmentPriceCardDetail->amount) ? $item->SegmentPriceCardDetail->amount : 0,
                   'segment_price_card_detail_id' => isset($item->SegmentPriceCardDetail->id)   ? $item->SegmentPriceCardDetail->id :null,
                   'selected' => isset($item->Driver[0]->id)  && !empty($item->Driver[0]->id) ? true :false,
                     'description' => !empty($item->ServiceDescription($merchant_id)) ? $item->ServiceDescription($merchant_id) : "",
                   'service_icon' =>isset($item->Merchant[0]['pivot']) && !empty($item->Merchant[0]['pivot']->service_icon) ? get_image($item->Merchant[0]['pivot']->service_icon,'service',$merchant_id) : "",
                   'service_sequence' => isset($item->Merchant[0]['pivot']) ? $item->Merchant[0]['pivot']->sequence : 0,
               );
           });
           // sort the sequences of $arr_services
           $arr_services = $arr_services->toArray(); // it must be array
           array_multisort(array_column($arr_services, 'service_sequence'), SORT_ASC, $arr_services);
       }
       return $arr_services;
   }

//    public function segmentGroup($merchant_id)
//    {
//        $arr_groups = SegmentGroup::select('id','group_name')
//            ->with(['Segment'=>function($q) use($merchant_id){
//                $q->addSelect('id','name','segment_group_id');
//                $q->whereHas('Merchant',function($qq) use($merchant_id){
//                    $qq->where('merchant_id',$merchant_id);
//                });
//            }])
//            ->get();
//        foreach ($arr_groups as $key => $groups) {
//            $groups->Segment->transform(function ($item, $key) {
//                $item->name = $item->language_single['name'];
//                $item->id = $item->id;
//                return $item;
//            });
//////                ->sortBy('AreaName')->values();
//        }
//
//        return $arr_groups;
//    }

    public function segmentGroup($merchant_id,$return_type = "",$string_file = "")
    {
        $arr_groups = SegmentGroup::select('id','group_name')
            ->with(['Segment'=>function($q) use($merchant_id){
                $q->addSelect('id','segment_group_id','icon','name as segment_name');
                $q->whereHas('Merchant',function($qq) use($merchant_id){
                    $qq->where('merchant_id',$merchant_id);
                });
                $q->with(['ServiceType'=>function($qq) use($merchant_id){
                    $qq->addSelect('id','segment_id','serviceName');
                    $qq->whereHas('MerchantServiceType', function ($qqq) use ($merchant_id) {
                        $qqq->where('merchant_id', $merchant_id);
                    });
                }]);
            }])->whereHas('Segment',function($q) use($merchant_id){
                $q->addSelect('id','segment_group_id','icon','name as segment_name');
                $q->whereHas('Merchant',function($qq) use($merchant_id){
                    $qq->where('merchant_id',$merchant_id);
                });
                $q->with(['ServiceType'=>function($qq) use($merchant_id){
                    $qq->addSelect('id','segment_id','serviceName');
                    $qq->whereHas('MerchantServiceType', function ($qqq) use ($merchant_id) {
                        $qqq->where('merchant_id', $merchant_id);
                    });
                }]);
            })->get();
        if($return_type == "drop_down")
        {
            $return_group = [];
            foreach ($arr_groups as $key => $groups) {
                if($groups->Segment->count() > 0)
                {
                    $groups->id == 1 ? trans("$string_file.vehicle_based") : trans("$string_file.helper_based");
                    $return_group[$groups->id] = $groups->group_name;
                }
            }
            $return = ['arr_group'=>$return_group,
                'single_group'=>count($return_group) == 1 ? 1:0];
            return $return;

        }
        foreach ($arr_groups as $key => $groups) {
            // This is static text because app developer check is working according to this text
            $groups->group_name = $groups->id == 1 ? "Vehicle Based" : "Helper Based";
//            $groups->group_name = $groups->id == 1 ? trans("$string_file.vehicle_based") : trans("$string_file.helper_based");
            $groups->Segment->transform(function ($item, $key) use($merchant_id) {
                $merchant_segment =  $item->Merchant->where('id',$merchant_id);
                $merchant_segment = collect($merchant_segment->values());
                // $item->icon = get_image($item->icon, 'segment_super_admin', NULL, false);
                $item->icon=isset($merchant_segment[0]['pivot']->segment_icon) && !empty($merchant_segment[0]['pivot']->segment_icon) ? get_image($merchant_segment[0]['pivot']->segment_icon, 'segment', $merchant_id, true,false) :
                    get_image($item->icon, 'segment_super_admin', NULL, false);
                unset($item->segment_group_id);
                unset($item->ServiceType);
                unset($item->Merchant);
                return $item;
            });
        }
        return $arr_groups;
    }

    public function getMerchantPaymentMethod($arr_payment_method,$merchant_id)
    {
        $arr_payment = [];
        foreach ($arr_payment_method as $method)
        {
            $arr_payment[$method->id] = $method->MethodName($merchant_id) ? $method->MethodName($merchant_id) : $method->payment_method;
        }
        return $arr_payment;
    }

    public function getAdditionalSupportServices($merchant_id = null,$support)
    {
        $merchant_id = empty($merchant_id) ? get_merchant_id() : $merchant_id;
        $merchant_services = Merchant::with(['ServiceType'=>function($q) use($support){
            $q->where('additional_support',$support);
        }])->find($merchant_id);

        $arr_services = [];
        foreach ($merchant_services['ServiceType'] as $service)
        {
            $arr_services[$service->id] =  $service->serviceName;
        }
        return $arr_services;
    }

    public function getSegmentService($segment_id,$merchant_id,$return ='')
    {
        $service_type = ServiceType::select('id','serviceName')
            ->whereHas('MerchantServiceType', function ($q) use ($segment_id,$merchant_id) {
                $q->where('merchant_id', $merchant_id);
                $q->orderBy('sequence');
            })->where('segment_id',$segment_id)->first();

        if($return == 'id')
        {
            return $service_type->id;
        }
        return $service_type;
    }
    public function getMerchantBusinessSegment(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $segment_id =  $request->segment_id;
        $user_id =  $request->user_id;
        $is_fav = $request->is_favourite;
        $is_search = $request->is_search;
        $search_text = $request->search_text;
        $is_popular = $request->is_popular;
        $distance_unit = 1;
        $radius = $distance_unit == 2 ? 3958.756 : 6367;
        $distance = $request->distance;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        // 1 means demo login
        $user = $request->user('api');
        $user_type = $user->login_type == 1 ?  $user->login_type : NULL;
        $locale = \App::getLocale();

        $arr_categories_id = [];
        if(!empty($search_text) && $is_search == 1)
        {
            $arr_category = Category::select('id')
                ->with(['LangCategory'=>function($q) use ($search_text,$locale){
                    $q->where('name','like',"%$search_text%")
                        ->where(function ($q) use ($locale) {
                            $q->where('locale', $locale);
                            $q->orWhere('locale', '!=', NULL);
                        });
                }])
                ->whereHas('LangCategory',function($q) use ($search_text,$locale){
                    $q->where('name','like',"%$search_text%")
                        ->where(function ($q) use ($locale) {
                            $q->where('locale', $locale);
                            $q->orWhere('locale', '!=', NULL);
                        });
                })
                ->whereHas('Segment',function($q) use ($segment_id){
                    $q->where('segment_id',$segment_id);
                })
                ->where('merchant_id',$merchant_id)
                ->where('delete',NULL)
                ->get();
            $arr_categories_id = array_pluck($arr_category,'id');
        }

        $haversineSQL = '( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( latitude ) ) ) )';
        // p($haversineSQL);
        $query = BusinessSegment::with(['StyleManagement'=>function($q) {
            $q->where('delete',NULL);
        }])
            ->select('id','country_area_id','full_name','country_id','delivery_time','minimum_amount','business_logo','is_popular','minimum_amount_for','open_time','close_time','latitude','longitude','rating')
            ->addSelect(DB::raw($haversineSQL .' AS distance'))
            ->where([['merchant_id','=',$merchant_id],['segment_id','=',$segment_id],['status','=',1]])
            ->where(function($q) use ($is_search,$search_text,$locale,$arr_categories_id){
                if(!empty($search_text) && $is_search == 1)
                {
                    $q->where('full_name','like',"%$search_text%");
                    $q->orWhereIn('business_segments.id', function ($query) use ($search_text,$locale,$arr_categories_id) {
                        $query->select('p.business_segment_id')
                            ->from('products as p')
                            ->join('language_products as lp','p.id','=','lp.product_id')
                            ->where(function($q) use($search_text,$locale){
                                $q->where('lp.name','like',"%$search_text%");
                                $q->where(function ($q) use ($locale) {
                                    $q->where('locale', $locale);
                                    $q->orWhere('locale', '!=', NULL);
                                });
                            });
                        if(!empty($arr_categories_id))
                        {
                        $query->orWhereIn('category_id',$arr_categories_id);
                        }

                    });
                }
            })
            ->where(function($q) use ($is_popular){
                if(!empty($is_popular) && $is_popular == "YES")
                {
                    $q->where('is_popular',1);
                }
            })
            ->where('country_area_id',$request->area)
            ->orderBy('distance');
            if(!empty($is_fav) && $is_fav == "YES")
            {
                $query->whereHas('FavouriteBusinessSegment',function($qq) use ($user_id,$segment_id){
                    $qq->where([['user_id','=',$user_id],['segment_id','=',$segment_id]]);

                });
            }
          // means radius check works only in case of normal user not demo
          if($user_type !=1)
          {
              $query->whereRaw($haversineSQL . '<= ?', [$distance]);
          }
        $arr_restaurant =  $query->paginate(10);
        // p($arr_restaurant);
        return $arr_restaurant;
    }

    public function getMerchantSegmentDetails($merchant_id,$arr_segment = NULL){
        $query = Segment::select('id', 'name', 'slag', 'icon', 'segment_group_id','sub_group_for_app')
            ->with(['Merchant'=> function ($q) use ($merchant_id) {
                $q->where('id', $merchant_id);
                $q->select('id');
            }])
//            ->where('is_coming_soon','!=',1)
            ->whereHas('Merchant', function ($q) use ($merchant_id) {
                $q->where('merchant_id', $merchant_id);
                $q->orderBy('sequence','ASC');
            });
        if(!empty($arr_segment))
        {
            $query->whereIn('id',$arr_segment);
        }
        $segments = $query->get();
        $arr_segment = $segments->map(function ($item) use ($merchant_id) {
            return [
                'segment_id' => $item->id,
                'slag' => $item->slag,
                'sub_group_for_app'=>$item->sub_group_for_app,
                'segment_group_id' => $item->segment_group_id,
                'name' => !empty($item->Name($merchant_id)) ? $item->Name($merchant_id) : $item->slag,
                'segment_icon' => isset($item->Merchant[0]['pivot']->segment_icon) && !empty($item->Merchant[0]['pivot']->segment_icon) ? get_image($item->Merchant[0]['pivot']->segment_icon, 'segment', $merchant_id, true) :
                    get_image($item->icon, 'segment_super_admin', NULL, false),
            ];
        });
        return $arr_segment;
    }

    /******************************** Language String Module Start ******************************/

    public function getStringFile($merchant_id = NULL,$merchant = [])
    {
        $file = "";
        if(!empty($merchant))
        {
            $file = $merchant->string_file;
        }
        elseif(!empty($merchant_id))
        {
            $merchant = Merchant::select('string_file')->Find($merchant_id);
            $file = isset($merchant->string_file) && !empty($merchant->string_file) ? $merchant->string_file : "";
        }

        if(!empty($file))
        {
            $locale = App::getLocale();
            $file_path = base_path().'/resources/lang/'.$locale.'/'.$file.'.php';

            if(!file_exists($file_path))
            {
                $file = "all_in_one";
            }

        }
        else
        {
            $file = "all_in_one";
        }
        return $file;
    }

    public function getStringFileConfig()
    {
        return [
            'all_in_one'=>"All Segments(all_in_one.php)",
            'taxi'=>"Taxi(taxi.php)",
            'delivery'=>"Delivery(delivery.php)",
            'food'=>"Food (food.php)",
            'grocery'=>"Grocery (grocery.php)",
            'handyman'=>"Handyman(Plumber,Cleaning, Electrician) (handyman.php)",
        ];
    }
    public function merchantType($merchant)
    {
        $merchant_type = "";
        $segment_type = array_pluck($merchant->Segment,'segment_group_id');
        if(in_array(1,$segment_type) && in_array(2,$segment_type))
        {
            $merchant_type = "BOTH";
        }
        elseif(in_array(1,$segment_type))
        {
            $merchant_type = "VEHICLE";
        }
        elseif(in_array(1,$segment_type) && in_array(2,$segment_type))
        {
            $merchant_type = "HANDYMAN";
        }
        return $merchant_type;
    }
    /******************************** Language String Module End ********************************/

    /**
     * @param $merchant_id
     * @return bool
     * @summery : Check merchant having handyman segment and promocode is enable or not for that
     * @author Bhuvanesh Soni
     */
    public function merchantHandymanPromocode($merchant_id){
        $merchant = Merchant::find($merchant_id);
        $handyman_apply_promocode = false;
        $arr_segment_group =   $this->segmentGroup($merchant_id,$return_type = "drop_down","");
        $merchant_segment_group = isset($arr_segment_group['arr_group']) ?  array_keys($arr_segment_group['arr_group']) : [];
        if(in_array(2,$merchant_segment_group) && isset($merchant->HandymanConfiguration->advance_payment_of_min_bill) && $merchant->HandymanConfiguration->advance_payment_of_min_bill == false && $merchant->HandymanConfiguration->price_type_config == "FIXED"){
            $handyman_apply_promocode = true;
        }
        return $handyman_apply_promocode;
    }

    // set step value in html form based on trip calculation
    public function stepValue($merchant_id){

        $settings = Configuration::select('trip_calculation_method')->where([['merchant_id', '=', $merchant_id]])->first();
        switch ($settings->trip_calculation_method) {
            case "1":
                $step = 1;
                break;
            case "2":
                $step = 0.01;
                break;
            case "3":
                $step = 0.01;
                break;
            case "4":
                $step = 0.001;
                break;
            default:
                $step = 1;
        }
        return $step;
    }
    
    // popular restaurant/stores for home screen
    public function getMerchantPopularBusinessSegment(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $distance_unit = 1;
        $radius = $distance_unit == 2 ? 3958.756 : 6367;
        $distance = $request->distance;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $segment_slug = $request->segment_slug;
        // 1 means demo login
        $user = $request->user('api');
        $user_type = $user->login_type == 1 ?  $user->login_type : NULL;

        $haversineSQL = '( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( latitude ) ) ) )';
        $query = BusinessSegment::with('StyleManagement')->select('id','country_area_id','segment_id','full_name','country_id','delivery_time','minimum_amount','business_logo','is_popular','minimum_amount_for','open_time','close_time','latitude','longitude','rating')
            ->addSelect(DB::raw($haversineSQL .' AS distance'))
            ->where([['merchant_id','=',$merchant_id],['is_popular','=',1],['status','=',1]])
            ->where('country_area_id',$request->area)
            ->orderBy('distance');
        // means radius check works only in case of normal user not demo
        if($user_type !=1)
        {
            $query->whereRaw($haversineSQL . '<= ?', [$distance]);
        }

            $query->whereHas('Segment',function($q) use($segment_slug){
                $q->whereIn('sub_group_for_app',[1,2]); // food and food's clone
//                if($segment_slug ="FOOD")
//                {
//                    $q->where('sub_group_for_app',1); // food and food's clone
//                }
//                else
//                {
//                    $q->where('sub_group_for_app',2); // grocery and grocery's clones
//                }

            });

        $arr_restaurant =  $query->get();
        return $arr_restaurant;
    }

    // popular restaurant/stores for home screen
    public function getUserFavouriteBusinessSegment(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $distance_unit = 1;
        $radius = $distance_unit == 2 ? 3958.756 : 6367;
        $distance = $request->distance;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $segment_slug = $request->segment_slug;
        // 1 means demo login
        $user = $request->user('api');
        $user_type = $user->login_type == 1 ?  $user->login_type : NULL;

        $haversineSQL = '( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( latitude ) ) ) )';
        $query = BusinessSegment::with('StyleManagement')->select('id','country_area_id','segment_id','full_name','country_id','delivery_time','minimum_amount','business_logo','is_popular','minimum_amount_for','open_time','close_time','latitude','longitude','rating')
            ->addSelect(DB::raw($haversineSQL .' AS distance'))
            ->where([['merchant_id','=',$merchant_id],['is_popular','=',1],['status','=',1]])
            ->where('country_area_id',$request->area)
            ->orderBy('distance');
        // means radius check works only in case of normal user not demo
        if($user_type !=1)
        {
            $query->whereRaw($haversineSQL . '<= ?', [$distance]);
        }

        $query->whereHas('Segment',function($q) use($segment_slug){
            $q->whereIn('sub_group_for_app',[1,2]); // food and food's clone
//                if($segment_slug ="FOOD")
//                {
//                    $q->where('sub_group_for_app',1); // food and food's clone
//                }
//                else
//                {
//                    $q->where('sub_group_for_app',2); // grocery and grocery's clones
//                }

        });

        $arr_restaurant =  $query->get();
        return $arr_restaurant;
    }
}
