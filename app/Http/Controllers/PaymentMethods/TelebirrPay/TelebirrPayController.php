<?php

namespace App\Http\Controllers\PaymentMethods\TelebirrPay;

use App\Http\Controllers\Controller;
use App\Models\DriverCard;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use App\Models\UserCard;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TelebirrPayController extends Controller
{
    use ApiResponseTrait;
    public function getTelebirrPayConfig($merchant_id){
        $payment_option = PaymentOption::where('slug', 'TELEBIRRPAY')->first();
        $paymentOption = PaymentOptionsConfiguration::where([['merchant_id', '=', $merchant_id],['payment_option_id','=',$payment_option->id]])->first();
        if (empty($paymentOption)) {
            return response()->json(['result' => 0, 'message' => trans('api.message194')]);
        }
        return $paymentOption;
    }

    public function generateTeliberrUrl(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }

        $user = $request->type == 1 ? $request->user('api') : $request->user('api-driver');
        $paymentConfig = $this->getTelebirrPayConfig($user->merchant_id);
        $api = $paymentConfig->payment_redirect_url;
        $appkey = $paymentConfig->api_secret_key;
        $publicKey = $paymentConfig->auth_token;
        $outTradeNo = date('Ymd').time();

        $data=[
            'outTradeNo' => $outTradeNo,
            'subject' => $user->Merchant->BusinessName.' Payment',
            'totalAmount' => $request->amount,
            'shortCode' => $paymentConfig->tokenization_url,
            'notifyUrl' => route('teliberrNotify',['merchant_id' => $user->merchant_id]),
            'returnUrl' => route('teliberrCallback',['outTradeNo' => $outTradeNo]),
            'receiveName' => $user->Merchant->BusinessName,
            'appId' => $paymentConfig->api_public_key,
            'timeoutExpress' => 30,
            'nonce' => $this->TeliberrUUID(),
            'timestamp' => time()
        ];
        // dd($data);
        ksort($data);
        $ussd = $data;
        $data['appKey'] = $appkey;
        ksort($data);
        $sign = $this->sign($data);
        $encode = [
            'appid' => $data['appId'],
            'sign' => $sign['sha256'],
            'ussd' => $this->encryptRSA(json_encode($ussd),$publicKey)
        ];

        list($returnCode, $returnContent) = $this->http_post_json($api, json_encode($encode));
        if($returnCode == 200){
            DB::table('telebirrpay_transactions')->insert([
                'merchant_id' => $user->merchant_id,
                'user_id' => $user->id,
                'type' => $request->type,
                'outTradeNo' => $outTradeNo,
                'amount' => $request->amount,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $rsp = json_decode($returnContent,true);
            return $this->successResponse($rsp['message'], ['payment_url' => $rsp['data']['toPayUrl'],'success_url' => route('teliberr.success'),'failed_url' => route('teliberr.failed')]);
        }else{
            return $this->failedResponse('Fail:'.$returnCode);
        }
    }

    public function teliberrNotify($merchant_id){
        \Log::channel('teliberrPay')->emergency(['merchant_id' => $merchant_id]);
        $paymentConfig = $this->getTelebirrPayConfig($merchant_id);
        $content = file_get_contents('php://input');
        $publicKey = $paymentConfig->auth_token;
        
        $nofityData = $this->decryptRSA($content, $publicKey);
        // \Log::channel('teliberrPay')->emergency($nofityData);
        $notify_data = json_decode($nofityData,true);
        \Log::channel('teliberrPay')->emergency($notify_data);
        if(!empty($notify_data)){
            if ($notify_data['tradeStatus'] == 2){
                $trans = DB::table('telebirrpay_transactions')->where(['payment_status' => null, 'outTradeNo' => $notify_data['outTradeNo']])->first();
                if (!empty($trans)){
                    DB::table('telebirrpay_transactions')
                        ->where(['outTradeNo' => $notify_data['outTradeNo']])
                        ->update(['payment_status' => 'Success', 'updated_at' => date('Y-m-d H:i:s'), 'callback_response' => $nofityData]);
                }
            }else{
                $trans = DB::table('telebirrpay_transactions')->where(['payment_status' => null, 'outTradeNo' => $notify_data['outTradeNo']])->first();
                if (!empty($trans)){
                    DB::table('telebirrpay_transactions')
                        ->where(['outTradeNo' => $notify_data['outTradeNo']])
                        ->update(['payment_status' => 'Failed', 'updated_at' => date('Y-m-d H:i:s'), 'callback_response' => $nofityData]);
                }
            }
        }
    }

    public function sign($params){
        $signPars = '';
        foreach($params as $k => $v){
            if($signPars == ''){
                $signPars = $k.'='.$v;
            }else{
                $signPars = $signPars.'&'.$k.'='.$v;
            }
        }
        $sign = [
            'sha256' => hash("sha256", $signPars),
            'values' => $signPars
        ];
        return $sign;
    }

    public function encryptRSA($data, $public){
        $pubPem = chunk_split($public, 64, "\n");
        $pubPem = "-----BEGIN PUBLIC KEY-----\n" . $pubPem . "-----END PUBLIC KEY-----\n";

        $public_key = openssl_pkey_get_public($pubPem);
        if(!$public_key){
            die('invalid public key');
        }
        $crypto = '';
        foreach(str_split($data, 117) as $chunk){
            $return = openssl_public_encrypt($chunk, $cryptoItem, $public_key);
            if(!$return){
                return('fail');
            }
            $crypto .= $cryptoItem;
        }
        $ussd = base64_encode($crypto);
        return $ussd;
    }

    public function decryptRSA($source, $key) {
        $pubPem = chunk_split($key, 64, "\n");
        $pubPem = "-----BEGIN PUBLIC KEY-----\n" . $pubPem . "-----END PUBLIC KEY-----\n";
        $public_key = openssl_pkey_get_public($pubPem);
        if(!$public_key){
            return 'invalid public key';
        }
        $decrypted='';//decode must be done before spliting for getting the binary String
        $data=str_split(base64_decode($source),256);
        foreach($data as $chunk){
            $partial = '';//be sure to match padding
            $decryptionOK = openssl_public_decrypt($chunk,$partial,$public_key,OPENSSL_PKCS1_PADDING);
            if($decryptionOK===false){
                return 'fail';
            }
            $decrypted.=$partial;
        }
        return $decrypted;
    }

    public function TeliberrUUID()
    {
        return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function http_post_json($url, $jsonStr){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array($httpCode, $response);
    }

    public function teliberrCallback($outTradeNo){
        $trans = DB::table('telebirrpay_transactions')->where(['outTradeNo' => $outTradeNo])->first();
        if(!empty($trans)){
            if($trans->payment_status == "Success"){
                return redirect(route('teliberr.success'));
            }else{
                return redirect(route('teliberr.failed'));
            }
        }else{
            return redirect(route('teliberr.failed',"outTradeNo Data Not Found"));
        }
    }
    
    public function teliberrSuccess(Request $request){
        $response = "Success!";
        return view('payment/telebirr_pay/callback', compact('response'));
    }
    
    public function teliberrFailed(Request $request){
        $response = !empty($request->msg) ? $request->msg : "Failed!";
        return view('payment/telebirr_pay/callback', compact('response'));
    }
}
