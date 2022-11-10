<?php

namespace App\Http\Controllers\Merchant;

use App\Events\SendUserInvoiceMailEvent;
use App\Http\Controllers\Helper\DistanceCalculation;
use App\Http\Controllers\Helper\DistanceController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\Merchant as helperMerchant;
use App\Http\Controllers\Helper\PriceController;
use App\Http\Controllers\Helper\Toll;
use App\Models\BusinessSegment\LanguageProductVariant;
use App\Models\CancelReason;
use App\Models\Configuration;
use App\Models\Country;
use App\Models\CustomerSupport;
use App\Models\InfoSetting;
use App\Models\User;
use App\Models\Driver;
use App\Models\DriverVehicle;
use App\Models\DistanceMethod;
use App\Models\DistanceSetting;
use App\Models\LanguageString;
use App\Models\LanguageStringTranslation;
use App\Models\Merchant;
use App\Models\Onesignal;
use App\Models\MerchantWebOneSignal;
use App\Models\VersionManagement;
use App\Traits\RatingTrait;
use Auth;
use App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Booking;
use App\Models\ServiceType;
use App\Traits\BookingTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\UserSignupWelcome;
use App\Traits\ImageTrait;
use App\Traits\PolylineTrait;
use App\Traits\DriverTrait;
use Schema;
use Session;
use App\Traits\ContentTrait;
use App\Traits\MerchantTrait;

use App\Models\LangName;

//use App\Models\Category;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stichoza\GoogleTranslate\GoogleTranslate;
//require_once 'vendor/autoload.php';
use MercadoPago;

class DashBoardController extends Controller
{
    use MerchantTrait, BookingTrait, RatingTrait, ImageTrait, PolylineTrait, DriverTrait, ContentTrait;

//    public function test()
//    {
//        $booking = Booking::find(10340);
//        $bookingcoordinates = App\Models\BookingCoordinate::where([['booking_id', '=', $booking->id]])->first();
//        $pick = $booking->pickup_latitude . "," . $booking->pickup_longitude;
//        $drop = $booking->drop_latitude . "," . $booking->drop_longitude;
//        $from = $booking->BookingDetail->start_latitude . "," . $booking->BookingDetail->start_longitude;
//        $to = $booking->BookingDetail->end_latitude . "," . $booking->BookingDetail->end_latitude;
//        $distanceCalculation = new App\Http\Controllers\Helper\DistanceCalculation();
//        $distance = $distanceCalculation->distance($from, $to, $pick, $drop, $bookingcoordinates['coordinates'], 49, "AIzaSyBXxXN55HjxqcvAY5nXDrNvKDNWOFr5KfE");
//        return $distance;
//        $distance = round($distance);
//        $coordinates = $bookingcoordinates['coordinates'];
//    }

    public function processPayment(Request $request)
    {

        MercadoPago\SDK::setAccessToken("TEST-1603575310218843-101401-5e542e082c12d169a5ab7406d540fa98-840387326");

        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = (float)$_POST['transactionAmount'];
        $payment->token = $_POST['token'];
        $payment->description = $_POST['description'];
        $payment->installments = (int)$_POST['installments'];
        $payment->payment_method_id = $_POST['paymentMethodId'];
        $payment->issuer_id = (int)$_POST['issuer'];

        $payer = new MercadoPago\Payer();
        $payer->email = $_POST['email'];
        $payer->identification = array(
            "type" => $_POST['docType'],
            "number" => $_POST['docNumber']
        );
        $payment->payer = $payer;

        $payment->save();

        $response = array(
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
            'id' => $payment->id
        );
        echo json_encode($response);
    }
    public function mercadoPage(Request $request)
    {

        return view('merchant.random.mercado');
    }

    function numberAbbreviation($number)
    {
        $abbrevs = array(12 => "T", 9 => "B", 6 => "M", 3 => "K", 0 => "");
        foreach ($abbrevs as $exponent => $abbrev) {
            if ($number >= pow(10, $exponent)) {
                $display_num = $number / pow(10, $exponent);
                $decimals = ($exponent >= 3 && round($display_num) < 100) ? 1 : 0;
                return number_format($display_num, $decimals) . " " . $abbrev;
            }
        }
    }

    public function CheckDriverExist()
    {
        $merchant_id = Auth::user()->id;
        $driversVehicles = DriverVehicle::whereNotExists(function ($query) use ($merchant_id) {
            $query->select('*')->from('driver_driver_vehicle')->whereRaw('driver_vehicles.id = driver_driver_vehicle.driver_vehicle_id')->orWhereRaw('driver_vehicles.driver_id = driver_driver_vehicle.driver_id');
        })->where('merchant_id', $merchant_id)->get();
        foreach ($driversVehicles as $driversVehicle) {
            $driverData = Driver::find($driversVehicle->driver_id);
            if (!empty($driverData)) {
                $driversVehicle->Drivers()->sync($driversVehicle->driver_id);
            }
        }
    }

    public function InsertVehicleShareCode()
    {
        $merchant_id = Auth::user()->id;
        $driverVehicles = DriverVehicle::where([['shareCode', NULL], ['merchant_id', $merchant_id]])->get();
        $counter = 0;
        foreach ($driverVehicles as $driverVehicle) {
            $shareCode = $driverVehicle->driver_id . getRandomCode(5);
            $existCode = DriverVehicle::where([['shareCode', $shareCode], ['merchant_id', $merchant_id]])->latest()->first();
            if (empty($existCode)) {
                $driverVehicle->shareCode = $shareCode;
                $driverVehicle->save();
                $counter++;
            }
        }
        dd($counter);
    }

//    function translate($q, $sl, $tl){
//        $res= file_get_contents("https://translate.googleapis.com/translate_a/single?client=gtx&ie=UTF-8&oe=UTF-8&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&sl=".$sl."&tl=".$tl."&hl=hl&q=".urlencode($q));
//        $res=json_decode($res);
//        return $res[0][0][0];
//    }

    public function testToll()
    {
//        $booking = Booking::find(4596);
        $bookingcoordinates = App\Models\BookingCoordinate::where([['booking_id', '=', $booking->id]])->first();

        $newTool = new Toll();
        $api = "SBBELVbJsZ51wzcHj00lx4W3jymAcIbV4IqBSjvm";
        $from = '40.628377410204926,-73.97529445588589';
        $to = '40.75774776755874,-73.9723527431488';
        $coordinates = $bookingcoordinates['coordinates'];
        $toolPrice = $newTool->checkToll(1, $from, $to, $coordinates, $api);
        p($toolPrice);
    }

//    function distance($lat1, $lon1, $lat2, $lon2, $unit) {
//
//
//        https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=Washington,DC&destinations=New+York+City,NY&key=YOUR_API_KEY
//        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
//            return 0;
//        }
//        else {
//            $theta = $lon1 - $lon2;
//            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
//            $dist = acos($dist);
//            $dist = rad2deg($dist);
//            $miles = $dist * 60 * 1.1515;
//            $unit = strtoupper($unit);
//
//            if ($unit == "K") {
////                p($miles* 1.609344);
//                $d = $miles * 1.609344;
//                p(number_format($d,'2'));
////                return ($miles * 1.609344);
//
//            } else if ($unit == "N") {
//                return ($miles * 0.8684);
//            } else {
//                return $miles;
//            }
//        }
//    }

//    public function logoutDrivers()
//    {
//        DB::beginTransaction();
//        try {
//            $drivers = Driver::select('id','online_offline','login_logout','free_busy')->get();
//            foreach ($drivers as $driver)
//            {
//                $driver->online_offline = 2;
//                $driver->login_logout = 2;
//                $driver->free_busy = 2;
//                $driver->save();
//                if (has_driver_multiple_or_existing_vehicle($driver->id) == true) {
//                    $driverVehicle = DriverVehicle::with('Drivers')->whereHas('Drivers', function ($query) use ($driver) {
//                        $query->where('id', $driver->id);
//                    })->where('vehicle_active_status', 1)->first();
//                    if (!empty($driverVehicle->id)) {
//                        $drivers = $driverVehicle->Drivers;
//                        $vehicleActiveStatus = array();
//                        foreach ($drivers as $driverData) {
//                            $vehicleActiveStatus[] = $driverData->online_offline == 1 ? 1 : 2;
//                        }
//                        if (!in_array(1, $vehicleActiveStatus)) {
//                            $driverVehicle->vehicle_active_status = 2;
//                            $driverVehicle->save();
//                        }
//                    }
//                }
////                $driver->token()->revoke();
//                //            $newDriverRecord = new DriverRecords();
////            ($driver->online_offline == 1) ? $newDriverRecord->OfflineTimeRecord($driver->id, $driver->merchant_id) : '';
//            }
//        } catch (\Exception $e) {
//            $message = $e->getMessage();
//            p($message);
//            // Rollback Transaction
//            DB::rollback();
//        }
//        DB::commit();
//        p('success');
////        return response()->json(['result' => "1", 'message' => trans('api.logoutSuccessfully'), 'data' => []]);
//    }

    public function amole()
    {

        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => "http://uatapi.myamole.com:10080/amole/pay",
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "POST",
        //     CURLOPT_POSTFIELDS => "BODY_CardNumber=+251911885386&BODY_ExpirationDate=&BODY_PIN=9999&BODY_PaymentAction=09&BODY_AmountX=123.45&BODY_AmoleMerchantID=HUBERTAXI&BODY_OrderDescription=YourTransDescription&BODY_SourceTransID=YourUniqueTransID&BODY_VendorAccount=&BODY_AdditionalInfo1=&BODY_AdditionalInfo2=&BODY_AdditionalInfo3=&BODY_AdditionalInfo4=&BODY_AdditionalInfo5=",
        //     CURLOPT_HTTPHEADER => array(
        //         "HDR_Signature: CgRs_7DpRQm8StaX9n5jBdLy8sHl67rzyNTqPR4ZpPPbmsFrMBJEbyq-mb5dnitt",
        //         "HDR_IPAddress: 35.178.56.137",
        //         "HDR_UserName: hubert1",
        //         "HDR_Password: test",
        //         "Content-Type: application/x-www-form-urlencoded"
        //     ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // p( $response);
        // p("executed");


        $header = array(
            "HDR_Signature: CgRs_7DpRQm8StaX9n5jBdLy8sHl67rzyNTqPR4ZpPPbmsFrMBJEbyq-mb5dnitt",
            "HDR_IPAddress: 35.178.56.137",
            "HDR_UserName: hubert2",
            "HDR_Password: test",
            "Content-Type: application/x-www-form-urlencoded"
        );
        $body_param = array(
            "BODY_CardNumber" => "+251911885386",
            "BODY_ExpirationDate" => "",
            "BODY_PaymentAction" => "09",
            "BODY_AmountX" => 100,
            "BODY_AmoleMerchantID" => "HUBERTAXI",
            "BODY_OrderDescription" => "For Taxi Payment",
            "BODY_SourceTransID" => time(),
            "BODY_VendorAccount" => "",
            "BODY_AdditionalInfo1" => "",
            "BODY_AdditionalInfo2" => "",
            "BODY_AdditionalInfo3" => "",
            "BODY_AdditionalInfo4" => "",
            "BODY_AdditionalInfo5" => ""
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://uat.api.myamole.com:10080/amole/pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($body_param),
            CURLOPT_HTTPHEADER => $header,
        ));
        $response = curl_exec($curl);
        // curl_close($curl);
        $response = json_decode($response);
        p("code executed", 0);
        if (curl_exec($curl) === false) {
            echo "ok";
        } else {
            echo "error";
            // p(curl_error($curl),0);
        }
        p($response);


    }
    public function index(Request $request)
    {



//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://demo.apporioproducts.com/multi-service/public/api/user/configuration',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => array('unique_no' => 'bf7e7c275d78d7b0','c_long' => 'longitude','latitude' => '28.6869169','language_code' => 'some language code','manufacture' => 'HUAWEI','player_id' => 'null','package_name' => 'com.apporio.productgrocery','operating_system' => '28','model' => 'BND-AL10','c_lat' => 'latitude','apk_version' => 2.6,'device' => '1','longitude' => '77.1450533'),
//            CURLOPT_HTTPHEADER => array(
//                'publicKey: O3CHoxPt2I1r6Z8ksi0RqJlGDu4N9m',
//                'secretKey: i5SovWpXmCOZr41UZMPIKLflqN6syt',
//                'Cookie: multiservice_session=CPZTLjgPN7AIdnUPEJrGK58aM8QSa1lYILiBgqaD'
//            ),
//        ));
//
//        $response = curl_exec($curl);
//p($response);
//        curl_close($curl);
//        echo $response;

        // pre authorize
//        $arr = [
//            'schemaVersion' => "1.0",
//            'requestId' => "6457672760",
//            'timestamp' => "2021-11-25 Africa",
//            'channelName' => "WEB",
//            'serviceName' => "API_PREAUTHORIZE",
//            'serviceParams' => [
//                'merchantUid' => "M0910353",
//                'apiUserId' => "1000573",
//                'apiKey' => "API-975062629AHX",
//                'paymentMethod' => "MWALLET_ACCOUNT",
//                'payerInfo'=>[
//                    'accountNo'=>"252634718812"
//                ],
//                'pgAccountId'=>'20001250',
//                'transactionInfo'=>[
//                    'referenceId'=>"111111",
//                    'invoiceId'=>"222222",
//                    'amount'=>"1",
//                    'currency'=>"SLSH",
//                    'description'=>"test"
//                ]
//            ],
//
//        ];
//
//        $url = "https://api.waafi.com/asm";
//        $json_param = json_encode($arr);
////        p($json_param);
//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => $url,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_POST => true,
//            CURLOPT_POSTFIELDS => $json_param,
//            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json'
//            ),
//        ));
//        $result = curl_exec($curl);
//        $response = json_decode($result, true);
//         p($response);
//
//         $transaction_id = $response['params']['transactionId'];
//         // authorize commit
//
//
//
//        $arr_v1 = [
//            'schemaVersion' => "1.0",
//            'requestId' => "1992142164",
//            'timestamp' => "2021-11-26 Standard",
//            'channelName' => "WEB",
//            'serviceName' => "API_PREAUTHORIZE_COMMIT",
//            'serviceParams' => [
//                'merchantUid' => "M0910353",
//                'apiUserId' => "1000573",
//                'apiKey' => "API-975062629AHX",
//                'transactionId' => $transaction_id,
//                'description'=>"Commited",
//                'referenceId'=>"11111",
//            ],
//        ];
//
////        $url = "https://api.waafipay.net/asm";
//        $json_param = json_encode($arr_v1);
//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => $url,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_POST => true,
//            CURLOPT_POSTFIELDS => $json_param,
//            CURLOPT_HTTPHEADER => array(
//                'Content-Type: application/json'
//            ),
//        ));
//        $result = curl_exec($curl);
//        $response = json_decode($result, true);
//        p($response);


        //     $service_type = DB::table('service_types')->where('owner_id',68)->get();
        //   foreach($service_type as $service)
        //     {
        //      $check_service = DB::table('merchant_service_type')->where('service_type_id',$service->id)->where('segment_id',$service->segment_id)->first();
        //       if(empty($check_service))
        //         {
        //          DB::table('merchant_service_type')->insert(['merchant_id'=>68,'service_type_id'=>$service->id,'segment_id'=>$service->segment_id]);
        //         // p($check_service);
        //       }
        //     }
        //   p($service_type);


//        $ref_ctr = new App\Http\Controllers\Helper\ReferralController();
//        $ref_ctr->testReferralSystem();
//        p('end');
//        DB::beginTransaction();
//        $merchant_id = get_merchant_id();
//        $country = Country::where([["phoneCode","=","+56"],["merchant_id","=",$merchant_id]])->first();
//        $users = User::where([["merchant_id","=",$merchant_id],["country_id","=",1]])->get();
//        $update_ids = [];
//        foreach($users as $user){
//            array_push($update_ids,$user->id);
//            $user_obj = User::find($user->id);
//            $user_obj->country_id = $country->id;
//            $user_obj->UserPhone = $country->phoneCode.$user->UserPhone;
//            $user_obj->save();
//        }
//
//        $drivers = Driver::where([["merchant_id","=",$merchant_id]])->whereIn("account_type_id",[1,2])->get();
//        foreach($drivers as $driver){
//            $driver_obj = Driver::find($driver->id);
//            $driver_obj->account_type_id = 9;
//            $driver_obj->save();
//        }
////
//        DB::commit();
//        p($update_ids);

//        $merchant_segment = helperMerchant::MerchantSegments(1);
//        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
//        $grocery_food_exist = (count(array_intersect($merchant_segment, $all_food_grocery_clone)) > 0) ? true :false;
//        p($merchant_segment,0);
//        p($all_food_grocery_clone,0);
//        p($grocery_food_exist);
//        $myfile = fopen("amba.php", "w");
//        p("done");
//        $permission_array = array_pluck($role->getAllPermissions(), 'id');
//        $permissions = Permission::get()->pluck('id')->toArray();
//        $role = Role::findOrFail(2);
//        $role->syncPermissions($permissions);
//        p($role);
//        $booking = Booking::find(960);
//        $handymanOrder = App\Models\HandymanOrder::find(187);
//        $order = App\Models\BusinessSegment\Order::find(373);
//        $user = User::find(59);
//        $driver = Driver::find(389);
//        event(new UserSignupWelcome($user->id));
//        event(new App\Events\DriverSignupWelcome($driver->id));
//        $email_listener = new emailTemplateController();
//        $email_listener->SendTaxiInvoiceEmail($booking);
//        event(new SendUserInvoiceMailEvent($booking));
//        event(new App\Events\SendUserHandymanInvoiceMailEvent($handymanOrder));
//        event(new App\Events\SendNewOrderRequestMailEvent($order));
//        event(new App\Events\SendNewRideRequestMailEvent($booking));
//        event(new App\Events\UserForgotPasswordEmailOtpEvent($user, "8901"));
//        event(new App\Events\DriverForgotPasswordEmailOtpEvent($driver,"1202"));
//        event(new App\Events\DriverSignupEmailOtpEvent($driver->merchant_id,"bhuvanesh@apporio.com","4431"));
//        event(new App\Events\UserSignupEmailOtpEvent($user->merchant_id,"bhuvanesh@apporio.com","4521"));
//        p('Finish');
//        $booking = Booking::where('merchant_booking_id',2347)->first();
////        p($booking->BookingCoordinate);
//        $coordinates = json_decode($booking->BookingCoordinate->coordinates,true);
//        foreach($coordinates as $coord){
//            echo $coord['latitude'].",".$coord['longitude']."<br>";
//        }
//        $distanceCalculation = new DistanceCalculation();
//        $pick = $booking->pickup_latitude . "," . $booking->pickup_longitude;
//        $drop = $booking->drop_latitude . "," . $booking->drop_longitude;
//        $from = $booking->BookingDetail->start_latitude . "," . $booking->BookingDetail->start_longitude;
//        $to = $request->latitude . "," . $request->longitude;
//        $google_key = $booking->Merchant->BookingConfiguration->google_key;
//        $distance = $distanceCalculation->distance($from, $to, $pick, $drop,$booking->BookingCoordinate->coordinates, $booking->merchant_id, $google_key, "endRide$booking->id");
//        $distance = round($distance);
//        p($distance);
//        p($booking);
//        p($this->amole());
//        $this->logoutDrivers();
//
//        $image = env('AWS_BUCKET_URL').'bike.png';
//        $image = env('AWS_BUCKET_URL').'userA/one.png';
//        p($image);
//        $this->testToll();
//        $this->test();
//        $a =  Schema::getColumnListing('merchants');
//        p($a);
//        p(Onesignal::UserPushMessage($playerid = null, $data = [], $message = null, $type = null, $mercahnt_id = 1, $single = null));

        $merchant = get_merchant_id(false);
        Session::put('demo_otp', '');
//        $segment = App\Http\Controllers\Helper\Merchant::MerchantSegments();
        $merchant_id = $merchant->id;
        $request->request->add(['merchant_id' => $merchant_id]);
        $driver = $this->getDriverSummary($request);
        $users = User::select('id')->where(array('taxi_company_id' => NULL, 'user_delete' => NULL, 'merchant_id' => $merchant_id))->count();

        $site_states = (object)array(
            'users' => $users,
            'drivers' => $driver->approved,
            'totalCountry' => $merchant->Country->count(),
            'totalCountryArea' => $merchant->CountryArea->count(),
        );
        $countries = Country::where([['merchant_id','=',$merchant->id],['country_status','=',1]])->orderBy('sequance','ASC')->get();
        $reminder_days = Configuration::where('merchant_id', '=', $merchant_id)->select('reminder_doc_expire')->first();
        $expire_driver_doc = 0;
        $expire_class = new ExpireDocumentController;
        if (!empty($reminder_days)) {
            $currentDate = date('Y-m-d');
            $reminder_last_date = date('Y-m-d', strtotime('+' . $reminder_days->reminder_doc_expire . ' days'));
            $expire_driver_doc = $expire_class->getDocumentGoingToExpire($currentDate, $reminder_last_date, $merchant_id);
            $expire_driver_doc = $expire_driver_doc->count();
        }
        $taxi_states = App\Models\Segment::select('id', 'name', 'slag')
            ->with(['Booking' => function ($q) use ($merchant_id) {
                $q->select('segment_id', 'id', 'booking_status', 'booking_closure'
//                    DB::raw('COUNT(*) AS all_rides'),
//                    DB::raw('COUNT(CASE WHEN booking_status = "1005"  THEN 1  END) AS completed'),
//                    DB::raw('COUNT(CASE WHEN booking_status = "1001" OR booking_status = "10012" OR booking_status = "1002" OR booking_status = "1003" OR booking_status = "1004"  THEN 1  END) AS ongoing'),
//                    DB::raw('COUNT(CASE WHEN booking_status = "1006" OR booking_status = "1007" OR booking_status = "1008"  THEN 1  END) AS cancelled'),
//                    DB::raw('COUNT(CASE WHEN booking_status = "1016" OR booking_status = "1018"  THEN 1  END) AS auto_cancelled')
                )->where([['merchant_id', '=', $merchant_id]]);
            }])
            ->whereHas('Merchant', function ($q) use ($merchant_id) {
                $q->where([['merchant_id', '=', $merchant_id]]);
            })
            ->where('segment_group_id', 1)
            ->whereIn('id', [1, 2])
            ->get();
        $taxi_states = $taxi_states->map(function ($segment) use ($merchant_id) {
            $earning = DB::table('booking_transactions as bt')
                ->join('bookings as b', 'bt.booking_id', '=', 'b.id')
                ->where('b.segment_id', $segment->id)->where('b.merchant_id', $merchant_id)
                ->select('bt.id', 'bt.booking_id', 'b.segment_id', DB::raw('SUM(bt.discount_amount) as discount_amount'), DB::raw('SUM(bt.company_earning) as company_earning'))
//               ->sum('company_earning')
                ->first();
//           p($earning);
//            ->toSql();
            return (object)[
                'id' => $segment->id,
                'is_coming_soon' => isset($segment->Merchant[0]) ? $segment->Merchant[0]['pivot']->is_coming_soon : NULL,
                'slag' => $segment->slag,
                'name' => $segment->Name($merchant_id),
                'total_earning' => $earning->company_earning,
                'total_discount' => $earning->discount_amount,
                'ride' => (object)[
                    'all_rides' => $segment->Booking->count(),
                    'completed' => $segment->Booking->where('booking_status', 1005)->where('booking_closure', 1)->count(),
                    'ongoing' => $segment->Booking->whereIn('booking_status', [1001, 1012, 1002, 1003, 1004, 1005])->where('booking_closure', NULL)->count(),
                    'cancelled' => $segment->Booking->whereIn('booking_status', [1006, 1007, 1008])->count(),
                    'auto_cancelled' => $segment->Booking->whereIn('booking_status', [1016, 1018])->count(),
                ]
            ];
        });
        $corporates = $merchant->Corporate->count();
        $home_delivery = App\Models\Segment::select('id', 'name', 'slag')
            ->with(['BusinessSegment' => function ($q) use ($merchant_id) {
                $q->select('segment_id', 'id')->where([['merchant_id', '=', $merchant_id]]);
            }])
            ->with(['Order' => function ($q) use ($merchant_id) {
                $q->select('segment_id', 'id')->where([['merchant_id', '=', $merchant_id]]);
            }])
            ->with(['Product' => function ($q) use ($merchant_id) {
                $q->select('segment_id', 'id')->where([['merchant_id', '=', $merchant_id]]);
            }])
            ->with(['Category' => function ($q) use ($merchant_id) {
                $q->select('segment_id', 'id')->where([['merchant_id', '=', $merchant_id]]);
            }])
            ->whereHas('Merchant', function ($q) use ($merchant_id) {
                $q->where([['merchant_id', '=', $merchant_id]]);
            })
            ->where('segment_group_id', 1)
            ->whereIn('sub_group_for_app', [1, 2])
            ->orderBy('sub_group_for_app')
            ->get();

        $home_delivery->map(function ($segment) use ($merchant_id) {
            $earning = DB::table('booking_transactions as bt')
                ->join('orders as o', 'bt.order_id', '=', 'o.id')
                ->where('o.segment_id', $segment->id)->where('o.merchant_id', $merchant_id)
                ->select('bt.id', 'bt.booking_id', 'o.segment_id', DB::raw('SUM(bt.discount_amount) as discount_amount'), DB::raw('SUM(bt.company_gross_total) as company_earning'))
//                ->sum('company_earning')
                ->first();

            $segment['total_earning'] = $earning->company_earning;
            $segment['total_discount'] = $earning->discount_amount;
//            p($segment);
            return $segment;
        });
//        p($home_delivery);
        // handyman segments
        $handyman_booking_statistics = NULL;
        $handyman_booking_statistics = App\Models\Segment::select('id', 'name', 'slag')
            ->with(['HandymanOrder' => function ($q) use ($merchant_id) {
                $q->select('segment_id', 'id', 'order_status'
//                    DB::raw('COUNT(*) AS all_bookings'),
//                    DB::raw('COUNT(CASE WHEN order_status = "7"  THEN 1  END) AS completed'),
//                    DB::raw('COUNT(CASE WHEN order_status = "6"  THEN 1  END) AS ongoing'),
//                    DB::raw('COUNT(CASE WHEN order_status = "5" OR order_status = "2"  THEN 1  END) AS cancelled')
                )->where([['merchant_id', '=', $merchant_id]]);
            }])
            ->whereHas('Merchant', function ($q) use ($merchant_id) {
                $q->where([['merchant_id', '=', $merchant_id]]);
            })
            ->where('segment_group_id', 2)
            ->get();
        $handyman_booking_statistics = $handyman_booking_statistics->map(function ($segment) use ($merchant_id) {

            $earning = DB::table('booking_transactions as bt')
                ->join('handyman_orders as ho', 'bt.handyman_order_id', '=', 'ho.id')
                ->where('ho.segment_id', $segment->id)->where('ho.merchant_id', $merchant_id)
                ->select('bt.id', 'bt.booking_id', 'ho.segment_id', DB::raw('SUM(bt.discount_amount) as discount_amount'), DB::raw('SUM(bt.company_earning) as company_earning'))
//                ->sum('company_earning')
                ->first();
//            ;
//p($earning);
            return (object)[
                'id' => $segment->id,
                'is_coming_soon' => $segment->Merchant[0]['pivot']->is_coming_soon,
                'slag' => $segment->slag,
                'name' => $segment->Name($merchant_id),
                'total_earning' => $earning->company_earning,
                'total_discount' => $earning->discount_amount,
                'booking' => (object)[
                    'all_bookings' => $segment->HandymanOrder->count(),
                    'completed' => $segment->HandymanOrder->where('order_status', 7)->count(),
                    'ongoing' => $segment->HandymanOrder->where('order_status', 6)->count(),
                    'cancelled' => $segment->HandymanOrder->whereIn('order_status', [5, 2])->count(),
                ]
            ];
        });
        $total_earning = 0;
        $total_discount = 0;
//        p($home_delivery);
        if (!empty($taxi_states)) {
            $total_earning = $total_earning + array_sum(array_pluck($taxi_states, 'total_earning'));
            $total_discount = $total_discount + array_sum(array_pluck($taxi_states, 'total_discount'));
        }
        if (!empty($home_delivery)) {
            $total_earning = $total_earning + array_sum(array_pluck($home_delivery, 'total_earning'));
            $total_discount = $total_discount + array_sum(array_pluck($home_delivery, 'total_discount'));
        }
        if (!empty($handyman_booking_statistics)) {
            $total_earning = $total_earning + array_sum(array_pluck($handyman_booking_statistics, 'total_earning'));
            $total_discount = $total_discount + array_sum(array_pluck($handyman_booking_statistics, 'total_discount'));
        }
        $site_states->total_earning = $total_earning;
        $site_states->total_discount = $total_discount;
        return view('merchant.home', compact('site_states', 'taxi_states', 'merchant', 'handyman_booking_statistics', 'merchant_id', 'expire_driver_doc', 'corporates', 'home_delivery','countries'));
    }

    public function DashboardFilter(Request $request)
    {
        $merchant = get_merchant_id(false);
        Session::put('demo_otp', '');
//        $segment = App\Http\Controllers\Helper\Merchant::MerchantSegments();
        $merchant_id = $merchant->id;
        $request->request->add(['merchant_id' => $merchant_id]);
        $driver = $this->getDriverSummary($request);
        $users = User::select('id')->where(array('taxi_company_id' => NULL, 'user_delete' => NULL, 'merchant_id' => $merchant_id))->count();
        if($request->country_id){
            $users = User::select('id')->where(array('taxi_company_id' => NULL, 'user_delete' => NULL, 'merchant_id' => $merchant_id,'country_id' => $request->country_id))->count();
        }

        $site_states = (object)array(
            'users' => $users,
            'drivers' => $driver->approved,
            'totalCountry' => $merchant->Country->count(),
            'totalCountryArea' => $merchant->CountryArea->count(),
        );
        $countries = Country::where([['merchant_id','=',$merchant->id],['country_status','=',1]])->orderBy('sequance','ASC')->get();
        $reminder_days = Configuration::where('merchant_id', '=', $merchant_id)->select('reminder_doc_expire')->first();
        $expire_driver_doc = 0;
        $expire_class = new ExpireDocumentController;
        if (!empty($reminder_days)) {
            $currentDate = date('Y-m-d');
            $reminder_last_date = date('Y-m-d', strtotime('+' . $reminder_days->reminder_doc_expire . ' days'));
            $expire_driver_doc = $expire_class->getDocumentGoingToExpire($currentDate, $reminder_last_date, $merchant_id);
            $expire_driver_doc = $expire_driver_doc->count();
        }
        $taxi_states = App\Models\Segment::select('id', 'name', 'slag')
            ->with(['Booking' => function ($q) use ($merchant_id) {
                $q->select('segment_id', 'id', 'booking_status', 'booking_closure'
//                    DB::raw('COUNT(*) AS all_rides'),
//                    DB::raw('COUNT(CASE WHEN booking_status = "1005"  THEN 1  END) AS completed'),
//                    DB::raw('COUNT(CASE WHEN booking_status = "1001" OR booking_status = "10012" OR booking_status = "1002" OR booking_status = "1003" OR booking_status = "1004"  THEN 1  END) AS ongoing'),
//                    DB::raw('COUNT(CASE WHEN booking_status = "1006" OR booking_status = "1007" OR booking_status = "1008"  THEN 1  END) AS cancelled'),
//                    DB::raw('COUNT(CASE WHEN booking_status = "1016" OR booking_status = "1018"  THEN 1  END) AS auto_cancelled')
                )->where([['merchant_id', '=', $merchant_id]]);
            }])
            ->whereHas('Merchant', function ($q) use ($merchant_id) {
                $q->where([['merchant_id', '=', $merchant_id]]);
            })
            ->where('segment_group_id', 1)
            ->whereIn('id', [1, 2])
            ->get();
        $taxi_states = $taxi_states->map(function ($segment) use ($merchant_id) {
            $earning = DB::table('booking_transactions as bt')
                ->join('bookings as b', 'bt.booking_id', '=', 'b.id')
                ->where('b.segment_id', $segment->id)->where('b.merchant_id', $merchant_id)
                ->select('bt.id', 'bt.booking_id', 'b.segment_id', DB::raw('SUM(bt.discount_amount) as discount_amount'), DB::raw('SUM(bt.company_earning) as company_earning'))
//               ->sum('company_earning')
                ->first();
//           p($earning);
//            ->toSql();
            return (object)[
                'id' => $segment->id,
                'is_coming_soon' => isset($segment->Merchant[0]) ? $segment->Merchant[0]['pivot']->is_coming_soon : NULL,
                'slag' => $segment->slag,
                'name' => $segment->Name($merchant_id),
                'total_earning' => $earning->company_earning,
                'total_discount' => $earning->discount_amount,
                'ride' => (object)[
                    'all_rides' => $segment->Booking->count(),
                    'completed' => $segment->Booking->where('booking_status', 1005)->where('booking_closure', 1)->count(),
                    'ongoing' => $segment->Booking->whereIn('booking_status', [1001, 1012, 1002, 1003, 1004, 1005])->where('booking_closure', NULL)->count(),
                    'cancelled' => $segment->Booking->whereIn('booking_status', [1006, 1007, 1008])->count(),
                    'auto_cancelled' => $segment->Booking->whereIn('booking_status', [1016, 1018])->count(),
                ]
            ];
        });
        $corporates = $merchant->Corporate->count();
        $home_delivery = App\Models\Segment::select('id', 'name', 'slag')
            ->with(['BusinessSegment' => function ($q) use ($merchant_id) {
                $q->select('segment_id', 'id')->where([['merchant_id', '=', $merchant_id]]);
            }])
            ->with(['Order' => function ($q) use ($merchant_id) {
                $q->select('segment_id', 'id')->where([['merchant_id', '=', $merchant_id]]);
            }])
            ->with(['Product' => function ($q) use ($merchant_id) {
                $q->select('segment_id', 'id')->where([['merchant_id', '=', $merchant_id]]);
            }])
            ->with(['Category' => function ($q) use ($merchant_id) {
                $q->select('segment_id', 'id')->where([['merchant_id', '=', $merchant_id]]);
            }])
            ->whereHas('Merchant', function ($q) use ($merchant_id) {
                $q->where([['merchant_id', '=', $merchant_id]]);
            })
            ->where('segment_group_id', 1)
            ->whereIn('sub_group_for_app', [1, 2])
            ->orderBy('sub_group_for_app')
            ->get();

        $home_delivery->map(function ($segment) use ($merchant_id) {
            $earning = DB::table('booking_transactions as bt')
                ->join('orders as o', 'bt.order_id', '=', 'o.id')
                ->where('o.segment_id', $segment->id)->where('o.merchant_id', $merchant_id)
                ->select('bt.id', 'bt.booking_id', 'o.segment_id', DB::raw('SUM(bt.discount_amount) as discount_amount'), DB::raw('SUM(bt.company_gross_total) as company_earning'))
//                ->sum('company_earning')
                ->first();

            $segment['total_earning'] = $earning->company_earning;
            $segment['total_discount'] = $earning->discount_amount;
//            p($segment);
            return $segment;
        });
//        p($home_delivery);
        // handyman segments
        $handyman_booking_statistics = NULL;
        $handyman_booking_statistics = App\Models\Segment::select('id', 'name', 'slag')
            ->with(['HandymanOrder' => function ($q) use ($merchant_id) {
                $q->select('segment_id', 'id', 'order_status'
//                    DB::raw('COUNT(*) AS all_bookings'),
//                    DB::raw('COUNT(CASE WHEN order_status = "7"  THEN 1  END) AS completed'),
//                    DB::raw('COUNT(CASE WHEN order_status = "6"  THEN 1  END) AS ongoing'),
//                    DB::raw('COUNT(CASE WHEN order_status = "5" OR order_status = "2"  THEN 1  END) AS cancelled')
                )->where([['merchant_id', '=', $merchant_id]]);
            }])
            ->whereHas('Merchant', function ($q) use ($merchant_id) {
                $q->where([['merchant_id', '=', $merchant_id]]);
            })
            ->where('segment_group_id', 2)
            ->get();
        $handyman_booking_statistics = $handyman_booking_statistics->map(function ($segment) use ($merchant_id) {

            $earning = DB::table('booking_transactions as bt')
                ->join('handyman_orders as ho', 'bt.handyman_order_id', '=', 'ho.id')
                ->where('ho.segment_id', $segment->id)->where('ho.merchant_id', $merchant_id)
                ->select('bt.id', 'bt.booking_id', 'ho.segment_id', DB::raw('SUM(bt.discount_amount) as discount_amount'), DB::raw('SUM(bt.company_earning) as company_earning'))
//                ->sum('company_earning')
                ->first();
//            ;
//p($earning);
            return (object)[
                'id' => $segment->id,
                'is_coming_soon' => $segment->Merchant[0]['pivot']->is_coming_soon,
                'slag' => $segment->slag,
                'name' => $segment->Name($merchant_id),
                'total_earning' => $earning->company_earning,
                'total_discount' => $earning->discount_amount,
                'booking' => (object)[
                    'all_bookings' => $segment->HandymanOrder->count(),
                    'completed' => $segment->HandymanOrder->where('order_status', 7)->count(),
                    'ongoing' => $segment->HandymanOrder->where('order_status', 6)->count(),
                    'cancelled' => $segment->HandymanOrder->whereIn('order_status', [5, 2])->count(),
                ]
            ];
        });
        $total_earning = 0;
        $total_discount = 0;
//        p($home_delivery);
        if (!empty($taxi_states)) {
            $total_earning = $total_earning + array_sum(array_pluck($taxi_states, 'total_earning'));
            $total_discount = $total_discount + array_sum(array_pluck($taxi_states, 'total_discount'));
        }
        if (!empty($home_delivery)) {
            $total_earning = $total_earning + array_sum(array_pluck($home_delivery, 'total_earning'));
            $total_discount = $total_discount + array_sum(array_pluck($home_delivery, 'total_discount'));
        }
        if (!empty($handyman_booking_statistics)) {
            $total_earning = $total_earning + array_sum(array_pluck($handyman_booking_statistics, 'total_earning'));
            $total_discount = $total_discount + array_sum(array_pluck($handyman_booking_statistics, 'total_discount'));
        }
        $site_states->total_earning = $total_earning;
        $site_states->total_discount = $total_discount;
        $data = $request->all();
        return view('merchant.home', compact('site_states', 'taxi_states', 'merchant', 'handyman_booking_statistics', 'merchant_id', 'expire_driver_doc', 'corporates', 'home_delivery','countries','data'));
    }

//    public function getRecordCountryArea($merchant, $query)
//    {
//        if (!empty($merchant->CountryArea->toArray())) {
//            $area_ids = array_pluck($merchant->CountryArea, 'id');
//            $query->whereIn('country_area_id', $area_ids);
//        }
//    }

    public function webPlayerIdSubscription(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $business_segment_id = $request->business_segment_id;
        $status = $request->status;
        $active = MerchantWebOneSignal::updateOrCreate(
            ['merchant_id' => $merchant_id, 'business_segment_id' => $business_segment_id, 'player_id' => $request->player_id],
            ['status' => $status, 'merchant_id' => $merchant_id, 'business_segment_id' => $business_segment_id, 'player_id' => $request->player_id]
        );
        echo "success";
    }

//    public function RemovePlayerId(Request $request)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $deactive = MerchantWebOneSignal::updateOrCreate(
//            ['merchant_id' => $merchant_id, 'player_id' => $request->player_id],
//            ['status' => 0]
//        );
//        echo "success";
//    }

//    public function ReferShow()
//    {
//        $checkPermission = check_permission(1, 'view_refer');
//        if ($checkPermission['isRedirect']) {
//            return $checkPermission['redirectBack'];
//        }
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $refers = ReferralSystem::where([['merchant_id', '=', $merchant_id], ['application', '=', 0]])->paginate(10);
//        return view('merchant.random.refer', compact('refers'));
//    }
//
//    public function Driver_ReferShow_view()
//    {
//        $checkPermission = check_permission(1, 'view_refer');
//        if ($checkPermission['isRedirect']) {
//            return $checkPermission['redirectBack'];
//        }
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $refers = ReferralSystem::where([['merchant_id', '=', $merchant_id], ['application', '=', 2]])->paginate(10);
//        return view('merchant.random.driver_refer_view', compact('refers'));
//    }
//
//    public function Driver_ReferCreateShow()
//    {
//        $checkPermission = check_permission(1, 'edit_refer');
//        if ($checkPermission['isRedirect']) {
//            return $checkPermission['redirectBack'];
//        }
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
//        return view('merchant.random.driver_refer', compact('countries'));
//    }
//
//    public function Driver_ReferStore(Request $request)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $request->validate([
//            'country_id' => ['required',
//                Rule::unique('referral_systems', 'country_id')->where(function ($query) use ($merchant_id) {
//                    $query->where([['merchant_id', '=', $merchant_id], ['application', '=', 2]]);
//                })],
//            'sender_discount' => 'required|integer|between:1,2',
//            'receiver_discount' => 'required|integer|between:1,2',
//            'start_date' => 'required|date',
//            'end_date' => 'required|date',
//            'offer_type' => 'required|integer|between:1,2',
//            'status' => 'required|integer|between:1,2',
//            'offer_value' => 'required|integer',
//        ]);
//
//        ReferralSystem::create([
//            'merchant_id' => $merchant_id,
//            'country_id' => $request->country_id,
//            'sender_discount' => $request->sender_discount,
//            'receiver_discount' => $request->receiver_discount,
//            'start_date' => $request->start_date,
//            'end_date' => $request->end_date,
//            'offer_type' => $request->offer_type,
//            'offer_value' => $request->offer_value,
//            'status' => $request->status,
//            'application' => 2,
//            'fixed_value' => $request->fixed_amount,
//        ]);
//
//        return redirect()->route('merchant.refer.driver_view');
//    }
//
//    public function Driver_Referedit($id)
//    {
//        $checkPermission = check_permission(1, 'edit_refer');
//        if ($checkPermission['isRedirect']) {
//            return $checkPermission['redirectBack'];
//        }
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $refer = ReferralSystem::where([['merchant_id', '=', $merchant_id], ['application', '=', 2]])->findOrFail($id);
//        return view('merchant.random.driver_refer-edit', compact('refer'));
//    }
//
//    public function Driver_ReferUpdate(Request $request, $id)
//    {
//        $request->validate([
//            'sender_discount' => 'required|integer|between:1,2',
//            'receiver_discount' => 'required|integer|between:1,2',
//            'start_date' => 'required',
//            'end_date' => 'required',
//            'offer_type' => 'required|integer|between:1,2',
//            'status' => 'required|integer|between:1,2',
//        ]);
//        $refer = ReferralSystem::find($id);
//        $refer->sender_discount = $request->sender_discount;
//        $refer->receiver_discount = $request->receiver_discount;
//        $refer->start_date = $request->start_date;
//        $refer->end_date = $request->end_date;
//        $refer->offer_type = $request->offer_type;
//        $refer->offer_value = $request->offer_value;
//        $refer->status = $request->status;
//        $refer->fixed_value = $request->fixed_amount;
//        $refer->save();
//        return redirect()->route('merchant.refer.driver_view', ' Driver Refer Updated');
//    }
//
//    public function ReferCreateShow()
//    {
//        $checkPermission = check_permission(1, 'edit_refer');
//        if ($checkPermission['isRedirect']) {
//            return $checkPermission['redirectBack'];
//        }
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
//        return view('merchant.random.refer-create', compact('countries'));
//    }
//
//    public function ReferStore(Request $request)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $request->validate([
//            'country_id' => ['required',
//                Rule::unique('referral_systems', 'country_id')->where(function ($query) use ($merchant_id) {
//                    $query->where([['merchant_id', '=', $merchant_id], ['application', '=', 0]]);
//                })],
//            'sender_discount' => 'required|integer|between:1,2',
//            'receiver_discount' => 'required|integer|between:1,2',
//            'start_date' => 'required|date',
//            'end_date' => 'required|date',
//            'offer_type' => 'required|integer|between:1,3',
//            'status' => 'required|integer|between:1,2',
//            'offer_value' => 'required|integer',
//        ]);
//        ReferralSystem::create([
//            'merchant_id' => $merchant_id,
//            'country_id' => $request->country_id,
//            'application' => 0,
//            'sender_discount' => $request->sender_discount,
//            'receiver_discount' => $request->receiver_discount,
//            'start_date' => $request->start_date,
//            'end_date' => $request->end_date,
//            'offer_type' => $request->offer_type,
//            'offer_value' => $request->offer_value,
//            'status' => $request->status,
//        ]);
//        return redirect()->route('merchant.refer.index');
//    }
//
//    public function Referedit($id)
//    {
//        $checkPermission = check_permission(1, 'edit_refer');
//        if ($checkPermission['isRedirect']) {
//            return $checkPermission['redirectBack'];
//        }
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $refer = ReferralSystem::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
//        return view('merchant.random.refer-edit', compact('refer'));
//    }
//
//    public function ReferUpdate(Request $request, $id)
//    {
//        $request->validate([
//            'sender_discount' => 'required|integer|between:1,2',
//            'receiver_discount' => 'required|integer|between:1,2',
//            'start_date' => 'required',
//            'end_date' => 'required',
//            'offer_type' => 'required|integer|between:1,3',
//            'status' => 'required|integer|between:1,2',
//            'offer_value' => 'required|integer',
//        ]);
//        $refer = ReferralSystem::find($id);
//        $refer->sender_discount = $request->sender_discount;
//        $refer->receiver_discount = $request->receiver_discount;
//        $refer->start_date = $request->start_date;
//        $refer->end_date = $request->end_date;
//        $refer->offer_type = $request->offer_type;
//        $refer->offer_value = $request->offer_value;
//        $refer->status = $request->status;
//        $refer->save();
//        return redirect()->back()->with('refer', 'Refer Updated');
//    }
//
//    public function ChangeStatus($id, $status)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $validator = Validator::make(
//            [
//                'id' => $id,
//                'status' => $status,
//            ],
//            [
//                'id' => ['required'],
//                'status' => ['required', 'integer', 'between:1,2'],
//            ]);
//        if ($validator->fails()) {
//            return redirect()->back();
//        }
//        $refer = ReferralSystem::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
//        $refer->status = $status;
//        $refer->save();
//        return redirect()->back();
//    }
//
//    public function Driver_ChangeStatus($id, $status)
//    {
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        $validator = Validator::make(
//            [
//                'id' => $id,
//                'status' => $status,
//            ],
//            [
//                'id' => ['required'],
//                'status' => ['required', 'integer', 'between:1,2'],
//            ]);
//        if ($validator->fails()) {
//            return redirect()->back();
//        }
//        $refer = ReferralSystem::where([['merchant_id', '=', $merchant_id], ['application', '=', 2]])->findOrFail($id);
//        $refer->status = $status;
//        $refer->save();
//        return redirect()->back();
//    }

    public function Configuration()
    {
        $checkPermission = check_permission(1, 'view_configuration');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $languages = Merchant::with('Language')->find($merchant_id);
        $service_types = $languages->Service;
        $languages = $languages->language;
        $configuration = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        return view('merchant.random.configuration', compact('service_types', 'configuration', 'languages'));
    }

    public function StoreConfiguration(Request $request)
    {
        $checkPermission = check_permission(1, 'edit_configuration');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'driver_wallet' => 'required|integer|between:1,2',
            'google_key' => 'required',
            'distance' => 'required|numeric',
            'number_of_driver' => 'integer',
            'report_issue_email' => 'required|email',
            'report_issue_phone' => 'required',
            'number_of_driver_user_map' => 'required|integer',
            'location_update_timeband' => 'required|integer',
            'tracking_screen_refresh_timeband' => 'required|integer',
            'ride_later_request' => 'integer|between:1,2',
            'ride_later_request_number_driver' => 'integer',
            'distance_ride_later' => 'integer',
            'android_user_maintenance_mode' => 'required|integer|between:1,2',
            'android_user_version' => 'required',
            'android_user_mandatory_update' => 'required|integer|between:1,2',
            'ios_user_maintenance_mode' => 'required|integer|between:1,2',
            'ios_user_version' => 'required',
            'ios_user_mandatory_update' => 'required|integer|between:1,2',
            'android_driver_maintenance_mode' => 'required|integer|between:1,2',
            'android_driver_mandatory_update' => 'required|integer|between:1,2',
            'ios_driver_maintenance_mode' => 'required|integer|between:1,2',
            'ios_driver_mandatory_update' => 'required|integer|between:1,2',
            'outstation_request_type' => 'integer|between:1,2',
            'home_screen' => 'required|integer|between:1,2',
            'pool_radius' => 'numeric',
            'driver_request_timeout' => 'integer',
            'outstation_time_before' => 'integer',
            'no_driver_outstation' => 'integer',
            'ride_later_time_before' => 'integer',
            'outstation_radius' => 'numeric',
            'pool_drop_radius' => 'numeric',
            'no_of_drivers' => 'integer',
            'maximum_exceed' => 'integer',
            'outstation_time' => 'integer',
            'android_driver_version' => 'required',
            'ios_driver_version' => 'required',
            'user_wallet_amount' => 'required',
            'driver_wallet_amount' => 'required',
            'ride_later_hours' => 'integer',
            'default_language' => 'required',
        ]);
        $userWallet = array();
        foreach ($request->user_wallet_amount as $value) {
            $userWallet[] = array('amount' => $value);
        }
        $driverWallet = array();
        foreach ($request->driver_wallet_amount as $value) {
            $driverWallet[] = array('amount' => $value);
        }
        Configuration::updateOrCreate(
            ['merchant_id' => $merchant_id],
            [
                'driver_wallet_status' => $request->driver_wallet,
                'google_key' => $request->google_key,
                'distance' => $request->distance,
                'number_of_driver' => $request->number_of_driver,
                'report_issue_email' => $request->report_issue_email,
                'report_issue_phone' => $request->report_issue_phone,
                'number_of_driver_user_map' => $request->number_of_driver_user_map,
                'location_update_timeband' => $request->location_update_timeband,
                'tracking_screen_refresh_timeband' => $request->tracking_screen_refresh_timeband,
                'ride_later_request' => $request->ride_later_request,
                'ride_later_request_number_driver' => $request->ride_later_request_number_driver,
                'distance_ride_later' => $request->distance_ride_later,
                'android_user_maintenance_mode' => $request->android_user_maintenance_mode,
                'android_user_version' => $request->android_user_version,
                'android_user_mandatory_update' => $request->android_user_mandatory_update,
                'ios_user_maintenance_mode' => $request->ios_user_maintenance_mode,
                'ios_user_version' => $request->ios_user_version,
                'ios_user_mandatory_update' => $request->ios_user_mandatory_update,
                'android_driver_maintenance_mode' => $request->android_driver_maintenance_mode,
                'android_driver_version' => $request->android_driver_version,
                'android_driver_mandatory_update' => $request->android_driver_mandatory_update,
                'ios_driver_maintenance_mode' => $request->ios_driver_maintenance_mode,
                'ios_driver_version' => $request->ios_driver_version,
                'ios_driver_mandatory_update' => $request->ios_driver_mandatory_update,
                'driver_request_timeout' => $request->driver_request_timeout,
                'ride_later_hours' => $request->ride_later_hours,
                'default_language' => $request->default_language,
                'ride_later_time_before' => $request->ride_later_time_before,
                'outstation_time' => $request->outstation_time,
                'outstation_request_type' => $request->outstation_request_type,
                'no_driver_outstation' => $request->no_driver_outstation,
                'outstation_time_before' => $request->outstation_time_before,
                'outstation_radius' => $request->outstation_radius,
                'home_screen' => $request->home_screen,
                'pool_radius' => $request->pool_radius,
                'pool_drop_radius' => $request->pool_drop_radius,
                'no_of_drivers' => $request->no_of_drivers,
                'maximum_exceed' => $request->maximum_exceed,
                'user_wallet_amount' => json_encode($userWallet, true),
                'driver_wallet_amount' => json_encode($driverWallet, true),
            ]
        );
        VersionManagement::updateVersion($merchant_id);
        return redirect()->back()->with('configuration', 'Updated');
    }

    public function Ratings()
    {
        $checkPermission = check_permission(1, 'ratings');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $ratings = $this->getAllRating();
        $data = [];
        return view('merchant.random.ratings', compact('ratings', 'data'));
    }

    public function RatingsDelivery()
    {
        $checkPermission = check_permission(1, 'ratings');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $ratings = $this->getAllRatingDelivery();
        return view('merchant.random.ratings', compact('ratings'));
    }


    public function SearchRating(Request $request)
    {
        $query = $this->getAllRating(false);
        if ($request->booking_id) {
            $keyword = $request->booking_id;
            $query->WhereHas('Booking', function ($q) use ($keyword) {
                $q->where('merchant_booking_id', $keyword);
            });
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('Booking', function ($q) use ($keyword) {
                $q->WhereHas('User', function ($qu) use ($keyword) {
                    $qu->where('UserName', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
                });
            });
        }
        if ($request->driver) {
            $driverKeyword = $request->driver;
            $query->WhereHas('Booking', function ($q) use ($driverKeyword) {
                $q->WhereHas('Driver', function ($qu) use ($driverKeyword) {
                    $qu->where('fullName', 'LIKE', "%$driverKeyword%")->orWhere('email', 'LIKE', "%$driverKeyword%")->orWhere('phoneNumber', 'LIKE', "%$driverKeyword%");
                });
            });
        }
        $ratings = $query->paginate(25);
        $data = $request->all();
        return view('merchant.random.ratings', compact('ratings', 'data'));
    }

    public function SearchRatingDelivery(Request $request)
    {
        $query = $this->getAllRatingDelivery(false);
        if ($request->booking_id) {
            $query->where('booking_id', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('Booking', function ($q) use ($keyword) {
                $q->WhereHas('User', function ($qu) use ($keyword) {
                    $qu->where('UserName', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
                });
            });
        }
        if ($request->driver) {
            $driverKeyword = $request->driver;
            $query->WhereHas('Booking', function ($q) use ($driverKeyword) {
                $q->WhereHas('Driver', function ($qu) use ($driverKeyword) {
                    $qu->where('fullName', 'LIKE', "%$driverKeyword%")->orWhere('email', 'LIKE', "%$driverKeyword%")->orWhere('phoneNumber', 'LIKE', "%$driverKeyword%");
                });
            });
        }
        $ratings = $query->paginate(25);
        return view('merchant.random.ratings', compact('ratings'));
    }

    public function ServiceType()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $services = ServiceType::where([['merchant_id', '=', $merchant_id]])->get();
        return view('merchant.service', compact('services'));
    }

    public function OneSignal()
    {
        $checkPermission = check_permission(1, 'view_onesignal');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $arr_food_grocery = get_merchant_segment(false, $merchant_id, 1, 2);
        $food_grocery = !empty($arr_food_grocery) ? count($arr_food_grocery) > 0 == true :false;
        $is_demo = $merchant->demo == 1 ? true : false;
        $onesignal = Onesignal::where([['merchant_id', '=', $merchant_id]])->first();
        $info_setting = InfoSetting::where('slug', 'PUSH_NOTIFICATION_CONFIGURATION')->first();
        return view('merchant.random.onesignal', compact('onesignal','info_setting','is_demo','food_grocery'));
    }

    public function UpdateOneSignal(Request $request)
    {
        $checkPermission = check_permission(1, 'edit_onesignal');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $config = get_merchant_notification_provider($merchant_id,null,null,'full');
        $string_file = $this->getStringFile($merchant_id);
        $arr_fields = [];
        if (isset($config->fire_base) && $config->fire_base == true) {
            $request->validate([
                'pem_password_user' => 'required',
                'pem_password_driver' => 'required',
                'firebase_api_key_android' => 'required',
            ]);

            $arr_fields = [
                'firebase_api_key_android' => $request->firebase_api_key_android,
                'pem_password_driver' => $request->pem_password_driver,
                'pem_password_user' => $request->pem_password_user,
            ];
            /* upload pem file of user and driver*/
            if ($request->hasFile('firebase_ios_pem_user')) {
                $pem_file_user = $request->file('firebase_ios_pem_user');
//                p($pem_file_user);
                $user_filename = $pem_file_user->getClientOriginalName();
                $pem_file_user->move(public_path('pem-file'), $user_filename);
                $arr_fields['firebase_ios_pem_user'] = $user_filename;
            }

            if ($request->hasFile('firebase_ios_pem_driver')) {
                $pem_file_driver = $request->file('firebase_ios_pem_driver');
                $driver_filename = $pem_file_driver->getClientOriginalName();
                $pem_file_driver->move('pem-file', $driver_filename);
                $arr_fields['firebase_ios_pem_driver'] = $driver_filename;
            }
        } else {
            $request->validate([
                'user_application_key' => 'required',
                'user_rest_key' => 'required',
                'user_channel_id' => 'required',
                'driver_application_key' => 'required',
                'driver_rest_key' => 'required',
                'driver_channel_id' => 'required',
            ]);
            $arr_fields = [
                'user_application_key' => $request->user_application_key,
                'user_rest_key' => $request->user_rest_key,
                'user_channel_id' => $request->user_channel_id,
                'driver_application_key' => $request->driver_application_key,
                'driver_rest_key' => $request->driver_rest_key,
                'driver_channel_id' => $request->driver_channel_id,
                'web_application_key' => $request->web_application_key,
                'web_rest_key' => $request->web_rest_key,

                'business_segment_application_key' => $request->business_segment_application_key,
                'business_segment_rest_key' => $request->business_segment_rest_key,
                'business_segment_channel_id' => $request->business_segment_channel_id,
            ];
        }
        Onesignal::updateOrCreate(
            ['merchant_id' => $merchant_id],
            $arr_fields
        );
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function Customer_Support()
    {
        $checkPermission = check_permission(1, 'customer_support');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $customer_supports = CustomerSupport::where([['merchant_id', '=', $merchant_id]])->orderBy('created_at', 'DESC')->paginate(25);
        $info_setting = InfoSetting::where('slug', 'CUSTOMER_SUPPORT')->first();
        return view('merchant.random.customer_support', compact('customer_supports', 'info_setting'));
    }

    public function Customer_Support_Search(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $query = CustomerSupport::where([['merchant_id', '=', $merchant_id]]);
        if ($request->application) {
            $query->where('application', $request->application);
        }
        if ($request->date) {
            $query->whereDate('created_at', '=', $request->date);
        }
        if ($request->name) {
            $query->where('name', 'LIKE', "%$request->name%");
        }
        $customer_supports = $query->paginate(25);
        $info_setting = InfoSetting::where('slug', 'CUSTOMER_SUPPORT')->first();
        return view('merchant.random.customer_support', compact('customer_supports', 'info_setting'));
    }

    public function DistnaceIndex()
    {
        $checkPermission = check_permission(1, 'view_distnace');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $distance = DistanceSetting::where([['merchant_id', '=', $merchant_id]])->first();
        return view('merchant.random.distance', compact('distance'));
    }

    public function DistnaceCreate()
    {
        $checkPermission = check_permission(1, 'edit_distnace');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $methods = DistanceMethod::get();
        return view('merchant.random.create-distance', compact('methods'));
    }

    public function DistnaceStore(Request $request)
    {
        $this->validate($request, [
            'method_id' => 'required'
        ]);
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $methods = $request->method_id;
        $methods = array_filter($methods);
        $distanceSettings = array();
        foreach ($methods as $key => $value) {
            $logic = $key;
            $method_id = $value;
            $distance_method = DistanceMethod::find($method_id);
            $method_name = $distance_method->method;
            switch ($logic) {
                case "0":
                    $distanceSettings[] = array(
                        'method_id' => $method_id,
                        'method_name' => $method_name,
                        'last_timestamp_difference' => $request->last_timestamp_difference_one,
                        'maximum_timestamp_difference' => $request->maximum_timestamp_difference_one,
                        'minimum_lat_long' => $request->minimum_lat_long_one,
                        'unknown_road' => $request->unknown_road_one,
                        'min_speed' => $request->min_speed_one,
                        'max_speed' => $request->max_speed_one
                    );
                    break;
                case "1":
                    $distanceSettings[] = array(
                        'method_id' => $method_id,
                        'method_name' => $method_name,
                        'last_timestamp_difference' => $request->last_timestamp_difference_second,
                        'maximum_timestamp_difference' => $request->maximum_timestamp_difference_second,
                        'minimum_lat_long' => $request->minimum_lat_long_second,
                        'unknown_road' => $request->unknown_road_second,
                        'min_speed' => $request->min_speed_second,
                        'max_speed' => $request->max_speed_second
                    );
                    break;
                case "2":
                    $distanceSettings[] = array(
                        'method_id' => $method_id,
                        'method_name' => $method_name,
                        'last_timestamp_difference' => $request->last_timestamp_difference_third,
                        'maximum_timestamp_difference' => $request->maximum_timestamp_difference_third,
                        'minimum_lat_long' => $request->minimum_lat_long_third,
                        'unknown_road' => $request->unknown_road_third,
                        'min_speed' => $request->min_speed_third,
                        'max_speed' => $request->max_speed_third
                    );
                    break;
                case "3":
                    $distanceSettings[] = array(
                        'method_id' => $method_id,
                        'method_name' => $method_name,
                        'last_timestamp_difference' => "",
                        'maximum_timestamp_difference' => "",
                        'minimum_lat_long' => "",
                        'unknown_road' => "",
                        'min_speed' => "",
                        'max_speed' => ""
                    );
                    break;
            }
        }
        DistanceSetting::updateOrCreate(
            ['merchant_id' => $merchant_id],
            [
                'distance_methods' => json_encode($distanceSettings, true),
            ]
        );
        return redirect()->route('merchant.distnace');
    }

    public function LanguageStrings()
    {
        $merchant_id = get_merchant_id();
        $string = [];
//            $this->langaugeString(App::getLocale());
        $language_strings = LanguageString::with('LanguageSingleMessage')->get();
        $exist_data = LanguageStringTranslation::where([['locale', '=', App::getLocale()], ['merchant_id', '=', $merchant_id]])->get();
        if ($exist_data->count() > 0) {
            $string = [];
        }
        $info_setting = App\Models\InfoSetting::where('slug', 'LANGUAGE_STRING')->first();
        return view('merchant.random.languagestrings', compact('language_strings', 'string', 'info_setting'));
    }

//    // get common language file
//    public function commonLanguageStrings()
//    {
//        $merchant = get_merchant_id(false);
//        $locale = App::getLocale();
//        // it will return only returned data of file
//        $module_file = $this->getStringFile($merchant->merchant_id);
//        $string_group = $merchant->string_group;
//        $merchant_lang_file = "";
//        $merchant_file_exist = false;
//        try {
//            // check merchant file in selected locale
//            $file = base_path().'/resources/lang/'.$locale.'/'.$module_file;
//            if(file_exists($file))
//            {
//                $merchant_lang_file = require($file);
//                $merchant_file_exist = true;
//            }
//            else
//            {
//                // check merchant file in english locale
//                $file = base_path().'/resources/lang/en/'.$module_file;
//                if(file_exists($file))
//                {
//                    $merchant_lang_file = require($file);
//                }
//                else
//                {
//                    // check all_in_one file in english locale
//                    $file = base_path().'/resources/lang/en/all_in_one.php';
//                    $merchant_lang_file = require($file);
//                }
//
//            }
//        }catch (\Exception $e)
//        {
//            return redirect()->back()->withErrors($e->getMessage());
//            p($e->getMessage());
//        }
//
//        $language_strings = [];
//        $project_strings =  $this->langaugeString($string_group); //
//        $info_setting = App\Models\InfoSetting::where('slug', 'LANGUAGE_STRING')->first();
//        return view('merchant.language-file.common-strings', compact('language_strings','merchant_lang_file','info_setting','project_strings','merchant_file_exist'));
//    }
//
//    // submit common language file
//    public function submitCommonLanguageStrings(Request $request)
//    {
//        try {
//            $merchant = get_merchant_id(false);
//            $locale = App::getLocale();
//            $module_file = $this->getStringFile($merchant->merchant_id);
//            $file = base_path().'/resources/lang/'.$locale.'/'.$module_file;
//
//            if(file_exists($file))
//            {
//                $string_file = fopen($file, "w+") or die("Unable to open file!");
//                // add php tag
//                $content = "<?php\n\n";
//                fwrite($string_file, $content);
//                // add return
//                $content = "return ";
//                fwrite($string_file, $content);
//
//                // submitted key by merchant
//                $dummyArr = $request->all()['name'];
//                // add key array
//                fwrite($string_file, var_export($dummyArr,true));
//                // add semi colon
//                $content = ";";
//                fwrite($string_file, $content);
//                fclose($string_file);
//                chmod($file,0777);
//                return redirect()->route('merchant.custom-language-strings')->withSuccess(trans("$string_file.string_file_updated"));
//            }
//            else
//            {
//                return redirect()->route('merchant.common-strings')->withErrors(trans("$string_file.string_file_not_found"));
//            }
//        }catch (\Exception $e)
//        {
//            return redirect()->back()->withErrors($e->getMessage());
//            p($e->getMessage());
//        }
//    }

    public function UpdateLanguageString(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        foreach ($request->name as $key => $value) {
            if ($value) {
                $this->SaveMessage($key, $merchant_id, $value);
            }
        }
        return redirect()->back()->with('languageString', trans('admin.languageString'));
    }

    public function SaveMessage($language_string_id, $merchant_id, $value)
    {
        LanguageStringTranslation::updateOrCreate([
            'language_string_id' => $language_string_id,
            'merchant_id' => $merchant_id,
            'locale' => App::getLocale()
        ], [
            'name' => $value
        ]);
    }

    public function SetLangauge(Request $request, $locle)
    {
        $request->session()->put('locale', $locle);
        return redirect()->back();
    }

    public function profile()
    {
        return view('merchant.random.edit-profile');
    }

    public function ProfileUpdate(Request $request)
    {
        $request->validate([
            'merchantFirstName' => "required",
            'merchantLastName' => "required",
            'merchantAddress' => "required",
            'merchantPhone' => 'required',
//            'login_background_image' => 'mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=1280,min_height=980',
            'login_background_image' => 'mimes:jpeg,png,jpg,gif,svg',
            'password' => 'required_if:edit_password,1'
        ]);
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL, $merchant);
        if ($request->hasFile('business_logo')) {
            $merchant->BusinessLogo = $this->uploadImage('business_logo', 'business_logo');
        }
        if ($request->hasFile('login_background_image')) {
            $theme = $merchant->ApplicationTheme;
            $theme->merchant_id = $merchant->id;
            $theme->login_background_image = $this->uploadImage('login_background_image', 'login_background');
            $theme->save();
        }
        $merchant->merchantFirstName = $request->merchantFirstName;
        $merchant->merchantLastName = $request->merchantLastName;
        $merchant->merchantAddress = $request->merchantAddress;
        $merchant->merchantPhone = $request->merchantPhone;
        if ($request->edit_password == 1) {
            $password = Hash::make($request->password);
            $merchant->password = $password;
        }
        $merchant->save();
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function givePermissionToSuperAdmin(Request $request)
    {
        $roles = Role::get();
        foreach ($roles as $role) {
            if ($role->name == "Super Admin" . $role->merchant_id) {
                $permissions = Permission::where("permission_type", 1)->get()->pluck('id')->toArray();
                $role->syncPermissions($permissions);
            }
        }
        p("Done");
    }
}
