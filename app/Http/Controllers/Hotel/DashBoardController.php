<?php

namespace App\Http\Controllers\Hotel;

use App\Models\Booking;
use App\Models\BookingRating;
use App\Models\CancelReason;
use App\Models\HotelWalletTransaction;
use App\Models\TaxiCompaniesWalletTransaction;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashBoardController extends Controller
{
    public function index()
    {
        $hotel = get_hotel();
        $curreny = isset($taxicompany->Country->isoCode) ? $hotel->Country->isoCode : 'INR';
        $booking = Booking::where([['hotel_id', '=', $hotel->id]])->count();
        $bookings = Booking::where([['hotel_id', '=', $hotel->id], ['booking_type', '=', 1]])->whereIn('booking_status', [1001, 1002, 1003, 1004])->paginate(25);
        $complete = Booking::where([['hotel_id', '=', $hotel->id], ['booking_closure', '=', 1]])->count();
        $activebookings = Booking::where([['hotel_id', '=', $hotel->id]])->whereIn('booking_status', [1001, 1002, 1003, 1004])->count();
        $cancelbookings = Booking::where([['hotel_id', '=', $hotel->id]])->whereIn('booking_status', [1006, 1007, 1008])->count();
        $cancelreasons = CancelReason::where([['merchant_id', '=', $hotel->merchant_id], ['reason_type', '=', 3]])->get();
        $earings = $curreny.' '.Booking::where([['hotel_id', '=', $hotel->id],['booking_closure', '=', 1], ['booking_status', '=', 1005]])->join('booking_transactions', 'bookings.id', '=', 'booking_transactions.booking_id')->sum('booking_transactions.hotel_earning');
        return view('hotel.home', compact('complete', 'activebookings', 'booking', 'bookings', 'earings', 'cancelreasons', 'cancelbookings'));
    }

    public function Ratings()
    {
        $hotel_id = get_hotel(true);
        $ratings = BookingRating::with(['Booking' => function ($query) {
            $query->with('Driver', 'User');
        }])->whereHas('Booking', function ($q) use ($hotel_id) {
            $q->where('hotel_id', $hotel_id);
        })->latest()->paginate(25);
        return view('hotel.random.ratings', compact('ratings'));
    }

    public function SearchRating(Request $request)
    {
        $hotel_id = get_hotel(true);;
        $query = BookingRating::with(['Booking' => function ($query) {
            $query->with('Driver', 'User');
        }])->whereHas('Booking', function ($q) use ($hotel_id) {
            $q->where('hotel_id', $hotel_id);
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
        return view('hotel.random.ratings', compact('ratings'));
    }

    public function Profile()
    {
        $hotel = get_hotel();
        return view('hotel.random.profile', compact('hotel'));
    }

    public function UpdateProfile(Request $request)
    {
        $hotel = get_hotel();
        $request->validate([
            'name' => 'required|alpha',
            'email' => ['required',
                Rule::unique('hotels')->where(function($query)use($hotel){
                    $query->where([['merchant_id','=',$hotel->merchant_id]]);
                })->ignore($hotel->id)
            ],
            'phone' => ['numeric','required',
                Rule::unique('hotels')->where(function($query)use($hotel){
                    $query->where([['merchant_id','=',$hotel->merchant_id]]);
                })->ignore($hotel->id)
            ],
            'password' => 'required_if:edit_password,1'
        ]);
        DB::beginTransaction();
        try {
            $hotel->name = $request->name;
            $hotel->email = $request->email;
            $hotel->phone = $request->phone;
            if ($request->edit_password == 1) {
                $password = Hash::make($request->password);
                $hotel->password = $password;
            }
            $hotel->save();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->back()->with('message181', trans('admin.message181'));
    }

    public function Wallet()
    {
        $hotel = get_hotel();
        $wallet_transactions = HotelWalletTransaction::where([['hotel_id', '=', $hotel->id]])->paginate(25);
        return view('hotel.random.wallet', compact('wallet_transactions', 'hotel'));
    }
}
