<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;
use App\Models\BusinessSegment\Option;

class OptionType extends Model
{
    protected $guarded=[];
    public function Merchant(){
        return $this->belongsTo(Merchant::class);
    }
      public function Option(){
        return $this->hasMany(Option::class);
    }

    public function LanguageOptionTypeAny()
    {
        return $this->hasOne(LanguageOptionType::class);
    }

    public function LanguageOptionTypeSingle()
    {
        return $this->hasOne(LanguageOptionType::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getOptionTypeNameAttribute()
    {
        if (empty($this->LanguageOptionTypeSingle)) {
            return $this->LanguageOptionTypeAny->type;
        }
        return $this->LanguageOptionTypeSingle->type;
    }

    public function Type($merchant_id = NULL)
    {
        $locale = App::getLocale();
        $product = $this->hasOne(LanguageOptionType::class, 'option_type_id')
            ->where(function ($q) use ($locale) {
                $q->where('locale', $locale);
                $q->orWhere('locale', '!=', NULL);
            })
            ->where([['merchant_id', '=', $merchant_id]])->first();
        if (!empty($product->id)) {
            return $product->type;
        }
    }
}
