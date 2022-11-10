<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Helper\CommonController;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use DateTime;
use App\Models\BusinessSegment\Order;
use \App\Models\Segment;
use DateTimeZone;

class Driver extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $hidden = ['pivot'];

    protected $guarded = [];
    // socket true means get drivers lat long from node+mongodb
    public static $socket = true;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->merchant_driver_id = $model->NewDriverId($model->merchant_id);
            return $model;
        });
    }

    public function NewDriverId($merchantID)
    {
        $driver = Driver::where([['merchant_id', '=', $merchantID]])->latest()->first();
        if (!empty($driver)) {
            return $driver->merchant_driver_id + 1;
        } else {
            return 1;
        }
    }

    public function OutStandings()
    {
        return $this->hasMany(OutStanding:: class);
    }

    public function ActiveAddress()
    {
        return $this->hasOne(DriverAddress::class)->where('address_status', 1);
    }

    public function DriverVehicle()
    {
        return $this->belongsToMany(DriverVehicle::class)->wherePivot('vehicle_active_status', 1);
    }

    public function ManualDowngradedVehicleTypes()
    {
        return $this->belongsToMany(VehicleType::class, 'driver_downgraded_vehicle_types');
    }

    public function Franchisee()
    {
        return $this->belongsToMany(Franchisee::class);
    }

    public function BookingRequestDriver()
    {
        return $this->hasMany(BookingRequestDriver::class);
    }

    public function getfullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function DriverAccount()
    {
        return $this->hasMany(DriverAccount::class);
    }

    public function DriverDocument()
    {
        return $this->hasMany(DriverDocument::class);
    }

    public function Booking()
    {
        return $this->hasMany(Booking::class);
    }

    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }

    public function DriverVehicles()
    {
        return $this->hasMany(DriverVehicle::class);
    }

    public function FirstVehicle()
    {
        return $this->hasOne(DriverVehicle::class, 'owner_id');
    }

    public static function UpdateDetails($id, $data)
    {
        Driver::where([['id' => $id]])->update($data);
    }

    public function DriverRideConfig()
    {
        return $this->hasOne(DriverRideConfig::class);
    }

    public function DriverCurrentActivePack()
    {
        return $this->hasOne(DriverActivePack::class);
    }

    public function DriverSubscriptionRecord()
    {
        return $this->hasMany(DriverSubscriptionRecord::class);
    }

    public function DriverWalletTransaction()
    {
        return $this->hasMany(DriverWalletTransaction::class);
    }

    public function DriverCard()
    {
        return $this->hasMany(DriverCard::class);
    }

    public function AccountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function ReferralDriverDiscount()
    {
        return $this->hasMany(ReferralDriverDiscount::class);
    }

    public function GeofenceAreaQueue()
    {
        return $this->hasMany(GeofenceAreaQueue::class);
    }

    public function Segment()
    {
        return $this->belongsToMany(Segment::class);
    }

    function ServiceTimeSlotDetail()
    {
        return $this->belongsToMany(ServiceTimeSlotDetail::class, 'driver_service_time_slot_detail', 'driver_id')->withPivot('segment_id');
    }

    public function DriverSegmentRating()
    {
        return $this->hasMany(DriverSegmentRating:: class);
    }

    public function ServiceType()
    {
        return $this->belongsToMany(ServiceType:: class, 'driver_service_type', 'driver_id')->withPivot('segment_id');
    }

    public function ServiceTypeOnline()
    {
        return $this->belongsToMany(ServiceType:: class, 'driver_online', 'driver_id')->withPivot('segment_id', 'driver_vehicle_id');
    }

    public function DriverGallery()
    {
        return $this->hasMany(DriverGallery:: class);
    }

    public function SegmentPriceCard()
    {
        return $this->hasOne(SegmentPriceCard:: class);
    }

    public function Country()
    {
        return $this->belongsTo(Country::class);
    }

    public function DriverSegmentDocument()
    {
        return $this->hasMany(DriverSegmentDocument::class, 'driver_id');
    }

    public function Order()
    {
        return $this->hasMany(Order::class);
    }

    public function HandymanOrder()
    {
        return $this->hasMany(HandymanOrder::class);
    }

    public function Vehicle()
    {
        return $this->hasMany(DriverVehicle::class);
    }

    public function WorkShopArea()
    {
        return $this->hasOne(DriverAddress::class)->where('address_status', 1)->where('address_type',1);
    }

    public function findForPassport($user_cred = null)
    {
        if (!empty($_SERVER['HTTP_PUBLICKEY']) && !empty($_SERVER['HTTP_SECRETKEY'])) {
            $merchant = CommonController::MerchantObj($_SERVER['HTTP_PUBLICKEY'], $_SERVER['HTTP_SECRETKEY']);
            $driver_login = $merchant->ApplicationConfiguration->driver_login;
            $merchant_id = $merchant['id'];
        }
        if ($driver_login == "EMAIL") {
            return Driver::where([['merchant_id', '=', $merchant_id], ['email', '=', $user_cred], ['driver_admin_status', '=', 1]])->latest()->first();
        } else {
            return Driver::where([['merchant_id', '=', $merchant_id], ['phoneNumber', '=', $user_cred], ['driver_admin_status', '=', 1]])->latest()->first();
        }
    }

    public function GenrateReferCode()
    {
        $code = getRandomCode();
        if ($this->CheckReferCode($code)) {
            return $this->GenrateReferCode();
        }
        return $code;
    }

    public function CheckReferCode($referCode)
    {
        return static::where([['driver_referralcode', '=', $referCode]])->exists();
    }

    public static function Logout($player, $merchant_id)
    {
        Driver::where([['merchant_id', '=', $merchant_id], ['player_id', '=', $player]])->update(['online_offline' => 2, 'login_logout' => 2]);
    }
    // common function to get driver
//    public static function GetNearestDriver($arr_reqest)
//    {
//        // query parameters
//        $limit = isset($arr_reqest['limit']) ? $arr_reqest['limit'] :10;
//        $area = isset($arr_reqest['area']) ? $arr_reqest['area'] :null;
//        $distance_unit = isset($arr_reqest['distance_unit']) ? $arr_reqest['distance_unit'] :'';
////        $driver_request_time_out = isset($arr_reqest['driver_request_time_out']) ? $arr_reqest['driver_request_time_out'] :null;
//        $service_type_id = isset($arr_reqest['service_type']) ? $arr_reqest['service_type'] :null;
//        $vehicle_type_id = isset($arr_reqest['vehicle_type']) ? $arr_reqest['vehicle_type'] :null;
//        $longitude = isset($arr_reqest['longitude']) ? $arr_reqest['longitude'] :null;
//        $latitude = isset($arr_reqest['latitude']) ? $arr_reqest['latitude'] :null;
//        $ac_nonac = isset($arr_reqest['ac_nonac']) ? $arr_reqest['ac_nonac'] :null;
//        $user_gender = isset($arr_reqest['user_gender']) ? $arr_reqest['user_gender'] :null;
//        $driver_ids = isset($arr_reqest['driver_ids']) ? $arr_reqest['driver_ids'] :[];
//        $baby_seat = isset($arr_reqest['baby_seat']) ? $arr_reqest['baby_seat'] :null;
//        $wheel_chair = isset($arr_reqest['wheel_chair']) ? $arr_reqest['wheel_chair'] :null;
//        $riders_num = isset($arr_reqest['riders_num']) ? $arr_reqest['riders_num'] :1;
//        $distance = isset($arr_reqest['distance']) && !empty($arr_reqest['distance']) ? $arr_reqest['distance'] :10;
//        $vehicleTypeRank = isset($arr_reqest['vehicleTypeRank']) ? $arr_reqest['vehicleTypeRank'] :null; // check higher rank vehicle type in case of auto upgrade
//        $radius = $distance_unit == 2 ? 3958.756 : 6367;
//        $select = isset($arr_reqest['select']) ? $arr_reqest['select'] : '*';
//        $type = isset($arr_reqest['type']) ? $arr_reqest['type'] : null;  //for admin driver map
//        $taxi_company_id = isset($arr_reqest['taxi_company_id']) ? $arr_reqest['taxi_company_id'] : null;
//        $isManual = isset($arr_reqest['isManual']) ? $arr_reqest['isManual'] : null;
//        $drop_lat = isset($arr_reqest['drop_lat']) ? $arr_reqest['drop_lat'] : null;
//        $drop_long = isset($arr_reqest['drop_long']) ? $arr_reqest['drop_long'] : null;
//        $bookingId = isset($arr_reqest['booking_id']) ? $arr_reqest['booking_id'] : null;
//        $segment_id = isset($arr_reqest['segment_id']) ? $arr_reqest['segment_id'] : null;
//        $merchant_id = isset($arr_reqest['merchant_id']) ? $arr_reqest['merchant_id'] : null;
//        $payment_method_id = isset($arr_reqest['payment_method_id']) ? $arr_reqest['payment_method_id'] : null;
//        $booking_amount = isset($arr_reqest['estimate_bill']) ? $arr_reqest['estimate_bill'] : null;
//        $base_areas = null;
//
//        $queue_system = false;
//        $queue_drivers = [];
//
//        try {
//            if(!empty($longitude) && !empty($latitude))
//            {
//                // cash earning + new booking <= cash limit
//                $merchantData = CountryArea::find($area);
//                $date_obj = new DateTime(date('Y-m-d'));
//                $date_obj->setTimezone(new DateTimeZone($merchantData->timezone));
//                $booking_date = $date_obj->format('Y-m-d');
//                $cash_limit = $merchantData->driver_cash_limit_amount;
//                $remaining_amount = $cash_limit - $booking_amount;
//                $merchant = Merchant::with('Configuration','DriverConfiguration','ApplicationConfiguration')
//                    ->where([['id', '=', $merchantData->merchant_id]])->first();
//
//                if(isset($merchantData->is_geofence) && $merchantData->is_geofence == 1){
//                    $base_areas = isset($merchantData->RestrictedArea->base_areas) ? explode(',',$merchantData->RestrictedArea->base_areas) : '';
//                    if(isset($merchantData->RestrictedArea) && $merchantData->RestrictedArea->queue_system == 1){
//                        $queue_system = true;
//                        $queue_drivers = GeofenceAreaQueue::where([
//                            ['merchant_id', '=', $merchant->id],
//                            ['geofence_area_id','=',$merchantData->id],
//                            ['queue_status', '=', 1], /// Check for entry queue
//                            ['exit_time', '=', null]
//                        ])->where(function($query) use ($base_areas){
//                            if(!empty($base_areas)){
//                                $query->whereIn('country_area_id',$base_areas);
//                            }
//                        })->whereDate('entry_time',date('Y-m-d'))->orderBy('queue_no')->pluck('driver_id')->toArray();
//                        $driver_ids = $queue_drivers;
//                    }
//                }
//                $merchantGender = isset($merchant->ApplicationConfiguration->gender) ? $merchant->ApplicationConfiguration->gender : NULL;
//                $area_notifi = isset($merchant->Configuration->driver_area_notification) ? $merchant->Configuration->driver_area_notification : null;
//                $minute = isset($merchant->DriverConfiguration->inactive_time) ? $merchant->DriverConfiguration->inactive_time : null;
//                $location_updated_last_time = '';
//                date_default_timezone_set($merchantData->timezone);
//                if($minute > 0)
//                {
//                    $date = new DateTime;
//                    $date->modify("-$minute minutes");
//                    $location_updated_last_time = $date->format('Y-m-d H:i:s');
//                }
//
//                $drivers = [];
//                // socket calling
//                if($merchant->Configuration->lat_long_storing_at == 2)
//                {
//                    // send distance in meter
//                    $arr = ['area'=>$area,'limit'=>$limit,'distance_unit'=>$distance_unit, 'service_type_id'=>$service_type_id,'vehicle_type_id'=>$vehicle_type_id,
//                        'longitude'=>$longitude,'latitude'=>$latitude, 'ac_nonac'=>$ac_nonac,'user_gender'=>$user_gender,
//                        'driver_ids'=>$driver_ids,'baby_seat'=>$baby_seat, 'wheel_chair'=>$wheel_chair,'riders_num'=>$riders_num,
//                        'distance'=> $distance_unit == 2 ? ($distance * 1609.34): ($distance * 1000) ,'vehicleTypeRank'=>$vehicleTypeRank, 'radius'=>$radius,'select'=>$select,
//                        'taxi_company_id'=>$taxi_company_id,'isManual'=>$isManual, 'drop_lat'=>$drop_lat,'drop_long'=>$drop_long,'bookingId'=>$bookingId,'base_areas'=>$base_areas,
//                        'location_updated_last_time'=>$location_updated_last_time,'area_notifi'=>$area_notifi,'minute'=>$minute,'merchant_id'=>$merchant->id,'segment_id'=>$segment_id,
//                    ];
//                    $drivers = Driver::GetNearestDriverFromNode($arr);
//                }
//                else
//                {
//                    $query = Driver::select("drivers.*")
//                        ->addSelect(DB::raw('( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( current_latitude ) ) * cos( radians( current_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( current_latitude ) ) ) ) AS distance,drivers.id AS driver_id'))
//                        ->join('driver_online as do', 'drivers.id', '=', 'do.driver_id')
//                        ->join('driver_vehicles as dv', 'do.driver_vehicle_id', '=', 'dv.id');
//
//                    if($taxi_company_id != null || $isManual == true){
//                        $query->where('drivers.taxi_company_id',$taxi_company_id);
//                    }
//                    if($service_type_id == 5)
//                    {
//                        // pool service type
//                        $query->where([['drivers.status_for_pool', '!=', 2008], ['drivers.pool_ride_active', '=', 1], ['drivers.avail_seats', '>=', $riders_num]]);
//                        $query->whereIn('drivers.free_busy',[1,2]);
//                    }
//                    else{
//                        $query->where('do.service_type_id',$service_type_id);
//                        $query->where('do.segment_id',$segment_id);
////                $query->where([['drivers.free_busy', '=', 2]]);
//                    }
//
//                    if(!empty($vehicleTypeRank))
//                    {
//                        $query->join('driver_ride_configs as dvc', 'drivers.id', '=', 'dvc.driver_id');
//                        $query->join('vehicle_types as vt', 'dv.vehicle_type_id', '=', 'vt.id');
//                        $query->where([['vt.vehicleTypeRank', '<=', $vehicleTypeRank], ['dvc.auto_upgradetion', '=', 1]]);
//                        $vehicle_type_id = null;
//                    }
//                    $query->where([
//                        ['drivers.free_busy', '=', 2],
//                        ['drivers.player_id', "!=", NULL],
//                        ['login_logout', '=', 1],
//                        ['drivers.online_offline', '=', 1],
//                        ['dv.vehicle_verification_status', '=', 2], // only verified vehicle
//                        ['drivers.driver_delete', '=', NULL]
//                    ])
////                ->where(function($q) use($location_updated_last_time){
////                    $q->where('last_location_update_time', '>=', $location_updated_last_time);
////                })
//                        ->whereRaw('( is_suspended is NULL or is_suspended < ? )' , [date('Y-m-d H:i:s')])
//                        ->where(function($q) use ($area_notifi, $area, $base_areas){
//                            if($area_notifi == 2)
//                            {
//                                if(!empty($base_areas)){
//                                    array_push($base_areas,$area);
//                                    $q->whereIn('drivers.country_area_id', $base_areas);
//                                }else{
//                                    $q->where('drivers.country_area_id', $area);
//                                }
//                            }
//                        })
//                        ->where(function($q) use ($vehicle_type_id){
//                            if(!empty($vehicle_type_id))
//                            {
//                                $q->where('dv.vehicle_type_id',$vehicle_type_id);
//                            }
//                        })
//                        ->where(function ($query) use ($ac_nonac) {
//                            if ($ac_nonac == 1) {
//                                return $query->where('dv.ac_nonac', $ac_nonac);
//                            }
//                        })
//                        ->where(function ($query) use ($user_gender,$merchantGender) {
//                            if (!empty($user_gender) && $merchantGender == 1) {
//                                return $query->where('drivers.driver_gender', $user_gender);
//                            }
//                        })
//                        ->where(function ($query) use ($wheel_chair) {
//                            if ($wheel_chair == 1) {
//                                return $query->where('dv.wheel_chair', $wheel_chair);
//                            }
//                        })
//                        ->where(function ($query) use ($driver_ids) {
//                            if (!empty($driver_ids))
//                            {
//                                return $query->whereIn('drivers.id', $driver_ids);
//                            }
//                        })
//                        ->where(function ($query) use ($baby_seat) {
//                            if ($baby_seat == 1) {
//                                return $query->where('dv.baby_seat', $baby_seat);
//                            }
//                        })
//                        // commenting for development purpose
//                        ->whereNOTIn('drivers.id', function ($query) use($bookingId){
//                            $query->select('brd.driver_id')
//                                ->from('booking_request_drivers as brd')
//                                ->join('bookings as b', 'brd.booking_id', '=', 'b.id')
//                                ->where('b.booking_status', '=', 1001)
//                                ->where(function ($p) use($bookingId){
//                                    if(!empty($bookingId))
//                                    {
//                                        $p->where([['brd.booking_id',$bookingId],['brd.request_status',3]])
//                                            ->orWhere([['brd.request_status',1]]);
//                                    }
//                                    else
//                                    {
//                                        $p->where([['brd.request_status',1]]);
//                                    }
//                                });
//                        })
//                        ->whereExists(function ($query) {
//                            $query->select("ddv.driver_id")
//                                ->from('driver_driver_vehicle as ddv')
//                                ->join('driver_vehicles as dv', 'ddv.driver_vehicle_id', '=', 'dv.id')
//                                ->where('ddv.vehicle_active_status', 1)
//                                ->whereRaw('ddv.driver_id = drivers.id');
//                        })
//                        ->having('distance', '<', $distance)
//                        ->orderBy('distance')
//                        ->take($limit);
////                if($payment_method_id == 1 && $merchant->Configuration->driver_cash_limit == 1)
////                {
////                    $query->whereNOTIn('drivers.id', function ($query1) use($booking_date,$remaining_amount){
////                        $query1->select('b.driver_id')
////                            ->from('bookings as b')
////                    ->whereIn('b.driver_id',function($query2) use($booking_date,$remaining_amount){
////                        $query2->select('b.driver_id',DB::raw('sum("final_amount_paid") as cash_amount'))
////                            ->from('bookings as b')
////                            ->where('b.booking_status', '=', 1005)
////                            ->where('b.payment_method_id', '=', 1)
////                            ->where('b.payment_status', '=', 1)
////                            ->whereDate('b.created_at', $booking_date)
////                            ->groupBy('b.driver_id')
////                            ->having('cash_amount','>=',$remaining_amount);
////                         });
////                    });
////                }
//                    $drivers = $query->get();
//
////                    if($payment_method_id == 1 && $merchant->Configuration->driver_cash_limit == 1 && $drivers->count () > 0)
////                    {
////                        $all_drivers = array_pluck($drivers,'id');
////                        $under_cash_limit_drivers  = Booking::select('b.driver_id',DB::raw('sum("final_amount_paid") as cash_amount'))
////                            ->from('bookings as b')
////                            ->where('b.booking_status', '=', 1005)
////                            ->where('b.payment_method_id', '=', 1)
////                            ->where('b.payment_status', '=', 1)
////                            ->whereDate('b.created_at', $booking_date)
////                            ->having('cash_amount','<=',$remaining_amount)
////                            ->groupBy('b.driver_id')
////                            ->whereIn('b.driver_id',$all_drivers)
////                            ->get();
//////                    p($under_cash_limit_drivers);
////
////                        // if drivers are available  with under cash limit then return that drivers
////                        // otherwise return all drivers with warning to change payment method
////                        if($under_cash_limit_drivers->count() > 0)
////                        {
////                            $under_cash_limit_drivers_id = array_pluck($under_cash_limit_drivers);
////                            $drivers = $drivers->whereIn('id',$under_cash_limit_drivers_id);
////                            $drivers = collect($drivers->values());
////                        }
////                        else
////                        {
////                            throw  new \Exception(trans("$string_file.no_driver_available_with_cash"));
////                        }
////                    }
//                }
//                if($queue_system){
//                    if(count($queue_drivers) > 0 && count($drivers) > 0 && $drop_lat != '' && $drop_long != ''){
//                        $i = 0;
//                        $found_driver = [];
//                        foreach($drivers as $driver){
//                            if($driver->driver_id == $queue_drivers[$i]){
//                                array_push($found_driver, $driver);
//                                break;
//                            }
//                        }
//                        return $found_driver;
//                    }else{
//                        return [];
//                    }
//                }
//            }else{
//                if (!empty($type)){
//                    $merchant = get_merchant_id(false);
//                    $select = ['id', 'first_name', 'last_name', 'email', 'phoneNumber', 'profile_image', 'current_latitude', 'current_longitude', 'online_offline', 'free_busy'];
//                    $query = Driver::select($select)->where([
//                        ['merchant_id', '=', $merchant->id],
//                        ['current_latitude', '!=', null],
//                        ['login_logout', '=', 1],
//                        ['driver_delete', '=', null]
//                    ]);
//                    if ($type == 2) {
//                        $query->where([['online_offline', '=', 1], ['free_busy', '=', 2]]);
//                    }elseif ($type == 3){
//                        $query->where([['free_busy', '=', 1]])->whereHas('Booking', function ($query) {
//                            $query->where([['booking_status', '=', 1002]]);
//                        });
//                    }elseif ($type == 4){
//                        $query->where([['free_busy', '=', 1]])->whereHas('Booking', function ($query) {
//                            $query->where([['booking_status', '=', 1003]]);
//                        });
//                    }elseif ($type == 5){
//                        $query->where([['free_busy', '=', 1]])->whereHas('Booking', function ($query) {
//                            $query->where([['booking_status', '=', 1004]]);
//                        });
//                    }elseif ($type == 6){
//                        $query->where([['online_offline', '=', 2]]);
//                    }
//                    $drivers = $query->get();
//                }
//            }
//            if(empty($drivers) && count($drivers) == 0)
//            {
//                $drivers = [];
//            }else{
//                $home_address_active = isset($merchant->BookingConfiguration->home_address_enable) ? $merchant->BookingConfiguration->home_address_enable : NULL;
//                if($home_address_active == 1){
//                    $total_driver_ids = array_pluck($drivers->toArray(), 'driver_id');
//                    $homelocation_driver_ids = array_pluck($drivers->where('home_location_active', 1)->toArray(), 'driver_id');
//                    $accepted_driver_ids = array_diff($total_driver_ids, $homelocation_driver_ids);
//                    if(!empty($drop_lat) && !empty($drop_long)){
//                        if (!empty($homelocation_driver_ids)) {
//                            $nearHomelocation = Driver::GetHomeLocationsNearestToDropLocation($homelocation_driver_ids, $drop_lat, $drop_long, $distance, $limit, $merchant);
//                            if (!empty($nearHomelocation->toArray())) {
//                                $homelocation_id = array_pluck($nearHomelocation, 'driver_id');
//                                $newArray = array_intersect($homelocation_driver_ids, $homelocation_id);
//                                $newArray = array_merge($newArray,$accepted_driver_ids);
//                                $drivers = $drivers->whereIn('driver_id', $newArray);
//                                if (empty($drivers->toArray())) {
//                                    return [];
//                                }
//                            }else{
//                                $drivers = $drivers->where('home_location_active','!=', 1);
//                                if(count($drivers) == 0){
//                                    $drivers = [];
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//            return $drivers;
//        }catch (\Exception $e)
//        {
//            throw new \Exception($e->getMessage());
//        }
//    }

    public static function GetNearestDriver($arr_reqest)
    {
        // query parameters
        $limit = isset($arr_reqest['limit']) ? $arr_reqest['limit'] : 10;
        $area = isset($arr_reqest['area']) ? $arr_reqest['area'] : null;
        $distance_unit = isset($arr_reqest['distance_unit']) ? $arr_reqest['distance_unit'] : '';
//        $driver_request_time_out = isset($arr_reqest['driver_request_time_out']) ? $arr_reqest['driver_request_time_out'] :null;
        $service_type_id = isset($arr_reqest['service_type']) ? $arr_reqest['service_type'] : null;
        $vehicle_type_id = isset($arr_reqest['vehicle_type']) ? $arr_reqest['vehicle_type'] : null;
        $longitude = isset($arr_reqest['longitude']) ? $arr_reqest['longitude'] : null;
        $latitude = isset($arr_reqest['latitude']) ? $arr_reqest['latitude'] : null;
        $ac_nonac = isset($arr_reqest['ac_nonac']) ? $arr_reqest['ac_nonac'] : null;
        $user_gender = isset($arr_reqest['user_gender']) ? $arr_reqest['user_gender'] : null;
        $driver_ids = isset($arr_reqest['driver_ids']) ? $arr_reqest['driver_ids'] : [];
        $baby_seat = isset($arr_reqest['baby_seat']) ? $arr_reqest['baby_seat'] : null;
        $wheel_chair = isset($arr_reqest['wheel_chair']) ? $arr_reqest['wheel_chair'] : null;
        $riders_num = isset($arr_reqest['riders_num']) ? $arr_reqest['riders_num'] : 1;
        $distance = isset($arr_reqest['distance']) && !empty($arr_reqest['distance']) ? $arr_reqest['distance'] : 10;
        $vehicleTypeRank = isset($arr_reqest['vehicleTypeRank']) ? $arr_reqest['vehicleTypeRank'] : null; // check higher rank vehicle type in case of auto upgrade
        $radius = $distance_unit == 2 ? 3958.756 : 6367;
        $select = isset($arr_reqest['select']) ? $arr_reqest['select'] : '*';
        $type = isset($arr_reqest['type']) ? $arr_reqest['type'] : null;  //for admin driver map
        $taxi_company_id = isset($arr_reqest['taxi_company_id']) ? $arr_reqest['taxi_company_id'] : null;
        $isManual = isset($arr_reqest['isManual']) ? $arr_reqest['isManual'] : null;
        $drop_lat = isset($arr_reqest['drop_lat']) ? $arr_reqest['drop_lat'] : null;
        $drop_long = isset($arr_reqest['drop_long']) ? $arr_reqest['drop_long'] : null;
        $bookingId = isset($arr_reqest['booking_id']) ? $arr_reqest['booking_id'] : null;
        $segment_id = isset($arr_reqest['segment_id']) ? $arr_reqest['segment_id'] : null;
        $merchant_id = isset($arr_reqest['merchant_id']) ? $arr_reqest['merchant_id'] : null;
        $payment_method_id = isset($arr_reqest['payment_method_id']) ? $arr_reqest['payment_method_id'] : null;
        $booking_amount = isset($arr_reqest['estimate_bill']) ? $arr_reqest['estimate_bill'] : null;
        $gender_match = isset($arr_reqest['gender_match']) ? $arr_reqest['gender_match'] : null;
        $string_file = isset($arr_reqest['string_file']) ? $arr_reqest['string_file'] : "all_in_one";
        $base_areas = null;

        $queue_system = false;
        $queue_drivers = [];

        try {
            if (!empty($longitude) && !empty($latitude)) {
                // cash earning + new booking <= cash limit
                $merchantData = CountryArea::find($area);
                $date_obj = new DateTime(date('Y-m-d'));
//                $date_obj->setTimezone(new DateTimeZone($merchantData->timezone));
                $booking_date = $date_obj->format('Y-m-d');
                $cash_limit = $merchantData->driver_cash_limit_amount;
                $remaining_amount = $cash_limit - $booking_amount;
                $merchant = Merchant::with('Configuration', 'DriverConfiguration', 'ApplicationConfiguration')
                    ->where([['id', '=', $merchantData->merchant_id]])->first();

                $new_ride_before_ride_end = false;
                if(isset($merchant->Configuration->new_ride_before_ride_end) && $merchant->Configuration->new_ride_before_ride_end == 1){
                    $new_ride_before_ride_end = true;
                }

                if (isset($merchantData->is_geofence) && $merchantData->is_geofence == 1) {
                    $base_areas = isset($merchantData->RestrictedArea->base_areas) ? explode(',', $merchantData->RestrictedArea->base_areas) : '';
                    if (isset($merchantData->RestrictedArea) && $merchantData->RestrictedArea->queue_system == 1) {
                        $queue_system = true;
                        $queue_drivers = GeofenceAreaQueue::where([
                            ['merchant_id', '=', $merchant->id],
                            ['geofence_area_id', '=', $merchantData->id],
                            ['queue_status', '=', 1], /// Check for entry queue
                            ['exit_time', '=', null]
                        ])->where(function ($query) use ($base_areas) {
                            if (!empty($base_areas)) {
                                $query->whereIn('country_area_id', $base_areas);
                            }
                        })->whereDate('entry_time', date('Y-m-d'))->orderBy('queue_no')->pluck('driver_id')->toArray();
                        $driver_ids = $queue_drivers;
                    }
                }
                $merchantGender = isset($merchant->ApplicationConfiguration->gender) ? $merchant->ApplicationConfiguration->gender : NULL;
                $area_notifi = isset($merchant->Configuration->driver_area_notification) ? $merchant->Configuration->driver_area_notification : null;
                $minute = isset($merchant->DriverConfiguration->inactive_time) ? $merchant->DriverConfiguration->inactive_time : null;
                $delivery_busy_driver_accept_ride = isset($merchant->DriverConfiguration->delivery_busy_driver_accept_ride) ? $merchant->DriverConfiguration->delivery_busy_driver_accept_ride : null;
                $location_updated_last_time = '';

                if ($minute > 0) {
                    // driver last location is updated in utc zone, so no need to convert into time zone
                    $date = new DateTime;
                    $date->modify("-$minute minutes");
                    $location_updated_last_time = $date->format('Y-m-d H:i:s');

                }

                $drivers = [];
                // socket calling
                if ($merchant->Configuration->lat_long_storing_at == 2) {
                    // send distance in meter
                    $arr = ['area' => $area, 'limit' => $limit, 'distance_unit' => $distance_unit, 'service_type_id' => $service_type_id, 'vehicle_type_id' => $vehicle_type_id,
                        'longitude' => $longitude, 'latitude' => $latitude, 'ac_nonac' => $ac_nonac, 'user_gender' => $user_gender,
                        'driver_ids' => $driver_ids, 'baby_seat' => $baby_seat, 'wheel_chair' => $wheel_chair, 'riders_num' => $riders_num,
                        'distance' => $distance_unit == 2 ? ($distance * 1609.34) : ($distance * 1000), 'vehicleTypeRank' => $vehicleTypeRank, 'radius' => $radius, 'select' => $select,
                        'taxi_company_id' => $taxi_company_id, 'isManual' => $isManual, 'drop_lat' => $drop_lat, 'drop_long' => $drop_long, 'bookingId' => $bookingId, 'base_areas' => $base_areas,
                        'location_updated_last_time' => $location_updated_last_time, 'area_notifi' => $area_notifi, 'minute' => $minute, 'merchant_id' => $merchant->id, 'segment_id' => $segment_id,
                    ];
                    $drivers = Driver::GetNearestDriverFromNode($arr);
                } else {

                    $manual_downgradation = false;
                    if(isset($area)){
                        $country_area = CountryArea::find($area);
                        if (isset($country_area) && $merchant->Configuration->manual_downgrade_enable == 1 && $country_area->manual_downgradation == 1) {
                            $manual_downgradation = true;
                        }
                    }

                    $query = Driver::select("drivers.*")
                        ->addSelect(DB::raw('( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( current_latitude ) ) * cos( radians( current_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( current_latitude ) ) ) ) AS distance,drivers.id AS driver_id'))
                        ->join('driver_online as do', 'drivers.id', '=', 'do.driver_id')
                        ->join('driver_vehicles as dv', 'do.driver_vehicle_id', '=', 'dv.id')
                        ->leftJoin('driver_downgraded_vehicle_types as ddvt', 'drivers.id', '=', 'ddvt.driver_id');

                    if ($taxi_company_id != null || $isManual == true) {
                        $query->where('drivers.taxi_company_id', $taxi_company_id);
                    }
                    if ($service_type_id == 5) {
                        // pool service type
                        $query->where([['drivers.status_for_pool', '!=', 2008], ['drivers.pool_ride_active', '=', 1], ['drivers.avail_seats', '>=', $riders_num]]);
                        $query->whereIn('drivers.free_busy', [1, 2]);
                    } else {
                        $query->where('do.service_type_id', $service_type_id);
                        $query->where('do.segment_id', $segment_id);
//                $query->where([['drivers.free_busy', '=', 2]]);
                    }

                    if (!empty($vehicleTypeRank)) {
                        $query->join('driver_ride_configs as dvc', 'drivers.id', '=', 'dvc.driver_id');
                        $query->join('vehicle_types as vt', 'dv.vehicle_type_id', '=', 'vt.id');
                        $query->where([['vt.vehicleTypeRank', '<=', $vehicleTypeRank], ['dvc.auto_upgradetion', '=', 1]]);
                        $vehicle_type_id = null;
                    }
                    if(!empty($delivery_busy_driver_accept_ride) && $delivery_busy_driver_accept_ride == 1){
                        $query->whereIn('drivers.free_busy',[1,2]);  // Both free and busy
                    }else{
                        $query->where([
                            ['drivers.free_busy', '=', 2],
                        ]);
                    }
                    $query->where([
                        ['drivers.player_id', "!=", NULL],
                        ['login_logout', '=', 1],
                        ['drivers.online_offline', '=', 1],
                        ['dv.vehicle_verification_status', '=', 2], // only verified vehicle
                        ['drivers.driver_delete', '=', NULL]
                    ])
                        ->where(function($q) use($location_updated_last_time){
                            if(!empty($location_updated_last_time))
                            {
                              $q->where('last_location_update_time', '>=', $location_updated_last_time);
                            }
                        })
                        ->whereRaw('( is_suspended is NULL or is_suspended < ? )', [date('Y-m-d H:i:s')])
                        ->where(function ($q) use ($area_notifi, $area, $base_areas) {
                            if ($area_notifi == 1) { // 1 enable area wise, 2 disable
                                if (!empty($base_areas)) {
                                    array_push($base_areas, $area);
                                    $q->whereIn('drivers.country_area_id', $base_areas);
                                } else {
                                    $q->where('drivers.country_area_id', $area);
                                }
                            }
                        })
                        ->where(function ($q) use ($vehicle_type_id, $manual_downgradation) {
                            if (!empty($vehicle_type_id)) {
                                $q->where('dv.vehicle_type_id', $vehicle_type_id);
                                if ($manual_downgradation) {
                                    $q->orWhere('ddvt.vehicle_type_id', $vehicle_type_id);
                                }
                            }
                        })
                        ->where(function ($query) use ($ac_nonac) {
                            if ($ac_nonac == 1) {
                                return $query->where('dv.ac_nonac', $ac_nonac);
                            }
                        })
                        ->where(function ($query) use ($user_gender,$merchantGender,$gender_match) {
                            if (!empty($user_gender) && $merchantGender == 1) {
                                if ($gender_match == 1){
                                    return $query->where('drivers.driver_gender', $user_gender);
                                }else{
                                    return $query->whereIn('drivers.rider_gender_choice',[0,$user_gender]);
                                }
                            }
                        })
                        ->where(function ($query) use ($wheel_chair) {
                            if ($wheel_chair == 1) {
                                return $query->where('dv.wheel_chair', $wheel_chair);
                            }
                        })
                        ->where(function ($query) use ($driver_ids) {
                            if (!empty($driver_ids)) {
                                return $query->whereIn('drivers.id', $driver_ids);
                            }
                        })
                        ->where(function ($query) use ($baby_seat) {
                            if ($baby_seat == 1) {
                                return $query->where('dv.baby_seat', $baby_seat);
                            }
                        })
                        // commenting for development purpose
                        ->whereNOTIn('drivers.id', function ($query) use ($bookingId) {
                            $query->select('brd.driver_id')
                                ->from('booking_request_drivers as brd')
                                ->join('bookings as b', 'brd.booking_id', '=', 'b.id')
                                ->where('b.booking_status', '=', 1001)
                                ->where(function ($p) use ($bookingId) {
                                    if (!empty($bookingId)) {
                                        $p->where([['brd.booking_id', $bookingId], ['brd.request_status', 3]])
                                            ->orWhere([['brd.request_status', 1]]);
                                    } else {
                                        $p->where([['brd.request_status', 1]]);
                                    }
                                });
                        })
                        ->whereExists(function ($query) {
                            $query->select("ddv.driver_id")
                                ->from('driver_driver_vehicle as ddv')
                                ->join('driver_vehicles as dv', 'ddv.driver_vehicle_id', '=', 'dv.id')
                                ->where('ddv.vehicle_active_status', 1)
                                ->whereRaw('ddv.driver_id = drivers.id');
                        })
                        ->having('distance', '<', $distance)
                        ->orderBy('distance')
                        ->take($limit);
                    // if($payment_method_id == 1 && $merchant->Configuration->driver_cash_limit == 1)
                    // {
                    // p('kk');
                    //   p($remaining_amount);
                    // $query->whereNOTIn('drivers.id', function ($query1) use($booking_date,$remaining_amount){
                    //     $query1->select('b.driver_id')
                    //         ->from('bookings as b')


                    //   $query->whereNOTIn('drivers.id',DB::table('bookings as b')->select('b.driver_id',DB::raw('sum("final_amount_paid") as cash_amount'))
                    //   ->where('b.booking_status', '=', 1005)
                    //     ->where('b.payment_method_id', '=', 1)
                    //     ->where('b.payment_status', '=', 1)
                    //     ->whereDate('b.created_at', $booking_date)
                    //     ->groupBy('b.driver_id')
                    //     ->having('cash_amount','>',$remaining_amount)
                    //   ->pluck('driver_id')
                    //   );

                    // $query->whereNOTIn('drivers.id',function($query2) use($booking_date,$remaining_amount){
                    // return Booking::where('booking_status', '=', 1015)->get()->pluck('drive_id')->toArray();
                    //   return $query2->select('driver_id',DB::raw('sum("final_amount_paid") as cash_amount'))->from('bookings')->where('booking_status', '=', 1015)->get()->pluck('drive_id')->toArray();
                    // $query2->select('b.driver_id',DB::raw('sum("final_amount_paid") as cash_amount'))
                    //     ->from('bookings as b')
                    //     ->where('b.booking_status', '=', 1005)
                    //     ->where('b.payment_method_id', '=', 1)
                    //     ->where('b.payment_status', '=', 1)
                    //     ->whereDate('b.created_at', $booking_date)
                    //     ->groupBy('b.driver_id')
                    //     ->having('cash_amount','<',$remaining_amount)
                    // ;
                    // });
                    // });
                    // }
                    $drivers = $query->get();
                    // p($drivers);

                    if ($payment_method_id == 1 && $merchant->Configuration->driver_cash_limit == 1 && $drivers->count() > 0) {
                        // p($remaining_amount);
                        // $all_drivers = array_pluck($drivers,'id');
                        $under_cash_limit_drivers = Booking::select('b.driver_id', DB::raw('SUM(final_amount_paid) as cash_amount'))
                            ->from('bookings as b')
                            ->where('b.booking_status', '=', 1005)
                            ->where('b.payment_method_id', '=', 1)
                            ->where('b.payment_status', '=', 1)
                            ->where('b.country_area_id', '=', $area)
                            ->where('b.segment_id', '=', $segment_id)
                            ->where('b.service_type_id', '=', $service_type_id)
                            ->where('b.vehicle_type_id', '=', $vehicle_type_id)
                            ->whereDate('b.created_at', $booking_date)
                            ->having('cash_amount', '>=', $remaining_amount)
                            ->groupBy('b.driver_id')
                            // ->whereIn('b.driver_id',$all_drivers)
                            ->get();
                        $under_cash_limit_drivers = array_pluck($under_cash_limit_drivers, 'driver_id');
                        $upper_limit_driver = $drivers->whereNOTIN('id', $under_cash_limit_drivers);
                        // p($drivers);

                        // if drivers are available  with under cash limit then return that drivers
                        // otherwise return all drivers with warning to change payment method
                        if ($upper_limit_driver->count() > 0) {
                            // $under_cash_limit_drivers_id = array_pluck($under_cash_limit_drivers);
                            // $drivers = $drivers->whereIn('id',$under_cash_limit_drivers_id);
                            $drivers = collect($upper_limit_driver->values());
                        } else {
                            throw  new \Exception(trans("$string_file.no_driver_available_with_cash"));
                        }
                    }
                    // p('sa');
                }
                if ($queue_system) {
                    if (count($queue_drivers) > 0 && count($drivers) > 0 && $drop_lat != '' && $drop_long != '') {
                        $i = 0;
                        $found_driver = [];
                        foreach ($drivers as $driver) {
                            if ($driver->driver_id == $queue_drivers[$i]) {
                                array_push($found_driver, $driver);
                                break;
                            }
                        }
                        return $found_driver;
                    } else {
                        return [];
                    }
                }
            } else {
                if (!empty($type)) {
                    $merchant_id = !empty($merchant_id) ? $merchant_id : get_merchant_id();
                    $select = ['id', 'first_name', 'last_name', 'email', 'phoneNumber', 'profile_image', 'current_latitude', 'current_longitude', 'online_offline', 'free_busy'];
                    $query = Driver::select($select)->where([
                        ['merchant_id', '=', $merchant_id],
                        ['current_latitude', '!=', null],
                        ['login_logout', '=', 1],
                        ['driver_delete', '=', null]
                    ]);
                    if ($type == 2) {
                        $query->where([['online_offline', '=', 1], ['free_busy', '=', 2]]);
                    } elseif ($type == 3) {
                        $query->where([['free_busy', '=', 1]])->whereHas('Booking', function ($query) {
                            $query->where([['booking_status', '=', 1002]]);
                        });
                    } elseif ($type == 4) {
                        $query->where([['free_busy', '=', 1]])->whereHas('Booking', function ($query) {
                            $query->where([['booking_status', '=', 1003]]);
                        });
                    } elseif ($type == 5) {
                        $query->where([['free_busy', '=', 1]])->whereHas('Booking', function ($query) {
                            $query->where([['booking_status', '=', 1004]]);
                        });
                    } elseif ($type == 6) {
                        $query->where([['online_offline', '=', 2]]);
                    }
                    $drivers = $query->get();
                }
            }
            if (empty($drivers) && count($drivers) == 0) {
                $drivers = [];
            } else {
                $home_address_active = isset($merchant->BookingConfiguration->home_address_enable) ? $merchant->BookingConfiguration->home_address_enable : NULL;
                if ($home_address_active == 1) {
                    $total_driver_ids = array_pluck($drivers->toArray(), 'driver_id');
                    $homelocation_driver_ids = array_pluck($drivers->where('home_location_active', 1)->toArray(), 'driver_id');
                    $accepted_driver_ids = array_diff($total_driver_ids, $homelocation_driver_ids);
                    if (!empty($drop_lat) && !empty($drop_long)) {
                        if (!empty($homelocation_driver_ids)) {
                            $nearHomelocation = Driver::GetHomeLocationsNearestToDropLocation($homelocation_driver_ids, $drop_lat, $drop_long, $distance, $limit, $merchant);
                            if (!empty($nearHomelocation->toArray())) {
                                $homelocation_id = array_pluck($nearHomelocation, 'driver_id');
                                $newArray = array_intersect($homelocation_driver_ids, $homelocation_id);
                                $newArray = array_merge($newArray, $accepted_driver_ids);
                                $drivers = $drivers->whereIn('driver_id', $newArray);
                                if (empty($drivers->toArray())) {
                                    return [];
                                }
                            } else {
                                $drivers = $drivers->where('home_location_active', '!=', 1);
                                if (count($drivers) == 0) {
                                    $drivers = [];
                                }
                            }
                        }
                    }
                }
                if(isset($merchant->Configuration->driver_limit) && $merchant->Configuration->driver_limit == 1 && !empty($drivers)){
                    foreach($drivers as $key => $driver){
                        $driver_ride_config = DriverRideConfig::where('driver_id',$driver->driver_id)->first();
                        if(!empty($driver_ride_config) && $driver_ride_config->radius > 0){
                            if($driver->distance > $driver_ride_config->radius){
                                $drivers->forget($key);
                            }
                        }
                    }
                }
            }

            if($new_ride_before_ride_end == true && !empty($longitude) && !empty($latitude)){
                $booked_driver_ids = Booking::select(DB::raw('( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( drop_latitude ) ) * cos( radians( drop_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( drop_latitude ) ) ) ) AS distance, driver_id, id, merchant_booking_id'))
                    ->where('booking_status', 1004)->having('distance', '<', $distance)->get()->pluck('driver_id')->toArray();
                $extra_drivers = [];
                $query = Driver::select($select)->addSelect(DB::raw('( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( current_latitude ) ) * cos( radians( current_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( current_latitude ) ) ) ) AS distance,drivers.id AS driver_id'))
                    ->join('driver_driver_vehicle', 'drivers.id', '=', 'driver_driver_vehicle.driver_id')
                    ->join('driver_vehicles', 'driver_driver_vehicle.driver_vehicle_id', '=', 'driver_vehicles.id');

                if($taxi_company_id != null || $isManual == true){
                    $query->where('drivers.taxi_company_id',$taxi_company_id);
                }

                if($service_type_id == 5)
                {
                    // pool service type
                    $query->where([['drivers.status_for_pool', '!=', 2008], ['drivers.pool_ride_active', '=', 1], ['drivers.avail_seats', '>=', $riders_num]]);
                    $query->whereIn('drivers.free_busy',[1,2]);
                }
                else{
                    $query->join('driver_vehicle_service_type', 'driver_driver_vehicle.driver_vehicle_id', '=', 'driver_vehicle_service_type.driver_vehicle_id');
                    $query->where('driver_vehicle_service_type.service_type_id',$service_type_id);
                    $query->where([['drivers.free_busy', '=', 1]]);  // Get busy drivers
                }

                if(!empty($vehicleTypeRank))
                {
                    $query->join('driver_ride_configs', 'drivers.id', '=', 'driver_ride_configs.driver_id');
                    $query->join('vehicle_types', 'driver_vehicles.vehicle_type_id', '=', 'vehicle_types.id');
                    $query->where([['vehicle_types.vehicleTypeRank', '<=', $vehicleTypeRank], ['driver_ride_configs.auto_upgradetion', '=', 1]]);
                    //in auto upgrade case next rank vehicle type will be searched
                    $vehicle_type_id = null;
                }

                $query->where([
                    ['drivers.player_id', "!=", NULL],
                    ['login_logout', '=', 1],
                    ['drivers.online_offline', '=', 1],
                    ['drivers.driver_delete', '=', NULL]
                ])
                    ->whereRaw('( is_suspended is NULL or is_suspended < ? )' , [date('Y-m-d H:i:s')])
                    ->where(function($q) use ($area_notifi, $area, $base_areas){

                        if ($area_notifi == 1) { // 1 enable area wise, 2 disable
                            if (!empty($base_areas)) {
                                array_push($base_areas, $area);
                                $q->whereIn('drivers.country_area_id', $base_areas);
                            } else {
                                $q->where('drivers.country_area_id', $area);
                            }
                        }
                    })
                    ->where(function ($query) use ($ac_nonac) {
                        if ($ac_nonac == 1) {
                            return $query->where('driver_vehicles.ac_nonac', $ac_nonac);
                        }
                    })
                    ->where(function ($query) use ($user_gender,$merchantGender,$gender_match) {
                        if (!empty($user_gender) && $merchantGender == 1) {
                            if ($gender_match == 1){
                                return $query->where('drivers.driver_gender', $user_gender);
                            }else{
                                return $query->whereIn('drivers.rider_gender_choice',[0,$user_gender]);
                            }
                        }
                    })
                    ->where(function ($query) use ($wheel_chair) {
                        if ($wheel_chair == 1) {
                            return $query->where('driver_vehicles.wheel_chair', $wheel_chair);
                        }
                    })
                    ->where(function ($query) use ($driver_ids) {
                        if (!empty($driver_ids))
                        {
                            return $query->whereIn('drivers.id', $driver_ids);
                        }
                    })
                    ->where(function ($query) use ($baby_seat) {
                        if ($baby_seat == 1) {
                            return $query->where('driver_vehicles.baby_seat', $baby_seat);
                        }
                    })
                    ->whereNOTIn('drivers.id', function ($query) use($bookingId){
                        $query->select('brd.driver_id')
                            ->from('booking_request_drivers as brd')
                            ->join('bookings as b', 'brd.booking_id', '=', 'b.id')
                            ->where('b.booking_status', '=', 1001)
                            ->where(function ($p) use($bookingId){
                                $p->where([['brd.booking_id',$bookingId],['brd.request_status',3]])->orWhere([['brd.request_status',1]]);
                            });
                    })
                    ->whereNOTIn('drivers.id', function ($query){
                        $query->select('b.driver_id')
                            ->from('bookings as b')
                            ->where('b.booking_status', '=', 1002);
                    })
                    ->whereExists(function ($query) {
                        $query->select("ddv.driver_id")
                            ->from('driver_driver_vehicle as ddv')
                            ->join('driver_vehicles as dv', 'ddv.driver_vehicle_id', '=', 'dv.id')
                            ->where('ddv.vehicle_active_status', 1)
                            ->whereRaw('ddv.driver_id = drivers.id');
                    })
                    ->whereIn('drivers.id',$booked_driver_ids)
                    ->having('distance', '<', $distance)
                    ->orderBy('distance')
                    ->take($limit)
                    ->distinct();
                $extra_drivers = $query->get();
                // dd($extra_drivers);
                if(!empty($extra_drivers)){
                    foreach($extra_drivers as $driver){
                        $current_booking = Booking::select(DB::raw('( ' . $radius . ' * acos( cos( radians(' . $driver->current_latitude . ') ) * cos( radians( drop_latitude ) ) * cos( radians( drop_longitude ) - radians(' . $driver->current_longitude . ') ) + sin( radians(' . $driver->current_latitude . ') ) * sin( radians( drop_latitude ) ) ) ) AS first_distance, ( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( drop_latitude ) ) * cos( radians( drop_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( drop_latitude ) ) ) ) AS second_distance'))
                            ->where([['driver_id','=',$driver->driver_id],['booking_status','=',1004]])->first();

                        if(!empty($current_booking)){
                            $driver->distance = $current_booking->first_distance + $current_booking->second_distance;
                        }
                    }
                    if(count($drivers) > 0){
                        $drivers = $drivers->merge($extra_drivers);
                        $drivers = $drivers->sortBy('distance')->take($limit);
                        $drivers = $drivers->values();
                    }else{
                        $drivers = $extra_drivers;
                    }
                }
            }

            if(count($drivers) == 0){
                $drivers = [];
            }
            return $drivers;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function GetHomeLocationsNearestToDropLocation(array $drivers, $latitude, $longitude, $distance, $limit, $merchant)
    {
        $date = new DateTime;
        $minute = isset($merchant->DriverConfiguration->inactive_time) ? $merchant->DriverConfiguration->inactive_time : null;
        $location_updated_last_time = '';
        if ($minute > 0) {
            $date = new DateTime;
            $date->modify("-$minute minutes");
            $location_updated_last_time = $date->format('Y-m-d H:i:s');
        }
        $drivers_active_home = DriverAddress::select(DB::raw('*, ( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( latitude ) ) ) ) AS distance,driver_addresses.id AS driver_addresses_id'))
            ->join('drivers', 'driver_addresses.driver_id', '=', 'drivers.id')
            ->having('distance', '<', $distance)
            ->where([['driver_addresses.address_status', TRUE],
                ['last_location_update_time', '>=', $location_updated_last_time]])
            ->whereIn('driver_id', $drivers)
            ->orderBy('distance')
            ->take($limit)
            ->whereNOTIn('drivers.id', function ($query) {
                $query->select('brd.driver_id')
                    ->from('booking_request_drivers as brd')
                    ->join('bookings as b', 'brd.booking_id', '=', 'b.id')
                    ->where('b.booking_status', '=', 1001);
            })
            ->get();
        return $drivers_active_home;
    }

    public static function CheckNewDropPointNearestToDropLocation($poolrideid, $latitude, $longitude, $distance)
    {
        $return_data = PoolRideList::select(DB::raw('*, ( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( drop_lat ) ) * cos( radians( drop_long ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( drop_lat ) ) ) ) AS distance'))
            ->having('distance', '<', $distance)
            ->where([['pool_ride_lists.id', '=', $poolrideid]])
            ->orderBy('distance')
            ->get();
        return $return_data;
    }

    public static function getNearestPlumbers($arr_reqest)
    {
        /*******NOTE*********/
        // pagination 2 : all drivers without pagination for map
        // pagination 1 & page value: drivers with pagination
        //pagination 1 without page value means only fav driver of that user

        $limit = isset($arr_reqest['limit']) ? $arr_reqest['limit'] : 10;
        $segment_id = isset($arr_reqest['segment_id']) ? $arr_reqest['segment_id'] : NULL;
        $merchant_id = isset($arr_reqest['merchant_id']) ? $arr_reqest['merchant_id'] : null;
        $distance_unit = isset($arr_reqest['distance_unit']) ? $arr_reqest['distance_unit'] : '';
        $longitude = isset($arr_reqest['longitude']) ? $arr_reqest['longitude'] : null;
        $latitude = isset($arr_reqest['latitude']) ? $arr_reqest['latitude'] : null;
        $driver_ids = isset($arr_reqest['driver_ids']) ? $arr_reqest['driver_ids'] : [];
        $distance = isset($arr_reqest['distance']) && !empty($arr_reqest['distance']) ? $arr_reqest['distance'] : 10;
        $radius = $distance_unit == 2 ? 3958.756 : 6367;
        $select = isset($arr_reqest['select']) ? $arr_reqest['select'] : '*';
        $user_id = isset($arr_reqest['user_id']) ? $arr_reqest['user_id'] : null;
        $pagination = isset($arr_reqest['pagination']) ? $arr_reqest['pagination'] : null;
//        $distance = isset($arr_reqest['distance']) ? $arr_reqest['distance'] : null;
        $popularity = isset($arr_reqest['popularity']) ? $arr_reqest['popularity'] : null;
        $price_low = isset($arr_reqest['price_low']) ? $arr_reqest['price_low'] : null;
        $price_high = isset($arr_reqest['price_high']) ? $arr_reqest['price_high'] : null;
        $area_id = isset($arr_reqest['area_id']) ? $arr_reqest['area_id'] : null;
//        $price_card_owner = isset($arr_reqest['price_card_owner']) ? $arr_reqest['price_card_owner'] : 1;
        $page = isset($arr_reqest['page']) ? $arr_reqest['page'] : null;
        $service_time_slot_detail_id = isset($arr_reqest['service_time_slot_detail_id']) ? $arr_reqest['service_time_slot_detail_id'] : null;
        $area_id = isset($arr_reqest['area']) ? $arr_reqest['area'] : NULL;
        $auto_assign = isset($arr_reqest['auto_assign']) ? $arr_reqest['auto_assign'] : NULL;
        //$arr_services = isset($arr_reqest['selected_services']) ? $arr_reqest['selected_services'] : null;

        $drivers = [];
//        $segment = Segment::Find($segment_id);
        $price_card_owner = isset($arr_reqest['price_card_owner']) ? $arr_reqest['price_card_owner'] : null;
//        $price_card_owner = isset($segment->Merchant[0]->price_card_owner) ? $segment->Merchant[0]->price_card_owner : 1;  //
        // 1 : merchant/admin , 2: provider/driver
        if (!empty($longitude) && !empty($latitude)) {
            $query = Driver::select('drivers.id', 'drivers.profile_image', 'drivers.first_name', 'drivers.last_name', 'country_area_id')
                ->addSelect('drivers.id AS driver_id,fd.driver_id as is_favourite,profile_image')
//                ->addSelect('dsr.rating')
                ->join('driver_segment as ds', 'drivers.id', '=', 'ds.driver_id')
                ->leftJoin('favourite_drivers as fd', 'drivers.id', '=', 'fd.driver_id')
//                ->leftJoin(DB::raw('(SELECT dsr.driver_id, CAST(AVG (dsr.rating) AS DECIMAL (2,1)) AS rating FROM `driver_segment_ratings` as dsr GROUP BY dsr.driver_id) dsr'),'drivers.id','=','dsr.driver_id')
                // join driver workshop address (location of driver)
                ->join('driver_addresses as da', 'drivers.id', '=', 'da.driver_id')
                ->addSelect(DB::raw('( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( latitude ) ) ) ) AS distance,da.driver_id,da.latitude,da.longitude,da.address_status'))
                ->orderBy('distance')
                ->with(['ServiceTypeOnline' => function ($q) use ($segment_id) {
                    $q->where('driver_online.segment_id', $segment_id);
                }])
                ->whereHas('ServiceTypeOnline', function ($q) use ($segment_id) {
                    $q->where('driver_online.segment_id', $segment_id);
                })
                ->with(['ServiceType' => function ($q) use ($segment_id, $merchant_id) {
                    $q->where('driver_service_type.segment_id', $segment_id);
//                    $q->with(['SegmentPriceCardDetail'=>function($q) use($segment_id,$merchant_id){
                    //$q->addSelect('segment_price_cards.id','segment_price_cards.service_type_id','segment_price_cards.amount','segment_price_cards.price_type','segment_price_cards.driver_id');
//                        $q->where('segment_id', $segment_id);
//                        $q->where('merchant_id', $merchant_id);
//                    }]);

                    $q->with(['DriverOnline' => function ($q) use ($segment_id) {
                        $q->where('driver_online.segment_id', $segment_id);
                    }]);

                    $q->whereHas('DriverOnline', function ($q) use ($segment_id) {
                        $q->where('driver_online.segment_id', $segment_id);
                    });
                }])
                ->whereHas('ServiceType', function ($q) use ($segment_id, $merchant_id) {
                    $q->where('driver_service_type.segment_id', $segment_id);
                });

            if ($price_card_owner == 2) {
                $query->with(['SegmentPriceCard' => function ($q) use ($segment_id, $merchant_id) {
                    $q->where('segment_id', $segment_id);
                    $q->where('merchant_id', $merchant_id);
                    $q->with(['SegmentPriceCardDetail' => function ($q) use ($segment_id, $merchant_id) {
                    }]);
                }]);
                $query->whereHas('SegmentPriceCard', function ($q) use ($segment_id, $merchant_id) {
                    $q->where('segment_id', $segment_id);
                    $q->where('merchant_id', $merchant_id);
                });
            }
            if ($auto_assign == 1) {
                $query->with(['ServiceTimeSlotDetail' => function ($q) use ($segment_id, $merchant_id) {
                    $q->where('segment_id', $segment_id);

                }]);
                $query->whereHas('ServiceTimeSlotDetail', function ($q) use ($segment_id, $merchant_id) {
                    $q->where('segment_id', $segment_id);
                });
            }

            $query->where([
                ['drivers.segment_group_id', '=', 2],
                ['drivers.signupStep', '=', 9],
                ['drivers.driver_delete', '=', NULL],
                ['drivers.merchant_id', '=', $merchant_id],
                ['ds.segment_id', '=', $segment_id],
                ['da.address_status', '=', 1],
            ])
//                 ->where(function($q) use($user_id,$page,$pagination,$segment_id){
//                     if(empty($page) && $pagination == 1)
//                     {
//                         $q->where('fd.user_id',  $user_id);
//                         $q->where('fd.segment_id',  $segment_id);
//                     }
//                 })
                ->whereRaw('( is_suspended is NULL or is_suspended < ? )', [date('Y-m-d H:i:s')])
                ->where(function ($query) use ($driver_ids) {
                    if (!empty($driver_ids)) {
                        return $query->whereIn('drivers.id', $driver_ids);
                    }
                });
            if (!empty($service_time_slot_detail_id)) {
                $query->whereNOTIn('drivers.id', function ($query) use ($service_time_slot_detail_id, $merchant_id) {
                    $query->select('ho.driver_id')
                        ->from('handyman_orders as ho')
                        ->where([['service_time_slot_detail_id', '=', $service_time_slot_detail_id], ['is_order_completed', '!=', 1], ['merchant_id', '=', $merchant_id]])
//                        ->join('handyman_orders as b', 'ho.driver_id', '=', 'b.id')
                        ->whereIn('ho.order_status', [4, 6, 7]);
                });
            }
            if ($popularity == 1) {
                $query->orderBy('rating');
            }
            if (!empty($area_id)) {
                $query->where('country_area_id', $area_id);
            }
            $query->groupBy("drivers.id");
            if ($pagination == 1 && !empty($page)) {
                $drivers = $query->paginate(6);
            } else {
                $drivers = $query->get();
            }
        }
        return $drivers;
    }

    public static function getPlumber($arr_reqest)
    {
        $merchant_id = $arr_reqest->merchant_id;
        $segment_id = $arr_reqest->segment_id;
        $price_type = $arr_reqest->price_type;
        $price_card_owner_config = $arr_reqest->price_card_owner_config;
        $id = $arr_reqest->id;
        $query = Driver::select('drivers.id', 'drivers.first_name', 'drivers.last_name', 'drivers.profile_image', 'drivers.created_at', 'fd.is_favourite')
//            ->addSelect(DB::raw("CAST(AVG (dsr.rating) AS DECIMAL (2,1)) AS rating"), 'dsr.driver_id')
//            ->leftJoin('booking_rating as dsr', 'drivers.id', '=', 'dsr.driver_id')
            ->leftJoin(DB::raw('(SELECT fd.driver_id, count(fd.driver_id) AS is_favourite FROM `favourite_drivers` as fd where( segment_id="' . $segment_id . '" or segment_id IS NULL) GROUP BY fd.driver_id) fd'), 'drivers.id', '=', 'fd.driver_id')
            ->with(['ServiceType.ServiceTranslation' => function ($q) use ($merchant_id) {
                $q->addSelect('service_translations.name', 'service_translations.service_type_id');
                $q->where('service_translations.merchant_id', $merchant_id);
            }])
            ->with(['ServiceType.ServiceTranslation' => function ($q) use ($merchant_id) {
                $q->addSelect('service_translations.name', 'service_translations.service_type_id');
                $q->where('service_translations.merchant_id', $merchant_id);
            }])
            ->with(['DriverGallery' => function ($q) use ($segment_id) {
                $q->addSelect('id', 'driver_id', 'image_title');
                $q->where('driver_galleries.segment_id', $segment_id);
                $q->orWhere('driver_galleries.segment_id', NULL);
            }])
            ->with(['ServiceType' => function ($q) use ($segment_id) {
//                $q->where('segment_id', $segment_id);
                $q->where('driver_service_type.segment_id', $segment_id);
            }])
            ->whereHas('ServiceType', function ($q) use ($segment_id) {
//                $q->where('segment_id', $segment_id);
                $q->where('driver_service_type.segment_id', $segment_id);
            })
            ->where([
                ['drivers.driver_delete', '=', NULL],
                ['drivers.merchant_id', '=', $merchant_id],
                ['drivers.id', '=', $id],
            ]);
//            ->groupBy('dsr.driver_id');
        if ($price_card_owner_config == 2) {
            $query->with(['SegmentPriceCard' => function ($q) use ($segment_id, $merchant_id) {
                $q->where('segment_id', $segment_id);
                $q->where('merchant_id', $merchant_id);
                $q->with(['SegmentPriceCardDetail' => function ($q) use ($segment_id, $merchant_id) {
                }]);
            }]);

            $query->whereHas('SegmentPriceCard', function ($q) use ($segment_id, $merchant_id) {
                $q->where('segment_id', $segment_id);
                $q->where('merchant_id', $merchant_id);
            });
        }
        $driver = $query->first();
        return $driver;
    }

    public static function getDeliveryCandidate($arr_request, $pagination = false)
    {
        $merchant_id = $arr_request->merchant_id;
        $segment_id = $arr_request->segment_id;
        // in case of demo lat long will be user and in other case it will be of restro or shop
        $latitude = $arr_request->latitude;
        $longitude = $arr_request->longitude;
        $user_id = $arr_request->user_id;
        $order_id = $arr_request->id;
        $arr_id = $arr_request->arr_id;
        $distance_unit = 1;
        $merchant = Merchant::Find($merchant_id);
        $radius = $distance_unit == 2 ? 3958.756 : 6367;
        $service_type_id = $arr_request->service_type_id;
        $driver_vehicle_id = $arr_request->driver_vehicle_id;
        $driver_not = $arr_request->driver_not;
        $arr_not_drivers = $arr_request->arr_not_drivers;
        $arr_agency_id = $arr_request->arr_agency_id;
        $limit = $merchant->BookingConfiguration->normal_ride_now_request_driver;
        $remain_ride_radius_slot = json_decode($merchant->BookingConfiguration->driver_ride_radius_request, true);
        $distance = isset($remain_ride_radius_slot[0]) ? $remain_ride_radius_slot[0] : 5;
        if (!empty($merchant->Configuration->lat_long_storing_at) && $merchant->Configuration->lat_long_storing_at == 2)
        {
            $query = Driver::select('drivers.id', 'ats_id', 'drivers.first_name', 'drivers.last_name', 'drivers.profile_image', 'drivers.created_at', 'fd.is_favourite', 'drivers.id AS driver_id', 'email', 'phoneNumber','drivers.rating')
                ->join('driver_online as do', 'drivers.id', '=', 'do.driver_id')
                ->leftJoin(DB::raw('(SELECT fd.driver_id, count(fd.driver_id) AS is_favourite FROM `favourite_drivers` as fd where( segment_id="' . $segment_id . '" or segment_id IS NULL AND user_id="' . $user_id . '" or user_id IS NULL) GROUP BY fd.driver_id) fd'), 'drivers.id', '=', 'fd.driver_id')
                ->whereHas('ServiceType', function ($q) use ($segment_id) {
                    $q->where('driver_service_type.segment_id', $segment_id);
                })
                ->whereHas('Segment', function ($q) use ($segment_id) {
                    $q->where('segment_id', $segment_id);
                })
                ->with(['Order' => function ($q) {
                    $q->addSelect('driver_id', 'merchant_order_id', 'id', 'order_status');
                }])
                ->where([
                    ['drivers.merchant_id', '=', $merchant_id],
                    ['drivers.player_id', "!=", NULL],
                    ['login_logout', '=', 1],
                    ['drivers.online_offline', '=', 1],
                    ['drivers.free_busy', '=', 2],
                    ['drivers.driver_delete', '=', NULL]
                ])
                ->where(function ($q) use ($arr_id) {
                    if (!empty($arr_id)) {
                        $q->whereIn('id', $arr_id);
                    }
                })
                ->where('do.service_type_id', $service_type_id)
                ->where('do.segment_id', $segment_id)
                ->whereNOTIn('drivers.id', function ($query) use ($order_id) {
                    $query->select('brd.driver_id')
                        ->from('booking_request_drivers as brd')
                        ->join('orders as o', 'brd.order_id', '=', 'o.id')
                        ->where('o.order_status', '=', 1)
                        ->where(function ($p) use ($order_id) {
                            $p->where([['brd.order_id', $order_id], ['brd.request_status', 3]])->orWhere([['brd.request_status', 1]]);
                        });
                });
            if(!empty($arr_agency_id) && count($arr_agency_id) > 0)
            {
                $query->whereIn('driver_agency_id',$arr_agency_id);
            }
            $temp_drivers = $query->get();

            $php_server_drivers = $temp_drivers; // temp variable
            $temp_drivers = $temp_drivers->toArray();
            $arr_driver_id = array_column($temp_drivers, 'ats_id');
            $arr_param['arr_driver_id'] = $arr_driver_id;
            $time_span = 0; // minute converted into seconds
            if ($time_span == 0) {
                $time_span = 5000; // seconds
            }
            $distance = $distance_unit == 2 ? ($distance * 1609.34) : ($distance * 1000);
            $arr_param = ['radius' => $distance, 'limit' => $limit, 'latitude' => $latitude, 'longitude' => $longitude, 'ats_ids' => $arr_driver_id, 'timespan' => $time_span];
            $drivers = Driver::getFinalDrivers($php_server_drivers, $arr_param);
        } else {
//            'fd.is_favourite',
            $query = Driver::select('drivers.id', 'drivers.first_name', 'drivers.last_name', 'drivers.profile_image', 'drivers.created_at', 'drivers.rating')
                ->addSelect(DB::raw('( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( current_latitude ) ) * cos( radians( current_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( current_latitude ) ) ) ) AS distance,drivers.id AS driver_id'))
                ->join('driver_online as do', 'drivers.id', '=', 'do.driver_id')
//                ->leftJoin(DB::raw('(SELECT fd.driver_id, count(fd.driver_id) AS is_favourite FROM `favourite_drivers` as fd where( segment_id="' . $segment_id . '" or segment_id IS NULL AND user_id="' . $user_id . '" or user_id IS NULL) GROUP BY fd.driver_id) fd'), 'drivers.id', '=', 'fd.driver_id')
                ->whereHas('ServiceType', function ($q) use ($segment_id) {
                    $q->where('driver_service_type.segment_id', $segment_id);
                })
                ->whereHas('Segment', function ($q) use ($segment_id) {
                    $q->where('segment_id', $segment_id);
                })
                ->with(['Order' => function ($q) {
                    $q->addSelect('driver_id', 'merchant_order_id', 'id', 'order_status');
                }])
                ->where([
                    ['drivers.merchant_id', '=', $merchant_id],
                    ['drivers.player_id', "!=", NULL],
                    ['login_logout', '=', 1],
                    ['drivers.online_offline', '=', 1],
                    ['drivers.free_busy', '=', 2],
                    ['drivers.driver_delete', '=', NULL]
                ])
                ->where(function ($q) use ($arr_id) {
                    if (!empty($arr_id)) {
                        $q->whereIn('id', $arr_id);
                    }
                })
                ->where(function ($q) use ($driver_not,$arr_not_drivers) {
                    if (!empty($driver_not)) {
                        $q->whereNotIn('id', $arr_not_drivers);
                    }
                })
                ->where('do.service_type_id', $service_type_id)
                ->where('do.segment_id', $segment_id)
                ->whereNOTIn('drivers.id', function ($query) use($order_id){
                    $query->select('brd.driver_id')
                        ->from('booking_request_drivers as brd')
                        ->join('orders as o', 'brd.order_id', '=', 'o.id')
                        ->where('o.order_status', '=', 1)
                        ->where(function ($p) use($order_id){
                            $p->where([['brd.order_id',$order_id],['brd.request_status',3]])->orWhere([['brd.request_status',1]]);
                        });
                })
                ->limit($limit)
                ->having('distance', '<', $distance)
                ->orderBy('distance');
            if(!empty($arr_agency_id) && count($arr_agency_id) > 0)
                {
                    $query->whereIn('driver_agency_id',$arr_agency_id);
                }
            if ($pagination == true) {
                $drivers = $query->paginate(10);
            } else {
                $drivers = $query->get();
            }
        }
        return $drivers;
    }

    public function getDriverGallery($request)
    {
        $driver = $request->user('api-driver');
        $driver_id = $driver->id;
        $merchant_id = $driver->merchant_id;
        $arr_max_limit = 10;
        $segment_gallery = Segment::with(['DriverGallery' => function ($q) use ($driver_id) {
            $q->where('driver_id', $driver_id);
            $q->where('handyman_order_id',NULL);
        }])
            ->whereHas('Driver', function ($q) use ($driver_id) {
                $q->where('driver_id', $driver_id);
            })
            ->get();

        $gallery_data = $segment_gallery->map(function ($item) use ($merchant_id) {
            $images = $item->DriverGallery->map(function ($item_inner) use ($merchant_id) {
                return array(
                    'id' => $item_inner->id,
                    'image' => get_image($item_inner->image_title, 'driver_gallery', $merchant_id),
                );
            });
            return array(
                'id' => $item->id,
                'segment_name' => $item->Name($merchant_id),
                'segment_images' => $images,

            );
        });
        $final_return_data['maximum_limit'] = 10;
        $final_return_data['segment_gallery'] = $gallery_data;
        return $final_return_data;
    }

    // socket code
    // socket code to get nearest driver from node server
    public static function GetNearestDriverFromNode($arr)
    {
        $manual_downgradation = false;
        if (isset($arr['area'])) {
            $country_area = CountryArea::find($arr['area']);
            if (isset($country_area) && $country_area->Merchant->Configuration->manual_downgrade_enable == 1 && $country_area->manual_downgradation == 1) {
                $manual_downgradation = true;
            }
        }
        $vehicle_type_id = $arr['vehicle_type_id'] ?? null;
        $riders_num = $arr['riders_num'];
        $location_updated_last_time = isset($arr['location_updated_last_time']) ? $arr['location_updated_last_time'] : "";
        $query = Driver::select("drivers.*", 'drivers.id AS driver_id')
//            ->addSelect(DB::raw('( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( current_latitude ) ) * cos( radians( current_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( current_latitude ) ) ) ) AS distance,drivers.id AS driver_id'))
            ->join('driver_online as do', 'drivers.id', '=', 'do.driver_id')
            ->join('driver_vehicles as dv', 'do.driver_vehicle_id', '=', 'dv.id');

        if ($arr['taxi_company_id'] != null || $arr['isManual'] == true) {
            $query->where('drivers.taxi_company_id', $arr['taxi_company_id']);
        }
        if ($arr['service_type_id'] == 5) {
            // pool service type
            $query->where([['drivers.status_for_pool', '!=', 2008], ['drivers.pool_ride_active', '=', 1], ['drivers.avail_seats', '>=', $riders_num]]);
            $query->whereIn('drivers.free_busy', [1, 2]);
        } else {
            $query->where('do.service_type_id', $arr['service_type_id']);
            $query->where('do.segment_id', $arr['segment_id']);
            $query->where([['drivers.free_busy', '=', 2]]);
        }

        if (!empty($vehicleTypeRank)) {
            $query->join('driver_ride_configs as dvc', 'drivers.id', '=', 'dvc.driver_id');
            $query->join('vehicle_types as vt', 'dv.vehicle_type_id', '=', 'vt.id');
            $query->where([['vt.vehicleTypeRank', '<=', $vehicleTypeRank], ['dvc.auto_upgradetion', '=', 1]]);
            $vehicle_type_id = null;
        }
        $query->where([
            ['drivers.free_busy', '=', 2],
            ['drivers.player_id', "!=", NULL],
            ['login_logout', '=', 1],
            ['drivers.online_offline', '=', 1],
            ['dv.vehicle_verification_status', '=', 2], // only verified vehicle
            ['drivers.driver_delete', '=', NULL]
        ])
            ->where(function($q) use($location_updated_last_time){
                if(!empty($location_updated_last_time))
                {
                 $q->where('last_location_update_time', '>=', $location_updated_last_time);
                }
            })
            ->whereRaw('( is_suspended is NULL or is_suspended < ? )', [date('Y-m-d H:i:s')])
            ->where(function ($q) use ($arr) {
                if ($arr['area_notifi'] == 2) {
                    if (!empty($base_areas)) {
                        array_push($base_areas, $arr['area']);
                        $q->whereIn('drivers.country_area_id', $arr['base_areas']);
                    } else {
                        $q->where('drivers.country_area_id', $arr['area']);
                    }
                }
            })
            ->where(function ($q) use ($vehicle_type_id, $manual_downgradation) {
                if (!empty($vehicle_type_id)) {
                    $q->where('dv.vehicle_type_id', $vehicle_type_id);
                }
                if ($manual_downgradation) {
                    $q->join('driver_downgraded_vehicle_types as ddvt');
                    $q->orWhere('ddvt.vehicle_type_id', $vehicle_type_id);
                }
            })
            ->where(function ($query) use ($arr) {
                if ($arr['ac_nonac'] == 1) {
                    return $query->where('dv.ac_nonac', $arr['ac_nonac']);
                }
            })
            ->where(function ($query) use ($arr) {
                if (!empty($user_gender) && $arr['merchantGender'] == 1) {
                    return $query->where('drivers.driver_gender', $arr['user_gender']);
                }
            })
            ->where(function ($query) use ($arr) {
                if ($arr['wheel_chair'] == 1) {
                    return $query->where('dv.wheel_chair', $arr['wheel_chair']);
                }
            })
            ->where(function ($query) use ($arr) {
                if (!empty($arr['driver_ids'])) {
                    return $query->whereIn('drivers.id', $arr['driver_ids']);
                }
            })
            ->where(function ($query) use ($arr) {
                if ($arr['baby_seat'] == 1) {
                    return $query->where('dv.baby_seat', $arr['baby_seat']);
                }
            })
            // commenting for development purpose
            ->whereNOTIn('drivers.id', function ($query) use ($arr) {
                $query->select('brd.driver_id')
                    ->from('booking_request_drivers as brd')
                    ->join('bookings as b', 'brd.booking_id', '=', 'b.id')
                    ->where('b.booking_status', '=', 1001)
                    ->where(function ($p) use ($arr) {
                        $p->where([['brd.booking_id', $arr['bookingId']], ['brd.request_status', 3]])->orWhere([['brd.request_status', 1]]);
                    });
            })
            ->whereExists(function ($query) {
                $query->select("ddv.driver_id")
                    ->from('driver_driver_vehicle as ddv')
                    ->join('driver_vehicles as dv', 'ddv.driver_vehicle_id', '=', 'dv.id')
                    ->where('ddv.vehicle_active_status', 1)
                    ->whereRaw('ddv.driver_id = drivers.id');
            });
//            ->having('distance', '<', $distance)
//            ->orderBy('distance')
//            ->take($limit);
        $drivers = $query->get();

        if ($drivers->count() >0) {
            $merchant_id = $arr['merchant_id'];
            $php_server_drivers = $drivers; // temp variable
            $drivers = $drivers->toArray();
            $arr_driver_id = array_column($drivers, 'ats_id');
            $arr_param['arr_driver_id'] = $arr_driver_id;
            $time_span = $arr['minute']; // minutes
            if ($time_span == 0) {
                $time_span = 60; // minutes
            }
            $time_span = $time_span*60;
            $arr_param = ['radius' => $arr['distance'], 'limit' => $arr['limit'], 'latitude' => $arr['latitude'], 'longitude' => $arr['longitude'], 'ats_ids' => $arr_driver_id, 'timespan' => $time_span];
            $drivers = Driver::getFinalDrivers($php_server_drivers, $arr_param);
        }
        return $drivers;
    }

    public static function getFinalDrivers($php_server_drivers, $arr_param)
    {
        if (!empty($php_server_drivers)) {
            $request_log = ['request' => $arr_param, 'time' => date('Y-m-d H:i:s')];
            \Log::channel('node_driver')->emergency($request_log);
            $arr_driver_latlong = Driver::getDriverLatLong($arr_param);
            $response_log = ['response' => $php_server_drivers, 'time' => date('Y-m-d H:i:s')];
            \Log::channel('node_driver')->emergency($response_log);
            // identifier = driver_id
            if ($arr_driver_latlong['result'] == 1) {
                $arr_driver_latlong = array_column($arr_driver_latlong['response']['result'], null, "ats_id");
                $searched_driver = array_keys($arr_driver_latlong);
                $php_server_drivers = $php_server_drivers->whereIn('ats_id', $searched_driver);
                if (!empty($arr_driver_latlong)) {
                    foreach ($php_server_drivers as $key => $driver) {
                        if (in_array($driver->ats_id, $searched_driver)) {
                            $php_server_drivers[$key]->current_latitude = $arr_driver_latlong[$driver->ats_id]['lat'];
                            $php_server_drivers[$key]->current_longitude = $arr_driver_latlong[$driver->ats_id]['lng'];
                        }
                    }
                    $response_log = ['return_driver' => $php_server_drivers, 'time' => date('Y-m-d H:i:s')];
                    \Log::channel('node_driver')->emergency($response_log);
                    $php_server_drivers = collect($php_server_drivers->values());
                    return $php_server_drivers;
                }
            }
        }
        return [];
    }

    public static function getDriverLatLong($data)
    {
//        {"radius":1000,"location":{"latitude":28.4123743,"longitude":77.0440803},"filter_by_extra_data":[289]}
        $payload = json_encode($data);
// Prepare new cURL resource
//        $ch = curl_init('http://68.183.85.170:3027/api/v1/ats/getNearByAtsIds');
        $ch = curl_init('http://68.183.85.170:3040/api/v1/ats/getNearByAtsIds');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

// Set HTTP Header for POST request
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload))
        );

// Submit the POST request
        $result = curl_exec($ch);
//        p($result);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            return [];
        } else {
            return json_decode($result, true);
        }
// Close cURL session handle
        curl_close($ch);
    }
}