<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $guarded = [];

    protected $hidden = ['LanguageAny', 'LanguageSingle'];
    
    public function Page()
    {
        return $this->belongsTo(Page::class,'slug','slug');
    }

    public function LanguageAny()
    {
        return $this->hasOne(LanguageCmsPage::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(LanguageCmsPage::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getCmsPageTitleAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->title;
        }
        return $this->LanguageSingle->title;
    }

    public function getCmsPageDescriptionAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->description;
        }
        return $this->LanguageSingle->description;
    }

    public function Country()
    {
        return $this->belongsTo(Country::class);
    }
}
