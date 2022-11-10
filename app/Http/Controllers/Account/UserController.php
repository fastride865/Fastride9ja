<?php

namespace App\Http\Controllers\Account;

use App\Events\UserSignupWelcome;
use App\Http\Controllers\Helper\PolygenController;
use App\Http\Controllers\Helper\ReferralController;
use App\Http\Controllers\Helper\RewardPoint;
use App\Models\Configuration;
use App\Models\DemoConfiguration;
use App\Models\Driver;
use App\Models\QuestionUser;
use App\Models\ApplicationConfiguration;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserCard;
use App\Models\Country;
//use App\Models\UserDevice;
use App\Traits\ApiResponseTrait;
use App\Traits\AreaTrait;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use DB;
use App\Traits\ImageTrait;
use App\Models\EmailConfig;
use App\Models\EmailTemplate;
use App\Models\Merchant;
use App\Traits\MailTrait;
use App\Traits\MerchantTrait;
use View;

class UserController extends Controller
{
    use ImageTrait, ApiResponseTrait, MailTrait, MerchantTrait, AreaTrait;

    public function UserDetail(Request $request)
    {
        $user = $request->user('api');
        $device_data = array('user_id' => $user->id, 'unique_number' => $request->unique_no, 'package_name' => $request->package_name, 'apk_version' => $request->apk_version, 'language_code' => $request->language_code, 'manufacture' => $request->manufacture, 'model' => $request->model, 'device' => $request->device, 'operating_system' => $request->operating_system, 'player_id' => $request->player_id);
        save_user_device_player_id($device_data);
        return new UserResource($request->user('api'));
    }

//    public function getSenderDetails($sender, $code, $country_id, $merchant_id)
//    {
//        switch ($sender) {
//            case 1:
//                $sender_details = User::where([['ReferralCode', '=', $code], ['country_id', '=', $country_id], ['merchant_id', '=', $merchant_id], ['user_delete', '=', NULL]])->first();
//                return $sender_details;
//                break;
//            case 2:
//                $sender_details = Driver::where([['driver_referralcode', '=', $code], ['merchant_id', '=', $merchant_id], ['driver_delete', '=', NULL]])->first();
//                return $sender_details;
//                break;
//            default:
//                break;
//        }
//    }

//    public function getOfferDetails($referral_code, $merchant_id, $country_id)
//    {
//        if (ReferralSystem::where([['code_name', '=', $referral_code], ['merchant_id', '=', $merchant_id], ['country_id', '=', $country_id], ['delete_status', '=', NULL]])->exists()) {
//            $offer_details = ReferralSystem::where([['code_name', '=', $referral_code], ['country_id', '=', $country_id], ['merchant_id', '=', $merchant_id], ['status', '=', 1], ['delete_status', '=', NULL]])->whereIn('application', array(1, 2, 3))->first();
//            $senderType = 0;
//        } elseif (User::where([['ReferralCode', '=', $referral_code], ['merchant_id', '=', $merchant_id], ['country_id', '=', $country_id], ['user_delete', '=', NULL]])->exists()) {
//            $offer_details = ReferralSystem::where([['default_code', '=', 0], ['country_id', '=', $country_id], ['merchant_id', '=', $merchant_id], ['status', '=', 1], ['delete_status', '=', NULL]])->whereIn('application', array(1, 3))->latest()->first();
//            $senderType = 1;
//        } elseif (Driver::where([['driver_referralcode', '=', $referral_code], ['merchant_id', '=', $merchant_id]])->exists()) {
//            $offer_details = ReferralSystem::where([['default_code', '=', 0], ['country_id', '=', $country_id], ['merchant_id', '=', $merchant_id], ['status', '=', 1], ['delete_status', '=', NULL]])->whereIn('application', array(2, 3))->latest()->first();
//            $senderType = 2;
//        }
//        return array($offer_details, $senderType);
//    }
//
//    public function ReferralOffer($referOffer, $receiver_type, $refer_id, $sender_type, $refer_sender_id, $merchant_id)
//    {
//        $this->AddDiscount($merchant_id, $referOffer->id, $refer_id, $receiver_type, $refer_sender_id, $sender_type, $referOffer->offer_type, $referOffer->offer_value, 1, $referOffer->limit, $referOffer->no_of_limit, $referOffer->no_of_day, $referOffer->day_count, $referOffer->start_date, $referOffer->end_date, $referOffer->offer_applicable);
//    }
//
//    public function AddDiscount($merchant_id, $referral_offer_id, $user_id, $receiver_type, $sender_id, $sender_type, $referral_offer, $referral_offer_value, $referral_available, $limit, $limit_usage, $no_of_day, $day_count, $start_date, $end_date, $offer_applicable)
//    {
//        $sender_get_ride = null;
//        $receiver_get_ride = null;
//        if ($referral_offer == 4) {
//            $sender_get_ride = 1;
//            $receiver_get_ride = 1;
//        }
//        ReferralDiscount::create([
//            'referral_system_id' => $referral_offer_id,
//            'merchant_id' => $merchant_id,
//            'receiver_id' => $user_id,
//            'receiver_type' => $receiver_type,
//            'sender_id' => $sender_id,
//            'sender_type' => $sender_type,
//            'limit' => $limit,
//            'limit_usage' => $limit_usage,
//            'no_of_day' => $no_of_day,
//            'day_count' => $day_count,
//            'start_date' => $start_date,
//            'end_date' => $end_date,
//            'offer_applicable' => $offer_applicable,
//            'offer_type' => $referral_offer,
//            'offer_value' => $referral_offer_value,
//            'referral_available' => $referral_available,
//            'sender_get_ride' => $sender_get_ride,
//            'receiver_get_ride' => $receiver_get_ride
//        ]);
//    }

    public function SignUp(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $customMessages = [
            'player_id.required' => trans("$string_file.invalid_player_id"),
            'player_id.min' => trans("$string_file.invalid_player_id"),
            'phone.unique' => trans("$string_file.number_already_used"),
        ];
        if ($request->email != null) {
            $validator = Validator::make($request->all(), ['email' => ['email',
                Rule::unique('users', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
                })]
            ], [
                'email.unique' => trans("$string_file.email_already_used"),
            ]);

            if ($validator->fails()) {
                $errors = $validator->messages()->all();
                return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
            }
        }
        $request_fields = [
            'first_name' => 'required',
            'password' => 'required',
            'smoker_type' => 'required_if:smoker,1|between:1,2',
            'country_id' => 'required|exists:countries,id',
            'phone' => ['required_if:user_phone_enable,1', 'regex:/^[0-9+]+$/',
                Rule::unique('users', 'UserPhone')->where(function ($query) use ($merchant_id) {
                    return $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
                })],
            'email' => 'required_if:user_email_enable,1',
            'questions' => 'nullable|json',
            'user_cpf_number' => ['required_if:user_cpf_enable,1',
                Rule::unique('users', 'user_cpf_number')->where(function ($query) use ($merchant_id) {
                    return $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
                })],
            'network_code' => 'required_if:network_code_visibility,1',
            'referral_code' => 'required_if:referral_code_mandatory_user_signup,1',
        ];
        if ($request->requested_from != 'web') {
            $request_fields['unique_no'] = 'required';
            $request_fields['package_name'] = 'required';
            $request_fields['player_id'] = 'required';
            $request_fields['apk_version'] = 'required';
            $request_fields['device'] = 'required';
            $request_fields['operating_system'] = 'required';
            $request_fields['user_gender'] = 'required_if:gender,1|between:1,2';
        }
        $validator = Validator::make($request->all(), $request_fields, $customMessages);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        // check current service for user
        $area = PolygenController::Area($request->latitude, $request->longitude, $merchant_id);

        DB::beginTransaction();
        try {
            $where1 = ['country_id', '=', $request->country_id];
            $where2 = ['merchant_id', '=', $request->merchant_id];
            $where3 = ['delete_status', '=', NULL];

//            if ($request->referral_code) {
//                if (!((ReferralSystem::where([['code_name', '=', $request->referral_code], $where1, $where2, $where3])->exists()) || (User::where([['ReferralCode', '=', $request->referral_code], $where1, $where2, ['user_delete', '=', NULL]])->exists()) || (Driver::where([['driver_referralcode', '=', $request->referral_code], $where2])->exists()))) {
//                    return response()->json(['result' => "0", 'message' => trans('api.invalid_code'), 'data' => []]);
//                }
//                $offer = $this->getOfferDetails($request->referral_code, $request->merchant_id, $request->country_id);
//            }

            $network_code = isset($request->network_code) ? $request->network_code : NULL;

            // aplication config
            $app_config = ApplicationConfiguration::select('reward_points')->where('merchant_id', $merchant_id)->first();

            $country = Country::select('id')->find($request->country_id);
            // check user document
            $signup_step = $app_config->user_document == 1 && $documentList = $country->documents->count() > 0 ? 1 : 3;

            $first_reward_pending = ($app_config->reward_points == 1) ? 1 : null;
            $gender = $request->user_gender == 0 ? NULL : $request->user_gender;
            $password = Hash::make($request->password);
            $user_mdl = new User();

            $user = new User();
            $user->merchant_id = $merchant_id;
            $user->country_id = $request->country_id;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->UserPhone = $request->phone;
            $user->email = $request->email;
            $user->user_gender = $gender;
            $user->password = $password;
            $user->UserSignupType = 1;
            $user->UserSignupFrom = 1;
            $user->ReferralCode = $user_mdl->GenrateReferCode();
            $user->user_type = 2;
            $user->smoker_type = $request->smoker_type;
            $user->allow_other_smoker = $request->allow_other_smoker;
            $user->user_cpf_number = $request->user_cpf_number;
            $user->first_reward_pending = $first_reward_pending;
            $user->network_code = $network_code;
            $user->signup_status = $signup_step;
            if(isset($request->language) && $request->language != ""){
                $user->language = $request->language;
            }
            $user->save();

            if (isset($request->latitude) && isset($request->longitude) && isset($area['id'])) {
                // call area trait to get id of area
                $user->country_area_id = $area['id'];
                $user->save();
                $ref = new ReferralController();
                $ref->giveReferral($request->referral_code, $user, $user->merchant_id, $user->country_id, $user->country_area_id, "USER");

                $arr_params = array(
                    "user_id" => $user->id,
                    "check_referral_at" => "SIGNUP"
                );
                $ref->checkReferral($arr_params);
            }

            // if ($request->profile_image != "") {
            //     list($format, $image) = explode(',', $request->profile_image);
            //     $temp = explode('/', $format);
            //     list($ext,) = explode(';', $temp[1]);
            //     $file_name = str_random(60) . "." . $ext;
            //     file_put_contents(public_path() . '/user/' . $file_name, base64_decode($image));
            //     $User->UserProfileImage = "user/" . $file_name;
            //     $User->save();
            // }

            if ($request->hasFile('profile_image') && !empty($request->profile_image)) {
                $user->UserProfileImage = $this->uploadImage('profile_image', 'user', $merchant_id);
                $user->save();
            }
            /* $otp = '2019';
            $user_obj = User::where([['id',$user->id],['merchant_id',$merchant_id]])->first();
            event(new UserSignupEmailOtpEvent($user_obj, 'otp',$otp)); */
            if (!empty($request->questions)) {
                $this->QuestionAnswer($request->questions, $user->id);
            }
//            if (!empty($request->country_id) && !empty($request->referral_code)) {
////            $referOffer = ReferralSystem::where([['start_date', '<=', date('Y-m-d')], ['end_date', '>=', date('Y-m-d')], ['country_id', '=', $request->country_id], ['merchant_id', '=', $merchant_id], ['status', '=', 1], ['application', '=', 0]])->first();
//                if (!empty($offer[0])) {
//                    if ($offer[1] != 0) {
//                        $senderDetails = $this->getSenderDetails($offer[1], $request->referral_code, $request->country_id, $merchant_id);
//                        if (!empty($senderDetails)) {
//                            RewardPoint::giveReferralReward($senderDetails, $offer[1]);
//                            $this->ReferralOffer($offer[0], 1, $user->id, $offer[1], $senderDetails->id, $merchant_id);
//                        }
//                    } else {
//                        $this->ReferralOffer($offer[0], 1, $user->id, 0, 0, $merchant_id);
//                    }
//                }
//            }

            $parameter = $request->login_type == "EMAIL" ? $request->email : $request->phone;
            // event(new UserSignupWelcome($user->id));
            // event(new UserSignupWelcome($user->id, $merchant_id, 'welcome'));
//            $temp = EmailTemplate::where('merchant_id', '=', $merchant_id)->where('template_name', '=', "welcome")->first();
//            $merchant=Merchant::Find($merchant_id);
//            $data['temp'] = $temp;
//            $data['merchant']=$merchant;
//            $data['user'] = $user;
//            $data['login_type']=$request->login_type;
//            $email_html = View::make('mail.user-welcome')->with($data)->render();
//            $configuration = EmailConfig::where('merchant_id', '=', $merchant_id)->first();
//            $response = $this->sendMail($configuration, $user->email, $email_html, 'welcome_email', $merchant->BusinessName,NULL,$merchant->email);

            // generate passport token for signup user
            $client = Client::where([['user_id', '=', $merchant_id], ['password_client', '=', 1]])->first();
            Config::set('auth.guards.api.provider', 'users');
            $request->request->add([
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $parameter,
                'password' => $request->password,
                'scope' => '',
            ]);
            $token_generation_after_login = Request::create(
                'oauth/token',
                'POST'
            );
            // return login details after user signup
            $collect_response = \Route::dispatch($token_generation_after_login)->getContent();
            $collectArray = json_decode($collect_response);
            if (isset($collectArray->error)) {
                return response()->json(['result' => "0", 'message' => $collectArray->message, 'data' => []]);
            }
            // add user player id in user_devices id
            if ($request->requested_from != 'web') {
                $device_data = array('user_id' => $user->id, 'unique_number' => $request->unique_no, 'package_name' => $request->package_name, 'apk_version' => $request->apk_version, 'language_code' => $request->language_code, 'manufacture' => $request->manufacture, 'model' => $request->model, 'device' => $request->device, 'operating_system' => $request->operating_system, 'player_id' => $request->player_id);
                save_user_device_player_id($device_data);
            }
            $push_notification = get_merchant_notification_provider($merchant_id,$user->id,'user');
            $return_data = array(
                'access_token' => $collectArray->access_token,
                'push_notification'=>$push_notification
            );
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.signup_done"), $return_data); //signup_done
    }

    public function login(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $customMessages = [
            'player_id.required' => trans("$string_file.invalid_player_id"),
            'player_id.min' => trans("$string_file.invalid_player_id"),
        ];
        $request_fields = [
            'password' => 'required',
            'phone' => 'required',
        ];

        if ($request->requested_from != 'web') {
            $request_fields['unique_no'] = 'required';
            $request_fields['package_name'] = 'required';
            $request_fields['player_id'] = 'required';
            $request_fields['apk_version'] = 'required';
            $request_fields['device'] = 'required';
            $request_fields['operating_system'] = 'required';
        }
        $validator = Validator::make($request->all(), $request_fields, $customMessages);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            // login type will be set from middleware to check login parameter like email, phone
            $parameter = $request->login_type == "EMAIL" ? "email" : "UserPhone";
            $user = User::where([[$parameter, '=', $request->phone], ['merchant_id', '=', $merchant_id],['user_delete','=',NULL]])->latest()->first();
            if (empty($user)) {
                $msg = $request->login_type == "EMAIL" ? trans("$string_file.email_is_not_registered") : trans("$string_file.phone_number_is_not_registered");
//                $msg = $msg.' '.trans("$string_file.is") .' '.trans("$string_file.not_registered");
//                $msg = $request->login_type == "EMAIL" ? trans('api.email_not') : trans('api.phone_not');
                return response()->json(['result' => "0", 'message' => $msg, 'data' => []]);
            }
            if ($user->UserStatus == 2 || $user->user_delete == 1) {
                $msg = $user->driver_delete == 1 ? trans("$string_file.account_has_been_deleted") : trans("$string_file.account_has_been_inactivated");
                return response()->json(['result' => "0", 'message' => $msg, 'data' => []]);
            }

            if ($request->login_via == 2) {
                if (empty($user->corporate_id)) {
//                    trans('api.no_corporate')
                    return response()->json(['result' => "0", 'message' => "", 'data' => []]);
                } else {
                    $user->login_via = $request->login_via;

                }
            }
            $user->save();
            $client = Client::where([['user_id', '=', $merchant_id], ['password_client', '=', 1]])->first();
            Config::set('auth.guards.api.provider', 'users');
            $request->request->add([
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $request->phone,
                'password' => $request->password,
                'scope' => '',
            ]);
            $token_generation_after_login = Request::create(
                'oauth/token',
                'POST'
            );
            $collect_response = \Route::dispatch($token_generation_after_login)->getContent();
            $collectArray = json_decode($collect_response);
            if (isset($collectArray->error)) {
                return $this->failedResponse(trans("$string_file.failed_cred"));
            }
            // add user player id in user_devices id
            // $device_data = array('user_id'=>$user->id,'unique_number'=>$request->unique_no,'package_name'=>$request->package_name, 'player_id' => $request->player_id);
            if ($request->requested_from != 'web') {
                $device_data = array('user_id' => $user->id, 'unique_number' => $request->unique_no, 'package_name' => $request->package_name, 'apk_version' => $request->apk_version, 'language_code' => $request->language_code, 'manufacture' => $request->manufacture, 'model' => $request->model, 'device' => $request->device, 'operating_system' => $request->operating_system, 'player_id' => $request->player_id);
                save_user_device_player_id($device_data);
            }
            $config = Configuration::where('merchant_id', $user->merchant_id)->first();
            $user_card = true;
            $user_signup_card_store = true;
            if (isset($config->user_signup_card_store_enable) && $config->user_signup_card_store_enable == 1) {
                $user_signup_card_store = true;
                $cardList_count = UserCard::where([['user_id', '=', $user->id]])->count();
                if ($cardList_count > 0) {
                    $user_card = false;
                }
            }
            $push_notification = get_merchant_notification_provider($merchant_id,$user->id,'user');
            $return_data = array(
                'access_token' => $collectArray->access_token,
                'user_card' => $user_card,
                'user_signup_card_store' => $user_signup_card_store,
                'push_notification'=>$push_notification
            );
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.success"), $return_data);
    }

    public function loginOtp(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $customMessages = [
            'player_id.required' => trans("$string_file.invalid_player_id"),
            'player_id.min' => trans("$string_file.invalid_player_id"),
        ];
        $request_fields = [
            'phone' => 'required',
            'login_otp' => 'required'
        ];

        if ($request->requested_from != 'web') {
            $request_fields['unique_no'] = 'required';
            $request_fields['package_name'] = 'required';
            $request_fields['player_id'] = 'required';
            $request_fields['apk_version'] = 'required';
            $request_fields['device'] = 'required';
            $request_fields['operating_system'] = 'required';
        }
        $validator = Validator::make($request->all(), $request_fields, $customMessages);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            // login type will be set from middleware to check login parameter like email, phone
            $parameter = $request->login_type == "EMAIL" ? "email" : "UserPhone";
            $user = User::where([[$parameter, '=', $request->phone], ['merchant_id', '=', $merchant_id]])->latest()->first();
            if (empty($user)) {
                $msg = $request->login_type == "EMAIL" ? trans("$string_file.email_is_not_registered") : trans("$string_file.phone_number_is_not_registered");

//                $msg = $request->login_type == "EMAIL" ? trans('api.email_not') : trans('api.phone_not');
                return response()->json(['result' => "0", 'message' => $msg, 'data' => []]);
            }
            if ($user->UserStatus == 2 || $user->user_delete == 1) {
                $msg = $user->driver_delete == 1 ? trans("$string_file.account_has_been_deleted") : trans("$string_file.account_has_been_inactivated");
                return response()->json(['result' => "0", 'message' => $msg, 'data' => []]);
            }

            if ($request->login_via == 2) {
                if (empty($user->corporate_id)) {
//                    trans('api.no_corporate')
                    return response()->json(['result' => "0", 'message' => "", 'data' => []]);
                } else {
                    $user->login_via = $request->login_via;
                    $user->save();
                }
            }

            $client = Client::where([['user_id', '=', $merchant_id], ['password_client', '=', 1]])->first();
            Config::set('auth.guards.api.provider', 'userOtp');
            $request->request->add([
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $user->id,
                'password' => '',
                'scope' => '',
            ]);
            $token_generation_after_login = Request::create(
                'oauth/token',
                'POST'
            );
            $collect_response = \Route::dispatch($token_generation_after_login)->getContent();
            $collectArray = json_decode($collect_response);
            if (isset($collectArray->error)) {
                return $this->failedResponse(trans("$string_file.failed_cred"));
//                return response()->json(['result' => "0", 'message' => trans('auth.failed'), 'data' => []]);
            }
            // add user player id in user_devices id
            // $device_data = array('user_id'=>$user->id,'unique_number'=>$request->unique_no,'package_name'=>$request->package_name, 'player_id' => $request->player_id);
            if ($request->requested_from != 'web') {
                $device_data = array('user_id' => $user->id, 'unique_number' => $request->unique_no, 'package_name' => $request->package_name, 'apk_version' => $request->apk_version, 'language_code' => $request->language_code, 'manufacture' => $request->manufacture, 'model' => $request->model, 'device' => $request->device, 'operating_system' => $request->operating_system, 'player_id' => $request->player_id);
                save_user_device_player_id($device_data);
            }
            $push_notification = get_merchant_notification_provider($merchant_id,$user->id,'user');
            $return_data = array(
                'access_token' => $collectArray->access_token,
                'push_notification'=>$push_notification
                );
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.success"), $return_data);
    }

    public function QuestionAnswer($questions, $user_id)
    {
        $questions = json_decode($questions, true);
        foreach ($questions as $val) {
            $question[] = array(
                'question_id' => $val['question_id'],
                'user_id' => $user_id,
                'answer' => $val['answer'],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            );
        }
        QuestionUser::insert($question);
    }

    public function ForgotPassword(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $fields = [
            'password' => 'required|string',
            'for' => 'required|string',
        ];
        if ($request->for == 'PHONE') {
            $fields['phone'] = ['required', 'regex:/^[0-9+]+$/',
                Rule::exists('users', 'UserPhone')->where(function ($query) use ($merchant_id) {
                    return $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
                })];
        } else {
            $fields['phone'] = ['required', 'email',
                Rule::exists('users', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
                })];
        }
        $validator = Validator::make($request->all(), $fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//                response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $keyword = $request->for == 'PHONE' ? 'UserPhone' : 'email';
        $user = User::where([['merchant_id', '=', $merchant_id], [$keyword, '=', $request->phone]])->where('user_delete', null)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        return $this->successResponse(trans("$string_file.password_changed"), $user);
//            response()->json(['result' => "1", 'message' => trans("password").' '.trans("updated").' '.trans("successfully"), 'data' => $user]);
    }

    public function ChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//                response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL, $user->Merchant);
        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            return $this->successResponse(trans("$string_file.password_changed"), $user);
//            return response()->json(['result' => "1", 'message' => trans('api.changepassword'), 'data' => $user]);
        } else {
            $message = trans("$string_file.invalid_password");
            return $this->failedResponse($message);
        }
    }

    public function EditProfile(Request $request)
    {
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL, $user->Merchant);
        $user_id = $user->id;
        $merchant_id = $user->merchant_id;
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            // 'user_gender' => 'required_if:gender,1|between:1,2',
            'email' => ['required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->where(function ($query) use ($merchant_id) {
                    $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
                })->ignore($user_id)],
            'phone' => ['required', 'string', 'max:255',
                Rule::unique('users', 'UserPhone')->where(function ($query) use ($merchant_id) {
                    $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
                })->ignore($user_id)],
            'smoker_type' => 'required_if:smoker,1|between:1,2',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }

        if ($request->api_type == 1) {
            if ($request->profile_image != "") {
                $user->UserProfileImage = $this->uploadBase64Image('profile_image', 'user', $merchant_id);
            }
        } else {
            if ($request->hasFile('profile_image')) {
                $user->UserProfileImage = $this->uploadImage('profile_image', 'user', $merchant_id);
            }
        }
        $gender = $request->user_gender == 0 ? NULL : $request->user_gender;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->user_gender = $gender;
        $user->email = $request->email;
        $user->UserPhone = $request->phone;
        $user->smoker_type = (int)$request->smoker_type;
        $user->allow_other_smoker = (int)$request->allow_other_smoker;
        $user->save();
        $user->phone_code = $user->Country->phonecode;
        $user->UserPhone = str_replace($user->Country->phonecode, "", $user->UserPhone);
        $user->UserProfileImage = get_image($user->UserProfileImage, 'user', $merchant_id, true, true, 'user');
        $user->country_code = $user->Country->country_code;
        unset($user->Merchant);
        unset($user->Country);
        return $this->successResponse(trans("$string_file.profile_updated"), $user);
//        return response()->json(['result' => "1", 'message' => trans("$string_file.profile_updated"), 'data' => $user]);
    }

    public function Details(Request $request)
    {
        $user = $request->user('api');
        $device_data = array('user_id' => $user->id, 'unique_number' => $request->unique_no, 'package_name' => $request->package_name, 'apk_version' => $request->apk_version, 'language_code' => $request->language_code, 'manufacture' => $request->manufacture, 'model' => $request->model, 'device' => $request->device, 'operating_system' => $request->operating_system, 'player_id' => $request->player_id);
        save_user_device_player_id($device_data);
        $merchant_id = $user->merchant_id;
        $request->user('api')->UserProfileImage = get_image($request->user('api')->UserProfileImage, 'user', $merchant_id,true,true,"user");
        $request->user('api')->signup_status = $request->user('api')->signup_status ? (string)$request->user('api')->signup_status : "";
        $request->user('api')->outstanding_amount = $request->user('api')->outstanding_amount ? $request->user('api')->outstanding_amount : "";
        $request->user('api')->user_gender = $request->user('api')->user_gender ? (string)$request->user('api')->user_gender : "";
        $request->user('api')->wallet_balance = $request->user('api')->wallet_balance ? $request->user('api')->wallet_balance : "";
        $request->user('api')->phone_code = $request->user('api')->Country->phonecode ? $request->user('api')->Country->phonecode : "";
        $request->user('api')->country_code = $request->user('api')->Country->country_code ? $request->user('api')->Country->country_code : "";
        $request->user('api')->UserPhone = str_replace($request->user('api')->Country->phonecode, "",$request->user('api')->UserPhone);
        return response()->json(['result' => "1", 'message' => "success", 'data' => $request->user('api')]);
    }

    public function Logout(Request $request)
    {
        $string_file = $this->getStringFile($request->user('api')->Merchant);
        $request->user('api')->token()->revoke();
        return $this->successResponse(trans("$string_file.logout"));
    }

    public function SocialSign(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $validator = Validator::make($request->all(), [
                'social_id' => ['required',
//                    Rule::exists('users', 'social_id')->where(function ($query) use ($merchant_id) {
//                        return $query->where([['merchant_id', '=', $merchant_id]]);
//                    })
                ],
            ]
        );
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $user = User::where([['social_id', '=', $request->social_id], ['merchant_id', '=', $merchant_id]])->latest()->first();
            if (empty($user)) {
                return $this->successResponse(trans("$string_file.social_account_not_exist"), ['is_social_id_exist' => false]);
//                return $this->failedResponse(trans('api.social_account_not_exist'),['is_social_id_exist' => false]);
            }
            if ($user->user_delete == 1) {
                return $this->failedResponse(trans("$string_file.account_has_been_deleted"));
//                return response()->json(['result' => "0", 'message' => trans("$string_file.account_has_been_deleted"), 'data' => []]);
            }
            if ($user->UserStatus == 2) {
                return $this->failedResponse(trans("$string_file.account_has_been_inactivated"));
//                return response()->json(['result' => "0", 'message' => trans("$string_file.account_has_been_inactivated"), 'data' => []]);
            }
            $client = Client::where([['user_id', '=', $merchant_id], ['password_client', '=', 1]])->first();
            Config::set('auth.guards.api.provider', 'social');
            $request->request->add([
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $request->social_id,
                'password' => "",
                'scope' => '',
            ]);
            $token_generation_after_login = Request::create(
                'oauth/token',
                'POST'
            );
            $collect_response = \Route::dispatch($token_generation_after_login)->getContent();
            $collectArray = json_decode($collect_response);
            if (isset($collectArray->error) || empty($collectArray)) {
                return $this->failedResponse($collectArray->message);
            }
            $push_notification = get_merchant_notification_provider($merchant_id,$user->id,'user');
            $return_data = array(
                'access_token' => $collectArray->access_token,
                'is_social_id_exist' => true,
                'push_notification'=>$push_notification
            );
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.success"), $return_data);
    }

//    public function SocialSignup(Request $request)
//    {
//        $merchant_id = $request->merchant_id;
//        $validator = Validator::make($request->all(), [
//            'social_id' => 'required',
//            'country_id' => 'nullable|exists:countries,id',
//            'platfrom' => 'required',
//            'first_name' => 'required',
//            'phone' => ['required', 'regex:/^[0-9+]+$/'],
//            'email' => ['required_if:user_email_enable,1', 'email',
//                Rule::unique('users', 'email')->where(function ($query) use ($merchant_id) {
//                    return $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
//                })],
//            'user_gender' => 'required_if:gender,1',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
//        }
//
//        $where1 = ['country_id','=',$request->country_id];
//        $where2 = ['merchant_id','=',$request->merchant_id];
//        $where3 = ['delete_status','=',NULL];
//
//        if ($request->referral_code){
//            if (!((ReferralSystem::where([['code_name','=',$request->referral_code],$where1,$where2,$where3])->exists()) || (User::where([['ReferralCode','=',$request->referral_code],$where1,$where2,['user_delete', '=', NULL]])->exists()) || (Driver::where([['driver_referralcode','=',$request->referral_code],$where2])->exists()))){
//                return response()->json(['result' => "0", 'message' => trans('api.invalid_code'), 'data' => []]);
//            }
//            $offer = $this->getOfferDetails($request->referral_code,$request->merchant_id,$request->country_id);
//        }
//
//        $user = User::where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]])
//                ->where(function($q) use($request){
//                    $q->where('email', '=', $request->email);
//                    $q->orWhere('UserPhone','=',$request->phone);
//                })->first();
////        $user = User::where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id], ['email', '=', $request->email],['UserPhone','=',$request->phone]])->first();
//        if (!empty($user)) {
//            $user->social_id = $request->social_id;
//            $user->save();
//        } else {
//            $gender = $request->user_gender == 0 ? NULL : $request->user_gender;
//            $user = new User();
//            $user = User::create([
//                'social_id' => $request->social_id,
//                'country_id' => $request->country_id,
//                'merchant_id' => $merchant_id,
//                'first_name' => $request->first_name,
//                'last_name' => $request->last_name,
//                'user_gender' => $gender,
//                'UserPhone' => $request->phone,
//                'email' => $request->email,
//                'password' => "",
//                'UserSignupType' => 1,
//                'UserSignupFrom' => $request->platfrom,
//                'ReferralCode' => $user->GenrateReferCode(),
//                'user_type' => 2,
//                'UserProfileImage' => ''
//            ]);
//            //event(new UserSignupWelcome($user->id, $merchant_id, 'welcome'));
//        }
//
//        if (!empty($request->country_id) && !empty($request->referral_code)) {
////            $referOffer = ReferralSystem::where([['start_date', '<=', date('Y-m-d')], ['end_date', '>=', date('Y-m-d')], ['country_id', '=', $request->country_id], ['merchant_id', '=', $merchant_id], ['status', '=', 1], ['application', '=', 0]])->first();
//            if (!empty($offer[0])){
//                if ($offer[1] != 0){
//                    $senderDetails = $this->getSenderDetails($offer[1],$request->referral_code,$request->country_id,$merchant_id);
//                    if (!empty($senderDetails)){
//                        RewardPoint::giveReferralReward($senderDetails,$offer[1]);
//                        $this->ReferralOffer($offer[0],1,$User->id,$offer[1], $senderDetails->id,$merchant_id);
//                    }
//                }else{
//                    $this->ReferralOffer($offer[0],1, $User->id, 0,0,$merchant_id);
//                }
//            }
//        }
//
//        $client = Client::where([['user_id', '=', $merchant_id], ['password_client', '=', 1]])->first();
//        Config::set('auth.guards.api.provider', 'social');
//        $request->request->add([
//            'grant_type' => 'password',
//            'client_id' => $client->id,
//            'client_secret' => $client->secret,
//            'username' => $request->social_id,
//            'password' => "",
//            'scope' => '',
//        ]);
//        $token_generation_after_login = Request::create(
//            'oauth/token',
//            'POST'
//        );
//        $collect_response = \Route::dispatch($token_generation_after_login)->getContent();
//        $collectArray = json_decode($collect_response);
//        if (isset($collectArray->error)) {
//            return response()->json(['result' => "0", 'message' => $collectArray->message, 'data' => []]);
//        }
//        return response()->json(['result' => "1", 'message' => trans("$string_file.signup_done"), 'data' => ['access_token' => $collectArray->access_token, 'refresh_token' => $collectArray->refresh_token]]);
//    }

    public function SocialSignup(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $validator = Validator::make($request->all(), [
            'social_id' => 'required',
            'country_id' => 'nullable|exists:countries,id',
            'platfrom' => 'required',
            'first_name' => 'required',
            'phone' => ['required', 'regex:/^[0-9+]+$/'],
            'email' => ['required_if:user_email_enable,1', 'email',
                // Rule::unique('users', 'email')->where(function ($query) use ($merchant_id) {
                //     return $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
                // })
            ],
            'user_gender' => 'required_if:gender,1',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }


        // check current service for user
        $area = PolygenController::Area($request->latitude, $request->longitude, $merchant_id);

        DB::beginTransaction();
        try {
            $where1 = ['country_id', '=', $request->country_id];
            $where2 = ['merchant_id', '=', $request->merchant_id];
            $where3 = ['delete_status', '=', NULL];

//            if ($request->referral_code) {
//                if (!((ReferralSystem::where([['code_name', '=', $request->referral_code], $where1, $where2, $where3])->exists()) || (User::where([['ReferralCode', '=', $request->referral_code], $where1, $where2, ['user_delete', '=', NULL]])->exists()) || (Driver::where([['driver_referralcode', '=', $request->referral_code], $where2])->exists()))) {
//                    return response()->json(['result' => "0", 'message' => trans('api.invalid_code'), 'data' => []]);
//                }
//                $offer = $this->getOfferDetails($request->referral_code, $request->merchant_id, $request->country_id);
//            }

            $user = User::where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]])
                ->where(function ($q) use ($request) {
                    $q->where('email', '=', $request->email);
                    $q->orWhere('UserPhone', '=', $request->phone);
                })->first();
            if (!empty($user->id)) {
                $user->social_id = $request->social_id;
                $user->save();
            } else {
                $gender = $request->user_gender == 0 ? NULL : $request->user_gender;
                $user = new User();
                $User = User::create([
                    'social_id' => $request->social_id,
                    'country_id' => $request->country_id,
                    'merchant_id' => $merchant_id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'user_gender' => $gender,
                    'UserPhone' => $request->phone,
                    'email' => $request->email,
                    'password' => "",
                    'UserSignupType' => 1,
                    'UserSignupFrom' => $request->platfrom,
                    'ReferralCode' => $user->GenrateReferCode(),
                    'user_type' => 2,
                    'UserProfileImage' => ''
                ]);
                //event(new UserSignupWelcome($user->id, $merchant_id, 'welcome'));
            }

            if (isset($request->latitude) && isset($request->longitude) && isset($area['id'])) {
                // call area trait to get id of area
                $User->country_area_id = $area['id'];
                $User->save();
                $ref = new ReferralController();
                $ref->giveReferral($request->referral_code, $User, $User->merchant_id, $User->country_id, $User->country_area_id, "USER");
                $arr_params = array(
                    "user_id" => $User->id,
                    "check_referral_at" => "SIGNUP"
                );
                $ref->checkReferral($arr_params);
            }

//            if (!empty($request->country_id) && !empty($request->referral_code)) {
////            $referOffer = ReferralSystem::where([['start_date', '<=', date('Y-m-d')], ['end_date', '>=', date('Y-m-d')], ['country_id', '=', $request->country_id], ['merchant_id', '=', $merchant_id], ['status', '=', 1], ['application', '=', 0]])->first();
//                if (!empty($offer[0])) {
//                    if ($offer[1] != 0) {
//                        $senderDetails = $this->getSenderDetails($offer[1], $request->referral_code, $request->country_id, $merchant_id);
//                        if (!empty($senderDetails)) {
//                            RewardPoint::giveReferralReward($senderDetails, $offer[1]);
//                            $this->ReferralOffer($offer[0], 1, $User->id, $offer[1], $senderDetails->id, $merchant_id);
//                        }
//                    } else {
//                        $this->ReferralOffer($offer[0], 1, $User->id, 0, 0, $merchant_id);
//                    }
//                }
//            }

            $client = Client::where([['user_id', '=', $merchant_id], ['password_client', '=', 1]])->first();
            Config::set('auth.guards.api.provider', 'social');
            $request->request->add([
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $request->social_id,
                'password' => "",
                'scope' => '',
            ]);
            $token_generation_after_login = Request::create(
                'oauth/token',
                'POST'
            );
            $collect_response = \Route::dispatch($token_generation_after_login)->getContent();
            $collectArray = json_decode($collect_response);
            if (isset($collectArray->error)) {
                return $this->failedResponse($collectArray->message);
            }
            $push_notification = get_merchant_notification_provider($merchant_id,$user->id,'user');
            $return_data = array(
                'access_token' => $collectArray->access_token,
                'push_notification'=>$push_notification
            );
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.signup_done"), $return_data);
    }

    public function DemoUser(Request $request)
    {
        $string_file = $this->getStringFile($request->merchant_id);
        $validator = Validator::make($request->all(), [
            'unique_no' => 'required|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors);
        }
        DB::beginTransaction();
        try {
            $demo = DemoConfiguration::where([['merchant_id', '=', $request->merchant_id]])->first();
            \App\User::where([['unique_number', '=', $request->unique_no], ['merchant_id', '=', $request->merchant_id], ['login_type', '=', 1]])->delete();
//            if (empty($user)) {
                $user = new User();
                $user = User::create([
                    'social_id' => null,
                    'unique_number' => $request->unique_no,
                    'merchant_id' => $request->merchant_id,
                    'first_name' => !empty($request->first_name) ? $request->first_name : "Demo",
                    'last_name' => !empty($request->last_name) ? $request->ladt_name : "User",
                    'UserPhone' => !empty($request->phone_number) ? $request->phone_number : time(),
                    'email' => !empty($request->email) ? $request->email : $request->unique_no . "@User.com",
//                    'email' => $request->unique_no . "@User.com",
                    'password' => "",
                    'UserSignupType' => 1,
                    'UserSignupFrom' => 1,
                    'ReferralCode' => $user->GenrateReferCode(),
                    'user_type' => 2,
                    'login_type' => 1, // for demo login
                    'user_gender' => NULL,
                    'UserProfileImage' => "",
                    'country_area_id' => $demo->country_area_id,
                    'country_id' => $demo->CountryArea->country_id,
                    'signup_status' => 3
                ]);
//            }
            $user->user_gender = NULL;
            $user->save();
            $client = Client::where([['user_id', '=', $request->merchant_id], ['password_client', '=', 1]])->first();
            Config::set('auth.guards.api.provider', 'social');
            $request->request->add([
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $request->unique_no,
                'password' => "",
                'scope' => '',
            ]);
            $token_generation_after_login = Request::create(
                'oauth/token',
                'POST'
            );
            $collect_response = \Route::dispatch($token_generation_after_login)->getContent();
            $collectArray = json_decode($collect_response);
            if (isset($collectArray->error)) {
                return $this->failedResponse($collectArray->message);
            }
            $push_notification = get_merchant_notification_provider($request->merchant_id,$user->id,'user');
            $return_data = array(
                'access_token' => $collectArray->access_token,
                'push_notification'=>$push_notification
            );
            // do entry in user device table
            $device_data = array('user_id' => $user->id, 'unique_number' => $request->unique_no, 'package_name' => $request->package_name, 'apk_version' => $request->apk_version, 'language_code' => $request->language_code, 'manufacture' => $request->manufacture, 'model' => $request->model, 'device' => $request->device, 'operating_system' => $request->operating_system, 'player_id' => $request->player_id);
            save_user_device_player_id($device_data);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.signup_done"), $return_data);
    }

    //    this module is known as saved address of user irrestpective of segments
    // add address for food and handyman segments + taxi & delivery based segment
    public function saveUserAddress(Request $request)
    {
        $user = $request->user('api');
        $request_fields = [
//            'house_name' => 'required',
            //'building' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $id = $request->id;
        if (!empty($id)) {
            $user_address = UserAddress::Find($id);
        } else {
            $user_address = new UserAddress;
            $user_address->user_id = $user->id;
        }
        $user_address->house_name = !empty($request->house_name) ? $request->house_name : "";
        $user_address->floor = !empty($request->floor) ? $request->floor : "";
        $user_address->building = $request->building;
        $user_address->land_mark = !empty($request->land_mark) ? $request->land_mark : "";
        $user_address->address = $request->address;
        $user_address->latitude = $request->latitude;
        $user_address->longitude = $request->longitude;
        $user_address->category = $request->category;
        $user_address->address_title = $request->other_name;
        $user_address->save();
        return $this->successResponse(trans("common.success"),$user_address);
//        return response()->json(['result' => "1", 'message' => trans('api.data_added'), 'data' => $user_address]);
    }

    // get saved address
    public function getUserAddress(Request $request)
    {
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL, $user->Merchant);
        $user_addresses = $user->UserAddress;
        $arr_address = $user_addresses->map(function($item) {

            return [
                'id'=>$item->id,
                'user_id'=>$item->user_id,
                'house_name'=>!empty($item->house_name) ? $item->house_name : "",
                'floor'=>!empty($item->floor) ? $item->floor : "",
                'building'=>!empty($item->building) ? $item->building : "",
                'land_mark'=>!empty($item->land_mark) ? $item->land_mark : "",
                'address'=>!empty($item->address) ? $item->address : "",
                'latitude'=>$item->latitude,
                'longitude'=>$item->longitude,
                'category'=>!empty($item->category) ? $item->category : "",
                'address_title'=>!empty($item->address_title) ? $item->address_title : "",
                'created_at'=>$item->created_at,
                'updated_at'=>$item->created_at,
            ];
        });
        return $this->successResponse(trans("$string_file.data_found"),$arr_address);
    }

    // delete saved address
    public function deleteUserAddress(Request $request)
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
}