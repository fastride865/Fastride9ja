<?php

namespace App\Traits;

use App\Models\BookingConfiguration;
use App\Models\CancelReason;
use App\Models\FailBooking;
use Auth;
use App\Models\Booking;

trait DeliveryBookingTrait
{
  public function ActiveBookingNow($pagination = true)
  {
    $merchant = Auth::user('merchant')->load('CountryArea');
    $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
    $query = Booking::where([
      ['merchant_id', '=', $merchant_id],
      ['booking_type', '=', 1],
      ['delivery_type_id' , '!=' , null],
      ['delivery_type_id' , '!=' , 0]
    ])
      ->whereIn('booking_status', [1001, 1002, 1003, 1004])
      ->latest();
    if (!empty($merchant->CountryArea->toArray())) {
      $area_ids = array_pluck($merchant->CountryArea, 'id');
      $query->whereIn('country_area_id', $area_ids);
    }
    $activeBooking = $pagination == true ? $query->paginate(25) : $query;
    return $activeBooking;
  }

  public function ActiveBookingLater($pagination = true)
  {
    $merchant = Auth::user('merchant')->load('CountryArea');
    $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
    $query = Booking::where([
      ['merchant_id', '=', $merchant_id],
      ['booking_type', '=', 2],
      ['delivery_type_id' , '!=' , null],
      ['delivery_type_id' , '!=' , 0]
    ])
      ->whereIn('booking_status', [1001, 1012, 1002, 1003, 1004])
      ->latest();
    if (!empty($merchant->CountryArea->toArray())) {
      $area_ids = array_pluck($merchant->CountryArea, 'id');
      $query->whereIn('country_area_id', $area_ids);
    }
    $activeBooking = $pagination == true ? $query->paginate(25) : $query;
    return $activeBooking;
  }

  public function SearchForActiveRide()
  {
    $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
    $query = $this->ActiveBookingNow(false);
    if (request()->booking_id) {
      $query->where('id', request()->booking_id);
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
    $later_bookings = $this->ActiveBookingLater();
    $cancelreasons = $this->CancelReason();
    $bookingConfig = BookingConfiguration::select('ride_otp')->where([['merchant_id', '=', $merchant_id]])->first();
    return view('merchant.booking.active', compact('bookingConfig', 'bookings', 'cancelreasons', 'later_bookings'));
  }

  public function SearchForActiveLaterRide()
  {
    $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
    $query = $this->ActiveBookingLater(false);
    if (request()->booking_id) {
      $query->where('id', request()->booking_id);
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
    $bookings = $this->ActiveBookingNow();
    $cancelreasons = $this->CancelReason();
    $bookingConfig = BookingConfiguration::select('ride_otp')->where([['merchant_id', '=', $merchant_id]])->first();
    return view('merchant.booking.active', compact('bookingConfig', 'bookings', 'cancelreasons', 'later_bookings'));
  }

  public function CancelReason()
  {
    $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
    $cancelreasons = CancelReason::where([['merchant_id', '=', $merchant_id], ['reason_type', '=', 3]])->get();
    return $cancelreasons;
  }

  public function bookings($pagination = true, $booking_status = [])
  {
    $merchant = Auth::user('merchant')->load('CountryArea');
    $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
    $query = Booking::where([
      ['merchant_id', '=', $merchant_id],
      ['delivery_type_id' , '!=' , null],
      ['delivery_type_id' , '!=' , 0]
    ])
      ->whereIn('booking_status', $booking_status)
      ->latest();
    if (!empty($merchant->CountryArea->toArray())) {
      $area_ids = array_pluck($merchant->CountryArea, 'id');
      $query->whereIn('country_area_id', $area_ids);
    }
    $booking = $pagination == true ? $query->paginate(25) : $query;
    return $booking;
  }

  public function failsBookings($pagination = true)
  {
    $merchant = Auth::user('merchant')->load('CountryArea');
    $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
    $query = FailBooking::where([['merchant_id', '=', $merchant_id]])->latest();
    if (!empty($merchant->CountryArea->toArray())) {
      $area_ids = array_pluck($merchant->CountryArea, 'id');
      $query->whereIn('country_area_id', $area_ids);
    }
    $booking = $pagination == true ? $query->paginate(25) : $query;
    return $booking;
  }

  public function autoCancelRide()
  {
    $bookings = $this->bookings(false, [1016]);
    return $bookings;
  }

  public function getAllTransaction($pagination = true)
  {
    $merchant = Auth::user('merchant')->load('CountryArea');
    $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
    $query = Booking::where([
      ['merchant_id', '=', $merchant_id],
      ['booking_closure', '=', 1],
      ['delivery_type_id' , '!=' , null],
      ['delivery_type_id' , '!=' , 0]
    ])
      ->latest();
    if (!empty($merchant->CountryArea->toArray())) {
      $area_ids = array_pluck($merchant->CountryArea, 'id');
      $query->whereIn('country_area_id', $area_ids);
    }
    $transactions = $pagination == true ? $query->paginate(25) : $query;
    return $transactions;
  }

  public function allBookings($pagination = true)
  {
    $merchant = Auth::user('merchant')->load('CountryArea');
    $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
    $query = Booking::where([
      ['merchant_id', '=', $merchant_id],
      ['delivery_type_id' , '!=' , null],
      ['delivery_type_id' , '!=' , 0]
    ])
      ->latest();
    if (!empty($merchant->CountryArea->toArray())) {
      $area_ids = array_pluck($merchant->CountryArea, 'id');
      $query->whereIn('country_area_id', $area_ids);
    }
    $result = $pagination == true ? $query->paginate(25) : $query;
    return $result;
  }
}
