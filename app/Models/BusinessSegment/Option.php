<?php

namespace App\Models\BusinessSegment;

use Illuminate\Database\Eloquent\Model;
use App;
use App\Models\OptionType;

class Option extends Model
{
    protected $guarded=[];
    public function BusinessSegment(){
        return $this->belongsTo(BusinessSegment::class);
    }
    public function OptionType(){
        return $this->belongsTo(OptionType::class);
    }

    public function Name($bs_id = NULL)
    {
        $locale = App::getLocale();
        $product = $this->hasOne(LanguageOption::class, 'option_id')
            ->where(function ($q) use ($locale) {
                $q->where('locale', $locale);
                $q->orWhere('locale', '!=', NULL);
            })
            ->where([['business_segment_id', '=', $bs_id]])->first();
        if (!empty($product->id)) {
            return $product->name;
        }
    }
    public function Product()
    {
        return $this->belongsToMany(Product::class, 'option_product', 'option_id')->withPivot('option_amount','product_id');
    }
}
