<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAccount extends Model
{
    protected $guarded = [];

    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function CreateBy()
    {
        return $this->hasOne(Merchant::class, 'id', 'create_by');
    }

    public function SettleBy()
    {
        return $this->hasOne(Merchant::class, 'id', 'settle_by');
    }
}
