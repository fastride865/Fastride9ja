<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageDeliveryProduct extends Model
{
    protected $guarded = [];

    public function LanguageName()
    {
        return $this->belongsTo(Language::class, 'locale', 'locale');
    }
}
