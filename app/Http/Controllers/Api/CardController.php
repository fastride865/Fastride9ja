<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentMethods\Paygate\PaygateController;
use App\Http\Controllers\PaymentMethods\PayHere\PayHereController;
use App\Http\Controllers\PaymentMethods\PayMaya\PayMayaController;
use App\Http\Controllers\PaymentMethods\Paystack\Paystack;
use App\Http\Controllers\PaymentMethods\paytm\PaytmKit\lib\encdec_paytm;
use App\Http\Controllers\PaymentMethods\RandomPaymentController;
use App\Http\Controllers\PaymentMethods\SDGExpress\SDGExpressController;
use App\Http\Controllers\PaymentMethods\StripeController;
use App\Http\Resources\DriverLoginResource;
use App\Models\Driver;
use App\Models\DriverCard;
use App\Models\PaymentOptionsConfiguration;
use App\Models\UserCard;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PaymentOption;
use DB;
use App\Http\Controllers\Helper\WalletTransaction;
use YoAPI;
use App\Models\User;
use App\Http\Controllers\PaymentMethods\DPO\DpoController;

class CardController extends Controller
{
    use ApiResponseTrait,MerchantTrait;
    public function CardDetails($cardId)
    {
        $card = UserCard::with('User')->find($cardId);
        $string_file = $this->getStringFile(NULL, $card->User->Merchant);
        $cards = [];
        if (!empty($card)) {
            switch ($card->PaymentOption->slug) {
                case "STRIPE":
                    $merchant_id = $card->User->merchant_id;
                    $payment_option = PaymentOption::where('slug', 'STRIPE')->first();
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $stripe = $paymentoption->api_secret_key;
                        $newCard = new StripeController($stripe);
                        $cards = $newCard->CustomerDetails($card);
                    } else {
                        return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                    }
                    break;
                case "SENANGPAY":
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card->card_number,
                        'card_type' => "",
                        'exp_month' => "",
                        'exp_year' => $card->expiry_date
                    );
                    break;
                case "PAYSTACK":
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card->card_number,
                        'card_type' => "",
                        'exp_month' => $card->exp_month,
                        'exp_year' => $card->exp_year
                    );
                    break;
                case "CIELO":
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card->card_number,
                        'expiry_date' => $card->expiry_date,
                        'card_type' => $card->card_type
                    );
                    break;
                case "BANCARD":
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card->card_number,
                        'expiry_date' => $card->expiry_date,
                        'card_type' => $card->card_type
                    );
                    break;
                case "DPO":
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card->card_number,
                        'expiry_date' => "",
                        'card_type' => $card->card_type
                    );
                    break;
                case "PEACHPAYMENT":
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card->card_number,
                        'expiry_date' => "",
                        'card_type' => $card->card_type
                    );
                    break;
                case "HYPERPAY":
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card->card_number,
                        'expiry_date' => "",
                        'card_type' => $card->card_type
                    );
                    break;
                case "MONERIS":
                    $merchant_id = $card->User->merchant_id;
                    $payment_option = PaymentOption::where('slug', 'MONERIS')->first();
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $storeId = $paymentoption->api_secret_key;
                        $apiToken = $paymentoption->auth_token;
                        $newCard = new RandomPaymentController();
                        $cardToken = $card->token;
                        $cards = $newCard->MonerisViewCard($cardToken,$storeId,$apiToken);
                        if (!empty($cards) && $cards['api_response']['ResponseCode'] == '001') {
                            $cards = array(
                                'card_id' => $card->id,
                                'card_number' => $cards['card_details']['masked_pan'],
                                'card_type' => '',
                                'exp_month' => '',
                                'exp_year' => '',
                                'exp_date' => $cards['card_details']['expdate']
                            );
                        }
                    } else {
                        return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                    }
                    break;
                case "EZYPOD":
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card->card_number,
                        'expiry_date' => "",
                        'card_type' => $card->card_type
                    );
                    break;
                case "CONEKTA":
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card->card_number,
                        'expiry_date' => "",
                        'card_type' => $card->card_type
                    );
                    break;
                case "PAYU":
                    $cc_number = $card->card_number;
                    $card_number = $this->getCardNumber($cc_number);
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card_number,
                        'expiry_date' => "",
                        'card_type' => $card->card_type
                    );
                    break;
                case "FLUTTERWAVE":
                    $cc_number = $card->card_number;
                    $card_number = $this->getCardNumber($cc_number);
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card_number,
                        'expiry_date' => "",
                        'card_type' => $card->card_type
                    );
                    break;
                case "PayGate":
                    $cc_number = $card->card_number;
                    $card_number = $this->getCardNumber($cc_number);
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card_number,
                        'expiry_date' => "",
                        'card_type' => $card->card_type
                    );
                    break;
                case "TELR":
                    $cc_number = $card->card_number;
                    $card_number = $this->getCardNumber($cc_number);
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card_number,
                        'expiry_date' => "",
                        'card_type' => $card->card_type
                    );
                    break;
                case "PAYMAYA":
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card->card_number,
                        'card_type' => $card->card_type,
                        'exp_month' => $card->exp_month,
                        'exp_year' => $card->exp_year,
                        'exp_date' => $card->exp_date
                    );
                    break;
                default:
                    $cards = array(
                        'card_id' => $card->id,
                        'card_number' => $card->card_number,
                        'expiry_date' => $card->expiry_date,
                        'card_type' => $card->card_type
                    );
                    break;
            }
            return $cards;
        }
        return [];
    }

    // wallet recharge function
    public function CardPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_id' => 'required|exists:user_cards,id',
            'amount' => 'required',
            'currency' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $card = UserCard::find($request->card_id);
        switch ($card->PaymentOption->slug) {
            case "STRIPE":
                $payment_option = PaymentOption::where('slug', 'STRIPE')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $stripe = $paymentoption->api_secret_key;
                    $newCard = new StripeController($stripe);
                    $charge = $newCard->Charge($request->amount, $request->currency, $card->token, $user->email);
                    $result = "0";
                    if (is_array($charge)) {
                        $result = "1";
                        $message = trans("$string_file.success");
                    } else {
                        $message = $charge;
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "PAYSTACK":
                $payment_option = PaymentOption::where('slug', 'PAYSTACK')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id' ,'=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $newCard = new RandomPaymentController();
                    $charge = $newCard->ChargePaystack($request->amount, $request->currency, $card->token, $user->email, $paymentoption->api_secret_key, $paymentoption->payment_redirect_url, 'USER');
                    $result = "0";
                    if (!empty($charge) && array_key_exists('id', $charge)) {
                        $result = "1";
                        $message =trans("$string_file.success");
                    }
                    else {
                        $message = $charge[0];
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "CIELO":
                $payment_option = PaymentOption::where('slug', 'CIELO')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id' ,'=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $newCard = new RandomPaymentController();
                    $userName = $user->first_name . " " . $user->last_name;
                    $charge = $newCard->ChargeCielo($request->amount, $userName, $card->card_type, $card->token, $paymentoption->api_secret_key, $paymentoption->api_public_key, $paymentoption->payment_redirect_url);
                    $result = "0";
                    if (is_array($charge)) {
                        $result = "1";
                        $message = trans("$string_file.success");
                    } else {
                        $message = $charge;
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "BANCARD":
                $payment_option = PaymentOption::where('slug', 'BANCARD')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id' ,'=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $charge = new RandomPaymentController();
                    // $shopProcessId = uniqid();
                    $shopProcessId = rand(11111, 99999);
                    $number_items = 1;
                    $amount = number_format($request->amount, 2);
                    $token = md5('.'.$paymentoption->api_secret_key.$shopProcessId."charge".$amount.$number_items);
                    $payed = $charge->ChargeBancard($paymentoption->payment_redirect_url, $paymentoption->api_public_key, $token, $shopProcessId, $request->amount, $request->currency, $card->token);
                    $result = "0";
                    if (is_array($payed) && $payed['result'] == 1) {
                        $result = "1";
                        $message = trans("$string_file.success");
                    } else {
                        $message = $payed['message'];
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "DPO":
                $payment_option = PaymentOption::where('slug', 'DPO')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id' ,'=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {

                    $payment = new DpoController();
                    $charge = $payment->cardPayment("USER",$user,$paymentoption,$card,$request->amount);

                    if (is_array($charge) && $charge['status'] == true) {
                        $result = "1";
                        $message = $charge['message'];
//                        $this->UpdateStatus($array_param);
//                        return true;
                    } else {
                        $message = $charge['message'];
//                        $this->UpdateBookingOrderDetailStatus($array_param, $amount);
                    }

//                    $charge = new RandomPaymentController();
//                    $charge = $charge->ridePaymentDPO($paymentoption->auth_token, $request->amount, $request->currency, $user->email, $user->first_name, $user->last_name, $user->UserPhone, $card->token);
//                    if (is_array($charge)) {
//                        $result = "1";
//                        $message = trans("$string_file.success");
//                    } else {
//                        $message = $charge;
//                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "MONERIS":
                $payment_option = PaymentOption::where('slug', 'MONERIS')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id' ,'=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $charge = new RandomPaymentController();
                    $chargeData = $charge->MonerisMakePayment($user->id,$card->token,$request->amount,$paymentoption->api_secret_key,$paymentoption->auth_token);
                    if ($chargeData['ResponseCode'] == '027' && !empty($chargeData['DataKey'])) {
                        $result = "1";
                        $message = trans("$string_file.success");
                    } else {
                        $result = "0";
                        $message = $chargeData['Message'];
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "SENANGPAY":
                $payment_option = PaymentOption::where('slug', 'SENANGPAY')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id' ,'=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $condition = $paymentoption['gateway_condition'];
                    $payment_redirect_url = $paymentoption['payment_redirect_url'];
                    if ($condition == 2) {
                        $payment_redirect_url = "https://sandbox.senangpay.my/apiv1/pay_cc/".$paymentoption->api_secret_key;
                    }
                    $charge = new RandomPaymentController();
                    $reqe = $request->all();
                    $name = $user->first_name . "" . $user->last_name;
                    $email = $user->email;
                    $phone = $user->UserPhone;
                    $order_id = time();
                    $amount = $reqe['amount'] * 100;
                    $card_token = $card->token;
                    $secretkey = $paymentoption['api_secret_key'];
                    $detail = "taxi payment";
                    # assuming all of the data passed is correct and no validation required. Preferably you will need to validate the data passed
                    $hashed_string = md5($secretkey.urldecode($detail).urldecode($amount).urldecode($order_id));
                    $chargeData = $charge->SENANGPAYMakePayment($payment_redirect_url,$paymentoption['auth_token'],$name,$email,$phone,$order_id,$amount,$card_token,$detail);
                    if ($chargeData['result'] == 'success' ) {
                        $response = json_decode($chargeData['data'], true);
                        if($response['status'] == 1){
                            $result = "1";
                            $message = trans("$string_file.success");
                        }else{
                            $result = "0";
                            $message = $chargeData['data'];
                        }
                    }else{
                        $result = "0";
                        $message = $chargeData['data'];
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "CONEKTA":
                $payment = new RandomPaymentController();
                $payment_config = PaymentOptionsConfiguration::where([['payment_option_id','=',$card->payment_option_id],['merchant_id','=',$card->User->merchant_id]])->first();
                if(!empty($payment_config))
                {
                    $arr_data = ['currency'=>$request->currency,'quantity'=>1,'name'=>"Wallet Recharge",'amount'=>$request->amount,'private_key'=>$payment_config->api_secret_key,'customer_token'=>$card->user_token];
                    $payment_response =  $payment->createConektaOrder($arr_data);
                    $payment_response = json_decode($payment_response);
                    if (!empty($payment_response->id) && $payment_response->payment_status == 'paid') {
                        return $this->successResponse(trans("$string_file.money_added_in_wallet"));
                    } else {
                        $message = isset($payment_response->details[0]) ? $payment_response->details[0]->debug_message : $payment_response->type;
                        return $this->failedResponse($message);
                    }
                }
                else{
                    return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                }
                break;
            case "PAYU":
                $payment_config = PaymentOptionsConfiguration::where([['payment_option_id','=',$card->payment_option_id],['merchant_id','=',$user->merchant_id]])->first();
                $payment = new RandomPaymentController();
                $locale = "en";
                $amount = $request->amount;
                if(isset($request->calling_from) && $request->calling_from == "WALLET"){
                    $payment_data = $payment->payuPaymentAuthorizationAndCapture($user, $amount, $card,$payment_config,$locale);
                }else{
                    $payment_data = $payment->payuPayment($user, $amount, $card,$payment_config,$locale);
                }
                if(isset($payment_data['code']) && $payment_data['code'] == "SUCCESS" && $payment_data['transactionResponse']['state'] == "APPROVED")
                {
                    $result="1";
                    $message = trans("$string_file.success");
                    $array_param['transaction_id'] = $payment_data['transactionResponse']['transactionId'];
                }
                elseif(isset($payment_data['code']) && $payment_data['code'] == "SUCCESS" && $payment_data['transactionResponse']['state'] == "DECLINED")
                {
                    $message = isset($payment_data['transactionResponse']['responseCode']) ? $payment_data['transactionResponse']['responseCode'] : "";
                    return $this->failedResponse(trans("$string_file.payment_failed").' : '.$message);
                }
                else
                {
                    $message = isset($payment_data['error']) ? $payment_data['error'] : "";
                    return $this->failedResponse(trans("$string_file.payment_failed").' : '.$message);
                }
                break;
            case "FLUTTERWAVE":
                $amount=$request->amount;
                $token = $card->token;

                $payment_option = PaymentOption::where('slug', 'FLUTTERWAVE')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if(!empty($paymentoption))
                {
                    $secret_key=$paymentoption->api_secret_key;
                    $curl = curl_init();
                    $data = array(
                       "token"=>$token,
                       "currency"=>$request->currency,
                       "country"=>($request->currency=="ZAR")?"ZA":"NG",
                       "amount"=>$amount,
                       "email"=>$user->email,
                       "first_name"=> $user->first_name,
                       "last_name"=> $user->last_name,
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
                        $result = "1";
                        $message = trans("$string_file.success");
                    }else{
                        $result="0";
                        $message=$response->message;
                    }
                }
                else{
                    return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                }
                break;
            case "PAYMAYA":
                $payment_option = PaymentOption::where('slug', 'PAYMAYA')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $newCard = new PayMayaController();
                    $charge = $newCard->card_payment($request->amount, $request->currency, $card, $paymentoption, 'USER');
                    $result = "0";
                    if (is_array($charge)) {
                        $result = "1";
                        $message = trans("$string_file.success");
                    } else {
                        $message = $charge;
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "PAYHERE":
                $payment_option = PaymentOption::where('slug', 'PAYHERE')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $newCard = new PayHereController();
                    $charge = $newCard->CardPayment($request->amount, $request->currency, $user->id, 1, $card);
                    $result = "0";
                    if ($charge['result'] == true) {
                        $result = "1";
                        $message = trans("$string_file.success");
                    } else {
                        $message = $charge['message'];
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "SDGEXPRESS":
                $merchant_id = $request->merchant_id;
                $payment_option = PaymentOption::where('slug', 'SDGEXPRESS')->first();
                $paymentOption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentOption)) {
                    $sdg_card = new SDGExpressController();
                    $sdg_card_response = $sdg_card->card_payment($request->amount, $request->currency, $card, $paymentOption, 'USER');
                    $result = "0";
                    if ($sdg_card_response['result'] == true) {
                        $result = "1";
                        $message = trans("$string_file.success");
                    } else {
                        $message = $sdg_card_response['message'];
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "COVERAGEPAY":
                try
                {
                    $payment_option = PaymentOption::where('slug', 'COVERAGEPAY')->first();
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $newCard = new RandomPaymentController();
                        $charge = $newCard->coverageCardPayment($request->amount, $card,$user,$paymentoption);
                        $result = "0";
                        if ($charge == true) {
                            $result = "1";
                            $message = trans("$string_file.success");
                        } else {
                            $message = trans("$string_file.error");
                        }
                    } else {
                        return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                    }
                }catch (\Exception $e)
                {
                    return response()->json(['result' => 0, 'message' => $e->getMessage()]);
                }

                break;
        }
        return response()->json(['result' => $result, 'message' => $message]);
    }

    // payment from card
    public function DriverCardPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_id' => 'required|exists:driver_cards,id',
            'amount' => 'required',
            'currency' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $driver = $request->user('api-driver');
        $string_file = $this->getStringFile(NULL,$driver->Merchant);
        $card = DriverCard::find($request->card_id);
        switch ($card->PaymentOption->slug) {
            case "STRIPE":
                $payment_option = PaymentOption::where('slug', 'STRIPE')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $driver->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $stripe = $paymentoption->api_secret_key;
                    $newCard = new StripeController($stripe);
                    $charge = $newCard->Charge($request->amount, $request->currency, $card->token, $driver->email);
                    $result = "0";
                    if (is_array($charge)) {
                        $result = "1";
                        $message = trans("$string_file.payment_done");
                    } else {
                        $message = $charge;
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "SENANGPAY":
                $card = DriverCard::find($request->card_id);
                $driver = Driver::find($card->driver_id);
                $reqe = $request->all();
                $name = $driver->first_name . "" . $driver->last_name;
                $email = $driver->email;
                $detail = "taxi payment";
                $phone = $driver->phoneNumber;
                $order_id = $reqe['order_id'];
                $amount = $reqe['amount'] * 100;
                $token = $card->token;

                $payment_option = PaymentOption::where('slug', 'SENANGPAY')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $driver->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
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
                        return response()->json(['result' => $response['status'], 'transaction_id' => $response['transaction_id'], 'order_id' => $response['order_id'], 'amount_paid' => $response['amount_paid'], 'message' => $response['msg'], 'hash' => $response['hash']]);
                    } else {
                        return response()->json(['result' => $response['status'], 'message' => $response['msg']]);
                    }
                }
                break;
            case "PAYSTACK":
                $payment_option = PaymentOption::where('slug', 'PAYSTACK')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $driver->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $newCard = new RandomPaymentController();
                    $charge = $newCard->ChargePaystack($request->amount, $request->currency, $card->token, $driver->email, $paymentoption->api_secret_key, $paymentoption->payment_redirect_url, 'DRIVER');
                    $result = "0";
                    if (array_key_exists('id', $charge)) {
                        $result = "1";
                        $message = trans("$string_file.success");
                    } else {
                        $message = $charge[0];
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "MONERIS":
                $payment_option = PaymentOption::where('slug', 'MONERIS')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $driver->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $charge = new RandomPaymentController();
                    $chargeData = $charge->MonerisMakePayment($driver->id,$card->token,$request->amount,$paymentoption->api_secret_key,$paymentoption->auth_token);
                    if ($chargeData['ResponseCode'] == 027 && !empty($chargeData['DataKey'])) {
                        $result = "1";
                        $message = trans("$string_file.success");
                    } else {
                        $result = "0";
                        $message = $chargeData['Message'];
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "PAYMAYA":
                $payment_option = PaymentOption::where('slug', 'PAYMAYA')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $driver->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $newCard = new PayMayaController();
                    $charge = $newCard->card_payment($request->amount, $request->currency, $card, $paymentoption, 'DRIVER');
                    $result = "0";
                    if (is_array($charge)) {
                        $result = "1";
                        $message = trans("$string_file.success");
                    } else {
                        $message = $charge;
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "PAYHERE":
                $payment_option = PaymentOption::where('slug', 'PAYHERE')->first();
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $driver->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $newCard = new PayHereController();
                    $charge = $newCard->CardPayment($request->amount, $request->currency, $driver->id, 2, $card);
                    $result = "0";
                    if ($charge['result'] == true) {
                        $result = "1";
                        $message = trans("$string_file.success");
                    } else {
                        $message = $charge['message'];
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "SDGEXPRESS":
                $merchant_id = $request->merchant_id;
                $payment_option = PaymentOption::where('slug', 'SDGEXPRESS')->first();
                $paymentOption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentOption)) {
                    $sdg_card = new SDGExpressController();
                    $sdg_card_response = $sdg_card->card_payment($request->amount, $request->currency, $card, $paymentOption, 'DRIVER');
                    $result = "0";
                    if ($sdg_card_response['result'] == true) {
                        $result = "1";
                        $message = trans("$string_file.success");
                    } else {
                        $message = $sdg_card_response['message'];
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "COVERAGEPAY":
                try
                {
                    $payment_option = PaymentOption::where('slug', 'COVERAGEPAY')->first();
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $driver->merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $newCard = new RandomPaymentController();
                        $charge = $newCard->coverageCardPayment($request->amount, $card,$driver,$paymentoption,null,
                            null,null,'DRIVER');
                        $result = "0";
                        if ($charge == true) {
                            $result = "1";
                            $message = trans("$string_file.success");
                        } else {
                            $message = trans("$string_file.error");
                        }
                    } else {
                        return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                    }
                }catch (\Exception $e)
                {
                    return response()->json(['result' => 0, 'message' => $e->getMessage()]);
                }
                break;
                case "DPO":
                try
                {
                    $payment_option = PaymentOption::where('slug', 'DPO')->first();
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $driver->merchant_id],['payment_option_id' ,'=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {

                        $payment = new DpoController();
                        $charge = $payment->cardPayment("DRIVER",$driver,$paymentoption,$card,$request->amount);
                        if (is_array($charge) && $charge['status'] == true) {
                            $result = "1";
                            $message = $charge['message'];
                        } else {
                            $result = "0";
                            $message = $charge['message'];
                        }

                    } else {
                        return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                    }

                }catch (\Exception $e)
                {
                    return response()->json(['result' => 0, 'message' => $e->getMessage()]);
                }
                break;
        }
        return response()->json(['result' => $result, 'message' => $message]);
    }

    // delete card from user account
    public function DriverDeleteCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_id' => 'required|exists:driver_cards,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $card = DriverCard::with('Driver')->find($request->card_id);
        $merchant_id = $card->Driver->merchant_id;
        $string_file = $this->getStringFile(NULL,$card->Driver->Merchant);
        $payment_option = $card->PaymentOption;
        switch ($payment_option->slug) {
            case "STRIPE":
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $stripe = $paymentoption->api_secret_key;
                    $newCard = new StripeController($stripe);
                    $newCard->DeleteCustomer($card->token);
                } else {
                    return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                }
                break;
            case "MONERIS":
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $newCard = new RandomPaymentController();
                    $result =  $newCard->MonerisDeleteCard($paymentoption->api_secret_key, $paymentoption->auth_token, $card->token);
                    if($result['ResponseCode'] != 001){
                        return $this->failedResponse($result['Message']);
                    }
                } else {
                    return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                }
                break;
                case "PayGate":
                $paygate_obj =  new PaygateController;
                $delete =  $paygate_obj->deleteCard($request,$card,$card->Driver,2);
                if(!$delete)
                {
                    return $this->failedResponse(trans("$string_file.card_cant_deleted"));
                }
                break;
            case "DPO":
                $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentoption)) {
                    $newCard = new DpoController;
                   $result_data = $newCard->deleteCard($paymentoption,$card,"DRIVER");
                   if(!$result_data['status'])
                   {
                       return $this->failedResponse($result_data['message']);
                   }
                } else {
                    return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                }
                break;

        }
        // soft delete card
//        $card->delete();
        $card->card_delete = 1;
        $card->save();

        $message = trans("$string_file.card_deleted");
        return $this->successResponse($message);

    }

    // delete user card
    public function DeleteCard(Request $request)
    {
        if(isset($request->payment_option) && $request->payment_option == 'EZYPOD'){
            if(isset($request->token) && $request->token != ''){
                $card = UserCard::with('User')->where([['token','=',$request->token]])->first();
            }else{
                return response()->json(['result' => 0, 'message' => 'Token Required']);
            }
        }else{
            $validator = Validator::make($request->all(), [
                'card_id' => 'required|exists:user_cards,id',
            ]);
            if ($validator->fails()) {
                $errors = $validator->messages()->all();
                return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
            }
            $card = UserCard::with('User')->find($request->card_id);

        }
        $string_file = $this->getStringFile(NULL,$card->User->Merchant);
        $merchant_id = $card->User->merchant_id;
        $userID = $card->User->id;
        if(isset($card->PaymentOption) && $card->PaymentOption->slug != null){
            $payment_option = $card->PaymentOption;
            switch ($payment_option->slug) {
                case "STRIPE":
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $stripe = $paymentoption->api_secret_key;
                        $newCard = new StripeController($stripe);
                        $newCard->DeleteCustomer($card->token);
                    } else {
                        return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
//                            response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                    }
                    break;
                case"BANCARD":
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $token = md5('.' . $paymentoption->api_secret_key . 'delete_card' . $userID . $card->token);
                        $newCard = new RandomPaymentController();
                        $newCard->DeleteCardBancard($card->token, $userID, $token, $paymentoption->api_public_key);
                    } else {
                        return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
//                        return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                    }
                    break;
                case "PEACHPAYMENT":
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $newCard = new RandomPaymentController();
                        $result =  $newCard->DeleteCardPeachPayment($paymentoption->api_secret_key, $paymentoption->auth_token, $card->token, $paymentoption->tokenization_url);
                        if(!$result){
                            return response()->json(['result' => 0, 'message' => $result]);
                        }
                    } else {
                        return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                    }
                    break;
                case "MONERIS":
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $newCard = new RandomPaymentController();
                        $result =  $newCard->MonerisDeleteCard($paymentoption->api_secret_key, $paymentoption->auth_token, $card->token);
                        if($result['ResponseCode'] != 001){
                            return response()->json(['result' => '0', 'message' => $result['Message'], 'data' => []]);
                        }
                    } else {
                        return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                    }
                    break;
                case "EZYPOD":
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $card->delete();
                        return $this->successResponse(trans("$string_file.card_deleted"));
                    } else {
                        return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                    }
                    break;
                case "FLUTTERWAVE":
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $card->delete();
                        return $this->successResponse(trans("$string_file.card_deleted"));
                    } else {
                        return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                    }
                    break;
                case "PayGate":
                    $paygate_obj =  new PaygateController;
                    $delete =  $paygate_obj->deleteCard($request,$card,$card->User,1);
                    if(!$delete)
                    {
                        return $this->failedResponse(trans("$string_file.card_cant_deleted"));
                    }
                    break;
                case "DPO":
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $newCard = new DpoController;
                        $result_data = $newCard->deleteCard($paymentoption,$card,"USER");
                        if(!$result_data['status'])
                        {
                            return $this->failedResponse($result_data['message']);
                        }
                    } else {
                        return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                    }
                    break;
            }
//            $card->delete();
            $card->card_delete = 1;
            $card->save();
            return $this->successResponse(trans("$string_file.card_deleted"));
        }else {
            return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
        }
    }

    public function DriverCards(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_option' => 'sometimes|required|exists:payment_options,slug',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//                response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        try {
            $driver = $request->user('api-driver');
            $string_file = $this->getStringFile(NULL, $driver->Merchant);

            $user_cards = DriverCard::with(["PaymentOption" => function ($query) use ($request) {
                if (isset($request->payment_option) && $request->payment_option != "") {
                    $query->where("slug", $request->payment_option);
                }
            }])->whereHas("PaymentOption", function ($query) use ($request) {
                if (isset($request->payment_option) && $request->payment_option != "") {
                    $query->where("slug", $request->payment_option);
                }
            })
                ->where([['driver_id', '=', $driver->id]])
                ->where([['card_delete', '=', NULL]])
                ->get();
            if (!empty($user_cards)) {
                $cards = $this->getCardDetails($user_cards, $driver->merchant_id);
                return $this->successResponse(trans("$string_file.success"),$cards);
            } else {
                if($request->payment_option == "PayGate" || $request->payment_option == "DPO")
                  {
                    $message = trans("$string_file.paygate_card_add_instruction_driver");
                  }
                    else
                    {
                        $message = trans("$string_file.no_card");
                    }
                return $this->failedResponse($message);
            }
        } catch (\Exception $exception) {
            return $this->failedResponse($exception->getMessage());
        }
    }

    public function Cards(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_option' => 'sometimes|required|exists:payment_options,slug',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        try {
            $user = $request->user('api');
            // p($user->id);
            $string_file = $this->getStringFile(NULL, $user->Merchant);

            $user_cards = UserCard::with(["PaymentOption" => function ($query) use ($request) {
                if (isset($request->payment_option) && $request->payment_option != "") {
                    $query->where("slug", $request->payment_option);
                }
            }])->whereHas("PaymentOption", function ($query) use ($request) {
                if (isset($request->payment_option) && $request->payment_option != "") {
                    $query->where("slug", $request->payment_option);
                }
            })
                ->where([['card_delete', '=', NULL]])
                ->where([['user_id', '=', $user->id]])->get();
            // p($user_cards);
            if (!empty($user_cards) && $user_cards->count() > 0) {
                $cards = $this->getCardDetails($user_cards, $user->merchant_id);
                return $this->successResponse(trans("$string_file.success"),$cards);

            } else {
                    $message = "";
                    if($request->payment_option == "PayGate" || $request->payment_option == "DPO")
                    {
                        $message = trans("$string_file.paygate_card_add_instruction_user");
                    }
                    else
                    {
                        $message = trans("$string_file.no_card");
                    }
                return $this->failedResponse($message);
            }
        } catch (\Exception $exception) {
            return $this->failedResponse($exception->getMessage());
        }
    }

    public function getUserAllCards($userID)
    {
        $user = User::find($userID);
        $cardList = UserCard::with(['User','PaymentOption'])->where([['user_id', '=', $userID],['status', '=', '1']])->get();
        $merchant_id = $user->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $cards = array();
        if (!empty($cardList)) {
            foreach ($cardList as $value) {
                $slug = $value->PaymentOption->slug;
                switch ($slug) {
                    case "STRIPE":
                        $merchant_id = $cardList->pluck('User.merchant_id')[0];
                        $payment_option = PaymentOption::where('slug', 'STRIPE')->first();
                        $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                        if (!empty($paymentoption)) {
                            $stripe = $paymentoption->api_secret_key;
                            $newCard = new StripeController($stripe);
                            $cards = $newCard->ListCustomer($cardList);
                        } else {
                            return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                        }
                        break;
                    case "SENANGPAY":
                        $cards = $cardList->toArray();
                        break;
                    case "PAYSTACK":
                        $cards = $cardList->toArray();
                        break;
                    case "CIELO":
                        $cards = $cardList->toArray();
                        break;
                    case"BANCARD":
                        $cards = $cardList->toArray();
                        break;
                    case "DPO":
                        $cards = $cardList->toArray();
                        break;
                    case "PEACHPAYMENT":
                        $cards = $cardList->toArray();
                        break;
                    case "HYPERPAY":
                        $cards = $cardList->toArray();
                        break;
                    case "MONERIS":

                        $payment_option = PaymentOption::where('slug', 'MONERIS')->first();
                        $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();

                        if (!empty($paymentoption)) {
                            $storeId = $paymentoption->api_secret_key;
                            $apiToken = $paymentoption->auth_token;
                            $newCard = new RandomPaymentController();
                            $cardToken = $value->token;
                            $cards = $newCard->MonerisViewCard($cardToken,$storeId,$apiToken);
                            if (!empty($cards) && $cards['api_response']['ResponseCode'] == '001') {
                                $cards = [array(
                                    'card_id' => $value->id,
                                    'card_number' => $cards['card_details']['masked_pan'],
                                    'card_type' => '',
                                    'exp_month' => '',
                                    'exp_year' => '',
                                    'exp_date' => $cards['card_details']['expdate']
                                )];
                            }
                        } else {
                            return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                        }
                        break;
                    case "EZYPOD":
                        $cards = $cardList->toArray();
                        break;
                    case "CONEKTA":
                        $cards = $cardList->toArray();
                        break;
                    case "FLUTTERWAVE":
                        $cards = $cardList->toArray();
                        break;
                    case "PayGate":
                        $cards = $cardList->toArray();
                        break;
                    case "PayMaya":
                        $cards = $cardList->toArray();
                        break;
                }
            }
        }

        return $cards;
    }

    public function SaveCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_option' => 'required|exists:payment_options,slug',
            'token' => 'required_if:payment_option,STRIPE,PAYSTACK,EZYPOD,CONEKTA,PAYMAYA,FATOORAH',
            'name' => 'required_if:payment_option,SENANGPAY,PAYU',
            'email' => 'required_if:payment_option,SENANGPAY',
            'phone' => 'required_if:payment_option,SENANGPAY',
            'cc_number' => 'required_if:payment_option,SENANGPAY,PAYSTACK,CIELO,PEACHPAYMENT,EZYPOD,PAYU,PAYMAYA,FATOORAH,SDGEXPRESS',
            'cc_exp' => 'required_if:payment_option,SENANGPAY,PAYMAYA',
            'exp_month' => 'required_if:payment_option,PAYSTACK,CIELO,PEACHPAYMENT,CONEKTA,PAYU,PAYMAYA,FATOORAH,SDGEXPRESS',
            'exp_year' => 'required_if:payment_option,PAYSTACK,CIELO,PEACHPAYMENT,CONEKTA,PAYU,PAYMAYA,FATOORAH,SDGEXPRESS',
            'card_type' => 'required_if:payment_option,CIELO,PEACHPAYMENT,PAYU,PAYMAYA,SDGEXPRESS',
            'cvv' => 'required_if:payment_option,CIELO,PEACHPAYMENT,PAYMAYA,SDGEXPRESS',
            'customer_id' => 'required_if:payment_option,SENANGPAY,PAYMAYA'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try{
            $user = $request->user('api');
            $string_file = $this->getStringFile(NULL,$user->Merchant);
            $status = false;
            $message = trans("$string_file.card_saved_successfully");
            $payment_option = PaymentOption::where('slug', $request->payment_option)->first();
            if(empty($payment_option)){
               return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
            }
            $paymentoption = PaymentOptionsConfiguration::where([['merchant_id', $user->merchant_id],['payment_option_id','=',$payment_option->id]])->first();
            if(empty($paymentoption)){
                return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
            }
            switch ($request->payment_option) {
                case "STRIPE":
                    $stripe = $paymentoption->api_secret_key;
                    $newCard = new StripeController($stripe);
                    $card = $newCard->CreateCustomer($request->token, $user->email);
                    if (is_array($card)) {
                        $this->StripeCard($user->id, $card['id'], $request->payment_option);
                        $status = true;
//                        $message = trans('api.message131');
                    } else {
                        $status = true;
                        $message = $card;
                    }
                    break;
                case "SENANGPAY":
                    $token_url = $paymentoption->tokenization_url;
                    if ($paymentoption->gateway_condition == 2) {
                        $token_url = "https://sandbox.senangpay.my/apiv1/get_payment_token";
                    }
                    $username = $paymentoption['auth_token'];
                    $password = '';
                    $auth_token = $username.':'.$password;
                    $auth_token = base64_encode($auth_token);

                    $req = $request->all();
                    $user_id = $user->id;
                    $card = [];
                    $card['name'] = $req['name'];
                    $card['email'] = $req['email'];
                    $card['phone'] = $req['phone'];
                    $card['cc_number'] = $req['cc_number'];
                    $card['cc_exp'] = $req['cc_exp'];
                    $card['cc_cvv'] = $req['cvv'];
                    $cardObj = new RandomPaymentController();
                    $response = $cardObj->SENANGPAYTokenGenerate($token_url,$auth_token,$paymentoption['api_secret_key'],$card);
                    if($response['result'] == 'success'){
                        $response = json_decode($response['data'], true);
                        if ($response['status'] == 1) {
                            $cc_num_pre = substr($req['cc_number'], -4);
                            $check_card = UserCard::where([['card_number', '=', $cc_num_pre], ['user_id', '=', $user_id]])->first();
                            if (empty($check_card)) {
                                $user_card = UserCard::create([
                                    'user_id' => $user_id,
                                    'token' => $response['token'],
                                    'card_number' => $cc_num_pre,
                                    'expiry_date' => $req['cc_exp'],
                                    'payment_option_id' => 'SENANGPAY'
                                ]);
                            }
                            $status = true;
                        } else {
                            $status = false;
                            $message = $response['msg'];
                        }
                    }else{
                        $status = false;
                        $message = $response['data'];
                    }
                    break;
                case "PAYSTACK":
//                    $cc_num_pre = substr($request->cc_number, -4);
                    $cc_num_pre = str_replace(' ','',$request->cc_number);
                    $newCard = new RandomPaymentController();
                    $charge = $newCard->VerifyTransactionPaystack($request->token, $paymentoption->api_secret_key);
                    if (!empty($paymentoption) && !empty($charge['id']['authorization_code'])) {
                        $user_card = UserCard::create([
                            'user_id' => $user->id,
                            'token' => $charge['id']['authorization_code'],
                            'payment_option_id' => $paymentoption->payment_option_id,
                            'card_number' => $cc_num_pre,
                            'card_type' => $charge['id']['brand'],
                            'exp_month' => $request->exp_month,
                            'exp_year' => $request->exp_year
                        ]);
                        $status = true;
                    } else {
                        $status = false;
                        $message = trans("$string_file.payment_configuration_not_found");
                    }
                    break;
                case "CIELO":
                    $cieloe = $paymentoption->api_secret_key;
                    $newCard = new RandomPaymentController($cieloe);
                    $userName = $user->first_name . " " . $user->last_name;
                    $card = $newCard->tokenGenerateCielo($request->cc_number, $request->exp_month, $request->exp_year, $request->card_type, $request->cvv, $user->email, $userName, $paymentoption->api_secret_key, $paymentoption->api_public_key, $paymentoption->tokenization_url);
                    if (array_key_exists("CardToken", $card)) {
                        $cc_num_pre = substr($request->cc_number, -4);
                        $this->SaveCardCielo($user->id, $cc_num_pre, $request->exp_month.'/'.$request->exp_year, $card['CardToken'], $request->card_type, $paymentoption->payment_option_id);
                        $status = true;
                    } else {
                        $status = false;
                        $message = $card[0];
                    }
                    break;
                case "BANCARD":
                    $token = md5("." . $paymentoption->api_secret_key . $user->id . "request_user_cards");
                    $newcard = new RandomPaymentController();
                    $card = $newcard->SaveCardBancard($paymentoption->api_public_key, $user->id, $token);
                    if (is_array($card)) {
                        $status = true;
                    } else {
                        $status = false;
                        $message = $card;
                    }
                    break;
                case "DPO":
//                    $card = new RandomPaymentController();
//                    $card = $card->getSubTokenDPO($paymentoption->auth_token, $user->email);
//                    $status = "0";
//                    if (is_array($card)) {
//                        $this->SaveCardDPO($user->id, $card, $request->payment_option);
//                        $status = true;
//                    } else {
//                        $status = false;
//                        $message = $card;
//                    }
                    break;
                case "PEACHPAYMENT":
                    $userName = $user->first_name . " " . $user->last_name;
                    $card = new RandomPaymentController();
                    $card = $card->tokenizePeach($request->cc_number, $request->exp_month, $request->exp_year, $request->card_type, $request->cvv, $userName, $paymentoption->api_secret_key, $paymentoption->auth_token, $paymentoption->tokenization_url);
                    $status = "0";
                    if (is_array($card)) {
                        $this->savecardPeach($card, "user", $user->id, $paymentoption->payment_option_id, $request->cc_number, $request->card_type, $request->exp_month, $request->exp_year);
                        $status = true;
                    } else {
                        $status = false;
                        $message = $card;
                    }
                    break;
                case "HYPERPAY":
                    $userName = $user->first_name . " " . $user->last_name;
                    $card = new RandomPaymentController();
                    $card = $card->tokenizeHyper($request->cc_number, $request->exp_month, $request->exp_year, $request->card_type, $request->cvv, $userName, $paymentoption->api_secret_key, $paymentoption->auth_token, $paymentoption->tokenization_url);
                    $status = "0";
                    if (is_array($card)) {
                        $this->savecardHyper($card, "user", $user->id, $paymentoption->payment_option_id, $request->cc_number);
                        $status = true;
                    } else {
                        $status = false;
                        $message = $card;
                    }
                    break;
                case "MONERIS":
                    $card = new RandomPaymentController();
                    $cardDetails = $card->MonerisVaultAddCard($user->id,$user->UserPhone,$user->email,$request->cc_number,$request->expire_date,$paymentoption->api_secret_key,$paymentoption->auth_token);
                    if ($cardDetails['ResponseCode'] == '001' && !empty($cardDetails['DataKey'])){
                        UserCard::create([
                            'user_id' => $user->id,
                            'token' => $cardDetails['DataKey'],
                            'payment_option_id' => $paymentoption->payment_option_id
                        ]);
                        $status = true;
                    }else{
                        $status = false;
                        $message = $cardDetails['Message'];
                    }
                    break;
                case "EZYPOD":
                    UserCard::create([
                        'user_id' => $user->id,
                        'token' => $request->token,
                        'payment_option_id' => $paymentoption->payment_option_id,
                        'card_number' => $request->cc_number
                    ]);
                    $status = true;
                    break;
                case "CONEKTA":
                    $private_key = $paymentoption->api_secret_key;
                    $random_payment = new RandomPaymentController();
                    $customer_data = $random_payment->createConektaCustomerToken($request->token,$private_key,$user);
                    $customer_data =  json_decode($customer_data);
                    if(!empty($customer_data->id))
                    {
                        UserCard::create([
                            'user_id' => $user->id,
                            'token' => $request->token,
                            'payment_option_id' => $paymentoption->payment_option_id,
                            'card_number' => $request->card_number,
//                            'card_number' => $request->cc_number, // need to send full card number in param of cc_number
                            'exp_month' => $request->exp_month,
                            'exp_year' => $request->exp_year,
                            'card_type' => $customer_data->payment_sources->data[0]->brand,
                            'user_token' => $customer_data->id,

                        ]);
                        $status = true;
                    }
                    else
                    {
                        $status = false;
                        $message = isset($customer_data->details[0]) ? $customer_data->details[0]->debug_message : $customer_data->type;
                    }
                    break;
                case "PAYU":
                    $merchant_id = $request->merchant_id;
                    $payment_option = PaymentOption::where('slug', 'PAYU')->first();
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $request->api_login = $paymentoption->api_secret_key; // sandbox ZUy1qlh29pM1HSz
                        $request->api_key = $paymentoption->api_public_key; // sandbox Xx6UMYQ499q4Pz9GC6fMeG5o3J
                        $save_card_response =  $this->payuSaveCard($request);
                        $user = $request->user('api');
                        if(!empty($save_card_response) && $save_card_response['code'] == 'SUCCESS')
                        {
                            UserCard::create([
                                'user_id' => $user->id,
                                'card_holder' => $save_card_response['creditCardToken']['name'],
                                'token' => $save_card_response['creditCardToken']['creditCardTokenId'],
                                'payment_option_id' => $paymentoption->payment_option_id,
                                'card_number' => $request->cc_number,
                                'exp_month' => $request->exp_month,
                                'exp_year' => $request->exp_year,
                                'card_type' => $save_card_response['creditCardToken']['paymentMethod'],
                            ]);
                            $status = true;
                            $message = trans("$string_file.card_saved_successfully");
                        }
                        else
                        {
                            if(isset($save_card_response['error']))
                            {
                                throw new \Exception($save_card_response['error']);
                            }
                        }
                    } else {
                        throw new \Exception(trans("$string_file.payment_configuration_not_found"));
                    }
                    break;
                case "TELR":
                    UserCard::create([
                        'user_id' => $user->id,
                        'token' => $request->token,
                        'payment_option_id' => $paymentoption->payment_option_id,
                        'card_number' => $request->card_number,
                        'exp_month' => $request->exp_month,
                        'exp_year' => $request->exp_year,
                    ]);
                    $status = true;
                    break;
                case "PayGate":
                    $status = false;
                    $message = trans("$string_file.some_thing_went_wrong");
                    $paygate = new PaygateController;
                    $data = [
                        'card_number'=>$request->card_number,
                        'expiry_date'=>$request->exp_month.$request->exp_year,
                        'id'=>$paymentoption->api_public_key,
                        'password'=>$paymentoption->api_secret_key,
                        'condition'=>$paymentoption->gateway_condition,
                    ];
                    $vault_response = $paygate->vaultRequest($data);
                    if($vault_response)
                    {

                        $card_number = $this->getCardNumber($request->card_number);
                        $card =  UserCard::where([['card_number','=',$card_number],['user_id','=',$user->id],['payment_option_id','=',$paymentoption->payment_option_id]])->first();
                        if(empty($card))
                        {
                            $card = new UserCard;
                        }
                        $card->user_id = $user->id;
                        $card->token = $vault_response['VaultId'];
                        $card->payment_option_id = $paymentoption->payment_option_id;
                        $card->card_number = $card_number;
                        $card->exp_month = $request->exp_month;
                        $card->exp_year = $request->exp_year;
                        $card->status = 1;
                        $card->expiry_date = $request->exp_month.$request->exp_year;
                        $card->save();
                        $status = true;
                        $message = trans("$string_file.card_saved_successfully");
                    }
                    break;
                case "PAYMAYA":
                    UserCard::create([
                        'user_id' => $user->id,
                        'token' => $request->token,
                        'payment_option_id' => $paymentoption->payment_option_id,
                        'card_number' => $request->cc_number,
                        'exp_month' => $request->exp_month,
                        'exp_year' => $request->exp_year,
                        'card_type' => $request->card_type,
                        'user_token' => $request->customer_id,
                    ]);
                    $status = true;
                    break;
                case "FATOORAH":
                    UserCard::create([
                        'user_id' => $user->id,
                        'token' => $request->token,
                        'payment_option_id' => $paymentoption->payment_option_id,
                        'card_number' => $request->cc_number,
                        'exp_month' => $request->exp_month,
                        'exp_year' => $request->exp_year,
                        'card_type' => $request->card_type,
                    ]);
                    $status = true;
                    break;
                case "SDGEXPRESS":
                    $merchant_id = $request->merchant_id;
                    $payment_option = PaymentOption::where('slug', 'SDGEXPRESS')->first();
                    $paymentOption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                    if (!empty($paymentOption)) {
                        $card = new SDGExpressController();
                        $save_card_response = $card->addCard($paymentOption, $request);
                        if($save_card_response['result'] == true)
                        {
                            UserCard::create([
                                'user_id' => $user->id,
                                'payment_option_id' => $paymentOption->payment_option_id,
                                'card_number' => $request->cc_number,
                                'exp_month' => $request->exp_month,
                                'exp_year' => $request->exp_year,
                                'token' => base64_encode($request->cvv)
                            ]);
                            $status = true;
                        } else {
                            $message = $save_card_response['message'];
                        }
                    } else {
                        throw new \Exception(trans("$string_file.payment_configuration_not_found"));
                    }
                    break;
            }

            DB::commit();
            if($status){
                return $this->successResponse($message,[]);
            }else{
                return $this->failedResponse($message);
            }
        }catch (\Exception $e){
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
    }

    public function saveDriverCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_option' => 'required|exists:payment_options,slug',
            'token' => 'required_if:payment_option,STRIPE,PAYMAYA,FATOORAH',
            'cc_number' => 'required_if:payment_option,PAYMAYA,FATOORAH,SDGEXPRESS',
            'cc_exp' => 'required_if:payment_option,PAYMAYA',
            'exp_month' => 'required_if:payment_option,PAYMAYA,FATOORAH,SDGEXPRESS',
            'exp_year' => 'required_if:payment_option,PAYMAYA,FATOORAH,SDGEXPRESS',
            'card_type' => 'required_if:payment_option,PAYMAYA,FATOORAH,SDGEXPRESS',
            'cvv' => 'required_if:payment_option,PAYMAYA,SDGEXPRESS',
            'customer_id' => 'required_if:payment_option,PAYMAYA'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $driver = $request->user('api-driver');
        $string_file = $this->getStringFile(NULL,$driver->Merchant);
        $status = false;
        $message = trans("$string_file.card_saved_successfully");
        $payment_option = PaymentOption::where('slug', $request->payment_option)->first();
        if(empty($payment_option)){
           return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
        }
        $paymentoption = PaymentOptionsConfiguration::where([['merchant_id', $driver->merchant_id],['payment_option_id','=',$payment_option->id]])->first();
        if(empty($paymentoption)){
            return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
        }
        switch ($request->payment_option) {
            case "STRIPE":
                if (!empty($paymentoption)) {
                    $stripe = $paymentoption->api_secret_key;
                    $newCard = new StripeController($stripe);
                    $card = $newCard->CreateCustomer($request->token, $driver->email);
                    if (is_array($card)) {
                        $payment_option = PaymentOption::where([['slug', 'STRIPE']])->first();
                        $this->StripeCard($driver->id, $card['id'], $payment_option->slug, 1);
                        $status = true;
                        $message = trans("$string_file.card_saved_successfully");
                    } else {
                        $message = $card;
                    }
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
                }
                break;
            case "SENANGPAY":
                $token_url = $paymentoption->tokenization_url;
                if ($paymentoption->gateway_condition == 2) {
                    $token_url = "https://sandbox.senangpay.my/apiv1/get_payment_token";
                }
                $req = $request->all();
                $name = $req['name'];
                $driver_id = $driver->id;
                $email = $req['email'];
                $phone = $req['phone'];
                $cc_number = $req['cc_number'];
                $cc_exp = $req['cc_exp'];
                $cc_cvv = $req['cc_cvv'];
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $token_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"name\"\r\n\r\n$name\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"email\"\r\n\r\n$email\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"phone\"\r\n\r\n$phone\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"cc_number\"\r\n\r\n$cc_number\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"cc_exp\"\r\n\r\n$cc_exp\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"cc_cvv\"\r\n\r\n$cc_cvv\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
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
                    $status = false;
                    $message = "cURL Error #:" . $err;
                } else {
                    $response = json_decode($response, true);
                    if ($response['status'] == 1) {
                        $cc_num_pre = substr($cc_number, -4);
                        $cc_num_pre = $cc_num_pre;
                        $check_card = DriverCard::where([['card_number', '=', $cc_num_pre], ['driver_id', '=', $driver_id]])->first();
                        $payment_option_detail = PaymentOption::where('slug', 'SENANGPAY')->first();
                        if (empty($check_card)) {
                            DriverCard::create([
                                'driver_id' => $driver_id,
                                'token' => $response['token'],
                                'card_number' => $cc_num_pre,
                                'expiry_date' => $cc_exp,
                                'payment_option_id' => $payment_option_detail->id
                            ]);
                        }
                        return response()->json(['result' => $response['status'], 'token' => $response['token'], 'message' => $response['msg']]);
                    } else {
                        return response()->json(['result' => $response['status'], 'message' => $response['msg']]);
                    }
                }
                break;
            case "PAYSTACK":
//                $cc_num_pre = substr($request->cc_number, -4);
                $cc_num_pre = str_replace(' ','',$request->cc_number);
                $newCard = new RandomPaymentController();
                $charge = $newCard->VerifyTransactionPaystack($request->token, $paymentoption->api_secret_key);
                if (!empty($paymentoption) && !empty($charge['id']['authorization_code'])) {
                    $user_card = DriverCard::create([
                        'driver_id' => $driver->id,
                        'token' => $charge['id']['authorization_code'],
                        'payment_option_id' => $paymentoption->payment_option_id,
                        'card_number' => $cc_num_pre,
                        'card_type' => $charge['id']['brand'],
                        'exp_month' => $request->exp_month,
                        'exp_year'=>$request->exp_year
                    ]);
                    return response()->json(['result' => '1', 'message' => $message]);
                } else {
                    return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found")]);
                }
                break;
            case "MONERIS":
                $card = new RandomPaymentController();
                $cardDetails = $card->MonerisVaultAddCard($driver->id,$driver->phoneNumber,$driver->email,$request->cc_number,$request->expire_date,$paymentoption->api_secret_key,$paymentoption->auth_token);
                if ($cardDetails['ResponseCode'] == 001 && !empty($cardDetails['DataKey'])){
                    DriverCard::create([
                        'driver_id' => $driver->id,
                        'token' => $cardDetails['DataKey']
                    ]);
                    $status = true;
                }else{
                    $message = $cardDetails['Message'];
                }
                break;
            case "PayGate":
                $message = trans("$string_file.some_thing_went_wrong");
                $paygate = new PaygateController;
                $data = [
                    'card_number'=>$request->card_number,
                    'expiry_date'=>$request->exp_month.$request->exp_year,
                    'id'=>$paymentoption->api_public_key,
                    'password'=>$paymentoption->api_secret_key,
                    'condition'=>$paymentoption->gateway_condition,
                ];
                $vault_response = $paygate->vaultRequest($data);
                if($vault_response && $vault_response['StatusName'] == "Completed")
                {
                    $card_number = $this->getCardNumber($request->card_number);
                    $card =  DriverCard::where([['card_number','=',$card_number],['driver_id','=',$driver->id],['payment_option_id','=',$paymentoption->payment_option_id]])->first();
                    if(empty($card))
                    {
                       $card = new DriverCard;
                    }
                    $card->driver_id = $driver->id;
                    $card->token = $vault_response['VaultId'];
                    $card->payment_option_id = $paymentoption->payment_option_id;
                    $card->card_number = $card_number;
                    $card->exp_month = $request->exp_month;
                    $card->exp_year = $request->exp_year;
                    $card->expiry_date = $request->exp_month.$request->exp_year;
                    $card->save();
                    $status = true;
                    $message = trans("$string_file.card_saved_successfully");
                }
                else
                {
                    if(isset($vault_response['ResultDescription']))
                    {
                        $message = $vault_response['ResultDescription'];
                    }
                }
                break;
            case "PAYMAYA":
                DriverCard::create([
                    'driver_id' => $driver->id,
                    'token' => $request->token,
                    'payment_option_id' => $paymentoption->payment_option_id,
                    'card_number' => $request->cc_number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'card_type' => $request->card_type,
                    'driver_token' => $request->customer_id,
                ]);
                $status = true;
                break;
            case "FATOORAH":
                DriverCard::create([
                    'driver_id' => $driver->id,
                    'token' => $request->token,
                    'payment_option_id' => $paymentoption->payment_option_id,
                    'card_number' => $request->cc_number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'card_type' => $request->card_type,
                ]);
                $status = true;
                break;
            case "SDGEXPRESS":
                $merchant_id = $request->merchant_id;
                $payment_option = PaymentOption::where('slug', 'SDGEXPRESS')->first();
                $paymentOption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
                if (!empty($paymentOption)) {
                    $card = new SDGExpressController();
                    $save_card_response = $card->addCard($paymentOption, $request);
                    if($save_card_response['result'] == true)
                    {
                        DriverCard::create([
                            'driver_id' => $driver->id,
                            'payment_option_id' => $paymentOption->payment_option_id,
                            'card_number' => $request->cc_number,
                            'exp_month' => $request->exp_month,
                            'exp_year' => $request->exp_year,
                            'token' => base64_encode($request->cvv)
                        ]);
                        $status = true;
                    } else {
                        $message = $save_card_response['message'];
                    }
                } else {
                    throw new \Exception(trans("$string_file.payment_configuration_not_found"));
                }
                break;
        }
        if($status){
            return $this->successResponse($message,[]);
        } else {
            return $this->failedResponse($message);
        }
    }

    public function checkUserCardBalance($cardId, $amount)
    {
        $amount = $amount * 100;
        $card = UserCard::find($cardId);
        $user = User::find($card->user_id);
        switch ($card->PaymentOption->slug) {
            case "PAYSTACK":
                if($user->Country->country_code == "NG"){
                    $paymentOption = PaymentOptionsConfiguration::where([['merchant_id','=', $user->merchant_id],['payment_option_id','=', $card->payment_option_id]])->first();
                    $data = array('authorization_code' => $card->token, 'email' => $user->email ?? $user->Merchant->email, 'currency' => $user->Country->isoCode, 'amount' => $amount);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://api.paystack.co/transaction/check_authorization');
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  //Post Fields
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $headers = [
                        'Authorization: Bearer ' . $paymentOption->api_secret_key,
                        'Content-Type: application/json',
                    ];
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    return json_decode($response, true); // response is already in this format - array('status' => false, 'message' => ''))
                }else{
                    return array('status' => true, 'message' => '');
                }
            default:
                return array('status' => true, 'message' => '');
        }
    }

    public function StripeCard($user_id, $id, $payment_option_id, $type = null)
    {
        $payment_option = PaymentOption::where('slug', $payment_option_id)->first();
        if ($type == 1) {
            DriverCard::create([
                'driver_id' => $user_id,
                'token' => $id,
                'payment_option_id' => $payment_option->id
            ]);
        } else {
            UserCard::create([
                'user_id' => $user_id,
                'token' => $id,
                'payment_option_id' => $payment_option->id
            ]);
        }

    }

    public function SaveCardCielo($user_id, $cc_num_pre, $expDate, $token, $cardType, $paymentOption)
    {
        UserCard::create([
            'user_id' => $user_id,
            'token' => $token,
            'payment_option_id' => $paymentOption,
            'card_number' => $cc_num_pre,
            'expiry_date' => $expDate,
            'card_type' => $cardType
        ]);

    }

//    public function SaveCardDPO($userId, $cards, $paymentOption)
//    {
//        foreach ($cards as $card) {
//            UserCard::create([
//                'user_id' => $userId,
//                'token' => $card['data']['subscriptionToken'],
//                'payment_option_id' => $paymentOption,
//                'card_number' => $card['data']['paymentLast4'],
//                'card_type' => $card['data']['paymentType']
//            ]);
//        }
//    }

    public function savecardPeach($responseData, $for, $userId, $payment_option_id, $cc_number, $card_type = NULL, $card_month = NULL, $card_year = NULL)
    {
        if ($for == "user") {
            $card = UserCard::updateOrCreate([
                'user_id' => $userId,
                'card_number' => $responseData['card']['last4Digits']],
                ['token' => $responseData['id'],
                    'payment_option_id' => $payment_option_id,
                    'card_type' => $card_type,
                    'exp_month' => $card_month,
                    'exp_year' => $card_year,
                ]);
        } else {
            DriverCard::updateOrCreate([
                'driver_id' => $userId,
                'card_number' => $responseData['card']['last4Digits']],
                ['token' => $responseData['id'],
                    'payment_option_id' => $payment_option_id
                ]);
        }
    }

    public function savecardHyper($responseData, $for, $userId, $payment_option_id, $cc_number, $card_type = NULL, $card_month = NULL, $card_year = NULL)
    {
        if ($for == "user") {
            $card = UserCard::updateOrCreate([
                'user_id' => $userId,
                'card_number' => $responseData['card']['last4Digits']],
                ['token' => $responseData['id'],
                    'payment_option_id' => $payment_option_id,
                    'card_type' => $card_type,
                    'exp_month' => $card_month,
                    'exp_year' => $card_year,
                ]);
        } else {
            DriverCard::updateOrCreate([
                'driver_id' => $userId,
                'card_number' => $responseData['card']['last4Digits']],
                ['token' => $responseData['id'],
                    'payment_option_id' => $payment_option_id
                ]);
        }
    }

    public function IugoPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'for' => 'required',
            'card_number' => 'required',
            'cvv' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required',
            'amount' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $user = ($request->for == "user") ? $request->user('api') : $request->user('api-driver');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $paymentoption = PaymentOptionsConfiguration::where([['merchant_id', $user->merchant_id]])->first();
        if (!empty($paymentoption)) {
            $charge = new RandomPaymentController();
            $userDetails = array('firstName' => $user->first_name, 'lastName' => $user->last_name, 'email' => $user->email);
            $cardDetails = array('card_number' => $request->card_number, 'cvv' => $request->cvv, 'exp_month' => $request->exp_month, 'exp_year' => $request->exp_year);
            $paymentoption = array('api_secret_key' => $paymentoption->api_secret_key, 'auth_token' => $paymentoption->auth_token, 'gateway_condition' => $paymentoption->gateway_condition);
            $charge = $charge->IugoToken($request->amount, $userDetails, $cardDetails, $paymentoption);
            if (is_array($charge)) {
                $result = "1";
                $message = trans("$string_file.success");
            } else {
                $result = "0";
                $message = trans("$string_file.payment_pending");
            }
        } else {
            $result = "0";
            $message = trans("$string_file.payment_configuration_not_found");
        }
        return response()->json(['result' => $result, 'message' => $message]);
    }

    public function PaytmChecksum(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'txnAmount' => 'required',
            'for' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        // Default values
        $mobile_phone = "7777777777";
        $email = "username@emailprovider.com";
        $website = "WEBSTAGING";
        $call_back_url = "https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=";
        if ($request->for == "user") {
            $user = $request->user('api');
            $string_file = $this->getStringFile(NULL,$user->Merchant);
            $payconfig = PaymentOptionsConfiguration::where([['merchant_id', $user->merchant_id]])->first();
            $custid = "cust" . $user->id;
            $mobile_phone = $user->UserPhone;
            $email = $user->email;
            $website = "DEFAULT";
            $call_back_url = $payconfig->callback_url;
        } else {
            $driver = $request->user('api-driver');
            $string_file = $this->getStringFile(NULL,$user->Merchant);
            $payconfig = PaymentOptionsConfiguration::where([['merchant_id', $driver->merchant_id]])->first();
            $custid = "cust" . $driver->id;
            $mobile_phone = $driver->phoneNumber;
            $email = $driver->email;
            $website = "DEFAULT";
            $call_back_url = $payconfig->callback_url;
        }
        $dt = date('mdhms');
        $order_id = "order" . $dt;
        $call_back_url = $call_back_url.''.$order_id;
        if (!empty($payconfig)) {
            define("merchantMid", $payconfig->api_secret_key);
            define("merchantKey", $payconfig->api_public_key);

            // Key in your staging and production MID available in your dashboard
            define("orderId", $order_id);
            define("custId", $custid);
            define("industryTypeId", "Retail");
            define("channelId", "WAP");
            define("txnAmount", $request->txnAmount);
            define("website", $website);
            define("mobileNo", $mobile_phone);
            define("email", $email);

            define("callbackUrl", $call_back_url);
            $paytmParams = array();
            $paytmParams["MID"] = merchantMid;
            $paytmParams["ORDER_ID"] = orderId;
            $paytmParams["CUST_ID"] = custId;
            $paytmParams["MOBILE_NO"] = mobileNo;
            $paytmParams["EMAIL"] = email;
            $paytmParams["CHANNEL_ID"] = channelId;
            $paytmParams["TXN_AMOUNT"] = txnAmount;
            $paytmParams["WEBSITE"] = website;
            $paytmParams["INDUSTRY_TYPE_ID"] = industryTypeId;
            $paytmParams["CALLBACK_URL"] = callbackUrl;
            $paytmend = new encdec_paytm();
            $paytmChecksum = $paytmend->getChecksumFromArray($paytmParams, merchantKey);
//            $paytmParams = $paytmParams;
//            $merchantKey = "@m_5wAFo@cX6p5ee";
//            $paytmChecksum = $paytmChecksum;
            $isValidChecksum = $paytmend->verifychecksum_e($paytmParams, merchantKey, $paytmChecksum);
            if ($isValidChecksum == "TRUE") {
                return response()->json([
                    'result' => "1",
                    'id' => $custid,
                    'paytmChecksum' => $paytmChecksum,
                    'order_id' => $order_id,
                    'merchantId' => $payconfig->api_secret_key,
                    'merchantKey' => $payconfig->api_public_key,
                    'gatewayCondition' => $payconfig->gateway_condition,
                    'callbackURL' => $payconfig->callback_url . '' . $order_id,
                    'industryTypeId' => 'Retail',
                    'website' => website,
                    'msg' => 'Checksum Matched',
                    'mobileNo' => mobileNo,
                    'email' => email
                ]);
            } else {
                return response()->json(['result' => "0", 'msg' => "Required fields missing!!", 'msg' => 'Checksum MisMatch']);
            }
        } else {
            return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
        }
    }

    public function prepareCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'currency' => 'required',
            'for' => 'required',
            'type' => 'required',
            'checkout_id' => 'required_if:type,2'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        if ($request->type == 1) {
            if ($request->for == "user") {
                $user = $request->user('api');
                $status = 1;
            } else {
                $user = $request->user('api-driver');
                $status = 2;
            }
        } else {
            $user = Booking::find($request->checkout_id);
            $status = 3;
        }
        $payConfig = PaymentOptionsConfiguration::where([['merchant_id', $user->merchant_id]])->first();
        $data = "entityId=" . $payConfig->api_secret_key .
            "&amount=" . $request->amount .
            "&currency=" . $request->currency .
            "&paymentType=DB" .
            "&notificationUrl=" . route('notify');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $payConfig->tokenization_url."v1/checkouts");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . $payConfig->auth_token));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $responseData = json_decode($responseData, true);

        $id = $responseData['id'];
        return response()->json($responseData);
    }

    public function shopper(Request $request)
    {
        $trans = Transaction::where([['checkout_id', $request->id]])->first();
        // $paymentStatus = $this->paymentStatus($trans->checkout_id, $trans->merchant_id, "user");
        // if(is_array($paymentStatus)){
        //     return response()->json($paymentStatus);
        // }else{
        //     return false;
        // }
    }

    public function paymentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TxnId' => 'required',
            'for' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }

        if ($request->for == "user") {
            $user = $request->user('api');
        } else {
            $user = $request->user('api-driver');
        }

        $payConfig = PaymentOptionsConfiguration::where([['merchant_id', $user->merchant_id]])->first();
        $url = $payConfig->tokenization_url."v1/checkouts/" . $request->TxnId . "/payment";
        $url .= "?entityId=$payConfig->api_secret_key";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . $payConfig->auth_token));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $responseData = json_decode($responseData, true);
        return $responseData;
    }

    public function BrainTree(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'for' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $user = ($request->for == "user") ? $request->user('api') : $request->user('api-driver');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $payconfig = PaymentOptionsConfiguration::where([['merchant_id', $user->merchant_id]])->first();
        if (!empty($payconfig)) {
            $brainTree = new RandomPaymentController();
            $clientToken = $brainTree->brainTreeClientToken($payconfig->api_secret_key, $payconfig->api_public_key, $payconfig->auth_token, $payconfig->gateway_condition);
            if (is_array($clientToken)) {
                $status = "1";
                $message = trans('api.clienttokencreated');
            } else {
                $status = "0";
                $message = $clientToken;
            }
        } else {
            return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
        }
        return response()->json(['result' => $status, 'message' => $message, 'data' => $clientToken]);
    }

    public function BraintreeTrans(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'paymentMethodNonce' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $transaction = new RandomPaymentController();
        $trans = $transaction->brainTreeCreateTrans($request->amount, $request->paymentMethodNonce);
        if (is_array($trans)) {
            $status = "1";
            $message = trans('api.transcreated');
        } else {
            $status = "0";
            $message = $trans;
        }
        return response()->json(['result' => $status, 'message' => $message, 'data' => $trans]);
    }

    // creating payment of mercado gateway
    public function prefIdMercado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'for' => 'required',
            'currency' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $user = ($request->for == "user") ? $request->user('api') : $request->user('api-driver');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $payConfig = PaymentOptionsConfiguration::where([['merchant_id', $user->merchant_id]])->first();
        if (!empty($payConfig)) {
            $prefId = new RandomPaymentController();
            $prefId = $prefId->createPrefIdMercado($payConfig->auth_token, $request->amount, $user->email, $request->currency);

            if (array_key_exists('id', $prefId)) {
                $result = "1";
                $message = trans('api.message195');
            } else {
                $result = "0";
                $message = trans('api.message196');
            }
        } else {
            return response()->json(['result' => 0, 'message' => trans("$string_file.payment_configuration_not_found"), 'data' => []]);
        }
        return response()->json(['result' => $result, 'message' => $message, 'data' => $prefId]);
    }

    public function getPaymentConfigurations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_option' => 'required',
            'for' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $user = ($request->for == "user") ? $request->user('api') : $request->user('api-driver');
        $payConfig = PaymentOptionsConfiguration::where([['payment_gateway_provider', $request->payment_option], ['merchant_id', $user->merchant_id]])->first();
        $message = trans('api.paymentconfig');
        return response()->json(['result' => "1", 'message' => $message, 'data' => $payConfig]);
    }

    public function prepareHyperCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'currency' => 'required',
            'for' => 'required',
            'type' => 'required',
            'checkout_id' => 'required_if:type,2'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        if ($request->type == 1) {
            if ($request->for == "user") {
                $user = $request->user('api');
                $status = 1;
            } else {
                $user = $request->user('api-driver');
                $status = 2;
            }
        } else {
            $user = Booking::find($request->checkout_id);
            $status = 3;
        }
        $payConfig = PaymentOptionsConfiguration::where([['merchant_id', $user->merchant_id]])->first();
        $data = "entityId=" . $payConfig->api_secret_key .
            "&amount=" . $request->amount .
            "&currency=" . $request->currency .
            "&paymentType=DB" .
            "&notificationUrl=" . route('notify');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $payConfig->tokenization_url."v1/checkouts");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . $payConfig->auth_token));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $responseData = json_decode($responseData, true);

        $id = $responseData['id'];
        return response()->json($responseData);
    }

    public function SenangPayToken(Request $request){
        $validator = Validator::make($request->all(), [
            'payment_option' => 'required|exists:payment_options,slug',
            'name' => 'required_if:payment_option,SENANGPAY',
            'email' => 'required_if:payment_option,SENANGPAY',
            'phone' => 'required_if:payment_option,SENANGPAY',
            'cc_number' => 'required_if:payment_option,SENANGPAY',
            'cc_exp' => 'required_if:payment_option,SENANGPAY',
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        if(isset($request->type) && $request->type == 2){
            $user = $request->user('api-driver');
        }else{
            $user = $request->user('api');
        }
        $payment_option = PaymentOption::where('slug', 'SENANGPAY')->first();
        $paymentoption = PaymentOptionsConfiguration::where([['merchant_id', '=', $user->merchant_id],['payment_option_id','=',$payment_option->id]])->first();
        $token_url = $paymentoption->tokenization_url;
        if ($paymentoption->gateway_condition == 2) {
            $token_url = "https://sandbox.senangpay.my/apiv1/get_payment_token";
        }
        $username = $paymentoption['auth_token'];
        $password = '';
        $auth_token = $username.':'.$password;
        $auth_token = base64_encode($auth_token);

        $req = $request->all();
        $user_id = $user->id;
        $card = [];
        $card['name'] = $req['name'];
        $card['email'] = $req['email'];
        $card['phone'] = $req['phone'];
        $card['cc_number'] = $req['cc_number'];
        $card['cc_exp'] = $req['cc_exp'];
        $card['cc_cvv'] = $req['cvv'];
        $cardObj = new RandomPaymentController();
        $response = $cardObj->SENANGPAYTokenGenerate($token_url,$auth_token,$paymentoption['api_secret_key'],$card);
        if($response['result'] == 'success'){
            $response = json_decode($response['data'], true);
            if ($response['status'] == 1) {
                $cc_num_pre = substr($req['cc_number'], -4);
                if(isset($request->type) && $request->type == 2){
                    $check_card = DriverCard::where([['card_number', '=', $cc_num_pre], ['driver_id', '=', $user_id]])->first();
                    if (empty($check_card)) {
                        $user_card = DriverCard::create([
                            'driver_id' => $user_id,
                            'token' => $response['token'],
                            'card_number' => $cc_num_pre,
                            'expiry_date' => $req['cc_exp'],
                            'payment_option_id' => $payment_option->id
                        ]);
                    }
                }else{
                    $check_card = UserCard::where([['card_number', '=', $cc_num_pre], ['user_id', '=', $user_id]])->first();
                    if (empty($check_card)) {
                        $user_card = UserCard::create([
                            'user_id' => $user_id,
                            'token' => $response['token'],
                            'card_number' => $cc_num_pre,
                            'expiry_date' => $req['cc_exp'],
                            'payment_option_id' => $payment_option->id
                        ]);
                    }
                }
                return response()->json(['result' => $response['status'], 'token' => $response['token'], 'message' => $response['msg']]);
            } else {
                return response()->json(['result' => $response['status'], 'message' => $response['msg']]);
            }
        }else{
            return response()->json(['result' => 0, 'message' => $response['data']]);
        }
    }

    // payu save card
    public function payuSaveCard($request)
    {
        // p($request->all());
        $locale = !empty($request->header('locale')) ? $request->header('locale') : 'en';
        $apiLogin = !empty($request->api_login) ? $request->api_login : '';
        $apiKey = !empty($request->api_key) ? $request->api_key : '';
        $full_name = !empty($request->name) ? $request->name : '';
        $card_number = !empty($request->cc_number) ? $request->cc_number : '';
        $expiration_date = $request->exp_year.'/'.$request->exp_month;
        // $expiration_date = date('m/Y',strtotime($expiration_date));
        // p($expiration_date);
        $payment_method = $request->card_type;

        $arr_param = [
            "language"=> $locale,
            "command"=> "CREATE_TOKEN",
            "merchant"=> [
                "apiLogin"=> $apiLogin,
                "apiKey"=> $apiKey
            ],
            "creditCardToken"=> [
                "payerId"=> "10",
                "name"=> $full_name,
                "identificationNumber"=> "32144457",
//                "identificationNumber"=> "895379", // live
                "paymentMethod"=> $payment_method,
                "number"=> $card_number,
                "expirationDate"=> "$expiration_date"
            ]
        ];
        // p($arr_param);
        $arr_param = json_encode($arr_param,JSON_UNESCAPED_SLASHES);
        // p($arr_param);
        $curl = curl_init();
        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi', // sandbox
            CURLOPT_URL => 'https://api.payulatam.com/payments-api/4.0/service.cgi', // live
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$arr_param,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        $xml   = simplexml_load_string($response);
        $response = json_decode(json_encode($xml),true);
        curl_close($curl);
        // p($response);

        return $response;


//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS =>'{
//               "language": "en",
//               "command": "CREATE_TOKEN",
//               "merchant": {
//                  "apiLogin": "pRRXKOl8ikMmt9u",
//                  "apiKey": "4Vj8eK4rloUd272L48hsrarnUA"
//               },
//           "creditCardToken": {
//              "payerId": "10",
//              "name": "full name",
//              "identificationNumber": "32144457",
//              "paymentMethod": "VISA",
//              "number": "4111111111111111",
//              "expirationDate": "2022/01"
//           }
//        }',
//            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json'
//            ),
//        ));
//        $response = curl_exec($curl);
//        curl_close($curl);
//        return $response;
    }

    public function flutterwavePaymentRequest(Request $request){
        $user = $request->user('api');
        $merchant_id=$user->merchant_id;
        $validator = Validator::make($request->all(), [
            'card_number' => 'required',
            'cvv' => 'required',
            'expiry_month' => 'required',
            'expiry_year' => 'required',
            'currency' => 'required',
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $payment_option = PaymentOption::where('slug', 'FLUTTERWAVE')->first();
        $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();

        if(!empty($paymentoption))
        {
            $secret_key=$paymentoption->api_secret_key;
            // International Card
            $data = array(
               "card_number"=>$request->card_number,
               "cvv"=>$request->cvv,
               "expiry_month"=>$request->expiry_month,
               "expiry_year"=>$request->expiry_year,
               "currency"=>$request->currency,
               "country"=>($request->currency=="ZAR")?"ZA":"NG",
               "amount"=>$request->amount,
               "email"=>$user->email,
               "fullname"=>$user->first_name.' '.$user->last_name,
               "tx_ref"=>"MC-3243e1",
               "redirect_url"=>route("api.verifyFlutterwaveTransaction").'#ignore_opener',
            );
            //Nigerian Card
            // $data = array(
            //     "card_number"=>"5531886652142950",
            //    "cvv"=>"564",
            //    "expiry_month"=>"09",
            //    "expiry_year"=>"32",
            //    "currency"=>"NGN",
            //    "amount"=>"1000",
            //    "email"=>"user@gmail.com",
            //    "fullname"=>"yemi desola",
            //    "tx_ref"=>"MC-3243e",
            //    "redirect_url"=>"http://127.0.0.1:8000/merchant/admin/handyman/verifyFlutterwaveTransaction",
            // );
            // p($data);
            $data = json_encode($data);
            $key=$this->getKey($secret_key);
            $encryt_key=$this->encrypt3Des($data,$key);
            $fields=array(
                'client'=>$encryt_key
            );
            $fields = json_encode($fields);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.flutterwave.com/v3/charges?type=card");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json',
                "Authorization: Bearer $secret_key"));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response);
            // p($response);
            if($response->status=='success'){
                // p($response->meta->authorization->mode);
                if($response->meta->authorization->mode=='avs_noauth'){
                     $data1 = array(
                       "card_number"=>$request->card_number,
                       "cvv"=>$request->cvv,
                       "expiry_month"=>$request->expiry_month,
                       "expiry_year"=>$request->expiry_year,
                       "currency"=>$request->currency,
                       "country"=>($request->currency=="ZAR")?"ZA":"NG",
                       "amount"=>$request->amount,
                       "email"=>$user->email,
                       "fullname"=>$user->first_name.' '.$user->last_name,
                       "tx_ref"=>"MC-3243e1",
                        "redirect_url"=>route("api.verifyFlutterwaveTransaction").'#ignore_opener',
                        "authorization"=>array(
                            "mode"=>"avs_noauth",
                            "city"=>"San Francisco",
                            "address"=>"333 Fremont Street, San Francisco, CA",
                            "state"=>"California",
                            "country"=>"US",
                            "zipcode"=>"94105"
                       )

                    );
                }
                elseif($response->meta->authorization->mode=='pin'){
                    $data1 = array(
                        "card_number"=>"5531886652142950",
                       "cvv"=>"564",
                       "expiry_month"=>"09",
                       "expiry_year"=>"32",
                       "currency"=>"NGN",
                       "amount"=>"1000",
                       "email"=>"user@gmail.com",
                       "fullname"=>"yemi desola",
                       "tx_ref"=>"MC-3243e",
                       "redirect_url"=>"https://demo.apporioproducts.com/multi-service/public/api/user/verifyFlutterwaveTransaction#ignore_opener",
                        "authorization"=>array(
                            "mode"=>"pin",
                            "pin"=>"3310"
                       )

                    );
                }
                $data1 = json_encode($data1);
                $key=$this->getKey($secret_key);
                $encryt_key=$this->encrypt3Des($data1,$key);
                $fields=array(
                    'client'=>$encryt_key
                );
                $fields = json_encode($fields);
                $ch1 = curl_init();
                curl_setopt($ch1, CURLOPT_URL, "https://api.flutterwave.com/v3/charges?type=card");
                curl_setopt($ch1, CURLOPT_HTTPHEADER, array('content-type: application/json',
                    "Authorization: Bearer $secret_key"));
                curl_setopt($ch1, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                $res = curl_exec($ch1);
                curl_close($ch1);
                $response = json_decode($res);
                // p($response);
                if($response->status=='success'){
                    if($response->meta->authorization->mode=='redirect'){
                        // p($response->meta->authorization->redirect);
                        UserCard::create([
                            'user_id' => $user->id,
                            'token' => $response->data->flw_ref,
                            'payment_option_id' => 20,
                            'card_number' => $request->card_number,
                            'exp_month' => $request->expiry_month,
                            'exp_year' => $request->expiry_year,
                            'card_type' => $response->data->card->type,
                            'user_token'=>$response->data->id,
                            'status' => '0'
                        ]);

                        // return redirect($response->meta->authorization->redirect);
                        return response()->json(['result' => '1', 'redirect' => $response->meta->authorization->redirect]);
                    }
                    elseif($response->meta->authorization->mode=='otp'){
                        // p()
                         // p($response);
                        return redirect("https://api.flutterwave.com/v3/validate-charge");
                    }
                }
                else{
                    return response()->json(['result' => '0','message'=>$response->message]);
                }
            }
            else{
                return response()->json(['result' => '0','message'=>$response->message]);
            }
        }else {
            return response()->json(['result' => "0", 'message' => "configurations not found"]);
        }
    }

    public function verifyFlutterwaveTransaction(Request $request){
        $request=$request->all();
        $request=json_decode($request['response']);
        $id=$request->id;

        $card = UserCard::with('User')->where([['user_token','=',$id]])->first();
        $merchant_id=$card['user']->merchant_id;

        $payment_option = PaymentOption::where('slug', 'FLUTTERWAVE')->first();
        $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/".$request->id."/verify",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer $paymentoption->api_secret_key"
          ),
        ));

        $res = curl_exec($curl);
        $response = json_decode($res);
        curl_close($curl);
        // p($response);
        if($response->status=='success')
        {
            $card->token=$response->data->card->token;
            $card->status='1';
            $card->save();

            $paramArray = array(
                'user_id' => $card['user']->id,
                'booking_id' => NULL,
                'amount' => $response->data->amount,
                'narration' => 12,
                'platform' => 2,
                'payment_method' => 2,
                'transaction_id' => NULL,
            );
            WalletTransaction::UserWalletCredit($paramArray);
            return response()->json(['result' => '1','message'=>'Payment done sucessfully. Please go back to the wallet']);
        }else{
            return response()->json(['result' => 'error']);
        }
    }

    public function getKey($seckey){
      $hashedkey = md5($seckey);
      $hashedkeylast12 = substr($hashedkey, -12);

      $seckeyadjusted = str_replace("FLWSECK-", "", $seckey);
      $seckeyadjustedfirst12 = substr($seckeyadjusted, 0, 12);

      $encryptionkey = $seckeyadjustedfirst12.$hashedkeylast12;
      return $encryptionkey;

    }

    public function encrypt3Des($data, $key){

        $encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);

        return base64_encode($encData);

    }

    public function YoPaymentRequest(Request $request){
        $user = $request->user('api');
        $merchant_id=$user->merchant_id;
        $validator = Validator::make($request->all(), [
            'account_no' => 'required',
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $string_file = $this->getStringFile($user->merchant_id);
        $payment_option = PaymentOption::where('slug', 'YOPayments')->first();
        $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id','=', $payment_option->id]])->first();
        if(!empty($paymentoption)){
            $yoAPI = new YoAPI($paymentoption->api_public_key, $paymentoption->api_secret_key);
            $yoAPI->set_nonblocking("TRUE");
            $response = $yoAPI->ac_deposit_funds($request->account_no, $request->amount, 'Wallet Recharge');
            if ($response['Status']=='OK')

                return $this->successResponse(trans("$string_file.success"),$response['TransactionReference']);
//                return response()->json(['result' => '1','message'=>$response['TransactionReference']]);
            else
            return $this->failedResponse($response['TransactionReference']);
//                return response()->json(['result' => '0','message'=>$response['StatusMessage']]);
        }else {
            return $this->failedResponse(trans("$string_file.configuration")." ".trans("$string_file.data_not_found"));
//            return response()->json(['result' => "0", 'message' => "configurations not found"]);
        }
    }

    /***
     * display only last 4 digit of card number rest will be replaced by "*"
     * $cc_number is card number from db
    ***/
    public  function getCardNumber($cc_number){
        $length = strlen($cc_number);
        if($length > 4)
        {
            return substr_replace($cc_number, str_repeat('*', $length - 4), 0, $length - 4);
        }else{
            return $cc_number;
        }
    }

    public function getCardDetails($user_cards, $merchant_id){
        $cards = [];
        foreach ($user_cards as $card) {
            $paymentoption = PaymentOptionsConfiguration::where([['merchant_id', '=', $merchant_id], ['payment_option_id', '=', $card->payment_option_id]])->first();
            if(!empty($paymentoption)){
                switch ($card->PaymentOption->slug) {
                    case "STRIPE":
                        if (!empty($paymentoption)) {
                            $stripe = $paymentoption->api_secret_key;
                            $newCard = new StripeController($stripe);
//                                $cards = $newCard->ListCustomer($cardList);
                            $stripe_card = $newCard->CustomerDetails($card);
                            $stripe_card["id"] = $card->id;
                            $stripe_card["slug"] = "STRIPE";
                            $stripe_card["token"] = "";
                            array_push($cards, $stripe_card);
                        }
                        break;
                    case "MONERIS":
                        if (!empty($paymentoption)) {
                            $storeId = $paymentoption->api_secret_key;
                            $apiToken = $paymentoption->auth_token;
                            $newCard = new RandomPaymentController();
                            $moneris_card = $newCard->MonerisViewCard($card->token, $storeId, $apiToken);
                            if (!empty($moneris_card) && $moneris_card['api_response']['ResponseCode'] == 001) {
                                array_push($cards, array(
                                    'id' => $card->id,
                                    'card_id' => $card->id,
                                    'card_number' => $moneris_card['card_details']['masked_pan'],
                                    'card_type' => '',
                                    'exp_month' => '',
                                    'exp_year' => '',
                                    'exp_date' => $moneris_card['card_details']['expdate'],
                                    'slug' => "MONERIS",
                                    'token' => $card->token,
                                ));
                            }
                        }
                        break;
                    default:
                        array_push($cards, array(
                            'id' => $card->id,
                            'card_type' => $card->card_type,
                            'expiry_date' => $card->expiry_date,
                            'exp_month' => $card->exp_month,
                            'exp_year' => $card->exp_year,
                            'card_id' => $card->id,
                            'card_number' => $this->getCardNumber($card->card_number),
                            'slug' => $card->PaymentOption->slug,
                            'token' => $card->token,
                        ));
                        break;
                }
            }
        }
        return $cards;
    }

    public function PaystackRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_code' => 'required|string',
            'bank_name' => 'required|string',
            'account_holder_name' => 'required|string',
            'account_number' => 'required|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $driver = $request->user('api-driver');
        DB::beginTransaction();
        try {
            $request_data = [
                'business_name' => $request->account_holder_name,
                'bank_code' => $request->bank_code,
                'account_number' => $request->account_number,
                'percentage_charge' => 0.2
            ];

            $payment_option = PaymentOption::where('slug', 'PAYSTACK')->first();
            $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $driver->merchant_id],['payment_option_id' ,'=', $payment_option->id]])->first();
            $gateway_condition = $paymentoption->gateway_condition == 1 ? true : false;

//            $paystack =  new Paystack("sk_test_aff9ebdf40859731c8c661985c55a32fb929bb7e", "", false);
            $paystack =  new Paystack($paymentoption->api_secret_key, $paymentoption->api_public_key, $gateway_condition);
            $subaccount_code = $paystack->createUpdateSubAccount($request_data, $driver->paystack_account_id);

            $driver->paystack_account_id = $subaccount_code;
            $driver->paystack_account_status = 'active';
            $driver->bank_name = $request->bank_name;
            $driver->online_code = $request->bank_code;
            $driver->account_holder_name = $request->account_holder_name;
            $driver->account_number = $request->account_number;
            $driver->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        $driver_data = new DriverLoginResource($driver);
        DB::commit();
        return $this->successResponse(trans('api.message104'), array('driver' => $driver_data));
    }

    public function getPaystackBankCodes(Request $request){
        try {
            $driver = $request->user('api-driver');

            $payment_option = PaymentOption::where('slug', 'PAYSTACK')->first();
            $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $driver->merchant_id],['payment_option_id' ,'=', $payment_option->id]])->first();
            $gateway_condition = $paymentoption->gateway_condition == 1 ? true : false;

//            $paystack =  new Paystack("sk_test_aff9ebdf40859731c8c661985c55a32fb929bb7e", "", false);
            $paystack =  new Paystack($paymentoption->api_secret_key, $paymentoption->api_public_key, $gateway_condition);
            $bank_codes = $paystack->fetchBankCodes();
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans('api.message104'), array('bank_codes' => $bank_codes));
    }

    public function getPaystackBankCodesForMerchant($merchant_id){
        try {
            $payment_option = PaymentOption::where('slug', 'PAYSTACK')->first();
            $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id' ,'=', $payment_option->id]])->first();
            $gateway_condition = $paymentoption->gateway_condition == 1 ? true : false;

            $paystack =  new Paystack($paymentoption->api_secret_key, $paymentoption->api_public_key, $gateway_condition);
            return $paystack->fetchBankCodes();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function registerPaystackDriverForMerchant($merchant_id, $request_data, $paystack_account_id = NULL){
        try {
            $payment_option = PaymentOption::where('slug', 'PAYSTACK')->first();
            $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $merchant_id],['payment_option_id' ,'=', $payment_option->id]])->first();
            $gateway_condition = $paymentoption->gateway_condition == 1 ? true : false;

            $paystack =  new Paystack($paymentoption->api_secret_key, $paymentoption->api_public_key, $gateway_condition);
            return $paystack->createUpdateSubAccount($request_data, $paystack_account_id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
