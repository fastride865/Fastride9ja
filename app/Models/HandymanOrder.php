<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandymanOrder extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->merchant_order_id = $model->NewBookigId($model->merchant_id);
            return $model;
        });
    }

    public function NewBookigId($merchantID)
    {
        $booking = HandymanOrder::where([['merchant_id', '=', $merchantID]])->orderBy('id','DESC')->first();
        if (!empty($booking)) {
            return $booking->merchant_order_id + 1;
        } else {
            return 1;
        }
    }

    public function HandymanOrderDetail()
    {
        return $this->hasMany(HandymanOrderDetail:: class);
    }

    public function PaymentMethod()
    {
        return $this->belongsTo(PaymentMethod:: class);
    }
    public function Driver()
    {
        return $this->belongsTo(Driver:: class);
    }
    public function User()
    {
        return $this->belongsTo(User:: class);
    }
    public function Segment()
    {
        return $this->belongsTo(Segment:: class);
    }
    public function CountryArea()
    {
        return $this->belongsTo(CountryArea:: class);
    }
    function ServiceTimeSlotDetail()
    {
        return $this->belongsTo(ServiceTimeSlotDetail::class);
    }

    public function SegmentPriceCard()
    {
        return $this->belongsTo(SegmentPriceCard:: class);
    }

    public function PendingOutstanding()
    {
        return $this->hasOne(Outstanding::class,'handyman_order_id')->where('pay_status',0);
    }

    function getSegmentOrders($request,$pagination = true)
    {

        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $merchant_id = get_merchant_id();
        $query = HandymanOrder::select('id','merchant_id','country_area_id','merchant_order_id','segment_id','segment_price_card_id','country_area_id','payment_method_id','user_id','driver_id','service_time_slot_detail_id','order_status','quantity','booking_date','drop_location','created_at','updated_at','cart_amount','price_type','total_service_hours','minimum_booking_amount','tax','total_booking_amount','final_amount_paid','tax_per','is_order_completed','drop_latitude','drop_longitude')
                    ->with(['HandymanOrderDetail'=>function($q) use($merchant_id){
                        $q->addSelect('id','handyman_order_id','service_type_id','segment_price_card_detail_id','quantity','price','discount','total_amount');
                    }])
                   ->with(['User'=>function($q)  {
                        $q->addSelect('id','first_name','last_name','UserPhone','email');
                    }])
                   ->with(['CountryArea'=>function($q) {
                        $q->addSelect('id','merchant_id','country_id','timezone');
                    }])
                  ->with(['ServiceTimeSlotDetail'=>function($q) {
                        $q->addSelect('id','to_time','from_time');
                    }])
                    ->where([['merchant_id','=',$merchant_id]])
                    ->orderBy('created_at', 'DESC');

                    if ($request->booking_id) {
                        $query->where('merchant_order_id', $request->booking_id);
                    }
                    if (!empty($request->segment_id)) {
                        $query->where('segment_id', $request->segment_id);
                    }
                    if ($request->order_status) {
                        $query->where('order_status', $request->order_status);
                    }
                    if ($request->rider) {
                        $keyword = $request->rider;
                        $query->WhereHas('User', function ($q) use ($keyword) {
                            $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")
                                ->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
                        });
                    }
                    if ($request->start) {
                        $start_date = date('Y-m-d',strtotime($request->start));
                        $end_date = date('Y-m-d ',strtotime($request->end));
                        $query->whereBetween('booking_date', [$start_date,$end_date]);
                    }
                    if ($request->date) {
                        $query->whereDate('booking_date', '=', $request->date);
                    }
                    if (!empty($request->status) && $request->status == "COMPLETED") {
                        $query->where('order_status', 7);
                    }
                    if ($request->driver) {
                        $keyword = $request->driver;
                        $query->WhereHas('Driver', function ($q) use ($keyword) {
                            $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")
                                ->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
                        });
                    }
                if (!empty($request->driver_id)) {
                    $query->where('driver_id', $request->driver_id);
                }

                if(!empty($permission_area_ids)){
                    $query->whereIn("country_area_id",$permission_area_ids);
                }

                if($pagination == true)
                {
                 $arr_orders = $query->paginate(25);
                }
                else
                {
                    $arr_orders = $query->get();
                }
                return $arr_orders;
    }

    // get order details
    function getOrder($request)
    {
        $order_id = $request->order_id;
        $order = HandymanOrder::
//        select('id','country_area_id','order_status_history','merchant_id','drop_latitude','drop_longitude','final_amount_paid','merchant_order_id','segment_id','country_area_id','payment_method_id','payment_method_id','user_id','driver_id','service_time_slot_detail_id','order_status','quantity','cart_amount','booking_date','drop_location','created_at','updated_at')->
           with(['HandymanOrderDetail'=>function($q){
                $q->addSelect('id','handyman_order_id','service_type_id','segment_price_card_detail_id','quantity','price','discount','total_amount');
                $q->with(['ServiceType'=>function($q){}]);
            }])
            ->with(['User'=>function($q) {
                $q->addSelect('id','first_name','last_name','UserPhone','email','UserProfileImage','country_id');
            }])
            ->with(['CountryArea'=>function($q) {
                $q->addSelect('id','merchant_id','country_id','timezone');
            }])
            ->with(['ServiceTimeSlotDetail'=>function($q) {
                $q->addSelect('id','from_time','to_time','slot_time_text');
            }])
            ->where([['id','=',$order_id]])->first();
        return $order;
    }

    public function BookingRating()
    {
        return $this->hasOne(BookingRating::class,'handyman_order_id');
    }

    function HandymanOrderTransaction()
    {
        return $this->hasOne(BookingTransaction::class,'handyman_order_id');
    }

    public function Merchant()
    {
        return $this->belongsTo(Merchant:: class);
    }

    public function DriverGallery()
    {
        return $this->hasMany(DriverGallery:: class,'handyman_order_id');
    }

    function bookingList($request)
    {
        $merchant_id = $request->merchant_id;
        $query = HandymanOrder::select('id','merchant_id','country_area_id','merchant_order_id','segment_id','segment_price_card_id','country_area_id','payment_method_id','payment_method_id','user_id','driver_id','service_time_slot_detail_id','order_status','quantity','booking_date','drop_location','created_at','updated_at','cart_amount','price_type','total_service_hours','minimum_booking_amount','tax','total_booking_amount','final_amount_paid','tax_per','is_order_completed','drop_latitude','drop_longitude')
//            ->with(['HandymanOrderDetail'=>function($q) use($merchant_id){
//                $q->addSelect('id','handyman_order_id','service_type_id','segment_price_card_detail_id','quantity','price','discount','total_amount');
//            }])
//            ->with(['User'=>function($q)  {
//                $q->addSelect('id','first_name','last_name','UserPhone','email');
//            }])
//            ->with(['CountryArea'=>function($q) {
//                $q->addSelect('id','merchant_id','country_id','timezone');
//            }])
            ->with(['ServiceTimeSlotDetail'=>function($q) {
                $q->addSelect('id','to_time','from_time');
            }])
            ->where([['merchant_id','=',$merchant_id]])
            ->orderBy('created_at', 'DESC');
            $query->where('order_status', 7);

//        if ($request->start_date) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $query->whereBetween('booking_date', [$start_date,$end_date]);
//        }
//        if ($request->date) {
//            $query->whereDate('booking_date', '=', $request->date);
//        }
        $arr_orders = $query->get();
        return $arr_orders;
    }
}
