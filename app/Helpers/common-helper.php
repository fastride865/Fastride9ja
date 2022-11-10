<?php

// print or echo function
use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\GetString;
use App\Models\CountryArea;
use App\Models\Driver;
use App\Models\DriverDocument;
use App\Models\DriverSegmentDocument;
use App\Models\DriverVehicle;
use App\Models\DriverVehicleDocument;
use App\Models\Merchant;
use App\Models\BookingConfiguration;
use App\Http\Controllers\Helper\GoogleController;
use App\Models\Merchant as MerchantModel;
use App\Models\Sos;
use App\Models\UserDevice;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\BusinessSegment\BusinessSegment;
use App\Http\Controllers\Helper\Merchant as helperMerchant;
use Spatie\Permission\Models\Permission;
use Stichoza\GoogleTranslate\GoogleTranslate;

//use DateTime;


function p($p, $exit = 1)
{
    echo '<pre>';
    print_r($p);
    echo '</pre>';
    if ($exit == 1) {
        exit;
    }
}



// functions to get images from s3 bucket
function get_image($file_name = '', $custom_key = '', $merchant_id = NULL, $merchant = true, $signed_url = true,
                    $session = "")
{
    $return_file = '';
    $alias = '';
    if (!empty($file_name)) {
        if ($file_name == 'stub_document') {
            $return_file = 'static-images/stub_document.png';
        } else {
            $upload_path = \Config::get('custom.' . $custom_key);
            if ($merchant) {
                $id = $merchant_id ? $merchant_id : get_merchant_id();
                $merchant = Merchant::Find($id);
                $alias = $merchant->alias_name;
                $file = $alias . $upload_path['path'] . $file_name;
            } else {
                $file = $upload_path['path'] . $file_name;
            }
            $return_file = $file;
        }
    } else {
        $return_file = 'static-images/no-image.png';
    }

    // return simple url
//    if($signed_url == false)
//    {
//        $return_file = env('AWS_BUCKET_URL') . $return_file;
//        return $return_file;
//    }

    $minutes = 600;
    if (!empty($session)) {
        if ($session == "driver" || $session == "user" || $session == "bs" || $session == "email") {
            $minutes = 10080; // 3 months
        }
    }

    // return signed url
    $sharedConfig = [
        'region' => env('AWS_DEFAULT_REGION'),
        'version' => 'latest'
    ]; //I have AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY as environment variables

    $s3Client = new S3Client($sharedConfig);
    $cmd = $s3Client->getCommand('GetObject', [
        'Bucket' => env("AWS_BUCKET"),
        'Key' => $return_file
    ]);

    $request = $s3Client->createPresignedRequest($cmd, "+$minutes minutes");
    $presignedUrl = (string)$request->getUri();
    return $presignedUrl;
}


function explode_image_path($file_name)
{
    if (!empty($file_name)) {
        $image = explode('/', $file_name);
        return end($image);
    }
    return '';
}

function view_config_image($file_name)
{
    if (!empty($file_name)) {
        return env('AWS_BUCKET_URL') . $file_name;
    } else {
        return env('AWS_BUCKET_URL') . 'static-images/no-image.png';
    }
}

function get_config_image($dir)
{
    $upload_path = \Config::get('custom.' . $dir);
    $files = \Storage::disk('s3')->files($upload_path['path']);
    return $files;
}

// delete image from s3
function delete_image($image, $dir = 'images', $merchant_id = null)
{
    $upload_path = \Config::get('custom.' . $dir);
    $id = $merchant_id ? $merchant_id : get_merchant_id();
    $merchant = Merchant::Find($id);
    $alias = $merchant->alias_name;
    $filePath = $alias . $upload_path['path'] . $image;
    // its returning 1 in case of success
    return Storage::disk('s3')->delete($filePath);
}



//functions to get images from gsc
//function get_image($file_name = '', $custom_key = '', $merchant_id = NULL, $merchant = true,$signed_url = true,$session = "")
//{
//    if (!empty($file_name)) {
//        if($file_name == 'stub_document')
//        {
//            $return_file = 'static-images/stub_document.png';
//        }
//        else
//        {
//            $upload_path = \Config::get('custom.' . $custom_key);
//            if ($merchant) {
//                $id = $merchant_id ? $merchant_id : get_merchant_id();
//                $merchant = Merchant::Find($id);
//                $alias = $merchant->alias_name;
//                $file = $alias. $upload_path['path'] . $file_name;
//            } else {
//                $file = $upload_path['path'] . $file_name;
//            }
//            $return_file = $file;
//        }
//    } else {
//        $return_file = 'static-images/no-image.png';
//    }
//    $duration = 604800;
//    $url = Storage::disk('gcs'/* following your filesystem configuration */)
//        ->getAdapter()
//        ->getBucket()
//        ->object($return_file)
//        ->signedUrl(new \DateTime('+ ' . $duration . 'seconds'));
//    return $url;
//}
//
//function view_config_image($file_name)
//{
//    if (!empty($file_name)) {
//        $return_file = $file_name;
//    } else {
//        $return_file ='static-images/no-image.png';
//    }
//    $duration = 604800;
//    $url = Storage::disk('gcs'/* following your filesystem configuration */)
//        ->getAdapter()
//        ->getBucket()
//        ->object($return_file)
//        ->signedUrl(new \DateTime('+ ' . $duration . 'seconds'));
//    return $url;
//}
//
//function get_config_image($dir)
//{
////    $upload_path = \Config::get('custom.' . $dir);
////    $files = \Storage::disk('s3')->files($upload_path['path']);
//    $files = [
//        "mapicon/ambulance.png"=>"mapicon/ambulance.png",
//        "mapicon/auto-rickshaw.png"=>"mapicon/auto-rickshaw.png",
//        "mapicon/car.png"=>"mapicon/car.png",
//        "mapicon/taxi_en_mapa.png"=>"mapicon/taxi_en_mapa.png",
//        "mapicon/yellow_car.png"=>"mapicon/yellow_car.png",
//        "mapicon/yellow_car_luxury.png"=>"mapicon/yellow_car_luxury.png",
//        "mapicon/new_car_icon.jpeg"=>"mapicon/new_car_icon.jpeg",
//    ];
//    return $files;
//}
//function explode_image_path($file_name)
//{
//    if (!empty($file_name)) {
//        $image = explode('/', $file_name);
//        return end($image);
//    }
//    return '';
//}







function get_merchant_id($return_id = true)
{
    if (Auth::guard('merchant')->check()) {
        if ($return_id == true) {// return only id of merchant
            return Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        } else {// return object of loggedin user
            return Auth::user('merchant')->parent_id != 0 ? Merchant::Find(Auth::user('merchant')->parent_id) : Auth::user('merchant');
        }
    }
}

function get_business_segment($business_segment_id = true)
{
    if (Auth::guard('business-segment')->check()) {
        if ($business_segment_id == true) {
            return Auth::user('business-segment')->parent_id != 0 ? Auth::user('business-segment')->parent_id : Auth::user('business-segment')->id;
        } else {
            return Auth::user('business-segment')->parent_id != 0 ? BusinessSegment::Find(Auth::user('business-segment')->parent_id) : Auth::user('business-segment');
        }
    }
}

//function get_logged_user($guard = 'merchant',$return_id = true)
//{
//    if($guard == "merchant" && Auth::guard('merchant')->check())
//    {
//        if($return_id == true)
//        {// return only id of merchant
//            return Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//        }
//        else
//        {// return object of loggedin user
//            return Auth::user('merchant')->parent_id != 0 ? Merchant::Find(Auth::user('merchant')->parent_id): Auth::user('merchant');
//        }
//    }
//    elseif($guard == "business-segment" && Auth::guard('business-segment')->check())
//    {
//        if(Auth::guard('business-segment')->check()){
//            if($return_id == true)
//            {
//                return Auth::user('business-segment')->parent_id != 0 ? Auth::user('business-segment')->parent_id : Auth::user('business-segment')->id;
//            }
//            else{
//                return Auth::user('business-segment')->parent_id != 0 ? BusinessSegment::Find(Auth::user('business-segment')->parent_id) : Auth::user('business-segment');
//            }
//        }
//    }
//}

//function get_merchant_config()
//{
//    $taxi_company = get_taxicompany();
//    if(!empty($taxi_company)){
//        $merchant_id = $taxi_company->merchant_id;
//    }else{
//        $merchant_id = get_merchant_id();
//    }
//    $data = [];
//    if ($merchant_id) {
//        $data = Configuration::where('merchant_id', $merchant_id)->first()->toArray();
//    }
//    return $data;
//}

function get_merchant_google_key($merchant_id = NULL, $request_from = 'admin_backend')
{
    if (empty($merchant_id)) {
        $taxi_company = get_taxicompany();
        $hotel = get_hotel();
        if (!empty($taxi_company)) {
            $merchant_id = $taxi_company->merchant_id;
        } elseif (!empty($hotel)) {
            $merchant_id = $hotel->merchant_id;
        } else {
            $merchant_id = get_merchant_id();
        }

    }
    $data = BookingConfiguration::select('google_key', 'google_key_admin')->where('merchant_id', $merchant_id)->first();
    if ($request_from == 'admin_backend') {
        return !empty($data['google_key_admin']) ? $data['google_key_admin'] : '';
    } else {
        return !empty($data['google_key']) ? $data['google_key'] : '';
    }
}

function corporate_get_merchant_google_key()
{
    $merchant_id = Auth::user('corporate')->merchant_id;
    $data = BookingConfiguration::select('google_key_admin')->where('merchant_id', $merchant_id)->first();
    return !empty($data['google_key_admin']) ? $data['google_key_admin'] : '';

}


function redis_config()
{

    $redis = \Illuminate\Support\Facades\Redis::connection();
    $driver_id = 1;
    $redis->geoadd('drivers_trial', 77.0726, 28.4591, $driver_id);
    $driver_id = 2;
    $redis->geoadd('drivers_trial', 77.0726, 28.4591, $driver_id);
//    $redis->geodist();
    $redis->pipeline(function ($pipe) {
        for ($i = 0; $i < 10; $i++) {
            $pipe->set("index_key:1", $i);
            $pipe->set("index_key:2", 45);

        }
    });


    if ($posts = $redis->get('drivers_trial:1')) {

        return p(json_decode($posts), 0);
    }
    if ($posts = $redis->get('index_key:2')) {

        p(json_decode($posts));
    }
}

//function update_booking_map_image_at_ride_end($booking_id, $latitude, $longitude, $booking_coordinates, $key)
//{
//    // Store ride end image.
//    if (!empty($booking_coordinates)) {
//        $drop_location_lat_long = json_decode($booking_coordinates, true);
//    }
//    $start = $latitude . ',' . $longitude;
//    if (count($drop_location_lat_long) > 1) {
//        $end = array_pop($drop_location_lat_long);
//        $finish = $end['latitude'] . ',' . $end['longitude'];
//        $count_waypoints = count($drop_location_lat_long); // CHECK FOR MULTIPLE WAYPOINTS or SINGLE WAYPOINT
//        $multiple_waypoints = array();
//        for ($j = 0; $j < $count_waypoints; $j++) {
//            $lat_long = $drop_location_lat_long[$j]['latitude'] . ',' . $drop_location_lat_long[$j]['longitude'];
//            $multiple_waypoints[] = $lat_long;
//        }
//        $waypoints = implode("|", $multiple_waypoints);
//        $data = GoogleController::GoogleStaticMultiplePointsImage($start, $finish, $waypoints, $key, "metric");
//        $image = $data['image'];
//        if (!empty($image)) {
//            $booking = Booking::Find($booking_id);
//            $booking->map_image = $image;
//            $booking->save();
//            return $image;
//        }
//    }
//}

function get_date($date)
{
    return date('d F Y', strtotime($date));
}

function set_date($date)
{
    $new_date = new DateTime($date);
    return $new_date->format('Y-m-d H:i:s');
}

function add_blank_option($arr_option = [], $blank_option = 'Select')
{
    $first_option = array('' => $blank_option);
    return $first_option + $arr_option;
}

function get_sos_list($merchant_id, $application, $id = null)
{
    if (!empty($merchant_id) && !empty($application)) {
        $list = Sos::where([['merchant_id', '=', $merchant_id], ['sosStatus', '=', 1], ['application', '=', $application]])
            ->where(function ($q) use ($id) {
                if (!empty($id)) {
                    $q->where('user_id', $id);
                }
                $q->orWhere('user_id', NULL);
            })
            ->get();
        return $list;
    }
    return [];
}

function success_response($message, $data = [])
{
    return response()->json(['result' => 1, 'message' => $message, 'data' => $data]);
}

function error_response($message, $data = [])
{
    return response()->json(['result' => 0, 'message' => $message]);
}

//GetReferCode
function getRandomCode($length = 5)
{
    $code = base_convert(sha1(uniqid(mt_rand())), 16, 36);
    $newCode = substr(str_replace(array('0', 'o', '1', 'i'), '', $code), 0, $length);
    $referCode = strtoupper($newCode);
    return $referCode;
}

function getTempDocUpload($expireDate, $currentDate, $reminderDate)
{
    if (strtotime($expireDate) > strtotime($currentDate) && strtotime($expireDate) < strtotime($reminderDate)) {
        return true;
    } else {
        return false;
    }
}


function booking_log($data)
{
    $log_data = array(
        'request_type' => $data['request_type'],
        'request_data' => $data['data'],
        'additional_notes' => $data['additional_notes'],
        'hit_time' => date('Y-m-d H:i:s')
    );
    \Log::channel('booking')->emergency($log_data);
}

function save_user_device_player_id($request)
{
    if (!empty($request['user_id'])) {
//        \App\Models\User::where('id',$request['user_id'])->update(['unique_number' => $request['unique_number']]);
        $device_with_other_user = UserDevice::where('user_id', "!=", $request['user_id'])
            ->where('player_id', $request['player_id'])
            ->first();

        if (!empty($device_with_other_user->id)) {
            //delete player_id which had mapped with any other user device.
            $device_with_other_user->delete();
        }
        $device = UserDevice::where(['unique_number' => $request['unique_number'], 'package_name' => $request['package_name'], 'user_id' => $request['user_id']])->first();
        if (empty($device['id'])) {
            $device = new UserDevice;
            $device->user_id = $request['user_id'];
            $device->unique_number = $request['unique_number'];
            $device->apk_version = $request['apk_version'];
            $device->language_code = $request['language_code'];
            $device->manufacture = $request['manufacture'];
            $device->model = $request['model'];
            $device->device = $request['device'];
            $device->package_name = $request['package_name'];
            $device->operating_system = $request['operating_system'];
            $device->language_code = 'some language code';
        }
        $device->player_id = $request['player_id'];
        $device->save();
    }
}

function get_merchant_configuration($merchant_id = null)
{
    $taxi_company = get_taxicompany();
    $hotel = get_hotel();
    if (empty($merchant_id)) {
        if (!empty($taxi_company)) {
            $merchant_id = $taxi_company->merchant_id;
        } elseif (!empty($hotel)) {
            $merchant_id = $hotel->merchant_id;
        } else {
            $merchant_id = get_merchant_id();
        }
    }
    $config = MerchantModel::with('ApplicationConfiguration', 'BookingConfiguration')->where('id', $merchant_id)->first();
    return $config;
}

function get_package_type()
{
    return \Config::get('custom.package_type');
}

function get_driver_auto_verify_status($driver_id, $status = '', $for = 'doc')
{
    $auto_verify = $for == 'doc' ? 1 : 2;
    if (!empty($driver_id)) {
        $driver = Driver::Find($driver_id);
        if (isset($driver->Merchant->DriverConfiguration->auto_verify)) {
            $auto_verify = $driver->Merchant->DriverConfiguration->auto_verify;
            if ($status == 'final_status') {
                if ($for == 'doc') {
                    return $auto_verify == 1 ? 2 : 1; // 2 means verified // 1 means pending
                } elseif ($for == 'vehicle') {
                    //old status 1 means verified // 2 means pending
                    //new status 2 means verified // 1 means pending
                    return $auto_verify == 1 ? 2 : 1;
                }
            }
        }
//        else{
//            if($status == 'final_status')
//            {
//                if($for == 'doc')
//                {
//                    return   $auto_verify == 1 ? 2 : 1; // 2 means verified // 1 means pending
//                }
//                elseif($for =='vehicle')
//                {
//                    return   $auto_verify == 1 ? 1 : 2; // 1 means verified // 2 means pending
//                }
//            }
//        }
    }
    return $auto_verify;
}


function get_driver_multi_existing_vehicle_status($driver_id)
{
    $vehicle_active_status = 1; // active
    if (has_driver_multiple_or_existing_vehicle($driver_id) == true) {
        $vehicle_active_status = 2; // inactive
    }
    return $vehicle_active_status;
}

function has_driver_multiple_or_existing_vehicle($driver_id = null, $merchant_id = null, $by = 'driver')
{
    $return = false;
    if ($by == 'merchant' && !empty($merchant_id)) {
        $data = Merchant::Find($merchant_id);
    } else {
        $driver = Driver::Find($driver_id);
        $data = $driver->Merchant;
    }
    if ($data->demo == 1 && $by == 'merchant') {
        return false;
    }
    if ($data->Configuration->existing_vehicle_enable == 1 || ($data->Configuration->add_multiple_vehicle == 1) && $data->demo != 1) {
        $return = true;
    }
    return $return;
}

function get_driver_document_details($driver_id, $return_type = 'status', $document_type = 'any', $document_status = 4, $vehicle_id = null)
{
    $return = false;
    $personal_document_count = [];
    $vehicle_document_count = [];
    if ($document_type == 'personal' || $document_type == 'any') {
        $personal_document_count = DB::table('driver_documents as dd')
            ->join('drivers as d', 'dd.driver_id', '=', 'd.id')
            ->join('documents as doc', 'dd.document_id', '=', 'doc.id')
            ->where(function ($q) use ($document_status) {
                if ($document_status == 4) {
                    $q->where('doc.expire_date', 1);

                }
            })
            ->where(function ($q) use ($document_status) {
                if ($document_status == 4) {
                    $q->where('dd.document_verification_status', $document_status); // 1 means pending, 4 means expired
                    $q->orWhere('dd.document_verification_status', 3); // rejected case
                } else {
                    $q->where('dd.document_verification_status', $document_status); // 1 means pending, 4 means expired
                }
            })
            ->where('dd.status', 1)
            ->where('d.id', $driver_id)
            ->select('dd.id')
            ->get()->toArray(); // driver_id
    }
    if ($document_type == 'vehicle' || $document_type == 'any') {
        $vehicle_document_count = DB::table('driver_vehicle_documents as dvd')
            ->join('driver_driver_vehicle as ddv', 'dvd.driver_vehicle_id', '=', 'ddv.driver_vehicle_id')
            ->join('driver_vehicles as dv', 'ddv.driver_vehicle_id', '=', 'dv.id')
            ->join('drivers as d', 'dv.driver_id', '=', 'd.id')
            ->join('documents as doc', 'dvd.document_id', '=', 'doc.id')
            // ->where('dvd.document_verification_status',$document_status)// 4 means expired
            ->where(function ($q) use ($document_status) {
                if ($document_status == 4) {
                    $q->where('doc.expire_date', 1);

                }
            })
            ->where(function ($q) use ($document_status) {
                if ($document_status == 4) {
                    $q->where('dvd.document_verification_status', $document_status); // 1 means pending, 4 means expired
                    $q->orWhere('dvd.document_verification_status', 3); // rejected case
                } else {
                    $q->where('dvd.document_verification_status', $document_status); // 1 means pending, 4 means expired
                }
            })
            ->where(function ($q) use ($vehicle_id) {
                if (!empty($vehicle_id)) {
                    $q->where('ddv.driver_vehicle_id', $vehicle_id);
                }
            })
            ->where('d.id', $driver_id)
            ->where('dvd.status', 1)
            ->select('dvd.id')
            ->get()->toArray();
    }
    if ((count($personal_document_count) > 0 || count($vehicle_document_count) > 0) && $return_type == 'status') {
        $return = true;
    }
    return $return;
}

function check_driver_document($driver_id, $type = 'any', $vehicle_id = null, $document_verification_status = null, $status = '')
{
    $driver = Driver::Find($driver_id);
    if ($driver->Merchant->demo != 1) {
        if (($type == 'vehicle' || $type == 'any') && $driver->segment_group_id == 1) {
            $driver_vehicle = !empty($vehicle_id) ? DriverVehicle::where('id', $vehicle_id)->get() : $driver->DriverVehicle;
            $driver_vehicle = collect($driver_vehicle->values());
            $vehicle_type_id = $driver_vehicle[0]->vehicle_type_id;
            $country_area_id = $driver->country_area_id;
            $country_area = CountryArea::select('id')->whereHas('VehicleDocuments', function ($q) use ($vehicle_type_id, $country_area_id) {
                $q->where('vehicle_type_id', $vehicle_type_id);
                $q->where('country_area_id', $country_area_id);
            })
                ->with(['VehicleDocuments' => function ($q) use ($vehicle_type_id, $country_area_id) {
                    $q->where('vehicle_type_id', $vehicle_type_id);
                    $q->where('country_area_id', $country_area_id);
                }])
                ->Find($country_area_id);
            // p($country_area);
            if (!empty($country_area)) {
                $country_area_vehicle_documents = !empty($country_area->VehicleDocuments) ? $country_area->VehicleDocuments : NULL;
                // p($country_area_vehicle_documents->count());
                //p($country_area_vehicle_documents);
                if (!empty($country_area_vehicle_documents)) {
                    $vehicle_document = $country_area_vehicle_documents->where('documentNeed', 1)->count();
                    if ($vehicle_document > 0) {
                        if (isset($driver_vehicle)) {
                            $driver_vehicle_document = DriverVehicleDocument::whereHas('Document', function ($q) {
                                $q->where('documentNeed', 1);
                            })
                                ->where('driver_vehicle_id', $vehicle_id)
                                ->where(function ($q) use ($document_verification_status, $status) {
                                    if (!empty($document_verification_status)) {
                                        $q->where('document_verification_status', $document_verification_status);
//                                    if ($reject == 'reject' && $document_verification_status == 1) {
                                        if ($status == 'reject' || $status == 'expired') {
                                            $q->orWhere('document_verification_status', 2);
                                        }
                                    }
                                })
                                ->where('status', 1) // only active
                                ->count();
                            if ($vehicle_document > $driver_vehicle_document) {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        if (($type == 'segment' || $type == 'any') && $driver->segment_group_id == 2) {
            $country_area = CountryArea::findOrFail($driver->country_area_id);
            $country_area_segment_documents = $country_area->SegmentDocument;
            if (!empty($country_area_segment_documents)) {
                $segment_document = $country_area_segment_documents->where('documentNeed', 1)->count();
                if ($segment_document > 0) {
                    $driver_segment_document_count = DriverSegmentDocument::whereHas('Document', function ($q) {
                        $q->where('documentNeed', 1);
                    })->where('driver_id', $driver->id)
                        ->where(function ($q) use ($document_verification_status, $status) {
                            if (!empty($document_verification_status)) {
                                $q->where('document_verification_status', $document_verification_status);

                                if ($status == 'reject' || $status == 'expired') {
                                    $q->orWhere('document_verification_status', 2);
                                }
                            }
                        })
                        ->where('status', 1)// only active
                        ->count();
                    if ($segment_document > $driver_segment_document_count) {
                        return false;
                    }
                }
            }
        }
        if ($type == 'personal' || $type == 'any') {
            $country_area = CountryArea::findOrFail($driver->country_area_id);
            $country_area_driver_documents = $country_area->documents;
            if (!empty($country_area_driver_documents)) {
                $driver_document = $country_area_driver_documents->where('documentNeed', 1)->count();
                if ($driver_document > 0) {
                    $driver_document_count = DriverDocument::whereHas('Document', function ($q) {
                        $q->where('documentNeed', 1);
                    })->where('driver_id', $driver->id)
                        ->where(function ($q) use ($document_verification_status, $status) {
                            if (!empty($document_verification_status)) {
                                $q->where('document_verification_status', $document_verification_status);

                                if ($status == 'reject' || $status == 'expired') {
                                    $q->orWhere('document_verification_status', 2);
                                }
                            }
                        })
                        ->where('status', 1)// only active
                        ->count();
                    if ($driver_document > $driver_document_count) {
                        return false;
                    }
                }
            }
        }
    }
    return true;
}

function driver_all_document_status($driver_id, $vehicle_id = null)
{
    $final_document_status = false;
    $driver = Driver::select('id', 'merchant_id')->find($driver_id);
    if ($driver->Merchant->demo != 1) {
        $pending_document_status = check_driver_document($driver_id, $type = 'any', $vehicle_id);
        $expired_document_status = get_driver_document_details($driver_id, 'status', 'any', 4, $vehicle_id);
        if ($expired_document_status == true || $pending_document_status == false) {
            $final_document_status = true;
        }
    }
    return $final_document_status;
}

function get_online_and_busy_drivers($merchant_id)
{
    return DB::table('drivers as d')->where('d.login_logout', 1)->where('d.free_busy', 1)
        ->where('d.online_offline', 1)->where('d.merchant_id', $merchant_id)->count();
}

function get_driver_verified_vehicle($driver_id, $vehicle_id)
{
    $active_vehicle_count = DB::table('drivers as d')
        ->join('driver_driver_vehicle as ddv', 'd.id', '=', 'ddv.driver_id')
        ->join('driver_vehicles as dv', 'ddv.driver_vehicle_id', '=', 'dv.id')
        ->where('d.id', $driver_id)
        ->where('dv.id', '!=', $vehicle_id)
        ->where('vehicle_verification_status', 2)->count();
    return $active_vehicle_count;
}

function get_verified_vehicle($driver_id)
{
    $verified_vehicle = DB::table('drivers as d')
        ->join('driver_driver_vehicle as ddv', 'd.id', '=', 'ddv.driver_id')
        ->join('driver_vehicles as dv', 'ddv.driver_vehicle_id', '=', 'dv.id')
        ->where('d.id', $driver_id)
        ->where('vehicle_verification_status', 2)
        ->select('dv.id')
        ->first();
    return $verified_vehicle;
}


function get_taxicompany($id = false)
{
    if (Auth::guard('taxicompany')->check()) {
        return (Auth::user('taxicompany')->parent_id != 0) ? Auth::user('taxicompany')->parent_id : (($id) ? Auth::user('taxicompany')->id : Auth::user('taxicompany'));
    } else {
        return null;
    }
}
function get_driver_agency($id = true)
{
    if (Auth::guard('driver-agency')->check()) {
        if ($id == true) {// return only id of merchant
            return Auth::user('driver-agency')->id;
        } else {// return object of loggedin user
            return Auth::user('driver-agency');
        }
    }
    else
    {
        return null;
    }
}

function get_taxicompany_wallet($id)
{
    if ($id != null) {
        $taxicompany = \App\Models\TaxiCompany::select('wallet_money')->find($id);
        return !empty($taxicompany) ? $taxicompany->wallet_money : null;
    } else {
        return null;
    }
}

function get_hotel($id = false)
{
    if (Auth::guard('hotel')->check()) {
        return (Auth::user('hotel')->parent_id != 0) ? Auth::user('hotel')->parent_id : (($id) ? Auth::user('hotel')->id : Auth::user('hotel'));
    } else {
        return null;
    }
}

function check_permission($type, $permission, $hasOne = false, $string_file = "")
{
    //type 1 = merchant, type 2 = corporate, type 3 = taxicompany
    switch ($type) {
        case 1:
            $redirect = 'merchant.dashboard';
            $authUser = 'merchant';
            break;
        case 2:
            $redirect = 'corporate.dashboard';
            $authUser = 'corporate';
            break;
        case 3:
            $redirect = 'taxicompany.dashboard';
            $authUser = 'taxicompany';
            break;
        default:
            $redirect = '/';
            $authUser = 'merchant';
            break;
    }
    $isRedirect = false;
    $redirectBack = '';
//    $user = Auth::user($authUser);
    if (is_array($permission)) {
        if ($hasOne) {
            if (!Auth::user($authUser)->hasAnyPermission($permission)) {
                $redirectBack = Redirect::route($redirect)->withErrors(trans("all_in_one.permission_denied"));
                $isRedirect = true;
            }
        } else {
            if (!Auth::user($authUser)->hasAllPermissions($permission)) {
                $redirectBack = Redirect::route($redirect)->withErrors(trans("all_in_one.permission_denied"));
                $isRedirect = true;
            }
        }
    } else {
        if (!Auth::user($authUser)->can($permission)) {
            $redirectBack = Redirect::route($redirect)->withErrors(trans("all_in_one.permission_denied"));
            $isRedirect = true;
        }
    }
    return array('isRedirect' => $isRedirect, 'redirectBack' => $redirectBack);
}

function get_permission_segments($type = 1, $is_slag = false, $for_filter_segments = [])
{
    $return_segments = [];
    switch ($type) {
        case 1:
            $authUser = 'merchant';
            break;
        case 2:
            $authUser = 'corporate';
            break;
        case 3:
            $authUser = 'taxicompany';
            break;
        default:
            $authUser = 'merchant';
            break;
    }
    $segments = \App\Models\Segment::get()->pluck("slag")->toArray();
    if (!empty($segments)) {
        foreach ($segments as $segment) {
            if (Auth::user($authUser)->can($segment)) {
                array_push($return_segments, $segment);
            }
        }
        if (Auth::user($authUser)->can('HANDYMAN')) {
            $handyman_segments = \App\Models\Segment::where("segment_group_id", 2)->get()->pluck("slag")->toArray();
            $return_segments = array_merge($return_segments, $handyman_segments);
        }
    }
    $merchant_id = get_merchant_id();
    if (!empty($return_segments)) {
        $arr_segment = [];
        $return_segments = \App\Models\Segment::whereIn('slag', $return_segments)->get();
        foreach ($return_segments as $segment) {
            if ($is_slag) {
                $arr_segment[$segment['id']] = $segment->slag;// $segment->slag;
            } else {
                $arr_segment[$segment['id']] = !empty($segment->Name($merchant_id)) ? $segment->Name($merchant_id) : $segment->slag;// $segment->slag;
            }
        }
        $return_segments = $arr_segment;
    }
    if (!empty($for_filter_segments)) {
        foreach ($for_filter_segments as $key => $segment) {
            if (!in_array($segment, $return_segments)) {
                unset($for_filter_segments[$key]);
            }
        }
        $return_segments = $for_filter_segments;
    }
    return $return_segments;
}

function getRequestTimes($merchant_id)
{
    $bookingConfig = BookingConfiguration::where('merchant_id', $merchant_id)->first();
    $driverRequestTime = $bookingConfig->driver_request_timeout;
    $data = array(
        'user_request_timeout' => $driverRequestTime * 3,
        'ride_radius_increase_api_call_time' => $driverRequestTime
    );
    return $data;
}

function getSendDriverRequestLimit($booking)
{
    $booking_config = BookingConfiguration::where('merchant_id', $booking->merchant_id)->latest()->first();
    switch ($booking->service_type_id) {
        case '1':
            $limit = $booking->booking_type == 1 ? $booking_config->normal_ride_now_request_driver : $booking_config->normal_ride_later_request_driver;
            break;
        case '2':
            $limit = $booking->booking_type == 1 ? $booking_config->rental_ride_now_request_driver : $booking_config->rental_ride_later_request_driver;
            break;
        case '4':
            $limit = $booking->booking_type == 1 ? $booking_config->outstaion_ride_now_request_driver : $booking_config->outstation_request_driver;
            break;
        case '5':
            $limit = $booking->booking_type == 1 ? $booking_config->pool_now_request_driver : null;
            break;
        default :
            $limit = null;
            break;
    }
    return $limit;
}

function google_api_log($data)
{
    $log_data = array(
        'request_type' => $data['request_type'],
        'request_data' => $data['data'],
        'additional_notes' => $data['additional_notes'],
        'hit_time' => date('Y-m-d H:i:s')
    );
    \Log::channel('google_api')->emergency($log_data);
}

//function get_merchant_notification_provider($merchant_id = null)
//{
//    $merchant_id = empty($merchant_id) ? get_merchant_id() : $merchant_id;
//    $return = NULL;
//    if (!empty($merchant_id)) {
//        $merchant = Merchant::find($merchant_id);
//        $fire_base = false;
//        // 1: onesignal, 2: firebase
//        if (isset($merchant->Configuration->push_notification_provider) && $merchant->Configuration->push_notification_provider == 2) {
//            $fire_base = true;
//        }
//        if (!empty($merchant->Onesignal)) {
//            $return = $merchant->Onesignal;
//            $return->fire_base = $fire_base;
//        }
//        return $return;
//    }
//}

function get_merchant_notification_provider($merchant_id = null,$id = null,$type = null,$return = 'status')
{
    $merchant_id = empty($merchant_id) ? get_merchant_id():$merchant_id;
    if(!empty($merchant_id))
    {
        $merchant =  Merchant::find($merchant_id);
        $fire_base = false;
        $notification_provider = 1;
        // 1: onesignal, 2: firebase, 3: both
        if(isset($merchant->Configuration->push_notification_provider) && $merchant->Configuration->push_notification_provider == 2)
        {
            $fire_base = true;
            $notification_provider = 2;
        }
        elseif(isset($merchant->Configuration->push_notification_provider) && $merchant->Configuration->push_notification_provider == 3)
        {
            $notification_provider = 3;
            if(!empty($id) && !empty($type))
            {
                $arr_firebase_country = $merchant->Country->where('isoCode','EGP');
                $arr_firebase_country = array_pluck($arr_firebase_country,'id');
                if($type == 'user')
                {
                    $country = DB::table('users')->select('country_id')->where([['merchant_id',$merchant_id],['id',$id]])->first();
                    $country_id = $country->country_id;
                }
                else
                {
                    $country = DB::table('drivers as d')
                        ->select('country_id')->where([['merchant_id',$merchant_id],['d.id',$id]])->first();
                    $country_id = $country->country_id;
                }
                if(in_array($country_id,$arr_firebase_country))
                {
                    $fire_base = true;
                }
            }
        }
        if($return == 'full')
        {
            $return = $merchant->Onesignal;

        }
        else
        {
            $return = new stdClass;
        }
        $return->fire_base = $fire_base;
        $return->push_notification_provider = $notification_provider;
        return $return;
    }
}

function getAdditionalInfo()
{
    $data = array(
        'parameter_name' => 'Temperature',
        'display' => true
    );
    return $data;
}

function get_merchant_segment($with_taxi = true, $merchant_id = null, $segment_group_id = NULL, $sub_group_for_admin = NULL)
{
    if (empty($merchant_id)) {
        $merchant_id = get_merchant_id();
    }
    $segments = Merchant::with(['Segment' => function ($q) use ($merchant_id, $segment_group_id, $with_taxi, $sub_group_for_admin) {
        $q->select('id', 'slag', 'segment_id', 'name');
        if (!empty($segment_group_id)) {
            $q->where('segment_group_id', $segment_group_id);
        }
        if ($with_taxi == false) {
            $q->whereNotIn('id', [1, 2]);
        }
        if (!empty($sub_group_for_admin)) {
            $q->where('sub_group_for_admin', $sub_group_for_admin);
        }
    }])
        ->whereHas('Segment', function ($q) use ($merchant_id, $with_taxi, $sub_group_for_admin) {
            $q->where('merchant_id', $merchant_id);
            if ($with_taxi == false) {
                $q->whereNotIn('id', [1, 2]);
            }
            if (!empty($sub_group_for_admin)) {
                $q->where('sub_group_for_admin', $sub_group_for_admin);
            }
        })
        ->select('id')
        ->first();
    $arr_segment = [];

    if (!empty($segments->Segment)) {
        foreach ($segments->Segment as $segment) {
            $arr_segment[$segment['id']] = !empty($segment->Name($merchant_id)) ? $segment->Name($merchant_id) : $segment->slag;// $segment->slag;
        }
    }
    return $arr_segment;
}

function get_merchant_country($arr_list)
{
    $arr_country = [];
    foreach ($arr_list as $country) {
        $arr_country[$country['id']] = $country['CountryName'];
    }
    return $arr_country;
}

function get_merchant_document($arr_list)
{
    $arr_document = [];
    foreach ($arr_list as $document) {
        $arr_document[$document['id']] = $document['DocumentName'];
    }
    return $arr_document;
}

function get_merchant_vehicle($arr_list)
{
    $arr_vehicle = [];
    foreach ($arr_list as $vehicle) {
        $arr_vehicle[$vehicle['id']] = $vehicle['VehicleTypeName'];
    }
    return $arr_vehicle;
}

function get_merchant_delivery_type($arr_list)
{
    $arr_delivery_type = [];
    foreach ($arr_list as $delivery_type) {
        $arr_delivery_type[$delivery_type['id']] = $delivery_type['name'];
    }
    return $arr_delivery_type;
}

function get_merchant_package($arr_list)
{
    $arr_package_type = [];
    foreach ($arr_list as $package_type) {
        $arr_package_type[$package_type['id']] = $package_type['PackageName'];
    }
    return $arr_package_type;
}

function get_bill_type()
{
    $arr_list = App\Models\BillPeriod::get();
    $arr_type = [];
    foreach ($arr_list as $type) {
        $arr_type[$type['id']] = $type['name'];
    }
    return $arr_type;
}

function get_status($order = true, $string_file = "")
{
    if ($order == false) {
        $return = array(
            '1' => trans("$string_file.no"),
            '2' => trans("$string_file.yes"),
        );
    } else {
        $return = array(
            '1' => trans("$string_file.yes"),
            '2' => trans("$string_file.no"),
        );
    }
    return $return;
}

function get_days($string_file = "")
{
    return
        array(
            '0' => trans("$string_file.sunday"),
            '1' => trans("$string_file.monday"),
            '2' => trans("$string_file.tuesday"),
            '3' => trans("$string_file.wednesday"),
            '4' => trans("$string_file.thursday"),
            '5' => trans("$string_file.friday"),
            '6' => trans("$string_file.saturday"),
        );
    //\Config::get('custom.days');
}

function get_enable($string_file = "")
{
    return array(
        '1' => trans("$string_file.enable"),
        '2' => trans("$string_file.disable"),
    );
//        \Config::get('custom.status');
}

function get_service_vehicle($area_id, $service_type_id)
{
    $vehicle = [];
    if (!empty($area_id) && !empty($service_type_id)) {
        $vehicle = DB::table('country_area_vehicle_type')->where([['country_area_id', '=', $area_id], ['service_type_id', $service_type_id]])->select('vehicle_type_id')->get()->toArray();
        $vehicle = array_pluck($vehicle, 'vehicle_type_id');
    }
    return $vehicle;
}

function get_price_parameter($string_file, $on = "add")
{
    $merchant_type = [
        '' => trans("$string_file.select"),
        10 => trans("$string_file.base_fare_type"),
        1 => trans("$string_file.per_km_mile"),
        8 => trans("$string_file.per_minute"),
        2 => trans("$string_file.per_hour"),
        3 => trans("$string_file.standard"),
        9 => trans("$string_file.wait_type"),
        11 => trans("$string_file.discount"),
        12 => trans("$string_file.promo_code_discount"),
        13 => trans("$string_file.tax"),
        6 => trans("$string_file.dead_mileage")
    ];

    $super_admin_type = [
        14 => trans("$string_file.ac_charges"),
        15 => trans("$string_file.outstation_distance_charges"),
        17 => trans("$string_file.insurance"),
        18 => trans("$string_file.waiting_type_during_ride"),
        16 => trans("$string_file.minimum_fare_type"),
        19 => trans("$string_file.booking_fee_type"),
        20 => trans("$string_file.wait_type_fixed_charges"),
        21 => trans("$string_file.additional_drop_fair_type"),
    ];
    if ($on == "edit") {
        return $merchant_type + $super_admin_type;
    }

    return $merchant_type;
}

function merchant_price_type($merchant_rate_card)
{
    $data = [];
    if (!empty($merchant_rate_card)) {
        foreach ($merchant_rate_card as $value) {
            $data[$value->id] = $value->name;
        }
    }
    return $data;
}

function get_commission_type($string_file = "")
{
    return ["1" => trans("$string_file.prepaid"), "2" => trans("$string_file.postpaid")];
}

function get_commission_method($string_file = "")
{
    return ["1" => trans("$string_file.flat"), "2" => trans("$string_file.percentage")];
}

function get_on_off($string_file = "")
{
    return ["1" => trans("$string_file.on"), "0" => trans("$string_file.off")];
}

// its handyman segment price card type
function get_price_card_type($calling_from = '', $price_type_config = "BOTH", $string_file = "", $return_slug = false)
{
    $price_type = [];
    if ($price_type_config == "FIXED") {
        $price_type = $return_slug ? [1 => "FIXED"] : [1 => trans("$string_file.fixed")];
//        $price_type = [1 => trans("$string_file.fixed")];
    } elseif ($price_type_config == "HOURLY") {
        $price_type = $return_slug ? [2 => "HOURLY"] : [2 => trans("$string_file.hourly")];
//        $price_type = [2 => trans("$string_file.hourly")];
    } else {
        if ($return_slug) {
            $price_type = [1 => "FIXED", 2 => "HOURLY"];
        } else {
            $price_type = [1 => trans("$string_file.fixed"), 2 => trans("$string_file.hourly")];
        }
//        $price_type = [1 => trans("$string_file.fixed"), 2 => trans("$string_file.hourly")];
    }
    if ($calling_from == 'api') {
        foreach ($price_type as $id => $value) {
            $arr_price_type [] = ['id' => $id, 'value' => $value];
        }
        return $arr_price_type;
    }
    return $price_type;
}

function get_segment_group()
{
    $arr_group = App\Models\SegmentGroup::get();
    $arr = [];
    foreach ($arr_group as $group) {
        $arr[$group->id] = $group->group_name;
    }
    return $arr;
//    return array(
//        '1' => 'Vehicle Based Services(Taxi, Delivery etc)',
//        '2' => 'Helper/Helping Based Services(Plumber, Cleaning, Painting etc)',
//    );
}

function formatted_date($date)
{
    return date('Y-m-d', strtotime($date));
}

function is_merchant_segment_exist($segment, $for_all = false)
{
    $merchant_segments = helperMerchant::MerchantSegments();
    $result = false;
    if ($for_all) {
        $resultObj = array_intersect($merchant_segments, $segment);
        if (count($segment) == count($resultObj)) {
            $result = true;
        }
    } else {
        $result = !empty(array_intersect($merchant_segments, $segment));
    }
    return $result;
}

//function get_merchant_segment_group(){
//    $segment_group = \App\Http\Controllers\Helper\Merchant::MerchantSegments(2);
//    return $segment_group;
//}

function round_number($num , $decimals = 2)
{
    return number_format((float)$num, $decimals, '.', '');
}

function get_product_status($calling_from = "web", $string_file = "")
{
    $ava = trans("$string_file.available");
    $not = trans("$string_file.not");
    if ($calling_from == "app") {
        $return = [['key' => 1, 'value' => $ava], ['key' => 2, 'value' => $not . ' ' . $ava]];
    } else {
        $return = array('1' => $ava, '2' => $not . ' ' . $ava);
    }
    return $return;
}

function get_active_status($calling_from = "web", $string_file = "")
{
    $act = trans("$string_file.active");
    $inact = trans("$string_file.inactive");
    if ($calling_from == "app") {
        $return = [['key' => 1, 'value' => $act], ['key' => 2, 'value' => $inact]];
    } else {
        $return = array('1' => $act, '2' => $inact);
    }
    return $return;
}

function get_food_type($string_file, $calling_from = "web")
{
    $veg = trans("$string_file.veg");
    $non_veg = trans("$string_file.non_veg");
    $including_egg = trans("$string_file.including_egg");
//    ,['key'=>3, 'value'=>$including_egg]
    if ($calling_from == "app") {
        $return = [['key' => 1, 'value' => $veg], ['key' => 2, 'value' => $non_veg]];
    } else {
        $return = array('1' => $veg, '2' => $non_veg);
    }
    return $return;
}

function is_demo_data($string, $merchant_object = NULL, $merchant_id = NULL)
{
    if (empty($merchant_object) && !empty($merchant_id)) {
        $merchant_object = Merchant::select('demo')->find($merchant_id);
    }
    // check merchant data
    if (!empty($merchant_object) && $merchant_object->demo == 1) {
        $return_string = "********" . substr($string, -2);
    } else {
        $return_string = $string;
    }
    return $return_string;
}

function get_narration_value($narration_for, $narration, $merchant_id, $id = NULL, $receipt = NULL, $amount = NULL, $user_name = NULL)
{

    $get_string = new GetString($merchant_id);
    $string_file = $get_string->getStringFileText();
    $description = "";
    // common strings
    $no_description = trans("$string_file.no_description");
    $description_admin = trans("$string_file.wallet_recharged_by_admin");
    $description_self_credit = trans("$string_file.wallet_recharged_successfully");
    switch ($narration_for) {
        case "DRIVER":
            switch ($narration) {
                case "1":
                    $description = $description_admin;
//                    trans('api.message44');
                    break;
                case "2":
                    $description = $description_self_credit;
//                $description = trans('api.message45');
                    break;
                case "3":
                    //trans("$string_file.company").' '.
//                    $description = trans("$string_file.commission_of_ride_id") . ' #' . $id;
                    $description = trans("$string_file.ride_amount_debited") . $id;
//                $description = trans('api.message46') .' '. $booking_id;
                    break;
                case "4":
                    // In this cash, booking id is package id
                    $subscription_package_id = $id;
                    $booking_id = NULL;
                    $description = trans("$string_file.bought_subscription_package") . ' ' . $subscription_package_id;
                    break;
                case "5":
                    $description = trans("$string_file.money_added_in_wallet") . '(' . trans("$string_file.cashback") . ')';
                    break;
                case "6":
                    $description = trans("$string_file.ride_amount_credited") . $id;
                    break;
                case "7":
                    $description = $receipt;
                    break;
                case "8":
                    $description = trans("$string_file.cancelled_ride_amount_debited") . $id;
                    break;
                case "9":
                    $description = trans('api.reward_point_redeem_credit');
                    break;
                case "10":
                    $description = trans("$string_file.cashout_amount_deducted");
                    break;
                case "11":
                    $description = trans("$string_file.cancelled_ride_amount_credited") . $id;
                    break;
                case "12":
                    $description = trans("$string_file.user_old_outstanding_deducted");
                    break;
                case "13":
                    $description = trans("$string_file.order_amount_debited").$id;
//                    $description = trans("$string_file.order_commission_deducted");
                    break;
                case "14":
                    $description = trans("$string_file.order_amount_credited").$id;
//                    $description = trans("$string_file.order_commission_received");
                    break;
                case "15":
                    $description = trans("$string_file.cashout_request_rejected_refund_amount");
                    break;
                case "16":
                    $description = trans("$string_file.tip_credited_to_driver");
                    break;
                case "17":
                    $description = trans("$string_file.tax_amount_deducted");
                    break;
                case "18":
                    $description = trans("$string_file.amount_debited_by_admin");
                    break;
                case "19":
                    $description = trans("$string_file.cancelled_order_amount_credited") . $id;
                    break;
                case "20":
                    $description = trans("$string_file.booking_amount_debited").$id;
                    break;
                case "21":
                    $description = trans("$string_file.booking_amount_credited").$id;
                    break;
                case "22":
                    $description = trans("$string_file.referral_amount_credit");
                    break;
                case "23":
                    $description = trans("$string_file.you_have_received_amount_from", ['AMOUNT' => $amount, 'FROM' => $user_name]);
                    break;
                case "23":
                    $description = trans("$string_file.cashout_amount_debited");
                    break;
                case "25":
                    $description = trans("$string_file.tranaction_amount_settled_through_bank");
                    break;
                default:
                    $description = $no_description;
            }
            break;
        case "USER":
            switch ($narration) {
                case "1":
                    $description = $description_admin;
                    break;
                case "2":
                    $description = trans("$string_file.wallet_recharged_successfully");
                    break;
                case "3":
                    $description = trans("$string_file.wallet_money_added_with_coupon") . ' ' . $receipt;
                    break;
                case "4":
                    $description = trans("$string_file.ride_amount_debited") . ' ' . $id;
                    break;
                case "5":
                    $description = trans("$string_file.cancelled_ride_amount_debited") . ' ' . $id;
                    break;
                case "6":
                    $description = $receipt;
                    break;
                case "7":
                    $description = $id;
                    $booking_id = NULL;
                    break;
                case "8":
                    $description = trans("$string_file.wallet_debited");
                    break;
                case "9":
                    $description = trans("$string_file.amount_received");
                    break;
                case "10":
                    $description = trans("$string_file.tip_amount_debited");
                    break;
                case "11":
                    $description = trans_choice("$string_file.food_order_refund", ['ID' => $id]);
                    break;
                // wallet amount transfer
                case "12":
                    $description = trans("$string_file.you_have_received_amount_from", ['AMOUNT' => $amount, 'FROM' => $user_name]);
                    break;
                case "13":
                    $description = trans("$string_file.you_have_transferred_amount_to", ['AMOUNT' => $amount, 'TO' => $user_name]);
                    break;
                case "14":
                    $description = trans("$string_file.amount_debited_by_admin");
                    break;
                case "15":
                    $description = trans("$string_file.cancelled_order_amount_debited") . ' ' . $id;
                    break;
                case "16":
                    $description = trans("$string_file.referral_amount_credit");
                    break;
                case "17":
                    $description = trans("$string_file.cashout_amount_debited");
                    break;
                default:
                    $description = $no_description;
            }
            break;
        case "TAXI_COMPANY":
            break;
        case "HOTEL":
            break;
        case "BUSINESS_SEGMENT":
            switch ($narration) {
                case "1":
                    $description = $description_admin;
                    break;
                case "2":
                    $description = trans("$string_file.order_amount_added_by_admin");
                    break;
                case "3":
                    $description = trans("$string_file.order_commission_deducted") . $id;
                    break;
                case "4":
                    $description = trans("$string_file.cashout_amount_deducted");
                    break;
                case "5":
                    $description = trans("$string_file.cashout_request_rejected_refund_amount");
                    break;
                case "6":
                    $description = trans("$string_file.amount_debited_by_admin");
                    break;
                default:
                    $description = $no_description;
            }
            break;
    }
    return $description;
}

//$key = "AIzaSyC7lIIgBajzx409vxmmY_CJPcRvDb114w4";
//$booking_id = 440;
//$latitude = "-1.352772";
//$longitude = "36.7562829";
//$co = App\Models\BookingCoordinate::where('booking_id',440)->first();
////        p($co);
//$booking_coordinates = $co->coordinates;
////        p($booking_coordinates);
//$result = update_booking_map_image_at_ride_end($booking_id, $latitude, $longitude, $booking_coordinates, $key);
//p($result);

//function update_booking_map_image_at_ride_end($booking_id, $latitude, $longitude, $booking_coordinates, $key)
//{
//    if (!empty($booking_coordinates)) {
//        $drop_location_lat_long = json_decode($booking_coordinates, true);
//    }
//    $start = $latitude . ',' . $longitude;
//    if (count($drop_location_lat_long) > 1) {
//        $end = array_pop($drop_location_lat_long);
//        $finish = $end['latitude'] . ',' . $end['longitude'];
////        $count_waypoints = count($drop_location_lat_long);
//        $googleServices = new GoogleController();// CHECK FOR MULTIPLE WAYPOINTS or SINGLE WAYPOINT
//        $snapToRoad = $googleServices->SnapToRoad($drop_location_lat_long, $key);
////        $start = array_shift($snapToRoad);
////        $finish = array_pop($snapToRoad);
////        $finish = $finish ? $finish : $start;
//        $count_snapToRoad = count($snapToRoad);
//        if ($count_snapToRoad > 23) {
//            $average_way = ceil($count_snapToRoad / 22);
//            $new_array1 = array();
//            for ($j = 0; $j < $count_snapToRoad; $j = $j + $average_way) {
//                $lat_long = $snapToRoad[$j];
//                $new_array1[] = $lat_long;
//            }
////            p($new_array1);
//            $waypoints = implode("|", $new_array1);
//        } else {
//            $waypoints = implode("|", $snapToRoad);
//        }
//        //$multiple_waypoints = $googleServices->WayPointDistance($snapToRoad, $key);
////        p($waypoints);
////      p($multiple_waypoints);
////        $multiple_waypoints = array();
////        for ($j = 0; $j < $count_waypoints; $j++) {
////            $lat_long = $drop_location_lat_long[$j]['latitude'] . ',' . $drop_location_lat_long[$j]['longitude'];
////            $multiple_waypoints[] = $lat_long;
////        }
////        $waypoints = implode("|", $multiple_waypoints);
////        p($waypoints);
//        $data = GoogleController::GoogleStaticMultiplePointsImage($start, $finish, $waypoints, $key, "metric");
////        p($data);
//        $image = $data['image'];
//        p($image);
//        if (!empty($image)) {
//            $booking = Booking::Find($booking_id);
//            $booking->map_image = $image;
//            $booking->save();
//            return $image;
//        }
//    }
//}

function get_free_paid($string_file = "")
{
    return [
        "" => trans("$string_file.select"),
        "1" => trans("$string_file.free"),
        "2" => trans("$string_file.paid")
    ];
}

function get_optional_mandatory($string_file = "")
{
    return [
        "" => trans("$string_file.select"),
        "1" => trans("$string_file.optional"),
        "2" => trans("$string_file.mandatory")
    ];
}

function product_inventory_status($string_file = "")
{
    return array(
        '0' => trans("$string_file.not_added"),
        '1' => trans("$string_file.added"),
        '2' => trans("$string_file.partial_added"),
    );
}

function inventory_status($string_file = "")
{
    return array(
        '1' => trans("$string_file.yes"),
        '2' => trans("$string_file.no"),
    );
}

function driver_document_status($string_file = "")
{
    return array(
        '0' => trans("$string_file.pending"),//'PENDING',
        '1' => trans("$string_file.uploaded"),//'UPLOADED',
        '2' => trans("$string_file.approved"),//'APPROVED',
        '3' => trans("$string_file.rejected"),//'REJECTED',
        '4' => trans("$string_file.expired"),//'EXPIRED',
    );
}

function request_receiver($string_file)
{
    return [
        '1' => trans("$string_file.admin"),
        '2' => trans("$string_file.driver"),
    ];
}

function arr_driver_search_status($string_file)
{
    return array(
        "" => trans("$string_file.all"),
        "active" => trans("$string_file.active"),
        "busy" => trans("$string_file.busy"),
        "free" => trans("$string_file.free"),
        "inactive" => trans("$string_file.inactive"),
        "login" => trans("$string_file.login"),
        "logout" => trans("$string_file.logout"),
        "offline" => trans("$string_file.offline"),
        "online" => trans("$string_file.online")
    );
}

if (!function_exists('mobileNumber')) {
    function mobileNumber($swissNumberStr)
    {

        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $swissNumberProto = $phoneUtil->parse($swissNumberStr, "SA");
            return $swissNumberProto;
        } catch (\libphonenumber\NumberParseException $e) {
            return $e->getMessage();
        }
    }
}

function translateLocalContent($data, $locale = 'en', $source = 'en')
{
//    if($locale != $source){
//        return GoogleTranslate::trans($data, $locale, 'en');
//    }else{
    return $data;
//    }
}

function convertTimeToUTCzone($str, $userTimezone, $format = 'Y-m-d H:i:s')
{

    $new_str = new DateTime($str, new DateTimeZone($userTimezone));
    $new_str->setTimeZone(new DateTimeZone('UTC'));
    return $new_str->format($format);
}

//this function converts string from UTC time zone to current user timezone
/**
 * @param int $return_type 1 for date and time, 2 for date only, 3 for time only
 */
function convertTimeToUSERzone($str, $userTimezone, $merchant_id = NULL, $merchant = [], $return_type = 1, $format_type_check = null)
{
    if (empty($userTimezone)) {
        $userTimezone = 'UTC';
    }
    if (empty($str)) {
        return '--';
    }
    $format_type = 1;
    if (!empty($merchant)) {
        $format_type = $merchant->datetime_format;
    } elseif (!empty($merchant_id)) {
        $merchant = Merchant::select('datetime_format')->Find($merchant_id);
        $format_type = $merchant->datetime_format;
    }
    if ($format_type_check == 1) {
        $format_type = 100; // for timestamp to convert date time only with format
    }
    $format = getDateTimeFormat($format_type, $return_type);
    $new_str = new DateTime($str, new DateTimeZone('UTC'));
    $new_str->setTimeZone(new DateTimeZone($userTimezone));
    return $new_str->format($format);
}

function getDateTimeFormat($format_type, $return_type = 1)
{
    $format = "Y-m-d H:i:s";
    switch ($format_type) {
        case 1:
            if ($return_type == 2) {
                $format = "Y-m-d";
            } elseif ($return_type == 3) {
                $format = "H:i:s";
            } else {
                $format = "Y-m-d H:i:s";
            }
            break;
        case 2:
            if ($return_type == 2) {
                $format = "D, F d";
            } elseif ($return_type == 3) {
                $format = "H:i:s";
            } else {
                $format = "D, F d, Y h:i A";
            }
            break;
        case 3:  // // "12:49 pm Apr 16, 2020",
            if ($return_type == 2) {
                $format = "M d, Y ";
            } elseif ($return_type == 3) {
                $format = "h:i A";
            } else {
                $format = "H:i A <\b\\r> M d, Y";
            }
            break;
        default:
            if ($return_type == 2) {
                $format = "Y-m-d";
            } elseif ($return_type == 3) {
                $format = "H:i:s";
            } else {
                $format = "Y-m-d H:i:s";
            }
            break;
    }
    return $format;
}

function getReferralSystemOfferCondition($string_file)
{
    return array(
        1 => trans("$string_file.limited"),
        2 => trans("$string_file.unlimited"),
        3 => trans("$string_file.signup") . " Only",
        4 => "Conditional (No of Driver register with no of rides)"
    );
}

function getReferralSystemDriverCondition($string_file)
{
    return array(
        1 => trans("$string_file.after")." ".trans("$string_file.basic")." ".trans("$string_file.signup"),
        2 => trans("$string_file.after")." ".trans("$string_file.complete")." ".trans("$string_file.signup"),
        3 => trans("$string_file.after")." ".trans("$string_file.ride"),
    );
}

function setLocal($locale = null){
    $default_locale = "en";
    $req_locale = request()->header("locale");
    if (!empty($locale)) {
        $set_locale = $locale;
    }elseif(!empty($req_locale)){
        $set_locale = $req_locale;
    }else{
        $set_locale = $default_locale;
    }
    App::SetLocale($set_locale);
}

function custom_number_format($amount, $trip_calculation_method = NULL, $merchant_id = NULL)
{

    switch ($trip_calculation_method) {
        case "1":
            $amount = round($amount);
            break;
        case "2":
            $amount = sprintf("%.2f", $amount);
            break;
        case "3":
            $amount = number_format(round($amount), 2, ".", '');
            break;
        case "4":
            $amount = sprintf('%.3f', $amount);
            break;
        default:
            $amount = sprintf("%.2f", $amount);
    }
    return $amount;
}

function get_merchant_required_additional_information_on_signup($merchant_id, $merchant_obj = NULL)
{
    $result = [
        "required" => false,
        "requirement" => "",
        "step_name" => "No Name"
    ];
    if(!empty($merchant_id)){
        $configuration = \App\Models\Configuration::where("merchant_id", $merchant_id)->first();
    }else{
        $configuration = \App\Models\Configuration::where("merchant_id", $merchant_obj->id)->first();
    }

    if($configuration->stripe_connect_enable == 1){
        $result = [
            "required" => true,
            "requirement" => "STRIPE_CONNECT",
            "step_name" => "Stripe Registration",
            "step_description" => "Please register driver for Stripe",
            "step_verified_message" => "Stripe verification Done",
            "step_pending_message" => "Stripe verification is pending",
        ];
    }elseif($configuration->paystack_split_payment_enable == 1){
        $result = [
            "required" => true,
            "requirement" => "PAYSTACK_SPLIT",
            "step_name" => "Paystack Registration",
            "step_description" => "Please register driver for Paystack",
            "step_verified_message" => "Paystack verification Done",
            "step_pending_message" => "Paystack verification is pending",
        ];
    }
    return $result;
}
function curl_post_request($url , $body , $headers = [] , $body_type = 'json') {

    switch ($body_type) {
        case 'urlencoded' :
            $params = http_build_query($body);
            break;
        case 'stream' :
            $params = $body;
            break;
        default :
            $params = json_encode($body);
            break;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    $result = curl_exec($ch);
    $error = curl_error($ch);
    $status = curl_getinfo($ch , CURLINFO_HTTP_CODE);
    curl_close($ch);
    return (object)['status' => $status , 'data' => $result];
}
