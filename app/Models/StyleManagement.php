<?php

namespace App\Models;

use App\Models\BusinessSegment\BusinessSegment;
use Illuminate\Database\Eloquent\Model;
use App;
class StyleManagement extends Model
{
    protected $guarded =[];
    protected $table = "style_managements";

    public  function  BusinessSegment(){
     return $this->belongsToMany(BusinessSegment::class,'business_segment_style_management');
 }

    public function LangStyle()
    {
        return $this->morphMany(LangName::class, 'dependable');
    }
    public function LangStyleSingle()
    {
        return $this->morphOne(LangName::class, 'dependable')->where([['locale', '=', \App::getLocale()]]);
    }
 // multi lang for style
    public function Name($merchant_id = NULL)
    {
        $locale = App::getLocale();
        $style = $this->morphOne(LangName::class, 'dependable')->where([['merchant_id', '=', $merchant_id]])
            ->where(function ($q) use ($locale) {
                $q->where('locale', $locale);
            })->first();
        if (!empty($style->id)) {
            return $style->name;
        }
        else
        {
            $style = $this->morphOne(LangName::class, 'dependable')->where([['merchant_id', '=', $merchant_id]])
                ->where(function ($q) use ($locale) {
                    $q->where('locale', '!=', NULL);
                })->first();
            if (!empty($style->id)) {
                return $style->name;
            }
        }
        return "";
    }
}
