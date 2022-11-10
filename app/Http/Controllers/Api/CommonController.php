<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\CustomerSupportEvent;
use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\FaceRecognition;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\Merchant;
use App\Http\Controllers\Helper\PolygenController;
use App\Http\Controllers\Helper\PriceController;
use App\Models\Booking;
use App\Models\BookingConfiguration;
use App\Models\BookingRating;
use App\Models\BookingRequestDriver;
use App\Models\BusinessSegment\Order;
use App\Models\BusinessSegment\BusinessSegment;
use App\Models\CancelReason;
use App\Models\CmsPage;
use App\Models\Configuration;
use App\Models\Corporate;
use App\Models\Country;
use App\Models\CountryArea;
use App\Models\ChildTerm;
use App\Models\CustomerSupport;
use App\Models\Driver;
use App\Models\DriverVehicle;
use App\Models\FavouriteLocation;
use App\Models\GeofenceAreaQueue;
use App\Models\HandymanOrder;
use App\Models\Merchant as merchantModel;
use App\Models\Onesignal;
use App\Models\OutstationPackage;
use App\Models\PaymentConfiguration;
use App\Models\PaymentOptionsConfiguration;
use App\Models\PriceCard;
use App\Models\PricingParameter;
use App\Models\PromoCode;
use App\Models\RestrictedArea;
use App\Models\ReferralDiscount;
use App\Models\Segment;
use App\Models\ServiceType;
use App\Models\SosRequest;
use App\Models\CancelRate;
use App\Models\UserWalletTransaction;
use App\Models\VehicleType;
use App\Models\RewardPoint;
use App\Models\WalletCouponCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Traits\AreaTrait;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Models\PaymentMethod;
use App\Models\UserAddress;
use App\Traits\DriverTrait;
use App\Models\DriverConfiguration;

class CommonController extends Controller
{
    use AreaTrait, ApiResponseTrait,MerchantTrait,DriverTrait;

    public function DriverRating(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $start_date = $request->start_date . " " . '00:00:00';
        $end_date = $request->end_date . " " . '23:59:59';
        $driver = $request->user('api-driver');
        $averageRating = BookingRating::whereHas('Booking', function ($query) use ($driver) {
            $query->with(['Driver'])->where([['driver_id', '=', $driver->id]]);
        })->where([['created_at', '>=', $start_date], ['created_at', '<=', $end_date]])->avg('user_rating_points');
        $averageRating = $averageRating ? $averageRating : '0.0';
        return response()->json(['result' => "1", 'message' => 'Average Rating', 'data' => ['rating' => sprintf("%0.1f", $averageRating), 'name' => $driver->fullName, 'image' => $driver->profile_image]]);
    }

    public function UserRaing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $start_date = $request->start_date . " " . '00:00:00';
        $end_date = $request->end_date . " " . '23:59:59';
        $user = $request->user('api');
        $averageRating = BookingRating::whereHas('Booking', function ($query) use ($user) {
            $query->where([['user_id', '=', $user->id]]);
        })->where([['created_at', '>=', $start_date], ['created_at', '<=', $end_date]])->avg('driver_rating_points');
        $averageRating = $averageRating ? $averageRating : '0.0';
        return response()->json(['result' => "1", 'message' => 'Average Rating', 'data' => ['rating' => sprintf("%0.1f", $averageRating), 'name' => $user->UserName, 'image' => $user->UserProfileImage]]);
    }

    public function CountryList(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $countries = Country::where([['country_status', '=', 1], ['merchant_id', '=', $merchant_id]])->with('LanguageCountrySingle')->latest()->get();
        if (empty($countries->toArray())) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }
        foreach ($countries as $key => $country) {
            $country->name = $country->CountryName;
            $country->currency = $country->CurrencyName;
        }
        return response()->json(['result' => "1", 'message' => trans('api.message169'), 'data' => $countries]);
    }

    // for all segments
    public function cancelReason(Request $request)
    {
        $validator = Validator::make($request->all(), [
//            'booking_id' => 'required|exists:bookings,id',
            'segment_id' => 'required|exists:segments,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $user = $request->user('api');

        $merchant_id = $user->merchant_id;
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $payment_config = PaymentConfiguration::select('cancel_rate_table_enable')->where('merchant_id', $merchant_id)->first();
        $cancelReasons = CancelReason::Reason($merchant_id, 1, $request->segment_id);
        if (empty($cancelReasons->toArray())) {
            return $this->failedResponse(trans("$string_file.data_not_found"));
        }
        $charges = "0";

        // tutu changes
        try {
            $code = "";
            $booking = Booking::find($request->booking_id);
            if (!empty($booking->id) && !empty($booking->BookingDetail)) {
                if ($booking->PriceCard->cancel_charges == 1) {
                    $acceptTime = $booking->BookingDetail->accept_timestamp;
                    $current = strtotime('now');
                    $canceTime = strtotime("+{$booking->PriceCard->cancel_time} minutes", $acceptTime);
                    if ($current > $canceTime && $payment_config->cancel_rate_enable == 1) {
                        $cancel_rate = CancelRate::where('merchant_id', $merchant_id)
                            ->where([
                                ['start_range', '<=', $booking->estimate_bill],
                                ['end_range', '>=', $booking->estimate_bill]
                            ])->first();

                        if ($cancel_rate) {
                            if ($cancel_rate->charge_type == 1) {
                                $charges = $cancel_rate->charges;
                            } else {
                                $charge_value = ($booking->estimate_bill * $cancel_rate->charges) / 100;
                                $charges = round($charge_value, 1);
                            }
                        }
                    } else if ($current > $canceTime && $booking->PriceCard->cancel_amount > 0) {

                        $charges = $booking->PriceCard->cancel_amount;
                    }
                }
               $code = $booking->CountryArea->Country->isoCode;
            }
            $return_data = ['response_data' => $cancelReasons, 'code' => $code, 'cancel_charges' => $charges];
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans("$string_file.success"), $return_data);
    }

    public function driverCancelReason(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'segment_id' => 'required|exists:segments,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $user = $request->user('api-driver');
            $merchant_id = $user->merchant_id;
            $string_file = $this->getStringFile(NULL,$user->Merchant);
            $cancelReasons = CancelReason::Reason($merchant_id, 2, $request->segment_id);
            if (empty($cancelReasons->toArray())) {
                return $this->failedResponse(trans("$string_file.data_not_found"));
            }
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans('api.message169'), $cancelReasons);
    }

    public function CheckBookingTime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->where('booking_status', 1012);
                }),
            ],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $booking = Booking::find($request->booking_id);
        $today = new \DateTime();
        $expires = new \DateTime($booking->later_booking_date);
        $config = Configuration::where([['merchant_id', '=', $booking->merchant_id]])->first();
        if ($today <= $expires) {
            if ($today < $expires) {
                return response()->json(['result' => "1", 'message' => trans('api.message142')]);
            }
            $bookingTimestamp = strtotime($booking->later_booking_time) + $config->ride_later_time_before;
            $currentTimestamp = strtotime("now");
            if ($bookingTimestamp >= $currentTimestamp) {
                return response()->json(['result' => "1", 'message' => trans('api.message142')]);
            }
            $time = $config->ride_later_time_before;
            $hours = floor($time / 60);
            $minutes = ($time % 60);
            return response()->json(['result' => "0", 'message' => trans_choice('api.minutes_ago', 3, ['hours' => $hours, 'min' => $minutes]), 'data' => []]);
        } else {
            $time = $config->ride_later_time_before;
            $hours = floor($time / 60);
            $minutes = ($time % 60);
            return response()->json(['result' => "0", 'message' => trans_choice('api.minutes_ago', 3, ['hours' => $hours, 'min' => $minutes]), 'data' => []]);
        }
    }

    public function Customer_Support(Request $request)
    {
        $merchant_id = $request->user('api')->merchant_id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $created_customer_support = CustomerSupport::create([
            'merchant_id' => $merchant_id,
            'application' => 1,
            'email' => $request->email,
            'name' => $request->name,
            'phone' => $request->phone,
            'query' => $request->message,
        ]);
        $config = Configuration::select('email_functionality')->where([['merchant_id', '=', $merchant_id]])->first();
        if ($config->email_functionality == 1) {
            event(new CustomerSupportEvent($created_customer_support));
        }
        $string_file = $this->getStringFile($merchant_id);
        return $this->successResponse(trans("$string_file.customer_support_response"),[]);
    }

    public function Driver_Customer_Support(Request $request)
    {
        $merchant_id = $request->user('api-driver')->merchant_id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $customer_supports = new CustomerSupport();
        $customer_supports->merchant_id = $merchant_id;
        $customer_supports->application = 2;
        $customer_supports->email = $request->email;
        $customer_supports->name = $request->name;
        $customer_supports->phone = $request->phone;
        $customer_supports->query = $request->message;
        $customer_supports->save();

        $config = Configuration::select('email_functionality')->where([['merchant_id', '=', $merchant_id]])->first();
        if ($config->email_functionality == 1) {
            event(new CustomerSupportEvent($customer_supports));
        }
        $string_file = $this->getStringFile($merchant_id);
        return $this->successResponse(trans("$string_file.customer_support_response"),[]);
    }

    public function DriverCmsPage(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $validator = Validator::make($request->all(), [
            'slug' => [
                'required',
                Rule::exists('cms_pages', 'slug')->where(function ($query) use ($merchant_id) {
                    $query->where(['merchant_id' => $merchant_id, 'application' => 2]);
                }),
            ],
            'country_id' => 'required_if:slug,terms_and_Conditions',
        ], [
            'exists' => trans("$string_file.data_not_found"),
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
            // return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        try {
            $message = '';
            if ($request->slug == 'terms_and_Conditions') {
                $message = trans("$string_file.terms_conditions");
                $page = CmsPage::where([['merchant_id', '=', $merchant_id], ['application', '=', 2], ['slug', '=', $request->slug], ['country_id', '=', $request->country_id]])->first();
            } else {
                $message = trans("$string_file.cms_pages");
                $page = CmsPage::where([['merchant_id', '=', $merchant_id], ['application', '=', 2], ['slug', '=', $request->slug]])->first();
            }
            if (empty($page)) {
                return $this->failedResponse($message);
//                    response()->json(['result' => "0", 'message' => $message, 'data' => []]);
            }
            $page_data = array(
                'title' => $page->CmsPageTitle,
                'description' => $page->CmsPageDescription,
            );
            // $page->title = $page->CmsPageTitle;
            // $page->description = $page->CmsPageDescription;
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse($message, $page_data);
//        return response()->json(['result' => "1", 'message' => $message, 'data' => $page]);
    }

    public function UserCmsPage(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $customMessages = [
            'exists' => trans("$string_file.data_not_found"),
        ];
        if ($request->slug == 'child_terms'):
            $validator = Validator::make($request->all(), [
                'slug' => [
                    'required',
                    Rule::exists('child_terms', 'slug')->where(function ($query) use ($merchant_id) {
                        $query->where(['merchant_id' => $merchant_id, 'application' => 1]);
                    }),
                ],
                'country_id' => 'required',
            ], $customMessages);
        else:
            $validator = Validator::make($request->all(), [
                'slug' => [
                    'required',
                    Rule::exists('cms_pages', 'slug')->where(function ($query) use ($merchant_id) {
                        $query->where(['merchant_id' => $merchant_id, 'application' => 1]);
                    }),
                ],
                'country_id' => 'required_if:slug,terms_and_Conditions',
            ], $customMessages);
        endif;
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        if ($request->slug == 'terms_and_Conditions') {
            $page = CmsPage::where([['merchant_id', '=', $merchant_id], ['application', '=', 1], ['slug', '=', $request->slug], ['country_id', '=', $request->country_id]])->first();
        } elseif ($request->slug == 'child_terms') {
            $page = ChildTerm::where([['merchant_id', '=', $merchant_id], ['application', '=', 1], ['slug', '=', $request->slug], ['country_id', '=', $request->country_id]])->first();
        } else {
            $page = CmsPage::where([['merchant_id', '=', $merchant_id], ['application', '=', 1], ['slug', '=', $request->slug], ['slug', '=', $request->slug]])->first();
        }
        if (empty($page)) {
            return $this->failedResponse(trans("$string_file.cms_pages"));
//            return response()->json(['result' => "0", 'message' => trans('api.message50'), 'data' => []]);
        }
        if ($request->slug == 'child_terms'):
            $page->title = $page->Title;
            $page->description = $page->Description;
        else:
            $page->title = $page->CmsPageTitle;
            $page->description = $page->CmsPageDescription;
        endif;
        return $this->successResponse(trans("$string_file.cms_pages"),$page);
//            response()->json(['result' => "1", 'message' => trans("$string_file.cms_pages"), 'data' => $page]);
    }

    public function DriverSosRequest(Request $request)
    {
        $merchant_id = $request->user('api-driver')->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer|exists:bookings,id',
            'number' => 'required|exists:sos,number',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $booking = Booking::find($request->booking_id);
        $sos_request = SosRequest::create([
            'merchant_id' => $merchant_id,
            'country_area_id' => $booking->country_area_id,
            'application' => 1,
            'booking_id' => $request->booking_id,
            'number' => $request->number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
        return response()->json(['result' => "1", 'message' => trans("$string_file.sos_request"), 'data' => $sos_request]);
    }

    public function SosRequest(Request $request)
    {
        $merchant_id = $request->user('api')->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer|exists:bookings,id',
            'number' => 'required|exists:sos,number',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $booking = Booking::find($request->booking_id);
        $sos_request = SosRequest::create([
            'merchant_id' => $merchant_id,
            'application' => 1,
            'country_area_id' => $booking->country_area_id,
            'booking_id' => $request->booking_id,
            'number' => $request->number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
        return response()->json(['result' => "1", 'message' => trans("$string_file.sos_request"), 'data' => $sos_request]);
    }

    public function Pricecard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area' => 'required|integer|exists:country_areas,id',
            'segment_id' => 'required|integer|exists:segments,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $area_id = $request->area;
        $area = CountryArea::find($area_id);
        $currency = $area->Country->isoCode;
        $merchant_id = $area->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $segment_id = $request->segment_id;
        $services = ServiceType::with(['PriceCard' => function ($query) use ($area_id,$segment_id) {
            $query->where([['country_area_id', '=', $area_id],['segment_id','=',$segment_id]]);
            $query->where('status', 1);
        }])->whereHas('PriceCard', function ($q) use ($area_id,$segment_id) {
            $q->where([['country_area_id', '=', $area_id],['segment_id','=',$segment_id]]);
            $q->where('status', 1);

        })->where([['id', '!=', 4]])->get();
        if (count($services) == 0) {
            return $this->failedResponse(trans("$string_file.data_not_found"));
        }
        if ($area->Country->distance_unit == 1) {
            $distance_unit = trans("$string_file.km");
        } else {
            $distance_unit = trans("$string_file.miles");
        }
        $return_services = [];
        $arr_vehicle_return = [];
        foreach ($services as $key => $value) {
            $service_id = $value->id;
            $vehicle_type = [];
            $arr_vehicle_return = [];
            if ($service_id == 2 || $service_id == 4) {
                $vehicle_type = VehicleType::whereHas('PriceCard', function ($query) use ($area_id, &$service_id) {
                    $query->where([['country_area_id', '=', $area_id], ['service_type_id', '=', $service_id]]);
                    $query->where('status', 1);
                })->with(['PriceCard' => function ($q) use ($area_id, &$service_id) {
                    $q->where([['country_area_id', '=', $area_id], ['service_type_id', '=', $service_id]])->with('ServicePackage');
                    $q->where('status', 1);
                }])->get();
                if (!empty($vehicle_type->toArray())) {
                    foreach ($vehicle_type as $keys => $valuess) {
                        $price_card = $valuess->PriceCard;
                        $price_card_values = array();
                        foreach ($price_card as $nKey => $nValue) {
                            $pricing_type = $nValue->pricing_type;
                            $parameter_price = $currency . " " . $nValue->base_fare;
                            $description = "Free Distance " . $nValue->free_distance . " " . $distance_unit . " Free Time " . $nValue->free_time . " Mintues";
                            if ($pricing_type == 3) {
                                $parameter_price = trans('api.message129');
                                $description = trans('api.message128');
                            }

                            $price_card_values[] = array(
                                "parameter_price" => $parameter_price,
                                "pricing_parameter" => !empty($nValue->service_package_id) ? $nValue->ServicePackage->PackageName : "",
                                "description" => $description
                            );
                        }
                        unset($valuess->PriceCard);
                        $return_vehicle['price_card_values'] = $price_card_values;
                        $return_vehicle['vehicleTypeName'] = $valuess->VehicleTypeName;
                        $return_vehicle['vehicleTypeDescription'] = $valuess->VehicleTypeDescription;
                        $return_vehicle['vehicleTypeImage'] = get_image($valuess->vehicleTypeImage, 'vehicle', $merchant_id);
                        $arr_vehicle_return [] = $return_vehicle;

                    }
                }
            } else {
                foreach ($value->PriceCard as $login) {
                    $price_card_values = $login->PriceCardValues;
                    $return_price_card =[];
                    foreach ($price_card_values as $values) {
                        $parameter_price = $currency . " " . $values->parameter_price;
                        $parameterType = $values->PricingParameter->parameterType;
//                        switch ($parameterType) {
//                            case "1":
//                                $description = trans('api.message78');
//                                break;
//                            case "2":
//                                $description = trans('api.message79');
//                                break;
//                            case "3":
//                                $description = trans('api.message80');
//                                break;
//                            case "4":
//                                $description = trans('api.message81');
//                                break;
//                            case "6":
//                                $description = trans('api.message81');
//                                break;
//                            case "7":
//                                $description = trans('admin.toll');
//                                break;
//                            case "8":
//                                $description = trans('admin.message4');
//                                break;
//                            case "9":
//                                $description = trans('admin.message219');
//                                break;
//                            case "10":
//                                $description = trans('admin.message220');
//                                break;
//                            default:
//                                $description = "";
//                        }
                        $arr_param_desc = get_price_parameter($string_file);
                        $description = isset($arr_param_desc[$parameterType]) ? $arr_param_desc[$parameterType] : "";
                        $return_price_card[] = array(
                            'parameter_price' => $parameter_price,
                            'pricing_parameter' => $values->PricingParameter->ParameterApplication,
                            "description" => $description,
                        );
                    }
                    $base_fare = $login->base_fare;
                    if (!empty($base_fare)) {
                        $parameterBase = PricingParameter::where([['parameterType', '=', 10], ['merchant_id', '=', $merchant_id]])->first();
                        $name = $parameterBase->ParameterApplication;

                        $newBase = array(
                            'parameter_price' => $currency . " " . $login->base_fare,
                            'pricing_parameter' => $name,
                            "description" => "Free Distance " . $login->free_distance . " " . $distance_unit . " Free Time " . $login->free_time . " Mintues",
                        );
                    }

                    array_push($return_price_card, $newBase);
                    $return_vehicle['price_card_values'] = $return_price_card;
                    $return_price_card = array_push($return_price_card, $newBase);
                    $return_vehicle['vehicleTypeName'] = $login->VehicleType->VehicleTypeName;
                    $return_vehicle['vehicleTypeDescription'] = $login->VehicleType->VehicleTypeDescription;
                    $return_vehicle['vehicleTypeImage'] = get_image($login->VehicleType->vehicleTypeImage, 'vehicle', $merchant_id);
                    $arr_vehicle_return [] = $return_vehicle;
                }
            }
            unset($value->PriceCard);
            $return_services[] = ['serviceName'=>$value->serviceName,'vehicle_type'=> $arr_vehicle_return];
        }
        return $this->successResponse(trans("$string_file.success"),$return_services);
    }



    //Fav location module is merged with add address in Account/userController
    // There is no use of this module
    public function saveFavouriteLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
            'location' => 'required',
            'category' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//                response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $user = $request->user('api');
        $user_id = $user->id;
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        if ($request->category == 3): //Other category
            $location = UserAddress::create([
                'user_id' => $user_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->location,
                'category' => $request->category,
                'address_title' => $request->other_name,
            ]);
        else:
            $location = UserAddress::updateOrCreate(
                ['user_id' => $user_id, 'category' => $request->category],
                ['latitude' => $request->latitude, 'longitude' => $request->longitude, 'address' => $request->location, 'category' => $request->category, 'address_title' => $request->other_name]
            );
        endif;
        return $this->successResponse(trans("$string_file.success"),$location);
//            response()->json(['result' => "1", 'message' => trans('api.locationadded'), 'data' => $location]);
    }

    public function viewFavouriteLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
//            'category' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $user = $request->user('api');
        $user_id = $user->id;
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $locations = UserAddress::where([['user_id', '=', $user_id]])->whereIn('category',[1,2,3])->get();
        return $this->successResponse(trans("$string_file.success"),$locations);
    }

    public function deleteFavouriteLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:user_addresses,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        UserAddress::where('id', '=', $request->id)->delete();
        $string_file = $this->getStringFile($request->merchant_id);
        return $this->successResponse(trans("$string_file.deleted"),[]);
    }





//    I think this function is not using
//    public function estimate(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'service_type' => 'required|integer|exists:service_types,id',
//            'pickup_latitude' => 'required',
//            'pickup_longitude' => 'required',
//            'drop_location' => 'required_if:total_drop_location,1,2,3,4',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
//        }
//        $driver = $request->user('api-driver');
//        $configuration = BookingConfiguration::where([['merchant_id', '=', $driver->merchant_id]])->first();
//        $otp_manual_dispatch = $configuration->otp_manual_dispatch == 1 ? true : false;
//
//        $driver_Vehicle = DriverVehicle::with(['Drivers' => function ($q) use ($driver) {
//            $q->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 1]]);
//        }])->whereHas('Drivers', function ($query) use ($driver) {
//            $query->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 1]]);
//        })->first();
//
//        if (empty($driver_Vehicle)) {
//            return response()->json(['result' => "0", 'message' => "No Vehicle Added", 'data' => []]);
//        }
//        $merchant_id = $driver->merchant_id;
//        $pricecards = PriceCard::where([['country_area_id', '=', $driver->country_area_id], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $request->service_type], ['vehicle_type_id', '=', $driver_Vehicle->vehicle_type_id]])->first();
//        if (empty($pricecards)) {
//            return ['result' => "0", 'message' => trans('api.no_price_card'), 'data' => []];
//        }
//        $drop_locationArray = json_decode($request->drop_location, true);
//        $googleArray = GoogleController::GoogleStaticImageAndDistance($request->pickup_latitude, $request->pickup_longitude, $drop_locationArray, $configuration->google_key);
//        if (empty($googleArray)) {
//            return ['result' => "0", 'message' => trans("$string_file.google_key_not_working"), 'data' => []];
//        }
//        $time = $googleArray['total_time_text'];
//        $distance = $googleArray['total_distance_text'];
//
//        $timeSmall = $googleArray['total_time_minutes'];
//        $distanceSmall = $googleArray['total_distance'];
//
//        // Calculate bill estimate
//        $estimatePrice = new PriceController();
//        $fare = $estimatePrice->BillAmount([
//            'price_card_id' => $pricecards->id,
//            'merchant_id' => $driver->merchant_id,
//            'distance' => $distanceSmall,
//            'time' => $timeSmall,
//            'booking_id' => 0,
//            'booking_time' => date('H:i'),
//            'units' => $pricecards->CountryArea->Country->distance_unit
//        ]);
//
//        $merchant = new Merchant();
//        $fare['amount'] = $merchant->FinalAmountCal($fare['amount'], $driver->merchant_id);
//
//        // $newArray = PriceController::CalculateBill($pricecards->id, $distanceSmall, $timeSmall, 0, 0,0,0,$pricecards->CountryArea->Country->distance_unit);
//        // $newArray = array_filter($newArray, function ($e) {
//        //     return ($e['type'] == "CREDIT");
//        // });
//        //$amount = $pricecards->CountryArea->Country->isoCode . sprintf("%0.2f", array_sum(array_pluck($newArray, 'amount')));
//
//        $amount = $pricecards->CountryArea->Country->isoCode . sprintf("%0.2f", $fare['amount']);
//        return response()->json(['result' => "1", 'message' => "estimate", 'data' => array('time' => $time, 'amount' => $amount, 'distance' => $distance), 'otp_manual_dispatch' => $otp_manual_dispatch]);
//    }

    public function AddWalletMoneyCoupon(Request $request)
    {
        $user = $request->user('api');
        $validator = Validator::make($request->all(), [
            'coupon_code' => ['required',
                Rule::exists('wallet_coupon_codes', 'coupon_code')->where(function ($query) use ($user) {
                    $query->where([['country_id', '=', $user->country_id]]);
                })]
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $coupon = WalletCouponCode::where([['merchant_id', $user->merchant_id], ['country_id', $user->country_id], ['coupon_code', $request->coupon_code]])->first();
        $paramArray = array(
            'user_id' => $user->id,
            'booking_id' => NULL,
            'amount' => $coupon->amount,
            'narration' => 3,
            'platform' => 1,
            'payment_method' => 1,
            'receipt' => $coupon->coupon_code,
        );
        WalletTransaction::UserWalletCredit($paramArray);
//        \App\Http\Controllers\Helper\CommonController::UserWalletCredit($user->id, NULL, $coupon->amount, 3, 1, 1, $coupon->coupon_code);
//        $user->wallet_balance = $user->wallet_balance + $coupon->amount;
//        $user->save();
//        $moneyAdded = UserWalletTransaction::create([
//            'merchant_id' => $user->merchant_id,
//            'user_id' => $user->id,
//            'platfrom' => 1,
//            'amount' => $user->wallet_balance,
//            'type' => 1,
//            'payment_method' => "Coupon",
//            'receipt_number' => $coupon->coupon_code,
//            'description' => "Wallet money added with Coupon" . $coupon->coupon_code
//        ]);
//        return response()->json(['result' => "1", 'message' => trans("$string_file.money_added_in_wallet"), 'data' => $moneyAdded]);
    }

    public function redeemPoints(Request $request)
    {
        $validate = validator($request->all(), [
            'reward_points' => 'required|numeric',
        ]);

        if ($validate->fails()) {
            return response()->json(['result' => '0', 'message' => __('api.validation.failed'), 'data' => []]);
        }

        $user = $request->user('api');
        $reward_points_data = RewardPoint:: where('merchant_id', $user->merchant_id)
            ->where('country_area_id', $user->country_area_id)
            ->where('active', 1)
            ->first();
//      dd($reward_points_data);

        if (!$reward_points_data) {
            return response()->json([
                'result' => 0,
                'message' => __('api.reward.notfound'),
                'data' => []
            ]);
        }

        $usable_reward_points = $user->usable_reward_points;

        if ($user) {
            if ($request->reward_points > $user->reward_points || $request->reward_points > $usable_reward_points) {
                return response()->json([
                    'result' => 0,
                    'message' => __('api.points.exceeded'),
                    'data' => []
                ]);
            }

            // recharge user wallet
            $recharge_amount = $request->reward_points / $reward_points_data->value_equals;
//            $user->wallet_balance = (double)$user->wallet_balance + $recharge_amount;
            $user->reward_points = $user->reward_points - $request->reward_points;
            $user->usable_reward_points = $user->usable_reward_points - $request->reward_points;
//        $user->use_reward_count = 0;
            $user->save();

            // make wallet transaction
//            WalletTransaction::userWallet($user, $recharge_amount, 1);
            $paramArray = array(
                'user_id' => $user->id,
                'booking_id' => NULL,
                'amount' => $recharge_amount,
                'narration' => 1,
                'platform' => 2,
                'payment_method' => 2,
            );
            WalletTransaction::UserWalletCredit($paramArray);
//            \App\Http\Controllers\Helper\CommonController::UserWalletCredit($user->id,NULL,$recharge_amount,1,2,2);

            return response()->json([
                'result' => 1,
                'message' => __('api.success'),
                'data' => []
            ]);
        }


        return response()->json(['result' => '0', 'message' => __('api.user.notfound'), 'data' => []]);
    }

    public function driverRedeemPoints(Request $request)
    {
        $validate = validator($request->all(), [
            'reward_points' => 'required|numeric',
        ]);

        if ($validate->fails()) {
            return response()->json(['result' => '0', 'message' => __('api.validation.failed'), 'data' => []]);
        }

        $driver = $request->user('api-driver');
        $reward_points_data = RewardPoint:: where([
            ['merchant_id', '=', $driver->merchant_id],
            ['country_area_id', '=', $driver->country_area_id],
            ['active', '=', 1]
        ])->first();

        if (!$reward_points_data) {
            return response()->json([
                'result' => 0,
                'message' => __('api.reward.notfound'),
                'data' => []
            ]);
        }

        $usable_reward_points = $driver->usable_reward_points;

        if ($driver) {
            if ($request->reward_points > $driver->reward_points || $request->reward_points > $usable_reward_points) {
                return response()->json([
                    'result' => 0,
                    'message' => __('api.points.exceeded'),
                    'data' => []
                ]);
            }

            // recharge user wallet
            $recharge_amount = $request->reward_points / $reward_points_data->value_equals;
//            $driver->wallet_balance = (double)$driver->wallet_balance + $recharge_amount;
            $driver->reward_points = $driver->reward_points - $request->reward_points;
//      $driver->use_reward_count = 0;
            $driver->usable_reward_points = $driver->usable_reward_points - $request->reward_points;
            $driver->save();
            $paramArray = array(
                'driver_id' => $driver->id,
                'booking_id' => NULL,
                'amount' => $recharge_amount,
                'narration' => 9,
                'platform' => 1,
                'payment_method' => 2,
            );
            WalletTransaction::WalletCredit($paramArray);
//            \App\Http\Controllers\Helper\CommonController::WalletCredit($driver->id, NULL, $recharge_amount, 9, 1, 2);
            // make wallet transaction
//      WalletTransaction::driverWallet($driver , $recharge_amount , 1 );

            return response()->json([
                'result' => 1,
                'message' => __('api.success'),
                'data' => []
            ]);
        }


        return response()->json(['result' => '0', 'message' => __('api.user.notfound'), 'data' => []]);
    }


    public function getNetworkCode(Request $request)
    {
//        $user = ($request->for == "user") ? $request->user('api') : $request->user('api-driver');
        $paymentConfig = PaymentOptionsConfiguration::where([['merchant_id', $request->merchant_id]])->first();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://xchange.korbaweb.com/api/v1.0/collection_network_options/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\r\n    \"client_id\": \"$paymentConfig->auth_token\"\r\n}",
            CURLOPT_HTTPHEADER => array(
                "authorization: HMAC $paymentConfig->api_public_key:79201d66e586712d736334bb63861cfe6fac39851dc0a709214d9598e4c0d5fc",
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: 72da0f74-9b01-46d9-97d6-2edef4867c7d"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return response()->json(['result' => '0', 'message' => $err, 'data' => []]);
        }
        $re = json_decode($response);
        return response()->json([
            'result' => 1,
            'message' => ('api.success'),
            'data' => $re
        ]);
    }

//    public function geofenceEnqueue(Request $request){
//        $driver = $request->user('api-driver');
//        $validator = validator($request->all() , [
//            'latitude' => 'required',
//            'longitude' => 'required',
//            'type' => 'required|between:1,2'
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return response()->json(['result' => "0", 'message' => $errors[0]]);
//        }
//        $geofence_queue_text = trans('api.not_in_geofence_queue_area');
//        $geofence_queue_color_code = '#FF0000';
//
//        $config = Configuration::where('merchant_id',$driver->merchant_id)->first();
//        if(isset($config->geofence_module) && $config->geofence_module == 1){
//            if($driver->online_offline == 1 && $driver->login_logout == 1 && $driver->free_busy == 2){
//                $driverArea = CountryArea::find($driver->country_area_id);
//                $checkGeofenceArea = $this->findGeofenceArea($request->latitude, $request->longitude,$driverArea->id,$driver->merchant_id);
//                if(!empty($checkGeofenceArea) && isset($checkGeofenceArea->RestrictedArea->queue_system) && $checkGeofenceArea->RestrictedArea->queue_system == 1){
//                    if($request->type == 1){
//                        $driverQueue = GeofenceAreaQueue::where(function($query) use($driver,$driverArea, $checkGeofenceArea){
//                            $query->where([
//                                ['merchant_id','=',$driver->merchant_id],
//                                ['country_area_id','=',$driverArea->id],
//                                ['geofence_area_id','=',$checkGeofenceArea['id']],
//                                ['driver_id','=',$driver->id],
//                                ['queue_status','=','1'] // Check if already in queue
//                            ]);
//                        })->whereDate('created_at',date('Y-m-d'))->get();
//                        if(count($driverQueue) <= 0){
//                            $existingQueue = GeofenceAreaQueue::where(function($query) use($driver,$driverArea, $checkGeofenceArea){
//                                $query->where([['merchant_id','=',$driver->merchant_id],['country_area_id','=',$driverArea->id],['geofence_area_id','=',$checkGeofenceArea['id']]]);
//                            })->orderBy('queue_no','desc')->whereDate('created_at',date('Y-m-d'))->first();
//                            if(!empty($existingQueue)){
//                                $newQueue = GeofenceAreaQueue::create(
//                                    ['merchant_id' => $driver->merchant_id,
//                                        'country_area_id' => $driverArea->id,
//                                        'geofence_area_id' => $checkGeofenceArea['id'],
//                                        'driver_id' => $driver->id,
//                                        'queue_no' => ($existingQueue['queue_no'] + 1),
//                                        'queue_status' => 1,
//                                        'entry_time' => date('Y-m-d H:i:s')]);
//                            }else{
//                                $newQueue = GeofenceAreaQueue::create(
//                                    ['merchant_id' => $driver->merchant_id,
//                                        'country_area_id' => $driverArea->id,
//                                        'geofence_area_id' => $checkGeofenceArea['id'],
//                                        'driver_id' => $driver->id,
//                                        'queue_no' => 1,
//                                        'queue_status' => 1,
//                                        'entry_time' => date('Y-m-d H:i:s')]);
//                            }
//                            $geofence_queue_text = $checkGeofenceArea->LanguageSingle->AreaName.' Queue On - '.$newQueue->queue_no;
//                            $geofence_queue_color_code = '#008000';
//                            return response()->json(['result' => '1', 'type' => '1', 'message' => trans('api.now_in_queue'), 'queue_no' => $newQueue->queue_no,'geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code]);
//                        }else{
//                            $driverQueue = GeofenceAreaQueue::where(function($query) use($driver,$driverArea, $checkGeofenceArea){
//                                $query->where([
//                                    ['merchant_id','=',$driver->merchant_id],
//                                    ['country_area_id','=',$driverArea->id],
//                                    ['geofence_area_id','=',$checkGeofenceArea['id']],
//                                    ['driver_id','=',$driver->id],
//                                    ['queue_status','=','1'] // Check if already in queue
//                                ]);
//                            })->whereDate('created_at',date('Y-m-d'))->first();
//                            $geofence_queue_text = $checkGeofenceArea->LanguageSingle->AreaName.' Queue On - '.$driverQueue->queue_no;
//                            $geofence_queue_color_code = '#008000';
//                            return response()->json(['result' => '1', 'type' => '1', 'message' => trans('api.already_in_queue'), 'geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code]);
//                        }
//                    }elseif($request->type == 2){
//                        $this->geofenceDequeue($request->latitude, $request->longitude,$driver,$checkGeofenceArea->id);
//                        $geofence_queue_text = $checkGeofenceArea->LanguageSingle->AreaName.' Queue Off';
//                        $geofence_queue_color_code = '#FF0000';
//                        return response()->json(['result' => '1', 'type' => '2', 'message' => trans('api.removed_from_queue'), 'geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code]);
//                    }
//                }else{
//                    return response()->json(['result' => '0', 'message' => trans('api.not_in_geofence_queue_area'),'geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code]);
//                }
//            }else{
//                return response()->json(['result' => '0', 'message' => trans('api.not_eligible'), 'geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code]);
//            }
//        }else{
//            return response()->json(['result' => '0', 'message' => trans('api.geofence_not_enable'), 'geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code]);
//        }
//    }
//
//    public function geofenceDequeue($lat, $long, $driver, $geofence_area_id){
//        $config = Configuration::where('merchant_id',$driver->merchant_id)->first();
//        if(isset($config->geofence_module) && $config->geofence_module == 1){
//            $geofenceArea = CountryArea::with('RestrictedArea')->where([['is_geofence','=',1],['id','=',$geofence_area_id]])->first();
//            if(!empty($geofenceArea) && isset($geofenceArea->RestrictedArea->queue_system) && $geofenceArea->RestrictedArea->queue_system == 1){
//                $existingQueue = GeofenceAreaQueue::where([
//                    ['merchant_id','=',$driver->merchant_id],
//                    ['country_area_id', '=', $driver->country_area_id],
//                    ['geofence_area_id','=',$geofence_area_id],
//                    ['driver_id','=',$driver->id],
//                    ['queue_status','=','1'] // Check if already in queue
//                ])->whereDate('created_at',date('Y-m-d'))->first();
//                if(!empty($existingQueue)){
//                    $existingQueue->queue_status = 2;
//                    $existingQueue->exit_time = date('Y-m-d H:i:s');
//                    $existingQueue->save();
//                }
//            }
//        }
//    }

    public function geofenceQueueInOut(Request $request)
    {
        $driver = $request->user('api-driver');

        $validator = validator($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
            'type' => 'required|between:1,2'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//            return response()->json(['result' => "0", 'message' => $errors[0]]);
        }
        $geofence_queue_text = trans('api.not_in_geofence_queue_area');
        $geofence_queue_color_code = '#FF0000';

        $config = Configuration::where('merchant_id', $driver->merchant_id)->first();
        if (isset($config->geofence_module) && $config->geofence_module == 1) {
            if ($driver->online_offline == 1 && $driver->login_logout == 1 && $driver->free_busy == 2) {
                $driverArea = CountryArea::find($driver->country_area_id);
                $checkGeofenceArea = $this->findGeofenceArea($request->latitude, $request->longitude, $driverArea->id, $driver->merchant_id);
                if (!empty($checkGeofenceArea) && isset($checkGeofenceArea->RestrictedArea->queue_system) && $checkGeofenceArea->RestrictedArea->queue_system == 1) {
                    if ($request->type == 1) {
                        $driverQueue = GeofenceAreaQueue::where(function ($query) use ($driver, $driverArea, $checkGeofenceArea) {
                            $query->where([
                                ['merchant_id', '=', $driver->merchant_id],
                                ['country_area_id', '=', $driverArea->id],
                                ['geofence_area_id', '=', $checkGeofenceArea['id']],
                                ['driver_id', '=', $driver->id],
                                ['queue_status', '=', '1'] // Check if already in queue
                            ]);
                        })->whereDate('created_at', date('Y-m-d'))->get();
                        if (count($driverQueue) <= 0) {
                            $existingQueue = GeofenceAreaQueue::where(function ($query) use ($driver, $driverArea, $checkGeofenceArea) {
                                $query->where([['merchant_id', '=', $driver->merchant_id], ['country_area_id', '=', $driverArea->id], ['geofence_area_id', '=', $checkGeofenceArea['id']]]);
                            })->orderBy('queue_no', 'desc')->whereDate('created_at', date('Y-m-d'))->first();
                            if (!empty($existingQueue)) {
                                $newQueue = GeofenceAreaQueue::create(
                                    ['merchant_id' => $driver->merchant_id,
                                        'country_area_id' => $driverArea->id,
                                        'geofence_area_id' => $checkGeofenceArea['id'],
                                        'driver_id' => $driver->id,
                                        'queue_no' => ($existingQueue['queue_no'] + 1),
                                        'queue_status' => 1,
                                        'entry_time' => date('Y-m-d H:i:s')]);
                            } else {
                                $newQueue = GeofenceAreaQueue::create(
                                    ['merchant_id' => $driver->merchant_id,
                                        'country_area_id' => $driverArea->id,
                                        'geofence_area_id' => $checkGeofenceArea['id'],
                                        'driver_id' => $driver->id,
                                        'queue_no' => 1,
                                        'queue_status' => 1,
                                        'entry_time' => date('Y-m-d H:i:s')]);
                            }
                            $geofence_queue_text = $checkGeofenceArea->LanguageSingle->AreaName . ' Queue On - ' . $newQueue->queue_no;
                            $geofence_queue_color_code = '#008000';
                            $result = array(
                                'queue_no' => $newQueue->queue_no,
                                'geofence_queue_text' => $geofence_queue_text,
                                'geofence_queue_color' => $geofence_queue_color_code
                            );
                            $message = trans('api.now_in_queue');
//                            return response()->json(['result' => '1', 'type' => '1', 'message' => trans('api.now_in_queue'), 'queue_no' => $newQueue->queue_no,'geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code]);
                        } else {
                            $driverQueue = GeofenceAreaQueue::where(function ($query) use ($driver, $driverArea, $checkGeofenceArea) {
                                $query->where([
                                    ['merchant_id', '=', $driver->merchant_id],
                                    ['country_area_id', '=', $driverArea->id],
                                    ['geofence_area_id', '=', $checkGeofenceArea['id']],
                                    ['driver_id', '=', $driver->id],
                                    ['queue_status', '=', '1'] // Check if already in queue
                                ]);
                            })->whereDate('created_at', date('Y-m-d'))->first();
                            $geofence_queue_text = $checkGeofenceArea->LanguageSingle->AreaName . ' Queue On - ' . $driverQueue->queue_no;
                            $geofence_queue_color_code = '#008000';
                            $result = array(
                                'queue_no' => $driverQueue->queue_no,
                                'geofence_queue_text' => $geofence_queue_text,
                                'geofence_queue_color' => $geofence_queue_color_code,
                                'type' => '1'
                            );
                            $message = trans('api.already_in_queue');
//                            return response()->json(['result' => '1', 'type' => '1', 'message' => trans('api.already_in_queue'), 'queue_no' => $driverQueue->queue_no, 'geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code]);
                        }
                    } elseif ($request->type == 2) {
                        $result = $this->geofenceDequeue($request->latitude, $request->longitude, $driver, $checkGeofenceArea->id);
                        if (!$result) {
                            return response()->json(['result' => '0', 'message' => "You Can't Exit Before 15 Minute."]);
                        }
                        $geofence_queue_text = $checkGeofenceArea->LanguageSingle->AreaName . ' Queue Off';
                        $geofence_queue_color_code = '#FF0000';
                        $result = array(
                            'geofence_queue_text' => $geofence_queue_text,
                            'geofence_queue_color' => $geofence_queue_color_code,
                            'type' => '2'
                        );
                        $message = trans('api.removed_from_queue');
//                        return response()->json(['result' => '1', 'type' => '2', 'message' => trans('api.removed_from_queue'), 'geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code]);
                    } else {
                        return $this->failedResponse(trans('api.invalid_type'));
                    }
                    return $this->successResponse($message, $result);
                } else {
                    return $this->failedResponse(trans('api.not_in_geofence_queue_area'), array('geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code));
//                    return response()->json(['result' => '0', 'message' => trans('api.not_in_geofence_queue_area'),'geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code]);
                }
            } else {
                return $this->failedResponse(trans('api.not_eligible'), array('geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code));
//                return response()->json(['result' => '0', 'message' => trans('api.not_eligible'), 'geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code]);
            }
        } else {
            return $this->failedResponse(trans('api.geofence_not_enable'), array('geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code));
//            return response()->json(['result' => '0', 'message' => trans('api.geofence_not_enable'), 'geofence_queue_text' => $geofence_queue_text, 'geofence_queue_color' => $geofence_queue_color_code]);
        }
    }

    public function geofenceDequeue($lat, $long, $driver, $geofence_area_id)
    {
        $config = Configuration::where('merchant_id', $driver->merchant_id)->first();
        if (isset($config->geofence_module) && $config->geofence_module == 1) {
            $geofenceArea = CountryArea::with('RestrictedArea')->where([['is_geofence', '=', 1], ['id', '=', $geofence_area_id]])->first();
            if (!empty($geofenceArea) && isset($geofenceArea->RestrictedArea->queue_system) && $geofenceArea->RestrictedArea->queue_system == 1) {
                $existingQueue = GeofenceAreaQueue::where([
                    ['merchant_id', '=', $driver->merchant_id],
                    ['country_area_id', '=', $driver->country_area_id],
                    ['geofence_area_id', '=', $geofence_area_id],
                    ['driver_id', '=', $driver->id],
                    ['queue_status', '=', '1'] // Check if already in queue
                ])->whereDate('created_at', date('Y-m-d'))->first();
                if (!empty($existingQueue)) {
                    $existingQueueTime = strtotime($existingQueue->created_at->toDateTimeString());
                    $currentTime = strtotime(Carbon::now()->toDateTimeString());
                    $total_diff_mint = ($currentTime - $existingQueueTime) / 60;
                    if ($total_diff_mint < 15) {
                        return false;
                    } else {
                        $existingQueue->queue_status = 2;
                        $existingQueue->exit_time = date('Y-m-d H:i:s');
                        $existingQueue->save();
                        return true;
                    }
                }
            }
        }
    }

    public function findGeofenceArea($lat, $long, $base_area_id, $merchant_id)
    {
        $geofenceAreas = CountryArea::with('RestrictedArea')->whereHas('RestrictedArea', function ($query) use ($base_area_id) {
            $query->whereRaw(DB::raw("find_in_set($base_area_id,base_areas)"));
        })->get();
        $checkGeofenceArea = [];
        if (!empty($geofenceAreas)) {
            foreach ($geofenceAreas as $geofenceArea) {
                $checkGeofenceArea = $this->GeofenceArea($lat, $long, $merchant_id, $geofenceArea->id);
                if (!empty($checkGeofenceArea)) {
                    $geofenceAreaFound = CountryArea::with('RestrictedArea')->find($checkGeofenceArea['id']);
                    return $geofenceAreaFound;
                }
            }
        }
        return $checkGeofenceArea;
    }

    public function geofenceInOut(Request $request)
    {
        $validator = validator($request->all(), [
            'type' => 'required', // 1 - In, 2 - Out
            'geofence_area_id' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $geofence_area = CountryArea::find($request->geofence_area_id);
            $driver = $request->user('api-driver');
            if ($request->type == 1) {
                $message = trans('api.welcome_to_geofence_area') . ' : ' . $geofence_area->CountryAreaName;
                $title = trans('api.welcome_to') . ' : ' . $geofence_area->CountryAreaName;
            } elseif ($request->type == 2) {
                $message = trans('api.thanks_for_visit_geofence_area') . ' : ' . $geofence_area->CountryAreaName;
                $title = trans('api.exit_from') . ' : ' . $geofence_area->CountryAreaName;
            } else {
                return $this->failedResponse(trans('api.invalid_type'));
            }
            $large_icon = '';
            $data = array(
                'notification_type' => "GEOFENCE",
                'segment_type' => "TAXI",
                'segment_data' => time(),
                'notification_gen_time' => time(),
            );
            $arr_param = ['driver_id' => $driver->id, 'data' => $data, 'message' => $message, 'merchant_id' => $driver->merchant_id, 'title' => $title, 'large_icon' => $large_icon];
            Onesignal::DriverPushMessage($arr_param);
            return $this->successResponse(trans('api.geofence_updated_successfully'));
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
    }

    public function SaveGuestUserInfo(Request $request)
    {
        $string_file = $this->getStringFile($request->merchant_id);
        $validator = validator($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'merchant_id' => 'required',
            'secret_key' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0]]);
        }
        try {
            $merchant = DB::table('merchants')->where(['id' => $request->merchant_id, 'merchantSecretKey' => $request->secret_key])->first();
            if (!empty($merchant)) {
                DB::table('guest_users')->insert(
                    ['merchant_id' => $merchant->id, 'name' => $request->name, 'phone' => $request->phone]
                );
                return response()->json(['result' => "1", 'message' => trans('api.guest_record_saved_successfully')]);
            } else {
                return response()->json(['result' => "0", 'message' => trans("$string_file.merchant_not_found")]);
            }

        } catch (\Exception $e) {
            $errors = $e->getMessage();
            return response()->json(['result' => "0", 'message' => $errors]);
        }
    }

    function getPaymentMethod(Request $request)
    {
        $validator = validator($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0]]);
        }
        $paymentMethods = NULL;
        $user = $request->user('api');
        $string_file = $this->getStringFile($request->merchant_id);
        try
        {
            $this->getAreaByLatLong($request,$string_file);
            if(!empty($request->area))
            {
                $service_area = CountryArea::where('id',$request->area)->first();
                if($request->payment_type == "ADVANCE_PAYMENT")
                {
                    $paymentMethods = $service_area->PaymentMethod->where('id','!=',1);
                }
                elseif(!empty($request->outstanding_id))
                {
                    $paymentMethods = $service_area->PaymentMethod->whereNotIn('id',[1,6]);
                }
                else
                {
                    $paymentMethods = $service_area->PaymentMethod;
                }
            }
            $bookingData = new BookingDataController();
            $options = $bookingData->PaymentOption($paymentMethods, $user->id, null, NULL);

        }catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }

        return $this->successResponse(trans("$string_file.data_found"), $options);
    }

    public function segmentSubGroup($segment_slug,$segment_group_id = 2)
    {
        $sub_group = "";
        $segment_sub_group = \Config::get('custom.segment_sub_group');
        foreach ($segment_sub_group as $sub_group_key => $group) {
            $in_array = in_array($segment_slug, $group);
            if ($in_array == true) {
                $sub_group = $sub_group_key;
                break;
            }
        }
      if(empty($sub_group) && $segment_group_id == 2)
       {
           $sub_group = 'handyman_order';
       }
        return $sub_group;
    }

    // common apis for all segments

    /*
     *  get booking or order information on driver screen
    */
    public function bookingOrderInfo(Request $request)
    {
        $request_fields = [
            'segment_slug' => 'required',
            'booking_order_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ];

        $request->request->add(['id' => $request->booking_order_id]);
        $string_file = $this->getStringFile($request->merchant_id);
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {

            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "booking":
                    $booking = new BookingController;
                    $return_data = $booking->driverBookingInfo($request);
                    break;
                case "order":
                    $order = new OrderController;
                    $return_data = $order->getOrderInformation($request);
                    break;

                case "handyman_order":
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction

            return $this->failedResponse($message);
        }
        return $this->successResponse(trans("$string_file.data_found"), $return_data);
    }


    /*
    *  update status of booking or order
   */
    public function bookingOrderAcceptReject(Request $request)
    {
        $request_fields = [
            'segment_slug' => 'required',
            'booking_order_id' => 'required',
            'status' => 'required|in:ACCEPT,REJECT',
            'latitude' => 'required',
            'longitude' => 'required',
        ];
//        [
//            'id' => [
//                'required',
//                'integer',
//                Rule::exists('orders', 'id')->where(function ($query) {
//                    $query->whereIn('booking_status', array(1));
//                }),
//                Rule::exists('booking_request_drivers')->where(function ($query) use ($driver_id) {
//                    $query->where('driver_id', $driver_id);
//                }),
//            ],
//        ], [
//        'exists' => trans('api.ride_already'),
//    ]


        $request->request->add(['id' => $request->booking_order_id]);
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {

            $message = "";
            $driver = $request->user('api-driver');
            $driver_config = DriverConfiguration::where("merchant_id",$driver->merchant_id)->first();
            $string_file = $this->getStringFile(Null, $driver->Merchant);
            if($driver->free_busy == 1)
            {
                if((isset($driver_config->delivery_busy_driver_accept_ride) && $driver_config->delivery_busy_driver_accept_ride == 1) || $driver->Merchant->Configuration->new_ride_before_ride_end == 1){
                    // Skip this case
                }else{
                    return $this->failedResponse(trans("$string_file.existing_ride_order_error"));
                }
            }
            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "booking":
                   // $this->waitForAccept($request->booking_order_id, $request->user('api-driver')->id);
                    $booking = new BookingController;
                    $response = $booking->bookingAcceptReject($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "order":
                    $order = new OrderController;
                    //$this->waitOrderForAccept($request->booking_order_id,$driver->id);
                    $response = $order->orderAcceptReject($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "handyman_order":
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // p($message);
            // Rollback Transaction

            return $this->failedResponse($message);
        }
        return $this->successResponse($message, $return_data);
    }


    /*
    *  get booking or order information on driver screen
   */
    public function sliderData(Request $request)
    {
        $request_fields = [];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {
            $driver = $request->user('api-driver');
            if(!empty($request->player_id))
            {
                $driver->player_id = $request->player_id;
                $driver->device = $request->device;
                $driver->save();
            }
            // Set language for notification
            $commonObj = new \App\Http\Controllers\Helper\CommonController();
            $commonObj->setLanguage($driver->id,2);

            $string_file = $this->getStringFile(NULL,$driver->Merchant);
            if($driver->Merchant->Configuration->driver_wallet_status == 1 && !empty($driver->CountryArea->minimum_wallet_amount) && !empty($driver->wallet_money) && $driver->wallet_money < $driver->CountryArea->minimum_wallet_amount)
            {
                $message = trans_choice("$string_file.low_wallet_warning", 3, ['AMOUNT' => $driver->CountryArea->minimum_wallet_amount]);
                return $this->failedResponse($message);
//                return $this->failedResponse(trans("$string_file.low_wallet_warning"));
            }
            $data = [];
            $return_online_config = [];
            if($driver->segment_group_id == 1)
            {
                $booking = new BookingController;
                $return_data1 = $booking->getOngoingBookings($request);
                $order = new OrderController;
                $return_data2 = $order->getOngoingOrders($request);
                $data = array_merge($return_data1, $return_data2);
                $online_config = $this->getDriverOnlineConfig($driver, 'online_details');
                $return_online_config = $online_config['detail'];
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            return $this->failedResponse($message);
        }
        $ride_acco_to_gender = $driver->Merchant->ApplicationConfiguration->gender == 1 ? (($driver->driver_gender == NULL || $driver->driver_gender == 1) ? false : true) : false;
        $return_data['driver_mode'] = $data;
        $return_data['driver_mode'] = $data;
        $return_data['driver_mode_count'] = count($data);
        $return_data['driver_free_busy_status'] = $driver->free_busy;
        $return_data['driver_online_offline_status'] = $driver->online_offline;
        $return_data['driver_area_downgrade_config'] = ($driver->Merchant->Configuration->manual_downgrade_enable == 1 && $driver->CountryArea->manual_downgradation == 1);
        $return_data['term_status'] = $driver->term_status;
        $return_data['rides_according_to_gender'] = $ride_acco_to_gender;
        $return_data['rider_gender_choice'] = (string)$driver->rider_gender_choice;
        $return_data['work_set'] = $return_online_config;
        return $this->successResponse(trans("$string_file.data_found"), $return_data);
    }


    /*
    *  driver arrived at pickup location
   */
    public function arrivedAtPickup(Request $request)
    {
        $request_fields = [
            'segment_slug' => 'required',
            'booking_order_id' => 'required',
            // 'status' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ];

        $request->request->add(['id' => $request->booking_order_id]);
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {

            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "booking":
                    $booking = new BookingController;
                    $response = $booking->arrivedAtPickup($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "order":
                    $order = new OrderController;
                    $response = $order->arrivedAtPickup($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "handyman_order":
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse($message, $return_data);
    }

    /*
    *  driver arrived at pickup location
   */
    public function orderInProcess(Request $request)
    {
        $request_fields = [
            'segment_slug' => 'required',
            'booking_order_id' => 'required',
            'status' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ];

        $request->request->add(['id' => $request->booking_order_id]);
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {

            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "booking":
                    $booking = new BookingController;
                    $response = $booking->BookingAccept($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "order":
                    $order = new OrderController;
                    $response = $order->OrderInProcess($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "handyman_order":
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse($message, $return_data);
    }


    /*
    *  driver arrived at pickup location and now driver either pick order or start ride
   */
    public function bookingOrderPicked(Request $request)
    {
        $request_fields = [
            'segment_slug' => 'required',
            'booking_order_id' => 'required',
//            'status' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ];

        $request->request->add(['id' => $request->booking_order_id]);
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {

            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "booking":
                    $booking = new BookingController;
                    $response = $booking->startBooking($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "order":
                    $order = new OrderController;
                    $response = $order->pickedOrder($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "handyman_order":
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse($message, $return_data);
    }


    /*
  * driver  delivered order
   */
    public function deliverOrder(Request $request)
    {
        $request_fields = [
            'segment_slug' => 'required',
            'booking_order_id' => 'required',
//            'status' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ];

        $request->request->add(['id' => $request->booking_order_id]);
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {

            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "booking":
                    $booking = new BookingController;
                    $response = $booking->BookingAccept($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "order":
                    $order = new OrderController;
                    $response = $order->deliverOrder($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "handyman_order":
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse($message, $return_data);
    }


    /*
    * get booking order payment info
   */
    public function bookingOrderPaymentInfo(Request $request)
    {
        $request_fields = [
            'segment_slug' => 'required',
            'booking_order_id' => 'required',
        ];

        $request->request->add(['id' => $request->booking_order_id]);
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $return_data = [];
        try {
            $string_file = $this->getStringFile($request->merchant_id);
            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "booking":
                    $booking = new BookingController;
                    $response = $booking->BookingAccept($request);
                    $return_data = $response['data'];
                    break;
                case "order":
                    $order = new OrderController;
                    $response = $order->orderPaymentInfo($request);
                    $return_data = $response['data'];
                    break;
                case "handyman_order":
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse(trans("$string_file.data_found"), $return_data);
    }

    /*
    * update booking order payment status
   */
    public function updateBookingOrderPaymentStatus(Request $request)
    {
        $request_fields = [
            'segment_slug' => 'required',
            'booking_order_id' => 'required',
            'payment_status' => 'required|in:0,1', //0 means pending, 1 means paid, 3 means failed
        ];

        $request->request->add(['id' => $request->booking_order_id]);
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $return_data = [];
        try {
            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
//                case "booking":
//                    $booking = new BookingController;
//                    $response = $booking->BookingAccept($request);
//                    break;
                case "order":
                    $order = new OrderController;
                    $response = $order->updateOrderPaymentStatus($request);
                    break;
                case "handyman_order":
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse(trans('api.payment_status_updated'), $return_data);
    }

    /*
    *  driver completed ride, delivered order
   */
    public function completeBookingOrder(Request $request)
    {
        $request_fields = [
            'segment_slug' => 'required',
            'booking_order_id' => 'required',
        ];

        $request->request->add(['id' => $request->booking_order_id]);
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {
            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "booking":
                    $booking = new BookingController;
                    $response = $booking->completeBooking($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "order":
                    $order = new OrderController;
                    $response = $order->completeOrder($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "handyman_order":
                    break;
            }
//            $return_data['data']['driver_offline']  = true; // means driver online
            $return_data['data']['driver_online']  = true; // means driver online
            $driver = $request->user('api-driver');
            if($driver->Merchant->Configuration->driver_wallet_status == 1 && $driver->wallet_money < $driver->CountryArea->minimum_wallet_amount )
            {
//                $return_data['data']['driver_offline']  = false; // means driver offline
                $return_data['data']['driver_online']  = false; // means driver offline
                $driver->online_offline = 2;
                $driver->save();
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse($message, $return_data);
    }


    /*
    *  driver cancel ride, order
   */
    public function cancelBookingOrder(Request $request)
    {
        $request_fields = [
            'segment_slug' => 'required',
            'booking_order_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ];

        $request->request->add(['id' => $request->booking_order_id]);
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {

            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "booking":
                    $booking = new BookingController;
                    $response = $booking->cancelBooking($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "order":
                    $order = new OrderController;
                    $response = $order->cancelOrder($request);
                    $message = $response['message'];
                    $return_data = $response['data'];
                    break;
                case "handyman_order":
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse($message, $return_data);
    }

    /*
   *  driver get active booking order
  */
    public function getActiveBookingOrder(Request $request)
    {
        $request_fields = [
//            'segment_slug' => 'required',
        ];

        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {
            $string_file = $this->getStringFile($request->merchant_id);
            $booking = new BookingHistoryController;
            $return_data1 = $booking->getActiveBooking($request);

            $order = new OrderController;
            $return_data2 = $order->getActiveOrders($request);

            $return_data = array_merge($return_data1, $return_data2);
            if(empty($return_data))
            {
                return $this->failedResponse(trans("$string_file.no_live_data"));
            }

        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse(trans("$string_file.data_found"), $return_data);
    }


    /*
  *  driver get past booking order
 */
    public function getPastBookingOrder(Request $request)
    {
        $request_fields = [
            'segment_slug' => 'required',
            'segment_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {
            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "booking":
                    $booking = new BookingHistoryController;
                    $data = $booking->getPastBooking($request);
                    $return_data = $data['data'];
                    $message = $data['message'];
                    break;
                case "order":
                    $order = new OrderController;
                    $data = $order->getPastOrders($request);
                    $return_data = $data['data'];
                    $message = $data['message'];
                    break;
                case "handyman_order":
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse($message, $return_data);
    }

    /*
  *  driver get details of  booking/order
 */
    public function getBookingOrderDetails(Request $request)
    {
        $request_fields = [
            'segment_slug' => 'required',
            'booking_order_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {
            $request->request->add(['id' => $request->booking_order_id]);
            $string_file = $this->getStringFile($request->merchant_id);
            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "booking":
                    $booking = new BookingHistoryController;
                    $return_data = $booking->getBookingDetails($request);
                    break;
                case "order":
                    $order = new OrderController;
                    $return_data = $order->getOrderDetails($request);
                    break;
                case "handyman_order":
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse(trans("$string_file.data_found"), $return_data);
    }

    function googleDirectionData(Request $request)
    {
        $request_fields = [
            'from_latitude' => 'required',
            'from_longitude' => 'required',
            'to_latitude' => 'required',
            'to_longitude' => 'required',
            'segment_slug' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $return_data = [];
        try {
            if(isset($request->is_user) && $request->is_user == true){
                $user = $request->user('api');
            }else{
                $user = $request->user('api-driver');
            }
            $string_file = $this->getStringFile($request->merchant_id);
            $poly_line = $user->Merchant->BookingConfiguration->polyline;
            if($poly_line == 1)
            {
                $key = $user->Merchant->BookingConfiguration->google_key;
                $from = $request->from_latitude . ',' . $request->from_longitude;
                $to = $request->to_latitude . ',' . $request->to_longitude;
                $units = ($user->Country['distance_unit'] == 1) ? 'metric' : 'imperial';
                $google = new GoogleController;
                $return_data = $google->GoogleDistanceAndTime($from, $to, $key, $units, true,'googleDirectionData',$string_file);
                if(!empty($request->booking_id))
                {
                    $booking = Booking::select('id','ploy_points')->find($request->booking_id);
                    $booking->ploy_points = isset($return_data['poly_point']) ? $return_data['poly_point'] : "";
                    $booking->save();
                }
            }

        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse(trans("$string_file.data_found"), $return_data);
    }

    // user rating to driver
    public function rateToDriverByUser(Request $request)
    {
        $string_file = $this->getStringFile($request->merchant_id);
        $request_fields = [
            'segment_slug' => 'required',
            'booking_order_id' => 'required',
            'rating' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {
            $request->request->add(['id' => $request->booking_order_id]);
            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "booking":
                    $booking = new BookingController;
                    $return_data = $booking->bookingRating($request);
                    break;
                case "order":
                    $order = new OrderController;
                    $return_data = $order->orderRating($request);
                    break;
                case "handyman_order":
                    $order = new HandymanOrderController;
                    $return_data = $order->handymanOrderRating($request);
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse(trans("$string_file.rating_thanks"), $return_data);
    }

//    public function getGeofenceArea(Request $request){
//        $driver = $request->user('api-driver');
//        try{
//            $areas = $this->getGeofenceAreaList(false,$driver->merchant_id, $driver->country_area_id);
//            $areas = $areas->get();
//            if(!empty($areas)){
//                $areas = $areas->map(function ($item, $key)
//                {
//                    return array(
//                        'id' => $item->id,
//                        'area_name' => $item->CountryAreaName,
//                        'queue_system' => (isset($item->RestrictedArea->queue_system) && $item->RestrictedArea->queue_system == 1) ? true : false,
//                        'coordinates' => json_decode($item->AreaCoordinates,true),
//                    );
//                });
//            }
//            return $this->successResponse(trans('admin.geofence_area'),$areas);
//        }catch(\Exception $e){
//            return $this->failedResponse($e->getMessage());
//        }
//    }

    public function checkUserWallet($user,$amount){
        try{
            $string_file = $this->getStringFile($user->merchant_id);
            if(!empty($user->id))
            {
                if($user->wallet_balance < $amount)
                {
                    $message = trans_choice("$string_file.low_wallet_warning", 3, ['AMOUNT' => $amount]);
//                    $message = trans("$string_file.low_wallet_warning");
                    throw new \Exception($message);
                }
            }
            return true;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }

    }

    /*
      *  driver get earning details of  booking/order
     */
    public function getBookingOrderAccountDetails(Request $request)
    {
        $request_fields = [
            'segment_id' => 'required',
            'date' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $return_data = [];
        try {
            $segment = Segment::find($request->segment_id);
            $request->request->add(['segment_slug' => $segment->slag]);
            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug,$segment->segment_group_id);
            $driver_earning = new DriverEarningController();
            switch ($sub_group) {
                case "booking":
                    $return_data = $driver_earning->DriverBookingAccountEarnings($request);
                    break;
                case "order":
                    $return_data = $driver_earning->DriverOrderAccountEarnings($request);
                    break;
                case "handyman_order":
                    $return_data = $driver_earning->DriverHandymanAccountEarnings($request);
                    break;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse(trans('api.earning'), $return_data);
    }

    // tip from tracking screen
    public function addTip(Request $request)
    {
        $request_fields = [
            'id' => 'required',
            'tip_amount' => 'required',
            'segment_slug' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $message = "";
        try {
            $segment_slug = $request->segment_slug;
            $sub_group = $this->segmentSubGroup($segment_slug);
            switch ($sub_group) {
                case "order":
                    $order = new OrderController;
                    $message = $order->orderTip($request);
                    break;
                case "handyman_order":
                    $order = new HandymanOrderController;
                    $message = $order->handymanOrderRating($request);
                    break;
            }
            return $this->successResponse($message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
    }

    public function checkPromoCode($request, $is_handyman = false)
    {
        $user = $request->user('api');
        $string_file = $this->getStringFile($request->merchant_id);
        $user_id = $user->id;
        $promo_code = $request->promo_code;
        $merchant_id = $request->merchant_id;
        $promocode = PromoCode::where([['segment_id','=',$request->segment_id],['promoCode', '=', $promo_code], ['merchant_id', '=', $merchant_id], ['promo_code_status', '=', 1]])->whereNull('deleted')->first();
        // p($promocode);
        if (empty($promocode)) {
            throw new \Exception (trans("$string_file.invalid_promo_code"));
            // return $this->failedResponse(trans("$string_file.invalid_promo_code"));
        }
        $validity = $promocode->promo_code_validity;
        $start_date = $promocode->start_date;
        $end_date = $promocode->end_date;
        $currentDate = date("Y-m-d");
        if ($validity == 2 && ($currentDate < $start_date || $currentDate > $end_date)) {
            throw new \Exception (trans("$string_file.promo_code_expired_message"));
            // return $this->failedResponse(trans("$string_file.promo_code_expired_message"));
        }
        $promo_code_limit = $promocode->promo_code_limit;
        $total_usage = Order::select('id','promo_code_id','user_id')->where([['promo_code_id', '=', $promocode->id]])
            ->whereIn('order_status',[1,6,7,9,10,11])->get();
        $all_uses = !empty($total_usage) ? $total_usage->count() : 0;
        if (!empty($all_uses)) {
            if ($all_uses >= $promo_code_limit) {
                throw new \Exception (trans("$string_file.user_limit_promo_code_expired"));
            }
            $promo_code_limit_per_user = $promocode->promo_code_limit_per_user;
            $used_by_user = $total_usage->where('user_id', $user_id)->count();
            if ($used_by_user >= $promo_code_limit_per_user) {
                throw new \Exception (trans("$string_file.user_limit_promo_code_expired"));
            }
        }
        $applicable_for = $promocode->applicable_for;
        if ($applicable_for == 2 && $user->created_at < $promocode->updated_at)
        {
            throw new \Exception (trans("$string_file.promo_code_for_new_user"));
            // return $this->failedResponse(trans("$string_file.promo_code_for_new_user"));
        }
        $order_minimum_amount = $promocode->order_minimum_amount;
        if (!empty($request->order_amount) && $request->order_amount < $order_minimum_amount) {
            $message = trans_choice("$string_file.promo_code_order_value", 3, ['AMOUNT' => $order_minimum_amount]);
            throw new \Exception ($message);
        }
        return array('status' => true, 'promo_code' => $promocode);
    }

    public function waitForAccept($booking_id, $driver_id){
        $booking = Booking::find($booking_id);
        if($booking->booking_status == 1001){
            $booking_requests = BookingRequestDriver::where([['booking_id','=',$booking_id],['inside_function','=',1]])->first();
            if(!empty($booking_requests)){
                sleep(1);
                $this->waitForAccept($booking_id, $driver_id);
            }else{
                $booking_requests = BookingRequestDriver::where([['booking_id','=',$booking_id],['request_status','=',2]])->get()->count();
                if($booking_requests == 0){
                    BookingRequestDriver::where([['booking_id','=',$booking_id],['driver_id','=',$driver_id]])->update(['inside_function' => 1]);
                }
            }
        }
    }

    public function waitOrderForAccept($order_id, $driver_id){
        $order = Order::find($order_id);
        if($order->order_status == 1){
            $booking_requests = BookingRequestDriver::where([['order_id','=',$order_id],['inside_function','=',1]])->first();
            if(!empty($booking_requests)){
                sleep(1);
                $this->waitOrderForAccept($order_id, $driver_id);
            }else{
                $booking_requests = BookingRequestDriver::where([['order_id','=',$order_id],['request_status','=',2]])
                    ->get()->count();
                if($booking_requests == 0){
                    BookingRequestDriver::where([['order_id','=',$order_id],['driver_id','=',$driver_id]])->update(['inside_function' => 1]);
                }
            }
        }
        return true;
    }

    public function getPaymentGateway(Request $request){
        try{
            $string_file = $this->getStringFile($request->merchant_id);
            $payment_gateways = PaymentOptionsConfiguration::where("merchant_id",$request->merchant_id)->get();
            $payment_gateways = $payment_gateways->map(function($item){
               return array(
                "payment_gateway_provider" => $item->payment_gateway_provider,
                "data" => array(
                    "api_secret_key" => $item->api_secret_key,
                    "api_public_key" => $item->api_public_key,
                    "auth_token" => $item->auth_token,
                    "tokenization_url" => !empty($item->tokenization_url) ? $item->tokenization_url : "",
                    "payment_redirect_url" => !empty($item->payment_redirect_url) ? $item->payment_redirect_url : "",
                    "callback_url" => !empty($item->callback_url) ? $item->callback_url : "",
                    "gateway_condition" => $item->gateway_condition,
                    "payment_step" => $item->payment_step,
                    "additional_data" => !empty($item->additional_data) ? $item->additional_data : "",
                )
               );
            });
            return $this->successResponse(trans("$string_file.success"),$payment_gateways);
        }catch (\Exception $exception){
            return $this->failedResponse($exception->getMessage());
        }
    }

    public function getSubAdmin(Request $request){
        $request_fields = [
            'public_key' => 'required',
            'secret_key' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try{
            $merchant = \App\Models\Merchant::where([['merchantPublicKey','=',$request->public_key],['merchantSecretKey','=',$request->secret_key]])->first();
            if(empty($merchant)){
                return $this->failedResponse('Account Not Found');
            }
            $string_file = $this->getStringFile($merchant->id);
            $get_subAdmin = \App\Models\Merchant::where([['parent_id','=',$merchant->id]])->get();
            $sub_admin = $get_subAdmin->map(function($item){
                return array(
                    "name" => $item->merchantFirstName.' '.$item->merchantLastName,
                    "phone" => $item->merchantPhone,
                    "email" => $item->email,
                    "address" => $item->merchantAddress,
                );
            });
            return $this->successResponse(trans("$string_file.success"),$sub_admin);
        }catch (\Exception $exception){
            return $this->failedResponse($exception->getMessage());
        }
    }

    public function getUsers(Request $request){
        $request_fields = [
            'public_key' => 'required',
            'secret_key' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try{
            $merchant = \App\Models\Merchant::where([['merchantPublicKey','=',$request->public_key],['merchantSecretKey','=',$request->secret_key]])->first();
            if(empty($merchant)){
                return $this->failedResponse('Account Not Found');
            }
            $string_file = $this->getStringFile($merchant->id);
            $get_users = \App\Models\User::where([['merchant_id','=',$merchant->id],['user_delete','=',NULL]])->get();
            $users = $get_users->map(function($item){
                $referral_discount = ReferralDiscount::where([['merchant_id','=',$item->merchant_id],['receiver_id','=',$item->id],['receiver_type','=','USER']])->first();
                $sender = '';
                $sponsor_code = '';
                $sponsor_name = '';
                if(!empty($referral_discount)){
                    $sender = $referral_discount->sender_type == 'USER' ? User::find($referral_discount->sender_id) : Driver::find($referral_discount->sender_id);
                    $sponsor_code = !empty($sender) ? ($referral_discount->sender_type == 'USER' ? $sender->ReferralCode : $sender->driver_referralcode) : '';
                    $sponsor_name = !empty($sender) ? $sender->first_name.' '.$sender->last_name : '';
                }
                return array(
                    "id" => $item->user_merchant_id,
                    "name" => $item->first_name.' '.$item->last_name,
                    "phone" => $item->UserPhone,
                    "email" => $item->email,
                    "password" => $item->password,
                    "total_trips" => !empty($item->total_trips) ? $item->total_trips : 0,
                    "wallet_balance" => !empty($item->wallet_balance) ? $item->wallet_balance : '0',
                    "ReferralCode" => $item->ReferralCode,
                    "rating" => !empty($item->rating) ? $item->rating : '0',
                    "sponsor_code" => $sponsor_code,
                    "sponsor_name" => $sponsor_name
                );
            });
            return $this->successResponse(trans("$string_file.success"),$users);
        }catch (\Exception $exception){
            return $this->failedResponse($exception->getMessage());
        }
    }

    public function getDriver(Request $request){
        $request_fields = [
            'public_key' => 'required',
            'secret_key' => 'required',
            'group' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try{
            $merchant = \App\Models\Merchant::where([['merchantPublicKey','=',$request->public_key],['merchantSecretKey','=',$request->secret_key]])->first();
            if(empty($merchant)){
                return $this->failedResponse('Account Not Found');
            }
            $string_file = $this->getStringFile($merchant->id);
            $group = $request->group;
            if($group == 1){
                $get_drivers = \App\Models\Driver::with('Booking','Order')->where([['merchant_id','=',$merchant->id],['driver_delete','=',NULL],['segment_group_id','=',$request->group]])->get();
            }else{
                $get_drivers = \App\Models\Driver::with('HandymanOrder')->where([['merchant_id','=',$merchant->id],['driver_delete','=',NULL],['segment_group_id','=',$request->group]])->get();
            }

            $drivers = $get_drivers->map(function($item) use ($group){
                $booking_arr = [];
                if($group == 1){
                    $bookings = $item->Booking;
                    if(!empty($bookings)){
                        foreach($bookings as $booking){
                            $booking_arr[] = [
                                "rider_id" => $booking->driver_id,
                                "rider_referCode" => "",
                                "transaction_id" => $booking->id,
                                "customer_name" => $booking->User->first_name.' '.$booking->User->last_name,
                                "booking_date_time" => convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone, $booking->merchant_id, null, 1),
                                "booking_amount" => $booking->final_amount_paid,
                                "tax" => '',
                                "total_booking_amount" => $booking->final_amount_paid,
                                "complete_date_time" => $booking->booking_status == 1005 ? convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone, $booking->merchant_id, null, 1) : '',
                                "cancel_date_time" => !in_array($booking->booking_status,[1005,1004,1002,1003,1001]) ? convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone, $booking->merchant_id, null, 1) : '',
                                "status" => $booking->booking_status == 1005 ? "Completed" : "Pending",
                                "ride_from" => "Delivery",
                            ];
                        }
                    }
                    $orders = $item->Order;
                    if(!empty($orders)){
                        foreach($orders as $booking){
                            $booking_arr[] = [
                                "rider_id" => $booking->driver_id,
                                "rider_referCode" => "",
                                "transaction_id" => $booking->id,
                                "customer_name" => $booking->User->first_name.' '.$booking->User->last_name,
                                "booking_date_time" => convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone, $booking->merchant_id, null, 1),
                                "booking_amount" => $booking->final_amount_paid,
                                "tax" => '',
                                "total_booking_amount" => $booking->final_amount_paid,
                                "complete_date_time" => $booking->order_status == 11 ? convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone, $booking->merchant_id, null, 1) : '',
                                "cancel_date_time" => in_array($booking->order_status,[2,3,12,5,8]) ? convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone, $booking->merchant_id, null, 1) : '',
                                "status" => $booking->is_order_completed == 1 ? "Completed" : "Pending",
                                "ride_from" => "Grocery",
                            ];
                        }
                    }
                }else{
                    $bookings = $item->HandymanOrder;
                    foreach($bookings as $booking){
                        $booking_arr[] = [
                            "rider_id" => $booking->driver_id,
                            "rider_referCode" => "",
                            "transaction_id" => $booking->id,
                            "customer_name" => $booking->User->first_name.' '.$booking->User->last_name,
                            "booking_date_time" => convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone, $booking->merchant_id, null, 1),
                            "booking_amount" => $booking->final_amount_paid,
                            "tax" => '',
                            "total_booking_amount" => $booking->final_amount_paid,
                            "complete_date_time" => $booking->order_status == 7 ? convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone, $booking->merchant_id, null, 1) : '',
                            "cancel_date_time" => $booking->order_status == 3 ? convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone, $booking->merchant_id, null, 1) : '',
                            "status" => $booking->is_order_completed == 1 ? "Completed" : "Pending",
                            "ride_from" => "Handyman",
                        ];
                    }
                }

                return array(
                    "id" => $item->id,
                    "name" => $item->first_name.' '.$item->last_name,
                    "phone" => $item->phoneNumber,
                    "email" => $item->email,
                    "total_trips" => !empty($item->total_trips) ? $item->total_trips : 0,
                    "wallet_money" => !empty($item->wallet_money) ? $item->wallet_money : '0',
                    "total_earnings" => !empty($item->total_earnings) ? $item->total_earnings : '0',
                    "ReferralCode" => $item->driver_referralcode,
                    "rating" => !empty($item->rating) ? $item->rating : '0',
                    "last_location_update_time" => !empty($item->last_location_update_time) ? $item->last_location_update_time : NULL,
                    "booking_data" => $booking_arr
                );
            });
            return $this->successResponse(trans("$string_file.success"),$drivers);
        }catch (\Exception $exception){
            return $this->failedResponse($exception->getMessage());
        }
    }
    
    public function storeList(Request $request){
        $request_fields = [
            'public_key' => 'required',
            'secret_key' => 'required',
            // 'group' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        
        try{
            $merchant = \App\Models\Merchant::where([['merchantPublicKey','=',$request->public_key],['merchantSecretKey','=',$request->secret_key]])->first();
            if(empty($merchant)){
                return $this->failedResponse('Account Not Found');
            }
            $string_file = $this->getStringFile($merchant->id);
            
            $business_segment = BusinessSegment::whereHas('Segment',function($q){
                $q->where('slag','GROCERY');
            })
            ->where([['merchant_id', '=', $merchant->id]])
            ->orderBy('created_at','DESC')->get();
            $stores = $business_segment->map(function ($item){
                return array(
                    'id' => $item->id,
                    'full_name' => $item->full_name,
                    'contact_number' => $item->phone_number,
                    'email' => $item->email,
                    'address' => $item->address,
                );
            });
            return $this->successResponse(trans("$string_file.success"),$stores);
        }catch(\Exception $exception){
            return $this->failedResponse($exception->getMessage());
        }
    }
    
    public function getStoreBookings(Request $request){
        $request_fields = [
            'public_key' => 'required',
            'secret_key' => 'required',
            'store_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try{
            $merchant = \App\Models\Merchant::where([['merchantPublicKey','=',$request->public_key],['merchantSecretKey','=',$request->secret_key]])->first();
            if(empty($merchant)){
                return $this->failedResponse('Account Not Found');
            }
            $string_file = $this->getStringFile($merchant->id);
            $segment = Segment::where('slag','GROCERY')->first();
            $request->request->add(['merchant_id'=>$merchant->id,'segment_id'=>$segment->id,'business_segment_id'=>$request->store_id]);
            $order = new Order;
            $all_orders = $order->getOrders($request,false);
            $all_orders = $all_orders->map(function ($item) use($string_file){
                $currency = $item->CountryArea->Country->isoCode;
                $tax_amount =    !empty($item->tax) ? $item->tax : 0;
                return array(
                    'store_id' => $item->business_segment_id,
                    'transaction_id' => $item->merchant_order_id,
                    'customer_name' => $item->User->UserName,
                    'booking_date_time' => convertTimeToUSERzone($item->created_at, $item->CountryArea->timezone, null, $item->Merchant),
                    'subtotal' => $currency.' '.$item->cart_amount,
                    'tax' => $currency.' '.$item->$tax_amount,
                    'delivery_charge' => $currency.' '.$item->delivery_amount,
                    'total_amount' => $currency.' '.$item->final_amount_paid,
                    'complete_date_time' => $item->is_order_completed == 1 ? convertTimeToUSERzone($item->updated_at, $item->CountryArea->timezone, null, $item->Merchant) : '',
                    'cancel_date_time' => $item->is_order_completed != 1 ? convertTimeToUSERzone($item->updated_at, $item->CountryArea->timezone, null, $item->Merchant) : '',
                    'status' => $item->order_status == 11 ? trans("$string_file.complete") : trans("$string_file.pending").' / '.trans("$string_file.cancel"),
                );
            });
            return $this->successResponse(trans("$string_file.success"),$all_orders);
        }catch(\Exception $exception){
            return $this->failedResponse($exception->getMessage());
        }
        
    }
    
    // calling for driver and user before register
    public function faceRecognition(Request $request){
        $merchant_id = $request->merchant_id;
        $merchant = merchantModel::find($merchant_id);
        $string_file = $this->getStringFile($merchant_id);
        $request_fields = [
            'profile_image' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try{
            if(isset($merchant->Configuration->face_recognition_feature) && $merchant->Configuration->face_recognition_feature == 1){
                $faceRecognition = new FaceRecognition($merchant->Configuration->face_recognition_end_point, $merchant->Configuration->face_recognition_subscription_key);
                $face_recognition_id = $faceRecognition->detect_face_binary($request->profile_image, $string_file);
                if(!empty($face_recognition_id)){
                    return $this->successResponse(trans("$string_file.success"), []);
                }else{
                    return $this->failedResponse(trans("$string_file.invalid_image_captured"));
                }
            }else{
                return $this->successResponse(trans("$string_file.success"), []);
            }
        }catch (\Exception $exception){
            return $this->failedResponse($exception->getMessage());
        }
    }
    
    public function getRecordsForExternal(Request $request){
        $request_fields = [
            'start_date' => 'required',
            'end_date' => 'required',
            'public_key' => 'required|exists:merchants,merchantPublicKey',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $merchant = merchantModel::where(["merchantPublicKey" => $request->public_key])->first();
        if(!empty($merchant)){
            $return_data = [];
            $string_file = $this->getStringFile(null, $merchant);
            $bookings = Booking::with("BookingRating","BookingDetail","DriverVehicle")->where(["merchant_id" => $merchant->id, "booking_status" => 1005, "booking_closure" => 1])->whereBetween("created_at",[$request->start_date, $request->end_date])->get();
            foreach($bookings as $booking){
                $booking_detail = $booking->BookingDetail;
                $driver_license = $booking->Driver->DriverDocument->where("document_id", 444)->first();
                array_push($return_data, array(
                    "trip_id" => $booking->merchant_booking_id,
                    "start_coordinates" => $booking_detail->start_latitude.",".$booking_detail->start_longitude,
                    "end_coordinates" => $booking_detail->end_latitude.",".$booking_detail->end_longitude,
                    "start_time" => date("Y-m-d H:i:s",$booking_detail->start_timestamp),
                    "end_time" => date("Y-m-d H:i:s",$booking_detail->end_timestamp),
                    "total_fare_amount" => $booking->final_amount_paid,
                    "trip_distance" => trim(str_replace("Km","",$booking->travel_distance)) * 1000,
                    "rating" => (string)$booking->BookingRating->user_rating_points,
                    "driver_earning" => $booking->driver_cut,
                    "driver_license_no" => !empty($driver_license) ? $driver_license->document_number : "",
                    "vehicle_registration_license_no" => $booking->DriverVehicle->vehicle_number,
                ));
            }
            return $this->successResponse("Success", $return_data);
        }else{
            return $this->failedResponse("Invalid Merchant");
        }
    }
}
