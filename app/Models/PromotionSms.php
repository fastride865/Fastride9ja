<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionSms extends Model
{
    protected $guarded = [];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }
}
