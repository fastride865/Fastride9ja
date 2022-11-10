<?php

namespace App\Models;
use DB;
use App;
use App\Models\BusinessSegment\Product;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public function Product()
    {
       return $this->hasMany(Product::class,'category_id');
    }
    public function BusinessSegment()
    {
       return $this->belongsTo(BusinessSegment::class);
    }
    public function Merchant()
    {
       return $this->belongsTo(Merchant::class,'merchant_id');
    }
    public function Segment()
    {
        return $this->belongsToMany(Segment::class);
    }
    public function VehicleType()
    {
        return $this->belongsToMany(VehicleType::class)->withPivot('country_area_id','service_type_id','segment_id');
    }

    public function LangCategory()
    {
        return $this->morphMany(LangName::class, 'dependable');
    }
    public function LangCategorySingle()
    {
        return $this->morphOne(LangName::class, 'dependable')->where([['locale', '=', \App::getLocale()]]);
    }
    // multi-lang for category
    public function Name($merchant_id = NULL)
    {
        $locale = App::getLocale();
        $category = $this->morphOne(LangName::class, 'dependable')->where([['merchant_id', '=', $merchant_id]])
        ->where(function ($q) use ($locale) {
            $q->where('locale', $locale);
         })->first();
        if (!empty($category->id)) {
            return $category->name;
        }
        else
        {
            $category = $this->morphOne(LangName::class, 'dependable')->where([['merchant_id', '=', $merchant_id]])
                ->where(function ($q) use ($locale) {
                 $q->where('locale', '!=', NULL);
                })->first();
            if (!empty($category->id)) {
                return $category->name;
            }
        }
        return "";
    }

}
