<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\DistanceController;
use App\Http\Controllers\Helper\FindDriverController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\PriceController;
use App\Http\Controllers\Helper\SmsController;
use App\Http\Resources\DeliveryCheckoutResource;
use App\Models\Booking;
use App\Models\BookingCheckout;
use App\Models\BookingConfiguration;
use App\Models\BookingDeliveryDetails;
use App\Models\BookingDetail;
use App\Models\Configuration;
use App\Models\DeliveryCheckoutDetail;
use App\Models\DeliveryPackage;
use App\Models\DeliveryProduct;
use App\Models\Driver;
use App\Models\PriceCard;
use App\Models\SmsConfiguration;
use App\Traits\ApiResponseTrait;
use App\Traits\ImageTrait;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;
use App\Traits\BookingTrait;
use App\Traits\MerchantTrait;
use mysql_xdevapi\Exception;

class DeliveryController extends Controller
{
    use ApiResponseTrait, ImageTrait, BookingTrait, MerchantTrait;

    public function Checkout(Request $request)
    {
        $validator = Validator::make($request->all(), ['segment_id' => 'required|integer|exists:segments,id', 'area' => 'required|integer|exists:country_areas,id', 'service_type_id' => 'required|integer', 'vehicle_type' => 'required|integer', 'pickup_latitude' => 'required', 'pickup_longitude' => 'required', 'pick_up_locaion' => 'required', 'booking_type' => 'required|integer|in:1,2', 'later_date' => 'required_if:booking_type,2', 'later_time' => 'required_if:booking_type,2', 'total_drop_location' => 'required|integer|between:0,4', 'drop_location' => 'required_if:total_drop_location,1,2,3,4',]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $user = $request->user('api');
        $config = Configuration::where('merchant_id', $user->merchant_id)->first();
        if (isset($config->user_outstanding_enable) && $config->user_outstanding_enable == 1) {
            // Check for previous booking outstanding.
            $booking_con = new BookingController;
            $result = $booking_con->checkBookingOutstanding($user->id);
            if (!empty($result)) {
                return response()->json(['result' => "3", 'message' => 'success', 'data' => $result]);
            }
        }
        DB::beginTransaction();
        try {
            if ($request->booking_type == 1) {
                $booking = $this->InstantCheckout($request);
            } else {
                $booking = $this->LaterCheckout($request);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            return $this->failedResponse($message);
        }
        DB::commit();
        return $booking;
    }

    public function InstantCheckout($request)
    {

        DB::beginTransaction();
        try {
            $drop_locationArray = json_decode($request->drop_location, true);
            $dropLocation = isset($drop_locationArray[0]) ? $drop_locationArray[0] : '';
            $drop_lat = isset($dropLocation['drop_latitude']) ? $dropLocation['drop_latitude'] : '';
            $drop_long = isset($dropLocation['drop_longitude']) ? $dropLocation['drop_longitude'] : '';

            $merchant_id = $request->user('api')->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
            $pricecards = PriceCard::where([['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type_id], ['vehicle_type_id', '=', $request->vehicle_type]])->first();
            $newBookingData = new BookingDataController();
            if (empty($pricecards)) {
                $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 1);
                return $this->failedResponse(trans("$string_file.no_price_card_for_area"));
            }
//            $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
            $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
            $remain_ride_radius_slot = [];
            if (!empty($configuration->driver_ride_radius_request)) {
                $remain_ride_radius_slot = json_decode($configuration->driver_ride_radius_request, true);
            }

            $req_parameter = ['area' => $request->area, 'segment_id' => $request->segment_id, 'latitude' => $request->pickup_latitude, 'longitude' => $request->pickup_longitude, 'distance' => !empty($remain_ride_radius_slot) ? $remain_ride_radius_slot[0] : $configuration->normal_ride_now_radius, 'limit' => $configuration->normal_ride_now_request_driver, 'service_type' => $request->service_type_id, 'vehicle_type' => $request->vehicle_type, 'drop_lat' => $drop_lat, 'drop_long' => $drop_long];
            $drivers = Driver::GetNearestDriver($req_parameter);
            if (empty($drivers) || $drivers->count() == 0) {
                $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 2);
                return $this->failedResponse(trans("$string_file.no_driver_available"));
            }
            $auto_upgradetion = isset($auto_upgradetion) ? $auto_upgradetion : 2;
            $from = $request->pickup_latitude . "," . $request->pickup_longitude;
            $current_latitude = isset($drivers['0']) ? $drivers['0']->current_latitude : "";
            $current_longitude = isset($drivers['0']) ? $drivers['0']->current_longitude : "";
            $driverLatLong = $current_latitude . "," . $current_longitude;
            $nearDriver = DistanceController::DistanceAndTime($from, $driverLatLong, $configuration->google_key);
            // Dump data
//            $nearDriver = array('time' => 20, 'distance' => 100);
            $estimate_driver_distnace = $nearDriver['distance'];
            $estimate_driver_time = $nearDriver['time'];
            $drop_locationArray = json_decode($request->drop_location, true);
            $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $configuration->google_key, "", $string_file);
            // Dump data
            //  $googleArray = array('total_time_text' => '2 hr','total_time_minutes' => 20, 'total_distance_text' => "", 'total_distance' => 20, 'image' => "");
            if (empty($googleArray)) {
                return $this->failedResponse(__("$string_file.google_key_not_working"));
            }
            $lastLocation = $newBookingData->wayPoints($drop_locationArray);
            $time = $googleArray['total_time_text'];
            $timeSmall = $googleArray['total_time_minutes'];
            $distance = $googleArray['total_distance_text'];
            $distanceSmall = $googleArray['total_distance'];
            $image = $googleArray['image'];
            $bill_details = "";
            switch ($pricecards->pricing_type) {
                case "1":
                case "2":
                    $estimatePrice = new PriceController();
                    $fare = $estimatePrice->BillAmount(['price_card_id' => $pricecards->id, 'merchant_id' => $merchant_id, 'distance' => $distanceSmall, 'time' => $timeSmall, 'booking_id' => 0, 'user_id' => $request->user('api')->id, 'booking_time' => date('H:i'),]);
                    $amount = $fare['amount'];
                    $bill_details = json_encode($fare['bill_details']);
                    break;
                case "3":
                    $amount = trans('api.message62');
                    break;
            }
            $rideData = array('distance' => $distance, 'bill_details' => $bill_details, 'time' => $time, 'amount' => $amount, 'estimate_driver_distnace' => $estimate_driver_distnace, 'estimate_driver_time' => $estimate_driver_time, 'auto_upgradetion' => $auto_upgradetion);
            $result = $newBookingData->CreateDeliveryCheckout($request, $request->user('api')->id, $merchant_id, $pricecards->id, $image, $rideData, $lastLocation);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            return $this->failedResponse($message);
        }
        DB::commit();

        return $this->successResponse(__("$string_file.ready_for_ride"), $result);
    }

    public function LaterCheckout($request)
    {
        DB::beginTransaction();
        try {
            $user = $request->user('api');
            $merchant_id = $user->merchant_id;
            $string_file = $this->getStringFile(NULL, $user->Merchant);
            $pricecards = PriceCard::where([['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type_id], ['vehicle_type_id', '=', $request->vehicle_type]])->first();
            $newBookingData = new BookingDataController();
            if (empty($pricecards)) {
                $newBookingData->failbooking($request, $merchant_id, $request->user('api')->id, 1);
                return $this->failedResponse(__("$string_file.no_price_card_for_area"));
            }
            $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
            $drop_locationArray = json_decode($request->drop_location, true);
            $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $configuration->google_key, "", $string_file);
            if (empty($googleArray)) {
                return $this->failedResponse(__("$string_file.google_key_not_working"));
            }
            $lastLocation = $newBookingData->wayPoints($drop_locationArray);
            $time = $googleArray['total_time_text'];
            $timeSmall = $googleArray['total_time_minutes'];
            $distance = $googleArray['total_distance_text'];
            $distanceSmall = $googleArray['total_distance'];
            $image = $googleArray['image'];
            $bill_details = "";
            switch ($pricecards->pricing_type) {
                case "1":
                case "2":
                    $estimatePrice = new PriceController();
                    $fare = $estimatePrice->BillAmount(['price_card_id' => $pricecards->id, 'merchant_id' => $merchant_id, 'distance' => $distanceSmall, 'time' => $timeSmall, 'booking_id' => 0, 'user_id' => $request->user('api')->id, 'booking_time' => $request->later_time,]);
                    $amount = $fare['amount'];
                    $bill_details = json_encode($fare['bill_details']);
                    break;
                case "3":
                    $amount = trans('api.message62');
                    break;
            }
            $rideData = array('distance' => $distance, 'time' => $time, 'bill_details' => $bill_details, 'amount' => $amount, 'estimate_driver_distnace' => "", 'estimate_driver_time' => "", 'auto_upgradetion' => 2);
            $result = $newBookingData->CreateDeliveryCheckout($request, $request->user('api')->id, $merchant_id, $pricecards->id, $image, $rideData, $lastLocation);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            return $this->failedResponse($message);
        }
        DB::commit();

        return $this->successResponse(__("$string_file.ready_for_ride"), $result);
    }

    public function getDeliveryProduct(Request $request)
    {
        $user = $request->user('api');
        $string_file = $this->getStringFile(null,$user->Merchant);
        $products = DeliveryProduct::where([['merchant_id', '=', $user->merchant_id], ['status', '=', 1]])->get();
        $data = [];
        foreach ($products as $product) {
            $data[] = array('id' => $product->id, 'segment_id' => $product->segment_id, 'merchant_id' => $product->merchant_id, 'product_name' => $product->ProductName, 'weight_unit' => $product->WeightUnit->WeightUnitName);
        }
        return $this->successResponse(__("$string_file.success"), $data);
    }

    public function Confirm(Request $request)
    {
        $validator = Validator::make($request->all(), ['checkout' => ['required', 'integer', Rule::exists('booking_checkouts', 'id')->where(function ($query) {
//                    $query->where([['payment_method_id', '!=', 0]]);
        })], 'fav_driver_id' => 'nullable|exists:drivers,id', //            'product_data' => 'required',
//            'receiver_details' => 'required',
            'additional_movers' => 'required', 'payment_method_id' => 'required', 'card_id' => 'required_if:payment_method_id,2',]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        DB::beginTransaction();
        try {
            $checkout = BookingCheckout::find($request->checkout);
            $string_file = $this->getStringFile($checkout->merchant_id);
            $booking_obj = new BookingController();
            if ($booking_obj->CheckWalletBalance($checkout) == 1) {
                if ($request->additional_movers > 0) {
                    $bill_details = $checkout->bill_details;
                    $estimate_bill = $checkout->estimate_bill;
                    if (!empty($bill_details)) {
                        $bill_details = json_decode($bill_details, true);
                        $additional_mover_charges = round_number($request->additional_movers * $checkout->PriceCard->additional_mover_charge);
                        $parameter = array('price_card_id' => $checkout->price_card_id, 'booking_id' => NULL, 'parameter' => trans("$string_file.additional_mover_charges"), 'amount' => (string)$additional_mover_charges, 'type' => "CREDIT", 'code' => "");
                        array_push($bill_details, $parameter);
                        $bill_details = json_encode($bill_details);
                        $estimate_bill += $additional_mover_charges;
                    }
                    $checkout->estimate_bill = $estimate_bill;
                    $checkout->bill_details = $bill_details;
                    $checkout->save();
                }
                if ($checkout->booking_type == 1) {
                    $booking = $this->InstantBookingAssign($checkout, $request);
                } else {
                    $booking = $this->LaterBookingAssign($checkout, $request);
                }

                $user_id = $checkout->user_id;
                // delete checkout pavkages
//                $checkout->delete();
                $merchant_id = $request->user('api')->merchant_id;
                $SmsConfiguration = SmsConfiguration::select('ride_book_enable', 'ride_book_msg')->where([['merchant_id', '=', $merchant_id]])->first();
                if ($SmsConfiguration && $SmsConfiguration->ride_book_enable && $SmsConfiguration->ride_book_msg) {
                    $sms = new SmsController();
                    $user = User::where([['id', '=', $user_id]])->first();
                    $phone = $user->UserPhone;
                    $sms->SendSms($merchant_id, $phone, null, 'RIDE_BOOK', $user->email);
                }
            } elseif ($booking_obj->CheckWalletBalance($checkout) == 2) {
                return $this->failedResponse(trans("$string_file.low_wallet_warning"));
            } else {
                return $this->failedResponse(trans("$string_file.wallet_low_estimate"));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            return $this->failedResponse($message);
        }
        // inset booking status history
        $this->saveBookingStatusHistory($request, $booking);
        DB::commit();
        $return_booking = ['id' => $booking->id, 'booking_type' => $booking->booking_type, 'merchant_booking_id' => $booking->merchant_booking_id,];
        $string_file = $this->getStringFile($merchant_id);
        return $this->successResponse(__("$string_file.ride_booked"), $return_booking);
    }

    public function InstantBookingAssign($checkOut, $request)
    {
        try {
            $findDriver = new FindDriverController();
//        $config = Configuration::where([['merchant_id', '=', $checkOut->merchant_id]])->first();
            $configuration = BookingConfiguration::where([['merchant_id', '=', $checkOut->merchant_id]])->first();
            $remain_ride_radius_slot = [];
            if (!empty($configuration->driver_ride_radius_request)) {
                $remain_ride_radius_slot = json_decode($configuration->driver_ride_radius_request, true);
            }
            $req_parameter = ['area' => $checkOut->country_area_id, 'segment_id' => $checkOut->segment_id, 'latitude' => $checkOut->pickup_latitude, 'longitude' => $checkOut->pickup_longitude, 'distance' => !empty($remain_ride_radius_slot) ? $remain_ride_radius_slot[0] : $configuration->normal_ride_now_radius, 'limit' => $configuration->normal_ride_now_request_driver, 'service_type' => $checkOut->service_type_id, 'vehicle_type' => $checkOut->vehicle_type_id,];
            $string_file = $this->getStringFile($checkOut->merchant_id);
            $drivers = Driver::GetNearestDriver($req_parameter);
            if (empty($drivers)) {
                throw new \Exception("$string_file.no_driver_available");
            }
            $checkOut->auto_upgradetion = 2;
//            $checkOut->additional_notes = $request->additional_notes;
            $delivery_checkout_details = DeliveryCheckoutDetail::where([['booking_checkout_id', '=', $checkOut->id]])->orderBy('stop_no')->get();
            $Bookingdata = $checkOut->toArray();
//            unset($Bookingdata['id']);
//            unset($Bookingdata['user']);
//            unset($Bookingdata['price_card']);
//            $Bookingdata['booking_timestamp'] = time();
//            $Bookingdata['booking_status'] = 1001;
            $arr_booking_data = ["merchant_id" => $Bookingdata["merchant_id"], "corporate_id" => $Bookingdata["corporate_id"], "segment_id" => $Bookingdata["segment_id"], "user_id" => $Bookingdata["user_id"], "country_area_id" => $Bookingdata["country_area_id"], "service_type_id" => $Bookingdata["service_type_id"], "vehicle_type_id" => $Bookingdata["vehicle_type_id"], "service_package_id" => $Bookingdata["service_package_id"], "is_geofence" => $Bookingdata["is_geofence"], "base_area_id" => $Bookingdata["base_area_id"], "price_card_id" => $Bookingdata["price_card_id"], "total_drop_location" => $Bookingdata["total_drop_location"], "auto_upgradetion" => $Bookingdata["auto_upgradetion"], "number_of_rider" => $Bookingdata["number_of_rider"], "payment_method_id" => $request->payment_method_id, "pickup_latitude" => $Bookingdata["pickup_latitude"], "pickup_longitude" => $Bookingdata["pickup_longitude"], "pickup_location" => $Bookingdata["pickup_location"], "drop_latitude" => $Bookingdata["drop_latitude"], "drop_longitude" => $Bookingdata["drop_longitude"], "drop_location" => $Bookingdata["drop_location"], "waypoints" => $Bookingdata["waypoints"], "promo_code" => $Bookingdata["promo_code"], "map_image" => $Bookingdata["map_image"], "estimate_bill" => $Bookingdata["estimate_bill"], "hotel_charges" => $Bookingdata["hotel_charges"], "estimate_distance" => $Bookingdata["estimate_distance"], "estimate_time" => $Bookingdata["estimate_time"], "estimate_driver_distnace" => $Bookingdata["estimate_driver_distnace"], "estimate_driver_time" => $Bookingdata["estimate_driver_time"], "booking_type" => $Bookingdata["booking_type"], "later_booking_date" => $Bookingdata["later_booking_date"], "later_booking_time" => $Bookingdata["later_booking_time"], "return_date" => $Bookingdata["return_date"], "return_time" => $Bookingdata["return_time"], "additional_notes" => $request->additional_notes, "additional_information" => $Bookingdata["additional_information"], "bill_details" => $Bookingdata["bill_details"], "baby_seat_enable" => $Bookingdata["baby_seat_enable"], "wheel_chair_enable" => $Bookingdata["wheel_chair_enable"], "no_of_person" => $Bookingdata["no_of_person"], "no_of_children" => $Bookingdata["no_of_children"], "no_of_bags" => $Bookingdata["no_of_bags"], "bags_weight_kg" => $Bookingdata["bags_weight_kg"], "manual_dispatch_ride" => $Bookingdata["manual_dispatch_ride"], "gender" => $Bookingdata["gender"], "booking_status" => 1001, "additional_movers" => $request->additional_movers, "card_id" => !empty($request->card_id) ? $request->card_id : NULL, "booking_timestamp" => time(),];
            $booking = Booking::create($arr_booking_data);

            // Fill Delivery drop details
            $this->storeDeliveryDropDetails($booking, $delivery_checkout_details);

            $findDriver->AssignRequest($drivers, $booking->id);
            $bookingData = new BookingDataController();
            // add delivery packages
            unset($booking->map_image);
//        $bookingData->SendNotificationToDriversDelivery($booking, $drivers, $message);
            $bookingData->SendNotificationToDrivers($booking, $drivers);
        } catch (\Exception $e) {
            throw  new \Exception($e->getMessage());
        }
        return $booking;
    }

    public function storeDeliveryDropDetails($booking, $delivery_drop_details)
    {
        DB::beginTransaction();
        try {
            if (!empty($delivery_drop_details)) {
                foreach ($delivery_drop_details as $delivery_drop_detail) {
                    $booking_delivery_detail = new BookingDeliveryDetails;
                    $booking_delivery_detail->booking_id = $booking->id;
                    $booking_delivery_detail->stop_no = $delivery_drop_detail->stop_no;
                    $booking_delivery_detail->drop_latitude = $delivery_drop_detail->drop_latitude;
                    $booking_delivery_detail->drop_longitude = $delivery_drop_detail->drop_longitude;
                    $booking_delivery_detail->drop_location = $delivery_drop_detail->drop_location;
                    $booking_delivery_detail->receiver_name = $delivery_drop_detail->receiver_name;
                    $booking_delivery_detail->receiver_phone = $delivery_drop_detail->receiver_phone;
                    $booking_delivery_detail->receiver_image = $delivery_drop_detail->receiver_image;
                    $booking_delivery_detail->product_data = $delivery_drop_detail->product_data;
                    $booking_delivery_detail->product_image_one = $delivery_drop_detail->product_image_one;
                    $booking_delivery_detail->product_image_two = $delivery_drop_detail->product_image_two;
                    $booking_delivery_detail->additional_notes = $delivery_drop_detail->additional_notes;
                    $booking_delivery_detail->opt_for_verify = mt_rand(1111, 9999);
                    $booking_delivery_detail->save();
                    if (!empty($delivery_drop_detail->product_data)) {
                        $productData = json_decode($delivery_drop_detail->product_data, true);
                        foreach ($productData as $product) {
                            DeliveryPackage::updateOrCreate(['booking_id' => $booking->id, 'booking_delivery_detail_id' => $booking_delivery_detail->id,], ['delivery_product_id' => $product['id'], 'quantity' => $product['quantity'],]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        DB::commit();
    }

    public function LaterBookingAssign($checkOut, $request)
    {
        try {
            $delivery_checkout_details = DeliveryCheckoutDetail::where([['booking_checkout_id', '=', $checkOut->id]])->orderBy('stop_no')->get();
            $Bookingdata = $checkOut->toArray();
            $arr_booking_data = ["merchant_id" => $Bookingdata["merchant_id"], "corporate_id" => $Bookingdata["corporate_id"], "segment_id" => $Bookingdata["segment_id"], "user_id" => $Bookingdata["user_id"], "country_area_id" => $Bookingdata["country_area_id"], "service_type_id" => $Bookingdata["service_type_id"], "vehicle_type_id" => $Bookingdata["vehicle_type_id"], "service_package_id" => $Bookingdata["service_package_id"], "is_geofence" => $Bookingdata["is_geofence"], "base_area_id" => $Bookingdata["base_area_id"], "price_card_id" => $Bookingdata["price_card_id"], "total_drop_location" => $Bookingdata["total_drop_location"], "auto_upgradetion" => $Bookingdata["auto_upgradetion"], "number_of_rider" => $Bookingdata["number_of_rider"], "payment_method_id" => $request->payment_method_id, "pickup_latitude" => $Bookingdata["pickup_latitude"], "pickup_longitude" => $Bookingdata["pickup_longitude"], "pickup_location" => $Bookingdata["pickup_location"], "drop_latitude" => $Bookingdata["drop_latitude"], "drop_longitude" => $Bookingdata["drop_longitude"], "drop_location" => $Bookingdata["drop_location"], "waypoints" => $Bookingdata["waypoints"], "promo_code" => $Bookingdata["promo_code"], "map_image" => $Bookingdata["map_image"], "estimate_bill" => $Bookingdata["estimate_bill"], "hotel_charges" => $Bookingdata["hotel_charges"], "estimate_distance" => $Bookingdata["estimate_distance"], "estimate_time" => $Bookingdata["estimate_time"], "estimate_driver_distnace" => $Bookingdata["estimate_driver_distnace"], "estimate_driver_time" => $Bookingdata["estimate_driver_time"], "booking_type" => $Bookingdata["booking_type"], "later_booking_date" => $Bookingdata["later_booking_date"], "later_booking_time" => $Bookingdata["later_booking_time"], "return_date" => $Bookingdata["return_date"], "return_time" => $Bookingdata["return_time"], "additional_notes" => $request->additional_notes, "additional_information" => $Bookingdata["additional_information"], "bill_details" => $Bookingdata["bill_details"], "baby_seat_enable" => $Bookingdata["baby_seat_enable"], "wheel_chair_enable" => $Bookingdata["wheel_chair_enable"], "no_of_person" => $Bookingdata["no_of_person"], "no_of_children" => $Bookingdata["no_of_children"], "no_of_bags" => $Bookingdata["no_of_bags"], "bags_weight_kg" => $Bookingdata["bags_weight_kg"], "manual_dispatch_ride" => $Bookingdata["manual_dispatch_ride"], "gender" => $Bookingdata["gender"], "booking_status" => 1001, "additional_movers" => $request->additional_movers, "card_id" => !empty($request->card_id) ? $request->card_id : NULL, "booking_timestamp" => time(),];
            $booking = Booking::create($arr_booking_data);

            // Fill Delivery drop details
            $this->storeDeliveryDropDetails($booking, $delivery_checkout_details);

            $remain_ride_radius_slot = [];
            $configuration = BookingConfiguration::where([['merchant_id', '=', $checkOut->merchant_id]])->first();
            if (!empty($configuration->driver_ride_radius_request)) {
                $remain_ride_radius_slot = json_decode($configuration->driver_ride_radius_request, true);
            }
            if ($configuration->normal_ride_later_request_type == 1) {
                $req_parameter = ['area' => $checkOut->country_area_id, 'segment_id' => $checkOut->segment_id, 'latitude' => $checkOut->pickup_latitude, 'longitude' => $checkOut->pickup_longitude, 'distance' => !empty($remain_ride_radius_slot) ? $remain_ride_radius_slot[0] : $configuration->normal_ride_now_radius, 'limit' => $configuration->normal_ride_later_request_driver, 'service_type' => $checkOut->service_type_id, 'vehicle_type' => $checkOut->vehicle_type_id,];
                $drivers = Driver::GetNearestDriver($req_parameter);
                if (!empty($drivers)) {
                    $bookingData = new BookingDataController();
                    $bookingData->SendNotificationToDrivers($booking, $drivers);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $booking;
    }

    public function saveProductLoadedImages(Request $request)
    {
        $request_fields = ['image' => 'required', 'booking_id' => 'required',];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        DB::beginTransaction();
        try {
            $driver = $request->user('api-driver');
            $string_file = $this->getStringFile(NULL, $driver->Merchant);
            $merchant_id = $driver->merchant_id;
            $image = $this->uploadBase64Image("image", 'product_loaded_images', $merchant_id);
            $booking_details = BookingDetail::select('id', 'product_loaded_images')->where('booking_id', $request->booking_id)->first();
            $images = $booking_details->product_loaded_images;
            $new_images = [];
            if (!empty($images)) {
                $new_images = json_decode($images, true);
            }

            array_push($new_images, $image);
            $booking_details->product_loaded_images = json_encode($new_images);
            $booking_details->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        $return_image = get_image($image, 'product_loaded_images', $merchant_id, true);
        $uploaded_image = ['uploaded_image' => $return_image];
        return $this->successResponse(trans("$string_file.success"), $uploaded_image);
    }

    public function CheckoutDetails(Request $request)
    {
        $request_fields = ['checkout_id' => 'required|exists:booking_checkouts,id',];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        DB::beginTransaction();
        try {
            $user = $request->user('api');
            $string_file = $this->getStringFile(NULL, $user->Merchant);
            $merchant_id = $user->merchant_id;
            $checkout = BookingCheckout::where([['merchant_id', '=', $merchant_id], ['user_id', '=', $user->id]])->find($request->checkout_id);
            $data = new DeliveryCheckoutResource($checkout);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.success"), $data);
    }

    public function storeCheckoutDetails(Request $request)
    {
        $request_fields = ['checkout_id' => 'required|exists:booking_checkouts,id', 'delivery_checkout_detail_id' => 'required|exists:delivery_checkout_details,id', 'product_image_one' => 'file|max:6000', 'product_image_two' => 'file|max:6000', 'product_data' => 'required', 'receiver_name' => 'required', 'receiver_phone' => 'required',];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        DB::beginTransaction();
        try {
            $user = $request->user('api');
            $string_file = $this->getStringFile(NULL, $user->Merchant);
            $merchant_id = $user->merchant_id;
            $checkout = BookingCheckout::where([['merchant_id', '=', $merchant_id], ['user_id', '=', $user->id]])->find($request->checkout_id);
            $delivery_checkout_detail = DeliveryCheckoutDetail::where([['booking_checkout_id', '=', $request->checkout_id]])->find($request->delivery_checkout_detail_id);
            if (!empty($delivery_checkout_detail)) {
                $delivery_checkout_detail->receiver_name = $request->receiver_name;
                $delivery_checkout_detail->receiver_phone = $request->receiver_phone;
                $delivery_checkout_detail->additional_notes = $request->additional_notes;
                $delivery_checkout_detail->product_data = $request->product_data;
                if (!empty($request->product_image_one)) {
                    $delivery_checkout_detail->product_image_one = $this->uploadImage('product_image_one', 'product_image', $checkout->merchant_id);
                }
                if (!empty($request->product_image_two)) {
                    $delivery_checkout_detail->product_image_two = $this->uploadImage('product_image_two', 'product_image', $checkout->merchant_id);
                }
                $delivery_checkout_detail->details_fill_status = 1;
                $delivery_checkout_detail->save();
            } else {
                return $this->failedResponse(trans("$string_file.data_not_found"), []);
            }
            $data = new DeliveryCheckoutResource($checkout);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.success"), $data);
    }

    public function sendOtpToReceiver($booking_id)
    {
        $booking = Booking::find($booking_id);
        $booking_delivery_details = BookingDeliveryDetails::where('booking_id', $booking->id)->get();
        if (!empty($booking_delivery_details) && $booking->Merchant->BookingConfiguration->delivery_drop_otp == 1 && $booking->Merchant->BookingConfiguration->send_otp_to_number == 1) {
            $SmsConfiguration = SmsConfiguration::where([['merchant_id', '=', $booking->merchant_id]])->first();
            if (!empty($SmsConfiguration)) {
                foreach ($booking_delivery_details as $booking_delivery_detail) {
                    $phone = $booking_delivery_detail->receiver_phone;
                    $otp = $booking_delivery_detail->opt_for_verify;
                    if (!empty($phone)) {
                        $sms = new SmsController();
                        $sms->SendSms($booking->merchant_id, $phone, $otp);
                    }
                }
            }
        }
    }
}
