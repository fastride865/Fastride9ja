<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class HandymanChargeType extends Model
{
    protected $guarded = [];
    protected $hidden = ['LanguageAny', 'LanguageSingle'];

    public function LanguageAny()
    {
        return $this->hasOne(LanguageHandymanChargeType::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(LanguageHandymanChargeType::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getChargeTypeAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->charge_type;
        }
        return $this->LanguageSingle->charge_type;
    }

    public function getChargeDescriptionAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->charge_description;
        }
        return $this->LanguageSingle->charge_description;
    }

    public static function ChargeType($merchant_id = null,$segment_id = null)
    {
        $query = HandymanChargeType::select('id','maximum_amount')->where([['merchant_id', '=', $merchant_id],['segment_id', '=', $segment_id], ['status', '=', 1]]);
//        if($segment_id != NULL){
//            $query->where('segment_id',$segment_id);
//        }
        $HandymanChargeType = $query->get();
        foreach ($HandymanChargeType as $value) {
            if (empty($value->LanguageSingle)) {
                $charge_type = $value->LanguageAny->charge_type;
            } else {
                $charge_type = $value->LanguageSingle->charge_type;
            }
            $value->charge_type = $charge_type;
        }
        return $HandymanChargeType;
    }

    public function Segment()
    {
        return $this->belongsTo(Segment::class);
    }
}
