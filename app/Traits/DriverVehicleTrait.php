<?php

namespace App\Traits;

use App\Models\Driver;
use App\Models\LanguageVehicleMake;
use App\Models\LanguageVehicleModel;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Auth;
use Illuminate\Support\Facades\App;

trait DriverVehicleTrait
{
    public function vehicleModel($vehicle_model, $merchant_id, $vehicleMake_id, $vehicle_type, $vehicle_seat)
    {
        $vehcleModel = VehicleModel::find($vehicle_model);
        if (empty($vehcleModel)) {
            $vehcleModel = VehicleModel::select('id')->whereHas('LanguageVehicleModel', function ($query) use ($vehicle_model) {
                $query->where([['vehicleModelName', '=', $vehicle_model]]);
            })->where([['merchant_id', '=', $merchant_id], ['vehicle_type_id', '=', $vehicle_type], ['vehicle_make_id', '=', $vehicleMake_id]])->first();

            if (empty($vehcleModel)) {
                $vehcleModel = VehicleModel::create([
                    'merchant_id' => $merchant_id,
                    'vehicle_type_id' => $vehicle_type,
                    'vehicle_make_id' => $vehicleMake_id,
                    'vehicle_seat' => $vehicle_seat,
                ]);
                $this->SaveLanguageVehicleModel($merchant_id, $vehcleModel->id, $vehicle_model, $vehicle_model);
            }
        }
        return $vehcleModel->id;
    }

    public function SaveLanguageVehicleModel($merchant_id, $vehicle_model_id, $name, $description)
    {
        LanguageVehicleModel::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'vehicle_model_id' => $vehicle_model_id
        ], [
            'vehicleModelName' => $name,
            'vehicleModelDescription' => $description,
        ]);
    }

    public function vehicleMake($vehicleMake_id, $merchant_id)
    {
        $vehicleMake = VehicleMake::select('id')->find($vehicleMake_id);
        if (empty($vehicleMake)) {
            $checkMake = LanguageVehicleMake::where([['merchant_id', '=', $merchant_id], ['vehicleMakeName', '=', $vehicleMake_id]])->first();
            if (!empty($checkMake)) {
                $vehicleMake = VehicleMake::find($checkMake->vehicle_make_id);
            } else {
                $vehicleMake = VehicleMake::create([
                    'merchant_id' => $merchant_id,
                    'vehicleMakeLogo' => $vehicleMake_id,
                ]);
                $this->SaveLanguageVehicle($merchant_id, $vehicleMake->id, $vehicleMake_id, $vehicleMake_id);
            }
        }
        return $vehicleMake->id;
    }

    public function SaveLanguageVehicle($merchant_id, $vehicle_make_id, $name, $description)
    {
        LanguageVehicleMake::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'vehicleMakeName' => $name,
        ], [
            'vehicle_make_id' => $vehicle_make_id,
            'vehicleMakeDescription' => $description,
        ]);
    }
}
