<?php

namespace App\Http\Controllers\Franchise;

use App\Models\CancelReason;
use Auth;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    public function index()
    {
        $franchise_id = Auth::user('franchise')->id;
        $bookings = Booking::where([['franchise_id', '=', $franchise_id], ['booking_type', '=', 1]])->whereIn('booking_status', [1001, 1002, 1003, 1004])->paginate(25);
        $later_bookings = Booking::where([['franchise_id', '=', $franchise_id], ['booking_type', '=', 2]])->whereIn('booking_status', [1001, 1002, 1003, 1004])->paginate(25);
        $cancelreasons = CancelReason::where([['merchant_id', '=', Auth::user('franchise')->merchant_id], ['reason_type', '=', 3]])->get();
        return view('franchise.booking.active', compact('bookings', 'cancelreasons', 'later_bookings'));
    }

    public function CancelBooking()
    {
        $franchise_id = Auth::user('franchise')->id;
        $bookings = Booking::where([['franchise_id', '=', $franchise_id]])->whereIn('booking_status', [1006, 1007, 1008])->paginate(25);
        return view('franchise.booking.cancel', compact('bookings'));
    }

    public function CompleteBooking()
    {
        $franchise_id = Auth::user('franchise')->id;
        $bookings = Booking::where([['franchise_id', '=', $franchise_id]])->whereIn('booking_status', [1005])->paginate(25);
        return view('franchise.booking.complete', compact('bookings'));
    }


    public function DriverRequest($id)
    {
        $franchise_id = Auth::user('franchise')->id;
        $booking = Booking::with(['BookingRequestDriver' => function ($query) {
            $query->with('Driver');
        }])->where([['franchise_id', '=', $franchise_id]])->findOrFail($id);
        return view('franchise.booking.request', compact('booking'));
    }

    public function BookingDetails(Request $request, $id)
    {
        $franchise_id = Auth::user('franchise')->id;
        $booking = Booking::with('User')->where([['franchise_id', '=', $franchise_id]])->findOrFail($id);
        $booking->map_image = $booking->map_image . "&zoom=12&size=600x300";
        return view('franchise.booking.detail', compact('booking'));
    }
}
