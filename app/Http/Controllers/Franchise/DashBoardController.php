<?php

namespace App\Http\Controllers\Franchise;

use App\Models\Booking;
use App\Models\BookingRating;
use App\Models\CancelReason;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    public function index()
    {
        $franchise_id = Auth::user('franchise')->id;
        $booking = Booking::where([['franchise_id', '=', $franchise_id]])->count();
        $bookings = Booking::where([['franchise_id', '=', $franchise_id], ['booking_type', '=', 1]])->whereIn('booking_status', [1001, 1002, 1003, 1004])->paginate(25);
        $earings = Booking::where([['franchise_id', '=', $franchise_id], ['booking_closure', '=', 1]])->sum('final_amount_paid');
        $complete = Booking::where([['franchise_id', '=', $franchise_id], ['booking_closure', '=', 1]])->count();
        $activebookings = Booking::where([['franchise_id', '=', $franchise_id]])->whereIn('booking_status', [1001, 1002, 1003, 1004])->count();
        $cancelbookings = Booking::where([['franchise_id', '=', $franchise_id]])->whereIn('booking_status', [1006, 1007, 1008])->count();
        $cancelreasons = CancelReason::where([['merchant_id', '=', Auth::user('franchise')->merchant_id], ['reason_type', '=', 3]])->get();
        return view('franchise.home', compact('complete', 'activebookings', 'booking', 'bookings', 'earings', 'cancelreasons', 'cancelbookings'));
    }

    public function Ratings()
    {
        $franchise_id = Auth::user('franchise')->id;
        $ratings = BookingRating::with(['Booking' => function ($query) {
            $query->with('Driver', 'User');
        }])->whereHas('Booking', function ($q) use ($franchise_id) {
            $q->where('franchise_id', $franchise_id);
        })->latest()->paginate(25);
        return view('franchise.random.ratings', compact('ratings'));
    }

    public function SearchRating(Request $request)
    {
        $franchise_id = Auth::user('franchise')->id;
        $query = BookingRating::with(['Booking' => function ($query) {
            $query->with('Driver', 'User');
        }])->whereHas('Booking', function ($q) use ($franchise_id) {
            $q->where('franchise_id', $franchise_id);
        })->latest();
        if ($request->booking_id) {
            $query->where('booking_id', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('Booking', function ($q) use ($keyword) {
                $q->WhereHas('User', function ($qu) use ($keyword) {
                    $qu->where('UserName', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
                });
            });
        }
        if ($request->driver) {
            $driverKeyword = $request->driver;
            $query->WhereHas('Booking', function ($q) use ($driverKeyword) {
                $q->WhereHas('Driver', function ($qu) use ($driverKeyword) {
                    $qu->where('fullName', 'LIKE', "%$driverKeyword%")->orWhere('email', 'LIKE', "%$driverKeyword%")->orWhere('phoneNumber', 'LIKE', "%$driverKeyword%");
                });
            });
        }
        $ratings = $query->paginate(25);
        return view('franchise.random.ratings', compact('ratings'));
    }
}
