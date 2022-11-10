<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class PricingParameter extends Model
{
    protected $guarded = [];
    protected $hidden =['LanguageAny','LanguageSingle'];

    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function PricingType()
    {
        return $this->hasMany(PricingParameterValue::class)->orderBy('id', 'DESC');
    }
    public function PriceCardValue()
    {
        return $this->hasMany(PriceCardValue::class);
    }

    public function LanguageAny()
    {
        return $this->hasOne(LanguagePricingParameter::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(LanguagePricingParameter::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getParameterNameAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->parameterName;
        }
        return $this->LanguageSingle->parameterName;
    }

    public function getParameterApplicationAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->parameterNameApplication;
        }
        return $this->LanguageSingle->parameterNameApplication;
    }

    public function Segment()
    {
        return $this->belongsToMany(Segment::class);
    }
}
