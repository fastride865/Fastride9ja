<?php

namespace App\Http\Controllers\PaymentMethods\PayHere;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\DriverCard;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use App\Models\Merchant;
use App\Models\User;
use App\Models\UserCard;
use DateTime;
use Carbon\Carbon;
use App\Models\Onesignal;

class PayHereController extends Controller
{
    use ApiResponseTrait,MerchantTrait;

    public function AddCardTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $user = ($request->type == 1) ? $request->user('api') : $request->user('api-driver');
        $user_id = $user->id;
        $merchant_id = $user->merchant_id;
        $payment_option = PaymentOption::where('slug', 'PAYHERE')->first();
        $paymentOption = PaymentOptionsConfiguration::where([['merchant_id', '=', $merchant_id],['payment_option_id','=',$payment_option->id]])->first();
        if (empty($paymentOption)) {
            return response()->json(['result' => 0, 'message' => trans('api.message194')]);
        }
        $merchant_id_api_key = $paymentOption->api_public_key;
        $url = $paymentOption->gateway_condition == 1 ? "https://www.payhere.lk/pay/preapprove" : "https://sandbox.payhere.lk/pay/preapprove";
        $transaction_id = Carbon::now()->timestamp;
        
        DB::table('transactions')->insert([
            'status' => $request->type,
            'user_id' => $request->type == 1 ? $user_id : NULL,
            'driver_id' => $request->type == 1 ? NULL : $user_id,
            'merchant_id' => $merchant_id,
            'payment_option_id' => $payment_option->payment_option_id,
            'amount' => 1,
            'booking_id' => NULL,
            'order_id' => NULL,
            'handyman_order_id' => NULL,
            'payment_transaction_id' => $transaction_id,
            'request_status' => 1,
            'payment_mode' => 'Card',
        ]);

        $data = array(
            'url' => $url,
            'merchant_id' => $merchant_id_api_key,
            'notify_url' => route('PayHere.AddCardNotification'),
            'return_url' => "",
            'cancel_url' => "",
            'order_id' => $transaction_id,
            'items' => "Add Card",
            'currency' => $user->CountryArea->Country->isoCode ?? ($user->Country->isoCode ?? ''),
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => ($request->type == 1) ? $user->UserPhone : $user->phoneNumber,
            'address' => "Address Not Specified",
            'city' => $user->CountryArea->CountryAreaName ?? 'N/A',
            'country' => $user->CountryArea->Country->CountryName ?? ($user->Country->CountryName ?? 'Sri Lanka'),
        );
        return response()->json(['result' => 1, 'message' => trans('api.success'), 'data' => $data]);
    }

    public function AddCardCallBack(Request $request)
    {
        \Log::channel('payhere_api')->emergency($request->all());
        try{
            $validator = Validator::make($request->all(), [
                'merchant_id' => 'required',
                'order_id' => 'required',
                'payhere_amount' => 'required',
                'payhere_currency' => 'required',
                'status_code' => 'required',
                'md5sig' => 'required',
                'status_message' => 'required',
                'customer_token' => 'required',
            ]);
            if ($validator->fails()) {
                $errors = $validator->messages()->all();
                return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
            }
            $merchant_id_api_key = $request->merchant_id;
            $order_id = $request->order_id;
            $payment_id = $request->payment_id;
            $payhere_amount = $request->payhere_amount;
            $payhere_currency = $request->payhere_currency;
            $status_code = $request->status_code;
            $md5sig = $request->md5sig;
            $status_message = $request->status_message;
            $customer_token = $request->customer_token;
            $payment_option = PaymentOption::where('slug', 'PAYHERE')->first();
            $paymentOption = PaymentOptionsConfiguration::where([['api_public_key', '=', $merchant_id_api_key],['payment_option_id','=',$payment_option->id]])->first();
            $merchant_id = $paymentOption->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
    
            $trans = DB::table('transactions')->where([['payment_transaction_id','=',$order_id], ['merchant_id','=',$merchant_id]])->first();
            if(!empty($trans) && $trans->request_status == 1){
                $merchant_secret = $paymentOption->api_secret_key;
                $local_md5sig = strtoupper (md5 ( $merchant_id_api_key . $order_id . $payhere_amount . $payhere_currency . $status_code . strtoupper(md5($merchant_secret)) ) );
                $message = trans("$string_file.payment_done");
                $data = ['result' => '1', 'amount' => $payhere_currency.' '.$payhere_amount, 'message' => $message];
                if (($local_md5sig === $md5sig) AND ($status_code == 2) ){
                    DB::table('transactions')->where('payment_transaction_id',$order_id)->update([
                        'amount' => $payhere_currency.' '.$payhere_amount,
                        'request_status' => 2,
                        'reference_id' => $payment_id,
                        'status_message' => $status_message
                    ]);
                    if($trans->status == 1){
                        $user = User::find($trans->user_id);
                        $paramArray = array(
                            'user_id' => $user->id,
                            'booking_id' => NULL,
                            'amount' => $payhere_amount,
                            'narration' => 2,
                            'platform' => 2,
                            'payment_method' => 2,
                            'payment_option_id' => $paymentOption->payment_option_id,
                            'transaction_id' => 2,
                        );
                        WalletTransaction::UserWalletCredit($paramArray);
    
                        $card = new UserCard;
                        $card->user_id = $user->id;

                        $notification_data['notification_type'] = 'CARD_ADDED';
                        $notification_data['segment_data'] = [];
                        $notification_data['segment_type'] = 'CARD_ADDED';
                        // send save card notification to user
                        $arr_param = array(
                            'user_id' => $user->id,
                            'data'=>$notification_data,
                            'message'=>trans("$string_file.card_saved_successfully"),
                            'merchant_id'=>$user->merchant_id,
                            'title' => trans("$string_file.card_added"),
                            'large_icon'=>""
                        );
                        Onesignal::UserPushMessage($arr_param);

                    }else{
                        $driver = Driver::find($trans->driver_id);
                        $paramArray = array(
                            'driver_id' => $driver->id,
                            'booking_id' => NULL,
                            'amount' => $payhere_amount,
                            'narration' => 2,
                            'platform' => 2,
                            'payment_method' => 2,
                            'payment_option_id' => $paymentOption->payment_option_id,
                            'receipt' => $order_id,
                        );
                        WalletTransaction::WalletCredit($paramArray);
    
                        $card = new DriverCard;
                        $card->driver_id = $driver->id;


                        // send notification to driver
                        $notification_data['notification_type'] = 'CARD_ADDED';
                        $notification_data['segment_type'] = 'CARD_ADDED';
                        $notification_data['segment_data'] = [];
                        $arr_param = array(
                            'driver_id' => $driver->id,
                            'large_icon' => "",
                            'data'=>$notification_data,
                            'message'=>trans("$string_file.card_saved_successfully"),
                            'merchant_id'=>$driver->merchant_id,
                            'title' => trans("$string_file.card_added"),
                        );
                        Onesignal::DriverPushMessage($arr_param);

                    }
                    $card->card_number = $request->card_no;
                    $card->token = $customer_token;
                    $card->payment_option_id = $payment_option->id;
                    $card->expiry_date = $request->card_expiry;
                    $card->exp_month = substr($request->card_expiry,0,2);
                    $card->exp_year = substr($request->card_expiry,-2);
                    $card->card_type = $_POST['method'];
                    $card->save();
                    DB::table('transactions')->where('payment_transaction_id',$order_id)->update([
                        'card_id' => $card->id
                    ]);
                }
            }
            return response()->json(['status' => 1, 'message' => 'PayHere Success']);
        } catch(\Exception $e) {
            \Log::channel('payhere_api')->emergency(array('PayHere Error - '. $e->getMessage()));
            return response()->json(['status' => 0, 'message' => 'PayHere Error - '. $e->getMessage()]);
        }
        
    }

    public function CardPayment($amount, $currency, $user_id, $type, $card, $booking_id = NULL, $order_id = NULL, $handyman_order_id = NULL)
    {
        try{
            $user = $type == 1 ? User::find($user_id) : Driver::find($user_id);
            $transaction_id = Carbon::now()->timestamp;
            $merchant_id = $user->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
            $payment_option = PaymentOption::where('slug', 'PAYHERE')->first();
            $paymentOption = PaymentOptionsConfiguration::where([['merchant_id', '=', $user->merchant_id],['payment_option_id','=',$payment_option->id]])->first();
            if(empty($paymentOption)){
                return array('result' => false, 'message' => trans("$string_file.payment_configuration_not_found"));
            }

            DB::table('transactions')->insert([
                'status' => $type,
                'card_id' => $card->id,
                'user_id' => $type == 1 ? $user_id : NULL,
                'driver_id' => $type == 1 ? NULL : $user_id,
                'merchant_id' => $merchant_id,
                'payment_option_id' => $paymentOption->payment_option_id,
                'amount' => $currency.' '.$amount,
                'booking_id' => $booking_id,
                'order_id' => $order_id,
                'handyman_order_id' => $handyman_order_id,
                'payment_transaction_id' => $transaction_id,
                'request_status' => 1,
                'payment_mode' => 'Card',
            ]);

            $token_url = $paymentOption->gateway_condition == 1 ? "https://www.payhere.lk/merchant/v1/oauth/token" : "https://sandbox.payhere.lk/merchant/v1/oauth/token";
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $token_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "grant_type=client_credentials",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic ".$paymentOption->auth_token,
                    "Content-Type: application/x-www-form-urlencoded"
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response);

            if(isset($response->access_token) && !empty($response->access_token != '')){
                $charge_url = $paymentOption->gateway_condition == 1 ? "https://www.payhere.lk/merchant/v1/payment/charge" : "https://sandbox.payhere.lk/merchant/v1/payment/charge";
                $post_array = array(
                    'order_id' => "$transaction_id",
                    'items' => 'Payment',
                    'currency' => $currency,
                    'amount' => $amount,
                    'customer_token' => $card->token
                );
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $charge_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($post_array),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Bearer ".$response->access_token,
                        "Content-Type: application/json"
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $response = json_decode($response);
                if(isset($response->data->status_code) && $response->data->status_code == 2){
                    DB::table('transactions')->where('payment_transaction_id',$transaction_id)->update([
                        'request_status' => 2,
                        'reference_id' => $response->data->payment_id,
                        'status_message' => $response->msg,
                    ]);
                    return array('result' => true, 'message' => 'Payment Success');
                } else{
                    DB::table('transactions')->where('payment_transaction_id',$transaction_id)->update([
                        'request_status' => 3,
                        'status_message' => $response->msg,
                    ]);
                    return array('result' => false, 'message' => 'Payment Pending or Failed');
                }
            }else{
                DB::table('transactions')->where('payment_transaction_id',$transaction_id)->update([
                    'request_status' => 3,
                    'status_message' => 'Authorization Failed'
                ]);
                return array('result' => false, 'message' => 'Authorization Failed');
            }
        } catch (\Exception $e) {
            return array('result' => false, 'message' => $e->getMessage());
        }
        
    }
}
