<?php

namespace App\Models\BusinessSegment;

use App\Models\Merchant;
use Illuminate\Database\Eloquent\Model;

class BusinessSegmentWalletTransaction extends Model
{
    protected $guarded = [];

    public function BusinessSegment(){
        return $this->belongsTo(BusinessSegment::class);
    }

    public function ActionMerchant()
    {
        return $this->belongsTo(Merchant::class, "action_merchant_id");
    }
}
