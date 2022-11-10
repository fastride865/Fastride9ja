<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RideShareController extends Controller
{
    public function index(Request $request, $code)
    {
        $booking = Booking::where([['unique_id', '=', $code]])->first();
        if (empty($booking)) {
            return "Sorry No Record";
        }
        if ($booking->booking_closure == 1 || !in_array($booking->booking_status, array(1004, 1005))) {
            return "Sorry!! Ride Data Not Available";
        }
        return view('map', compact('booking'));
    }
}
