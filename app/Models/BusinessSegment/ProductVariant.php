<?php

namespace App\Models\BusinessSegment;

use App\Models\LangName;
use App\Models\WeightUnit;
use Illuminate\Database\Eloquent\Model;
use App;

class ProductVariant extends Model
{
    public function Product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function WeightUnit()
    {
        return $this->belongsTo(WeightUnit::class);
    }

    public function ProductInventory()
    {
        return $this->hasOne(ProductInventory::class);
    }
    public function LanguageProductVariant()
    {
        return $this->hasMany(LanguageProductVariant::class);
    }
//    public function LangVariant()
//    {
//        return $this->morphMany(LangName::class, 'dependable');
//    }
//    public function LangVariantSingle()
//    {
//        return $this->morphOne(LangName::class, 'dependable')->where([['locale', '=', \App::getLocale()]]);
//    }
    // multi lang for style

    public function Name($merchant_id = NULL)
    {
        $locale = App::getLocale();
        $variant = $this->hasOne(LanguageProductVariant::class)->where([['merchant_id', '=', $merchant_id]])
            ->where(function ($q) use ($locale) {
                $q->where('locale', $locale);
            })->first();
        if (!empty($variant->id)) {
            return $variant->name;
        }
        else
        {
            $variant = $this->hasOne(LanguageProductVariant::class)->where([['merchant_id', '=', $merchant_id]])
                ->where(function ($q) use ($locale) {
                    $q->where('locale', '!=', NULL);
                })->first();
            if (!empty($variant->id)) {
                return $variant->name;
            }
        }
        return "";
    }
}
