<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    protected $guarded = [];
    protected $hidden = [];

    public function State()
    {
        return $this->belongsTo(State::class);
    }

    public function LangTownAny()
    {
        return $this->morphOne(LangName::class, 'dependable');
        //return $this->hasOne(LangSubscriptionPack::class);
    }

    public function LangTownSingle()
    {
        return $this->morphOne(LangName::class, 'dependable')->where([['locale', '=', \App::getLocale()]]);
        //return $this->hasOne(LangSubscriptionPack::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function LangTowns()
    {
        return $this->morphMany(LangName::class, 'dependable');
    }

    public function LangTownSingleApi()
    {
        $lang_data_fetch =  $this->morphOne(LangName::class, 'dependable')->where([['locale','=',\App::getLocale()]]);
        if (empty($lang_data_fetch->get()->toArray())) :
            return $this->LangTownAny();
        else:
            return $lang_data_fetch;
        endif;
    }

    public function getNameAttribute()
    {
        if (empty($this->LangTownSingle)) {
            return $this->LangTownAny->name;
        }
        return $this->LangTownSingle->name;
    }

    public function getDescriptionAttribute()
    {
        if (empty($this->LangTownSingle)) {
            return $this->LangTownAny->field_one;
        }
        return $this->LangTownSingle->field_one;
    }
}
