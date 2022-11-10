<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelWalletTransaction extends Model
{
    //
    protected $guarded = [];
    protected $table = 'hotels_wallet_transaction';
    public function Hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
