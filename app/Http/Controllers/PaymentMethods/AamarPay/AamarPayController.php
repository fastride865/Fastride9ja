<?php

namespace App\Http\Controllers\PaymentMethods\AamarPay;
use App\Http\Controllers\Controller;
use hisorange\BrowserDetect\Exceptions\Exception;
use Illuminate\Http\Request;
use DB;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use Illuminate\Support\Facades\Validator;
use App\Traits\ContentTrait;

class AamarPayController extends Controller
{
    use ApiResponseTrait, MerchantTrait, ContentTrait;

    public function __construct()
    {
    }

    // aamarPay payment option
    public function aamarPayRequest($request,$payment_option_config,$calling_from){

        try {
            // check whether gateway is on sandbox or live
            $url = "https://sandbox.aamarpay.com/request.php";
            $submit_url = "https://sandbox.aamarpay.com";
            $secret_key = "dbb74894e82415a2f7ff0ec3a97e4183";
            $store_id = "aamarpaytest";
            if($payment_option_config->gateway_condition == 1)
            {
                $url = "https://secure.aamarpay.com/request.php";
                $submit_url = "https://secure.aamarpay.com";
                $secret_key = $payment_option_config->api_secret_key;
                $store_id = $payment_option_config->api_public_key;
            }


            // check whether request is from driver or user
            if($calling_from == "DRIVER")
            {
                $driver = $request->user('api-driver');
                $code = $driver->Country->phonecode;
                $country = $driver->Country;
                $country_name = $country->CountryName;
                $currency = $driver->Country->isoCode;
                $phone_number = str_replace($code,"",$driver->phoneNumber);
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
                $currency = $user->Country->isoCode;
                $phone_number = str_replace($code,"",$user->UserPhone);
                $logged_user = $user;
                $user_merchant_id = $user->user_merchant_id;
                $first_name = $user->first_name;
                $last_name = $user->last_name;
                $email = $user->email;
                $id = $user->id;
                $merchant_id = $user->merchant_id;
                $description = "payment from user";
            }

            $transaction_id = $id.'_'.time();
            $fields = array(
                'store_id' => $store_id, //store id will be aamarpay,  contact integration@aamarpay.com for test/live id
                'amount' => $request->amount, //transaction amount
                'payment_type' => 'VISA', //no need to change
                'currency' => $currency,  //currenct will be USD/BDT
                'tran_id' => $id.'_'.time(), //transaction id must be unique from your end
                'cus_name' => $first_name.'_'.$last_name,  //customer name
                'cus_email' => $email, //customer email address
                'cus_add1' => 'Dhaka',  //customer address
                'cus_add2' => 'Mohakhali DOHS', //customer address
                'cus_city' => 'Dhaka',  //customer city
                'cus_state' => 'Dhaka',  //state
                'cus_postcode' => '1206', //postcode or zipcode
                'cus_country' => $country_name,  //country
                'cus_phone' => $phone_number, //customer phone number
                'cus_fax' => 'Not¬Applicable',  //fax
                'ship_name' => 'ship name', //ship name
                'ship_add1' => 'House B-121, Road 21',  //ship address
                'ship_add2' => 'Mohakhali',
                'ship_city' => 'Dhaka',
                'ship_state' => 'Dhaka',
                'ship_postcode' => '1212',
                'ship_country' => $country_name,
                'desc' => $description,
                'success_url' => route('aamarpay-success'), //your success route
                'fail_url' => route('aamarpay-fail'), //your fail route
                'cancel_url' => route('aamarpay-cancel'), //your cancel url
                'opt_a' => '',  //optional paramter
                'opt_b' => '',
                'opt_c' => '',
                'opt_d' => '',
                'signature_key' => $secret_key
            ); //signature key will provided aamarpay, contact integration@aamarpay.com for test/live signature key
        }catch(\Exception $e)
        {
            throw new Exception($e->getMessage());
        }
        $fields_string = http_build_query($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $url_forward = str_replace('"', '', stripslashes($response));
        curl_close($ch);

        DB::table('transactions')->insert([
            'status' => 1, // for user
            'reference_id' => "",
            'card_id' => NULL,
            'user_id' => $calling_from == "USER" ? $id : NULL,
            'driver_id' => $calling_from == "DRIVER" ? $id : NULL,
            'merchant_id' => $merchant_id,
            'payment_option_id' => $payment_option_config->payment_option_id,
            'checkout_id' => NULL,
//            'booking_id' => isset($additional_data['booking_id']) ? $additional_data['booking_id'] : NULL,
//            'order_id' => isset($additional_data['order_id']) ? $additional_data['order_id'] :NULL,
//            'handyman_order_id' => isset($additional_data['handyman_order_id']) ? $additional_data['handyman_order_id'] : NULL,
            'payment_transaction_id' => $transaction_id,
            'payment_transaction' => NULL,
            'request_status' => 1,
        ]);

        return [
            'status'=>'NEED_TO_OPEN_WEBVIEW',
            'url'=>$submit_url.$url_forward
        ];
    }

    public function aamarPaysuccess(Request $request){
        $data = $request->all();
        \Log::channel('aamarpay_api')->emergency($data);
        $this->updateTransaction($data);
    }

    public function aamarPayFail(Request $request){
        $data = $request->all();
        \Log::channel('aamarpay_api')->emergency($data);
        $this->updateTransaction($data);
    }

    public function aamarPayCancel(Request $request){
        $data = $request->all();
        \Log::channel('aamarpay_api')->emergency($data);
        $this->updateTransaction($data);
    }

    public function updateTransaction($data){
        if(isset($data['pay_status']))
        {
            $request_status = $data['pay_status'] == "Successful" ? 2 : 3;
            $transaction_id = $data['mer_txnid'];
            DB::table('transactions')->where('payment_transaction_id', $transaction_id)->update(['request_status' => $request_status]);
        }
    }
}