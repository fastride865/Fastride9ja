<?php

namespace App\Http\Controllers\PaymentMethods\SDGExpress;

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
use Carbon\Carbon;
use phpseclib\Crypt\RSA as Crypt_RSA;

class SDGExpressController extends Controller
{
    use ApiResponseTrait,MerchantTrait;

    const ENCRYPTION_OAEP = 1;
    /**
     * Use PKCS#1 padding.
     *
     * Although self::ENCRYPTION_OAEP offers more security, including PKCS#1 padding is necessary for purposes of backwards
     * compatibility with protocols (like SSH-1) written before OAEP's introduction.
     */
    const ENCRYPTION_PKCS1 = 2;
    /**
     * Do not use any padding
     *
     * Although this method is not recommended it can none-the-less sometimes be useful if you're trying to decrypt some legacy
     * stuff, if you're trying to diagnose why an encrypted message isn't decrypting, etc.
     */
    const ENCRYPTION_NONE = 3;

    public function addCard($payment_option, Request $request)
    {
        try{
            $uuid = Carbon::now()->timestamp;
            $cvv = $request->cvv;

            $rsa = new Crypt_RSA();
            $rsa->loadKey($payment_option->api_public_key);
            $rsa->setEncryptionMode(self::ENCRYPTION_PKCS1);
            $i_pin = base64_encode($rsa->encrypt($uuid.$cvv));

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://www.sdgexpress.net/online-payment/express/doDeposit',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'amount' => '1',
                    'UUID' => $uuid,
                    'IPIN' => $i_pin,
                    'card_number' => $request->cc_number,
                    'terminal_id' => $payment_option->api_secret_key,
                    'ajax' => 'true'
                )
            ));

            $response = json_decode(curl_exec($curl));
            curl_close($curl);
            $message = 'Error in saving card';
            $response_code = $response->doPurchaseSaleResponse->doPurchaseSaleResult->eSerResponseCode ?? NULL;
            if(!is_null($response_code)) {
                if($response_code == 0) {
                    return array('result' => true, 'message' => 'Success');
                } else {
                    $error = $response->doPurchaseSaleResponse->doPurchaseSaleResult->eSerResponseMessage ?? $message;
                    return array('result' => false, 'message' => $error);
                }
            }
            return array('result' => false, 'message' => $message);
        } catch(\Exception $e) {
            return array('result' => false, 'message' => $e->getMessage());
        }
    }

    public function card_payment($amount, $currency, $card, $payment_option, $user, $booking_id = NULL, $order_id = NULL, $handyman_order_id = NULL)
    {
        $merchant_id = $payment_option->merchant_id;
        switch ($user) {
            case 'USER' :
                $user_id = $card->user_id;
                $driver_id = NULL;
                $transaction_id = $user_id.time();
                $status = 1;
                break;
            case 'DRIVER' :
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

        $uuid = $transaction_id;
        $cvv = base64_decode($card->token);

        $rsa = new Crypt_RSA();
        $rsa->loadKey($payment_option->api_public_key);
        $rsa->setEncryptionMode(self::ENCRYPTION_PKCS1);
        $i_pin = base64_encode($rsa->encrypt($uuid.$cvv));

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.sdgexpress.net/online-payment/express/doDeposit',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'exp_month' => $card->exp_month,
                'exp_year' => $card->exp_year,
                'amount' => $amount,
                'UUID' => $uuid,
                'IPIN' => $i_pin,
                'card_number' => $card->card_number,
                'terminal_id' => $payment_option->api_secret_key,
                'ajax' => 'true'
            )
        ));

        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        $response_code = $response->doPurchaseSaleResponse->doPurchaseSaleResult->eSerResponseCode ?? NULL;
        if(!is_null($response_code)) {
            if($response_code == 0) {
                DB::table('transactions')->where([['payment_transaction_id', '=', $transaction_id], ['merchant_id', '=', $merchant_id]])->update([
                    'request_status' => 2,
                    'status_message' => 'Successful'
                ]);
                return array('result' => true, 'message' => 'Success');
            } else {
                $error = $response->doPurchaseSaleResponse->doPurchaseSaleResult->eSerResponseMessage ?? 'Unknown Error';
                DB::table('transactions')->where([['payment_transaction_id', '=', $transaction_id], ['merchant_id', '=', $merchant_id]])->update([
                    'request_status' => 3,
                    'status_message' => $error
                ]);
                return array('result' => false, 'message' => $error);
            }
        }
    }
}