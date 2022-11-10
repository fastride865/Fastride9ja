<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class EmailTemplate extends Model
{
    protected $guarded = [];

    protected $hidden = ['LanguageEmailTemplateSingle', 'LanguageEmailTemplateAny'];

    public function LanguageEmailTemplateAny()
    {
        return $this->hasOne(LanguageEmailTemplate::class);
    }

    public function LanguageEmailTemplateSingle()
    {
        return $this->hasOne(LanguageEmailTemplate::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getHeadingAttribute()
    {
        if (empty($this->LanguageEmailTemplateSingle)) {
            if (empty($this->LanguageEmailTemplateAny)){
                return  '';
            }
            return $this->LanguageEmailTemplateAny->heading;
        }
        return $this->LanguageEmailTemplateSingle->heading;
    }

    public function getSubheadingAttribute()
    {
        if (empty($this->LanguageEmailTemplateSingle)) {
            if (empty($this->LanguageEmailTemplateAny)){
                return  '';
            }
            return $this->LanguageEmailTemplateAny->subheading;
        }
        return $this->LanguageEmailTemplateSingle->subheading;
    }

    public function getMessageAttribute()
    {
        if (empty($this->LanguageEmailTemplateSingle)) {
            if (empty($this->LanguageEmailTemplateAny)){
                return  '';
            }
            return $this->LanguageEmailTemplateAny->message;
        }
        return $this->LanguageEmailTemplateSingle->message;
    }
}
