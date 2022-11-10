<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\DistanceController;
use App\Http\Controllers\Helper\FindDriverController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\Merchant;
use App\Http\Controllers\Helper\PolygenController;
use App\Http\Controllers\Helper\PriceController;
use App\Http\Requests\ManualDispatch;
use App\Models\ApplicationConfiguration;
use App\Models\Booking;
use App\Models\BookingConfiguration;
use App\Models\Configuration;
use App\Models\Corporate;
use App\Models\Country;
use App\Models\CountryArea;
use App\Models\Driver;
use App\Models\Outstanding;
use App\Models\PaymentMethod;
use App\Models\PriceCard;
use App\Models\PromoCode;
use App\Models\Segment;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\AreaTrait;

class ManualDispatchController extends Controller
{
    use AreaTrait;
    public function index()
    {
        $hotel = get_hotel();
        $merchant_id = $hotel->merchant_id;
        $config = ApplicationConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $areas = CountryArea::where([['merchant_id', '=', $merchant_id]])->get();
        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
        $config = ApplicationConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        return view('hotel.manual.index', compact('areas', 'countries','config'));
    }

    public function TestIndex(){
        $hotel = get_hotel();
        $merchant_id = $hotel->merchant_id;
        $config = ApplicationConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $baseConfig = Configuration::select('corporate_admin')->where('merchant_id', '=', $merchant_id)->first();
        $bookingConfig = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $corporates = Corporate::where([['merchant_id', '=', $merchant_id]])->latest()->get();
        $countries = Country::where([['merchant_id', '=', $merchant_id], ['country_status', '=', 1]])->get();
        $paymentmethods = PaymentMethod::get();
        return view('hotel.manual.manual', compact('config', 'paymentmethods', 'corporates', 'countries', 'baseConfig','bookingConfig','hotel'));
    }

    public function SearchUser(Request $request)
    {
        $hotel = get_hotel();
        $merchant_id = $hotel->merchant_id;
        $booking_config = BookingConfiguration::where('merchant_id','=',$merchant_id)->first();
        $rider = User::where([['merchant_id', '=', $merchant_id], ['UserPhone', '=', $request->user_phone],['user_delete','=', NULL]])->first();
        $id = $rider->id;
        if ($rider->country_id){
            $country = Country::where([['merchant_id', '=', $merchant_id],['id','=',$rider->country_id]])->first();
            $distance_unit = $country->distance_unit;
            $iso = $country->isoCode;
        }else{
            $country = Country::where([['merchant_id', '=', $merchant_id],['id','=',$request->country_id]])->first();
            $distance_unit = $country->distance_unit;
            $iso = $country->isoCode;
        }
        return array('id'=>$id,'distance_unit'=>$distance_unit,'multi_destination'=>$booking_config->multi_destination,'user_gender'=>$rider->user_gender,'iso'=>$iso,'max_multi_count'=> $booking_config->count_multi_destination);
    }

    public function AddManualUser(Request $request)
    {
        $hotel = get_hotel();
        $merchant_id = $hotel->merchant_id;
        $booking_config = BookingConfiguration::where('merchant_id','=',$merchant_id)->first();
        $val = validator($request->all(),[
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

        if ($val->fails()){
            return error_response($val->errors()->first());
        }

        $password = "";
        $user = new User();
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
            'ReferralCode' => $user->GenrateReferCode(),
            'UserProfileImage' => "",
            'user_type' => 2,
            'country_id' => $request->country_id,
        ]);
        $country = Country::where([['merchant_id', '=', $merchant_id],['id','=',$request->country_id]])->first();
        $distance_unit = $country->distance_unit;
        $iso = $country->isoCode;
        return array('id'=>$rider->id,'distance_unit'=>$distance_unit,'multi_destination'=> $booking_config->multi_destination,'user_gender'=>$rider->user_gender,'iso'=>$iso,'max_multi_count'=> $booking_config->count_multi_destination);
    }

    public function BookingDispatch(ManualDispatch $request)
    {
        $hotel = get_hotel();
        $hotel_id = $hotel->id;
        $merchant_id = $hotel->merchant_id;
        $query = PriceCard::where([['country_area_id', '=', $request->manual_area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type]]);
        if (!empty($request->package) && $request->package != "null") {
            $query->where([['package_id', '=', $request->package]]);
        }
        $pricecards = $query->first();
        if (empty($pricecards)) {
            return redirect()->back()->withErrors(trans("$string_file.no_price_card_for_area"));
        }
//        $this->SetTimeZone($request->manual_area);
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
                $drivers = $this->getDrivers($request);
                if (empty($drivers) && \request()->booking_type == 1) {
                    return redirect()->back()->withErrors(trans("$string_file.no_driver_available"));
                }
                $booking = $this->AddBooking($request, $muliLocation, $merchant_id, $pricecards->id, $drivers, $configuration->google_key);
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
            case "2":
            case "3":
                $driver_id[] = request()->driver_id;
                $drivers = Driver::GetNearestDriver([
                    'area' => $request->manual_area,
                    'latitude' => $request->pickup_latitude,
                    'longitude' => $request->pickup_longitude,
                    'service_type' => $request->service,
                    'vehicle_type' => $request->vehicle_type,
                    'driver_ids' => $driver_id,
                    'merchant_id' => $merchant_id,
                ]);
                if (empty($drivers)) {
                    return redirect()->route('hotel.test.manualdispach')->withErrors(trans('admin.no_driver_found'));
                }
                $booking = $this->AddBooking($request, $muliLocation, $merchant_id, $pricecards->id, $drivers, $configuration->google_key, 'single');
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
        // return view('taxicompany.manual.loader', compact('id', 'time'));
        return redirect()->route('hotel.ride-requests', $booking->id);
    }

    public function AddBooking($request, $muliLocation = null, $merchant_id, $pricecardid, $drivers, $key, $request_type = null)
    {
        $hotel_id = get_hotel(true);
        $driver_id = null;
        if ($request_type != null && $request_type == 'single') {
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
            $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $static_image, $key);
        } else {
            $drop_locationArray = [];
            if (!empty($request->drop_latitude)) {
                $drop_locationArray[] = array('drop_latitude' => $request->drop_latitude, 'drop_longitude' => $request->drop_longitude);
            }
            $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $key);
        }

        // Generate bill details
        $estimatePrice = new PriceController();
        $outstanding_amount = Outstanding::where('user_id', $request->user_id)->sum('amount');
        $newBookingData = new BookingDataController();
        $to = "";
        if (!empty($drop_locationArray)) {
            $lastLocation = $newBookingData->wayPoints($drop_locationArray);
            $to = $lastLocation['last_location']['drop_latitude'] . "," . $lastLocation['last_location']['drop_longitude'];
        }
        $fare = $estimatePrice->BillAmount([
            'hotel_id' => $hotel_id,
            'price_card_id' => $pricecardid,
            'merchant_id' => $merchant_id,
            'distance' => $googleArray['total_distance'],
            'time' => $googleArray['total_time_minutes'],
            'booking_id' => 0,
            'user_id' => $request->user_id,
            'booking_time' => date('H:i'),
            'outstanding_amount' => $outstanding_amount,
            'units' => CountryArea::find($request->manual_area)->Country['distance_unit'],
            'from' => $from,
            'to' => $to,
        ]);

        if($request->promo_code){
            $promoCode = PromoCode::find($request->promo_code);
            if (!empty($promoCode)) {
                $code = $promoCode->promoCode;
                if ($promoCode->promo_code_value_type == 1) {
                    $promoDiscount = $promoCode->promo_code_value;
                } else {
                    $promoDiscount = ($fare['amount'] * $promoCode->promo_code_value) / 100;
                    $promoMaxAmount = $promoCode->promo_percentage_maximum_discount;
                    $promoDiscount = ($promoDiscount > $promoMaxAmount) ? $promoMaxAmount : $promoDiscount;
                }
                $request->estimate_fare = $fare['amount'] > $promoDiscount ? $fare['amount'] - $promoDiscount : '0.00';
                $parameter = array('subTotal' => $promoCode->id, 'price_card_id' => $pricecardid, 'booking_id' => 0, 'parameter' => $code, 'parameterType' => "PROMO CODE", 'amount' => (string)$promoDiscount, 'type' => "DEBIT", 'code' => $code, 'freeValue' => $promoCode->promo_code_value);
                array_push($fare['bill_details'], $parameter);
            }
        }
        $bill_details = json_encode($fare['bill_details'], true);

        $additional_notes = NULL;
        if (isset($request->note)) {
            $additional_notes = $request->note;
        }
        $booking = Booking::create([
            'merchant_id' => $merchant_id,
            'hotel_id' => $hotel_id,
            'user_id' => $request->user_id,
            'driver_id' => $driver_id,
            'platform' => 2,
            'country_area_id' => $request->manual_area,
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
            'later_booking_date' => $request->date ? date("Y-m-d", strtotime($request->date)) : NULL,
            'later_booking_time' => $request->time,
            'return_date' => $request->retrun_date,
            'return_time' => $request->retrun_time,
            'estimate_driver_distnace' => $estimate_driver_distnace,
            'estimate_driver_time' => $estimate_driver_time,
            'waypoints' => json_encode($muliLocation, true),
            'bill_details' => $bill_details,
            'price_for_ride' => $request->price_for_ride,
            'price_for_ride_amount' => $request->price_for_ride_value,
            'promo_code' => $request->promo_code,
//            'hotel_charges' => $fare['hotel_amount'],
        ]);
        return $booking;
    }

    public function PromoCode(Request $request)
    {
        $hotel = get_hotel();
        $merchant_id = $hotel->merchant_id;
        $manual_area = $request->manual_area;
        $service = $request->service;
        $user_id = $request->user_id;
        $promocodes = PromoCode::where([['merchant_id', '=', $merchant_id],['country_area_id', '=', $manual_area], ['promo_code_status', '=', 1], ['deleted', '=', 0]])->get();
        if (!empty($promocodes)) {
            echo "<option value=''>Select Promo Code</option>";
            foreach ($promocodes as $promocode) {
                echo "<option value='" . $promocode['id'] . "'>" . $promocode['promoCode'] . "</option>";
            }
        } else {
            echo "<option value=''>No Promo Code Found For This User</option>";
        }
    }

    public function PromoCodeEta(Request $request)
    {
        $promocode = PromoCode::find($request->promocode_id);
        if (!empty($promocode)) {
            if ($request->estimate_fare < $promocode->promo_code_value) {
                $eta = 0.00;
            } else {
                $eta = $request->estimate_fare - $promocode->promo_code_value;
            }
            echo $eta;
        }
    }

//    public function EstimatePrice(Request $request)
//    {
//        $hotel = get_hotel();
//        $merchant_id = $hotel->merchant_id;
//        $merchant = new Merchant();
//        if($request->service == 2 || $request->service == 4){
//            $where = [['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type],['package_id',$request->package_id]];
//        }else{
//            $where = [['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type]];
//        }
//        $price = PriceCard::where($where)->first();
//        if (empty($price)) {
//            echo "No Price Card Found";
//        } else {
//            if (in_array($price->CountryArea->timezone, \DateTimeZone::listIdentifiers())) {
//                date_default_timezone_set($price->CountryArea->timezone);
//            }
//            switch ($price->pricing_type) {
//                case "1":
//                case "2":
//                    $estimatePrice = new PriceController();
//                    $time = sprintf("%0.2f", $request->ride_time / 60);
//                    $fare = $estimatePrice->BillAmount([
//                        'price_card_id' => $price->id,
//                        'merchant_id' => $price->merchant_id,
//                        'distance' => $request->distance,
//                        'time' => $time,
//                        'booking_id' => 0,
//                        'booking_time' => date('H:i'),
//                        'units' => $request->distance_unit,
//                        'hotel_id' => $hotel->id
//                    ]);
//                    $amount = $fare['amount'];
//                    break;
//                case "3":
//                    $amount = trans('api.message62');
//                    break;
//            }
//            $amount = $merchant->FinalAmountCal($amount, $merchant_id);
//            echo $amount;
//        }
//    }
    public function EstimatePrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service' => 'required',
            'area' => 'required',
            'vehicle_type' => 'required',
            'package_id' => 'required_if:service,2',
            'ride_time' => 'required',
            'distance' => 'required',
            'distance_unit' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return array('result' => 0, 'message' => $errors);
        }
        if(isset($request->outstation_type) && $request->outstation_type == 2){
            $validator = Validator::make($request->all(), [
                'package_id' => 'required_if:service,4',
            ]);
            if ($validator->fails()) {
                $errors = $validator->messages()->all();
                return array('result' => 0, 'message' => $errors);
            }
        }
        try{
            $hotel = get_hotel();
            $merchant_id = $hotel->merchant_id;
            $merchant = new Merchant();
            if($request->service == 2){
                $where = [['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type],['service_package_id','=',$request->package_id]];
            }elseif($request->service == 4 && isset($request->outstation_type) && $request->outstation_type == 1){
                $where = [['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type],['outstation_type','=',1]];
            }elseif($request->service == 4 && isset($request->outstation_type) && $request->outstation_type == 2){
                $where = [['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type],['service_package_id','=',$request->package_id],['outstation_type','=',2]];
            }else{
                $where = [['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type]];
            }
            $price = PriceCard::where($where)->first();
            if (empty($price)) {
                return array('result' => 0, 'message' => trans("$string_file.no_price_card_for_area"));
            } else {
                if (in_array($price->CountryArea->timezone, \DateTimeZone::listIdentifiers())) {
//                    date_default_timezone_set($price->CountryArea->timezone);
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
                $amount = $merchant->FinalAmountCal($amount, $merchant_id);
                return array('result' => 1, 'price_card_id' => $price->id, 'amount' => $amount);
            }
        }catch (\Exception $e){
            return array('result' => 0, 'message' => $e->getMessage());
        }
    }

    public function CheckDriver(Request $request)
    {
        $drivers = $this->GetNearestDriverMenual($request);
        echo !empty($drivers) ? count($drivers->toArray()) : 0;
    }

    public function GetNearestDriverMenual($request)
    {
        $hotel = get_hotel();
        $config = get_merchant_configuration($hotel->merchant_id);
        $drivers = Driver::GetNearestDriver([
            'area'=>$request->manual_area,
            'latitude'=>$request->pickup_latitude,
            'longitude'=>$request->pickup_longitude,
            'limit'=>$config->BookingConfiguration->number_of_driver_user_map,
            'service_type'=>$request->service,
            'vehicle_type'=>$request->vehicle_type,
            'distance_unit'=>$request->distance_unit,
            'distance'=>$request->radius,
            'user_gender'=>$config->ApplicationConfiguration->gender == 1 && $request->driver_gender == 2 ? 2 : null,
            'riders_num' => isset($request->riders_num)? $request->riders_num : null
        ]);
        return $drivers;
    }

    public function getDriverOnMap(Request $request)
    {
//        $this->SetTimeZone($request->manual_area);
        $hotel = get_hotel();
        $merchant_id = $hotel->merchant_id;
        $config = get_merchant_configuration($hotel->merchant_id);
        $drivers = Driver::GetNearestDriver([
            'area'=>$request->manual_area,
            'latitude'=>$request->pickup_latitude,
            'longitude'=>$request->pickup_longitude,
            'limit'=>$config->BookingConfiguration->number_of_driver_user_map,
            'service_type'=>$request->service,
            'vehicle_type'=>$request->vehicle_type,
            'distance_unit'=>$request->distance_unit,
            'distance'=>$request->radius,
            'user_gender'=>$config->ApplicationConfiguration->gender == 1 && $request->driver_gender == 2 ? 2 : null,
            'type' => $request->type,
            'riders_num' => isset($request->riders_num)? $request->riders_num : null,
            'merchant_id' => $merchant_id,
            'taxi_company_id' => NULL,
            'isManual' => true,
        ]);
        $mapMarkers = array();
        foreach ($drivers as $values) {
            $marker_icon = $this->getDriverVehicleImage($values);
            if (Auth::user()->demo == 1) {
                $mapMarkers[] = array(
                    'marker_id' => $values->id,
                    'marker_name' => "********" . substr($values->first_name . $values->last_name, -2),
                    'marker_address' => "",
                    'marker_number' => "********" . substr($values->phoneNumber, -2),
                    'marker_email' => "********" . substr($values->email, -2),
                    'marker_latitude' => $values->current_latitude,
                    'marker_longitude' => $values->current_longitude,
                    'marker_image' => get_image($values->profile_image, 'driver',$merchant_id),
                    'marker_icon' => $marker_icon,
                );
            } else {
                $mapMarkers[] = array(
                    'marker_id' => $values->id,
                    'marker_name' => $values->first_name . $values->last_name,
                    'marker_address' => "",
                    'marker_number' => $values->phoneNumber,
                    'marker_email' => $values->email,
                    'marker_latitude' => $values->current_latitude,
                    'marker_longitude' => $values->current_longitude,
                    'marker_image' => get_image($values->profile_image, 'driver',$merchant_id),
                    'marker_icon' => $marker_icon,
                );
            }
        }
        echo json_encode($mapMarkers, true);
    }

    public function getDriverVehicleImage($driver){
        $driverVehicle = $driver->DriverVehicles;
        if (isset($driverVehicle[0])){
            $driverVehicleImage = $driverVehicle[0]->VehicleType->vehicleTypeMapImage;
            $marker_icon = view_config_image($driverVehicleImage);
        }else{
            $marker_icon = view_config_image("marker/available.png");
        }
        return $marker_icon;
    }

    public function SetTimeZone($areaID)
    {
        $area = CountryArea::find($areaID);
        if (!empty($area)) {
//            date_default_timezone_set($area->timezone);
        }
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
        return $muliLocation;
    }

    public function getDrivers($request)
    {
//        $this->SetTimeZone(request()->manual_area);
        $hotel = get_hotel();
        $config = get_merchant_configuration($hotel->merchant_id);
        $drivers = Driver::GetNearestDriver([
            'area'=>$request->manual_area,
            'latitude'=>$request->pickup_latitude,
            'longitude'=>$request->pickup_longitude,
            'limit'=>$config->BookingConfiguration->number_of_driver_user_map,
            'service_type'=>$request->service,
            'vehicle_type'=>$request->vehicle_type,
            'distance'=>$request->ride_radius
        ]);
        return $drivers;
    }

    public function AllDriver(Request $request)
    {
        $unit = $request->distance_unit == 1 ? "Km" : "Miles";
        $drivers = $this->GetNearestDriverMenual($request);
        if (empty($drivers)) {
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

//    public function checkArea(Request $request){
//        if($request->service == 4){
//            $request->request->add(['service_type' => $request->service,'area_id'=>$request->manual_area]);
//            $area = $this->checkOutstationDropArea($request);
//            return $area;
//        }
//
//        $area = $this->checkGeofenceArea($request->latitude, $request->longitude, 'pickup', $request->merchant_id);
//        if(empty($area)){
//            $area = PolygenController::Area($request->latitude, $request->longitude, $request->merchant_id);
//            if (empty($area)) {
//                $msg = trans("$string_file.no_service_area");
//                return array('result' => '0','message' => $msg);
//            }
//        }
//
//        $area_id = $area['id'];
//        $areas = CountryArea::find($area_id);
//        if (!empty($request->user_id)){
//            $user = User::find($request->user_id);
//            $user->country_area_id = $area_id;
//            $user->save();
//        }
//
//        $msg = "<option value=''>- Select One -</option>";
//        foreach ($areas->ServiceTypes as $serviceType){
//            if($serviceType->id != 5){
//                $msg .= "<option value='".$serviceType->id."'>".$serviceType->serviceName."</option>";
//            }
//        }
//        return array('result' => '1','message' => $msg,'area_id'=>$area_id);
//    }

    public function checkArea(Request $request){
        try{
            if($request->service == 4){
                $request->request->add(['service_type' => $request->service,'area_id'=>$request->manual_area]);
                $area = $this->checkOutstationDropArea($request);
                return $area;
            }
            $area = $this->checkGeofenceArea($request->latitude, $request->longitude, 'pickup', $request->merchant_id);
            if(empty($area)){
                $area = PolygenController::Area($request->latitude, $request->longitude, $request->merchant_id);
                if (empty($area)) {
                    $msg = trans("$string_file.no_service_area");
                    return array('result' => '0','message' => $msg);
                }
            }
            $area_id = $area['id'];
            $segment = Segment::where('slag','TAXI')->first();
            $area = CountryArea::with(['VehicleType' => function ($query)use($segment){
                $query->where('segment_id',$segment->id);
            }])->with(['ServiceTypes' => function ($query)use($segment){
//                $query->where('segment_id',$segment->id);
            }])->find($area_id);

            $vehicle_types = "<option value=''>".trans("$string_file.select")."</option>";
            if(!empty($area->VehicleType)){
                foreach ($area->VehicleType->unique() as $vehicle) {
                    $vehicle_types .= "<option value='" . $vehicle->id . "'>" . $vehicle->VehicleTypeName . "</option>";
                }
            }
            if (!empty($request->user_id)){
                $user = User::find($request->user_id);
                $user->country_area_id = $area_id;
                $user->save();
            }
            $services = "<option value=''>".trans("$string_file.select")."</option>";
            if(!empty($area->ServiceTypes)){
                foreach ($area->ServiceTypes as $serviceType){
                    if($serviceType->id != 5){
                        $services .= "<option value='".$serviceType->id."'>".$serviceType->serviceName."</option>";
                    }
                }
            }
//            'services' => $services,
            return array('result' => '1','vehicle_types' => $vehicle_types, 'area_id'=>$area_id);
        }catch (\Exception $e){
            return array('result' => '0','message' => $e->getMessage());
        }
    }

    public function checkOutstationDropArea(Request $request){
        $home = new HomeController();
        $area = $home->CheckDropLocation($request);
        return $area;
    }
}
