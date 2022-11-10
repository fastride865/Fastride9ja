<?php

namespace App\Traits;

use App\Models\BookingConfiguration;
use App\Models\BookingCoordinate;
use App\Models\CancelReason;
use App\Models\FailBooking;
use App\Models\Merchant;
use Auth;
use App\Models\Booking;
use Illuminate\Http\Request;
use DB;

trait BookingTrait
{
    public function ActiveBooking($pagination = true, $type = 'MERCHANT'){
        $merchant_id = '';
        $taxi_company_id = '';
        if($type == 'TAXICOMPANY'){
            $taxi_company = get_taxicompany();
            $merchant_id = $taxi_company->merchant_id;
            $taxi_company_id = $taxi_company->id;
        }else if($type == 'MERCHANT'){
            $merchant = Auth::user('merchant')->load('CountryArea');
            $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
        }
        $query = Booking::where('merchant_id', $merchant_id);
        if($type == 'TAXICOMPANY'){
            $query->where('taxi_company_id', $taxi_company_id);
        }
        $query = $query->whereIn('booking_status', [1001,1002,1003,1004,1012])->latest();
        $activeBooking = $pagination == true ? $query->paginate(25) : $query;
        return $activeBooking;
    }

    public function ActiveBookingNow($pagination = true, $type = 'MERCHANT',$url_slug = NULL)
    {
        if ($type == 'CORPORATE'){
            $corporate = Auth::user('corporate');
            $merchant = $corporate->Merchant;
            $where = [['merchant_id', '=', $merchant->id],['corporate_id','=',$corporate->id], ['booking_type', '=', 1]];
        }else if($type == 'TAXICOMPANY'){
            $taxicompany = get_taxicompany();
            $merchant = $taxicompany->Merchant;
            $where = [['merchant_id', '=', $merchant->id],['taxi_company_id','=',$taxicompany->id], ['booking_type', '=', 1]];
        }else if ($type == 'HOTEL'){
            $hotel = get_hotel();
            $merchant = $hotel->Merchant;
            $where = [['merchant_id', '=', $merchant->id],['hotel_id','=',$hotel->id], ['booking_type', '=', 2]];
        }else{
            $merchant = Auth::user('merchant')->load('CountryArea');
            $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
            $where = [['merchant_id', '=', $merchant_id], ['taxi_company_id','=',NULL], ['booking_type', '=', 1]];
        }
        $query = Booking::where($where)->whereIn('booking_status', [1001, 1002, 1003, 1004]);
        if(!empty($url_slug)){
            $query->whereHas('Segment',function ($q) use($url_slug){
                $q->where('slag',$url_slug);
            });
        }
        $query = $query->latest();
        if (!empty($merchant->CountryArea->toArray())) {
            $area_ids = array_pluck($merchant->CountryArea, 'id');
            $query->whereIn('country_area_id', $area_ids);
        }
        $activeBooking = $pagination == true ? $query->paginate(25) : $query;
        return $activeBooking;
    }

    public function ActiveBookingLater($pagination = true,$type = 'MERCHANT',$url_slug = NULL)
    {
        if ($type == 'CORPORATE'){
            $corporate = Auth::user('corporate');
            $merchant = $corporate->Merchant;
            $where = [['merchant_id', '=', $merchant->id],['corporate_id','=',$corporate->id], ['booking_type', '=', 2]];
        }else if ($type == 'TAXICOMPANY'){
            $taxicompany = get_taxicompany();
            $merchant = $taxicompany->Merchant;
            $where = [['merchant_id', '=', $merchant->id],['taxi_company_id','=',$taxicompany->id], ['booking_type', '=', 2]];
        }else if ($type == 'HOTEL'){
            $hotel = get_hotel();
            $merchant = $hotel->Merchant;
            $where = [['merchant_id', '=', $merchant->id],['hotel_id','=',$hotel->id], ['booking_type', '=', 2]];
        }else{
            $merchant = Auth::user('merchant')->load('CountryArea');
            $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
            $where = [['merchant_id', '=', $merchant_id], ['booking_type', '=', 2]];
        }
        $query = Booking::where($where)->whereIn('booking_status', [1001, 1012, 1002, 1003, 1004]);
        if(!empty($url_slug)){
            $query->whereHas('Segment',function ($q) use($url_slug){
                $q->where('slag',$url_slug);
            });
        }
        $query = $query->latest();
        if (!empty($merchant->CountryArea->toArray())) {
            $area_ids = array_pluck($merchant->CountryArea, 'id');
            $query->whereIn('country_area_id', $area_ids);
        }
        $activeBooking = $pagination == true ? $query->paginate(25) : $query;
        return $activeBooking;
    }

    public function SearchForActiveRide($url_slug)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $query = $this->ActiveBookingNow(false,'MERCHANT',$url_slug);
        if (request()->booking_id) {
            $query->where('merchant_booking_id', request()->booking_id);
        }
        if (request()->booking_status) {
            $query->where('booking_status', request()->booking_status);
        }
        if (request()->rider) {
            $keyword = request()->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if (request()->driver) {
            $keyword = request()->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $bookings = $query->paginate(25);
        $later_bookings = $this->ActiveBookingLater(true,'MERCHANT',$url_slug);
        $cancelreasons = $this->CancelReason();
        $bookingConfig = BookingConfiguration::select('ride_otp')->where([['merchant_id', '=', $merchant_id]])->first();
        return view('merchant.booking.active', compact('bookingConfig', 'bookings', 'cancelreasons', 'later_bookings','url_slug'));
    }

    public function SearchForActiveLaterRide($url_slug)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $query = $this->ActiveBookingLater(false,'MERCHANT',$url_slug);
        if (request()->booking_id) {
            $query->where('merchant_booking_id', request()->booking_id);
        }
        if (request()->rider) {
            $keyword = request()->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if (request()->driver) {
            $keyword = request()->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $later_bookings = $query->paginate(25);
        $bookings = $this->ActiveBookingNow(true,'MERCHANT',$url_slug);
        $cancelreasons = $this->CancelReason();
        $bookingConfig = BookingConfiguration::select('ride_otp')->where([['merchant_id', '=', $merchant_id]])->first();
        return view('merchant.booking.active', compact('bookingConfig', 'bookings', 'cancelreasons', 'later_bookings','url_slug'));
    }

    public function CancelReason($type = 'MERCHANT')
    {
        if ($type == 'CORPORATE'){
            $corporate = Auth::user('corporate');
            $merchant_id = $corporate->merchant_id;
        }else if ($type == 'TAXICOMPANY'){
            $taxicompany = get_taxicompany();
            $merchant_id = $taxicompany->merchant_id;
        }else{
            $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        }
        $cancelreasons = CancelReason::where([['merchant_id', '=', $merchant_id], ['reason_type', '=', 3]])->get();
        return $cancelreasons;
    }

    public function bookings($pagination = true, $booking_status = [],$type = 'MERCHANT',$url_slug = NULL)
    {
        if ($type == 'CORPORATE'){
            $corporate = Auth::user('corporate');
            $merchant = $corporate->Merchant;
            $where = [['merchant_id', '=', $merchant->id],['corporate_id','=',$corporate->id]];
        }else if ($type == 'TAXICOMPANY'){
            $taxicompany = get_taxicompany();
            $merchant = $taxicompany->Merchant;
            $where = [['merchant_id', '=', $merchant->id],['taxi_company_id','=',$taxicompany->id]];
        }else if ($type == 'HOTEL'){
            $hotel = get_hotel();
            $merchant = $hotel->Merchant;
            $where = [['merchant_id', '=', $merchant->id],['hotel_id','=',$hotel->id]];
        }else{
            $merchant = Auth::user('merchant')->load('CountryArea');
            $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
            $where = [['merchant_id', '=', $merchant_id],['taxi_company_id', '=', NULL]];
        }
        $query = Booking::where($where)->whereIn('booking_status', $booking_status);
        if(!empty($url_slug)){
            $query->whereHas('Segment',function ($q) use($url_slug){
                $q->where('slag',$url_slug);
            });
        }
        $query = $query->latest();
        if (!empty($merchant->CountryArea->toArray())) {
            $area_ids = array_pluck($merchant->CountryArea, 'id');
            $query->whereIn('country_area_id', $area_ids);
        }
        $booking = $pagination == true ? $query->latest()->paginate(25) : $query;
        return $booking;
    }

    public function failsBookings($pagination = true,$type = 'MERCHANT')
    {
        if ($type == 'CORPORATE'){
            $corporate = Auth::user('corporate');
            $merchant = $corporate->Merchant;
            $where = [['merchant_id', '=', $merchant->id],['corporate_id','=',$corporate->id]];
        }else{
            $merchant = Auth::user('merchant')->load('CountryArea');
            $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
            $where = [['merchant_id', '=', $merchant_id]];
        }
        $query = FailBooking::where($where)->latest();
        if (!empty($merchant->CountryArea->toArray())) {
            $area_ids = array_pluck($merchant->CountryArea, 'id');
            $query->whereIn('country_area_id', $area_ids);
        }
        $booking = $pagination == true ? $query->paginate(25) : $query;
        return $booking;
    }

    public function autoCancelRide()
    {
        $bookings = $this->bookings(false, [1016]);
        return $bookings;
    }

    public function getAllTransaction($pagination = true,$type = 'MERCHANT')
    {
        if ($type == 'CORPORATE'){
            $corporate = Auth::user('corporate');
            $merchant = $corporate->Merchant;
            $where = [['merchant_id', '=', $merchant->id],['corporate_id','=',$corporate->id],['booking_closure', '=', 1]];
        }else{
            $merchant = Auth::user('merchant')->load('CountryArea');
            $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
            $where = [['merchant_id', '=', $merchant_id],['taxi_company_id','=',NULL],['booking_closure', '=', 1]];
        }
        $query = Booking::where($where)->latest();
        if (!empty($merchant->CountryArea->toArray())) {
            $area_ids = array_pluck($merchant->CountryArea, 'id');
            $query->whereIn('country_area_id', $area_ids);
        }
        $transactions = $pagination == true ? $query->paginate(25) : $query;
        return $transactions;
    }

    public function allBookings($pagination = true)
    {
        $merchant = Auth::user('merchant')->load('CountryArea');
        $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
        $query = Booking::where([['merchant_id', '=', $merchant_id]])->latest();
        if (!empty($merchant->CountryArea->toArray())) {
            $area_ids = array_pluck($merchant->CountryArea, 'id');
            $query->whereIn('country_area_id', $area_ids);
        }
        $result = $pagination == true ? $query->paginate(25) : $query;
        return $result;
    }

    public function updateBookingCoordinates($coordinate,$booking_id)
    {
        $driverlocation = array('latitude' => $coordinate['latitude'], 'longitude' => $coordinate['longitude'], 'accuracy' => $coordinate['accuracy'], 'timestamp' => time());
        $locationArray = [];
        $getLocation = BookingCoordinate::where([['booking_id', '=', $booking_id]])->first();
        if (!empty($getLocation)) {
            $locationArray = json_decode($getLocation->coordinates, true);
        }
        array_push($locationArray, $driverlocation);
        $locationArray = json_encode($locationArray);
        BookingCoordinate::updateOrCreate(
            ['booking_id' => $booking_id],
            [
                'coordinates' => $locationArray,
            ]
        );
    }

    public function makeBookingExpire($all_bookings){
        foreach ($all_bookings as $booking) {
            if (!empty($booking->driver_id)) {
                $timezone = $booking->Driver->CountryArea->timezone;
            } else {
                if (isset($booking->User->Country->CountryArea[0]->timezone)) {
                    $timezone = $booking->User->Country->CountryArea[0]->timezone;
                } else {
                    $timezone = 'Asia/Kolkata';
                }
            }
//            date_default_timezone_set($timezone);
            $date = date('Y-m-d');
            $currenct_time = date('H:i');
            $later_booking_date = set_date($booking->later_booking_date);
            if ($booking->booking_type == 1 && $date > $booking->updated_at) {
                Booking::where('id', '=', $booking->id)->update(['booking_status' => 1016]);
            }
            if ($booking->booking_type == 2 && ($date > $later_booking_date || ($date == $later_booking_date && $currenct_time > $booking->later_booking_time))) {
                Booking::where('id', '=', $booking->id)->update(['booking_status' => 1016]);
            }
        }
    }

    function saveBookingStatusHistory($request,$booking_obj,$booking_id = NULL)
    {
        if(!empty($booking_obj->id))
        {
          $booking = $booking_obj;
            if(isset($booking_obj->trip_way))
            {
                unset($booking_obj->trip_way);
            }
        }
        else
        {
            $booking = Booking::select('id','booking_status','booking_status_history')->Find($booking_id);
        }
        if(!empty($booking->id))
        {
            $new_status = [
                    'booking_status'=>$booking->booking_status,
                    'booking_timestamp'=>time(),
                    'latitude'=>$request->latitude,
                    'longitude'=>$request->longitude,
                ];
            if(in_array($booking->booking_status,[1001]))
            {
                $booking->booking_status_history = json_encode([$new_status]);
                $booking->save();
            }
            else
            {
                $status_history = !empty(json_decode($booking->booking_status_history, true)) ? json_decode($booking->booking_status_history, true) : [];
                array_push($status_history, $new_status);
                $booking->booking_status_history = json_encode($status_history);
                $booking->save();
            }
        }
    }
    public function getNotificationLargeIconForBooking($booking)
    {
        $large_icon = "";
        if (!empty($booking->Segment))
        {
            $merchant_id = $booking->merchant_id;
            $item = $booking->Segment;
            $large_icon = isset($item->Merchant[0]['pivot']->segment_icon) && !empty($item->Merchant[0]['pivot']->segment_icon) ? get_image($item->Merchant[0]['pivot']->segment_icon, 'segment', $merchant_id, true) :
                get_image($item->icon, 'segment_super_admin', NULL, false);
        }
        return $large_icon;
    }

    public function getBookingStatus($string_file)
    {
        return array(
        '1001' => trans("$string_file.new_ride"),//ride booked
        '1002' => trans("$string_file.accepted_by_driver"),//
        '1003' => trans("$string_file.arrived_at_pickup"),
        '1004' => trans("$string_file.ride_started"),
        '1005' => trans("$string_file.ride_completed"),
        '1006' => trans("$string_file.ride_cancelled_by_user"),
        '1007' => trans("$string_file.ride_cancelled_by_driver"),
        '1008' => trans("$string_file.ride_cancelled_by_admin"),
        '1012' => trans("$string_file.partial_accepted"),
        '1016' => trans("$string_file.auto_cancelled"),
        '1018' => trans("$string_file.expired_by_cron"),//'Expired by cron (rider later case)',
    );
    }

    public function getBookings($request,$pagination = true, $type = 'MERCHANT'){
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $url_slug = isset($request->url_slug) ? $request->url_slug : "";
        $merchant_id = '';
        $taxi_company_id = '';
        if($type == 'TAXICOMPANY'){
            $taxi_company = get_taxicompany();
            $merchant_id = $taxi_company->merchant_id;
            $taxi_company_id = $taxi_company->id;
        }else if($type == 'MERCHANT'){
            $merchant_id = get_merchant_id();
        }
        $query = Booking::where('merchant_id', $merchant_id)
        ->orderBy('created_at','DESC');
        if(!empty($url_slug)){
            $query->whereHas('Segment',function ($q) use($url_slug){
                $q->where('slag',$url_slug);
            });
        }
        if($type == 'TAXICOMPANY'){
            $query->where('taxi_company_id', $taxi_company_id);
        }
        // booking based on status
        if(!empty($request->request_from) && $request->request_from == "ACTIVE")
        {
            $query->where(function($q){
                $q->whereIn('booking_status', [1001,1002,1003,1004,1012]);
                $q->orWhere([['booking_status','=',1005],['booking_closure','=',NULL]]);
            });
        }
        if(!empty($request->request_from) && $request->request_from == "CANCEL")
        {
        $query = $query->whereIn('booking_status', [1006, 1007, 1008]);
        }
        if(!empty($request->request_from) && $request->request_from == "AUTO_CANCEL")
        {
        $query = $query->whereIn('booking_status', [1016,1018]);
        }
        elseif(!empty($request->request_from) && $request->request_from == "COMPLETE")
        {
            $query = $query->where(function($q) use($request){
                $q->where([['booking_status','=',1005],['booking_closure','=',1]]);
            });
        }
        // search params and conditions
        if (!empty($request->booking_type) && $request->booking_type) {
            $query->where('booking_type', $request->booking_type);
        }
        if (!empty($request->booking_status) && $request->booking_status) {
            $query->where('booking_status', $request->booking_status);
        }
        if (!empty($request->booking_id) && $request->booking_id) {
            $query->where('merchant_booking_id', $request->booking_id);
        }
        if (!empty($request->segment_id)) {
            $query->where('segment_id', $request->segment_id);
        }
        if (!empty($request->driver_id)) {
            $query->where('driver_id', $request->driver_id);
        }
        if (!empty($request->rider) && $request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")
                    ->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if (!empty($request->driver) && $request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")
                    ->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        if ($request->start) {
            $start_date = date('Y-m-d',strtotime($request->start));
            $end_date = date('Y-m-d ',strtotime($request->end));
            $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
        }
        if(!empty($permission_area_ids)){
            $query->whereIn("country_area_id",$permission_area_ids);
        }
        $bookings = $pagination == true ? $query->paginate(25) : $query->get();

        return $bookings;
    }

}
