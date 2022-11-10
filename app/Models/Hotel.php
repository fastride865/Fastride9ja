<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Hotel extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];

    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function AccountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function Country()
    {
        return $this->belongsTo(Country::class);
    }
}
