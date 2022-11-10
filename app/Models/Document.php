<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $hidden = array('pivot','LanguageSingle','LanguageAny');
    protected $guarded = [];

    public function LanguageAny()
    {
        return $this->hasOne(LanguageDocument::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(LanguageDocument::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getDocumentNameAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->documentname;
        }
        return $this->LanguageSingle->documentname;
    }
    
    public function getDocumentAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny();
        }
        return $this->LanguageSingle();
    }

    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function CountryAreas()
    {
        return $this->belongsToMany(CountryArea::class);
    }

}
