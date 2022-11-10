<?php

namespace App;

use App\Http\Controllers\Helper\CommonController;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Driver extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $guarded = [];

    public function findForPassport($user_cred = null)
    {
        if (!empty($_SERVER['HTTP_PUBLICKEY']) && !empty($_SERVER['HTTP_SECRETKEY'])) {
            $merchant = CommonController::Marchant($_SERVER['HTTP_PUBLICKEY'], $_SERVER['HTTP_SECRETKEY']);
            $merchant_id = $merchant['id'];
        }
        return Driver::where([['merchant_id', '=', $merchant_id], ['unique_number', '=', $user_cred], ['driver_admin_status', '=', 1]])->orWhere([['merchant_id', '=', $merchant_id], ['id', '=', $user_cred], ['driver_admin_status', '=', 1]])->first();
    }

    public function validateForPassportPasswordGrant($pass = null)
    {
        return true;
    }
}
