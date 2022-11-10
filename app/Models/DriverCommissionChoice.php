<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class DriverCommissionChoice extends Model
{
    use SoftDeletes;

    protected $hidden = ['LangCommissionChoiceAny', 'LangCommissionChoiceSingle', 'LangCommissionChoiceSingleApi',
        'LangCommissionChoiceAccMerchantAny', 'LangCommissionChoiceAccMerchantSingle'];
    protected $guarded = [];

    public function LangCommissionChoiceAny()
    {
        return $this->morphOne(LangName::class, 'dependable');
    }

    public function LangCommissionChoiceSingle()
    {
        return $this->morphOne(LangName::class, 'dependable')->where([['locale', '=', \App::getLocale()]]);
    }

    public function LangCommissionChoices()
    {
        return $this->morphMany(LangName::class, 'dependable');
    }

    public function LangCommissionChoiceSingleApi()
    {
        $lang_commission_choices_fetch = $this->morphOne(LangName::class, 'dependable')->where([['locale', '=', \App::getLocale()]]);
        if (empty($lang_commission_choices_fetch->get()->toArray())) :
            return $this->LangCommissionChoiceAny();
        else:
            return $lang_commission_choices_fetch;
        endif;
    }

    public function getNameAttribute()
    {
        if (empty($this->LangCommissionChoiceSingle)) {
            return $this->LangCommissionChoiceAny['name'];
        }
        return $this->LangCommissionChoiceSingle['name'];
    }

    public function getNameAccMerchantWebAttribute()
    {
        if (empty($this->LangCommissionChoiceAccMerchantSingle)) {
            if (empty($this->LangCommissionChoiceAccMerchantAny)) {
                return;
            }
            return $this->LangCommissionChoiceAccMerchantAny['name'];
        }
        return $this->LangCommissionChoiceAccMerchantSingle['name'];
    }

    public function LangCommissionChoiceAccMerchantAny($merchant_id = null)
    {

        $merchant_id = ($merchant_id != null) ? $merchant_id : (\Auth::user('merchant')['parent_id'] != 0 ? \Auth::user('merchant')['parent_id'] : \Auth::user('merchant')['id']);
        return $this->morphOne(LangName::class, 'dependable')->where([['merchant_id', $merchant_id]]);
    }

    public function LangCommissionChoiceAccMerchantSingle($merchant = null)
    {
        $merchant_id = ($merchant != null) ? $merchant : (\Auth::user('merchant')['parent_id'] != 0 ? \Auth::user('merchant')['parent_id'] : \Auth::user('merchant')['id']);
        return $this->morphOne(LangName::class, 'dependable')->where([['locale', '=', \App::getLocale()], ['merchant_id', $merchant_id]]);
    }

    public function getNameAccMerchantApiAttribute($merchant_id)
    {
        if (empty($this->LangCommissionChoiceAccMerchantSingle($merchant_id)->first())) {
            if (empty($this->LangCommissionChoiceAccMerchantAny($merchant_id)->first())) {
                return $this->slug;
            }
            return $this->LangCommissionChoiceAccMerchantAny($merchant_id)->first()['name'];
            //return $this->LangCommissionChoiceAccMerchantAny;
        }
        //return $this->LangCommissionChoiceAccMerchantSingle;
        return $this->LangCommissionChoiceAccMerchantSingle($merchant_id)->first()['name'];
    }
}
