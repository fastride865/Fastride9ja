<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class WebsiteFeature extends Model
{
    protected $guarded = [];

    public function LanguageAny()
    {
        return $this->hasOne(WebsiteFeatureTranslation::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(WebsiteFeatureTranslation::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getFeatureTitleAttribute()
    {
        if (empty($this->LanguageSingle)) {
            if(empty($this->LanguageAny)){
                return '';
            }
            return $this->LanguageAny->title;
        }
        return $this->LanguageSingle->title;
    }

    public function getFeatureDiscriptionAttribute()
    {
        if (empty($this->LanguageSingle)) {
            if(empty($this->LanguageAny)){
                return '';
            }
            return $this->LanguageAny->description;
        }
        return $this->LanguageSingle->description;
    }
}
