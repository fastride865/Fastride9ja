<?php

namespace App\Http\Controllers\PaymentMethods\PayMaya;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Models\Booking;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use App\Models\Merchant;
use DateTime;

class PayMayaController extends Controller
{
    use ApiResponseTrait,MerchantTrait;

    public function createToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cc_number' => 'required',
            'cc_exp' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required',
            'cvv' => 'required',
            'request_from' => 'required|in:USER,DRIVER'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        DB::beginTransaction();

        try{

            $request_from = $request->request_from;
            switch ($request_from) {
                case 'USER' :
                    $user = request()->user('api');
                    $gender = $user->user_gender;
                    $phone = $user->UserPhone;
                    break;
                case 'DRIVER' :
                    $user = request()->user('api-driver');
                    $gender = $user->driver_gender;
                    $phone = $user->phoneNumber;
                    break;
            }
            $user_id = $user->id;
            $merchant_id = $user->merchant_id;
            $string_file = $this->getStringFile($merchant_id);

            $payment_option = PaymentOption::where('slug', 'PAYMAYA')->first();
            if(empty($payment_option)){
                return $this->failedResponse(trans("$string_file.payment_option_not_found"));
            }
            $paymentOption = PaymentOptionsConfiguration::where([['merchant_id', $merchant_id],['payment_option_id','=',$payment_option->id]])->first();
            if(empty($paymentOption)){
                return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
            }

            $secret_key = base64_encode($paymentOption->api_secret_key.':');
            $public_key = base64_encode($paymentOption->api_public_key.':');
            $gateway_condition = $paymentOption->gateway_condition;

            $url = $gateway_condition == 1 ? 'https://pg.paymaya.com/payments/v1/customers' : 'https://pg-sandbox.paymaya.com/payments/v1/customers';
            $post_object = array(
                "firstName" => $user->first_name,
                "lastName" => $user->last_name,
                "customerSince" => date_format(date_create($user->created_at),"Y-m-d"),
                "sex" => $gender == 1 ? 'M' : ($gender == 2 ? 'F' : ''),
                "contact" => [
                    "phone" => $phone,
                    "email" => $user->email
                ]
            );

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
                CURLOPT_POSTFIELDS => json_encode($post_object),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    "Authorization: Basic $secret_key"
                ),
            ));

            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if(!isset($response['id'])) {
                return $this->failedResponse($response['message'] ?? 'Customer Not Found');
            }

            $customer_id = $response['id'];
            $data = [];
            $data['customer_id'] = $customer_id;

            $url = $gateway_condition == 1 ? 'https://pg.paymaya.com/payments/v1/payment-tokens' : 'https://pg-sandbox.paymaya.com/payments/v1/payment-tokens';
            $post_object = array(
                "card" => [
                    "number" => $request->cc_number,
                    "expMonth" => $request->exp_month,
                    "expYear" => $request->exp_year,
                    "cvc" => $request->cvv
                ]
            );
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
                CURLOPT_POSTFIELDS => json_encode($post_object),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    "Authorization: Basic $public_key"
                ),
            ));

            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if(!isset($response['paymentTokenId'])) {
                return $this->failedResponse($response['message'] ?? 'Payment Token Error');
            }

            $payment_token_id = $response['paymentTokenId'];

            $url = $gateway_condition == 1 ? "https://pg.paymaya.com/payments/v1/customers/$customer_id/cards" : "https://pg-sandbox.paymaya.com/payments/v1/customers/$customer_id/cards";
            $post_object = array(
                "paymentTokenId" => $payment_token_id,
                "isDefault" => true,
                "redirectUrl" => [
                    "success" => "https://www.merchantsite.com/success",
                    "failure" => "https://www.merchantsite.com/failure",
                    "cancel" => "https://www.merchantsite.com/cancel",
                ],
                "requestReferenceNumber" => (string)time(),
            );
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
                CURLOPT_POSTFIELDS => json_encode($post_object),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    "Authorization: Basic $secret_key"
                ),
            ));

            $response = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if(!isset($response['cardTokenId'])) {
                return $this->failedResponse($response['message'] ?? 'Card Token Error');
            }
            $data['card_token'] = $response['cardTokenId'];
            $data['type'] = $response['cardType'] ?? '';
            $data['verification_url'] = $response['verificationUrl'] ?? '';

            return $this->successResponse(trans("$string_file.success"), $data);

        } catch(\Exception $e) {

            return $this->failedResponse($e->getMessage());

        }
    }

    public function card_payment($amount, $currency, $card, $payment_option, $user, $booking_id = NULL, $order_id = NULL, $handyman_order_id = NULL)
    {
        $merchant_id = $payment_option->merchant_id;
        switch ($user) {
            case 'USER' :
                $customer_id = $card->user_token;
                $user_id = $card->user_id;
                $driver_id = NULL;
                $transaction_id = $user_id.time();
                $status = 1;
                break;
            case 'DRIVER' :
                $customer_id = $card->driver_token;
                $user_id = NULL;
                $driver_id = $card->driver_id;
                $transaction_id = $driver_id.time();
                $status = 2;
                break;
        }

        DB::table('transactions')->insert([
            'status' => $status,
            'card_id' => $card->id,
            'user_id' => $user_id,
            'driver_id' => $driver_id,
            'merchant_id' => $merchant_id,
            'payment_option_id' => $payment_option->payment_option_id,
            'amount' => $currency.' '.$amount,
            'booking_id' => $booking_id,
            'order_id' => $order_id,
            'handyman_order_id' => $handyman_order_id,
            'payment_transaction_id' => $transaction_id,
            'request_status' => 1,
            'payment_mode' => 'Card',
        ]);

        $token = $card->token;
        $secret_key = base64_encode($payment_option->api_secret_key.':');
        $gateway_condition = $payment_option->gateway_condition;
        $url = $gateway_condition == 1 ? "https://pg.paymaya.com/payments/v1/customers/$customer_id/cards/$token/payments" : "https://pg-sandbox.paymaya.com/payments/v1/customers/$customer_id/cards/$token/payments";
        $post_object = array(
            "totalAmount" => [
                "amount" => $amount,
                "currency" => $currency,
            ],
            "requestReferenceNumber" => $transaction_id
        );
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
            CURLOPT_POSTFIELDS => json_encode($post_object),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                "Authorization: Basic $secret_key"
            ),
        ));
        
        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        if(isset($response['id']) && isset($response['isPaid'])) {
            if($response['isPaid'] == true) {    
                DB::table('transactions')->where([['payment_transaction_id', '=', $transaction_id], ['merchant_id', '=', $merchant_id]])->update([
                    'request_status' => 2,
                    'reference_id' => $response['receiptNumber'],
                    'status_message' => $response['status']
                ]);
                return array('id' => $response['receiptNumber']);
            } elseif ($response->isPaid == false) {
                DB::table('transactions')->where([['payment_transaction_id', '=', $transaction_id], ['merchant_id', '=', $merchant_id]])->update([
                    'request_status' => 3,
                    'status_message' => $response['status']
                ]);
            } else {
                DB::table('transactions')->where([['payment_transaction_id', '=', $transaction_id], ['merchant_id', '=', $merchant_id]])->update([
                    'request_status' => 4,
                    'status_message' => $response['message'] ?? 'Unknown status'
                ]);
            }
        } else {
            if(isset($response['error'])) {
                DB::table('transactions')->where([['payment_transaction_id', '=', $transaction_id], ['merchant_id', '=', $merchant_id]])->update([
                    'request_status' => 3,
                    'status_message' => $response['error']
                ]);
            }
            if(isset($response['message'])) {
                DB::table('transactions')->where([['payment_transaction_id', '=', $transaction_id], ['merchant_id', '=', $merchant_id]])->update([
                    'request_status' => 3,
                    'status_message' => $response['message']
                ]);
            }
        }
        return 'false';
    }
}
