<?php

namespace App\Models;

use App\Models\BusinessSegment\Order;
use Illuminate\Database\Eloquent\Model;

class UserWalletTransaction extends Model
{
    protected $guarded = [];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function ActionMerchant()
    {
        return $this->belongsTo(Merchant::class, "action_merchant_id");
    }

    public function Booking()
    {
        return $this->belongsTo(Booking::class,'booking_id');
    }
    public function Order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
    public function HandymanOrder()
    {
        return $this->belongsTo(HandymanOrder::class,'handyman_order_id');
    }

    // wallet transfer details of user
    public function WalletTransfer()
    {
        return $this->belongsTo(User::class);
    }
}
