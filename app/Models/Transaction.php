<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
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

    public function Booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function PaymentOption()
    {
        return $this->belongsTo(PaymentOption::class);
    }
}
