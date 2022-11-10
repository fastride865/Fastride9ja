<?php

namespace App\Http\Controllers\PaymentMethods\ProxyPay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Models\Booking;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use DateTime;

class ProxyPayController extends Controller
{
    use ApiResponseTrait,MerchantTrait;

    public function createReference(Request $request)
    {
        $merchant_id = request()->merchant_id;
        $transaction_id = mt_rand(1000,10000).time();
        $booking_id = null;
        $string_file = $this->getStringFile($merchant_id);
        $payment_option = PaymentOption::where('slug', 'PROXYPAY')->first();
        $paymentOption = PaymentOptionsConfiguration::where([['merchant_id', '=', $merchant_id], ['payment_option_id', '=', $payment_option->id]])->first();

        $request_from = $request->request_from;
        switch ($request_from) {
            case 'USER' :
                $status = 1;
                $user = request()->user('api');
                $user_id = $user->id;
                $driver_id = NULL;
                break;
            case 'DRIVER' :
                $status = 2;
                $user = request()->user('api-driver');
                $user_id = NULL;
                $driver_id = $user->id;
                break;
            case 'BOOKING' :
                $status = 3;
                $user = request()->user('api');
                $booking = Booking::find($booking_id);
                $user_id = $booking->user_id;
                $driver_id = $booking->driver_id;
                break;
            default :
                $status = 0;
                $user_id = request()->user('api')->id;
                $driver_id = request()->user('api-driver')->id;
                break;
        }
        $currency = $user->CountryArea->Country->isoCode ?? '';
        $amount = $request->amount;
        DB::table('transactions')->insert([
            'status' => $status,
            'card_id' => request()->card_id ?? NULL,
            'user_id' => $user_id,
            'driver_id' => $driver_id,
            'merchant_id' => $merchant_id,
            'payment_option_id' => $payment_option->id,
            'amount' => $currency.' '.$amount,
            'booking_id' => $booking_id,
            'payment_transaction_id' => $transaction_id,
            'payment_mode' => 'Third-party App',
            'request_status' => 1,
        ]);

        $url = $paymentOption->gateway_condition == 1 ? 'https://api.proxypay.co.ao/reference_ids' : 'https://api.sandbox.proxypay.co.ao/reference_ids';

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
            CURLOPT_HTTPHEADER => array(
                "Authorization: Token $paymentOption->api_secret_key",
                'Accept: application/vnd.proxypay.v2+json',
                'Content-Type: application/json'
            ),
        ));

        $reference_id = curl_exec($curl);

        if(curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200) {
            return $this->failResponse(trans("$string_file.reference_error"));
        }
        curl_close($curl);

        DB::table('transactions')->where([
            ['merchant_id', '=', $merchant_id],
            ['payment_transaction_id', '=', $transaction_id],
            ['payment_option_id', '=', $payment_option->id]
        ])->update([
            'payment_transaction_id' => $reference_id
        ]);

        $datetime = new DateTime('tomorrow');
        $tomorrow = $datetime->format('Y-m-d');
        $payload = '{
            "custom_fields": {
                "callback_url": "'.route('proxy_pay.callback').'"
            },
            "amount": "'.$amount.'",
            "end_datetime": "'.$tomorrow.'"
        }';
        $url = $paymentOption->gateway_condition == 1 ? "https://api.proxypay.co.ao/references/$reference_id" : "https://api.sandbox.proxypay.co.ao/references/$reference_id";
        $curl_ch = curl_init();
        curl_setopt_array($curl_ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Token $paymentOption->api_secret_key",
                'Accept: application/vnd.proxypay.v2+json',
                'Content-Type: application/json'
            ),
        ));

        curl_exec($curl_ch);
        $responseCode = curl_getinfo($curl_ch, CURLINFO_HTTP_CODE);
        curl_close($curl_ch);

        if($responseCode == 204) {
            $return_data = array(
                'entity_code' => $paymentOption->api_public_key,
                'reference_id' => $reference_id,
                'amount' => $amount
            );
            return $this->successResponse(trans("$string_file.success"), $return_data);
        } else {
            return $this->failResponse(trans("$string_file.error"));
        }

    }

    public function acknowledgePayment(Request $request)
    {
        try{
            $merchant_id = request()->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
            $payment_option = PaymentOption::where('slug', 'PROXYPAY')->first();

            $transaction = DB::table('transactions')->where([
                ['merchant_id', '=', $merchant_id],
                ['payment_transaction_id', '=', $request->reference_id],
                ['payment_option_id', '=', $payment_option->id]
            ])->first();

            switch($transaction->request_status) {
                case '1':
                    $status = 'PENDING';
                    break;
                case '2':
                    $status = 'SUCCESS';
                    break;
                case '3':
                    $status = 'FAILED';
                    break;
                default:
                    $status = 'N/A';
                    break;
            }
            return $this->successResponse(trans("$string_file.status"), ['status' => $status]);
        } catch (\Exception $e){
            return $this->failedResponse($e->getMessage());
        }

        // $url = $paymentOption->gateway_condition == 1 ? 'https://api.proxypay.co.ao/payments' : 'https://api.sandbox.proxypay.co.ao/payments';

        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //   CURLOPT_URL => $url,
        //   CURLOPT_RETURNTRANSFER => true,
        //   CURLOPT_ENCODING => '',
        //   CURLOPT_MAXREDIRS => 10,
        //   CURLOPT_TIMEOUT => 0,
        //   CURLOPT_FOLLOWLOCATION => true,
        //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //   CURLOPT_CUSTOMREQUEST => 'GET',
        //   CURLOPT_HTTPHEADER => array(
        //     "Authorization: Token $paymentOption->api_secret_key",
        //     'Accept: application/vnd.proxypay.v2+json',
        //     'Content-Type: application/json'
        //   ),
        // ));

        // $response = curl_exec($curl);
        // curl_close($curl);
    }

    public function resultWebhook(Request $request)
    {
        \Log::channel('proxypay_api')->emergency($request->all());
        try{
            $payment_option = PaymentOption::where('slug', 'PROXYPAY')->first();
            $trans = DB::table('transactions')->where([['payment_transaction_id', '=', $request->reference_id], ['payment_option_id', '=', $payment_option->id]])->first();
            $merchant_id = $trans->merchant_id;
            $paymentOption = PaymentOptionsConfiguration::where([['merchant_id', '=', $merchant_id], ['payment_option_id', '=', $payment_option->id]])->first();
            $entityBody = file_get_contents('php://input');
            $signature = hash_hmac('sha256', $entityBody, $paymentOption->api_secret_key);
            $header = $request->header('X-signature');
            if($signature != $header) {
                DB::table('transactions')->where([
                    ['payment_transaction_id', '=', $request->reference_id],
                    ['payment_option_id', '=', $payment_option->id]
                ])->update([
                    'request_status' => 3,
                    'reference_id' => $request->transaction_id
                ]);
                throw new \Exception('Authentication Error');
            }

            DB::table('transactions')->where([
                ['merchant_id', '=', $merchant_id],
                ['payment_transaction_id', '=', $request->reference_id],
                ['payment_option_id', '=', $payment_option->id]
            ])->update([
                'request_status' => 2,
                'status_message' => 'Payment Successful',
                'payment_mode' => $request->terminal_type,
                'reference_id' => $request->transaction_id
            ]);

            return response()->json(['result' => true]);
        } catch (\Exception $e){
            \Log::channel('proxypay_api')->emergency($e->getMessage());
            return response()->json(['result' => false, 'message' => $e->getMessage()]);
        }
    }
}
