<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class WeightUnit extends Model
{
    protected $guarded = [];

    protected $hidden = ['pivot', 'LanguageAny', 'LanguageSingle'];

    public function LanguageAny()
    {
        return $this->hasOne(WeightUnitTranslation::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(WeightUnitTranslation::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getWeightUnitNameAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->name;
        }
        return $this->LanguageSingle->name;
    }

    public function getWeightUnitDescriptionAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->description;
        }
        return $this->LanguageSingle->description;
    }

    public function Segment()
    {
        return $this->belongsToMany(Segment::class);
    }
}
