<?php

namespace App\Http\Controllers\Corporate;


use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\DistanceController;
use App\Http\Controllers\Helper\FindDriverController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\Merchant;
use App\Models\ApplicationConfiguration;
use App\Models\BookingConfiguration;
use App\Models\Configuration;
use App\Models\Country;
use App\Models\CountryArea;
use App\Traits\AreaTrait;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\FavouriteDriver;
use Auth;
use App\Models\User;
use App\Models\PriceCard;
use App\Models\PromoCode;
use App\Models\Corporate;
use App\Models\PaymentMethod;
use App\Http\Requests\ManualDispatch;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Helper\PriceController;

class ManualDispatchController extends Controller
{
    use AreaTrait;

    public function index()
    {
        $corporate = Auth::user('corporate');
        $config = ApplicationConfiguration::where([['merchant_id', '=', $corporate->merchant_id]])->first();
        $countries = Country::where([['merchant_id', '=', $corporate->merchant_id]])->get();
        $paymentmethods = PaymentMethod::get();
        return view('corporate.manual.index', compact('config', 'paymentmethods', 'countries'));
    }

    public function CorporateAreaList(Request $request)
    {
        $corporate = Auth::user('corporate');
        $phone_code = $request->phone_code;
        $country_id = $request->country_id;
        $areaList = CountryArea::whereHas('Country', function ($query) use ($phone_code,$country_id) {
            $query->where([['phonecode', '=', $phone_code],['id','=',$country_id]]);
        })->where([['merchant_id', '=', $corporate->merchant_id]])->get();
        if (empty($areaList->toArray())) {
            echo "<option value=''>" . trans("$string_file.no_service_area") . "</option>";
        } else {
            echo "<option value=''>" . trans("$string_file.area") . "</option>";
            foreach ($areaList as $value) {
                echo "<option value='" . $value->id . "'>" . $value->CountryAreaName . "</option>";
            }
        }
    }

    public function getDriverOnMap(Request $request)
    {
        $type = $request->type;
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        switch ($type) {
            case "1":
                $drivers = Driver::select('id', 'first_name', 'last_name', 'email', 'phoneNumber', 'profile_image', 'current_latitude', 'current_longitude', 'online_offline', 'free_busy')->where([['merchant_id', '=', $merchant_id], ['current_latitude', '!=', null], ['login_logout', '=', 1],['driver_delete','=',null]])->get();
                break;
            case "2":
                $drivers = Driver::select('id', 'first_name', 'last_name', 'email', 'phoneNumber', 'profile_image', 'current_latitude', 'current_longitude', 'online_offline', 'free_busy')->where([['merchant_id', '=', $merchant_id], ['current_latitude', '!=', null], ['login_logout', '=', 1], ['online_offline', '=', 1], ['free_busy', '=', 2],['driver_delete','=',null]])->get();
                break;
            case "3":
//                Enroute to Pickup
                $drivers = Driver::select('id', 'first_name', 'last_name', 'email', 'phoneNumber', 'profile_image', 'current_latitude', 'current_longitude', 'online_offline', 'free_busy')
                    ->where([['merchant_id', '=', $merchant_id], ['current_latitude', '!=', null], ['login_logout', '=', 1], ['free_busy', '=', 1]])
                    ->whereHas('Booking', function ($query) {
                        $query->where([['booking_status', '=', 1002]]);
                    })
                    ->get();
                break;
            case "4":
                $drivers = Driver::select('id', 'first_name', 'last_name', 'email', 'phoneNumber', 'profile_image', 'current_latitude', 'current_longitude', 'online_offline', 'free_busy')
                    ->where([['merchant_id', '=', $merchant_id], ['current_latitude', '!=', null], ['login_logout', '=', 1], ['free_busy', '=', 1]])
                    ->whereHas('Booking', function ($query) {
                        $query->where([['booking_status', '=', 1003]]);
                    })
                    ->get();
                break;
            case "5":
                $drivers = Driver::select('id', 'first_name', 'last_name', 'email', 'phoneNumber', 'profile_image', 'current_latitude', 'current_longitude', 'online_offline', 'free_busy')
                    ->where([['merchant_id', '=', $merchant_id], ['current_latitude', '!=', null], ['login_logout', '=', 1], ['free_busy', '=', 1]])
                    ->whereHas('Booking', function ($query) {
                        $query->where([['booking_status', '=', 1004]]);
                    })
                    ->get();
                break;
            case "6":
                $drivers = Driver::select('id', 'first_name', 'last_name', 'email', 'phoneNumber', 'profile_image', 'current_latitude', 'current_longitude', 'online_offline', 'free_busy')->where([['merchant_id', '=', $merchant_id], ['current_latitude', '!=', null], ['login_logout', '=', 1], ['online_offline', '=', 2]])->get();
                break;
            default:
                $drivers = Driver::select('id', 'first_name', 'last_name', 'email', 'phoneNumber', 'profile_image', 'current_latitude', 'current_longitude', 'online_offline', 'free_busy')->where([['merchant_id', '=', $merchant_id], ['current_latitude', '!=', null], ['login_logout', '=', 1]])->get();
        }
        $mapMarkers = array();
        foreach ($drivers as $values) {
            $online_offline = $values->online_offline;
            if ($online_offline == 2) {
                $marker_icon = view_config_image("marker/offline.png");
            } else {
                if ($values->free_busy == 1) {
                    $lastRide = Booking::where([['driver_id', '=', $values->id]])->whereIn('booking_status', array(1002, 1003, 1004))->first();
                    $booking_status = $lastRide->booking_status;
                    switch ($booking_status) {
                        case "1002":
                            $marker_icon = view_config_image("marker/Enroute-to-Pickup.png");
                            break;
                        case "1003":
                            $marker_icon = view_config_image("marker/Reached-Pickup.png");
                            break;
                        case "1004":
                            $marker_icon = view_config_image("marker/Journey-Started.png");
                            break;
                        default:
                            $marker_icon = view_config_image("marker/available.png");
                    }
                } else {
                    $marker_icon = view_config_image("marker/available.png");
                }
            }
            if(Auth::user()->demo == 1){
                $mapMarkers[] = array(
                    'marker_id' => $values->id,
                    'marker_name' => "********".substr($values->first_name . $values->last_name, -2),
                    'marker_address' => "",
                    'marker_number' => "********".substr($values->phoneNumber, -2),
                    'marker_email' => "********".substr($values->email, -2),
                    'marker_latitude' => $values->current_latitude,
                    'marker_longitude' => $values->current_longitude,
                    'marker_image' => get_image($values->profile_image,'driver'),
                    'marker_icon' => $marker_icon,
                );
            }
            else{
                $mapMarkers[] = array(
                    'marker_id' => $values->id,
                    'marker_name' => $values->first_name . $values->last_name,
                    'marker_address' => "",
                    'marker_number' => $values->phoneNumber,
                    'marker_email' => $values->email,
                    'marker_latitude' => $values->current_latitude,
                    'marker_longitude' => $values->current_longitude,
                    'marker_image' => get_image($values->profile_image,'driver'),
                    'marker_icon' => $marker_icon,
                );
            }
        }
        echo json_encode($mapMarkers, true);
    }

    public function BookingDispatch(ManualDispatch $request)
    {
        $corporate = Auth::user('corporate');
        $merchant_id = $corporate->merchant_id;
        $query = PriceCard::where([['country_area_id', '=', $request->area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type]]);
        if (!empty($request->package) && $request->package != "null") {
            $query->where([['package_id', '=', $request->package]]);
        }
        $pricecards = $query->first();
        if (empty($pricecards)) {
            return redirect()->back()->with('nodriver', trans("$string_file.no_price_card_for_area"));
        }
//        $this->SetTimeZone($request->area);
        $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $findDriver = new FindDriverController();
        $muliLocation = $this->MultipleLocation();
        if ($request->booking_type == 2) {
            switch ($request->service) {
                case "1":
                    $requestType = $configuration->normal_ride_later_request_type;
                    break;
                case "2":
                    $requestType = $configuration->rental_ride_later_request_type;
                    break;
                case "3":
                    $requestType = $configuration->transfer_ride_later_request_type;
                    break;
                case "4":
                    $requestType = $configuration->outstation_request_type;
                    break;
            }
        }
        switch ($request->driver_request) {
            case "1":
                $drivers = $this->getDrivers();
                if (empty($drivers) && \request()->booking_type == 1) {
                    return redirect()->back()->with('nodriver', trans('api.message58'));
                }
                $booking = $this->AddBooking($request, $muliLocation, $merchant_id, $pricecards->id, $drivers, $configuration->google_key,$corporate->id);
                if ($request->booking_type == 1):
                    $findDriver->AssignRequest($drivers, $booking->id);
                    $message = "New Booking";
                    $bookingData = new BookingDataController();
                    $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
                else:
                    if ($requestType == 1 && !empty($drivers)) {
                        $message = "There is new upcomming Booking";
                        $bookingData = new BookingDataController();
                        $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
                    }
                endif;
                break;
            case "2":
            case "3":
                $driver_id[] = request()->driver_id;
//                $drivers = Driver::GetNearestDriverByIds($request->area, $request->pickup_latitude, $request->pickup_longitude, $request->vehicle_type, $request->service, $driver_id);
                $drivers = Driver::GetNearestDriver([
                'area'=>$request->area,
                'latitude'=>$request->pickup_latitude,
                'longitude'=>$request->pickup_longitude,
                'vehicle_type'=>$request->vehicle_type,
                'driver_ids'=>$driver_id,
                'service_type'=>$request->service]);
                if ($drivers->count() == 0){
                    return redirect()->route('merchant.manualdispach')->with('success',trans('admin.no_driver_found'));
                }
                $booking = $this->AddBooking($request, $muliLocation, $merchant_id, $pricecards->id, $drivers, $configuration->google_key, $corporate->id,'single');
                if ($request->booking_type == 1):
                    $findDriver->AssignRequest($drivers, $booking->id);
                    $message = "New Booking";
                    $bookingData = new BookingDataController();
                    $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
                else:
                    if ($requestType == 1 && !empty($drivers)) {
                        $message = "There Is New Upcomming Booking";
                        $bookingData = new BookingDataController();
                        $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
                    }
                endif;
                break;
        }
        return redirect()->route('corporate.ride-requests', $booking->id);
//        die();
//
//        $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
//
//        $driver_request = $request->driver_request;
//        $drivers = array();
//        switch ($driver_request) {
//            case "1":
//                $drivers = Driver::GetNearestDriver($request->area, $request->pickup_latitude, $request->pickup_longitude, $request->ride_radius, 500, $request->vehicle_type, $request->service);
//                $drivers = $drivers->toArray();
//                break;
//            case "2":
//                $driver_id = array($request->favourite_list);
//                $drivers = Driver::GetNearestDriverByIds($request->area, $request->pickup_latitude, $request->pickup_longitude, $request->vehicle_type, $request->service, $driver_id);
//                $drivers = $drivers->toArray();
//                break;
//            case "3":
//                $driver_id = array($request->driver_id);
//                $drivers = Driver::GetNearestDriverByIds($request->area, $request->pickup_latitude, $request->pickup_longitude, $request->vehicle_type, $request->service, $driver_id);
//                $drivers = $drivers->toArray();
//                break;
//        }
//
//        $findDriver = new FindDriverController();
//        switch ($request->service) {
//            case "1":
//                if ($request->booking_type == 1) { //Ride Now
//                    switch ($driver_request) {
//                        case "1":
//                            $drivers = $findDriver->GetAllNearestDriver($request->area, $request->pickup_latitude, $request->pickup_longitude, $configuration->normal_ride_now_radius, $configuration->normal_ride_now_request_driver, $request->vehicle_type, $request->service);
//                            break;
//                        case "2":
//                            $driver_id = array($request->favourite_list);
//                            $drivers = Driver::GetNearestDriverByIds($request->area, $request->pickup_latitude, $request->pickup_longitude, $request->vehicle_type, $request->service, $driver_id);
//                            break;
//                        case "3":
//                            $driver_id = array($request->driver_id);
//                            $drivers = Driver::GetNearestDriverByIds($request->area, $request->pickup_latitude, $request->pickup_longitude, $request->vehicle_type, $request->service, $driver_id);
//                            break;
//                    }
//                    //$drivers = $findDriver->GetAllNearestDriver($request->area, $request->pickup_latitude, $request->pickup_longitude, $configuration->normal_ride_now_radius, $configuration->number_of_driver, $request->vehicle_type, $request->service);
//                    if (empty($drivers)) {
//                        return redirect()->back()->with('nodriver', trans('api.message58'));
//                    }
//                    $booking = $this->AddBooking($request, $muliLocation, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
//                    $findDriver->AssignRequest($drivers, $booking->id);
//                    $message = "New Booking";
//                    $bookingData = new BookingDataController();
//                    $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
//                } else {  // Ride Later
//                    $drivers = [];
//                    $booking = $this->AddBooking($request, $muliLocation, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
//                    if ($configuration->normal_ride_later_request_type == 1) {
//                        $findDriver = new FindDriverController();
//                        switch ($driver_request) {
//                            case "1":
//                                $drivers = $findDriver->GetAllNearestDriver($request->area, $request->pickup_latitude, $request->pickup_longitude, $configuration->normal_ride_later_radius, $configuration->normal_ride_later_request_driver, $request->vehicle_type, $request->service);
//                                break;
//                            case "2":
//                                $driver_id = array($request->favourite_list);
//                                $drivers = Driver::GetNearestDriverByIds($request->area, $request->pickup_latitude, $request->pickup_longitude, $request->vehicle_type, $request->service, $driver_id);
//                                break;
//                            case "3":
//                                $driver_id = array($request->driver_id);
//                                $drivers = Driver::GetNearestDriverByIds($request->area, $request->pickup_latitude, $request->pickup_longitude, $request->vehicle_type, $request->service, $driver_id);
//                                break;
//                        }
//                        //$drivers = $findDriver->GetAllNearestDriver($booking->country_area_id, $booking->pickup_latitude, $booking->pickup_longitude, $configuration->normal_ride_later_radius, $configuration->normal_ride_later_request_driver, $booking->vehicle_type_id, $booking->service_type_id);
//                        if (!empty($drivers)) {
//                            $message = "There Is New Upcomming Booking";
//                            $bookingData = new BookingDataController();
//                            $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
//                        }
//                    }
//                }
//                break;
//            case "2":
//                if ($request->booking_type == 1) { // Ride Now
//                    switch ($driver_request) {
//                        case "1":
//                            $drivers = $findDriver->GetAllNearestDriver($request->area, $request->pickup_latitude, $request->pickup_longitude, $configuration->rental_ride_now_radius, $configuration->rental_ride_now_request_driver, $request->vehicle_type, $request->service);
//                            break;
//                        case "2":
//                            $driver_id = array($request->favourite_list);
//                            $drivers = Driver::GetNearestDriverByIds($request->area, $request->pickup_latitude, $request->pickup_longitude, $request->vehicle_type, $request->service, $driver_id);
//                            break;
//                        case "3":
//                            $driver_id = array($request->driver_id);
//                            $drivers = Driver::GetNearestDriverByIds($request->area, $request->pickup_latitude, $request->pickup_longitude, $request->vehicle_type, $request->service, $driver_id);
//                            break;
//                    }
//                    //$drivers = $findDriver->GetAllNearestDriver($request->area, $request->pickup_latitude, $request->pickup_longitude, $configuration->rental_ride_now_radius, $configuration->rental_ride_now_request_driver, $request->vehicle_type, $request->service);
//                    if (empty($drivers)) {
//                        return redirect()->back()->with('nodriver', trans('api.message58'));
//                    }
//                    $booking = $this->AddBooking($request, $muliLocation, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
//                    $findDriver->AssignRequest($drivers, $booking->id);
//                    $message = "New Booking";
//                    $bookingData = new BookingDataController();
//                    $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
//                } else {
//                    $drivers = [];
//                    $booking = $this->AddBooking($request, $muliLocation, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
//                    if ($configuration->rental_ride_later_request_type == 1) {
//                        $findDriver = new FindDriverController();
//                        switch ($driver_request) {
//                            case "1":
//                                $drivers = $findDriver->GetAllNearestDriver($request->area, $request->pickup_latitude, $request->pickup_longitude, $configuration->rental_ride_later_radius, $configuration->rental_ride_later_request_driver, $request->vehicle_type, $request->service);
//                                break;
//                            case "2":
//                                $driver_id = array($request->favourite_list);
//                                $drivers = Driver::GetNearestDriverByIds($request->area, $request->pickup_latitude, $request->pickup_longitude, $request->vehicle_type, $request->service, $driver_id);
//                                break;
//                            case "3":
//                                $driver_id = array($request->driver_id);
//                                $drivers = Driver::GetNearestDriverByIds($request->area, $request->pickup_latitude, $request->pickup_longitude, $request->vehicle_type, $request->service, $driver_id);
//                                break;
//                        }
//                        //$drivers = $findDriver->GetAllNearestDriver($booking->country_area_id, $booking->pickup_latitude, $booking->pickup_longitude, $configuration->rental_ride_later_radius, $configuration->rental_ride_later_request_driver, $booking->vehicle_type_id, $booking->service_type_id);
//                        if (!empty($drivers)) {
//                            $message = "There Is New Upcomming Booking";
//                            $bookingData = new BookingDataController();
//                            $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
//                        }
//                    }
//                }
//            case "3":
//                // if ($request->booking_type == 1) {
//                // $drivers = $findDriver->GetAllNearestDriver($request->area, $request->pickup_latitude, $request->pickup_longitude, $configuration->distance, $configuration->number_of_driver, $request->vehicle_type, $request->service);
//                // if (empty($drivers)) {
//                // return redirect()->back()->with('nodriver', trans('api.message58'));
//                // }
//                // $booking = $this->AddBooking($request, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
//                // $findDriver->AssignRequest($drivers, $booking->id);
//                // $message = "New Booking";
//                // $bookingData = new BookingDataController();
//                // $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
//                // } else {
//                // $drivers = [];
//                // $booking = $this->AddBooking($request, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
//                // if ($configuration->ride_later_request == 1) {
//                // $findDriver = new FindDriverController();
//                // $drivers = $findDriver->GetAllNearestDriver($booking->country_area_id, $booking->pickup_latitude, $booking->pickup_longitude, $configuration->distance_ride_later, $configuration->ride_later_request_number_driver, $booking->vehicle_type_id, $booking->service_type_id);
//                // if (!empty($drivers)) {
//                // $message = "There Is New Upcomming Booking";
//                // $bookingData = new BookingDataController();
//                // $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
//                // }
//                // }
//                // }
//                // break;
//            case"4":
//                $drivers = [];
//                $booking = $this->AddBooking($request, $muliLocation, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
//                if ($configuration->outstation_request_type == 1) {
//                    $findDriver = new FindDriverController();
//                    $drivers = $findDriver->GetAllNearestDriver($booking->country_area_id, $booking->pickup_latitude, $booking->pickup_longitude, $configuration->outstation_radius, $configuration->outstation_request_driver, $booking->vehicle_type_id, $booking->service_type_id);
//                    if (!empty($drivers)) {
//                        $message = "There Is New Upcomming Booking";
//                        $bookingData = new BookingDataController();
//                        $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
//                    }
//                }
//                break;
//            case "5":
//                if ($request->booking_type == 1) {
//                    $vehicle_type_id = [$request->vehicle_type];
//                    $drivers = $findDriver->getPoolDriver($request->area, $request->pickup_latitude, $request->drop_latitude, $request->drop_longitude, $request->pickup_longitude, $configuration->pool_radius, $configuration->pool_now_request_driver, 1, $vehicle_type_id, $configuration->pool_drop_radius);
//                    if (empty($drivers)) {
//                        return redirect()->back()->with('nodriver', trans('api.message58'));
//                    }
//                    $booking = $this->AddBooking($request, $muliLocation, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
//                    $findDriver->AssignRequest($drivers, $booking->id);
//                    $message = "New Booking";
//                    $bookingData = new BookingDataController();
//                    $bookingData->SendNotificationToDrivers($booking, $drivers, $message);
//                }
//                break;
//        }
//        return redirect()->route('merchant.ride-requests', $booking->id);
    }

    public function MultipleLocation()
    {
        $muliLocation = array();
        if (!empty(\request()->multiple_destination)) {
            $old_array = \request()->multiple_destination;
            $tot_loc = count($old_array);
            for ($i = 0; $i < $tot_loc; $i++) {
                $muliLocation[$i]['stop'] = $i;
                $muliLocation[$i]['drop_location'] = $old_array[$i];
                $muliLocation[$i]['drop_latitude'] = $_REQUEST['multiple_destination_lat_' . ($i + 1)];
                $muliLocation[$i]['drop_longitude'] = $_REQUEST['multiple_destination_lng_' . ($i + 1)];
                $muliLocation[$i]['status'] = 1;
                $muliLocation[$i]['end_latitude'] = "";
                $muliLocation[$i]['end_longitude'] = "";
                $muliLocation[$i]['end_time'] = "";
            }
        }
    }

    public function SetTimeZone($areaID)
    {
        $area = CountryArea::find($areaID);
        if (!empty($area)) {
//            date_default_timezone_set($area->timezone);
        }
    }

    public function getDrivers()
    {
//        $this->SetTimeZone(request()->area);
        $findDriver = new FindDriverController();
        $drivers = $findDriver->GetAllNearestDriver(request()->area, request()->pickup_latitude, request()->pickup_longitude, request()->ride_radius, 500, request()->vehicle_type, request()->service);
        return $drivers;
    }


    public function AddBooking($request, $muliLocation = null, $merchant_id, $pricecardid, $drivers, $key, $corporate_id,$request_type = null)
    {
        $driver_id = null;
        if($request_type != null && $request_type == 'single'){
            $driver_id = $drivers[0]->driver_id;
        }
        $from = $request->pickup_latitude . "," . $request->pickup_longitude;
        if (!empty($drivers)) {
            $current_latitude = $drivers[0]->current_latitude;
            $current_longitude = $drivers[0]->current_longitude;
            $driverLatLong = $current_latitude . "," . $current_longitude;
            $nearDriver = DistanceController::DistanceAndTime($from, $driverLatLong, $key);
            $estimate_driver_distnace = $nearDriver['distance'];
            $estimate_driver_time = $nearDriver['time'];
        } else {
            $estimate_driver_distnace = "";
            $estimate_driver_time = "";
        }
        if (!empty($muliLocation)) {
            $tot_loc = count($muliLocation);
            $new_array[$tot_loc]['drop_location'] = $request->drop_location;
            $new_array[$tot_loc]['drop_latitude'] = $request->drop_latitude;
            $new_array[$tot_loc]['drop_longitude'] = $request->drop_longitude;
            $static_image = array_merge($muliLocation, $new_array);
            $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $static_image, $key,"",$string_file);
        } else {
            $drop_locationArray = [];
            if (!empty($request->drop_latitude)) {
                $drop_locationArray[] = array('drop_latitude' => $request->drop_latitude, 'drop_longitude' => $request->drop_longitude);
            }
            $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $key);
        }
        $additional_notes = NULL;
        if(isset($request->note)){
            $additional_notes = $request->note;
        }
        if($muliLocation != null){
            $muliLocation = json_encode($muliLocation);
        }
        $booking = Booking::create([
            'merchant_id' => $merchant_id,
            'corporate_id' => $corporate_id,
            'user_id' => $request->user_id,
            'driver_id' => $driver_id,
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
            'additional_notes' => $additional_notes,
            'pickup_location' => $request->pickup_location,
            'estimate_distance' => $googleArray['total_distance_text'],
            'estimate_time' => $googleArray['total_time_text'],
            'payment_method_id' => $request->payment_method_id,
            'estimate_bill' => $request->estimate_fare,
            'booking_timestamp' => strtotime("now"),
            'booking_status' => 1001,
            'package_id' => $request->package,
            'later_booking_date' => $request->date ? date("Y-m-d", strtotime($request->date)) : "",
            'later_booking_time' => $request->time,
            'return_date' => $request->retrun_date,
            'return_time' => $request->retrun_time,
            'estimate_driver_distnace' => $estimate_driver_distnace,
            'estimate_driver_time' => $estimate_driver_time,
            'waypoints' => $muliLocation
        ]);
        return $booking;
    }


    public function PromoCode(Request $request)
    {
        $manual_area = $request->manual_area;
        $promocodes = PromoCode::where([['country_area_id', '=', $manual_area],['promo_code_status', '=', 1],['deleted', '=', 0]])->get();
        if (!empty($promocodes)) {
            echo "<option value=''>Select Promo Code</option>";
            foreach ($promocodes as $promocode) {
                echo "<option value='" . $promocode['id'] . "'>" . $promocode['promoCode'] . "</option>";
            }
        } else {
            echo "<option value=''>No Promo Code Found For This User</option>";
        }
    }

    public function PromoCodeEta(Request $request){
        $promocode = PromoCode::find($request->promocode_id);
        if (!empty($promocode)){
            if ($request->estimate_fare < $promocode->promo_code_value){
                $eta = 0.00;
            }else{
                $eta = $request->estimate_fare - $promocode->promo_code_value;
            }
            echo $eta;
        }
    }

    public function EstimatePrice(Request $request)
    {
        $merchant_id = Auth::user('corporate')->merchant_id;
        $merchant = new Merchant();
        $price = PriceCard::where([['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type]])->first();
        if (empty($price)) {
            echo "No Price Card Found";
        } else {
            if (in_array($price->CountryArea->timezone, \DateTimeZone::listIdentifiers())) {
//                date_default_timezone_set($price->CountryArea->timezone);
            }
            switch ($price->pricing_type) {
                case "1":
                case "2":
                    $estimatePrice = new PriceController();
                    $time = sprintf("%0.2f", $request->ride_time / 60);
                    $fare = $estimatePrice->BillAmount([
                        'price_card_id' => $price->id,
                        'merchant_id' => $price->merchant_id,
                        'distance' => $request->distance,
                        'time' => $time,
                        'booking_id' => 0,
                        'booking_time' => date('H:i'),
                        'units' => $request->distance_unit
                    ]);
                    $amount = $fare['amount'];
                    break;
                case "3":
                    $amount = trans('api.message62');
                    break;
            }
            $amount = $merchant->FinalAmountCal($amount,$merchant_id);
            echo $amount;
        }
    }

    public function CheckDriver(Request $request)
    {
        $merchant_id = Auth::user('corporate')->merchant_id;
//        $this->SetTimeZone(request()->manual_area);
        $km = $request->radius;
        $pickup_latitude = $request->pickup_latitude;
        $pickup_longitude = $request->pickup_longitude;
        $vehicle_type_id = $request->vehicle_type;
        $service_id = $request->service;
        $config = ApplicationConfiguration::where('merchant_id','=',$merchant_id)->first();
        $driver_obj = new Driver();
        if ($config->gender == 1){
            $drivers = $driver_obj->getNearestDriverGender($request->manual_area, $pickup_latitude, $pickup_longitude, $km, 500, $vehicle_type_id, $service_id,$request->distance_unit,$request->driver_gender);
        }else{
            $drivers = $driver_obj->GetNearestDriver($request->manual_area, $pickup_latitude, $pickup_longitude, $km, 500, $vehicle_type_id, $service_id);
        }
        //GetNearestDriver($area, $latitude, $longitude, $distance = 1, $limit = 1, $vehicle_type_id, $service_type_id, $user_gender = NULL, $driver_request_time_out = 60)
        // print_r($drivers->toArray());die();
        echo count($drivers->toArray());
    }

    public function AllDriver(Request $request)
    {
        $merchant_id = Auth::user('corporate')->merchant_id;
//        $this->SetTimeZone(request()->manual_area);
        $km = $request->radius;
        $pickup_latitude = $request->pickup_latitude;
        $pickup_longitude = $request->pickup_longitude;
        $vehicle_type_id = $request->vehicle_type;
        $service_id = $request->service;
        $unit = $request->distance_unit == 1 ? "Km" : "Miles";
        $config = ApplicationConfiguration::where('merchant_id','=',$merchant_id)->first();
        $driver_obj = new Driver();
        if ($config->gender == 1){
            $drivers = $driver_obj->getNearestDriverGender($request->manual_area, $pickup_latitude, $pickup_longitude, $km, 500, $vehicle_type_id, $service_id,$request->distance_unit,$request->driver_gender);
        }else{
            $drivers = $driver_obj->GetNearestDriver($request->manual_area, $pickup_latitude, $pickup_longitude, $km, 500, $vehicle_type_id, $service_id,$request->distance_unit);
        }
        if (empty($drivers->toArray())) {
            echo "<option value=''>No Driver Online</option>";
        } else {
            echo "<option value=''>Select Driver</option>";
            if (Auth::user()->demo == 1){
                foreach ($drivers as $driver) {
                    $driver_name = $driver->fullName . "(" . "********".substr($driver->phoneNumber, -2) . ")" . "(" .sprintf("%0.2f", $driver->distance) . " ".$unit.")";
                    echo "<option value='" . $driver->driver_id . "'>" . $driver_name . "</option>";
                }
            }else{
                foreach ($drivers as $driver) {
                    $driver_name = $driver->fullName . "(" . $driver->phoneNumber . ")" . "(" . sprintf("%0.2f", $driver->distance) . " ".$unit.")";
                    echo "<option value='" . $driver->driver_id . "'>" . $driver_name . "</option>";
                }
            }
        }
    }

    public function FavouriteDriver(Request $request)
    {
//        $this->SetTimeZone(request()->manual_area);
        $user_id = $request->user_id;
        $pickup_latitude = $request->pickup_latitude;
        $pickup_longitude = $request->pickup_longitude;
        $vehicle_type_id = $request->vehicle_type;
        $service_id = $request->service;
        $unit = $request->distance_unit == 1 ? "Km" : "Miles";
        $drivers = FavouriteDriver::where([['user_id', '=', $user_id]])->get();
        if (empty($drivers->toArray())) {
            echo "<option value=''>Sorry No Driver Online</option>";
        } else {
            $drivers = $drivers->toArray();
            $driver_id = array_pluck($drivers, 'id');
            $drivers = Driver::GetNearestDriverByIds($request->manual_area, $pickup_latitude, $pickup_longitude, $vehicle_type_id, $service_id, $driver_id);
            echo "<option value=''>Select Driver</option>";
            if (Auth::user()->demo == 1){
                foreach ($drivers as $driver) {
                    $driver_name = $driver->fullName . "(" . "********".substr($driver->phoneNumber, -2) . ")" . "(" . sprintf("%0.2f", $driver->distance) ." ".$unit.")";
                    echo "<option value='" . $driver->driver_id . "'>" . $driver_name . "</option>";
                }
            }else{
                foreach ($drivers as $driver) {
                    $driver_name = $driver->fullName . "(" . $driver->phoneNumber . ")" . "(" . sprintf("%0.2f", $driver->distance) ." ".$unit.")";
                    echo "<option value='" . $driver->driver_id . "'>" . $driver_name . "</option>";
                }
            }
        }
    }

    public function SearchUser(Request $request)
    {
        $corporate = Auth::user('corporate');
        $booking_config = BookingConfiguration::where('merchant_id','=',$corporate->merchant_id)->first();
        $rider = User::where([['merchant_id', '=', $corporate->merchant_id],['corporate_id','=',$corporate->id], ['UserPhone', '=', $request->user_phone],['user_delete','=', NULL]])->first();
        $id = $rider->id;
        if ($rider->country_id){
            $country = Country::where([['merchant_id', '=', $corporate->merchant_id],['id','=',$rider->country_id]])->first();
            $distance_unit = $country->distance_unit;
            $iso = $country->isoCode;
        }else{
            $country = Country::where([['merchant_id', '=', $corporate->merchant_id],['id','=',$request->country_id]])->first();
            $distance_unit = $country->distance_unit;
            $iso = $country->isoCode;
        }
        return array('id'=>$id,'distance_unit'=>$distance_unit,'multi_destination'=>$booking_config->multi_destination,'user_gender'=>$rider->user_gender,'iso'=>$iso,'max_multi_count'=> $booking_config->count_multi_destination);
    }


    public function AddManualUser(Request $request)
    {
        $corporate = Auth::user('corporate');
        $merchant_id = $corporate->merchant_id;
        $booking_config = BookingConfiguration::where('merchant_id','=',$merchant_id)->first();
        $this->validate($request, [
            'first_name' => 'required|alpha',
            'last_name' => 'required|alpha',
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
        $rider = User::create([
            'merchant_id' => $merchant_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'UserPhone' => $request->new_user_phone,
            'email' => $request->new_user_email,
            'user_gender' => $request->gender,
            'password' => $password,
            'UserSignupType' => 1,
            'UserSignupFrom' => 2,
            'UserProfileImage' => "",
            'user_type' => 1,
            'country_id' => $request->country_id,
            'corporate_id' => $corporate->id
        ]);
        $country = Country::where([['merchant_id', '=', $merchant_id],['id','=',$request->country_id]])->first();
        $distance_unit = $country->distance_unit;
        $iso = $country->isoCode;
        return array('id'=>$rider->id,'distance_unit'=>$distance_unit,'multi_destination'=>$booking_config->multi_destination,'user_gender'=>$rider->user_gender,'iso'=>$iso,'max_multi_count'=> $booking_config->count_multi_destination);
    }

}