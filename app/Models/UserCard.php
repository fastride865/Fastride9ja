<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{
    protected $guarded = [];
    
    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function PaymentOption()
    {
        return $this->belongsTo(PaymentOption::class);
    }
}