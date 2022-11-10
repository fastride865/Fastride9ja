<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BusinessSegment\Order;

class DriverWalletTransaction extends Model
{
    protected $guarded = [];

    public function Driver()
    {
        return $this->belongsTo(Driver::class);
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
}
