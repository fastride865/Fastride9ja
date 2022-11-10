<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationString extends Model
{
    protected $guarded = [];
    protected $hidden = ['LanguageAny', 'LanguageSingle'];
    
    public function ApplicationStringLanguage()
    {
        return $this->hasMany(ApplicationStringLanguage::class);
    }

    public function LanguageAny()
    {
        return $this->hasOne(ApplicationStringLanguage::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(ApplicationStringLanguage::class)->where([['locale', '=', \App::getLocale()]]);
    }

    public function ApplicationMerchantString()
    {
        return $this->hasMany(ApplicationMerchantString::class);
    }

}
