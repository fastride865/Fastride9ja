<?php

namespace App\Models\BusinessSegment;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
use App;
use App\Models\Merchant;
use App\Models\Segment;
use App\Models\Country;
use App\Models\CountryArea;
use App\Models\StyleManagement;
use App\Models\FavouriteBusinessSegment;
use App\Models\DriverAgency\DriverAgency;
use App\Models\MerchantWebOneSignal;
use Laravel\Passport\HasApiTokens;
use App\Http\Controllers\Helper\CommonController;

//use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessSegment extends Authenticatable
{
    use Notifiable, HasApiTokens;
//    use SoftDeletes;

    protected $guarded = [];
    protected $hidden = [
        'password', 'remember_token','pivot',
    ];


    public function findForPassport($user_cred = null)
    {
        if (!empty($_SERVER['HTTP_PUBLICKEY']) && !empty($_SERVER['HTTP_SECRETKEY'])) {
            $merchant = CommonController::Marchant($_SERVER['HTTP_PUBLICKEY'], $_SERVER['HTTP_SECRETKEY']);
            $merchant_id = $merchant['id'];
        }
        $user =  BusinessSegment::where([['merchant_id', '=', $merchant_id], ['email', '=', $user_cred], ['status', '=', 1]])
//            ->orWhere([['merchant_id', '=', $merchant_id], ['id', '=', $user_cred], ['status', '=', 1]])
       ->first();
        return $user;
    }

    // public function validateForPassportPasswordGrant($pass = null)
    // {
    //     return true;
    // }

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
        return $this->belongsTo(CountryArea::class);
    }

    public function Segment()
    {
        return $this->belongsTo(Segment::class);
    }
    public function StyleManagement(){
        return $this->belongsToMany(StyleManagement::class,'business_segment_style_management','business_segment_id');
    }

    public  function BannerManagement(){
        return $this->hasMany(App\Models\BannerManagement::class,'id');
    }
    public  function Category(){
        return $this->hasMany(App\Models\Category::class,'id');
    }
    public  function Product(){
        return $this->hasMany(Product::class);
    }
    public  function Option(){
        return  $this->belongsToMany(Option::class);
    }
    // one signal web player id
    public  function webOneSignalPlayerId(){
        return $this->hasMany(MerchantWebOneSignal::class);
    }

    public function BusinessSegmentCashout(){
        return $this->hasMany(BusinessSegmentCashout::class);
    }

    public function BusinessSegmentWalletTransaction(){
        return $this->hasMany(BusinessSegmentWalletTransaction::class);
    }

    public function Onesignal()
    {
        return $this->hasOne(BusinessSegmentOnesignal::class);
    }

    public function DriverAgency(){
        return $this->belongsToMany(DriverAgency::class,'business_segment_driver_agency','business_segment_id');
    }
    public function FavouriteBusinessSegment(){
        return $this->hasOne(FavouriteBusinessSegment::class);
    }
}
