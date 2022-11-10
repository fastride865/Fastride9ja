<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationMerchantString extends Model
{
    protected $guarded = [];

    public function ApplicationString()
    {
        return $this->belongsTo(ApplicationString::class);
    }
}
