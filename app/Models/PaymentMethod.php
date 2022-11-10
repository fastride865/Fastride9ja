<?php

namespace App\Models;
use Auth;
use App;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $hidden = array('pivot');
    public $timestamps= false;

    protected $guarded = [];
    
    public function PaymentMethodTranslation()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        return $this->hasOne(PaymentMethodTranslation::class,'payment_method_id')->where([['locale', '=', App::getLocale()],['merchant_id','=',$merchant_id]]);
    }

    public function LanguageSingle($merchant_id)
    {
        return $this->hasOne(PaymentMethodTranslation::class)->where([['locale', '=', App::getLocale()],['merchant_id','=',$merchant_id]]);
    }

    public function LanguageAny($merchant_id)
    {
        return $this->hasOne(PaymentMethodTranslation::class)->where([['merchant_id','=',$merchant_id]]);
    }

    public function MethodName($merchant_id)
    {
        if (!empty($this->LanguageSingle($merchant_id)->first())) {
            return $this->LanguageSingle($merchant_id)->first()->name;
        }
        if (!empty($this->LanguageAny($merchant_id)->first()))
        {
            return $this->LanguageAny($merchant_id)->first()->name;
        }
    }

    public function Merchant()
    {
        return $this->belongsToMany(Merchant::class)->withPivot('merchant_id','payment_method_id','icon');
    }
}
