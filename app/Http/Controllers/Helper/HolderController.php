<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Helper\GetString;
use App\Models\Booking;
use App\Models\PriceCardValue;
use App\Models\PricingParameter;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HolderController extends Controller
{
    public static function PriceDetailHolder($booking_prices, $booking_id = null, $currency = null,$calling_from= "user", $segment_id = NULL,$calling_screen = "",$merchant_id = NULL)
    {
        if(!empty($booking_prices))
        {
            $booking_prices = array_map(function($item){
                return (object)$item;
            },$booking_prices);
        }
//        $merchant_id = NULL;
        if (!empty($booking_id) && $booking_id != null) {
            $booking = Booking::find($booking_id);
            $segment_id = $booking->segment_id;
            $currency = $booking->CountryArea->Country->isoCode;
            $merchant_id = $booking->merchant_id;
        }
        $get_string = new GetString($merchant_id);
        $string_file = $get_string->getStringFileText();
//        $holder[] = array(
//            "highlighted_text" => trans('api.message665'),
//            "highlighted_text_color" => "333333",
//            "highlighted_style" => "BOLD",
//            "highlighted_visibility" => true,
//            "small_text" => "eee",
//            "small_text_color" => "333333",
//            "small_text_style" => "",
//            "small_text_visibility" => false,
//            "value_text" => trans('api.message665'),
//            "value_text_color" => "333333",
//            "value_text_style" => "",
//            "value_textvisibility" => false
//        );
        $final_amount_paid = 0;
        $holder = [];
//        p($booking_prices);
        $merchant = new Merchant();
        foreach ($booking_prices as $key => $value) {
            $code = "";
            if (!empty($value->code)) {
                $code = "({$value->code})";
            }
            $parameter = $value->parameter;
            $parameterDetails = PricingParameter::find($parameter);
            if (empty($parameterDetails)) {
                $prameterName = $parameter;
                if (property_exists($value,'parameterType') && $value->parameterType == "tip_amount"):
                    $prameterName = trans("$string_file.tip");
                endif;
                if (property_exists($value,'parameterType') && $value->parameterType == "PROMO CODE") {
                    $details = PromoCode::find($value->subTotal);
                    $prameterName = $details->PromoName;
                }
                if (property_exists($value,'parameter') && $value->parameter == "TollCharges") {
                    $prameterName = trans("$string_file.toll");
                }
            } else {
                if ($parameterDetails->parameterType == 13) {
                    $applicable = $parameterDetails->applicable == 1 ? trans("$string_file.total_amount") : '';
                    $price_card_id = null;
                    if(!empty($value->price_card_id))
                    {
                        $price_card_id = $value->price_card_id;
                    }
                    elseif ($booking->price_card_id)
                    {
                        $price_card_id = $booking->price_card_id;
                    }
                    if(!empty($price_card_id))
                    {
                    $priceCardValue = PriceCardValue::where([['price_card_id', '=', $price_card_id], ['pricing_parameter_id', '=', $parameter]])->first();
                        if(!empty($priceCardValue))
                        {
                            $code = "($priceCardValue->parameter_price %)\n" . $applicable;
                        }
                    }
                }
                $prameterName = $parameterDetails->ParameterApplication . $code;
            }
            if($value->type == "DEBIT")
            {
                $final_amount_paid = $final_amount_paid - $value->amount;
            }
            else
            {
            $final_amount_paid = $final_amount_paid + $value->amount;
            }
            $amount = !empty($value->amount) ? $value->amount : 0;

            if(!empty($merchant_id))
            {
//                will remove this asap
                // this method should not be call on every parameter
                $amount = $merchant->TripCalculation($amount, $merchant_id);
            }
           if($calling_from=='driver')
           {
               $holder[] = array(
                   'name'=>$prameterName,
                   'value'=>$currency.$amount,
                   'colour'=>"333333",
                   'bold'=>false,
                   'id'=>$parameter, // only used to show on earning report
                   'parameterType'=>!empty($value->parameterType) ? $value->parameterType : "", // only used to show on earning report
               );
           }
           else
           {
               $holder[] = array(
                'highlighted_text' => $prameterName,
                "highlighted_text_color" => "333333",
                "highlighted_style" => "NORMAL",
                "highlighted_visibility" => true,
                "small_text" => "eee",
                "small_texot_clor" => "333333",
                "small_text_style" => "",
                "small_text_visibility" => false,
                "value_text" => $currency.$amount,
                "value_text_color" => "333333",
                "value_text_style" => "",
                "value_textvisibility" => true
               );
           }
        }
        if(isset($booking->Merchant->BookingConfiguration->final_amount_to_be_shown)){
            $rounded_amount = isset($booking->BookingTransaction->rounded_amount) ? number_format($booking->BookingTransaction->rounded_amount,2) : 0;
            if($rounded_amount > 0)
            {
                if($calling_from=='driver')
                {
                    array_push($holder, [
                        'name'=>trans("$string_file.round_off"),
                        'value'=>$currency . " " . $rounded_amount,
                        'colour'=>"333333",
                        'bold'=>false,
                    ]);
                }
                else
                {
                    array_push($holder, [
                    'highlighted_text' => trans("$string_file.round_off"),
                    "highlighted_text_color" => "333333",
                    "highlighted_style" => "NORMAL",
                    "highlighted_visibility" => true,
                    "small_text" => "eee",
                    "small_texot_clor" => "333333",
                    "small_text_style" => "",
                    "small_text_visibility" => false,
                    "value_text" => $currency.$amount,
                    "value_text_color" => "333333",
                    "value_text_style" => "",
                    "value_textvisibility" => true
                    ]);
                }
            }
        }

        // add total amount
        $total_amount_text = trans("$string_file.total");
        if (!empty($booking_id) && $booking_id != null) {
            $final_amount_paid = $booking->final_amount_paid;
        }
        if(!empty($merchant_id))
        {
            $final_amount_paid = $merchant->TripCalculation($final_amount_paid, $merchant_id);
        }
        $total_amount = $currency."".$final_amount_paid;
        if($calling_from == 'driver')
        {
            $total_holder = array(
                'name'=>$total_amount_text,
                'value'=>$total_amount,
                'colour'=>"333333",
                'bold'=>true,
                'id'=>"total"
            );
        }
        else
        {
            if($segment_id == 2 && $calling_screen == "delivery_checkout")
            {
                $total_holder = []; // excluding total from delivery checkout screen
            }
            else {
                $total_holder = array(
                    'highlighted_text' => $total_amount_text,
                    "highlighted_text_color" => "333333",
                    "highlighted_style" => "NORMAL",
                    "highlighted_visibility" => true,
                    "small_text" => "eee",
                    "small_texot_clor" => "333333",
                    "small_text_style" => "",
                    "small_text_visibility" => false,
                    "value_text" => $total_amount,
                    "value_text_color" => "333333",
                    "value_text_style" => "",
                    "value_textvisibility" => true
                );
            }
        }
        if(!empty($total_holder))
        {
        array_push($holder,$total_holder);
        }
//        p($holder);
        return $holder;
    }

    public static function PriceDetailHolderArray($booking_prices, $booking_id = null)
    {
        $merchant_id = NULL;
        if (!empty($booking_id) && $booking_id != null) {
            $booking = Booking::find($booking_id);
            $merchant_id = $booking->merchant_id;
            $currency = $booking->CountryArea->Country->isoCode;
        } else {
            $currency = "";
        }
        $get_string = new GetString($merchant_id);
        $string_file = $get_string->getStringFileText();
        $holder[] = array(
            "highlighted_text" => trans('api.message665'),
            "highlighted_text_color" => "333333",
            "highlighted_style" => "BOLD",
            "highlighted_visibility" => true,
            "small_text" => "eee",
            "small_text_color" => "333333",
            "small_text_style" => "",
            "small_text_visibility" => false,
            "value_text" => "ee",
            "value_text_color" => "333333",
            "value_text_style" => "",
            "value_textvisibility" => false
        );
        foreach ($booking_prices as $key => $value) {
            $code = "";
            if (!empty($value['code'])) {
                $code = "({$value['code']})";
            }
            $parameter = $value['parameter'];
            $parameterDetails = PricingParameter::find($parameter);
            if (empty($parameterDetails)) {
                $prameterName = $parameter;
                if (isset($value['parameterType']) && $value['parameterType'] == "PROMO CODE") {
                    $details = PromoCode::find($value['subTotal']);
                    $prameterName = $details->PromoName;
                }
            } else {
                $parameterDetails = PricingParameter::find($parameter);
                if ($parameterDetails->parameterType == 13) {
                    $applicable = $parameterDetails->applicable == 1 ? trans("$string_file.total_amount") : trans('api.message175');
                    $priceCardValue = PriceCardValue::where([['price_card_id', '=', $value['price_card_id']], ['pricing_parameter_id', '=', $parameter]])->first();
                    $code = "($priceCardValue->parameter_price %)\n" . $applicable;
                }
                $prameterName = $parameterDetails->ParameterApplication . $code;
            }
            $holder[] = array(
                'highlighted_text' => $prameterName,
                "highlighted_text_color" => "333333",
                "highlighted_style" => "NORMAL",
                "highlighted_visibility" => true,
                "small_text" => "eee",
                "small_text_color" => "333333",
                "small_text_style" => "",
                "small_text_visibility" => false,
                "value_text" => $currency . " " . $value['amount'],
                "value_text_color" => "333333",
                "value_text_style" => "",
                "value_textvisibility" => true
            );
        }

        if(isset($booking->Merchant->BookingConfiguration->final_amount_to_be_shown)){
            $rounded_amount = isset($booking->BookingTransaction->rounded_amount) ? number_format($booking->BookingTransaction->rounded_amount,2) : 0;
            array_push($holder, [
                'highlighted_text' => trans("$string_file.round_off"),
                "highlighted_text_color" => "333333",
                "highlighted_style" => "NORMAL",
                "highlighted_visibility" => true,
                "small_text" => "eee",
                "small_texot_clor" => "333333",
                "small_text_style" => "",
                "small_text_visibility" => false,
                "value_text" => $currency . " " . $rounded_amount,
                "value_text_color" => "333333",
                "value_text_style" => "",
                "value_textvisibility" => true
            ]);
        }
        return $holder;
    }

    public static function userOrderCancelHolder($order, $string_file){
        $cancelled_charges_paid = 0;
        $cancelled_bottom_text = "";
        $cancelled_tital = "";
        $cancel_receipt_visibility = false;
        if(in_array($order->order_status, [2,5,8])){
            $cancel_receipt_visibility = true;
            $cancelled_at = "";
            $order_history = json_decode($order->order_status_history, true);
            foreach($order_history as $history){
                if($history['order_status'] == $order->order_status){
                    $cancel_order_timestamp = date("Y-m-d H:i:s",$history['order_timestamp']);
                    $cancelled_at = convertTimeToUSERzone($cancel_order_timestamp, $order->CountryArea->timezone, null, $order->Merchant, 1);
                    break;
                }
            }
            if(!empty($order->OrderTransaction) && $order->OrderTransaction->cancellation_charge_applied != null){
                $cancelled_charges_paid = $order->OrderTransaction->cancellation_charge_applied;
            }
            $cancelled_bottom_text = trans("$string_file.cancelled")." ".trans("$string_file.by")." : ";
            switch ($order->order_status){
                case 2:
                    $cancelled_bottom_text .= trans("$string_file.user");
                    $cancelled_tital = trans("$string_file.cancelled")." ".trans("$string_file.charges");
                    break;
                case 5:
                    $cancelled_bottom_text .= trans("$string_file.driver");
                    $cancelled_tital = trans("$string_file.refund")." ".trans("$string_file.amount");
                    break;
                case 8:
                    $cancelled_bottom_text .= $order->BusinessSegment->full_name;
                    $cancelled_tital = trans("$string_file.refund")." ".trans("$string_file.amount");
                    break;
                default:
                    $cancelled_bottom_text .= "--";
                    $cancelled_tital = "---";
            }
            $cancelled_bottom_text .= " | ".trans("$string_file.at")." : ".$cancelled_at;
        }
        return array(
            'cancel_receipt_visibility' => $cancel_receipt_visibility,
            'cancelled_tital' => $cancelled_tital,
            'cancelled_charges' => $cancelled_charges_paid,
            'cancelled_bottom_text' => $cancelled_bottom_text
        );
    }
}