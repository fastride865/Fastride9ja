<?php

namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\DistanceController;
use App\Http\Controllers\Helper\FindDriverController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\Merchant;
use App\Http\Controllers\Helper\PolygenController;
use App\Models\ApplicationConfiguration;
use App\Models\BookingConfiguration;
use App\Models\Configuration;
use App\Models\Country;
use App\Models\CountryArea;
use App\Models\InfoSetting;
use App\Models\Outstanding;
use App\Models\Segment;
use App\Traits\AreaTrait;
use App\Traits\DriverTrait;
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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Helper\PriceController;
use App\Traits\MerchantTrait;
use App\Traits\BookingTrait;
use App\Models\HandymanOrder;
use App\Models\BusinessSegment\Order;

class ManualDispatchController extends Controller
{
    use AreaTrait, MerchantTrait, BookingTrait, DriverTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'TAXI_MANUAL_DISPATCH')->first();
        view()->share('info_setting', $info_setting);
    }

//    public function index()
//    {
//        $checkPermission =  check_permission(1,'manualdispach');
//        if ($checkPermission['isRedirect']){
//            return  $checkPermission['redirectBack'];
//        }
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $config = ApplicationConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
//        $baseConfig = Configuration::select('corporate_admin')->where('merchant_id', '=', $merchant_id)->first();
//        $bookingConfig = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
//        $corporates = Corporate::where([['merchant_id', '=', $merchant_id]])->latest()->get();
//        $countries = Country::where([['merchant_id', '=', $merchant_id], ['country_status', '=', 1]])->get();
//        $paymentmethods = PaymentMethod::get();
//        return view('merchant.manual.index', compact('config', 'paymentmethods', 'corporates', 'countries', 'baseConfig','bookingConfig'));
//    }

    public function index()
    {
        $merchant_id = get_merchant_id();
        $config = ApplicationConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $baseConfig = Configuration::select('corporate_admin')->where('merchant_id', '=', $merchant_id)->first();
        $bookingConfig = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $corporates = Corporate::where([['merchant_id', '=', $merchant_id]])->latest()->get();
        $countries = Country::where([['merchant_id', '=', $merchant_id], ['country_status', '=', 1]])->get();
        $paymentmethods = PaymentMethod::get();
        return view('merchant.manual.manual', compact('config', 'paymentmethods', 'corporates', 'countries', 'baseConfig', 'bookingConfig'));
    }

    public function getDriverOnMap(Request $request)
    {
        $merchant_id = get_merchant_id();
//        $this->SetTimeZone(request()->manual_area);
        $drivers = $this->getAllDriver(false, $request);

        $mapMarkers = array();
        $countries = [];
        $test = [];
        foreach ($drivers as $values) {
            $latitude = null;
            $longitude = null;
            if($values->segment_group_id == 1)
            {
                $latitude = $values->current_latitude;
                $longitude = $values->current_longitude;
            }
            elseif(!empty($values->WorkShopArea))
            {
                $latitude = $values->WorkShopArea->latitude;
                $longitude = $values->WorkShopArea->longitude;
            }
            $marker_icon = $this->getDriverVehicleImage($values);

            if(!empty($latitude) && !empty($longitude)){

                if (!empty($values->country_id)) {
                    if (isset($countries[$values->country_id])) {
                        $countries[$values->country_id] = $countries[$values->country_id] + 1;
                    } else {
                        $countries[$values->country_id] = 1;
                    }
                }
                if (Auth::user()->demo == 1) {
                    $mapMarkers[] = array(
                        'marker_id' => $values->id,
                        'marker_name' => "********" . substr($values->first_name . $values->last_name, -2),
                        'marker_address' => "",
                        'marker_number' => "********" . substr($values->phoneNumber, -2),
                        'marker_email' => "********" . substr($values->email, -2),
                        'marker_latitude' => $latitude,
                        'marker_longitude' => $longitude,
                        'marker_image' => get_image($values->profile_image, 'driver'),
                        'marker_icon' => $marker_icon,
                    );
                } else {
                    $mapMarkers[] = array(
                        'marker_id' => $values->id,
                        'marker_name' => $values->first_name . $values->last_name,
                        'marker_address' => "",
                        'marker_number' => $values->phoneNumber,
                        'marker_email' => $values->email,
                        'marker_latitude' => $latitude,
                        'marker_longitude' => $longitude,
                        'marker_image' => get_image($values->profile_image, 'driver'),
                        'marker_icon' => $marker_icon,
                    );
                }
            }
        }
        $country = [];
        $country_name = "";
        // p($countries);
        if (!empty($countries)) {
            // asort($countries);
            $maxVal = max($countries); // maximum drivers in a country
            $maxKey = array_search($maxVal, $countries); // maximum driver's country
            $country = Country::find($maxKey);
            if (empty($country)) {
                $country = [];
            }
            $country_name = isset($country->LanguageCountrySingle->name) ? $country->LanguageCountrySingle->name : $country->LanguageCountryAny->name;
        }
        echo json_encode(array('map_markers' => $mapMarkers, 'country' => $country_name), true);
    }

    public function BookingDispatch(ManualDispatch $request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $query = PriceCard::where([['country_area_id', '=', $request->manual_area], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type]]);
        if (!empty($request->package) && $request->package != "null") {
            $query->where([['service_package_id', '=', $request->package]]);
        }
        $pricecards = $query->first();
        if (empty($pricecards)) {
            return redirect()->back()->withErrors(trans("$string_file.something_went_wrong"));
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
                $radius = $request->ride_radius_driver;
                $request->request->add(['radius' => $radius]);
                $drivers = $this->GetNearestDriverMenual($request);
                if ((empty($drivers) && \request()->booking_type == 1) || count($drivers) == 0) {
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
                $configs = get_merchant_configuration();
                $radius = $request->ride_radius_manual_driver;
                $request->request->add(['radius' => $radius]);
                $drivers = $this->GetNearestDriverMenual($request);
//                $drivers = Driver::GetNearestDriver([
//                    'area' => $request->manual_area,
//                    'latitude' => $request->pickup_latitude,
//                    'longitude' => $request->pickup_longitude,
//                    'service_type' => $request->service,
//                    'vehicle_type' => $request->vehicle_type,
//                    'driver_ids' => $driver_id,
//                    'user_gender'=>$configs->ApplicationConfiguration->gender == 1 && $request->driver_gender == 2 ? 2 : null,
//                    'distance' => $request->ride_radius_manual_driver,
//                    'segment_id' => 1,
//                ]);
                if (empty($drivers) || count($drivers) == 0) {
                    return redirect()->route('merchant.test.manualdispach')->withErrors(trans(trans("$string_file.no_driver_available")));
                }
                $booking = $this->AddBooking($request, $muliLocation, $merchant_id, $pricecards->id, $drivers, $configuration->google_key, 'single');

                $this->saveBookingStatusHistory($request, $booking, $booking->id);
                if ($request->booking_type == 1):
                    $findDriver->AssignRequest($drivers, $booking->id);
//                    $message = "New Booking";
                    $bookingData = new BookingDataController();
                    $bookingData->SendNotificationToDrivers($booking, $drivers);
                else:
                    if ($requestType == 1 && !empty($drivers)) {
//                        $message = "There Is New Upcoming Booking";
                        $bookingData = new BookingDataController();
                        $bookingData->SendNotificationToDrivers($booking, $drivers);
                    }
                endif;
                break;
        }
        $string_file = $this->getStringFile($merchant_id);
        return redirect()->route('merchant.ride-requests', $booking->id)->withSuccess(trans($string_file . ".ride") . ' ' . trans($string_file . ".booked_successfully"));
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

    public function SetTimeZone($areaID)
    {
        $area = CountryArea::find($areaID);
//        if (!empty($area)) {
//            date_default_timezone_set($area->timezone);
//        }
    }

    public function getDrivers($request)
    {
        $merchant_id = get_merchant_id();
//        $this->SetTimeZone($request->manual_area);
        $config = get_merchant_configuration($merchant_id);
        $drivers = Driver::GetNearestDriver([
            'area' => $request->manual_area,
            'latitude' => $request->pickup_latitude,
            'longitude' => $request->pickup_longitude,
            'limit' => $config->BookingConfiguration->number_of_driver_user_map,
            'service_type' => $request->service,
            'vehicle_type' => $request->vehicle_type,
            'distance' => $request->ride_radius,
            'isManual' => true,
//            'user_gender'=>$config->ApplicationConfiguration->gender == 1 && $request->driver_gender == 2 ? 2 : null,

        ]);
//        $findDriver = new FindDriverController();
//        $drivers = $findDriver->GetAllNearestDriver(request()->area, request()->pickup_latitude, request()->pickup_longitude, request()->ride_radius, 500, request()->vehicle_type, request()->service);
        return $drivers;
    }


    public function AddBooking($request, $muliLocation = null, $merchant_id, $pricecardid, $drivers, $key, $request_type = null)
    {
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

        if ($request->promo_code) {
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
//         $amount = $fare['amount'];
//        p($googleArray, 0);
//        p($amount, 0);
//        p($fare['bill_details'], 0);
//        p($request->estimate_fare, 0);
//        p($googleArray['total_distance_text']);

        $additional_notes = NULL;
        if (isset($request->note)) {
            $additional_notes = $request->note;
        }
//        if($muliLocation != null){
//            $muliLocation = json_encode($muliLocation, true);
//        }
        $booking = Booking::create([
            'merchant_id' => $merchant_id,
            'user_id' => $request->user_id,
            'segment_id' => 1,
            'driver_id' => $driver_id,
            'platform' => 2, // web
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
            'service_package_id' => $request->package,
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
        ]);
        return $booking;
    }


    public function PromoCode(Request $request)
    {
        $merchant_id = get_merchant_id();
        $manual_area = $request->manual_area;
        $timeZone = CountryArea::select('timezone')->find($manual_area);
//        date_default_timezone_set($timeZone->timezone);
        $date = date('Y-m-d');
        $price_card_id = $request->price_card_id;
        $promocodes = PromoCode::with(['PriceCardForPromo' => function ($query) use ($price_card_id) {
            $query->where('price_card_id', $price_card_id);
        }])->whereHas('PriceCardForPromo', function ($query) use ($price_card_id) {
            $query->where('price_card_id', $price_card_id);
        })->where(function ($q) use ($date) {
            $q->where([['end_date', '>', $date]])->orWhere('end_date', null);
        })->where([['merchant_id', '=', $merchant_id], ['country_area_id', '=', $manual_area], ['promo_code_status', '=', 1], ['deleted', '=', 0]])->get();
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
            if ($promocode->promo_code_value_type == 1) {
                if ($request->estimate_fare < $promocode->promo_code_value) {
                    $eta = 0.00;
                } else {
                    $eta = $request->estimate_fare - $promocode->promo_code_value;
                }
            } else {
                $promo_code_discount = round(($request->estimate_fare * $promocode->promo_code_value) / 100, 2);
                if ($promo_code_discount > $promocode->promo_percentage_maximum_discount) {
                    $eta = $request->estimate_fare - $promocode->promo_percentage_maximum_discount;
                } else {
                    $eta = $request->estimate_fare - $promo_code_discount;
                }
            }
            echo $eta;
        }
    }

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
        if (isset($request->outstation_type) && $request->outstation_type == 2) {
            $validator = Validator::make($request->all(), [
                'package_id' => 'required_if:service,4',
            ]);
            if ($validator->fails()) {
                $errors = $validator->messages()->all();
                return array('result' => 0, 'message' => $errors);
            }
        }
        try {
            $merchant_id = get_merchant_id();
            $merchant = new Merchant();
            if ($request->service == 2) {
                $where = [['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type], ['service_package_id', '=', $request->package_id]];
            } elseif ($request->service == 4 && isset($request->outstation_type) && $request->outstation_type == 1) {
                $where = [['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type], ['outstation_type', '=', 1]];
            } elseif ($request->service == 4 && isset($request->outstation_type) && $request->outstation_type == 2) {
                $where = [['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type], ['service_package_id', '=', $request->package_id], ['outstation_type', '=', 2]];
            } else {
                $where = [['country_area_id', '=', $request->area], ['service_type_id', '=', $request->service], ['vehicle_type_id', '=', $request->vehicle_type]];
            }
            $price = PriceCard::where($where)->first();
            if (empty($price)) {
                return array('result' => 0, 'message' => trans("$string_file.no_price_card_for_area"));
            } else {
//                if (in_array($price->CountryArea->timezone, \DateTimeZone::listIdentifiers())) {
//                    date_default_timezone_set($price->CountryArea->timezone);
//                }
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
        } catch (\Exception $e) {
            return array('result' => 0, 'message' => $e->getMessage());
        }
    }

    public function GetNearestDriverMenual($request)
    {
//        $segment = Segment::where('slag','TAXI')->first();
        $merchant_id = get_merchant_id();
//        $config = get_merchant_configuration($merchant_id);
        $type = isset($request->type) ? $request->type : NULL;
        $segment_id = 1;
        if (isset($request->segment_id) && !empty($request->segment_id) && $type != NULL) {
            $segment_id = $request->segment_id;
        }
        $drivers = Driver::GetNearestDriver([
            'segment_id' => $segment_id,
            'taxi_company_id' => NULL,
            'isManual' => true,
            'area' => $request->manual_area,
            'latitude' => $request->pickup_latitude,
            'longitude' => $request->pickup_longitude,
//            'limit'=>$config->BookingConfiguration->number_of_driver_user_map,
            'limit' => 10,
            'service_type' => $request->service,
            'vehicle_type' => $request->vehicle_type,
            'distance_unit' => $request->distance_unit,
            'distance'=>isset($request->radius) ? $request->radius : $request->ride_radius,
            'driver_ids' => !empty($request->driver_id) ? [$request->driver_id] : [],
//            'user_gender'=>$config->ApplicationConfiguration->gender == 1 && $request->driver_gender == 2 ? 2 : null,
            'type' => $type,
        ]);

        return $drivers;
    }

    public function CheckDriver(Request $request)
    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $this->SetTimeZone(request()->manual_area);
        //commented by amba
//        $km = $request->radius;
//        $pickup_latitude = $request->pickup_latitude;
//        $pickup_longitude = $request->pickup_longitude;
//        $vehicle_type_id = $request->vehicle_type;
//        $service_id = $request->service;
//        $config = ApplicationConfiguration::where('merchant_id','=',$merchant_id)->first();
//        $driver_obj = new Driver();
//        if ($config->gender == 1){
//            $drivers = $driver_obj->getNearestDriverGender($request->manual_area, $pickup_latitude, $pickup_longitude, $km, 500, $vehicle_type_id, $service_id,$request->distance_unit,$request->driver_gender);
//        }else{
//
//            $drivers = $driver_obj->GetNearestDriver($request->manual_area, $pickup_latitude, $pickup_longitude, $km, 500, $vehicle_type_id, $service_id);
//        }


        //GetNearestDriver($area, $latitude, $longitude, $distance = 1, $limit = 1, $vehicle_type_id, $service_type_id, $user_gender = NULL, $driver_request_time_out = 60)
        // print_r($drivers->toArray());die();
        $drivers = $this->GetNearestDriverMenual($request);
        echo !empty($drivers) ? count($drivers) : 0;
//        if(!empty($drivers))
//        {
//            echo count($drivers);
//        }
//        else{
//            echo 0;
//        }
    }

    public function AllDriver(Request $request)
    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $this->SetTimeZone(request()->manual_area);
//        $km = $request->radius;
//        $pickup_latitude = $request->pickup_latitude;
//        $pickup_longitude = $request->pickup_longitude;
//        $vehicle_type_id = $request->vehicle_type;
//        $service_id = $request->service;
//        $unit = $request->distance_unit == 1 ? "Km" : "Miles";
//        $config = ApplicationConfiguration::where('merchant_id','=',$merchant_id)->first();
//        $driver_obj = new Driver();
//        if ($config->gender == 1){
//            $drivers = $driver_obj->getNearestDriverGender($request->manual_area, $pickup_latitude, $pickup_longitude, $km, 500, $vehicle_type_id, $service_id,$request->distance_unit,$request->driver_gender);
//        }else{
//            $drivers = $driver_obj->GetNearestDriver($request->manual_area, $pickup_latitude, $pickup_longitude, $km, 500, $vehicle_type_id, $service_id,$request->distance_unit);
//        }
        $unit = $request->distance_unit == 1 ? "Km" : "Miles";
        $drivers = $this->GetNearestDriverMenual($request);
        if (empty($drivers)) {
            echo "<option value=''>No Driver Online</option>";
        } else {
            echo "<option value=''>Select Driver</option>";
            if (Auth::user()->demo == 1) {
                foreach ($drivers as $driver) {
                    $driver_name = $driver->fullName . "(" . "********" . substr($driver->phoneNumber, -2) . ")" . "(" . sprintf("%0.2f", $driver->distance) . " " . $unit . ")";
                    echo "<option value='" . $driver->driver_id . "'>" . $driver_name . "</option>";
                }
            } else {
                foreach ($drivers as $driver) {
                    $driver_name = $driver->fullName . "(" . $driver->phoneNumber . ")" . "(" . sprintf("%0.2f", $driver->distance) . " " . $unit . ")";
                    echo "<option value='" . $driver->driver_id . "'>" . $driver_name . "</option>";
                }
            }
        }
    }

    public function FavouriteDriver(Request $request)
    {
//        $this->SetTimeZone(request()->manual_area);
        $user_id = $request->user_id;
//        $pickup_latitude = $request->pickup_latitude;
//        $pickup_longitude = $request->pickup_longitude;
//        $vehicle_type_id = $request->vehicle_type;
//        $service_id = $request->service;
        $unit = $request->distance_unit == 1 ? "Km" : "Miles";
        $drivers = FavouriteDriver::where([['user_id', '=', $user_id]])->get();
        if (empty($drivers->toArray())) {
            echo "<option value=''>Sorry No Driver Online</option>";
        } else {
            $drivers = $drivers->toArray();
            $driver_id = array_pluck($drivers, 'id');
//            $drivers = Driver::GetNearestDriverByIds($request->manual_area, $pickup_latitude, $pickup_longitude, $vehicle_type_id, $service_id, $driver_id);
            $drivers = Driver::GetNearestDriver([
                'area' => $request->manual_area,
                'latitude' => $request->pickup_latitude,
                'longitude' => $request->pickup_longitude,
                'distance_unit' => $request->distance_unit,
                'service_type' => $request->service,
                'vehicle_type' => $request->vehicle_type,
                'driver_ids' => $driver_id,
            ]);
            echo "<option value=''>Select Driver</option>";
            if (Auth::user()->demo == 1) {
                foreach ($drivers as $driver) {
                    $driver_name = $driver->fullName . "(" . "********" . substr($driver->phoneNumber, -2) . ")" . "(" . sprintf("%0.2f", $driver->distance) . " " . $unit . ")";
                    echo "<option value='" . $driver->driver_id . "'>" . $driver_name . "</option>";
                }
            } else {
                foreach ($drivers as $driver) {
                    $driver_name = $driver->fullName . "(" . $driver->phoneNumber . ")" . "(" . sprintf("%0.2f", $driver->distance) . " " . $unit . ")";
                    echo "<option value='" . $driver->driver_id . "'>" . $driver_name . "</option>";
                }
            }
        }
    }

    public function SearchUser(Request $request)
    {
        $merchant_id = get_merchant_id();
        $booking_config = BookingConfiguration::where('merchant_id', '=', $merchant_id)->first();
        $rider = User::where([['merchant_id', '=', $merchant_id], ['taxi_company_id', '=', NULL], ['UserPhone', '=', $request->user_phone], ['user_delete', '=', NULL]])->first();
        $id = NULL;
        $gender = '';
        if (!empty($rider->id)) {
            $id = $rider->id;
            $gender = $rider->user_gender;
        }
        if (isset($rider->country_id)) {
            $country = Country::where([['merchant_id', '=', $merchant_id], ['id', '=', $rider->country_id]])->first();
            $distance_unit = $country->distance_unit;
            $iso = $country->isoCode;
        } else {
            $country = Country::where([['merchant_id', '=', $merchant_id], ['id', '=', $request->country_id]])->first();
            $distance_unit = $country->distance_unit;
            $iso = $country->isoCode;
        }
        return array('id' => $id, 'distance_unit' => $distance_unit, 'multi_destination' => $booking_config->multi_destination, 'user_gender' => $gender, 'iso' => $iso, 'max_multi_count' => $booking_config->count_multi_destination);
    }

    public function AddManualUser(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $booking_config = BookingConfiguration::where('merchant_id', '=', $merchant_id)->first();
        $app_config = ApplicationConfiguration::where('merchant_id', '=', $merchant_id)->first();
        $valid_arr = [
            'first_name' => 'required|alpha',
            'last_name' => 'required|alpha',
            'new_user_phone' => ['required', 'regex:/^[0-9+]+$/',
                Rule::unique('users', 'UserPhone')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', $merchant_id], ['user_delete', null]]);
                })]

        ];
        if ($app_config->user_email == 1) {
            $valid_arr = array_merge($valid_arr, ['new_user_email' => ['required', 'email',
                Rule::unique('users', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', $merchant_id], ['user_delete', null]]);
                })]]);
        }
        $val = validator($request->all(), $valid_arr);

        if ($val->fails()) {
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
        $country = Country::where([['merchant_id', '=', $merchant_id], ['id', '=', $request->country_id]])->first();
        $distance_unit = $country->distance_unit;
        $iso = $country->isoCode;
        return array('id' => $rider->id, 'distance_unit' => $distance_unit, 'multi_destination' => $booking_config->multi_destination, 'user_gender' => $rider->user_gender, 'iso' => $iso, 'max_multi_count' => $booking_config->count_multi_destination);
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

    public function checkArea(Request $request)
    {
        try {
            if ($request->service == 4) {
                $request->request->add(['service_type' => $request->service, 'area_id' => $request->manual_area]);
                $area = $this->checkOutstationDropArea($request);
                return $area;
            }
            $string_file = $this->getStringFile($request->merchant_id);
            $area = $this->checkGeofenceArea($request->latitude, $request->longitude, 'pickup', $request->merchant_id);
            if (empty($area)) {
                $area = PolygenController::Area($request->latitude, $request->longitude, $request->merchant_id);
                if (empty($area)) {
                    $msg = trans("$string_file.no_service_area");
                    return array('result' => '0', 'message' => $msg);
                }
            }
            $area_id = $area['id'];
             if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
                $permission_area_ids = explode(",",Auth::user()->role_areas);
                if(!in_array($area_id,$permission_area_ids))
                {
                    $msg = trans("$string_file.permission_denied");
                    return array('result' => '0', 'message' => $msg);
                }
            }
            $segment = Segment::where('slag', 'TAXI')->first();
            $area = CountryArea::with(['VehicleType' => function ($query) use ($segment) {
                $query->where('segment_id', $segment->id);
            }])->with(['ServiceTypes' => function ($query) use ($segment) {
//                $query->where('segment_id',$segment->id);
            }])->find($area_id);

            $string_file = $this->getStringFile($area->merchant_id);
            $vehicle_types = "<option value=''>" . trans("$string_file.vehicle_type") . "</option>";
            if (!empty($area->VehicleType)) {
                foreach ($area->VehicleType->unique() as $vehicle) {
                    $vehicle_types .= "<option value='" . $vehicle->id . "'>" . $vehicle->VehicleTypeName . "</option>";
                }
            }
            if (!empty($request->user_id)) {
                $user = User::find($request->user_id);
                $user->country_area_id = $area_id;
                $user->save();
            }
            $services = "<option value=''>" . trans("$string_file.select") . "</option>";
            if (!empty($area->ServiceTypes)) {
                foreach ($area->ServiceTypes as $serviceType) {
                    if ($serviceType->id != 5) {
                        $services .= "<option value='" . $serviceType->id . "'>" . $serviceType->serviceName . "</option>";
                    }
                }
            }
//            'services' => $services,
            return array('result' => '1', 'vehicle_types' => $vehicle_types, 'area_id' => $area_id);
        } catch (\Exception $e) {
            return array('result' => '0', 'message' => $e->getMessage());
        }
    }

    public function checkOutstationDropArea(Request $request)
    {
        $home = new HomeController();
        $area = $home->CheckDropLocation($request);
        return $area;
    }

    public function getDriverVehicleImage($driver)
    {
        if($driver->segment_group_id == 1)
        {
        $driverVehicle = $driver->DriverVehicles;
        if (isset($driverVehicle[0])) {
            $driverVehicleImage = $driverVehicle[0]->VehicleType->vehicleTypeMapImage;
            $marker_icon = view_config_image($driverVehicleImage);
        } else {
            $marker_icon = view_config_image("marker/available.png");
        }
        }
        else
        {
            $marker_icon = view_config_image("marker/plumber.png");
        }
        return $marker_icon;
    }

//    public function VehicleConfig(Request $request)
//    {
//        p($request->all());
//        $validator = Validator::make($request->all(), [
//            'manual_area' => 'required',
////            'service' => 'required',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return $errors;
//            exit();
//        }
//        $manual_area = $request->manual_area;
//        $merchant_segment = helperMerchant::MerchantSegments();
//        $vehicle = [];
//        if(in_array('TAXI',$merchant_segment)){
//            $segment = Segment::where('slag','TAXI')->first();
//            $area = CountryArea::with(['VehicleType' => function ($query)use($segment){
//                $query->where('segment_id',$segment->id);
//            }])->find($manual_area);
//            $vehicles = $area->VehicleType;
//        }
//        if(!empty($vehicle)){
//            foreach ($vehicles as $vehicle) {
//                echo "<option value='" . $vehicle->id . "'>" . $vehicle->VehicleTypeName . "</option>";
//            }
//        }else{
//            return '';
//        }
////        $area = CountryArea::find($manual_area);
////        $merchant_id = $area->merchant_id;
////        $areaName = $area->CountryAreaName;
////        $service = $request->service;
////        switch ($service) {
////            case "1":
////                $data = CountryArea::with(['VehicleType' => function ($query) use ($service) {
////                    $query->where([['service_type_id', '=', $service]]);
////                }])->find($manual_area);
////                echo "<option value=''>" . trans("$string_file.select") . "</option>";
////                foreach ($data->VehicleType as $vehicle) {
////                    echo "<option value='" . $vehicle->id . "'>" . $vehicle->VehicleTypeName . "</option>";
////                }
////                break;
////            case "2":
////            case "3":
////                $data = CountryArea::with(['Package' => function ($query) use ($service) {
////                    $query->where([['country_area_package.service_type_id', '=', $service]]);
////                }])->find($manual_area);
////                echo "<option value=''>" . trans('admin.message538') . "</option>";
////                foreach ($data->Package as $package) {
////                    echo "<option value='" . $package->id . "'>" . $package->PackageName . "</option>";
////                }
////                break;
////            case "4":
////                $data = OutstationPackage::where([['merchant_id', '=', $area->merchant_id]])->get();
////                echo "<option value=''>" . trans('admin.message539') . "</option>";
////                foreach ($data as $package) {
////                    echo "<option value='" . $package->id . "'>" . $areaName . " -> " . $package->PackageName . "</option>";
////                }
////                break;
////            case "5":
////                $data = VehicleType::where([['merchant_id', '=', $merchant_id], ['pool_enable', '=', 1]])->get();
////                echo "<option value=''>" . trans("$string_file.select") . "</option>";
////                foreach ($data as $vehicle) {
////                    echo "<option value='" . $vehicle->id . "'>" . $vehicle->VehicleTypeName . "</option>";
////                }
////                break;
////            default :
////                $data = CountryArea::with(['VehicleType' => function ($query) use ($service) {
////                    $query->where([['service_type_id', '=', $service]]);
////                }])->find($manual_area);
////                echo "<option value=''>" . trans("$string_file.select") . "</option>";
////                foreach ($data->VehicleType as $vehicle) {
////                    echo "<option value='" . $vehicle->id . "'>" . $vehicle->VehicleTypeName . "</option>";
////                }
////                break;
////        }
//    }

    public function getBookingsOnHeatMap(Request $request)
    {
        // p($request->all());
        $merchant_id = get_merchant_id();
        $request->request->add(['merchant_id' => $merchant_id]); 
//        $this->SetTimeZone(request()->manual_area);
        $query=Segment::latest();
        if (!empty($request->segment_id)) {
            $query->where('id', $request->segment_id);
        }
        else{
            $merchant_segments = get_merchant_segment();
            $query->whereIn('id', array_keys($merchant_segments));
        }
        $segments = $query->get(['id','sub_group_for_admin']);
        $data=[];
        if(!empty($segments)){
            foreach($segments as $segment){                
                if($segment->sub_group_for_admin==1){
                    $booking = $this->allBookings(false);
                    $bookings = $booking->get(['drop_latitude', 'drop_longitude','id']);
                    if(!empty($bookings)){
                        foreach($bookings as $booking){
                           $data[]= array(
                                'segment'=>$segment->id,
                               'id'=>$booking->id,
                               'drop_latitude'=>$booking->drop_latitude,
                               'drop_longitude'=>$booking->drop_longitude,
                           );
                        }
                    }
                }
                elseif($segment->sub_group_for_admin==2){
                    $order = new Order;
                    $arr_orders = $order->getOrders($request);  
                    if(!empty($arr_orders)){
                        foreach($arr_orders as $order){
                           $data[]= array(
                                'segment'=>$segment->id,
                                'id'=>$order->id,
                               'drop_latitude'=>$order->drop_latitude,
                               'drop_longitude'=>$order->drop_longitude,
                           );
                        }
                    }
                }
                else{
                    // $request->segment_id=$segment->id;
                    $handyman = new HandymanOrder;
                    $arr_orders = $handyman->getSegmentOrders($request);   
                    if(!empty($arr_orders)){
                        foreach($arr_orders as $order){
                           $data[]= array(
                                'segment'=>$segment->id,
                                'id'=>$order->id,
                               'drop_latitude'=>$order->drop_latitude,
                               'drop_longitude'=>$order->drop_longitude,
                           );
                        }
                    }
                }
            }    
        }
        // p($data);
        echo json_encode($data, true);
    }

}