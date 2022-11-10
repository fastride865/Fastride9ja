<?php

namespace App\Models\BusinessSegment;

use Illuminate\Database\Eloquent\Model;

class LanguageOption extends Model
{
    protected $guarded = [];

    public function LanguageName()
    {
        return $this->belongsTo(Language::class, 'locale', 'locale');
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
            return $product->name;
        }
    }
}
