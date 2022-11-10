<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Helper\FindDriverController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\PaymentMethods\Payment;
use App\Models\BookingRating;
use App\Models\CancelReason;
use App\Models\HandymanCommission;
use App\Models\Segment;
use App\Models\SegmentPriceCard;
use App\Traits\BannerTrait;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Driver;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponseTrait;
use App\Models\HandymanOrder;
use App\Models\HandymanOrderDetail;
use App\Traits\HandymanTrait;
use App\Traits\AreaTrait;
use App\Models\ServiceTimeSlot;
use App\Models\LanguageHandymanChargeType;
use App\Models\Outstanding;

class PlumberController extends Controller
{
    use BannerTrait, ApiResponseTrait, HandymanTrait, AreaTrait, MerchantTrait;

    public function getPlumbers(Request $request)
    {
        // call area trait to get id of area

        $merchant_id = $request->merchant_id;
        $user = $request->user('api');
        $request->request->add(['user_id' => $user->id]);
        $validator = Validator::make($request->all(), [
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {
            }),],
            'latitude' => 'required',
            'longitude' => 'required',
            'pagination' => 'required',
            'selected_services' => 'required',
//            'page' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }


        try {

        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $this->getAreaByLatLong($request,$string_file);
        $price_card_owner_config = $user->Merchant->HandymanConfiguration->price_card_owner_config;
        $request->request->add(['price_card_owner'=>$price_card_owner_config]);
        $arr_plumbers = Driver::getNearestPlumbers($request);

        $arr_type = get_price_card_type('','BOTH',$string_file);
        $arr_type_slug = get_price_card_type('','BOTH',$string_file,true);
        $currency = $user->Country->isoCode;
        $arr_selected_services = array_column(json_decode($request->selected_services, true), 'service_type_id');
        $arr_plumber_data = [];
        $time_slot_object = new ServiceTimeSlot;

//       $segment_price_card = SegmentPrice
         $string_file = $this->getStringFile($merchant_id);
        foreach ($arr_plumbers as $item) {

            $rating = BookingRating::whereHas('HandymanOrder', function ($q) use ($item){
                $q->where('driver_id', $item->driver_id);
            })
                ->where('handyman_order_id','!=',NULL)
                ->avg('driver_rating_points');
            $rating = isset($rating) ? round($rating,1) : $rating;

            $arr_online_service = $item->ServiceTypeOnline->map(function ($inner_item) use ($arr_selected_services) {
                return array(
                    'service_type_id' => $inner_item->pivot->service_type_id,
                );
            });

            // price card of provider
            $hourly_amount = "0.0";
            $minimum_booking_amount = "0.0";
            $price_type_text="";
            $price_type_slug="";
            $segment_price_card_id=NULL;
            $services_charges = [];
            $price_type = NULL;
            $min_bill_description = "";
            if($price_card_owner_config == 2)
            {
                $handyman_commission = HandymanCommission::where([['merchant_id','=',$merchant_id],['country_area_id','=',$request->area],['segment_id','=',$request->segment_id]])->first();
                $tax = !empty($handyman_commission->id) ? $handyman_commission->tax : NULL;
                if(!empty($item->SegmentPriceCard))
                {
                    $price_type = $item->SegmentPriceCard->price_type;
                    $hourly_amount = !empty($item->SegmentPriceCard->amount) ? $item->SegmentPriceCard->amount : "";
                    $minimum_booking_amount = $item->SegmentPriceCard->minimum_booking_amount;
                    $price_type_text = isset($arr_type[$price_type]) ? $arr_type[$price_type] : "";
                    $price_type_slug = isset($arr_type_slug[$price_type]) ? $arr_type_slug[$price_type] : "";
                    $segment_price_card_id = $item->SegmentPriceCard->id;
                    if($price_type == 1)
                    {
                        $services_charges = $item->SegmentPriceCard->SegmentPriceCardDetail->toArray();
                        $services_charges = array_column($services_charges,NULL,'service_type_id');
                    }
                    if($tax > 0)
                    {
                        $minimum_booking_amount = $minimum_booking_amount + ($minimum_booking_amount * $tax)/100;
                        $min_bill_description = trans_choice("$string_file.min_bill_description",3,['AMOUNT'=>$currency.' '.$minimum_booking_amount,'TAX'=>$tax]);
                    }
                }
            }

            $arr_online_service = array_pluck($arr_online_service, 'service_type_id');
            if (count(array_intersect($arr_selected_services, $arr_online_service)) === count($arr_selected_services)) {
                $arr_service = [];

                foreach ($item->ServiceType as $inner_item) {
                    if (in_array($inner_item->id, $arr_online_service)) {
                        $price_card_detail_id = NULL;
                        $service_amount = 0;
                        if($price_type == 1 && $price_card_owner_config == 2)
                        {
                            $price_card_detail = isset($services_charges[$inner_item->id]) ? $services_charges[$inner_item->id] : [];
                            if(!empty($price_card_detail))
                            {
                                $price_card_detail_id = $price_card_detail['id'];
                                $service_amount = $price_card_detail['amount'];
                            }
                        }
                         elseif(!empty($inner_item->SegmentPriceCardDetail) && $price_card_owner_config == 1)
                        {
                            $price_card_detail_id = $inner_item->SegmentPriceCardDetail->id;
                            $service_amount = $inner_item->SegmentPriceCardDetail->amount;
                        }

                        $arr_service[] = array(
                            'id' => $inner_item->id,
                            'name' => !empty($inner_item->serviceName) ? $inner_item->serviceName : "",
                            'amount' => !empty($service_amount) ? $service_amount : 0,
                            'segment_price_card_detail_id' => $price_card_detail_id,
                        );
                    }
                }
                $distance = number_format($item->distance,2);
                $current_day = date("w"); //current day num
                $request->request->add(['day'=>$current_day,'driver_id'=>$item->id]);
                $time_slot = $time_slot_object->driverTimeSlot($request);

                $arr_plumber_data[] = array(
                    'id' => $item->id,
                    'first_name' => "$item->first_name",
                    'last_name'  => "$item->last_name",
                    'distance' =>trans("$string_file.away").' '.$distance.' '.trans("$string_file.km"),
                    'is_favourite' =>"$item->is_favourite",
                    'rating' =>"$rating",
                    'time_range' =>trans("$string_file.today").' '.$time_slot,//"$day $item->time_range",
                    'current_latitude' =>$item->latitude,
                    'current_longitude' =>$item->longitude,
                    'image' => get_image($item->profile_image,'driver',$merchant_id),
                    'segment_price_card_id'=>$segment_price_card_id,
                    'hourly_amount'=>$hourly_amount,
                    'minimum_booking_amount'=>$minimum_booking_amount,
                    'min_bill_description'=>$min_bill_description,
                    'price_type_text'=> $price_type_text,
                    'price_type_slug'=> $price_type_slug,
                    'service_type' => $arr_service,
                );
            }
        }

        $plumber_data = $arr_plumbers->toArray();
        $next_page_url = '';
        $total_pages = 0;
        $current_page = 0;
        if ($request->pagination == 1) {
            $next_page_url = isset($plumber_data['next_page_url']) && !empty($plumber_data['next_page_url']) ? $plumber_data['next_page_url'] : "";
            $total_pages = isset($plumber_data['last_page']) && !empty($plumber_data['last_page']) ? $plumber_data['last_page'] : 0;
            $current_page = isset($plumber_data['current_page']) && !empty($plumber_data['current_page']) ? $plumber_data['current_page'] : 0;
        }
          return response()->json(['result' => "1", 'message' => trans("$string_file.data_found"),'limit'=>6,'next_page_url' => $next_page_url, 'total_pages' =>$total_pages, 'current_page' =>$current_page,'data' => $arr_plumber_data]);
        }
        catch (\Exception $e)
        {
          return $this->failedResponse($e->getMessage());
        }
    }

    // get single plumber data
    public function getPlumber(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $request->request->add(['user_id' => $user->id]);
        $validator = Validator::make($request->all(), [
            'segment_id' => 'required',
            'id' => 'required',// driver id
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        $price_card_owner_config = $user->Merchant->HandymanConfiguration->price_card_owner_config;
        $request->request->add(['price_card_owner_config'=>$price_card_owner_config]);
        $plumber_data = Driver::GetPlumber($request);
        $plumber_service = $plumber_data;
        if (!empty($plumber_data)) {
            $ratings = BookingRating::whereHas('HandymanOrder', function ($q) use ($plumber_data) {
                $q->where('driver_id', $plumber_data->id);
            });
            $driver_rating = round($ratings->avg('user_rating_points'), 2);
            $driver_rating_count = $ratings->count();
            $ratings = $ratings->get()->map(function ($item, $key) {
                return array(
                    'id' => $item->id,
                    'user_rating_points' => $item->user_rating_points,
                    'user_comment' => $item->user_comment,
                    'created_at' => date('m/d/Y', strtotime($item->created_at))
                );
            });

            $arr_gallery_data = [];
            if(!empty($plumber_data->DriverGallery))
            {
                foreach($plumber_data->DriverGallery as $gallery)
                {
                    $arr_gallery_data[] = ['image_title'=>get_image($gallery->image_title, 'driver_gallery', $merchant_id)];
                }
            }
            $completed_job = $plumber_data->HandymanOrder->where('order_status',7)->count();

            $services_charges = [];
            $price_type = NULL;
            if($price_card_owner_config == 2)
            {
                if(!empty($plumber_data->SegmentPriceCard))
                {
                    $price_type = $plumber_data->SegmentPriceCard->price_type;
                    if($price_type == 1)
                    {
                        $services_charges = $plumber_data->SegmentPriceCard->SegmentPriceCardDetail->toArray();
                        $services_charges = array_column($services_charges,NULL,'service_type_id');
                    }
                }
            }
            $plumber_data = $plumber_data->toArray();
            $fav = $plumber_data['is_favourite'];
            if (!empty($plumber_service->ServiceType)) {
                $services_data = $plumber_service->ServiceType->map(function ($item, $key) use ($merchant_id,$services_charges,$price_type) {
                    $price_card_detail_id = NULL;
                    $service_amount = 0;
                    if($price_type == 1)
                    {
                        $price_card_detail = isset($services_charges[$item->id]) ? $services_charges[$item->id] : [];
                        if(!empty($price_card_detail))
                        {
                            $price_card_detail_id = $price_card_detail['id'];
                            $service_amount = $price_card_detail['amount'];
                        }
                    }
                    return array(
                        'id' => $item->id,
                        'name' => !empty($item->ServiceName($merchant_id)) ? $item->ServiceName($merchant_id) : $item->serviceName,
                        'amount' => $service_amount,
                        'sequence'=>$item->Merchant[0]['pivot']->sequence,
                        'segment_price_card_detail_id' => $price_card_detail_id,
                    );
                });
                $services_data = $services_data->toArray();
                array_multisort(array_column($services_data, 'sequence'), SORT_ASC, $services_data);
                $return_data = [
                    'id'=>$plumber_data['id'],
                    'first_name'=>$plumber_data['first_name'],
                    'last_name'=>$plumber_data['last_name'],
                    'is_favourite'=>$fav > 0 ? "$fav" : "",
                    'completed_jobs'=>$completed_job,
                    'currency'=>$user->Country->isoCode,
                    'total_reviews'=>$driver_rating_count,
                    'ratings'=>$driver_rating,
                    'driver_reviews'=>$ratings,
                    'created_at'=>date('M y', strtotime($plumber_data['created_at'])),
                    'profile_image'=>get_image($plumber_data['profile_image'], 'driver', $merchant_id),
                    'arr_services'=>$services_data,
                    'driver_gallery'=>$arr_gallery_data,
                    ];
            }
        } else {
            return $this->failedResponse(trans("$string_file.data_not_found"));
        }
        return $this->successResponse(trans("$string_file.success"),$return_data);
    }

    public function serviceTypes($arr_data, $calling_from = '',$merchant_id = NULL)
    {
        $arr_service = [];
        foreach ($arr_data as $data) {
            if ($calling_from == 'order-confirm') {
                $data = $data->ServiceType;
                $arr_service[] = array(
                    'id' => $data->id,
                    'name' => !empty($data->ServiceName($merchant_id)) ? $data->ServiceName($merchant_id) : $data->serviceName,
                );
            } else {
                $arr_service[] = array(
                    'id' => $data->id,
                    'name' => !empty($data->ServiceType->ServiceName($merchant_id)) ? $data->ServiceType->ServiceName($merchant_id) : $data->ServiceType->serviceName,
                    'amount' => isset($data->SegmentPriceCard->amount) ? $data->SegmentPriceCard->amount : 0,
                    'currency' => "",
                    'price_type' => isset($data->SegmentPriceCard->price_type) ? $data->SegmentPriceCard->price_type : NULL,
                    'segment_price_card_id' => isset($data->SegmentPriceCard->id) ? $data->SegmentPriceCard->id : NULL,
                );
            }
        }
        return $arr_service;
    }

    // get plumber services
    public function getPlumberServices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'segment_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $user = $request->user('api');
            $string_file = $this->getStringFile(NULL,$user->Merchant);
            $this->getAreaByLatLong($request, $string_file);
            $merchant_id = $user->merchant_id;
            $segment_id = $request->segment_id;
            $currency = $user->Country->isoCode;
            // in case of driver price card owner this price card is not correct
            $segment_pc = SegmentPriceCard::select('id','amount','price_type','minimum_booking_amount')
                ->where([['merchant_id','=',$merchant_id],['segment_id','=',$segment_id],['country_area_id','=',$request->area],['delete','=',NULL],['status','=',1]])
                ->first();
            if(!empty($segment_pc->id))
            {
                $handyman_commission = HandymanCommission::where([['merchant_id','=',$merchant_id],['country_area_id','=',$request->area],['segment_id','=',$segment_id]])->first();
                $minimum_booking_amount = $segment_pc->minimum_booking_amount;
                $tax = !empty($handyman_commission->id) ? $handyman_commission->tax : NULL;
                if($tax > 0)
                {
                    $minimum_booking_amount = $minimum_booking_amount + ($minimum_booking_amount * $tax)/100;
                }
                $request->request->add(['segment_group_id'=>2,'segment_price_card_id'=>$segment_pc->id]);
                $arr_price_type =  get_price_card_type("","BOTH",$string_file);
                $arr_price_type_slug =  get_price_card_type("","BOTH",$string_file, true);
                $plumber_services = $this->getSegmentServices($request);
                $return_data =[
                    'segment_price_id'=>$segment_pc->id,
                    'currency'=>$currency,
                    'price_type_text'=>isset($arr_price_type[$segment_pc->price_type]) ? $arr_price_type[$segment_pc->price_type] : "",
                    'price_type_slug'=>isset($arr_price_type_slug[$segment_pc->price_type]) ? $arr_price_type_slug[$segment_pc->price_type] : "",
                    'minimum_booking_amount'=>$minimum_booking_amount,
                    'min_bill_description'=>trans_choice("$string_file.min_bill_description",3,['AMOUNT'=>$currency.' '.$minimum_booking_amount,'TAX'=>$tax]),
                    'hourly_amount'=>$segment_pc->amount,
                    'arr_services'=> $plumber_services
                ];
                return $this->successResponse(trans("$string_file.data_found"),$return_data);
            }
            else
            {
                $message = trans($string_file.'.services_price_card');
                return $this->failedResponse($message);
            }
        }catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }
    }

    public function applyRemovePromoCode(Request $request)
    {
        $user = $request->user('api');
        $request_fields = [
            'segment_price_card_id' => ['required'],
            'service_details' => ['required'],
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {
            }),],
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            // call area trait to get id of area
//            $this->getAreaByLatLong($request);
            $string_file = $this->getStringFile(NULL,$user->Merchant);
            $promocode = NULL;
            $price_card = SegmentPriceCard::select('id','price_type','minimum_booking_amount','amount')
                ->Find($request->segment_price_card_id);
            $request->request->add(['price_card'=>$price_card]);
            if (isset($request->promo_code) && !empty($request->promo_code)) {
                $cart =  $this->getCartData($request);
                $request->request->add(['order_amount'=>$cart['total_amount']]);
                $common = new CommonController;
                $check_promo_code = $common->checkPromoCode($request, true);
                if(isset($check_promo_code['status']) && $check_promo_code['status'] == true) {
                    $promocode = $check_promo_code['promo_code'];
                }else{
                    return $check_promo_code;
                }
            }
            $return_cart = $this->getCartData($request,$promocode);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            return $this->failedResponse($message);
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.promo_code_applied"),$return_cart);
    }

    public function getCartData($request,$promo_code = null)
    {
        $arr_cart_service = json_decode($request->service_details,true);
        $total_cart_amount = 0;
        $total_quantity = 0;
        $tax_per = 0;
        if(isset($request->segment_id) && isset($request->area) && isset($request->merchant_id)){
            $handyman_commission = HandymanCommission::where([['merchant_id','=',$request->merchant_id],['country_area_id','=',$request->area],['segment_id','=',$request->segment_id]])->first();
            if(!empty($handyman_commission) && isset($handyman_commission->tax) && !empty($handyman_commission->tax)){
                $tax_per = $handyman_commission->tax;
            }
        }

        $price_card = $request->price_card;
        $price_type = $price_card->price_type;
        $price_card_details = $price_card->SegmentPriceCardDetail;
        foreach($arr_cart_service as $key => $service)
        {
           $quantity = NULL;
           $price = NULL;
            $service_price = NULL;
            $quantity = $service['quantity'];
           if($price_type == 1)
           {
               $segment_price_card_detail_id = $service['segment_price_card_detail_id'];
               $details = $price_card_details->toArray();
               $arr_price_card = array_column($details, NULL, 'id');
               $arr_price_card = isset($arr_price_card[$segment_price_card_detail_id]) ? $arr_price_card[$segment_price_card_detail_id] : [];
               $service_price = isset($arr_price_card['amount']) ? $arr_price_card['amount'] : 0;
               $price = $service_price * $quantity;
           }
            $total_cart_amount +=$price;
            $total_quantity +=$quantity;
            $arr_cart_service[$key]['price'] = $price; // price card amount of service
            $arr_cart_service[$key]['service_price'] = $service_price; // price card amount of service
        }
        $discount_amount = 0;
        if(!empty($promo_code->id))
        {
            $promo_details = $promo_code;
            // flat discount promo_code_value_type ==1
            $discount_amount = $promo_details->promo_code_value;
            if ($promo_details->promo_code_value_type == 2) {
                // percentage discount promo_code_value_type == 2
                $promoMaxAmount = $promo_details->promo_percentage_maximum_discount;
                $discount_amount = ($total_cart_amount * $discount_amount) / 100;
                $discount_amount = !empty($promoMaxAmount) && ($discount_amount > $promoMaxAmount) ? $promoMaxAmount : round($discount_amount,2);
            }
        }
        $tax_amount = ($total_cart_amount * $tax_per)/100;
        $service_cart = [
            'total_quantity'=>$total_quantity,
            'total_amount'=>$total_cart_amount,
            'discount_amount'=>$discount_amount,
            'tax' => $tax_amount,
            'tax_per' => $tax_per,
            'final_amount'=>($total_cart_amount - $discount_amount + $tax_amount),
        ];
        $service_cart['ordered_services'] = $arr_cart_service;
        return $service_cart;
    }

    public function checkHandymanBookingOutstanding($user_id)
    {
        $outstanding = Outstanding::where(['user_id' => $user_id, 'pay_status' => 0, 'reason' => 3])->first();
        // p($outstanding);
        if ($outstanding) {
            $booking = HandymanOrder::findOrFail($outstanding->handyman_order_id);
            $data['handyman_order_id'] = (string)$booking->id;
            $data['amount'] = (string)$outstanding->amount;
            $data['iso_code'] = $booking->CountryArea->Country->isoCode;
            $data['outstanding_id'] = $outstanding->id;
            $data['pay_later_payment'] = true;
            return $data;
        }
        return [];
    }

    public function confirmOrder(Request $request)
    {
        $user = $request->user('api');
        $request_fields = [
            'payment_method_id' => ['required', 'integer', Rule::exists('payment_methods', 'id')->where(function ($query) {
            }),],
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {
            }),],
            'service_time_slot_detail_id' => 'required|exists:service_time_slot_details,id',
            'minimum_booking_amount' => 'required',
            'segment_price_card_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'drop_location' => 'required',
            'booking_date' => 'required',
            'auto_assign' => 'required',
            'card_id' => 'required_if:payment_method_id,2',
            'driver_id' => 'required_if:auto_assign,==,2',
            'advance_payment_of_min_bill' => 'required',// its configuration
        ];
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $this->getAreaByLatLong($request,$string_file);
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
        // check previous outstanding
//        if (isset($config->user_outstanding_enable) && $config->user_outstanding_enable == 1) {
            // Check for previous booking outstanding.
            $result = $this->checkHandymanBookingOutstanding($user->id);
            if (!empty($result)) {
                return response()->json(['result' => "3", 'message' => 'success', 'data' => $result]);
            }
//        }

        $same_user = HandymanOrder::where([['service_time_slot_detail_id', '=', $request->service_time_slot_detail_id], ['is_order_completed', '!=', 1], ['merchant_id', '=', $user->merchant_id]])
            ->where('booking_date', $request->booking_date)->whereIn('order_status', [1, 4, 6, 7])->where([['user_id', '=', $user->id]])->count();
        if ($same_user > 0) {
            return $this->failedResponse(trans("$string_file.user_already_booked"));
        }
        $driver_id = NULL;
        $driver_ids = NULL;
        $arr_plumbers = [];
        if (!empty($request->driver_id) && $request->auto_assign != 1) {
            $other_user = HandymanOrder::where([['service_time_slot_detail_id', '=', $request->service_time_slot_detail_id], ['is_order_completed', '!=', 1], ['merchant_id', '=', $user->merchant_id]])
                ->where('booking_date', $request->booking_date)->where([['driver_id', '=', $request->driver_id]])->whereIn('order_status', [4, 6, 7])->count();
            // driver not available
            if ($other_user > 0) {
                return $this->failedResponse(trans("$string_file.slot_already_booked"));
            }
//            $arr_plumbers = Driver::where("id",$request->driver_id)->get();
            $driver_id = $request->driver_id;
            $arr_plumbers = Driver::whereIn("id",[$driver_id])->get();
            $driver = $arr_plumbers[0]; // single driver details
            $user_lat = $request->latitude;
            $user_long = $request->longitude;
            $address = $driver->WorkShopArea; // workshop area of driver
            $driver_radius = $address->radius;
            $driver_latitude = $address->latitude;
            $driver_longitude = $address->longitude;
            $unit = $user->Country->distance_unit;
            $unit_lang = ($unit == 2 ? trans("$string_file.miles") : trans("$string_file.km"));
            $google = new GoogleController;
            $distance_from_user = $google->arialDistance($user_lat, $user_long, $driver_latitude, $driver_longitude,$unit,$string_file,false);
            if(ceil($distance_from_user) > $driver_radius)
            {
                return $this->failedResponse(trans_choice("$string_file.provider_radius_warning",3,['RANGE'=>$driver_radius.$unit_lang]));
            }
        } else {
            $arr_plumbers = Driver::getNearestPlumbers($request);
            if ($arr_plumbers->count() == 0) {
                return $this->failedResponse(trans("$string_file.no_provider_available"));
            }
            $driver_ids = $arr_plumbers->pluck('id')->toArray();
        }
        DB::beginTransaction();
        try {
            $promocode = $request->promo_code;
            $promo_code_id = NULL;
            if (!empty($request->promo_code)) {
                $common = new CommonController;
                $check_promo_code = $common->checkPromoCode($request, true);
                if (isset($check_promo_code['status']) && $check_promo_code['status'] == true) {
                    $promocode = $check_promo_code['promo_code'];
                    $promo_code_id = $promocode->id;
                } else {
                    return $check_promo_code;
                }
            }
            //
            $arr_detail_ids = json_decode($request->service_details,true);
            $arr_detail_ids = array_column($arr_detail_ids,'segment_price_card_detail_id');
            $price_card = SegmentPriceCard::select('id','price_type','minimum_booking_amount','amount')
                ->with(['SegmentPriceCardDetail'=>function($q) use($arr_detail_ids) {
                  $q->whereIn('id',$arr_detail_ids);
                }])
                ->whereHas('SegmentPriceCardDetail',function($q) use($arr_detail_ids) {
                    $q->whereIn('id',$arr_detail_ids);
                })
                ->first();
            $request->request->add(['price_card'=>$price_card]);
            $return_cart = $this->getCartData($request, $promocode);

            $order = new HandymanOrder;
            $order->merchant_id = $request->merchant_id;
            $order->user_id = $user->id;
            // $order->driver_id = $request->driver_id;
            $order->segment_id = $request->segment_id;
            $order->order_status = 1; //order placed
            $order->country_area_id = $request->area;

            $cart_amount = $return_cart;
            $order->quantity = $cart_amount['total_quantity'];
            $order->card_id = $request->card_id;
            $order->tax_per = $cart_amount['tax_per'];
            $order->cart_amount = $cart_amount['total_amount'];  // service amount

            // Promo code apply
            $order->promo_code_id = $promo_code_id;

            $order->discount_amount = $cart_amount['discount_amount'];
            $order->total_booking_amount = $cart_amount['final_amount']; // final booking amount (service charges + tax)
            $tax_on_minimum_booking = ($price_card->minimum_booking_amount * $cart_amount['tax_per'])/100;
            $order->minimum_booking_amount = $price_card->minimum_booking_amount + $tax_on_minimum_booking;

            if($price_card->price_type == 1)
            {
                $mini_amount =  $order->minimum_booking_amount; // tax is already included
                if($mini_amount > $order->total_booking_amount)
                {
                    $order->final_amount_paid =  $mini_amount;
                    $order->tax = $tax_on_minimum_booking;
                }
                else
                {
                    $order->final_amount_paid =  $order->total_booking_amount;
                    $order->tax = $cart_amount['tax'];
                }
            }
            else
            {
                //in case of hourly
                $order->final_amount_paid =  $order->minimum_booking_amount;
                $order->tax = $tax_on_minimum_booking;
            }

            /*******Check user wallet if payment method is wallet *********/
            $final_amount = $order->final_amount_paid;
            if($request->payment_method_id == 3)
            {
                $common_controller = new CommonController;
                $common_controller->checkUserWallet($user,$final_amount);
            }

            $order->payment_method_id = $request->payment_method_id;
            $order->min_booking_payment_method_id = $request->payment_method_id;
            $order->advance_payment_of_min_bill = $request->advance_payment_of_min_bill;
            $order->booking_date = $request->booking_date;
            $order->segment_price_card_id = $price_card->id;
            $order->service_time_slot_detail_id = $request->service_time_slot_detail_id;
            $order->price_type = $price_card->price_type;
            $order->hourly_amount = $price_card->amount;

            $order->drop_latitude = $request->latitude;
            $order->drop_longitude = $request->longitude;
            $order->drop_location = !empty($request->drop_location) ? $request->drop_location : "";

            $order->additional_notes = $request->additional_notes;
            $order->booking_timestamp = time();
            $order->driver_id = $driver_id;

            $status_history[] = [
                'order_status'=>1,
                'order_timestamp'=>time(),
                'latitude'=>$request->latitude,
                'longitude'=>$request->longitude,
            ];

            $order->order_status_history = json_encode($status_history);
            $order->save();
            $arr_ordered_services = $return_cart['ordered_services'];
            foreach ($arr_ordered_services as $service) {
                $service_obj = new HandymanOrderDetail;
                $service_obj->handyman_order_id = $order->id;
                $service_obj->service_type_id = $service['service_type_id'];

                if ($price_card->price_type == 1) // it will  insert in case of fixed price type
                {
                    $service_obj->segment_price_card_detail_id = $service['segment_price_card_detail_id'];
                    $service_obj->quantity = $service['quantity'];
                    $service_obj->price = $service['service_price']; // service price
                }
                $service_obj->discount = 0;
                $service_obj->total_amount = $service['price']; // total charges of service
                $service_obj->save();
            }
            $data = [
                'order_id' => $order->id,
                'order_status' => $order->order_status
            ];
            // In case of wallet do payment first
            $advance_payment = $order->advance_payment_of_min_bill;

            //means advance payment will done payment method is not cash and configuration is enabled for it.
            if($advance_payment == 1 && $request->payment_method_id != 1)
            {
                // check wallet balance
//                if($request->payment_method_id == 3)
//                {
//                    $common_controller = new CommonController;
//                    $common_controller->checkUserWallet($user,$order->minimum_booking_amount);
//                }
                if($request->payment_status == 1) // set payment status if online payment is done before place order
                {
                    $order->minimum_booking_amount_payment_status = !empty($request->payment_status) ? $request->payment_status : 2; // if payment done
                }
                else
                {
                    $payment = new Payment();
                    $currency = $order->User->Country->isoCode;
                    $array_param = array(
                        'handyman_order_id' => $order->id,
                        'payment_method_id' => $order->payment_method_id,
                        'amount' => $order->minimum_booking_amount,
                        'user_id' => $order->user_id,
                        'card_id' => $order->card_id,
                        'currency' => $currency,
                        'quantity' => $order->quantity,
                        'order_name' => $order->merchant_order_id,
                        'payment_type' => "ADVANCE", // NORMAL
                    );
                    $payment_status =   $payment->MakePayment($array_param);
                    $order->minimum_booking_amount_payment_status = 1;
                }
                // save minimum booking payment status
                $order->save();
            }

            $arr_driver_id = !empty($driver_ids) ? $driver_ids : [$driver_id];
            $request->request->add(['notification_type' => 'NEW_ORDER']);
            $this->sendNotificationToProvider($request, $arr_driver_id, $order,$string_file);
            if (!empty($arr_driver_id)) {
                $findDriver = new FindDriverController();
                $findDriver->AssignRequest($arr_plumbers, null, null, $order->id);
            }

        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            DB::rollback();
            return $this->failedResponse($message);
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.handyman_order_placed"),$data);
    }

    public function getOrders(Request $request)
    {
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $user_id = $user->id;
        $currency = $user->Country->isoCode;
        $merchant_id = $request->merchant_id;
        $request_fields = [
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {
            }),],
            'type' => 'required|in:SCHEDULED,ONGOING,PAST'
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $arr_orders = HandymanOrder::select('id', 'handyman_orders.driver_id', 'handyman_orders.driver_id', 'payment_method_id', 'quantity', 'final_amount_paid', 'order_status', 'booking_date', 'service_time_slot_detail_id','segment_price_card_id','is_order_completed','price_type')
            ->with(['Driver' => function ($q) {
                $q->addSelect('id', 'first_name', 'last_name', 'profile_image','rating');

            }])
            ->with(['HandymanOrderDetail' => function ($q) {
                $q->addSelect('handyman_order_id', 'service_type_id', 'segment_price_card_id');

            }])
            ->with(['HandymanOrderDetail.ServiceType' => function ($q) {
                $q->addSelect('service_types.id', 'service_types.serviceName');

            }])
            ->with(['ServiceTimeSlotDetail' => function ($q) {
                $q->addSelect('id','from_time','to_time');
            }])
            ->with(['BookingRating' => function ($q) {
                $q->addSelect('handyman_order_id','driver_rating_points as rating');
            }])
            ->where([['segment_id', '=', $request->segment_id], ['user_id', '=', $user_id], ['merchant_id', '=', $merchant_id]])
            ->where(function ($q) use ($request) {
                if ($request->type == 'SCHEDULED') {
                    $order_status = [1, 4];
                    $q->whereIn('order_status', $order_status);
                } elseif ($request->type == 'ONGOING') {
                    $order_status = [6];
                    $q->whereIn('order_status', $order_status);
                    $q->orWhere(function($qq){
                        $qq->where('order_status', 7);
                        $qq->where('payment_status','!=', 1);
//                        $qq->where('is_order_completed','!=', 1);
                    });

                } elseif ($request->type == 'PAST') {
                    // $q->where([['is_order_completed', '=', 1],['order_status','=',7]]);
                    $order_status = [2, 5,3];
                    $q->whereIn('order_status', $order_status);
                    $q->orWhere(function($qq){
                        $qq->where('order_status', 7);
                        $qq->where('payment_status', 1);
//                        $qq->where('is_order_completed', 1);
                    });
                }
            })
            ->get();

        $time_format =  $user->Merchant->Configuration->time_format;
        $currency = $user->Country->isoCode;
        $req_param['merchant_id'] = $merchant_id;
        $arr_order_status = $this->getHandymanBookingStatus($req_param,$string_file);
        $arr_orders = $arr_orders->map(function ($item, $key)use ($merchant_id,$arr_order_status,$currency,$time_format,$string_file){
            $status = isset($arr_order_status[$item->order_status]) ? $arr_order_status[$item->order_status] : "";
            $time = "";
            if(isset($item->ServiceTimeSlotDetail))
             {
               $start = strtotime($item->ServiceTimeSlotDetail->from_time);
               $start = $time_format == 2  ? date("H:i",$start) : date("h:i a",$start);
               $end = strtotime($item->ServiceTimeSlotDetail->to_time);
               $end =  $time_format == 2  ? date("H:i",$end) : date("h:i a",$end);
               $time = $start."-".$end;
             }

            $final_amount_paid = $item->final_amount_paid;
            if($item->price_type == 2 && $item->order_status != 7 && $item->is_order_completed !=1)
            {
                $final_amount_paid =  $item->SegmentPriceCard->amount.' '.trans("$string_file.hourly");
            }

            return array(
                'order_id' => $item->id,
                'first_name' => isset($item->Driver->id) ? $item->Driver->first_name : "",
                'last_name' => isset($item->Driver->id) ? $item->Driver->last_name : "",
                'rating' => isset($item->Driver->rating) ? $item->Driver->rating :"",
                'profile_image' =>isset($item->Driver->id) ?get_image($item->Driver->profile_image,'driver',$merchant_id) : get_image(),
                'final_amount_paid' =>$final_amount_paid,
                'currency' =>$currency,
                'total_services' => $item->quantity,
                'order_status' => $status,
                'booking_date' => date('d M y',strtotime($item->booking_date)),
                'slot_time_text' =>$time,
                'service_type' => $this->serviceTypes($item->HandymanOrderDetail,'',$merchant_id),
            );
        });
        return $this->successResponse(trans("$string_file.data_found"), $arr_orders);
    }

    public function getOrderDetail(Request $request)
    {
        $user = $request->user('api');
        $currency = $user->Country->isoCode;
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $request_fields = [
            'order_id' => ['required', 'integer', Rule::exists('handyman_orders', 'id')->where(function ($query) {
            }),],
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $item = HandymanOrder::select('id','merchant_id','payment_status','segment_id','cart_amount','discount_amount','drop_location','drop_location', 'order_otp','handyman_orders.driver_id', 'payment_method_id', 'quantity', 'final_amount_paid','order_status','booking_date','service_time_slot_detail_id','minimum_booking_amount_payment_status','minimum_booking_amount','tax','price_type','segment_price_card_id','is_order_completed','total_booking_amount','extra_charges_details')
            ->with(['Driver' => function ($q) {
                $q->addSelect('id', 'first_name', 'last_name','profile_image');
            }])
            ->with(['HandymanOrderDetail' => function ($q) {
                $q->addSelect('handyman_order_id', 'service_type_id', 'segment_price_card_id');
            }])
            ->with(['HandymanOrderDetail.ServiceType' => function ($q) {
                $q->addSelect('service_types.id','service_types.serviceName');
            }])
            ->with(['ServiceTimeSlotDetail' => function ($q) {
                $q->addSelect('id','to_time','from_time');
            }])
            ->with('BookingRating')
//            ->leftJoin(DB::raw('(SELECT dsr.driver_id, CAST(AVG (dsr.rating) AS DECIMAL (2,1)) AS rating FROM `driver_segment_ratings` as dsr GROUP BY dsr.driver_id) dsr'), 'handyman_orders.driver_id', '=', 'dsr.driver_id')
            ->where('id', $request->order_id)
            ->first();

        $time = "";
        $time_format =  $user->Merchant->Configuration->time_format;
        $cancel_reasons = CancelReason::Reason($item->merchant_id, 1,$item->segment_id);
        $req_param['merchant_id'] = $merchant_id;
        $arr_order_status = $this->getHandymanBookingStatus($req_param,$string_file);
        $status = isset($arr_order_status[$item->order_status]) ? $arr_order_status[$item->order_status] : "";
        $user_outstanding_enable = $user->Merchant->Configuration->user_outstanding_enable;
        $is_rated = false;
        if (isset($item->BookingRating->user_rating_points) && $item->BookingRating->user_rating_points != '') {
            $is_rated = true;
        }
        if(isset($item->ServiceTimeSlotDetail))
        {
            $start = strtotime($item->ServiceTimeSlotDetail->from_time);
            $start = $time_format == 2  ? date("H:i",$start) : date("h:i a",$start);
            $end = strtotime($item->ServiceTimeSlotDetail->to_time);
            $end =  $time_format == 2  ? date("H:i",$end) : date("h:i a",$end);
            $time = $start."-".$end;
        }

        $total_payable_amount = $item->final_amount_paid;
        if($item->minimum_booking_amount > $item->final_amount_paid)
        {
            $total_payable_amount = $item->minimum_booking_amount;
        }
        $total_pending_amount = $total_payable_amount;
        if($item->minimum_booking_amount_payment_status == 1)
        {
            $total_pending_amount = $total_pending_amount - $item->minimum_booking_amount;
        }

//        $cart_amount = $item->cart_amount;
        $payment_message = "";
        $total_pending_status = false;
        if($item->price_type == 2 && $item->order_status != 7 && $item->is_order_completed !=1)
        {
            $cart_amount =  $item->SegmentPriceCard->amount.' '.trans("$string_file.hourly");
            $payment_message = trans("$string_file.handyman_order_payment");
        }
        else
        {
            $service_charges_included_tax = $item->minimum_booking_amount  > $item->total_booking_amount ? $item->minimum_booking_amount  : $item->total_booking_amount;
            $cart_amount =  $service_charges_included_tax - $item->tax + $item->discount_amount;
//            $service_tax = $order->tax;
//            $total_amount = $order->final_amount_paid;
        }
        if($total_pending_amount > 0 && $payment_message = "")
        {
            $total_pending_status = true;
        }

        $additional_charges = [];
        if(!empty($item->extra_charges_details))
        {
          $arr_details = json_decode($item->extra_charges_details,true);
          $locale = \App::getLocale();
          foreach ($arr_details as $key => $value) {

            $charge_type = LanguageHandymanChargeType::where([['handyman_charge_type_id','=',$value['id']],['merchant_id','=',$item->merchant_id],['locale','=',$locale]])->first();
            $additional_charges[] = [
                'key' => $charge_type->charge_type, 'value' => $currency . $value['amount'], 'color' => "757575", 'bold' => false
            ];

          }
        }

        // id outstanding is enabled and failed has failed then give option to user to create outstanding
        $pending_outstanding =  $user_outstanding_enable == 1 && !empty($item->PendingOutstanding->id)  ? true: false;
        $order = array(
            'order_id' => $item->id,
            'first_name' => isset($item->Driver->id) ? $item->Driver->first_name : "",
            'last_name' => isset($item->Driver->id) ? $item->Driver->last_name : "",
            'rating' => "$item->rating",
            'profile_image' => isset($item->Driver->id) ? get_image($item->Driver->profile_image, 'driver', $merchant_id) : get_image(),
            'drop_location' => $item->drop_location,
            'currency' => $currency,
            'total_services' => $item->quantity,
            'order_status' => $status,
            'status' => $item->order_status,
            'order_otp' => "$item->order_otp",
            'segment_name' => !empty($item->Segment->Name($merchant_id)) ? $item->Segment->Name($merchant_id) : $item->Segment->slag,
            'booking_date' => date('d M y', strtotime($item->booking_date)),
            'slot_time_text' => $time,
            'service_type' => $this->serviceTypes($item->HandymanOrderDetail,'',$merchant_id),
            'payment_detail' => [
                'cart_amount' => $cart_amount,
                'tax' => $item->tax,
                'final_amount_paid' => $item->final_amount_paid,
                'minimum_booking_amount' => $item->minimum_booking_amount,
                'minimum_booking_amount_payment_status' => $item->minimum_booking_amount_payment_status == 1 ? true : false,
                'total_pending_amount' => $total_pending_amount,
                'pending_amount_status' => $total_pending_status,
                'pending_message' => $payment_message,
                'paid_status' => $item->payment_status == 1 ? true : false,
                'payment_method_id' => $item->payment_method_id,
                'payment_mode' =>!empty($item->payment_method_id) ?  $item->PaymentMethod->payment_method : "",

                'discount_amount' => $item->discount_amount,
                //'equipment_amount' => 0.0,
                'additional_amount' => $additional_charges,
            ],
            'cancel_reason' => $cancel_reasons,
            'is_rated' => $is_rated,
            'arr_action'=>[
                'cancel'=>in_array($item->order_status,[1,4]) ? true : false,
                'pay'=> (($item->payment_status != 1  && $item->order_status == 7 && $total_pending_amount > 0) || $pending_outstanding) ? true : false,
                'create_outstanding'=> $pending_outstanding == 1 ? trans("$string_file.clear_outstanding") : "",
            ]
        );
        return $this->successResponse(trans("$string_file.data_found"), $order);
    }

    // order cancel
    public function cancelOrder(Request $request)
    {
        $request_fields = [
            'order_id' => ['required', 'integer', Rule::exists('handyman_orders', 'id')->where(function ($query) {
            }),
            ],
            'latitude' => 'required',
            'longitude' => 'required',
            'cancel_reason_id' => 'required'
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        DB::beginTransaction();
        try {

            $handyman = new HandymanOrder;
            $order = $handyman->getOrder($request);
            $status_history = json_decode($order->order_status_history, true);
            $order->order_status = 2;

            $new_status = [
                'order_status' => $order->order_status,
                'order_timestamp' => time(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ];
            array_push($status_history, $new_status);
            $order->order_status_history = json_encode($status_history);
            $order->cancel_reason_id = $request->cancel_reason_id;
            $order->save();

            $string_file = $this->getStringFile(NULL,$order->Merchant);
            $request->request->add(['notification_type' => "CANCEL_ORDER"]);

            /**send notification to user*/
//            $this->sendHandymanNotificationToUser($request,$order);

            if(!empty($order->driver_id))
            {
                $driver = $order->Driver;
                $driver->free_busy = 2; // driver is free now
                $driver->save();

                $arr_driver_id = [$order->driver_id];
                $this->sendNotificationToProvider($request, $arr_driver_id, $order,$string_file);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.success"),[]);
    }
}
