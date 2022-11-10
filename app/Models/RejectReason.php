<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class RejectReason extends Model
{
    protected $guarded =[];

    public function LanguageAny()
    {
        return $this->hasOne(RejectTranslation::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(RejectTranslation::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getReasonNameAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->title;
        }
        return $this->LanguageSingle->title;
    }
}
