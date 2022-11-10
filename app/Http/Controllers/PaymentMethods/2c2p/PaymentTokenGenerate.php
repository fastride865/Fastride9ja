<?php

//Import necessary classes
require "utils/PaymentGatewayHelper.php";
require "enum/APIEnvironment.php";
require "model/SignatureError.php";

require "paymentToken/enum/CardSecureMode.php";
require "paymentToken/enum/PaymentChannel.php";

class PaymentTokenGenerate
{
    public $mid;
    public $secret_key;
    //Set currency code in 3 alphabet values as specified in ISO 4217
    public $currency_code = "MMK";
    public $api_version = "10.01";

    function __construct($mid, $secret_key) {
        $this->mid = $mid;
        $this->secret_key = $secret_key;
    }

    public function GenerateToken($amount, $desc, $invoice_no){
        //Set API request enviroment
        $api_env = APIEnvironment::SANDBOX . "/paymentToken";
        //Generate an unique random string
        $nonce_str = uniqid('', true);
        //Set payment channel
        $payment_channel = PaymentChannel::ALL;
        //Set credit card 3D Secure mode
        $request_3ds = CardSecureMode::NO;
        //Enable Card tokenization without authorization
        $tokenize_only = "Y";

        //---------------------------------- Request ---------------------------------------//
        //Construct payment token request
        $payment_token_request = new stdClass();
        $payment_token_request->version = $this->api_version;
        $payment_token_request->merchantID = $this->mid;
        $payment_token_request->invoiceNo = $invoice_no;
        $payment_token_request->desc = $desc;
        $payment_token_request->amount = $amount;
        $payment_token_request->currencyCode = $this->currency_code;
        $payment_token_request->paymentChannel = $payment_channel;
        $payment_token_request->nonceStr = $nonce_str;

        //Important: Generate signature
        //Init 2C2P PaymentGatewayHelper
        $pgw_helper = new PaymentGatewayHelper();

        //Generate signature of payload
        $hashed_signature = $pgw_helper->generateSignature($payment_token_request, $this->secret_key);

        //Set hashed signature
        $payment_token_request->signature = $hashed_signature;

        //---------------------------------- Response ---------------------------------------//
        //Do Payment Token API request
        $encoded_payment_token_response = $pgw_helper->requestAPI($api_env, $payment_token_request);

        //Important: Verify response signature
        $is_valid_signature = $pgw_helper->validateSignature($encoded_payment_token_response, $this->secret_key);

        if($is_valid_signature) {
            //Parse api response
            $payment_token_response = $pgw_helper->parseAPIResponse($encoded_payment_token_response);
            //Get payment token and pass token to your mobile application.
            return $payment_token_response->paymentToken;
        } else {
            //Return encoded error response
            return base64_encode(json_encode(get_object_vars(new SignatureError($this->api_version))));
        }
    }

    public function checkPaymentStatus($request){
        //Get payment response from POST method
        $encoded_payment_response = urldecode($request->paymentResponse);

        //Important: Generate signature
        //Init 2C2P PaymentGatewayHelper
        $pgw_helper = new PaymentGatewayHelper();

        //Important: Verify response signature
        $is_valid_signature = $pgw_helper->validateSignature($encoded_payment_response, $this->secret_key);

        if($is_valid_signature) {
            //Parse payment response and convert JSON to std object
            $payment_response = $pgw_helper->parseAPIResponse($encoded_payment_response);
            if(isset($payment_response->invoiceNo)){
                //Get payment result
                return ['result' => 1, 'invoice_no' => $payment_response->invoiceNo, 'resp_code' => $payment_response->respCode];
            }else{
                //Get payment result
                return ['result' => 0, 'message' => $payment_response->respDesc];
            }
        } else {
            return ['result' => 0, 'message' => "Payment response has been modified by middle man attack, do not trust and use this payment response. Please contact 2c2p support."];
        }
    }
}
?>