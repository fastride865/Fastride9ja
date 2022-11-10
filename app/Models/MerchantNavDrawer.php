<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantNavDrawer extends Model
{
    public $hidden = ['AppNavigationDrawer', 'LanguageAppNavigationDrawersOneViews', 'LanguageAppNavigationDrawersAnyViews'];

    public function AppNavigationDrawer()
    {
        return $this->belongsTo(AppNavigationDrawer::class);
    }

    public function LanguageAppNavigationDrawers()
    {
        return $this->hasMany(LangAppNavDrawer::class);
    }

    public function LanguageAppNavigationDrawersOneViews()
    {
        return $this->hasOne(LangAppNavDrawer::class)->where([['locale', '=', \App::getLocale()]]);
    }

    public function LanguageAppNavigationDrawersAnyViews()
    {
        return $this->hasOne(LangAppNavDrawer::class);
    }

    public function getSlugAttribute()
    {
        return $this->AppNavigationDrawer->slug;
    }

    public function getNameAttribute()
    {
        if (empty($this->LanguageAppNavigationDrawersOneViews)) {
            return $this->LanguageAppNavigationDrawersAnyViews->name;
        }
        return $this->LanguageAppNavigationDrawersOneViews->name;
    }

    public function LanguageAppNavigationDrawersApi()
    {
        $lang_drawers_fetch = $this->hasOne(LangAppNavDrawer::class)->where([['locale', '=', \App::getLocale()]]);
        if (empty($lang_drawers_fetch->get()->toArray())) :
            return $this->LanguageAppNavigationDrawersAnyViews();
        endif;
        return $lang_drawers_fetch;
    }
}
