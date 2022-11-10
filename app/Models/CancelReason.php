<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class CancelReason extends Model
{
    protected $guarded = [];
    protected $hidden = ['LanguageAny', 'LanguageSingle'];

    public function LanguageAny()
    {
        return $this->hasOne(LanguageCancelReason::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(LanguageCancelReason::class)->where([['locale', '=', App::getLocale()]]);
    }

    public function getReasonNameAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->reason;
        }
        return $this->LanguageSingle->reason;
    }

    public function getReasonDescriptionAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->description;
        }
        return $this->LanguageSingle->description;
    }

    public static function Reason($merchant_id = null, $type = null,$segment_id = null)
    {
        $query = CancelReason::select('id')->where([['merchant_id', '=', $merchant_id], ['reason_type', '=', $type], ['reason_status', '=', 1]]);
        if($segment_id != NULL){
            $query->where('segment_id',$segment_id);
        }
        $cancelReason = $query->get();
        foreach ($cancelReason as $value) {
            if (empty($value->LanguageSingle)) {
                $reason = $value->LanguageAny->reason;
            } else {
                $reason = $value->LanguageSingle->reason;
            }
            $value->reason = $reason;
        }
        return $cancelReason;
    }

    public function Segment()
    {
        return $this->belongsTo(Segment::class);
    }
}
