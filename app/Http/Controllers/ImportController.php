<?php

namespace App\Http\Controllers;
use App\Models\Driver;
use App\Models\DriverDocument;
use App\Models\DriverVehicle;
use App\Models\DriverVehicleDocument;

class ImportController extends Controller
{
    public function test()
    {
        $driver = $this->DriverList();
        foreach ($driver as $value) {
            $oldId = $value['id'];
            $newDriver = Driver::create([
                'merchant_id' => '52',
                'unique_number' => NULL,
                'first_name' => $value['first_name'],
                'last_name' => $value['last_name'],
                'email' => $value['email'],
                'password' => $value['password'],
                'home_location_active' => '2',
                'pool_ride_active' => '2',
                'status_for_pool' => '1',
                'avail_seats' => '6',
                'occupied_seats' => '0',
                'pick_exceed' => NULL,
                'pool_user_id' => NULL,
                'phoneNumber' => $value['phoneNumber'],
                'profile_image' => $value['profile_image'],
                'wallet_money' => $value['wallet_money'],
                'total_trips' => NULL,
                'total_earnings' => $value['total_earnings'],
                'total_comany_earning' => '10',
                'outstand_amount' => NULL,
                'current_latitude' => $value['current_latitude'],
                'current_longitude' => $value['current_longitude'],
                'last_location_update_time' => $value['last_location_update_time'],
                'bearing' => '0.0',
                'accuracy' => $value['accuracy'],
                'player_id' => $value['player_id'],
                'rating' => '0',
                'country_area_id' => $value['country_area_id'],
                'login_logout' => $value['login_logout'],
                'online_offline' => $value['online_offline'],
                'free_busy' => $value['free_busy'],
                'bank_name' => $value['bank_name'],
                'account_holder_name' => $value['account_holder_name'],
                'account_number' => $value['account_number'],
                'driver_verify_status' => $value['driver_verify_status'],
                'signupFrom' => $value['signupFrom'],
                'signupStep' => $value['signupStep'],
                'driver_verification_date' => $value['driver_verification_date'],
                'driver_admin_status' => $value['driver_admin_status'],
                'access_token_id' => '',
                'driver_delete' => $value['driver_delete'],
                'created_at' => $value['created_at'],
                'updated_at' => $value['updated_at'],
            ]);


            $docs = $this->DriverDoc($oldId);
            foreach ($docs as $v) {
                DriverDocument::create([
                    "driver_id" => $newDriver->id,
                    "document_id" => $v['document_id'],
                    "document_file" => $v['document_file'],
                    "expire_date" => $v['expire_date'],
                    "document_verification_status" => $v['document_verification_status'],
                    "reject_reason_id" => $v['reject_reason_id'],
                    "created_at" => $v['created_at'],
                    "updated_at" => $v['updated_at'],
                ]);
            }
            $driverVehilce = $this->DriverVehicle($oldId);
            foreach ($driverVehilce as $v) {
                $oldVehile = $v['id'];
                $driverv = DriverVehicle::create([
                    'driver_id' => $newDriver->id,
                    'merchant_id'=>52,
                    'owner_id' => $newDriver->id,
                    'vehicle_type_id' => $v['vehicle_type_id'],
                    'vehicle_make_id' => $v['vehicle_make_id'],
                    'vehicle_model_id' => $v['vehicle_model_id'],
                    'vehicle_number' => $v['vehicle_number'],
                    'vehicle_color' => $v['vehicle_color'],
                    'vehicle_image' => $v['vehicle_image'],
                    'vehicle_number_plate_image' => $v['vehicle_number_plate_image'],
                    'vehicle_active_status' => $v['vehicle_active_status'],
                    'vehicle_verification_status' => $v['vehicle_verification_status'],
                    'reject_reason_id' => $v['reject_reason_id'],
                    'created_at' => $v['created_at'],
                    'updated_at' => $v['updated_at'],
                ]);
                $aa = $this->VEhicleSevice($oldVehile);
                $driverv->ServiceTypes()->sync($aa);
                $driverv->Drivers()->sync($newDriver->id);
                $vehileDoc = $this->VehicleDOc($oldVehile);
                foreach ($vehileDoc as $i) {
                    DriverVehicleDocument::create([
                        'driver_vehicle_id' => $driverv->id,
                        'document_id' => $i['document_id'],
                        'document' => $i['document'],
                        'expire_date' => $i['expire_date'],
                        'document_verification_status' => $i['document_verification_status'],
                        'reject_reason_id' => $i['reject_reason_id'],
                        'created_at' => $i['created_at'],
                        'updated_at' => $i['updated_at'],
                    ]);
                }
            }
        }
    }

    public function DriverList()
    {
    }
     public function DriverDoc($driver_id)
    {
    }
    public function DriverVehicle($driver_id)
    {
	}
    public function VEhicleSevice($vehicle_id)
    {
	}
	public function VehicleDOc($oldVehile)
    {
	}
}
