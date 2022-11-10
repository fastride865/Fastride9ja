<?php

//Import necessary classes
require "../utils/PaymentGatewayHelper.php";
require "../enum/APIEnvironment.php";
require "../model/SignatureError.php";

//Set API request enviroment
$api_env = APIEnvironment::SANDBOX . "/paymentInquiry";

//Request information 
//Set API request version
$api_version = "1.1";

//Merchant's account information
//Get MerchantID when opening account with 2C2P
$mid = "JT01"; 
//Get SecretKey from 2C2P PGW dashboard
$secret_key = "7jYcp4FxFdf0"; 

//Inquiry information
//Note: Can be request with transaction id or invoice no
//Set your transaction id
$transaction_id = "1345111"; 
//(Optional) Set your invoice no
$invoice_no = "1534489652";

//---------------------------------- Request ---------------------------------------//

//Build payment inquiry request
$payment_inquiry_request = new stdClass();
$payment_inquiry_request->version = $api_version;
$payment_inquiry_request->merchantID = $mid;
$payment_inquiry_request->transactionID = $transaction_id;
// $payment_inquiry_request->invoiceNo = $invoice_no;

//Important: Generate signature
//Init 2C2P PaymentGatewayHelper
$pgw_helper = new PaymentGatewayHelper(); 

//Generate signature of payload
$hashed_signature = $pgw_helper->generateSignature($payment_inquiry_request, $secret_key);

//Set hashed signature
$payment_inquiry_request->signature = $hashed_signature;

//---------------------------------- Response ---------------------------------------//

//Do Payment Inquiry API request
$encoded_payment_inquiry_response = $pgw_helper->requestAPI($api_env, $payment_inquiry_request); 

//Important: Verify response signature
$is_valid_signature = $pgw_helper->validateSignature($encoded_payment_inquiry_response, $secret_key);

if($is_valid_signature) {

    //Parse api response
    $payment_inquiry_response = $pgw_helper->parseAPIResponse($encoded_payment_inquiry_response);

    //Get payment result
    $invoice_no = $payment_inquiry_response->invoiceNo;
    $resp_code = $payment_inquiry_response->respCode;
} else {

    //Return encoded error response
    echo base64_encode(json_encode(get_object_vars(new SignatureError($api_version))));
}

?>