<?php

namespace App\Http\Controllers\PaymentMethods\Mercado;
use App\Models\BookingTransaction;
use App\Models\BusinessSegment\Order;
use App\Models\Booking;
use App\Models\HandymanOrder;
use MercadoPago;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Driver;
use App\Models\User;
use App\Models\PaymentOptionsConfiguration;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\MercadoToken;


class MercadoController extends Controller
{


    public function mercadoPageSuccess(Request $request)
    {
        p('Success');
    }
    public function mercadoPageFail(Request $request)
    {
        p('Fail');
    }

    public function cardPaymenNotification(Request $request)
    {
        $data = [
            'type'=>'Card Payment Notification',
            'data'=>$request->all()
        ];
        \Log::channel('mercadocard_api')->emergency($data);
        if(isset($request->action) && $request->action == 'payment.updated')
        {
            $payment_transaction_id = $request->data_id;
            $transaction  = Transaction::where('payment_transaction_id',$payment_transaction_id)->first();
            $transaction->status_message = 'Payment Done';
            $transaction->request_status = 2;
            $transaction->save();
        }
    }

    public function webhookNotification(Request $request)
    {
        // p($request->data->id);
        $data = [
            'type'=>'webhook Notification',
            'data'=>$request->all()
        ];
        \Log::channel('mercadocard_api')->emergency($data);
        //p($request['data']['data_id']);
        // \Log::channel('mercadocard_api')->emergency($data);
        if(isset($request->action) && $request->action == 'payment.updated')
        {

            $payment_transaction_id = $request->data_id;
            $transaction  = Transaction::where('reference_id',$payment_transaction_id)->first();
            $transaction->status_message = 'Payment Done';
            $transaction->request_status = 2;
            $transaction->save();

            if(isset($request->action) && $request->action == 'payment.updated')
            {
                $payment_transaction_id = $request->data_id;
                $transaction  = Transaction::where('reference_id',$payment_transaction_id)->where('request_status',1)
                    ->first();
                if(!empty($transaction->id))
                {
                    // if payment option is pix and request then send credit user/driver wallet
                    if($transaction->payment_option_id == 45)
                    {
                        if(!empty($transaction->driver_id))
                        {
                            $receipt = "Mercado Pix Payment : " . $request->data_id;
                            $paramArray = array(
                                'driver_id' => $transaction->driver_id,
                                'booking_id' => NULL,
                                'amount' => $transaction->amount,
                                'narration' => 2,
                                'platform' => 2,
                                'payment_method' => 4,
                                'receipt' => $receipt,
                                'transaction_id' => $transaction->id,
                                'payment_option_id' => $transaction->payment_option_id,
                            );
                            WalletTransaction::WalletCredit($paramArray);
                        }
                        else
                        {
                            $receipt = "Mercado Pix Payment : " . $request->data_id;
                            $paramArray = array(
                                'user_id' => $transaction->user_id,
                                'booking_id' => NULL,
                                'amount' => $transaction->amount,
                                'narration' => 2,
                                'platform' => 2,
                                'payment_method' => 4,
                                'receipt' => $receipt,
                                'transaction_id' => $transaction->id,
                                'payment_option_id' => $transaction->payment_option_id,
                            );
                            WalletTransaction::UserWalletCredit($paramArray);

                        }
                    }
                    else
                    {

                    }
                }
                $transaction->status_message = 'Payment Done';
                $transaction->request_status = 2;
                $transaction->save();
            }
        }


    }

    public function getWebViwUrl(Request $request,$payment_option_config,$calling_from)
    {
        // check whether request is from driver or user

        $transaction  = new Transaction;
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
            $transaction->driver_id = $id;
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
            $transaction->user_id = $id;
        }

        $transaction_id = $id.'_'.time();
        $transaction->merchant_id = $merchant_id;
        $transaction->payment_option_id = $payment_option_config->payment_option_id;
        $transaction->status = 1;
        $transaction->payment_transaction_id = $transaction_id;
        $transaction->amount = $request->amount;
        $transaction->order_id = $request->order_id;
        $transaction->handyman_order_id = $request->handyman_order_id;
        $transaction->booking_id = $request->booking_id;

        $transaction->save();

        $url = route('mercado-web-page',['unique_no'=>$transaction_id]);
        return [
            'status'=>'NEED_TO_OPEN_WEBVIEW',
            'url'=>$url
        ];

    }

    public function mercadoWebViewPage(Request $request,$unique_no = "")
    {
        if(!empty($unique_no))
        {
            $transaction = Transaction::where('payment_transaction_id',$unique_no)->first();
            if(!empty($transaction->user_id))
            {
                $data = User::find($transaction->user_id);
                $first_name = $data->first_name;
                $last_last = $data->last_name;
                $email = $data->email;
            }
            else
            {

                $data = Driver::find($transaction->driver_id);
                $first_name = $data->first_name;
                $last_last = $data->last_name;
                $email = $data->email;

            }
            $payment_option = PaymentOptionsConfiguration::where('payment_option_id',$transaction->payment_option_id)->first();
            $return_data = [
                'amount'=>$transaction->amount,
                'name'=>$first_name.' '.$last_last,
                'email'=>$email,
                'public_key'=>$payment_option->api_public_key,
                'unique_no'=>$unique_no,
                'response_url'=>route('process-payment'),
            ];
        }
        // load webview page
        return view('merchant.random.mercado',compact('return_data'));
    }

    // make card payment using card token
    public function processPayment(Request $request)
    {
//        MercadoPago\SDK::setAccessToken("APP_USR-1603575310218843-101401-bbd7807ca6133907d85953ef134f70a7-840387326");
        // driver access token
        MercadoPago\SDK::setAccessToken("APP_USR-1603575310218843-112104-c3997b7e4f8aabf415090f507889afa9-840387326");
        $data = [
            'type'=>'Card Payment Token',
            'data'=>$request->all()
        ];
        \Log::channel('mercadocard_api')->emergency($data);
        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = $request->transactionAmount;
        $payment->notification_url = route('card-payment-notification');
        $payment->token = $request->token;
        $payment->description = $request->description; // its unique id of payment request
        $payment->installments = (int)$request->installments;
        $payment->payment_method_id = $request->paymentMethodId;
        $payment->issuer_id = (int)$request->issuerId;


        $payer = new MercadoPago\Payer();
        $payer->email = $request->payer['email'];
        $payer->identification = array(
            "type" => $request->payer['identification']['type'],
            "number" => $request->payer['identification']['number']
        );
        $payment->payer = $payer;
        $payment->save();

        $transaction = Transaction::where('payment_transaction_id',$request->description)->first();
        $transaction->reference_id = $payment->id;
        $transaction->status_message = $payment->status_detail;

        if($payment->status == "approved")
        {
            $transaction->request_status = 2;

            if(!empty($transaction->driver_id))
            {
                // credit driver wallet
                $receipt = "Mercado Card Payment: " . $payment->id;
                $paramArray = array(
                    'driver_id' => $transaction->driver_id,
                    'booking_id' => NULL,
                    'amount' => $transaction->amount,
                    'narration' => 2,
                    'platform' => 2,
                    'payment_method' => 4,
                    'receipt' => $receipt,
                    'transaction_id' => $transaction->id,
                    'payment_option_id' => $transaction->payment_option_id,
                );
                WalletTransaction::WalletCredit($paramArray);
            }
            else
            {
                if($transaction->booking_id == NULL && $transaction->order_id == NULL && $transaction->handyman_order_id
                    == NULL)
                // credit user wallet
                $receipt = "Mercado Card Payment : " . $payment->id;
                $paramArray = array(
                    'user_id' => $transaction->user_id,
                    'booking_id' => NULL,
                    'amount' => $transaction->amount,
                    'narration' => 2,
                    'platform' => 2,
                    'payment_method' => 4,
                    'receipt' => $receipt,
                    'transaction_id' => $transaction->id,
                    'payment_option_id' => $transaction->payment_option_id,
                );
                WalletTransaction::UserWalletCredit($paramArray);

            }
        }
        elseif($payment->status == "in_process")
        {
            $transaction->request_status = 1;
        }
        else
        {
            $transaction->request_status = 3;
        }

        $transaction->save();

        if(!empty($payment->error->message) && empty($payment->id))
        {
            $response_fields = array(
                'status' => $payment->error->status,
                'detail' => $payment->error->message,
                'id' => $payment->id
            );
        }
        else
        {
            $response_fields = array(
                'status' => $payment->status,
                'detail' => $payment->status_detail,
                'id' => $payment->id
            );
        }
        // echo json_encode($response);
        \Log::channel('mercadocard_api')->emergency($request->all());
        $response_body = $response_fields;
        return $response_body;
    }

    public function pixPaymentRequest(Request $request,$payment_option_config,$calling_from)
    {

        $data = [
            'type'=>'Pix Payment Request',
            'data'=>$request->all()
        ];

        try{

            $transaction  = new Transaction;
            if($calling_from == "DRIVER")
            {
                $driver = $request->user('api-driver');
                $code = $driver->Country->phonecode;
                $country = $driver->Country;
                $logged_user = $driver;
                $id = $driver->id;
                $merchant_id = $driver->merchant_id;
                $transaction->driver_id = $id;
            }
            else
            {
                $user = $request->user('api');
                $logged_user = $user;
                $id = $user->id;
                $merchant_id = $user->merchant_id;
                $transaction->user_id = $id;
            }
            $transaction_id = $id.'_'.time();

            $arr_request_param = [
                'transaction_amount'=> (int)$request->amount,
                'description'=> $transaction_id,
                'payment_method_id'=> "pix",
                'payer'=> [
                    'email'=> $request->email,
                    'first_name'=> $request->first_name,
                    'last_name'=> $request->last_name,
                    'identification'=> [
                        'type'=>$request->doc_type,
                        'number'=>$request->doc_number
                    ],
                    'address'=> [
                        'zip_code'=>$request->zip_code,
                        'street_name'=>$request->street_name,
                        'street_number'=>$request->street_number,
                        'neighborhood'=>$request->neighborhood,
                        'city'=>$request->city,
                        'federal_unit'=>$request->federal_unit,

                    ],
                ],
            ];
            $arr_request_param = json_encode($arr_request_param);
            \Log::channel('mercadopix_api')->emergency($data);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$arr_request_param,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$payment_option_config->api_secret_key,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response,true);

            if($response['status'] == 400)
            {
                throw new \Exception ($response['message']);
            }
            $return = [
                'transaction_id'=>$response['id'],
                'status'=>$response['status'],
                'status_detail'=>$response['status_detail'],
                'transaction_amount'=>$response['transaction_amount'],
                'currency'=>$response['currency_id'],
                'qr_code'=>$response['point_of_interaction']['transaction_data']['qr_code'],
                'qr_code_base64'=>$response['point_of_interaction']['transaction_data']['qr_code_base64'],
            ];
            $data = [
                'type'=>'Pix Payment result',
                'data'=>$request->all()
            ];

            $transaction->merchant_id = $request->merchant_id;
            $transaction->payment_option_id = $payment_option_config->payment_option_id;
            $transaction->status = 1;
            $transaction->payment_transaction_id = $transaction_id;
            $transaction->amount = $request->amount;
            $transaction->reference_id = $response['id'];
            $transaction->status_message = $response['status_detail'];
            $transaction->save();

            \Log::channel('mercadopix_api')->emergency($return);
            return $return;

        }catch (\Exception $e)
        {
            throw new \Exception ($e->getMessage());
        }

    }

    /********Split Payment for Ride, Booking and Order *********/
    // payment for split
    public function mercadoAuthCodeRequest(Request $request,$calling_from ="DRIVER")
    {
        // seup mercado token for split
        $token = new MercadoToken;
        if($calling_from == "DRIVER")
        {
            $driver = $request->user('api-driver');
            $id = $driver->id;
            $merchant_id = $driver->merchant_id;
            $token->driver_id = $id;
        }
        else
        {
            $user = $request->user('business-segment-api');
            $id = $user->id;
            $merchant_id = $user->merchant_id;
            $token->business_segment_id = $id;
        }
        $token->merchant_id = $merchant_id;
        $token->save();
        $RANDOM_ID =  $token->id;
        $redirect_ur = route('mercado.code.response');

        // keys are used of trem project
        // this url will open seller mercado account and get auth code
        $url = "https://auth.mercadopago.com.ar/authorization?client_id=1603575310218843&response_type=code&platform_id=mp&state=".$RANDOM_ID."&redirect_uri=".$redirect_ur;
        return [
            'status'=>'NEED_TO_OPEN_WEBVIEW',
            'url'=>$url,
            'redirect_url'=>$redirect_ur
        ];
    }

    public function mercadoAuthCodeResponse(Request $request)
    {
        $arr_param = $request->all();
        $code = $arr_param['code'];
        $redirect_ur = route('mercado.code.response');
        $post_param = [
            'client_secret'=>'nqn6b50S6IosPGrYzJ8GO5AmDC3vszAY', // trem mercado client secret id
            'client_id'=>'1603575310218843',// trem mercado client id
            'grant_type'=>"authorization_code",
            'code'=>$code,
            'redirect_uri'=>$redirect_ur,
        ];
        $post_param = json_encode($post_param);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mercadopago.com/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$post_param,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $json_response = $response;
        $response = json_decode($response,true);
        $token = MercadoToken::find($arr_param['state']);
        // seup mercado token for split
        $token->token = $response['access_token']; // access token of seller (driver/restaurant)
        $token->mercado_user_id = $response['user_id'];
        $token->additional_param = $json_response;
        $token->save();
        echo 'success';
    }

    public function getWebViwUrlSplit(Request $request,$payment_option_config,$calling_from)
    {
        // check whether request is from driver or user

        $transaction  = new Transaction;
        if($calling_from == "DRIVER")
        {
            $driver = $request->user('api-driver');
            $id = $driver->id;
            $merchant_id = $driver->merchant_id;
            $transaction->driver_id = $id;
        }
        else
        {
            $user = $request->user('api');
            $id = $user->id;
            $merchant_id = $user->merchant_id;
            $transaction->user_id = $id;
        }

        $transaction_id = $id.'_'.time();
        $transaction->merchant_id = $merchant_id;
        $transaction->payment_option_id = $payment_option_config->payment_option_id;
        $transaction->status = 1;
        $transaction->payment_transaction_id = $transaction_id;
        $transaction->amount = $request->amount;
        $transaction->application_fee = $request->application_fee; // inc case of food and grocery payment is done at confirm
        // order so we have to get application fee in cart api
        $transaction->order_id = $request->order_id;
        $transaction->handyman_order_id = $request->handyman_order_id;
        $transaction->booking_id = $request->booking_id;

        $transaction->save();

        $url = route('mercado-web-page-split',['unique_no'=>$transaction_id]);
        return [
            'status'=>'NEED_TO_OPEN_WEBVIEW',
            'url'=>$url
        ];

    }

    public function mercadoWebViewPageSplit(Request $request,$unique_no = "")
    {
        if(!empty($unique_no))
        {
            $transaction = Transaction::where('payment_transaction_id',$unique_no)->first();
            if(!empty($transaction->user_id))
            {
                $data = User::find($transaction->user_id);
                $first_name = $data->first_name;
                $last_last = $data->last_name;
                $email = $data->email;
            }
            else
            {

                $data = Driver::find($transaction->driver_id);
                $first_name = $data->first_name;
                $last_last = $data->last_name;
                $email = $data->email;

            }
            $payment_option = PaymentOptionsConfiguration::where('payment_option_id',$transaction->payment_option_id)->first();
            $booking_transaction =  BookingTransaction::where(function($q) use ($transaction){
                if(!empty($transaction->booking_id))
                {
                 $q->where('booking_id',$transaction->booking_id);
                }
                elseif(!empty($transaction->order_id))
                {
                    $q->where('order_id',$transaction->order_id);
                }
                elseif(!empty($transaction->handyman_order_id))
                {
                    $q->where('handyman_order_id',$transaction->handyman_order_id);
                }
            })->first();


            $amount = 0;
            if(!empty($transaction->booking_id) || !empty($transaction->handyman_order_id))
            {
                $amount = $booking_transaction->customer_paid_amount;
            }
            elseif(!empty($transaction->order_id))
            {
                $amount = $transaction->amount;
            }


            $return_data = [
                'amount'=>str_replace('.',',',$amount),
                'name'=>$first_name.' '.$last_last,
                'email'=>$email,
                'public_key'=>$payment_option->api_public_key, // public key of trem mercado account // 'public_key'=>"APP_USR-8e3698bd-9334-44ff-94ca-fced8836dbd4",
                'unique_no'=>$unique_no,
                'response_url'=>route('process-payment-split'),
            ];
        }
        // load webview page
        return view('merchant.random.mercado',compact('return_data'));
    }

    public function processPaymentSplit(Request $request)
    {
        // check seller access token
        $transaction = Transaction::where('payment_transaction_id',$request->description)->first();

        if(!empty($transaction->booking_id))
        {
            $ride = Booking::select('driver_id')->find($transaction->booking_id);
            $transaction->driver_id = $ride->driver_id;
            $mercado_token =   MercadoToken::where('driver_id',$ride->driver_id)->first();
        }
        elseif(!empty($transaction->handyman_order_id))
        {
            $booking = HandymanOrder::select('driver_id')->find($transaction->handyman_order_id);
            $transaction->driver_id = $booking->driver_id;
            $mercado_token =   MercadoToken::where('driver_id',$booking->driver_id)->first();
        }
        elseif(!empty($transaction->order_id))
        {
            $order = Order::select('business_segment_id')->find($transaction->order_id);
            $mercado_token =   MercadoToken::where('business_segment_id',$order->business_segment_id)->first();
        }
        $transaction->save();
        $seller_token = $mercado_token->token;
        MercadoPago\SDK::setAccessToken($seller_token);
        $data = [
            'type'=>'Card Payment Token',
            'data'=>$request->all()
        ];

        \Log::channel('mercadocard_api')->emergency($data);
        $booking_transaction =  BookingTransaction::where(function($q) use ($transaction){
            if(!empty($transaction->booking_id))
            {
                $q->where('booking_id',$transaction->booking_id);
            }
            elseif(!empty($transaction->order_id))
            {
                $q->where('order_id',$transaction->order_id);
            }
            elseif(!empty($transaction->handyman_order_id))
            {
                $q->where('handyman_order_id',$transaction->handyman_order_id);
            }
        })->first();

        $total_amount = 0;
        $application_fee = 0;
        if(!empty($transaction->booking_id) || !empty($transaction->handyman_order_id))
        {
            $total_amount = $booking_transaction->customer_paid_amount;// driver commission
            $application_fee = $booking_transaction->company_gross_total; // merchant commission
        }
        elseif(!empty($transaction->order_id))
        {
            $total_amount = $transaction->amount;// store commission
            $application_fee = $transaction->application_fee; // merchant commission
        }



        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = str_replace('.',',',$total_amount);
            //$request->transactionAmount;
        $payment->notification_url = route('card-payment-notification');
        $payment->token = $request->token;
        $payment->description = $request->description; // its unique id of payment request
        $payment->installments = (int)$request->installments;
        $payment->payment_method_id = $request->paymentMethodId;
        $payment->issuer_id = (int)$request->issuerId;
        $payment->application_fee = str_replace('.',',',$application_fee);

        $payer = new MercadoPago\Payer();
        $payer->email = $request->payer['email'];
        $payer->identification = array(
            "type" => $request->payer['identification']['type'],
            "number" => $request->payer['identification']['number']
        );
        $payment->payer = $payer;
        $payment->save();

        $transaction->reference_id = $payment->id;
        $transaction->status_message = $payment->status_detail;

        if($payment->status == "approved")
        {
            $transaction->request_status = 2;

            if(!empty($transaction->driver_id))
            {
                // credit driver wallet
                $receipt = "Mercado Card Payment: " . $payment->id;
                $paramArray = array(
                    'driver_id' => $transaction->driver_id,
                    'booking_id' => NULL,
                    'amount' => $transaction->amount,
                    'narration' => 2,
                    'platform' => 2,
                    'payment_method' => 4,
                    'receipt' => $receipt,
                    'transaction_id' => $transaction->id,
                    'payment_option_id' => $transaction->payment_option_id,
                );
                WalletTransaction::WalletCredit($paramArray);
            }
            else
            {
                if($transaction->booking_id == NULL && $transaction->order_id == NULL && $transaction->handyman_order_id
                    == NULL)
                    // credit user wallet
                    $receipt = "Mercado Card Payment : " . $payment->id;
                $paramArray = array(
                    'user_id' => $transaction->user_id,
                    'booking_id' => NULL,
                    'amount' => $transaction->amount,
                    'narration' => 2,
                    'platform' => 2,
                    'payment_method' => 4,
                    'receipt' => $receipt,
                    'transaction_id' => $transaction->id,
                    'payment_option_id' => $transaction->payment_option_id,
                );
                WalletTransaction::UserWalletCredit($paramArray);

            }
        }
        elseif($payment->status == "in_process")
        {
            $transaction->request_status = 1;
        }
        else
        {
            $transaction->request_status = 3;
        }

        $transaction->save();

        if(!empty($payment->error->message) && empty($payment->id))
        {
            $response_fields = array(
                'status' => $payment->error->status,
                'detail' => $payment->error->message,
                'id' => $payment->id
            );
        }
        else
        {
            $response_fields = array(
                'status' => $payment->status,
                'detail' => $payment->status_detail,
                'id' => $payment->id
            );
        }
        // echo json_encode($response);
        \Log::channel('mercadocard_api')->emergency($request->all());
        $response_body = $response_fields;
        return $response_body;
    }

    public function pixPaymentRequestSplit(Request $request,$payment_option_config,$calling_from)
    {

        $data = [
            'type'=>'Pix Payment Request',
            'data'=>$request->all()
        ];

        try{

            $transaction  = new Transaction;
            if($calling_from == "DRIVER")
            {
                $driver = $request->user('api-driver');
                $code = $driver->Country->phonecode;
                $country = $driver->Country;
                $logged_user = $driver;
                $id = $driver->id;
                $merchant_id = $driver->merchant_id;
                $transaction->driver_id = $id;
            }
            else
            {
                $user = $request->user('api');
                $logged_user = $user;
                $id = $user->id;
                $merchant_id = $user->merchant_id;
                $transaction->user_id = $id;
            }
            $transaction_id = $id.'_'.time();

            $arr_request_param = [
                'transaction_amount'=> (int)$request->amount,
                'application_fee'=> 1.5,
                'description'=> $transaction_id,
                'payment_method_id'=> "pix",
                'payer'=> [
                    'email'=> $request->email,
                    'first_name'=> $request->first_name,
                    'last_name'=> $request->last_name,
                    'identification'=> [
                        'type'=>$request->doc_type,
                        'number'=>$request->doc_number
                    ],
                    'address'=> [
                        'zip_code'=>$request->zip_code,
                        'street_name'=>$request->street_name,
                        'street_number'=>$request->street_number,
                        'neighborhood'=>$request->neighborhood,
                        'city'=>$request->city,
                        'federal_unit'=>$request->federal_unit,

                    ],
                ],
            ];

            if(!empty($request->booking_id) || !empty($request->handyman_order_id))
            {
                if(!empty($request->booking_id))
                {
                    $order = Booking::select('driver_id')->find($request->booking_id);
                    $driver_id = $order->driver_id;
                }
                else
                {
                    $order = HandymanOrder::select('driver_id')->find($request->handyman_order_id);
                    $driver_id = $order->driver_id;
                }
                // $application_fee of driver
                $mercado_token =   MercadoToken::where('driver_id',$driver_id)->first();
            }
            else
            {
                // $application_fee of business segment
                $order = Order::select('business_segment_id')->find($transaction->order_id);
                $mercado_token =   MercadoToken::where('business_segment_id',$order->business_segment_id)->first();
            }
            $seller_token = $mercado_token->token;
            $arr_request_param = json_encode($arr_request_param);
            \Log::channel('mercadopix_api')->emergency($data);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$arr_request_param,
                CURLOPT_HTTPHEADER => array(
//                    'Authorization: Bearer APP_USR-1603575310218843-112514-419ac59e10bedaa55b0cba7560e3f0d4-204088280',
                    'Authorization: Bearer '.$seller_token,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response,true);

            if($response['status'] == 400)
            {
                throw new \Exception ($response['message']);
            }
            $return = [
                'transaction_id'=>$response['id'],
                'status'=>$response['status'],
                'status_detail'=>$response['status_detail'],
                'transaction_amount'=>$response['transaction_amount'],
                'currency'=>$response['currency_id'],
                'qr_code'=>$response['point_of_interaction']['transaction_data']['qr_code'],
                'qr_code_base64'=>$response['point_of_interaction']['transaction_data']['qr_code_base64'],
            ];
            $data = [
                'type'=>'Pix Payment result',
                'data'=>$request->all()
            ];

            $transaction->merchant_id = $request->merchant_id;
            $transaction->payment_option_id = $payment_option_config->payment_option_id;
            $transaction->status = 1;
            $transaction->payment_transaction_id = $transaction_id;
            $transaction->amount = $request->amount;
            $transaction->reference_id = $response['id'];
            $transaction->status_message = $response['status_detail'];
            $transaction->save();

            \Log::channel('mercadopix_api')->emergency($return);
            return $return;

        }catch (\Exception $e)
        {
            throw new \Exception ($e->getMessage());
        }
    }
}
