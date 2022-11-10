<?php

namespace App\Models\BusinessSegment;
use App\Models\Segment;
use App\Models\Merchant;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use App;

class Product extends Model
{
    public function ProductImage()
    {
        return $this->hasMany(ProductImage::class);
    }
    public  function Category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
    public  function Segment()
    {
        return $this->belongsTo(Segment::class);
    }
    public  function BusinessSegment()
    {
        return $this->belongsTo(BusinessSegment::class);
    }
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
    public function ProductVariant()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function langData($merchant_id = NULL)
    {
        $locale = App::getLocale();
        $product = $this->hasOne(LanguageProduct::class, 'product_id')
            ->where('locale', $locale)
            ->where([['merchant_id', '=', $merchant_id]])->first();
           if(empty($product))
           {
               $product = $this->hasOne(LanguageProduct::class, 'product_id')
                   ->where([['merchant_id', '=', $merchant_id]])->first();
           }
        if (!empty($product->id)) {
            return $product;
        }
    }

    public function Name($merchant_id = NULL)
    {
        $locale = App::getLocale();
        $product = $this->hasOne(LanguageProduct::class, 'product_id')
            ->where('locale', $locale)
            ->where([['merchant_id', '=', $merchant_id]])->first();
        if(empty($product))
        {
            $product = $this->hasOne(LanguageProduct::class, 'product_id')
                ->where([['merchant_id', '=', $merchant_id]])->first();
        }
        if (!empty($product->id)) {
            return $product->name;
        }
    }

    public function LanguageProduct()
    {
        return $this->hasMany(LanguageProduct::class);
    }

    public function Option()
    {
        return $this->belongsToMany(Option::class, 'option_product', 'product_id')->withPivot('option_amount');
    }
}
