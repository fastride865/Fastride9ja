<?php

namespace App\Models;

use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ServiceTimeSlot extends Model
{
    function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
//    function ServiceType()
//    {
//        return $this->belongsTo(ServiceType::class);
//    }
    function ServiceTimeSlotDetail()
    {
        return $this->hasMany(ServiceTimeSlotDetail::class);
    }

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }

    public function Segment()
    {
        return $this->belongsTo(Segment::class);
    }

//    public static function getServiceTimeSlot($request)
//    {
//        $driver_id = $request->driver_id;
//        if($request->calling_from == 'user')
//        {
//            if(!empty($driver_id))
//            {
//                $driver = Driver::Find($driver_id);
//            }
//            elseif($request->auto_assign == 1 ){
//                $arr_providers = Driver::getNearestPlumbers($request);
//                $driver = isset($arr_providers[0]) ? $arr_providers[0] : null;
//                $driver_id = isset($driver->id) ? $driver->id : null;
//            }
//        }
//        else{
//            $driver = $request->user('api-driver');
//            $driver_id = $driver->id;
//        }
//        $slot_type = $request->slot_type;
//        $merchant_id = $request->merchant_id;
//        $segment_id = $request->segment_id;
//        $arr_driver_service = array_pluck($driver->ServiceType->where('segment_id',$segment_id),'id');
//        $country_area_id = $driver->country_area_id;
//        $data = ServiceTimeSlot::select('id','day')
//            ->with(['ServiceTimeSlotDetail'=>function($q) use($segment_id,$merchant_id,$country_area_id,$arr_driver_service,$driver_id,$slot_type){
//                $q->select('id','service_time_slot_id','slot_time_text');
//                $q->orderBy('from_time');
//                 if($slot_type == 'selected')
//                 {
//                     $q->join(DB::raw('(SELECT das.service_time_slot_detail_id,das.id as driver_availability_slot_id  FROM `driver_availability_slots` as das where( segment_id='.$segment_id.' or segment_id IS NULL) and ( driver_id='.$driver_id.' or driver_id IS NULL)) das'),'service_time_slot_details.id','=','das.service_time_slot_detail_id');
////                     $q->whereHas('DriverAvailabilitySlot',function($qq) use($merchant_id){
////                         $qq->where('merchant_id',$merchant_id);
////                     });
//                 }
//                 else
//                 {
//                   $q->leftJoin(DB::raw('(SELECT das.service_time_slot_detail_id,das.id as driver_availability_slot_id  FROM `driver_availability_slots` as das where( segment_id='.$segment_id.' or segment_id IS NULL) and ( driver_id='.$driver_id.' or driver_id IS NULL)) das'),'service_time_slot_details.id','=','das.service_time_slot_detail_id');
//                 }
//                 }])
//            ->whereHas('ServiceTimeSlotDetail', function($q){
//                $s = $q->get();
//                if(!empty($s)){
//                    return $s;
//                }
//            })
//            ->orderBy('day')
//            ->where('segment_id',$segment_id)
//            ->whereIn('service_type_id',$arr_driver_service)
//            ->where([['merchant_id','=',$merchant_id],['country_area_id','=',$country_area_id]])
//            ->get();
//        $arr_days = get_days();
//
//        $return_data = $data->map(function($item) use($arr_days){
//            $day = isset($arr_days[$item->day]) ? $arr_days[$item->day] : "";
//            return  array(
//                'id' => $item->id,
//                'day' =>$item->day,
//                'day_title' =>$day,
//                'service_time_slot' =>$item->ServiceTimeSlotDetail->toArray()
//            );
//        });
//
//        $final_return_data['time_slots'] = $return_data;
//        if($request->calling_from == 'user')
//            {
//                $final_return_data['driver_details'] = [
//                    'driver_id' => $driver->id,
//                    'latitude' => $driver->current_latitude,
//                    'longitude' => $driver->current_longitude,
//                    'first_name' => $driver->first_name,
//                    'last_name' => $driver->last_name,
//                    'profile_image' => get_image($driver->profile_image, 'driver', $merchant_id),
//                ];
//            }
//        return $final_return_data;
//    }

    public static function getServiceTimeSlot($request, $string_file = "")
    {
        try {
            $driver_id = $request->driver_id;
            if ($request->calling_from == 'grocery') {
                $slot_type = '';
                $merchant_id = $request->merchant_id;
                $segment_id = $request->segment_id;
                $country_area_id = $request->area;
            } else {
                if ($request->calling_from == 'user') {
                    if (!empty($driver_id)) {
                        $driver = Driver::Find($driver_id);
                    } elseif ($request->auto_assign == 1) {
                        $arr_providers = Driver::getNearestPlumbers($request);
                        $driver = isset($arr_providers[0]) ? $arr_providers[0] : null;
                        if (empty($driver)) {
                            throw new \Exception(trans("$string_file.no_provider_available"));
                        }
                        $driver_id = isset($driver->id) ? $driver->id : null;
                    }
                } elseif ($request->calling_from == 'admin') {
                    $driver_id = $request->driver_id;
                    $driver = $request->driver;
                    //            $request->request->add(['segment_id' => 7]);
                } else {
                    $driver = $request->user('api-driver');
                    $driver_id = $driver->id;
                }
                $slot_type = $request->slot_type;
                $merchant_id = !empty($request->merchant_id) ? $request->merchant_id : $driver->merchant_id;
                $segment_id = $request->segment_id;
                $country_area_id = $driver->country_area_id;
            }
            $config = Configuration::select("time_format", "merchant_id")->where('merchant_id', $merchant_id)->First();
            $time_format = $config->time_format;
            $query = ServiceTimeSlot::select('id', 'day', 'status', 'segment_id')
                ->with(['ServiceTimeSlotDetail' => function ($q) use ($segment_id, $merchant_id, $country_area_id, $driver_id, $slot_type) {
                    $q->select('id', 'service_time_slot_id', 'to_time', 'from_time');
                    $q->orderBy('from_time');
                    $q->with(['Driver' => function ($qq) use ($driver_id, $segment_id) {
                        $qq->where('driver_id', $driver_id);
                        if (is_array($segment_id)) {
                            $qq->whereIn('segment_id', $segment_id);
                        } else {
                            $qq->where('segment_id', $segment_id);
                        }
                    }]);
                    if ($slot_type == 'selected') {
                        //$q->join(DB::raw('(SELECT das.service_time_slot_detail_id,das.id as driver_availability_slot_id  FROM `driver_availability_slots` as das where( segment_id='.$segment_id.' or segment_id IS NULL) and ( driver_id='.$driver_id.' or driver_id IS NULL)) das'),'service_time_slot_details.id','=','das.service_time_slot_detail_id');
                        $q->whereHas('Driver', function ($qq) use ($driver_id, $segment_id) {
                            $qq->where('driver_id', $driver_id);
                            if (is_array($segment_id)) {
                                $qq->whereIn('segment_id', $segment_id);
                            } else {
                                $qq->where('segment_id', $segment_id);
                            }
                        });
                    } else {
                        $q->with(['HandymanOrder' => function ($qq) use ($driver_id, $segment_id, $merchant_id) {
                            $qq->where('driver_id', $driver_id);
                            if (is_array($segment_id)) {
                                $qq->whereIn('segment_id', $segment_id);
                            } else {
                                $qq->where('segment_id', $segment_id);
                            }
                            $qq->where('merchant_id', $merchant_id);
                            $qq->where('is_order_completed', '!=', 1);
                            $qq->whereIn('order_status', [4, 6, 7]);
                        }]);
                        // $q->leftJoin(DB::raw('(SELECT das.service_time_slot_detail_id,das.id as driver_availability_slot_id  FROM `driver_availability_slots` as das where( segment_id='.$segment_id.' or segment_id IS NULL) and ( driver_id='.$driver_id.' or driver_id IS NULL)) das'),'service_time_slot_details.id','=','das.service_time_slot_detail_id');
                    }

                }])
                ->orderBy('day');
            if (is_array($segment_id)) {
                $query->whereIn('segment_id', $segment_id);
            } else {
                $query->where('segment_id', $segment_id);
            }
            $query->where([['merchant_id', '=', $merchant_id], ['country_area_id', '=', $country_area_id]]);
            $data = $query->get();
//        $arr_days = \Config::get('custom.days');
            $arr_days = get_days($string_file);

            $return_data = $data->map(function ($item) use ($arr_days, $time_format) {
                $day = isset($arr_days[$item->day]) ? $arr_days[$item->day] : "";
                $slot_details = $item->ServiceTimeSlotDetail->map(function ($item_inner) use ($time_format) {
                    $start = strtotime($item_inner->from_time);
                    $start = $time_format == 2 ? date("H:i", $start) : date("h:i a", $start);
                    $end = strtotime($item_inner->to_time);
                    $end = $time_format == 2 ? date("H:i", $end) : date("h:i a", $end);
                    $time = $start . "-" . $end;
                    return array(
                        'id' => $item_inner->id,
                        'service_time_slot_id' => $item_inner->service_time_slot_id,
                        'from_time' => $item_inner->from_time,
                        'to_time' => $item_inner->to_time,
                        'slot_time_text' => $time,
                        'selected' => isset($item_inner->Driver[0]['pivot']->driver_id) ? true : false,
                        'booked' => !empty($item_inner->HandymanOrder) && $item_inner->HandymanOrder->count() > 0 ? true : false,
                    );
                });
                return array(
                    'id' => $item->id,
                    'segment_id' => $item->segment_id,
                    'day' => $item->day,
                    'status' => $item->status,
                    'day_title' => $day,
                    'service_time_slot' => $slot_details
                );
            });

            $final_return_data['time_slots'] = $return_data;
            if ($request->calling_from == 'user') {
                $final_return_data['driver_details'] = [
                    'driver_id' => $driver->id,
                    'latitude' => $driver->current_latitude,
                    'longitude' => $driver->current_longitude,
                    'first_name' => $driver->first_name,
                    'last_name' => $driver->last_name,
                    'profile_image' => get_image($driver->profile_image, 'driver', $merchant_id),
                ];
            } elseif ($request->calling_from == 'grocery') {
                $final_return_data = $return_data;
            }
            return $final_return_data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function driverTimeSlot($request)
    {
        $driver_id = $request->driver_id;
        $merchant_id = $request->merchant_id;
        $segment_id = $request->segment_id;
        $day = $request->day;
        $detail = ServiceTimeSlotDetail::select('from_time', 'to_time', 'id')
            ->whereHas('ServiceTimeSlot', function ($q) use ($day, $merchant_id, $segment_id) {
                $q->where('day', $day);
                $q->where('merchant_id', $merchant_id);
                $q->where('segment_id', $segment_id);
            })
            ->whereHas('Driver', function ($q) use ($driver_id) {
                $q->where('driver_id', $driver_id);
            })->get();
        if (!empty($detail) && $detail->count() > 0) {
            $from_time = substr_replace(min(array_pluck($detail, 'from_time')), "", -3);
            $to_time = substr_replace(max(array_pluck($detail, 'to_time')), "", -3);
        } else {
            $from_time = "";
            $to_time = "";
        }
        return $from_time . '-' . $to_time;
    }
}
