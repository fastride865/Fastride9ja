<?php

namespace App\Models;

use App\Http\Controllers\Helper\CommonController;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->user_merchant_id = $model->NewUserId($model->merchant_id);
            return $model;
        });
    }

    public function NewUserId($merchantID)
    {
        $user = User::where([['merchant_id', '=', $merchantID]])->latest()->first();
        if (!empty($user)) {
            return $user->user_merchant_id + 1;
        } else {
            return 1;
        }
    }

//    public static function PushMessage($playerid, $data, $message, $type)
//    {
//        $content = array(
//            "en" => $message,
//        );
//        $sendField = "include_player_ids";
//        $sendField = $type == "2" ? "included_segments" : $sendField;
//        $fields = array(
//            'app_id' => "468c3d76-ca91-421e-9928-2ee475d58f51",
//            $sendField => $playerid,
//            'contents' => $content,
//            'data' => array('data' => $data, 'type' => $type),
//        );
//        $fields = json_encode($fields);
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
//            'Authorization: Basic Mzg4ZGUxN2ItYTU2MC00YWRiLTkxNzAtZTU1MzU0YTY2MzE3'));
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_HEADER, FALSE);
//        curl_setopt($ch, CURLOPT_POST, TRUE);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        $response = curl_exec($ch);
//        curl_close($ch);
//        return $response;
//    }


    public function findForPassport($user_cred = null)
    {
        if (!empty($_SERVER['HTTP_PUBLICKEY']) && !empty($_SERVER['HTTP_SECRETKEY'])) {
            $merchant = CommonController::MerchantObj($_SERVER['HTTP_PUBLICKEY'], $_SERVER['HTTP_SECRETKEY']);
            $user_login = $merchant->ApplicationConfiguration->user_login;
            $merchant_id = $merchant['id'];
        }
        if ($user_login == "EMAIL") {
            return User::where([['merchant_id', '=', $merchant_id], ['email', '=', $user_cred], ['UserStatus', '=', 1],['user_delete','=',NULL]])
//                ->orWhere([['merchant_id', '=', $merchant_id], ['unique_number', '=', $user_cred], ['UserStatus', '=', 1], ['login_type', '=', 1]])
                ->latest()->first();
        }
        return User::where([['merchant_id', '=', $merchant_id], ['UserPhone', '=', $user_cred], ['UserStatus', '=', 1],['user_delete','=',NULL]])
//            ->orWhere([['merchant_id', '=', $merchant_id], ['unique_number', '=', $user_cred], ['UserStatus', '=', 1], ['login_type', '=', 1]])
            ->latest()->first();
    }

    public function GenrateReferCode()
    {
        $code = getRandomCode();
        if ($this->CheckReferCode($code)) {
            return $this->GenrateReferCode();
        }
        return $code;
    }

    public function CheckReferCode($referCode)
    {
        return static::where([['ReferralCode', '=', $referCode]])->exists();
    }

    public function FavouriteLocation()
    {
        return $this->hasMany(FavouriteLocation::class);
    }
    public function UserDevice()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function Franchisee()
    {
        return $this->belongsToMany(Franchisee::class);
    }

    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function Country()
    {
        return $this->belongsTo(Country::class);
    }
    
    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class, 'country_area_id');
    }

    public function UserDocuments()
    {
        return $this->belongsToMany(Document::class, 'user_documents')->withPivot(['id', 'document_file', 'expire_date', 'document_verification_status', 'reject_reason_id','document_number']);
    }

    public function getUserNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function outstandings()
    {
        return $this->hasMany(OutStanding:: class);
    }

    public function Corporate(){
        $this->belongsTo(Corporate::class);
    }

    public function employeeDesignations()
    {
        $this->belongsToMany(EmployeeDesignation::class);
    }
    public function UserAddress()
    {
        return $this->hasMany(UserAddress::class);
    }
    public function FavouriteDriver()
    {
        return $this->hasMany(FavouriteDriver::class);
    }
    public function Booking()
    {
        return $this->hasMany(Booking::class)->where('booking_status', 1005);
    }
    public function Sos()
    {
        return $this->hasMany(Sos::class);
    }

}
