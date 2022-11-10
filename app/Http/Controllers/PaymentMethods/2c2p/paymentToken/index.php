<?php

//Import necessary classes
require "../utils/PaymentGatewayHelper.php";
require "../enum/APIEnvironment.php";
require "../model/SignatureError.php";

require "enum/CardSecureMode.php";
require "enum/PaymentChannel.php";

//Set API request enviroment
$api_env = APIEnvironment::SANDBOX . "/paymentToken"; 

//Request information 
//Set API request version
$api_version = "10.01"; 
//Generate an unique random string
$nonce_str = uniqid('', true);  

//Merchant's account information
//Get MerchantID when opening account with 2C2P
$mid = "JT01"; 
//Get SecretKey from 2C2P PGW dashboard
$secret_key = "7jYcp4FxFdf0"; 

//Transaction information
//Set product description
$desc = "2 days 1 night hotel room"; 
//Set an unique invoice no
$invoice_no = time(); 
//Set currency code in 3 alphabet values as specified in ISO 4217
$currency_code = "SGD"; 
//Amount formatted into 12-digit format with leading zero as specified in ISO 4217
$amount = "000000001000";

//Set payment options
//Set payment channel
$payment_channel = PaymentChannel::ALL;
//Set credit card 3D Secure mode
$request_3ds = CardSecureMode::NO;
//Enable Card tokenization without authorization
$tokenize_only = "Y";

//Set advance payment options
//IPP
$interest_type = "M";

//Recurring
$recurring = "Y";				//Enable / Disable RPP option
$invoice_prefix = 'demo'.time();			//RPP transaction invoice prefix
$recurring_amount = "000000000100";		//Recurring amount
$allow_accumulate = "Y";				//Allow failed authorization to be accumulated
$max_accumulateAmt = "000000001000";				//Maximum threshold of total accumulated amount
$recurring_interval = "5";			//Recurring interval by no of days
$recurring_count = "3";				//Number of Recurring occurance
$charge_next_date = (new DateTime('tomorrow'))->format("dmY");	//The first day to start recurring charges. format DDMMYYYY

//---------------------------------- Request ---------------------------------------//

//Construct payment token request
$payment_token_request = new stdClass();
$payment_token_request->version = $api_version;
$payment_token_request->merchantID = $mid;
$payment_token_request->invoiceNo = $invoice_no;
$payment_token_request->desc = $desc;
$payment_token_request->amount = $amount;
$payment_token_request->currencyCode = $currency_code;
$payment_token_request->paymentChannel = $payment_channel;
// $payment_token_request->userDefined1 = "This is my user defined 1";
// $payment_token_request->userDefined2 = "This is my user defined 2";
// $payment_token_request->userDefined3 = "This is my user defined 3";
// $payment_token_request->userDefined4 = "This is my user defined 4";
// $payment_token_request->userDefined5 = "This is my user defined 5";
// $payment_token_request->interestType = $interest_type;
// $payment_token_request->productCode = "";
// $payment_token_request->recurring = $recurring;
// $payment_token_request->invoicePrefix = $invoice_prefix;
// $payment_token_request->recurringAmount = $recurring_amount;
// $payment_token_request->allowAccumulate = $allow_accumulate;
// $payment_token_request->maxAccumulateAmt = $max_accumulateAmt;
// $payment_token_request->recurringInterval = $recurring_interval;
// $payment_token_request->recurringCount = $recurring_count;
// $payment_token_request->chargeNextDate = $charge_next_date;
// $payment_token_request->promotion = "";
// $payment_token_request->request3DS = $request_3ds;
// $payment_token_request->tokenizeOnly = $tokenize_only;
// $payment_token_request->statementDescriptor = "";
$payment_token_request->nonceStr = $nonce_str;

//Important: Generate signature
//Init 2C2P PaymentGatewayHelper
$pgw_helper = new PaymentGatewayHelper(); 

//Generate signature of payload
$hashed_signature = $pgw_helper->generateSignature($payment_token_request, $secret_key); 

//Set hashed signature
$payment_token_request->signature = $hashed_signature;

//---------------------------------- Response ---------------------------------------//

//Do Payment Token API request
$encoded_payment_token_response = $pgw_helper->requestAPI($api_env, $payment_token_request);

//Important: Verify response signature
$is_valid_signature = $pgw_helper->validateSignature($encoded_payment_token_response, $secret_key);

if($is_valid_signature) {

    //Parse api response
    $payment_token_response = $pgw_helper->parseAPIResponse($encoded_payment_token_response);
    
    //Get payment token and pass token to your mobile application.
    echo $payment_token = $payment_token_response->paymentToken;
} else {

    //Return encoded error response
    echo base64_encode(json_encode(get_object_vars(new SignatureError($api_version))));
}

?>