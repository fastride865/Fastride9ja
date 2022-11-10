<?php

namespace App;

use App\Models\CountryArea;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\Http\Controllers\Helper\CommonController;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function findForPassport($user_cred = null)
    {
        if (!empty($_SERVER['HTTP_PUBLICKEY']) && !empty($_SERVER['HTTP_SECRETKEY'])) {
            $merchant = CommonController::Marchant($_SERVER['HTTP_PUBLICKEY'], $_SERVER['HTTP_SECRETKEY']);
            $merchant_id = $merchant['id'];
        }
        return User::Where([['merchant_id', '=', $merchant_id], ['social_id', '=', $user_cred], ['UserStatus', '=', 1], ['user_delete', '=', NULL]])
            ->orWhere([['merchant_id', '=', $merchant_id], ['unique_number', '=', $user_cred], ['UserStatus', '=', 1], ['login_type', '=', 1], ['user_delete', '=', NULL]])
//            ->orWhere([['merchant_id', '=', $merchant_id], ['id', '=', $user_cred], ['UserStatus', '=', 1], ['user_delete', '=', NULL]])
            ->first();
    }

    public function validateForPassportPasswordGrant($pass = null)
    {
        return true;
    }

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }

    public function outstandings() {
      return $this->hasMany(OutStanding :: class);
    }
}
