<?php

namespace App\Http\Controllers\PaymentMethods\Paygate;

use App\Http\Controllers\PaymentMethods\Paygate\DeleteVaultRequest;
use App\Http\Controllers\PaymentMethods\Paygate\CardVaultRequest;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Http\Controllers\PaymentMethods\Paygate\SingleVaultRequest;
use App\Http\Controllers\PaymentMethods\Paygate\SingleFollowUpRequest;
use App\Http\Controllers\PaymentMethods\Paygate\WebPaymentRequest;
use App\Http\Controllers\PaymentMethods\Paygate\QueryRequest;
use App\Models\DriverCard;
use App\Models\Onesignal;
use App\Models\BookingDetail;
use App\Models\PaymentMethod;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Driver;
use App\Models\UserCard;
use Illuminate\Http\Request;
use DB;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use Illuminate\Support\Facades\Validator;
use PayGate\PayHost\Helper;
use Browser as BrowserDetect;
use SoapClient;
use SoapVar;
use SoapFault;
use App\Http\Controllers\PaymentMethods\Payment;
use App\Traits\ContentTrait;



class PaygateController extends Controller
{
    use ApiResponseTrait, MerchantTrait, ContentTrait;

    public function __construct()
    {
//        $this->Account = new \PayGate\PayHost\types\Account();
//
//        $this->Account->setPayGateId((Integer)env('PAYGATE_ID', 10011072130));
//        $this->Account->setPassword(env("PAYGATE_PASSWORD",'test'));
        // $this->Account->setPayGateId(1028822100012);
//        1044022
        // $this->Account->setPassword('gqY0szq6hfdaerOgUUoYZUy1jucJivgF');
    }

    /*****
     * get Paygate webview url for user wallet topup
     *
     ******/
    public function getWebViewUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'type' => 'required|integer',
            'payment_option_id' => 'required|integer',
            'new_card' => 'required',
            'save_card' => 'required_if:new_card,==,1', // 1 for save 2 for only payment
            'card_id' => 'required_if:new_card,==,2', // payment using saved card by token
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $user = $request->user('api');
        try {
            $string_file = $this->getStringFile($user->Merchant);
            $user_id = $user->id;
            $money = $request->amount;
            $currency = $user->Country->isoCode;
            $additional_param = ['user_id' => $user_id, 'amount' => $money, 'currency' => $currency, 'new_card' => $request->new_card, 'save_card' => $request->save_card, 'card_id' => $request->card_id, 'payment_option_id' => $request->payment_option_id, 'booking_id' => $request->booking_id, 'order_id' => $request->order_id, 'handyman_order_id' => $request->handyman_order_id, 'driver_id' => NULL];
            $return = [
                'webview_url' => route('paygate-step2', $additional_param),
            ];
            return $this->successResponse(trans("$string_file.success"), $return);
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }

    }

    public function getWebViewUrlDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
//            'type' => 'required|integer',
            'payment_option_id' => 'required|integer',
            'new_card' => 'required',
            'save_card' => 'required_if:new_card,==,1', // 1 for save 2 for only payment
            'card_id' => 'required_if:new_card,==,2', // payment using saved card by token
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $driver = $request->user('api-driver');
        try {
            $string_file = $this->getStringFile($driver->Merchant);
            $driver_id = $driver->id;
            $money = $request->amount;
            $currency = $driver->Country->isoCode;
            $additional_param = ['user_id' => NULL, 'amount' => $money, 'currency' => $currency, 'new_card' => $request->new_card, 'save_card' => $request->save_card, 'card_id' => $request->card_id, 'payment_option_id' => $request->payment_option_id, 'booking_id' => $request->booking_id, 'order_id' => NULL, 'driver_id' => $driver_id];
            $return = [
                'webview_url' => route('paygate-step2', $additional_param),
            ];
            return $this->successResponse(trans("$string_file.success"), $return);
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }

    }

    public function getAccountDetail($user, $payment_option_id)
    {
        // first object is either be user or driver
        // payment configuration
        $this->Account = new \PayGate\PayHost\types\Account();
        $paygate_id = 10011072130;
        $paygate_password = 'test';
        if (!empty($payment_option_id)) {
            $payment_info = PaymentOptionsConfiguration::where('payment_option_id', $payment_option_id)->where('merchant_id', $user->merchant_id)->first();
            if (!empty($payment_info) && $payment_info->gateway_condition == 1) {
                $paygate_id = $payment_info->api_public_key;
                $paygate_password = $payment_info->api_secret_key;
            }
        }
        $this->Account->setPayGateId($paygate_id);
        $this->Account->setPassword($paygate_password);
        return $this->Account;
    }

    public function paygateStep2(Request $request)
    {
        $user_id = NULL;
        $driver_id = NULL;
        $id = NULL;
        if (!empty($request->user_id)) {
            $user = User::Find($request->user_id);
            $user_id = $user->id;
            $currency = $user->Country->isoCode;
            $country_code = $user->Country->country_code;
            $Customer = $this->customer($user, "USER");
            $id = $user_id;
            $account = $this->getAccountDetail($user, $request->payment_option_id);
        } elseif (!empty($request->driver_id)) {
            $driver = Driver::Find($request->driver_id);
            $driver_id = $driver->id;
            $currency = $driver->Country->isoCode;
            $country_code = $driver->Country->country_code;
            $Customer = $this->customer($driver, "DRIVER");
            $id = $driver_id;
            $account = $this->getAccountDetail($driver, $request->payment_option_id);
        }

        $money = $request->amount * 100; // multiply amount by 100

        $booking = '';
        $finalMoney = $money;
        $WebPaymentRequest = new WebPaymentRequest();

        $WebPaymentRequest->setAccount($account)->setCustomer($Customer);
        $order = new \PayGate\PayHost\types\Order();
        $BillingDetails = new \PayGate\PayHost\types\BillingDetails();
        $AddressType = new \PayGate\PayHost\types\Address();
        $order->setMerchantOrderId(time());
        $order->setCurrency($currency);
        $order->getTransactionDate(\Carbon\Carbon::now('UTC'));
        // get country list
        $default_country_list = $this->countryListNew();
        $list = array_column($default_country_list, NULL, 'ISO3166_1_Alpha_2');
        $alpha_3_code = isset($list[$country_code]) ? $list[$country_code]['ISO3166_1_Alpha_3'] : "";
        $AddressType->setCountry($alpha_3_code);
        $AddressType->setCity("");
        $BillingDetails->setAddress($AddressType);
        $BillingDetails->setCustomer($Customer);
        $order->setBillingDetails($BillingDetails);
        $order->setAmount((int)($finalMoney));
        $Risk = new \PayGate\PayHost\types\Risk();
        $Browser = new \PayGate\PayHost\types\Browser();
        $Risk->setAccountNumber($id);
        $Risk->setSessionId(session()->getId());
        $Risk->setIpV4Address($request->ip());
        $Browser->setUserAgent(BrowserDetect::userAgent());
        $Browser->setLanguage('en-US');
        $Risk->setBrowser($Browser);
        $Risk->setUserId($id);
        $Risk->setUserProfile('AfriRide Client');
        $WebPaymentRequest->setRisk($Risk);
        if (!empty($request->save_card) && $request->save_card == 1 && $request->new_card == 1) {
            $WebPaymentRequest->setVault(true);
        } else {
            if (!empty($user_id)) {
                $user_card = UserCard::select('id', 'token')->where('id', $request->card_id)->where('user_id', $user_id)->first();
                if (!empty($user_card->id)) {
                    $WebPaymentRequest->setVaultId($user_card->token);
                } else {
                    $WebPaymentRequest->setVault(false);
                }
            } elseif (!empty($driver_id)) {
                $driver_card = DriverCard::select('id', 'token')->where('id', $request->card_id)->where('driver_id', $driver_id)->first();
                if (!empty($driver_card->id)) {
                    $WebPaymentRequest->setVaultId($driver_card->token);
                } else {
                    $WebPaymentRequest->setVault(false);
                }
            }
        }
        $redirect = new \PayGate\PayHost\types\Redirect();
        $redirect->setNotifyUrl(route('paygate-notify'));
        $additional_aram = ['user_id' => $user_id, 'amount' => $request->amount, 'payment_option_id' => $request->payment_option_id, 'booking_id' => $request->booking_id, 'driver_id' => $driver_id, 'order_id' => $request->order_id, 'handyman_order_id' => $request->handyman_order_id];
        if ($booking !== '') {
            $redirect->setReturnUrl(route('paygate-success', $additional_aram));
        } else {
            $redirect->setReturnUrl(route('paygate-success', $additional_aram));
        }
        $WebPaymentRequest->setRedirect($redirect);
        $WebPaymentRequest->setOrder($order);
        /****Printing log*****/
        $log_data = ['request_sending' => $WebPaymentRequest];
        \Log::channel('paygate_api')->emergency($log_data);
        $SinglePaymentRequest = new \PayGate\PayHost\types\SinglePaymentRequest();
        $SinglePaymentRequest->setWebPaymentRequest($WebPaymentRequest);
        $response = $this->callSoap($SinglePaymentRequest, 'SinglePaymentRequest', 'SinglePayment');
        if (isset($response->WebPaymentResponse)) {
            if (isset($response->WebPaymentResponse->Redirect)) {
                return view('payment.paygate.redirect3d', (array)$response->WebPaymentResponse->Redirect);
            } else {
                return redirect(url('paymentfail'));
            }
        } else {
            return redirect(url('paymentfail'));
        }
    }

    public function followuprequest($queryrequest)
    {
        $SingleFollowUpRequest = new SingleFollowUpRequest();
        $SingleFollowUpRequest->setQueryRequest($queryrequest);
        $response = $this->callSoap($SingleFollowUpRequest, 'SingleFollowUpRequest', 'SingleFollowUp');
        return $response;
    }

    public function notify(Request $request)
    {
        p($request->all());
        $input['pay_request_id'] = $request->PAY_REQUEST_ID ?? 'hello';
        $input['status'] = $request->TRANSACTION_STATUS ?? '1';
        $input['checksum'] = $request->CHECKSUM ?? '1';
        PaymentCheck::create($input);
        return 'true';
    }

    public function customer($user, $type)
    {
        $Customer = new \PayGate\PayHost\types\Customer();
        $Customer->setFirstName($user->first_name);
        $Customer->setLastName($user->last_name);
        if($type == "USER"){
            $Customer->setMobile($user->UserPhone);
        }else{
            $Customer->setMobile($user->phoneNumber);
        }
//        $Customer->setMobile($user->UserPhone);
//        $Customer->setTelephone($user->UserPhone);
        if(!empty($user->email)){
            $Customer->setEmail($user->email);
        }else{
            $Customer->setEmail($user->Merchant->email);
        }
        $Customer->setEmail($user->email);
        return $Customer;
    }

    public function getQuery($payRequestId, $account)
    {
        $QueryRequest = new QueryRequest();
        $QueryRequest->setAccount($account);
        $QueryRequest->setPayRequestId($payRequestId);
        return $QueryRequest;
    }

    public function callSoap($data, $paymentType, $type)
    {

        $xml = Helper::generateValidXmlFromObj($data, $paymentType);
        $wsdlUrl = 'https://secure.paygate.co.za/payhost/process.trans?wsdl';
        $SoapClient = new SoapClient($wsdlUrl, array('trance' => 1)); //point to WSDL and set trace value to debug
        try {
            /*
            * Send SOAP request
            */
//            p( new SoapVar($xml, XSD_ANYXML));
            $result = $SoapClient->__soapCall($type, array(
                new SoapVar($xml, XSD_ANYXML)
            ));
            // p($result);
            return $result;
            //dd($result);
        } catch (SoapFault $sf) {
            /*
            * handle errors
            */
            dd($sf);
            Log::alert(json_encode($sf));
            return $sf->getMessage();
        }
    }

    /*******Payment Success response ********/
    public function paygateStep3(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->PAY_REQUEST_ID) {
                $user_id = $request->user_id;
                $driver_id = $request->driver_id;
                if (!empty($user_id)) {
                    $user = User::find($user_id);
                    $account = $this->getAccountDetail($user, $request->payment_option_id);
                    $response = $this->followuprequest($this->getQuery($request->PAY_REQUEST_ID, $account));
                    $cardResponse = $response->QueryResponse;
                    $log_data = ['request_coming' => $cardResponse];
                    \Log::channel('paygate_api')->emergency($log_data);
                    if ($cardResponse->Status->StatusName == "Completed" && $cardResponse->Status->TransactionStatusCode == 1) {
                        $transaction_id = $cardResponse->Status->TransactionId;
                        /**** Case of Order *****/
                        // can't update payment transaction id in case of order
                        /****Case of ride *****/
                        if (!empty($request->booking_id)) {
                            // update booking payment
                            $array_param = ['booking_id' => $request->booking_id, 'transaction_id' => $transaction_id];
                            $payment = new Payment();
                            $payment->UpdateStatus($array_param);
                            $booking = Booking::find($request->booking_id);
                            $booking_obj = new BookingController;
                            $booking_obj->updateRideAmountInDriverWallet($booking,$booking->BookingTransaction,$booking->id);
//                            if ($booking->payment_status == 1) {
//                                $array_param = array(
//                                    'booking_id' => $booking->id,
//                                    'driver_id' => $booking->driver_id,
//                                    'amount' => $request->amount,
//                                    'payment_method_type' => $booking->PaymentMethod->payment_method_type,
//                                    'discount_amount' => $booking->BookingTransaction->discount_amount,
//                                    'tax_amount' => $booking->BookingTransaction->tax_amount,
//                                    'cancellation_amount_received' => $booking->BookingTransaction->cancellation_charge_received,
//                                );
//                                // user final paid amount will be credited into driver account
//                                $driverPayment = new CommonController();
//                                $driverPayment->DriverRideAmountCredit($array_param);
//                            }
                        }
                        elseif (!empty($request->order_id)) {
                            if ($request->order_id) {
                                // update order payment
                                $array_param = ['order_id' => $request->order_id, 'transaction_id' => $transaction_id];
                                $payment = new Payment();
                                $payment->UpdateStatus($array_param);
                            }
                        }
                        elseif (!empty($request->handyman_order_id)) {
                            if ($request->handyman_order_id) {
                                // update order payment
                                $array_param = ['handyman_order_id' => $request->handyman_order_id, 'transaction_id' => $transaction_id];
                                $payment = new Payment();
                                $payment->UpdateStatus($array_param);
                            }
                        }
                        else {
                            // wallet topup
                            $paramArray = array(
                                'user_id' => $user->id,
                                'booking_id' => NULL,
                                'amount' => $request->amount,
                                'narration' => 2,
                                'platform' => 2,
                                'payment_method' => 4,
                                'transaction_id' => $transaction_id,
                                'payment_option_id' => $request->payment_option_id,
                                'receipt' => "PayGate Transaction",
                            );
                            WalletTransaction::UserWalletCredit($paramArray);
                        }
                        // insert card
                        if ($cardResponse->Status->VaultId) {
                            $exp = $cardResponse->Status->PayVaultData[1]->value;
                            $card_number = $cardResponse->Status->PayVaultData[0]->value;
                            $check_existing_card = UserCard::where([['user_id', '=', $user_id], ['card_number', '=', $card_number],['payment_option_id','=',$request->payment_option_id]])->count();
                            if ($check_existing_card == 0) {
                                $user_card = new UserCard();
                                $user_card->user_id = $user->id;
                                $user_card->token = $cardResponse->Status->VaultId;
                                $user_card->card_number = $card_number;
                                $user_card->exp_month = substr($exp, 0, 2);
                                $user_card->exp_year = substr($exp, -4);
                                $user_card->expiry_date = $exp;
                                $user_card->card_type = $cardResponse->Status->PaymentType->Detail;
                                $user_card->status = 1;
                                $user_card->payment_option_id = $request->payment_option_id;
                                $user_card->save();
                            }
                        }
                        $redirect_url = url('paymentcomplate');

                    }
                    elseif (!empty($request->booking_id) && $cardResponse->Status->TransactionStatusCode == 2) {
                        $transaction_id = $cardResponse->Status->TransactionId;
                        // update booking payment
                        $booking = BookingDetail::where('booking_id',$request->booking_id);
                        $booking->payment_failure = 2;
                        $booking->save();
                        $redirect_url = url('paymentfail');
                    }
                    else
                    {
                        $redirect_url = url('paymentfail');
                    }
                }
                elseif ($driver_id) {
                    $driver = Driver::find($driver_id);
                    // sending driver object to get merchant id
                    $account = $this->getAccountDetail($driver, $request->payment_option_id);
                    $response = $this->followuprequest($this->getQuery($request->PAY_REQUEST_ID, $account));
                    $cardResponse = $response->QueryResponse;
                    $log_data = ['request_coming' => $cardResponse];
                    \Log::channel('paygate_api')->emergency($log_data);
                    if ($cardResponse->Status->StatusName == "Completed" && $cardResponse->Status->TransactionStatusCode == 1) {
                        $transaction_id = $cardResponse->Status->TransactionId;
                        // wallet topup
                        $paramArray = array(
                            'driver_id' => $driver->id,
                            'booking_id' => NULL,
                            'amount' => $request->amount,
                            'narration' => 2,
                            'platform' => 2,
                            'payment_method' => 4, // online payment using paygate card
                            'transaction_id' => $transaction_id,
                            'payment_option_id' => $request->payment_option_id,
                            'receipt' => "PayGate Transaction",
                        );
                        WalletTransaction::WalletCredit($paramArray);
                        // insert card
                        if ($cardResponse->Status->VaultId) {
                            $exp = $cardResponse->Status->PayVaultData[1]->value;
                            $card_number = $cardResponse->Status->PayVaultData[0]->value;
                            $check_existing_card = DriverCard::where([['driver_id', '=', $driver_id], ['card_number', '=', $card_number],['payment_option_id','=',$request->payment_option_id]])->count();
                            if ($check_existing_card == 0) {
                                $driver_card = new DriverCard();
                                $driver_card->driver_id = $driver->id;
                                $driver_card->token = $cardResponse->Status->VaultId;
                                $driver_card->card_number = $card_number;
                                $driver_card->exp_month = substr($exp, 0, 2);
                                $driver_card->exp_year = substr($exp, -4);
                                $driver_card->expiry_date = $exp;
                                $driver_card->card_type = $cardResponse->Status->PaymentType->Detail;
//                                    $driver_card->status = 1;
                                $driver_card->payment_option_id = $request->payment_option_id;
                                $driver_card->save();
                            }
                        }
//                        DB::commit();
                        $redirect_url = url('paymentcomplate');
                        // echo $cardResponse->Status->ResultDescription;
//                        return redirect(url('paymentcomplate'));
                    }
                    else
                    {
                        $redirect_url = url('paymentfail');
                    }
                }
            }
            \Log::channel('paygate_api')->emergency($request->all());
            DB::commit();
            // payment failed status
            if (!empty($request->booking_id) && $request->TRANSACTION_STATUS != 1) {
//                $transaction_id = $cardResponse->Status->TransactionId;
                // update booking payment
                $booking = Booking::find($request->booking_id);
                $booking->payment_failure = 2;
                $booking->save();
            }
        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage();
            $redirect_url = url('paymentfail');
        }
        // send notification to driver to make his screen refresh
        if (!empty($request->booking_id)) {
            $booking = Booking::Find($request->booking_id);
            setLocal($booking->Driver->language);
            $string_file = $this->getStringFile(null, $booking->Merchant);
            if($request->TRANSACTION_STATUS == 1)
            {
                $title = trans("$string_file.payment_success");
                $message = trans("$string_file.payment_done");
            }
            else
            {
                $title = trans("$string_file.payment_failed");
                $message = trans("$string_file.payment_failed");
            }
            $data['notification_type'] = "ONLINE_PAYMENT_RECEIVED";
            $data['segment_type'] = $booking->Segment->slag;
            $data['segment_data'] = [
                "id" => $booking->id,
                "generated_time" => $booking->order_timestamp,
            ];
            $arr_param = ['driver_id' => $booking->driver_id, 'data' => $data, 'message' => $message, 'merchant_id' => $booking->merchant_id, 'title' => $title, 'large_icon' => ""];
            Onesignal::DriverPushMessage($arr_param);
            setLocal();
        }

        // redirect on redirect url
        return redirect($redirect_url);
    }

    public function deleteCard(Request $request,$card,$user_driver , $type = 1){

        $account = $this->getAccountDetail($user_driver, $card->payment_option_id);
        $DeleteVaultRequest = new DeleteVaultRequest();
        $DeleteVaultRequest->setAccount($account);
        $DeleteVaultRequest->setVaultId($card->token);
        $SingleVaultRequest = new SingleVaultRequest();
        $SingleVaultRequest->setDeleteVaultRequest($DeleteVaultRequest);
        $request->session()->flash('error','error');
        $response  = $this->callSoap($SingleVaultRequest,'SingleVaultRequest','SingleVault');
        if($response && $response->DeleteVaultResponse->Status->StatusName == "Completed"){
            return true;
        }
        return false;
    }

    // save card
     public function vaultRequest($data)
     {
         $card_no = $data['card_number'];
         $expiry_date = $data['expiry_date'];
         $mode = $data['condition'];
         $id = "10011072130";
         $password = "test";
         if($mode == 1) // live
         {
             $id = $data['id'];
             $password = $data['password'];
         }
         $curl = curl_init();
         curl_setopt_array($curl, array(
             CURLOPT_URL => 'https://secure.paygate.co.za/payhost/process.trans',
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_ENCODING => '',
             CURLOPT_MAXREDIRS => 10,
             CURLOPT_TIMEOUT => 0,
             CURLOPT_FOLLOWLOCATION => true,
             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
             CURLOPT_CUSTOMREQUEST => 'POST',
             CURLOPT_POSTFIELDS =>'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                                    <SOAP-ENV:Header/>
                                       <SOAP-ENV:Body>
                                        <SingleVaultRequest xmlns="http://www.paygate.co.za/PayHOST">
                                        <CardVaultRequest>
                                            <Account>
                                                <PayGateId>'.$id.'</PayGateId>
                                                <Password>'.$password.'</Password>
                                            </Account>
                                            <CardNumber>'.$card_no.'</CardNumber>
                                            <CardExpiryDate>'.$expiry_date.'</CardExpiryDate>
                                        </CardVaultRequest>
                                    </SingleVaultRequest>
                                    </SOAP-ENV:Body>
                                    </SOAP-ENV:Envelope>',
             CURLOPT_HTTPHEADER => array(
                 'Content-Type: application/xml'
             ),
         ));

         $response = curl_exec($curl);
         curl_close($curl);
         $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:','ns2:'], '', $response);
         $xml = json_decode(json_encode(simplexml_load_string($clean_xml)),true);

         if(!empty($xml) && isset($xml['Body']['SingleVaultResponse']['CardVaultResponse']['Status']))
         {
             if($xml['Body']['SingleVaultResponse']['CardVaultResponse']['Status']['StatusName'] == "Completed")
             {
                 return $xml['Body']['SingleVaultResponse']['CardVaultResponse']['Status'];
             }
             return false;
         }
         return false;
     }
}