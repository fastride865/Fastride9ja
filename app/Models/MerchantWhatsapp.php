<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantWhatsapp extends Model
{
    protected $guarded = [];

    public function Merchant(){
        $this->belongsTo(Merchant::class);
    }
}
