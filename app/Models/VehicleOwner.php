<?php

namespace App\Models;

use App\Http\Controllers\Helper\CommonController;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class VehicleOwner extends Authenticatable
{
    use Notifiable, HasApiTokens;
    protected $guarded = [];

    public function findForPassport($user_cred = null)
    {
        $merchant_id = "";
        if (!empty($_SERVER['HTTP_PUBLICKEY']) && !empty($_SERVER['HTTP_SECRETKEY'])) {
            $merchant = CommonController::MerchantObj($_SERVER['HTTP_PUBLICKEY'], $_SERVER['HTTP_SECRETKEY']);
            $merchant_id = $merchant['id'];
        }
        return VehicleOwner::where([['merchant_id', '=', $merchant_id], ['phone', '=', $user_cred]])->latest()->first();
    }

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }

    public function VehicleList()
    {
        return $this->hasMany(DriverVehicle::class, 'owner_id')->where('ownerType', '=', 2);
    }
}
