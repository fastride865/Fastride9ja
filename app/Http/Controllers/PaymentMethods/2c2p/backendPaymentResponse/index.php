<?php 

//Import necessary classes
require "../utils/PaymentGatewayHelper.php";

//Get SecretKey from 2C2P PGW dashboard
$secret_key = "7jYcp4FxFdf0"; 

//Get payment response from POST method
$encoded_payment_response = urldecode($_REQUEST["paymentResponse"]);

//Important: Generate signature
//Init 2C2P PaymentGatewayHelper
$pgw_helper = new PaymentGatewayHelper(); 

//Important: Verify response signature
$is_valid_signature = $pgw_helper->validateSignature($encoded_payment_response, $secret_key);

if($is_valid_signature) {

    //Parse payment response and convert JSON to std object
    $payment_response = $pgw_helper->parseAPIResponse($encoded_payment_response);

    //Get payment result
    echo $invoice_no = $payment_response->invoiceNo;
    echo "\n";
    echo $resp_code = $payment_response->respCode;
} else {

    //Return invalid response message and dont trust this payment response.
    echo "Payment response has been modified by middle man attack, do not trust and use this payment response. Please contact 2c2p support.";
}
?>