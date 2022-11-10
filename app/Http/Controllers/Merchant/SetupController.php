<?php

namespace App\Http\Controllers\Merchant;

use App\Models\AppNavigationDrawer;
use App\Models\Driver;
use App\Models\DriverAccount;
use App\Models\DriverActivePack;
use App\Models\DriverAddress;
use App\Models\DriverCard;
use App\Models\DriverDocument;
use App\Models\DriverRideConfig;
use App\Models\DriverVehicle;
use App\Models\DriverVehicleDocument;
use App\Models\Merchant;
use App\Models\MerchantNavDrawer;
use App\Models\PromotionNotification;
use App\Models\User;
use App\Models\UserDocument;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use League\Flysystem\MountManager;

class SetupController extends Controller
{
    public function index(){
        p('End');
        ini_set('max_execution_time', '90000');

        $existing_merchant_id = 136;
        $new_merchant_id = 21;

        $data['merchant_id'][136] = 21;

        $data['country_area_id'][310] = 25;
        $data['country_area_id'][321] = 29;

        $data['document_id'][358] = 49;
        $data['document_id'][359] = 48;
        $data['document_id'][360] = 47;
        $data['document_id'][361] = 46;
        $data['document_id'][362] = 45;
        $data['document_id'][420] = 44;

        $data['vehicle_type_id']['341'] = 41;
        $data['vehicle_make_id']['458'] = 43;
        $data['vehicle_model_id']['1118'] = 36;

        $merchant = Merchant::find($existing_merchant_id);
        if (!empty($merchant)) {
            echo "<pre>";
            echo "Merchant : - ".$merchant->BusinessName;
            $merchant_drivers = $merchant->Driver;
            if($merchant_drivers->count() > 0) {
                echo "<br> No. Merchant Driver - " . $merchant_drivers->count() . '<br>';
                DB::beginTransaction();
                try{
                    foreach($merchant_drivers as $driver){
                        $driver_new = DB::connection('mysql2')->table('drivers')->insertGetId([
                            'merchant_driver_id' => $driver->merchant_driver_id,
                            'merchant_id' => $data['merchant_id'][$driver->merchant_id],
                            'last_bill_generated' => $driver->last_bill_generated,
                            'subscription_wise_commission' => $driver->subscription_wise_commission,
                            'taxi_company_id' => $driver->taxi_company_id,
                            'unique_number' => $driver->unique_number,
                            'driver_gender' => $driver->driver_gender,
                            'first_name' => $driver->first_name,
                            'last_name' => $driver->last_name,
                            'email' => $driver->email,
                            'password' => $driver->password,
                            'driver_address' => $driver->driver_address,
                            'home_location_active' => $driver->home_location_active,
                            'pool_ride_active' => $driver->pool_ride_active,
                            'status_for_pool' => $driver->status_for_pool,
                            'avail_seats' => $driver->status_for_pool,
                            'occupied_seats' => $driver->occupied_seats,
                            'pick_exceed' => $driver->pick_exceed,
                            'pool_user_id' => $driver->pool_user_id,
                            'phoneNumber' => $driver->phoneNumber,
                            //  'profile_image' => $this->uploadImage('image', 'driver'),
                            'wallet_money' => $driver->wallet_money,
                            'total_trips' => 0,
                            'total_earnings' => 0,
                            'total_comany_earning' => 0,
                            'outstand_amount' => 0,
                            'current_latitude' => $driver->current_latitude,
                            'current_longitude' => $driver->current_longitude,
                            'last_location_update_time' => $driver->last_location_update_time,
                            'bearing' => $driver->bearing,
                            'accuracy' => $driver->accuracy,
                            'player_id' => NULL,
                            'rating' => $driver->rating,
                            'country_area_id' => $data['country_area_id'][$driver->country_area_id],
                            'login_logout' => 2,
                            'online_offline' => 2,
                            'free_busy' => 2,
                            'bank_name' => $driver->bank_name,
                            'account_holder_name' => $driver->account_holder_name,
                            'account_number' => $driver->account_number,
                            'account_type_id' => $driver->account_type_id,
                            'driver_verify_status' => $driver->driver_verify_status,
                            'signupFrom' => $driver->signupFrom,
                            'signupStep' => $driver->signupStep,
                            'driver_verification_date' => $driver->driver_verification_date,
                            'driver_admin_status' => $driver->driver_admin_status,
                            'access_token_id' => NULL,
                            'driver_delete' => $driver->driver_delete,
                            'online_code' => $driver->online_code,
                            'last_ride_request_timestamp' => $driver->last_ride_request_timestamp,
                            'created_at' => $driver->created_at,
                            'updated_at' => $driver->updated_at,
                            'driver_referralcode' => $driver->driver_referralcode,
                            'driver_block_status' => $driver->driver_block_status,
                            'term_status' => $driver->term_status,
                            'pending_document_status' =>$driver->pending_document_status,
                            'expire_personal_document' => $driver->expire_personal_document,
                            'expire_vehicle_document' => $driver->expire_vehicle_document,
                            'admin_msg' => $driver->admin_msg,
                            'fullName' => $driver->fullName,
                            'dob' => $driver->dob,
                            'reject_driver' => $driver->reject_driver,
                            'driver_cpf_number' => $driver->driver_cpf_number,
                            'total_expire_document' => $driver->total_expire_document,
                            'agency_number' => $driver->agency_number,
                            'driver_additional_data' => $driver->driver_additional_data,
                            // 'reward_points' => $driver->reward_points,
                            // 'usable_reward_points' => $driver->usable_reward_points,
                            // 'use_reward_trip_count' => $driver->use_reward_trip_count,
                            // 'is_suspended' => $driver->is_suspended,
                            // 'network_code' => $driver->network_code,
                            // 'referred_by' => $driver->referred_by
                        ]);

                        // Driver Ride config
                        $driver_ride_config = $driver->DriverRideConfig;
                        if (!empty($driver_ride_config) && $driver_ride_config->count() > 0) {
                            DB::connection('mysql2')->table('driver_ride_configs')->insert([
                                'driver_id' => $driver_new,
                                'auto_upgradetion' => $driver_ride_config->auto_upgradetion,
                                'pool_enable' => $driver_ride_config->pool_enable,
                                'smoker_type' => $driver_ride_config->smoker_type,
                                'allow_other_smoker' => $driver_ride_config->allow_other_smoker,
                                'latitude' => $driver_ride_config->latitude,
                                'longitude' => $driver_ride_config->longitude,
                                'radius' => $driver_ride_config->radius,
                                'created_at' => $driver_ride_config->created_at,
                                'updated_at' => $driver_ride_config->updated_at,
                                'auto_accept_enable' => $driver_ride_config->auto_accept_enable
                            ]);
                            p('Driver ride configs inserted.', 0);
                        }else{
                            echo "<br>Driver Ride Configs Not found.<br>";
                        }

                        // Driver documents
                        $driver_documents = $driver->DriverDocument;
                        if (!empty($driver_documents) && $driver_documents->count() > 0) {
                            foreach ($driver_documents as $driver_document) {
                                DB::connection('mysql2')->table('driver_documents')->insert([
                                    'driver_id' => $driver_new,
                                    'document_id' => $data['document_id'][$driver_document->document_id],
                                    //  'document_file' => $this->uploadImage($image,'driver_document',NULL,'multiple'),
                                    'expire_date' => $driver_document->expire_date,
                                    'document_verification_status' => $driver_document->document_verification_status,
                                    'reject_reason_id' => $driver_document->reject_reason_id,
                                    'created_at' => $driver_document->created_at,
                                    'updated_at' => $driver_document->updated_at
                                ]);
                            }
                            p('Driver Documents inserted.', 0);
                        }else{
                            echo "<br>Driver Documents Not found.<br>";
                        }

                        // Driver accounts
                        $driver_accounts = $driver->DriverAccount;
                        if (!empty($driver_accounts) && $driver_accounts->count() > 0) {
                            foreach ($driver_accounts as $driver_account){
                                DB::connection('mysql2')->table('driver_accounts')->insert([
                                    'merchant_id' => $data['merchant_id'][$driver_account->merchant_id],
                                    'driver_id' => $driver_new,
                                    'from_date' => $driver_account->from_date,
                                    'to_date' => $driver_account->to_date,
                                    'amount' => $driver_account->amount,
                                    'trips_outstanding_sum' => $driver_account->trips_outstanding_sum,
                                    'cash_payment_received' => $driver_account->cash_payment_received,
                                    'referral_amount' => $driver_account->referral_amount,
                                    'cancellation_charges' => $driver_account->cancellation_charges,
                                    'tip_amount' => $driver_account->tip_amount,
                                    'toll_amount' => $driver_account->toll_amount,
                                    'company_commission' => $driver_account->company_commission,
                                    'fare_amount' => $driver_account->fare_amount,
                                    'create_by' => $data['merchant_id'][$driver_account->merchant_id],
                                    //'settle_by' => $driver_account->settle_by,
                                    'referance_number' => $driver_account->referance_number,
                                    'total_trips' => $driver_account->total_trips,
                                    'total_trips_till_now' => $driver_account->total_trips_till_now,
                                    'status' => $driver_account->status,
                                    'block_date' => $driver_account->block_date,
                                    'due_date' => $driver_account->due_date,
                                    'fee_after_grace_period' => $driver_account->fee_after_grace_period,
                                    'created_at' => $driver_account->created_at,
                                    'updated_at' => $driver_account->updated_at
                                ]);
                            }
                            p('Driver accounts created.', 0);
                        }else{
                            echo "<br>Driver Accounts Not found.<br>";
                        }

                        // Driver active pack
                        $driver_cur_active_pack = $driver->DriverCurrentActivePack;
                        if (!empty($driver_cur_active_pack)) {
                            DB::connection('mysql2')->table('driver_active_packs')->insert([
                                'driver_id' => $driver_new,
                                'payment_method_id' => $driver_cur_active_pack->payment_method_id,
                                'subscription_pack_id' => $driver_cur_active_pack->subscription_pack_id,
                                'package_total_trips' => $driver_cur_active_pack->package_total_trips,
                                'used_trips' => $driver_cur_active_pack->used_trips,
                                'start_date_time' =>$driver_cur_active_pack->start_date_time,
                                'end_date_time' => $driver_cur_active_pack->end_date_time,
                                'created_at' => $driver_cur_active_pack->created_at,
                                'updated_at' =>$driver_cur_active_pack->updated_at
                            ]);
                            p('Driver active pack inserted.', 0);
                        }else{
                            echo "<br>Driver No current active pack.<br>";
                        }

                        // Driver active address
                        $driver_active_address = $driver->ActiveAddress;
                        if (!empty($driver_active_address)) {
                            DB::connection('mysql2')->table('driver_addresses')->insert([
                                'driver_id' => $driver_new,
                                'address_name' => $driver_active_address->address_name,
                                'location' => $driver_active_address->location,
                                'latitude' => $driver_active_address->latitude,
                                'longitude' => $driver_active_address->longitude,
                                'address_status' => $driver_active_address->address_status,
                                'created_at' => $driver_active_address->created_at,
                                'updated_at' =>  $driver_active_address->updated_at
                            ]);

                            p('Driver addresses created.', 0);
                        }else {
                            echo "<br>Driver No active address.<br>";
                        }

                        // Driver cards
                        $driver_cards = $driver->DriverCard;
                        if (!empty($driver_cards) && $driver_cards->count() > 0) {
                            foreach ($driver_cards as $driver_card){
                                DB::connection('mysql2')->table('driver_cards')->insert([
                                    'driver_id' => $driver_new,
                                    'payment_method_id' => $driver_card->payment_method_id,
                                    'token' => $driver_card->token,
                                    'card_number' => $driver_card->card_number,
                                    'expiry_date' => $driver_card->expiry_date,
                                    'created_at' => $driver_card->created_at,
                                    'updated_at' => $driver_card->updated_at,
                                ]);
                            }
                            p('Driver cards inserted.', 0);
                        }else {
                            echo "<br>Driver No card found.<br>";
                        }

                        // Driver vehicles
                        $driver_vehicles = $driver->DriverVehicles;
                        if (!empty($driver_vehicles) && $driver_vehicles->count() > 0) {
                            foreach ($driver_vehicles as $driver_vehicle){

                                $driver_vehicle_id = DB::connection('mysql2')->table('driver_vehicles')->insertGetId([
                                    'merchant_id' => $data['merchant_id'][$driver_vehicle->merchant_id],
                                    'driver_id' => $driver_new,
                                    'owner_id' => $driver_new,
                                    'ownerType' => $driver_vehicle->ownerType,
                                    'vehicle_type_id' => $data['vehicle_type_id'][$driver_vehicle->vehicle_type_id],
                                    'shareCode' => $driver_vehicle->shareCode,
                                    'vehicle_make_id' => $data['vehicle_make_id'][$driver_vehicle->vehicle_make_id],
                                    'vehicle_model_id' => $data['vehicle_model_id'][$driver_vehicle->vehicle_model_id],
                                    'vehicle_number' => $driver_vehicle->vehicle_number,
                                    'vehicle_color' => $driver_vehicle->vehicle_color,
                                    // 'vehicle_image' =>
                                    //  'vehicle_number_plate_image' =>
                                    'vehicle_active_status' => $driver_vehicle->vehicle_active_status,
                                    'vehicle_verification_status' => $driver_vehicle->vehicle_verification_status,
                                    'reject_reason_id' => $driver_vehicle->reject_reason_id,
                                    'ac_nonac' => $driver_vehicle->ac_nonac,
                                    'baby_seat' => $driver_vehicle->baby_seat,
                                    'wheel_chair' => $driver_vehicle->wheel_chair,
                                    'vehicle_delete' => $driver_vehicle->vehicle_delete,
                                    'created_at' => $driver_vehicle->created_at,
                                    'updated_at' => $driver_vehicle->updated_at,
                                    'total_expire_document' => $driver_vehicle->total_expire_document,
                                    'vehicle_additional_data' => $driver_vehicle->vehicle_additional_data
                                ]);

                                // Pivot table entry for driver_vehicle_service_type
                                $driver_vehicle_service_types = $driver_vehicle->ServiceTypes;
                                if(!empty($driver_vehicle_service_types) && $driver_vehicle_service_types->count() > 0){
                                    foreach ($driver_vehicle_service_types as $driver_vehicle_service_type){
                                        DB::connection('mysql2')->table('driver_vehicle_service_type')->insert([
                                            'driver_vehicle_id' => $driver_vehicle_id,
                                            'service_type_id' => $driver_vehicle_service_type->pivot->service_type_id
                                        ]);
                                    }
                                }

                                //pivot table entry for driver_driver_vehicle
                                DB::connection('mysql2')->table('driver_driver_vehicle')->insert([
                                    'driver_id' => $driver_new,
                                    'driver_vehicle_id' => $driver_vehicle_id
                                ]);


                                $driver_vehicle_documents = $driver_vehicle->DriverVehicleDocument;
                                if(!empty($driver_vehicle_documents) && $driver_vehicles->count() > 0){
                                    foreach ($driver_vehicle_documents as $driver_vehicle_document){
                                        DB::connection('mysql2')->table('driver_vehicle_documents')->insert([
                                            'driver_vehicle_id' => $driver_vehicle_id,
                                            'document_id' => $data['document_id'][$driver_vehicle_document->document_id],
                                            // 'document' => '',
                                            'expire_date' => $driver_vehicle_document->expire_date,
                                            'document_verification_status' => $driver_vehicle_document->document_verification_status,
                                            'reject_reason_id' => $driver_vehicle_document->reject_reason_id,
                                            'created_at' => $driver_vehicle_document->created_at,
                                            'updated_at' => $driver_vehicle_document->updated_at
                                        ]);
                                    }
                                }
                            }
                            p('Driver vehicles created.', 0);
                        }else {
                            echo "<br>Driver Vehicles Not found.<br>";
                        }


                        p('Driver Inserted successfully', 0);
                    }

                }catch (\Exception $e){
                    DB::rollBack();
                    p($e->getMessage());
                }
                DB::commit();
            } else {
                echo "Merchant drivers not found";
            }

            $merchant_users = $merchant->User;
            if($merchant_users->count() > 0) {
                echo "<br> No. Merchant Users - " . $merchant_users->count() . '<br>';
                DB::beginTransaction();
                try{
                    foreach($merchant_users as $user){

                        $driver_new = DB::connection('mysql2')->table('drivers')->insertGetId([
                            'merchant_driver_id' => $driver->merchant_driver_id,
                            'merchant_id' => $data['merchant_id'][$driver->merchant_id],
                            'last_bill_generated' => $driver->last_bill_generated,
                            'subscription_wise_commission' => $driver->subscription_wise_commission,
                            'taxi_company_id' => $driver->taxi_company_id,
                            'unique_number' => $driver->unique_number,
                            'driver_gender' => $driver->driver_gender,
                            'first_name' => $driver->first_name,
                            'last_name' => $driver->last_name,
                            'email' => $driver->email,
                            'password' => $driver->password,
                            'driver_address' => $driver->driver_address,
                            'home_location_active' => $driver->home_location_active,
                            'pool_ride_active' => $driver->pool_ride_active,
                            'status_for_pool' => $driver->status_for_pool,
                            'avail_seats' => $driver->status_for_pool,
                            'occupied_seats' => $driver->occupied_seats,
                            'pick_exceed' => $driver->pick_exceed,
                            'pool_user_id' => $driver->pool_user_id,
                            'phoneNumber' => $driver->phoneNumber,
                            //  'profile_image' => $this->uploadImage('image', 'driver'),
                            'wallet_money' => $driver->wallet_money,
                            'total_trips' => 0,
                            'total_earnings' => 0,
                            'total_comany_earning' => 0,
                            'outstand_amount' => 0,
                            'current_latitude' => $driver->current_latitude,
                            'current_longitude' => $driver->current_longitude,
                            'last_location_update_time' => $driver->last_location_update_time,
                            'bearing' => $driver->bearing,
                            'accuracy' => $driver->accuracy,
                            'player_id' => NULL,
                            'rating' => $driver->rating,
                            'country_area_id' => $data['country_area_id'][$driver->country_area_id],
                            'login_logout' => 2,
                            'online_offline' => 2,
                            'free_busy' => 2,
                            'bank_name' => $driver->bank_name,
                            'account_holder_name' => $driver->account_holder_name,
                            'account_number' => $driver->account_number,
                            'account_type_id' => $driver->account_type_id,
                            'driver_verify_status' => $driver->driver_verify_status,
                            'signupFrom' => $driver->signupFrom,
                            'signupStep' => $driver->signupStep,
                            'driver_verification_date' => $driver->driver_verification_date,
                            'driver_admin_status' => $driver->driver_admin_status,
                            'access_token_id' => NULL,
                            'driver_delete' => $driver->driver_delete,
                            'online_code' => $driver->online_code,
                            'last_ride_request_timestamp' => $driver->last_ride_request_timestamp,
                            'created_at' => $driver->created_at,
                            'updated_at' => $driver->updated_at,
                            'driver_referralcode' => $driver->driver_referralcode,
                            'driver_block_status' => $driver->driver_block_status,
                            'term_status' => $driver->term_status,
                            'pending_document_status' =>$driver->pending_document_status,
                            'expire_personal_document' => $driver->expire_personal_document,
                            'expire_vehicle_document' => $driver->expire_vehicle_document,
                            'admin_msg' => $driver->admin_msg,
                            'fullName' => $driver->fullName,
                            'dob' => $driver->dob,
                            'reject_driver' => $driver->reject_driver,
                            'driver_cpf_number' => $driver->driver_cpf_number,
                            'total_expire_document' => $driver->total_expire_document,
                            'agency_number' => $driver->agency_number,
                            'driver_additional_data' => $driver->driver_additional_data,
                            // 'reward_points' => $driver->reward_points,
                            // 'usable_reward_points' => $driver->usable_reward_points,
                            // 'use_reward_trip_count' => $driver->use_reward_trip_count,
                            // 'is_suspended' => $driver->is_suspended,
                            // 'network_code' => $driver->network_code,
                            // 'referred_by' => $driver->referred_by
                        ]);

                        // Driver Ride config
                        $driver_ride_config = $driver->DriverRideConfig;
                        if (!empty($driver_ride_config) && $driver_ride_config->count() > 0) {
                            DB::connection('mysql2')->table('driver_ride_configs')->insert([
                                'driver_id' => $driver_new,
                                'auto_upgradetion' => $driver_ride_config->auto_upgradetion,
                                'pool_enable' => $driver_ride_config->pool_enable,
                                'smoker_type' => $driver_ride_config->smoker_type,
                                'allow_other_smoker' => $driver_ride_config->allow_other_smoker,
                                'latitude' => $driver_ride_config->latitude,
                                'longitude' => $driver_ride_config->longitude,
                                'radius' => $driver_ride_config->radius,
                                'created_at' => $driver_ride_config->created_at,
                                'updated_at' => $driver_ride_config->updated_at,
                                'auto_accept_enable' => $driver_ride_config->auto_accept_enable
                            ]);
                            p('Driver ride configs inserted.', 0);
                        }else{
                            echo "<br>Driver Ride Configs Not found.<br>";
                        }

                        // Driver documents
                        $driver_documents = $driver->DriverDocument;
                        if (!empty($driver_documents) && $driver_documents->count() > 0) {
                            foreach ($driver_documents as $driver_document) {
                                DB::connection('mysql2')->table('driver_documents')->insert([
                                    'driver_id' => $driver_new,
                                    'document_id' => $data['document_id'][$driver_document->document_id],
                                    //  'document_file' => $this->uploadImage($image,'driver_document',NULL,'multiple'),
                                    'expire_date' => $driver_document->expire_date,
                                    'document_verification_status' => $driver_document->document_verification_status,
                                    'reject_reason_id' => $driver_document->reject_reason_id,
                                    'created_at' => $driver_document->created_at,
                                    'updated_at' => $driver_document->updated_at
                                ]);
                            }
                            p('Driver Documents inserted.', 0);
                        }else{
                            echo "<br>Driver Documents Not found.<br>";
                        }

                        // Driver accounts
                        $driver_accounts = $driver->DriverAccount;
                        if (!empty($driver_accounts) && $driver_accounts->count() > 0) {
                            foreach ($driver_accounts as $driver_account){
                                DB::connection('mysql2')->table('driver_accounts')->insert([
                                    'merchant_id' => $data['merchant_id'][$driver_account->merchant_id],
                                    'driver_id' => $driver_new,
                                    'from_date' => $driver_account->from_date,
                                    'to_date' => $driver_account->to_date,
                                    'amount' => $driver_account->amount,
                                    'trips_outstanding_sum' => $driver_account->trips_outstanding_sum,
                                    'cash_payment_received' => $driver_account->cash_payment_received,
                                    'referral_amount' => $driver_account->referral_amount,
                                    'cancellation_charges' => $driver_account->cancellation_charges,
                                    'tip_amount' => $driver_account->tip_amount,
                                    'toll_amount' => $driver_account->toll_amount,
                                    'company_commission' => $driver_account->company_commission,
                                    'fare_amount' => $driver_account->fare_amount,
                                    'create_by' => $data['merchant_id'][$driver_account->merchant_id],
                                    //'settle_by' => $driver_account->settle_by,
                                    'referance_number' => $driver_account->referance_number,
                                    'total_trips' => $driver_account->total_trips,
                                    'total_trips_till_now' => $driver_account->total_trips_till_now,
                                    'status' => $driver_account->status,
                                    'block_date' => $driver_account->block_date,
                                    'due_date' => $driver_account->due_date,
                                    'fee_after_grace_period' => $driver_account->fee_after_grace_period,
                                    'created_at' => $driver_account->created_at,
                                    'updated_at' => $driver_account->updated_at
                                ]);
                            }
                            p('Driver accounts created.', 0);
                        }else{
                            echo "<br>Driver Accounts Not found.<br>";
                        }

                        // Driver active pack
                        $driver_cur_active_pack = $driver->DriverCurrentActivePack;
                        if (!empty($driver_cur_active_pack)) {
                            DB::connection('mysql2')->table('driver_active_packs')->insert([
                                'driver_id' => $driver_new,
                                'payment_method_id' => $driver_cur_active_pack->payment_method_id,
                                'subscription_pack_id' => $driver_cur_active_pack->subscription_pack_id,
                                'package_total_trips' => $driver_cur_active_pack->package_total_trips,
                                'used_trips' => $driver_cur_active_pack->used_trips,
                                'start_date_time' =>$driver_cur_active_pack->start_date_time,
                                'end_date_time' => $driver_cur_active_pack->end_date_time,
                                'created_at' => $driver_cur_active_pack->created_at,
                                'updated_at' =>$driver_cur_active_pack->updated_at
                            ]);
                            p('Driver active pack inserted.', 0);
                        }else{
                            echo "<br>Driver No current active pack.<br>";
                        }

                        // Driver active address
                        $driver_active_address = $driver->ActiveAddress;
                        if (!empty($driver_active_address)) {
                            DB::connection('mysql2')->table('driver_addresses')->insert([
                                'driver_id' => $driver_new,
                                'address_name' => $driver_active_address->address_name,
                                'location' => $driver_active_address->location,
                                'latitude' => $driver_active_address->latitude,
                                'longitude' => $driver_active_address->longitude,
                                'address_status' => $driver_active_address->address_status,
                                'created_at' => $driver_active_address->created_at,
                                'updated_at' =>  $driver_active_address->updated_at
                            ]);

                            p('Driver addresses created.', 0);
                        }else {
                            echo "<br>Driver No active address.<br>";
                        }

                        // Driver cards
                        $driver_cards = $driver->DriverCard;
                        if (!empty($driver_cards) && $driver_cards->count() > 0) {
                            foreach ($driver_cards as $driver_card){
                                DB::connection('mysql2')->table('driver_cards')->insert([
                                    'driver_id' => $driver_new,
                                    'payment_method_id' => $driver_card->payment_method_id,
                                    'token' => $driver_card->token,
                                    'card_number' => $driver_card->card_number,
                                    'expiry_date' => $driver_card->expiry_date,
                                    'created_at' => $driver_card->created_at,
                                    'updated_at' => $driver_card->updated_at,
                                ]);
                            }
                            p('Driver cards inserted.', 0);
                        }else {
                            echo "<br>Driver No card found.<br>";
                        }

                        // Driver vehicles
                        $driver_vehicles = $driver->DriverVehicles;
                        if (!empty($driver_vehicles) && $driver_vehicles->count() > 0) {
                            foreach ($driver_vehicles as $driver_vehicle){

                                $driver_vehicle_id = DB::connection('mysql2')->table('driver_vehicles')->insertGetId([
                                    'merchant_id' => $data['merchant_id'][$driver_vehicle->merchant_id],
                                    'driver_id' => $driver_new,
                                    'owner_id' => $driver_new,
                                    'ownerType' => $driver_vehicle->ownerType,
                                    'vehicle_type_id' => $data['vehicle_type_id'][$driver_vehicle->vehicle_type_id],
                                    'shareCode' => $driver_vehicle->shareCode,
                                    'vehicle_make_id' => $data['vehicle_make_id'][$driver_vehicle->vehicle_make_id],
                                    'vehicle_model_id' => $data['vehicle_model_id'][$driver_vehicle->vehicle_model_id],
                                    'vehicle_number' => $driver_vehicle->vehicle_number,
                                    'vehicle_color' => $driver_vehicle->vehicle_color,
                                    // 'vehicle_image' =>
                                    //  'vehicle_number_plate_image' =>
                                    'vehicle_active_status' => $driver_vehicle->vehicle_active_status,
                                    'vehicle_verification_status' => $driver_vehicle->vehicle_verification_status,
                                    'reject_reason_id' => $driver_vehicle->reject_reason_id,
                                    'ac_nonac' => $driver_vehicle->ac_nonac,
                                    'baby_seat' => $driver_vehicle->baby_seat,
                                    'wheel_chair' => $driver_vehicle->wheel_chair,
                                    'vehicle_delete' => $driver_vehicle->vehicle_delete,
                                    'created_at' => $driver_vehicle->created_at,
                                    'updated_at' => $driver_vehicle->updated_at,
                                    'total_expire_document' => $driver_vehicle->total_expire_document,
                                    'vehicle_additional_data' => $driver_vehicle->vehicle_additional_data
                                ]);

                                // Pivot table entry for driver_vehicle_service_type
                                $driver_vehicle_service_types = $driver_vehicle->ServiceTypes;
                                if(!empty($driver_vehicle_service_types) && $driver_vehicle_service_types->count() > 0){
                                    foreach ($driver_vehicle_service_types as $driver_vehicle_service_type){
                                        DB::connection('mysql2')->table('driver_vehicle_service_type')->insert([
                                            'driver_vehicle_id' => $driver_vehicle_id,
                                            'service_type_id' => $driver_vehicle_service_type->pivot->service_type_id
                                        ]);
                                    }
                                }

                                //pivot table entry for driver_driver_vehicle
                                DB::connection('mysql2')->table('driver_driver_vehicle')->insert([
                                    'driver_id' => $driver_new,
                                    'driver_vehicle_id' => $driver_vehicle_id
                                ]);


                                $driver_vehicle_documents = $driver_vehicle->DriverVehicleDocument;
                                if(!empty($driver_vehicle_documents) && $driver_vehicles->count() > 0){
                                    foreach ($driver_vehicle_documents as $driver_vehicle_document){
                                        DB::connection('mysql2')->table('driver_vehicle_documents')->insert([
                                            'driver_vehicle_id' => $driver_vehicle_id,
                                            'document_id' => $data['document_id'][$driver_vehicle_document->document_id],
                                            // 'document' => '',
                                            'expire_date' => $driver_vehicle_document->expire_date,
                                            'document_verification_status' => $driver_vehicle_document->document_verification_status,
                                            'reject_reason_id' => $driver_vehicle_document->reject_reason_id,
                                            'created_at' => $driver_vehicle_document->created_at,
                                            'updated_at' => $driver_vehicle_document->updated_at
                                        ]);
                                    }
                                }
                            }
                            p('Driver vehicles created.', 0);
                        }else {
                            echo "<br>Driver Vehicles Not found.<br>";
                        }


                        p('Driver Inserted successfully', 0);
                    }

                }catch (\Exception $e){
                    DB::rollBack();
                    p($e->getMessage());
                }
                DB::commit();
            } else {
                echo "Merchant users not found";
            }

        } else {
            echo "Merchant not found";
        }
    }

    public function uploadImagesToS3(){
        ini_set('max_execution_time', '90000');
        $merchants = Merchant::where(['id' => 52])->get();
//        p('end');
        if (!empty($merchants)) {
            foreach ($merchants as $merchant) {
                // Merchant
//                DB::beginTransaction();
//                try{
//                    echo "<pre>";
//                    echo "Merchant : - " . $merchant->BusinessName . ", ID - " . $merchant->id;
//                    $new_businessLogo = $this->uploadImage($merchant->BusinessLogo, $merchant->alias_name, 'business_logo');
//                    Merchant::where(['id' => $merchant->id])->update(['BusinessLogo' => $new_businessLogo]);
//                    echo "<br>merchant logo uploaded<br>";
//                } catch (\Exception $e) {
//                    DB::rollBack();
//                    p($e->getMessage());
//                }
//                DB::commit();

                // Driver
                $merchant_drivers = Driver::where(['merchant_id' => $merchant->id])->get();
                if ($merchant_drivers->count() > 0) {
                    echo "<br> No. Merchant Driver - " . $merchant_drivers->count() . '<br>';
                    foreach ($merchant_drivers as $driver) {
                        DB::beginTransaction();
                        try{
                            $new_driverProfile = $this->uploadImage($driver->profile_image, $merchant->alias_name, 'driver');
                            Driver::where(['id' => $driver->id, 'merchant_id' => $merchant->id])->update([
                                'profile_image' => $new_driverProfile,
                            ]);
                            echo "<br>Driver profile images uploaded<br>";

                            // Driver documents
                            $driver_documents = $driver->DriverDocument;
                            if (!empty($driver_documents) && $driver_documents->count() > 0) {
                                foreach ($driver_documents as $driver_document) {
                                    $new_driverDocument = $this->uploadImage($driver_document->document_file, $merchant->alias_name, 'driver_document');
                                    DriverDocument::where(['id' => $driver_document->id, 'driver_id' => $driver->id])->update(['document_file' => $new_driverDocument]);
                                }
                                echo "<br>Driver document images uploaded<br>";
                            } else {
                                echo "<br>Driver Documents Not found.<br>";
                            }

                            // Driver vehicles
                            $driver_vehicles = $driver->DriverVehicles;
                            if (!empty($driver_vehicles) && $driver_vehicles->count() > 0) {
                                foreach ($driver_vehicles as $driver_vehicle) {
                                    $new_driverVehicle_vehicle = $this->uploadImage($driver_vehicle->vehicle_image, $merchant->alias_name, 'vehicle_document');
                                    $new_driverVehicle_vehicleNumber = $this->uploadImage($driver_vehicle->vehicle_number_plate_image, $merchant->alias_name, 'vehicle_document');
                                    DriverVehicle::where(['id' => $driver_vehicle->id, 'driver_id' => $driver->id, 'merchant_id' => $merchant->id])->update([
                                        'vehicle_image' => $new_driverVehicle_vehicle,
                                        'vehicle_number_plate_image' => $new_driverVehicle_vehicleNumber,
                                    ]);

                                    $driver_vehicle_documents = $driver_vehicle->DriverVehicleDocument;
                                    if (!empty($driver_vehicle_documents) && $driver_vehicle_documents->count() > 0) {
                                        foreach ($driver_vehicle_documents as $driver_vehicle_document) {
                                            $new_driverVehicleDocument = $this->uploadImage($driver_vehicle_document->document, $merchant->alias_name, 'vehicle_document');
                                            DriverVehicleDocument::where(['id' => $driver_vehicle_document->id, 'driver_vehicle_id' => $driver_vehicle->id])->update([
                                                'document' => $new_driverVehicleDocument,
                                            ]);
                                        }
                                        echo "Driver vehicles images uploaded";
                                    } else {
                                        echo "Driver vehicles images not uploaded";
                                    }
                                }
                                echo "Driver vehicles images uploaded";
                            } else {
                                echo "Driver Vehicles Images Not found.";
                            }
                            echo "Driver All Documents successfully uploaded";
                        } catch (\Exception $e) {
                            DB::rollBack();
                            p($e->getMessage());
                        }
                        DB::commit();
                    }
                } else {
                     echo "Merchant drivers not found";
                }

                // Users
                $merchant_users = User::where(['merchant_id' => $merchant->id])->get();
                if($merchant_users->count() > 0) {
                    echo "<br> No. Merchant Users - " . $merchant_users->count() . '<br>';
                    foreach ($merchant_users as $user) {
                        DB::beginTransaction();
                        try{
                            $new_userProfile = $this->uploadImage($user->UserProfileImage, $merchant->alias_name, 'user');
                            User::where(['id' => $user->id, 'merchant_id' => $merchant->id])->update([
                                'UserProfileImage' => $new_userProfile,
                            ]);
                            echo "<br>User profile images uploaded<br>";
                        }catch (\Exception $e){
                            DB::rollBack();
                            p($e->getMessage());
                        }
                        DB::commit();
                    }
                } else {
                     echo "Merchant users not found";
                }

                // Navigation Drawer
                $merchant_navigation_drawers = MerchantNavDrawer::where(['merchant_id' => $merchant->id])->get();
                if($merchant_navigation_drawers->count() > 0){
                    foreach ($merchant_navigation_drawers as $merchant_navigation_drawer){
                        DB::beginTransaction();
                        try{
                            if($merchant_navigation_drawer->image != null){
                                $new_merchantNavDrawer = $this->uploadImage($merchant_navigation_drawer->image, $merchant->alias_name, 'drawericons');
                                MerchantNavDrawer::where(['id' => $merchant_navigation_drawer->id, 'merchant_id' => $merchant->id])->update([
                                    'image' => $new_merchantNavDrawer,
                                ]);
                            }
                        }catch (\Exception $e){
                            DB::rollBack();
                            p($e->getMessage());
                        }
                        DB::commit();
                    }
                    echo "<br>Merchant navigation drawer images uploaded<br>";
                }else{
                    echo "Merchant navigation drawer images not found";
                }

                // Promotions notifications
                $merchant_promotion_notifications = PromotionNotification::where(['merchant_id' => $merchant->id])->get();
                if($merchant_promotion_notifications->count() > 0){
                    foreach ($merchant_promotion_notifications as $merchant_promotion_notification){
                        DB::beginTransaction();
                        try{
                            if($merchant_promotion_notification->image != null){
                                $new_merchantPromotionNotification = $this->uploadImage($merchant_promotion_notification->image, $merchant->alias_name, 'promotions');
                                PromotionNotification::where(['id' => $merchant_promotion_notification->id, 'merchant_id' => $merchant->id])->update([
                                    'image' => $new_merchantPromotionNotification,
                                ]);
                            }
                        }catch (\Exception $e){
                            DB::rollBack();
                            p($e->getMessage());
                        }
                        DB::commit();
                    }
                    echo "<br>Merchant promotion notification images uploaded<br>";
                }else{
                    echo "Merchant promotion notification images not found";
                }

                // Vehicle types
                $merchant_vehicle_types = VehicleType::where(['merchant_id' => $merchant->id])->get();
                if ($merchant_vehicle_types->count() > 0) {
                    foreach ($merchant_vehicle_types as $merchant_vehicle_type) {
                        DB::beginTransaction();
                        try {
                            if ($merchant_vehicle_type->vehicleTypeImage != null) {
                                $new_vehicleTypeImage = $this->uploadImage($merchant_vehicle_type->vehicleTypeImage, $merchant->alias_name, 'vehicle');
                                VehicleType::where(['id' => $merchant_vehicle_type->id, 'merchant_id' => $merchant->id])->update([
                                    'vehicleTypeImage' => $new_vehicleTypeImage,
                                ]);
                            }
                            if ($merchant_vehicle_type->vehicleTypeDeselectImage != null) {
                                $new_choose_deselect_image = $this->uploadImage($merchant_vehicle_type->choose_deselect_image, $merchant->alias_name, 'vehicle');
                                VehicleType::where(['id' => $merchant_vehicle_type->id, 'merchant_id' => $merchant->id])->update([
                                    'vehicleTypeDeselectImage' => $new_choose_deselect_image,
                                ]);
                            }
                        } catch (\Exception $e) {
                            DB::rollBack();
                            p($e->getMessage());
                        }
                        DB::commit();

                    }
                    echo "<br>Merchant vehicle type images uploaded<br>";
                } else {
                    echo "Merchant vehicle type images not found";
                }

                // Vehicle Make
                $merchant_vehicle_makes = VehicleMake::where(['merchant_id' => $merchant->id])->get();
                if ($merchant_vehicle_makes->count() > 0) {
                    foreach ($merchant_vehicle_makes as $merchant_vehicle_make) {
                        DB::beginTransaction();
                        try {
                            if ($merchant_vehicle_make->vehicleMakeLogo != null) {
                                $new_vehicle_make_logo = $this->uploadImage($merchant_vehicle_make->vehicleMakeLogo, $merchant->alias_name, 'vehicle');
                                VehicleMake::where(['id' => $merchant_vehicle_make->id, 'merchant_id' => $merchant->id])->update([
                                    'vehicleMakeLogo' => $new_vehicle_make_logo,
                                ]);
                            }
                        } catch (\Exception $e) {
                            DB::rollBack();
                            p($e->getMessage());
                        }
                        DB::commit();
                    }
                    echo "<br>Merchant vehicle make images uploaded<br>";
                } else {
                    echo "Merchant vehicle make images not found";
                }
            }
        } else {
            echo "Merchant not found";
        }
    }

    public function uploadImage($image_name, $alias_name, $dir = 'images')
    {
        $folder = '';
        if(File::exists(public_path('1/'.$image_name))){
            $folder = '1/';
        }else if(File::exists(public_path('2/'.$image_name))){
            $folder = '2/';
        }else if(File::exists(public_path('3/'.$image_name))){
            $folder = '3/';
        }else if(File::exists(public_path('4/'.$image_name))){
            $folder = '4/';
        }else if(File::exists(public_path('5/'.$image_name))){
            $folder = '5/';
        }
        if($image_name != null && File::exists(public_path($folder.$image_name))){
            try{
                $temp_image_name = explode_image_path($image_name);

                $upload_path = \Config::get('custom.' . $dir);
                $alias = $alias_name. $upload_path['path'];
                $name = time() . "_" . uniqid() ."_". $dir.'.' . \File::extension($temp_image_name);
                $filePath = $alias . $name;

                $inputStream = \Illuminate\Support\Facades\Storage::disk('public')->getDriver()->readStream($folder.$image_name);
                $destination = \Illuminate\Support\Facades\Storage::disk('s3')->getDriver()->getAdapter()->getPathPrefix().$filePath;
                \Illuminate\Support\Facades\Storage::disk('s3')->getDriver()->putStream($destination, $inputStream);
            }catch(\Exception $e){
                p($e->getMessage());
            }
            return $name;
        }else{
            return NULL;
        }
    }

}