<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: *');
//header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Credentials, Access-Control-Allow-Origin, Access-Control-Allow-Methods, Access-Control-Allow-Headers, aliasName, publicKey, secretKey, locale');

//sms testing route
Route::post('/checksms','SmsGateways\SimpleSms@chkms');

// Wave business payment gateway webhook
Route::post('/wave-business-handler', 'PaymentMethods\WaveBusiness\WaveBusiness@handlerCallBack');
Route::post('/wave-business-request/{status}', 'PaymentMethods\WaveBusiness\WaveBusiness@request');


//telebirr pay routes
Route::any('/teliberrNotify/{merchant_id?}','PaymentMethods\TelebirrPay\TelebirrPayController@teliberrNotify')->name('teliberrNotify');
Route::any('/teliberrCallback/{outTradeNo}','PaymentMethods\TelebirrPay\TelebirrPayController@teliberrCallback')->name('teliberrCallback');
Route::get('/telebirr/success/{msg?}','PaymentMethods\TelebirrPay\TelebirrPayController@teliberrSuccess')->name('teliberr.success');
Route::get('/telebirr/failed/{msg?}','PaymentMethods\TelebirrPay\TelebirrPayController@teliberrFailed')->name('teliberr.failed');


//for App Your Service
// Route::post('/getUser','Api\CommonController@getSubAdmin');
// Route::post('/getCustomer','Api\CommonController@getUsers');
// Route::post('/getRider','Api\CommonController@getDriver');
// Route::post('/getStoresList','Api\CommonController@storeList');
// Route::post('/getStoreBooking','Api\CommonController@getStoreBookings');

//MpesaB2C callback
Route::any('/mpesaCallback','PaymentMethods\RandomPaymentController@MpesaB2CRequestCallback')->name('mpesa.b2c.callback');

//MomoPay callback Routes
Route::post('/momo/callback','PaymentMethods\RandomPaymentController@MOMOCallback')->name('momo.callback');
Route::post('/wasl/driver-vehicle-regi','Api\WaslController@driverVehicleRegister');

//Buttler Routes
Route::any('user/mpessapayment_confirmation', 'PaymentMethods\RandomPaymentController@MpessaCallBack');
Route::post('/{alias_name}/api/trips', 'Api\ButtlerController@index');
Route::get('/{alias_name}/api/trips/{tripId}', 'Api\ButtlerController@tripstatus');
Route::delete('/{alias_name}/api/trips/{tripId}', 'Api\ButtlerController@trips_delete');

Route::get('callbackkorba', 'PaymentMethods\RandomPaymentController@callbackkorba');
Route::any('/bancardCallback', 'PaymentMethods\RandomPaymentController@bancardCallback')->name('bancardCallback');
Route::post('redirectPeach', 'PaymentMethods\RandomPaymentController@redirectPeach')->name('redirectPeach');
Route::get('/shopper/{id}', 'PaymentMethods\RandomPaymentController@shopper')->name('shopper');
Route::post('/beyonicCallback', 'PaymentMethods\RandomPaymentController@beyonicCallback')->name('beyonicCallback');
//common api's for all apps
//Route::post('/checkBookingStatus', 'Api\BookingController@CheckBookingStatus');
Route::post('/check-booking-status', 'Api\BookingController@checkBookingStatus');
Route::post('send-mail', 'Api\EmailController@test');
Route::post('/estimate', 'Helper\ExtraCharges@NewnightchargeEstimate');
Route::get('/copypaste', 'Api\UserController@CopySignUp');
Route::post('/bookingStatus', 'Api\BookingController@BookingStatus');
Route::post('/conekta', 'Api\FoodController@conekta');
Route::get('/time', function () {
    return response()->json(['result' => '1', 'message' => 'Time Stamp', 'time' => time()]);
});

Route::post('/PayHere/AddCardNotification', ['as' => 'PayHere.AddCardNotification', 'uses' => 'PaymentMethods\PayHere\PayHereController@AddCardCallBack']);

Route::get('/union-bank/auth_code/redirect', ['as' => 'union-bank.auth_code.redirect', 'uses' => 'PaymentMethods\UnionBank\UnionBankController@AuthCodeRedirect']);

Route::post('/proxypay/callback', ['as' => 'proxy_pay.callback', 'uses' => 'PaymentMethods\ProxyPay\ProxyPayController@resultWebhook']);

Route::post('/cash_free/redirect', ['as' => 'cash_free.redirect', 'uses' => 'PaymentMethods\CashFree\CashFreeController@Redirect']);
Route::post('/cash_free/notify', ['as' => 'cash_free.notify', 'uses' => 'PaymentMethods\CashFree\CashFreeController@Notify']);
Route::get('/cash_free/success', ['as' => 'cash_free.success', 'uses' => 'PaymentMethods\CashFree\CashFreeController@Success']);
Route::get('/cash_free/fail', ['as' => 'cash_free.fail', 'uses' => 'PaymentMethods\CashFree\CashFreeController@Fail']);

Route::get('/PagaditoPayment/{merchant_id}/{price}', ['as' => 'api.PagaditoPayment', 'uses' => 'PaymentMethods\RandomPaymentController@PagaditoPayment']);
Route::get('/PagaditoPayback/{value}/{ern_value}', ['as' => 'api.PagaditoPayback', 'uses' => 'PaymentMethods\RandomPaymentController@PagaditoPayback']);

// Syberpay payment gateway
Route::post('/SyberpayGetUrl', ['as' => 'api.syberpay.getUrl', 'uses' => 'PaymentMethods\RandomPaymentController@SyberpayGetUrl']);
Route::post('/SyberpayPaymentStatus', ['as' => 'api.syberpay.paymentstatus', 'uses' => 'PaymentMethods\RandomPaymentController@SyberpayPaymentStatus']);
Route::post('/SyberpayRedirect', ['as' => 'api.syberpay.redirectUrl', 'uses' => 'PaymentMethods\RandomPaymentController@SyberpayRedirectUrl']);

// imepay recording url
Route::post('/imepay/recording', ['as' => 'api.imepay.recording', 'uses' => 'PaymentMethods\RandomPaymentController@ImepayRecording']);

// UBpay payment gateway
Route::get('/ubpay/callback/{merchantRef}/{status}', ['as' => 'api.ubpay.callback', 'uses' => 'PaymentMethods\RandomPaymentController@UbpayCallback']);

Route::post('/driver/location/test', ['as' => 'api.driver-location', 'uses' => 'Api\DriverController@Location']);

// Razerpay Callback URL
Route::post('/razerpay/callback', 'PaymentMethods\RandomPaymentController@RazerpayCallback');

// 2C2P Redirect URL
Route::post('/2c2p/return/url', 'PaymentMethods\RandomPaymentController@TwoCTwoPReturn');
Route::post('/2c2p/token', 'PaymentMethods\RandomPaymentController@TwoCTwoPToken');

// paypal
Route::get('/paypal', 'PaymentMethods\RandomPaymentController@Paypal')->name('paypalview');
Route::get('/paypal/success', 'PaymentMethods\RandomPaymentController@paypal_success')->name('api.paypal_success_url');
Route::get('/paypal/fail', 'PaymentMethods\RandomPaymentController@paypal_fail')->name('api.paypal_fail_url');
Route::post('/paypal/notify', 'PaymentMethods\RandomPaymentController@paypal_notify')->name('api.paypal_notify_url');

//QuickPay
Route::get('/quickPay/checkout','PaymentMethods\RandomPaymentController@QuickPayCheckout')->name('quickpay.checkout');
// Route::post('/quickPay/checkOrder','PaymentMethods\RandomPaymentController@QuickPayReturn');
Route::get('/quickPay/approve','PaymentMethods\RandomPaymentController@QuickPayApprove')->name('quickpay.approve');
Route::get('/quickPay/cancel','PaymentMethods\RandomPaymentController@QuickPayCancel')->name('quickpay.cancel');
Route::get('/quickPay/decline','PaymentMethods\RandomPaymentController@QuickPayDecline')->name('quickpay.decline');

// Paygate Payhost payment gateway webview based url
//Step 1

Route::get('paygate/step1','PaymentMethods\Paygate\PaygateController@paygateStep1')->name('paygate-step1');
Route::get('paygate/step2', 'PaymentMethods\Paygate\PaygateController@paygateStep2')->name('paygate-step2');
Route::post('paygate/notify', 'PaymentMethods\Paygate\PaygateController@notify')->name('paygate-notify');
Route::any('paygate-success', 'PaymentMethods\Paygate\PaygateController@paygateStep3')->name('paygate-success');

// payphone response
Route::get('payphone-response', 'PaymentMethods\PayPhone\PayPhoneController@payPhoneResponse')->name('payphone-response');

// aamarpay
Route::any('aamarpay-success', 'PaymentMethods\AamarPay\AamarPayController@aamarPaysuccess')->name('aamarpay-success');
Route::any('aamarpay-fail', 'PaymentMethods\AamarPay\AamarPayController@aamarPayFail')->name('aamarpay-fail');
Route::any('aamarpay-cancel', 'PaymentMethods\AamarPay\AamarPayController@aamarPayCancel')->name('aamarpay-cancel');


// payfast
Route::any('payfast-success', 'PaymentMethods\RandomPaymentController@payFastSuccess')->name('payfast-success');
Route::any('payfast-fail', 'PaymentMethods\RandomPaymentController@payFastCancel')->name('payfast-fail');
//Route::get('payfast-redirect', ['as' => 'payfast-redirect', 'uses' => 'PaymentMethods\RandomController@payFastResponse'])->name('payfast-redirect');


//Route::get('/edahab-request', 'PaymentMethods\RandomPaymentController@edahabRequest')->name('edahab-request');
Route::get('/edahab-return', 'PaymentMethods\RandomPaymentController@edahabReturn')->name('edahab-return');


Route::get('/telebirr-request', 'PaymentMethods\RandomPaymentController@telebirrRequest')->name('telebirr-request');
Route::get('/telebirr-return', 'PaymentMethods\RandomPaymentController@telebirrReturn')->name('telebirr-return');
Route::get('/telebirr-notify', 'PaymentMethods\RandomPaymentController@telebirrNotify')->name('telebirr-notify');


//Route::get('/paybox-request', 'PaymentMethods\RandomPaymentController@payboxRequest')->name('paybox-request');
Route::get('/paybox-success', 'PaymentMethods\PayBox\PayBoxController@payboxSuccess')->name('paybox-success');
Route::get('/paybox-fail', 'PaymentMethods\PayBox\PayBoxController@payboxFail')->name('paybox-fail');
Route::get('/paybox-result', 'PaymentMethods\PayBox\PayBoxController@payboxResult')->name('paybox-result');



// mercado payment gateway
Route::get('/mercado/auth-code/response','PaymentMethods\Mercado\MercadoController@mercadoAuthCodeResponse')->name('mercado.code.response');
Route::post('/process_payment', 'PaymentMethods\Mercado\MercadoController@processPayment')->name('process-payment');
Route::get('mercado-webpage/{unique_no}/','PaymentMethods\Mercado\MercadoController@mercadoWebViewPage')->name('mercado-web-page');
Route::get('process-payment-success','PaymentMethods\Mercado\MercadoController@mercadoPageSuccess')->name('process-payment-success');
Route::get('process-payment-fail','PaymentMethods\Mercado\MercadoController@mercadoPageFail')->name('process-payment-fail');
Route::post('payment-notification','PaymentMethods\Mercado\MercadoController@cardPaymentNotification')->name('card-payment-notification');
Route::post('webhook-notification','PaymentMethods\Mercado\MercadoController@webhookNotification')->name('webhook-notification');
Route::get('mercado-webpage-split/{unique_no}/','PaymentMethods\Mercado\MercadoController@mercadoWebViewPageSplit')->name('mercado-web-page-split');
Route::post('/process_payment_split', 'PaymentMethods\Mercado\MercadoController@processPaymentSplit')->name('process-payment-split');


Route::any('/paygate-global-webhook','PaymentMethods\PaygateGlobal\PaygateGlobalController@webhook')->name('paygate-global-webhook');
// square webhook
Route::any('/square-webhook','PaymentMethods\Square\SquareController@webhook')->name('square-webhook');

// DPO Think payment
Route::any('/dpo-callback','PaymentMethods\DPO\DpoController@PaymentCallBack')->name('dpo-callback');
Route::any('/dpo-back','PaymentMethods\DPO\DpoController@back')->name('dpo-back');


// touch pay call back
Route::any('/touch-pay-callback','PaymentMethods\TouchPay\TouchPayController@touchPayCallback')->name('touch-pay.callback');



Route::get('paymentfail',function(){
    return 'failed';
});
Route::get('paymentcomplate', function () {
    return 'done';
});


//Route::get('paymentsuccess/{booking_id?}', 'PaymentController@returnResponse');
//Route::post('paymentsuccess/{booking_id?}', 'PaymentController@returnResponse');
//Route::post('payment/notify', 'PaymentController@notify');
//Route::get('managepayment/{paymentToken}', 'PaymentController@managepayment')->name('managecard');
//Route::get('deletemethod/{card_id}', 'PaymentController@deleteCard')->name('delete.method');
//
//Route::post('paymentUsingCard/{user_id}', 'PaymentController@PaymentUsingWeb')->name('payusingvault');
//Route::post('savecard/{user_id}','PaymentController@saveCard')->name('saveCard');
//Route::post('deletecard/{user_id}','PaymentController@deleteCard')->name('deleteCard');

Route::get('get-records-for-external', 'Api\CommonController@getRecordsForExternal');

/*map load api */
Route::group(['middleware' => ['merchant']], function () {
    Route::post('/map-load', 'Helper\GoogleController@mapLoad');
    Route::post('/static-map-load', 'Helper\GoogleController@staticMapLoad');
    Route::post('/country-list', 'Helper\CommonController@countryList');
    Route::post('/payment-gateway-list',['as' => 'api.merchant.payment-gateway', 'uses' => 'Api\CommonController@getPaymentGateway']);

    // get handyman bookings of merchant
    Route::post('/booking-list', 'Api\HandymanOrderController@bookingList');
});

// SenangPay URL
Route::get('/senangpay/callback', ['as' => 'user.api.senangpay-callback', 'uses' => 'PaymentMethods\RandomPaymentController@SenangPayCallback']);
Route::get('/senangpay/return', ['as' => 'user.api.senangpay-return', 'uses' => 'PaymentMethods\RandomPaymentController@SenangPayReturnUrl']);

// For Sahal Taxi
Route::post('/save-guest-user', ['as' => 'user.api.save-guest-user-info', 'uses' => 'Api\CommonController@SaveGuestUserInfo']);

// Pesapal Callback URL
Route::post('/pesapal/callback', 'PaymentMethods\RandomPaymentController@PesapalCallback');
Route::any('/flo-ozoh/notification', 'PaymentMethods\RandomPaymentController@ozowNotification')->name('api.ozo-payment-notification');
Route::any('/flo-ozoh/success', 'PaymentMethods\RandomPaymentController@ozowSuccess')->name('api.ozo-payment-success');
Route::any('/payu/notification', 'PaymentMethods\RandomPaymentController@payuNotification')->name('api.payu-notification');
Route::any('/maxi-cash/success', 'PaymentMethods\RandomPaymentController@maxiCashSuccess')->name('api.maxi-cash-success');
Route::any('/maxi-cash/cancel', 'PaymentMethods\RandomPaymentController@maxiCashCancel')->name('api.maxi-cash-cancel');
Route::any('/maxi-cash/failure', 'PaymentMethods\RandomPaymentController@maxiCashFailure')->name('api.maxi-cash-failure');
Route::any('/maxi-cash/notification', 'PaymentMethods\RandomPaymentController@maxiCashNotification')->name('api.maxi-cash-notification');

// tripay payment gateway routes
Route::post('/tripay/callback', 'PaymentMethods\RandomPaymentController@TriPayCheckTransactionCallback');
Route::post('/tripay/redirect', 'PaymentMethods\RandomPaymentController@TriPayCheckTransactionRedirect');

// bookeey payment gateway routes
Route::get('/bookeey/fail','PaymentMethods\RandomPaymentController@BookeeyFail')->name('BookeeyFail');
Route::get('/bookeey/success','PaymentMethods\RandomPaymentController@BookeeySuccess')->name('BookeeySuccess');
//jazzcash payment gateway routes
Route::get('jazzcash/webview','PaymentMethods\RandomPaymentController@JazzCashWebView')->name('jazzcash.webview');
Route::post('jazzcash/return','PaymentMethods\RandomPaymentController@JazzCashReturn')->name('jazzcash.return');
Route::get('jazzcash/success','PaymentMethods\RandomPaymentController@JazzCashSuccess')->name('jazzcash.success');
Route::get('jazzcash/fail','PaymentMethods\RandomPaymentController@JazzCashFail')->name('jazzcash.fail');




Route::get('twilio-token','Helper\SmsController@twilioToken')->name('twilio.token');

// api's for user app
Route::prefix('user')->group(function () {
    Route::any('/verifyFlutterwaveTransaction', ['as' => 'api.verifyFlutterwaveTransaction', 'uses' => 'Api\CardController@verifyFlutterwaveTransaction']);
    // api request either login token or merchant public+ secret key
    Route::group(['middleware' => ['merchant']], function () {
        //website
        Route::post('/website/homeScreen', 'Api\WebsiteController@HomeScreen');
        Route::post('/website/service', 'Api\WebsiteController@cars');
        Route::post('/carsWithoutLogin', 'Api\HomeController@cars');
//        Route::post('/checkEstimate', 'Api\BookingController@estimate');
        Route::post('/AddWalletMoneyCoupon', 'Api\CommonController@AddWalletMoneyCoupon');
        Route::post('/configuration', ['as' => 'api.user.configuration', 'uses' => 'Api\UserController@Configuration']);
        Route::post('/countryList', ['as' => 'api.user.countryList', 'uses' => 'Api\CommonController@CountryList']);
        Route::post('/otp', ['as' => 'api.user-otp', 'uses' => 'Api\UserController@Otp']);
        Route::post('/cms/pages', 'Api\CommonController@UserCmsPage');
        //Account
        Route::post('/getnetworkcode', 'Api\CommonController@getNetworkCode');

        // Login OLD Version
        Route::post('/demoUser', ['as' => 'user.api.demoUser', 'uses' => 'Account\UserController@DemoUser']);
        Route::post('/login', ['as' => 'api.user-login', 'uses' => 'Account\UserController@Login']);
        Route::post('/login/otp', ['as' => 'api.user-login-otp', 'uses' => 'Account\UserController@loginOtp']);
        Route::post('/signup', ['as' => 'api.user.signup', 'uses' => 'Account\UserController@SignUp']);
        Route::post('/socialsingup', ['as' => 'api.user-socialsingup', 'uses' => 'Account\UserController@SocialSignup']);
        Route::post('/socialsignin', ['as' => 'api.user-socialsignin', 'uses' => 'Account\UserController@SocialSign']);

        // Login New Version
        Route::post('/demo-onboard', ['as' => 'user.api.demoUser', 'uses' => 'Account\UserController@DemoUser']);
        Route::post('/on-board', ['as' => 'api.user-login', 'uses' => 'Account\UserController@Login']);
        Route::post('/on-board/otp', ['as' => 'api.user-login-otp', 'uses' => 'Account\UserController@loginOtp']);
        Route::post('/normal-reg', ['as' => 'api.user.signup', 'uses' => 'Account\UserController@SignUp']);
        Route::post('/social-reg', ['as' => 'api.user-socialsingup', 'uses' => 'Account\UserController@SocialSignup']);
        Route::post('/social-on-board', ['as' => 'api.user-socialsignin', 'uses' => 'Account\UserController@SocialSign']);

        Route::post('/validate-data','Api\UserController@SignupValidation');

        Route::post('/forgotpassword', ['as' => 'api.user.password', 'uses' => 'Account\UserController@ForgotPassword']);
        Route::post('/details', 'Account\UserController@Details');
        Route::post('/edit-profile', 'Account\UserController@EditProfile');

        Route::post('/getString', ['as' => 'api.getLatestString', 'uses' => 'Api\StringLanguageController@getLatestString']);
        Route::post('/korbapayment', 'PaymentMethods\RandomPaymentController@korbaWeb')->name('korbapayment');
        
        Route::post('/face-recognition', ['as' => 'api.user.face-recognition', 'uses' => 'Api\CommonController@faceRecognition']);
    });

    // api request with login token
    Route::group(['middleware' => ['auth:api', 'validuser']], function () {
        // user home screen for vehicle based segment
        //Route::post('/cars', ['as' => 'api.cars', 'uses' => 'Api\HomeController@index']);
        Route::post('/cars', ['as' => 'api.cars', 'uses' => 'Api\HomeController@userHomeScreen']);
        Route::post('/checkout', ['as' => 'api.checkout', 'uses' => 'Api\BookingController@checkout']);
        Route::post('/outstation-details', ['as' => 'api.outstation', 'uses' => 'Services\OutstationController@outstationDetail']);
        Route::post('/checkout-payment', ['as' => 'api.booking-payment', 'uses' => 'Api\BookingController@checkoutPayment']);
        Route::post('/changePaymentOption', 'Api\BookingController@changePaymentDuringRide');

        Route::post('/confirm', ['as' => 'api.booking-confirm', 'uses' => 'Api\BookingController@confirmBooking']);
        Route::post('/booking/details', ['as' => 'api.user.booking.details', 'uses' => 'Api\BookingController@bookingDetails']);
        //        Route::post('/PagaditoPayment',['as' => 'api.PagaditoPayment', 'uses' => 'Api\RandomPaymentController@PagaditoPayment']);

//        Route::post('/delivery/homescreen', 'Delivery\ApiController@HomeScreen');
//        Route::post('/delivery/vehicleType', 'Delivery\ApiController@VehicleType');
//        Route::post('/delivery/checkout', 'Delivery\ApiController@Checkout');

        //Delivery Routes
        Route::post('/delivery/checkout', 'Api\DeliveryController@Checkout');
        Route::post('/delivery/checkout-details', 'Api\DeliveryController@CheckoutDetails');
        Route::post('/delivery/checkout/store-drop-details', 'Api\DeliveryController@storeCheckoutDetails');
        Route::post('/confirm/delivery', ['as' => 'api.booking-confirm-delivery', 'uses' => 'Api\DeliveryController@Confirm']);


        Route::post('/increaseRideRequestArea', ['as' => 'api.increaseRideRequestArea', 'uses' => 'Api\BookingController@getNextRadiusDriver']);

        // sos routes
        Route::post('/sos', 'Api\SosController@SosUser');
        Route::post('/sos/create', 'Api\SosController@addSosUser');
        Route::post('/sos/distory', 'Api\SosController@delete');
        //Account module
        Route::post('/UserDetail', 'Account\UserController@UserDetail');
        Route::post('/edit-profile', ['as' => 'api.edit-profile', 'uses' => 'Account\UserController@EditProfile']);
        Route::post('/add-tip', 'Api\BookingController@addTip');

        Route::post('/logout', ['as' => 'api.user-logout', 'uses' => 'Account\UserController@Logout']);
        Route::post('/out-board', ['as' => 'api.user-logout', 'uses' => 'Account\UserController@Logout']);

        Route::post('/change-password', ['as' => 'api.change-password', 'uses' => 'Account\UserController@ChangePassword']);
        Route::post('/userDocList', 'Api\UserController@getCountryDocuments');
        Route::post('/userDocSave', 'Api\UserController@addDocument');
        Route::post('/favouritedrivers', 'Api\BookingController@getFavouriteDrivers');
        Route::post('/updateTerms', 'Api\UserController@UserTermUpdate');
        Route::post('/paytmchecksum', 'Api\CardController@PaytmChecksum');
        Route::post('/prepareCheckout', 'Api\CardController@prepareCheckout');
        Route::get('/paymentStatus', 'Api\CardController@paymentStatus');
        Route::get('/notify', 'Api\CardController@prepareCheckout')->name('notify');
        Route::post('/IugoPayment', 'Api\CardController@IugoPayment');
        Route::post('/creatPrefId', 'Api\CardController@prefIdMercado');
        Route::post('/flutterwavePaymentRequest', 'Api\CardController@flutterwavePaymentRequest');
        Route::post('/YoPaymentRequest', 'Api\CardController@YoPaymentRequest');

        Route::post('BancardCheckout', 'PaymentMethods\RandomPaymentController@BancardCheckout');
        Route::get('/redirectBancard', 'PaymentMethods\RandomPaymentController@redirectBancard')->name('redirectBancard');
        //Route::get('/redirectBancard', 'PaymentMethods\RandomPaymentController@redirectBancard');
        Route::post('/createTransDPO', 'PaymentMethods\RandomPaymentController@createTransDPO');
        Route::post('/mobileMoneyDPO', 'PaymentMethods\RandomPaymentController@DpoMobileMoney');
        Route::post('/beyonicMobileMoney', 'PaymentMethods\RandomPaymentController@beyonicMobileMoney');
        Route::post('/verifymobileMoneyDPO', 'PaymentMethods\RandomPaymentController@verifyMobileMoneyDPO');
        //user cancel reason
        Route::post('/cancel-reasons', ['as' => 'user.api.cancel.reason', 'uses' => 'Api\CommonController@cancelReason']);
        Route::post('/receipt', ['as' => 'user.api.viewDoneRideInfo', 'uses' => 'Api\BookingController@UserReceipt']);
        Route::post('/save-card', ['as' => 'user.api.save-card', 'uses' => 'Api\CardController@SaveCard']);

        Route::post('/senangpay/tokenization', ['as' => 'user.api.senangpay-token', 'uses' => 'Api\CardController@SenangPayToken']);
        Route::post('/senangpay/record/transaction', ['as' => 'user.api.senangpay-record', 'uses' => 'PaymentMethods\RandomPaymentController@SenangPayRecordTransaction']);

//        Route::post('/make_payment', 'Api\CardController@pay');
        Route::post('/cards', ['as' => 'api.user.cards', 'uses' => 'Api\CardController@Cards']);
        Route::post('/card/delete', ['as' => 'api.user.delete.card', 'uses' => 'Api\CardController@DeleteCard']);
        Route::post('/card/make-payment', ['as' => 'api.user.card.payment', 'uses' => 'Api\CardController@CardPayment']);

        Route::post('/paymaya/save-card', 'PaymentMethods\PayMaya\PayMayaController@createToken');

        Route::post('/peachsavecard', 'PaymentMethods\RandomPaymentController@tokenizePeach');

        Route::get('/mpes', 'PaymentMethods\MpesContoller@start');
        Route::post('/paymentsubmit', 'PaymentMethods\MpesContoller@paymentsubmit')->name('paymentsubmit');
        Route::get('/paymentresponse', 'PaymentMethods\MpesContoller@paymentresponse')->name('paymentresponse');
        Route::get('/paymentsuccessfull', 'PaymentMethods\MpesContoller@paymentSuccess')->name('paymentsuccessfull');
        Route::get('/paymentfailed', 'PaymentMethods\MpesContoller@paymentFailed')->name('paymentfailed');


        // add family members in app
        Route::post('/AddFamilyMember', 'Api\UserController@AddFamilyMember');
        Route::post('/DeleteFamilyMember', 'Api\UserController@DeleteFamilyMember');
        Route::post('/ListFamilyMember', 'Api\UserController@ListFamilyMember');
        Route::post('/check_babySeat', ['as' => 'api.user.babySeat', 'uses' => 'Api\UserController@check_babySeat']);

        // check additional features/facilities while booking ride
        Route::post('/CheckSeats', ['as' => 'api.user.CheckSeats', 'uses' => 'Api\UserController@CheckSeats']);
        Route::post('/check_wheelChair', ['as' => 'api.user.wheelChair', 'uses' => 'Api\UserController@check_wheelChair']);

        Route::post('/wallet/transaction', ['as' => 'api.user.wallet', 'uses' => 'Api\UserController@WalletTransaction']);
        Route::post('/wallet/addMoney', ['as' => 'api.user.addMoney', 'uses' => 'Api\UserController@AddMoneyWallet']);
        Route::post('/sos/request', ['as' => 'user.api.sos.request', 'uses' => 'Api\CommonController@SosRequest']);

        Route::post('/refer', ['as' => 'api.user-refer', 'uses' => 'Api\UserController@Referral']);

        // mark/remove favourite driver
        Route::post('/favourite-driver', ['as' => 'api.favourite-driver', 'uses' => 'Api\UserController@favouriteDriver']);

        /*This Api has been merged with get favourite driver in case of checkout*/
        //Route::post('/favourite/drivers', ['as' => 'api.user-favouritedriver', 'uses' => 'Api\UserController@FavouriteDrivers']);
         /*This api is merged with add fav driver*/
//        Route::post('/delete-favourite-driver', ['as' => 'ap.delete-favourite-driver', 'uses' => 'Api\UserController@DeleteFavouriteDrivers']);
        Route::post('/get-favourite-driver', ['as' => 'api.favourite.driver', 'uses' => 'Api\UserController@getFavouriteDriver']);
        Route::post('/location', ['as' => 'api.location', 'uses' => 'Api\UserController@Location']);
        //Route::post('/test/location', ['as' => 'api.location', 'uses' => 'Api\UserController@UserLocation']);

        //mark fav location to get easy drop location for taxi segment
        //Fav location module is merged with add address in Account/userController, so no use of this module
        Route::post('/get-favourite-location', ['as' => 'api.favourite.view-location', 'uses' => 'Api\CommonController@viewFavouriteLocation']);
        Route::post('/add-favourite-location', ['as' => 'api.save-favourite.location', 'uses' => 'Api\CommonController@saveFavouriteLocation']);
        Route::post('/delete-favourite-location', ['as' => 'api.delete-favourite.location', 'uses' => 'Api\CommonController@deleteFavouriteLocation']);

        Route::post('/pricecard', ['as' => 'api.pricecard', 'uses' => 'Api\CommonController@Pricecard']);
//        Route::post('/pricecard-delivery', ['as' => 'api.pricecard-delivery', 'uses' => 'Delivery\ApiController@Pricecard']);
        Route::post('/checkout/apply-promo', ['as' => 'api.apply-promo', 'uses' => 'Api\BookingController@ApplyPromoCode']);
        Route::post('/checkout/remove-promo', ['as' => 'api.remove-promo', 'uses' => 'Api\BookingController@RemovePromoCode']);
        Route::post('/driver', ['as' => 'api.home-driver', 'uses' => 'Api\HomeController@homeScreenDrivers']);
        Route::post('/areas', ['as' => 'api.home-areas', 'uses' => 'Api\HomeController@Areas']);
        Route::post('/payment-option', ['as' => 'api.payment-option', 'uses' => 'Api\BookingController@paymentOption']);
        Route::post('/check-ride-time', 'Api\CommonController@CheckBookingTime');
        Route::post('/check-droplocation/area', ['as' => 'api.droplocation-area', 'uses' => 'Api\HomeController@CheckDropLocation']);
        Route::post('/booking/cancel', ['as' => 'api.user.booking.cancel', 'uses' => 'Api\BookingController@cancelBookingByUSer']);
        Route::post('/booking/autocancel', ['as' => 'api.user.booking.autocancel', 'uses' => 'Api\BookingController@UserAutoCancel']);
        Route::post('/booking/change_address', ['as' => 'api.user.booking.changeaddess', 'uses' => 'Api\BookingController@UserChangeAddress']);

        // booking tracking on user app
        Route::post('/booking/tracking', ['as' => 'api.user.booking.tracking', 'uses' => 'Api\BookingController@userTracking']);

        Route::post('/rate-to-driver', ['as' => 'api.user.rate.driver', 'uses' => 'Api\CommonController@rateToDriverByUser']);
//        Route::post('/booking/rate/driver', ['as' => 'api.user.rate.driver', 'uses' => 'Api\BookingController@UserRating']);
        Route::post('/booking/active', ['as' => 'api.user.active.booking', 'uses' => 'Api\BookingHistoryController@ActiveBookings']);
//        Route::post('/booking/history', ['as' => 'api.user.bookings', 'uses' => 'Api\BookingHistoryController@UserBookings']);
        Route::post('/booking/history/detail', ['as' => 'api.user.bookings.details', 'uses' => 'Api\BookingHistoryController@BookingDetail']);

//        Route::post('/booking/history/active', ['as' => 'api.user.active.bookings', 'uses' => 'Api\BookingHistoryController@userHistoryBookings']);
        Route::post('/booking/history', ['as' => 'api.user.active.bookings', 'uses' => 'Api\BookingHistoryController@userHistoryBookings']);

        Route::post('/booking/invoice/{booking_id}', ['as' => 'api.user.active.bookings', 'uses' => 'Api\EmailController@Invoice']);
        Route::post('/booking/make-payment', ['as' => 'api.user.bookings.payment', 'uses' => 'Api\BookingController@MakePayment']);
        Route::post('/promotion/notification', ['as' => 'api.promotion.notification', 'uses' => 'Api\UserController@PromotionNotification']);
        Route::post('/chat/send_message', ['as' => 'api.user.send_message', 'uses' => 'Api\ChatController@UserSendMessage']);
        Route::post('/chat', ['as' => 'api.user.chat', 'uses' => 'Api\ChatController@ChatHistory']);

        Route::post('/customer_support', ['as' => 'api.user.customer_support', 'uses' => 'Api\CommonController@Customer_Support']);
        Route::post('/AverageRating', 'Api\CommonController@UserRaing');

        Route::post('/redeem-points', 'Api\CommonController@redeemPoints');
        Route::post('/reward-points', 'Api\UserController@rewardPoints');
        Route::post('mpessaAddmoney', 'PaymentMethods\RandomPaymentController@MpessaAddMoney');
        Route::post('/bayarindAddMoney', 'PaymentMethods\RandomPaymentController@BayarindAddMoney');

        // UBpay payment gateway
        Route::post('/ubpay', ['as' => 'api.ubpay', 'uses' => 'PaymentMethods\RandomPaymentController@UbpayGetUrl']);

        //Ragerpay store transaction
        Route::post('/razerpay/transaction', 'PaymentMethods\RandomPaymentController@razerpayTransaction');
        Route::post('/razerpay/logs', 'PaymentMethods\RandomPaymentController@razerpayUserLog');

        // 2C2P payment gateway
        Route::post('/2c2p/transaction', 'PaymentMethods\RandomPaymentController@TwoCTwoPStoreTransaction');

        // Pesapal transaction store
        Route::post('/pesapal/transaction', 'PaymentMethods\RandomPaymentController@PesapalTransaction');

        // Delivery Routes
        Route::post('/delivery/product-list', 'Api\DeliveryController@getDeliveryProduct');
        Route::post('/manage-tip', 'Api\CommonController@addTip');

        Route::post('/dpo-paygate-initiate', 'PaymentMethods\RandomPaymentController@dpoPaygateInitiate');

        Route::post('amole/otp/generate', ['as' => 'api.user.amole.otp.generate', 'uses' => 'PaymentMethods\EwalletController@amoleGeneratePaymentOtp']);

        // Food  Module APIs
        Route::prefix('food')->group(function () {
            Route::post('/home-screen', ['as' => 'api.user.food.home.screen', 'uses' => 'Api\FoodController@homeScreen']);
            Route::post('/product-list', ['as' => 'api.user.food.products', 'uses' => 'Api\FoodController@foodProducts']);
            Route::post('/save-product-cart', ['as' => 'api.save.product-cart', 'uses' => 'Api\FoodController@saveProductCart']);
            Route::post('/get-product-cart', ['as' => 'api.get.product-cart', 'uses' => 'Api\FoodController@getProductCart']);
            Route::post('/apply-remove-promo-code', ['as' => 'api.food-promo-code', 'uses' => 'Api\FoodController@applyRemovePromoCode']);
            Route::post('/place-order', ['as' => 'api.place-food-order', 'uses' => 'Api\FoodController@placeOrder']);
            Route::post('/get-orders', ['as' => 'api.get-food-order', 'uses' => 'Api\FoodController@getOrders']);
            Route::post('/get-order-details', ['as' => 'api.get-food-order', 'uses' => 'Api\FoodController@getOrderDetails']);
            Route::post('/delete-product-cart', ['as' => 'api.delete-product-or-cart', 'uses' => 'Api\FoodController@deleteCart']);
        });
        // Grocery Module APIs
        Route::prefix('grocery')->group(function () {
            Route::post('/home-screen', ['as' => 'api.user.grocery.home.screen', 'uses' => 'Api\GroceryController@homeScreen']);
            Route::post('/get-store-categories', ['as' => 'api.user.store-category', 'uses' => 'Api\GroceryController@getCategory']);
            Route::post('/get-sub-category', ['as' => 'api.user.get-sub-category', 'uses' => 'Api\GroceryController@getSubCategory']);
            Route::post('/category-products', ['as' => 'api.user.category.products', 'uses' => 'Api\GroceryController@categoryProducts']);
            Route::post('/save-product-cart', ['as' => 'api.save.product-cart', 'uses' => 'Api\GroceryController@saveProductCart']);
            Route::post('/get-product-cart', ['as' => 'api.get.product-cart', 'uses' => 'Api\GroceryController@getProductCart']);
            Route::post('/apply-remove-promo-code', ['as' => 'api.food-promo-code', 'uses' => 'Api\FoodController@applyRemovePromoCode']);
            Route::post('/checkout/remove-promo', ['as' => 'api.remove-promo', 'uses' => 'Api\BookingController@RemovePromoCode']);
            Route::post('/place-order', ['as' => 'api.grocery.place.order', 'uses' => 'Api\GroceryController@placeOrder']);
            Route::post('/get-orders', ['as' => 'api.get-grocery-order', 'uses' => 'Api\GroceryController@getOrders']);
//            Route::post('/cancel-order', ['as' => 'api.cancel-grocery-order', 'uses' => 'Api\GroceryController@cancelOrder']);
            Route::post('/get-order-details', ['as' => 'api.get-grocery-order', 'uses' => 'Api\GroceryController@getOrderDetails']);
            Route::post('/delete-product-cart', ['as' => 'api.delete-product-or-cart', 'uses' => 'Api\GroceryController@deleteCart']);
        });

        // food and grocery receipt api
        Route::post('/order-receipt', ['as' => 'api.user.food.grocery.receipt', 'uses' => 'Api\OrderController@orderReceipt']);
        Route::post('/track-order', ['as' => 'api.track-order', 'uses' => 'Api\OrderController@trackOrder']);
        Route::post('/track-order-details', ['as' => 'api.track-order', 'uses' => 'Api\OrderController@trackOrderDetails']);
        Route::post('/order-cancel', ['as' => 'api.cancel-order', 'uses' => 'Api\OrderController@userCancelOrder']);

        Route::post('/segments', ['as' => 'api.user.merchant.segments', 'uses' => 'Api\MainScreenController@mainScreenSegments']);
        Route::post('/add-address', ['as' => 'api.user.add-address', 'uses' => 'Account\UserController@saveUserAddress']);
        Route::post('/get-address', ['as' => 'api.user.get-address', 'uses' => 'Account\UserController@getUserAddress']);
        Route::post('/delete-address', ['as' => 'api.user.delete-address', 'uses' => 'Account\UserController@deleteUserAddress']);
        Route::post('/get-promo-code', ['as' => 'api.get-promo-code-list', 'uses' => 'Api\MainScreenController@getPromoCodeList']);
        Route::post('/service-slots', ['as' => 'api.user.merchant.service-slots', 'uses' => 'Api\DriverController@getServiceTimeSlot']);
        Route::post('/payment-methods', ['as' => 'api.user.payment.method', 'uses' => 'Api\CommonController@getPaymentMethod']);

        Route::prefix('handyman')->group(function () {
            Route::post('/get-providers', ['as' => 'api.get-driver-list', 'uses' => 'Api\PlumberController@getPlumbers']);
            Route::post('/get-provider', ['as' => 'api.get-driver', 'uses' => 'Api\PlumberController@getPlumber']);
            Route::post('/get-services', ['as' => 'api.get-handyman-services', 'uses' => 'Api\PlumberController@getPlumberServices']);
            Route::post('/apply-remove-promo-code', ['as' => 'api.handyman.promo-code', 'uses' => 'Api\PlumberController@applyRemovePromoCode']);
//            Route::post('/save-booking-cart', ['as' => 'api.save.handyman-booking.cart', 'uses' => 'Api\PlumberController@saveBookingCart']);
            Route::post('/confirm-order', ['as' => 'api.handyman.confirm-order', 'uses' => 'Api\PlumberController@confirmOrder']);
            Route::post('/get-orders', ['as' => 'api.handyman.orders', 'uses' => 'Api\PlumberController@getOrders']);
            Route::post('/get-order-detail', ['as' => 'api.handyman.order.detail', 'uses' => 'Api\PlumberController@getOrderDetail']);
            Route::post('/cancel-order', ['as' => 'api.handyman.cancel.order', 'uses' => 'Api\PlumberController@cancelOrder']);
            Route::post('/rate/provider', ['as' => 'api.provider.rate', 'uses' => 'Api\HandymanOrderController@providerRating']);
            Route::post('/booking-payment', ['as' => 'api.booking.payment', 'uses' => 'Api\HandymanOrderController@bookingPayment']);
        });

        //Check User for Wallet Transaction
        Route::post('/check-user', ['as' => 'api.check-user', 'uses' => 'Api\UserController@CheckUser']);
        Route::post('/transfer-money', ['as' => 'api.transfer-money', 'uses' => 'Api\UserController@TransferWalletMoney']);

        Route::post('/direction-data', ['as' => 'api.user-direction', 'uses' => 'Api\CommonController@googleDirectionData']);

        //paypal
        Route::post('/paypal', 'PaymentMethods\RandomPaymentController@PaypalWebViewURL');

        // Illico Cash Payment
        Route::post('/illicocash/payment', 'PaymentMethods\EwalletController@illicoCashPayment');
        Route::post('/illicocash/payment/confirm', 'PaymentMethods\EwalletController@illicoCashPaymentOTP');

        // Tripay Cash Payment
        Route::post('/tripay/get-payment-channels', 'PaymentMethods\RandomPaymentController@TriPayPaymentChannels');
        Route::post('/tripay/payment', 'PaymentMethods\RandomPaymentController@TriPayCreateTransaction');

        // Bookeey
        Route::post('/bookeey/url', 'PaymentMethods\RandomPaymentController@BookeeyURL')->name('BookeeyURL');
        Route::post('/paygate-webview-url', 'PaymentMethods\Paygate\PaygateController@getWebViewUrl')->name('api.get-paygate-webview');

        // payment via online payment options
        Route::post('/online/make-payment', ['as' => 'api.user.online.payment', 'uses' => 'PaymentMethods\Payment@onlinePayment']);
        Route::post('/online/payment-status', ['as' => 'api.user.online.payment.status', 'uses' => 'PaymentMethods\Payment@onlinePaymentStatus']);
        Route::post('/momopay/make-payment', ['as' => 'api.user.momopay.payment', 'uses' => 'PaymentMethods\RandomPaymentController@MOMOPayRequest']);

        // ProxyPay
        Route::post('/proxy_pay/initiate_transaction', ['as' => 'proxy_pay.initiate_transaction', 'uses' => 'PaymentMethods\ProxyPay\ProxyPayController@createReference']);
        Route::post('/proxy_pay/transaction_status', ['as' => 'proxy_pay.transaction_status', 'uses' => 'PaymentMethods\ProxyPay\ProxyPayController@acknowledgePayment']);

        //Payhere payment gateway
        Route::post('/PayHere/AddCard', ['as' => 'PayHere.AddCardTransaction', 'uses' => 'PaymentMethods\PayHere\PayHereController@AddCardTransaction']);
        //jazzcash Payment Gateway
        Route::post('jazzcash','PaymentMethods\RandomPaymentController@JazzCash');
        //mpesaB2C api
        Route::post('/mpesa/b2c/request','PaymentMethods\RandomPaymentController@submitB2CRequest');
        //QuickPay
        Route::post('/quickPay','PaymentMethods\RandomPaymentController@QuickPay');


        // mark/remove favourite business-segment
        Route::post('/favourite-business-segment', ['as' => 'api.favourite-business-segment', 'uses' => 'Api\FoodController@favouriteBusinessSegment']);
        Route::post('/get-favourite-business-segment', ['as' => 'api.favourite.business-segment', 'uses' => 'Api\FoodController@getFavouriteBusinessSegment']);
        
        // Wave payment gateway create transaction
        Route::post('/wave-business/create-transaction', 'PaymentMethods\WaveBusiness\WaveBusiness@createTransaction');
        //Telebirr
        Route::post('/generateTelebirrPayUrl','PaymentMethods\TelebirrPay\TelebirrPayController@generateTeliberrUrl');
        Route::post('/account-delete','Api\UserController@AccountDelete');
    });
});

// driver app api
Route::prefix('driver')->group(function () {
    Route::group(['middleware' => ['driver']], function () {
        Route::post('/validate-data','Api\DriverController@SignupValidation');
        // Driver Registration step one
        Route::post('/reg-step-one', ['as' => 'api.driver.reg-step-one', 'uses' => 'Api\DriverController@RegStepOne']);

        Route::post('/reg-step-two', ['as' => 'api.driver.reg-step-two', 'uses' => 'Api\DriverController@RegStepTwo']);
        // add working mode for driver
        Route::post('/reg-step-three', ['as' => 'api.driver.reg-step-three', 'uses' => 'Api\DriverController@RegStepThree']);

        Route::post('/reg-step-five', ['as' => 'api.driver.reg-step-five', 'uses' => 'Api\DriverController@RegStepFive']);

        Route::post('/bank-details/save', ['as' => 'driver.bankdetails', 'uses' => 'Api\DriverController@BankDetailsSave']);


        // driver login
        Route::post('/on-board', ['as' => 'api.driver-login', 'uses' => 'Api\DriverController@Login']);

        // driver login
        Route::post('/direction-data', ['as' => 'api.driver-login', 'uses' => 'Api\CommonController@googleDirectionData']);

        Route::post('/register-stripe-connect', 'Account\DriverController@RegisterToStripeConnect');
        Route::post('/check-stripe-connect', 'Account\DriverController@CheckStripeConnect');
        Route::post('/website/homeScreen', 'Api\WebsiteController@DriverHomeScreen');
        Route::post('/check-droplocation/area', ['as' => 'api.droplocation-area', 'uses' => 'Api\HomeController@CheckDropLocation']);
        Route::post('/configuration', ['as' => 'api.driver.configuration', 'uses' => 'Api\DriverController@Configuration']);

        Route::post('/get-document-list', ['as' => 'api.document-list', 'uses' => 'Api\DriverController@getDocumentList']);


        Route::post('/documentlist', ['as' => 'api.driver-documentlist', 'uses' => 'Api\DriverController@DocumentList']);
        Route::post('/vehicledocumentlist', 'Api\DriverController@VehicleDocumentList');
//        Route::post('/firtstep', ['as' => 'api.driver.signup-firstStep', 'uses' => 'Api\DriverController@BasicInformation']);
//        Route::post('/login', ['as' => 'api.driver-login', 'uses' => 'Api\DriverController@Login']);
        Route::post('/login/otp', ['as' => 'api.driver-login-otp', 'uses' => 'Api\DriverController@LoginOtp']);
        Route::post('/vehicle-configuration', ['as' => 'api.driver-vehicle-config', 'uses' => 'Api\DriverVehicleController@vehicleConfiguration']);
        Route::post('/vehicle-model', ['as' => 'api.driver-vehicle-model', 'uses' => 'Api\DriverVehicleController@getVehicleModel']);
        Route::post('/add-vehicle', ['as' => 'api.driver-add-vehicle', 'uses' => 'Api\DriverVehicleController@addVehicle']);
        Route::post('/vehicle/otp', ['as' => 'api.driver-addvehicle.otp', 'uses' => 'Api\DriverVehicleController@VehicleOtpVerifiy']);
        Route::post('/vehicle-request', ['as' => 'api.request', 'uses' => 'Api\DriverVehicleController@vehicleRequest']);
//        Route::post('/add-requested-vehicle', ['as' => 'api.add-requested-vehicle', 'uses' => 'Api\DriverVehicleController@addRequestedVehicle']);
        Route::post('/add-document', ['as' => 'api.driver-add-document', 'uses' => 'Api\DriverController@addDocument']); // add personal document
//        Route::post('/addvehicledocument', ['as' => 'api.driver-addvehicledocument', 'uses' => 'Api\DriverController@AddVehicleDocument']); // add vehicle document of driver
        Route::post('/otp', ['as' => 'api.driver-otp', 'uses' => 'Api\DriverController@Otp']);
        Route::post('/forgotpassword', ['as' => 'api.driver.password', 'uses' => 'Api\DriverController@ForgotPassword']);
        Route::post('/cms/pages', 'Api\CommonController@DriverCmsPage');
//        Route::post('/demo', ['as' => 'driver.api.demoUser', 'uses' => 'Api\DriverController@Demo']);
        Route::post('/edit-profile', 'Api\DriverController@editProfile');
        Route::post('/details', 'Api\DriverController@DriverDetails');
        Route::post('/account-types', ['as' => 'driver.api.account-types', 'uses' => 'Api\DriverController@AccountTypes']);
        Route::post('/getnetworkcode', 'Api\CommonController@getNetworkCode');
        Route::post('/korbapayment', 'PaymentMethods\RandomPaymentController@korbaWeb')->name('korbapayment');
        Route::post('/driver-all-document', 'Api\DriverController@driverDocument');
        Route::post('/ride-payment-status', 'Api\BookingController@driverRidePaymentStatus');


        // multi-service
        Route::post('/service-slots', ['as' => 'api.driver.merchant.service-slots', 'uses' => 'Api\DriverController@getServiceTimeSlot']);

        Route::post('/get-segment-gallery', ['as' => 'api.driver.get.segment.gallery', 'uses' => 'Api\DriverController@getDriverGallery']);
        Route::post('/save-segment-gallery', ['as' => 'api.driver.save.segment.gallery', 'uses' => 'Api\DriverController@saveDriverGallery']);
        Route::post('/delete-segment-gallery', ['as' => 'api.driver.delete.segment.gallery', 'uses' => 'Api\DriverController@deleteDriverGallery']);

        Route::post('/demo-onboard', ['as' => 'driver.api.demo.onboard', 'uses' => 'Api\DriverController@demoLogin']);

        // only get data according to driver time zone
        Route::post('/promotion/notification', ['as' => 'api.driver.promotion.notification', 'uses' => 'Api\DriverController@PromotionNotification']);
        Route::post('/wallet/transaction', ['as' => 'api.driver.wallet', 'uses' => 'Api\DriverEarningController@WalletTransaction']);
        Route::post('/getString', ['as' => 'api.getLatestString', 'uses' => 'Api\StringLanguageController@getLatestString']);
        
        Route::post('/face-recognition', ['as' => 'api.user.face-recognition', 'uses' => 'Api\CommonController@faceRecognition']);
    });


    Route::group(['middleware' => ['auth:api-driver', 'timezone']], function () {
        
        Route::post('/get-paystack-bank-codes', ['as' => 'driver.get-paystack-bank-codes', 'uses' => 'Api\CardController@getPaystackBankCodes']);

        Route::post('/paystack-registration', ['as' => 'driver.paystack-registration', 'uses' => 'Api\CardController@PaystackRegistration']);

        // driver segment time slot
        Route::post('/save-service-time-slot', ['as' => 'api.driver.segment-configuration', 'uses' => 'Api\DriverController@saveServiceTimeSlot']);

        // driver get driver online work configuration
        Route::post('/get-online-work-config', ['as' => 'api.driver.get-online-configuration', 'uses' => 'Api\DriverController@getOnlineConfig']);
        Route::post('/save-online-work-config', ['as' => 'api.driver.save-online-configuration', 'uses' => 'Api\DriverController@saveOnlineConfig']);

        // notification testing api
      //  Route::post('/test-notification', ['as' => 'api.test-noti', 'uses' => 'Api\DriverController@testNotification']);


        Route::post('/get-main-screen-config', ['as' => 'api.driver.main-screen-config', 'uses' => 'Api\DriverController@getMainScreenConfig']);

        // get driver segment list with already configured
        Route::post('/get-segment-list', ['as' => 'api.driver.config-segment-list', 'uses' => 'Api\DriverController@getSegmentList']);

        // get driver enrolled/signedup segment list
        Route::post('/get-enrolled-segments', ['as' => 'api.driver.enrolled-segment-list', 'uses' => 'Api\DriverController@getEnrolledSegments']);

        Route::post('/get-segment-services', ['as' => 'api.driver.segment-config', 'uses' => 'Api\DriverController@getSegmentServicesConfig']);
        // driver segment configuration
        Route::post('/save-segment-config', ['as' => 'api.driver.segment-configuration', 'uses' => 'Api\DriverController@saveSegmentConfig']);

        // get vehicle list
        Route::post('/get-vehicle-list', ['as' => 'api.driver-vehicle-list', 'uses' => 'Api\DriverVehicleController@getVehicleList']);

        // order or booking info
        //Route::post('/bookings/detail', ['as' => 'api.driver-bookings-detail', 'uses' => 'Api\BookingController@Detail']);
        Route::post('/booking-order-info', ['as' => 'api.booking-order-information', 'uses' => 'Api\CommonController@bookingOrderInfo']);

        // accept booking-order
       // Route::post('/bookings/accept', ['as' => 'api.driver-bookings-accept', 'uses' => 'Api\BookingController@BookingAccept']);
//        Route::post('/bookings/reject', ['as' => 'api.driver-bookings-reject', 'uses' => 'Api\BookingController@Reject']);
        Route::post('/booking-order-accept-reject', ['as' => 'api.driver-update-booking-order-status', 'uses' => 'Api\CommonController@bookingOrderAcceptReject']);
       // Route::post('/bookings/arrive', ['as' => 'api.driver-bookings-arrive', 'uses' => 'Api\BookingController@Arrive']);
        Route::post('/arrived-at-pickup', ['as' => 'api.driver-arrived-at-pickup', 'uses' => 'Api\CommonController@arrivedAtPickup']);
        Route::post('/order-in-process', ['as' => 'api.order-in-process', 'uses' => 'Api\CommonController@orderInProcess']);


        //Route::post('/bookings/start', ['as' => 'api.driver-bookings-start', 'uses' => 'Api\BookingController@Start']);
        Route::post('/booking-order-picked', ['as' => 'api.booking-order-on-the-way', 'uses' => 'Api\CommonController@bookingOrderPicked']);

        // in case of booking(taxi + delivery etc)
        Route::post('/booking/end', ['as' => 'api.driver-bookings-end', 'uses' => 'Api\BookingController@endBooking']);
        // in case of order
        Route::post('/deliver-order', ['as' => 'api.order-delivered', 'uses' => 'Api\CommonController@deliverOrder']);
       // Route::post('/bookings/cancel', ['as' => 'api.driver-bookings-cancel', 'uses' => 'Api\BookingController@DriverCancel']);
        Route::post('/cancel-booking-order', ['as' => 'api.cancel-booking-order', 'uses' => 'Api\CommonController@cancelBookingOrder']);

        Route::post('/get-booking-order-payment-info', ['as' => 'api.delivered-order-info', 'uses' => 'Api\CommonController@bookingOrderPaymentInfo']);
        Route::post('/update-booking-order-payment-status', ['as' => 'api.update-payment-status', 'uses' => 'Api\CommonController@updateBookingOrderPaymentStatus']);

        //Route::post('/bookings/close', ['as' => 'api.driver-bookings-close', 'uses' => 'Api\BookingController@BookingClose']);
        Route::post('/complete-booking-order', ['as' => 'api.complete-booking-order', 'uses' => 'Api\CommonController@completeBookingOrder']);

        Route::post('/slider-data', ['as' => 'api.driver-slider-data', 'uses' => 'Api\CommonController@sliderData']);

        // get active booking and orders
//        Route::post('/booking/history/active', ['as' => 'api.driver-.activebookings', 'uses' => 'Api\BookingHistoryController@DriverActiveBooking']);
        Route::post('/get-active-booking-order', ['as' => 'api.get-active-booking-order', 'uses' => 'Api\CommonController@getActiveBookingOrder']);
        // get past booking and orders
//        Route::post('/booking/history/past', ['as' => 'api.driver-bookings', 'uses' => 'Api\BookingHistoryController@DriverBookingHistory']);
        Route::post('/get-past-booking-order', ['as' => 'api.get-past-booking-order', 'uses' => 'Api\CommonController@getPastBookingOrder']);


        Route::post('/get-booking-order-details', ['as' => 'api.get-booking-order-details', 'uses' => 'Api\CommonController@getBookingOrderDetails']);
        // update driver location
        Route::post('/location', ['as' => 'api.driver-location', 'uses' => 'Api\DriverController@Location']);


        // tutu changes
        Route:: post('/redeem-points-driver', 'Api\CommonController@driverRedeemPoints');
        Route:: post('/withdraw-driver-wallet', 'Api\DriverController@withdrawWallet');
        Route:: post('/reward-points', 'Api\DriverController@rewardPoints');
        // end

        Route::post('/sos', 'Api\SosController@SosDriver');
        Route::post('/sos/create', 'Api\SosController@addSosDriver');
        Route::post('/sos/distory', 'Api\SosController@deleteDriverSos');

        Route::post('/timeStamp', 'Api\DriverController@CheckTimeStap');
//        Route::post('/sendMoneyToUser', 'Api\DriverController@sendMoneyToUser');
        Route::post('/view-subscription-packages', ['as' => 'api.driver-view-subscription-packages', 'uses' => 'Api\SubscriptionPackageController@ViewPackages']);
        Route::post('/activate-subscription-package', ['as' => 'api.driver-activate-subscription-packages', 'uses' => 'Api\SubscriptionPackageController@ActivatePackage']);
        Route::post('/driverData', 'Api\DriverController@Driver');
        Route::post('/updateTerms', 'Api\DriverController@DriverTermUpdate');
        Route::post('/booking/otp_verify', ['as' => 'api.driver-bookings-otp-verify', 'uses' => 'Api\BookingController@BookingOtpVerify']);

        Route::post('/auto_accept_mode', ['as' => 'driver.api.auto_accept_mode', 'uses' => 'Api\DriverController@AutoAcceptEnable']);

        // using
        Route::post('/manual-booking/checkout', ['as' => 'driver.api.manual.checkoutBooking', 'uses' => 'Api\ManualDispatchController@checkoutBooking']);
        // using
        Route::post('/manual-booking/confirm', ['as' => 'driver.api.manual.booking', 'uses' => 'Api\ManualDispatchController@confirmBooking']);

        Route::post('/cancel-reasons', ['as' => 'driver.api.cancel-reason', 'uses' => 'Api\CommonController@driverCancelReason']);
        Route::post('/auto-upgrade', ['as' => 'driver.api.auto.request', 'uses' => 'Api\DriverController@AutoUpgradetion']);
        Route::post('/manual-downgrade', ['as' => 'driver.api.downgrade.request', 'uses' => 'Api\DriverController@ManualDowngradation']);
        Route::post('/manual-downgrade/vehicle-type/list', ['as' => 'driver.downgrade.vehicle_type.list', 'uses' => 'Api\DriverController@ManualDowngradeVehicleTypeList']);
        Route::post('/sos/request', ['as' => 'driver.api.sos.request', 'uses' => 'Api\CommonController@DriverSosRequest']);
        Route::post('/bank-details/update', ['as' => 'driver.bankdetails', 'uses' => 'Api\DriverController@BankDetailsUpdate']);
        Route::post('/set-radius', ['as' => 'driver.set-radius-driver', 'uses' => 'Api\DriverController@driverSetRadius']);
        Route::post('/refer', ['as' => 'api.driver-refer', 'uses' => 'Api\DriverController@DriverReferral']);
//        Route::post('/manual_dispatch_estimate', ['as' => 'api.driver-estimate', 'uses' => 'Api\CommonController@estimate']);

        Route::post('/personal/documentlist', ['as' => 'api.personal-documentlist', 'uses' => 'Api\DriverController@PersonalDocumentList']);

        // driver home/additional address api
        Route::post('/add-address', ['as' => 'api.add-driver-address', 'uses' => 'Api\DriverController@addAddress']);
        Route::post('/get-address', ['as' => 'api.get-driver-address', 'uses' => 'Api\DriverController@getAddress']);

        Route::post('/home-address-status', ['as' => 'api.driver-homeaddress.status', 'uses' => 'Api\DriverController@homeAddressStatus']);
        Route::post('/select/homelocation', ['as' => 'api.driver-homelocation.select', 'uses' => 'Api\DriverController@SelectAddress']);
        Route::post('/delete/homelocation', ['as' => 'api.driver-delete.status', 'uses' => 'Api\DriverController@DeleteHomeLocation']);
        Route::post('/demand-spot', ['as' => 'api.driver-demand-spot', 'uses' => 'Api\DriverController@heatMap']);
        Route::post('/receipt', ['as' => 'api.driver.receipt', 'uses' => 'Api\BookingController@driverReceipt']);
        //Route::post('/vehicles', ['as' => 'api.driver-vehicles', 'uses' => 'Api\DriverVehicleController@VehicleList']);

        Route::post('/out-board', ['as' => 'api.driver-logout', 'uses' => 'Api\DriverController@Logout']);

//        Route::post('/active_vehicle', ['as' => 'api.driver-vehicles', 'uses' => 'Api\DriverVehicleController@ActiveVehicle']);
        Route::post('/changeVehicle', 'Api\DriverVehicleController@ChangeVehicle');
        Route::post('/pool/active/deactive', ['uses' => 'Api\DriverVehicleController@PoolOnOff']);


        Route::post('/getlocationfromLatlong', 'Api\DriverController@CurrentLocation');
        Route::post('/online-offline', ['as' => 'api.driver-location', 'uses' => 'Api\DriverController@OnlineOffline']);
//        Route::post('/changepassword', ['as' => 'api.driver-location', 'uses' => 'Api\DriverController@ChangePassword']);
        Route::post('/main/screen', ['as' => 'api.driver-main.screen', 'uses' => 'Api\DriverController@MainScreen']);
        Route::post('/booking/change_address', ['as' => 'api.driver.booking.changeaddess', 'uses' => 'Api\BookingController@DriverChangeAddress']);

        Route::post('/accept-upcoming-booking', ['as' => 'api.driver-bookings-partial-accept', 'uses' => 'Api\BookingController@acceptUpcomingBooking']);


        //  reached at multi-drop location
        Route::post('/reached-at-multi-drop', ['as' => 'api.driver-reached-at-multi-drop', 'uses' => 'Api\BookingController@reachedAtMultiDrop']);

        Route::post('/booking/rate/user', ['as' => 'api.user-rate', 'uses' => 'Api\BookingController@DriverRating']);


//        Route::post('/bookings/close', ['as' => 'api.driver-bookings-close', 'uses' => 'Api\BookingController@BookingClose']);

        //no need of tracking on driver side
        //Route::post('/booking/tracking', ['as' => 'api.driver.booking.tracking', 'uses' => 'Api\BookingController@DriverTracking']);

        Route::post('/get-schedule-upcoming-booking', ['as' => 'api.driver-bookings-schedule', 'uses' => 'Api\BookingHistoryController@getScheduleUpcomingBooking']);

        //        Route::post('/booking/history/schedule', ['as' => 'api.driver-bookings-schedule', 'uses' => 'Api\BookingHistoryController@DriverScheduleHistory']);
//        Route::post('/booking/history/upcomming', ['as' => 'api.driver-bookings-upcomming', 'uses' => 'Api\BookingHistoryController@DriverUpcommingHistory']);
        Route::post('/booking/history/upcomming/outstation', ['as' => 'api.driver-bookings-outstation', 'uses' => 'Api\BookingHistoryController@DriverUpcommingOutStationHistory']);

        Route::post('/booking/history/detail', ['as' => 'api.driver-booking-detail', 'uses' => 'Api\BookingHistoryController@DriverBookingDetails']);
        Route::post('/getSuperDrivers', 'Api\DriverController@SuperDrivers');

//        Route::post('/earnings_revised', ['as' => 'api.driver.earnings_revised', 'uses' => 'Api\DriverEarningController@DriverEarningsCalculation']);
//        Route::post('/earnings', ['as' => 'api.driver.earnings', 'uses' => 'Api\DriverEarningController@Earning']);
//        Route::post('/earning/details', ['as' => 'api.driver.earning', 'uses' => 'Api\DriverEarningController@EarningHolder']);
//        Route::post('/earnings/singleDay', ['as' => 'api.driver.earnings.details', 'uses' => 'Api\DriverEarningController@DailyEarning']);

        Route::post('/chat/send_message', ['as' => 'api.driver.send_message', 'uses' => 'Api\ChatController@DriverSendMessage']);
        Route::post('/chat', ['as' => 'api.driver.chat', 'uses' => 'Api\ChatController@ChatHistory']);
        Route::post('/customer_support', ['as' => 'api.driver.customer_support', 'uses' => 'Api\CommonController@Driver_Customer_Support']);
        Route::post('/AverageRating', 'Api\CommonController@DriverRating');
        Route::post('/paytmchecksumdriver', 'Api\CardController@PaytmChecksum');
        Route::post('/chargePaystackDriver', 'Api\CardController@ChargePaystack');
        ///driver cards
        Route::post('/cards', 'Api\CardController@DriverCards');
//        Route::post('/savecards', 'Api\CardController@DriverSaveCards');
        Route::post('/save-card', 'Api\CardController@saveDriverCard');
        Route::post('/card/delete', 'Api\CardController@DriverDeleteCard');
//        Route::post('/makePayment', 'Api\CardController@DriverCardPayment');
        Route::post('/card/make-payment', 'Api\CardController@DriverCardPayment');
//        Route::post('/wallet/addMoney', ['as' => 'api.driver.addMoney', 'uses' => 'Api\DriverEarningController@AddMoney']);
//      /// New add walletmoney api
        Route::post('/wallet/add-money', ['as' => 'api.driver.addMoney', 'uses' => 'Api\DriverEarningController@AddMoney']);
        Route::post('/expiredocuments', ['as' => 'driver.expiredocuments', 'uses' => 'Api\ExpireDocumentController@index']);
        Route::post('/IugoPayment', 'Api\CardController@IugoPayment');
        Route::post('/creatPrefId', 'Api\CardController@prefIdMercado');
        Route::post('/createTransDPO', 'PaymentMethods\RandomPaymentController@createTransDPO');
        Route::post('/mobileMoneyDPO', 'PaymentMethods\RandomPaymentController@DpoMobileMoney');
        Route::post('/beyonicMobileMoney', 'PaymentMethods\RandomPaymentController@beyonicMobileMoney');
        Route::post('/peachsavecard', 'PaymentMethods\RandomPaymentController@tokenizePeach');
        Route::post('/paymaya/save-card', 'PaymentMethods\PayMaya\PayMayaController@createToken');
        Route::post('/verifymobileMoneyDPO', 'PaymentMethods\RandomPaymentController@verifyMobileMoneyDPO');
//        Route::post('/booking/history/upcomming/delivery', ['as' => 'api.driver-bookings-upcomming-delivery', 'uses' => 'Api\BookingHistoryController@DriverUpcommingHistoryDelivery']);
        Route::post('mpessaAddmoney', 'PaymentMethods\RandomPaymentController@MpessaAddMoney');

        //Geofence Queue
        Route::post('/geofence/queue/in-out', 'Api\CommonController@geofenceQueueInOut');
        Route::post('/geofence/in-out', 'Api\CommonController@geofenceInOut');
//        Route::post('/geofence/list', 'Api\CommonController@getGeofenceArea');

        Route::post('/bookings/pause/resume', ['as' => 'api.driver-bookings-pause-resume', 'uses' => 'Api\BookingController@RidePauseResume']);

        //Razerpay store transaction
        Route::post('/razerpay/transaction', 'PaymentMethods\RandomPaymentController@razerpayTransaction');
        Route::post('/razerpay/logs', 'PaymentMethods\RandomPaymentController@razerpayDriverLog');

        // Senangpay
        Route::post('/senangpay/tokenization', ['as' => 'user.api.senangpay-token', 'uses' => 'Api\CardController@SenangPayToken']);
        Route::post('/senangpay/record/transaction', ['as' => 'user.api.senangpay-record', 'uses' => 'PaymentMethods\RandomPaymentController@SenangPayRecordTransaction']);

        // 2C2P payment gateway
        Route::post('/2c2p/transaction', 'PaymentMethods\RandomPaymentController@TwoCTwoPStoreTransaction');

        Route::prefix('handyman')->group(function () {
            //get driver's orders
            Route::post('/get-orders', ['as' => 'api.handyman.orders', 'uses' => 'Api\HandymanOrderController@getOrders']);
            Route::post('/get-order', ['as' => 'api.handyman.order', 'uses' => 'Api\HandymanOrderController@getOrder']);
            Route::post('/accept-reject-order', ['as' => 'api.handyman.accept-reject.order', 'uses' => 'Api\HandymanOrderController@acceptRejectOrder']);
            Route::post('/cancel-order', ['as' => 'api.handyman.cancel.order', 'uses' => 'Api\HandymanOrderController@cancelOrder']);
            Route::post('/start-order-otp', ['as' => 'api.handyman.order.processing-otp', 'uses' => 'Api\HandymanOrderController@startOrderOTP']);
            Route::post('/start-order', ['as' => 'api.handyman.order.processing', 'uses' => 'Api\HandymanOrderController@startOrder']);
            Route::post('/end-order', ['as' => 'api.handyman.end.order', 'uses' => 'Api\HandymanOrderController@endOrder']);
            Route::post('/update-payment-order', ['as' => 'api.handyman.update.payment.order', 'uses' => 'Api\HandymanOrderController@updateOrderPaymentStatus']);
            Route::post('/complete-order', ['as' => 'api.handyman.complete.order', 'uses' => 'Api\HandymanOrderController@completeOrder']);
        });

        // Cashout Module
        Route::post('/cashout/request', ['as' => 'merchant.driver.cashout.request', 'uses' => 'Api\DriverCashoutController@request']);
        Route::post('/cashout/history', ['as' => 'merchant.driver.cashout.history', 'uses' => 'Api\DriverCashoutController@index']);

        // New Earning Screen
        Route::post('/account/earnings', ['as' => 'api.driver.account.earnings', 'uses' => 'Api\CommonController@getBookingOrderAccountDetails']);
//        Route::post('/account/earnings/old', ['as' => 'api.driver.account.earnings', 'uses' => 'Api\DriverEarningController@DriverAccountEarningsOld']);
//        Route::post('/account/earning/details', ['as' => 'api.driver.account.earning.details', 'uses' => 'Api\DriverEarningController@AccountEarningHolder']);

        Route::post('development/verification', ['as' => 'api.driver.develop.mode.verification', 'uses' => 'Api\DriverController@developModeVerification']);

        // uploaded item loaded images in delivery
        Route::post('upload-loaded-item-image', ['as' => 'api.driver.delivery-loaded-images', 'uses' => 'Api\DeliveryController@saveProductLoadedImages']);

        // check driver document expired/ will expire
        Route::post('check-expired-document', ['as' => 'api.driver.expired-document', 'uses' => 'Api\DriverController@checkDocumentStatus']);


        // upload booking image
        Route::post('upload-booking-image', ['as' => 'api.driver.booking-image', 'uses' => 'Api\HandymanOrderController@saveBookingImage']);
        Route::post('get-booking-image', ['as' => 'api.driver.get-booking-image', 'uses' => 'Api\HandymanOrderController@getBookingImage']);
        Route::post('toll-api', ['as' => 'api.driver.toll-api', 'uses' => 'Helper\Toll@peajeTollApi']);

        //paypal to open paypal web view
        Route::post('/paypal', 'PaymentMethods\RandomPaymentController@PaypalWebViewURL');

        // TriPay Payment
        Route::post('/tripay/payment', 'PaymentMethods\RandomPaymentController@TriPayCreateTransaction');
        Route::post('/tripay/get-payment-channels', 'PaymentMethods\RandomPaymentController@TriPayPaymentChannels');

        // Bookeey
        Route::post('/bookeey/url', 'PaymentMethods\RandomPaymentController@BookeeyURL')->name('BookeeyURL');
        //  paygate
        Route::post('/paygate-webview-url-driver', 'PaymentMethods\Paygate\PaygateController@getWebViewUrlDriver')->name('api.get-paygate-webview-driver');

        // wallet topup via online payment options
        Route::post('/online/make-payment', ['as' => 'api.driver.online.payment', 'uses' => 'PaymentMethods\Payment@onlinePayment']);
        Route::post('/online/payment-status', ['as' => 'api.driver.online.payment.status', 'uses' => 'PaymentMethods\Payment@onlinePaymentStatus']);
        Route::post('/momopay/make-payment', ['as' => 'api.driver.momopay.payment', 'uses' => 'PaymentMethods\RandomPaymentController@MOMOPayRequest']);

        // ProxyPay
        Route::post('/proxy_pay/initiate_transaction', ['as' => 'proxy_pay.initiate_transaction', 'uses' => 'PaymentMethods\ProxyPay\ProxyPayController@createReference']);
        Route::post('/proxy_pay/transaction_status', ['as' => 'proxy_pay.transaction_status', 'uses' => 'PaymentMethods\ProxyPay\ProxyPayController@acknowledgePayment']);

        //Payhere payment gateway
        Route::post('/PayHere/AddCard', ['as' => 'PayHere.AddCardTransaction', 'uses' => 'PaymentMethods\PayHere\PayHereController@AddCardTransaction']);
        //jazzcash Payment Gateway
        Route::post('jazzcash','PaymentMethods\RandomPaymentController@JazzCash');
        //mpesaB2C api
        Route::post('/mpesa/b2c/request','PaymentMethods\RandomPaymentController@submitB2CRequest');
        Route::post('/changeRideGender', 'Api\DriverController@changeRideGender');
        //QuickPay
        Route::post('/quickPay','PaymentMethods\RandomPaymentController@QuickPay');


        // mercado token setup
        Route::post('/mercado/auth-code','PaymentMethods\Mercado\MercadoController@mercadoAuthCodeRequest')->name('mercado.code');
        
        // Wave payment gateway create transaction
        Route::post('/wave-business/create-transaction', 'PaymentMethods\WaveBusiness\WaveBusiness@createTransaction');
        //Telebirr Pay
        Route::post('/generateTelebirrPayUrl','PaymentMethods\TelebirrPay\TelebirrPayController@generateTeliberrUrl');
        Route::post('/account-delete','Api\DriverController@AccountDelete');
    });
});


// business segments (restaurant's) api
Route::prefix('business-segment')->group(function () {
    Route::group(['middleware' => ['business_segment']], function () {

        // driver login
        Route::post('/on-board', ['as' => 'api.business-segment-login', 'uses' => 'BusinessSegment\Api\AccountController@login']);
        // Route::post('/demo-onboard', ['as' => 'api.business-segment-demo-login', 'uses' => 'Api\BusinessSegmentController@demoLogin']);
        Route::post('/reset-password', ['as' => 'api.business-segment.reset-password', 'uses' => 'BusinessSegment\Api\AccountController@resetPassword']);
        Route::post('/forgot-password', ['as' => 'api.business-segment.password', 'uses' => 'BusinessSegment\Api\AccountController@ForgotPassword']);
    });

    Route::group(['middleware' => ['auth:business-segment-api','valid_business_segment']], function () {
        Route::post('/out-board', ['as' => 'bs.business-segment-logout', 'uses' => 'BusinessSegment\Api\AccountController@logout']);
        Route::post('/edit-profile', ['as' => 'bs.business-segment-profile', 'uses' => 'BusinessSegment\Api\AccountController@editProfile']);
        Route::post('/get-orders', ['as' => 'bs.all-orders', 'uses' => 'BusinessSegment\Api\OrderController@getOrders']);
        Route::post('/get-order-details', ['as' => 'bs.order-details', 'uses' => 'BusinessSegment\Api\OrderController@getOrderDetails']);
        Route::post('/get-products', ['as' => 'bs.all-products', 'uses' => 'BusinessSegment\Api\OrderController@getProducts']);
        Route::post('/get-product-details', ['as' => 'bs.product-details', 'uses' => 'BusinessSegment\Api\OrderController@getProductDetails']);

        Route::post('/get-product-step1', ['as' => 'bs.get-product-step1', 'uses' => 'BusinessSegment\Api\OrderController@productBasicStep']);
        Route::post('/save-product-step1', ['as' => 'bs.add-product', 'uses' => 'BusinessSegment\Api\OrderController@saveProductBasicStep']);

        Route::post('/get-product-step2', ['as' => 'bs.get-product-step2', 'uses' => 'BusinessSegment\Api\OrderController@productVariantStep']);
        Route::post('/save-product-step2', ['as' => 'bs.add-product-step2', 'uses' => 'BusinessSegment\Api\OrderController@saveProductVariantStep']);

        Route::post('/get-product-step3', ['as' => 'bs.get-product-step3', 'uses' => 'BusinessSegment\Api\OrderController@productInventoryStep']);
        Route::post('/save-product-step3', ['as' => 'bs.add-product-step3', 'uses' => 'BusinessSegment\Api\OrderController@saveProductInventoryStep']);

        Route::post('/get-sub-categories', ['as' => 'bs.categories', 'uses' => 'BusinessSegment\Api\OrderController@getSubCategories']);

        Route::post('/get-drivers', ['as' => 'bs.choose-delivery-boy', 'uses' => 'BusinessSegment\Api\OrderController@getDrivers']);
        Route::post('/send-request-to-drivers', ['as' => 'bs.send-order-request', 'uses' => 'BusinessSegment\Api\OrderController@sendOrderRequestToDriver']);
        Route::post('/cancel-order', ['as' => 'bs.cancel-order', 'uses' => 'BusinessSegment\Api\OrderController@cancelOrder']);
        Route::post('/reject-order', ['as' => 'bs.reject-order', 'uses' => 'BusinessSegment\Api\OrderController@rejectOrder']);
        Route::post('/pickup-order-otp-verification', ['as' => 'bs.pickup-otp-verification', 'uses' => 'BusinessSegment\Api\OrderController@orderPickupOTPVerification']);
        Route::post('/process-order', ['as' => 'bs.process-order', 'uses' => 'BusinessSegment\Api\OrderController@processOrder']);
        Route::post('/cancel-reasons', ['as' => 'bs.cancel.reason', 'uses' => 'BusinessSegment\Api\OrderController@orderCancelReason']);
        Route::post('/auto-assign-driver', ['as' => 'bs.auto-order-assign', 'uses' => 'BusinessSegment\Api\OrderController@autoAssignDriver']);
        Route::post('/manual-assign-driver', ['as' => 'bs.manual-order-assign', 'uses' => 'BusinessSegment\Api\OrderController@manualAssignDriver']);
        Route::post('/update-product-status', ['as' => 'bs-update-product-status', 'uses' => 'BusinessSegment\Api\OrderController@updateProductStatus']);
        Route::post('/get-styles', ['as' => 'bs-get-styles', 'uses' => 'BusinessSegment\Api\AccountController@getStyle']);
        Route::post('/save-styles', ['as' => 'bs-save-styles', 'uses' => 'BusinessSegment\Api\AccountController@saveStyle']);
        Route::post('/get-options-types', ['as' => 'bs.get-options-types', 'uses' => 'BusinessSegment\Api\OptionController@getOptionTypes']);
        Route::post('/add-option', ['as' => 'bs.add-option', 'uses' => 'BusinessSegment\Api\OptionController@addOption']);
        Route::post('/get-options', ['as' => 'bs.get-options', 'uses' => 'BusinessSegment\Api\OptionController@getOptions']);
        Route::post('/delete-option', ['as' => 'bs.delete-option', 'uses' => 'BusinessSegment\Api\OptionController@deleteOption']);
        Route::post('/get-wallet-transactions', ['as' => 'bs.get-wallet-transactions', 'uses' => 'BusinessSegment\Api\WalletTransactionController@getTransactions']);
        Route::post('/get-cashout-transactions', ['as' => 'bs.get-cashout-transactions', 'uses' => 'BusinessSegment\Api\WalletTransactionController@getCashoutTransactions']);
        Route::post('/request-cashout', ['as' => 'bs.request-cashout', 'uses' => 'BusinessSegment\Api\WalletTransactionController@requestCashout']);
        Route::post('/get-order-statistics', ['as' => 'bs.get-order-statistics', 'uses' => 'BusinessSegment\Api\OrderController@getOrderStatistics']);
        Route::post('/get-earnings', ['as' => 'bs.get-earnings', 'uses' => 'BusinessSegment\Api\OrderController@getEarnings']);
        Route::post('/get-configurations', ['as' => 'bs-get-configurations', 'uses' => 'BusinessSegment\Api\AccountController@getConfigurations']);
        Route::post('/save-options', ['as' => 'bs.save-options', 'uses' => 'BusinessSegment\Api\OrderController@optionStepSave']);
        Route::post('/get-product-options', ['as' => 'bs.get-product-options', 'uses' => 'BusinessSegment\Api\OrderController@getProductOptions']);
    });
});
