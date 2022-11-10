<?php

namespace App\Http\Controllers\PaymentMethods\PayBox;
use App\Http\Controllers\Controller;
use hisorange\BrowserDetect\Exceptions\Exception;
use Illuminate\Http\Request;
use DB;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use Illuminate\Support\Facades\Validator;
use App\Traits\ContentTrait;

class PayBoxController extends Controller
{
    use ApiResponseTrait, MerchantTrait, ContentTrait;

    public function __construct()
    {

    }

     public function payboxRequest(Request $request,$payment_option_config,$calling_from)
     {

       DB::beginTransaction();
      try {
         $url = "https://api.paybox.money/init_payment.php";
         $pg_merchant_id = '541515';
         $secret_key = "uGyTUzoZHf8xG404";
        if($payment_option_config->gateway_condition == 1)
        {
          $url = "https://api.paybox.money/init_payment.php";
          $pg_merchant_id = $payment_option_config->api_public_key;
          $secret_key =$payment_option_config->api_secret_key;

        }

        // check whether request is from driver or user
        if($calling_from == "DRIVER")
        {
            $driver = $request->user('api-driver');
            $id = $driver->id;
            $merchant_id = $driver->merchant_id;
            $description = "driver wallet topup";
        }
        else
        {
            $user = $request->user('api');
            $id = $user->id;
            $merchant_id = $user->merchant_id;
            $description = "payment from user";
        }
         $transaction_id = $id.'_'.time();
        $request_body = $requestForSignature = [
           'pg_order_id' => $transaction_id,
           'pg_merchant_id'=> $pg_merchant_id,
           'pg_amount' => $request->amount,
           'pg_description' => $description,
           'pg_salt' => 'molbulak',
           'pg_success_url' => route('paybox-success'),
           'pg_failure_url' => route('paybox-fail'),
           'pg_success_url_method' => 'GET',
           'pg_failure_url_method' => 'GET',
           'pg_user_id' => (string)$id,
           //'pg_testing_mode'=>1,
           'pg_language'=>'en',
           'pg_result_url'=>route('paybox-result'),
           'pg_request_method'=>'GET',
           'pg_card_id'=>NULL,
           'pg_ps_currency'=>'KGS'
        ];

    $requestForSignature = $this->makeFlatParamsArray($requestForSignature);
        //p($requestForSignature);
     // Генерация подписи
     ksort($requestForSignature); // Сортировка по ключю
     array_unshift($requestForSignature, 'init_payment.php'); // Добавление в начало имени скрипта
     array_push($requestForSignature, $secret_key); // Добавление в конец секретного ключа
     // create signature of request param
     $request_body['pg_sig'] = md5(implode(';', $requestForSignature)); // Полученная подпись

     $signature = $request_body['pg_sig'];
     $request_body = json_encode($request_body);

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
          CURLOPT_POSTFIELDS =>$request_body,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        $clean_xml = str_ireplace(['SOAP-ENV:', 'SOAP:','ns2:'], '', $response);
        $return_response = json_decode(json_encode(simplexml_load_string($clean_xml)),true);
//p($return_response);
        // write into log file
        $data = [
          'type'=>'Payment Request & Response',
          'request_data'=>$request_body,
          'return_data'=>$return_response
        ];
        \Log::channel('paybox_api')->emergency($data);
        $redirect_url = "";
        if(isset($return_response['pg_status']) && $return_response['pg_status'] == 'ok')
        {
          $redirect_url = $return_response['pg_redirect_url'];
          $pg_payment_id = $return_response['pg_payment_id'];
          // insert data in transaction table
           DB::table('transactions')->insert([
               'status' => 1, // for user
               'card_id' => NULL,
               'user_id' => $calling_from == "USER" ? $id : NULL,
               'driver_id' => $calling_from == "DRIVER" ? $id : NULL,
               'merchant_id' => $merchant_id,
               'payment_option_id' => $payment_option_config->payment_option_id,
               'checkout_id' => NULL,
               'booking_id' => isset($additional_data['booking_id']) ? $additional_data['booking_id'] : NULL,
               'order_id' => isset($additional_data['order_id']) ? $additional_data['order_id'] :NULL,
               'handyman_order_id' => isset($additional_data['handyman_order_id']) ? $additional_data['handyman_order_id'] : NULL,
               'payment_transaction_id' => $transaction_id,
               'payment_transaction' => NULL,
               'request_status' => 1,
               'amount' => $request->amount,
               'reference_id' => $pg_payment_id,
           ]);
        }
        else {
         // p($return_response);
           throw new \Exception($return_response['pg_error_description']);
          // code...
        }
        curl_close($curl);
      }
      catch(\Exception $e)
      {
        DB::rollBack();
        throw new \Exception($e->getMessage());

      }
      DB::commit();
        return [
            'status'=>'NEED_TO_OPEN_WEBVIEW',
            'url'=>$redirect_url
        ];
     }

  public function makeFlatParamsArray($arrParams, $parent_name = '')
     {
         $arrFlatParams = [];
         $i = 0;
         foreach ($arrParams as $key => $val) {
             $i++;
             /**
              * Имя делаем вида tag001subtag001
              * Чтобы можно было потом нормально отсортировать и вложенные узлы не запутались при сортировке
              */
             $name = $parent_name . $key . sprintf('%03d', $i);
             if (is_array($val)) {
                 $arrFlatParams = array_merge($arrFlatParams, $this->makeFlatParamsArray($val, $name));
                 continue;
             }
             $arrFlatParams += array($name => (string)$val);
         }

         return $arrFlatParams;
     }

    public function payboxSuccess(Request $request){
      // $request->request->add(['pg_status'=>'success']);
        $data = $request->all();

      //p( $data);
      $log_data = [
        'type'=>"Success URL",
        'response'=>$data
      ];
        \Log::channel('paybox_api')->emergency($log_data);
        //$this->updateTransaction($data);
    }

    public function payboxFail(Request $request){
      // $request->request->add(['pg_status'=>'fail']);
        $data = $request->all();

     // p($data);
       $log_data = [
        'type'=>"Failed URL",
        'response'=>$data
      ];
        \Log::channel('paybox_api')->emergency($log_data);
      //  \Log::channel('paybox_api')->emergency($data);
        //$this->updateTransaction($data);
    }
   public function payboxResult(Request $request){
      //  $request->request->add(['pg_status'=>'result']);
        $data = $request->all();

         $log_data = [
        'type'=>"Result URL",
        'response'=>$data
      ];
        \Log::channel('paybox_api')->emergency($log_data);
     // p($data);
        //\Log::channel('paybox_api')->emergency($data);
        $this->updateTransaction($data);
    }

    public function updateTransaction($data){
      //p($data);
        if(isset($data['pg_result']))
        {
            $request_status = $data['pg_result'] == 1 ? 2 : 3;
            $transaction_id = $data['pg_order_id'];
            $reference_id = $data['pg_payment_id'];
            $status_message = $data['pg_result'];
            DB::table('transactions')->where('payment_transaction_id', $transaction_id)->where('reference_id', $reference_id)->update(['request_status' => $request_status,'status_message'=>$status_message]);
        }
    }
}
