<?php

namespace App\Http\Controllers\SmsGateways;
//require __DIR__.'/../vendor/autoload.php';
use Plivo\RestClient;

class SimpleSms
{

    public function getCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $result = curl_exec($ch);
        curl_close($ch);
        // p($result);
        return $result;
    }

    public function TextLocal($numbers = array(), $message = null, $api_key, $sender = 'TextLocal', $sched = null, $test = false, $receiptURL = null, $custom = null, $optouts = false, $simpleReplyService = false)
    {
        if (!is_null($sched) && !is_numeric($sched))
            throw new Exception('Invalid date format. Use numeric epoch format');

        $params = array(
            'message' => rawurlencode($message),
            'numbers' => implode(',', $numbers),
            'sender' => rawurlencode($sender),
            'schedule_time' => $sched,
            'test' => $test,
            'receipt_url' => $receiptURL,
            'custom' => $custom,
            'optouts' => $optouts,
            'simple_reply' => $simpleReplyService,
            'api_key' => $api_key,
            'username' => false,
        );
        $url = 'https://api.textlocal.in/send/';//'https://api.txtlocal.com/send/';
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ));

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

    }

    public function kutility($phone = null, $message = null, $api_key = null, $from = null)
    {
        $phone = str_replace("+", "", $phone);
        $sms_text = urlencode($message);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://kutility.in/app/smsapi/index.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "key=" . $api_key . "&routeid=415&type=text&contacts=" . $phone . "&senderid=" . $from . "&msg=" . $sms_text);
        $a = curl_exec($ch);
        curl_close($ch);
    }

    public function MobiReach($phone = null, $message = null, $username, $password, $sender)
    {

        $phone = str_replace("+", "", $phone);
        $message = urlencode($message);
        $url = "https://api.mobireach.com.bd/SendTextMessage?Username=$username&Password=$password&From=$sender&To=$phone&Message=$message";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));

        curl_exec($curl);
        curl_close($curl);
    }

    public function Senagpay($to, $msg, $username, $password)
    {

        $to = str_replace("+", "", $to);
        $postarray = array(
            "recipient" => $to,
            "body" => $msg
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.senangpay.my/notification/sms/send/linkz');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postarray);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_exec($ch);
        curl_close($ch);
    }

    public function Onewaysms($phone = null, $message = null, $username, $password, $sender)
    {
        $phone = str_replace("+", "", $phone);
        $query_string = "api.aspx?apiusername=" . $username . "&apipassword=" . $password;
        $query_string .= "&senderid=" . rawurlencode($sender) . "&mobileno=" . rawurlencode($phone);
        $query_string .= "&message=" . rawurlencode(stripslashes($message)) . "&languagetype=1";
        $url = "http://gatewayd2.onewaysms.sg:10002/" . $query_string;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));

        $a = curl_exec($curl);
        curl_close($curl);
        // $fd = @implode ('', file ($url));
        // if ($fd)  {
        //     if ($fd > 0) {Print("MT ID : " . $fd); 	$ok = "success";
        //     }   else {	print("Please refer to API on Error : " . $fd);
        // 	$ok = "fail";
        //     }
        //           } else{    $ok = "fail"; }

    }


    public function Knowlarity($phone = null, $message = null, $username, $password, $sender)
    {
        $phone = str_replace("+", "", $phone);
        $message = urlencode($message);
        $url = "http://message.knowlarity.com/api/mt/SendSMS?user=$username&password=$password&senderid=$sender&channel=Trans&DCS=0&flashsms=0&number=$phone&text=$message&route=9";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));

        curl_exec($curl);
        curl_close($curl);
    }

    public function RouteSms($phone = null, $message = null, $username, $password, $sender)
    {
        $phone = str_replace("+", "", $phone);
        $message = urlencode($message);
        //Smpp http Url to send sms.
        $live_url = "http://rslr.connectbind.com/bulksms/bulksms?username=" . $username . "&password=" . $password . "&type=0&dlr=1&destination=" . $phone . "&source=" . $sender . "&message=" . $message . "";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $live_url
        ));

        $a = curl_exec($curl);
        curl_close($curl);

    }

    public function JavnaSms($phone = null, $otpmessage = null, $username = null, $password = null)
    {
        $phone = str_replace("+", "", $phone);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://http1.javna.com/epicenter/gatewaysend.asp?LoginName=" . $username . "&Password=" . $password . "&MessageRecipients=" . $phone . "&MessageBody=" . urlencode($otpmessage) . "&SenderName=Pink",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }


    public function Easysendsms($phone = null, $message = null, $username, $password, $sender)
    {
        $phone = str_replace("+", "", $phone);
        $message = urlencode($message);
        //Smpp http Url to send sms.
        $live_url = "https://www.easysendsms.com/sms/bulksms-api/bulksms-api?username=" . $username . "&password=" . $password . "&from=" . $sender . "&to=" . $phone . "&text=" . $message . "&type=0";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $live_url
        ));

        $a = curl_exec($curl);
        curl_close($curl);

    }

    public function Robisearch($phone = null, $message = null, $username, $password, $sender)
    {

        $url = 'http://sms.robisearch.com/sendsms.jsp?';
        $xml_data = '<?xml version="1.0"?><smslist><sms><user>' . $username . '</user><password>' . $password . '</password><message>' . $message . '</message><mobiles>' . $phone . '</mobiles><senderid>' . $sender . '</senderid><cdmasenderid>00201009546244</cdmasenderid><group>-1</group><clientsmsid>0</clientsmsid></sms></smslist>';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
    }

    public function Exotel($phone = null, $message = null, $username, $password, $sender)
    {
        $phone = str_replace("+", "", $phone);
        $post_data = array(
            'From' => $sender,
            'To' => $phone,
            'Body' => $message,
        );
        $url = "https://" . $username . ":" . $password . "@api.exotel.com/v1/Accounts/" . $username . "/Sms/send";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

        $http_result = curl_exec($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        //print "Response = ".print_r($http_result);
    }

    public function clickatell($phone = null, $message = null, $username = null)
    {
        $message = urlencode($message);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://platform.clickatell.com/messages/http/send?apiKey=$username&to=$phone&content=$message",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Access-Control-Allow-Credentials: true",
                "Access-Control-Allow-Headers: application/json",
                "Access-Control-Allow-Methods: GET,POST,PUT,PATCH,DELETE",
                "Access-Control-Allow-Origin: *",
                "Content-Type: application/json",
                "Postman-Token: 9cdf3ad0-4298-4bc0-9a45-744f96e371d7",
                "aliasName: apporiotaxi",
                "cache-control: no-cache",
                "locale: en",
                "publicKey: "
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }


    public function Nexmo($phone, $message, $api_key, $api_secret)
    {

        $basic = new \Nexmo\Client\Credentials\Basic($api_key, $api_secret);
        $client = new \Nexmo\Client($basic);

        try {
            $message = $client->message()->send([
                'to' => $phone,
                'from' => config('app.name'),
                'text' => $message
            ]);
            $response = $message->getResponseData();

            // if($response['messages'][0]['status'] == 0) {
            //     echo "The message was sent successfully\n";
            // } else {
            //     echo "The message failed with status: " . $response['messages'][0]['status'] . "\n";
            // }
        } catch (Exception $e) {
            echo "The message was not sent. Error: " . $e->getMessage() . "\n";
        }
    }


    public function Easyservice($phone = null, $message = null, $api_key = null, $sender = null, $messageType = null)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://app.easy.com.np/easyApi?key=" . $api_key . "&source=" . $sender . "&destination=" . $phone . "&type=" . $messageType . "&message=" . urlencode($message),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $response;

    }

    public function NrsGateway($phone = null, $message = null, $api_key = null, $auth_token = null, $sender = null)
    {
        $post['to'] = array($phone);
        $post['text'] = $message;
        $post['from'] = $sender;
        $user = $api_key;
        $password = $auth_token;
        $ch =
            curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://gateway.plusmms.net/rest/message");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json", "Authorization: Basic " . base64_encode($user . ":" . $password)));
        $result = curl_exec($ch);
        // echo $result;die();
    }

    public function WIREPICK($phone = null, $message = null, $username, $password, $sender)
    {
        $phone = str_replace("+", "", $phone);
        $message = urlencode($message);
        $live_url = "https://sms.wirepick.com/httpsms/send?client=" . $username . "&password=" . $password . "&phone=$phone&text=$message&from=" . $sender . "&type=0";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $live_url
        ));

        $a = curl_exec($curl);
        curl_close($curl);

    }

    public function Cellsynt($phone = null, $message = null, $username, $password, $sender)
    {
        $sms_url = "http://se-1.cellsynt.net/sms.php";        // Gateway URL

        $type = "text";                                        // Message type
        $originatortype = "alpha";                            // Message originator (alpha = Alphanumeric, numeric = Numeric, shortcode = Operator shortcode)
        $originator = $sender;                                // Message originator

        // GET parameters
        $parameters = "username=$username&password=$password";
        $parameters .= "&type=$type&originatortype=$originatortype&originator=" . urlencode($originator);
        $parameters .= "&destination=$phone&text=" . urlencode($message);

        // Send HTTP request
        $response = file_get_contents($sms_url . "?" . $parameters);
    }

    public function SmsCountry($phone = null, $message = null, $username, $password, $sender)
    {
        $user = $username;
        $password = $password;
        $mobilenumbers = $phone;
        $message = $message;
        $senderid = $sender;
        $messagetype = "N";
        $DReports = "Y";
        $url = "http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
        $message = urlencode($message);
        $ch = curl_init();
        $ret = curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "User=$user&passwd=$password&mobilenumber=$mobilenumbers&message=$message&sid=$senderid&mtype=$messagetype&DR=$DReports");
        $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $curlresponse = curl_exec($ch); // execute
        if (curl_errno($ch))
            if (empty($ret)) {
                curl_close($ch);
            } else {
                $info = curl_getinfo($ch);
                curl_close($ch);
            }

    }

    public function Sendpulse($phone = null, $message = null, $username, $password, $sender)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendpulse.com/oauth/access_token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"grant_type\"\r\n\r\nclient_credentials\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"client_id\"\r\n\r\n$username\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"client_secret\"\r\n\r\n$password\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
                "postman-token: 6d5fc463-ee52-07fd-726e-ebd892c29edc"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $access_token = $response['access_token'];
        if ($access_token) {
            $phone = str_replace("+", "", $phone);
            $message = substr($message, -4);
            $body_param = json_encode(array("sender" => $sender,"phones"=> "[$phone]","body" => $message,"transliterate" => 0,"emulate" => 0));
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.sendpulse.com/sms/send",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $body_param,
                CURLOPT_HTTPHEADER => array(
                    "Authorization:Bearer $access_token",
                    "Content-Type: application/json"
                ),
            ));
            //"https://api.sendpulse.com/sms/numbers/variables",
//                CURLOPT_POSTFIELDS => 'phones={"' . $phone . '":[[{"name":"User_ID","type":"number","value" : "' . $message . '"}]]}&addressBookId=280143',
            $result = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                //echo "cURL Error #:" . $err; die();
            } else {
                //echo $response; die();
            }
        }
    }

    public function WayUSms($phone = null, $message = null, $username, $password, $sender)
    {
        $post['to'] = array($phone);
        $post['text'] = $message;
        $post['from'] = $sender;
        $user = $username;
        $password = $password;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://dashboard.wausms.com/Api/rest/message");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                "Accept: application/json",
                "Authorization: Basic " . base64_encode($user . ":" . $password)));
        $result = curl_exec($ch);
    }

    public function EBulkSMS($data)
    {
        $url = "http://api.ebulksms.com:8080/sendsms?username=" . $data['user_name'] . "&apikey=" . $data['api_key'] . "&sender=" . $data['from'] . "&messagetext=" . urlencode($data['message']) . "&flash=0&recipients=" . $data['to'];
        $this->getCurl($url);
    }

    public function EngageSpark($phone, $msg, $orgId, $auth, $sender)
    {
        $phone = substr($phone, 1);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.engagespark.com/v1/sms/contact?=",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\t\"orgId\":$orgId,\n\t\"to\":\"$phone\",\n\t\"from\":\"$sender\",\n\t\"message\":\"$msg\"\n}\n",
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $auth,
                "Content-Type: application/json",
                "Postman-Token: e3dc5d3c-ae32-4f85-9796-cb2f9615c0b7",
                "cache-control: no-cache"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }

    public function PostaGuvercini($phone, $msg, $username, $password)
    {
        $url = "http://www.postaguvercini.com/api_http/sendsms.asp?user=" . $username . "&password=" . $password . "&gsm=" . $phone . "&text=" . urlencode($msg);
        $this->getCurl($url);
    }

    public function SmartSmsSolutions($phone, $msg, $senderId, $token)
    {
        $message = urlencode($msg);
        $senderid = urlencode($senderId);
        $to = $phone;
        $routing = 5; //basic route = 5
        $type = 0;
        $baseurl = 'https://smartsmssolutions.com/api/json.php?';
        $sendsms = $baseurl . 'message=' . $message . '&to=' . $to . '&sender=' . $senderid . '&type=' . $type . '&routing=' . $routing . '&token=' . $token;
        $response = $this->getCurl($sendsms);
        //echo $response;
    }

    public function SMSVIRO($phone, $msg, $senderId, $token)
    {
        $senderid = urlencode($senderId);
        $baseurl = 'http://107.20.199.106/restapi/sms/1/text/single';
        $paremeters = json_encode(array('from' => '', 'to' => $phone, 'text' => $msg), true);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseurl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $paremeters,
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $token,
                "Content-Type: application/json",
                "Accept: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }

    public function AakashSMS($phone, $msg, $sender, $token)
    {
        $args = http_build_query(array(
            'auth_token' => $token,
            'from' => $sender,
            'to' => "$phone",
            'text' => $msg));
        $url = "http://aakashsms.com/admin/public/sms/v1/send/";
        $this->postCurl($url, $args);
    }

    public function postCurl($url, $postFields)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function BulkSmsNigeria($phone, $msg, $senderId, $token)
    {
        $url = "https://www.bulksmsnigeria.com/api/v1/sms/create?api_token=" . $token . "&from=" . $senderId . "&to=" . $phone . "&body=" . urlencode($msg);
        $this->getCurl($url);
    }

    public function BulkSmsZamtel($phone, $msg, $apiKey, $senderId)
    {
        $phone = substr($phone, 1);
        $msg = urlencode($msg);
        $url = "http://bulksms.zamtel.co.zm/api/sms/send/batch?message=" . $msg . "&key=" . $apiKey . "&contacts=" . $phone . "&senderId=" . $senderId;
        $response = file_get_contents($url);
    }

    public function SslWireLess($phone, $msg, $apiKey, $auth_token, $api_secret_key)
    {
        $user = $auth_token;
        $pass = $api_secret_key;
        $sid = $apiKey;
        $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php";
        $message = urlencode($msg);
        $to = $phone;

        $param = "user=$user&pass=$pass&sms[0][0]=$to&sms[0][1]=$message&sms[0][2]=123456789&sid=$sid";
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HEADER, 0);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_POST, 1);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($crl);
        curl_close($crl);
    }

    public function MYTELESOM($phone, $msg, $apiKey, $auth_token, $api_secret_key, $sender)
    {
        $username = $auth_token;
        $password = $api_secret_key;
        $key = $apiKey;
        $from = $sender;
        $to = "0" . substr($phone, -9);// Format used in sms
        $date = date('d/m/Y');

        $hashkey = $username . "|" . $password . "|" . $to . "|" . $msg . "|" . $from . "|" . $date . "|" . $key;
        $hashkey = strtoupper(md5($hashkey));
        $message = urlencode($msg);
        $url = "http://gateway.mytelesom.com/gw/" . strtolower($from) . "/sendsms?username=" . $username . "&password=" . $password . "&to=" . $to . "&msg=" . $message . "&from=" . $from . "&key=" . $hashkey;
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HEADER, 0);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_POST, 0);
        $response = curl_exec($crl);
        curl_close($crl);
    }

    public function SELCOMSMS($phone, $msg, $api_key, $api_secret_key)
    {
        $phone = str_replace('+', '', $phone);
        $url = "https://gw.selcommobile.com:8443/bin/send.json?USERNAME=" . $api_key . "&PASSWORD=" . $api_secret_key . "&DESTADDR=" . $phone . "&MESSAGE=" . urlencode($msg);
        $ch = curl_init();
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $body = '{}';
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $result = curl_exec($ch);
        $error = curl_error($ch);
    }

    public function Nsemfua($phone, $msg, $api_key, $sender)
    {
        $phone = str_replace('+', '', $phone);
        $url = "http://nsemfua.com/portal/sms/api?action=send-sms&api_key=" . $api_key . "&to=" . $phone . "&from=" . $sender . "&sms=" . urlencode($msg);
        $this->getCurl($url);
    }

    public function Plivo($phone, $msg, $authId, $authToken, $sender)
    {
        $client = new RestClient($authId, $authToken);
        try {
            $client->messages->create(
                $sender, #from
                [$phone], #to
                $msg #text
            );
        } catch (Exception $e) {
            echo "The message was not sent. Error: " . $e->getMessage() . "\n";
        }
    }

    public function BulkSmsBD($phone, $msg, $username, $password)
    {
        $phone = str_replace('+880', '', $phone);  //country specific sms gateway for bangladesh
        $url = "http://66.45.237.70/api.php";
        $data = array(
            'username' => "$username",
            'password' => "$password",
            'number' => "$phone",
            'message' => "$msg"
        );
        $data = http_build_query($data);
        $this->postCurl($url, $data);
    }

    public function MULTITEXTER($phone, $msg, $api_user, $api_password, $sender)
    {
        $phone = str_replace('+', '', $phone);
        $message = urlencode($msg);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://www.multitexter.com/tools/geturl/Sms.php?username=" . $api_user . "&password=" . $api_password . "&sender=" . $sender . "&message=" . $message . "&flash=1&recipients=" . $phone,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "Postman-Token: 6eb3c471-922a-4008-8688-f278d4aa0038",
                "cache-control: no-cache"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }

    public function Msg91($phone, $msg, $token, $sender)
    {
        $phone = str_replace('+', '', $phone);
        $message = urlencode($msg);
        $url = "https://api.msg91.com/api/sendhttp.php?mobiles=" . $phone . "&authkey=" . $token . "&route=4&sender=" . $sender . "&message=" . $message . "&country=91";
        $this->getCurl($url);
    }

    public function OutReach($phone, $msg, $username, $password, $masking)
    {
        $phone = str_replace('+', '', $phone);
        $type = "xml";
        $lang = "English";
        $msg = urlencode($msg);
        $data = "id=" . $username . "&pass=" . $password . "&msg=" . $msg . "&to=" . $phone . "&lang=" . $lang . "&mask=" . $masking . "&type=" . $type;
        $url = 'http://www.outreach.pk/api/sendsms.php/sendsms/url';
        $this->postCurl($url, $data);
    }

    public function BudgetSms($phone, $msg, $username, $handle, $senderId, $userId)
    {
        $phone = str_replace('+', '', $phone);
        $message = urlencode($msg);
        $senderId = urlencode($senderId);
        $url = "https://api.budgetsms.net/sendsms/?username=" . $username . "&userid=" . $userId . "&handle=" . $handle . "&msg=" . $message . "&from=" . $senderId . "&to=" . $phone;
        $this->getCurl($url);
    }

    public function ClickATellApi($phone, $msg, $username, $password, $apiId)
    {
        $phone = str_replace('+', '', $phone);
        $message = urlencode($msg);
        $url = "https://api.clickatell.com/http/sendmsg?user=" . $username . "&password=" . $password . "&api_id=" . $apiId . "&to=" . $phone . "&text=" . $message;
        $this->getCurl($url);
    }

    public function DataSoft($username, $senderId, $password, $phone, $message)
    {
        $phone = str_replace('+', '', $phone);
        $message = urlencode($message);
        $senderId = urlencode($senderId);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://196.202.134.90/dsms/webacc.aspx?user=$username&pwd=$password&Sender=$senderId&smstext=$message&Nums=$phone",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Host: 196.202.134.90",
                "Postman-Token: 750c7a4b-7b60-49db-88cd-86d2c144af4d,9449895b-bac7-44a5-8595-752cb7326de9",
                "User-Agent: PostmanRuntime/7.20.1",
                "cache-control: no-cache"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }

    public function ShamelSms($username, $password, $phone, $msg, $senderName)
    {
        $phone = str_replace('+', '', $phone);
        $url = 'http://www.shamelsms.net/api/httpSms.aspx?' . http_build_query(array(
                'username' => $username,
                'password' => $password,
                'mobile' => $phone,
                'message' => $msg,
                'sender' => $senderName,
                'unicodetype' => 'U'
            ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $content = curl_exec($ch);
        $err = curl_error($ch);
//        echo $content;
    }

    public function SMSLive247($mainAccount, $subAccount, $subAccountPass, $sender, $phone, $msg)
    {
        $phone = str_replace('+', '', $phone);
        $msg = urlencode($msg);
        $url = "http://www.smslive247.com/http/index.aspx?cmd=sendquickmsg&owneremail=" . $mainAccount . "&subacct=" . $subAccount . "&subacctpwd=" . $subAccountPass . "&message=" . $msg . "&sender=" . $sender . "&sendto=" . $phone . "&msgtype=0";
        $this->getCurl($url);
    }

    public function Infobip($username, $password, $phone, $msg)
    {
        $phone = str_replace('+', '', $phone);
        $msg = urlencode($msg);
        $url = "https://qzepm.api.infobip.com/sms/1/text/query?username=" . $username . "&password=" . $password . "&to=" . $phone . "&text=" . $msg;
        $this->getCurl($url);
    }

    public function TWWWireless($username, $password, $sender, $phone, $msg)
    {
        $phone = str_replace('+', '', $phone);
        $msg = urlencode($msg);
        $url = "http://webservices2.twwwireless.com.br/reluzcap/wsreluzcap.asmx/EnviaSMS?NumUsu=" . $username . "&Senha=" . $password . "&SeuNum=" . $sender . "&Celular=" . $phone . "&Mensagem=" . $msg;
        $this->getCurl($url);
    }

    public function Sms123($api, $companyName, $phone, $msg)
    {
        $phone = str_replace('+', '', $phone);
        $msg = $companyName . ' ' . urlencode($msg);
        $url = "https://www.sms123.net/api/send.php?apiKey=" . $api . "&recipients=" . $phone . "&messageContent=" . $msg;
        $this->getCurl($url);
    }

    public function BulkSMS($userName, $password, $phone, $msg)
    {
        $phone = str_replace('+', '', $phone);
        $msg = urlencode($msg);
        $auth = base64_encode($userName . ':' . $password);

        $url = "https://api.bulksms.com/v1/messages/send?to=" . $phone . "&body=" . $msg;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Authorization: Basic " . $auth,
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Host: api.bulksms.com",
                "Postman-Token: edc10af9-b90f-4509-a7b1-cd5acf4f9a56,9cf6ad82-af55-4c99-b126-61b31c3a7d4e",
                "User-Agent: PostmanRuntime/7.20.1",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function textingHouse($userName, $password, $phone, $msg, $sender = "")
    {
        $phone = str_replace("+", "", $phone);
        $data = array(
            'user' => $userName,
            'pass' => $password,
            'cmd' => 'sendsms',
            'to' => $phone,
            'txt' => $msg,
            'iscom' => 'N',
            'from' => $sender
        );
        $url = 'https://api.textinghouse.com/http/v1/do';
        $ch = curl_init($url);
        $postString = http_build_query($data, '', '&');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function mobile360Sms($userName, $password, $sender, $phone, $msg)
    {
        $phone = str_replace("+", '', $phone);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.mobile360.ph/v3/api/broadcast",//"https://smsapi.mobile360.ph/v2/api/broadcast",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\"username\": \"$userName\",\n\"password\": \"$password\",\n\"msisdn\" : \"$phone\",\n\"content\" : \"$msg\",\n\"shortcode_mask\" : \"$sender\",\n\"is_intl\" : false\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }

    public function facilitaMovel($username, $password, $phone, $msg)
    {
        $phone = str_replace("+", '', $phone);
        $msgEncoded = urlencode($msg);
        $urlChamada = "https://www.facilitamovel.com.br/api/simpleSend.ft?user=$username&password=$password&destinatario=$phone&msg=" . $msgEncoded;
        $result = file_get_contents($urlChamada);
    }

    public function eSMS($api_key, $secret_key, $sender, $phone, $msg)
    {
        $phone = str_replace("+", '0', $phone);
        $msg = "($sender) " . $msg;
        $msgEncoded = urlencode($msg);
        $sender = urlencode($sender);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_get?Phone=$phone&Content=$msgEncoded&ApiKey=$api_key&SecretKey=$secret_key&Brandname=$sender&SmsType=2",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: ASP.NET_SessionId=uvwdn4pjvixphfn1qzgghb05'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }

    public function INFOBIPSMS($api_key, $base_url, $sender, $phone, $msg)
    {
        $phone = str_replace("+", '', $phone);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$base_url/sms/2/text/advanced",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"messages": [{"from": "' . $sender . '","destinations": [{"to": "' . $phone . '"}],"text": "' . $msg . '"}]}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: App ' . $api_key,
                'Content-Type: application/json',
                'Accept: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }

    public function iSmart($api_key, $secret_key, $phone, $msg)
    {
        $phone = str_replace("+", '', $phone);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.ismartsms.net/iBulkSMS/HttpWS/SMSDynamicAPI.aspx?UserId='.$api_key.'&Password='.$secret_key.'&MobileNo='.$phone.'&Message='.urlencode($msg).'&Lang=0&FLashSMS=N',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: ASP.NET_SessionId=lbxvoo45xf0hl445hdffa345'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }

    public function smsPortal($api_key, $secret_key, $phone, $msg)
    {

        $curl = curl_init();
        $base64_secret = base64_encode("$api_key:$secret_key");
        // p($base64_secret);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://rest.smsportal.com/v1/Authentication',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic '.$base64_secret
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // p($response);
        $returned_data = json_decode($response,true);
        $token = isset($returned_data['token']) ? $returned_data['token'] : "";
        // echo $response;

        // message send part
        $curl = curl_init();
        $phone = str_replace("+", '', $phone);
        $data = json_encode(['Messages'=>[['Content'=>$msg,'Destination'=>$phone]]]);
// p($data);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://rest.smsportal.com/v1/BulkMessages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer '.$token
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return true;
    }

    public function ArkeselSMS($api_key, $sender, $phone, $msg)
    {
        $phone = str_replace('+', '', $phone);
        $msg = urlencode($msg);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sms.arkesel.com/sms/api?action=send-sms&api_key=$api_key&to=$phone&from=$sender&sms=$msg",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: XSRF-TOKEN=eyJpdiI6Inh6Q1RsWnhMOXZ4TGVUbEc3SnJMcUE9PSIsInZhbHVlIjoicEFFcmJiQU8yZGlWZk9oQWV0T2d5VGMzZ21OaEY1b096VUYyanNwb205d1oybnI3a2ZwR3JLZW9YYU93Q2ZGd21vMy82TEZhajVPd2pQVDdPNXFDWlFnVjNqckRwcG0wNkxTVmZtNFUxL3ZzR0QzV0RWS2ZXWVRwelZDbjh4VUEiLCJtYWMiOiJiZDFmNjg4ZjU0MDIyOGQyYThiNWIyNmEzZGJhMmY0YTNmMGNmYmQwZDBmYjRjZjZjMjQwMWViOGE5N2VkNzZiIn0%3D; arkesel_sms_messenger_session=eyJpdiI6Im5VM05OT2tGNkdEQnZ2K1YweGJwYVE9PSIsInZhbHVlIjoiczhMcFlkMEFCSkd5L0NLQzVWVlpsVzFPVmE5aG9FOGthRWFFeXl6UWNSbWdKRmdOTnJJY01GYkNDS0ZhaW1YbDVMMkwveDNWcGNsZjFNZnBsRW82Y29EbFB5YUtVYStpekFRK251ejBjV2FPUGw2bitFMHlEYnc5VUl4UE5RMU4iLCJtYWMiOiIzOGRhYzFiYzc3NjIzYTA0NjVlNmMzY2Y4MzNiZjQyZmM2MTNhYTE1NzU1MTU1ODk3MmY3Y2RkOGI2ZDAxNWM3In0%3D'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }

    public function BurstSMS($api_key, $sender, $phone, $msg)
    {
        $phone = str_replace('+', '', $phone);
        $msg = urlencode($msg);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.transmitsms.com/send-sms.json?message=$msg&to=$phone&from=$sender",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic $api_key"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }

    public function SMSBOX($userName, $password, $customerId, $sender_id, $phone, $message){
        $phone = str_replace('+', '', $phone);
        $message = urlencode($message);
        $url = 'https://www.smsbox.com/SMSGateway/Services/Messaging.asmx/Http_SendSMS?username='.$userName.'&password='.$password.'&customerId='.$customerId.'&senderText='.$sender_id.'&messageBody='.$message.'&recipientNumbers='.$phone.'&defdate=&isBlink=false&isFlash=false';
        $this->getCurl($url);
    }

    // to-do en client's customised sms gateway
    public function whatsAppTodo($phone, $message)
    {
        $phone = str_replace('+', '', $phone);
        $data = [
            'phone'=>$phone,
            'message'=>$message,
        ];
        $data = json_encode($data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://api.norrisgps.com/teu/norris.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function myMobileApi($api_key, $secret_key, $phone, $msg)
    {
        $curl = curl_init();
        $base64_secret = base64_encode("$api_key:$secret_key");
        // p($base64_secret);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://rest.mymobileapi.com/v1/Authentication',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic '.$base64_secret
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // p($response);
        $returned_data = json_decode($response,true);
        $token = isset($returned_data['token']) ? $returned_data['token'] : "";
        // echo $response;

        // message send part
        $curl = curl_init();
        $phone = str_replace("+", '', $phone);
        $data = json_encode(['Messages'=>[['Content'=>$msg,'Destination'=>$phone]]]);
// p($data);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://rest.mymobileapi.com/v1/BulkMessages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer '.$token
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return true;
    }

    public function SMSCTP($userName, $password, $sender_id, $phone, $message)
    {
        $phone = str_replace('+', '', $phone);
        $message = urlencode($message);
        $url = "http://smsctp3.eocean.us:24555/api?action=sendmessage&username=$userName&password=$password&recipient=$phone&originator=$sender_id&messagedata=$message";
        $this->getCurl($url);
    }

    public function SMSDEV($api_key, $phone, $message)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.smsdev.com.br/v1/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'key' => $api_key,
                'type' => '9',
                'number' => str_replace('+', '', $phone),
                'msg' => $message
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function MuthoFun($userName, $password, $phone, $message)
    {
        $message = urlencode($message);
        $phone = str_replace('+', '', $phone);

        $url = "http://developer.muthofun.com/sms.php?username=$userName&password=$password&mobiles=$phone&sms=$message&uniccode=1";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data);
    }

    public function KingSms($userName, $password, $phone, $message)
    {
        $phone = str_replace('+', '', $phone);
        $message = urlencode($message);
        $url = "http://painel.kingsms.com.br/kingsms/api.php?acao=sendsms&login=$userName&token=$password&numero=$phone&msg=$message";
        $this->getCurl($url);
    }

    public function GlobeLabs($app_id, $app_secret, $short_code, $pass_phrase, $phone, $message)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://devapi.globelabs.com.ph/smsmessaging/v1/outbound/".$short_code."/requests?app_id=".$app_id."&app_secret=".$app_secret."&passphrase=".$pass_phrase ,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"outboundSMSMessageRequest\": { \"senderAddress\": \"".$short_code."\", \"outboundSMSTextMessage\": {\"message\": \"".$message."\"}, \"address\": \"".$phone."\" } }",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function MultiTexterSms($userName, $password, $sender, $phone, $message)
    {
        $message = urlencode($message);
        $url = "https://app.multitexter.com/v2/app/sms?email=$userName&password=$password&message=$message&sender_name=$sender&recipients=$phone";
        $this->getCurl($url);
    }

    public function MessageBird($key, $sender, $phone, $message)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://rest.messagebird.com/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('recipients' => $phone,'originator' => $sender,'body' => $message),
            CURLOPT_HTTPHEADER => array(
                "Authorization: AccessKey $key"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
    }

    public function FloppySend($api_key, $sender, $phone, $message)
    {
        $phone = str_replace('+', '', $phone);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.floppy.ai/sms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "to=$phone&from=$sender&Dcs=0&Text=$message",
            CURLOPT_HTTPHEADER => array(
                "X-api-key: $api_key",
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
    }

    public function RichCommunication($auth_key, $senderId, $phone, $message)
    {
        $message = urlencode($message);
        $phone = str_replace('+', '', $phone);
        $url = "https://richcommunication.dialog.lk/api/sms/inline/send?q=$auth_key&destination=$phone&message=$message&from=$senderId";
        $this->getCurl($url);
    }


    public function smsProNikita($username,$password,$sender,$phone, $message)
    {
        $phone = str_replace('+', '', $phone);
        $url = 'https://smspro.nikita.kg/api/message';
        $id = time();
        /*        $xml_data = '<?xml version="1.0" encoding="UTF-8"?><message><login>'.$username.'</login><pwd>'.$password.'</pwd><id>'.$id.'</id><sender>'.$sender.'</sender><text>'.$message.'</text><time>'.time().'</time><phones><phone>'.$phone.'</phone></phones><test>1</test></message>';*/
        $xml_data = '<?xml version="1.0" encoding="UTF-8"?><message><login>'.$username.'</login><pwd>'.$password.'</pwd><id>'.$id.'</id><sender>'.$sender.'</sender><text>'.$message.'</text><time></time><phones><phone>'.$phone.'</phone></phones></message>';
        //  p($xml_data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        //p($output);
        curl_close($ch);
    }

    // smspro nikita gateway
    public function montyMobile($phone = null, $message = null, $username, $password, $sender)
    {
//        $url = "https://sms.montymobile.com/API/SendSMS?username=ame305a@gmail.com&apiId=Z4E62EnL&json=True&destination=9461764927&source=251911679409&text=(text)";
        $url = "https://sms.montymobile.com/API/SendSMS?username=".$username."&apiId=".$password."&json=True&destination=".$phone."&source=251911679409&text=(text)";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
    }

    public function SMSTo($phone, $message, $api_key, $sender){
        //zx1lYXZX2qt1DuStJf8tPyGDzmMO4Fu5
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sms.to/sms/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\n    \"message\": \"$message\",\n    \"to\": \"$phone\",\n    \"sender_id\": \"$sender\"    \n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: Bearer $api_key"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        //
    }
    // for gaari grocery
    public function telesom($username, $passowrd, $from,$to,$message,$key){

        $to = str_replace('+', '', $to);
        $curl = curl_init();
        $message = str_ireplace(" ","%20",$message);
        $curentDate = date('d/m/Y');
        $hashkey = strtoupper(md5($username ."|".$passowrd ."|".$to."|".$message ."|".$from ."|".$curentDate ."|".$key));
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sms.mytelesom.com/index.php/Gateway/sendsms/".$from."/".$message."/" .$to."/" .$hashkey,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    }

    // for oro project
    public function sudacellBulkSms($username, $passowrd, $from,$to,$message){

        $to = str_replace('+', '', $to);
        $curl = curl_init();
        $message = str_ireplace(" ","%20",$message);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://196.202.134.90/Smsbulk/webacc.aspx?user=".$username."&pwd=".$passowrd."&Sender=".$from."&smstext=".$message."&Nums=".$to,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

    }

    public function BulkSMSServices($username,$password,$sender,$phone,$message){
        $message = urlencode($message);
        $phone = str_replace('+', '', $phone);
        $url = 'https://www.bulksmsservices.net/components/com_spc/smsapi.php?username='.$username.'&password='.$password.'&sender='.$sender.'&recipient='.$phone.'&message='.$message;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        echo $response;
    }

    public function ORANGESMS($phone, $message, $senderName,$senderAddress, $authToken){
        // dd($phone,$message,$senderAddress,$senderName,$authToken);
        $token = $this->getAccessTokenOrangeSms($authToken);
        /*$mobile = '+' . $ISOCode . $phone;*/
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B".$senderAddress."/requests",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\t\"outboundSMSMessageRequest\": {\n \"address\": \"tel:$phone\",\n \"outboundSMSTextMessage\":{\n \"message\": \"$message\"\n },\n \"senderAddress\": \"tel:+$senderAddress\",\n \"senderName\": \"$senderName\"\n }\n}",
            CURLOPT_HTTPHEADER => array(
                "Authorization:  Bearer $token",
                "Content-Type:  application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        // dd($response);
    }

    public function getAccessTokenOrangeSms($token){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.orange.com/oauth/v3/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=client_credentials",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic $token",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        // dd(json_decode($response));
        return json_decode($response)->access_token;
    }

    public function ClickSend($username,$api_key,$phone,$message){
        $message = urlencode($message);
        $url = 'https://api-mapper.clicksend.com/http/v2/send.php?method=http&username='.$username.'&key='.$api_key.'&to='.$phone.'&message='.$message;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        echo $response;
    }

    public function Sinch($service_plan_id,$bearer_token,$send_from,$phone,$message){
        // $service_plan_id = "c6abb7f9074f4877adc71c3158145275";
        // $bearer_token = "2abc56d109a04c0393630c405d24867c";

        //Any phone number assigned to your API
        // $send_from = "+447520651008";
        //May be several, separate with a comma ,
        // $recipient_phone_numbers = "+919690603004";
        // $message = "Test message to {$recipient_phone_numbers} from {$send_from}";

        // Check recipient_phone_numbers for multiple numbers and make it an array.
        if(stristr($phone, ',')){
        $phone = explode(',', $phone);
        }else{
        $phone = [$phone];
        }

        // Set necessary fields to be JSON encoded
        $content = [
        'to' => array_values($phone),
        'from' => $send_from,
        'body' => $message
        ];

        $data = json_encode($content);

        $ch = curl_init("https://us.sms.api.sinch.com/xms/v1/{$service_plan_id}/batches");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BEARER);
        curl_setopt($ch, CURLOPT_XOAUTH2_BEARER, $bearer_token);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);

        if(curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            // echo $result;
        }
        curl_close($ch);
    }

    public function SmsBus($username,$passowrd,$sender,$phone,$message){
        $message = urlencode($message);
        $sender = urlencode($sender);
        $phone = str_replace('+', '', $phone);
        $url = 'https://www.lesmsbus.com:7170/ines.smsbus/smsbusMt?to='.$phone.'&text='.$message.'&username='.$username.'&password='.$passowrd.'&from='.$sender;

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Cookie: JSESSIONID=32AE952222887E12B9FE904CC0F92DDF'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
    }

    public function MessageMedia($api_key, $api_secret,$phone,$message){
        $token = base64_encode($api_key.':'.$api_secret);
        $data = [
            'messages' => [
                [
                    'content' => $message,
                    'destination_number' => $phone
                ]
            ]
        ];
        $data = \GuzzleHttp\json_encode($data);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.messagemedia.com/v1/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic '.$token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
    }

    // sms nigeria bulk sms
    public function NigeriaBulkSms($username,$passoword,$sender,$phone,$message){

        $message = urlencode($message);
        $sender = urlencode($sender);
        $phone = str_replace('+', '', $phone);
        $url = 'https://portal.nigeriabulksms.com/api/?mobiles='.$phone.'&message='.$message.'&username='.$username.'&password='.$passoword.'&sender='.$sender;

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Cookie: JSESSIONID=32AE952222887E12B9FE904CC0F92DDF'
        ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        // echo $response;
    }

    public function AirtelBulkSMS($username,$password,$sender_id,$phone,$message){
        $phone = str_replace('+', '', $phone);
        $message = urlencode($message);
        $url = "http://www.airtel.sd/bulksms/webacc.aspx?user=".$username."&pwd=".$password."&smstext=".$message."&Sender=".$sender_id."&Nums=".$phone;
        $this->getCurl($url);
    }

    public function OrangeSMSPro($login,$api_access_key,$token,$subject,$signature,$recipient,$content)
    {
        $recipient = str_replace('+', '', $recipient);
        $content = urlencode($content);
        $subject = urlencode($subject);
        $signature = urlencode($signature);
        $timestamp=time();
        $msgToEncrypt=$token . $subject . $signature . $recipient . $content . $timestamp;
        $key=hash_hmac('sha1', $msgToEncrypt, $api_access_key);
        //$key=md5($msgToEncrypt.$api_access_key); //si vous utilisez MD5
        $uri='https://api.orangesmspro.sn:8443/api?token='.$token.'&subject='.$subject.'&signature='.$signature.'&recipient='.$recipient.'&content='.$content.'&timestamp='.$timestamp.'&key='.$key;
        $baisc_auth = base64_encode($login.':'.$token);

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $uri,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '.$baisc_auth
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
    }

    public function SmsZedekaa($apiKey,$clientId,$sender,$phone,$message){
        $message = urlencode($message);
        $sender = urlencode($sender);
        $phone = str_replace('+', '', $phone);
        $url = "http://dashboard.smszedekaa.com:6005/api/v2/SendSMS?SenderId=".$sender."&Is_Unicode=false&Is_Flash=false&Message=".$message."&MobileNumbers=".$phone."&ApiKey=".$apiKey."&ClientId=".$clientId;
        $this->getCurl($url);
    }

    // public function chkms(){
    //     $this->SmsZedekaa('I61TAMc7Qx28ejjSfV1Xxi8fQ0XvM8lhKF7x/7qYHhc=','1da38972-9796-4787-9b16-c4194634b199','2Go','+22890064664','first testing message for otp verification');
    // }


    public function appNotifyLk($user_id,$api_key,$sender,$phone,$message){
        $message = urlencode($message);
        $sender = urlencode($sender);
        $phone = str_replace('+', '', $phone);
        $url = "https://app.notify.lk/api/v1/send?user_id=".$user_id."&api_key=".$api_key."&sender_id=".$sender."&to=".$phone."&message=".$message;
        $this->getCurl($url);
    }
    public function BeemAfrica($apiKey,$secretKey,$phone,$message){
//        $message = urlencode($message);
        $phone = str_replace('+', '', $phone);
        $postData = array(
            'source_addr' => 'INFO',
            'encoding'=>0,
            'message' => $message,
            'recipients' => [array('recipient_id' => '1','dest_addr'=>$phone)]
        );

        $Url ='https://apisms.beem.africa/v1/send';

        $ch = curl_init($Url);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization:Basic ' . base64_encode("$apiKey:$secretKey"),
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        $response = curl_exec($ch);

        if($response === FALSE){
            echo $response;
            die(curl_error($ch));
        }
        curl_close($ch);
    }

    public function MultiTexterV2($apiKey, $sender, $phone, $message){
//        $message = urlencode($message);
        $phone = str_replace('+', '', $phone);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.multitexter.com/v2/app/sendsms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('message' => $message,'sender_name' => $sender,'recipients' => $phone, 'forcednd' => 1),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$apiKey,
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    public function LinxSMS($username, $password, $sender, $phone, $message){
        $message = urlencode($message);
        $phone = str_replace('+', '', $phone);
        $url = "https://www.5linxsms.com/api/sendsms.php?user=".$username."&pass=".$password."&receiver=".$phone."&sender=".$sender."&message=".$message;
        $this->getCurl($url);
    }

    public function BulkSMSDhiraagu($username, $password, $phone, $message){
        $message = urlencode($message);
        $phone = str_replace('+', '', $phone);
        $url = "https://bulkmessage.dhiraagu.com.mv/jsp/receiveSMS.jsp?userid=".$username."&password=".$password."&to=".$phone."&text=".$message;
        $this->getCurl($url);
    }

    public function CloudWebSMS($apiKey, $apiToken, $sender, $phone, $message){
        $message = urlencode($message);
        $phone = str_replace('+', '', $phone);
        $url = "http://cloud.websms.lk/smsAPI?sendsms&apikey=".$apiKey."&apitoken=".$apiToken."&type=sms&from=".$sender."&to=".$phone."&text=".$message;
        $this->getCurl($url);
    }

    public function SMSPoh($apiKey, $sender, $phone, $message){
        $data = [
            'to' => $phone,
            'message' => $message,
            'sender' => $sender
        ];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://smspoh.com/api/v2/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data,true),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$apiKey,
                'Content-Type: application/json',
                'Cookie: _csrf=aI7auZ_tn4tUw4qaJcYXZNBcXJojosPw'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
    }
}
