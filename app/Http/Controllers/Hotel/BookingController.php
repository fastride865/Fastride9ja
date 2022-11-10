<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Helper\HolderController;
use App\Models\BookingConfiguration;
use App\Models\CancelReason;
use App\Models\Driver;
use App\Models\Onesignal;
use App\Traits\BookingTrait;
use App\Traits\MerchantTrait;
use Auth;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    use BookingTrait,MerchantTrait;
    public function index()
    {
        $hotel = get_hotel();
        $hotel_id = $hotel->id;
        $bookings = Booking::where([['hotel_id', '=', $hotel_id], ['booking_type', '=', 1]])->whereIn('booking_status', [1001, 1002, 1003, 1004])->paginate(25);
        $later_bookings = Booking::where([['hotel_id', '=', $hotel_id], ['booking_type', '=', 2]])->whereIn('booking_status', [1001, 1002, 1003, 1004])->paginate(25);
        $cancelreasons = CancelReason::where([['merchant_id', '=', $hotel->merchant_id], ['reason_type', '=', 3]])->get();
        $bookingConfig = BookingConfiguration::select('ride_otp')->where([['merchant_id','=',$hotel->merchant_id]])->first();
        return view('hotel.booking.active', compact('bookings', 'cancelreasons', 'later_bookings','bookingConfig'));
    }

    public function CancelBooking()
    {
        $hotel_id = get_hotel(true);
        $bookings = Booking::where([['hotel_id', '=', $hotel_id]])->whereIn('booking_status', [1006, 1007, 1008])->paginate(25);
        return view('hotel.booking.cancel', compact('bookings'));
    }

    public function CompleteBooking()
    {
        $hotel_id = get_hotel(true);
        $bookings = Booking::where([['hotel_id', '=', $hotel_id]])->whereIn('booking_status', [1005])->paginate(25);
        return view('hotel.booking.complete', compact('bookings'));
    }


    public function DriverRequest($id)
    {
        $hotel = get_hotel();
        $merchant_id = $hotel->merchant_id;
        $booking = Booking::with(['BookingRequestDriver' => function ($query) {
            $query->with('Driver');
        }])->where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        return view('hotel.booking.request', compact('booking'));
    }

    public function BookingDetails(Request $request, $id)
    {
        $hotel_id = get_hotel(true);
        $booking = Booking::with('User')->where([['hotel_id', '=', $hotel_id]])->findOrFail($id);
        $booking->map_image = $booking->map_image . "&zoom=12&size=600x300";
        return view('hotel.booking.detail', compact('booking'));
    }

    public function Invoice(Request $request, $id)
    {
        $hotel = get_hotel();
        $merchant_id = $hotel->merchant_id;
        $booking = Booking::with('User', 'BookingDetail')->where([['hotel_id','=',$hotel->id],['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $price = json_decode($booking->bill_details,true);
        $holder = HolderController::PriceDetailHolder($price, $booking->id);
        array_shift($holder);
//        p($holder);
        $booking->holder = $holder;
        $booking->map_image = $booking->map_image . "&zoom=12&size=600x300";
        return view('hotel.booking.invoice', compact('booking'));
    }

    public function bookingInvoiceSend(Request $request, $id)
    {
        $hotel = get_hotel();
        $merchant_id = $hotel->merchant_id;
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

    public function AllRides()
    {
        $hotel = get_hotel();
        $query = Booking::where([['merchant_id', '=', $hotel->merchant_id],['hotel_id','=',$hotel->id]])->whereIn('booking_status', [1001, 1012, 1002, 1003, 1004, 1005, 1006, 1007, 1008, 1016]);
        $bookings = $query->latest()->paginate(25);
        $data = [];
        $merchant_id = $hotel->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $arr_status = $this->getBookingStatus($string_file);
        return view('hotel.booking.all-ride', compact('bookings','data','arr_status'));
    }

    public function SearchForAllRides(Request $request)
    {
        $hotel = get_hotel();
        $query = Booking::where([['merchant_id', '=', $hotel->merchant_id],['hotel_id','=',$hotel->id]])->whereIn('booking_status', [1001, 1012, 1002, 1003, 1004, 1005, 1006, 1007, 1008, 1016]);
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
        $cancelreasons = CancelReason::where([['merchant_id', '=', $hotel->merchant_id], ['reason_type', '=', 3]])->get();
        $bookings = $query->paginate(25);
        $data = $request->all();
        return view('hotel.booking.all-ride', compact('bookings','data','cancelreasons'));
    }

    public function CancelBookingAdmin(Request $request)
    {
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
            Onesignal::DriverPushMessage($driver_id, $data, $message, 1, $booking->merchant_id);
        }
        return redirect()->route('hotel.activeride')->with('ridecancel', 'Ride Cancel Dispatcher');
    }

    public function SearchCancelBooking(Request $request)
    {
        $hotel = get_hotel();
        $query = Booking::where([['merchant_id', '=', $hotel->merchant_id],['hotel_id','=',$hotel->id]])->whereIn('booking_status',[1006, 1007, 1008]);
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
        $data = $request->all();
        return view('hotel.booking.cancel', compact('bookings','data'));
    }

    public function SearchForActiveRide(Request $request)
    {
        $hotel = get_hotel();
        $merchant_id = $hotel->merchant_id;
        $query = $this->ActiveBookingNow(false,'HOTEL');
        if (request()->booking_id) {
            $query->where('merchant_booking_id', request()->booking_id);
        }
        if (request()->booking_status) {
            $query->where('booking_status', request()->booking_status);
        }
        if (request()->rider) {
            $keyword = request()->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if (request()->driver) {
            $keyword = request()->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $bookings = $query->paginate(25);
        $later_bookings = $this->ActiveBookingLater(true,'HOTEL');
        $cancelreasons = $this->CancelReason();
        $bookingConfig = BookingConfiguration::select('ride_otp')->where([['merchant_id', '=', $merchant_id]])->first();
        $data_later = [];
        $data = request()->all();
        $data['merchant_id'] = $merchant_id;
        return view('hotel.booking.active', compact('bookingConfig', 'bookings', 'cancelreasons', 'later_bookings','data_later','data'));
    }

    public function SearchForActiveLaterRide(Request $request)
    {
        $hotel = get_hotel();
        $merchant_id = $hotel->merchant_id;
        $query = $this->ActiveBookingLater(false,'HOTEL');
        if (request()->booking_id) {
            $query->where('merchant_booking_id', request()->booking_id);
        }
        if (request()->rider) {
            $keyword = request()->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if (request()->driver) {
            $keyword = request()->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $later_bookings = $query->paginate(25);
        $bookings = $this->ActiveBookingNow(true,'HOTEL');
        $cancelreasons = $this->CancelReason();
        $bookingConfig = BookingConfiguration::select('ride_otp')->where([['merchant_id', '=', $merchant_id]])->first();
        $data = [];
        $data_later = request()->all();
        $data_later['merchant_id'] = $merchant_id;
        return view('hotel.booking.active', compact('bookingConfig', 'bookings', 'cancelreasons', 'later_bookings','data_later','data'));
    }

    public function SerachCompleteBooking(Request $request)
    {
        $query = $this->bookings(false, [1005],'HOTEL');
        if ($request->booking_id) {
            $query->where('merchant_booking_id', $request->booking_id);
        }
        if ($request->date) {
            $query->whereDate('created_at', '>=', $request->date);
        }
        if ($request->date1) {
            $query->whereDate('created_at', '<=', $request->date1);
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
        $data = $request->all();
        return view('hotel.booking.complete', compact('bookings','data'));
    }
}
