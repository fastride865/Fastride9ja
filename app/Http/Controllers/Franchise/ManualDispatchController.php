<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\DistanceController;
use App\Http\Controllers\Helper\FindDriverController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Requests\ManualDispatch;
use App\Models\Booking;
use App\Models\Configuration;
use App\Models\Country;
use App\Models\PriceCard;
use App\Models\User;
use Auth;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManualDispatchController extends Controller
{
    public function index()
    {
        $franchise = Auth::user('franchise');
        $merchant_id = $franchise->merchant_id;
        $areas = $franchise->CountryArea;
        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
        return view('franchise.manual.index', compact('areas', 'countries'));
    }

    public function AddManualUser(Request $request)
    {
        $merchant_id = Auth::user('franchise')->merchant_id;
        $this->validate($request, [
            'new_user_name' => 'required|alpha',
            'new_user_phone' => ['required', 'regex:/^[0-9+]+$/',
                Rule::unique('users', 'UserPhone')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', $merchant_id);
                })],
            'new_user_email' => ['required', 'email',
                Rule::unique('users', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', $merchant_id);
                })],
        ]);
        $password = "";
        $user = new User();
        $rider = User::create([
            'merchant_id' => $merchant_id,
            'UserName' => $request->new_user_name,
            'UserPhone' => $request->new_user_phone,
            'email' => $request->new_user_email,
            'password' => $password,
            'UserSignupType' => 1,
            'UserSignupFrom' => 2,
            'ReferralCode' => $user->GenrateReferCode(),
            'UserProfileImage' => "",
            'user_type' => 2
        ]);
        $rider->Franchisee()->sync(Auth::user('franchise')->id);
        echo $rider->id;
    }

    public function SearchUser(Request $request)
    {
        $merchant_id = Auth::user('franchise')->merchant_id;
        $rider = User::where([['merchant_id', '=', $merchant_id], ['UserPhone', '=', $request->user_phone]])->first();
        $id = $rider->id;
        echo $id;
    }

    public function BookingDispatch(ManualDispatch $request)
    {
        $merchant_id = Auth::user('franchise')->merchant_id;
        $configuration = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $query = PriceCard::where([['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type]]);
        if (!empty($request->package) && $request->package != "null") {
            $query->where([['package_id', '=', $request->package]]);
        }
        $pricecards = $query->first();
        if (empty($pricecards)) {
            return redirect()->back()->with('nodriver', trans("$string_file.no_price_card_for_area"));
        }
        $findDriver = new FindDriverController();
        switch ($request->service) {
            case "1":
            case "2":
            case "3":
                if ($request->booking_type == 1) {
                    $drivers = $findDriver->GetAllNearestDriver($request->area, $request->pickup_latitude, $request->pickup_longitude, $configuration->distance, $configuration->number_of_driver, $request->vehicle_type, $request->service);
                    if (empty($drivers)) {
                        return redirect()->back()->with('nodriver', trans('api.message58'));
                    }
                    $booking = $this->AddBooking($request, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
                    $findDriver->AssignRequest($drivers, $booking->id);
                    $message = "New Booking";
                    $bookingData = new BookingDataController();
                    $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
                } else {
                    $drivers = [];
                    $booking = $this->AddBooking($request, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
                    if ($configuration->ride_later_request == 1) {
                        $findDriver = new FindDriverController();
                        $drivers = $findDriver->GetAllNearestDriver($booking->country_area_id, $booking->pickup_latitude, $booking->pickup_longitude, $configuration->distance_ride_later, $configuration->ride_later_request_number_driver, $booking->vehicle_type_id, $booking->service_type_id);
                        if (!empty($drivers)) {
                            $message = "There Is New Upcomming Booking";
                            $bookingData = new BookingDataController();
                            $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
                        }
                    }
                }
                break;
            case"4":
                $drivers = [];
                $booking = $this->AddBooking($request, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
                if ($configuration->outstation_request_type == 1) {
                    $findDriver = new FindDriverController();
                    $drivers = $findDriver->GetAllNearestDriver($booking->country_area_id, $booking->pickup_latitude, $booking->pickup_longitude, $configuration->outstation_radius, $configuration->no_driver_outstation, $booking->vehicle_type_id, $booking->service_type_id);
                    if (!empty($drivers)) {
                        $message = "There Is New Upcomming Booking";
                        $bookingData = new BookingDataController();
                        $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
                    }
                }
                break;
            case "5":
                $vehicle_type_id = [$request->vehicle_type];
                $drivers = $findDriver->getPoolDriver($request->area, $request->pickup_latitude, $request->drop_latitude, $request->drop_longitude, $request->pickup_longitude, $configuration->pool_radius, $configuration->no_of_drivers, 1, $vehicle_type_id, $configuration->pool_drop_radius);
                if (empty($drivers)) {
                    return redirect()->back()->with('nodriver', trans('api.message58'));
                }
                $booking = $this->AddBooking($request, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
                $findDriver->AssignRequest($drivers, $booking->id);
                $message = "New Booking";
                $bookingData = new BookingDataController();
                $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
                break;
        }
        return redirect()->route('franchise.ride-requests', $booking->id);
    }


    public function AddBooking($request, $merchant_id, $pricecardid, $drivers, $key)
    {

        $from = $request->pickup_latitude . "," . $request->pickup_longitude;
        if (!empty($drivers)) {
            $current_latitude = $drivers['0']->current_latitude;
            $current_longitude = $drivers['0']->current_longitude;
            $driverLatLong = $current_latitude . "," . $current_longitude;
            $nearDriver = DistanceController::DistanceAndTime($from, $driverLatLong, $key);
            $estimate_driver_distnace = $nearDriver['distance'];
            $estimate_driver_time = $nearDriver['time'];
        } else {
            $estimate_driver_distnace = "";
            $estimate_driver_time = "";
        }
        $drop_locationArray[] = array('drop_latitude' => $request->drop_latitude, 'drop_longitude' => $request->drop_longitude);
        $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $key);
        $booking = Booking::create([
            'merchant_id' => $merchant_id,
            'user_id' => $request->user_id,
            'franchise_id' => Auth::user('franchise')->id,
            'platform' => 2,
            'country_area_id' => $request->area,
            'service_type_id' => $request->service,
            'vehicle_type_id' => $request->vehicle_type,
            'price_card_id' => $pricecardid,
            'pickup_latitude' => $request->pickup_latitude,
            'pickup_longitude' => $request->pickup_longitude,
            'drop_latitude' => $request->drop_latitude,
            'drop_longitude' => $request->drop_longitude,
            'booking_type' => $request->booking_type,
            'map_image' => $googleArray['image'],
            'drop_location' => $request->drop_location,
            'pickup_location' => $request->pickup_location,
            'estimate_distance' => $request->estimate_distance,
            'estimate_time' => $request->estimate_time,
            'payment_method_id' => $request->payment_method_id,
            'estimate_bill' => $request->estimate_fare,
            'booking_timestamp' => strtotime("now"),
            'booking_status' => 1001,
            'package_id' => $request->package,
            'later_booking_date' => $request->date,
            'later_booking_time' => $request->time,
            'return_date' => $request->retrun_date,
            'return_time' => $request->retrun_time,
            'estimate_driver_distnace' => $estimate_driver_distnace,
            'estimate_driver_time' => $estimate_driver_time,
        ]);
        return $booking;
    }
}
