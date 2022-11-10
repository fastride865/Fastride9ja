<?php

namespace App\Http\Controllers\PaymentMethods;

use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\Driver;
use App\Models\IllicoCashTransaction;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use App\Models\User;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponseTrait;

class EwalletController extends Controller
{
    use ApiResponseTrait, MerchantTrait;

    public function amoleGeneratePaymentOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'payment_option' => 'required|exists:payment_options,slug',
            'phone_card_no' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $user = $request->user('api');
        $payment_option = PaymentOption::where('slug', 'AMOLE')->first();
        $paymentoption = PaymentOptionsConfiguration::where([['merchant_id', $user->merchant_id], ['payment_option_id', '=', $payment_option->id]])->first();
        if (empty($paymentoption)) {
            $message = trans("$string_file.payment_configuration_not_found");
            return $this->failedResponse($message);
        }
        $header = array(
            "HDR_Signature: CgRs_7DpRQm8StaX9n5jBdLy8sHl67rzyNTqPR4ZpPPbmsFrMBJEbyq-mb5dnitt",
            "HDR_IPAddress: 35.178.56.137",
            "HDR_UserName: hubert2",
            "HDR_Password: test",
            "Content-Type: application/x-www-form-urlencoded"
        );
        $body_param = array(
            "BODY_CardNumber" => $request->phone_card_no,
            "BODY_ExpirationDate" => "",
            "BODY_PaymentAction" => "09",
            "BODY_AmountX" => $request->amount,
            "BODY_AmoleMerchantID" => "HUBERTAXI",
            "BODY_OrderDescription" => "For Payment",
            "BODY_SourceTransID" => time(),
            "BODY_VendorAccount" => "",
            "BODY_AdditionalInfo1" => "",
            "BODY_AdditionalInfo2" => "",
            "BODY_AdditionalInfo3" => "",
            "BODY_AdditionalInfo4" => "",
            "BODY_AdditionalInfo5" => ""
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://uat.api.myamole.com:10082/amole/pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($body_param),
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        // p($response);
        if (is_array($response) && $response[0]->MSG_ShortMessage == 'Success') {
            $no_str = strlen($request->phone_card_no);
            // $payment_type_msg = $no_str == 16 ? trans("$string_file.card_pin") : trans("$string_file.otp_sent");
            // p($response);
            $payment_type_msg = $response[0]->MSG_LongMessage;
            return $this->successResponse($payment_type_msg);
        } else {
            if (!empty($response)) {
                return $this->failedResponse($response[0]->MSG_LongMessage);
            }
            return $this->successResponse("not getting response from api", []);
        }
    }

    public static function amolePayment($header, $body_param)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://uat.api.myamole.com:10082/amole/pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($body_param),
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        return $response;
    }

    public function illicoCashPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'type' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $phone_number = "00";
            if ($request->type == 1) {
                $user = $request->user('api');
                $phone_number .= str_replace("+", "", $user->UserPhone);
            } elseif ($request->type == 2) {
                $user = $request->user('api-driver');
                $phone_number .= str_replace("+", "", $user->phoneNumber);
            } else {
                return $this->failedResponse("Invalid Type");
            }
            $amount = number_format((float)$request->amount, 2, '.', '');
            $string_file = $this->getStringFile($user->merchant_id);
            $payment_option = PaymentOption::where('slug', 'IllicoCash')->first();
            $paymentoption = PaymentOptionsConfiguration::where([['merchant_id', '=', $user->merchant_id], ['payment_option_id', '=', $payment_option->id]])->first();
            if (!empty($paymentoption)) {
                $additional_data = $paymentoption->additional_data;
                $additional_data = json_decode($additional_data, true);
                $invoice_id = $this->getUniqueIllicoCashInvoiceID($user->merchant_id);
                $posting_params = array(
                    "mobilenumber" => $phone_number, //"00243907763838",
                    "trancurrency" => "USD",
                    "amounttransaction" => $amount,
                    "merchantid" => $additional_data['merchantid'],//"merch0000000000000203",
                    "invoiceid" => $invoice_id,
                    "terminalid" => $additional_data['terminalid'],//"123456789012",
                    "encryptkey" => $additional_data['encryptkey'], //"NozZSGL660ZZM8u4kUTV4CfgSy3G7wpFDQ0vCOhLWLpmnkNLkGia6mn7J2j2f4CJ/RDKF0ICxN7mBD9ciURYWj97KT2LYBoaPJVJs3hv5s5SGYoOw4fcAigt7+0nQiza",
                    "securityparams" => array(
                        "gpslatitude" => $request->latitude,
                        "gpslongitude" => $request->longitude
                    )
                );
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://test.new.rawbankillico.com:4003/RAWAPIGateway/ecommerce/payment',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($posting_params),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        "LogInName: $paymentoption->api_secret_key",
                        "LoginPass: $paymentoption->api_public_key",
                        "Authorization: Basic $paymentoption->auth_token"
                    )
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $response = json_decode($response, true);
                if (isset($response['respcode']) && $response['respcode'] == "00") {
                    $transaction = new IllicoCashTransaction();
                    $transaction->merchant_id = $user->merchant_id;
                    if ($request->type == 1) {
                        $transaction->user_id = $user->id;
                    } else {
                        $transaction->driver_id = $user->id;
                    }
                    $transaction->amount = $amount;
                    $transaction->invoice_id = $invoice_id;
                    $transaction->reference_number = $response['referencenumber'];
                    $transaction->save();
                } elseif (isset($response['respcode']) && $response['respcode'] == "93") {
                    return $this->failedResponse("Number not registered with Illico Cash.");
                } else {
                    return $this->failedResponse($response['respcodedesc']);
                }
                DB::commit();
                return $this->successResponse("Payment Request Recorded", array("invoice_id" => $invoice_id, "reference_number" => $transaction->reference_number));
            } else {
                return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
    }

    protected function getUniqueIllicoCashInvoiceID($merchant_id, $length = 10)
    {
        $key_generate = substr(str_shuffle("1234567890abcdefghijklmnopqrstuvwxyz"), 0, $length);
        if (IllicoCashTransaction::where([['invoice_id', '=', $key_generate], ['merchant_id', '=', $merchant_id]])->exists()):
            $this->getUniqueIllicoCashInvoiceID($merchant_id);
        endif;
        return $key_generate;
    }

    public function illicoCashPaymentOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required',
            'reference_number' => 'required',
            'otp' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $transaction = IllicoCashTransaction::where([["invoice_id", '=', $request->invoice_id], ["reference_number", '=', $request->reference_number]])->first();
            if (!empty($transaction)) {
                if ($transaction->payment_status == 0) {
                    $string_file = $this->getStringFile($transaction->merchant_id);
                    $payment_option = PaymentOption::where('slug', 'IllicoCash')->first();
                    $paymentoption = PaymentOptionsConfiguration::where([['merchant_id', '=', $transaction->merchant_id], ['payment_option_id', '=', $payment_option->id]])->first();
                    if (!empty($paymentoption)) {
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://test.new.rawbankillico.com:4003/RAWAPIGateway/ecommerce/payment/$request->otp/$request->reference_number",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => array(
                                'Content-Type: application/json',
                                "LogInName: $paymentoption->api_secret_key",
                                "LoginPass: $paymentoption->api_public_key",
                                "Authorization: Basic $paymentoption->auth_token"
                            ),
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $response = json_decode($response, true);
                        if ($response['respcode'] == "00") {
                            $transaction->payment_status = 1;
                            $transaction->response =  json_encode($response);
                            $transaction->save();
                            if (!empty($transaction->user_id)) {
                                $receipt = "Application : " . $transaction->reference_number;
                                $paramArray = array(
                                    'user_id' => $transaction->user_id,
                                    'booking_id' => NULL,
                                    'amount' => $transaction->amount,
                                    'narration' => 2,
                                    'platform' => 2,
                                    'payment_method' => 2,
                                    'receipt' => $receipt,
                                    'transaction_id' => $transaction->reference_number,
                                    'notification_type' => 89
                                );
                                WalletTransaction::UserWalletCredit($paramArray);
                                return $this->successResponse("Payment Status updated with Success");
                            }
                            else {
                                $receipt = "Application : " . $transaction->reference_number;
                                $paramArray = array(
                                    'driver_id' => $transaction->driver_id,
                                    'booking_id' => NULL,
                                    'amount' => $transaction->amount,
                                    'narration' => 2,
                                    'platform' => 2,
                                    'payment_method' => 3,
                                    'receipt' => $receipt,
                                    'transaction_id' => $transaction->reference_number,
                                    'notification_type' => 89
                                );
                                WalletTransaction::WalletCredit($paramArray);
                            }
                        } else {
                            $transaction->response =  json_encode($response);
                            $transaction->save();
                            return $this->failedResponse("IlliocoCash Transaction : " . $response['respcodedesc']);
                        }
                    } else {
                        return $this->failedResponse(trans("$string_file.payment_configuration_not_found"));
                    }
                } else {
                    $payment_status = ($transaction->payment_status == 1) ? "Success" : "Failed";
                    return $this->successResponse("Payment Status already updated with $payment_status");
                }
            } else {
                return $this->failedResponse("Transaction not found");
            }
        } catch (\Exception $e) {
            error_response($e->getMessage());
        }
    }
}