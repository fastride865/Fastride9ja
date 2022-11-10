<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];

    //protected $hidden = ['User'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->merchant_booking_id = $model->NewBookigId($model->merchant_id);
            return $model;
        });
    }

    public function NewBookigId($merchantID)
    {
        $booking = Booking::where([['merchant_id', '=', $merchantID]])->orderBy('id','DESC')->first();
        if (!empty($booking)) {
            return $booking->merchant_booking_id + 1;
        } else {
            return 1;
        }
    }

    public function OutStanding()
    {
        return $this->hasOne(OutStanding :: class);
    }
    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function SosRequest()
    {
        return $this->hasMany(SosRequest::class);
    }

    public function Chat()
    {
        return $this->hasMany(Chat::class);
    }

    public function DriverVehicle()
    {
        return $this->belongsTo(DriverVehicle::class);
    }

    public function ServicePackage()
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function PaymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }

    public function ServiceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function VehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function BookingRequestDriver()
    {
        return $this->hasMany(BookingRequestDriver::class);
    }

    public function PriceCard()
    {
        return $this->belongsTo(PriceCard::class);
    }

    public function CancelReason()
    {
        return $this->belongsTo(CancelReason::class);
    }

    public function BookingTransaction()
    {
        return $this->hasOne(BookingTransaction::class);
    }

    public function BookingDetail()
    {
        return $this->hasOne(BookingDetail::class);
    }
    public function BookingRating()
    {
        return $this->hasOne(BookingRating::class);
    }

    public function PromoCode()
    {
        return $this->belongsTo(PromoCode::class, 'promo_code');
    }

    public static function VehicleDetail($booking)
    {
        //$booking = Booking::with('VehicleType', 'DriverVehicle')->find($booking_id);
        $newObj = array();
        if (!empty($booking)) {
            $image= isset($booking->VehicleType) ? $booking->VehicleType->vehicleTypeImage : $booking->DriverVehicle->VehicleType->vehicleTypeImage;
            $newObj['service'] = ($booking->ServiceType) ?$booking->ServiceType->ServiceName($booking->merchant_id) :  $booking->ServiceType->serviceName;
            $newObj['vehicle'] = isset($booking->VehicleType) ? $booking->VehicleType->VehicleTypeName : $booking->DriverVehicle->VehicleType->VehicleTypeName;
            $newObj['vehicleTypeImage'] = get_image($image,'vehicle',$booking->merchant_id);
            $newObj['vehicle_number'] = $booking->DriverVehicle ? $booking->DriverVehicle->vehicle_number : "";
            $newObj['vehicle_color'] = $booking->DriverVehicle ? $booking->DriverVehicle->vehicle_color : "";;
            $newObj['vehicle_image'] = $booking->DriverVehicle ? get_image($booking->DriverVehicle->vehicle_image,'vehicle_document',$booking->merchant_id) : "";
            $newObj['vehicle_make'] = $booking->DriverVehicle ? $booking->DriverVehicle->VehicleMake->VehicleMakeName : "";
            $newObj['vehicle_model'] = $booking->DriverVehicle ? $booking->DriverVehicle->VehicleModel->VehicleModelName : "";
        }
        return $newObj;
    }

    public static function UpcomingBookings($area, $latitude, $longitude, $vehicle_type_id, $service_type_id, $distance, $driver_id = null, $driver_area_notification = 2)
    {
        $bookings = [];
        if(!empty($area) && !empty($latitude) && !empty($longitude) && !empty($vehicle_type_id) && !empty($service_type_id) && !empty($distance) && !empty($driver_id))
        {
            $bookings = Booking::select(DB::raw('*,( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( pickup_latitude ) ) * cos( radians( pickup_longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( pickup_latitude ) ) ) ) AS distance'))
//                ->where(DB::raw('CONCAT_WS(" ", later_booking_date, later_booking_time)'), '>=', date('Y-m-d H:i'))
//                ->where([['later_booking_date', '>=', date('Y-m-d')], ['country_area_id', '=', $area], ['vehicle_type_id', '=', $vehicle_type_id], ['booking_status', '=', 1001], ['booking_type', '=', 2]])
                ->where([['vehicle_type_id', '=', $vehicle_type_id], ['booking_status', '=', 1001], ['booking_type', '=', 2]])
                ->where(function($q) use($driver_id, $driver_area_notification, $area){
                    $q->where('driver_id', null);
                    if(!empty($driver_id)){
                        $q->orwhere('driver_id', $driver_id);
                    }
                    if ($driver_area_notification == 2) {
                        $q->where('country_area_id', $area);
                    }
                })
                ->having('distance', '<', $distance)
                ->where('booking_status', 1001)
                ->whereIn("service_type_id", $service_type_id)
                ->whereNotIn('id', function ($query) use ($driver_id) {
                    $query->select('booking_id')->where('driver_id', $driver_id)->from('driver_cancel_bookings');
                })
                ->get();
            }
        return $bookings;
    }

    // Code merged by @Amba

    public function packages()
    {
        return $this->hasMany(BookingPackage :: class);
    }

    public function BookingCoordinate()
    {
        return $this->hasOne(BookingCoordinate::class);
    }
    public function OneSignalLog()
    {
        return $this->hasOne(OneSignalLog::class);
    }
    public function FamilyMember()
    {
        return $this->belongsTo(FamilyMember::class);
    }

    public function DeliveryPackage(){
        return $this->hasMany(DeliveryPackage::class);
    }

    public function Segment()
    {
        return $this->belongsTo(Segment::class);
    }

    public function UserCard()
    {
        return $this->belongsTo(UserCard::class,'card_id');
    }

    public function getBooking($booking_id)
    {
        $booking = Booking::select('id','user_id','final_amount_paid','family_member_id','booking_status_history','estimate_driver_distnace','estimate_distance','travel_distance','merchant_booking_id','booking_status','vehicle_type_id','driver_vehicle_id','price_card_id','ride_otp','ride_otp_verify','total_drop_location','booking_type','ploy_points','payment_method_id','vehicle_type_id','driver_id','merchant_id','segment_id','pickup_location','country_area_id','driver_id','user_id','pickup_latitude','pickup_longitude','service_type_id','additional_notes','family_member_id','onride_waiting_type','waypoints','drop_latitude','drop_longitude','drop_location','payment_status','estimate_bill','travel_time','country_area_id','booking_closure','later_booking_date','later_booking_time','unique_id','map_image','platform','user_masked_number','driver_masked_number')
            ->with(['User' => function ($query) {
                $query->select('id','country_id', 'merchant_id', 'first_name', 'last_name', 'UserPhone', 'email', 'rating', 'UserProfileImage','rating');
            }])
            ->with(['Driver' => function ($query) {
                $query->select('id', 'first_name','current_latitude','country_area_id','current_longitude','last_location_update_time','driver_gender', 'last_name', 'email', 'phoneNumber', 'profile_image', 'rating','ats_id');
            }])
            ->with(['PaymentMethod' => function ($query) {
                $query->select('id', 'payment_method', 'payment_icon');
            }])
            ->with(['VehicleType' => function ($query) {
                $query->select('id', 'vehicleTypeImage','rating');
            }])
            ->with(['ServiceType' => function ($query) {
                $query->select('id', 'serviceName','type');
            }])
            ->orderBy('created_at','DESC')
            ->find($booking_id);
        return $booking;
    }

    public function getDriverOngoingBookings($request)
    {
        $driver_id = $request->user('api-driver')->id;
        $bookings = Booking::select('id','merchant_booking_id','booking_timestamp','estimate_bill','booking_status','booking_type','payment_method_id','vehicle_type_id','driver_id','merchant_id','segment_id','pickup_location','user_id','pickup_latitude','pickup_longitude','service_type_id','drop_latitude','drop_longitude','drop_location')
            ->with(['User' => function ($query) {
                $query->select('id', 'merchant_id','country_id', 'first_name', 'last_name', 'UserPhone', 'email', 'rating', 'UserProfileImage');
            }])
            ->with(['PaymentMethod' => function ($query) {
                $query->select('id', 'payment_method', 'payment_icon');
            }])
            ->with(['ServiceType' => function ($query) {
                $query->select('id', 'serviceName');
            }])
            ->whereIn('booking_status', array(1002, 1003, 1004,1005))
            ->where('booking_closure',NULL)
            ->where('driver_id',$driver_id)
            ->orderBy('created_at','DESC')
            ->get();
        return $bookings;
    }

    public function getDriverBooking($request)
    {
        $driver_id = $request->user('api-driver')->id;
        $request_type = $request->request_type;
        $query = Booking::select('id','driver_id','user_id','later_booking_date','later_booking_time','drop_latitude','drop_longitude','drop_location','pickup_latitude','pickup_longitude','pickup_location','merchant_booking_id','merchant_id','segment_id','service_package_id','service_type_id','vehicle_type_id','driver_vehicle_id','booking_timestamp','booking_status')
            ->with('BookingDetail')
            ->where([['driver_id', '=', $driver_id]])
            ->orderBy('created_at','DESC')
        ;
        if($request_type == "PAST")
        {
            $query->where(function ($q){
                $q->whereIn('booking_status', array(1006, 1007, 1008,1018));
                $q->orWhere([['booking_status','=', 1005],['booking_closure','=',1]]);
            });
            if(isset($request->segment_id)){
                $query->whereHas('Segment',function($q)use($request){
                    $q->where('id',$request->segment_id);
                });
            }
            $bookings = $query->latest()->paginate(10);
        }
        elseif($request_type == "ACTIVE")
        {
            $query->whereIn('booking_status', array(1002, 1003, 1004,1005));
            $query->where('booking_closure',NULL);
            $query->orderBy('created_at','DESC');
            $bookings = $query->get();
        }
        elseif($request_type == "SCHEDULE")
        {
            $query->whereIn('booking_status', array(1012));
            $query->where('booking_type', 2);
            $bookings = $query->latest()->paginate(10);
        }

        return $bookings;
    }

    public function getUserBooking($request)
    {
        $user_id = $request->user('api')->id;
        $segment_id = $request->segment_id;
        $request_type = $request->request_type;
        $query = Booking::select('id','map_image','estimate_bill','country_area_id','driver_id','user_id','later_booking_date','later_booking_time','drop_latitude','drop_longitude','drop_location','pickup_latitude','pickup_longitude','pickup_location','merchant_booking_id','merchant_id','segment_id','service_package_id','service_type_id','vehicle_type_id','driver_vehicle_id','booking_timestamp','booking_status','payment_method_id','created_at')
            ->with('BookingDetail')
            ->where([['user_id', '=', $user_id],['segment_id', '=', $segment_id]])
            ->orderBy('created_at','DESC');
        if($request_type == "PAST")
        {
            $query->where(function($q) {
                $q->whereIn('booking_status', array(1006, 1007, 1008,1018));
                $q->orWhere(function ($qq){
                    $qq->where('booking_status', array(1005));
                    $qq->where('booking_closure', 1);
                });
            });
        }
        elseif($request_type == "ACTIVE")
        {
            $query->where(function($q) {
                $q->whereIn('booking_status', array(1002,1003,1004,1005,1001,1012));
                $q->where('booking_closure',NULL);
            });
            // $bookings = $query->get();
        }
        $bookings = $query->latest()->paginate(10);
//         elseif($request_type == "SCHEDULE")
//         {
// //            1002,
//             $query->whereIn('booking_status', array(1012));
//             $query->where('booking_type', 2);
//             $bookings = $query->latest()->paginate(10);
//         }
        return $bookings;
    }

    public function getBookingBasicData($request)
    {
        $booking = Booking::select('id','merchant_id','segment_id','user_id','driver_id','country_area_id','booking_status','payment_method_id','payment_status')
            ->with(['Segment'=>function($q){
                $q->addSelect('id','slag','name','icon');
                $q->with(['Merchant'=>function($q){
                }]);
            }])
            ->find($request->booking_id);
        return $booking;
    }

//    public function BookingDeliveryDetails()
//    {
//        return $this->hasMany(BookingDeliveryDetails::class);
//    }



    // Note : it has hasMany relation
    //it has many relation
    public function BookingDeliveryDetail()
    {
        return $this->hasMany(BookingDeliveryDetails::class,'booking_id')->orderBy('stop_no');
    }

   // it get single stop
    public function BookingDeliveryDetails()
    {
        return $this->hasOne(BookingDeliveryDetails::class,'booking_id')->orderBy('stop_no');
    }
}
