<?php

namespace App\Http\Controllers\Helper;

use App\Models\Booking;
use App\Models\BookingConfiguration;
use App\Models\Configuration;
use App\Models\Driver;
use App\Models\DriverVehicle;
use App\Models\LanguageVehicleType;
use App\Models\PriceCard;
use App\Models\User;
use App\Models\Merchant;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class ButtlerTaxi
{
    public function checkout($alias_name)
    {
        $merchant_id = Merchant::where([['alias_name',$alias_name]])->first()->id;
        $req = request()->all();
        $validator = Validator::make(request()->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
            'passengerInfo.carType' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0]]);
        }
        $carType = $req['passengerInfo']['carType'];
        $veh = LanguageVehicleType::where([['vehicleTypeName', '=', $carType], ['merchant_id', '=', $merchant_id]])->first();
        if (empty($veh)) {
            return response()->json(['result' => "0", 'message' => 'Vehicle type not found!!']);
        }
        $vehicle_type_id = $veh->vehicle_type_id;
        $area = PolygenController::Area($req['latitude'], $req['longitude'], $merchant_id);
        $country_id = $area['id'];
        if (empty($area)) {
            return response()->json(['result' => "0", 'message' => 'City not found!!']);
        }
        $price_card = PriceCard::where([['vehicle_type_id', '=', $vehicle_type_id], ['country_area_id', '=', $country_id], ['merchant_id', '=', $merchant_id]])->first();
        if (empty($price_card)) {
            return response()->json(['result' => "0", 'message' => 'Rate card not found!!']);
        }
        $user = $this->user($req, $country_id, $alias_name);
        $drivers = $this->driver($vehicle_type_id, $alias_name);
        $player_id = Arr::pluck($drivers, 'player_id');
        $Bookingdata = $req;
        unset($Bookingdata['passengerInfo']['passengerName']);
        unset($Bookingdata['id']);
        $Bookingdata['booking_timestamp'] = time();
        $Bookingdata['booking_status'] = 1231;
        $address = $req['number'] . "," . $req['houseNumberSuffix'] . "," . $req['displayName'] . "," . $req['street'] . "," . $req['city'] . "," . $req['zipCode'];
        $personalMessage = $req['passengerInfo']['personalMessage'];
        $reservationTime = $req['passengerInfo']['reservationTime'];
        $booking = Booking::create([
            'merchant_id' => $merchant_id,
            'user_id' => $user->id,
            'country_area_id' => $country_id,
            'service_type_id' => 1,
            'vehicle_type_id' => $vehicle_type_id,
            'price_card_id' => $price_card->id,
            'pickup_latitude' => $req['latitude'],
            'pickup_longitude' => $req['longitude'],
            'pickup_location' => $address,
            'booking_type' => 1,
            'payment_method_id' => 1,
            'additional_notes' => $personalMessage
        ]);
        $configuration = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        if (!empty($drivers)) {
            $message = "There Is New Upcomming Booking";
            $bookingData = new BookingDataController();
            $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
            $ride_id = $booking->id;
            return response()->json(['tripId' => $ride_id, 'success' => true, 'failure_mode' => '', 'reason' => 'Your Taxi has Booked !']);
        } else {
             return response()->json(['tripId' => "", 'success' => false, 'failure_mode' => 'CAR_NOT_AVAILABLE', 'reason' => 'No drivers found']);
        }
    }

    public function user($req, $country_id, $alias_name)
    {
        $merchant_id = Merchant::where([['alias_name',$alias_name]])->first()->id;
        $telephone = $req['telephone'];
        $passengerName = $req['passengerInfo']['passengerName'];
        if (empty($passengerName)) {
            $passengerName = '';
        }
        $check_user = User::where([['UserPhone', '=', $telephone], ['merchant_id', '=', $merchant_id]])->first();
        if (empty($check_user)) {
            $user = new User();
            $rider = User::create([
                'merchant_id' => $merchant_id,
                'country_id' => $country_id,
                'UserName' => $passengerName,
                'UserPhone' => $telephone,
                'email' => 'Buttler taxi',
                'password' => '$2y$12$2nlxMMnPDZQpfmIqo6yCNuYEDCn56NPFg5Dqd3kkvDW4yyz9uKj5u',
                'UserSignupType' => 1,
                'UserSignupFrom' => 2,
                'ReferralCode' => $user->GenrateReferCode(),
                'UserProfileImage' => "",
                'user_type' => 2
            ]);
            $rider->save();
        }
        $select_user = User::where([['UserPhone', '=', $telephone], ['merchant_id', '=', $merchant_id]])->first();
        return $select_user;
    }

    public function driver($vehicle_type_id, $alias_name)
    {
        $merchant_id = Merchant::where([['alias_name',$alias_name]])->first()->id;
        $driver_vehicle = DriverVehicle::where([['vehicle_type_id', '=', $vehicle_type_id], ['vehicle_active_status', '=', 1], ['merchant_id', '=', $merchant_id]])->first();
        $driver_id = $driver_vehicle->driver_id;
        $driver = Driver::where([['id', '=', $driver_id], ['merchant_id', '=', $merchant_id], ['online_offline', '=', 1], ['free_busy', '=', 2], ['login_logout', '=', 1]])->get();
        if (empty($driver)) {
            return response()->json(['result' => "0", 'message' => 'No drivers!!']);
        }
        return $driver;
    }

    public function get_status($ride_id = NULL, $alias_name)
    {
        $merchant_id = Merchant::where([['alias_name',$alias_name]])->first()->id;
        $ride_details = Booking::where([['id', '=', $ride_id], ['merchant_id', '=', $merchant_id]])->first();
        $booking_status = $ride_details['booking_status'];
        $pickup_lat = $ride_details['pickup_latitude'];
        $pickup_long = $ride_details['pickup_longitude'];
        $driver_id = $ride_details['driver_id'];
        $estimate_time = $ride_details['estimate_time'];
        $drivers = Driver::where([['id', '=', $driver_id], ['merchant_id', '=', $merchant_id]])->first();
        $driver_current_lat = $drivers['current_latitude'];
        $driver_current_long = $drivers['current_longitude'];
        $driver_name = $drivers['fullName'];
        $vehicle = DriverVehicle::where([['driver_id', '=', $driver_id], ['merchant_id', '=', $merchant_id]])->first();
        $vehicle_type_id = $vehicle['vehicle_type_id'];
        $vehicle_name = LanguageVehicleType::where([['vehicle_type_id', '=', $vehicle_type_id], ['merchant_id', '=', $merchant_id]])->first();
        $vehicleTypeName = $vehicle_name['vehicleTypeName'];

        if ($booking_status != "") {
            $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
            $findDriver = new FindDriverController();
            $user_gender = "";
            $service_type = 1;
            $area = PolygenController::Area($pickup_lat, $pickup_long, $merchant_id);
            $drivers = $findDriver->GetAllNearestDriver($area['id'], $pickup_lat, $pickup_long, $configuration->normal_ride_now_radius, $configuration->normal_ride_now_request_driver, $vehicle_type_id, $service_type, $user_gender);
            $time = $estimate_time * 60;
            $current_time = time();
            $time = $time + $current_time;
            $now_date = date('Y-m-d', $time);
            $now_time = date('H:i:s', $time);
            $etam = $now_date . "T" . $now_time . "Z";

            if ($booking_status == 1001) {
                $ride_stat = "REQUESTED";
                $eta = $etam;
            } elseif ($booking_status == 1002) {
                $ride_stat = "DRIVING_TO_PICKUP";
                $eta = $etam;
            } elseif ($booking_status == 1003) {
                $ride_stat = "COMPLETED";
                $eta = $etam;
            } elseif ($booking_status == 1006 || $booking_status == 1008) {
                $ride_stat = "CANCELLED";
                $eta = $etam;
            } elseif ($booking_status == 1007) {
                $ride_stat = "CANCELLED_BY_DRIVER";
                $eta = $etam;
            } elseif ($booking_status == 1005) {
                $ride_stat = "COMPLETED";
                $eta = $etam;
            }
            return response()->json(['tripStatus' => $ride_stat, 'eta' => $eta, 'taxiLatitude' => $driver_current_lat, 'taxiLongitude' => $driver_current_long, 'displayField1' => $driver_name, 'displayField2' => $vehicleTypeName]);

        } else {
             return response()->json(['result' => 0, 'msg' => "Booking status Missing!!!"]);
        }
    }

    public function delete_ride($ride_id = NULL, $alias_name)
    {
        $booking_details = Booking::find($ride_id);
        $driver_id = $booking_details['driver_id'];
        $drivers = Driver::find($driver_id);
        if (!empty($booking_details)) {
            $booking_details->booking_status = 1008;
            $booking_details->save();
            $message = "Ride Cancelled";
            $bookingData = new BookingDataController();
            $bookingData->SendNotificationToDrivers($booking_details, $drivers, $message);
            return response()->json(['success' => true]);
        } else {
             return response()->json(['success' => false]);
        }
    }

}