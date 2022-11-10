<?php
Route::get('driver/locaion', ['as' => 'driverTrack', 'uses' => 'Merchant\DriverController@driver_location']);
Route::get('share/ride/{code}', ['as' => 'ride.share', 'uses' => 'Merchant\RideShareController@index']);
//Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('return-dpo', 'Merchant\DashBoardController@returndpo');
Route::get('migrateData', 'ImportController@test');
Route::get('redirectPeach', 'PaymentMethods\RandomPaymentController@redirectPeach')->name('redirectPeach');



/*Cron Job start */
Route::get('/per-minute-functionalities', 'CronJob\CronController@perMinuteCron');
Route::get('/every-day-functionalities', 'CronJob\CronController@perDayCron');
Route::get('/give-permission-super-admin', 'Merchant\DashBoardController@givePermissionToSuperAdmin');
//test cron
Route::get('/per-minute-func', 'CronJob\PerMinuteCronController@checkCron');
/*Cron Job end */


/* Twilio Whatsapp */
//header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Credentials, Access-Control-Allow-Origin, Access-Control-Allow-Methods, Access-Control-Allow-Headers, aliasName, publicKey, secretKey, locale');
Route::post('whatsapp-message', 'Merchant\WhatsappController@newMessage');
Route::post('message-status', 'Merchant\WhatsappController@messageStatus');


Route::get('mercado-webpage','Merchant\DashBoardController@mercadoPage');



Route::get('paymentfail',function(){
    return 'failed';
});
Route::get('paymentcomplate', function () {
    return 'done';
});

/* Clear cache of laravel manually*/
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "Cache is cleared";
});

Route::get('/queue-start', function () {
    Artisan::call('queue:listen');
    return "Queue started";
});

Route::get('/queue-restart', function () {
    Artisan::call('queue:restart');
    return "Queue now restarted";
});

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', function () {
    return view('welcome');
});
Route::get('/404', function () {
    //return view('apporio');
    return view('404');
})->name('404');


Route::prefix('taxicompany/admin')->group(function () {
    Route::group(['middleware' => ['guest:taxicompany']], function () {
        Route::get('{merchant_alias_name}/{alias}/login', 'Auth\TaxicompanyLoginController@showloginform')->name('taxicompany.login');
        Route::post('/login/{alias}','Auth\TaxicompanyLoginController@login')->name('taxicompany.login.submit');
    });
    Route::group(['middleware' => ['auth:taxicompany','admin_language']], function () {

        // update locale
        Route::get('/change-language/{locale}', ['as' => 'taxicompany.language', 'uses' => 'Merchant\DashBoardController@SetLangauge']);

        Route::get('/dashboard', 'Taxicompany\TaxicompanyController@dashboard')->name('taxicompany.dashboard');
        Route::get('/edit_profile', ['as' => 'taxicompany.profile', 'uses' => 'Taxicompany\TaxicompanyController@Profile']);
        Route::post('/edit_profile', ['as' => 'taxicompany.profile.submit', 'uses' => 'Taxicompany\TaxicompanyController@UpdateProfile']);
        Route::get('/transaction', ['as' => 'taxicompany.transaction', 'uses' => 'Taxicompany\TransactionController@index']);
        Route::post('/transaction', ['as' => 'taxicompany.transaction.search', 'uses' => 'Taxicompany\TransactionController@Search']);
        Route::get('/logout', 'Auth\TaxicompanyLoginController@logout')->name('taxicompany.logout');
        Route::get('/wallet', ['as' => 'taxicompany.wallet', 'uses' => 'Taxicompany\TaxicompanyController@Wallet']);

        Route::post('/users/search', ['as' => 'users.search', 'uses' => 'Taxicompany\UserController@search']);
        Route::get('/user/wallet/{id}', ['as' => 'taxicompany.user.wallet', 'uses' => 'Taxicompany\UserController@Wallet']);
        Route::post('/user/addmoney', ['as' => 'taxicompany.user.add.wallet', 'uses' => 'Taxicompany\UserController@AddWalletMoney']);
        Route::resource('/users', 'Taxicompany\UserController', ['as' => 'taxicompany']);


        Route::resource('account', 'Taxicompany\DriverAccountController');

        /** driver module start **/
        // get drivers
        Route::get('/driver', ['as' => 'taxicompany.driver.index', 'uses' => 'Taxicompany\DriverController@index']);
        // add driver
        Route::get('/driver/add/{id?}', ['as' => 'taxicompany.driver.add', 'uses' => 'Taxicompany\DriverController@add']);
        // save driver
        Route::post('/driver/save/{id?}', ['as' => 'taxicompany.driver.save', 'uses' => 'Taxicompany\DriverController@save']);
        // view driver
        Route::get('/driver/profile/{id}', ['as' => 'taxicompany.driver.show', 'uses' => 'Taxicompany\DriverController@show']);

        /** driver module end **/

        Route::get('/country/config', ['as' => 'taxicompany.country.config', 'uses' => 'Taxicompany\DriverController@CountryConfig']);
        Route::post('/driver/personal-document', ['as' => 'taxicompany.driver.country-area-document', 'uses' => 'Taxicompany\DriverController@getPersonalDocument']);

        /** driver vehicle module start **/
        Route::get('/driver/add-vehicle/{id}/{vehicle_id?}', ['as' => 'taxicompany.driver.vehicle.create', 'uses' => 'Taxicompany\DriverController@addVehicle']);
        Route::post('/driver/save-vehicle/{id}', ['as' => 'taxicompany.driver.vehicle.store', 'uses' => 'Taxicompany\DriverController@saveVehicle']);
//        Route::get('/driver/delete/pending-vehicle/{id?}', ['as' => 'taxicompany.delete.pendingvehicle', 'uses' => 'Taxicompany\DriverController@DeletePendingVehicle']);
        Route::get('/vehicle/details/{id}', ['as' => 'taxicompany.driver-vehicledetails', 'uses' => 'Taxicompany\DriverController@VehiclesDetail']);
        /** driver vehicle module end **/

        Route::get('/drivers/basicses/', ['as' => 'taxicompany.driver.basics', 'uses' => 'Taxicompany\DriverController@NewDriver']);
//        Route::resource('/drivers', 'Taxicompany\DriverController', ['as' => 'Taxicompany']);
        Route::get('/drivers/basic/search', ['as' => 'taxicompany.driver.basic.search', 'uses' => 'Taxicompany\DriverController@NewDriverSearch']);
        Route::get('/driver/document/{id}', ['as' => 'taxicompany.driver.document.show', 'uses' => 'Taxicompany\DriverController@ShowDocument']);
        Route::post('/driver/document/{id}', ['as' => 'taxicompany.driver.document.store', 'uses' => 'Taxicompany\DriverController@StoreDocument']);
//        Route::get('/driver/vehicletype/{id}', ['as' => 'taxicompany.driver.vehicle.create', 'uses' => 'Taxicompany\DriverController@CreateVehicle']);
//        Route::post('/driver/vehicletype/{id}', ['as' => 'taxicompany.driver.vehicle.store', 'uses' => 'Taxicompany\DriverController@StoreVehicle']);
        Route::get('/driver/vehicle/{id}', ['as' => 'taxicompany.driver-vehicle', 'uses' => 'Taxicompany\DriverController@Vehicles']);
        Route::get('/driver/active/deactive/{id}/{status}', ['as' => 'taxicompany.driver.active.deactive', 'uses' => 'Taxicompany\DriverController@ChangeStatus']);
        Route::post('/ajax/servicess', ['as' => 'taxicompany.ajax.servicess', 'uses' => 'Helper\AjaxController@VehicleServices']);
        Route::post('/ajax/vehiclemodels', ['as' => 'taxicompany.ajax.vehiclemodels', 'uses' => 'Helper\AjaxController@VehicleModel']);
//        Route::get('/drivers/serach/', ['as' => 'taxicompany.driver.search', 'uses' => 'Taxicompany\DriverController@Serach']);
        Route::post('/drivers/delete/', ['as' => 'taxicompany.drivers.delete', 'uses' => 'Taxicompany\DriverController@destroy']);
        Route::post('/promotions/send/driver', ['as' => 'taxicompany.sendsingle-driver', 'uses' => 'Taxicompany\DriverController@SendNotificationDriver']);
        Route::get('/coutry/areaList', ['as' => 'taxicompany.country.arealist', 'uses' => 'Taxicompany\DriverController@AreaList']);
        Route::get('/allvehicles/', ['as' => 'taxicompany.driver.allvehicles', 'uses' => 'Taxicompany\DriverController@AllVehicle']);
        Route::get('/accounts/search/', ['as' => 'taxicompany.account.search', 'uses' => 'Taxicompany\DriverAccountController@Search']);
        Route::get('/drivers/temp/doc/pending/', ['as' => 'taxicompany.driver.tempDocPending.show', 'uses' => 'Taxicompany\DriverController@TempDocPending']);

        Route::get('/heatmaps', ['as' => 'taxicompany.heatmap', 'uses' => 'Taxicompany\MapController@HeatMap']);
        Route::get('/drivermaps', ['as' => 'taxicompany.drivermap', 'uses' => 'Taxicompany\MapController@DriverMap']);
        Route::post('/getDriverOnMap', ['as' => 'taxicompany.getDriverOnMap', 'uses' => 'Taxicompany\ManualDispatchController@getDriverOnMap']);

        Route::get('/manual-dispatch', ['as' => 'taxicompany.test.manualdispatch', 'uses' => 'Taxicompany\ManualDispatchController@TestIndex']);
        Route::post('/checkArea', ['as' => 'taxicompany.checkArea', 'uses' => 'Taxicompany\ManualDispatchController@checkArea']);
        Route::get('/manualdispach', ['as' => 'taxicompany.manualdispatch', 'uses' => 'Taxicompany\ManualDispatchController@index']);
        Route::post('/manualdispach', ['as' => 'taxicompany.book.manual.dispatch', 'uses' => 'Taxicompany\ManualDispatchController@BookingDispatch']);
        Route::post('/SearchUser', ['as' => 'taxicompany.SearchUser', 'uses' => 'Taxicompany\ManualDispatchController@SearchUser']);
        Route::post('/getPromoCode', ['as' => 'taxicompany.getPromoCode', 'uses' => 'Taxicompany\ManualDispatchController@PromoCode']);
        Route::post('/getPromoCodeEta', ['as' => 'taxicompany.getPromoCodeEta', 'uses' => 'Taxicompany\ManualDispatchController@PromoCodeEta']);
        Route::post('/estimatePrice', ['as' => 'taxicompany.estimatePrice', 'uses' => 'Taxicompany\ManualDispatchController@EstimatePrice']);
        Route::post('/checkDriver', ['as' => 'taxicompany.checkDriver', 'uses' => 'Taxicompany\ManualDispatchController@CheckDriver']);
        Route::post('/getallDriverForManual', ['as' => 'taxicompany.getallDriver', 'uses' => 'Taxicompany\ManualDispatchController@AllDriver']);
        Route::post('/AddManualUser', ['as' => 'taxicompany.AddManualUser', 'uses' => 'Taxicompany\ManualDispatchController@AddManualUser']);
        Route::any('/findNearDriver', ['as' => 'BookingStatusWaiting', 'uses' => 'Taxicompany\BookingController@checkBookingStatusWaiting']);
        Route::get('/transactions/billdetails', ['as' => 'taxicompany.billdetails.search', 'uses' => 'Taxicompany\TransactionController@GetBillDetails']);


        //ajax route
        Route::post('/ajax/area', ['as' => 'taxicompany.ajax.area', 'uses' => 'Helper\AjaxController@AreaList']);
        Route::post('/getRideConfig', ['as' => 'taxicompany.getRideConfig', 'uses' => 'Helper\AjaxController@VehicleConfig']);
        Route::post('/getServices', ['as' => 'taxicompany.area.services', 'uses' => 'Helper\AjaxController@ServiceType']);
        Route::post('/getVehicle', ['as' => 'taxicompany.vehicles', 'uses' => 'Helper\AjaxController@VehicleType']);
        Route::post('/checkPriceCard', ['as' => 'taxicompany.checkPriceCard', 'uses' => 'Helper\AjaxController@PriceCard']);

//        Route::post('/getAllPriceCard', ['as' => 'getAllPriceCard', 'uses' => 'Helper\AjaxController@GetPriceCard']);
//        Route::post('/ajax/vehiclemodel', ['as' => 'taxicompany.ajax.vehiclemodel', 'uses' => 'Helper\AjaxController@VehicleModel']);
//        Route::post('/ajax/services', ['as' => 'taxicompany.ajax.services', 'uses' => 'Helper\AjaxController@VehicleServices']);
//        Route::post('/getServicescashback', ['as' => 'taxicompany.area.servicescashback', 'uses' => 'Helper\AjaxController@ServiceTypeCashBack']);
//        Route::post('/getVehicletypescashback', ['as' => 'taxicompany.area.vehicletypescashback', 'uses' => 'Helper\AjaxController@VehicleTypeCashBack']);
//        Route::get('/cashback/change_status/{id}/{status}', 'Merchant\CashbackController@Change_Status')->name('cashback.changestatus');
//        Route::resource('cashback', 'Merchant\CashbackController');
//        Route::post('/checkPool', ['as' => 'taxicompany.area.checkPool', 'uses' => 'Helper\AjaxController@CheckPool']);

        Route::get('/booking/track/{id}', ['as' => 'taxicompany.activeride.track', 'uses' => 'Taxicompany\BookingController@ActiveBookingTrack']);
        Route::get('/booking/activeride', ['as' => 'taxicompany.activeride', 'uses' => 'Taxicompany\BookingController@index']);
        Route::post('/booking/activeride', ['as' => 'taxicompany.activeride.serach', 'uses' => 'Taxicompany\BookingController@SearchForActiveRide']);
        Route::get('/booking/autocancel', ['as' => 'taxicompany.autocancel', 'uses' => 'Taxicompany\BookingController@AutoCancel']);
        Route::post('/booking/autocancel', ['as' => 'taxicompany.autocancel.serach', 'uses' => 'Taxicompany\BookingController@SearchForAutoCancel']);
        Route::get('/booking/all', ['as' => 'taxicompany.all.ride', 'uses' => 'Taxicompany\BookingController@AllRides']);
        Route::post('/booking/all', ['as' => 'taxicompany.all.serach', 'uses' => 'Taxicompany\BookingController@SearchForAllRides']);
        Route::get('/booking/activeride/search', ['as' => 'taxicompany.activeride.later', 'uses' => 'Taxicompany\BookingController@SearchForActiveLaterRide']);
        Route::post('/booking/activeride/search', ['as' => 'taxicompany.activeride.later.serach', 'uses' => 'Taxicompany\BookingController@SearchForActiveLaterRide']);
        Route::get('/booking/cancel', ['as' => 'taxicompany.cancelride', 'uses' => 'Taxicompany\BookingController@CancelBooking']);
        Route::get('/booking/cancel/search', ['as' => 'taxicompany.cancelride.search', 'uses' => 'Taxicompany\BookingController@SearchCancelBooking']);
        Route::get('/booking/complete', ['as' => 'taxicompany.completeride', 'uses' => 'Taxicompany\BookingController@CompleteBooking']);
        Route::post('/booking/complete/search', ['as' => 'taxicompany.completeride.search', 'uses' => 'Taxicompany\BookingController@SerachCompleteBooking']);
        Route::get('/booking/failride', ['as' => 'taxicompany.failride', 'uses' => 'Taxicompany\BookingController@FailedBooking']);
        Route::post('/booking/failride', ['as' => 'taxicompany.failride.search', 'uses' => 'Taxicompany\BookingController@SearchFailedBooking']);
        Route::post('/booking/cancelbooking', ['as' => 'taxicompany.cancelbooking', 'uses' => 'Taxicompany\BookingController@CancelBookingAdmin']);
        Route::get('/booking/{id}', ['as' => 'taxicompany.booking.details', 'uses' => 'Taxicompany\BookingController@BookingDetails']);
        Route::get('/booking/invoice/{id}', ['as' => 'taxicompany.booking.invoice', 'uses' => 'Taxicompany\BookingController@Invoice']);
        Route::get('/ride/request/{id}', ['as' => 'taxicompany.ride-requests', 'uses' => 'Taxicompany\BookingController@DriverRequest']);
        Route::get('/ride/requestRides/{id}', ['as' => 'taxicompany.requestRides', 'uses' => 'Taxicompany\BookingController@requestRides']);
        Route::any('/findNearDriver', ['as' => 'BookingStatusWaiting', 'uses' => 'Taxicompany\BookingController@checkBookingStatusWaiting']);
        Route::post('/price-card-service-config', ['as' => 'taxicompany.price.card.service.config', 'uses' => 'Helper\AjaxController@ServiceConfig']);

        Route::get('/ratings', ['as' => 'taxicompany.ratings', 'uses' => 'Taxicompany\TaxicompanyController@Ratings']);
        Route::post('/ratings', ['as' => 'taxicompany.ratings.search', 'uses' => 'Taxicompany\TaxicompanyController@SearchRating']);

        Route::post('checkOutstationDropArea', ['as' => 'taxicompany.manual.checkArea', 'uses' => 'Taxicompany\ManualDispatchController@checkOutstationDropArea']);
    });
});

Route::prefix('franchise/admin')->group(function () {
    Route::group(['middleware' => ['guest:franchise']], function () {
        Route::get('{merchant_alias_name}/{alias}/login', 'Auth\FranchiseLoginController@showLoginForm')->name('franchise.login');
        Route::post('/login/{alias}', 'Auth\FranchiseLoginController@login')->name('franchise.login.submit');
    });
    Route::group(['middleware' => ['auth:franchise','admin_language']], function () {
        Route::get('/dashboard', ['as' => 'franchise.dashboard', 'uses' => 'Franchise\DashBoardController@index']);
        Route::get('/logout', ['as' => 'franchise.logout', 'uses' => 'Auth\FranchiseLoginController@logout']);
        Route::get('/manualdispach', ['as' => 'franchise.manualdispach', 'uses' => 'Franchise\ManualDispatchController@index']);
        Route::post('/SearchUser', ['as' => 'franchise.SearchUser', 'uses' => 'Franchise\ManualDispatchController@SearchUser']);
        Route::resource('user', 'Franchise\UserController');
        Route::resource('franchise-driver', 'Franchise\DriverController');
        Route::get('/driver/document/{id}', ['as' => 'franchise.driver.document.show', 'uses' => 'Franchise\DriverController@ShowDocument']);
        Route::post('/driver/document/{id}', ['as' => 'franchise.driver.document.store', 'uses' => 'Franchise\DriverController@StoreDocument']);
        Route::get('/driver/vehicletype/{id}', ['as' => 'franchise.driver.vehicle.create', 'uses' => 'Franchise\DriverController@CreateVehicle']);
        Route::post('/driver/vehicletype/{id}', ['as' => 'franchise.driver.vehicle.store', 'uses' => 'Franchise\DriverController@StoreVehicle']);
        Route::post('/ajax/vehiclemodel', ['as' => 'franchise.ajax.vehiclemodel', 'uses' => 'Helper\AjaxController@VehicleModel']);
        Route::post('/ajax/services', ['as' => 'franchise.ajax.services', 'uses' => 'Helper\AjaxController@VehicleServices']);
        Route::post('users/serach', ['as' => 'franchise.user.search', 'uses' => 'Franchise\UserController@Serach']);
        Route::post('/AddManualUser', ['as' => 'franchise.AddManualUser', 'uses' => 'Franchise\ManualDispatchController@AddManualUser']);
        Route::post('/getServices', ['as' => 'merchant.area.services', 'uses' => 'Helper\AjaxController@ServiceType']);
        Route::post('/getRideConfig', ['as' => 'merchant.getRideConfig', 'uses' => 'Helper\AjaxController@VehicleConfig']);
        Route::post('/getVehicle', ['as' => 'package.vehicles', 'uses' => 'Helper\AjaxController@VehicleType']);
        Route::post('/estimatePrice', ['as' => 'merchant.estimatePrice', 'uses' => 'Merchant\ManualDispatchController@EstimatePrice']);
        Route::post('/checkPriceCard', ['as' => 'merchant.checkPriceCard', 'uses' => 'Helper\AjaxController@PriceCard']);
        Route::post('/checkDriver', ['as' => 'merchant.checkDriver', 'uses' => 'Merchant\ManualDispatchController@CheckDriver']);
        Route::post('/manualdispach', ['as' => 'franchise.book.manual.dispach', 'uses' => 'Franchise\ManualDispatchController@BookingDispatch']);
        Route::get('/ride/request/{id}', ['as' => 'franchise.ride-requests', 'uses' => 'Franchise\BookingController@DriverRequest']);


        Route::get('/booking/activeride', ['as' => 'franchise.activeride', 'uses' => 'Franchise\BookingController@index']);
        Route::post('/booking/activeride', ['as' => 'franchise.activeride.serach', 'uses' => 'Franchise\BookingController@SearchForActiveRide']);
        Route::post('/booking/activeride/search', ['as' => 'franchise.activeride.later.serach', 'uses' => 'Franchise\BookingController@SearchForActiveLaterRide']);
        Route::get('/booking/cancel', ['as' => 'franchise.cancelride', 'uses' => 'Franchise\BookingController@CancelBooking']);
        Route::post('/booking/cancel/search', ['as' => 'franchise.cancelride.search', 'uses' => 'Franchise\BookingController@SearchCancelBooking']);
        Route::get('/booking/complete', ['as' => 'franchise.completeride', 'uses' => 'Franchise\BookingController@CompleteBooking']);
        Route::post('/booking/complete/search', ['as' => 'franchise.completeride.search', 'uses' => 'Franchise\BookingController@SerachCompleteBooking']);

        Route::post('/booking/cancelbooking', ['as' => 'franchise.cancelbooking', 'uses' => 'Franchise\BookingController@CancelBookingAdmin']);
        Route::get('/booking/{id}', ['as' => 'franchise.booking.details', 'uses' => 'Franchise\BookingController@BookingDetails']);
        Route::get('/booking/invoice/{id}', ['as' => 'franchise.booking.invoice', 'uses' => 'Franchise\BookingController@Invoice']);

        Route::get('/ratings', ['as' => 'franchise.ratings', 'uses' => 'Franchise\DashBoardController@Ratings']);
        Route::post('/ratings', ['as' => 'franchise.ratings.search', 'uses' => 'Franchise\DashBoardController@SearchRating']);

        Route::get('/transactions', ['as' => 'franchise.transactions', 'uses' => 'Franchise\TransactionController@index']);
        Route::post('/transactions', ['as' => 'franchise.transactions.search', 'uses' => 'Franchise\TransactionController@Search']);


    });
});

Route::prefix('hotel/admin')->group(function () {//hotel
    Route::group(['middleware' => ['guest:hotel']], function () {
        Route::get('{merchant_alias_name}/{alias}/login', 'Auth\HotelLoginController@showLoginForm')->name('hotel.login');
        Route::post('/login/{alias}', 'Auth\HotelLoginController@login')->name('hotel.login.submit');
    });
    Route::group(['middleware' => ['auth:hotel','admin_language']], function () {
        Route::get('/dashboard', ['as' => 'hotel.dashboard', 'uses' => 'Hotel\DashBoardController@index']);
        Route::get('/logout', ['as' => 'hotel.logout', 'uses' => 'Auth\HotelLoginController@logout']);
        Route::get('/edit_profile', ['as' => 'hotel.profile', 'uses' => 'Hotel\DashBoardController@Profile']);
        Route::post('/edit_profile', ['as' => 'hotel.profile.submit', 'uses' => 'Hotel\DashBoardController@UpdateProfile']);
        Route::get('/wallet', ['as' => 'hotel.wallet', 'uses' => 'Hotel\DashBoardController@Wallet']);

        Route::get('/manual-dispatch', ['as' => 'hotel.test.manualdispach', 'uses' => 'Hotel\ManualDispatchController@TestIndex']);
        Route::post('/checkArea', ['as' => 'hotel.checkArea', 'uses' => 'Hotel\ManualDispatchController@checkArea']);

        Route::get('/manualdispach', ['as' => 'hotel.manualdispatch', 'uses' => 'Hotel\ManualDispatchController@index']);
        Route::post('/manualdispach', ['as' => 'hotel.book.manual.dispatch', 'uses' => 'Hotel\ManualDispatchController@BookingDispatch']);

        Route::post('/SearchUser', ['as' => 'hotel.SearchUser', 'uses' => 'Hotel\ManualDispatchController@SearchUser']);
        Route::post('/AddManualUser', ['as' => 'hotel.AddManualUser', 'uses' => 'Hotel\ManualDispatchController@AddManualUser']);
        Route::post('/checkPriceCard', ['as' => 'hotel.checkPriceCard', 'uses' => 'Helper\AjaxController@PriceCard']);
        Route::post('/checkPriceCard', ['as' => 'hotel.checkPriceCard', 'uses' => 'Helper\AjaxController@PriceCard']);
        Route::post('/ajax/area', ['as' => 'hotel.ajax.area', 'uses' => 'Helper\AjaxController@AreaList']);
        Route::post('/ajax/vehiclemodel', ['as' => 'ajax.vehiclemodel', 'uses' => 'Helper\AjaxController@VehicleModel']);
        Route::post('/ajax/services', ['as' => 'ajax.services', 'uses' => 'Helper\AjaxController@VehicleServices']);
        Route::post('/getRideConfig', ['as' => 'hotel.getRideConfig', 'uses' => 'Helper\AjaxController@VehicleConfig']);
        Route::post('/getServices', ['as' => 'hotel.area.services', 'uses' => 'Helper\AjaxController@ServiceType']);
        Route::post('/getVehicle', ['as' => 'package.vehicles', 'uses' => 'Helper\AjaxController@VehicleType']);
        Route::post('/estimatePrice', ['as' => 'hotel.estimatePrice', 'uses' => 'Hotel\ManualDispatchController@EstimatePrice']);
        Route::post('/checkDriver', ['as' => 'hotel.checkDriver', 'uses' => 'Hotel\ManualDispatchController@CheckDriver']);
        Route::post('/price-card-service-config', ['as' => 'hotel.price.card.service.config', 'uses' => 'Helper\AjaxController@ServiceConfig']);

        Route::post('/getPromoCode', ['as' => 'hotel.getPromoCode', 'uses' => 'Hotel\ManualDispatchController@PromoCode']);
        Route::post('/getPromoCodeEta', ['as' => 'hotel.getPromoCodeEta', 'uses' => 'Hotel\ManualDispatchController@PromoCodeEta']);
        Route::post('/getDriverOnMap', ['as' => 'hotel.getDriverOnMap', 'uses' => 'Hotel\ManualDispatchController@getDriverOnMap']);

        Route::get('/ride/request/{id}', ['as' => 'hotel.ride-requests', 'uses' => 'Hotel\BookingController@DriverRequest']);
        Route::post('/getallDriverForManual', ['as' => 'hotel.getallDriver', 'uses' => 'Hotel\ManualDispatchController@AllDriver']);

//bookingfranchise.ride-requests
        Route::get('/booking/activeride', ['as' => 'hotel.activeride', 'uses' => 'Hotel\BookingController@index']);
        Route::post('/booking/activeride', ['as' => 'hotel.activeride.serach', 'uses' => 'Hotel\BookingController@SearchForActiveRide']);
        Route::post('/booking/activeride/search', ['as' => 'hotel.activeride.later.serach', 'uses' => 'Hotel\BookingController@SearchForActiveLaterRide']);
        Route::get('/booking/cancel', ['as' => 'hotel.cancelride', 'uses' => 'Hotel\BookingController@CancelBooking']);
        Route::post('/booking/cancel/search', ['as' => 'hotel.cancelride.search', 'uses' => 'Hotel\BookingController@SearchCancelBooking']);
        Route::get('/booking/complete', ['as' => 'hotel.completeride', 'uses' => 'Hotel\BookingController@CompleteBooking']);
        Route::post('/booking/complete/search', ['as' => 'hotel.completeride.search', 'uses' => 'Hotel\BookingController@SerachCompleteBooking']);
        Route::get('/booking/all', ['as' => 'hotel.allrides', 'uses' => 'Hotel\BookingController@AllRides']);
        Route::get('/booking/all/search', ['as' => 'hotel.allrides.search', 'uses' => 'Hotel\BookingController@SearchForAllRides']);

        Route::post('/booking/cancelbooking', ['as' => 'hotel.cancelbooking', 'uses' => 'Hotel\BookingController@CancelBookingAdmin']);
        Route::get('/booking/{id}', ['as' => 'hotel.booking.details', 'uses' => 'Hotel\BookingController@BookingDetails']);
        Route::get('/booking/invoice/{id}', ['as' => 'hotel.booking.invoice', 'uses' => 'Hotel\BookingController@Invoice']);


        Route::get('/ratings', ['as' => 'hotel.ratings', 'uses' => 'Hotel\DashBoardController@Ratings']);
        Route::post('/ratings', ['as' => 'hotel.ratings.search', 'uses' => 'Hotel\DashBoardController@SearchRating']);

        Route::post('checkOutstationDropArea', ['as' => 'hotel.manual.checkArea', 'uses' => 'Hotel\ManualDispatchController@checkOutstationDropArea']);
    });
});

Route::prefix('merchant/admin')->group(function () {
    Route::group(['middleware' => ['guest:merchant']], function () {
        Route::get('{alias_name}/login', 'Auth\MerchantLoginController@showLoginForm')->name('merchant.login');
        Route::post('/login/{alias_name}', 'Auth\MerchantLoginController@login')->name('merchant.login.submit');
    });
    // logs of system
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    Route::group(['middleware' => ['auth:merchant', 'isactiveuser','admin_language']], function () {
        Route::get('/test-referral', 'Helper\ReferralController@testReferralSystem');




        // reward points routes
        Route:: resource('reward-points', 'Merchant\RewardController');
        // tutu - cancel rate
        Route:: get('cancelrate', 'Merchant\CancelRateController@index')->name('merchant.cancelrate');
        Route:: get('cancelrate/create', 'Merchant\CancelRateController@create')->name('merchant.cancelrate.create');
        Route:: get('cancelrate/{id}/edit', 'Merchant\CancelRateController@edit')->name('merchant.cancelrate.edit');
        Route:: post('cancelrate/store', 'Merchant\CancelRateController@store')->name('merchant.cancelrate.store');
        Route:: put('cancelrate/{id}/update', 'Merchant\CancelRateController@update')->name('merchant.cancelrate.update');
        Route:: post('cancelrate/{id}/destroy', 'Merchant\CancelRateController@destroy')->name('merchant.cancelrate.destroy');


        //end

        Route::get('/sendinvoice/{id}', ['as' => 'admin.sendinvoice', 'uses' => 'Merchant\BookingController@bookingInvoiceSend']);
        Route::get('taxicompany/statusupdate/{id}', 'Merchant\TaxiCompanyController@statusupdate')->name('taxicompany.status');

//        Route::resource('taxicompany', 'Merchant\TaxiCompanyController');
        Route::get('taxi-company', ['as' =>'merchant.taxi-company','uses' =>  'Merchant\TaxiCompanyController@index']);
        Route::get('taxi-company/add/{id?}', ['as' => 'merchant.taxi-company.add','uses' => 'Merchant\TaxiCompanyController@add']);
        Route::post('taxi-company/save/{id?}', ['as' => 'merchant.taxi-company.save','uses' => 'Merchant\TaxiCompanyController@save']);

        Route::post('/taxicompany/AddMoney', ['as' => 'taxicompany.AddMoney', 'uses' => 'Merchant\TaxiCompanyController@AddMoney']);
        Route::get('/taxicompany/wallet/{id}', ['as' => 'merchant.taxicompany.wallet.show', 'uses' => 'Merchant\TaxiCompanyController@Wallet']);
        Route::get('/taxicompany/transactions/{id}', ['as' => 'merchant.taxicompany.transactions', 'uses' => 'Merchant\TransactionController@TaxiCompanyTransaction']);
        Route::post('/taxicompany/transactions/{id}', ['as' => 'merchant.taxicompany.transactions.search', 'uses' => 'Merchant\TransactionController@TaxiCompanySearch']);

        Route::resource('/busBooking', 'Merchant\BusController');
        Route::resource('/website-user-home-headings', 'Merchant\WebsiteUserHomeController', ['only' => ['index', 'edit', 'store']]);
        Route::resource('/website-driver-home-headings', 'Merchant\WebsiteDriverHomeController', ['only' => ['index', 'edit', 'store']]);
//        Route::resource('/weightunit', 'Merchant\WeightUnitController');
        Route::get('weight-unit', ['as' =>'weightunit.index','uses' =>  'Merchant\WeightUnitController@index']);
        Route::get('weight-unit/add/{id?}', ['as' => 'weightunit.add','uses' => 'Merchant\WeightUnitController@add']);
        Route::post('weight-unit/save/{id?}', ['as' => 'weightunit.save','uses' => 'Merchant\WeightUnitController@save']);
        Route::post('weight-unit/delete/{id?}', ['as' => 'weightunit.destroy','uses' => 'Merchant\WeightUnitController@save']);


//        Route::resource('subscription', 'Merchant\SubscriptionController');
//        Route::get('/subscription/', 'Merchant\SubscriptionController@index');
        Route::get('/subscription/add/{id?}', 'Merchant\SubscriptionController@add');
        Route::post('/subscription/save/{id?}', 'Merchant\SubscriptionController@save');
        Route::resource('subscription', 'Merchant\SubscriptionController');

        Route::get('/subscription/change_status/{id}/{status}', 'Merchant\SubscriptionController@Change_Status')->name('subscription.changepackstatus');
        Route::post('/web-playerid-subscription', 'Merchant\DashBoardController@webPlayerIdSubscription')->name('merchant-playerid.onesignal');
//        Route::post('/remove-playerid', 'Merchant\DashBoardController@removeWebPlayerId')->name('merchant-remove-playerid.onesignal');
        Route::resource('/duration', 'Merchant\DurationController', ['only' => ['index', 'edit', 'update']]);
        Route::get('/duration/add/{id?}', 'Merchant\DurationController@add');
        Route::post('/duration/save/{id?}', 'Merchant\DurationController@save');
        Route::resource('/driver-commission-choices', 'Merchant\DriverCommissionChoiceController', ['only' => ['index', 'edit', 'update']]);

        Route::get('/paymentMethod', ['as' => 'merchant.paymentMethod.index', 'uses' => 'Merchant\PaymentMethodController@index']);
        Route::get('/paymentMethod/{id}', ['as' => 'merchant.paymentMethod.edit', 'uses' => 'Merchant\PaymentMethodController@edit']);
        Route::put('/paymentMethod/{id}', ['as' => 'merchant.paymentMethod.update', 'uses' => 'Merchant\PaymentMethodController@update']);

        Route::get('/serviceType', ['as' => 'merchant.serviceType.index', 'uses' => 'Merchant\ServiceTypeController@index']);
        Route::get('/serviceType/{segment_id}/{id?}', ['as' => 'merchant.serviceType.edit', 'uses' => 'Merchant\ServiceTypeController@add']);
        Route::put('/serviceType/{id?}', ['as' => 'merchant.serviceType.update', 'uses' => 'Merchant\ServiceTypeController@update']);
        //ajax route
        Route::post('/getAllPriceCard', ['as' => 'getAllPriceCard', 'uses' => 'Helper\AjaxController@GetPriceCard']);
        Route::post('/ajax/area', ['as' => 'ajax.area', 'uses' => 'Helper\AjaxController@AreaList']);
        Route::post('/ajax/vehiclemodel', ['as' => 'ajax.vehiclemodel', 'uses' => 'Helper\AjaxController@VehicleModel']);
        Route::post('/ajax/services', ['as' => 'ajax.services', 'uses' => 'Helper\AjaxController@VehicleServices']);
        Route::post('/getRideConfig', ['as' => 'merchant.getRideConfig', 'uses' => 'Helper\AjaxController@VehicleConfig']);
        Route::post('/price-card-service-config', ['as' => 'merchant.price.card.service.config', 'uses' => 'Helper\AjaxController@ServiceConfig']);
        Route::post('/getServices', ['as' => 'merchant.area.services', 'uses' => 'Helper\AjaxController@ServiceType']);
        Route::post('/getServicescashback', ['as' => 'merchant.area.servicescashback', 'uses' => 'Helper\AjaxController@ServiceTypeCashBack']);
        Route::post('/getVehicletypescashback', ['as' => 'merchant.area.vehicletypescashback', 'uses' => 'Helper\AjaxController@VehicleTypeCashBack']);
        Route::get('/cashback/change_status/{id}/{status}', 'Merchant\CashbackController@Change_Status')->name('cashback.changestatus');
        Route::resource('cashback', 'Merchant\CashbackController');
        Route::post('/checkPool', ['as' => 'merchant.area.checkPool', 'uses' => 'Helper\AjaxController@CheckPool']);
        Route::post('/getVehicle', ['as' => 'get.area.vehicles', 'uses' => 'Helper\AjaxController@VehicleType']);
        Route::post('/getVehicleSegment', ['as' => 'get.area.vehicle.segment', 'uses' => 'Helper\AjaxController@VehicleSegment']);
        Route::post('/get-area-segment', ['as' => 'get.area.segment', 'uses' => 'Helper\AjaxController@countryAreaSegment']);
        Route::post('/checkPriceCard', ['as' => 'merchant.checkPriceCard', 'uses' => 'Helper\AjaxController@PriceCard']);


        ////excel route START
        Route::get('/excel/user', ['as' => 'excel.user', 'uses' => 'ExcelController@UserExport']);
        Route::get('/excel/user-wallet-trans/{id}', ['as' => 'excel.userwallettrans', 'uses' => 'ExcelController@userWalletTransaction']);
        Route::get('/excel/user-Rides/{id}', ['as' => 'excel.userRides', 'uses' => 'ExcelController@userRides']);
        Route::get('/excel/driver', ['as' => 'excel.driver', 'uses' => 'ExcelController@DriverExport']);
        Route::get('/excel/basic-signup-driver', ['as' => 'excel.basicsignupdriver', 'uses' => 'ExcelController@basicSignupDriver']);
        Route::get('/excel/pending-drivers', ['as' => 'excel.pendingdrivers', 'uses' => 'ExcelController@pendingDrivers']);
        Route::get('/excel/rejected-driver', ['as' => 'excel.rejecteddriver', 'uses' => 'ExcelController@rejectedDriver']);
        Route::get('/excel/blocked-drivers', ['as' => 'excel.blockeddrivers', 'uses' => 'ExcelController@blockedDrivers']);
        Route::get('/excel/pending-vehicles', ['as' => 'excel.pendingvehicles', 'uses' => 'ExcelController@pendingVehicles']);
        Route::get('/excel/ride-now', ['as' => 'excel.ridenow', 'uses' => 'ExcelController@RideNow']);
        Route::get('/excel/ride-later', ['as' => 'excel.ridelater', 'uses' => 'ExcelController@RideLater']);
        Route::get('/excel/ride-complete', ['as' => 'excel.complete', 'uses' => 'ExcelController@RideComplete']);
        Route::get('/excel/ride-cancel', ['as' => 'excel.ridecancel', 'uses' => 'ExcelController@CancelledRide']);
        Route::get('/excel/ride-failed', ['as' => 'excel.ridefailed', 'uses' => 'ExcelController@FailedRide']);
        Route::get('/excel/auto-cancel-rides', ['as' => 'excel.autocancelrides', 'uses' => 'ExcelController@autocancelrides']);
        Route::get('/excel/all-rides', ['as' => 'excel.allrides', 'uses' => 'ExcelController@allRides']);
        Route::get('/excel/sub-admin', ['as' => 'excel.subadmin', 'uses' => 'ExcelController@SubAdmin']);
        Route::get('/excel/transactions', ['as' => 'excel.transactions', 'uses' => 'ExcelController@Transactions']);
        Route::get('/excel/sos-requests', ['as' => 'excel.sosrequests', 'uses' => 'ExcelController@SosRequests']);
        Route::get('/excel/ratings', ['as' => 'excel.ratings', 'uses' => 'ExcelController@Ratings']);
        Route::get('/excel/customer-supports', ['as' => 'excel.customersupports', 'uses' => 'ExcelController@CustomerSupports']);
        Route::get('/excel/promotion-notifications', ['as' => 'excel.promotionnotifications', 'uses' => 'ExcelController@PromotionNotifications']);
        Route::get('/excel/countries-export', ['as' => 'excel.countriesexport', 'uses' => 'ExcelController@countriesExport']);
        Route::get('/excel/booking-report', ['as' => 'excel.bookingreport', 'uses' => 'ExcelController@BookingReport']);
        Route::get('/excel/booking-variance-report', ['as' => 'excel.bookingvariancereport', 'uses' => 'ExcelController@BookingVarianceReport']);
        Route::get('/excel/user-wallet-report', ['as' => 'excel.userwalletreport', 'uses' => 'ExcelController@UserWalletReport']);
        Route::get('/excel/driver-wallet-report', ['as' => 'excel.driverwalletreport', 'uses' => 'ExcelController@DriverWalletReport']);
        Route::get('/excel/driver-acceptance-report', ['as' => 'excel.driveracceptancereport', 'uses' => 'ExcelController@DriverAcceptanceReport']);
        Route::get('/excel/driver-online-time-report', ['as' => 'excel.driveronlinetimereport', 'uses' => 'ExcelController@DriverOnlineTimeReport']);
        Route::get('/excel/driver-accounts', ['as' => 'excel.driveraccounts', 'uses' => 'ExcelController@DriverAccounts']);
        Route::get('/excel/driver-bills/{id}', ['as' => 'excel.driverbills', 'uses' => 'ExcelController@DriverBills']);
        Route::get('/excel/promo-code', ['as' => 'excel.promocode', 'uses' => 'ExcelController@PromoCode']);
        Route::get('/excel/price-card', ['as' => 'excel.pricecard', 'uses' => 'ExcelController@PriceCard']);
        Route::get('/excel/service-area-management', ['as' => 'excel.serviceareamanagement', 'uses' => 'ExcelController@ServiceAreaManagement']);
        Route:: get('/excel/vehicle-types', 'ExcelController@vehicleTypes')->name('excel.vehicle-types');
        Route::get('/excel/refer', ['as' => 'excel.refer', 'uses' => 'ExcelController@Referral']);
        Route::get('/excel/vehiclemake', ['as' => 'excel.vehicle.make', 'uses' => 'ExcelController@VehicleMake']);
        Route::get('/excel/vehiclemodel', ['as' => 'excel.vehicle.model', 'uses' => 'ExcelController@VehicleModel']);
        Route::get('/excel/merchant-orders', ['as' => 'excel.merchant.orders', 'uses' => 'ExcelController@OrderManagement']);
        Route::get('/excel/payment/transactions', ['as' => 'excel.payment.transactions', 'uses' => 'ExcelController@PaymentTransactions']);
        //excel Routes END

        Route::get('/driver_configuration', ['as' => 'merchant.driver_configuration', 'uses' => 'Merchant\ConfigurationController@DriverConfiguration']);
        Route::post('/driver_configuration', ['as' => 'merchant.driver_configuration.store', 'uses' => 'Merchant\ConfigurationController@StoreDriverConfiguration']);

        //reports
//        Route::get('/report/booking', ['as' => 'report.booking', 'uses' => 'Merchant\ReportController@index']);
//        Route::get('/report/booking/search', ['as' => 'report.booking.search', 'uses' => 'Merchant\ReportController@SearchBooking']);
//
//        Route::get('/report/bookingVariance', ['as' => 'report.bookingVariance', 'uses' => 'Merchant\ReportController@BookingVariance']);
//        Route::get('/report/bookingVariance/search', ['as' => 'report.bookingVariance.search', 'uses' => 'Merchant\ReportController@SearchBookingVariance']);
//        Route::get('/report/companyReferral', ['as' => 'report.company.referral', 'uses' => 'Merchant\ReportController@CompanyReferral']);
//        Route::get('/report/areaReport', ['as' => 'report.area', 'uses' => 'Merchant\ReportController@AreaReport']);
//        Route::get('/report/areaReport/search', ['as' => 'report.area.search', 'uses' => 'Merchant\ReportController@AreaReportSearch']);
//        Route::get('/report/areaReport/ajax', ['as' => 'report.area.ajax', 'uses' => 'Merchant\ReportController@AreaReportData']);

        //Mansu
//        Route::get('/report/company_income', ['as' => 'report.company_income', 'uses' => 'Merchant\ReportController@CompanyIncome']);
//        Route::any('/report/company_income/search', ['as' => 'report.company_income.search', 'uses' => 'Merchant\ReportController@CompanyIncomeSearch']);
//        Route::get('/report/user/wallet', ['as' => 'report.user.wallet', 'uses' => 'Merchant\ReportController@UserWallet']);
//        Route::get('/report/user/wallet/search', ['as' => 'report.user.wallet.search', 'uses' => 'Merchant\ReportController@SearchUserWallet']);
//        Route::get('/report/driver/wallet', ['as' => 'report.driver.wallet', 'uses' => 'Merchant\ReportController@DriverWallet']);
//        Route::get('/report/driver/wallet/search', ['as' => 'report.driver.wallet.search', 'uses' => 'Merchant\ReportController@SerachDriverWallet']);
//        Route::get('/report/driver/acceptance', ['as' => 'report.driver.acceptance', 'uses' => 'Merchant\ReportController@DriverAcceptance']);
//        Route::get('/report/driver/acceptance/search', ['as' => 'report.driver.acceptance.search', 'uses' => 'Merchant\ReportController@SearchDriverAcceptance']);
//        Route::get('/report/promocode', ['as' => 'report.promocode', 'uses' => 'Merchant\ReportController@PromoCodeReport']);
//        Route::get('/report/promocode/details/{id}', ['as' => 'report.promocode.details', 'uses' => 'Merchant\ReportController@PromoCodeDetails']);
//        Route::get('/charts/driver', ['as' => 'charts.driver', 'uses' => 'Merchant\ReportController@DriverCharts']);
        Route::get('/logout', ['as' => 'merchant.logout', 'uses' => 'Auth\MerchantLoginController@logout']);

        //manual dispatch
        Route::get('/manual-dispatch', ['as' => 'merchant.test.manualdispach', 'uses' => 'Merchant\ManualDispatchController@index']);
        Route::post('/checkArea', ['as' => 'manualDispatch.checkArea', 'uses' => 'Merchant\ManualDispatchController@checkArea']);
        Route::get('/manualdispach', ['as' => 'merchant.manualdispach', 'uses' => 'Merchant\ManualDispatchController@index']);
        Route::post('/manualdispach', ['as' => 'merchant.book.manual.dispach', 'uses' => 'Merchant\ManualDispatchController@BookingDispatch']);
        Route::post('/SearchUser', ['as' => 'merchant.SearchUser', 'uses' => 'Merchant\ManualDispatchController@SearchUser']);
        Route::post('/getPromoCode', ['as' => 'merchant.getPromoCode', 'uses' => 'Merchant\ManualDispatchController@PromoCode']);
        Route::post('/getPromoCodeEta', ['as' => 'merchant.getPromoCodeEta', 'uses' => 'Merchant\ManualDispatchController@PromoCodeEta']);
        Route::get('/application', ['as' => 'merchant.application', 'uses' => 'Merchant\ApplicationController@index']);
        Route::post('/application', ['as' => 'merchant.application.store', 'uses' => 'Merchant\ApplicationController@store']);
        Route::get('/profile', ['as' => 'merchant.profile', 'uses' => 'Merchant\DashBoardController@profile']);
        Route::post('/profile', ['as' => 'merchant.profile.update', 'uses' => 'Merchant\DashBoardController@ProfileUpdate']);

        Route::post('/packageVehicles', ['as' => 'merchant.packageVehicles', 'uses' => 'Merchant\ManualDispatchController@PackageVehicles']);
        Route::post('/estimatePrice', ['as' => 'merchant.estimatePrice', 'uses' => 'Merchant\ManualDispatchController@EstimatePrice']);
        Route::post('/checkDriver', ['as' => 'merchant.checkDriver', 'uses' => 'Merchant\ManualDispatchController@CheckDriver']);
        Route::post('/getFavouriteDriver', ['as' => 'merchant.getFavouriteDriver', 'uses' => 'Merchant\ManualDispatchController@FavouriteDriver']);
        Route::post('/getallDriverForManual', ['as' => 'merchant.getallDriver', 'uses' => 'Merchant\ManualDispatchController@AllDriver']);
        Route::get('/onesignal', ['as' => 'merchant.onesignal', 'uses' => 'Merchant\DashBoardController@OneSignal']);
        Route::post('/onesignal', ['as' => 'merchant.onesignal.submit', 'uses' => 'Merchant\DashBoardController@UpdateOneSignal']);

//        Route::get('/common-strings', ['as' => 'merchant.common-strings', 'uses' => 'Merchant\DashBoardController@commonLanguageStrings']);
//        Route::post('/common-strings', ['as' => 'merchant.common-string.submit', 'uses' => 'Merchant\DashBoardController@submitCommonLanguageStrings']);

//        Route::get('/module-strings', ['as' => 'merchant.module-strings', 'uses' => 'Merchant\DashBoardController@moduleLanguageStrings']);
//        Route::post('/module-strings', ['as' => 'merchant.module-string.submit', 'uses' => 'Merchant\DashBoardController@submitModuleLanguageStrings']);
        Route::get('/module-strings', ['as' => 'merchant.module-strings', 'uses' => 'Merchant\ApplicatonStringController@moduleLanguageStrings']);
        Route::post('/module-strings', ['as' => 'merchant.module-string.submit', 'uses' => 'Merchant\ApplicatonStringController@submitModuleLanguageStrings']);

        Route::get('/languagestring', ['as' => 'merchant.languagestring', 'uses' => 'Merchant\DashBoardController@LanguageStrings']);
        Route::post('/languagestring', ['as' => 'merchant.languagestring.submit', 'uses' => 'Merchant\DashBoardController@UpdateLanguageString']);

        Route::get('/applicationtheme', ['as' => 'merchant.applicationtheme', 'uses' => 'Merchant\ConfigurationController@Applicationtheme']);
        Route::post('/applicationtheme', ['as' => 'merchant.applicationtheme.submit', 'uses' => 'Merchant\ConfigurationController@UpdateApplicationtheme']);

        Route::get('/setup', ['as' => 'merchant.setup', 'uses' => 'Merchant\SetupController@index']);
        Route::get('/images', ['as' => 'merchant.setup', 'uses' => 'Merchant\SetupController@uploadImagesToS3']);
        Route::get('/dashboard', ['as' => 'merchant.dashboard', 'uses' => 'Merchant\DashBoardController@index']);
        Route::get('/ratings', ['as' => 'merchant.ratings', 'uses' => 'Merchant\DashBoardController@Ratings']);
        Route::get('/ratings/search', ['as' => 'merchant.ratings.search', 'uses' => 'Merchant\DashBoardController@SearchRating']);

        // Old URL's
//        Route::get('/refer', ['as' => 'merchant.refer.index', 'uses' => 'Merchant\DashBoardController@ReferShow']);
//        Route::get('/refer/create', ['as' => 'merchant.refer.create', 'uses' => 'Merchant\DashBoardController@ReferCreateShow']);
//        Route::post('/refer/create', ['as' => 'merchant.refer.store', 'uses' => 'Merchant\DashBoardController@ReferStore']);
//        Route::get('/refer/edit/{id}', ['as' => 'merchant.refer.edit', 'uses' => 'Merchant\DashBoardController@Referedit']);
//        Route::get('/refer/active/deactive/{id}/{status}', ['as' => 'merchant.refer.active-deactive', 'uses' => 'Merchant\DashBoardController@ChangeStatus']);
        //Mansu
//        Route::get('/refer/driver_ref', ['as' => 'merchant.refer.driver_view', 'uses' => 'Merchant\DashBoardController@Driver_ReferShow_view']);
//        Route::get('/refer/driver', ['as' => 'merchant.refer.driver', 'uses' => 'Merchant\DashBoardController@Driver_ReferCreateShow']);
//        Route::post('/refer/driver/create', ['as' => 'merchant.refer.driver.store', 'uses' => 'Merchant\DashBoardController@Driver_ReferStore']);
//        Route::get('/refer/driver/edit/{id}', ['as' => 'merchant.refer.driver.edit', 'uses' => 'Merchant\DashBoardController@Driver_Referedit']);
//        Route::post('/refer/driver/update/{id}', ['as' => 'merchant.refer.driver.update', 'uses' => 'Merchant\DashBoardController@Driver_ReferUpdate']);
//        Route::get('/refer/driver/active/deactive/{id}/{status}', ['as' => 'merchant.refer.driver.active-deactive', 'uses' => 'Merchant\DashBoardController@Driver_ChangeStatus']);

        //Referral System
//        Route::resource('/referral-system', 'Merchant\ReferralSystemController');
        Route::get('/referral-system',['as' => 'referral-system', 'uses' => 'Merchant\ReferralSystemController@index']);
        Route::get('/referral-system/add/{id?}',['as' => 'referral-system.create', 'uses' => 'Merchant\ReferralSystemController@create']);
        Route::post('/referral-system/save/{id?}',['as' => 'referral-system.store', 'uses' => 'Merchant\ReferralSystemController@store']);
        Route::get('/referral-system/changeStatus/{id}/{status}', ['as' => 'referral-system.change-status', 'uses' => 'Merchant\ReferralSystemController@ChangeStatus']);
        Route::post('/referral-system/delete', ['as' => 'referral-system.delete', 'uses' => 'Merchant\ReferralSystemController@deleteReferral']);
        Route::get('/check/referral-system', ['as' => 'referral-system.check-referral', 'uses' => 'Merchant\ReferralSystemController@checkReferralSystem']);

//        Route::post('/refer/add/default', ['as' => 'merchant.add.default.refer', 'uses' => 'Merchant\ReferralSystemController@defaultReferral']);
//        Route::get('/get-country-area',['merchant.country.area', 'uses' => 'Merchant\ReferralSystemController@getCountryArea']);


        // routes for driver commission fare table
        Route:: get('/driver-commission-fare', 'Merchant\ReferController@index')->name('merchant.driver.commission.fare');
        Route:: get('/driver-commission-fare/create/{id?}', 'Merchant\ReferController@create')->name('merchant.driver.commissionfare.create');
        Route:: post('/driver-commissionfare/store/{id?}', 'Merchant\ReferController@store')->name('merchant.driver.commissionfare.store');
        Route:: post('/driver-commission-fare/{id}/delete', 'Merchant\ReferController@destroy')->name('merchant.driver.commissionfare.destroy');


        Route::post('/refer/update/{id}', ['as' => 'merchant.refer.update', 'uses' => 'Merchant\DashBoardController@ReferUpdate']);
        // @Bhuvanesh
        // This route currently not in use
        // Route::resource('category', 'Merchant\CategoryController');
        Route::get('/users/wallet/{id}', ['as' => 'merchant.user.wallet', 'uses' => 'Merchant\UserController@Wallet']);
        Route::post('/user/addmoney', ['as' => 'merchant.user.add.wallet', 'uses' => 'Merchant\UserController@AddWalletMoney']);
        Route::get('/users/favourite/location/{id}', ['as' => 'merchant.user.favourite-location', 'uses' => 'Merchant\UserController@FavouriteLocation']);
        Route::get('/users/favourite/Driver/{id}', ['as' => 'merchant.user.favourite-driver', 'uses' => 'Merchant\UserController@FavouriteDriver']);
        Route::get('/users/active/deactive/{id}/{status}', ['as' => 'merchant.user.active-deactive', 'uses' => 'Merchant\UserController@ChangeStatus']);
        Route::get('users/serach', ['as' => 'merchant.user.search', 'uses' => 'Merchant\UserController@Serach']);
        Route::get('users/refer/{id}', ['as' => 'merchant.user.refer', 'uses' => 'Merchant\UserController@UserRefer']);
        Route::get('users/delete/{id?}', ['as' => 'merchant.user.delete', 'uses' => 'Merchant\UserController@destroy']);
        Route::resource('users', 'Merchant\UserController');
        Route::get('/allvehicles/', ['as' => 'merchant.driver.allvehicles', 'uses' => 'Merchant\DriverController@AllVehicle']);
//        Route::get('/allvehicles/document/edit/{id}', ['as' => 'merchant.driver.allvehicles.edit', 'uses' => 'Merchant\DriverController@EditVehicleDocument']);
//        Route::post('/allvehicles/document/edit/{id}', ['as' => 'merchant.driver.allvehicles.update', 'uses' => 'Merchant\DriverController@UpdateVehicleDocument']);
//        Route::get('/allvehicles/search', ['as' => 'merchant.driver.allvehicles.search', 'uses' => 'Merchant\DriverController@AllVehicleSearch']);


//        Route::any('getexpirepersonaldocument/', ['as' => 'merchant.docs.getexpirepersonaldocument', 'uses' => 'Merchant\ExpireDocumentController@ShowPersonalDocs']);
//        Route::any('getexpirevehicledocument/', ['as' => 'merchant.docs.getexpirevehicledocument', 'uses' => 'Merchant\ExpireDocumentController@ShowVehicleDocs']);
        Route::get('driver/expired-documents/', ['as' => 'merchant.driver.expiredocuments', 'uses' => 'Merchant\ExpireDocumentController@index']);

        //Document Expire module
        Route::get('driver/going_to_expire_document/', ['as' => 'merchant.driver.goingtoexpiredocuments', 'uses' => 'Merchant\ExpireDocumentController@GoingToExpireDocs']);
        Route::get('driver/goingToExpireDocument/sendNotification/{id}', ['as' => 'goingToExpireDocuments.sendNotification', 'uses' => 'Merchant\ExpireDocumentController@SendNotification']);
        Route::post('driver/send-notification-to-all-drivers/', ['as' => 'merchant.driver.sendNotificationToAll', 'uses' => 'Merchant\ExpireDocumentController@sendNotificationToAll']);
        Route::post('driver/uploadVehicleExpireDocs/', ['as' => 'merchant.driver.uploadVehicleExpireDocs', 'uses' => 'Merchant\ExpireDocumentController@UploadVehicleDocs']);
//        Route::post('driver/upload-handyman-document/', ['as' => 'merchant.driver.handyman-document-upload', 'uses' => 'Merchant\ExpireDocumentController@uploadHandymanDocs']);
        Route::post('driver/uploadDriverExpireDocs/', ['as' => 'merchant.driver.uploadDriverExpireDocs', 'uses' => 'Merchant\ExpireDocumentController@UploadDriverDocs']);


//        Route::get('driver/expirepersonaldocs/', ['as' => 'merchant.driver.expirepersonaldocs', 'uses' => 'Merchant\ExpireDocumentController@Check_PersonalDocumnet']);
//        Route::get('driver/expirevehicledocs/', ['as' => 'merchant.driver.expirevehicledocs', 'uses' => 'Merchant\ExpireDocumentController@Check_VehicleDocumnet']);
        Route::get('/drivers/block/', ['as' => 'merchant.driver.cronblock', 'uses' => 'Merchant\DriverController@Cronjob_DriverBlock']);
        Route::post('/drivers/delete', ['as' => 'driverDelete', 'uses' => 'Merchant\DriverController@destroy']);
        Route::get('/driver/editDocument/{id}', ['as' => 'driver.editDocument', 'uses' => 'Merchant\DriverController@EditDocument']);
        Route::post('/driver/editDocument/{id}', ['as' => 'driver.store.editDocument', 'uses' => 'Merchant\DriverController@StoreEdit']);
        Route::get('/driver/delete/pending-vehicle/{id?}', ['as' => 'driver.delete.pendingvehicle', 'uses' => 'Merchant\DriverController@DeletePendingVehicle']);
        Route::get('/driver/locationNotUpdate', 'Merchant\DriverController@FindDriverLocationNotUpdate')->name('driver.locationNotUpdate');
        Route::get('/driver/search/locationNotUpdate', 'Merchant\DriverController@SearchDriverLocationNotUpdate')->name('driver.search.locationNotUpdate');

       /** driver module start **/
        // get drivers
        Route::get('/driver', ['as' => 'driver.index', 'uses' => 'Merchant\DriverController@index']);
//        Route::get('/driver/search', ['as' => 'merchant.driver.search', 'uses' => 'Merchant\DriverController@index']);
        // add driver
        Route::get('/driver/add/{id?}', ['as' => 'driver.add', 'uses' => 'Merchant\DriverController@add']);
        // get driver
        Route::post('/driver/personal-document', ['as' => 'merchant.driver.country-area-document', 'uses' => 'Merchant\DriverController@getPersonalDocument']);
        // save driver
        Route::post('/driver/save/{id?}', ['as' => 'driver.save', 'uses' => 'Merchant\DriverController@save']);
        // view driver
        Route::get('/driver/profile/{id}', ['as' => 'driver.show', 'uses' => 'Merchant\DriverController@show']);
        // get driver's personal document
//        Route::get('/driver/personal-document/{id}', ['as' => 'merchant.driver.personal.document.show', 'uses' => 'Merchant\DriverController@addPersonalDocument']);
        // save driver's personal document
//        Route::post('/driver/personal-document/{id}', ['as' => 'merchant.driver.personal.document.save', 'uses' => 'Merchant\DriverController@savePersonalDocument']);


        // get driver's handyman segment and document
        Route::get('/driver/handyman-segment/{id}', ['as' => 'merchant.driver.handyman.segment', 'uses' => 'Merchant\DriverController@addHandymanSegment']);
        // save driver's handyman segment and document
        Route::post('/driver/handyman-segment/{id}', ['as' => 'merchant.driver.handyman.segment.save', 'uses' => 'Merchant\DriverController@saveHandymanSegment']);


        // get time slots of driver's handyman segment's
        Route::get('/driver/segment/time-slot/{id}', ['as' => 'merchant.driver.segment.time-slot', 'uses' => 'Merchant\DriverController@addSegmentTimeSlot']);

        // save time slots of driver's handyman segment
        Route::post('/driver/segment/time-slot/{id}', ['as' => 'merchant.driver.segment.time-slot.save', 'uses' => 'Merchant\DriverController@saveSegmentTimeSlot']);

        // add driver vehicle
//        Route::get('/driver/add-vehicle/{id}/{vehicle_id?}/{calling_from?}', ['as' => 'merchant.driver.vehicle.create', 'uses' => 'Merchant\DriverController@addVehicle']);
        Route::get('/driver/add-vehicle/{id}/{vehicle_id?}', ['as' => 'merchant.driver.vehicle.create', 'uses' => 'Merchant\DriverController@addVehicle']);
        // save driver vehicle
        Route::post('/driver/save-vehicle/{id}', ['as' => 'merchant.driver.vehicle.store', 'uses' => 'Merchant\DriverController@saveVehicle']);
        /** driver module end **/


//        Route::resource('driver', 'Merchant\DriverController');
        //        Route::get('/drivers/pending/edit/{id}', ['as' => 'merchant.driver.pending.edit', 'uses' => 'Merchant\DriverController@PendingDriverEdit']);
        Route::get('/driver/activated-subscription-pack/{id}', ['as' => 'driver.activated_subscription', 'uses' => 'Merchant\DriverController@Activated_Subscription']);
        Route::get('/driver/activate-subscription-pack/{id}', ['as' => 'driver.add-subscription-pack', 'uses' => 'Merchant\DriverController@ShowSubscriptionPacks']);
        Route::post('/driver/activate-subscription-pack-cash/{id}', ['as' => 'driver.subscription-cash-buy', 'uses' => 'Merchant\DriverController@Activate_Subscription_Cash']);
        Route::post('/driver/activate-subscription-pack-wallet/{id}', ['as' => 'driver.subscription-wallet-buy', 'uses' => 'Merchant\DriverController@Activate_Subscription_Wallet']);
        Route::post('/driver/subscription-assign/{id}', ['as' => 'driver.subscription-assign', 'uses' => 'Merchant\DriverController@AssignFreeSubscription']);
//        Route::get('/drivers/search/', ['as' => 'merchant.driver.search', 'uses' => 'Merchant\DriverController@Serach']);


        Route::post('/Driver_Delete', ['as' => 'Driver_Delete', 'uses' => 'Merchant\DriverController@delete']);
        Route::get('/driver/job/{job_type}/{id}', ['as' => 'merchant.driver.jobs', 'uses' => 'Merchant\DriverController@driverJobs']);
        Route::get('/drivers/pending/', ['as' => 'merchant.driver.pending.show', 'uses' => 'Merchant\DriverController@pendingDriver']);

        Route::get('/drivers/temp/doc/pending/', ['as' => 'merchant.driver.temp-doc-pending.show', 'uses' => 'Merchant\DriverController@tempDocApprovalPending']);

//        Route::get('/drivers/pending/search/', ['as' => 'merchant.driver.pending.search', 'uses' => 'Merchant\DriverController@PendingSerach']);
//        Route::get('/drivers/basic/', ['as' => 'merchant.driver.basic', 'uses' => 'Merchant\DriverController@NewDriver']);
        Route::get('/drivers/basic-signup/', ['as' => 'merchant.driver.basic', 'uses' => 'Merchant\DriverController@basicSignupDriver']);
//        Route::get('/drivers/basic/search', ['as' => 'merchant.driver.basic.search', 'uses' => 'Merchant\DriverController@NewDriverSearch']);
        Route::get('/pending/vehicles/', ['as' => 'merchant.driver.pending.vehicles', 'uses' => 'Merchant\DriverController@PendingVehicle']);
//        Route::get('/pending/vehicles/search', ['as' => 'merchant.driver.pending.vehicles.search', 'uses' => 'Merchant\DriverController@PendingVehicleSearch']);

       // Route::get('/driver/document/{id}', ['as' => 'merchant.driver.document.show', 'uses' => 'Merchant\DriverController@addPersonalDocument']);
        Route::get('/driver/wallet/{id}', ['as' => 'merchant.driver.wallet.show', 'uses' => 'Merchant\DriverController@Wallet']);
        Route::get('/driver/active/deactive/{id}/{status}', ['as' => 'merchant.driver.active.deactive', 'uses' => 'Merchant\DriverController@ChangeStatus']);
        Route::get('/driver/logout/{id}', ['as' => 'merchant.driver.logout', 'uses' => 'Merchant\DriverController@Logout']);
        Route::post('/driver/document/{id}', ['as' => 'merchant.driver.document.store', 'uses' => 'Merchant\DriverController@StoreDocument']);


        Route::get('/driver/personal/expire', ['as' => 'merchant.driver.personal.expire', 'uses' => 'Merchant\DriverController@PersonalDocExpire']);
        Route::get('/driver/vehicle/expire', ['as' => 'merchant.driver.vehicle.expire', 'uses' => 'Merchant\DriverController@VehicleDocExpire']);
        Route::get('/vehicle/rejected/', ['as' => 'merchant.vehicle.rejected', 'uses' => 'Merchant\DriverController@RejectedVehicle']);
        Route::get('/driver/rejected/', ['as' => 'merchant.driver.rejected', 'uses' => 'Merchant\DriverController@rejectedDriver']);
        Route::get('/driver/rejected/temporary', ['as' => 'merchant.driver.rejected.temporary', 'uses' => 'Merchant\DriverController@rejectedDriverTemporary']);
//        Route::get('/pending/rejected/search', ['as' => 'merchant.driver.rejected.search', 'uses' => 'Merchant\DriverController@RejectedSearch']);
        Route::post('/move-to/pending', ['as' => 'merchant.driver.move-to-pending', 'uses' => 'Merchant\DriverController@MoveToPending']);
//        Route::get('/driver/reject_driver/{id}', ['as' => 'merchant.driver-reject', 'uses' => 'Merchant\DriverController@DisapproveDriver']);
        Route::get('/driver/approve_driver/{id}', ['as' => 'merchant.driver-approve', 'uses' => 'Merchant\DriverController@ApproveDriver']);
        Route::get('/driver/referral/earning/{id}', ['as' => 'merchant.driver.referral.earning.show', 'uses' => 'Merchant\DriverController@referralEarning']);

        Route::get('driver/refer/{id}', ['as' => 'merchant.driver.refer', 'uses' => 'Merchant\DriverController@DriverRefer']);

        Route::get('pricecard/surgecharge', 'Merchant\PriceCardController@SurgeCharge')->name('pricecard.surgecharge');
        Route::get('/pricecard/active/deactive/{id}/{status}', ['as' => 'merchant.pricecard.active-deactive', 'uses' => 'Merchant\PriceCardController@ChangeStatus']);
        Route::post('pricecard/surgechargeupdate/{id}', 'Merchant\PriceCardController@SurgeChargeUpdate')->name('pricecard.surgecharge.update');
        Route::post('pricecard/surgechargevalupdate', 'Merchant\PriceCardController@SurgeChargeValUpdate')->name('pricecard.surgecharge.value.update');


        Route::resource('area-management/country', 'Merchant\CountryController');
//        Route::get('area-management/search/country', ['as' => 'mearchant.country.search', 'uses' => 'Merchant\CountryController@SearchCountry']);

        Route::post('area-management/countryareas/search', 'Merchant\CountryAreaController@index')->name('countryArea.Search');
        Route::get('area-management/countryareas/add/{id?}', ['as' => 'countryareas.add','uses' =>'Merchant\CountryAreaController@add']);
        Route::post('area-management/countryareas/save/{id?}', ['as' => 'countryareas.save','uses' =>'Merchant\CountryAreaController@save']);
        Route::get('area-management/countryareas', ['as' => 'countryareas.index','uses' =>'Merchant\CountryAreaController@index']);
        Route::get('area-management/show/{id?}', ['as' => 'countryareas.show','uses' =>'Merchant\CountryAreaController@show']);

        Route::get('area-management/countryareas/add/step2/{id}', ['as' => 'countryareas.add.step2','uses' =>'Merchant\CountryAreaController@addStep2']);
        Route::post('area-management/countryareas/save/step2/{id?}', ['as' => 'countryareas.save.step2','uses' =>'Merchant\CountryAreaController@saveStep2']);

        Route::get('area-management/countryareas/add/step3/{id}', ['as' => 'countryareas.add.step3','uses' =>'Merchant\CountryAreaController@addStep3']);
        Route::post('area-management/countryareas/save/step3/{id?}', ['as' => 'countryareas.save.step3','uses' =>'Merchant\CountryAreaController@saveStep3']);

        Route::post('area-management/countryareas/vehicle-type/edit', ['as' => 'merchant.country_area.vehicle-type','uses' =>'Merchant\CountryAreaController@vehicleTypeEdit']);
        Route::post('area-management/countryareas/vehicle-type/delete', ['as' => 'merchant.area_vehicle.destroy','uses' =>'Merchant\CountryAreaController@deleteStep2']);

        Route::get('country-areas/vehicle-type/categorization/{id}', ['as' => 'country-area.category.vehicle.type','uses' =>'Merchant\CountryAreaController@vehicleCategorization']);
        Route::post('country-areas/vehicle-type/categorization/{id}', ['as' => 'country-area.category.vehicle.type.save','uses' =>'Merchant\CountryAreaController@saveVehicleCategorization']);

        Route::resource('area-management/countryareas', 'Merchant\CountryAreaController');

        Route::resource('vehicle-management/vehicletype', 'Merchant\VehicleTypeController');
        Route::post('/vehicle-type/delete', ['as' => 'merchant.vehicletype.delete', 'uses' => 'Merchant\VehicleTypeController@destroy']);
        Route::resource('vehicle-management/vehiclemake', 'Merchant\VehicleMakeController');
        Route::post('/vehicle-make/delete', ['as' => 'merchant.vehiclemake.delete', 'uses' => 'Merchant\VehicleMakeController@destroy']);
        Route::resource('vehicle-management/vehiclemodel', 'Merchant\VehicleModelController');
        Route::post('/vehicle-model/delete', ['as' => 'merchant.vehiclemodel.delete', 'uses' => 'Merchant\VehicleModelController@destroy']);


        Route::get('promo-code', ['as'=>'promocode.index','uses'=>'Merchant\PromoCodeController@index']);
        Route::get('promo-code/add/{id?}', ['as'=>'promocode.create','uses'=>'Merchant\PromoCodeController@add']);
        Route::post('promo-code/save/{id?}', ['as'=>'promocode.store','uses'=>'Merchant\PromoCodeController@save']);
//        Route::resource('promocode', 'Merchant\PromoCodeController');
        Route::resource('walletpromocode', 'Merchant\WalletCouponCodeController');

        Route::get('priceparameter/', ['as'=>'pricingparameter.index','uses'=>'Merchant\PricingParameterController@index']);
        Route::get('priceparameter/add/{id?}', ['as'=>'priceparameter.add','uses'=>'Merchant\PricingParameterController@add']);
        Route::post('priceparameter/save/{id?}', ['as'=>'priceparameter.save','uses'=>'Merchant\PricingParameterController@save']);
//        Route::resource('pricingparameter', 'Merchant\PricingParameterController');
        Route::get('pricecard', ['as'=>'pricecard.index','uses'=>'Merchant\PriceCardController@index']);
        Route::get('pricecard/add/{id?}', ['as'=>'pricecard.add','uses'=>'Merchant\PriceCardController@add']);
        Route::post('pricecard/save/{id?}', ['as'=>'pricecard.save','uses'=>'Merchant\PriceCardController@save']);
        Route::resource('pricecard', 'Merchant\PriceCardController');

        Route::resource('cancelreason', 'Merchant\CancelReasonController');
        Route::resource('rejectreason', 'Merchant\RejectReasonController');
        Route::get('/promotions/search', ['as' => 'promotions.search', 'uses' => 'Merchant\PromotionNotificationController@Search']);
        Route::resource('promotions', 'Merchant\PromotionNotificationController');
        Route::resource('promotionsms', 'Merchant\PromotionSmsController');
        Route::resource('subadmin', 'Merchant\SubAdminController');
//        Route::resource('role', 'Merchant\RoleController');
//        Route::resource('new-role', 'Merchant\RoleController');
        Route::get('role', ['as' => 'new-role.index', 'uses' => 'Merchant\NewRoleController@index']);
        Route::get('role/create/{id?}', ['as' => 'new-role.create', 'uses' => 'Merchant\NewRoleController@create']);
        Route::post('role/store/{id?}', ['as' => 'new-role.store', 'uses' => 'Merchant\NewRoleController@store']);
        Route::resource('accounts', 'Merchant\DriverAccountController');
        Route::resource('newaccounts', 'Merchant\SettlementController');


//        Route::resource('hotels', 'Merchant\HotelController');
        Route::get('/hotel', ['as' => 'hotels.index', 'uses' => 'Merchant\HotelController@index']);
        Route::get('/hotel/add/{id?}', ['as' => 'hotels.create', 'uses' => 'Merchant\HotelController@add']);
        Route::post('/hotel/save/{id?}', ['as' => 'hotels.store', 'uses' => 'Merchant\HotelController@save']);

        Route::post('/hotel/AddMoney', ['as' => 'hotel.AddMoney', 'uses' => 'Merchant\HotelController@AddMoney']);
        Route::post('/hotel/AddMoney', ['as' => 'hotel.AddMoney', 'uses' => 'Merchant\HotelController@AddMoney']);
        Route::get('/hotel/wallet/{id}', ['as' => 'merchant.hotel.wallet.show', 'uses' => 'Merchant\HotelController@Wallet']);
        Route::get('/hotel/transactions/{id}', ['as' => 'merchant.hotel.transactions', 'uses' => 'Merchant\TransactionController@HotelTransaction']);
        Route::post('/hotel/transactions/{id}', ['as' => 'merchant.hotel.transactions.search', 'uses' => 'Merchant\TransactionController@HotelSearch']);

        Route::resource('questions', 'Merchant\QuestionController');
        Route::resource('account-types', 'Merchant\AccountTypeController');
        Route::post('/settle/newaccounts', 'Merchant\SettlementController@Settle')->name('newaccounts.changestatus');

        Route::get('/account-types/change_status/{id}/{status}', 'Merchant\AccountTypeController@Change_Status')->name('account-types.changestatus');
        Route::post('walletpromocode/bulk_coupon', ['as' => 'walletpromocode.bulk_code', 'uses' => 'Merchant\WalletCouponCodeController@bulk_code']);

        Route::get('wallet_recharge', ['as' => 'Wallet.recharge', 'uses' => 'Merchant\TransactionController@wallet']);
        Route::post('getDetails', ['as' => 'Wallet.getDetails', 'uses' => 'Merchant\TransactionController@getDetails'])->name('getDetails');
        Route::post('wallet_recharge_details', ['as' => 'Wallet.recharge.details', 'uses' => 'Merchant\TransactionController@walletRecharge']);
        Route::get('getReceiver', ['as' => 'wallet.getReceivers', 'uses' => 'Merchant\TransactionController@getWalletReceiver']);

        Route::get('/search/pricecard', ['as' => 'merchant.pricecard.search', 'uses' => 'Merchant\PriceCardController@index']);
        Route::get('/account/search/', ['as' => 'merchant.accounts.search', 'uses' => 'Merchant\DriverAccountController@Serach']);
        Route::get('/reject/active/deactive/{id}/{status}', ['as' => 'merchant.reject.active-deactive', 'uses' => 'Merchant\RejectReasonController@ChangeStatus']);
        Route::get('/promocode/delete/{id}', ['as' => 'merchant.promocode.delete', 'uses' => 'Merchant\PromoCodeController@destroy']);
        Route::get('/promocode/active/deactive/{id}/{status}', ['as' => 'merchant.promocode.active-deactive', 'uses' => 'Merchant\PromoCodeController@ChangeStatus']);
        Route::get('/country/areaList', ['as' => 'merchant.country.arealist', 'uses' => 'Merchant\CountryAreaController@AreaList']);

        Route::get('/country/config', ['as' => 'merchant.country.config', 'uses' => 'Merchant\CountryAreaController@CountryConfig']);

        Route::get('/cancelreason/active/deactive/{id}/{status}', ['as' => 'merchant.cancelreason.active-deactive', 'uses' => 'Merchant\CancelReasonController@ChangeStatus']);
        Route::get('/hotels/active/deactive/{id}/{status}', ['as' => 'merchant.hotel.active-deactive', 'uses' => 'Merchant\HotelController@ChangeStatus']);
        Route::resource('franchisee', 'Merchant\FranchiseController');
        Route::get('/franchisee/active/deactive/{id}/{status}', ['as' => 'merchant.franchisee.active-deactive', 'uses' => 'Merchant\FranchiseController@ChangeStatus']);
        Route::get('/promotionsms/userdriver', ['as' => 'merchant.promotionsms.userdriver', 'uses' => 'Merchant\PromotionSmsController@UserDriver']);
        Route::post('/promotionsms/storeUserDriver', ['as' => 'merchant.promotionsms.storeUserDriver', 'uses' => 'Merchant\PromotionSmsController@storeUserDriver']);
        Route::get('/promotionsms/delete/{id}', ['as' => 'promotionsms.delete', 'uses' => 'Merchant\PromotionSmsController@destroy']);
        Route::get('/country/active/deactive/{id}/{status}', ['as' => 'merchant.country.active-deactive', 'uses' => 'Merchant\CountryController@ChangeStatus']);

        Route::get('/subadmin/active/deactive/{id}/{status}', ['as' => 'merchant.subadmin.active-deactive', 'uses' => 'Merchant\SubAdminController@ChangeStatus']);
        Route::post('/promocode/search', ['as' => 'promocode.search', 'uses' => 'Merchant\PromoCodeController@Search']);
        Route::post('/cancelreason/search', ['as' => 'cancelreason.search', 'uses' => 'Merchant\CancelReasonController@Search']);
        Route::get('/promotions/delete/{id}', ['as' => 'promotions.delete', 'uses' => 'Merchant\PromotionNotificationController@destroy']);
        Route::post('/promotions/send/driver', ['as' => 'merchant.sendsingle-driver', 'uses' => 'Merchant\PromotionNotificationController@SendNotificationDriver']);
        Route::post('/promotions/send/areawise', ['as' => 'merchant.areawise-notification', 'uses' => 'Merchant\PromotionNotificationController@SendNotificationAreaWise']);
        Route::post('/promotions/send/user', ['as' => 'merchant.sendsingle-user', 'uses' => 'Merchant\PromotionNotificationController@SendNotificationUser']);
        Route::resource('rental/packages', 'Merchant\ServicePackageController');
        Route::get('/rental/packages/active/deactive/{id}/{status}', ['as' => 'merchant.rental.packages.active-deactive', 'uses' => 'Merchant\ServicePackageController@ChangeStatus']);
        Route::resource('transferpackage', 'Merchant\TransferPackageController');
        Route::resource('outstationpackage', 'Merchant\OutstationPackageController');
        Route::get('/outstationpackage/active/deactive/{id}/{status}', ['as' => 'merchant.outstationpackage.active-deactive', 'uses' => 'Merchant\OutstationPackageController@ChangeStatus']);
        Route::resource('sos', 'Merchant\SosController');
        Route::resource('cms', 'Merchant\CmsPagesController');
        Route::post('/cms/search', ['as' => 'merchant.cms.search', 'uses' => 'Merchant\CmsPagesController@Search']);
        Route::resource('child-terms-conditions', 'Merchant\ChildTermsController');
//        Route::resource('terms', 'Merchant\TermsController');
//        Route::post('/terms/search', ['as' => 'merchant.terms.search', 'uses' => 'Merchant\TermsController@Search']);
        Route::get('/sos-requests', ['as' => 'merchant.sos.requests', 'uses' => 'Merchant\SosController@SosRequest']);
        Route::get('/sos-requests/sreach', ['as' => 'merchant.sos.sreach', 'uses' => 'Merchant\SosController@SercahSosRequest']);
        Route::post('/sos/search', ['as' => 'merchant.sos.search', 'uses' => 'Merchant\SosController@SearchSos']);
        Route::get('/sos/active/deactive/{id}/{status}', ['as' => 'merchant.sos.active-deactive', 'uses' => 'Merchant\SosController@ChangeStatus']);
        Route::get('/sos/delete/{id}', ['as' => 'merchant.sos.delete', 'uses' => 'Merchant\SosController@destroy']);
        Route::post('/driver/AddMoney', ['as' => 'merchant.AddMoney', 'uses' => 'Merchant\DriverController@AddMoney']);
        Route::post('/getDriverOnMap', ['as' => 'getDriverOnMap', 'uses' => 'Merchant\ManualDispatchController@getDriverOnMap']);
        Route::post('/getBookingsOnHeatMap', ['as' => 'getBookingsOnHeatMap', 'uses' => 'Merchant\ManualDispatchController@getBookingsOnHeatMap']);
        Route::post('/getfield', ['as' => 'admin.pricing.parameter', 'uses' => 'Merchant\PriceCardController@getPricingParameter']);
        Route::get('/heatmap', ['as' => 'merchant.heatmap', 'uses' => 'Merchant\MapController@HeatMap']);
        Route::get('/drivermap', ['as' => 'merchant.drivermap', 'uses' => 'Merchant\MapController@DriverMap']);
        //Route::get('countryareas/services/vehicle/{id}',['as'=>'merchant.service_vechicle','uses'=>'Merchant\CountryAreaController@SeriveVehicle']);
        Route::resource('documents', 'Merchant\DocumentController');
        Route::get('/document/add/{id?}', 'Merchant\DocumentController@add');
        Route::post('document/save/{id?}', 'Merchant\DocumentController@save');


        // get lat long from node server
        Route::post('/get-lat-long', ['as'=>'merchant.get-lat-long','uses'=>'Merchant\DriverController@getLatLongFromNode']);

        Route::post('document/update', ['as' => 'doc.update', 'uses' => 'Merchant\DocumentController@update']);
        Route::get('/document/active/deactive/{id}/{status}', ['as' => 'merchant.document.active-deactive', 'uses' => 'Merchant\DocumentController@ChangeStatus']);
        Route::get('/service', ['as' => 'merchant.service', 'uses' => 'Merchant\DashBoardController@ServiceType']);
        Route::get('/verifyDocument/{id}/{status}', ['as' => 'merchant.verifyDocument', 'uses' => 'Merchant\DriverController@VerifyDocument']);

        Route::post('/reject', ['as' => 'merchant.reject', 'uses' => 'Merchant\DriverController@Reject']);
        Route::get('/driver/vehicle/{id}', ['as' => 'merchant.driver-vehicle', 'uses' => 'Merchant\DriverController@Vehicles']);
        Route::get('/driver/vehicle/edit/{id}', ['as' => 'merchant.driver-vehicle.edit', 'uses' => 'Merchant\DriverController@EditVehicle']);
        Route::post('/driver/vehicle/update/{id}', ['as' => 'merchant.driver-vehicle.update', 'uses' => 'Merchant\DriverController@UpdateVehicle']);

        Route::get('/tempDoc/verify/{id}/{status}', ['as' => 'merchant.driverTempDocVerify', 'uses' => 'Merchant\DriverController@TempDocumentVerify']);
        Route::post('/tempDoc/reject/', ['as' => 'merchant.driverTempDocReject', 'uses' => 'Merchant\DriverController@rejectTempDoc']);

        Route::get('/vehicle/verify/{id}/{status}', ['as' => 'merchant.driver-vehicle-verify', 'uses' => 'Merchant\DriverController@verifyDriver']); // status 1 : approve vehicle & 2: document approve
        Route::post('/vehicle/reject/', ['as' => 'merchant.driver-vehicle-reject', 'uses' => 'Merchant\DriverController@rejectDriver']);
//        Route::get('/vehicle/details/{id}', ['as' => 'merchant.driver-vehicledetails', 'uses' => 'Merchant\DriverController@VehiclesDocument']);
        Route::get('/vehicle/details/{id}', ['as' => 'merchant.driver-vehicledetails', 'uses' => 'Merchant\DriverController@VehiclesDetail']);

        Route::get('/vehicle/document/{id}/{status}', ['as' => 'merchant.driver-vehicledocument', 'uses' => 'Merchant\DriverController@VehiclesDocumentVerify']);
        Route::post('/vehicle/rejectdocument', ['as' => 'merchant.driver-vehiclereject', 'uses' => 'Merchant\DriverController@VehiclesDocumentReject']);
        Route::get('/booking/track/{id}', ['as' => 'merchant.activeride.track', 'uses' => 'Merchant\BookingController@ActiveBookingTrack']);
        Route::get('/booking/{slug}/activeride', ['as' => 'merchant.activeride', 'uses' => 'Merchant\BookingController@index']);
        Route::post('/booking/{slug}/activeride', ['as' => 'merchant.activeride.serach', 'uses' => 'Merchant\BookingController@SearchForActiveRide']);
        Route::get('/booking/{slug}/autocancel', ['as' => 'merchant.autocancel', 'uses' => 'Merchant\BookingController@AutoCancel']);
        Route::get('/booking/{slug}/autocancel/search', ['as' => 'merchant.autocancel.serach', 'uses' => 'Merchant\BookingController@SearchForAutoCancel']);
        Route::get('/booking/{slug}/all', ['as' => 'merchant.all.ride', 'uses' => 'Merchant\BookingController@AllRides']);
        Route::get('/booking/{slug}/all/search', ['as' => 'merchant.all.serach', 'uses' => 'Merchant\BookingController@SearchForAllRides']);
        Route::get('/booking/{slug}/activeride/search', ['as' => 'merchant.activeride.later', 'uses' => 'Merchant\BookingController@SearchForActiveLaterRide']);
        Route::post('/booking/{slug}/activeride/search', ['as' => 'merchant.activeride.later.serach', 'uses' => 'Merchant\BookingController@SearchForActiveLaterRide']);
        Route::get('/booking/{slug}/cancel', ['as' => 'merchant.cancelride', 'uses' => 'Merchant\BookingController@CancelBooking']);
        Route::get('/booking/{slug}/cancel/search', ['as' => 'merchant.cancelride.search', 'uses' => 'Merchant\BookingController@SearchCancelBooking']);
        Route::get('/booking/{slug}/complete', ['as' => 'merchant.completeride', 'uses' => 'Merchant\BookingController@CompleteBooking']);
        Route::get('/booking/{slug}/complete/search', ['as' => 'merchant.completeride.search', 'uses' => 'Merchant\BookingController@SerachCompleteBooking']);
        Route::get('/booking/{slug}/failride', ['as' => 'merchant.failride', 'uses' => 'Merchant\BookingController@FailedBooking']);
        Route::get('/booking/{slug}/failride/search', ['as' => 'merchant.failride.search', 'uses' => 'Merchant\BookingController@SearchFailedBooking']);
        Route::post('/booking/cancelbooking', ['as' => 'merchant.cancelbooking', 'uses' => 'Merchant\BookingController@CancelBookingAdmin']);
        Route::post('/booking/completebooking', ['as' => 'merchant.completebooking', 'uses' => 'Merchant\BookingController@CompleteBookingAdmin']);
        Route::get('/booking/{id}', ['as' => 'merchant.booking.details', 'uses' => 'Merchant\BookingController@BookingDetails']);
        Route::get('/booking/invoice/{id}', ['as' => 'merchant.booking.invoice', 'uses' => 'Merchant\BookingController@Invoice']);
        Route::get('/ride/request/{id}', ['as' => 'merchant.ride-requests', 'uses' => 'Merchant\BookingController@DriverRequest']);
        Route::get('/ride/requestRides/{id}', ['as' => 'merchant.requestRides', 'uses' => 'Merchant\BookingController@requestRides']);
        Route::any('/findNearDriver', ['as' => 'BookingStatusWaiting', 'uses' => 'Merchant\BookingController@checkBookingStatusWaiting']);
        Route::get('/transactions', ['as' => 'merchant.transactions', 'uses' => 'Merchant\TransactionController@index']);
        Route::get('/transactions/search', ['as' => 'merchant.transactions.search', 'uses' => 'Merchant\TransactionController@Search']);
        Route::get('/transactions/billdetails', ['as' => 'merchant.billdetails.search', 'uses' => 'Merchant\TransactionController@GetBillDetails']);
        Route::get('/customer_support', ['as' => 'merchant.customer_support', 'uses' => 'Merchant\DashBoardController@Customer_Support']);
        Route::post('/customer_support', ['as' => 'merchant.customer_support.search', 'uses' => 'Merchant\DashBoardController@Customer_Support_Search']);
        Route::post('/AddManualUser', ['as' => 'merchant.AddManualUser', 'uses' => 'Merchant\ManualDispatchController@AddManualUser']);
        Route::get('/change-language/{locale}', ['as' => 'merchant.language', 'uses' => 'Merchant\DashBoardController@SetLangauge']);
        Route::post('/booking/rating',['as' => 'merchant.booking.rating','uses' => 'Merchant\BookingController@rateBooking']);

        ////email
        Route::get('/emailtemplate', ['as' => 'merchant.emailtemplate', 'uses' => 'Merchant\emailTemplateController@index']);
        Route::post('/saveemailtemplate', ['as' => 'merchant.emailtemplate.store', 'uses' => 'Merchant\emailTemplateController@store']);
//        Route::post('/saveemailconfig', ['as' => 'merchant.emailconfig.store', 'uses' => 'Merchant\emailTemplateController@configstore']);

        //config

        Route::get('/general_configuration', ['as' => 'merchant.general_configuration', 'uses' => 'Merchant\ConfigurationController@GeneralConfiguration']);
        Route::post('/general_configuration', ['as' => 'merchant.general_configuration.store', 'uses' => 'Merchant\ConfigurationController@StoreGeneralConfiguration']);

        Route::get('/payment-configuration', ['as' => 'merchant.payment-configuration', 'uses' => 'Merchant\ConfigurationController@paymentConfiguration']);
        Route::post('/payment-configuration', ['as' => 'merchant.payment-configuration.store', 'uses' => 'Merchant\ConfigurationController@paymentConfigurationStore']);


        Route::get('/booking_configuration', ['as' => 'merchant.booking_configuration', 'uses' => 'Merchant\ConfigurationController@BookingConfiguration']);
        Route::post('/booking_configuration', ['as' => 'merchant.booking_configuration.store', 'uses' => 'Merchant\ConfigurationController@StoreBookingConfiguration']);

        Route::get('/app_configuration', ['as' => 'merchant.application_configuration', 'uses' => 'Merchant\ConfigurationController@ApplicationConfiguration']);
        Route::post('/app_configuration', ['as' => 'merchant.application_configuration.store', 'uses' => 'Merchant\ConfigurationController@StoreApplicationConfiguration']);
//        Route::get('/driverinfo/{id}', ['as' => 'merchant.driverinfo', 'uses' => 'Merchant\DriverController@DriverProfile']);
        Route::resource('navigation-drawer', 'Merchant\NavigationController', ['only' => ['index', 'edit', 'update']]);
        Route::get('/navigation/active/deactive/{id}/{status}', ['as' => 'merchant.navigations.active-deactive', 'uses' => 'Merchant\NavigationController@ChangeStatus']);


        Route::get('/user/Alldocument', 'Merchant\UserController@AlldocumentStatus')->name('merchant.user.AlldocumentStatus');
        Route::get('/user/document/status', 'Merchant\UserController@ChangeDocumentStatus')->name('merchant.user.documentStatus');
        Route::get('/user/{id}/documents', 'Merchant\UserController@showDocuments')->name('merchant.user.documents');
        Route::get('/report/driver/online/time', ['as' => 'report.driver.online.time', 'uses' => 'Merchant\ReportController@DriverOnlineTime']);
        Route::get('/report/driver/online/time/search', ['as' => 'report.driver.online.time.search', 'uses' => 'Merchant\ReportController@SearchDriverOnlineTime']);

        Route::get('/viewDriverInvoice/{id}', ['as' => 'merchant.viewDriverInvoice', 'uses' => 'Merchant\DriverAccountController@viewDriverInvoice']);
        Route::post('/BillDriverEmail', ['as' => 'merchant.billDriverEmail', 'uses' => 'Merchant\DriverAccountController@DriverBillEmail']);
        Route::post('/Driver_unblock', ['as' => 'Driver_unblock', 'uses' => 'Merchant\DriverController@driver_unblock']);
        Route::get('/DriverBill/{id}', ['as' => 'merchant.DriverBill', 'uses' => 'Merchant\DriverAccountController@DriverBill']);

        Route::get('/block/drivers/', ['as' => 'merchant.driver.block', 'uses' => 'Merchant\DriverController@BlockDrivers']);
        Route::post('/Driver_unblock', ['as' => 'Driver_unblock', 'uses' => 'Merchant\DriverController@driver_unblock']);
        Route::get('/pending_rider_approval', ['as' => 'pending_rider_approval', 'uses' => 'Merchant\UserController@PendingRiderList']);
        Route::post('/pending_search_approval', ['as' => 'pending_search_approval', 'uses' => 'Merchant\UserController@PendingSearch']);


//        Route::post('/get-vehicle-types', ['as' => 'merchant.get-vehicle-types', 'uses' => 'Helper\AjaxController@getVehicleTypesByDelivery']);
//        Route::post('/get-delivery-types', ['as' => 'merchant.get-delivery-types', 'uses' => 'Helper\AjaxController@getDeliveryTypes']);


        Route::resource('applicationstring', 'Merchant\ApplicatonStringController');
        Route::get('customEdit', 'Merchant\ApplicatonStringController@customEdit')->name('customEdit');
        Route::post('customSave', 'Merchant\ApplicatonStringController@customSave')->name('customSave');
        Route::get('customstring', 'Merchant\ApplicatonStringController@custom')->name('customstring');
        Route::get('get-string-val', ['as' => 'admin-app-string', 'uses' => 'Merchant\ApplicatonStringController@getStringVal']);
        Route::post('exportString', 'Merchant\ApplicatonStringController@ExportString')->name('exportString');

        // Create @Bhuvanesh - For edit driver vehicle document
        Route::get('/driver/editVehicleDocument/{id}/{vehicle}', ['as' => 'driver.edit.driver-vehicle-document', 'uses' => 'Merchant\DriverController@editDriverVehicleDocument']);
        Route::post('/driver/editVehicleDocument/{id}/{vehicle}', ['as' => 'driver.store.driver-vehicle-document', 'uses' => 'Merchant\DriverController@storeDriverVehicleDocument']);

//        Route::resource('corporate', 'Merchant\CorporateController');

        Route::get('/corporate', ['as' => 'corporate.index', 'uses' => 'Merchant\CorporateController@index']);
        Route::get('/corporate/add/{id?}', ['as' => 'corporate.create', 'uses' => 'Merchant\CorporateController@add']);
        Route::post('/corporate/save/{id?}', ['as' => 'corporate.store', 'uses' => 'Merchant\CorporateController@save']);

        Route::get('/corporate/status/{id}/{status}', ['as' => 'merchant.corporate.status', 'uses' => 'Merchant\CorporateController@ChangeStatus']);
        Route::post('/corporate/add-money', ['as' => 'corporate.AddMoney', 'uses' => 'Merchant\CorporateController@AddMoney']);
        Route::get('/corporate/wallet/{id}', ['as' => 'corporate.wallet.show', 'uses' => 'Merchant\CorporateController@Wallet']);

        Route::resource('signupwalletrecharge', 'Merchant\SignUpWalletRechargeController');

        // Create @Bhuvanesh - For Advertisement Banner
        Route::get('/advertisement/banner', 'Merchant\AdvertisementBannerController@index')->name('advertisement.index');
        Route::get('/advertisement/banner/create/{id?}', 'Merchant\AdvertisementBannerController@create')->name('advertisement.create');
        Route::post('/advertisement/banner/store/{id?}', 'Merchant\AdvertisementBannerController@store')->name('advertisement.store');
        Route::get('/advertisement/active/deactive/{id}/{status}', ['as' => 'advertisement.active.deactive', 'uses' => 'Merchant\AdvertisementBannerController@ChangeStatus']);
        Route::get('/advertisement/delete', ['as' => 'advertisement.delete', 'uses' => 'Merchant\AdvertisementBannerController@Delete']);
        Route::get('/advertisement/get-business-segment',['as' => 'advertisement.get.business-segment', 'uses' => 'Merchant\AdvertisementBannerController@getBusinessSegment']);

        // for demo
        Route::get('/driver-list', ['as' => 'driver.detail-list', 'uses' => 'Merchant\DriverController@DetailList']);
        Route::post('/verify-otp', ['as' => 'driver.otp-verification', 'uses' => 'Merchant\DriverController@verfiyOtp']);

        // for demo user
        Route::get('/user-list', ['as' => 'user.detail-list', 'uses' => 'Merchant\UserController@UserList']);
        Route::post('/user-verify-otp', ['as' => 'user.otp-verification', 'uses' => 'Merchant\UserController@verfiyOtp']);


        // for geofence
        Route::get('geofence/restrict',['as' => 'geofence.restrict.index', 'uses' => 'Merchant\GeofenceRestrictedAreaController@RestrictedArea']);
        Route::get('geofence/restrict/edit/{id}',['as' => 'geofence.restrict.edit', 'uses' => 'Merchant\GeofenceRestrictedAreaController@EditRestrictedArea']);
        Route::post('geofence/restrict/save/{id}',['as' => 'geofence.restrict.save', 'uses' => 'Merchant\GeofenceRestrictedAreaController@SaveRestrictedArea']);
        Route::get('geofence/view/{id}', ['as'=>'geofence.restrict.viewgeofencequeue', 'uses'=> 'Merchant\GeofenceRestrictedAreaController@ViewGeofenceQueue']);
        Route::post('geofence/view/search/{id}', ['as'=>'geofence.restrict.viewgeofencequeue.search', 'uses'=> 'Merchant\GeofenceRestrictedAreaController@SearchViewGeofenceQueue']);

        Route::post('checkOutstationDropArea', ['as' => 'merchant.manual.checkArea', 'uses' => 'Merchant\ManualDispatchController@checkOutstationDropArea']);

        // Stripe Connect
        Route::get('/stripe_connect_configuration', ['as' => 'merchant.stripe_connect_configuration', 'uses' => 'Merchant\ConfigurationController@stripeConnectConfiguration']);
        Route::post('/stripe_connect_configuration', ['as' => 'merchant.stripe_connect_configuration.store', 'uses' => 'Merchant\ConfigurationController@stripeConnectConfigurationStore']);

        // Stripe Connect
        Route::get('driver/stripe-connect/{id}', ['as' => 'merchant.driver.stripe_connect', 'uses' => 'Merchant\DriverController@driverStripeConnect']);
        Route::post('driver/stripe-connect/{id}', ['as' => 'merchant.driver.stripe_connect.store', 'uses' => 'Merchant\DriverController@driverStripeConnectStore']);
        Route::get('driver/stripe-connect/sync/{id}', ['as' => 'merchant.driver.stripe_connect.sync', 'uses' => 'Merchant\DriverController@driverStripeConnectSync']);
        Route::get('driver/stripe-connect/delete/{id}', ['as' => 'merchant.driver.stripe_connect.delete', 'uses' => 'Merchant\DriverController@driverStripeConnectDelete']);


        /* Business Segment*/

        Route::get('business-segment/add/{slug}/{id?}', ['as' => 'merchant.business-segment/add','uses' => 'Merchant\BusinessSegmentController@add']);
        Route::post('business-segment/save/{slug}/{id?}', ['as' => 'merchant.business-segment.save','uses' => 'Merchant\BusinessSegmentController@save']);

        Route::get('business-segment/{slug}', ['as' =>'merchant.business-segment','uses' =>  'Merchant\BusinessSegmentController@index']);
        Route::get('business-segment/statistics/{slug}/{b_id?}', ['as' =>'merchant.business-segment.statistics','uses' =>  'Merchant\BusinessSegmentController@statistics']);
        Route::get('business-segment/orders/{slug}/{id?}', ['as' =>'merchant.business-segment.orders','uses' =>  'Merchant\BusinessSegmentController@orders']);

        /* Style Management*/
        Route::get('style-management',['as'=>'merchant.style-management','uses'=>'Merchant\StyleManagementController@index']);
        Route::get('style-management-add/{id?}',['as'=>'merchant.style-management.add','uses'=>'Merchant\StyleManagementController@add']);
        Route::post('style-management-save/{id?}',['as'=>'merchant.style-management.save','uses'=>'Merchant\StyleManagementController@save']);
        Route::post('style-management-delete/',['as'=>'merchant.style-management.destroy','uses'=>'Merchant\StyleManagementController@destroy']);

        /* Product Management Category*/
        Route::get('/category',['as'=>'merchant.category','uses'=>'Merchant\CategoryController@index']);
        Route::get('/category-add/{id?}',['as'=>'business-segment.category.add','uses'=>'Merchant\CategoryController@add']);
        Route::post('/category-save/{id?}',['as'=>'business-segment.category.save','uses'=>'Merchant\CategoryController@save']);
        Route::post('/category-delete/',['as'=>'business-segment.category.destroy','uses'=>'Merchant\CategoryController@destroy']);
        Route::get('/category-export/',['as'=>'merchant.category.export','uses'=>'ExcelController@categories']);

        /* Product order*/
        Route::get('/order',['as'=>'order.index','uses'=>'BusinessSegment\OrderController@index']);
        Route::get('/order/search',['as'=>'order.search','uses'=>'BusinessSegment\OrderController@index']);
        Route::get('/excel/order', ['as' => 'excel.order', 'uses' => 'ExcelController@PriceCard']);

        /*Segment update*/
         Route::get('/segment/edit/{id?}', ['as' => 'merchant.segment.edit', 'uses' => 'Merchant\ServiceTypeController@editSegment']);
         Route::post('/segment/update/{id?}', ['as' => 'merchant.segment.update', 'uses' => 'Merchant\ServiceTypeController@updateSegment']);

         /**HandyMan Segment PriceCard */
        Route::get('/segment/price-cards', ['as' => 'merchant.segment.price_card', 'uses' => 'Segment\SegmentPriceCardController@index']);
        Route::get('/segment/price-card/add/{id?}', ['as' => 'segment.price_card.add', 'uses' => 'Segment\SegmentPriceCardController@add']);
        Route::post('/segment/price-card/save/{id?}', ['as' => 'segment.price_card.save', 'uses' => 'Segment\SegmentPriceCardController@save']);
        Route::post('/segment/price-card/services', ['as' => 'segment.price_card.services', 'uses' => 'Segment\SegmentPriceCardController@getSegmentPriceCardServices']);

        /**HandyMan Segment Service Time slot */
        Route::get('/segment/service-time-slot', ['as' => 'segment.service-time-slot', 'uses' => 'Segment\ServiceTimeSlotController@index']);
        Route::get('/segment/service-time-slot/add/{id?}', ['as' => 'segment.service-time-slot.add', 'uses' => 'Segment\ServiceTimeSlotController@add']);
        Route::post('/segment/service-time-slot/save/{id?}', ['as' => 'segment.service-time-slot.save', 'uses' => 'Segment\ServiceTimeSlotController@save']);
        Route::get('/segment/service-time-slot/detail/add/{id}', ['as' => 'service-time-slot.detail', 'uses' => 'Segment\ServiceTimeSlotController@getSlotDetail']);
        Route::post('/segment/service-time-slot/detail/save/', ['as' => 'service-time-slot.detail.save', 'uses' => 'Segment\ServiceTimeSlotController@saveSlotDetail']);

        /**HandyMan Segment Service Time slot */
        Route::get('/segment/handyman-charge-type', ['as' => 'segment.handyman-charge-type', 'uses' => 'Segment\HandymanChargeTypeController@index']);
        Route::get('/segment/handyman-charge-type/add/{id?}', ['as' => 'segment.handyman-charge-type.add', 'uses' => 'Segment\HandymanChargeTypeController@add']);
        Route::post('/segment/handyman-charge-type/save/{id?}', ['as' => 'segment.handyman-charge-type.save', 'uses' => 'Segment\HandymanChargeTypeController@save']);


        /**HandyMan's Segment orders */
//        Route::get('/handyman/plumber/orders', ['as' => 'handyman.plumber.orders', 'uses' => 'Merchant\HandymanOrderController@plumberOrders']);
//        Route::get('/handyman/plumber/order/search', ['as' => 'merchant.plumber.order.search', 'uses' => 'Merchant\HandymanOrderController@plumberOrders']);
//        Route::get('/handyman/electrician/orders', ['as' => 'handyman.electrician.orders', 'uses' => 'Merchant\HandymanOrderController@electricianOrders']);
//        Route::get('/handyman/electrician/order/search', ['as' => 'handyman.electrician.order.search', 'uses' => 'Merchant\HandymanOrderController@electricianOrders']);

        Route::get('/handyman/orders', ['as' => 'handyman.orders', 'uses' => 'Merchant\HandymanOrderController@orders']);
        Route::get('/handyman/order/detail/{id}', ['as' => 'merchant.handyman.order.detail', 'uses' => 'Merchant\HandymanOrderController@orderDetail']);
        
        // send handyman booking invoice
        Route::get('/send-invoice/{id}', ['as' => 'admin.send-invoice', 'uses' => 'Merchant\HandymanOrderController@sendInvoice']);


        Route::get('/handyman/flutterwavePaymentRequest', ['as' => 'merchant.handyman.flutterwavePaymentRequest', 'uses' => 'Merchant\HandymanOrderController@flutterwayPaymentRequest']);
        
        Route::get('/handyman/verifyFlutterwaveTransaction', ['as' => 'merchant.handyman.verifyFlutterwaveTransaction', 'uses' => 'Merchant\HandymanOrderController@verifyFlutterwaveTransaction']);

        /*Delivery Product*/

        Route::resource('delivery_product','Merchant\DeliveryProductController');
        Route::get('delivery_product/change-status/{id}/{status}','Merchant\DeliveryProductController@ChangeStatus')->name('delivery_product.change_status');

        // driver order details
        Route::get('/driver/order/detail/{id}', ['as' => 'driver.order.detail', 'uses' => 'Merchant\DriverController@orderDetail']);

        /**HandyMan Segment PriceCard */
        Route::get('/food-grocery/pricecard/{price_card_for}', ['as' => 'food-grocery.price_card', 'uses' => 'Merchant\PriceCardController@indexFoodGrocery']);
        Route::get('/food-grocery/price-card/add/{price_card_for}/{id?}', ['as' => 'food-grocery.price_card.add', 'uses' => 'Merchant\PriceCardController@addFoodGrocery']);
        Route::post('/food-grocery/price-card/save/{id?}', ['as' => 'food-grocery.price_card.save', 'uses' => 'Merchant\PriceCardController@saveFoodGrocery']);

        // for Driver Cashout
        Route::get('drivers/cashout/request', ['as' => 'merchant.driver.cashout_request', 'uses' => 'Merchant\DriverCashoutController@index']);
        Route::get('drivers/cashout/request/search', ['as' => 'merchant.driver.cashout_request.search', 'uses' => 'Merchant\DriverCashoutController@search']);
        Route::get('drivers/cashout/status/{id}', ['as' => 'merchant.driver.cashout_status', 'uses' => 'Merchant\DriverCashoutController@changeStatus']);
        Route::post('drivers/cashout/status/{id}', ['as' => 'merchant.driver.cashout_status_update', 'uses' => 'Merchant\DriverCashoutController@changeStatusUpdate']);

        // for Business segment Cashout
        Route::get('business-segment/cashout/request', ['as' => 'merchant.business-segment.cashout_request', 'uses' => 'Merchant\BusinessSegmentController@cashoutRequest']);
        Route::get('business-segment/cashout/status/{id}', ['as' => 'merchant.business-segment.cashout_status', 'uses' => 'Merchant\BusinessSegmentController@cashoutChangeStatus']);
        Route::post('business-segment/cashout/status/{id}', ['as' => 'merchant.business-segment.cashout_status_update', 'uses' => 'Merchant\BusinessSegmentController@cashoutChangeStatusUpdate']);
        // for Business segment Order Details
        Route::get('/business-segment/order/detail/{id}', ['as' => 'merchant.business-segment.order.detail', 'uses' => 'Merchant\BusinessSegmentController@orderDetail']);

        /**HandyMan Segment Commission */
        Route::get('/segment/commissions', ['as' => 'merchant.segment.commission', 'uses' => 'Segment\HandymanCommissionController@index']);
        Route::get('/segment/commission/add/{id?}', ['as' => 'segment.commission.add', 'uses' => 'Segment\HandymanCommissionController@add']);
        Route::post('/segment/commission/save/{id?}', ['as' => 'segment.commission.save', 'uses' => 'Segment\HandymanCommissionController@save']);

        /*Option*/
        Route::get('/option-type',['as'=>'merchant.option-type.index','uses'=>'Merchant\OptionTypeController@index']);
        Route::get('/option-type/add/{id?}',['as'=>'merchant.option-type.add','uses'=>'Merchant\OptionTypeController@add']);
        Route::get('/option-type/active/deactive/{id}/{status}', ['as' => 'merchant.option-type.active-deactive', 'uses' => 'Merchant\OptionTypeController@ChangeStatus']);
        Route::post('/option-type/save/{id?}',['as'=>'merchant.option-type.save','uses'=>'Merchant\OptionTypeController@save']);
        Route::get('/option-type/delete/{id}',['as'=>'merchant.option-type.delete','uses'=>'Merchant\OptionTypeController@destroy']);

        // merchant's reports
        Route::get('/taxi-services/reports',['as'=>'merchant.taxi-services-report','uses'=>'Merchant\BookingController@taxiServicesEarning']);
        Route::get('/taxi-earning/export',['as'=>'merchant.taxi.earning.export','uses'=>'ExcelController@taxiServicesEarningExport']);
        Route::get('/handyman-services/reports',['as'=>'merchant.handyman-services-report','uses'=>'Merchant\HandymanOrderController@handymanServicesEarning']);
        Route::get('/handyman-earning/export',['as'=>'merchant.handyman-service.earning.export','uses'=>'ExcelController@handymanServicesEarningExport']);
        Route::get('/delivery-services/reports',['as'=>'merchant.delivery-services-report','uses'=>'BusinessSegment\OrderController@orderEarningSummary']);
        Route::get('/delivery-services/export',['as'=>'merchant.delivery-services-report.export','uses'=>'ExcelController@orderEarningSummary']);
        Route::get('/report/referral', ['as' => 'report.referral', 'uses' => 'Merchant\ReferralSystemController@referralReport']);
        Route::get('/report/referral/receiver-details', ['as' => 'report.referral.receiver-details', 'uses' => 'Merchant\ReferralSystemController@getReferralReceiverDetails']);


        // driver's report
        Route::get('/driver-earning',['as'=>'merchant.driver.earning','uses'=>'Merchant\DriverController@earningSummary']);
        Route::get('/driver-taxi-services/reports',['as'=>'merchant.driver-taxi-services-report','uses'=>'Merchant\DriverController@driverRideEarning']);
        Route::get('/driver-delivery-services/reports',['as'=>'merchant.driver-delivery-services-report','uses'=>'Merchant\DriverController@driverOrderEarning']);
        Route::get('/driver-handyman-services/reports',['as'=>'merchant.driver-handyman-services-report','uses'=>'Merchant\DriverController@driverHandymanServicesEarning']);

        // Wallet Report
        Route::get("/transaction/wallet-recharge/{slug}", ['as' => 'transaction.wallet-report', 'uses' => 'Merchant\TransactionController@walletReport']);
        Route::get("/transaction/wallet-recharge-report", ['as' => 'transaction.wallet-report.export', 'uses' => 'Merchant\TransactionController@walletReportExport']);

        /** Place order from admin panel **/
        Route::get('/place-order/step-one', ['as' => 'merchant.place-order.step-one', 'uses' => 'Merchant\OrderController@stepOne']);

        // Payment Gateway Transactions
        Route::get('/payment_gateway/transactions', ['as' => 'payment.gateway.transactions', 'uses' => 'Merchant\TransactionController@PaymentGatewayTransactions']);
        Route::post('/get/card-details', ['as' => 'merchant.get_card_details', 'uses' => 'Merchant\TransactionController@GetCardDetails']);


        // Driver Agency module
        Route::get('driver-agency', ['as' =>'merchant.driver-agency','uses' =>  'Merchant\DriverAgencyController@index']);
        Route::get('driver-agency/add/{id?}', ['as' => 'merchant.driver-agency.add','uses' => 'Merchant\DriverAgencyController@add']);
        Route::post('driver-agency/save/{id?}', ['as' => 'merchant.driver-agency.save','uses' => 'Merchant\DriverAgencyController@save']);
        Route::get('driver-agency/status-update/{id}', 'Merchant\DriverAgencyController@statusUpdate')->name('driver-agency.status');
        Route::post('/driver-agency/add-money', ['as' => 'driver-agency.add-wallet', 'uses' => 'Merchant\DriverAgencyController@AddMoney']);

        Route::get('/driver-agency/wallet/{id}', ['as' => 'merchant.driver-agency.wallet.show', 'uses' => 'Merchant\DriverAgencyController@Wallet']);
        Route::get('/driver-agency/transactions/{id}', ['as' => 'merchant.driver-agency.transactions', 'uses' => 'Merchant\TransactionController@DriverAgencyTransaction']);
        Route::post('/driver-agency/transactions/{id}', ['as' => 'merchant.driver-agency.transactions.search', 'uses' => 'Merchant\TransactionController@TaxiCompanySearch']);

        // drivers of driver-agency
        Route::get('/driver-agency/drivers', ['as' => 'merchant.driver-agency.drivers', 'uses' => 'Merchant\DriverController@getDriverAgencyDrivers']);

        // handyman booking export
        Route::get('/handyman-booking-export', ['as' => 'merchant.handyman-booking-export', 'uses' => 'ExcelController@exportHandymanBookings']);

    });
});

//Corporate Panel
Route::prefix('corporate/admin')->group(function () {
    Route::group(['middleware' => ['guest:corporate']], function () {
        Route::get('{merchant_alias_name}/{alias_name}/login', 'Auth\CorporateLoginController@showLoginForm')->name('corporate.login');
        Route::post('/login/{alias_name}', 'Auth\CorporateLoginController@login')->name('corporate.login.submit');
    });
    Route::group(['middleware' => ['auth:corporate','admin_language']], function () {
        Route::get('/dashboard', 'Corporate\CorporateHomeController@dashboard')->name('corporate.dashboard');
        Route::get('/logout', 'Auth\CorporateLoginController@logout')->name('corporate.logout');
        Route::get('/profile', 'Corporate\CorporateHomeController@Profile')->name('corporate.profile');
        Route::post('/update/profile', 'Corporate\CorporateHomeController@UpdateProfile')->name('corporate.update.profile');

        //User
        Route::get('/user', 'Corporate\UserController@index')->name('user.index');
        Route::get('/user/create', 'Corporate\UserController@create')->name('corporate.user.create');
        Route::post('/user/store', 'Corporate\UserController@store')->name('corporate.user.store');
        Route::get('/user/edit/{id}', 'Corporate\UserController@edit')->name('corporate.user.edit');
        Route::any('/user/update/{id}', 'Corporate\UserController@update')->name('corporate.user.update');
        Route::get('/user/show/{id}', 'Corporate\UserController@show')->name('corporate.user.show');
        Route::get('/user/favourite/{id}', 'Corporate\UserController@FavouriteLocation')->name('corporate.user.favourite');
        Route::get('/user/favourite/driver/{id}', 'Corporate\UserController@FavouriteDriver')->name('corporate.user.favourite.driver');
        Route::get('/user/changeStatus/{id}/{status}', 'Corporate\UserController@ChangeStatus')->name('corporate.user.change.status');
        Route::post('/user/destroy', 'Corporate\UserController@destroy')->name('corporate.user.destroy');
        Route::post('/user/search', 'Corporate\UserController@Search')->name('corporate.user.search');
        Route::post('/user/import', 'Corporate\UserController@ImportUserData')->name('corporate.user.import');
        Route::get('/user/import/fail', 'Corporate\UserController@FailImports')->name('corporate.user.import.fail');
        Route::post('/user/import/fail/destroy', 'Corporate\UserController@FailImportDelete')->name('corporate.user.import.fail.destroy');

        //Manual Dispatch
        Route::get('/manualDispatch', 'Corporate\ManualDispatchController@index')->name('corporate.manualDispatch');
        Route::post('/getArea', 'Corporate\ManualDispatchController@CorporateAreaList')->name('corporate.getArea');
        Route::post('/Search/CorporateUser', 'Corporate\ManualDispatchController@SearchUser')->name('corporate.SearchUser');
        Route::post('/getCorporateServices', 'Helper\AjaxController@ServiceType');
        Route::post('/getCorporateRideConfig', 'Helper\AjaxController@VehicleConfig');
        Route::post('/getCorporateVehicle', 'Helper\AjaxController@VehicleType');
        Route::post('/getCorporatePromoCode', 'Corporate\ManualDispatchController@PromoCode');
        Route::post('/checkCorporatePriceCard', 'Helper\AjaxController@PriceCard');
        Route::post('/estimatePriceCorporate', 'Corporate\ManualDispatchController@EstimatePrice');
        Route::post('/getCorporatePromoCodeEta', 'Corporate\ManualDispatchController@PromoCodeEta');
        Route::post('/getallDriverCorporate', 'Corporate\ManualDispatchController@AllDriver');
        Route::post('/checkCorporateDriver', 'Corporate\ManualDispatchController@CheckDriver');
        Route::post('/AddManualUser', 'Corporate\ManualDispatchController@AddManualUser');
        Route::post('/manualdispach', ['as' => 'corporate.book.manual.dispach', 'uses' => 'Corporate\ManualDispatchController@BookingDispatch']);

        //Rides Management

        Route::get('/booking/activeride', ['as' => 'corporate.activeride', 'uses' => 'Corporate\BookingController@index']);
        Route::get('/booking/complete', ['as' => 'corporate.completeride', 'uses' => 'Corporate\BookingController@CompleteBooking']);
        Route::get('/booking/cancel', ['as' => 'corporate.cancelride', 'uses' => 'Corporate\BookingController@CancelBooking']);
        Route::get('/booking/failride', ['as' => 'corporate.failride', 'uses' => 'Corporate\BookingController@FailedBooking']);
        Route::get('/booking/autocancel', ['as' => 'corporate.autocancel', 'uses' => 'Corporate\BookingController@AutoCancel']);
        Route::get('/booking/all', ['as' => 'corporate.all.ride', 'uses' => 'Corporate\BookingController@AllRides']);
        Route::get('/ride/request/{id}', ['as' => 'corporate.ride-requests', 'uses' => 'Corporate\BookingController@DriverRequest']);
        Route::get('/booking/{id}', ['as' => 'corporate.booking.details', 'uses' => 'Corporate\BookingController@BookingDetails']);
        Route::get('/booking/track/{id}', ['as' => 'corporate.activeride.track', 'uses' => 'Corporate\BookingController@ActiveBookingTrack']);
        Route::get('/booking/invoice/{id}', ['as' => 'corporate.booking.invoice', 'uses' => 'Corporate\BookingController@Invoice']);

        Route::post('/booking/complete/search', ['as' => 'corporate.completeRide.search', 'uses' => 'Corporate\BookingController@SerachCompleteBooking']);
        Route::get('/booking/cancel/search', ['as' => 'corporate.cancelRide.search', 'uses' => 'Corporate\BookingController@SearchCancelBooking']);
        Route::post('/booking/failRide', ['as' => 'corporate.failRide.search', 'uses' => 'Corporate\BookingController@SearchFailedBooking']);
        Route::post('/booking/autoCancel', ['as' => 'corporate.autoCancel.search', 'uses' => 'Corporate\BookingController@SearchForAutoCancel']);
        Route::post('/booking/all', ['as' => 'corporate.all.search', 'uses' => 'Corporate\BookingController@SearchForAllRides']);

        //Transactions

        Route::get('/transactions', ['as' => 'corporate.transactions', 'uses' => 'Corporate\TransactionController@index']);
        Route::post('/transactions', ['as' => 'corporate.transactions.search', 'uses' => 'Corporate\TransactionController@Search']);

        //Employee Designation
        Route::resource('/employeeDesignation', 'Corporate\EmployeeDesignationController');
        Route::post('/employeeDesignation/update', ['as' => 'employee.Designation.update', 'uses' => 'Corporate\EmployeeDesignationController@updateDesignation']);
        Route::post('/employeeDesignation/delete', ['as' => 'employee.Designation.delete', 'uses' => 'Corporate\EmployeeDesignationController@Delete']);
    });
});

Route::prefix('business-segment/admin')->group(function () {
    Route::get('/generate-invoice/{business_segment}/{id}',['as' => 'business-segment.generate-invoice', 'uses' => 'BusinessSegment\OrderController@generateInvoice']);
    Route::get('/view-invoice/{business_segment}/{id}',['as' => 'business-segment.view-invoice', 'uses' => 'BusinessSegment\OrderController@viewInvoice']);
    Route::group(['middleware' => ['guest:business-segment']], function () {
        Route::get('{merchant_alias_name}/{alias}/login', 'Auth\BusinessSegmentLoginController@showLoginForm')->name('business-segment.login');
        Route::post('/login/{alias}', 'Auth\BusinessSegmentLoginController@login')->name('business-segment.login.submit');
    });
    Route::group(['middleware' => ['auth:business-segment','admin_language']], function () {
        // logout business segment
        Route::get('/logout', 'Auth\BusinessSegmentLoginController@logout')->name('business-segment.logout');
        Route::get('/dashboard', 'BusinessSegment\BusinessSegmentController@dashboard')->name('business-segment.dashboard');

        // update locale
        Route::get('/change-language/{locale}', ['as' => 'business-segment.language', 'uses' => 'Merchant\DashBoardController@SetLangauge']);

        /* Product Management Category*/

//        Route::get('/category','BusinessSegment\MasterCategoryController@index')->name('business-segment.category.index');
//        Route::get('/category-add/{id?}',['as'=>'business-segment.category.add','uses'=>'BusinessSegment\MasterCategoryController@add']);
//        Route::post('/category-save/{id?}',['as'=>'business-segment.category.save','uses'=>'BusinessSegment\MasterCategoryController@save']);
//        Route::post('/category-delete/',['as'=>'business-segment.category.destroy','uses'=>'BusinessSegment\MasterCategoryController@destroy']);

        /* Product Management Product*/
        Route::get('/product',['as'=>'business-segment.product.index','uses'=>'BusinessSegment\ProductController@index']);
        Route::get('/product/basic-step/{id?}',['as'=>'business-segment.product.basic.add','uses'=>'BusinessSegment\ProductController@basicStep']);
        Route::get('/product-variant/{id}',['as'=>'business-segment.product.variant.index','uses'=>'BusinessSegment\ProductController@productVariant']);
        Route::post('/product/basic-step/save/{id?}',['as'=>'business-segment.product.basic.save','uses'=>'BusinessSegment\ProductController@basicStepSave']);
        Route::post('/product/variant-step/save',['as'=>'business-segment.product.variant.save','uses'=>'BusinessSegment\ProductController@variantStepSave']);
        Route::get('/get-sub-category',['as'=>'business-segment.get.subcategory','uses'=>'BusinessSegment\ProductController@getSubCategory']);
        Route::post('/product-delete/',['as'=>'business-segment.product.destroy','uses'=>'BusinessSegment\ProductController@destroy']);
        Route::get('/product-image-remove/{id}',['as'=>'business-segment.product.image.remove','uses'=>'BusinessSegment\ProductController@productImageRemove']);
        Route::get('/product-variant-delete',['as'=>'business-segment.product.variant.destroy','uses'=>'BusinessSegment\ProductController@varantDestroy']);
        Route::post('/product/options/save',['as'=>'business-segment.product.options.save','uses'=>'BusinessSegment\ProductController@optionStepSave']);

        /* Product Inventory */
        Route::any('/inventory/product/',['as'=>'business-segment.product.inventory.index','uses'=>'BusinessSegment\ProductInventoryController@index']);
        Route::post('/inventory/product/save',['as'=>'business-segment.product.inventory.save','uses'=>'BusinessSegment\ProductInventoryController@save']);

        /* Style Segment*/
        Route::get('/style','BusinessSegment\StyleSegmentController@index')->name('business-segment.style-segment.index');
        Route::post('/style{id?}',['as'=>'business-segment.style-segment.add','uses'=>'BusinessSegment\StyleSegmentController@save']);

        /*Option*/
        Route::get('/option','BusinessSegment\OptionController@index')->name('business-segment.option.index');
        Route::get('/option/add/{id?}',['as'=>'business-segment.option.add','uses'=>'BusinessSegment\OptionController@add']);
        Route::get('/option/active/deactive/{id}/{status}', ['as' => 'business-segment.option.active-deactive', 'uses' => 'BusinessSegment\OptionController@ChangeStatus']);
        Route::post('/option/save/{id?}',['as'=>'business-segment.option.save','uses'=>'BusinessSegment\OptionController@save']);
        Route::get('/option/delete/{id}',['as'=>'business-segment.option.delete','uses'=>'BusinessSegment\OptionController@destroy']);


        // Order management
        Route::get('/order',['as'=>'business-segment.order','uses'=>'BusinessSegment\OrderController@index']);
//        Route::get('/order/search',['as'=>'business-segment.order.search','uses'=>'BusinessSegment\OrderController@index']);

        Route::get('/new-order',['as'=>'business-segment.new-order','uses'=>'BusinessSegment\OrderController@newOrder']);
        Route::get('/today-order',['as'=>'business-segment.today-order','uses'=>'BusinessSegment\OrderController@todayOrder']);
        Route::get('/upcoming-order',['as'=>'business-segment.upcoming-order','uses'=>'BusinessSegment\OrderController@UpcomingOrder']);
//        Route::get('/new-order/search',['as'=>'business-segment.new-order.search','uses'=>'BusinessSegment\OrderController@newOrder']);

//        Route::get('/ongoing-order',['as'=>'business-segment.ongoing-order','uses'=>'BusinessSegment\OrderController@getOngoingOrder']);
//        Route::get('/ongoing-order/search',['as'=>'business-segment.ongoing-order.search','uses'=>'BusinessSegment\OrderController@getOngoingOrder']);

        Route::get('/cancelled-order',['as'=>'business-segment.cancelled-order','uses'=>'BusinessSegment\OrderController@getCancelledOrder']);
        Route::get('/cancelled-order/search',['as'=>'business-segment.cancelled-order.search','uses'=>'BusinessSegment\OrderController@getCancelledOrder']);

        Route::get('/rejected-order',['as'=>'business-segment.rejected-order','uses'=>'BusinessSegment\OrderController@getRejectedOrder']);
        Route::get('/expired-order',['as'=>'business-segment.expired-order','uses'=>'BusinessSegment\OrderController@getExpiredOrder']);
        Route::get('/pending-processing-order',['as'=>'business-segment.pending-process-order','uses'=>'BusinessSegment\OrderController@getPendingProcessingOrder']);
        Route::get('/pick-order-verification',['as'=>'business-segment.pending-pick-order-verification','uses'=>'BusinessSegment\OrderController@getPickupVerificationOrder']);
        Route::get('/order-ontheway',['as'=>'business-segment.order-ontheway','uses'=>'BusinessSegment\OrderController@orderOntheWay']);
//        Route::get('/cancelled-order/search',['as'=>'business-segment.cancelled-order.search','uses'=>'BusinessSegment\OrderController@getCancelledOrder']);

        Route::get('/delivered-order',['as'=>'business-segment.delivered-order','uses'=>'BusinessSegment\OrderController@getDeliveredOrder']);
        Route::get('/delivered-order/search',['as'=>'business-segment.delivered-order.search','uses'=>'BusinessSegment\OrderController@getDeliveredOrder']);

        Route::get('/completed-order',['as'=>'business-segment.completed-order','uses'=>'BusinessSegment\OrderController@getCompletedOrder']);
        Route::get('/completed-order/search',['as'=>'business-segment.completed-order.search','uses'=>'BusinessSegment\OrderController@getCompletedOrder']);

        Route::get('/order/detail/{id}',['as'=>'business-segment.order.detail','uses'=>'BusinessSegment\OrderController@orderDetail']);
        Route::get('/order/invoice/{id}',['as'=>'business-segment.order.invoice','uses'=>'BusinessSegment\OrderController@orderInvoice']);
        Route::get('/order/assign/{id}',['as'=>'business-segment.order.assign','uses'=>'BusinessSegment\OrderController@orderAssign']);
        Route::get('/order/reassign/{id}',['as'=>'business-segment.order.reassign','uses'=>'BusinessSegment\OrderController@orderReassign']);
        Route::post('/order/auto-assign',['as'=>'business-segment.order.auto-assign','uses'=>'BusinessSegment\OrderController@orderAutoAssign']);
        Route::post('/order/assign/',['as'=>'business-segment.order-assign-to-driver','uses'=>'BusinessSegment\OrderController@orderAssignToDriver']);
        Route::post('/order/reassign/',['as'=>'business-segment.order-reassign-to-driver','uses'=>'BusinessSegment\OrderController@reAssignToDriver']);
        Route::get('/order/cancel/{id}',['as'=>'business-segment.order-cancel','uses'=>'BusinessSegment\OrderController@orderCancel']);
        Route::get('/order/reject/{id}',['as'=>'business-segment.order-reject','uses'=>'BusinessSegment\OrderController@rejectOrder']);
        Route::get('/order/process/{id}',['as'=>'business-segment.process-order','uses'=>'BusinessSegment\OrderController@processOrder']);
        Route::get('/start-order-processing/{id}',['as'=>'business-segment.start-order-process','uses'=>'BusinessSegment\OrderController@startOrderProcessing']);
        Route::post('/order/pickup-verification',['as'=>'business-segment.order.pickup.verify','uses'=>'BusinessSegment\OrderController@orderPickupVerify']);
        Route::post('/web-playerid-subscription', 'Merchant\DashBoardController@webPlayerIdSubscription')->name('merchant-business-segment-playerid.onesignal');
        Route::get('order-statistics/{id}', ['as' =>'business-segment.statistics','uses' =>  'BusinessSegment\BusinessSegmentController@statistics']);

        Route::get('/wallet', ['as' => 'business-segment.wallet', 'uses' => 'BusinessSegment\WalletTransactionController@index']);
        Route::get('/cashouts', ['as' => 'business-segment.cashouts', 'uses' => 'BusinessSegment\WalletTransactionController@cashouts']);
        Route::post('/cashout/request', ['as' => 'business-segment.cashout.request', 'uses' => 'BusinessSegment\WalletTransactionController@cashoutRequest']);
        Route::get('/order-earning', ['as' => 'business-segment.earning', 'uses' => 'BusinessSegment\BusinessSegmentController@earningSummary']);
        Route::get('/order-earning-export', ['as' => 'business-segment.earning.export', 'uses' => 'ExcelController@businessSegmentEarningExport']);
        Route::get('/configurations', ['as' => 'business-segment.configurations', 'uses' => 'BusinessSegment\ConfigurationController@index']);
        Route::post('/save-configurations', ['as' => 'business-segment.save-configurations', 'uses' => 'BusinessSegment\ConfigurationController@save']);



        Route::get('/category-export/',['as'=>'business-segment.category.export','uses'=>'ExcelController@categories']);
        Route::get('/weight-unit-export/',['as'=>'business-segment.weight.export','uses'=>'ExcelController@weightUnit']);
        Route::get('/product-export-variant/',['as'=>'business-segment-product-export-variant','uses'=>'ExcelController@productForVariant']);
        // import products
        Route::post('product-import', ['as'=>'business-segment-product-import','uses'=>'BusinessSegment\ProductController@importProducts']);
        Route::post('product-variant-import', ['as'=>'business-segment-product-variant-import','uses'=>'BusinessSegment\ProductController@importProductVariants']);

    });
});


//Driver agency module
Route::prefix('driver-agency/admin')->group(function () {
    Route::group(['middleware' => ['guest:driver-agency']], function () {
        Route::get('{merchant_alias_name}/{alias}/login', 'Auth\DriverAgencyLoginController@showloginform')->name('driver-agency.login');
        Route::post('/login/{alias}','Auth\DriverAgencyLoginController@login')->name('driver-agency.login.submit');
    });
    Route::group(['middleware' => ['auth:driver-agency','admin_language']], function () {
        Route::get('/dashboard', 'DriverAgency\DriverAgencyController@dashboard')->name('driver-agency.dashboard');
        Route::get('/edit_profile', ['as' => 'driver-agency.profile', 'uses' => 'DriverAgency\DriverAgencyController@Profile']);
        Route::post('/edit_profile', ['as' => 'driver-agency.profile.submit', 'uses' => 'DriverAgency\DriverAgencyController@UpdateProfile']);
        Route::get('/transaction', ['as' => 'driver-agency.transaction', 'uses' => 'DriverAgency\TransactionController@index']);
        Route::post('/transaction', ['as' => 'driver-agency.transaction.search', 'uses' => 'DriverAgency\TransactionController@Search']);
        Route::get('/logout', 'Auth\DriverAgencyLoginController@logout')->name('driver-agency.logout');
        Route::get('/wallet', ['as' => 'driver-agency.wallet', 'uses' => 'DriverAgency\DriverAgencyController@Wallet']);


        /** driver module start **/
        // get drivers
        Route::get('/driver', ['as' => 'driver-agency.driver.index', 'uses' => 'DriverAgency\DriverController@index']);
        // add driver
        Route::get('/driver/add/{id?}', ['as' => 'driver-agency.driver.add', 'uses' => 'DriverAgency\DriverController@add']);
        // save driver
        Route::post('/driver/save/{id?}', ['as' => 'driver-agency.driver.save', 'uses' => 'DriverAgency\DriverController@save']);
        // view driver
        Route::get('/driver/profile/{id}', ['as' => 'driver-agency.driver.show', 'uses' => 'DriverAgency\DriverController@show']);

        /** driver module end **/

        Route::get('/country/config', ['as' => 'driver-agency.country.config', 'uses' => 'DriverAgency\DriverController@CountryConfig']);
        Route::post('/driver/personal-document', ['as' => 'driver-agency.driver.country-area-document', 'uses' => 'DriverAgency\DriverController@getPersonalDocument']);

        /** driver vehicle module start **/
        Route::get('/driver/add-vehicle/{id}/{vehicle_id?}', ['as' => 'driver-agency.driver.vehicle.create', 'uses' => 'DriverAgency\DriverController@addVehicle']);
        Route::post('/driver/save-vehicle/{id}', ['as' => 'driver-agency.driver.vehicle.store', 'uses' => 'DriverAgency\DriverController@saveVehicle']);
        Route::get('/vehicle/details/{id}', ['as' => 'driver-agency.driver-vehicledetails', 'uses' => 'DriverAgency\DriverController@VehiclesDetail']);
        /** driver vehicle module end **/

        Route::get('/drivers/basic-signup/', ['as' => 'driver-agency.driver.basic', 'uses' => 'DriverAgency\DriverController@basicSignupDriver']);
        Route::get('/driver/rejected/', ['as' => 'driver-agency.driver.rejected', 'uses' => 'DriverAgency\DriverController@rejectedDriver']);
//        Route::get('/drivers/basicses/', ['as' => 'driver-agency.driver.basics', 'uses' => 'DriverAgency\DriverController@NewDriver']);
//        Route::get('/drivers/basic/search', ['as' => 'driver-agency.driver.basic.search', 'uses' => 'DriverAgency\DriverController@NewDriverSearch']);
        Route::get('/driver/document/{id}', ['as' => 'driver-agency.driver.document.show', 'uses' => 'DriverAgency\DriverController@ShowDocument']);
        Route::post('/driver/document/{id}', ['as' => 'driver-agency.driver.document.store', 'uses' => 'DriverAgency\DriverController@StoreDocument']);
        Route::get('/driver/vehicle/{id}', ['as' => 'driver-agency.driver-vehicle', 'uses' => 'DriverAgency\DriverController@Vehicles']);
        Route::get('/driver/active/deactive/{id}/{status}', ['as' => 'driver-agency.driver.active.deactive', 'uses' => 'DriverAgency\DriverController@ChangeStatus']);
        Route::post('/ajax/servicess', ['as' => 'ajax.servicess', 'uses' => 'Helper\AjaxController@VehicleServices']);
        Route::post('/ajax/vehiclemodels', ['as' => 'ajax.vehiclemodels', 'uses' => 'Helper\AjaxController@VehicleModel']);

        Route::post('/drivers/delete/', ['as' => 'driver-agency.drivers.delete', 'uses' => 'DriverAgency\DriverController@destroy']);
        Route::post('/promotions/send/driver', ['as' => 'driver-agency.sendsingle-driver', 'uses' => 'DriverAgency\DriverController@SendNotificationDriver']);
        Route::get('/coutry/areaList', ['as' => 'driver-agency.country.arealist', 'uses' => 'DriverAgency\DriverController@AreaList']);
        Route::get('/allvehicles/', ['as' => 'driver-agency.driver.allvehicles', 'uses' => 'DriverAgency\DriverController@AllVehicle']);
        Route::get('/drivers/temp/doc/pending/', ['as' => 'driver-agency.driver.tempDocPending.show', 'uses' => 'DriverAgency\DriverController@TempDocPending']);
        Route::get('/wallet', ['as' => 'driver-agency.wallet', 'uses' => 'DriverAgency\DriverAgencyController@wallet']);

//        //ajax route
//        Route::post('/ajax/area', ['as' => 'taxicompany.ajax.area', 'uses' => 'Helper\AjaxController@AreaList']);
//        Route::post('/getRideConfig', ['as' => 'taxicompany.getRideConfig', 'uses' => 'Helper\AjaxController@VehicleConfig']);
//        Route::post('/getServices', ['as' => 'taxicompany.area.services', 'uses' => 'Helper\AjaxController@ServiceType']);
//        Route::post('/getVehicle', ['as' => 'taxicompany.vehicles', 'uses' => 'Helper\AjaxController@VehicleType']);
//        Route::post('/checkPriceCard', ['as' => 'taxicompany.checkPriceCard', 'uses' => 'Helper\AjaxController@PriceCard']);

    });
   });
