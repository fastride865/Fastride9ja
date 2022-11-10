<?php

namespace App\Http\Controllers\PaymentMethods\PaygateGlobal;
use App\Http\Controllers\Controller;
use hisorange\BrowserDetect\Exceptions\Exception;
use Illuminate\Http\Request;
use DB;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use Illuminate\Support\Facades\Validator;
use App\Traits\ContentTrait;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\Onesignal;


class PaygateGlobalController extends Controller
{
    use ApiResponseTrait, MerchantTrait, ContentTrait;

    public function __construct()
    {
    }




//    Card payment by webview

    public function paymentRequest($request,$payment_option_config,$calling_from){

        try {
            // check whether gateway is on sandbox or live
            $url = "https://paygateglobal.com/api/v1/pay";
//            $token_url = "https://api-uat.kushkipagos.com/card-async/v1/tokens";

            if($payment_option_config->gateway_condition == 1)
            {
                $url = "https://paygateglobal.com/api/v1/pay";
//                $token_url = "https://token.clover.com/v1/tokens";
            }

            // check whether request is from driver or user
            if($calling_from == "DRIVER")
            {
                $driver = $request->user('api-driver');
                $code = $driver->Country->phonecode;
                $country = $driver->Country;
                $country_name = $country->CountryName;
                $currency = "CLP";//$driver->Country->isoCode;
                $phone_number = $driver->phoneNumber;
                $logged_user = $driver;
                $user_merchant_id = $driver->driver_merchant_id;
                $first_name = $driver->first_name;
                $last_name = $driver->last_name;
                $email = $driver->email;
                $id = $driver->id;
                $merchant_id = $driver->merchant_id;
                $description = "driver wallet topup";
            }
            else
            {
                $user = $request->user('api');
                $code = $user->Country->phonecode;
                $country = $user->Country;
                $country_name = $country->CountryName;
                $currency = "CLP";//$user->Country->isoCode;
                $phone_number = $user->UserPhone;
                $logged_user = $user;
                $user_merchant_id = $user->user_merchant_id;
                $first_name = $user->first_name;
                $last_name = $user->last_name;
                $email = $user->email;
                $id = $user->id;
                $merchant_id = $user->merchant_id;
                $description = "payment from user";
            }

            $amount = (int)$request->amount;
            $transaction_id = $id.'_'.time();


            $payment_phone_number = $request->payment_phone_number;
            $phone_number_prefix = substr($payment_phone_number,0,2);
            //p($payment_phone_number);
            // TMONEY =[70,90,91,92,93]
            // flooz =[79,96,97,98,99]
            $network = in_array($phone_number_prefix,[70,90,91,92,93]) ? "TMONEY" : "FLOOZ";//FLOOZ, TMONEY
            $fields['amount'] = $amount;
            $fields['auth_token'] = $payment_option_config->api_secret_key;//"94291109-0cb6-4639-8d97-7ebb0ca3d7a4";
            $fields['phone_number'] = $request->payment_phone_number;
            $fields['identifier'] = $transaction_id;
            $fields['network'] = $network;
            $fields['description'] = $description.' '.$transaction_id;

            $fields = json_encode($fields);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$fields,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                ),
            ));
            $response = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($response,true);
            $data = [
                'type'=>'payment request',
                'data'=>$response
            ];
            \Log::channel('paygate_global_api')->emergency($data);
// p($response);
            if(isset($response['status']) && $response['status'] == 2)
            {
                throw new Exception("Invalid authentication token");
            }
            if(isset($response['status']) && $response['status'] == 4)
            {
                throw new Exception("Invalid parameters");
            }
            if(isset($response['status']) && $response['status'] == 6)
            {
                throw new Exception("Duplicates detected. A transaction with the same identifier already exists.");
            }


            $tx_reference =  $response['tx_reference'];
            if(isset($response['status']) && $response['status'] == 0)
            {
                // enter data
                DB::table('transactions')->insert([
                    'status' => 1, // for user
                    'card_id' => NULL,
                    'user_id' => $calling_from == "USER" ? $id : NULL,
                    'driver_id' => $calling_from == "DRIVER" ? $id : NULL,
                    'merchant_id' => $merchant_id,
                    'payment_option_id' => $payment_option_config->payment_option_id,
                    'checkout_id' => NULL,
                    'payment_transaction_id' => $transaction_id,
                    'payment_transaction' => json_encode($response),
                    'reference_id' => $tx_reference, // payment reference id
                    'amount' => $amount, // amount
                    'request_status' => 1,
                    'status_message' => "success",
                ]);

                return [];
            }

        }catch(\Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }


    public function webhook(Request $request)
    {


        $request_response = $request->all();
        $data = [
            'type'=>'webhook notification',
            'data'=>$request_response
        ];
        \Log::channel('paygate_global_api')->emergency($data);


        $status_url = "https://paygateglobal.com/api/v1/status";
        $auth_token = "94291109-0cb6-4639-8d97-7ebb0ca3d7a4"; 
        $tx_reference = $request_response['tx_reference']; //"687941";//
        $fields['auth_token'] = $auth_token;
        $fields['tx_reference'] = $tx_reference;
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_URL, $status_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array('Accept: application/json',
                'Content-Type: application/json',
            )
        );

        $response2 = curl_exec($ch);
        curl_close($ch);

        $response2 = json_decode($response2,true);
        // p($response2);


        $request_response = $request->all();
        $data = [
            'type'=>'webhook notification',
            'data'=>$request_response
        ];
        \Log::channel('paygate_global_api')->emergency($data);


        $transaction_table =  DB::table("transactions")->where('reference_id',$tx_reference)->first();
        //0: Successful payment 2: In progress 4: Expired 6: Canceled

        if(isset($response2['status']) && $response2['status'] == 0)
        {
            DB::table("transactions")->where('reference_id',$tx_reference)->update(['request_status' =>2,'status_message'=>"Successful payment"]);


            // credit user wallet & and sed notification to user for payment success
// p($transaction_table->merchant_id);
            if(!empty($transaction_table->user_id))
            {
                $paramArray = [
                    'amount'=> $transaction_table->amount,
                    'user_id'=> $transaction_table->user_id,
                    'narration'=> 2,
                ];
                WalletTransaction::UserWalletCredit($paramArray);

                // payment done notification
                $string_file  = $this->getStringFile($transaction_table->merchant_id);
                $message = trans("$string_file.payment_done");
                $title =trans("$string_file.payment_success");
                $data['notification_type'] = "PAYMENT_STATUS";
                $data['segment_data'] = [];
                $arr_param['user_id'] = $transaction_table->user_id;
                $arr_param['data'] = $data;
                $arr_param['message'] = $message;
                $arr_param['merchant_id'] = $transaction_table->merchant_id;
                $arr_param['title'] = $title; // notification title
                Onesignal::UserPushMessage($arr_param);

            }
            elseif(!empty($transaction_table->driver_id))
            {
                $paramArray = array(
                    'merchant_id' => $transaction_table->merchant_id,
                    'driver_id' => $transaction_table->driver_id,
                    'amount' => $transaction_table->amount,
                    'narration' => 2,
                    'platform' => 1,
                    'payment_method' => 4,
                );
                WalletTransaction::WalletCredit($paramArray);


                $string_file  = $this->getStringFile($transaction_table->merchant_id);
                $message = trans("$string_file.payment_done");
                $title =trans("$string_file.payment_success");
                $data['notification_type'] = "PAYMENT_STATUS";
                $data['segment_data'] = [];
                $arr_param['driver_id'] = $transaction_table->driver_id;
                $arr_param['data'] = $data;
                $arr_param['message'] = $message;
                $arr_param['merchant_id'] = $transaction_table->merchant_id;
                $arr_param['title'] = $title; // notification title
                Onesignal::DriverPushMessage($arr_param);

            }


        }
        else
        {
            //0: Successful payment 2: In progress 4: Expired 6: Canceled

            $message = "";
            switch ($response2['status'])
            {
                case 2 :
                    $message = "In progress";
                    break;
                case 4 :
                    $message = "Expired";
                    break;
                case 6 :
                    $message = "Canceled";
                    break;

            }
            DB::table("transactions")->where('reference_id',$tx_reference)->update(['request_status' =>3,'status_message'=>$message]);
            if(!empty($transaction_table->user_id))
            {

                // payment done notification
                $string_file  = $this->getStringFile($transaction_table->merchant_id);
                $message = trans("$string_file.payment_failed");
                $title =trans("$string_file.payment_failed_title");
                $data['notification_type'] = "PAYMENT_STATUS";
                $data['segment_data'] = [];
                $arr_param['user_id'] = $transaction_table->user_id;
                $arr_param['data'] = $data;
                $arr_param['message'] = $message;
                $arr_param['merchant_id'] = $transaction_table->merchant_id;
                $arr_param['title'] = $title; // notification title
                Onesignal::UserPushMessage($arr_param);

            }
            elseif(!empty($transaction_table->driver_id))
            {
                $string_file  = $this->getStringFile($transaction_table->merchant_id);
                $message = trans("$string_file.payment_failed");
                $title =trans("$string_file.payment_failed_title");
                $data['notification_type'] = "PAYMENT_STATUS";
                $data['segment_data'] = [];
                $arr_param['driver_id'] = $transaction_table->driver_id;
                $arr_param['data'] = $data;
                $arr_param['message'] = $message;
                $arr_param['merchant_id'] = $transaction_table->merchant_id;
                $arr_param['title'] = $title; // notification title
                Onesignal::DriverPushMessage($arr_param);
            }
        }
    }


}