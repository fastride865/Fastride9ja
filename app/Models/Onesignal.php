<?php

namespace App\Models;
use App\Models\OneSignalLog;
use Illuminate\Database\Eloquent\Model;
use App\Models\BusinessSegment\BusinessSegment;
use App\Models\BusinessSegment\Order;
use GuzzleHttp\Client;

class Onesignal extends Model
{
    protected $guarded = [];

    public static function UserPushMessage($arr_param)
    {
        $user_id = $arr_param['user_id'];
        $merchant_id = $arr_param['merchant_id'];
        $detail = get_merchant_notification_provider($merchant_id,null,null,'full');
        $arr_param['detail'] = $detail;
        $response = 'No Users';
        $fare_base_device_token = [];
        $onesignal_player_id = [];
        $android_user_devices = [];
        $ios_user_devices = [];
        if (isset($detail->push_notification_provider) && $detail->push_notification_provider == 3)
        {
            // onesignal user's player id
            $onesignal_player_id = UserDevice::select('player_id', 'device')->where(function ($q) use ($user_id) {
                if (!empty($user_id)) {
                    if (is_array($user_id)) {
                        $q->whereIn('user_id', $user_id);
                    } else if($user_id != 'all'){
                        $q->where('user_id', $user_id);
                    }
                }
            })->whereHas('User', function ($q) use ($merchant_id) {
                $q->where([['merchant_id', '=', $merchant_id], ['user_delete', '=', NULL]]);
                $q->whereHas('Country', function ($qq) {
                    $qq->where('isoCode','!=','EGP');
                });
            })->where('player_id', '!=', '')->get()->toArray();

            // firebase device token
            $fare_base_device_token = UserDevice::select('player_id', 'device')->where(function ($q) use ($user_id) {
                if (!empty($user_id)) {
                    if (is_array($user_id)) {
                        $q->whereIn('user_id', $user_id);
                    } else if($user_id != 'all'){
                        $q->where('user_id', $user_id);
                    }
                }
            })->whereHas('User', function ($q) use ($merchant_id) {
                $q->where([['merchant_id', '=', $merchant_id], ['user_delete', '=', NULL]]);
                $q->whereHas('Country', function ($qq) {
                    $qq->where('isoCode','EGP');
                });
            })->where('player_id', '!=', '')->get()->toArray();

            // android
            $android_user_devices = array_filter($fare_base_device_token, function ($e) {
                return $e['device'] == 1;
            });

            //ios
            $ios_user_devices = array_filter($fare_base_device_token, function ($e) {
                return $e['device'] == 2;
            });
        }
        else
        {
            if($user_id == 'all' || !empty($user_id)) {
                $userdevices = UserDevice::select('player_id', 'device')->where(function ($q) use ($user_id) {
                    if (!empty($user_id)) {
                        if (is_array($user_id)) {
                            $q->whereIn('user_id', $user_id);
                        } else if($user_id != 'all'){
                            $q->where('user_id', $user_id);
                        }
                    }
                })->whereHas('User', function ($q) use ($merchant_id) {
                    $q->where([['merchant_id', '=', $merchant_id], ['user_delete', '=', NULL]]);
                })->where('player_id', '!=', '')->get()->toArray();

                if (isset($detail->push_notification_provider) && $detail->push_notification_provider == 2)
                {
                    // android
                    $android_user_devices = array_filter($userdevices, function ($e) {
                        return $e['device'] == 1;
                    });

                    //ios
                    $ios_user_devices = array_filter($userdevices, function ($e) {
                        return $e['device'] == 2;
                    });
                }

                if (isset($detail->push_notification_provider) && $detail->push_notification_provider == 1) {
                    $onesignal_player_id = $userdevices;
                }
            }
        }

        // for android firebase
        $result_android = '';
        if (count($android_user_devices) > 0) {
            $android_playerid = array_pluck($android_user_devices, 'player_id');
            $firebase_key = $detail->firebase_api_key_android;
            $arr_param['firebase_key']  = $firebase_key;
            $arr_param['playerid']  = $android_playerid;
            $result_android = self::fireBaseAndroid($arr_param);
        }

        // for ios firebase
        $result_ios = '';
        if (count($ios_user_devices) > 0) {
            $ios_playerid = array_pluck($ios_user_devices, 'player_id');
            $passphrase = $detail->pem_password_user;
            $pem_file = $detail->firebase_ios_pem_user;
            $firebase_key = $detail->firebase_api_key_android;
            $arr_param['firebase_key']  = $firebase_key;
            $arr_param['passphrase']  = $passphrase;
            $arr_param['pem_file']  = $pem_file;
            $arr_param['playerid']  = $ios_playerid;
            $result_ios = self::fireBaseiOS($arr_param);
        }

        // for android & ios signal
        $one_signal_response = "";
        if(count($onesignal_player_id) > 0)
        {
            $playerid = array_pluck($onesignal_player_id, 'player_id');
            $arr_param['playerid'] = $playerid;
            $arr_param['notification_for'] = "user";
            $one_signal_response = self::oneSignalCurl($arr_param);
        }
        /*for log*/
        $response = array(
            'fcm_android' => $result_android,
            'fcm_ios' => $result_ios,
            'one_signal' => $one_signal_response,
        );

        \Log::channel('onesignal')->emergency($response);
        return $response;
    }

    public static function DriverPushMessage($arr_param)
    {
        $driver_id = $arr_param['driver_id'];
        // p($driver_id);
        $merchant_id = $arr_param['merchant_id'];
        $detail = get_merchant_notification_provider($merchant_id,null,null,'full');
        // p($detail);
        $arr_param['detail'] = $detail;
        $response = 'No Drivers';
        $android_driver_devices = [];
        $ios_driver_devices = [];
        $onesignal_player_id = [];

        if (isset($detail->push_notification_provider) && $detail->push_notification_provider == 3)
        {
            // onesignal driver's player id
            $onesignal_player_id = Driver::select('player_id', 'device')->where(function ($q) use ($driver_id) {
                if (!empty($driver_id)) {
                    if (is_array($driver_id)) {
                        $q->whereIn('id', $driver_id);
                    } else if($driver_id != 'all'){
                        $q->where('id', $driver_id);
                    }
                }
            })->whereHas('Country', function ($qq) {
                $qq->where('isoCode','!=','EGP');
            })->where([['merchant_id', '=', $merchant_id], ['driver_delete', '=', NULL], ['player_id', '!=',NULL]])->get()->toArray();


            $fare_base_device_token = Driver::select('player_id', 'device')->where(function ($q) use ($driver_id) {
                if (!empty($driver_id)) {
                    if (is_array($driver_id)) {
                        $q->whereIn('id', $driver_id);
                    } else if($driver_id != 'all'){
                        $q->where('id', $driver_id);
                    }
                }
            })->whereHas('Country', function ($qq) {
                $qq->where('isoCode','=','EGP');
            })->where([['merchant_id', '=', $merchant_id], ['driver_delete', '=', NULL], ['player_id', '!=', '']])->get()->toArray();

            // android
            $android_driver_devices = array_filter($fare_base_device_token, function ($e) {
                return $e['device'] == 1;
            });

            //ios
            $ios_driver_devices = array_filter($fare_base_device_token, function ($e) {
                return $e['device'] == 2;
            });
        }
        else
        {
            if($driver_id == 'all' || !empty($driver_id)) {
                $driver_devices = Driver::select('player_id', 'device')->where(function ($q) use ($driver_id) {
                    if (!empty($driver_id)) {
                        if (is_array($driver_id)) {
                            $q->whereIn('id', $driver_id);
                        } else if($driver_id != 'all'){
                            $q->where('id', $driver_id);
                        }
                    }
                })->where([['merchant_id', '=', $merchant_id], ['driver_delete', '=', NULL], ['player_id', '!=', NULL]])->get()->toArray();

                if (isset($detail->push_notification_provider) && $detail->push_notification_provider == 2)
                {
                    // android
                    $android_driver_devices = array_filter($driver_devices, function ($e) {
                        return $e['device'] == 1;
                    });

                    //ios
                    $ios_driver_devices = array_filter($driver_devices, function ($e) {
                        return $e['device'] == 2;
                    });
                }
                if (isset($detail->push_notification_provider) && $detail->push_notification_provider == 1) {
                    $onesignal_player_id = $driver_devices;
                }
            }
        }
// p($android_driver_devices);
        // for android
        $result_android = '';
        if (count($android_driver_devices) > 0) {
            $android_playerid = array_pluck($android_driver_devices, 'player_id');
            $firebase_key = $detail->firebase_api_key_android;
            $arr_param['firebase_key']  = $firebase_key;
            $arr_param['playerid']  = $android_playerid;
            $result_android = self::fireBaseAndroid($arr_param);
        }
        //ios
        $result_ios = '';
        if (count($ios_driver_devices) > 0) {
            $ios_playerid = array_pluck($ios_driver_devices, 'player_id');
            $passphrase = $detail->pem_password_driver;
            $pem_file = $detail->firebase_ios_pem_driver;
            $firebase_key = $detail->firebase_api_key_android;
            $arr_param['firebase_key']  = $firebase_key;
            $arr_param['passphrase']  = $passphrase;
            $arr_param['pem_file']  = $pem_file;
            $arr_param['playerid']  = $ios_playerid;
            $result_ios = self::fireBaseiOS($arr_param);
        }


        $response = '';
        if(count($onesignal_player_id) > 0)
        {
            $playerid = array_pluck($onesignal_player_id, 'player_id');
            $arr_param['playerid'] = $playerid;
            $arr_param['notification_for'] = "driver";
            $response = self::oneSignalCurl($arr_param);
        }
        /*for log*/
        $response = array(
            'android' => $result_android,
            'ios' => $result_ios,
            'one_signal' => $response,
        );
        // p($response);
        \Log::channel('onesignal')->emergency($response);
        return $response;
    }

    public static function BusinessSegmentPushMessage($arr_param)
    {
//        $arr_param = array(
//            'driver_id' => $driver->id,
//            'data'=>$data,
//            'message'=>$message,
//            'merchant_id'=>$merchant_id,
//            'title'=>$title);
        $business_segment_id = $arr_param['business_segment_id'];
        $merchant_id = $arr_param['merchant_id'];
        $detail = get_merchant_notification_provider($merchant_id,null,null,'full');
        $arr_param['detail'] = $detail;
        $response = 'No Business Segment';
        if($business_segment_id == 'all' || !empty($business_segment_id)) {
            $business_segment_devices = BusinessSegment::select('player_id', 'device')->where(function ($q) use ($business_segment_id) {
                if (!empty($business_segment_id)) {
                    if (is_array($business_segment_id)) {
                        $q->whereIn('id', $business_segment_id);
                    } else if($business_segment_id != 'all'){
                        $q->where('id', $business_segment_id);
                    }
                }
            })->where([['merchant_id', '=', $merchant_id], ['player_id', '!=', '']])->get()->toArray();

            if (isset($detail->fire_base) && $detail->fire_base == true) {
                // for android
                $android_business_segment_devices = array_filter($business_segment_devices, function ($e) {
                    return $e['device'] == 1;
                });
                // for android
                $result_android = '';
                if (count($android_business_segment_devices) > 0) {
                    $android_playerid = array_pluck($android_business_segment_devices, 'player_id');
                    $firebase_key = $detail->firebase_api_key_android;
                    $arr_param['firebase_key']  = $firebase_key;
                    $arr_param['playerid']  = $android_playerid;
                    $result_android = self::fireBaseAndroid($arr_param);
                }
                //ios
                $result_ios = '';
                $ios_business_segment_devices = array_filter($business_segment_devices, function ($e) {
                    return $e['device'] == 2;
                });
                if (count($ios_business_segment_devices) > 0) {
                    $ios_playerid = array_pluck($ios_business_segment_devices, 'player_id');
                    $passphrase = $detail->pem_password_driver;
                    $pem_file = $detail->firebase_ios_pem_driver;
                    $arr_param['passphrase']  = $passphrase;
                    $arr_param['pem_file']  = $pem_file;
                    $arr_param['playerid']  = $ios_playerid;
                    $result_ios = self::fireBaseiOS($arr_param);
                }
                /*for log*/
                $response = array(
                    'android' => $result_android,
                    'ios' => $result_ios,
                );
            } else {
                $playerid = array_pluck($business_segment_devices, 'player_id');
                $arr_param['playerid'] = $playerid;
                $arr_param['notification_for'] = "business_segment";
                $response = self::oneSignalCurl($arr_param);

                // if (isset($arr_param['data']['segment_data']['id']) && isset($arr_param['data']['segment_data']['booking_status']) && $arr_param['data']['segment_data']['booking_status'] = 1001) {
                //     $response_data = json_decode($response, true);
                //     $failed_driver = isset($response_data['errors']['invalid_player_ids']) ? $response_data['errors']['invalid_player_ids'] : [];
                //     $success_driver = array_diff($playerid, $failed_driver);
                //     $signal_log = new OneSignalLog;
                //     $signal_log->booking_id = $arr_param['data']['segment_data']['id'];
                //     $signal_log->merchant_id = $merchant_id;
                //     $signal_log->recipients = isset($response_data['recipients']) ? $response_data['recipients'] : NULL;
                //     $signal_log->total_request_sent = count($playerid);
                //     $signal_log->failed_driver_id = json_encode($failed_driver);
                //     $signal_log->success_driver_id = json_encode($success_driver);
                //     $signal_log->save();
                // }
            }
        }
        \Log::channel('onesignal')->emergency($response);
        return $response;
    }

    /*OneSignal Android+ iOS notification*/
    public static function oneSignalCurl($arr_param)
    {
        $playerid = $arr_param['playerid'];
        $data = $arr_param['data'];
        $message = $arr_param['message'];
        $detail = $arr_param['detail'];
        $notification_for = $arr_param['notification_for'];
        $title = $arr_param['title'];
        $large_icon = isset($arr_param['large_icon']) ? $arr_param['large_icon'] : NULL;

        if($notification_for == "user")
        {
            $application_key = $detail->user_application_key;
            $rest_key = $detail->user_rest_key;
            $channel_id = $detail->user_channel_id;
            $data['segment_sub_group'] = isset($data['segment_sub_group']) ? $data['segment_sub_group'] :  NULL;
            $data['segment_group_id'] = isset($data['segment_group_id']) ? $data['segment_group_id'] :  NULL;
        }
        elseif($notification_for == "driver")
        {
            $application_key = $detail->driver_application_key;
            $rest_key = $detail->driver_rest_key;
            $channel_id = $detail->driver_channel_id;
            $data['segment_sub_group'] = isset($data['segment_sub_group']) ? $data['segment_sub_group'] :  "NA";
            $data['segment_group_id'] = isset($data['segment_group_id']) ? $data['segment_group_id'] :  "NA";
        }
        elseif($notification_for == "business_segment")
        {
            $application_key = $detail->business_segment_application_key;
            $rest_key = $detail->business_segment_rest_key;
            $channel_id = $detail->business_segment_channel_id;
            $data['segment_sub_group'] = isset($data['segment_sub_group']) ? $data['segment_sub_group'] :  "NA";
            $data['segment_group_id'] = isset($data['segment_group_id']) ? $data['segment_group_id'] :  "NA";
        }
        $playerid = array_filter($playerid);
        $content = array(
            "en" => $message,
        );
        $heading_content = array(
            "en" => $title,
        );
        // adding new key to data
        $data['notification_gen_time'] = time();

        $sendField = "include_player_ids";
        $fields = array(
            'app_id' => $application_key,
            $sendField => $playerid,
            'contents' => $content,
            'content_available' => true,
            'mutable_content' => true,
//            'android_channel_id' => $channel_id,
            'headings' => $heading_content,
            'data' => $data,
            'large_icon'=>"",
            'priority' => 10,
//                "https://i.ibb.co/6R3c78y/logo-circular-lack.png",//$large_icon // Dump icon
        );

        $fields = json_encode($fields);
        // p($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $rest_key));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        // p($response);
        curl_close($ch);
        return $response;
    }

    /*Firebase iOS notification*/
    public static function fireBaseiOS($arr_param)
    {

        $playerid = $arr_param['playerid'];
        $data = $arr_param['data'];
        $message = $arr_param['message'];
        //   p($message);
        $title = $arr_param['title'];
        // $firebase_key = $arr_param['firebase_key'];
        $type = isset($arr_param['type']) ? $arr_param['type'] : NULL;
        $playerid = array_filter($playerid);
        $token = implode(",",$playerid);
        $url = "https://fcm.googleapis.com/fcm/send";
        $serverKey = $arr_param['firebase_key'];
//        $serverKey='AAAAeZn_Pyc:APA91bGySjUgsMMRWPSBdvRUD4NESCRTo4E0o50m1l2Nvxu4FQlHblUshWr6IOL3MG_jMfOuGJQNjrohsBk8EwdRflQ6His3ABkkWK6QALh7peKJuV_oFy142guTlDB3FVzM5cFtLEdO';

        $body_data = [
            'alert' => $message,
            'badge' => 1,
            'contents' => "",
            'mutable_content' => true,
            'data'=>$data,
        ];
        $notification = array('title' =>$title , 'body' => $message,'sound' => 'default', 'content_available' => true);

        $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high','data'=>$body_data,"time_to_live"=> 0);
        $json = json_encode($arrayToSend);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='.$serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //Send the request
        // p('sd');
        $return_data = curl_exec($ch);
        // p('sdfsd');
        //Close request
        if ($return_data === FALSE) {
            // die('FCM Send Error: ' . curl_error($ch));
        }
        // p($response);
        curl_close($ch);
        return $return_data;
        // return $response;
    }

    /*Firebase Android notification*/
    public static function fireBaseAndroid($arr_param)
    {
        // p($arr_param);
        $playerid = $arr_param['playerid'];
//        if(isset($arr_param['data']['segment_data']['package_details']) && is_array($arr_param['data']['segment_data']['package_details']) && count($arr_param['data']['segment_data']['package_details']) == 0)
//        {
//            $arr_param['data']['segment_data']['package_details'] = (object)[];
//        }
        if(isset($arr_param['data']['segment_data']) && is_array($arr_param['data']['segment_data']) && count($arr_param['data']['segment_data']) == 0)
        {
            $arr_param['data']['segment_data'] = (object)[];
        }
        $data = $arr_param['data'];
        $message = $arr_param['message'];
        $title = $arr_param['title'];
        $firebase_key = $arr_param['firebase_key'];
        $type = isset($arr_param['type']) ? $arr_param['type'] : NULL;
        $playerid = array_filter($playerid);

        $data['message']  = $message;
        $fields = [
            'to' => implode(",",$playerid),
            'data'=>[
                'title'=>$title,
                'body'=>$data,
            ],
            'time_to_live'=>1000
        ];
        $headers = [
            'Authorization: key=' .$firebase_key,
            'Content-Type: application/json'
        ];
        $fields = json_encode($fields);
        // p($fields);
        #Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        // p($result);
        curl_close($ch);
        return $result;
    }


    public static function MerchantWebPushMessage($playerid, $data, $message, $title, $merchant_id, $onesignal_redirect_url = null)
    {
        // p($title);
        $order=Order::where('id',$data['id'])->first();
        $detail = Onesignal::where([['merchant_id', '=', $merchant_id]])->first();
        $playerid = array_filter($playerid);
        $content = array(
            "en" => $message,
        );
        $heading_content = array(
            "en" => $title,
        );
        // $application_key =$detail->web_application_key;
        if(!empty($order->BusinessSegment->OneSignal)){
            $application_key = $order->BusinessSegment->OneSignal->application_key;
        }
        else{
            $application_key =$detail->web_application_key;
        }
        $data['notification_gen_time'] = time();

        $sendField = "include_player_ids";
        $fields = array(
            'app_id' => $application_key,
            $sendField => $playerid,
            'contents' => $content,
            'content_available' => true,
            'mutable_content' => true,
            'headings' => $heading_content,
            'data' => $data,
            'url' => $onesignal_redirect_url,
        );
        $fields = json_encode($fields);
        // p($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $detail->web_rest_key));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        //p($response);
        curl_close($ch);
        \Log::channel('onesignal')->emergency($response);
        return $response;
    }

//    public static function MerchantWebPushMessage($playerid, $data, $message, $type, $merchant_id, $onesignal_redirect_url = null)
//    {
//        $detail = Onesignal::where([['merchant_id', '=', $merchant_id]])->first();
//        $playerid = array_filter($playerid);
//        $content = array(
//            "en" => $message,
//        );
//        //$sendField = "include_player_ids";
//        $sendField = "included_segments";
//        /*if ($single == null) {
//            $sendField = $type == "2" ? "included_segments" : $sendField;
//        }*/
//        $fields = array(
//            'app_id' => $detail->web_application_key,
//            $sendField => $playerid,
//            $sendField => array('Active Users'),
//            'contents' => $content,
//            'url' => $onesignal_redirect_url,
//            'data' => array('data' => $data, 'type' => $type),
//        );
//        $fields = json_encode($fields);
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
//            'Authorization: Basic ' . $detail->web_rest_key));
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_HEADER, FALSE);
//        curl_setopt($ch, CURLOPT_POST, TRUE);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        $response = curl_exec($ch);
//        curl_close($ch);
//        \Log::channel('onesignal')->emergency($response);
//        return $response;
//    }

    //    public static function DriverPushMessage($playerid, $data, $message, $type, $merchant_id, $single = null)
//    {
//        $detail = Onesignal::where([['merchant_id', '=', $merchant_id]])->first();
//        //$playerid = array_filter($playerid);
//        $content = array(
//            "en" => $message,
//        );
//        $sendField = "include_player_ids";
//        $fields = array(
//            'app_id' => $detail->driver_application_key,
//            $sendField => $playerid,
//            'contents' => $content,
//            'content_available' => true,
//            'mutable_content' => true,
//            'data' => array('data' => $data, 'type' => $type),
//        );
//        $fields = json_encode($fields);
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
//            'Authorization: Basic ' . $detail->driver_rest_key));
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_HEADER, FALSE);
//        curl_setopt($ch, CURLOPT_POST, TRUE);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        $response = curl_exec($ch);
//        curl_close($ch);
//        \Log::channel('onesignal')->emergency($response);
//        if(isset($data['booking_id']) && isset($data['booking_status']) && $data['booking_status'] =1001)
//        {
//            $response_data = json_decode($response,true);
//            $failed_driver = isset($response_data['errors']['invalid_player_ids']) ? $response_data['errors']['invalid_player_ids']:[];
//            $success_driver = array_diff($playerid,$failed_driver);
//            $signal_log = new OneSignalLog;
//            $signal_log->booking_id = $data['booking_id'];
//            $signal_log->merchant_id = $merchant_id;
//            $signal_log->recipients = isset($response_data['recipients']) ? $response_data['recipients'] : NULL;
//            $signal_log->total_request_sent = count($playerid);
//            $signal_log->failed_driver_id = json_encode($failed_driver);
//            $signal_log->success_driver_id = json_encode($success_driver);
//            $signal_log->save();
//        }
//        return $response;
//    }
}
