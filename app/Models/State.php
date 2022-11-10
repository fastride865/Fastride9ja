<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
   protected $guarded = [];
   protected $hidden = [];

   public function Country()
   {
       return $this->belongsTo(Country::class);
   }

    public function LangStateAny()
    {
        return $this->morphOne(LangName::class, 'dependable');
        //return $this->hasOne(LangSubscriptionPack::class);
    }

    public function LangStateSingle()
    {
        return $this->morphOne(LangName::class, 'dependable')->where([['locale', '=', \App::getLocale()]]);
        //return $this->hasOne(LangSubscriptionPack::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function LangStates()
    {
        return $this->morphMany(LangName::class, 'dependable');
    }

    public function LangStateSingleApi()
    {
        $lang_data_fetch =  $this->morphOne(LangName::class, 'dependable')->where([['locale','=',\App::getLocale()]]);
        if (empty($lang_data_fetch->get()->toArray())) :
            return $this->LangStateAny();
        else:
            return $lang_data_fetch;
        endif;
    }

    public function getNameAttribute()
    {
        if (empty($this->LangStateSingle)) {
            return $this->LangStateAny->name;
        }
        return $this->LangStateSingle->name;
    }

    public function getDescriptionAttribute()
    {
        if (empty($this->LangStateSingle)) {
            return $this->LangStateAny->field_one;
        }
        return $this->LangStateSingle->field_one;
    }
}
