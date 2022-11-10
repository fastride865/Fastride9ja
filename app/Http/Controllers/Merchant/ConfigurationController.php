<?php

namespace App\Http\Controllers\Merchant;

use App\Models\ApplicationConfiguration;
use App\Models\ApplicationTheme;
use App\Models\BookingConfiguration;
use App\Models\InfoSetting;
use App\Models\MerchantFarePolicy;
use App\Models\PaymentConfiguration;
use App\Models\Configuration;
use App\Models\DriverConfiguration;
use App\Models\Merchant;
use App\Models\VersionManagement;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Traits\ImageTrait;
use App\Traits\MerchantTrait;
use App\Models\MerchantStripeConnect;
use App\Models\Document;
use Illuminate\Support\Facades\App;

class ConfigurationController extends Controller
{
    use ImageTrait,MerchantTrait;
    public function ApplicationConfiguration()
    {
        $merchant = get_merchant_id(false);
        $configuration = $merchant->ApplicationConfiguration;
        $languages = $merchant->language;
        return view('merchant.random.applicationconfiguration', compact('configuration', 'languages'));
    }

    public function StoreApplicationConfiguration(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'splash_screen_driver' => 'file|image|mimes:jpeg,bmp,png',
            'splash_screen_user' => 'file|image|mimes:jpeg,bmp,png',
            'banner_image_user' => 'file|image|mimes:jpeg,bmp,png',
        ]);
        DB::beginTransaction();
        try {
            ApplicationConfiguration::updateOrCreate(
                ['merchant_id' => $merchant_id],
                [
                    'pickup_color' => $request->pickup_color,
                    'dropoff_color' => $request->dropoff_color,
                ]
            );
            $ApplicationConfiguration = ApplicationConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
            if ($request->hasFile('splash_screen_driver')):
                $ApplicationConfiguration->splash_screen_driver = $this->uploadImage('splash_screen_driver', 'splash');
            endif;
            if ($request->hasFile('splash_screen_user')):
                $ApplicationConfiguration->splash_screen_user = $this->uploadImage('splash_screen_user', 'splash');
            endif;
            if ($request->hasFile('banner_image_user')):
                $ApplicationConfiguration->banner_image_user = $this->uploadImage('banner_image_user', 'splash');
            endif;
            $ApplicationConfiguration->save();
            VersionManagement::updateVersion($merchant_id);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();

        return redirect()->back()->with('configuration', __('admin.message110'));
    }

    public function DriverConfiguration()
    {
        $checkPermission = check_permission(1, 'view_configuration');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }

        $merchant_id = get_merchant_id();
        $languages = Merchant::with('Language')->find($merchant_id);
        $is_demo = $languages->demo == 1 ? true :false;
        $merchant = $languages;
        $configuration = DriverConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $info_setting = InfoSetting::where('slug', 'DRIVER_CONFIGURATION')->first();
        return view('merchant.random.driverconfiguration', compact('configuration', 'languages', 'merchant','info_setting','is_demo'));
    }

    public function StoreDriverConfiguration(Request $request)
    {
        $checkPermission = check_permission(1, 'edit_configuration');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $request->validate([
//            'bill_due_period' => 'required|integer',
//            'bill_grace_period' => 'required|integer',
//            'fee_after_grace_period' => 'required|numeric',
            'auto_verify' => 'required|integer',
            'inactive_time' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $config = Configuration::where('merchant_id',$merchant_id)->first();
            $driver_config = DriverConfiguration::updateOrCreate(
                ['merchant_id' => $merchant_id],
                [
//                    'bill_due_period' => $request->bill_due_period,
//                    'bill_grace_period' => $request->bill_grace_period,
//                    'fee_after_grace_period' => $request->fee_after_grace_period,
                    'auto_verify' => $request->auto_verify,
                    'inactive_time' => $request->inactive_time
                ]
            );
//            if(isset($config->driver_cashout_module) && $config->driver_cashout_module == 1){
                $driver_config->driver_cashout_min_amount = $request->driver_cashout_min_amount;
                $driver_config->save();
//            }
            VersionManagement::updateVersion($merchant_id);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();


        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function GeneralConfiguration()
    {
        $checkPermission = check_permission(1, 'view_configuration');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $is_demo = $merchant->demo == 1 ? true : false;
        $merchant_id = $merchant->id;
        $languages = $merchant;
//            Merchant::with('Language')->find($merchant_id);
        $service_types = $languages->Service;
        $languages = $languages->language;
        $app_configuration = ApplicationConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $version_management = VersionManagement::where([['merchant_id', '=', $merchant_id]])->first();
        $configuration = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $fare_policy_text = MerchantFarePolicy::select('fare_policy')->where([['merchant_id', '=', $merchant_id],['locale', '=', App::getLocale()]])->first();
        $configuration->fare_policy_text = isset($fare_policy_text->fare_policy) ? $fare_policy_text->fare_policy : '';
        $info_setting = InfoSetting::where('slug', 'GENERAL_CONFIGURATION')->first();
        return view('merchant.random.generalconfiguration', compact('service_types', 'configuration', 'languages', 'app_configuration','version_management','info_setting','is_demo'));
    }

    public function StoreGeneralConfiguration(Request $request)
    {
        $checkPermission = check_permission(1, 'edit_configuration');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $request->validate([
            'report_issue_email' => 'required|email',
            'report_issue_phone' => 'required',
            'android_user_maintenance_mode' => 'required|integer|between:1,2',
            'android_user_version' => 'required',
            'android_user_mandatory_update' => 'required|integer|between:1,2',
            'ios_user_maintenance_mode' => 'required|integer|between:1,2',
            'ios_user_version' => 'required',
            'ios_user_mandatory_update' => 'required|integer|between:1,2',
            'android_driver_maintenance_mode' => 'required|integer|between:1,2',
            'android_driver_mandatory_update' => 'required|integer|between:1,2',
            'ios_driver_maintenance_mode' => 'required|integer|between:1,2',
            'ios_driver_mandatory_update' => 'required|integer|between:1,2',
            'android_driver_version' => 'required',
            'ios_driver_version' => 'required',
            'user_default_language' => 'required',
            'driver_default_language' => 'required',
            'default_language' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $userWallet = array();
            if (!empty($request->user_wallet_amount)) {
                foreach ($request->user_wallet_amount as $value) {
                    $userWallet[] = array('amount' => $value);
                }
            }
            $driverWallet = array();
            if (!empty($request->driver_wallet_amount)) {
                foreach ($request->driver_wallet_amount as $value) {
                    $driverWallet[] = array('amount' => $value);
                }
            }
            $tipAmount = array();
            if (!empty($request->tip_short_amount)) {
                foreach ($request->tip_short_amount as $value) {
                    $tipAmount[] = array('amount' => $value);
                }
            }

            $temp = Configuration::updateOrCreate(
                ['merchant_id' => $merchant_id],
                [
                    'report_issue_email' => $request->report_issue_email,
                    'report_issue_phone' => $request->report_issue_phone,
                    'android_user_maintenance_mode' => $request->android_user_maintenance_mode,
                    'android_user_version' => $request->android_user_version,
                    'android_user_mandatory_update' => $request->android_user_mandatory_update,
                    'ios_user_maintenance_mode' => $request->ios_user_maintenance_mode,
                    'ios_user_version' => $request->ios_user_version,
                    'ios_user_mandatory_update' => $request->ios_user_mandatory_update,
                    'android_driver_maintenance_mode' => $request->android_driver_maintenance_mode,
                    'android_driver_version' => $request->android_driver_version,
                    'android_driver_mandatory_update' => $request->android_driver_mandatory_update,
                    'ios_driver_maintenance_mode' => $request->ios_driver_maintenance_mode,
                    'ios_driver_version' => $request->ios_driver_version,
                    'ios_driver_mandatory_update' => $request->ios_driver_mandatory_update,
                    'user_wallet_amount' => json_encode($userWallet, true),
                    'driver_wallet_amount' => json_encode($driverWallet, true),
                    'reminder_doc_expire' => $request->reminder_expire_doc,
                    'default_language' => $request->default_language,
//                    'fare_policy_text' => $request->fare_policy_text,
                ]
            );
            if (isset($temp->twilio_call_masking) && $temp->twilio_call_masking == 1) {
                $temp->twilio_sid = $request->twilio_sid;
                $temp->twilio_service_id = $request->twilio_service_id;
                $temp->twilio_token = $request->twilio_token;
                $temp->save();
            }
            if (isset($temp->face_recognition_feature) && $temp->face_recognition_feature == 1) {
                $temp->face_recognition_end_point = $request->face_recognition_end_point;
                $temp->face_recognition_subscription_key = $request->face_recognition_subscription_key;
                $temp->face_recognition_for_user_register = $request->face_recognition_for_user_register;
                $temp->face_recognition_for_driver_register = $request->face_recognition_for_driver_register;
                $temp->face_recognition_for_driver_online_offline = $request->face_recognition_for_driver_online_offline;
                $temp->save();
            }
            ApplicationConfiguration::updateOrCreate(
                ['merchant_id' => $merchant_id],
                [
                    'logo_hide' => $request->logo_hide,
                    'user_default_language' => $request->user_default_language,
                    'driver_default_language' => $request->driver_default_language,
                    'tip_short_amount' => json_encode($tipAmount, true),
                ]
            );
            MerchantFarePolicy::updateOrCreate(
                ['merchant_id' => $merchant_id,
                    'locale' => App::getLocale()],
                ['fare_policy' => $request->fare_policy_text]
            );
            VersionManagement::updateOrCreate(
                ['merchant_id' => $merchant_id],
                [
                    'api_version' => $request->api_version
                ]);
            VersionManagement::updateVersion($merchant_id);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            p($message);
            // Rollback Transaction
        }
        DB::commit();

        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function StoreBookingConfiguration(Request $request)
    {
        $checkPermission = check_permission(1, 'edit_configuration');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }

        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);

        DB::beginTransaction();
        try {
            $parameter_array = array(
                'partial_accept_hours' => $request->partial_accept_hours,
                'auto_cancel_expired_rides' => $request->auto_cancel_expired_rides,
                'additional_note' => $request->additional_note,
                'driver_request_timeout' => $request->driver_request_timeout,
                'user_request_timeout' => $request->user_request_timeout,
                'tracking_screen_refresh_timeband' => $request->tracking_screen_refresh_timeband,
                'slide_button' => $request->slide_button,
                'drop_location_request' => $request->drop_location_request,
                'estimate_fare_request' => $request->estimate_fare_request,
                'number_of_driver_user_map' => $request->number_of_driver_user_map,
                'booking_eta' => $request->booking_eta,
                'normal_ride_now_radius' => $request->normal_ride_now_radius,
                'normal_ride_now_request_driver' => $request->normal_ride_now_request_driver,
                'normal_ride_now_drop_location' => $request->normal_ride_now_drop_location,
                'normal_ride_later_request_type' => $request->normal_ride_later_request_type,
                'normal_ride_later_radius' => $request->normal_ride_later_radius,
                'normal_ride_later_request_driver' => $request->normal_ride_later_request_driver,
                'normal_ride_later_booking_hours' => $request->normal_ride_later_booking_hours,
                'normal_ride_later_drop_location' => $request->normal_ride_later_drop_location,
                'normal_ride_later_time_before' => $request->normal_ride_later_time_before,
                'rental_ride_now_radius' => $request->rental_ride_now_radius,
                'rental_ride_now_request_driver' => $request->rental_ride_now_request_driver,
                'rental_ride_now_drop_location' => $request->rental_ride_now_drop_location,
                'rental_ride_later_request_type' => $request->rental_ride_later_request_type,
                'rental_ride_later_radius' => $request->rental_ride_later_radius,
                'rental_ride_later_request_driver' => $request->rental_ride_later_request_driver,
                'rental_ride_later_booking_hours' => $request->rental_ride_later_booking_hours,
                'rental_ride_later_drop_location' => $request->rental_ride_later_drop_location,
                'rental_ride_later_time_before' => $request->rental_ride_later_time_before,
                'transfer_ride_now_radius' => $request->transfer_ride_now_radius,
                'transfer_ride_now_request_driver' => $request->transfer_ride_now_request_driver,
                'transfer_ride_now_drop_location' => $request->transfer_ride_now_drop_location,
                'transfer_ride_later_request_type' => $request->transfer_ride_later_request_type,
                'transfer_ride_later_radius' => $request->transfer_ride_later_radius,
                'transfer_ride_later_request_driver' => $request->transfer_ride_later_request_driver,
                'transfer_ride_later_booking_hours' => $request->transfer_ride_later_booking_hours,
                'transfer_ride_later_drop_location' => $request->transfer_ride_later_drop_location,
                'transfer_ride_later_time_before' => $request->rental_ride_later_time_before,
                'pool_radius' => $request->pool_radius,
                'pool_drop_radius' => $request->pool_drop_radius,
                'pool_now_request_driver' => $request->pool_now_request_driver,
                'pool_maximum_exceed' => $request->pool_maximum_exceed,
                'outstation_request_type' => $request->outstation_request_type,
                'outstation_radius' => $request->outstation_radius,
                'outstation_request_driver' => $request->outstation_request_driver,
                'outstation_booking_hours' => $request->outstation_booking_hours,
                'outstation_time_before' => $request->outstation_time_before,
                'baby_seat_enable' => $request->baby_seat_enable,
                'ride_later_cancel_hour' => $request->ride_later_cancel_hour,
                'outstation_ride_now_radius' => $request->outstation_ride_now_radius,
                'outstaion_ride_now_request_driver' => $request->outstaion_ride_now_request_driver,
                'normal_ride_later_cron_hour' => $request->normal_ride_later_cron_hour,
                'rental_ride_later_cron_hour' => $request->rental_ride_later_cron_hour,
                'transfer_ride_later_cron_hour' => $request->transfer_ride_later_cron_hour,
                'outstation_ride_later_cron_hour' => $request->outstation_ride_later_cron_hour,
                'partial_accept_before_hours' => $request->partial_accept_before_hours,
                'ride_later_max_num_days' => $request->ride_later_max_num_days,
                'ride_later_payment_types' => ($request->ride_later_payment_types) ? json_encode($request->ride_later_payment_types) : null,
                'ride_later_cancel_charge_in_cancel_hour' => $request->ride_later_cancel_charge_in_cancel_hour,
                'ride_later_cancel_enable_in_cancel_hour' => $request->ride_later_cancel_enable_in_cancel_hour,
                'driver_ride_radius_request' => isset($request->driver_ride_radius_request) ? json_encode($request->driver_ride_radius_request) : null,
                'store_radius_from_user' => $request->store_radius_from_user,
                'driver_cancel_after_time' => $request->driver_cancel_after_time,
                'android_user_key' => $request->android_user_key,
                'android_driver_key' => $request->android_driver_key,
                'ios_user_key' => $request->ios_user_key,
                'ios_driver_key' => $request->ios_driver_key,
                'ios_map_load_from' => $request->ios_map_load_from,
            );
            if (Auth::user()->demo != 1) {
                $parameter_array = array_merge($parameter_array, ['google_key' => $request->google_key,'google_key_admin'=> $request->google_key_admin,]);
            }
            $bookingConfig = BookingConfiguration::updateOrCreate(
                ['merchant_id' => $merchant_id], $parameter_array
            );
            VersionManagement::updateVersion($merchant_id);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();

        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }


    public function BookingConfiguration(Request $request)
    {
        $checkPermission = check_permission(1, 'view_configuration');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }

        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $is_demo = $merchant->demo == 1 ? true : false;
        $languages = Merchant::with('Language')->find($merchant_id);
        $merchant = $languages;
        $service_types = $languages->Service;
        $paymentmethods = $languages->PaymentMethod;
        $arr_segment_group =   $this->segmentGroup($merchant_id,$return_type = "drop_down","");
        $merchant_segment_group_config = isset($arr_segment_group['arr_group']) ?  array_keys($arr_segment_group['arr_group']) : [];
        $languages = $languages->language;
        $configuration = BookingConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        $gen_config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $info_setting = InfoSetting::where('slug', 'REQUEST_CONFIGURATION')->first();
        return view('merchant.random.bookingconfiguration', compact('service_types', 'configuration', 'languages', 'paymentmethods', 'merchant', 'gen_config','merchant_segment_group_config','info_setting','is_demo'));
    }

    public function paymentConfiguration()
    {
        $checkPermission = check_permission(1, 'view_configuration');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $payment_configuration = PaymentConfiguration:: firstOrCreate(['merchant_id' => $merchant_id]);
        $configuration = Configuration::where('merchant_id', $merchant_id)->first();
        return view('merchant.random.payment_configuration', compact('payment_configuration', 'configuration'));

    }

    public function paymentConfigurationStore(Request $request)
    {
        $checkPermission =  check_permission(1,'edit_configuration');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $request->validate([
//            'outstanding_payment_to' => 'required|integer',
//        ]);

        $configuration = PaymentConfiguration:: where('merchant_id', $merchant_id)->update([
//            'outstanding_payment_to' => $request->outstanding_payment_to,
            'fare_table_based_refer' => $request->fare_table_based_refer,
            'fare_table_refer_type' => $request->fare_table_refer_type,
            'fare_table_refer_pass_value' => $request->fare_table_refer_pass_value,
            'wallet_withdrawal_min_amount' => $request->wallet_withdrawal_min_amount,
            'cancel_rate_table_enable' => $request->cancel_rate_table_enable
        ]);

        return redirect()->back()->with('configuration', __('admin.configuration.added'));
    }
    public function stripeConnectConfiguration()
    {
        $merchant = get_merchant_id(false);
        $configuration = Configuration::where('merchant_id', $merchant->id)->first();
        if ($configuration->stripe_connect_enable != 1) {
            return redirect()->route('merchant.dashboard');
        }
        $merchant_stripe_connect = MerchantStripeConnect::where('merchant_id', $merchant->id)->first();
        $docuements = Document::where('merchant_id', $merchant->id)->get();
        foreach ($docuements as $docuement) {
            $docuement_list[$docuement->id] = $docuement->DocumentName;
        }
        $docuement_list = add_blank_option($docuement_list, 'Select Document');
        return view('merchant.random.stripe_connect_configuration', compact('merchant_stripe_connect', 'merchant','docuement_list'));

    }

    public function stripeConnectConfigurationStore(Request $request)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $request->validate([
            'personal_document' => 'required|integer|exists:documents,id',
            'photo_front_document' => 'required|integer|exists:documents,id',
            'photo_back_document' => 'required|integer|exists:documents,id',
            'additional_document' => 'required|integer|exists:documents,id',
            'business_website' => 'required',
            'email' => 'required|email',
        ]);

        $merchant_stripe_connect = MerchantStripeConnect::updateOrCreate(['merchant_id' => $merchant->id], [
            'personal_document_id' => $request->personal_document,
            'photo_front_document_id' => $request->photo_front_document,
            'photo_back_document_id' => $request->photo_back_document,
            'additional_document_id' => $request->additional_document,
            'business_website' => $request->business_website,
            'email' => $request->email,
        ]);

        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function Applicationtheme()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $applicationtheme = ApplicationTheme::where([['merchant_id', '=', $merchant_id]])->first();
        $application_config = ApplicationConfiguration::select('pickup_color','dropoff_color')->where([['merchant_id', '=', $merchant_id]])->first();
        return view('merchant.random.applicationtheme', compact('applicationtheme','application_config'));
    }

    public function UpdateApplicationtheme(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'primary_color_user' => 'required',
            'primary_color_driver' => 'required',
            'chat_button_color' => 'required',
            'chat_button_color_driver' => 'required',
            'share_button_color' => 'required',
            'share_button_color_driver' => 'required',
            'cancel_button_color' => 'required',
            'cancel_button_color_driver' => 'required',
            'call_button_color' => 'required',
            'call_button_color_driver' => 'required',
            'pickup_color' => 'required',
            'dropoff_color' => 'required',
        ]);
        ApplicationTheme::updateOrCreate(
            ['merchant_id' => $merchant_id],
            [
                'primary_color_user' => $request->primary_color_user,
                'primary_color_driver' => $request->primary_color_driver,
                'chat_button_color' => $request->chat_button_color,
                'chat_button_color_driver' => $request->chat_button_color_driver,
                'share_button_color' => $request->share_button_color,
                'share_button_color_driver' => $request->share_button_color_driver,
                'cancel_button_color' => $request->cancel_button_color,
                'cancel_button_color_driver' => $request->cancel_button_color_driver,
                'call_button_color' => $request->call_button_color,
                'call_button_color_driver' => $request->call_button_color_driver,
            ]
        );
        ApplicationConfiguration::updateOrCreate(
            ['merchant_id' => $merchant_id],
            [
                'pickup_color' => $request->pickup_color,
                'dropoff_color' => $request->dropoff_color,
            ]
        );
        VersionManagement::updateVersion($merchant_id);
        return redirect()->back()->with('applicationtheme', 'Updated');
    }
}
