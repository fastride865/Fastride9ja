<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Helper\HolderController;
use App\Models\CancelReason;
use App\Models\EmailConfig;
use App\Models\EmailTemplate;
use App\Models\FailBooking;
use App\Models\Onesignal;
use Auth;
use App\Models\Driver;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\BookingConfiguration;
use App\Traits\BookingTrait;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Events\SendUserInvoiceMailEvent;

class BookingController extends Controller
{
    use BookingTrait;

    public function index()
    {
        $corporate = Auth::user('corporate');
        $bookings = $this->ActiveBookingNow(true,'CORPORATE');
        $later_bookings = $this->ActiveBookingLater(true,'CORPORATE');
        $cancelreasons = $this->CancelReason('CORPORATE');
        $bookingConfig = BookingConfiguration::select('ride_otp')->where([['merchant_id','=',$corporate->merchant_id]])->first();
        return view('corporate.booking.active', compact('bookings', 'cancelreasons', 'later_bookings','bookingConfig'));
    }

    public function AutoCancel()
    {
        $bookings = $this->bookings(true, [1016],'CORPORATE');
        return view('corporate.booking.auto-cancel', compact('bookings'));
    }

    public function SearchForAutoCancel(Request $request)
    {
        $query = $this->bookings(false, [1016],'CORPORATE');
        if ($request->booking_id) {
            $query->where('merchant_booking_id', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->date) {
            $query->whereDate('created_at', '=', $request->date);
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $bookings = $query->paginate(25);
        return view('corporate.booking.auto-cancel', compact('bookings'));
    }

    public function AllRides()
    {
        $bookings = $this->bookings(true, [1001, 1012, 1002, 1003, 1004, 1005, 1006, 1007, 1008, 1016],'CORPORATE');
        return view('corporate.booking.all-ride', compact('bookings'));
    }

    public function SearchForAllRides(Request $request)
    {
        $query = $this->bookings(false, [1001, 1012, 1002, 1003, 1004, 1005, 1006, 1007, 1008, 1016],'CORPORATE');
        if ($request->booking_id) {
            $query->where('merchant_booking_id', $request->booking_id);
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
        if ($request->date) {
            $query->whereDate('created_at', '=', $request->date);
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $bookings = $query->paginate(25);
        return view('corporate.booking.all-ride', compact('bookings'));
    }

    public function CancelBooking()
    {
        $bookings = $this->bookings(true, [1006, 1007, 1008],'CORPORATE');
        return view('corporate.booking.cancel', compact('bookings'));
    }

    public function SearchCancelBooking(Request $request)
    {
        $query = $this->bookings(false, [1006, 1007, 1008],'CORPORATE');
        if ($request->booking_id) {
            $query->where('merchant_booking_id', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->date) {
            $query->whereDate('created_at', '=', $request->date);
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $bookings = $query->paginate(25);
        return view('corporate.booking.cancel', compact('bookings'));
    }

    public function CompleteBooking()
    {
        $bookings = $this->bookings(true, [1005],'CORPORATE');
        return view('corporate.booking.complete', compact('bookings'));
    }

    public function SerachCompleteBooking(Request $request)
    {
        $query = $this->bookings(false, [1005],'CORPORATE');
        if ($request->booking_id) {
            $query->where('merchant_booking_id', $request->booking_id);
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
        return view('corporate.booking.complete', compact('bookings'));
    }

    public function FailedBooking()
    {
        $bookings = $this->failsBookings(true,'CORPORATE');
        return view('corporate.booking.fail', compact('bookings'));
    }

    public function SearchFailedBooking(Request $request)
    {
        $query = $this->failsBookings(false,'CORPORATE');
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
        return view('corporate.booking.fail', compact('bookings'));
    }

    public function CancelBookingAdmin(Request $request)
    {
        if (!Auth::user('merchant')->can('ride_cancel_dispatch')) {
            abort(404, 'Unauthorized action.');
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
        $message = trans('api.ride_cancelled_dispatcher');
        $data = array('booking_id' => $booking->id, 'booking_status' => $booking->booking_status);
        Onesignal::UserPushMessage($user_id, $data, $message, 1, $booking->merchant_id);
        $driver_id = $booking->driver_id;
        if (!empty($driver_id)) {
            $Driver = Driver::where([['id', '=', $driver_id]])->first();
            $Driver->free_busy = 2;
            $Driver->save();
            $data = array('booking_id' => $booking->id, 'booking_status' => $booking->booking_status);
            Onesignal::DriverPushMessage($Driver->id, $data, $message, 1, $booking->merchant_id);
        }
        return redirect()->route('corporate.activeride')->with('ridecancel', 'Ride Cancel Dispatcher');
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
        }])->with('OneSignalLog')->findOrFail($id);
        return view('corporate.booking.request', compact('booking'));
    }

    public function BookingDetails(Request $request, $id)
    {
        $corporate = Auth::user('corporate');
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $booking = Booking::with('User')->where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $booking->map_image = $booking->map_image . "&zoom=12&size=600x300";
        return view('corporate.booking.detail', compact('booking'));
    }

    public function Invoice(Request $request, $id)
    {
        $merchant_id = Auth::user()->merchant_id;
        $booking = Booking::with('User', 'BookingDetail')->where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $price = json_decode($booking->BookingDetail->bill_details);
        $holder = HolderController::PriceDetailHolder($price, $booking->id);
        array_shift($holder);
        $booking->holder = $holder;
        $booking->map_image = $booking->map_image . "&zoom=12&size=600x300";
        return view('corporate.booking.invoice', compact('booking'));
    }

    public function bookingInvoiceSend(Request $request, $id)
    {
        $booking = Booking::where([['id', '=', $id]])->first();
        $template = EmailConfig::where('merchant_id', '=', $booking->Merchant->id)->first();
        $temp = EmailTemplate::where('merchant_id', '=', $booking->Merchant->id)->where('template_name', '=', 'welcome')->first();
        if (!empty($template) && !empty($temp)){
            event(new SendUserInvoiceMailEvent($booking, 'invoice'));
            return redirect()->back()->with('success',trans('admin.invoice_send'));
        }else{
            return redirect()->back()->with('error',trans('admin.invoice_send_err'));
        }

    }
    
    public function ActiveBookingTrack($id){
        $booking = Booking::where([['id', '=', $id]])->first();
        return view('corporate.booking.track', compact('booking'));
    }
}
