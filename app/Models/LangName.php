<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LangName extends Model
{
    protected $guarded = [];
    protected $hidden = [];
	
	public function dependable()
    {
        return $this->morphTo();
    }

    public function LanguageName()
    {
        return $this->belongsTo(Language::class, 'locale', 'locale');
    }
    public function Merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
