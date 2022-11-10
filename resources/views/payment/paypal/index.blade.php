<!DOCTYPE html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Ensures optimal rendering on mobile devices. -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" /> <!-- Optimal Internet Explorer compatibility -->
</head>

<body>

<input id="amount" type="hidden" value="{{$amount}}">
<input id="currency" type="hidden" value="{{$currency}}">
<input id="client_id" type="hidden" value="{{$client_id}}">
<input id="order_id" type="hidden" value="{{$order_id}}">

<script>
    let order_id =  document.getElementById('order_id').value;
    let currency =  document.getElementById('currency').value;
    let client_id =  document.getElementById('client_id').value;
    let src="https://www.paypal.com/sdk/js?client-id="+client_id+"&currency="+currency;
    document.write("<script type='text/javascript' data-order-id='"+ order_id + "' src='"+ src + "'><\/script>");// Required. Replace SB_CLIENT_ID with your sandbox client ID.
</script>

<div style="text-align: center; padding-bottom: 20px;">Amount to pay: <b>{{$amount}} {{$currency}}</b><br>Please choose a payment method below:</div>
<div id="paypal-button-container" style="text-align: center;"></div>

<div style="text-align: center; padding-top: 20px;">
    <b>Note:-</b> Please <b>do not</b> press back, otherwise it would cancel the transaction.<br>

</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>
<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            // This function sets up the details of the transaction, including the amount and line item details.
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: document.getElementById('amount').value
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            var token = $('meta[name="csrf-token"]').attr('content');
            // This function captures the funds from the transaction.
            return actions.order.capture().then(function(details) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    type: "POST",
                    data: {
                        order_id : "{{$order_id}}",
                        ref_id : details.id,
                        status: details.status,
                    },
                    url: "{{$notify_url}}",
                });
                // This function shows a transaction success message to your buyer.
                console.log('Transaction completed by ' + details.payer.name.given_name);
                window.location.href = "{{$success_url}}";
            });
        },
        onCancel: function(data) {
            console.log(data);
            window.location.href = "{{$fail_url}}";
        }
    }).render('#paypal-button-container');
    //This function displays Smart Payment Buttons on your web page.
</script>
</body>




