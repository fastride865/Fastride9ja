<?php

namespace App\Http\Controllers\PaymentMethods\PayPhone;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use Illuminate\Support\Facades\Validator;
use App\Traits\ContentTrait;

class PayPhoneController extends Controller
{
    use ApiResponseTrait, MerchantTrait, ContentTrait;

    public function __construct()
    {
    }

    //this payment gateway works in 4 stpes
    //STEP 1 check the country regions which is supporting by payphone
    // STEP 2 Verify the user, who is registered on payphoe or not

    public function validateUser($request,$payment_option_config,$calling_from = "USER")
    {
        // p($calling_from);
        if($calling_from == "DRIVER")
        {
            $driver = $request->user('api-driver');
            $code = $driver->Country->phonecode;
            $country_code = str_replace("+","",$code);
            $currency = $driver->Country->isoCode;
            $phone_number = str_replace($code,"",$driver->phoneNumber);
            // p($phone_number);
            $logged_user = $driver;
        }
        else
        {
            $user = $request->user('api');
            $code = $user->Country->phonecode;
            $country_code = str_replace("+","",$code);
            $currency = $user->Country->isoCode;
            $phone_number = str_replace($code,"",$user->UserPhone);
            $logged_user = $user;
        }

        $string_file = $this->getStringFile(NULL,$logged_user->Merchant);
        try {

            // STEP 1
            $curl = curl_init();
            $token = $payment_option_config->api_secret_key;
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://pay.payphonetodoesposible.com/api/Regions',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$token,
                    'Cookie: ARRAffinity=932c5351a290b46f876f6d4453f2bdc8625e8682cbfa134e04eb88e0677cb1fc; ARRAffinitySameSite=932c5351a290b46f876f6d4453f2bdc8625e8682cbfa134e04eb88e0677cb1fc'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $arr_region = array_column(json_decode($response,true),'prefixNumber');
            // p($country_code);
            // var_dump(!in_array($country_code,$arr_region));
            // p($arr_region);
            if(!in_array($country_code,$arr_region))
            {
                $message = trans("$string_file.payphone_not_supported");
                throw new \Exception($message);
            }
            $locale = $request->header('locale');
// p('https://pay.payphonetodoesposible.com/api/Users/'.$phone_number.'/region/'.$country_code);
            //STEP 2 Check user's phone number which is registered on payphone or not
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://pay.payphonetodoesposible.com/api/Users/'.$phone_number.'/region/'.$country_code,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$token,
                    'Cookie: ARRAffinity=932c5351a290b46f876f6d4453f2bdc8625e8682cbfa134e04eb88e0677cb1fc; ARRAffinitySameSite=932c5351a290b46f876f6d4453f2bdc8625e8682cbfa134e04eb88e0677cb1fc',
                    'Accept-Language: '.$locale,
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($response,true);
            // p($result);
            if(isset($result['errorCode']))
            {
                $message = trans("$string_file.number_not_registered_on_payphone");
                throw new \Exception($message);
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }
    // STEP 3 Send the payment request to payphone
    // STEP 4 check the payment status by transaction id
    public function paymentRequest($request,$payment_option_config,$additional_data,$calling_from = "USER")
    {
        try {
            if($calling_from == "DRIVER")
            {
                $driver = $request->user('api-driver');
                $code = $driver->Country->phonecode;
                $country_code = str_replace("+","",$code);
                $currency = $driver->Country->isoCode;
                $phone_number = str_replace($code,"",$driver->phoneNumber);
                $logged_user = $driver;
                $nationality = $driver->Country->country_code;
                $user_merchant_id = $driver->driver_merchant_id;
                $first_name = $driver->first_name;
                $last_name = $driver->last_name;
                $email = $driver->email;
                $id = $driver->id;
                $merchant_id = $driver->merchant_id;
            }
            else
            {
                $user = $request->user('api');
                $code = $user->Country->phonecode;
                $country_code = str_replace("+","",$code);
                $currency = $user->Country->isoCode;
                $phone_number = str_replace($code,"",$user->UserPhone);
                $logged_user = $user;
                $nationality = $user->Country->country_code;
                $user_merchant_id = $user->user_merchant_id;
                $first_name = $user->first_name;
                $last_name = $user->last_name;
                $email = $user->email;
                $id = $user->id;
                $merchant_id = $user->merchant_id;
            }

//            $string_file = $this->getStringFile(NULL,$logged_user->Merchant);
//            $user = $request->user('api');
            $token = $payment_option_config->api_secret_key;
//            $code = $user->Country->phonecode;
//            $country_code = str_replace("+","",$code);
//            $currency = $user->Country->isoCode;
//            $nationality = $user->Country->country_code;
            $phone_number = str_replace($code,"",$phone_number);
            $phone_number = '0'.$phone_number;
            $user_merchant_id = (string)$user_merchant_id;

            $client_transaction_id = (string)time();

            $amount = $additional_data['amount'];
            $final_amount = round_number($amount['amount'],2) * 100;
            $amount_with_tax = round_number($amount['amount_with_tax'],2) * 100;
            $amount_without_tax = round_number($amount['amount_without_tax'],2) *100;
            $tax = round_number($amount['tax'],2) * 100;

            $locale = $request->header('locale');
            $arr_param = [
                "nickName"=> (string)$first_name,
                "chargeByNickName"=> false,
                "phoneNumber"=> (string)$phone_number,
                "countryCode"=> (string)$country_code,
                "timeZone"=> 5,
                "lat"=> "-0.237057",
                "lng"=> "-79.193153",
                "clientUserId"=> "cYRpxiTINEuGPKPqLyeHSw",
                "reference"=> $user_merchant_id,
                "optionalParameter1"=>$user_merchant_id,
                "optionalParameter2"=> $user_merchant_id,
                "optionalParameter3"=>$user_merchant_id,
                "deferredType"=>$user_merchant_id,
                "responseUrl"=>route("payphone-response"),
                "amount"=> (int)$final_amount,
                "amountWithTax"=> (int)$amount_with_tax,
                "amountWithoutTax"=> (int)$amount_without_tax,
                "tax"=>(int)$tax,
                "service"=> (int)0,
                "tip"=> (int)0,
                "clientTransactionId"=>$client_transaction_id,
                "storeId"=> "23abd629-e467-480c-8b4c-3dab3edf88a0",
                "terminalId"=> "2",
                "currency"=> (string)$currency,
                "order"=>[
                    "billTo"=>[
                        "billToId"=> 2,
                        "address1"=> "df",
                        "address2"=> "sdf",
                        "country"=> "sd",
                        "state"=> "sdf",
                        "locality"=> "s",
                        "firstName"=>(string) $first_name,
                        "lastName"=> (string) $last_name,
                        "phoneNumber"=> (string) $phone_number,
                        "email"=> (string) $email,
                        "postalCode"=> "230207",
                        "customerId"=> (string) $user_merchant_id,
                        "ipAddress"=> "35.178.56.137"
                    ],
                    "lineItems"=>[
                        [
                            "productName"=> "Ride",
                            "unitPrice"=> (int)100,
                            "quantity"=> (int)1,
                            "totalAmount"=> (int)100,
                            "taxAmount"=> (int)0,
                            "productSKU"=> "tretdf",
                            "productDescription"=> "sf",
                            "shippingDestinationTypes"=> "sdfsd",
                            "passenger"=>[
                                "type"=> "1",
                                "status"=> "1",
                                "phone"=> (string)$phone_number,
                                "firstName"=> (string)$first_name,
                                "lastName"=> (string)$last_name,
                                "id"=> (string)$id,
                                "email"=> (string)$email,
                                "nationality"=> (string)$nationality
                            ]
                        ]
                    ],
                ],
            ];

            $arr_param = json_encode($arr_param);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://pay.payphonetodoesposible.com/api/Sale',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$arr_param,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$token,
                    'Content-Type: application/json',
                    'Cookie: ARRAffinity=932c5351a290b46f876f6d4453f2bdc8625e8682cbfa134e04eb88e0677cb1fc; ARRAffinitySameSite=932c5351a290b46f876f6d4453f2bdc8625e8682cbfa134e04eb88e0677cb1fc',
                    'Accept-Language: '.$locale,
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response,true);
            $log_data = ['request_param' => $arr_param,'request_result' => $response,'time'=>date('Y-m-d H:i:s')];
            \Log::channel('payphone_api')->emergency($log_data);
            if(isset($response['errorCode']))
            {
                throw new \Exception($response['errorCode'].' : '.$response['message']);
            }
            $transaction_id = isset($response['transactionId']) ? $response['transactionId']:NULL;
            DB::table('transactions')->insert([
                'status' => 1, // for user
                'reference_id' => $client_transaction_id,
                'card_id' => NULL,
                'merchant_id' => $merchant_id,
                'payment_option_id' => $payment_option_config->payment_option_id,
                'checkout_id' => NULL,
                'booking_id' => isset($additional_data['booking_id']) ? $additional_data['booking_id'] : NULL,
                'order_id' => isset($additional_data['order_id']) ? $additional_data['order_id'] :NULL,
                'handyman_order_id' => isset($additional_data['handyman_order_id']) ? $additional_data['handyman_order_id'] : NULL,
                'payment_transaction_id' => $transaction_id,
                'payment_transaction' => NULL,
                'request_status' => 1,
                'user_id' => $calling_from == "USER" ? $id : NULL,
                'driver_id' => $calling_from == "DRIVER" ? $id : NULL,
            ]);
            return [
                'status'=>'REQUEST_PROCESSING',
                'token'=>$token,
                'url'=>"https://pay.payphonetodoesposible.com/api/Sale/".$transaction_id
            ];
        }
        catch(\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }
    // get payphone response
    public function payPhoneResponse(Request $request)
    {
        $arr_param = $request->all();
        $log_data = ['request_response' => $arr_param,'time'=>date('Y-m-d H:i:s')];
        \Log::channel('payphone_api')->emergency($log_data);
    }
}