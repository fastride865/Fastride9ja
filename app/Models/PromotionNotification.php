<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionNotification extends Model
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

    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
