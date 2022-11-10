<?php

namespace App\Http\Controllers\Helper;


use App\Models\ApplicationConfiguration;
use App\Models\BookingConfiguration;
use App\Models\Configuration;
use App\Models\DriverCommissionChoice;
use App\Models\Onesignal;
use App\Models\ServiceType;
use App\Models\DeliveryType;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;

class Merchant
{
    public function ServicesType($merchant)
    {
        $serviceTypes = ServiceType::get();
        foreach ($serviceTypes as $value) {
            $value->color = $value->ServiceTypeConfiguratoin($merchant->id) ? $value->ServiceTypeConfiguratoin($merchant->id)->colour : "#000000";
            $value->icon = $value->ServiceTypeConfiguratoin($merchant->id) ? $value->ServiceTypeConfiguratoin($merchant->id)->icon : "";
            $value->serviceName = $value->ServiceApplication($merchant->id) ? $value->ServiceApplication($merchant->id) : $value->serviceName;
        }
        return $serviceTypes;
    }

    public function DriverCommissionChoices($merchant)
    {
        $commission_data = [];
        $all_commissions = DriverCommissionChoice::where([['status', true], ['admin_delete', 0]])->get();
        if ($all_commissions->isNotEmpty()):
            $lang_data = collect();
            $commission_data = $all_commissions->map(function ($item, $key) use (&$merchant, &$lang_data) {
                $item->lang_data = $item->getNameAccMerchantApiAttribute($merchant->id);
                return $item->only('id', 'lang_data');
            });
        endif;
        return $commission_data;
    }

    public function TripCalculation($amount, $merchant_id = NULL,$trip_calculation_method = NULL)
    {
        // p($trip_calculation_method);
        if(empty($trip_calculation_method))
        {
            $settings = Configuration::select('trip_calculation_method')->where([['merchant_id', '=', $merchant_id]])->first();
            $trip_calculation_method = $settings->trip_calculation_method;
        }

        switch ($trip_calculation_method) {
            case "1":
                $amount = (string)round($amount);
                break;
            case "2":
                $amount = sprintf("%.2f", $amount);
                break;
            case "3":
                $amount = number_format(round($amount), 2, ".", '');
                break;
            case "4":
                $amount = sprintf('%.3f', $amount);
                break;
            default:
                $amount = sprintf("%.2f", $amount);
        }
        return $amount;
    }

    public function FinalAmountCal($amount, $merchant_id)
    {
        $settings = BookingConfiguration::select('final_amount_to_be_shown')->where([['merchant_id', '=', $merchant_id]])->first();
        switch ($settings->final_amount_to_be_shown) {
            case "1":
                $amount = ceil($amount / 100) * 100;
                break;
            case "2":
                $amount = sprintf('%.2f', round($amount, 1));
                break;
            case "3":
                $amount = ceil($amount / 50) * 50;
                break;
            case "4":
                $amount = round($amount);
                break;
            case "5":
                $amount = ceil((floor($amount / 50) * 50) / 100) * 100;
                break;
            case "6":
                $amount = sprintf('%.2f', $amount);
                break;
            case "7":
                $amount = sprintf('%.3f', $amount);
                break;
            case "8":
                $amount = round($amount/1000) * 1000;                //nearest 1000
                break;
            default:
                $amount = $amount;
        }
        return $amount;

    }

   public function CountryList($merchant,$permission_area_ids = [],$country_id = NULL)
    {
        $queries = Country::select('id', 'country_code','phonecode','maxNumPhone','minNumPhone','isoCode')->where([['merchant_id','=', $merchant->id],['country_status','=',1]])
            ->orderBy('sequance');
            if(!empty($permission_area_ids))
            {
                $queries->whereHas('CountryArea',function($q) use ($permission_area_ids){
                    $q->whereIn("id",$permission_area_ids);
                });
            }
            if(!empty($country_id))
            {
                $queries->where('id',$country_id);
            }
          $countries =   $queries->get();
        if (!empty($countries->toArray())) {
            foreach ($countries as $key => $country) {
                $country->name = $country->CountryName;
                $country->currency = $country->CurrencyName;
            }
        }
        return $countries;
    }

    public function CountrywithAreaList($merchant, $country_id = NULL)
    {
        $countries = Country::select('id', 'country_code','isoCode','maxNumPhone','minNumPhone','transaction_code')->where([['merchant_id','=', $merchant->id],['country_status','=',1]])
            ->with(['CountryArea' => function ($q) {
                $q->select('id', 'merchant_id', 'country_id', 'is_geofence', 'auto_upgradetion', 'timezone', 'minimum_wallet_amount', 'pool_postion', 'status', 'driver_earning_duration', 'manual_toll_price', 'created_at', 'updated_at')
                    ->where('is_geofence', 2)->where('status', 1);
            }])
            ->whereHas('CountryArea', function ($q) {
                $q->where('is_geofence', 2)->where('status', 1);
            })
            ->where(function ($q) use ($country_id) {
                if (!empty($country_id)) {
                    $q->where('id', $country_id);
                }
            })
            ->orderBy('sequance')
            ->get();
        if (!empty($countries->toArray())) {
            foreach ($countries as $key => $country) {
                $country->CountryArea->transform(function ($item, $key) {
                    $item->AreaName = $item->CountryAreaName;
                    return $item;
                })->sortBy('AreaName')->values();

                $country->name = $country->CountryName;
                $country->distance_unit = (int)$country->distance_unit;
            }
        }
        return $countries;
    }

    public function StoreGenralConfig($merchant_id, $data)
    {
        $social_option = $data->social_option;
        if (!$data->social_option) {
            $social_option = [];
        }
        $fb = 2;
        $google = 2;
        if (in_array(1, $social_option)) {
            $fb = 1;
        }
        if (in_array(2, $social_option)) {
            $google = 1;
        }
        $userWallet = array();
        $driverWallet = array();
        $config = Configuration::updateOrCreate(
            ['merchant_id' => $merchant_id],
            [
                'social_signup' => $data->social,
                'drop_outside_area' => $data->drop_outside_area,
                'driver_area_notification' => $data->driver_area_notification,
                'add_multiple_vehicle' => $data->add_multiple_vehicle,
                'homescreen_eta' => $data->homescreen_eta,
                'homescreen_estimate_fare' => $data->homescreen_estimate_fare,
                'subscription_package' => $data->subscription_package,
                'default_config' => $data->default_config,
                'gender' => $data->gender,
                'facebook' => $fb,
                'google' => $google,
                'driver_wallet_status' => 2,
                'report_issue_email' => "",
                'report_issue_phone' => "",
                'location_update_timeband' => 0,
                'android_user_maintenance_mode' => 0,
                'android_user_version' => 0,
                'android_user_mandatory_update' => 0,
                'ios_user_maintenance_mode' => 0,
                'ios_user_version' => 0,
                'ios_user_mandatory_update' => 0,
                'android_driver_maintenance_mode' => 0,
                'android_driver_version' => 0,
                'android_driver_mandatory_update' => 0,
                'ios_driver_maintenance_mode' => 0,
                'ios_driver_version' => 0,
                'ios_driver_mandatory_update' => 0,
                'user_wallet_amount' => json_encode($userWallet, true),
                'driver_wallet_amount' => json_encode($driverWallet, true),
                'corporate_admin' => $data->corporate_admin,
                'toll_api' => $data->toll_api,
                'toll_key' => $data->toll_key,
                'no_of_person' => $data->no_of_person,
                'no_of_children' => $data->no_of_children,
                'no_of_bags' => $data->no_of_bags,
                'no_of_pool_seats' => $data->no_of_pool_seats,
                'online_transaction_code' => $data->online_transaction_code,
                'family_member_enable' => $data->family_member_enable,
                'trip_calculation_method' => $data->trip_calculation_method,
            ]
        );
        return $config;
    }

    public function StoreDefaultGenralConfig($merchant_id, $data)
    {
        $config = Configuration::updateOrCreate(
            ['merchant_id' => $merchant_id],
            [
                'social_signup' => $data->social_signup,
                'drop_outside_area' => $data->drop_outside_area,
                'driver_area_notification' => $data->driver_area_notification,
                'homescreen_eta' => $data->homescreen_eta,
                'homescreen_estimate_fare' => $data->homescreen_estimate_fare,
                'subscription_package' => $data->subscription_package,
                'facebook' => $data->facebook,
                'google' => $data->google,
                'driver_wallet_status' => $data->driver_wallet_status,
                'report_issue_email' => $data->report_issue_email,
                'report_issue_phone' => $data->report_issue_phone,
                'location_update_timeband' => $data->location_update_timeband,
                'android_user_maintenance_mode' => $data->android_user_maintenance_mode,
                'android_user_version' => $data->android_user_version,
                'android_user_mandatory_update' => $data->android_user_mandatory_update,
                'ios_user_maintenance_mode' => $data->ios_user_maintenance_mode,
                'ios_user_version' => $data->ios_user_version,
                'ios_user_mandatory_update' => $data->ios_user_mandatory_update,
                'android_driver_maintenance_mode' => $data->android_driver_maintenance_mode,
                'android_driver_version' => $data->android_driver_version,
                'android_driver_mandatory_update' => $data->android_driver_mandatory_update,
                'ios_driver_maintenance_mode' => $data->ios_driver_maintenance_mode,
                'ios_driver_version' => $data->ios_driver_version,
                'ios_driver_mandatory_update' => $data->ios_driver_mandatory_update,
                'user_wallet_amount' => $data->user_wallet_amount,
                'driver_wallet_amount' => $data->driver_wallet_amount,
                'corporate_admin' => $data->corporate_admin,
                'toll_api' => $data->toll_api,
                'toll_key' => $data->toll_key,
                'email_functionality' => $data->email_functionality,
                'sms_gateway' => $data->sms_gateway,
                'vehicle_ac_enable' => $data->vehicle_ac_enable,
                'driver_limit' => $data->driver_limit,
                'driver_cash_limit' => $data->driver_cash_limit,
                'outside_area_ratecard' => $data->outside_area_ratecard,
                'bank_details_enable' => $data->bank_details_enable,
                'existing_vehicle_enable' => $data->existing_vehicle_enable,
                'no_of_person' => $data->no_of_person,
                'no_of_children' => $data->no_of_children,
                'no_of_bags' => $data->no_of_bags,
                'no_of_pool_seats' => $data->no_of_pool_seats,
                'online_transaction_code' => $data->online_transaction_code,
                'family_member_enable' => $data->family_member_enable,
                'trip_calculation_method' => $data->trip_calculation_method,
            ]
        );
        return $config;
    }

    public function storeOnesignal($merchant_id, $data)
    {
        $oneSIgnal = Onesignal::updateOrCreate(
            ['merchant_id' => $merchant_id],
            [
                'user_application_key' => $data->user_application_key,
                'user_rest_key' => $data->user_rest_key,
                'driver_application_key' => $data->driver_application_key,
                'driver_rest_key' => $data->driver_rest_key,
            ]
        );
        return $oneSIgnal;
    }

    public function ApplicationConfig($merchant_id, $data)
    {
        $appconfig = ApplicationConfiguration::updateOrCreate(
            ['merchant_id' => $merchant_id],
            [
                'vehicle_owner' => $data->vehicle_owner,
                'home_screen_view' => $data->home_screen_view,
                'user_default_language' => $data->user_default_language,
                'driver_default_language' => $data->driver_default_language,
                'demo' => $data->demo,
                'favourite_driver_module' => $data->favourite_driver_module,
                'user_email' => $data->user_email,
                'driver_email' => $data->driver_email,
                'super_driver_limit' => $data->super_driver_limit,
                'enable_super_driver' => $data->enable_super_driver,
                'tip_status' => $data->tip_status,
                'sub_charge' => $data->sub_charge,
                'user_document' => $data->user_document,
                'user_phone' => $data->user_phone,
                'driver_phone' => $data->driver_phone,
                'user_email_otp' => $data->user_email_otp,
                'user_phone_otp' => $data->user_phone_otp,
                'driver_email_otp' => $data->driver_email_otp,
                'driver_phone_otp' => $data->driver_phone_otp,
                'vehicle_rating_enable' => $data->vehicle_rating_enable,
                'user_login' => $data->user_login,
                'user_email_otp_while_phone' => $data->user_email_otp_while_phone,
                'driver_login' => $data->driver_login,
                'driver_email_otp_while_phone' => $data->driver_email_otp_while_phone,
                'smoker' => $data->smoker,
                'gender' => $data->gender,
                'security_question' => $data->security_question,
                'pickup_color' => $data->pickup_color,
                'dropoff_color' => $data->dropoff_color,
                'time_charges' => $data->time_charges,
                'default_config' => $data->default_config,
                'userImage_enable' => $data->userImage_enable,
                'driver_rating_enable' => $data->driver_rating_enable,
                'reward_points' => $data->reward_points
            ]
        );
        return $appconfig;
    }

    public function DefaultApplicationConfig($merchant_id, $data)
    {
        $appconfig = ApplicationConfiguration::updateOrCreate(
            ['merchant_id' => $merchant_id],
            [
                'vehicle_owner' => $data->vehicle_owner,
                'home_screen_view' => $data->home_screen_view,
                'user_default_language' => $data->user_default_language,
                'driver_default_language' => $data->driver_default_language,
                'demo' => $data->demo,
                'favourite_driver_module' => $data->favourite_driver_module,
                'user_email' => $data->user_email,
                'driver_email' => $data->driver_email,
                'super_driver_limit' => $data->super_driver_limit,
                'enable_super_driver' => $data->enable_super_driver,
                'tip_status' => $data->tip_status,
                'sub_charge' => $data->sub_charge,
                'user_document' => $data->user_document,
                'user_phone' => $data->user_phone,
                'driver_phone' => $data->driver_phone,
                'user_email_otp' => $data->user_email_otp,
                'user_phone_otp' => $data->user_phone_otp,
                'driver_email_otp' => $data->driver_email_otp,
                'driver_phone_otp' => $data->driver_phone_otp,
                'vehicle_rating_enable' => $data->vehicle_rating_enable,
                'user_login' => $data->user_login,
                'user_email_otp_while_phone' => $data->user_email_otp_while_phone,
                'driver_login' => $data->driver_login,
                'driver_email_otp_while_phone' => $data->driver_email_otp_while_phone,
                'smoker' => $data->smoker,
                'gender' => $data->gender,
                'security_question' => $data->security_question,
                'pickup_color' => $data->pickup_color,
                'dropoff_color' => $data->dropoff_color,
                'time_charges' => $data->time_charges,
                'driver_rating_enable' => $data->driver_rating_enable,
            ]
        );
        return $appconfig;
    }

    public function BookingConfig($merchant_id, $data)
    {
        $appconfig = BookingConfiguration::updateOrCreate(
            ['merchant_id' => $merchant_id],
            [
                'google_key' => "",
                'additional_note' => $data->additional_note,
                'home_address_enable' => $data->home_address_enable,
                'auto_accept_mode' => $data->auto_accept_mode,
                'static_map' => $data->static_map,
                'ride_otp' => $data->ride_otp,
                'otp_manual_dispatch' => $data->otp_manual_dispatch,
                'chat' => $data->chat,
                'change_payment_method' => $data->change_payment_method,
                'polyline' => $data->polyline,
                'driver_manual_dispatch' => $data->driver_manual_dispatch,
                'multi_destination' => $data->multiple_destination,
                'count_multi_destination' => $data->max_multiple_destination,
                'driver_request_timeout' => $data->driver_request_timeout,
                'driver_ride_distance' => $data->driver_ride_distance,
                'multiple_rides' => $data->multiple_ride,
                'default_config' => $data->default_config,
                'autocomplete_start' => $data->autocomplete_start,
                'outstation_notification_popup' => $data->outstation_notification_popup,
                'baby_seat_enable' => $data->baby_seat_enable,
                'wheel_chair_enable' => $data->wheel_chair_enable,
                'ride_later_payment_types_enable' => $data->ride_later_payment_types_enable

            ]
        );
        return $appconfig;
    }

    public function DefaultBookingConfig($merchant_id, $data)
    {
        $booking_config = BookingConfiguration::updateOrCreate(
            ['merchant_id' => $merchant_id],
            [
                'google_key' => "",
                'additional_note' => $data->additional_note,
                'static_map' => $data->static_map,
                'ride_otp' => $data->ride_otp,
                'otp_manual_dispatch' => $data->otp_manual_dispatch,
                'chat' => $data->chat,
                'polyline' => $data->polyline,
                'change_payment_method' => $data->change_payment_method,
                'driver_manual_dispatch' => $data->driver_manual_dispatch,
                'multi_destination' => $data->multiple_destination,
                'count_multi_destination' => $data->max_multiple_destination,
                'driver_request_timeout' => $data->driver_request_timeout,
                'tracking_screen_refresh_timeband' => $data->tracking_screen_refresh_timeband,
                'normal_ride_now_radius' => $data->normal_ride_now_radius,
                'normal_ride_now_request_driver' => $data->normal_ride_now_request_driver,
                'normal_ride_now_drop_location' => $data->normal_ride_now_drop_location,
                'normal_ride_later_request_type' => $data->normal_ride_later_request_type,
                'normal_ride_later_radius' => $data->normal_ride_later_radius,
                'normal_ride_later_request_driver' => $data->normal_ride_later_request_driver,
                'normal_ride_later_booking_hours' => $data->normal_ride_later_booking_hours,
                'normal_ride_later_drop_location' => $data->normal_ride_later_drop_location,
                'normal_ride_later_time_before' => $data->normal_ride_later_time_before,
                'rental_ride_now_radius' => $data->rental_ride_now_radius,
                'rental_ride_now_request_driver' => $data->rental_ride_now_request_driver,
                'rental_ride_now_drop_location' => $data->rental_ride_now_drop_location,
                'rental_ride_later_request_type' => $data->rental_ride_later_request_type,
                'rental_ride_later_radius' => $data->rental_ride_later_radius,
                'rental_ride_later_request_driver' => $data->rental_ride_later_request_driver,
                'rental_ride_later_booking_hours' => $data->rental_ride_later_booking_hours,
                'rental_ride_later_drop_location' => $data->rental_ride_later_drop_location,
                'rental_ride_later_time_before' => $data->rental_ride_later_time_before,
                'transfer_ride_now_radius' => $data->transfer_ride_now_radius,
                'transfer_ride_now_request_driver' => $data->transfer_ride_now_request_driver,
                'transfer_ride_now_drop_location' => $data->transfer_ride_now_drop_location,
                'transfer_ride_later_request_type' => $data->transfer_ride_later_request_type,
                'transfer_ride_later_radius' => $data->transfer_ride_later_radius,
                'transfer_ride_later_request_driver' => $data->transfer_ride_later_request_driver,
                'transfer_ride_later_booking_hours' => $data->transfer_ride_later_booking_hours,
                'transfer_ride_later_drop_location' => $data->transfer_ride_later_drop_location,
                'transfer_ride_later_time_before' => $data->transfer_ride_later_time_before,
                'pool_radius' => $data->pool_radius,
                'pool_drop_radius' => $data->pool_drop_radius,
                'pool_now_request_driver' => $data->pool_now_request_driver,
                'pool_maximum_exceed' => $data->pool_maximum_exceed,
                'outstation_request_type' => $data->outstation_request_type,
                'outstation_radius' => $data->outstation_radius,
                'outstation_request_driver' => $data->outstation_request_driver,
                'outstation_booking_hours' => $data->outstation_booking_hours,
                'outstation_time_before' => $data->outstation_time_before,
                'slide_button' => $data->slide_button,
                'drop_location_request' => $data->drop_location_request,
                'estimate_fare_request' => $data->estimate_fare_request,
                'number_of_driver_user_map' => $data->number_of_driver_user_map,
                'booking_eta' => $data->booking_eta,
                'driver_ride_distance' => $data->driver_ride_distance,
                'final_amount_cal_method' => $data->final_amount_cal_method,
                'outstation_ride_now_enabled' => $data->outstation_ride_now_enabled,
                'outstation_ride_now_radius' => $data->outstation_ride_now_radius,
                'outstaion_ride_now_request_driver' => $data->outstaion_ride_now_request_driver,
                'multiple_rides' => $data->multiple_ride,
                'baby_seat_enable' => $data->baby_seat_enable,
                'wheel_chair_enable' => $data->wheel_chair_enable,
                'ride_later_payment_types_enable' => $data->ride_later_payment_types_enable
            ]
        );
        return $booking_config;
    }
// code merged by @Amba
// Code of delivery booking
// Code updated by @Bhuvanesh
    public function DeliveryTypes($merchant)
    {
        $serviceTypes = DeliveryType::where('merchant_id', $merchant->id)->get();
        if (!empty($serviceTypes)) {
            foreach ($serviceTypes as $value) {
                $value->color = "#000000";
                $value->icon = "";
                $value->name = $value->name;
            }
        }
        return $serviceTypes;
    }

    public static function MerchantSegments($type = 1)
    {
        $merchant_id = get_merchant_id();
        $merchant = \App\Models\Merchant::find($merchant_id);
        if ($type == 1) {
            $segment_array = $merchant->Segment->pluck('slag')->toArray();
        } else {
            $segment_array = $merchant->Segment->transform(function ($segment) {
                return $segment->SegmentGroup;
            })->unique('group_name')->pluck('id')->toArray();
        }
//        p($segment_array);
        return $segment_array;
    }
}
