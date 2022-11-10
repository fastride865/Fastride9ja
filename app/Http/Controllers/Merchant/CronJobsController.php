<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\DriverSettlement;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CronJobsController extends Controller
{


    public function DriverSettleMent()
    {
        $merchants = Merchant::get();
        foreach ($merchants as $merchant) {
            $drivers = Driver::where([['merchant_id', '=', $merchant->id]])->latest()->get();
            foreach ($drivers as $value) {
                if (!empty($value->CountryArea->BillPeriod)):
                    $period = $value->CountryArea->BillPeriod; //Data From BillPeriodCountryArea Model
                    $timeZone = $value->CountryArea->timezone;
                    if (date_default_timezone_get() != $timeZone) {
                        date_default_timezone_set($timeZone);
                    }
                    $timePeriod = $this->getStartAndEnd($period->bill_period_id, $period->bill_period_start);
                    $bookings = Booking::whereHas('BookingDetail', function ($query) use ($timePeriod) {
                        $query->whereBetween('end_timestamp', [$timePeriod['from'], $timePeriod['to']]);
                    })->where([['driver_id', '=', $value->id], ['booking_status', '=', 1005], ['settlement', '=', NULL]])->oldest()->get();
                    if (!empty($bookings->toArray())) {
                        $numberFormat = new \App\Http\Controllers\Helper\Merchant();
                        $totalTripAmount = $numberFormat->TripCalculation(array_sum(array_pluck($bookings, 'final_amount_paid')), $value->merchant_id);
                        $company_cut = $numberFormat->TripCalculation(array_sum(array_pluck($bookings, 'company_cut')), $value->merchant_id);
                        $driver_cut = $numberFormat->TripCalculation(array_sum(array_pluck($bookings, 'driver_cut')), $value->merchant_id);
                        $cashCollected = $numberFormat->TripCalculation(array_sum(array_pluck($bookings->whereIn('payment_method_id', [1, 5]), 'final_amount_paid')), $value->merchant_id);
                        $finalOustatnd = $numberFormat->TripCalculation($totalTripAmount - $company_cut - $cashCollected, $value->merchant_id);
                        $booking_id = $bookings->first();
                        $last_booking_id = $bookings->last();
                        DriverSettlement::create([
                            'driver_id' => $value->id,
                            'total_trips' => count($bookings->toArray()),
                            'total_trip_amount' => $totalTripAmount,
                            'company_cut' => $company_cut,
                            'driver_cut' => $driver_cut,
                            'cash_collect' => $cashCollected,
                            'final_outstanding' => $finalOustatnd,
                            'bill_method_type' => $period->bill_period_id,
                            'bill_from' => $timePeriod['from'],
                            'bill_to' => $timePeriod['to'],
                            'booking_slot' => $booking_id->id . '-' . $last_booking_id->id,
                            'timezone' => $timeZone,
                        ]);
                        Booking::whereIn('id', array_pluck($bookings, 'id'))->update(['settlement' => 1]);
                    }
                endif;
            }
        }
    }

    public function getStartAndEnd($id, $time)
    {
        switch ($id) {
            case "1":
                $start = strtotime($time);
                $end = strtotime('+1 day', $start);
                break;
            case "2":
                $start = strtotime($time . ' this week');
                $end = strtotime('+7 day', $start);
                break;
            case "3":
                $start = strtotime(date('Y-m-' . $time));
                $end = strtotime('+1 month', $start);
                break;
        }
        return [
            'from' => $start,
            'to' => $end
        ];
    }

    public function ExpireOldBookings()
    {
        $all_bookings = Booking::whereHas('Merchant', function ($q) {
            $q->whereHas('BookingConfiguration', function ($query) {
                $query->where('auto_cancel_expired_rides', 1);
            });
        })->whereDate('later_booking_date', '<', date('Y-m-d'))->where([['booking_type', 2]])->whereIn('booking_status', [1001, 1012])->get();
        
        if ($all_bookings->isNotEmpty()):
            $booking_ids = $all_bookings->map(function ($item, $key) {
                date_default_timezone_set($item->CountryArea['timezone']);
                $now = new \DateTime();
                $booking_time = new \DateTime($item->later_booking_date . ' ' . $item->later_booking_time);
                return ($now > $booking_time) ? $item->id : null;
            })->filter()->values();
            if ($booking_ids->isNotEmpty()):
                Booking::whereIn('id', $booking_ids->toArray())
                    ->update([
                        'booking_status' => '1016', // Add as many as you need
                    ]);
            endif;
        endif;
    }
}

