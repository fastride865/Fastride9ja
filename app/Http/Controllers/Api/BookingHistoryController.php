<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helper\HolderController;
use App\Models\ApplicationConfiguration;
use App\Models\Booking;
use App\Models\BookingConfiguration;
use App\Models\DriverVehicle;
use App\Models\Merchant;
use App\Models\FamilyMember;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Helper\CommonController;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Traits\DriverTrait;

class BookingHistoryController extends Controller
{
    ////user bookings
    use ApiResponseTrait, DriverTrait, MerchantTrait;

    // user active bookings ActiveHistoryBookings
    public function userHistoryBookings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'segment_id' => 'required',
            'request_type' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $user = $request->user('api');
        $merchant_id = $user->merchant_id;
        $string_file = $this->getStringFile(NULL, $user->Merchant);
        $booking_obj = new Booking;
//        $request->request->add(['request_type'=>'ACTIVE']);
        $bookings = $booking_obj->getUserBooking($request);

//        $bookings = Booking::where([['segment_id','=',$request->segment_id]])
//            ->with('ServiceType', 'Driver', 'PaymentMethod','ServicePackage')
//            ->whereIn('booking_status', array(1001, 1012, 1002, 1003, 1004))->where([['user_id', '=', $user_id]])
//            ->latest()->paginate(10);
        $arr = $bookings;
        $newArray = $arr->toArray();
        if (empty($newArray['data'])) {
            return $this->failedResponse(trans("$string_file.data_not_found"));
        }
//        $merchant = Merchant::find($request->user('api')->merchant_id);
//        $newMerchant = new \App\Http\Controllers\Helper\Merchant();
//        $services = $newMerchant->ServicesType($merchant);
//        $delivery_types = $newMerchant->DeliveryTypes($merchant);
        $data = array();
        foreach ($bookings as $value) {

            $service_type_name = $value->ServiceType->ServiceName($value->merchant_id);
            $package_name = $value->ServiceType->type == 2 ? isset($value->ServicePackage->PackageName) ? ' (' . $value->ServicePackage->PackageName . ')' : '' : '';
            $serviceName = $service_type_name . ' ' . $package_name;
//                implode(',', array_pluck($serviceType, 'serviceName'));
            $driver = $value->Driver;
            $driver_block_visibility = false;
            $driver_image = "";
            $driver_name = "";
            $driver_rating = "";
            if (!empty($driver->id)) {
                $driver_image = get_image($driver->profile_image, 'driver', $user->merchant_id, true, false);
                $driver_name = $driver->first_name . ' ' . $driver->last_name;
                $driver_rating = $driver->rating;
                $driver_block_visibility = true;
            }
            $id = $value['id'];
            $date = $value['created_at'];

            $pick_text = $value['pickup_location'];
            $drop_location = $value['drop_location'];
            if($value['booking_status'] == "1005" && isset($value->BookingDetail) && !empty(isset($value->BookingDetail))){
                $pick_text = $value->BookingDetail->start_location;
                $drop_location = $value->BookingDetail->end_location;
            }
            $booking_text = CommonController::BookingStatus($value['booking_status'], $string_file);
            $data[] = array(
                'booking_id' => $id,
                'merchant_booking_id' => $value['merchant_booking_id'],
                'booking_type' => $value['booking_type'],
                'highlighted_text' => $serviceName . " #" . $value['merchant_booking_id'],
                'highlighted_left_text_color' => $service_type_name,//implode(',', array_pluck($serviceType, 'color')),
                'small_text' => date('Y-m-d', strtotime($date)),
                "highlighted_small_text" => "ID #" . $value['merchant_booking_id'] . ' ' . date('Y-m-d', strtotime($date)),
                'pick_visibility' => true,
                'estimate_bill' => $value['estimate_bill'],
                'pick_text' => $pick_text,
                'drop_visibility' => true,
                'drop_location' => $drop_location,
                'driver_block_visibility' => $driver_block_visibility,
                "driver_image" => $driver_image,
                "driver_name" => $driver_name,
                "driver_rating" => $driver_rating,
                "status_text" => $booking_text,
//                "value_text" => $value['PaymentMethod']['payment_method'],
                "value_text" => $value->PaymentMethod->MethodName($value->merchant_id) ? $value->PaymentMethod->MethodName($value->merchant_id) : $value->PaymentMethod->payment_method,
                "value_color" => "3ecc71",
                'status_color' => "333333",
                'map_image_visibility' => true,
                'currency' => $value->CountryArea->Country->isoCode,
                "value_text_color" => "2ecc71",
                "circular_image" => get_image($value['vehicleType']['vehicleTypeImage'], 'vehicle', $value['merchant_id'], true, false),
                "vehicle_type" => $value->VehicleType->VehicleTypeName,
                'map_image' => $value->map_image,
                'payment_method' => $value->PaymentMethod->MethodName($value->merchant_id) ? $value->PaymentMethod->MethodName($value->merchant_id) : $value->PaymentMethod->payment_method,
                "service_package" => $serviceName . $package_name,
            );
        }
        $arr_return['booking_data'] = $data;
        $next_page_url = isset($newArray['next_page_url']) ? $newArray['next_page_url'] : "";
        $arr_return['extra_data'] = ['next_url' => $next_page_url];

        $message = trans("$string_file.rides") . ' ' . $message = trans("$string_file.history");
        return $this->successResponse($message, $arr_return);
    }

    // user active booking
    public function ActiveBookings(Request $request)
    {
        $user = $request->user('api');
        $user_id = $user->id;
        $string_file = $this->getStringFile(null,$user->Merchant);
        switch ((int)$request->type == 1) {
            case 1:
                $bookings = Booking::select('id', 'merchant_booking_id', 'booking_status', 'booking_timestamp', 'payment_status', 'booking_closure')
                    ->whereIn('booking_status', array(1002, 1003, 1004, 1005))
                    ->where([['user_id', '=', $user_id], ['payment_status', '=', 0]])
                    ->orWhere([['booking_status', '=', 1001], ['booking_type', '=', 1], ['user_id', '=', $user_id]])
                    ->get();
                break;

            case 2:
                $bookings = Booking::select('id', 'merchant_booking_id', 'booking_status', 'booking_timestamp', 'payment_status', 'booking_closure')
                    ->whereIn('booking_status', array(1002, 1003, 1004, 1005))
                    ->where([['delivery_type_id', '!=', null], ['delivery_type_id', '!=', 0]])
                    ->where([['user_id', '=', $user_id], ['payment_status', '=', 0]])
                    ->orWhere([['booking_status', '=', 1001], ['booking_type', '=', 1], ['user_id', '=', $user_id]])
                    ->get();
                break;

            default:
                $bookings = Booking::select('id', 'merchant_booking_id', 'booking_status', 'booking_timestamp', 'payment_status', 'booking_closure')
                    ->whereIn('booking_status', array(1002, 1003, 1004, 1005))
                    ->where([['user_id', '=', $user_id], ['payment_status', '=', 0]])
                    ->orWhere([['booking_status', '=', 1001], ['booking_type', '=', 1], ['user_id', '=', $user_id]])
                    ->get();
                break;
        }

        if (empty($bookings->toArray())) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.no_active_ride"), 'data' => []]);
        }
        $bookings = $bookings->toArray();
        foreach ($bookings as $key => $value) {
            $booking_status = $value['booking_status'];
            if ($booking_status == 1001) {
                $booking_timestamp = $value['booking_timestamp'];
                $current = time();
                $diffrence = $current - $booking_timestamp;
                if ($diffrence < 60) {
                    $bookings[$key] = $value;
                } else {
                    $booking = Booking::find($value['id']);
                    $booking->booking_status = 1016;
                    $booking->save();
                    unset($bookings[$key]);
                }
            } else {
                $bookings[$key] = $value;
            }
        }
        if (empty($bookings)) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.no_active_ride"), 'data' => []]);
        }
        return response()->json(['result' => "1", 'message' => trans("$string_file.success"), 'bookable' => false, 'data' => $bookings]);
    }

    // booking details for user
    public function BookingDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer|exists:bookings,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $booking = Booking::with('CountryArea', 'VehicleType', 'Driver', 'BookingDetail', 'PaymentMethod', 'BookingRating')->find($request->booking_id);
        $string_file = $this->getStringFile(NULL, $booking->Merchant);
        $family_member_id = $booking->family_member_id;
        $family_member_name = "";
        $family_member_phoneNumber = "";
        $family_member_age = "";
        $family_visibility = false;
        if ($family_member_id != "") {
            $family_det = FamilyMember::find($family_member_id);
            $family_visibility = true;
            $family_member_name = $family_det->name;
            $family_member_phoneNumber = $family_det->phoneNumber;
            $family_member_age = $family_det->age;
        }
        $vehicleTypeName = $booking->VehicleType->LanguageVehicleTypeSingle == "" ? $booking->VehicleType->LanguageVehicleTypeAny->vehicleTypeName : $booking->VehicleType->LanguageVehicleTypeSingle->vehicleTypeName;
        $currency = $booking->CountryArea->Country->isoCode;
        $timeZone = $booking->CountryArea->timezone;
        $start_location = $booking->pickup_location;
        $end_location = $booking->drop_location;
        if (!empty($booking->BookingDetail)) {
            $start_location = $booking->BookingDetail->start_location;
            $start_location = $start_location == "" ? $booking->pickup_location : $start_location;
            $end_location = $booking->BookingDetail->end_location;
            $end_location = $end_location == "" ? $booking->drop_location : $end_location;
        }
        $appConfig = ApplicationConfiguration::select('favourite_driver_module', 'vehicle_rating_enable')->where([['merchant_id', '=', $booking['merchant_id']]])->first();
        $number = $booking->DriverVehicle ? $booking->DriverVehicle->vehicle_color : "";
        $holder_driver_vehicle_rating = array(
            'visibility' => $appConfig->vehicle_rating_enable == 1 ? true : false,
            'vehicle_data' => array(
                "booking_id" => $request->booking_id,
                "text" => $booking->VehicleType->VehicleTypeName . '( ' . $number . ' )',
                "image" => get_image($booking->VehicleType->vehicleTypeImage, 'vehicle', $booking->merchant_id, true, false),
            )
        );
        $booking = $booking->toArray();
        $booking_closure = $booking['booking_closure'];
        $booking_status = $booking['booking_status'];
        $holder_metering_visibility = false;
        $amount = $booking['estimate_bill'];

        switch ($booking_status) {
            case "1001":
                $track = false;
                $cancel = true;
                $mail_invoice = false;
                $support = true;
                break;
            case "1012":
                $track = false;
                $cancel = true;
                $mail_invoice = false;
                $support = true;
                break;
            case "1002":
                $track = true;
                $cancel = true;
                $mail_invoice = false;
                $support = true;
                break;
            case "1003":
                $track = true;
                $cancel = true;
                $mail_invoice = false;
                $support = true;
                break;
            case "1004":
                $track = true;
                $cancel = false;
                $mail_invoice = false;
                $support = true;
                break;
            case "1005":
                if ($booking_closure == 1) {
                    $mail_invoice = true;
                    $amount = $booking['final_amount_paid'];
                } else {
                    $mail_invoice = false;
                }
                $track = false;
                $cancel = false;
                $support = true;
                $holder_metering_visibility = true;
                break;
            default:
                $track = false;
                $cancel = false;
                $mail_invoice = false;
                $support = false;
        }
        $holder = array();
        $holder_receipt_visibility = false;
        if (!empty($booking['booking_detail'])) {
            if (!empty($booking['booking_detail']['bill_details'])) {
                $price = json_decode($booking['booking_detail']['bill_details']);
                $holder = HolderController::PriceDetailHolder($price, $request->booking_id);
                $holder_receipt_visibility = true;
            }
        }
        if (!empty($booking['driver'])) {
            $rating_button_visibility = false;
            $rating_button_enable = false;
            $rating_visibility = false;
            $rating_button_text = trans("$string_file.rate_driver");
            if (!empty($booking['booking_rating']) && !empty($booking['booking_rating']['user_rating_points'])) {
                $user_rating_points = $booking['booking_rating']['user_rating_points'];
                $rating_button_visibility = true;
                $rating_visibility = true;
                $rating_button_text = trans("$string_file.rated");
            } else {
                if ($booking_status == 1005) {
                    $rating_button_visibility = $appConfig->favourite_driver_module == 1 ? true : false;
                    $rating_button_enable = $appConfig->favourite_driver_module == 1 ? true : false;
                }
                $user_rating_points = "0";
            }
            $driver_visibility = true;
            $driver = array(
                "circular_image" => $booking['driver']['profile_image'],
                "highlighted_text" => $booking['driver']['first_name'] . ' ' . $booking['driver']['last_name'],
                "small_text" => "",
                "small_taxt_phone" => "",
                "rating_visibility" => $rating_visibility,
                "rating" => $user_rating_points,
                "rating_button_visibility" => $rating_button_visibility,
                "rating_button_enable" => $rating_button_enable,
                "rating_button_text" => $rating_button_text,
                "rating_button_text_color" => $user_rating_points,
                "rating_button_text_style" => "BOLD"
            );
        } else {
            $driver_visibility = false;
            $driver = array(
                "circular_image" => "",
                "highlighted_text" => "",
                "small_text" => "",
                "rating_visibility" => true,
                "rating" => "",
                "rating_button_visibility" => true,
                "rating_button_enable" => false,
                "rating_button_text" => "",
                "rating_button_text_color" => "",
                "rating_button_text_style" => ""
            );;
        }

        $dt = new DateTime($booking['created_at']);
        $dt->setTimeZone(new DateTimeZone($timeZone));
        if ($booking['booking_type'] == 1) {
            $bookingTime = $dt->format('Y-m-d H:i:s');
        } else {
            $bookingTime = $booking['later_booking_date'] . " " . $booking['later_booking_time'];
        }

        $multi_drop = [];
        $arr_drop = json_decode($booking['waypoints'], true);
        if(!empty($arr_drop) && count($arr_drop) > 0)
        {
//                $last_key = array_keys($arr_drop);
//                $last_key = end($last_key);
//              unset($arr_drop[$last_key]);
            $multi_drop = $arr_drop;
        }
        $data = array(
            'holder_map_image' => array(
                'visibility' => true,
                'data' => array(
                    'map_image' => $booking['map_image'],
                )
            ),
            "holder_family_member" => array(
                "visibility" => $family_visibility,
                "name" => $family_member_name,
                "phoneNumber" => $family_member_phoneNumber,
                "age" => $family_member_age,
            ),
            'holder_booking_description' =>
                array("visibility" => true,
                    "data" => array(
                        "highlighted_left_text" => $bookingTime,
                        "highlighted_left_text_style" => "BOLD",
                        "highlighted_left_text_color" => "#333333",
                        "small_left_text" => $vehicleTypeName,
                        "small_left_text_style" => "",
                        "small_left_text_color" => "bbbbbb",
                        "highlighted_right_text" => $currency . " " . $amount,
                        "highlighted_right_text_style" => "",
                        "highlighted_right_text_color" => "#333333",
                        "small_right_text" => "",
                        "small_right_text_style" => "BOL",
                        "small_right_text_color" => "e74c3c"
                    )),
            "holder_pickdrop_location" => array(
                "visibility" => true,
                "data" => array(
                    "pick_text_visibility" => true,
                    "pick_text" => $start_location,
                    "drop_text_visibility" => true,
                    "drop_text" => $end_location
                )
            ),
            "holder_metering" => array(
                "visibility" => $holder_metering_visibility,
                "data" => array(
                    "text_one" => $currency . " " . $booking['final_amount_paid'] ? $booking['final_amount_paid'] : $booking['estimate_bill'],
                    "text_two" => $booking['travel_distance'],
                    "text_three" => $booking['travel_time'],
                )
            ),
            "holder_driver" => array(
                "visibility" => $driver_visibility,
                "data" => $driver,
            ),
            'holder_receipt' => array('visibility' => $holder_receipt_visibility, 'data' => $holder),
            'holder_driver_vehicle_rating' => $holder_driver_vehicle_rating,
            "button_visibility" => array(
                "track" => $track,
                "cancel" => $cancel,
                "mail_invoice" => $mail_invoice,
                "support" => $support,
                "coupon" => false
            ),
            "middle_drop" => $multi_drop

        );
        return response()->json(['result' => "1", 'message' => trans("$string_file.success"), 'bookable' => true, 'data'
        => $data]);
    }


    public function DriverScheduleHistory(Request $request)
    {
        $driver = $request->user('api-driver');
        $driver_id = $driver->id;
        $string_file = $this->getStringFile(NULL, $driver->Merchant);
        $bookings = Booking::with('VehicleType', 'ServiceType', 'PaymentMethod', 'User', 'Package')
            ->where([['driver_id', '=', $driver_id], ['booking_status', '=', 1012]])->latest()->paginate(10);
        $newArray = $bookings->toArray();
        if (empty($newArray['data'])) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }
        $data = array();
        $merchant = Merchant::find($request->user('api-driver')->merchant_id);
        $newMerchant = new \App\Http\Controllers\Helper\Merchant();
        $services = $newMerchant->ServicesType($merchant);

        foreach ($bookings as $value) {
            $user = $value['user'];
            $service_id = $value['service_type_id'];
            $serviceType = $services->filter(function ($service) use ($service_id) {
                return $service->id == $service_id;
            });
            $payment_method = $value->PaymentMethod->MethodName($value->merchant_id) ? $value->PaymentMethod->MethodName($value->merchant_id) : $value->PaymentMethod->payment_method;
            $package_name = ($service_id == 2) ? isset($value->Package->PackageName) ? ' (' . $value->Package->PackageName . ')' : '' : '';
            $highlighted_left_text = implode(',', array_pluck($serviceType, 'serviceName')) . $package_name . " " . $value->VehicleType->VehicleTypeName;
            $small_left_text = $value->later_booking_date . " " . $value->later_booking_time;
            $booking_text = CommonController::BookingStatus($value['booking_status'], $string_file);
            $data[] = array(
                'booking_id' => $value['id'],
                'highlighted_left_text' => "#" . $value['merchant_booking_id'] . " " . $highlighted_left_text,
                'highlighted_left_text_style' => "BOLD",
                'highlighted_left_text_color' => implode(',', array_pluck($serviceType, 'color')),
                "small_left_text" => $small_left_text,
                "small_left_text_style" => "",
                "small_left_text_color" => "333333",
                "highlighted_right_text" => $payment_method,
                "highlighted_right_text_style" => "BOLD",
                "highlighted_right_text_color" => "27ae60",
                "small_right_text" => "",
                "small_right_text_style" => "BOLD",
                "small_right_text_color" => "bbbbbb",
                "pick_location" => $value['pickup_location'],
                "pick_location_visibility" => true,
                "drop_location" => $value['drop_location'],
                "drop_location_visibility" => true,
                "user_description_layout_visibility" => true,
                "circular_image" => get_image($user['UserProfileImage'], 'user', $value['merchant_id'], true, false),
                "user_name_text" => $user['UserName'],
                "user_descriptive_text" => $user['UserPhone'],
                "status_text" => $booking_text,
                "status_text_syle" => "BOLD",
                "status_text_color" => "333333"
            );
        }
        return response()->json(['result' => "1", 'message' => trans("$string_file.ride"), 'total_pages' =>
            $newArray['last_page'], 'current_page' => $newArray['current_page'], 'data' => $data]);

    }


    public function bookingData($bookings, $string_file = "")
    {
        $data = [];
//    $merchant_id = isset($bookings[0]->merchant_id) ? $bookings[0]->merchant_id : NULL;
//    $string_file = $this->getStringFile($merchant_id);
        $ride_string = trans("$string_file.ride");
        foreach ($bookings as $value) {
            $start_location = $value->pickup_location;
            $end_location = $value->drop_location;
            $start_lat = $value->pickup_latitude;
            $start_long = $value->pickup_longitude;
            $end_lat = $value->drop_latitude;
            $end_long = $value->drop_longitude;
            if (!empty($value->BookingDetail)) {
                $detail = $value->BookingDetail;
                $start_loc = $detail->start_location;
                $start_location = !empty($start_loc) ? $start_loc : $start_location;
                $end_loc = $detail->end_location;
                $end_location = !empty($end_loc) ? $end_loc : $end_location;
                $start_lat = !empty($detail->start_latitude) ? $detail->start_latitude : $start_lat;
                $start_long = !empty($detail->start_longitude) ? $detail->start_longitude : $start_long;
                $end_lat = !empty($detail->end_latitude) ? $detail->end_latitude : $end_lat;
                $end_long = !empty($detail->end_longitude) ? $detail->end_longitude : $end_long;
            }

            $package_name = "";
            if ($value['service_type_id']) {
                $package_name = ($value->service_type_id == 2) && isset($value->ServicePackage->PackageName) ? ' (' . $value->ServicePackage->PackageName . ')' : '';
            }
            $highlighted_left_text = $value->ServiceType->ServiceName($value->merchant_id) . ' ' . $package_name;
            if ($value->booking_status == 1002) {
                $booking_timestamp = $value->BookingDetail->accept_timestamp;
            } elseif ($value->booking_status == 1003) {
                $booking_timestamp = $value->BookingDetail->arrive_timestamp;
            } elseif ($value->booking_status == 1004) {
                $booking_timestamp = $value->BookingDetail->start_timestamp;
            } elseif ($value->booking_status == 1005) {
                $booking_timestamp = $value->BookingDetail->end_timestamp;
            } else {
                $booking_timestamp = $value->booking_timestamp;
            }

            $status_text = CommonController::DriverHistoryBookingStatus($value['booking_status'], $string_file);
            $merchant_segment = $value->Segment->Merchant->where('id', $value->merchant_id);
            $merchant_segment = collect($merchant_segment->values());
            $booking_details = [
                'id' => $value->id,
                'number' => $value->merchant_booking_id,
                'segment_slug' => $value->Segment->slag,
                'name' => $value->Segment->Name($value->merchant_id) . ' ' . $ride_string,
                'segment_image' => isset($merchant_segment[0]['pivot']->segment_icon) && !empty($merchant_segment[0]['pivot']->segment_icon) ? get_image($merchant_segment[0]['pivot']->segment_icon, 'segment', $value->merchant_id, true, false) :
                    get_image($value->Segment->icon, 'segment_super_admin', NULL, false, false),
                'status' => $value->booking_status,
                'status_text' => $status_text,
                'updated_timestamp' => $booking_timestamp,
                'status_description' => $highlighted_left_text,
                'timestamp' => strtotime($value->later_booking_date . ' ' . $value->later_booking_time),
            ];
            $pick_details = [
                'pick_image' => "",
                'pick_lat' => $start_lat,
                'pick_lng' => $start_long,
                'pick_address' => $start_location,

            ];
            $drop_details = [
                'drop_image' => "",
                'drop_lat' => $end_lat,
                'drop_lng' => $end_long,
                'drop_address' => $end_location,
            ];

            $booking_vehicle = [];
            if (!empty($value->driver_id)) {
                $booking_vehicle = Booking::VehicleDetail($value);
                $booking_vehicle = [$booking_vehicle];
            }
            $data[] = [
                'details' => $booking_details,
                'pick_details' => $pick_details,
                'drop_details' => $drop_details,
                'vehicle_details' => $booking_vehicle,
            ];
        }
        return $data;
    }

    // get past booking for driver
    public function getPastBooking(Request $request)
    {
        try {
            $booking_obj = new Booking;
            $request->request->add(['request_type' => 'PAST']);
            $bookings = $booking_obj->getDriverBooking($request);
            $driver = $request->user('api-driver');
            $string_file = $this->getStringFile(NULL, $driver->Merchant);

            if ($bookings->count() == 0) {

                throw new \Exception(trans("$string_file.no_past_rides"));
            }
            $data = $this->bookingData($bookings, $string_file);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        $newArray = $bookings->toArray();
        $next_page_url = $newArray['next_page_url'];
        $next_page_url = $next_page_url == "" ? "" : $next_page_url;
        $return_data = ['next_page_url' => $next_page_url, 'current_page' => $newArray['current_page'], 'response_data' => $data];
        return ['message' => trans("$string_file.success"), 'data' => $return_data];
    }

    // active booking for driver
    public function getActiveBooking(Request $request)
    {
        $driver = $request->user('api-driver');
        $string_file = $this->getStringFile(NULL, $driver->Merchant);
        try {
            $booking_obj = new Booking;
            $request->request->add(['request_type' => 'ACTIVE']);
            $bookings = $booking_obj->getDriverBooking($request);

            $data = $this->bookingData($bookings, $string_file);
        } catch (\Exception $e) {
            throw new \Exception(trans("$string_file.data_not_found"));
        }
        return $data;
    }


    public function getScheduleUpcomingBooking(Request $request)
    {
        $request_fields = [
            'request_type' => 'required|in:ALL,SCHEDULE,UPCOMING',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $schedule_data = [];
            $upcoming_data = [];
            $request_type = $request->request_type;
            $driver = $request->user('api-driver');
            $string_file = $this->getStringFile(NULL, $driver->Merchant);
            $booking_obj = new Booking;
            if ($request_type == "ALL" || $request_type == "SCHEDULE") {
                $request->request->add(['request_type' => 'SCHEDULE']);
                $schedule_bookings = $booking_obj->getDriverBooking($request);
                $schedule_data = $this->bookingData($schedule_bookings, $string_file);
            }
            // upcoming booking

            if ($request_type == "ALL" || $request_type == "UPCOMING") {
                $config = BookingConfiguration::select('normal_ride_later_request_type', 'normal_ride_later_radius')->where([['merchant_id', '=', $driver->merchant_id]])->first();
                // get driver's online work config
                $online_work_set = $this->getDriverOnlineConfig($driver, 'all');
                $driver_vehicle_id = $online_work_set['driver_vehicle_id'];
                $driver_vehicle_id = isset($driver_vehicle_id[0]) ? $driver_vehicle_id[0] : NULL;
                $service_type_id = $online_work_set['service_type_id'];
                $driver_vehicle = $driver->Vehicle->where('id', $driver_vehicle_id);
                $vehicle_type_id = NULL;
                foreach ($driver_vehicle as $vehicle) {
                    if (!empty($vehicle->id)) {
                        $vehicle_type_id = $vehicle->VehicleType->id;
                        break;
                    }
                }
                $driver_area_notification = isset($driver->Merchant->Configuration->driver_area_notification) ? $driver->Merchant->Configuration->driver_area_notification : 2;
                $upcoming_bookings = Booking::UpcomingBookings($driver->country_area_id, $driver->current_latitude, $driver->current_longitude, $vehicle_type_id, $service_type_id, $config->normal_ride_later_radius, $driver->id, $driver_area_notification);
                $upcoming_data = $this->bookingData($upcoming_bookings, $string_file);
            }
            $data = array_merge($schedule_data, $upcoming_data);
            if (empty($data)) {
                return $this->failedResponse(trans("$string_file.no_scheduled_rides"));
            }

        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
//            throw new \Exception(trans("$string_file.data_not_found"));
        }
        return $this->successResponse(trans("$string_file.data_found"), $data);
    }

    public function DriverBookingDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer|exists:bookings,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $booking = Booking::with('User', 'BookingDetail', 'BookingRating')->find($request->booking_id);
        $string_file = $this->getStringFile(NULL, $booking->Merchant);
        $family_member_id = $booking->family_member_id;
        $family_member_name = "";
        $family_member_phoneNumber = "";
        $family_member_age = "";
        $family_visibility = false;
        if ($family_member_id != "") {
            $family_det = FamilyMember::find($family_member_id);
            $family_visibility = true;
            $family_member_name = $family_det->name;
            $family_member_phoneNumber = $family_det->phoneNumber;
            $family_member_age = $family_det->age;
        }
        $config = BookingConfiguration::where([['merchant_id', '=', $booking->merchant_id]])->first();
        $vehicleTypeName = $booking->VehicleType->VehicleTypeName;
        $currency = $booking->CountryArea->Country->isoCode;
        $start_location = $booking->pickup_location;
        $end_location = $booking->drop_location;
        if (!empty($booking->BookingDetail)) {
            $start_location = $booking->BookingDetail->start_location;
            $start_location = $start_location == "" ? $booking->pickup_location : $start_location;
            $end_location = $booking->BookingDetail->end_location;
            $end_location = $end_location == "" ? $booking->drop_location : $end_location;
        }
        $booking_closure = $booking['booking_closure'];
        $booking_status = $booking['booking_status'];
        $holder_metering_visibility = false;
        if ($booking_status == '1012') {
            $today = new \DateTime(date('Y-m-d H:i:s'));
            $today = $today->format("Y-m-d H:i:s");
            $ride_later_cancel_hour = $config->ride_later_cancel_hour ? $config->ride_later_cancel_hour : 0;
            $bookingtimestamp = $booking['later_booking_date'] . " " . $booking->later_booking_time;
            $DateTime = new \DateTime($bookingtimestamp);
            $totmin = $ride_later_cancel_hour * 60;
            $min = $totmin % 60;
            $hour = explode('.', ($totmin / 60));

            if ($hour[0] != 0) {
                $str = $min != 0 ? "-{$hour[0]} hours -{$min} minutes" : "-{$hour[0]} hours";
            } else {
                $str = $min != 0 ? "-{$min} minutes" : "-0 minutes";
            }

            $DateTime->modify($str);
            $newDate = $DateTime->format("Y-m-d H:i:s");
            $cancel_button = $newDate > $today ? true : false;
            if ($config->ride_later_cancel_enable_in_cancel_hour == 1) {
                $cancel_button = true;
            }
        } else {
            $cancel_button = false;
        }
        switch ($booking_status) {
            case "1001":
                $visibility = true;
                $button_text = trans("$string_file.accept");
                $text_color = "ffffff";
                $text_back_ground = "e67e22";
                $action = "PARTIAL_ACCEPT_API";
                break;
            case "1012":
                $visibility = true;
                switch ($booking['service_type_id']) {
                    case "1":
                        $seconds = $config->normal_ride_later_time_before;
                        break;
                    case "2":
                        $seconds = $config->rental_ride_later_time_before;
                        break;
                    case "3":
                        $seconds = $config->transfer_ride_later_time_before;
                        break;
                    case "4":
                        $seconds = $config->outstation_time_before;
                        break;
                    default :
                        $seconds = $config->normal_ride_later_time_before;
                        break;
                }
                $today = new \DateTime(date('Y-m-d'));
                $expires = new \DateTime($booking['later_booking_date']);
                if ($today == $expires) {
                    $bookingTimestamp = strtotime($booking['later_booking_date'] . " " . $booking['later_booking_time']) - $seconds;
                    $currentTimestamp = time();
                    if ($bookingTimestamp <= $currentTimestamp) {
                        $button_text = trans("$string_file.start_to_pickup");
                        $action = "START_TO_PICK";
                    } else {
                        $hours = floor($seconds / 3600);
                        $minutes = ($seconds / 60 % 60);
                        $button_text = trans_choice("$string_file.minutes_ago", 3, ['hours' => $hours, 'min' => $minutes]);
                        $action = "NO_ACTION";
                    }
                } else {
                    $hours = floor($seconds / 3600);
                    $minutes = ($seconds / 60 % 60);
                    $button_text = trans_choice("$string_file.minutes_ago", 3, ['hours' => $hours, 'min' => $minutes]);
                    $action = "NO_ACTION";
                }
                $text_color = "ffffff";
                $text_back_ground = "2980b9";
                break;
            case "1002":
                $visibility = true;
                $button_text = trans("$string_file.track");
                $text_color = "ffffff";
                $text_back_ground = "2ecc71";
                $action = "OPEN_TRACKSCREEN";
                break;
            case "1003":
                $visibility = true;
                $button_text = trans("$string_file.track");
                $text_color = "ffffff";
                $text_back_ground = "e74c3c";
                $action = "OPEN_TRACKSCREEN";
                break;
            case "1004":
                $visibility = true;
                $button_text = trans("$string_file.track");
                $text_color = "ffffff";
                $text_back_ground = "e74c3c";
                $action = "OPEN_TRACKSCREEN";
                break;
            case "1005":
                if ($booking_closure == 1) {
                    $visibility = false;
                } else {
                    $visibility = true;
                }
                $button_text = trans("$string_file.track");
                $text_color = "ffffff";
                $text_back_ground = "e74c3c";
                $action = "OPEN_TRACKSCREEN";
                $holder_metering_visibility = true;
                break;
            default:
                $visibility = false;
                $button_text = trans("$string_file.track");
                $text_color = "ffffff";
                $text_back_ground = "e74c3c";
                $action = "OPEN_TRACKSCREEN";

        }

        $holder = array();
        $holder_receipt_visibility = false;
        if (!empty($booking->BookingDetail)) {
            if (!empty($booking->BookingDetail->bill_details)) {
                $price = json_decode($booking->BookingDetail->bill_details, true);
                $holder = HolderController::PriceDetailHolderArray($price, $request->booking_id);
                $holder_receipt_visibility = true;
            }
        }
        $date = $booking['booking_type'] == 1 ? $booking->created_at->toDateTimeString() : $booking['later_booking_date'] . " " . $booking['later_booking_time'];
        $rating_button_visibility = false;
        $rating_button_enable = false;
        $rating_visibility = false;
        $rating_button_text = trans("$string_file.rate_user");
        if (!empty($booking->BookingRating) && !empty($booking->BookingRating->driver_rating_points)) {
            $driver_rating_points = $booking->BookingRating->driver_rating_points;
            $rating_button_visibility = true;
            $rating_visibility = true;
            $rating_button_text = trans("$string_file.rating");
        } else {
            if ($booking_status == 1005) {
                $rating_button_visibility = true;
                $rating_button_enable = true;
            }
            $driver_rating_points = "0";
        }
        $payment = $booking['final_amount_paid'] == 0.00 ? $booking['estimate_bill'] : $booking['final_amount_paid'];
        $data = array(
            'holder_map_image' => array(
                'visibility' => true,
                'data' => array(
                    'map_image' => $booking['map_image'],
                )
            ),
            'holder_booking_description' =>
                array("visibility" => true,
                    "data" => array(
                        "highlighted_left_text" => $date,
                        "highlighted_left_text_style" => "BOLD",
                        "highlighted_left_text_color" => "333333",
                        "small_left_text" => $vehicleTypeName,
                        "small_left_text_style" => "",
                        "small_left_text_color" => "bbbbbb",
                        "highlighted_right_text" => $currency . " " . $payment,
                        "highlighted_right_text_style" => "",
                        "highlighted_right_text_color" => "333333",
                        "small_right_text" => "",
                        "small_right_text_style" => "BOL",
                        "small_right_text_color" => ""
                    )),
            "holder_pickdrop_location" => array(
                "visibility" => true,
                "data" => array(
                    "pick_text_visibility" => true,
                    "pick_text" => $start_location,
                    "drop_text_visibility" => true,
                    "drop_text" => $end_location
                )
            ),
            "holder_metering" => array(
                "visibility" => $holder_metering_visibility,
                "data" => array(
                    "text_one" => $currency . " " . $booking['final_amount_paid'],
                    "text_two" => $booking['travel_distance'],
                    "text_three" => $booking['travel_time'],
                )
            ),
            "holder_family_member" => array(
                "visibility" => $family_visibility,
                "name" => $family_member_name,
                "phoneNumber" => $family_member_phoneNumber,
                "age" => $family_member_age,
            ),
            "holder_user" => array(
                "visibility" => true,
                "data" => array(
                    "circular_image" => get_image($booking['user']['UserProfileImage'], 'user', $booking->merchant_id, true, false),
                    "highlighted_text" => $booking->User->UserName,
                    "small_text" => "",

                    "rating_visibility" => $rating_visibility,
                    "rating" => $driver_rating_points,
                    "rating_button_visibility" => $rating_button_visibility,
                    "rating_button_enable" => $rating_button_enable,
                    "rating_button_text" => $rating_button_text,
                    "rating_button_text_color" => $driver_rating_points,
                    "rating_button_text_style" => "BOLD"
                )
            ),
            'holder_receipt' => array(
                "visibility" => $holder_receipt_visibility,
                'data' => $holder
            ),
            "button_visibility" => array(
                "visibility" => $visibility,
                "button_text" => $button_text,
                "text_color" => $text_color,
                "text_back_ground" => $text_back_ground,
                "action" => $action
            ),
            "cancel_button_visibility" => array(
                "visibility" => $cancel_button,
                "button_text" => trans("$string_file.cancel"),
                "text_color" => $text_color,
                "text_back_ground" => $text_back_ground,
                "action" => 'CANCEL'
            )
        );
        return response()->json(['result' => "1", 'message' => trans("$string_file.success"), 'bookable' => true, 'data' => $data]);
    }


    // get  booking details of driver
    public function getBookingDetails(Request $request)
    {
        $data = [];

        $validator = Validator::make($request->all(), [
            'booking_order_id' => 'required|integer|exists:bookings,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            throw new \Exception($errors[0]);
            // return $this->failedResponse($errors[0]);
        }

        try {
            $booking_obj = new Booking;
            $booking_id = $request->id;
            $booking = $booking_obj->getBooking($booking_id);
            $string_file = $this->getStringFile(NULL, $booking->Merchant);
            $timezone = $booking->CountryArea->timezone;
//            date_default_timezone_set($booking->CountryArea->timezone);
//            will do later
//            $family_member_id = $booking->family_member_id;
//            if (!empty($family_member_id)) {
//                $family_det = FamilyMember::find($family_member_id);
//                $family_visibility = true;
//                $family_member_name = $family_det->name;
//                $family_member_phoneNumber = $family_det->phoneNumber;
//                $family_member_age = $family_det->age;
//            }

            $config = BookingConfiguration::where([['merchant_id', '=', $booking->merchant_id]])->first();

            $booking_status = $booking->booking_status;
            $currency = $booking->CountryArea->Country->isoCode;
            $booking_text = CommonController::UserHistoryBookingStatus($booking->booking_status, $string_file);
            $details = [
                'id' => $booking->id,
                'number' => $booking->merchant_booking_id,
                'segment_slug' => $booking->Segment->slag,
                'segment_group_id' => $booking->Segment->segment_group_id,
                'segment_sub_group' => $booking->Segment->sub_group_for_app,
                'segment_id' => $booking->segment_id,
                'status' => $booking_status,
                'status_text' => $booking_text,
                'map_image' => !empty($booking->map_image) ? $booking->map_image . "&zoom=12&key=" . $booking->Merchant->BookingConfiguration->google_key : "",
                'timestamp' => ($booking['booking_type'] == 1) ? convertTimeToUSERzone($booking->created_at, $timezone, $booking->merchant_id, null, 3, 1)  : date(getDateTimeFormat($booking->Merchant->datetime_format,3),strtotime($booking['later_booking_date'] . " " . $booking['later_booking_time'])),
                'estimate_price' => $booking->CountryArea->Country->isoCode." ".$booking->estimate_bill,
                'estimate_distance' => $booking->estimate_distance,
                'date_day' => ($booking['booking_type'] == 2) ? date('l, jS F',strtotime($booking['later_booking_date'] . " " . $booking['later_booking_time'])) : '',
            ];
            $detail = $booking->BookingDetail;
//            $string_file = $this->getStringFile($booking->merchant_id);
            switch ($booking_status) {
                case "1001":
                    $button_text = trans("$string_file.accept");
                    $text_back_ground = "2ECC71";
                    $action = "ACCEPT";
                    break;
                case "1012":
                    switch ($booking['service_type_id']) {
                        case "1":
                            $seconds = $config->normal_ride_later_time_before;
                            break;
                        case "2":
                            $seconds = $config->rental_ride_later_time_before;
                            break;
                        case "3":
                            $seconds = $config->transfer_ride_later_time_before;
                            break;
                        case "4":
                            $seconds = $config->outstation_time_before;
                            break;
                        default :
                            $seconds = $config->normal_ride_later_time_before;
                            break;
                    }
                    $today = new \DateTime(date('Y-m-d'));
                    $later_date = $booking->later_booking_date;
                    $later_time = $booking->later_booking_time;
                    $expires = new \DateTime($booking->later_booking_date);
                    // if ($today == $expires) {
                    $bookingTimestamp = strtotime($later_date . " " . $later_time) - $seconds;
                    // $currentTimestamp = time();
                    $currentTimestamp = convertTimeToUSERzone(date("Y-m-d H:i:s"), $timezone, $booking->merchant_id, null, 3, 1);
                    $currentTimestamp = strtotime($currentTimestamp);
                    if ($bookingTimestamp <= $currentTimestamp && $today == $expires) {
                        $button_text = trans("$string_file.start_to_pickup");
                        $action = "START_TO_PICK";
                    } else {
                        $hours = floor($seconds / 3600);
                        $minutes = ($seconds / 60 % 60);
                        // $button_text = trans_choice('api.minutes_ago', 3, ['hours' => $hours, 'min' => $minutes]);
                        $button_text_time = date(getDateTimeFormat($booking->Merchant->datetime_format, 3), strtotime($later_date . " " . $later_time) - $seconds);
                        $button_text = trans_choice("$string_file.ride_time_ago", 3, ['date' => $button_text_time]);
                        $action = "NO_ACTION";
                    }
                    // } else {
                    //     $hours = floor($seconds / 3600);
                    //     $minutes = ($seconds / 60 % 60);
                    //     $button_text = trans_choice('api.minutes_ago', 3, ['hours' => $hours, 'min' => $minutes]);
                    //     $action = "NO_ACTION";
                    // }
                    $text_back_ground = "2ECC71";
                    break;
                case "1002":
                    $button_text = trans("$string_file.track");
                    $text_back_ground = "3498DB";
                    $action = "TRACK";
                    break;
                case "1003":
                    $button_text = trans("$string_file.track");
                    $text_back_ground = "3498DB";
                    $action = "TRACK";
                    break;
                case "1004":
                    $button_text = trans("$string_file.track");
                    $text_back_ground = "3498DB";
                    $action = "TRACK";
                    break;
                case "1005":
                    $button_text = trans("$string_file.complete");
                    $text_back_ground = "3498DB";
                    $action = "COMPLETE";
                    break;
                default:
                    $button_text = trans("$string_file.track");
                    $text_back_ground = "3498DB";
                    $action = "TRACK";
            }

            $trip_details = [];

            $pre_html = "<strong><span style='color:black'>";
            $post_html = "</span></strong><br>";
            $status_history = json_decode($booking->booking_status_history, true);
            
           if (!empty($status_history)) {
                foreach ($status_history as $status) {
                    $status_time = convertTimeToUSERzone(date("Y-m-d H:i:s", $status['booking_timestamp']), $timezone, $booking->merchant_id, null, 3);
                    $status_detail = [];
                    if ($status['booking_status'] == 1001 || $status['booking_status'] == 1012 || $status['booking_status'] == 1005) {
                        $location = "";
                        if ($status['booking_status'] == 1001 || $status['booking_status'] == 1012) {
                            $location = $booking->pickup_location;
                        }
                        elseif ($status['booking_status'] == 1005) {
                            $location = $booking->drop_location;
                        }
                        $booking_text = CommonController::UserHistoryBookingStatus($status['booking_status'], $string_file);

//                        $status_text = $pre_html . $booking_text . $post_html;
//                        $status_text .= "<p>" . $sub_heading . "</p>";
                        $status_text = $booking_text;
                        $status_detail = [
                            'status_time' => $status_time, //date('H:i a',$status['booking_timestamp']),
                            'status_value' => $status_text,
//                            'location' => $booking->pickup_location,
                            'location' => $location,
                        ];
                    } else {
                        $booking_text = CommonController::UserHistoryBookingStatus($status['booking_status'], $string_file);
                        $status_text = $booking_text;
                        $status_detail = [
                            'status_time' => $status_time, //date('H:i a',$status['booking_timestamp']),
                            'status_value' => $status_text,
                            'location' => $this->getLocation($booking,$status['booking_status']),
                        ];
                    }
                    $trip_details[] = $status_detail;
                }
            }

            $arr_action = [];
            if (in_array($booking_status, [1001, 1002, 1003, 1004, 1012])) {
                $arr_action[] = [
                    'button_type' => "FULL_WIDTH",
                    'action' => $action,
                    'icon' => "",
                    'text' => $button_text,
                    'color' => $text_back_ground,
                ];
            } elseif ($booking_status == 1005 && $booking->booking_closure == NULL) {
                $arr_action[] = [
                    'button_type' => "FULL_WIDTH",
                    'action' => $action,
                    'icon' => "",
                    'text' => $button_text,
                    'color' => $text_back_ground,
                ];
            }
            $vehicle_details = [];
            if (!empty($booking->driver_id)) {
                $vehicle_details[] = [
                    'vehicle_type_image' => get_image($booking->DriverVehicle->VehicleType->vehicleTypeImage, 'vehicle', $booking->merchant_id, true, false),
                    'vehicle_type' => $booking->DriverVehicle->VehicleType->VehicleTypeName,
                    'vehicle_model' => $booking->DriverVehicle->VehicleModel->VehicleModelName,
                    'vehicle_number' => $booking->DriverVehicle->vehicle_number,
                ];
            }
            $bill_details = [];
            if (!empty($booking->BookingDetail)) {
                $price = json_decode($booking->BookingDetail->bill_details);
                if (!empty($price)) {
                    $bill_details = HolderController::PriceDetailHolder($price, $request->booking_id, $currency, 'driver');
                    $grand_total = [
                        'name' => trans("$string_file.grand_total"),
                        'value' => $currency . $booking->final_amount_paid,
                        'colour' => "333333",
                        'bold' => true,
                    ];
                    array_push($bill_details, $grand_total);
                }
            }

            $distance_details = [];
            if ($booking_status == 1005) {
                $distance_details = [
                    ['name' => trans("$string_file.travelled_distance"), 'value' => $booking->travel_distance, 'bold' => false],
                    ['name' => trans("$string_file.total_time"), 'value' => $booking->travel_time, 'bold' => false]
                ];
            }

            $payment_details = [
                'paid_status' => $booking->payment_status == 1 ? true : false,
                'payment_mode' => $booking->PaymentMethod->payment_method,
                'amount' => $currency . $booking->final_amount_paid,
                'currency' => $currency,
            ];
            $customer_details = [
                'customer_name' => $booking->User->first_name . ' ' . $booking->User->last_name,
                'customer_image' => get_image($booking->User->UserProfileImage, 'user', $booking->merchant_id, true, false),
                'customer_phone' => $booking->User->UserPhone,
                'rating' => $booking->User->rating,
            ];
            // drop details
            $drop_details = [
                'lat' => $booking->drop_latitude,
                'lng' => $booking->drop_longitude,
                'address' => $booking->drop_location,
            ];
            $pickup_details = [
                'lat' => $booking->pickup_latitude,
                'lng' => $booking->pickup_longitude,
                'address' => $booking->pickup_location,
            ];

            $multi_drop = [];
            $arr_drop = json_decode($booking->waypoints, true);
            if(!empty($arr_drop) && count($arr_drop) > 0)
            {
//                $last_key = array_keys($arr_drop);
//                $last_key = end($last_key);
//              unset($arr_drop[$last_key]);
              $multi_drop = $arr_drop;
            }
            $data = [
                'details' => $details,
                'trip_details' => $trip_details,
                'vehicle_details' => $vehicle_details,
                'bill_details' => $bill_details,
                'meter_details' => $distance_details,
                'payment_details' => $payment_details,
                'customer_details' => $customer_details,
                'action_buttons' => $arr_action,
                'drop_details' => $drop_details,
                'pickup_details' => $pickup_details,
                'middle_drop' => $multi_drop
            ];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \Exception($message);
        }
        return $data;
    }

    public function getLocation($booking,$booking_status){
        $loc = '';
        switch($booking_status){
//            case "1002":
//                $loc = $booking->BookingDetail->accept_latitude.','.$booking->BookingDetail->accept_longitude;
//                break;
//            case "1003":
//                $loc = $booking->BookingDetail->arrive_latitude.','.$booking->BookingDetail->arrive_longitude;
//                break;
            case "1004":
                $loc = $booking->BookingDetail->start_location;
                break;
            case "1005":
                $loc = $booking->BookingDetail->end_location;
                break;
        }
        return $loc;
    }
}
