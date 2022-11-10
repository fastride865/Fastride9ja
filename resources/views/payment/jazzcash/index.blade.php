<script>
    function submitForm() {
        CalculateHash();
        var IntegritySalt = document.getElementById("salt").innerText;
        var hash = CryptoJS.HmacSHA256(document.getElementById("hashValuesString").value, IntegritySalt);
        document.getElementsByName("pp_SecureHash")[0].value = hash + '';
        // console.log('string: ' + hashString);
        // console.log('hash: ' + document.getElementsByName("pp_SecureHash")[0].value);
        document.jsform.submit();
    }
</script>
<script src="https://sandbox.jazzcash.com.pk/Sandbox/Scripts/hmac-sha256.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

{{--<h3>JazzCash HTTP POST (Page Redirection) Testing</h3>--}}
<div class="">
    <form name="jsform" method="post" action="https://payments.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform">
        <input type="hidden" name="pp_Version" value="1.1">
        <input type="hidden" name="pp_TxnType" value="">
        <input type="hidden" name="pp_Language" value="EN">
        <input type="hidden" name="pp_MerchantID" value="{{$merchant_id}}">
        <input type="hidden" name="pp_SubMerchantID" value="">
        <input type="hidden" name="pp_Password" value="{{$password}}">
        <input type="hidden" name="pp_BankID" value="TBANK">
        <input type="hidden" name="pp_ProductID" value="RETL">
        <input type="hidden" name="pp_TxnRefNo" id="pp_TxnRefNo" value="{{$trans_ref_no}}">
        <input type="hidden" name="pp_Amount" value="{{$amount}}">
        <input type="hidden" name="pp_TxnCurrency" value="{{$currency}}">
        <input type="hidden" name="pp_TxnDateTime" value="{{$date_time}}">
        <input type="hidden" name="pp_TxnExpiryDateTime" value="">
        <input type="hidden" name="pp_BillReference" value="{{$bill_ref}}">
        <input type="hidden" name="pp_Description" value="Description of transaction">
        <input type="hidden" name="pp_ReturnURL" value="{{$return_url}}">
        <input type="hidden" name="pp_SecureHash" value="">
        <input type="hidden" name="ppmpf_1" value="1">
        <input type="hidden" name="ppmpf_2" value="2">
        <input type="hidden" name="ppmpf_3" value="3">
        <input type="hidden" name="ppmpf_4" value="4">
        <input type="hidden" name="ppmpf_5" value="5">
        <input type="hidden" id="hashValuesString" value="">
        {{--        <button type="button" onclick="submitForm()">Submit</button>--}}
    </form>

    <label id="salt" style="display:none;">{{$salt}}</label>
</div>

<script>
    function CalculateHash() {
        var IntegritySalt = document.getElementById("salt").innerText;
        hashString = '';
        hashString += IntegritySalt + '&';

        if (document.getElementsByName("pp_Amount")[0].value != '') {
            hashString += document.getElementsByName("pp_Amount")[0].value + '&';
        }
        if (document.getElementsByName("pp_BankID")[0].value != '') {
            hashString += document.getElementsByName("pp_BankID")[0].value + '&';
        }
        if (document.getElementsByName("pp_BillReference")[0].value != '') {
            hashString += document.getElementsByName("pp_BillReference")[0].value + '&';
        }
        if (document.getElementsByName("pp_Description")[0].value != '') {
            hashString += document.getElementsByName("pp_Description")[0].value + '&';
        }
        if (document.getElementsByName("pp_Language")[0].value != '') {
            hashString += document.getElementsByName("pp_Language")[0].value + '&';
        }
        if (document.getElementsByName("pp_MerchantID")[0].value != '') {
            hashString += document.getElementsByName("pp_MerchantID")[0].value + '&';
        }
        if (document.getElementsByName("pp_Password")[0].value != '') {
            hashString += document.getElementsByName("pp_Password")[0].value + '&';
        }
        if (document.getElementsByName("pp_ProductID")[0].value != '') {
            hashString += document.getElementsByName("pp_ProductID")[0].value + '&';
        }
        if (document.getElementsByName("pp_ReturnURL")[0].value != '') {
            hashString += document.getElementsByName("pp_ReturnURL")[0].value + '&';
        }
        if (document.getElementsByName("pp_SubMerchantID")[0].value != '') {
            hashString += document.getElementsByName("pp_SubMerchantID")[0].value + '&';
        }
        if (document.getElementsByName("pp_TxnCurrency")[0].value != '') {
            hashString += document.getElementsByName("pp_TxnCurrency")[0].value + '&';
        }
        if (document.getElementsByName("pp_TxnDateTime")[0].value != '') {
            hashString += document.getElementsByName("pp_TxnDateTime")[0].value + '&';
        }
        if (document.getElementsByName("pp_TxnExpiryDateTime")[0].value != '') {
            hashString += document.getElementsByName("pp_TxnExpiryDateTime")[0].value + '&';
        }
        if (document.getElementsByName("pp_TxnRefNo")[0].value != '') {
            hashString += document.getElementsByName("pp_TxnRefNo")[0].value + '&';
        }
        if (document.getElementsByName("pp_TxnType")[0].value != '') {
            hashString += document.getElementsByName("pp_TxnType")[0].value + '&';
        }
        if (document.getElementsByName("pp_Version")[0].value != '') {
            hashString += document.getElementsByName("pp_Version")[0].value + '&';
        }
        if (document.getElementsByName("ppmpf_1")[0].value != '') {
            hashString += document.getElementsByName("ppmpf_1")[0].value + '&';
        }
        if (document.getElementsByName("ppmpf_2")[0].value != '') {
            hashString += document.getElementsByName("ppmpf_2")[0].value + '&';
        }
        if (document.getElementsByName("ppmpf_3")[0].value != '') {
            hashString += document.getElementsByName("ppmpf_3")[0].value + '&';
        }
        if (document.getElementsByName("ppmpf_4")[0].value != '') {
            hashString += document.getElementsByName("ppmpf_4")[0].value + '&';
        }
        if (document.getElementsByName("ppmpf_5")[0].value != '') {
            hashString += document.getElementsByName("ppmpf_5")[0].value + '&';
        }

        hashString = hashString.slice(0, -1);
        document.getElementById("hashValuesString").value = hashString;
    }
    $(document).ready(function(){
        submitForm();
    });
</script>