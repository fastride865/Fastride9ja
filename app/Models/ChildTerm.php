<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildTerm extends Model
{
    protected $guarded = [];
    protected $hidden = ['LangTermsConditionSingle','LangTermsConditionAny','LangTermsConditions'];

    public function Country()
    {
        return $this->belongsTo(Country::class);
    }

    public function LangTermsConditionAny()
    {
        return $this->morphOne(LangName::class, 'dependable');
        //return $this->hasOne(LangSubscriptionPack::class);
    }

    public function LangTermsConditionSingle()
    {
        return $this->morphOne(LangName::class, 'dependable')->where([['locale', '=', \App::getLocale()]]);
        //return $this->hasOne(LangSubscriptionPack::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function LangTermsConditions()
    {
        return $this->morphMany(LangName::class, 'dependable');
    }

    public function getTitleAttribute()
    {
        if (empty($this->LangTermsConditionSingle)) {
            return $this->LangTermsConditionAny->name;
        }
        return $this->LangTermsConditionSingle->name;
    }

    public function getDescriptionAttribute()
    {
        if (empty($this->LangTermsConditionSingle)) {
            return $this->LangTermsConditionAny->field_three;
        }
        return $this->LangTermsConditionSingle->field_three;
    }
}
