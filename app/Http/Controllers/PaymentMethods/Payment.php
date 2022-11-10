<?php

namespace App\Http\Controllers\PaymentMethods;


use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Http\Controllers\PaymentMethods\AamarPay\AamarPayController;
use App\Http\Controllers\PaymentMethods\CashFree\CashFreeController;
use App\Http\Controllers\PaymentMethods\PayHere\PayHereController;
use App\Http\Controllers\PaymentMethods\PayMaya\PayMayaController;
use App\Http\Controllers\PaymentMethods\PayPhone\PayPhoneController;
use App\Http\Controllers\PaymentMethods\SDGExpress\SDGExpressController;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\BookingTransaction;
use App\Models\BusinessSegment\Order;
use App\Models\CorporateWalletTransaction;
use App\Models\Country;
use App\Models\HandymanOrder;
use App\Models\PaymentOption;
use App\Models\UserCard;
use App\Models\UserWalletTransaction;
use App\Models\PaymentOptionsConfiguration;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Runner\Exception;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Http\Controllers\PaymentMethods\PayBox\PayBoxController;
use App\Http\Controllers\PaymentMethods\Mercado\MercadoController;
use App\Http\Controllers\PaymentMethods\PaygateGlobal\PaygateGlobalController;
use App\Http\Controllers\PaymentMethods\Square\SquareController;
use App\Http\Controllers\PaymentMethods\DPO\DpoController;
use App\Http\Controllers\PaymentMethods\TouchPay\TouchPayController;
use App\Http\Controllers\PaymentMethods\Kushki\KushkiController;

class Payment
{
//    public function MakePayment($bookingId, $payment_method_id, $amount, $userId, $card_id = null, $currency = null, $booking_transaction = null, $driver_sc_account_id = null)
    use ApiResponseTrait,MerchantTrait;
    public function MakePayment($array_param)
    {
//        $array_param = array(
//            'booking_id' => 'bookingId',
//            'order_id' => 'order_id',
//            'handyman_order_id' => 'handyman_order_id',
//            'payment_method_id' => 'payment_method_id',
//            'amount' => 'amount',
//            'user_id' => 'user_id',
//            'card_id' => 'card_id',
//            'currency' => 'currency',
//            'booking_transaction' => 'booking_transaction',
//            'driver_sc_account_id' => 'driver_sc_account_id'
//        );
        $merchant_id = isset($array_param['merchant_id']) ? $array_param['merchant_id'] : NULL;
        $string_file = $this->getStringFile($merchant_id);
        $bookingId = isset($array_param['booking_id']) ? $array_param['booking_id'] : NULL;
        $order_id = isset($array_param['order_id']) ? $array_param['order_id'] : NULL;
        $handyman_order_id = isset($array_param['handyman_order_id']) ? $array_param['handyman_order_id'] : NULL;
        $payment_method_id = isset($array_param['payment_method_id']) ? $array_param['payment_method_id'] : NULL;
        $amount = isset($array_param['amount']) ? $array_param['amount'] : NULL;
        $userId = isset($array_param['user_id']) ? $array_param['user_id'] : NULL;
        $card_id = isset($array_param['card_id']) ? $array_param['card_id'] : NULL;
        $currency = isset($array_param['currency']) ? $array_param['currency'] : NULL;
        $booking_transaction = isset($array_param['booking_transaction']) ? $array_param['booking_transaction'] : NULL;
        $driver_sc_account_id = isset($array_param['driver_sc_account_id']) ? $array_param['driver_sc_account_id'] : NULL;
        $driver_paystack_account_id = isset($array_param['driver_paystack_account_id']) ? $array_param['driver_paystack_account_id'] : NULL;
        $payment_type = isset($array_param['payment_type']) ? $array_param['payment_type'] : "FINAL";

        // throw error if payment request is calling from user side
        $request_from = isset($array_param['request_from']) ? $array_param['request_from'] : "";

        try {
            switch ($payment_method_id) {
                case "1": // cash
                    $this->UpdateStatus($array_param);
                    $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                    return true;
                    break;
                // card
                case "2":
                    $card = UserCard::with('PaymentOption')->find($card_id);
                    if (!empty($card)) {
                        $slug = $card->PaymentOption->slug;
                        switch ($slug) {
                            case "STRIPE":
                                $user = User::find($userId);
                                $payment_config = PaymentOptionsConfiguration::where([['payment_option_id', '=', $card->payment_option_id], ['merchant_id', '=', $user->merchant_id]])->first();
                                if (!empty($payment_config) > 0) {
                                    ;
                                    $newCard = new StripeController($payment_config->api_secret_key);
                                    if ($booking_transaction && $booking_transaction->instant_settlement && $driver_sc_account_id) {
                                        $charge = $newCard->Connect_charge($amount, $currency, $card->token, $booking_transaction->driver_total_payout_amount, $driver_sc_account_id, $user->merchant_id);
                                    } else {
                                        $charge = $newCard->Charge($amount, $currency, $card->token, $user->email);
                                    }
                                    if (is_array($charge)) {
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    } else {
                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                    }
                                } else {
                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
                                }
                                break;
                            case "SENANGPAY":
                                $user = User::find($userId);
                                $cardss = UserCard::find($card_id);
                                $name = $user->first_name . "" . $user->last_name;
                                $email = $user->email;
                                $detail = "taxi payment";
                                $phone = $user->UserPhone;
                                $order_id = $bookingId;
                                $amount = $amount * 100;
                                $token = $cardss->token;

                                $payment_option = PaymentOption::where('slug', 'SENANGPAY')->first();
                                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id', '=', $user->merchant_id], ['payment_option_id', '=', $payment_option->id]])->first();

                                $condition = $paymentoption['gateway_condition'];
                                if ($condition == 2) {
                                    $payment_redirect_url = "https://sandbox.senangpay.my/apiv1/pay_cc";
                                } else {
                                    $payment_redirect_url = $paymentoption['payment_redirect_url'];
                                }

                                $curl = curl_init();

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => $payment_redirect_url,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => "",
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 30,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => "POST",
                                    CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"name\"\r\n\r\n$name\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"email\"\r\n\r\n$email\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"detail\"\r\n\r\n$detail\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"phone\"\r\n\r\n$phone\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"order_id\"\r\n\r\n$order_id\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"amount\"\r\n\r\n$amount\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"token\"\r\n\r\n$token\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
                                    CURLOPT_HTTPHEADER => array(
                                        "Authorization: Basic " . $paymentoption['auth_token'],
                                        "cache-control: no-cache",
                                        "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
                                    ),
                                ));

                                $response = curl_exec($curl);
                                $err = curl_error($curl);

                                curl_close($curl);

                                if ($err) {
                                    echo "cURL Error #:" . $err;
                                } else {
                                    $response = json_decode($response, true);
                                    if ($response['status'] == 1) {
                                        $this->UpdateStatus($array_param);
                                        return response()->json(['result' => $response['status'], 'transaction_id' => $response['transaction_id'], 'order_id' => $response['order_id'], 'amount_paid' => $response['amount_paid'], 'message' => $response['msg'], 'hash' => $response['hash']]);
                                    } else {
                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                        return response()->json(['result' => $response['status'], 'message' => $response['msg']]);
                                    }
                                }
                                break;
                            case "PAYSTACK":
                                $user = User::find($userId);
                                $payment_option = PaymentOption::where('slug', 'PAYSTACK')->first();
                                $paymentConfig = PaymentOptionsConfiguration::where([['merchant_id', '=', $user->merchant_id], ['payment_option_id', '=', $payment_option->id]])->first();
                                if (!empty($paymentConfig)) {
                                    $booking_transaction_fee = $booking_transaction->cancellation_charge_received + $booking_transaction->company_earning;
                                    $payment = new RandomPaymentController();
                                    if ($booking_transaction && $booking_transaction->instant_settlement && $driver_paystack_account_id) {
                                        $charge = $payment->ChargePaystack($amount, $user->Country->isoCode, $card->token, $user->email, $paymentConfig->api_secret_key, $paymentConfig->payment_redirect_url, 'BOOKING', $bookingId ?? $booking_transaction->booking_id, $request_from, $booking_transaction_fee, $driver_paystack_account_id);
                                    } else {
                                        $charge = $payment->ChargePaystack($amount, $user->Country->isoCode, $card->token, $user->email, $paymentConfig->api_secret_key, $paymentConfig->payment_redirect_url, 'BOOKING', $bookingId ?? $booking_transaction->booking_id, $request_from);
                                    }
                                    if (is_array($charge) && array_key_exists('id', $charge)) {
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    } else {
                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                    }
                                } else {
                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
                                }
                                break;
                            case "CIELO":
                                $user = User::find($userId);
                                $payment_option = PaymentOption::where('slug', 'CIELO')->first();
                                $paymentConfig = PaymentOptionsConfiguration::where([['merchant_id', '=', $user->merchant_id], ['payment_option_id', '=', $payment_option->id]])->first();
                                if (!empty($paymentConfig)) {
                                    $userName = $user->first_name . " " . $user->last_name;
                                    $payment = new RandomPaymentController();
                                    $charge = $payment->ChargeCielo($amount, $userName, $card->card_type, $card->token, $paymentConfig->api_secret_key, $paymentConfig->api_public_key, $paymentConfig->payment_redirect_url);
                                    if (is_array($charge)) {
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    } else {
                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                    }
                                } else {
                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
                                }
                                break;
                            case "BANCARD":
                                $user = User::find($userId);
                                $payment_option = PaymentOption::where('slug', 'BANCARD')->first();
                                $paymentConfig = PaymentOptionsConfiguration::where([['merchant_id', '=', $user->merchant_id], ['payment_option_id', '=', $payment_option->id]])->first();
                                if (!empty($paymentConfig)) {
                                    // $shopProcessId = uniqid();
                                    $shopProcessId = rand(11111, 99999);
                                    $amount = number_format($amount, 2);
                                    $token = md5('.' . $paymentConfig->api_secret_key . $shopProcessId . 'charge' . $amount . '1');
                                    $payment = new RandomPaymentController();
                                    $charge = $payment->ChargeBancard($paymentConfig->payment_redirect_url, $paymentConfig->api_public_key, $token, $shopProcessId, $amount, $currency, $card->token);
                                    if (is_array($charge) && $charge['result'] == 1) {
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    } else {
                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                    }
                                } else {
                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
                                }
                                break;
                            case "DPO":

                                $user = User::find($userId);
                                $payment_option = PaymentOption::where('slug', 'DPO')->first();
                                $paymentConfig = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                                if (!empty($paymentConfig))
                                {
                                    $payment = new DpoController();
                                    $charge = $payment->cardPayment("USER",$user,$paymentConfig,$card,$amount);

                                    if (is_array($charge) && $charge['status'] == true) {
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    } else {
                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                    }
                                }

//                                $user = User::find($userId);
//                                $payment_option = PaymentOption::where('slug', 'DPO')->first();
//                                $paymentConfig = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
//                                if (!empty($paymentConfig)) {
//                                    $payment = new RandomPaymentController();
//                                    $charge = $payment->ridePaymentDPO($paymentConfig->auth_token, $amount, $currency, $user->email, $user->first_name, $user->last_name, $user->UserPhone, $card->token);
//                                    if (is_array($charge)) {
//                                        $this->UpdateStatus($array_param);
//                                        return true;
//                                    } else {
//                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
//                                    }
//                                } else {
//                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
//                                }
                                break;
                            case "PEACHPAYMENT":
                                $user = User::find($userId);
                                $payment_option = PaymentOption::where('slug', 'PEACHPAYMENT')->first();
                                $paymentConfig = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                                if (empty($paymentConfig)) {
                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
                                }
                                $charge = new RandomPaymentController();
                                $charge = $charge->peachpayment($paymentConfig->api_secret_key, $paymentConfig->auth_token, $amount, "ZAR", $card->token, true, $userId, $paymentConfig->tokenization_url);
                                if (is_array($charge)) {
                                    $this->UpdateStatus($array_param);
                                    return true;
                                } else {
                                    $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                }
                                break;
                            case "HYPERPAY":
                                $user = User::find($userId);
                                $payment_option = PaymentOption::where('slug', 'HYPERPAY')->first();
                                $paymentConfig = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                                if (empty($paymentConfig)) {
                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
                                }
                                $charge = new RandomPaymentController();
                                $charge = $charge->hyperPayment($paymentConfig->api_secret_key, $paymentConfig->auth_token, $amount, "SAR", $card->token, true, $userId, $paymentConfig->tokenization_url);
                                if (is_array($charge)) {
                                    $this->UpdateStatus($array_param);
                                    return true;
                                } else {
                                    $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                }
                                break;
                            case "MONERIS":
                                $user = User::find($userId);
                                $payment_option = PaymentOption::where('slug', 'MONERIS')->first();
                                $payment_config = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                                if (!empty($payment_config) > 0){
                                    $storeId = $payment_config->api_secret_key;
                                    $apiToken = $payment_config->auth_token;
                                    $cardToken = $card->token;
                                    $charge = new RandomPaymentController();
                                    $charge = $charge->MonerisMakePayment($userId,$cardToken ,$amount, $storeId, $apiToken);
                                    if ($charge['ResponseCode'] == '027' && !empty($charge['DataKey'])) {
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    } else {
                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                    }
                                }else{
                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
                                }
                                break;
                            case "EZYPOD":
                                $user = User::find($userId);
                                $payment_option = PaymentOption::where('slug', 'EZYPOD')->first();
                                $payment_config = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                                if (!empty($payment_config) > 0){
                                    $apiKey = $payment_config->api_secret_key;
                                    $loginID = $payment_config->api_public_key;
                                    $paymentMode = $payment_config->payment_redirect_url;
                                    $am = $amount;
                                    $famt = '';
                                    if($am !='' ) {
                                        $am2 = str_replace(".", "", $am);
                                        $am3 = strlen($am2);
                                        $len = 12 - $am3;
                                        $amArr = "";
                                        for($len=0; $len<$len; $len++)
                                        {
                                            $amArr .= "0";
                                        }
                                        $famt = $amArr.$am2;
                                    }else
                                    {
                                        $famt = '000000000000';
                                    }
                                    $country = Country::find($user->country_id);
                                    $user_phone = str_replace($country->phonecode,"",$user->UserPhone);
                                    $payArr = array(
                                        "service" => "MOBI_EZYREC_REQ",
                                        "cardToken" => $card->token,
                                        "amount" => $famt,
                                        "mobileNo" => $user_phone);
                                    $charge = new RandomPaymentController();
                                    $result = $charge->EZYPODMakePayment($apiKey,$loginID ,$paymentMode, $payArr);
                                    if ($result){
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    } else {
                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                    }
                                }else{
                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
                                }
                                break;
                            case "CONEKTA":
                                $payment = new RandomPaymentController();
                                $payment_config = PaymentOptionsConfiguration::where([['payment_option_id','=',$card->payment_option_id],['merchant_id','=',$card->User->merchant_id]])->first();
                                if(!empty($payment_config))
                                {
                                    $arr_data = ['currency'=>$currency,'quantity'=>$array_param['quantity'],'name'=>$array_param['order_name'],'amount'=>$amount,'private_key'=>$payment_config->api_secret_key,'customer_token'=>$card->user_token];
                                    $payment_response =  $payment->createConektaOrder($arr_data);
                                    $payment_response = json_decode($payment_response);
                                    if (!empty($payment_response->id) && $payment_response->payment_status == 'paid') {
                                        $array_param['transaction_id'] = $payment_response->id;
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    } else {
                                        $message = isset($payment_response->details[0]) ? $payment_response->details[0]->debug_message : $payment_response->object;
                                        throw new \Exception(trans("$string_file.payment_failed").' : '.$message);
//                                $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                    }
                                }
                                else{
                                    return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                                }
                                break;
                            case "PAYU":
                                $user = User::find($userId);
                                $payment_config = PaymentOptionsConfiguration::where([['payment_option_id','=',$card->payment_option_id],['merchant_id','=',$user->merchant_id]])->first();
                                $payment = new RandomPaymentController();
                                $locale = "en";
                                $transaction = [];
                                $estimate = NULL;
                                $payment_data = [];
                                if(!empty($bookingId))
                                {
                                    $booking = Booking::select('estimate_bill','user_id')->find($bookingId);
                                    $estimate =   $booking->estimate_bill;
                                    // p($estimate,0);
                                    // p($amount);
                                    $call_void_if_error = false;
                                    // when authorization amount is equal to capture
                                    if($amount == $estimate)
                                    {
                                        if($payment_config->payment_step > 1)
                                        {
                                            $transaction = DB::table("transactions")->select("id","payment_transaction")->where([["reference_id",'=',$user->id],["card_id",'=',$card->id],["booking_id",'=',$bookingId],["status",'=',1]])->first();
                                            $transaction= !empty($transaction->payment_transaction)  ? json_decode($transaction->payment_transaction,true) : [];
                                        }
                                        $payment_data = $payment->payuPayment($user, $amount, $card,$payment_config,$locale,$transaction);
                                        $call_void_if_error = true;

                                    }
                                    elseif($amount < $estimate)
                                    {
                                        if($payment_config->payment_step > 1)
                                        {
                                            $transaction = DB::table("transactions")->select("id","payment_transaction")->where([["reference_id",'=',$user->id],["card_id",'=',$card->id],["booking_id",'=',$bookingId],["status",'=',1]])->first();
                                            $transaction= !empty($transaction->payment_transaction)  ? json_decode($transaction->payment_transaction,true) : [];
                                        }
                                        // partial capture
                                        $payment_data = $payment->payuPartialPayment($user, $amount, $card,$payment_config,$locale,$transaction);
                                        $call_void_if_error = false; // if state in refund mode then can't void
                                        // refund of remaining amount
                                    }
                                    elseif($amount > $estimate)
                                    {
                                        $transaction = DB::table("transactions")->select("id","payment_transaction")->where([["reference_id",'=',$user->id],["card_id",'=',$card->id],["booking_id",'=',$bookingId],["status",'=',1]])->first();
                                        $transaction= !empty($transaction->payment_transaction)  ? json_decode($transaction->payment_transaction,true) : [];
                                        // cancel the existing authorization and create new with final amount
                                        if(isset($transaction['code']) && $transaction['code'] == "SUCCESS"&& $transaction['transactionResponse']['state'] == "APPROVED")
                                        {
                                            // p($transaction);
                                            $result = $payment->payuPaymentVoid($booking->User, $booking->final_amount_paid, $booking->UserCard,$payment_config,$locale,$transaction);
                                        }
                                        $authorization_data = $payment->payuPaymentAuthorization($user, $amount, $card, $payment_config, $locale);
                                        // p($authorization_data);
                                        if(isset($authorization_data['code']) && $authorization_data['code'] == "SUCCESS"&& $authorization_data['transactionResponse']['state'] == "APPROVED")
                                        {
                                            DB::table('transactions')->insert([
                                                'status' => 1, // for user
                                                'reference_id' => $user->id,
                                                'card_id' => $card->id,
                                                'merchant_id' => $booking->merchant_id,
                                                'payment_option_id' => $card->payment_option_id,
                                                'checkout_id' => $booking->id,
                                                'payment_transaction' => json_encode($authorization_data),
                                            ]);
                                            $transaction = $authorization_data;
                                            // caputure
                                            $payment_data = $payment->payuPayment($user, $amount, $card,$payment_config,$locale,$transaction);
                                            // p($payment_data);
                                            $call_void_if_error = true;
                                        }
                                        elseif(isset($authorization_data['code']) && $authorization_data['code'] == "SUCCESS" && $authorization_data['transactionResponse']['state'] == "DECLINED")
                                        {
                                            // $payment->payuPaymentVoid($booking->User, $amount, $booking->UserCard,$payment_config,$locale,$transaction);
                                            $message = isset($authorization_data['transactionResponse']['paymentNetworkResponseErrorMessage']) ? $authorization_data['transactionResponse']['paymentNetworkResponseErrorMessage'] : $authorization_data['transactionResponse']['responseCode'];
                                            throw new \Exception(trans("$string_file.payment_failed").' : '.$message);

                                        }
                                        else
                                        {
                                            $message = isset($authorization_data['error']) ? $authorization_data['error'] : "";
                                            throw new \Exception(trans("$string_file.payment_failed").' : '.$message);
                                        }

                                    }

                                    if(isset($payment_data['code']) && $payment_data['code'] == "SUCCESS" && $payment_data['transactionResponse']['state'] == "APPROVED")
                                    {
                                        $array_param['transaction_id'] = $payment_data['transactionResponse']['transactionId'];
                                        $this->UpdateStatus($array_param);
                                    }
                                    elseif(isset($payment_data['code']) && $payment_data['code'] == "SUCCESS" && $payment_data['transactionResponse']['state'] == "DECLINED")
                                    {
                                        // first void existing authorisation
                                        if($call_void_if_error == true)
                                        {
                                            $payment->payuPaymentVoid($booking->User, $amount, $booking->UserCard,$payment_config,$locale,$transaction);
                                        }
                                        $message = isset($payment_data['transactionResponse']['paymentNetworkResponseErrorMessage']) ? $payment_data['transactionResponse']['paymentNetworkResponseErrorMessage'] : $payment_data['transactionResponse']['responseCode'];
                                        throw new \Exception(trans("$string_file.payment_failed").' : '.$message);
                                    }
                                    else
                                    {
                                        $message = isset($payment_data['error']) ? $payment_data['error'] : "";
                                        throw new \Exception(trans("$string_file.payment_failed").' : '.$message);
                                    }
                                }
                                break;
                            case "FLUTTERWAVE":
                                $user = User::find($userId);
                                $cardss = UserCard::find($card_id);
                                $first_name = $user->first_name;
                                $last_name = $user->last_name;
                                $email = $user->email;
                                $detail = "taxi payment";
                                $phone = $user->UserPhone;
                                $order_id = $bookingId;
                                $amount = $amount;
                                $token = $cardss->token;

                                $payment_option = PaymentOption::where('slug', 'FLUTTERWAVE')->first();
                                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                                if(!empty($paymentoption))
                                {
                                    $secret_key=$paymentoption->api_secret_key;
                                    $curl = curl_init();
                                    $data = array(
                                        "token"=>$token,
                                        "currency"=>"NGN",
                                        "country"=>"NG",
                                        "amount"=>$amount,
                                        "email"=>$email,
                                        "first_name"=> $first_name,
                                        "last_name"=> $last_name,
                                        "narration"=> "Sample tokenized charge",
                                        "tx_ref"=> "tokenized-c-001"
                                    );
                                    $data=json_encode($data);

                                    curl_setopt_array($curl, array(
                                        CURLOPT_URL => "https://api.flutterwave.com/v3/tokenized-charges",
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => "",
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 0,
                                        CURLOPT_FOLLOWLOCATION => true,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => "POST",
                                        CURLOPT_POSTFIELDS =>$data,
                                        CURLOPT_HTTPHEADER => array(
                                            "Content-Type: application/json",
                                            "Authorization: Bearer $secret_key"
                                        ),
                                    ));

                                    $res = curl_exec($curl);
                                    curl_close($curl);
                                    $response = json_decode($res);

                                    if($response->status=='success'){
                                        $array_param['transaction_id'] = $response->data->id;
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    }else{
                                        $message = $response->message;
                                        throw new \Exception(trans("$string_file.payment_failed").' : '.$message);
                                    }
                                }else{
                                    return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                                }
                                break;
                            case "PayGate":
                                // its webview based payment so we will consider it any online payment
                                break;
                            case "PAYMAYA":
                                $user = User::find($userId);
                                $payment_option = PaymentOption::where('slug', 'PAYSTACK')->first();
                                $paymentConfig = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                                if (!empty($paymentConfig)) {
                                    $newCard = new PayMayaController();
                                    $charge = $newCard->card_payment($amount, $user->Country->isoCode, $card, $paymentConfig, 'USER', $bookingId ?? ($booking_transaction->booking_id ?? NULL));
                                    if (is_array($charge)) {
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    } else {
                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                    }
                                } else {
                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
                                }
                                break;
                            case "PAYHERE":
                                $user = User::find($userId);
                                $payment_option = PaymentOption::where('slug', 'PAYHERE')->first();
                                $paymentConfig = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                                if (!empty($paymentConfig)) {
                                    $newCard = new PayHereController();
                                    $charge = $newCard->CardPayment($amount, $user->Country->isoCode, $user->id, 1, $card, $bookingId ?? ($booking_transaction->booking_id ?? NULL), $order_id, $handyman_order_id);
                                    if ($charge['result'] == true) {
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    } else {
                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                    }
                                } else {
                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
                                }
                                break;
                            case "SDGEXPRESS":
                                $user = User::find($userId);
                                $payment_option = PaymentOption::where('slug', 'SDGEXPRESS')->first();
                                $paymentConfig = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                                if (!empty($paymentConfig)) {
                                    $newCard = new SDGExpressController();
                                    $charge = $newCard->card_payment($amount, $user->Country->isoCode, $card, $paymentConfig, 'USER', $bookingId ?? ($booking_transaction->booking_id ?? NULL), $order_id, $handyman_order_id);
                                    if ($charge['result'] == true) {
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    } else {
                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                    }
                                } else {
                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
                                }
                                break;
                            case "COVERAGEPAY":
                                $user = User::find($userId);
                                $payment_option = PaymentOption::where('slug', 'COVERAGEPAY')->first();
                                $paymentConfig = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                                if (!empty($paymentConfig)) {
                                    $newCard = new RandomPaymentController();
                                    $charge = $newCard->coverageCardPayment($amount, $card,$user,$paymentConfig,
                                        $bookingId, $order_id, $handyman_order_id);
                                    if ($charge == true) {
                                        $this->UpdateStatus($array_param);
                                        return true;
                                    } else {
                                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                                    }
                                } else {
                                    return response()->json(['result' => "0", 'message' => "configurations not found"]);
                                }
                                break;
                        }
                    }
                    break;
                case "3": // wallet
                    $user = User::find($userId);
                    if ($user->corporate_id != null){
                        if ($user->Corporate->wallet_balance < $amount) {
                            $remain = $user->Corporate->wallet_balance;
                            $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                            $sucess = false;
                        } else {
                            $remain = $user->Corporate->wallet_balance - $amount;
                            $this->CorporateWalletTransaction($user->Corporate, $amount, $bookingId, $order_id, $handyman_order_id);
                            $this->UpdateStatus($array_param);
                            $sucess = true;
                        }
                        $user->Corporate->wallet_balance = round_number($remain);
                        $user->Corporate->save();
                    }else{
                        if ($user->wallet_balance < $amount) {
                            $remain = $user->wallet_balance;
                            $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                            $sucess = false;
                        } else {
                            $remain = $user->wallet_balance - $amount;
                            $paramArray = array(
                                'user_id' => $user->id,
                                'booking_id' => $bookingId,
                                'order_id' => $order_id,
                                'handyman_order_id' => $handyman_order_id,
                                'amount' => $amount,
                                'narration' => (!empty($bookingId) || !empty($order_id)) ? 4 : 8,
                                'platform' => 2,
                                'payment_method' => 1,
                            );
                            // wallet can be debit for tip
                            WalletTransaction::UserWalletDebit($paramArray);
//                        CommonController::UserWalletDebit($user->id,$bookingId,$amount,4,2,1);
                            if(!empty($bookingId))
                            {
                                $this->UpdateStatus($array_param);
                            }
                            $sucess = true;
                        }
                        $user->wallet_balance = sprintf("%.2f", $remain);
                        $user->save();
                    }
                    return $sucess;
                    break;
                case "4":
                    $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                    return false;
                    break;
                case "5":
                    $this->UpdateStatus($array_param);
                    $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                    return true;
                    break;
//            case "6":
//                // Pay later Case
//                $booking = Booking::find($bookingId);
//                CommonController::AddUserRideOutstading($booking->user_id, $booking->driver_id, $booking->id, $amount);
//                $this->UpdateStatus($array_param);
//                $this->UpdateBookingOrderDetailStatus($array_param, $amount);
//                return true;
//                break;
                case "6":
                    // Pay later Case
                    if(!empty($bookingId))
                    {
                        $booking = Booking::find($bookingId);
                        CommonController::AddUserRideOutstading($booking->user_id, $booking->driver_id,$amount, $booking->id);
                        $this->UpdateStatus($array_param);
                        BookingDetail::where([['booking_id', '=', $bookingId]])->update(['pending_amount' => $amount]);
                    }
                    elseif(!empty($handyman_order_id))
                    {
                        $booking = HandymanOrder::find($handyman_order_id);
                        CommonController::AddUserRideOutstading($booking->user_id, $booking->driver_id,$amount, NULL,$handyman_order_id);
                        $this->UpdateStatus($array_param);
//
//                        BookingDetail::where([['booking_id', '=', $bookingId]])->update(['pending_amount' => $amount]);
                    }
                    return true;
                    break;
                case "7":
                    $user = User::find($userId);
                    $ewallet_result = false;
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id]])->first();
                    $payment_option = PaymentOption::find($paymentoption->payment_option_id);

                    $phone_card_no = isset($array_param['phone_card_no']) ? $array_param['phone_card_no'] :null;
                    // p($payment_option);
                    switch ($payment_option->slug){
                        case "AMOLE":
                            $ewallet_user_otp = isset($array_param['ewallet_user_otp_pin']) ? $array_param['ewallet_user_otp_pin'] :null;
                            // p($array_param);
                            $ewallet_pin_expire = isset($array_param['ewallet_pin_expire']) ? $array_param['ewallet_pin_expire'] :"";
                            if($ewallet_user_otp != NULL){
                                $header = array(
                                    "HDR_Signature: CgRs_7DpRQm8StaX9n5jBdLy8sHl67rzyNTqPR4ZpPPbmsFrMBJEbyq-mb5dnitt",
                                    "HDR_IPAddress: 35.178.56.137",
                                    "HDR_UserName: hubert2",
                                    "HDR_Password: test",
                                    "Content-Type: application/x-www-form-urlencoded"
                                );
                                $body_param = array(
                                    "BODY_CardNumber" => $phone_card_no,
                                    "BODY_ExpirationDate" => $ewallet_pin_expire,
                                    "BODY_PaymentAction" => "01",
                                    "BODY_PIN" => $ewallet_user_otp,
                                    "BODY_AmountX" => $amount,
                                    "BODY_AmoleMerchantID" => "HUBERTAXI",
                                    "BODY_OrderDescription" => "For Taxi Payment",
                                    "BODY_SourceTransID" => time(),
                                    "BODY_VendorAccount" => "",
                                    "BODY_AdditionalInfo1" => "",
                                    "BODY_AdditionalInfo2" => "",
                                    "BODY_AdditionalInfo3" => "",
                                    "BODY_AdditionalInfo4" => "",
                                    "BODY_AdditionalInfo5" => ""
                                );
                                // p($body_param);
                                $payment_result = EwalletController::amolePayment($header,$body_param);
                                // p($payment_result);
                                if (is_array($payment_result) && $payment_result[0]->MSG_ShortMessage == 'Success') {
                                    $ewallet_result = true;
                                } else {
                                    throw new \Exception($payment_result[0]->MSG_LongMessage);
                                    break;
                                }
                            }else{
                                $ewallet_result = false;
                            }
                            break;
                        default:
                            $ewallet_result = false;
                    }
                    // var_dump($ewallet_result && (!empty($bookingId) || !empty($order_id) || !empty($handyman_order_id)));
                    // p('end');
                    // p($order_id);
                    if($ewallet_result && (!empty($bookingId) || !empty($order_id) || !empty($handyman_order_id))){
                        $this->UpdateStatus($array_param);
                    }
                    // Booking::where([['id', '=', $bookingId]])->update(['is_ewallet_payment' => 1]);
                    // BookingDetail::where([['booking_id', '=', $bookingId]])->update(['pending_amount' => $amount]);
                    return $ewallet_result;
                    break;
            }
        }catch (\Exception $e)
        {
           throw new \Exception($e->getMessage());
        }
    }

    public function UpdateStatus(array $array_param)
    {
        // means update status only when payment is final

        try{
            $bookingId = isset($array_param['booking_id']) ? $array_param['booking_id'] : NULL;
            $order_id = isset($array_param['order_id']) ? $array_param['order_id'] : NULL;
            $handyman_order_id = isset($array_param['handyman_order_id']) ? $array_param['handyman_order_id'] : NULL;
            if(!empty($bookingId)){
                Booking::where('id', $bookingId)->update(['payment_status' => 1]);
            }
            if(!empty($order_id)){
                Order::where('id', $order_id)->update(['payment_status' => 1]);
            }
            if(!empty($handyman_order_id)){

                $payment_type = isset($array_param['payment_type']) ? $array_param['payment_type'] : "FINAL";
                if($payment_type != "ADVANCE")
                {
                    HandymanOrder::where('id', $handyman_order_id)->update(['payment_status' => 1]);
                }
            }
            if(!empty($array_param['transaction_id']))
            {
                BookingTransaction ::where([['booking_id','=', $bookingId],['order_id','=', $order_id],['handyman_order_id','=', $handyman_order_id]])->update(['transaction_id' => $array_param['transaction_id']]);
            }
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }

    }

    public function UpdateBookingOrderDetailStatus($array_param, $amount)
    {
        try{
            $bookingId = isset($array_param['booking_id']) ? $array_param['booking_id'] : NULL;
            $order_id = isset($array_param['order_id']) ? $array_param['order_id'] : NULL;
            $handyman_order_id = isset($array_param['handyman_order_id']) ? $array_param['handyman_order_id'] : NULL;
            $payment_method_id = isset($array_param['payment_method_id']) ? $array_param['payment_method_id'] : NULL;
            if(!empty($bookingId) && $payment_method_id != 4){
                BookingDetail::where([['booking_id', '=', $bookingId]])->update(['pending_amount' => $amount,'payment_failure' => 2]);
            }else{
                BookingDetail::where([['booking_id', '=', $bookingId]])->update(['pending_amount' => $amount]);
            }
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function CorporateWalletTransaction($corporate,$amount,$bookingId = NULL, $order_id = NULL, $handyman_order_id = NULL){
        CorporateWalletTransaction::create([
            'merchant_id' => $corporate->merchant_id,
            'corporate_id' => $corporate->id,
            'narration' => 'Amount Deduct on Ride',
            'transaction_type' => 2,
            'payment_method' => 'Wallet',
            'amount' => $amount,
            'platform' => 'Application',
            'booking_id' => $bookingId,
            'description' => 'Amount Deduct on Ride',
            'receipt_number' => $bookingId
        ]);
    }


    // topup wallet of user and driver by online payment method
    // online payment
    // using for order and handyman bookings too
    public function onlinePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
            'payment_option_id' => 'required|integer|exists:payment_options,id',
            'calling_from' => 'required|in:USER,DRIVER',
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {

            $calling_from = $request->calling_from;
            if($calling_from == "DRIVER")
            {
                $user = $request->user('api-driver');
            }
            else
            {
                $user = $request->user('api');
            }

            $string_file = $this->getStringFile(null,$user->Merchant);
            // PAYPHONE CASE
            if($request->payment_method_id == 4 && !empty($request->payment_option_id))
            {
                $option = PaymentOption::select('slug','id')->where('id',$request->payment_option_id)->first();
                if(!empty($option) && $option->slug == 'PAYPHONE')
                {
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $payphone =  new PayPhoneController;
                    $response = $payphone->validateUser($request,$payment_option_config,$calling_from);
                    if($response)
                    {
                        $amount = $request->amount;
                        $arr_payment_details = [];
                        $arr_payment_details['amount'] = [
                            'amount'=>$amount,
                            'tax'=>0,
                            'amount_with_tax'=>0,
                            'amount_without_tax'=>$amount,
                        ];
                        $arr_payment_details['booking_id'] = NULL;
                        $arr_payment_details['order_id'] = $request->order_id;
                        $arr_payment_details['handyman_order_id'] = $request->handyman_order_id;

                        $payment_option_config  = $option->PaymentOptionConfiguration;
                        $payphone =  new PayPhoneController;
                        $payphone_response = $payphone->paymentRequest($request,$payment_option_config,$arr_payment_details,$calling_from);
                        return $this->successResponse(trans("$string_file.success"),$payphone_response);
                    }
                }
                elseif(!empty($option) && $option->slug == 'AAMARPAY')
                {
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $payphone =  new AamarPayController;
                    $payphone_response = $payphone->aamarPayRequest($request,$payment_option_config,$calling_from);
                    return $this->successResponse(trans("$string_file.success"),$payphone_response);
                }
                elseif(!empty($option) && $option->slug == 'CASHFREE')
                {
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $cash_free =  new CashFreeController();
                    $cash_free_response = $cash_free->PaymentUrl($request,$payment_option_config,$calling_from);
                    return $this->successResponse(trans("$string_file.success"),$cash_free_response);
                }
                elseif(!empty($option) && $option->slug == 'PAYBOX')
                {
                    if($request->amount < 50)
                    {
                      $message = trans("$string_file.minimum_amount").' >=50';
                      return $this->failedResponse($message);
                    }
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $payphone =  new PayBoxController;
                    $payphone_response = $payphone->payboxRequest($request,$payment_option_config,$calling_from);
                    return $this->successResponse(trans("$string_file.success"),$payphone_response);
                }
                elseif(!empty($option) && $option->slug == 'MERCADOCARD')
                {
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $mercado =  new MercadoController;
                    if($request->request_from == 'PAYMENT')
                    {
                        $payphone_response = $mercado->getWebViwUrlSplit($request,$payment_option_config,
                            $calling_from);
                    }
                    else
                    {
                        $payphone_response = $mercado->getWebViwUrl($request,$payment_option_config,$calling_from);
                    }

                    return $this->successResponse(trans("$string_file.success"),$payphone_response);
                }
                elseif(!empty($option) && $option->slug == 'MERCADOPIX')
                {
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $mercado =  new MercadoController;
                    if($request->request_from == 'PAYMENT')
                    {
                        $payphone_response = $mercado->pixPaymentRequest($request, $payment_option_config, $calling_from);
                    }
                    else
                    {
                        $payphone_response = $mercado->pixPaymentRequestSplit($request, $payment_option_config,
                            $calling_from);
                    }
                    return $this->successResponse(trans("$string_file.success"),$payphone_response);
                }
                elseif(!empty($option) && $option->slug == 'EDAHAB')
                {
                    $validator = Validator::make($request->all(), [
                        'edahab_number' => 'required',
                    ]);
                    if ($validator->fails()) {
                        $errors = $validator->messages()->all();
                        return $this->failedResponse($errors[0]);
                    }
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $random_payment = new RandomPaymentController();
                    $payphone_response = $random_payment->edahabRequest($request,$payment_option_config,$calling_from);
                    return $this->successResponse(trans("$string_file.success"),$payphone_response);
                }
                elseif(!empty($option) && $option->slug == 'COVERAGEPAY')//convergepay
                {
                    $validator = Validator::make($request->all(), [
                        'card_number' => 'required',
                        'expire_date' => 'required',
                        'cvv' => 'required',
                    ]);
                    if ($validator->fails()) {
                        $errors = $validator->messages()->all();
                        return $this->failedResponse($errors[0]);
                    }
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $random_payment = new RandomPaymentController();
                    $payphone_response = $random_payment->coveragePay($request,$payment_option_config,$calling_from);
                    return $this->successResponse(trans("$string_file.success"),$payphone_response);
                }
                elseif(!empty($option) && $option->slug == 'ZAAD')//convergepay
                {
                    $validator = Validator::make($request->all(), [
                        'mwallet_account_number' => 'required',
                    ]);
                    if ($validator->fails()) {
                        $errors = $validator->messages()->all();
                        return $this->failedResponse($errors[0]);
                    }
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $random_payment = new RandomPaymentController();
                    $payphone_response = $random_payment->zaadRequest($request,$payment_option_config,$calling_from);
                    return $this->successResponse(trans("$string_file.success"),$payphone_response);
                }
                elseif(!empty($option) && $option->slug == 'PAYGATEGLOBAL')//convergepay
                {
                    $validator = Validator::make($request->all(), [
                         'payment_phone_number' => 'required|min:8,max:8',
                        'amount' => 'integer|min:150',
                    ]);
                    if ($validator->fails()) {
                        $errors = $validator->messages()->all();
                        return $this->failedResponse($errors[0]);
                    }
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $random_payment = new PaygateGlobalController();
                    $payphone_response = $random_payment->paymentRequest($request,$payment_option_config,$calling_from);
                    return $this->successResponse(trans("$string_file.success"),$payphone_response);
                }
                elseif(!empty($option) && $option->slug == 'SQUARE')//convergepay
                {
                    $validator = Validator::make($request->all(), [
//                        'payment_phone_number' => 'required|min:8,max:8',
//                        'amount' => 'int|min:150',
                    ]);
                    if ($validator->fails()) {
                        $errors = $validator->messages()->all();
                        return $this->failedResponse($errors[0]);
                    }
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $random_payment = new SquareController();
                    $payphone_response = $random_payment->paymentRequest($request,$payment_option_config,$calling_from);
                    return $this->successResponse(trans("$string_file.success"),$payphone_response);
                }
                elseif(!empty($option) && $option->slug == 'DPO')//convergepay
                {
                    $validator = Validator::make($request->all(), [
//                        'payment_phone_number' => 'required|min:8,max:8',
//                        'amount' => 'int|min:150',
                    ]);
                    if ($validator->fails()) {
                        $errors = $validator->messages()->all();
                        return $this->failedResponse($errors[0]);
                    }
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $random_payment = new DpoController();
                    $payphone_response = $random_payment->paymentRequest($request,$payment_option_config,$calling_from);
                    return $this->successResponse(trans("$string_file.success"),$payphone_response);
                }
                elseif(!empty($option) && $option->slug == 'TOUCHPAY')//convergepay
                {
                    $validator = Validator::make($request->all(), [
                        'recipient_number' => 'required|min:8,max:8',
                        'amount' => 'int|min:100',
                    ]);
                    if ($validator->fails()) {
                        $errors = $validator->messages()->all();
                        return $this->failedResponse($errors[0]);
                    }
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $random_payment = new TouchPayController();
                    $payphone_response = $random_payment->paymentRequest($request,$payment_option_config,$calling_from);
                    return $this->successResponse(trans("$string_file.success"),$payphone_response);
                }
            }
            elseif(!empty($option) && $option->slug == 'KUSHKI')//KUSHKI payment gateway
            {
                $payment_option_config  = $option->PaymentOptionConfiguration;
                $random_payment = new KushkiController();
                if($request->payment_type == "CARD")
                {
                    // p('in');
//                        $validator = Validator::make($request->all(), [
//                            'card_number' => 'required',
//                            'exp_year' => 'required',
//                            'exp_month' => 'required',
//                            'cvv' => 'required',
//                            'document_type' => 'required',
//                            'document_number' => 'required',
                    //'payment_type' => 'required', // CARD/TRANSFERIN
//                        ]);

                    // if ($validator->fails()) {
                    //     $errors = $validator->messages()->all();
                    //     return $this->failedResponse($errors[0]);
                    // }

                    $payphone_response = $random_payment->paymentRequest($request,$payment_option_config,$calling_from);

                }
                elseif($request->payment_type == "TRANSFERIN")
                {
                    $validator = Validator::make($request->all(), [
                        'document_type' => 'required',
                        'document_number' => 'required',
                        //'payment_type' => 'required', // CARD/TRANSFERIN
                    ]);

                    if ($validator->fails()) {
                        $errors = $validator->messages()->all();
                        return $this->failedResponse($errors[0]);
                    }


                    $payphone_response = $random_payment->transferInRequest($request,$payment_option_config,$calling_from);
                }


                return $this->successResponse(trans("$string_file.success"),$payphone_response);
            }
        }catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }
    }

//    Check payment status
    public function onlinePaymentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
            'payment_option_id' => 'required|integer|exists:payment_options,id',
            'calling_from' => 'required|in:USER,DRIVER',
//            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {

            $calling_from = $request->calling_from;
            if($calling_from == "DRIVER")
            {
                $user = $request->user('api-driver');
            }
            else
            {
                $user = $request->user('api');
            }

            $string_file = $this->getStringFile(null,$user->Merchant);
            // PAYPHONE CASE
            $option = PaymentOption::select('slug','id')->where('id',$request->payment_option_id)->first();
            // p($option);
            if(!empty($request->payment_option_id) && $option->slug == 'SQUARE')
            {


//                    $validator = Validator::make($request->all(), [
////                        'payment_phone_number' => 'required|min:8,max:8',
////                        'amount' => 'int|min:150',
//                    ]);
//                    if ($validator->fails()) {
//                        $errors = $validator->messages()->all();
//                        return $this->failedResponse($errors[0]);
//                    }
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $random_payment = new SquareController();
                    $payphone_response = $random_payment->paymentStatus($request,$payment_option_config,$calling_from);
                    return $this->successResponse(trans("$string_file.success"),$payphone_response);

//                return $this->successResponse(trans("$string_file.success"),$payphone_response);
            }
            if(!empty($request->payment_option_id) && $option->slug == 'DPO')
            {

                    // p('in');
//                    $validator = Validator::make($request->all(), [
////                        'payment_phone_number' => 'required|min:8,max:8',
////                        'amount' => 'int|min:150',
//                    ]);
//                    if ($validator->fails()) {
//                        $errors = $validator->messages()->all();
//                        return $this->failedResponse($errors[0]);
//                    }
                    $payment_option_config  = $option->PaymentOptionConfiguration;
                    $random_payment = new DpoController();
                    $payphone_response = $random_payment->paymentStatus($request,$payment_option_config,$calling_from);
                    // p($payphone_response);
                    return $this->successResponse(trans("$string_file.success"),$payphone_response);

//                return $this->successResponse(trans("$string_file.success"),$payphone_response);
            }
        }catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }
    }
}
