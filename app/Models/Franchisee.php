<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Franchisee extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }

    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
