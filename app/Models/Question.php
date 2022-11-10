<?php

namespace App\Models;
use App;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $hidden = ['LanguageAny','LanguageSingle'];
    protected $fillable = [
        'question','merchant_id'
    ];
    
    public function LanguageAny()
    {
        return $this->hasOne(LanguageQuestion::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(LanguageQuestion::class)->where([['locale', '=', App::getLocale()]]);
    }
    
    public function getQuestionssAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->question;
        }
        return $this->LanguageSingle->question;
    }
}
