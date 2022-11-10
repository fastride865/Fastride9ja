<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Stmt\Return_;

class AccountType extends Model
{
    protected $guarded = [];
    protected $hidden = [];

    public function LangAccountTypeAny()
    {
        return $this->morphOne(LangName::class, 'dependable');
    }

    public function LangAccountTypeSingle()
    {
        return $this->morphOne(LangName::class, 'dependable')->where([['locale', '=', \App::getLocale()]]);
    }

    public function LangAccountTypes()
    {
        return $this->morphMany(LangName::class, 'dependable');
    }

    public function LangAccountTypeSingleApi()
    {
        $lang_data_fetch =  $this->morphOne(LangName::class, 'dependable')->where([['locale','=',\App::getLocale()]]);
        if (empty($lang_data_fetch->get()->toArray())) :
            return $this->LangAccountTypeAny();
        else:
            return $lang_data_fetch;
        endif;
    }

    public function getNameAttribute()
    {
        if (empty($this->LangAccountTypeSingle)) {
            return $this->LangAccountTypeAny->name;
        }
        return $this->LangAccountTypeSingle->name;
    }

    public function getDescriptionAttribute()
    {
        if (empty($this->LangAccountTypeSingle)) {
            return $this->LangAccountTypeAny->field_one;
        }
        return $this->LangAccountTypeSingle->field_one;
    }
}
