<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageDuration extends Model
{
    //
    use SoftDeletes;
    
    protected $hidden = ['LangPackageDurationAny','LangPackageDurationSingle','LangPackageDurationSingleApi',
                            'LangPackageDurationAccMerchantAny','LangPackageDurationAccMerchantSingle'];
    protected $guarded = [];
	
	public function LangPackageDurationAny()
    {
        return $this->morphOne(LangName::class, 'dependable');
		//return $this->hasOne(LangSubscriptionPack::class);
    }

    public function LangPackageDurationSingle()
    {
		return $this->morphOne(LangName::class, 'dependable')->where([['locale', '=', \App::getLocale()]]);
        //return $this->hasOne(LangSubscriptionPack::class)->where([['locale', '=', App::getLocale()]]);
    }
    
    public function LangPackageDurations()
    {
        return $this->morphMany(LangName::class, 'dependable');
    }

    public function LangPackageDurationSingleApi()
    {
        $lang_package_durations_fetch =  $this->morphOne(LangName::class, 'dependable')->where([['locale','=',\App::getLocale()]]);
        if (empty($lang_package_durations_fetch->get()->toArray())) :
            return $this->LangPackageDurationAny();
        else:
            return $lang_package_durations_fetch;
        endif;
    }

    public function getNameAttribute()
    {
        if (empty($this->LangPackageDurationSingle)) {
            return $this->LangPackageDurationAny['name'];
        }
        return $this->LangPackageDurationSingle['name'];
    }
    
    public function LangPackageDurationAccMerchantAny()
    {
        $merchant_id = \Auth::user('merchant')->parent_id != 0 ? \Auth::user('merchant')->parent_id : \Auth::user('merchant')->id;
        return $this->morphOne(LangName::class, 'dependable')->where([['merchant_id',$merchant_id]]);
        //return $this->hasOne(LangSubscriptionPack::class);
    }

    public function LangPackageDurationAccMerchantSingle()
    {
        $merchant_id = \Auth::user('merchant')->parent_id != 0 ? \Auth::user('merchant')->parent_id : \Auth::user('merchant')->id;
        return $this->morphOne(LangName::class, 'dependable')->where([['locale', '=', \App::getLocale()],['merchant_id',$merchant_id]]);
        //return $this->hasOne(LangSubscriptionPack::class)->where([['locale', '=', App::getLocale()]]);
    }

//    public function getNameAccMerchantAttribute()
//    {
//        if (empty($this->LangPackageDurationAccMerchantSingle))
//        {
//            if(empty($this->LangPackageDurationAccMerchantAny))
//            {
//                return ;
//            }
//            return $this->LangPackageDurationAccMerchantAny['name'];
//        }
//        return $this->LangPackageDurationAccMerchantSingle['name'];
//    }

    public function getNameAccMerchantAttribute($merchant_id = NULL,$dependable_id = null,$locale = 'en')
    {
        if(!empty($merchant_id))
        {
            // case for subscription api
            $data= LangName::where([['locale', '=',$locale],['dependable_id',$dependable_id],['merchant_id',$merchant_id]])->first();
            if(empty($data['name']))
            {
                $data= LangName::where([['dependable_id',$dependable_id],['merchant_id',$merchant_id]])->first();
            }
            return $data['name'];
        }
        else
        {
            if (empty($this->LangPackageDurationAccMerchantSingle))
            {
                if(empty($this->LangPackageDurationAccMerchantAny))
                {
                    return ;
                }
                return $this->LangPackageDurationAccMerchantAny['name'];
            }
            return $this->LangPackageDurationAccMerchantSingle['name'];
        }
    }


}
