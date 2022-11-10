<?php

namespace App\Http\Resources;

use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\Merchant;
use App\Models\AdvertisementBanner;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use App\Models\SmsConfiguration;
use App\Traits\AreaTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\MerchantTrait;

class DriverConfiguration extends JsonResource
{
     use MerchantTrait, AreaTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($data)
    {
        $string_file = $this->getStringFile($this->id);
        if (request()->device == 1) {
            $main_show_dialog = $this->Configuration->android_driver_maintenance_mode == 1 ? true : false;
            $main_show_message = $this->Configuration->android_driver_maintenance_mode == 1 ? trans("$string_file.android_driver_maintenance_mode"): "";
            $version_show_dialog = $this->Configuration->android_driver_version > request()->apk_version  ? true : false;
            $version_mandatory = $this->Configuration->android_driver_mandatory_update == 1 ? true : false;
            $version_dialog_message = $version_show_dialog == true ? trans("$string_file.app_update_warning") : '';
        } else {
            $main_show_dialog = $this->Configuration->ios_driver_maintenance_mode == 1 ? true : false;
            $main_show_message = $this->Configuration->ios_driver_maintenance_mode == 1 ? trans("$string_file.ios_driver_maintenance_mode") : "";
            $version_mandatory = $this->Configuration->ios_driver_mandatory_update == 1 ? true : false;
            $version_show_dialog =  $this->Configuration->ios_driver_version > request()->apk_version ? true : false;
            $version_dialog_message = $version_show_dialog == true ? trans("$string_file.app_update_warning") : '';
        }

        $online_transaction_code = array(
            'name' => $this->Configuration->online_transaction_code,
            'placeholder' => "Please enter " . $this->Configuration->online_transaction_code . " Code",
        );
        $newMerchant = new Merchant();
        $listCountry = $newMerchant->CountrywithAreaList($this);
        $listCommissionOptions = $this->ApplicationConfiguration->driver_commission_choice ==1 ? $newMerchant->DriverCommissionChoices($this) : [];

        $banners = [];
//        if(isset($this->advertisement_module) && $this->advertisement_module == 1){
//            $banner_for = explode(',',$this->advertisement_banner);
//            if(in_array(2,$banner_for)){
//                $current_date = date('Y-m-d');
//                $add_banners = AdvertisementBanner::where([['merchant_id', '=', $this->id], ['status', '=', 1], ['activate_date', '<=', $current_date],['is_deleted', '=', null]])->whereIn('banner_for',[2,4])->get();
//                if(!empty($add_banners)){
//                    foreach ($add_banners as $add_banner){
//                        $banner_button = false;
//                        $banner_redirect_url = "";
//                        if($add_banner->redirect_url != ''){
//                            $banner_button = true;
//                            $banner_redirect_url = $add_banner->redirect_url;
//                        }
//                        if($add_banner->validity == 1){
//                            array_push($banners, array(
//                                'banner_image' => get_image($add_banner->image, 'banner',$this->id),
//                                'banner_button' => $banner_button,
//                                'banner_redirect_url' => $banner_redirect_url
//                            ));
//                        }elseif($add_banner->validity == 2 && $add_banner->expire_date >= $current_date){
//                            array_push($banners, array(
//                                'banner_image' => get_image($add_banner->image, 'banner',$this->id),
//                                'banner_button' => $banner_button,
//                                'banner_redirect_url' => $banner_redirect_url
//                            ));
//                        }
//                    }
//                }
//            }
//        }

        $otp_enable = false;
        if(isset($this->Configuration->user_login_with_otp) && $this->Configuration->user_login_with_otp == 1){
            $SmsConfiguration = SmsConfiguration::where([['merchant_id', '=', $this->id]])->first();
            if(!empty($SmsConfiguration) || $this->ApplicationConfiguration->otp_from_firebase == 1){
                $otp_enable = true;
            }
        }
        $stripe_connect_enable = false;
        if(isset($this->Configuration->stripe_connect_enable) && $this->Configuration->stripe_connect_enable == 1){
            $payment_option = PaymentOption::where('slug', 'STRIPE')->first();
            $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $this->id],['payment_option_id','=', $payment_option->id]])->first();
            if(!empty($paymentoption)){
                $stripe_connect_enable = true;
            }
        }

        $paystack_split_payment_enable = false;
        if(isset($this->Configuration->paystack_split_payment_enable) && $this->Configuration->paystack_split_payment_enable == 1){
            $payment_option = PaymentOption::where('slug', 'PAYSTACK')->first();
            $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $this->id],['payment_option_id','=', $payment_option->id]])->first();
            if(!empty($paymentoption)){
                $paystack_split_payment_enable = true;
            }
        }

        $driver_add_wallet_money_enable = false;
        if(isset($this->Configuration->driver_add_wallet_money_enable) && $this->Configuration->driver_add_wallet_money_enable == 1){
            $driver_add_wallet_money_enable = true;
        }

        $areas = [];
        if($this->Configuration->geofence_module == 1){
            $areas = $this->getGeofenceAreaList(false,$this->id);
            $areas = $areas->get();
            if(!empty($areas)){
                $areas = $areas->map(function ($item, $key)
                {
                    return array(
                        'id' => $item->id,
                        'area_name' => $item->CountryAreaName,
                        'base_area_id' => isset($item->RestrictedArea->base_areas) ? explode(',',$item->RestrictedArea->base_areas) : NULL,
                        'queue_system' => (isset($item->RestrictedArea->queue_system) && $item->RestrictedArea->queue_system == 1) ? true : false,
                        'coordinates' => json_decode($item->AreaCoordinates,true),
                    );
                });
            }
        }

        $account_types = [];
        if(!empty($this->AccountType)){
            foreach($this->AccountType as $account_type){
                array_push($account_types,array(
                    'id' => $account_type->id,
                    'title' => $account_type->Name
                ));
            }
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
        // p($this->PaymentOption);
        $face_recognition_for_driver_register = false;
        $face_recognition_for_driver_online_offline = false;
        if (isset($this->Configuration->face_recognition_feature) && $this->Configuration->face_recognition_feature == 1) {
            if(isset($this->Configuration->face_recognition_for_driver_register) && $this->Configuration->face_recognition_for_driver_register == 1){
                $face_recognition_for_driver_register = true;
            }
            if(isset($this->Configuration->face_recognition_for_driver_online_offline) && $this->Configuration->face_recognition_for_driver_online_offline == 1){
                $face_recognition_for_driver_online_offline = true;
            }
        }
        return [
            'countries' => $listCountry,
            'navigation_drawer' => array(
                'language' => true,
                'customer_support' => true,
                'report_issue' => true,
                'cms_page' => true,
                'wallet' => $this->Configuration->driver_wallet_status == 1 ? true : false,
            ),
            'driver_commission_choices' =>$listCommissionOptions,
            'register' => [
                'driver_commission_choice' => ($this->Configuration->subscription_package == 1 && $this->ApplicationConfiguration->driver_commission_choice == 1 ) ? true : false,
                'smoker' => $this->ApplicationConfiguration->smoker == 1 ? true : false,
                'email' => $this->ApplicationConfiguration->driver_email == 1 ? true : false,
                'driver_email_otp' => $this->ApplicationConfiguration->driver_email_otp == 1 ? true : false,
                'phone' => $this->ApplicationConfiguration->driver_phone == 1 ? true : false,
                'driver_phone_otp' => $this->ApplicationConfiguration->driver_phone_otp == 1 ? true : false,
                'gender' => $this->ApplicationConfiguration->gender == 1 ? true : false,
            ],
            'general_config' => [
                'manual_dispatch' => $this->BookingConfiguration->driver_manual_dispatch == 1 ? true : false,
                'service_type_selection' => $this->BookingConfiguration->service_type_selection == 1 ? true : false,
                'demo' => $this->Configuration->demo == 1 ? true : false,
                'demand_spot_enable' => $this->Configuration->demand_spot_enable == 1 ? true : false,
                'add_multiple_vehicle' => $this->Configuration->add_multiple_vehicle == 1 ? true : false,
                'auto_accept_mode' => $this->BookingConfiguration->auto_accept_mode == 1 ? true : false,
                'subscription_package' => $this->Configuration->subscription_package == 1 ? true : false,
                'driver_limit' => $this->Configuration->driver_limit == 1 ? true : false,
                'default_language' => $this->ApplicationConfiguration->driver_default_language,
                'driver_wallet_package' => json_decode($this->Configuration->driver_wallet_amount),
                'chat' => $this->BookingConfiguration->chat == 1 ? true : false,
                'splash_screen' => $this->BusinessName,
                'emergency_contact' => $this->ApplicationConfiguration->sos_user_driver == 1 ? true : false,
                'vehicle_owner' => $this->ApplicationConfiguration->vehicle_owner == 1 ? true : false,
                'vehicle_ac_enable' => $this->Configuration->vehicle_ac_enable == 1 ? true : false,
                'vehicle_make_text' => $this->ApplicationConfiguration->vehicle_make_text == 1 ? true : false,
                'vehicle_model_text' => $this->ApplicationConfiguration->vehicle_model_text == 1 ? true : false,
                'enable_super_driver' => $this->ApplicationConfiguration->enable_super_driver == 1 ? true : false,
                'bank_details_enable' => $this->Configuration->bank_details_enable == 1 ? true : false,
//                'home_address_enable' => $this->BookingConfiguration->home_address_enable == 1 ? true : false,
                'show_logo_main' => true,
//                'existing_vehicle_enable' => $this->Configuration->existing_vehicle_enable == 1 ? true : false,
                'existing_vehicle_enable' =>false,
                'baby_seat_enable' => $this->BookingConfiguration->baby_seat_enable == 1 ? true : false,
                'wheel_chair_enable' => $this->BookingConfiguration->wheel_chair_enable == 1 ? true : false,
                'online_transaction_code' => $online_transaction_code,
                'driver_rating_enable' => $this->ApplicationConfiguration->driver_rating_enable == 1 ? true : false,
                'driver_cpf_number_enable' => $this->ApplicationConfiguration->driver_cpf_number_enable == 1 ? true : false,
                'splash_screen_driver' => $this->ApplicationConfiguration->splash_screen_driver,
                'otp_from_firebase' => $this->ApplicationConfiguration->otp_from_firebase == 1 ? true : false,
                'polyline' => $this->BookingConfiguration->polyline == 1 ? true : false,
                'booking_eta' => $this->BookingConfiguration->booking_eta == 1 ? true : false,
                'driver_address' => $this->Configuration->driver_address == 1 ? true : false,
                'network_code_visibility' => $this->Configuration->network_code_visibility == 1 ? true : false,
                'referral_code_enable' => isset($this->Configuration->referral_code_enable) && $this->Configuration->referral_code_enable == 1 ? true : false,
                'referral_code_mandatory_driver_signup' => $this->Configuration->referral_code_mandatory_driver_signup == 1 ? true : false,
//                'distance_unit' => Country::select('distance_unit')->where('merchant_id',$merchant_id)->first();
                'push_notification' => isset($this->Configuration->push_notification_provider) ? $this->Configuration->push_notification_provider : 1,
                'driver_cashout_module' => $this->Configuration->driver_cashout_module == 1 ? true : false,
                'restrict_country_wise_searching' => $this->ApplicationConfiguration->restrict_country_wise_searching == 1 ? true : false,
                // 'geofence_module' => $this->Configuration->geofence_module == 1 ? true : false,
                'lat_long_storing_at' => isset($this->Configuration->lat_long_storing_at) ? $this->Configuration->lat_long_storing_at : 1, //1 means in driver table of same db
                'vehicle_model_expire' => isset($this->Configuration->vehicle_model_expire)  && $this->Configuration->vehicle_model_expire == 1 ? true : false, //1 means in driver table of same db
            ],
            'languages' => $this->Language,
            'customer_support' => [
                "mail" => $this->Configuration->report_issue_email,
                "phone" => $this->Configuration->report_issue_phone
            ],
            'paymentOption' => CommonController::filteredPaymentOptions($this->PaymentOption, $this->id),
            'app_version_dialog' => [
                'show_dialog' => $version_show_dialog,
                "mandatory" => $version_mandatory,
                "android_driver_version" => $this->Configuration->android_driver_version,
                "ios_driver_version" => $this->Configuration->ios_driver_version,
                "dialog_message" => $version_dialog_message,
                "ios_driver_appid" => isset($this->Application->ios_driver_appid) ? (string)($this->Application->ios_driver_appid) : '',
            ],
            'app_maintainance' => [
                'show_dialog' => $main_show_dialog,
                'show_message' => $main_show_message
            ],
            'ride_config' => [
                'outstation' => in_array(4, $this->Service) ? true : false,
                "location_update_timeband" => $this->Configuration->location_update_timeband,
                "tracking_screen_refresh_timeband" => $this->Configuration->tracking_screen_refresh_timeband,
                "slide_button" => $this->BookingConfiguration->slide_button == 1 ? true : false,
                'drop_outside_area' => $this->Configuration->drop_outside_area == 1 ? true : false,
                "outstation_notification_popup" => $this->BookingConfiguration->outstation_notification_popup == 1 ? true : false,
                "auto_upgrade" => $this->Configuration->no_driver_availabe_enable == 1 ? true : false,
                "manual_downgrade" => $this->Configuration->manual_downgrade_enable == 1 ? true : false,
            ],
            'tracking' => [
                'scroll' => $this->BookingConfiguration->slide_button == 1 ? true : false
            ],
            'receiving' => [
                'drop_point' => $this->BookingConfiguration->drop_location_request == 1 ? true : false,
                'estimate_fare' => $this->BookingConfiguration->estimate_fare_request == 1 ? true : false,
            ],
            'login' => [
                'email' => $this->ApplicationConfiguration->driver_login == 'EMAIL' ? true : false,
                'phone' => $this->ApplicationConfiguration->driver_login == 'PHONE' ? true : false,
                'otp' => $otp_enable,
            ],
            'theme_cofig' => [
                'primary_color_driver' => $this->ApplicationTheme->primary_color_driver,
                'chat_button_color_driver' => $this->ApplicationTheme->chat_button_color_driver,
                'share_button_color_driver' => $this->ApplicationTheme->share_button_color_driver,
                'call_button_color_driver' => $this->ApplicationTheme->call_button_color_driver,
                'cancel_button_color_driver' => $this->ApplicationTheme->cancel_button_color_driver,
            ],
            'geofence_areas' => $areas,
            'business_logo' => get_image($this->BusinessLogo,'business_logo',$this->id),
            'advertise_banner' => $banners,
            'advertise_banner_visibility' => isset($this->advertisement_module) ? true : false,
            'additional_information' => getAdditionalInfo(),
            'stripe_connect_enable' => $stripe_connect_enable,
            'paystack_split_payment_enable' => $paystack_split_payment_enable,
            'add_wallet_money_enable' => $driver_add_wallet_money_enable,
            'segment_group' => $this->segmentGroup($this->id,"",$string_file),
            'account_types' => $account_types,
            'accept_mobile_number_without_zero' => $this->Configuration->accept_mobile_number_without_zero == 1 ? true : false,
            'arr_payment_method' =>$payment_method_list,
            'key_data' => [
                'driver_android_key' => $this->BookingConfiguration->android_driver_key,
                'driver_ios_key' => $this->BookingConfiguration->ios_driver_key,
            ],
            'face_recognition_feature' => $this->Configuration->face_recognition_feature == 1 ? true : false, // to validate image at the time of login
            'face_recognition_for_driver_online_offline' => $face_recognition_for_driver_online_offline, /// check image at online offline
            'face_recognition_for_driver_register' => $face_recognition_for_driver_register, // check human image at the time of register
        ];
    }

    public function with($data)
    {
        return [
            'result' => "1",
            'message' => "success",
        ];
    }
}
