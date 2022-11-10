<?php

namespace App\Http\Controllers\CronJob;

use App\Models\DriverVehicle;
use App\Models\Onesignal;
use App\Models\BusinessSegment\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\MerchantTrait;

class PerYearCronController extends Controller
{
    use MerchantTrait;
    public function perYear()
    {
        $this->expireRegisteredVehicle();
    }
    // expire placed orders
    public function expireRegisteredVehicle()
    {
        $all_vehicles = DriverVehicle::select('id','vehicle_type_id','driver_id','vehicle_register_date','vehicle_expire_date','merchant_id')
            ->whereHas('Merchant',function($q){
                $q->whereHas('Merchant',function($qq){
                $qq->where('vehicle_model_expire',1);
                });
            })
            ->whereHas('Driver',function($q){
                $q->where('driver_delete','!=',1);
            })
            ->whereNotNull('vehicle_expire_date')
            ->whereNotNull('vehicle_register_date')
            ->where('vehicle_verification_status',2)
            ->get();
        if ($all_vehicles->isNotEmpty())
        {
            foreach ($all_vehicles as $item) {
                date_default_timezone_set($item->CountryArea['timezone']);
                $model_age_diff = date_diff(date_create($item->vehicle_expire_date),date_create($item->vehicle_register_date));
                $model_age_diff_year = $model_age_diff->y;
                $model_age = $item->VehicleType->model_expire_year;
                if($model_age_diff_year > $model_age)
                {
                    $item->vehicle_verification_status = 4; // expire vehicle
                    $item->save();
                }
            }
        }
    }

    public function testCron(){
        \Log::channel('onesignal')->emergency(array(
            "text" => "Hello",
            "message" => "Your most welcome!"
        ));
    }
}
