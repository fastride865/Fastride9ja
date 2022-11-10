<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LangSubscriptionPack extends Model
{
    protected $guarded = [];

    public function LanguageName()
    {
        return $this->belongsTo(Language::class, 'locale', 'locale');
    }

    public function SubscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }
}
