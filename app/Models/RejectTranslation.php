<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RejectTranslation extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function LanguageName()
    {
        return $this->belongsTo(Language::class, 'locale', 'locale');
    }
}
