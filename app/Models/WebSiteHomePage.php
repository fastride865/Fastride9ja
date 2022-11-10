<?php

namespace App\Models;

use App;
use App\Models\WebSiteHomePageTranslation;
use Illuminate\Database\Eloquent\Model;

class WebSiteHomePage extends Model
{
    protected $guarded = [];

    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function LanguageAny()
    {
        return $this->hasOne(WebSiteHomePageTranslation::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(WebSiteHomePageTranslation::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getStartAddressAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->start_address_hint;
        }
        return $this->LanguageSingle->start_address_hint;
    }

    public function getEndAddressAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->end_address_hint;
        }
        return $this->LanguageSingle->end_address_hint;
    }

    public function getBookingButtonAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->book_btn_title;
        }
        return $this->LanguageSingle->book_btn_title;
    }
    //estimate_btn_title

    public function getEstimateButtonAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->estimate_btn_title;
        }
        return $this->LanguageSingle->estimate_btn_title;
    }

    public function getEstimateDescriptionAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->estimate_description;
        }
        return $this->LanguageSingle->estimate_description;
    }

    //driver_heading
    public function getDriverHeadingAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->driver_heading;
        }
        return $this->LanguageSingle->driver_heading;
    }

    //subHeading
    public function getsubHeadingAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->driver_sub_heading;
        }
        return $this->LanguageSingle->driver_sub_heading;
    }
    //driver_buttonText

    public function getdriverButtonTextAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->driver_buttonText;
        }
        return $this->LanguageSingle->driver_buttonText;
    }
    //

    public function getFooterHeadingAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->footer_heading;
        }
        return $this->LanguageSingle->footer_heading;
    }

    //subHeading

    public function getFooterSubHeadingAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->footer_sub_heading;
        }
        return $this->LanguageSingle->footer_sub_heading;
    }
}
