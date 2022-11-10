<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function stepOne(){
        $merchant = get_merchant_id(false);
        return view('merchant.order.place-order.step-one',compact('merchant'));
    }
}
