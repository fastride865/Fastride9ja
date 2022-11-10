<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguagePricingParameter extends Model
{
    protected $guarded = [];

    public function LanguageName()
    {
        return $this->belongsTo(Language::class, 'locale', 'locale');
    }
}
