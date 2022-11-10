<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\CommonController;
use App\Models\AdvertisementBanner;
use App\Models\Booking;
use App\Models\BookingConfiguration;
use App\Models\CountryArea;
use App\Models\Driver;
use App\Models\DriverSubscriptionRecord;
use App\Models\DriverVehicle;
use App\Models\GeofenceAreaQueue;
use App\Models\PriceCard;
use App\Models\VehicleType;
use App\Models\DriverRideConfig;
use App\Models\PaymentConfiguration;

use App\Models\ServiceType;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Resources\Json\JsonResource;

class MainScreenResource extends JsonResource
{
    public function toArray($data)
    {
        $subscription_package_not_bought = false;
        $subscription_package_expire = false;
        $additional_bank_details = false;
        $driver_details = $this;
        $config = BookingConfiguration::select('normal_ride_later_request_type', 'normal_ride_later_radius')->where([['merchant_id', '=', $this->merchant_id]])->first();
        if ($this->Merchant->Configuration->subscription_package == 1 && $this->subscription_wise_commission == 1):
            $active_package = DriverSubscriptionRecord::select('end_date_time','package_total_trips','id','used_trips')->where([['driver_id',$this->id],['status',2]])->orderBy('id','DESC')->first();
//            date_default_timezone_set($this->CountryArea->timezone);
            if (empty($active_package->id)):
                $subscription_package_not_bought = true;
            else:
                if (($active_package->package_total_trips <= $active_package->used_trips) || (strtotime("now") > strtotime($active_package->end_date_time))):
                    $subscription_package_expire = true;
                endif;
            endif;
        endif;

        $additional_bank_data_title = '';
        $additional_bank_data_placeholder = '';
        if($this->CountryArea->Country->additional_details == 1):
            $additional_bank_details = true;
            $additional_bank_data_title = $this->CountryArea->Country->AdditionalTitle;
            $additional_bank_data_placeholder = $this->CountryArea->Country->AdditionalPlaceholder;
        endif;

        $genderSelect = false;
        if ($this->Merchant->ApplicationConfiguration->gender == 1 && !in_array($this->driver_gender, ['1', '2', '3'])) {
            $genderSelect = true;
        }

        $currency = $this->CountryArea->Country->isoCode;
        $auto_upgradetion = $this->CountryArea->auto_upgradetion;
        $servives = array_pluck($this->CountryArea->ServiceTypes, 'id');
        $driver_id = $this->id;
        $term_status = $this->term_status;
        $allServices = [];
        $driver_vehicle = [];
        $wallet_balance_check = false;
        $wallet_money = 0;
        if($this->taxi_company_id != ''){
            $wallet_money = (int)get_taxicompany_wallet($this->taxi_company_id);
        }else{
            $wallet_money = $this->wallet_money == null ? 0 : $this->wallet_money;
        }
        $driver_vehicle_id = NULL;
        $walletCheck = '';
        if(!empty($this->vehicleId)){
            $driver_vehicle = DriverVehicle::whereHas('Drivers',function($q) use ($driver_id) {
                $q->where([['driver_id', '=', $driver_id]]);
                })->with('VehicleType')->where('id',$this->vehicleId)->first();
            if(!empty($driver_vehicle)){
                $driver_vehicle_id = $driver_vehicle->id;
                // overwriting images for s3 url in api
                $driver_vehicle->fullName = $this->fullName;
                $driver_vehicle->image = get_image($this->profile_image,'driver',$this->Merchant->id);
                $driver_vehicle->vehicle_type = $driver_vehicle->VehicleType->VehicleTypeName;
                $driver_vehicle->vehicleTypeImage = get_image($driver_vehicle->VehicleType->vehicleTypeImage,'vehicle',$this->Merchant->id);
                $driver_vehicle->vehicle_image = get_image($driver_vehicle->vehicle_image,'vehicle_document',$this->Merchant->id);
                $driver_vehicle->vehicle_number_plate_image = get_image($driver_vehicle->vehicle_number_plate_image,'vehicle_document',$this->Merchant->id);

                $vehicleServices = $this->CountryArea->VehicleType->where('id', $driver_vehicle->vehicle_type_id);

                $services = ServiceType::get();
                foreach($services as $service){
                    foreach ($vehicleServices as $value) {
                        if($service->id == $value->pivot->service_type_id){
                            $serviceId = $value->pivot->service_type_id;
                            $allServices[] = [
                                'service_id' => $serviceId,
                                'serviceName' => $service->ServiceName($data->merchant_id)
                            ];
                        }
                    }
                }
                $walletCheck = PriceCard::where([['country_area_id',$this->country_area_id],['vehicle_type_id',$driver_vehicle->vehicle_type_id]])->latest()->first();
            }

            // p($this);
            // $this->wallet_money = $this->wallet_money == null ? 0 : $this->wallet_money;

            if (!empty($walletCheck)){
                $wallet_balance_check = $walletCheck->PriceCardCommission->commission_type == 2 ? false : ($wallet_money < $this->CountryArea->minimum_wallet_amount ? true : false);
            }
        }
        $driver_vehicle = empty($driver_vehicle) ? (object)[] : $driver_vehicle;

        $bookings = Booking::select('id', 'user_id', 'booking_status', 'driver_id')->with(['User' => function ($query) {
            $query->select('id', 'UserPhone', 'email', 'UserProfileImage');
        }])->whereIn('booking_status', array(1002, 1003, 1004, 1005))->where([['driver_id', '=', $driver_id], ['booking_closure', '=', null]])->get();
        if (!empty($bookings->toArray())) {
            $active_rides = array();
            foreach ($bookings as $key => $values) {
                $id = $values->id;
                $booking_status = $values['booking_status'];
                switch ($booking_status) {
                    case "1002":
                        $ride_status_text = trans('api.message59');
                        $ride_status_color = "9b59b6";
                        break;
                    case "1003":
                        $ride_status_text = trans('api.message60');
                        $ride_status_color = "f39c12";
                        break;
                    case "1004":
                        $ride_status_text = trans('api.message61');
                        $ride_status_color = "2ecc71";
                        break;
                    case "1005":
                        $ride_status_text = trans('api.message115');
                        $ride_status_color = "2ecc71";
                        break;
                }
                $user = [];
                $user['booking_id'] = (string)$id;
                $user['booking_status'] = $booking_status;
                $user['id'] = $values->user_id;
                $user['UserPhone'] = $values->User->UserPhone;
                $user['UserName'] = $values->User->UserName;
                $user['email'] = $values->User->email;
                $user['UserProfileImage'] = get_image($values->User->UserProfileImage,'user',$this->Merchant->id);
                $user['ride_status_text'] = $ride_status_text;
                $user['ride_status_color'] = $ride_status_color;
                $active_rides[] = $user;
            }
        } else {
            $active_rides = [];
        }
        $date = new \DateTime;
        $date->modify('+15 minutes');
        $formatted_date = $date->format('H:i');
        $scheduled_rides = Booking::select('id', 'pickup_location', 'drop_location')->where([['driver_id', '=', $this->id], ['booking_status', '=', 1012]])->whereDate('later_booking_date', date('Y-m-d'))->where('later_booking_time', '<=', $formatted_date)->latest()->get();

        $doc_vehicle_id = $driver_vehicle_id;
        if(empty($driver_vehicle_id))
        {
            $verified_vehicle = get_verified_vehicle($driver_id);
            $doc_vehicle_id = !empty($verified_vehicle->id) ? $verified_vehicle->id : NULL;
        }

//        $personal_document_expired = get_driver_document_details($driver_id,'status','personal',4, $doc_vehicle_id);
//        $vehicle_document_expired = get_driver_document_details($driver_id,'status','vehicle',4,$doc_vehicle_id);

        $comType = array_pluck(array_pluck($this->CountryArea->PriceCard, 'PriceCardCommission'), 'commission_type');
        $wallet_balance_check = (in_array(1, $comType) && $wallet_money < $this->CountryArea->minimum_wallet_amount) ? true : false;
        // in case of multiple and existing deactivate vehicle when wallet balance become low
        if($wallet_balance_check == true && !empty($driver_vehicle_id))
        {
            $status = get_driver_multi_existing_vehicle_status($driver_id);
            if($status == 2)
            {
                DriverVehicle::where('id',$driver_vehicle_id)->update(array('vehicle_active_status'=>$status));
            }
            if(isset($this->vehicleId))
            {
                unset($this->vehicleId);
            }
            // when balance go low at app side make driver offline and at backend we do this in this api
            $driver_details->online_offline = 2;
            $driver_details->save();
            $this->vehicleId = $driver_vehicle_id;
        }
        $admin_message = [
            ['headline' => trans('api.gender'), 'message' => trans('api.genderNeed'), 'action' => 'GENDER_UPDATE', 'show' => $genderSelect],
            ['headline' => trans('api.subscription_package_not_bought'), 'message' => trans('api.subscription_package_not_bought'), 'action' => 'SUBSCRIPTION_BUY', 'show' => $subscription_package_not_bought],
            ['headline' => trans('api.subscription_package_expire'), 'message' => trans('api.subscription_package_expire'), 'action' => 'SUBSCRIPTION_EXPIRE', 'show' => $subscription_package_expire],
//            ['headline' => trans('api.message94'), 'message' =>  $personal_document_expired == true ? trans('api.personal_document_expired') : trans('api.document_review'), 'action' => 'PERSONAL_DOCUMENT_EXPIRE', 'show' => $personal_document_expired],
//            ['headline' => trans('api.vehicle_document'), 'message' => $vehicle_document_expired == true ? trans('api.vehicle_document_expired') : trans('api.document_review'), 'action' => 'VEHICLE_DOCUMENT_EXPIRE', 'show' => $vehicle_document_expired],
            ['headline' => trans('api.wallet_balance'), 'message' => $wallet_balance_check == true ? trans('api.message35') : trans('api.wallet_balance'), 'action' => 'WALLET_BALANCE', 'show' => $wallet_balance_check],
        ];

        // check status of all document of driver
        $final_document_status = driver_all_document_status($driver_id,$doc_vehicle_id);
        $document_message = [
            ['headline' => trans('api.document_status'), 'message' => $final_document_status == true ? trans('api.document_status_mainscreen') : trans('api.document_status'), 'action' => 'DOCUMENT_STATUS', 'show' => $final_document_status],
        ];
        $date = date("Y-m-d");
        $todayrides = Booking::where([['driver_id', '=', $driver_id], ['booking_status','=',1005], ['booking_closure', '=', 1]])->whereDate('created_at', '=', $date)->get();
        if (!empty($todayrides->toArray())) {
            $todayride = count($todayrides);
            $todayamount = sprintf("%0.2f", array_sum(array_pluck($todayrides, 'driver_cut')));
            $todayamount = (string)$todayamount;
        } else {
            $todayride = "0";
            $todayamount = "0.00";
        }
        $todays = array(
            "todays_rides" => $todayride,
            "todays_rides_color" => "bbbbbb",
            "todays_earning" => $currency . " " . $todayamount,
            "todays_earning_color" => "2ecc71"
        );
        $auto_upgradetion_status = "2";
        $driverRideConfig = DriverRideConfig::where([['driver_id', '=', $driver_id]])->first();
        if (!empty($driverRideConfig)) {
            $auto_upgradetion_status = (string)$driverRideConfig->auto_upgradetion;
        }
        $pool_enable = false;
        if (in_array(5, $servives) && !empty($this->vehicleId) && !empty($driver_vehicle) && $driver_vehicle->VehicleType->pool_enable == 1) {
            $pool_enable = true;
        }
        $ride_config = array(
            'auto_upgradetion' =>(int) $auto_upgradetion,
            'auto_upgradetion_status' => $auto_upgradetion_status,
            'pool_enable' => $pool_enable,
            'pool_enable_status' =>(int) $this->pool_ride_active,
        );

        $refer_enable = true;
        $payment_config = PaymentConfiguration::where('merchant_id' , $data->merchant_id)->first();
        if ($payment_config && $payment_config->fare_table_based_refer == 1) {
            if ($payment_config->fare_table_refer_type == 1) {
                $completed_trips_count = Booking::where('driver_id' , $data->id)->where('booking_status' , 1005)->count();
                if ($completed_trips_count < $payment_config->fare_table_refer_pass_value) {
                    $refer_enable = false;
                }
            }
            else {
                $week_timestamp = date('Y-m-d', strtotime('last week Monday')).' 00:00:00';
                $driver_weekly_fare_cf = Booking::where('payment_status' , 1)->where('driver_id' , $data->id)->where('updated_at' , $week_timestamp)->sum('driver_cut');
                if ($driver_weekly_fare_cf < $payment_config->fare_table_refer_pass_value) {
                    $refer_enable = false;
                }
            }
        }
        // $this means driver
        $driver_data = array(
            'driver_name'=>$driver_details->first_name.' '.$driver_details->last_name,
            'profile_image'=>get_image($driver_details->profile_image,'driver',$driver_details->merchant_id),
        );

        $service_type_id = array_pluck($allServices, 'service_id');
        $upcomming_bookings = null;
        if (isset($driver_vehicle->vehicle_type_id)){
            $driver_area_notification = isset($this->Merchant->Configuration->driver_area_notification) ? $this->Merchant->Configuration->driver_area_notification : 2;
            $bookings = Booking::UpcomingBookings($this->country_area_id, $this->current_latitude, $this->current_longitude, $driver_vehicle->vehicle_type_id, $service_type_id, $config->normal_ride_later_radius, $this->id, $driver_area_notification);
            if(!empty($bookings))
            {
            $upcomming_bookings = count($bookings);
            }
        }

        $geofence_queue = false;
        $geofence_queue_active = false;
        $geofence_queue_text = trans('api.not_in_geofence_queue_area');
        $geofence_queue_color_code = '#FF0000';
        if(isset($this->Merchant->Configuration->geofence_module) && $this->Merchant->Configuration->geofence_module == 1){
            if(isset($this->country_area_id) && $this->country_area_id != ''){
                $base_area_id = $this->country_area_id;
                $geofenceAreas = CountryArea::with('RestrictedArea')->whereHas('RestrictedArea',function($query) use($base_area_id){
                    $query->whereRaw(DB::raw("find_in_set($base_area_id,base_areas)"));
                })->get();
                if(!empty($geofenceAreas)){
                    $geofence_queue = true;
                    $commonController = new CommonController();
                    $checkGeofenceArea = $commonController->findGeofenceArea($this->current_latitude, $this->current_longitude,$base_area_id,$this->merchant_id);
                    if(!empty($checkGeofenceArea) && isset($checkGeofenceArea->RestrictedArea->queue_system) && $checkGeofenceArea->RestrictedArea->queue_system == 1){
                        $driverQueue = GeofenceAreaQueue::where(function($query) use($base_area_id,$checkGeofenceArea){
                            $query->where([
                                ['merchant_id','=',$this->merchant_id],
                                ['country_area_id','=',$base_area_id],
                                ['geofence_area_id','=',$checkGeofenceArea['id']],
                                ['driver_id','=',$this->id],
                                ['queue_status','=','1'] // Check if already in queue
                            ]);
                        })->whereDate('created_at',date('Y-m-d'))->first();
                        if(empty($driverQueue)){
                            $geofence_queue_active = false;
                            $geofence_queue_text = $checkGeofenceArea->LanguageSingle->AreaName.' Queue Off';
                            $geofence_queue_color_code = '#FF0000';
                        }else{
                            $geofence_queue_active = true;
                            $geofence_queue_text = $checkGeofenceArea->LanguageSingle->AreaName.' Queue On';
                            $geofence_queue_color_code = '#008000';
                        }
                    }
                }
            }
        }

        return [
            'currency' => $currency,
            'ride_config' => $ride_config,
            'free_busy' => $this->free_busy,
            'home_address_activated' => $this->home_location_active,
            'active_vehicle' => $driver_vehicle,
            'active_rides' => $active_rides,
            'scheduled_rides' => $scheduled_rides,
            'admin_message' => $admin_message,
            'document_message' => $document_message,
            'todays' => $todays,
            'term_status' => $term_status,
            'available_services' => $allServices,
            'additional_bank_data' => $additional_bank_details,
            'additional_bank_data_title' => $additional_bank_data_title,
            'additional_bank_data_placeholder' => $additional_bank_data_placeholder,
            'refer_enable' => $refer_enable,
            'is_suspended' => ($this->is_suspended) ? $this->is_suspended : '',
            'driver_data' => $driver_data,
            'wallet_money' => $this->wallet_money,
            'upcoming_bookings' => "$upcomming_bookings",
            'geofence_queue_enable' => $geofence_queue,
            'geofence_queue_active' => $geofence_queue_active,
            'geofence_queue_text' => $geofence_queue_text,
            'geofence_queue_color' => $geofence_queue_color_code,
            'online_config_status' => $this->getDriverOnlineConfig($this,'status')
        ];
    }

    public function with($data)
    {
        return [
            'result' => "1",
            'message' => trans('api.message17'),
        ];
    }
}