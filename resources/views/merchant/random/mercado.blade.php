
<!DOCTYPE html>
<html>
<head>
  <title>Card Payment</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  {{--<link rel="stylesheet" type="text/css" href="css/index.css">--}}
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://sdk.mercadopago.com/js/v2"></script>
  {{--<script type="text/javascript" src="js/index.js" defer></script>--}}

  <style>
    body {
      background-color: #fff;
      width: auto;
      height: auto;
      font-family: "Helvetica Neue",Helvetica,sans-serif;
      color: RGBA(0,0,0,0.8);
    }

    main {
      margin: 4px 0 0px 0;
      background-color: #f6f6f6;
      min-height: 90%;
      padding-bottom: 100px;
    }

    .hidden {
      display: none
    }

    /* Shopping Cart Section - Start */
    .shopping-cart {
      padding-bottom: 10px;
      overflow:hidden;
      transition: max-height 5s ease-in-out;
    }

    .shopping-cart.hide {
      max-height: 0;
      pointer-events: none;
    }

    .shopping-cart .content {
      box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.075);
      background-color: white;
    }

    .shopping-cart .block-heading {
      padding-top: 40px;
      margin-bottom: 30px;
      text-align: center;
    }

    .shopping-cart .block-heading p {
      text-align: center;
      max-width: 600px;
      margin: auto;
      color: RGBA(0,0,0,0.45);
    }

    .shopping-cart .block-heading h1,
    .shopping-cart .block-heading h2,
    .shopping-cart .block-heading h3 {
      margin-bottom: 1.2rem;
      color: #009EE3;
    }

    .shopping-cart .items {
      margin: auto;
    }

    .shopping-cart .items .product {
      margin-bottom: 0px;
      padding-top: 20px;
      padding-bottom: 20px;
    }

    .shopping-cart .items .product .info {
      padding-top: 0px;
      text-align: left;
    }

    .shopping-cart .items .product .info .product-details .product-detail {
      padding-top: 40px;
      padding-left: 40px;
    }

    .shopping-cart .items .product .info .product-details h5 {
      color: #009EE3;
      font-size: 19px;
    }

    .shopping-cart .items .product .info .product-details .product-info {
      font-size: 15px;
      margin-top: 15px;
    }

    .shopping-cart .items .product .info .product-details label {
      width: 50px;
      color: #009EE3;
      font-size: 19px;
    }

    .shopping-cart .items .product .info .product-details input {
      width: 80px;
    }

    .shopping-cart .items .product .info .price {
      margin-top: 15px;
      font-weight: bold;
      font-size: 22px;
    }

    .shopping-cart .summary {
      border-top: 2px solid #C6E9FA;
      background-color: #f7fbff;
      height: 100%;
      padding: 30px;
    }

    .shopping-cart .summary h3 {
      text-align: center;
      font-size: 1.3em;
      font-weight: 400;
      padding-top: 20px;
      padding-bottom: 20px;
    }

    .shopping-cart .summary .summary-item:not(:last-of-type) {
      padding-bottom: 10px;
      padding-top: 10px;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .shopping-cart .summary .text {
      font-size: 1em;
      font-weight: 400;
    }

    .shopping-cart .summary .price {
      font-size: 1em;
      float: right;
    }

    .shopping-cart .summary button {
      margin-top: 20px;
      background-color: #009EE3;
    }

    @media (min-width: 768px) {

      .shopping-cart .items .product .info .product-details .product-detail {
        padding-top: 40px;
        padding-left: 40px;
      }

      .shopping-cart .items .product .info .price {
        font-weight: 500;
        font-size: 22px;
        top: 17px;
      }

      .shopping-cart .items .product .info .quantity {
        text-align: center;
      }

      .shopping-cart .items .product .info .quantity .quantity-input {
        padding: 4px 10px;
        text-align: center;
      }
    }

    /* Card Payment Section - Start */
    /*.container__payment {*/
    /*display: none;*/
    /*}*/

    .payment-form {
      padding-bottom: 10px;
      margin-right: 15px;
      margin-left: 15px;
      font-family: "Helvetica Neue",Helvetica,sans-serif;
    }

    .payment-form.dark {
      background-color: #f6f6f6;
    }

    .payment-form .content {
      box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.075);
      background-color: white;
    }

    .payment-form .block-heading {
      padding-top: 40px;
      margin-bottom: 30px;
      text-align: center;
    }

    .payment-form .block-heading p {
      text-align: center;
      max-width: 420px;
      margin: auto;
      color: RGBA(0,0,0,0.45);
    }

    .payment-form .block-heading h1,
    .payment-form .block-heading h2,
    .payment-form .block-heading h3 {
      margin-bottom: 1.2rem;
      color: #009EE3;
    }

    .payment-form .form-payment {
      border-top: 2px solid #C6E9FA;
      box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.075);
      background-color: #ffffff;
      padding: 0;
      max-width: 600px;
      margin: auto;
    }

    .payment-form .title {
      font-size: 1em;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      margin-bottom: 0.8em;
      font-weight: 400;
      padding-bottom: 8px;
    }

    .payment-form .products {
      background-color: #f7fbff;
      padding: 25px;
    }

    .payment-form .products .item {
      margin-bottom: 1em;
    }

    .payment-form .products .item-name {
      font-weight: 500;
      font-size: 0.9em;
    }

    .payment-form .products .item-description {
      font-size: 0.8em;
      opacity: 0.6;
    }

    .payment-form .products .item p {
      margin-bottom: 0.2em;
    }

    .payment-form .products .price {
      float: right;
      font-weight: 500;
      font-size: 0.9em;
    }

    .payment-form .products .total {
      border-top: 1px solid rgba(0, 0, 0, 0.1);
      margin-top: 10px;
      padding-top: 19px;
      font-weight: 500;
      line-height: 1;
    }

    .payment-form .payment-details {
      padding: 25px 25px 15px;
      height: 100%;
    }

    .payment-form .payment-details label {
      font-size: 12px;
      font-weight: 600;
      margin-bottom: 15px;
      color: #8C8C8C;
      text-transform: uppercase;
    }

    .payment-form .payment-details button {
      margin-top: 0.6em;
      padding: 12px 0;
      font-weight: 500;
      background-color: #009EE3;
      margin-bottom: 10px;
    }

    .payment-form .date-separator {
      margin-left: 10px;
      margin-right: 10px;
      margin-top: 5px;
    }

    /*.payment-form a, .payment-form a:not([href]) {*/
    /*  margin: 0;*/
    /*  padding: 0;*/
    /*  font-size: 13px;*/
    /*  color: #009ee3;*/
    /*  cursor:pointer;*/
    /*}*/

    /*.payment-form a:not([href]):hover{*/
    /*  color: #3483FA;*/
    /*  cursor:pointer;*/
    /*}*/

    #loading-message {
      display: none;
      text-align: center;
      font-weight: 700;
    }

    footer {
      padding: 2% 10% 6% 10%;
      margin: 0 auto;
      position: relative;
    }

    #horizontal_logo {
      width: 150px;
      margin: 0;
    }

    footer p a {
      color: #009ee3;
      text-decoration: none;
    }

    footer p a:hover {
      color: #3483FA;
      text-decoration: none;
    }

    @media (min-width: 576px) {
      .payment-form .title {
        font-size: 1.2em;
      }

      .payment-form .products {
        padding: 40px;
      }

      .payment-form .products .item-name {
        font-size: 1em;
      }

      .payment-form .products .price {
        font-size: 1em;
      }

      .payment-form .payment-details {
        padding: 40px 40px 30px;
      }

      .payment-form .payment-details button {
        margin-top: 1em;
        margin-bottom: 15px;
      }

      .footer_logo {
        margin: 0 0 0 0;
        width: 20%;
        text-align: left;
        position: absolute;
      }

      .footer_text {
        margin: 0 0 0 65%;
        width: 200px;
        text-align: left;
        position: absolute
      }

      footer p {
        padding: 1px;
        font-size: 13px;
        color: RGBA(0,0,0,0.45);
        margin-bottom: 0;
      }
    }

    @media (max-width: 576px) {
      footer {
        padding: 5% 1% 15% 1%;
        height: 55px;
      }

      footer p {
        padding: 1px;
        font-size: 11px;
        margin-bottom: 0;
      }
      .footer_text {
        margin: 0 0 0 45%;
        width: 180px;
        position: absolute
      }

      .footer_logo {
        margin: 0 0 0 0;
        position: absolute;
      }

    }

    /* Payment Result Section - Start */
    .container__result {
      display: none;
    }

  </style>
</head>

<body>
<main>
  <!-- Hidden input to store your integration public key -->
  {{--<input type="hidden" id="mercado-pago-public-key" value="{{ 'APP_USR-8e3698bd-9334-44ff-94ca-fced8836dbd4' }}">--}}
  <input type="hidden" id="mercado-pago-public-key" value="{{ $return_data['public_key'] }}">
  <!-- Payment -->
  <section class="payment-form dark">
    <div class="container__payment">
      <div class="form-payment">
        <div class="products">
          <img id="horizontal_logo" src="{{url('basic-images/horizontal_logo.png')}}">
          <a href="{{route('process-payment-fail')}}">
          <button id="close" type="button" class="btn btn-primary" style="float:right">Close</button>
          </a>
        </div>
        <div class="all_errors">

        </div>
        <div class="payment-details">
          <form id="form-checkout">
            <h3 class="title">Buyer Details</h3>
            <div class="row">
              <div class="form-group col">
                <input id="form-checkout__cardholderEmail" value="{{$return_data['email']}}" name="cardholderEmail"
                       type="email"
                       class="form-control" required/>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-sm-5">
                <select id="form-checkout__identificationType" name="identificationType" class="form-control" required></select>
              </div>
              <div class="form-group col-sm-7">
                <input id="form-checkout__identificationNumber" name="docNumber" type="text" class="form-control" required/>
              </div>
            </div>
            <br>
            <h3 class="title">Card Details</h3>
            <div class="row">
              <div class="form-group col-sm-8">
                <input id="form-checkout__cardholderName" name="cardholderName" value="{{$return_data['name']}}"
                       type="text" class="form-control"
                       required/>
              </div>
              <div class="form-group col-sm-4">
                <div class="input-group expiration-date">
                  <input id="form-checkout__cardExpirationMonth" name="cardExpirationMonth" type="text" class="form-control" required/>
                  <span class="date-separator">/</span>
                  <input id="form-checkout__cardExpirationYear" name="cardExpirationYear" type="text" class="form-control" required/>
                </div>
              </div>
              <div class="form-group col-sm-8">
                <input id="form-checkout__cardNumber" name="cardNumber" type="text" class="form-control" required/>
              </div>
              <div class="form-group col-sm-4">
                <input id="form-checkout__securityCode" name="securityCode" type="text" class="form-control" required/>
              </div>
              <div id="issuerInput" class="form-group col-sm-12">
                <select id="form-checkout__issuer" name="issuer" class="form-control" required></select>
              </div>
              <div class="form-group col-sm-12">
                <select id="form-checkout__installments" name="installments" type="text" class="form-control" required></select>
              </div>
              {{--<div class="form-group col-sm-12">--}}
                {{--<input id="form-checkout__amount" name="amount"  value="{{$return_data['amount']}}" type="text" class="form-control" required>--}}
              {{--</div>--}}
              <div class="form-group col-sm-12">
                <input type="hidden" id="amount" />
                <input type="hidden" id="description"  value="{{$return_data['unique_no']}}"/>
                <br>
                <button id="form-checkout__submit" type="submit" class="btn btn-primary btn-block">Pay</button>
                <br>
                <p id="loading-message">Loading, please wait...</p>
                <br>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <!-- Result -->
  <section class="shopping-cart dark">
    <div class="container container__result">
        <div class="form-payment">
      <div class="content">
          <div class="block-heading">
        <h2>Payment Result
        <a href="{{route('process-payment-fail')}}">
                  <button id="close-result" type="button" class="btn btn-primary" style="margin-left:50px;">Close</button>
              </a>
        </h2>
      </div>
        <div class="row">
          <div class="col-md-12 col-lg-12">
            <div class="items product info product-details">
              <div class="row justify-content-md-center">
                <div class="col-md-4 product-detail">
                  <div class="product-info">
                    <br>
                    <p><b>ID: </b><span id="payment-id"></span></p>
                    <p><b>Status: </b><span id="payment-status"></span></p>
                    <p><b>Detail: </b><span id="payment-detail"></span></p>
                    <br>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </section>
</main>
</body>
</html>

<script>
    const publicKey = document.getElementById("mercado-pago-public-key").value;
    const mercadopago = new MercadoPago(publicKey);
    loadCardForm();
    function loadCardForm() {
        const productCost = "{{$return_data['amount']}}";
        const productDescription = document.getElementById("description").value;
        const cardForm = mercadopago.cardForm({
            amount: productCost,
            autoMount: true,
            form: {
                id: "form-checkout",
                cardholderName: {
                    id: "form-checkout__cardholderName",
                    placeholder: "Holder name",
                },
                cardholderEmail: {
                    id: "form-checkout__cardholderEmail",
                    placeholder: "E-mail",
                },
                cardNumber: {
                    id: "form-checkout__cardNumber",
                    placeholder: "Card number",
                },
                cardExpirationMonth: {
                    id: "form-checkout__cardExpirationMonth",
                    placeholder: "MM",
                },
                cardExpirationYear: {
                    id: "form-checkout__cardExpirationYear",
                    placeholder: "YY",
                },
                securityCode: {
                    id: "form-checkout__securityCode",
                    placeholder: "Security code",
                },
                installments: {
                    id: "form-checkout__installments",
                    placeholder: "Installments",
                },
                identificationType: {
                    id: "form-checkout__identificationType",
                },
                identificationNumber: {
                    id: "form-checkout__identificationNumber",
                    placeholder: "Identification number",
                },
                issuer: {
                    id: "form-checkout__issuer",
                    placeholder: "Issuer",
                },
            },
            callbacks: {
                onFormMounted: error => {
                    if (error) return console.warn('Form Mounted handling error: ', error)
                    console.log('Form mounted')
                },
                onFormUnmounted: error => {
                    if (error) return console.warn('Form Unmounted handling error: ', error)
                    console.log('Form unmounted')
                },
                onIdentificationTypesReceived: (error, identificationTypes) => {
                    if (error) return console.warn('identificationTypes handling error: ', error)
                    console.log('Identification types available: ', identificationTypes)
                },
                onPaymentMethodsReceived: (error, paymentMethods) => {
                    if (error) return console.warn('paymentMethods handling error: ', error)
                    console.log('Payment Methods available: ', paymentMethods)
                },
                onIssuersReceived: (error, issuers) => {
                    if (error) return console.warn('issuers handling error: ', error)
                    console.log('Issuers available: ', issuers)
                },
                onInstallmentsReceived: (error, installments) => {
                    if (error) return console.warn('installments handling error: ', error)
                    console.log('Installments available: ', installments)
                },
                onCardTokenReceived: (error, token) => {
                    if (error) return console.warn('Token handling error: ', error)
                    console.log('Token available: ', token)
                },
                // onFormMounted: error => {
                //     if (error)
                //         return console.warn("Form Mounted handling error: ", error);
                //     console.log("Form mounted");
                // },
                onSubmit: event => {
                    event.preventDefault();
                    document.getElementById("loading-message").style.display = "block";

                    const {
                        paymentMethodId,
                        issuerId,
                        cardholderEmail: email,
                        amount,
                        token,
                        installments,
                        identificationNumber,
                        identificationType,
                    } = cardForm.getCardFormData();

                    // "https://trem.app.br/ms-trem/public/api/process_payment"
                    //route('process-process-payment')
                    fetch("{{$return_data['response_url']}}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            token,
                            issuerId,
                            paymentMethodId,
                            transactionAmount: Number(amount),
                            installments: Number(installments),
                            description: productDescription,
                            payer: {
                                email,
                                identification: {
                                    type: identificationType,
                                    number: identificationNumber,
                                },
                            },
                        }),
                    })
                        .then(response => {
                            console.log(response);
                            return response.json();
                        })
                        .then(result => {
                            document.getElementById("payment-id").innerText = result.id;
                            document.getElementById("payment-status").innerText = result.status;
                            document.getElementById("payment-detail").innerText = result.detail;
                            $('.container__payment').fadeOut(500);
                            setTimeout(() => { $('.container__result').show(500).fadeIn(); }, 500);
                        })
                        .catch(error => {
                            alert("Unexpected error\n"+JSON.stringify(error));
                        });
                },
                onFetching: (resource) => {
                    console.log("Fetching resource: ", resource);
                    const payButton = document.getElementById("form-checkout__submit");
                    payButton.setAttribute('disabled', true);
                    return () => {
                        payButton.removeAttribute("disabled");
                    };
                },
            },
        });
    };

    // // Handle transitions
    // document.getElementById('checkout-btn').addEventListener('click', function(){
    //     $('.container__cart').fadeOut(500);
    //     setTimeout(() => {
    //         loadCardForm();
    //         $('.container__payment').show(500).fadeIn();
    //     }, 500);
    // });

    // document.getElementById('go-back').addEventListener('click', function(){
    //     $('.container__payment').fadeOut(500);
    //     setTimeout(() => { $('.container__cart').show(500).fadeIn(); }, 500);
    // });

    // Handle price update
    // function updatePrice(){
    //     let quantity = document.getElementById('quantity').value;
    //     let unitPrice = document.getElementById('unit-price').innerText;
    //     let amount = parseInt(unitPrice) * parseInt(quantity);
    //
    //     document.getElementById('cart-total').innerText = '$ ' + amount;
    //     document.getElementById('summary-price').innerText = '$ ' + unitPrice;
    //     document.getElementById('summary-quantity').innerText = quantity;
    //     document.getElementById('summary-total').innerText = '$ ' + amount;
    //     document.getElementById('amount').value = amount;
    // };

    // document.getElementById('quantity').addEventListener('change', updatePrice);
    // updatePrice();
</script>