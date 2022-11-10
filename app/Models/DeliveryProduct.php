<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class DeliveryProduct extends Model
{
    protected $guarded = [];
    protected $hidden = ['LanguageAny', 'LanguageSingle'];

    public function LanguageAny()
    {
        return $this->hasOne(LanguageDeliveryProduct::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(LanguageDeliveryProduct::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getProductNameAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->product_name;
        }
        return $this->LanguageSingle->product_name;
    }

    public function WeightUnit(){
        return $this->belongsTo(WeightUnit::class);
    }

    public function Merchant(){
        return $this->belongsTo(Merchant::class);
    }
}
