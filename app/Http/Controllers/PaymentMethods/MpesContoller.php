<?php

namespace App\Http\Controllers\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\VPCPaymentCodesHelpers;
use App\Http\Controllers\Helper\VPCPaymentConnection;
use App\Models\Booking;
use App\Models\PaymentOptionsConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MpesContoller extends Controller
{
    public function start(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'amount' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => 'Amount & booking Id required !!']);
        }
        $user = $request->user('api');
        $gateway_details = PaymentOptionsConfiguration::where([['merchant_id', '=', $user->merchant_id]])->first();
        if(!empty($gateway_details)){
        $random = mt_rand(1000, 2000);
        $time = time();
        $random_string = $random . "" . $time;
        $data = array('amount' => $request->amount, 'random_string' => $random_string, 'redirection_url' => $gateway_details->payment_redirect_url, 'gateway_merchant' => $gateway_details->api_secret_key, 'accesscode' => $gateway_details->api_public_key);
        return view('merchant.paymentgateways.PHP_VPC_3Party_Order', compact('data'));
        }else{
            return response()->json(['result' => "0", 'message' => "Payment Configuration Empty"]);
        }
    }

    public function paymentsubmit(Request $request)
    {
        $input_added = $request->except('_token', 'SubButL');
        $conn = new VPCPaymentConnection();


        // This is secret for encoding the SHA256 hash
        // This secret will vary from merchant to merchant

        $secureSecret = "5A29A379EB414C95BA4E56C66D48B732";

        // Set the Secure Hash Secret used by the VPC connection object
        $conn->setSecureSecret($secureSecret);


        // *******************************************
        // START OF MAIN PROGRAM
        // *******************************************
        // Sort the POST data - it's important to get the ordering right
        unset($input_added["_token"]);
        ksort($input_added);
        //echo"<pre>";
        //print_r($_POST);
        //die();
        // add the start of the vpcURL querystring parameters
        $vpcURL = $input_added["virtualPaymentClientURL"];
        // This is the title for display
        $title = $input_added["Title"];
        // Remove the Virtual Payment Client URL from the parameter hash as we
        // do not want to send these fields to the Virtual Payment Client.
        unset($input_added["virtualPaymentClientURL"]);
        unset($input_added["SubButL"]);
        unset($input_added["Title"]);
        // Add VPC post data to the Digital Order
        foreach ($input_added as $key => $value) {
            if (strlen($value) > 0) {
                $conn->addDigitalOrderField($key, $value);
            }
        }
        // Add original order HTML so that another transaction can be attempted.
        //$conn->addDigitalOrderField("AgainLink", $againLink);

        // Obtain a one-way hash of the Digital Order data and add this to the Digital Order
        $secureHash = $conn->hashAllFields();
        //$conn->addDigitalOrderField("Title", $title);
        $conn->addDigitalOrderField("vpc_SecureHash", $secureHash);
        $conn->addDigitalOrderField("vpc_SecureHashType", "SHA256");

        // Obtain the redirection URL and redirect the web browser
        $vpcURL = $conn->getDigitalOrder($vpcURL);

        redirect()->to($vpcURL)->send();

    }

    public function paymentresponse(Request $request)
    {
        $received_input = $request->all();
        $conn = new VPCPaymentConnection();

        // This is secret for encoding the SHA256 hash
        // This secret will vary from merchant to merchant

        $secureSecret = "5A29A379EB414C95BA4E56C66D48B732";

        // Set the Secure Hash Secret used by the VPC connection object
        $conn->setSecureSecret($secureSecret);

        // Set the error flag to false
        $errorExists = false;

        // *******************************************
        // START OF MAIN PROGRAM
        // *******************************************


        // This is the title for display
        //$title  = $_GET["Title"];


        // Add VPC post data to the Digital Order
        foreach ($received_input as $key => $value) {
            if (($key != "vpc_SecureHash") && ($key != "vpc_SecureHashType") && ((substr($key, 0, 4) == "vpc_") || (substr($key, 0, 5) == "user_"))) {
                $conn->addDigitalOrderField($key, $value);
            }
        }

        // Obtain a one-way hash of the Digital Order data and
        // check this against what was received.
        $serverSecureHash = array_key_exists("vpc_SecureHash", $received_input) ? $received_input["vpc_SecureHash"] : "";
        $secureHash = $conn->hashAllFields();
        if ($secureHash == $serverSecureHash) {
            $hashValidated = "<font color='#00AA00'><strong>CORRECT</strong></font>";
        } else {
            $hashValidated = "<font color='#FF0066'><strong>INVALID HASH</strong></font>";
            $errorsExist = true;
        }

        /*  If there has been a merchant secret set then sort and loop through all the
            data in the Virtual Payment Client response. while we have the data, we can
            append all the fields that contain values (except the secure hash) so that
            we can create a hash and validate it against the secure hash in the Virtual
            Payment Client response.

            NOTE: If the vpc_TxnResponseCode in not a single character then
            there was a Virtual Payment Client error and we cannot accurately validate
            the incoming data from the secure hash.

            // remove the vpc_TxnResponseCode code from the response fields as we do not
            // want to include this field in the hash calculation

            if (secureSecret != null && secureSecret.length() > 0 &&
                (fields.get("vpc_TxnResponseCode") != null || fields.get("vpc_TxnResponseCode") != "No Value Returned")) {

                // create secure hash and append it to the hash map if it was created
                // remember if secureSecret = "" it wil not be created
                String secureHash = vpc3conn.hashAllFields(fields);

                // Validate the Secure Hash (remember  hashes are not case sensitive)
                if (vpc_Txn_Secure_Hash.equalsIgnoreCase(secureHash)) {
                    // Secure Hash validation succeeded, add a data field to be
                    // displayed later.
                    hashValidated = "<font color='#00AA00'><strong>CORRECT</strong></font>";
                } else {
                    // Secure Hash validation failed, add a data field to be
                    // displayed later.
                    errorExists = true;
                    hashValidated = "<font color='#FF0066'><strong>INVALID HASH</strong></font>";
                }
            } else {
                // Secure Hash was not validated,
                hashValidated = "<font color='orange'><strong>Not Calculated - No 'SECURE_SECRET' present.</strong></font>";
            }
        */

        // Extract the available receipt fields from the VPC Response
        // If not present then let the value be equal to 'Unknown'
        // Standard Receipt Data
        $againLink = array_key_exists("AgainLink", $_GET) ? $_GET["AgainLink"] : "";
        $amount = array_key_exists("vpc_Amount", $_GET) ? $_GET["vpc_Amount"] : "";
        $locale = array_key_exists("vpc_Locale", $_GET) ? $_GET["vpc_Locale"] : "";
        $batchNo = array_key_exists("vpc_BatchNo", $_GET) ? $_GET["vpc_BatchNo"] : "";
        $command = array_key_exists("vpc_Command", $_GET) ? $_GET["vpc_Command"] : "";
        $message = array_key_exists("vpc_Message", $_GET) ? $_GET["vpc_Message"] : "";
        $version = array_key_exists("vpc_Version", $_GET) ? $_GET["vpc_Version"] : "";
        $cardType = array_key_exists("vpc_Card", $_GET) ? $_GET["vpc_Card"] : "";
        $orderInfo = array_key_exists("vpc_OrderInfo", $_GET) ? $_GET["vpc_OrderInfo"] : "";
        $receiptNo = array_key_exists("vpc_ReceiptNo", $_GET) ? $_GET["vpc_ReceiptNo"] : "";
        $merchantID = array_key_exists("vpc_Merchant", $_GET) ? $_GET["vpc_Merchant"] : "";
        $merchTxnRef = array_key_exists("vpc_MerchTxnRef", $_GET) ? $_GET["vpc_MerchTxnRef"] : "";
        $authorizeID = array_key_exists("vpc_AuthorizeId", $_GET) ? $_GET["vpc_AuthorizeId"] : "";
        $transactionNo = array_key_exists("vpc_TransactionNo", $_GET) ? $_GET["vpc_TransactionNo"] : "";
        $acqResponseCode = array_key_exists("vpc_AcqResponseCode", $_GET) ? $_GET["vpc_AcqResponseCode"] : "";
        $txnResponseCode = array_key_exists("vpc_TxnResponseCode", $_GET) ? $_GET["vpc_TxnResponseCode"] : "";
        $riskOverallResult = array_key_exists("vpc_RiskOverallResult", $_GET) ? $_GET["vpc_RiskOverallResult"] : "";

        // Obtain the 3DS response
        $vpc_3DSECI = array_key_exists("vpc_3DSECI", $_GET) ? $_GET["vpc_3DSECI"] : "";
        $vpc_3DSXID = array_key_exists("vpc_3DSXID", $_GET) ? $_GET["vpc_3DSXID"] : "";
        $vpc_3DSenrolled = array_key_exists("vpc_3DSenrolled", $_GET) ? $_GET["vpc_3DSenrolled"] : "";
        $vpc_3DSstatus = array_key_exists("vpc_3DSstatus", $_GET) ? $_GET["vpc_3DSstatus"] : "";
        $vpc_VerToken = array_key_exists("vpc_VerToken", $_GET) ? $_GET["vpc_VerToken"] : "";
        $vpc_VerType = array_key_exists("vpc_VerType", $_GET) ? $_GET["vpc_VerType"] : "";
        $vpc_VerStatus = array_key_exists("vpc_VerStatus", $_GET) ? $_GET["vpc_VerStatus"] : "";
        $vpc_VerSecurityLevel = array_key_exists("vpc_VerSecurityLevel", $_GET) ? $_GET["vpc_VerSecurityLevel"] : "";


        // CSC Receipt Data
        $cscResultCode = array_key_exists("vpc_CSCResultCode", $_GET) ? $_GET["vpc_CSCResultCode"] : "";
        $ACQCSCRespCode = array_key_exists("vpc_AcqCSCRespCode", $_GET) ? $_GET["vpc_AcqCSCRespCode"] : "";

        // Get the descriptions behind the QSI, CSC and AVS Response Codes
        // Only get the descriptions if the string returned is not equal to "No Value Returned".

        $txnResponseCodeDesc = "";
        $cscResultCodeDesc = "";
        $avsResultCodeDesc = "";
        $payment_helper = new VPCPaymentCodesHelpers();
        if ($txnResponseCode != "No Value Returned") {
            $txnResponseCodeDesc = $payment_helper->getResultDescription($txnResponseCode);
        }

        if ($cscResultCode != "No Value Returned") {
            $cscResultCodeDesc = $payment_helper->getCSCResultDescription($cscResultCode);
        }

        $error = "";
        // Show this page as an error page if error condition
        if ($txnResponseCode == "7" || $txnResponseCode == "No Value Returned" || $errorExists) {
            $error = "Error ";
        }

        if ($txnResponseCode == 0 && $message == 'Approved') {
           return redirect(route('paymentsuccessfull'));
        } else {
            return redirect(route('paymentfailed'));
        }

    }
}
