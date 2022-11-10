<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\SmsGateways\SimpleSms;
use App\Models\EmailConfig;
use App\Models\SmsConfiguration;
use App\Http\Controllers\Controller;
use AfricasTalking\SDK\AfricasTalking;
use App\Models\User;
use Twilio\Rest\Client;
use App\Traits\MailTrait;
use App\Traits\MerchantTrait;

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;

class SmsController extends Controller
{
    use MailTrait, MerchantTrait;



    // for testing purpose
//    public function twilioToken()
//    {
//
//        $twilioAccountSid = 'AC40e4cbedc505459f3c13479aeaf111bd';
//        $twilioApiKey = 'SKd50eeed60108b6878cb6113ddf3f83f0';
//        $twilioApiSecret = 's3TAfZWUdDEAUIfYXHgRCcKIy8ZOcFWR';
//
//
//        $serviceSid = 'ISxxxxxxxxxxxx';
//// choose a random username for the connecting user
//        $identity = "john_doe";
//
//
//        $token = new AccessToken(
//            $twilioAccountSid,
//            $twilioApiKey,
//            $twilioApiSecret,
//            3600,
//            $identity
//        );
//
//// Create Chat grant
//        $chatGrant = new ChatGrant();
//        $chatGrant->setServiceSid($serviceSid);
//
//// Add grant to token
//        $token->addGrant($chatGrant);
//
//// render token to string
//        echo $token->toJWT();
//
//
//    }

    public function SendSms($merchant_id, $phone, $otp, $event = null, $email = "")
    {
        //SendSms($merchant_id, $phone, 1, 'RIDE_BOOK');
        $string_file = $this->getStringFile($merchant_id);
        $message = trans("$string_file.otp_for_verification") . " " . $otp;
        $SmsConfiguration = SmsConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        switch ($event) {
            case 'USER_OTP':
                $message = trans("$string_file.otp_for_verification") . " " . $otp;
                break;
            case 'DRIVER_OTP':
                $message = trans("$string_file.otp_for_verification") . " " . $otp;
                break;
            case 'RIDE_START':
                $message = $SmsConfiguration->ride_start_msg;
                break;
            case 'RIDE_END':
                $message = $SmsConfiguration->ride_end_msg;
                break;
            case 'RIDE_ACCEPT':
                $message = $SmsConfiguration->ride_accept_msg;
                break;
            case 'RIDE_BOOK':
                $message = $SmsConfiguration->ride_book_msg;
                break;
            case 'PUSH_MSG':
                $message = $otp;
                break;
            case 'USER_LOGIN_OTP':
                $message = trans("$string_file.otp_for_verification") . " " . $otp;
                break;
            case 'USER_SIGN_OTP':
                $message = trans("$string_file.otp_for_verification") . " " . $otp;
                break;
            case 'USER_FORGOT_PASSWORD_OTP':
                $message = trans("$string_file.otp_for_verification") . " " . $otp;
                break;
            case 'DRIVER_LOGIN_OTP':
                $message = trans("$string_file.otp_for_verification") . " " . $otp;
                break;
            case 'DRIVER_FORGOT_PASSWORD':
                $message = trans("$string_file.otp_for_verification") . " " . $otp;
                break;
            case 'DRIVER_SIGNUP':
                $message = trans("$string_file.otp_for_verification") . " " . $otp;
                break;
        }
        if (!empty($email)) {
            $configuration = EmailConfig::where('merchant_id', '=', $merchant_id)->first();
            $response = $this->sendMail($configuration, $email, $message, 'otp','','','',$string_file);
        }
        if (!empty($phone) && !empty($SmsConfiguration->sms_provider)) {
            // dd($SmsConfiguration->sms_provider);
            switch ($SmsConfiguration->sms_provider) {
                case "KUTILITY":
                    $sendKutilty = new SimpleSms();
                    $sendKutilty->kutility($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->sender);
                    break;

                case "TWILLIO":
                    $accountSid = $SmsConfiguration->api_key;
                    $authToken = $SmsConfiguration->auth_token;
                    $client = new Client($accountSid, $authToken);
                    try {
                        $client->messages->create($phone,
                            array(
                                'from' => $SmsConfiguration->sender_number,
                                'body' => $message
                            )
                        );
                    } catch (\Exception $e) {
                        // echo "Error: " . $e->getMessage();
                    }
                    break;

                case "AFRICATALKING":
                    $username = $SmsConfiguration->api_key;
                    $authToken = $SmsConfiguration->auth_token;
                    try {
                        $AT = new AfricasTalking($username, $authToken);
                        $sms = $AT->sms();
                        $response = $sms->send(array(
                            "to" => $phone,
                            "from" => $SmsConfiguration->sender,
                            "message" => $message,
                        ));
                        // dd($response);
                        return $response;
                    } catch (\Exception $e) {
                        // dd($e->getMessage());
                    }
                    break;

                case "MobiReach":
                    $sendsms = new SimpleSms();
                    $sendsms->MobiReach($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;

                case "SENANGPAY":
                    $sendsms = new SimpleSms();
                    $sendsms->Senagpay($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token);
                    break;

                case "ONEWAYSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->Onewaysms($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;

                case "KNOWLARITY":
                    $sendsms = new SimpleSms();
                    $sendsms->Knowlarity($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;

                case "ROUTESMS":
                    $sendsms = new SimpleSms();
                    $sendsms->RouteSms($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;

                case "JAVNA":
                    $sendsms = new SimpleSms();
                    $sendsms->JavnaSms($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token);
                    break;

                case "EASYSENDSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->Easysendsms($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "ROBISEARCH":
                    $sendsms = new SimpleSms();
                    $sendsms->Robisearch($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "EXOTEL":
                    $sendsms = new SimpleSms();
                    $sendsms->Exotel($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "TEXTLOCAL":
                    $sendsms = new SimpleSms();
                    $phone = array($phone);
                    $sendsms->TextLocal($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->sender);
                    break;
                case "CLICKATELL":
                    $sendsms = new SimpleSms();
                    $sendsms->clickatell($phone, $message, $SmsConfiguration->api_key);
                    break;
                case "NEXMO":
                    $sendsms = new SimpleSms();
                    $sendsms->Nexmo($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->api_secret_key);
                    break;
                case "EASYSERVICE":
                    $sendsms = new SimpleSms();
                    $sendsms->Easyservice($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->sender, $SmsConfiguration->api_secret_key);
                    break;
                case "NRSGATEWAY":
                    $sendsms = new SimpleSms();
                    $sendsms->NrsGateway($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "WIREPICK":
                    $sendsms = new SimpleSms();
                    $sendsms->wirepick($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "WAYUSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->WayUSms($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "SENDPULSE":
                    $sendsms = new SimpleSms();
                    $sendsms->Sendpulse($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "SMSCOUNTRY":
                    //$url = ("http://api.smscountry.com/SMSCwebservice_bulk.aspx?User=$SmsConfiguration->api_key&passwd=$SmsConfiguration->auth_token&mobilenumber=$phone&message=$message&sid=$SmsConfiguration->sender&mtype=N&DR=Y");
                    $sendsms = new SimpleSms();
                    $sendsms->SmsCountry($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "CELLSYNT":
                    $sendsms = new SimpleSms();
                    $sendsms->Cellsynt($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;

                case "EBULKSMS":
                    $api_key = $SmsConfiguration->api_secret_key;
                    $username = $SmsConfiguration->api_key; // user names
                    try {
                        $data = array(
                            "to" => $phone,
                            "from" => $SmsConfiguration->sender,
                            "message" => $message,
                            "api_key" => $api_key,
                            "user_name" => $username,
                        );
                        $sendsms = new SimpleSms();
                        return $sendsms->EBulkSMS($data);
                    } catch (\Exception $e) {
                        p($e->getMessage());
                    }
                    break;
                case "ENGAGE SPARK":
                    $sendsms = new SimpleSms();
                    $sendsms->EngageSpark($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "POSTAGUVERCINI":
                    $sendsms = new SimpleSms();
                    $sendsms->PostaGuvercini($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token);
                    break;
                case "SMARTSMSSOLUTIONS":
                    $sendsms = new SimpleSms();
                    $sendsms->SmartSmsSolutions($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token);
                    break;
                case "SMSVIRO":
                    $sendsms = new SimpleSms();
                    $sendsms->SMSVIRO($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token);
                    break;
                case "AAKASHSMS":
                    $phone = substr($phone, -10);
                    $sendsms = new SimpleSms();
                    $sendsms->AakashSMS($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token);
                    break;
                case "BULKSMSNIGERIA":
                    $sendsms = new SimpleSms();
                    $sendsms->BulkSmsNigeria($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token);
                    break;
                case "BULKSMSZAMTEL":
                    $sendsms = new SimpleSms();
                    $sendsms->BulkSmsZamtel($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->sender);
                    break;
                case "SSLWIRELESS":
                    $sendsms = new SimpleSms();
                    $sendsms->SslWireLess($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->api_secret_key);
                    break;
                case "MYTELESOM":
                    $sendsms = new SimpleSms();
                    $sendsms->MYTELESOM($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->api_secret_key, $SmsConfiguration->sender);
                    break;
                case "SELCOMSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->SELCOMSMS($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->api_secret_key);
                    break;
                case "NSEMFUA":
                    $sendsms = new SimpleSms();
                    $sendsms->Nsemfua($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->sender);
                    break;
                case "PLIVO":
                    $sendsms = new SimpleSms();
                    $sendsms->Plivo($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "BULKSMSBD":
                    $sendsms = new SimpleSms();
                    $sendsms->BulkSmsBD($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token);
                    break;
                case "MULTITEXTER":
                    $sendsms = new SimpleSms();
                    $sendsms->MULTITEXTER($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "MSG91":
                    $sendsms = new SimpleSms();
                    $sendsms->Msg91($phone, $message, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "OUTREACH":
                    $sendsms = new SimpleSms();
                    $sendsms->OutReach($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender);
                    break;
                case "BUDGETSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->BudgetSms($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->auth_token, $SmsConfiguration->sender, $SmsConfiguration->api_secret_key);
                    break;
                case "CLICKATELLAPI":
                    $sendsms = new SimpleSms();
                    $sendsms->ClickATellApi($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $SmsConfiguration->sender);
                    break;
                case "DATASOFT":
                    $sendsms = new SimpleSms();
                    $sendsms->DataSoft($SmsConfiguration->api_key, $SmsConfiguration->sender, $SmsConfiguration->auth_token, $phone, $message);
                    break;
                case "SHAMELSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->ShamelSms($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message, $SmsConfiguration->sender);
                    break;
                case "SMSLIVE247":
                    $sendsms = new SimpleSms();
                    $sendsms->SMSLive247($SmsConfiguration->api_key, $SmsConfiguration->subacct, $SmsConfiguration->auth_token, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "INFOBIP":
                    $sendsms = new SimpleSms();
                    $sendsms->Infobip($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message);
                    break;
                case "TWWWIRELESS":
                    $sendsms = new SimpleSms();
                    $sendsms->TWWWireless($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "SMS123":
                    $sendsms = new SimpleSms();
                    $sendsms->Sms123($SmsConfiguration->api_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "BULKSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->BulkSMS($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message);
                    break;
                case "TEXTINGHOUSE":
                    $sendsms = new SimpleSms();
                    $sender = $SmsConfiguration->sender;
                    $sendsms->textingHouse($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message, $sender);
                    break;
                case "MOBILE360":
                    $sendsms = new SimpleSms();
                    $sendsms->mobile360Sms($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "FACILITA_MOVEL":
                    $sendsms = new SimpleSms();
                    $sendsms->facilitaMovel($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message);
                    break;
                case "E_SMS":
                    $sendsms = new SimpleSms();
                    $sendsms->eSMS($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "INFOBIP_SMS":
                    $sendsms = new SimpleSms();
                    $sendsms->INFOBIPSMS($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "iSmart":
                    $sendsms = new SimpleSms();
                    $sendsms->iSmart($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message);
                    break;
                case "smsportal":
                    $sendsms = new SimpleSms();
                    $sendsms->smsPortal($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message);
                    break;
                case "ArkeselSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->ArkeselSMS($SmsConfiguration->api_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "BurstSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->BurstSMS($SmsConfiguration->api_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "SMSBOX":
                    $sendsms = new SimpleSms();
                    $sendsms->SMSBOX($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $SmsConfiguration->account_id, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "WhatsAppTodo":
                    $sendsms = new SimpleSms();
                    $sendsms->whatsAppTodo($phone, $message);
                    break;
                case "mymobileapi":
                    $sendsms = new SimpleSms();
                    $sendsms->myMobileApi($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message);
                    break;
                case "SMSCTP":
                    $sendsms = new SimpleSms();
                    $sendsms->SMSCTP($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "SMSDEV":
                    $sendsms = new SimpleSms();
                    $sendsms->SMSDEV($SmsConfiguration->api_key, $phone, $message);
                    break;
                case "MUTHOFUN":
                    $sendsms = new SimpleSms();
                    $sendsms->MuthoFun($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message);
                    break;
                case "KINGSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->KingSms($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message);
                    break;
                case "GLOBELABS":
                    $sendsms = new SimpleSms();
                    $sendsms->GlobeLabs($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $SmsConfiguration->sender_number, $SmsConfiguration->auth_token, $phone, $message);
                    break;
                case "MULTITEXTERSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->MultiTexterSms($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "SMSPRO_NIKITA":
                    $sendsms = new SimpleSms();
                    $sendsms->smsProNikita($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "MONTYMOBILE":
                    $sendsms = new SimpleSms();
                    $sendsms->montyMobile($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "MESSAGEBIRD":
                    $sendsms = new SimpleSms();
                    $sendsms->MessageBird($SmsConfiguration->api_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "FLOPPYSEND":
                    $sendsms = new SimpleSms();
                    $sendsms->FloppySend($SmsConfiguration->api_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "RICHCOMMUNICATION":
                    $sendsms = new SimpleSms();
                    $sendsms->RichCommunication($SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "SMSTO":
                    $sendsms = new SimpleSms();
                    $sendsms->SMSTo($phone,$message,$SmsConfiguration->api_secret_key, $SmsConfiguration->sender);
                    break;
                case "TELESOM":
                    $sendsms = new SimpleSms();
                    $sendsms->telesom($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key,
                        $SmsConfiguration->sender, $phone, $message,$SmsConfiguration->auth_token);
                    break;
                case "SUDACELLBULKSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->sudacellBulkSms($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key,
                        $SmsConfiguration->sender, $phone, $message);
                    break;
                case "BULKSMSSERVICES":
                    $sendsms = new SimpleSms();
                    $sendsms->BulkSMSServices($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key,$SmsConfiguration->sender, $phone, $message);
                    break;
                case "ORANGESMS":
                    $sendsms = new SimpleSms();
                    $sendsms->ORANGESMS($phone, $message, $SmsConfiguration->api_key, $SmsConfiguration->sender, $SmsConfiguration->auth_token);
                    break;
                case "CLICKSEND":
                    $sendsms = new SimpleSms();
                    $sendsms->ClickSend($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message);
                    break;
                case "SINCH":
                    $sendsms = new SimpleSms();
                    $sendsms->Sinch($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key,$SmsConfiguration->sender, $phone, $message);
                    break;
                case "SMSBUS":
                    $sendsms = new SimpleSms();
                    $sendsms->SmsBus($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key,$SmsConfiguration->sender, $phone, $message);
                    break;
                case "MESSAGEMEDIA":
                    $sendsms = new SimpleSms();
                    $sendsms->MessageMedia($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message);
                    break;

                case "NIGERIABULKSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->NigeriaBulkSms($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key,$SmsConfiguration->sender, $phone, $message);
                    break;
                case "AIRTELBULKSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->AirtelBulkSMS($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "ORANGESMSPRO":
                    $sendsms = new SimpleSms();
                    $sendsms->OrangeSMSPro($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key,$SmsConfiguration->auth_token,$SmsConfiguration->sender,$SmsConfiguration->subacct, $phone, $message);
                    break;
                case "SMSZEDEKAA":
                    $sendsms = new SimpleSms();
                    $sendsms->SmsZedekaa($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key,$SmsConfiguration->sender, $phone, $message);
                    break;
                case "NOTIFY":
                    $sendsms = new SimpleSms();
                    $sendsms->appNotifyLk($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key,$SmsConfiguration->sender, $phone, $message);
                    break;
                    break;
                case "BEEMAFRICA":
                    $sendsms = new SimpleSms();
                    $sendsms->BeemAfrica($SmsConfiguration->api_key, $SmsConfiguration->api_secret_key, $phone, $message);
                    break;
                case "MULTITEXTER_V2":
                    $sendsms = new SimpleSms();
                    $sendsms->MultiTexterV2($SmsConfiguration->api_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "LINXSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->LinxSMS($SmsConfiguration->api_key,$SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "BULKSMSDHIRAAGU":
                    $sendsms = new SimpleSms();
                    $sendsms->BulkSMSDhiraagu($SmsConfiguration->api_key,$SmsConfiguration->api_secret_key, $phone, $message);
                    break;
                case "CLOUDWEBSMS":
                    $sendsms = new SimpleSms();
                    $sendsms->CloudWebSMS($SmsConfiguration->api_key,$SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
                case "SMSPOH":
                    $sendsms = new SimpleSms();
                    $sendsms->SMSPoh($SmsConfiguration->api_secret_key, $SmsConfiguration->sender, $phone, $message);
                    break;
            }
        }
    }
}
