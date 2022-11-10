<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Helper\HolderController;
use App\Models\FailBooking;
use App\Models\InfoSetting;
use App\Models\Onesignal;
use Auth;
use App\Models\Driver;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\BookingConfiguration;
use App\Traits\DeliveryBookingTrait;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Events\SendUserInvoiceMailEvent;

class DeliveryBookingController extends Controller
{
    use DeliveryBookingTrait;

    public function index()
    {
        $checkPermission =  check_permission(1,'active_ride');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $bookings = $this->ActiveBookingNow();
        $later_bookings = $this->ActiveBookingLater();
        $cancelreasons = $this->CancelReason();
        $bookingConfig = BookingConfiguration::select('ride_otp')->where([['merchant_id','=',$merchant_id]])->first();
        return view('merchant.booking.active', compact('bookings', 'cancelreasons', 'later_bookings','bookingConfig'));
    }

    public function AutoCancel()
    {
        $bookings = $this->bookings(true, [1016]);
        return view('merchant.booking.auto-cancel', compact('bookings'));
    }

    public function SearchForAutoCancel(Request $request)
    {
        $query = $this->bookings(false, [1016]);
        if ($request->booking_id) {
            $query->where('id', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $bookings = $query->paginate(25);
        return view('merchant.booking.auto-cancel', compact('bookings'));
    }

    public function AllRides()
    {
        $bookings = $this->bookings(true, [1001, 1012, 1002, 1003, 1004, 1005, 1006, 1007, 1008, 1016]);
        return view('merchant.booking.all-ride', compact('bookings'));
    }

    public function SearchForAllRides(Request $request)
    {
        $query = $this->bookings(false, [1001, 1012, 1002, 1003, 1004, 1005, 1006, 1007, 1008, 1016]);
        if ($request->booking_id) {
            $query->where('id', $request->booking_id);
        }
        if ($request->booking_status) {
            $query->where('booking_status', $request->booking_status);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $bookings = $query->paginate(25);
        return view('merchant.booking.all-ride', compact('bookings'));
    }

    public function CancelBooking()
    {
        $checkPermission =  check_permission(1,'canceled_ride');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $bookings = $this->bookings(true, [1006, 1007, 1008]);
        return view('merchant.booking.cancel', compact('bookings'));
    }

    public function SearchCancelBooking(Request $request)
    {
        $query = $this->bookings(false, [1006, 1007, 1008]);
        if ($request->booking_id) {
            $query->where('id', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $bookings = $query->paginate(25);
        return view('merchant.booking.cancel', compact('bookings'));
    }

    public function CompleteBooking()
    {
        $checkPermission =  check_permission(1,'completed_ride');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $bookings = $this->bookings(true, [1005]);
        return view('merchant.booking.complete', compact('bookings'));
    }

    public function SerachCompleteBooking(Request $request)
    {
        $query = $this->bookings(false, [1005]);
        if ($request->booking_id) {
            $query->where('id', $request->booking_id);
        }
        if ($request->date) {
            $query->whereDate('created_at', '=', $request->date);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $bookings = $query->paginate(25);
        return view('merchant.booking.complete', compact('bookings'));
    }

    public function FailedBooking()
    {
        $checkPermission =  check_permission(1,'failed_ride');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $bookings = $this->failsBookings();
        return view('merchant.booking.fail', compact('bookings'));
    }

    public function SearchFailedBooking(Request $request)
    {
        $query = $this->failsBookings(false);
        if ($request->booking_id) {
            $query->where('id', $request->booking_id);
        }
        if ($request->date) {
            $query->whereDate('created_at', '=', $request->date);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        $bookings = $query->paginate(25);
        return view('merchant.booking.fail', compact('bookings'));
    }

    public function CancelBookingAdmin(Request $request)
    {
        $checkPermission =  check_permission(1,'ride_cancel_dispatch');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $request->validate([
            'booking_id' => [
                'required',
                'integer',
                Rule::exists('bookings', 'id')->where(function ($query) {
                    $query->whereIn('booking_status', [1001, 1012, 1002, 1003, 1004]);
                }),
            ],
            'cancel_reason_id' => 'required|integer',
        ]);
        $booking = Booking::find($request->booking_id);
        $booking->cancel_reason_id = $request->cancel_reason_id;
        $booking->additional_notes = $request->description;
        $booking->booking_status = 1008;
        $booking->save();
        $user_id = $booking->user_id;
//        $userDevice = UserDevice::where([['user_id', '=', $user_id]])->get();
        $message = "Booking Cancel By Dispatcher";
        $data = array('booking_id' => $booking->id, 'booking_status' => $booking->booking_status);
//        $playerids = array_pluck($userDevice, 'player_id');
        Onesignal::UserPushMessage($user_id, $data, $message, 1, $booking->merchant_id);
        $driver_id = $booking->driver_id;
        if (!empty($driver_id)) {
            $Driver = Driver::where([['id', '=', $driver_id]])->first();
            $Driver->free_busy = 2;
            $Driver->save();
            $data = array('booking_id' => $booking->id, 'booking_status' => $booking->booking_status);
//            $playerids = array($Driver->player_id);
            Onesignal::DriverPushMessage($Driver->id, $data, $message, 1, $booking->merchant_id);
        }
        return redirect()->route('merchant.activeride')->with('ridecancel', 'Ride Cancel Dispatcher');
    }

    public function requestRides($id)
    {
        $booking = Booking::find($id);
        $time = BookingConfiguration::where([['merchant_id', $booking->merchant_id]])->first();
        $time = $time->driver_request_timeout * 100 / 3;
        return view('merchant.manual.loader', compact('time', 'id'));
    }

    public function checkBookingStatusWaiting(Request $request)
    {
        $booking = Booking::find($request->booking_id);
        if (!empty($booking)) {
            if ($booking->booking_status == 1002) {
                return redirect()->route('merchant.ride-requests', $request->booking_id);
            } else {
                $time = BookingConfiguration::where([['merchant_id', $booking->merchant_id]])->first();
                $time = ($time->driver_request_timeout * 1000) / 60;
                $id = $request->booking_id;
                $time_check = session('timer_no');
                $time_check = $time_check + 1;
                $request->session()->put('timer_no', $time_check);
                $request->session()->save();
                if ($time_check == 4) {
                    $request->session()->put('timer_no', 0);
                    $request->session()->save();
                    return redirect()->route('merchant.ride-requests', $request->booking_id)->with('success', 'NO Drivers Accepted');
                } else {
                    return view('merchant.manual.loader', compact('time', 'id'));
                }
            }
        }
    }

    public function DriverRequest($id)
    {
        $booking = Booking::with(['BookingRequestDriver' => function ($query) {
            $query->with('Driver');
        }])->findOrFail($id);
        return view('merchant.booking.request', compact('booking'));
    }

    public function BookingDetails(Request $request, $id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $booking = Booking::with('User')
              ->where([
                ['merchant_id', '=', $merchant_id],
                ['service_type_id' , '!=' , null ],
                ['service_type_id' , '!=' , 0]
              ])
              ->findOrFail($id);
        $booking->map_image = $booking->map_image . "&zoom=12&size=600x300";
        return view('merchant.booking.detail', compact('booking'));
    }

    public function Invoice(Request $request, $id)
    {
        $merchant_id = Auth::user()->id;
        $booking = Booking::with('User', 'BookingDetail')->where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $price = json_decode($booking->BookingDetail->bill_details);
        $holder = HolderController::PriceDetailHolder($price, $booking->id);
        array_shift($holder);
        $booking->holder = $holder;
        return view('merchant.booking.invoice', compact('booking'));
    }

    public function bookingInvoiceSend(Request $request, $id)
    {
        $booking = Booking::where([['id', '=', $id]])->first();
        event(new SendUserInvoiceMailEvent($booking, 'invoice'));
        session()->flash('msg', 'Invoice sent successfully!!');
        return redirect()->back();
    }

}
