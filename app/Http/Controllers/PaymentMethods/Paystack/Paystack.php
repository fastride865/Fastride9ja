<?php
namespace App\Http\Controllers\PaymentMethods\Paystack;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use App\Traits\MerchantTrait;
use DB;

class Paystack extends Controller
{
    use MerchantTrait;

    private $secret_key = "NULL";
    private $public_key = NULL;
    private $base_url = NULL;
    private $is_live = false;

    public function __construct($secret_key, $public_key, $is_live)
    {
        $this->secret_key = $secret_key;
        $this->public_key = $public_key;
        $this->is_live = $is_live;
        $this->base_url = ($is_live == true) ? "https://api.paystack.co" : "https://api.paystack.co";
    }

    public function fetchBankCodes()
    {
        try {
            $auth_token = base64_encode($this->secret_key.":"."");

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->base_url.'/bank',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic '.$auth_token,
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, true);
            if(isset($response['data'])){
                return $response['data'];
            }else{
                return [];
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function createUpdateSubAccount($request_data, $sub_account_id = NULL)
    {
        $url = !empty($sub_account_id) ? $this->base_url.'/subaccount/'.$sub_account_id : $this->base_url.'/subaccount';
        $request_type = !empty($sub_account_id) ? "PUT" : "POST";
        $auth_token = $this->secret_key;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $request_type,
            CURLOPT_POSTFIELDS => json_encode($request_data),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$auth_token,
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        if(isset($response['status']) && $response['status'] == 1){
            return $response["data"]["subaccount_code"];
        }else{
            throw new \Exception($response['message']);
        }
    }
}