<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LangAppNavDrawer extends Model
{
    public $fillable = ['merchant_id', 'merchant_nav_drawer_id', 'locale', 'name'];

    public function LanguageName()
    {
        return $this->belongsTo(Language::class, 'locale', 'locale');
    }
}


