<?php

namespace App\Http\Controllers\Helper;


use App\Models\Booking;
use App\Models\ExtraCharge;
use App\Models\PriceCard;
use DateTime;

class ExtraCharges
{
    public function nightchargeEstimate($price_card_id = null, $booking_id = null, $booking_amount = null, $booking_date = null, $booking_time = null)
    {
        $priceCard = PriceCard::find($price_card_id);
        // we have to reset timezone to UTC once extra charges calculated
        $utc_timzone = date_default_timezone_get();
        //date_default_timezone_set($priceCard->CountryArea->timezone);
        // this is defined in price controller so commented from here
        $dayId = $this->getCurrentDay(date('l'));
        $pricecards = ExtraCharge::where([['price_card_id', '=', $price_card_id], ['slot_status', '=', 1]])->whereRaw("FIND_IN_SET($dayId,slot_week_days)")->get()->toArray();
        $pricecards_array = array();
        foreach ($pricecards as $key => $value)
        {
            $slot_start_time = date('Y-m-d') . " " . $value['slot_start_time'];
            $slot_start_time = new DateTime($slot_start_time);
            $slot_end_time = date('Y-m-d') . " " . $value['slot_end_time'];
            $slot_end_time = new DateTime($slot_end_time);
//            $status_time = convertTimeToUSERzone($booking_date . " " . $booking_time, $priceCard->CountryArea->timezone, $priceCard->merchant_id, null, 1);
//            $booking_time_with_date = new DateTime($status_time);
            $booking_time_with_date = new DateTime($booking_date . " " . $booking_time);

            if ($value['slot_end_day'] == 1): // NEXT DAY CASE
                $slot_start_time_first = (new DateTime(date('Y-m-d') . " " . $value['slot_start_time']))->modify('-1 day');
                $slot_end_time_first = new DateTime(date('Y-m-d') . " " . $value['slot_end_time']);
                $slot_start_time_second = new DateTime(date('Y-m-d') . " " . $value['slot_start_time']);
                $slot_end_time_second = (new DateTime(date('Y-m-d') . " " . $value['slot_end_time']))->modify('+1 day');
                if ((($booking_time_with_date >= $slot_start_time_first) &&($booking_time_with_date <= $slot_end_time_first)) || (($booking_time_with_date >= $slot_start_time_second) &&($booking_time_with_date <= $slot_end_time_second))):
                    $pricecards_array[] = $value;
                endif;
            else:
                if ($slot_start_time <= $booking_time_with_date && $slot_end_time >= $booking_time_with_date):
                    $pricecards_array[] = $value;
                endif;
            endif;
        }

        $pricecards_unique_array = array();
        foreach ($pricecards_array as $element) {
            $unique = $element['parameterName'];
            $pricecards_unique_array[$unique] = $element;
        }
        // reset default utc time zone
        date_default_timezone_set($utc_timzone);
        return $this->getTimeCharge($price_card_id, $booking_id, $booking_amount, $pricecards_unique_array);
    }

    public function getTimeCharge($price_card_id, $booking_id, $booking_amount, $pricecards_unique_array)
    {
        $response = array();
        if (empty($pricecards_unique_array)) {
            $response = array();
            return $response;
        }
        foreach ($pricecards_unique_array as $key => $value) {
            if ($value['slot_charge_type'] == 2) {
                $slot_charges = $booking_amount * $value['slot_charges'];
            } else {
                $slot_charges = $value['slot_charges'];
            }
            $response[] = array(
                'extra_charges_amount'=> 'Extra-Charges',
                'price_card_id' => $price_card_id,
                'booking_id' => $booking_id,
                'parameter' => $value['parameterName'],
                'amount' => sprintf("%0.2f", $slot_charges),
                'type' => "CREDIT",
                'code' => ""
            );
        }

        return $response;
    }

    public function getCurrentDay($day)
    {
        switch ($day) {
            case 'Monday':
                return 1;
                break;
            case 'Tuesday':
                return 2;
                break;
            case 'Wednesday':
                return 3;
                break;
            case 'Thursday':
                return 4;
                break;
            case 'Friday':
                return 5;
                break;
            case 'Saturday':
                return 6;
                break;
            case 'Sunday':
                return 7;
                break;
            default:
                echo 0;
        }
    }
}