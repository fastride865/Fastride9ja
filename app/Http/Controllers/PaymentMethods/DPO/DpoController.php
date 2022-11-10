<?php

namespace App\Http\Controllers\PaymentMethods\DPO;
use App\Http\Controllers\Controller;
use App\Models\UserCard;
use hisorange\BrowserDetect\Exceptions\Exception;
use Illuminate\Http\Request;
use DB;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use Illuminate\Support\Facades\Validator;
use App\Traits\ContentTrait;
use SimpleXMLElement;
use App\Models\Transaction;
use App\Models\DriverCard;
use App\Models\PaymentOptionsConfiguration;


class DpoController extends Controller
{
    use ApiResponseTrait, MerchantTrait, ContentTrait;

    public function __construct()
    {
    }


    // DPO Think Payment


//    Card payment by webview

    public function paymentRequest($request,$payment_option_config,$calling_from){

        try {
            // check whether gateway is on sandbox or live
            $url = "https://secure.3gdirectpay.com/API/v6/";
//            $token_url = "https://api-uat.kushkipagos.com/card-async/v1/tokens";

            if($payment_option_config->gateway_condition == 1)
            {
                $url = "https://secure.3gdirectpay.com/API/v6/";
//                $token_url = "https://token.clover.com/v1/tokens";
            }

            // check whether request is from driver or user
            if($calling_from == "DRIVER")
            {
                $driver = $request->user('api-driver');
                $code = $driver->Country->phonecode;
                $country_code = $driver->Country->country_code;
                $country = $driver->Country;
                $country_name = $country->CountryName;
                $currency = $driver->Country->isoCode;
                $phone_number = $driver->phoneNumber;
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
                $phone_number = $user->UserPhone;
                $logged_user = $user;
                $user_merchant_id = $user->user_merchant_id;
                $first_name = $user->first_name;
                $last_name = $user->last_name;
                $email = $user->email;
                $id = $user->id;
                $merchant_id = $user->merchant_id;
                $description = "payment from user";
                $country_code = $user->Country->country_code;
            }

            $amount = $request->amount;
            $transaction_id = $id.'_'.time();
            $redirect_url =route("dpo-callback");

            // Pickme sandabox details
//            8D3DA73D-9D7F-4E09-96D4-3D44E7A83EA3
//            49FKEOA
//                3854

// p($payment_option_config);
// p($country_code);
            $arr_post_data =  '<?xml version="1.0" encoding="utf-8"?>
                        <API3G>
                            <CompanyToken>'.$payment_option_config->api_secret_key.'</CompanyToken>
                            <Request>createToken</Request>
                            <Transaction>
                                <PaymentAmount>'.$amount.'</PaymentAmount>
                                <PaymentCurrency>'.$currency.'</PaymentCurrency>
                                <CompanyRef>'.$payment_option_config->auth_token.'</CompanyRef>
                                <RedirectURL>'.$redirect_url.'</RedirectURL>
                                <BackURL> '.route("dpo-back").'</BackURL>
                                <CompanyRefUnique>0</CompanyRefUnique>
                                <PTL>15</PTL>
                                <PTLtype>hours</PTLtype>
                                <customerFirstName>'.$first_name.'</customerFirstName>
                                <customerLastName>'.$last_name.'</customerLastName>
                                <customerZip></customerZip>
                                <customerCity></customerCity>
                                <customerCountry>'.$country_code.'</customerCountry>
                                <customerPhone>'.$phone_number.'</customerPhone>
                        
                                <customerEmail>'.$email.'</customerEmail>
                                <AllowRecurrent>1</AllowRecurrent>
                            </Transaction>
                            <Services>
                                <Service>
                                    <ServiceType>'.$payment_option_config->api_public_key.'</ServiceType>
                                    <ServiceDescription>'.$description.'</ServiceDescription>
                                    <ServiceDate>'.date('Y-m-d').'</ServiceDate>
                                </Service>
                            </Services>
                        </API3G>';

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
                CURLOPT_POSTFIELDS =>$arr_post_data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type:application/xml'
                ),
            ));
            $response = curl_exec($curl);

            curl_close($curl);
            $xml = new SimpleXMLElement($response);
            $response_data = $xml = json_encode($xml);
            $xml = json_decode($xml, true);
            if (isset($xml['Result']) &&  $xml['Result'] == 000) {

                $data = [
                    'type'=>'payment request',
                    'data'=>$response
                ];
                \Log::channel('dpo_think_payment_api')->emergency($data);

                $tx_reference =  $xml['TransToken'];

                // enter data
                DB::table('transactions')->insert([
                    'status' => 1, // for user
                    'card_id' => NULL,
                    'user_id' => $calling_from == "USER" ? $id : NULL,
                    'driver_id' => $calling_from == "DRIVER" ? $id : NULL,
                    'merchant_id' => $merchant_id,
                    'payment_option_id' => $payment_option_config->payment_option_id,
                    'checkout_id' => NULL,
                    'payment_transaction_id' => $transaction_id,
                    'payment_transaction' => $response_data,
                    'reference_id' => $tx_reference, // payment reference id
                    'amount' => $amount, // amount
                    'request_status' => 1,
                    'status_message' => "pending",
                ]);


                $web_view_url = "https://secure.3gdirectpay.com/dpopayment.php?ID=".$tx_reference;
                return [
                    'status'=>'NEED_TO_OPEN_WEBVIEW',
                    'url'=>$web_view_url,
                    'redirect_url'=>$redirect_url
                ];

            } else {
                throw new Exception(isset($xml['ResultExplanation']) ? $xml['ResultExplanation'] : "Payment Request Failed, something went wrong");
            }

        }catch(\Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    public function PaymentCallBack(Request $request)
    {

        try
        {



            $request_response = $request->all();
            // p($request_response);
            $data = [
                'type'=>'callback notification',
                'data'=>$request_response
            ];
            \Log::channel('dpo_think_payment_api')->emergency($data);
//        p($request_response);



            $tx_reference = $request_response['TransID']; // order id
            $arr_data = DB::table("transactions")->where('reference_id', $tx_reference)->first();
            // p($arr_data);
            $payment_option = PaymentOptionsConfiguration::where([['merchant_id','=',$arr_data->merchant_id],['payment_option_id','=',$arr_data->payment_option_id]])->first();
            // p($payment_option);
            // C72F6B0A-1C7F-44E5-BFEC-CAD5C6E3F88E
            $status_url  = "https://secure.3gdirectpay.com/API/v6/";
//        $company_token =  "8D3DA73D-9D7F-4E09-96D4-3D44E7A83EA3"; //$payment_option_config->api_secret_key
            $company_token =  $payment_option->api_secret_key;
//p($company_token);
            $arr_post_data =  '<?xml version="1.0" encoding="utf-8"?>
        <API3G>
            <CompanyToken>'.$company_token.'</CompanyToken>
            <Request>verifyToken</Request>
            <TransactionToken>'.$tx_reference.'</TransactionToken>
        </API3G>';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $status_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$arr_post_data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type:application/xml'
                ),
            ));
            $response = curl_exec($curl);

            curl_close($curl);
            $xml = new SimpleXMLElement($response);
            $response_data = $xml = json_encode($xml);
            $xml = json_decode($xml, true);
// p($xml);
//p($response_data);
            //0: Successful payment 2: In progress 4: Expired 6: Canceled

            if (isset($xml['Result']) &&  $xml['Result'] == 000)
            {
                $transaction = Transaction::where('reference_id', $tx_reference)->first();
                $transaction->request_status  = 2;
                $transaction->status_message  = "Successful payment";
                $transaction->save();

//            DB::table("transactions")->where('reference_id', $tx_reference)->update(['request_status' => 2, 'status_message' => "Successful payment"]);

                // check card token
                $tx_reference = $request_response['TransID']; // order id
                $status_url  = "https://secure.3gdirectpay.com/API/v6/";
                $company_token =  $payment_option->api_secret_key;

                $user_id = NULL;
                $driver_id = NULL;

                if(!empty($transaction->driver_id))
                {
                    $email = $transaction->Driver->email;
                    $payment_for = "DRIVER";
                    $driver_id = $transaction->driver_id;
                }
                else
                {
                    $email =  $transaction->User->email;
                    $payment_for = "USER";
                    $user_id = $transaction->user_id;
                }
                $email = !empty($transaction->driver_id) ? $transaction->Driver->email : $transaction->User->email;
                // p($email);
//            SearchCriteria [1 : email, 2 phone]
                $arr_post_data =  '<?xml version="1.0" encoding="utf-8"?>
            <API3G>
                <CompanyToken>'.$company_token.'</CompanyToken>
                <Request>getSubscriptionToken</Request>
                <SearchCriteria>1</SearchCriteria> 
                <SearchCriteriaValue>'.$email.'</SearchCriteriaValue>
            </API3G>';

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $status_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>$arr_post_data,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type:application/xml'
                    ),
                ));
                $response1 = curl_exec($curl);
                $data = [
                    'type'=>'callback call back subscription',
                    'data'=>$response1
                ];
                \Log::channel('dpo_think_payment_api')->emergency($data);

// p($response1);

                $xml1 = new SimpleXMLElement($response1);
                $xml1 = json_encode($xml1);
                $xml1 = json_decode($xml1, true);
                //p($xml1);
                curl_close($curl);

                if (isset($xml1['Result']) &&  $xml1['Result'] == 000)
                {

// p('in');
                    //  sleep(60); // sleep for 1 minute
                    // if customer token exist then fetch all cards of that customer
                    // pull account / get all tokens of user and driver

                    $customer_token =  $xml1['CustomerToken'];
                    //p($customer_token);
                    $arr_post_data =  '<?xml version="1.0" encoding="utf-8"?>
                <API3G>
                    <CompanyToken>'.$company_token.'</CompanyToken>
                    <Request>pullAccount</Request>
                   <customerToken>'.$customer_token.'</customerToken>
                </API3G>';

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $status_url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS =>$arr_post_data,
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type:application/xml'
                        ),
                    ));
                    $response2 = curl_exec($curl);

                    $data = [
                        'type'=>'callback pull account details',
                        'data'=>$response2
                    ];
                    \Log::channel('dpo_think_payment_api')->emergency($data);

                    // p($response2);
                    $xml2 = new SimpleXMLElement($response2);
                    $xml2 = json_encode($xml2);
                    $xml2 = json_decode($xml2, true);
                    // p($xml2);
                    curl_close($curl);

                    if (isset($xml2['Result']) &&  $xml2['Result'] == 000)
                    {
                        $dpo_arr_cards = $xml2['paymentOptions'];
                        $dpo_arr_cards = isset($dpo_arr_cards['option']) ? $dpo_arr_cards['option'] : $dpo_arr_cards;
                        // $total_cards = count($dpo_arr_cards);

                        // p($dpo_arr_cards,0);
                        // p($total_cards);

                        if(isset($dpo_arr_cards[0])) // means response in array of objects
                        {
                            if($payment_for == "USER")
                            {
                                foreach ($dpo_arr_cards as $dpo_card)
                                {
                                    //$dpo_card = $dpo_ca;
                                    // p($dpo_card);
                                    $saved_card=UserCard::where([['user_id','=',$user_id],['token','=',$dpo_card['subscriptionToken']],['expiry_date','=',$dpo_card['expiryDate']]])->first();
                                    if(empty($saved_card))
                                    {
                                        $exp = $dpo_card['expiryDate'];
                                        $year =  substr($exp,-2,2);
                                        $month =  substr($exp,0,2);
                                        $new_card = new UserCard;
                                        $new_card->payment_option_id = $transaction->payment_option_id;
                                        $new_card->user_id = $user_id;
                                        $new_card->card_type = $dpo_card['paymentType'];
                                        $new_card->expiry_date = $dpo_card['expiryDate'];
                                        $new_card->exp_month = $month;
                                        $new_card->exp_year = $year;
                                        $new_card->token = $dpo_card['subscriptionToken'];
                                        $new_card->card_number = 'xxxxxxxxxxxx'.$dpo_card['paymentLast4'];
                                        $new_card->status = 1;
                                        $new_card->user_token = $customer_token;
                                        $new_card->save();
                                        //p($new_card);
                                    }
                                }
                            }
                            elseif($payment_for == "DRIVER")
                            {
                                foreach ($dpo_arr_cards as $dpo_card)
                                {
                                    $driver_saved_card = DriverCard::where([['driver_id', '=', $driver_id], ['token', '=', $dpo_card['subscriptionToken']], ['expiry_date', '=', $dpo_card['expiryDate']]])->first();
                                    if (empty($saved_card)) {
                                        $exp = $dpo_card['expiryDate'];
                                        $year =  substr($exp,-2,2);
                                        $month =  substr($exp,0,2);
                                        $new_card_driver = new DriverCard;
                                        $new_card_driver->payment_option_id = $transaction->payment_option_id;
                                        $new_card_driver->driver_id = $driver_id;
                                        $new_card_driver->card_type = $dpo_card['paymentType'];
                                        $new_card_driver->expiry_date = $dpo_card['expiryDate'];
                                        $new_card_driver->exp_month = $month;
                                        $new_card_driver->exp_year = $year;
                                        $new_card_driver->token = $dpo_card['subscriptionToken'];
                                        $new_card_driver->card_number = 'xxxxxxxxxxxx'.$dpo_card['paymentLast4'];
                                        $new_card_driver->driver_token = $customer_token;
                                        $new_card_driver->save();
                                    }
                                }
                            }
                        }
                        elseif(isset($dpo_arr_cards['subscriptionToken']))
                        {
                            $dpo_card = $dpo_arr_cards;
                            if($payment_for == "USER")
                            {
                                // foreach ($dpo_arr_cards as $dpo_card)
                                // {
                                //$dpo_card = $dpo_ca;
                                // p($dpo_card);
                                $saved_card=UserCard::where([['user_id','=',$user_id],['token','=',$dpo_card['subscriptionToken']],['expiry_date','=',$dpo_card['expiryDate']]])->first();
                                if(empty($saved_card))
                                {
                                    $exp = $dpo_card['expiryDate'];
                                    $year =  substr($exp,-2,2);
                                    $month =  substr($exp,0,2);
                                    $new_card = new UserCard;
                                    $new_card->payment_option_id = $transaction->payment_option_id;
                                    $new_card->user_id = $user_id;
                                    $new_card->card_type = $dpo_card['paymentType'];
                                    $new_card->expiry_date = $dpo_card['expiryDate'];
                                    $new_card->exp_month = $month;
                                    $new_card->exp_year = $year;
                                    $new_card->token = $dpo_card['subscriptionToken'];
                                    $new_card->card_number = 'xxxxxxxxxxxx'.$dpo_card['paymentLast4'];
                                    $new_card->status = 1;
                                    $new_card->user_token = $customer_token;
                                    $new_card->save();
                                    //p($new_card);
                                }
                                // }
                            }
                            elseif($payment_for == "DRIVER")
                            {
                                // foreach ($dpo_arr_cards as $dpo_card)
                                // {
                                $driver_saved_card = DriverCard::where([['driver_id', '=', $driver_id], ['token', '=', $dpo_card['subscriptionToken']], ['expiry_date', '=', $dpo_card['expiryDate']]])->first();
                                if (empty($saved_card)) {
                                    $exp = $dpo_card['expiryDate'];
                                    $year =  substr($exp,-2,2);
                                    $month =  substr($exp,0,2);
                                    $new_card_driver = new DriverCard;
                                    $new_card_driver->payment_option_id = $transaction->payment_option_id;
                                    $new_card_driver->driver_id = $driver_id;
                                    $new_card_driver->card_type = $dpo_card['paymentType'];
                                    $new_card_driver->expiry_date = $dpo_card['expiryDate'];
                                    $new_card_driver->exp_month = $month;
                                    $new_card_driver->exp_year = $year;
                                    $new_card_driver->token = $dpo_card['subscriptionToken'];
                                    $new_card_driver->card_number = 'xxxxxxxxxxxx'.$dpo_card['paymentLast4'];
                                    $new_card_driver->driver_token = $customer_token;
                                    $new_card_driver->save();
                                    // }
                                }
                            }

                        }

                    }
                }
                // p('end');

            }


        }catch(\Exception $e)
        {

//        p($e->getLine(),0);
            return $e->getMessage();
            //p($e->getTrace());
        }



//        Array
//        (
//            [TransID] => 36AB94FC-57AD-4A7B-B16C-5AABDAA9F432
//    [CCDapproval] => 4444444451
//    [PnrID] => 49FKEOA
//    [TransactionToken] => 36AB94FC-57AD-4A7B-B16C-5AABDAA9F432
//    [CompanyRef] => 49FKEOA
//)


    }


    public function paymentStatus(Request $request)
    {
        $tx_reference = $request->transaction_id; // order id
        $transaction_table =  DB::table("transactions")->where('reference_id',$tx_reference)->first();

//        if($transaction_table->request_status != 2) // payment pending/failed
//        {
//            $status_url  = "https://secure.3gdirectpay.com/API/v6/";
//            $company_token =  "8D3DA73D-9D7F-4E09-96D4-3D44E7A83EA3"; //$payment_option_config->api_secret_key
//
//            $arr_post_data =
//
//        <API3G>
//            <CompanyToken>'.$company_token.'</CompanyToken>
//            <Request>verifyToken</Request>
//            <TransactionToken>'.$tx_reference.'</TransactionToken>
//        </API3G>';
//
//            $curl = curl_init();
//            curl_setopt_array($curl, array(
//                CURLOPT_URL => $status_url,
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_ENCODING => '',
//                CURLOPT_MAXREDIRS => 10,
//                CURLOPT_TIMEOUT => 0,
//                CURLOPT_FOLLOWLOCATION => true,
//                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                CURLOPT_CUSTOMREQUEST => 'POST',
//                CURLOPT_POSTFIELDS =>$arr_post_data,
//                CURLOPT_HTTPHEADER => array(
//                    'Content-Type:application/xml'
//                ),
//            ));
//            $response = curl_exec($curl);
//
//            curl_close($curl);
//            $xml = new SimpleXMLElement($response);
//            $response_data = $xml = json_encode($xml);
//            $xml = json_decode($xml, true);
//
//
//            $request_response = $request->all();
//            $data = [
//                'type'=>'check payment status',
//                'data'=>$response_data
//            ];
//            \Log::channel('dpo_think_payment_api')->emergency($data);
//
//
//            if (isset($xml['Result']) &&  $xml['Result'] == 000)
//            {
//                DB::table("transactions")->where('reference_id', $tx_reference)->update(['request_status' => 2, 'status_message' => "Successful payment"]);
//
//                $transaction_table = DB::table("transactions")->where('reference_id', $tx_reference)->first();
//                //0: Successful payment 2: In progress 4: Expired 6: Canceled
//            }
//        }
//        else
//        {
//            DB::table("transactions")->where('reference_id', $tx_reference)->update(['request_status' => 3, 'status_message' => "Payment failed"]);
//        }

        // check payment status
        $payment_status =   $transaction_table->request_status == 2 ?  true : false;
        return $return = [
            'payment_status' =>$payment_status ];
    }


    public function cardPayment($calling_from,$user_driver,$payment_option_config,$card,$amount){

        try {
            // check whether gateway is on sandbox or live
            $url = "https://secure.3gdirectpay.com/API/v6/";
//            $token_url = "https://api-uat.kushkipagos.com/card-async/v1/tokens";

            if($payment_option_config->gateway_condition == 1)
            {
                $url = "https://secure.3gdirectpay.com/API/v6/";
//                $token_url = "https://token.clover.com/v1/tokens";
            }

            // check whether request is from driver or user
            if($calling_from == "DRIVER")
            {
                $driver = $user_driver;
                $code = $driver->Country->phonecode;
                $country_code = $driver->Country->country_code;
                $country = $driver->Country;
                $country_name = $country->CountryName;
                $currency = $driver->Country->isoCode;
                $phone_number = $driver->phoneNumber;
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
                $user = $user_driver;
                $code = $user->Country->phonecode;
                $country = $user->Country;
                $country_name = $country->CountryName;
                $currency = $user->Country->isoCode;
                $phone_number = $user->UserPhone;
                $logged_user = $user;
                $user_merchant_id = $user->user_merchant_id;
                $first_name = $user->first_name;
                $last_name = $user->last_name;
                $email = $user->email;
                $id = $user->id;
                $merchant_id = $user->merchant_id;
                $description = "payment from user";
                $country_code = $user->Country->country_code;
            }


            $transaction_id = $id.'_'.time();
            $redirect_url =route("dpo-callback");

            $arr_post_data =  '<?xml version="1.0" encoding="utf-8"?>
                        <API3G>
                            <CompanyToken>'.$payment_option_config->api_secret_key.'</CompanyToken>
                            <Request>createToken</Request>
                            <Transaction>
                                <PaymentAmount>'.$amount.'</PaymentAmount>
                                <PaymentCurrency>'.$currency.'</PaymentCurrency>
                                <CompanyRef>'.$payment_option_config->auth_token.'</CompanyRef>
                                <RedirectURL>'.$redirect_url.'</RedirectURL>
                                <BackURL> '.route("dpo-back").'</BackURL>
                                <CompanyRefUnique>0</CompanyRefUnique>
                                <PTL>15</PTL>
                                <PTLtype>hours</PTLtype>
                                <customerFirstName>'.$first_name.'</customerFirstName>
                                <customerLastName>'.$last_name.'</customerLastName>
                                <customerZip></customerZip>
                                <customerCity></customerCity>
                                <customerCountry>'.$country_code.'</customerCountry>
                                <customerPhone>'.$phone_number.'</customerPhone>
                        
                                <customerEmail>'.$email.'</customerEmail>
                                <AllowRecurrent>1</AllowRecurrent>
                            </Transaction>
                            <Services>
                                <Service>
                                    <ServiceType>'.$payment_option_config->api_public_key.'</ServiceType>
                                    <ServiceDescription>'.$description.'</ServiceDescription>
                                    <ServiceDate>'.date('Y-m-d').'</ServiceDate>
                                </Service>
                            </Services>
                        </API3G>';

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
                CURLOPT_POSTFIELDS =>$arr_post_data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type:application/xml'
                ),
            ));
            $response = curl_exec($curl);

            curl_close($curl);
            $xml = new SimpleXMLElement($response);
            $response_data = $xml = json_encode($xml);
            $xml = json_decode($xml, true);
            if (isset($xml['Result']) &&  $xml['Result'] == 000) {

                $data = [
                    'type'=>'card payment token request',
                    'data'=>$response
                ];
                \Log::channel('dpo_think_payment_api')->emergency($data);

                $tx_reference =  $xml['TransToken'];

                // enter data
                $transaction = new Transaction;
                $transaction->status = 1;
                $transaction->card_id = $card->id;
                $transaction->user_id = $calling_from == "USER" ? $id : NULL;
                $transaction->driver_id = $calling_from == "DRIVER" ? $id : NULL;
                $transaction->merchant_id = $merchant_id;
                $transaction->payment_option_id = $payment_option_config->payment_option_id;
                $transaction->payment_transaction_id = $transaction_id;
                $transaction->payment_transaction = $response_data;
                $transaction->reference_id = $tx_reference; //payment reference id
                $transaction->amount = $amount;
                $transaction->request_status = 1; // pending
                $transaction->status_message = 'Payment pending'; // pending
                $transaction->save();


                // charge subscription token
                $arr_post_data =  '<?xml version="1.0" encoding="utf-8"?>
                        <API3G>
                            <CompanyToken>'.$payment_option_config->api_secret_key.'</CompanyToken>
                            <Request>chargeTokenRecurrent</Request>
                            <TransactionToken>'.$tx_reference.'</TransactionToken>
                            <subscriptionToken>'.$card->token.'</subscriptionToken>
                        </API3G>';

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
                    CURLOPT_POSTFIELDS =>$arr_post_data,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type:application/xml'
                    ),
                ));
                $response = curl_exec($curl);

                $data = [
                    'type'=>'card payment request result',
                    'data'=>$response
                ];
                \Log::channel('dpo_think_payment_api')->emergency($data);

                curl_close($curl);
                $xml2 = new SimpleXMLElement($response);
                $response_data = $xml = json_encode($xml);
                $xml2 = json_decode($xml, true);
                $status = false;
                $message = $xml2['ResultExplanation'];
                if (isset($xml2['Result']) &&  $xml2['Result'] == 000) {
                    $status = true;
                    $transaction->request_status = 2; // done
                    $transaction->status_message = 'Payment Done'; // pending
                    $transaction->save();
                }
                else
                {

                    $transaction->request_status = 3; // failed
                    $transaction->status_message = 'Payment failed'; // pending
                    $transaction->save();
                }
                return  [
                    'status'=>$status,
                    'message'=>$message,
                ];
            } else {
                throw new Exception(isset($xml['ResultExplanation']) ? $xml['ResultExplanation'] : "Payment Request Failed, something went wrong");
            }

        }catch(\Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    // delete card
    public function deleteCard($payment_option_config,$card,$callinf_from = "USER"){

        try {
            // p($payment_option_config);
            // check whether gateway is on sandbox or live
            $url = "https://secure.3gdirectpay.com/API/v6/";
//            $token_url = "https://api-uat.kushkipagos.com/card-async/v1/tokens";

            if($payment_option_config->gateway_condition == 1)
            {
                $url = "https://secure.3gdirectpay.com/API/v6/";
//                $token_url = "https://token.clover.com/v1/tokens";
            }
            if($callinf_from == "DRIVER")
            {
                $customer_token = $card->driver_token;

            }
            else
            {

                $customer_token = $card->user_token;
            }


            $arr_post_data =  '<?xml version="1.0" encoding="utf-8"?>
                        <API3G>
                            <CompanyToken>'.$payment_option_config->api_secret_key.'</CompanyToken>
                            <Request>deleteCard</Request>
                            <subscriptionToken>'.$card->token.'</subscriptionToken>
                            <customerToken>'.$customer_token.'</customerToken>
     
                        </API3G>';
// p($arr_post_data);
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
                CURLOPT_POSTFIELDS =>$arr_post_data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type:application/xml'
                ),
            ));
            $response = curl_exec($curl);

            curl_close($curl);
            $xml = new SimpleXMLElement($response);
            $response_data = $xml = json_encode($xml);
            $xml = json_decode($xml, true);
            $data = [
                'type' => 'card delete request',
                'data' => $response
            ];
            \Log::channel('dpo_think_payment_api')->emergency($data);

            $status = false;
            if (isset($xml['Result']) &&  $xml['Result'] == 000) {


                $status = true;
            }
            return  [
                'status'=>$status,
                'message'=>$xml['ResultExplanation'],
            ];

        }catch(\Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }



    /**Currently this function is not in use*/
    // fetch card from DPO server
    public function fetchCard(Request $request,$payment_for = "USER",$user = NULL)
    {
        $payment_option = PaymentOptionsConfiguration::where([['merchant_id','=',$request->merchant_id],
        ])
            ->whereHas('PaymentOption',function($q) use ($request) {
                $q->where('slug',$request->payment_option);
            })
            ->first();
        //p($payment_option);
        $status_url  = "https://secure.3gdirectpay.com/API/v6/";
//        $company_token =  "8D3DA73D-9D7F-4E09-96D4-3D44E7A83EA3"; //$payment_option_config->api_secret_key
        $company_token =  $payment_option->api_secret_key;
        $user_id = NULL;
        $driver_id = NULL;

        if(!empty($payment_for == "DRIVER"))
        {
            $email = $user->email;
            $driver_id = $user->id;
        }
        else
        {
            $email =  $user->email;
            $user_id = $user->id;
        }
        // p($email);
//            SearchCriteria [1 : email, 2 phone]
        $arr_post_data =  '<?xml version="1.0" encoding="utf-8"?>
            <API3G>
                <CompanyToken>'.$company_token.'</CompanyToken>
                <Request>getSubscriptionToken</Request>
                <SearchCriteria>1</SearchCriteria> 
                <SearchCriteriaValue>'.$email.'</SearchCriteriaValue>
            </API3G>';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $status_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$arr_post_data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type:application/xml'
            ),
        ));
        $response1 = curl_exec($curl);
// p($response1);

        $xml1 = new SimpleXMLElement($response1);
        $xml1 = json_encode($xml1);
        $xml1 = json_decode($xml1, true);
        //p($xml1);
        curl_close($curl);

        if (isset($xml1['Result']) &&  $xml1['Result'] == 000)
        {

// p('in');
            // if customer token exist then fetch all cards of that customer
            // pull account / get all tokens of user and driver

            $customer_token =  $xml1['CustomerToken'];
            //p($customer_token);
            $arr_post_data =  '<?xml version="1.0" encoding="utf-8"?>
                <API3G>
                    <CompanyToken>'.$company_token.'</CompanyToken>
                    <Request>pullAccount</Request>
                   <customerToken>'.$customer_token.'</customerToken>
                </API3G>';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $status_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$arr_post_data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type:application/xml'
                ),
            ));
            $response2 = curl_exec($curl);

            // p($response2);
            $xml2 = new SimpleXMLElement($response2);
            $xml2 = json_encode($xml2);
            $xml2 = json_decode($xml2, true);
            //p($xml2);
            curl_close($curl);

            if (isset($xml2['Result']) &&  $xml2['Result'] == 000)
            {
                $dpo_arr_cards = $xml2['paymentOptions'];
                $dpo_arr_cards = isset($dpo_arr_cards['option']) ? $dpo_arr_cards['option'] : $dpo_arr_cards;
                // p($dpo_arr_cards);
                if($payment_for == "USER")
                {

                    foreach ($dpo_arr_cards as $dpo_card)
                    {

                        //$dpo_card = $dpo_ca;
                        //p($dpo_card);
                        $saved_card=UserCard::where([['user_id','=',$user_id],['token','=',$dpo_card['subscriptionToken']],['expiry_date','=',$dpo_card['expiryDate']]])->first();
                        if(empty($saved_card))
                        {
                            $exp = $dpo_card['expiryDate'];
                            $year =  substr($exp,-2,2);
                            $month =  substr($exp,0,2);
                            $new_card = new UserCard;
                            $new_card->payment_option_id = $payment_option->payment_option_id;
                            $new_card->user_id = $user_id;
                            $new_card->card_type = $dpo_card['paymentType'];
                            $new_card->expiry_date = $dpo_card['expiryDate'];
                            $new_card->exp_month = $month;
                            $new_card->exp_year = $year;
                            $new_card->token = $dpo_card['subscriptionToken'];
                            $new_card->card_number = 'xxxxxxxxxxxx'.$dpo_card['paymentLast4'];
                            $new_card->status = 1;
                            $new_card->user_token = $customer_token;
                            $new_card->save();
                            //p($new_card);
                        }
                    }
                }
                elseif($payment_for == "DRIVER")
                {
                    foreach ($dpo_arr_cards as $dpo_card)
                    {
                        $driver_saved_card = DriverCard::where([['driver_id', '=', $driver_id], ['token', '=', $dpo_card['subscriptionToken']], ['expiry_date', '=', $dpo_card['expiryDate']]])->first();
                        if (empty($saved_card)) {
                            $exp = $dpo_card['expiryDate'];
                            $year =  substr($exp,-2,2);
                            $month =  substr($exp,0,2);
                            $new_card_driver = new DriverCard;
                            $new_card_driver->payment_option_id = $payment_option->payment_option_id;
                            $new_card_driver->driver_id = $driver_id;
                            $new_card_driver->card_type = $dpo_card['paymentType'];
                            $new_card_driver->expiry_date = $dpo_card['expiryDate'];
                            $new_card_driver->exp_month = $month;
                            $new_card_driver->exp_year = $year;
                            $new_card_driver->token = $dpo_card['subscriptionToken'];
                            $new_card_driver->card_number = 'xxxxxxxxxxxx'.$dpo_card['paymentLast4'];
                            $new_card_driver->driver_token = $customer_token;
                            $new_card_driver->save();
                        }
                    }
                }
            }
        }

    }

}