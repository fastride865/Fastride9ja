<?php

namespace App\Http\Controllers\PaymentMethods\CashFree;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Traits\ContentTrait;

class CashFreeController extends Controller
{
    use ApiResponseTrait, MerchantTrait, ContentTrait;

    public function __construct()
    {
    }

    public function PaymentUrl($request, $payment_option_config, $calling_from)
    {
        DB::beginTransaction();
        try {
            // check whether gateway is on sandbox or live
            $url = $payment_option_config->gateway_condition == 1 ? "https://api.cashfree.com/api/v1/order/create" : "https://test.cashfree.com/api/v1/order/create";
            $app_id = $payment_option_config->api_public_key;
            $secret_key = $payment_option_config->api_secret_key;

            // check whether request is from driver or user
            if($calling_from == "DRIVER") {
                $driver = $request->user('api-driver');
                $code = $driver->Country->phonecode;
                $status = 2;
                $currency = $driver->Country->isoCode;
                $phone_number = str_replace($code,"",$driver->phoneNumber);
                $first_name = $driver->first_name;
                $last_name = $driver->last_name;
                $email = $driver->email;
                $id = $driver->id;
                $merchant_id = $driver->merchant_id;
                $description = "Driver wallet recharge";
                $admin_email = $driver->Merchant->email;
            } else {
                $user = $request->user('api');
                $code = $user->Country->phonecode;
                $status = 1;
                $currency = $user->Country->isoCode;
                $phone_number = str_replace($code,"",$user->UserPhone);
                $first_name = $user->first_name;
                $last_name = $user->last_name;
                $email = $user->email;
                $id = $user->id;
                $merchant_id = $user->merchant_id;
                $description = "Payment from user";
                $admin_email = $user->Merchant->email;
            }

            $transaction_id = $id.'_'.time();
            $amount = $request->amount;
            $status = isset($request->booking_id) || isset($request->order_id) || isset($request->handyman_order_id) ? 3 : $status;
            $fields = array(
                'appId' => $app_id,
                'secretKey' => $secret_key,
                'orderId' => $transaction_id,
                'orderAmount' => $amount,
                'orderCurrency' => $currency,
                'orderNote' => $description,
                'customerEmail' => $email ?? $admin_email,
                'customerPhone' => $phone_number,
                'customerName' => $first_name.' '.$last_name,
                'returnUrl' => route('cash_free.redirect'),
                'notifyUrl' => route('cash_free.notify')
            );

            DB::table('transactions')->insert([
                'status' => $status,
                'card_id' => NULL,
                'user_id' => $calling_from == "USER" ? $id : NULL,
                'driver_id' => $calling_from == "DRIVER" ? $id : NULL,
                'merchant_id' => $merchant_id,
                'payment_option_id' => $payment_option_config->payment_option_id,
                'checkout_id' => NULL,
                'amount' => $currency.' '.$amount,
                'booking_id' => $request->booking_id,
                'order_id' => $request->order_id,
                'handyman_order_id' => $request->handyman_order_id,
                'payment_transaction_id' => $transaction_id,
                'payment_transaction' => NULL,
                'request_status' => 1,
            ]);

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
                CURLOPT_POSTFIELDS => http_build_query($fields),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));
            $response = json_decode(curl_exec($curl));
            curl_close($curl);
            if(isset($response->status) && $response->status == 'ERROR') {
                throw new \Exception($response->reason);
            }
        }catch(\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }

        DB::commit();

        return [
            'status' => 'NEED_TO_OPEN_WEBVIEW',
            'url' => $response->paymentLink ?? ''
        ];
    }

    public function Redirect(Request $request)
    {
        $transaction_id = $request->orderId;
        $reference_id = $request->referenceId;
        $status = $request->txStatus;
        $payment_mode = $request->paymentMode;
        $message = $request->txMsg;

        DB::table('transactions')->where('payment_transaction_id', $transaction_id)->update([
            'request_status' => $status == 'SUCCESS' ? 2 : ($status == 'FAILED' ? 3 : 4),
            'reference_id' => $reference_id,
            'payment_mode' => $payment_mode,
            'status_message' => $message
        ]);

        if($status == 'SUCCESS'){
            return redirect()->route('cash_free.success');
        } else {
            return redirect()->route('cash_free.fail');
        }
    }

    public function Success()
    {
        $response = 'Transaction completed successfully';
        return view('payment/CashFree/callback', compact('response'));
    }

    public function Fail()
    {
        $response = 'Transaction failed, Please try again';
        return view('payment/CashFree/callback', compact('response'));
    }

    public function Notify(Request $request)
    {

    }
}
