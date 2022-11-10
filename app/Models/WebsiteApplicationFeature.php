<?php

namespace App\Models;
use App;
use Illuminate\Database\Eloquent\Model;

class WebsiteApplicationFeature extends Model
{
    protected $guarded = [];

    public function LanguageAny()
    {
        return $this->hasOne(WebsiteApplicationTranslation::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(WebsiteApplicationTranslation::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getFeatureTitleAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->title;
        }
        return $this->LanguageSingle->title;
    }

    public function getFeatureDiscriptionAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->description;
        }
        return $this->LanguageSingle->description;
    }
}
