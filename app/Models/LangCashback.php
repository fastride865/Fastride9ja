<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LangCashback extends Model
{
    protected $guarded = [];
    protected $hidden = [];
    public function LanguageName()
    {
        return $this->belongsTo(Language::class, 'locale', 'locale');
    }

    public function Cashback()
    {
        return $this->belongsTo(Cashback::class);
    }
}
