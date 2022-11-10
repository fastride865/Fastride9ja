<?php

namespace App\Http\Resources;

use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\CommonController;
use App\Models\AdvertisementBanner;
use App\Models\MerchantFarePolicy;
use App\Models\PaymentOptionsConfiguration;
use App\Models\SmsConfiguration;
use App\Traits\MerchantTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\Helper\PolygenController;
use App\Models\CountryArea;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Helper\Merchant;
use App\Models\PaymentOption;

class UserConfiguration extends JsonResource
{
    use MerchantTrait;
    public function toArray($data)
    {
        $request_data = $data->all();
//        $newBookingData = new BookingDataController();
        $string_file = $this->getStringFile(NULL,$this);
        if (request()->device == 1) {
            $main_show_dialog = $this->Configuration->android_user_maintenance_mode == 1 ? true : false;
            $main_show_message = $this->Configuration->android_user_maintenance_mode == 1 ? trans('api.message56') : "";
            $version_show_dialog = $this->Configuration->android_user_version > request()->apk_version ? true : false;
            $version_mandatory = $this->Configuration->android_user_mandatory_update == 1 ? true : false;
            $version_dialog_message = $version_show_dialog == true ? trans("$string_file.app_update_warning") : '';
        } else {
            $main_show_dialog = $this->Configuration->ios_user_maintenance_mode == 1 ? true : false;
            $main_show_message = $this->Configuration->ios_user_maintenance_mode == 1 ? trans('api.message56') : "";
            $version_show_dialog = $this->Configuration->ios_user_version > request()->apk_version ? true : false;
            $version_mandatory = $this->Configuration->ios_user_mandatory_update == 1 ? true : false;
            $version_dialog_message = $version_show_dialog == true ? trans("$string_file.app_update_warning") : '';
        }
        $merchant_obj = new Merchant;
        $countries = $merchant_obj->CountryList($this);
        //  $Question = $this->Question->toArray();
        if ($this->ApplicationConfiguration->security_question == 1) {
            $questions = $this->Question;
            foreach ($questions as $value) {
                $value->question = $value->Questionss;
            }
        }

        $no_of_pool_seats = $this->Configuration->no_of_pool_seats;
        $pool_seats = array();
        for ($i = 1; $i <= $no_of_pool_seats; $i++) {
            $pool_seats[]['name'] = $i . " Seat";
        }
        $additional_info = false;
        if ($this->ApplicationConfiguration->gender == 1 || $this->Configuration->family_member_enable == 1 || $this->BookingConfiguration->wheel_chair_enable == 1 || $this->Configuration->no_of_person == 1 || $this->Configuration->no_of_children == 1 || $this->Configuration->no_of_bags == 1 || $this->Configuration->vehicle_ac_enable == 1) {
            $additional_info = true;
        }


        $arr_nevigation = [];
        if (count($this->NavigationDrawer) > 0) {
            foreach ($this->NavigationDrawer as $nevigation) {
                $check_status = true;
                //check wallet user enabled from merchant
                if ($nevigation['slug'] == 'wallet-activity' && $this->Configuration->user_wallet_status != 1) {
                    $check_status = false;
                }
                //check favourite driver enabled from merchant
                if ($nevigation['slug'] == 'favourite-driver' && $this->ApplicationConfiguration->favourite_driver_module != 1) {
                    $check_status = false;
                }
                //check SOS for user/driver enabled from merchant
                if ($nevigation['slug'] == 'emergency-contacts' && $this->ApplicationConfiguration->sos_user_driver != 1) {
                    $check_status = false;
                }
                if ($check_status == true) {
                    // check images from merchant if not found then search in super admin
                    $image = $nevigation->image;
//                        !empty($nevigation->image) ? get_image($nevigation->image, 'drawericons', $request_data['merchant_id'], true) :
//                        get_image($nevigation->AppNavigationDrawer->image, 'drawer_icon', null, false);
                    $url = !empty($nevigation->additional_data) ? $nevigation->additional_data : "";
                    $arr_nevigation[] = array(
                        'id' => $nevigation['id'],
                        'image' => $image,
                        'sequence' => $nevigation['sequence'],
                        'name' => $nevigation['name'],
                        'url' => $url,
                        'slug' => $nevigation['slug'],
                        'text_colour' => $nevigation['text_colour'],
                        'text_style' => $nevigation['text_style'],
                    );
                }
            }
        }
        $banners = [];
//        if (isset($this->advertisement_module) && $this->advertisement_module == 1) {
//            $banner_for = explode(',', $this->advertisement_banner);
//            if (in_array(1, $banner_for)) {
//                $current_date = date('Y-m-d');
//                $add_banners = AdvertisementBanner::where([['merchant_id', '=', $this->id], ['status', '=', 1], ['activate_date', '<=', $current_date], ['is_deleted', '=', null]])->whereIn('banner_for', [1, 4])->get();
//                if (!empty($add_banners)) {
//                    foreach ($add_banners as $add_banner) {
//                        $banner_button = false;
//                        $banner_redirect_url = "";
//                        if ($add_banner->redirect_url != '') {
//                            $banner_button = true;
//                            $banner_redirect_url = $add_banner->redirect_url;
//                        }
//                        if ($add_banner->validity == 1) {
//                            array_push($banners, array(
//                                'banner_image' => get_image($add_banner->image, 'banners', $this->id),
//                                'banner_button' => $banner_button,
//                                'banner_redirect_url' => $banner_redirect_url
//                            ));
//                        } elseif ($add_banner->validity == 2 && $add_banner->expire_date >= $current_date) {
//                            array_push($banners, array(
//                                'banner_image' => get_image($add_banner->image, 'banners', $this->id),
//                                'banner_button' => $banner_button,
//                                'banner_redirect_url' => $banner_redirect_url
//                            ));
//                        }
//                    }
//                }
//            }
//        }
        $otp_enable = false;
        if (isset($this->Configuration->user_login_with_otp) && $this->Configuration->user_login_with_otp == 1) {
            $SmsConfiguration = SmsConfiguration::where([['merchant_id', '=', $this->id]])->first();
            if (!empty($SmsConfiguration) || $this->ApplicationConfiguration->otp_from_firebase == 1) {
                $otp_enable = true;
            }
        }
        $user_signup_card_store_enable = false;
        if (isset($this->Configuration->user_signup_card_store_enable) && $this->Configuration->user_signup_card_store_enable == 1) {
            $user_signup_card_store_enable = true;
        }

        $fare_policy_text = '';
        $fare_policy = MerchantFarePolicy::select('fare_policy')->where([['merchant_id', '=', $this->id], ['locale', '=', App::getLocale()]])->first();
        if (isset($fare_policy->fare_policy)) {
            $fare_policy_text = $fare_policy->fare_policy;
        } else {
            $fare_policy = MerchantFarePolicy::select('fare_policy')->where([['merchant_id', '=', $this->id], ['locale', '=', 'en']])->first();
            if (isset($fare_policy->fare_policy)) {
                $fare_policy_text = $fare_policy->fare_policy;
            }
        }
        $arr_payment_option = CommonController::filteredPaymentOptions($this->PaymentOption, $this->id);
//        foreach ($arr_payment_option as $option)
//        {
//            if($option['slug'] == "OZOH")
//            {
//                $arr_details =  json_decode($option['params'],true);
//                $arr_details['payment_redirect_url'] = route('api.ozo-payment-success');
//                $arr_details['callback_url'] = route('api.ozo-payment-notification');
//                $updated_details = json_encode($arr_details);
//                $option['params'] = $updated_details;
//            }
//            elseif($option['slug'] == "MaxiCash")
//            {
//                $arr_details =  json_decode($option['params'],true);
//                $arr_details['success_url'] = route('api.maxi-cash-success');
//                $arr_details['cancel_url'] = route('api.maxi-cash-cancel');
//                $arr_details['failure_url'] = route('api.maxi-cash-failure');
//                $arr_details['notify_url'] = route('api.maxi-cash-notification');
//                $updated_details = json_encode($arr_details);
//                $option['params'] = $updated_details;
//            }
//        }
        $card_option = PaymentOption::whereHas('PaymentOptionsConfiguration',function($q){
            $q->where('merchant_id',$this->id);
        })->get()->pluck('slug')->toArray();
        $add_card_option = !empty($card_option) ? $card_option[0] : "";

        $handyman_apply_promocode = $this->merchantHandymanPromocode($this->id);

        $only_cash = false;
        $id = NULL;
        $name = "";
        if($this->PaymentMethod->count() ==1 && in_array(1, array_pluck($this->PaymentMethod, 'id')))
        {
          $only_cash = true;
          $id = 1;
          $paymentMethod = isset($this->PaymentMethod[0]) ? $this->PaymentMethod[0] : NULL;
          $name = $paymentMethod->MethodName($this->id) ? $paymentMethod->MethodName($this->id) : $paymentMethod->payment_method;
        }

        $payment_method_list = [];
        foreach ($this->PaymentMethod as $paymentMethod)
        {
          $payment_method_list[] = [
              'id'=>$paymentMethod->id,
              'name'=>$paymentMethod->MethodName($this->id) ? $paymentMethod->MethodName($this->id) : $paymentMethod->payment_method,
              'slug'=>!empty($paymentMethod->slug) ? $paymentMethod->slug : "",
          ];
        }
        $face_recognition_for_user_register = false;
        if (isset($this->Configuration->face_recognition_feature) && $this->Configuration->face_recognition_feature == 1 && isset($this->Configuration->face_recognition_for_user_register) && $this->Configuration->face_recognition_for_user_register == 1) {
            $face_recognition_for_user_register = true;
        }
        return [
            'navigation_drawer' => $arr_nevigation,
            'register' => [
                'smoker' => $this->ApplicationConfiguration->smoker == 1 ? true : false,
                'email' => $this->ApplicationConfiguration->user_email == 1 ? true : false,
                'user_email_otp' => $this->ApplicationConfiguration->user_email_otp == 1 ? true : false,
                'phone' => $this->ApplicationConfiguration->user_phone == 1 ? true : false,
                'user_phone_otp' => $this->ApplicationConfiguration->user_phone_otp == 1 ? true : false,
                'gender' => $this->ApplicationConfiguration->gender == 1 ? true : false,
                'userImage_enable' => $this->ApplicationConfiguration->userImage_enable == 1 ? true : false,
            ],
            'ride_later' => [
                'ride_later_hours' => $this->BookingConfiguration->normal_ride_later_booking_hours ? (string)($this->BookingConfiguration->normal_ride_later_booking_hours * 60) : "",
                'outstation_time' => $this->BookingConfiguration->outstation_booking_hours ? (string)($this->BookingConfiguration->outstation_booking_hours * 60) : "",
                'rental_ride_later_hours' => $this->BookingConfiguration->rental_ride_later_booking_hours ? (string)($this->BookingConfiguration->rental_ride_later_booking_hours * 60) : "",
                'transfer_ride_later_hours' => $this->BookingConfiguration->transfer_ride_later_booking_hours ? (string)($this->BookingConfiguration->transfer_ride_later_booking_hours * 60) : "",
                'ride_later_max_num_days' => $this->BookingConfiguration->ride_later_max_num_days ? (string)($this->BookingConfiguration->ride_later_max_num_days) : '',
            ],
            'app_version' => [
                'show_dialog' => $version_show_dialog,
                "mandatory" => $version_mandatory,
                "dialog_message" => $version_dialog_message,
                "ios_user_appid" => isset($this->Application->ios_user_appid) ? (string)($this->Application->ios_user_appid) : '',
            ],
            'app_maintainance' => [
                'show_dialog' => $main_show_dialog,
                'show_message' => $main_show_message
            ],
            'languages' => $this->Language,
            'countries' => $countries,
            'ride_config' => [
                'ride_button_now_text' => trans("$string_file.ride_now"),
                'ride_button_later_text' => trans("$string_file.ride_later"),
                'gender_matching' => $this->ApplicationConfiguration->gender == 1 ? true : false,
                'category_vehicle_type_module' => $this->ApplicationConfiguration->home_screen_view == 1 ? true : false,
                'multiple_rides' => $this->BookingConfiguration->multiple_rides == 1 ? true : false,
                'add_note' => $this->BookingConfiguration->additional_note == 1 ? true : false,
                'outstation_ride_now_enabled' => $this->BookingConfiguration->outstation_ride_now_enabled == 1 ? true : false,
                'multi_destination' => $this->BookingConfiguration->multi_destination == 1 ? true : false,
                'total_distination' => (int)$this->BookingConfiguration->count_multi_destination ? $this->BookingConfiguration->count_multi_destination : 3,
                'normal' => array(
                    'drop_location' => array(
                        'ride_now' => ($this->BookingConfiguration->normal_ride_now_drop_location == 1 || $this->BookingConfiguration->final_bill_calculation == 2) ? true : false,
                        'ride_later' => ($this->BookingConfiguration->normal_ride_later_drop_location == 1 || $this->BookingConfiguration->final_bill_calculation == 2) ? true : false,
                    ),
                ),
                'rental' => array(
                    'drop_location' => array(
                        'ride_now' => ($this->BookingConfiguration->rental_ride_now_drop_location == 1 || $this->BookingConfiguration->final_bill_calculation == 2) ? true : false,
                        'ride_later' => ($this->BookingConfiguration->rental_ride_later_drop_location == 1 || $this->BookingConfiguration->final_bill_calculation == 2) ? true : false,
                    ),
                ),
                'pickup_color' => $this->ApplicationConfiguration->pickup_color,
                'dropoff_color' => $this->ApplicationConfiguration->dropoff_color,
                'booking_eta' => $this->BookingConfiguration->booking_eta == 1 ? true : false,
                'drop_location_request' => $this->BookingConfiguration->drop_location_request == 1 ? true : false,
                'change_payment_method' => $this->BookingConfiguration->change_payment_method == 1 ? true : false,
                'drop_outside_area' => $this->Configuration->drop_outside_area == 1 ? true : false,
                'user_request_timeout' => $this->BookingConfiguration->user_request_timeout,
            ],
            'general_config' => [
                'corporate_enable' => $this->Configuration->corporate_admin == 1 ? true : false,
                'additional_info' => $additional_info,
                'chat' => $this->BookingConfiguration->chat == 1 ? true : false,
                'googleKey' => $this->BookingConfiguration->google_key ? $this->BookingConfiguration->google_key : "",
                'favourite_driver_module' => $this->ApplicationConfiguration->favourite_driver_module == 1 ? true : false,
                'static_map' => $this->BookingConfiguration->static_map == 1,
//                'wallet' => in_array(3, array_pluck($this->PaymentMethod, 'id')) ? true : false,
                'wallet' => $this->Configuration->user_wallet_status == 1 ? true : false,
                'demo' => $this->Configuration->demo == 1 ? true : false,
                'homescreen_estimate_fare' => $this->Configuration->homescreen_estimate_fare == 1 ? true : false,
                'default_language' => $this->ApplicationConfiguration->user_default_language,
                'card' => in_array(2, array_pluck($this->PaymentMethod, 'id')) ? true : false,
                'splash_screen' => $this->BusinessName,
                'user_wallet_package' => json_decode($this->Configuration->user_wallet_amount),
                'vehicle_rating_enable' => $this->ApplicationConfiguration->vehicle_rating_enable == 1 ? true : false,
                'security_question' => $this->ApplicationConfiguration->security_question == 1 ? true : false,
                'security_questions' => $this->ApplicationConfiguration->security_question == 1 ? $this->Question : [],
                'tip_enable' => $this->ApplicationConfiguration->tip_status == 1 ? true : false,
                'user_document' => $this->ApplicationConfiguration->user_document == 1 ? true : false,
                'sur_charge' => $this->ApplicationConfiguration->sub_charge == 1 ? true : false,
                'emergency_contact' => $this->ApplicationConfiguration->sos_user_driver == 1 ? true : false,
                'show_logo_main' => false,
                'autocomplete_start' => (int)$this->BookingConfiguration->autocomplete_start,
                'baby_seat_enable' => $this->BookingConfiguration->baby_seat_enable == 1 ? true : false,
                'no_of_person' => $this->Configuration->no_of_person == 1 ? true : false,
                'no_of_children' => $this->Configuration->no_of_children == 1 ? true : false,
                'no_of_bags' => $this->Configuration->no_of_bags == 1 ? true : false,
                'wheel_chair_enable' => $this->BookingConfiguration->wheel_chair_enable == 1 ? true : false,
                'family_member_enable' => $this->Configuration->family_member_enable == 1 ? true : false,
                'user_number_track_screen' => $this->Configuration->user_number_track_screen == 1 ? true : false,
                'no_of_pool_seats' => $pool_seats,
                'user_cpf_number_enable' => $this->ApplicationConfiguration->user_cpf_number_enable == 1 ? true : false,
                'splash_screen_user' => $this->ApplicationConfiguration->splash_screen_user,
                'banner_image_user' => $this->ApplicationConfiguration->banner_image_user,
                'restrict_country_wise_searching' => $this->ApplicationConfiguration->restrict_country_wise_searching == 1 ? true : false,
                'otp_from_firebase' => $this->ApplicationConfiguration->otp_from_firebase == 1 ? true : false,
                'vehicle_ac_enable' => $this->Configuration->vehicle_ac_enable == 1 ? true : false,
                'network_code_visibility' => $this->Configuration->network_code_visibility == 1 ? true : false,
                'referral_code_enable' => isset($this->Configuration->referral_code_enable) && $this->Configuration->referral_code_enable == 1 ? true : false,
                'referral_code_mandatory_user_signup' => $this->Configuration->referral_code_mandatory_user_signup == 1 ? true : false,
                'push_notification' => isset($this->Configuration->push_notification_provider) ? $this->Configuration->push_notification_provider : 1,
                'advance_payment_of_min_bill' => !empty($this->HandymanConfiguration->advance_payment_of_min_bill) ? true : false,
                'segment_per_raw' => !empty($this->ApplicationConfiguration->segment_per_raw) ? $this->ApplicationConfiguration->segment_per_raw : 4,
                'lat_long_storing_at' => isset($this->Configuration->lat_long_storing_at) ? $this->Configuration->lat_long_storing_at : 1, //1 means in driver table of same db
                'handyman_apply_promocode' => $handyman_apply_promocode,
                'polyline' => $this->BookingConfiguration->polyline == 1 ? true : false,
            ],
            'login' => array(
                'email' => $this->ApplicationConfiguration->user_login == 'EMAIL' ? true : false,
                'phone' => $this->ApplicationConfiguration->user_login == 'PHONE' ? true : false,
                'otp' => $otp_enable,
                'skip_login' =>$this->Configuration->skip_login == 1 ? true : false,
            ),
            'customer_support' => [
                "mail" => $this->Configuration->report_issue_email,
                "phone" => $this->Configuration->report_issue_phone
            ],
            'social' => [
                'enable' => $this->Configuration->social_signup == 1 ? true : false,
                'google' => $this->Configuration->google == 1 ? true : false,
                'facebook' => $this->Configuration->facebook == 1 ? true : false,
            ],
            "paymentOption" => $arr_payment_option,
            'theme_cofig' => [
                'primary_color_user' => $this->ApplicationTheme->primary_color_user,
                'chat_button_color' => $this->ApplicationTheme->chat_button_color,
                'share_button_color' => $this->ApplicationTheme->share_button_color,
                'call_button_color' => $this->ApplicationTheme->call_button_color,
                'cancel_button_color' => $this->ApplicationTheme->cancel_button_color,
            ],
            'business_logo' => get_image($this->BusinessLogo, 'business_logo', $this->id),
            'advertise_banner' => $banners,
            'advertise_banner_visibility' => isset($this->advertisement_module) ? true : false,
            'additional_information' => getAdditionalInfo(),
            'user_signup_card_store' => $user_signup_card_store_enable,
            'user_card' => $this->user_card,
            'fare_policy_text' => $fare_policy_text,
            'add_card_option' => $add_card_option,
            'accept_mobile_number_without_zero' => $this->Configuration->accept_mobile_number_without_zero == 1 ? true : false,
            'payment_method' => [
                                'only_cash'=>$only_cash,
                                'name'=>$name,
                                'id'=>$id,
                            ],
            'arr_payment_method' =>$payment_method_list,
            'user_tip_package' => json_decode($this->ApplicationConfiguration->tip_short_amount),
            'payment_option_exist' =>!empty($this->PaymentOption) && $this->PaymentOption->count() > 0 ? true :false,
            'key_data' => [
                'user_android_key' => $this->BookingConfiguration->android_user_key,
                'user_ios_key' => $this->BookingConfiguration->ios_user_key,
            ],
            'map_load_from' => $this->BookingConfiguration->ios_map_load_from,
            'face_recognition_for_user_register' => $face_recognition_for_user_register,
        ];
    }

//    public function with($data)
//    {
//        return [
//            'result' => "1",
//            'message' => trans('api.appconfig'),
//        ];
//    }
}
