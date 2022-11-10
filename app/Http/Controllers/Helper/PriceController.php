<?php

namespace App\Http\Controllers\Helper;

use App\Models\Booking;
use App\Models\BookingConfiguration;
use App\Models\Configuration;
use App\Models\Driver;
use App\Http\Controllers\Helper\Merchant;
use App\Models\PriceCard;
use App\Models\PriceCardCommission;
use App\Models\PricingParameter;
use App\Http\Controllers\Controller;
use App\Models\ReferralCompanyDiscount;
use App\Models\ReferralDiscount;
use App\Models\ReferCommissionFare;
use App\Models\PaymentConfiguration;
use App\Models\DriverReferralDiscount;
use App\Models\ReferralDriverDiscount;
use App\Models\ReferralUserDiscount;
use App\Models\User;
use Illuminate\Support\Arr;
use App\Traits\MerchantTrait;

class PriceController extends Controller
{
    use MerchantTrait;
    public function Insurce(PriceCard $priceCard, $amount, $booking_id)
    {
        $InsurnceAmount = $priceCard->insurnce_type == 1 ? $priceCard->insurnce_value : $amount * $priceCard->insurnce_value;
        if ($InsurnceAmount < 1) {
            return [];
        } else {
            $name = "Insurance Fee";
            $PricingParameter = PricingParameter::where([['merchant_id', '=', $priceCard->merchant_id], ['parameterType', '=', 17]])->first();
            if (!empty($PricingParameter)) {
                $name = $PricingParameter->id;
            }
            $amountFormat = new Merchant();
            $amount = $amountFormat->TripCalculation($InsurnceAmount, $priceCard->merchant_id);
            $parameter[] = array('price_card_id' => $priceCard->id, 'booking_id' => $booking_id, 'parameter' => $name, 'amount' => $amount, 'type' => "CREDIT", 'code' => "");
        }
    }

    public function BillAmount($data = array())
    {
        $amountFormat = new Merchant();
        $price_card_id = array_key_exists("price_card_id", $data) ? $data['price_card_id'] : 0;
        $merchant_id = array_key_exists("merchant_id", $data) ? $data['merchant_id'] : 0;
        $distance = array_key_exists("distance", $data) ? $data['distance'] : 0;
        $time = array_key_exists("time", $data) ? $data['time'] : 0;
        $booking_id = array_key_exists("booking_id", $data) ? $data['booking_id'] : NULL;
        $waitTime = array_key_exists("waitTime", $data) ? $data['waitTime'] : 0;
        $dead_milage_distance = array_key_exists("dead_milage_distance", $data) ? $data['dead_milage_distance'] : 0;
        $outstanding_amount = array_key_exists("outstanding_amount", $data) ? $data['outstanding_amount'] : 0;
        $number_of_rider = array_key_exists("number_of_rider", $data) ? $data['number_of_rider'] : 0;
        $user_id = array_key_exists("user_id", $data) ? $data['user_id'] : NULL;
        $driver_id = array_key_exists("driver_id", $data) ? $data['driver_id'] : NULL;
        $units = array_key_exists("units", $data) ? $data['units'] : 1;

        $hotel_id = array_key_exists("hotel_id", $data) ? $data['hotel_id'] : NULL;
        $additional_movers = array_key_exists("additional_movers", $data) ? $data['additional_movers'] : 0;

        $priceCard = PriceCard::find($price_card_id);
        $newArray = self::CalculateBill($price_card_id, $distance, $time, $booking_id, $waitTime, $dead_milage_distance, $outstanding_amount, $units);
        $merchant = \App\Models\Merchant::find($merchant_id);

        $carditnewArray = array_filter($newArray, function ($e) {
            return ($e['type'] == "CREDIT");
        });
        $amount = array_sum(array_pluck($carditnewArray, 'amount'));

        $bookingFeeArray = array_filter($newArray, function ($e) {
            return (array_key_exists('parameterType', $e) ? $e['parameterType'] == "17" : []);
        });
        $bookingFee = '0.0';
        if (!empty($bookingFeeArray)):
            $bookingFee = array_sum(Arr::pluck($bookingFeeArray, 'amount'));
        endif;
//        if (!empty($bookingFee)){
//            $amount = $amount+$bookingFee;
//            $parameter = array('subTotal' => $amount, 'price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => trans('api.booking_fee'), 'parameterType' => "17", 'amount' => (string)$bookingFee, 'type' => "CREDIT", 'code' => "");
//            array_push($newArray, $parameter);
//        }

        $priceCardValues = $priceCard->PriceCardValues;
        foreach ($priceCardValues as $priceCardValue){
            if ($priceCardValue->PricingParameter->parameterType == 16){ // Minimum fair
                if($amount < $priceCardValue->parameter_price){
                    $amount = $priceCardValue->parameter_price;
                    $total_payable = $amount - $bookingFee;
                    $billData = array();
                    foreach($newArray as $billDetail){
                        if(array_key_exists('amount',$billDetail)){
                            if(array_key_exists('parameterType',$billDetail)){
                                if($billDetail['parameterType'] == 10 ){
                                    $billDetail['amount'] = $total_payable;
                                }elseif($billDetail['parameterType'] != 19){ //Booking Fee
                                    $billDetail['amount'] = "0.00";
                                }
                            }
                        }
                        if(isset($billDetail['subTotal'])){
                            $billDetail['subTotal'] = $total_payable;
                        }
                        $billData[]=$billDetail;
                    }
                    $newArray=$billData;
                }
            }
        }

        if ($number_of_rider > 1) {
            $number_of_rider = $number_of_rider - 1;
            $amount += ($priceCard->extra_sheet_charge * $number_of_rider);
        }
        // additional_mover_charges
        if ($additional_movers > 0){
            $string_file = $this->getStringFile($merchant_id);
            $additional_mover_charge = $additional_movers * $priceCard->additional_mover_charge;
            $parameter = array('price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => trans("$string_file.additional_mover_charges"), 'amount' => $additional_mover_charge, 'type' => "CREDIT", 'code' => "");
            array_push($newArray, $parameter);
            $amount += $additional_mover_charge;
        }

        $AmountWithOutDiscountAndSpecial = $amount;
        $AmountWithOutDiscount = $amount;
        $toolCharge = 0;
        $surge = 0;
        $Extracharge = 0;
        $insurnce_amount = 0;
        $total_tax = 0;

        if ($priceCard->sub_charge_status == 1 && $priceCard->sub_charge_value > 0) {
            $surge = $priceCard->sub_charge_type == 1 ? $priceCard->sub_charge_value : bcdiv($AmountWithOutDiscountAndSpecial, $priceCard->sub_charge_value, 2);
            $surge = $amountFormat->TripCalculation($surge, $merchant_id);
            $amount += $surge;
            $AmountWithOutDiscount += $surge;
            $parameter = array('price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => "Surge-Charge", 'amount' => $surge, 'type' => "CREDIT", 'code' => "");
            array_push($newArray, $parameter);
        }
        date_default_timezone_set($priceCard->CountryArea->timezone);
        $newExtraCharge = new ExtraCharges();
        $booking_time = (array_key_exists("booking_time", $data) && !empty($data['booking_time'])) ? $data['booking_time'] : date('H:i:s');
        $booking_date = (array_key_exists("booking_date", $data) && !empty($data['booking_date'])) ? $data['booking_date'] : date('Y-m-d');
        $timeCharge = $newExtraCharge->nightchargeEstimate($price_card_id, $booking_id, $AmountWithOutDiscountAndSpecial, $booking_date, $booking_time);
        if (!empty($timeCharge)) {
            $Extracharge = array_sum(array_pluck($timeCharge, 'amount'));
            $Extracharge = $amountFormat->TripCalculation($Extracharge, $merchant_id);
            $amount += $Extracharge;
            $AmountWithOutDiscount += $Extracharge;
            $newArray = array_merge($newArray, $timeCharge);
        }
        
        $promoDiscount = "0.00";
        $promocode = Booking::select('promo_code', 'insurnce')->find($booking_id);
        if (!empty($promocode->PromoCode)) {
            $code = $promocode->PromoCode->promoCode;
            if ($promocode->PromoCode->promo_code_value_type == 1) {
                $promoDiscount = $promocode->PromoCode->promo_code_value;
            } else {
                $promoMaxAmount = !empty($promocode->PromoCode->promo_percentage_maximum_discount) ? $promocode->PromoCode->promo_percentage_maximum_discount : 0;
                $promoDiscount = ($amount * $promocode->PromoCode->promo_code_value) / 100;
                $promoDiscount = (($promoDiscount > $promoMaxAmount) && ($promoMaxAmount > 0)) ? $promoMaxAmount : $promoDiscount;
            }
            $amount = $amount > $promoDiscount ? $amount - $promoDiscount : '0.00';
            $amount = $amountFormat->TripCalculation($amount, $merchant_id);
//            $parameter = array('subTotal' => $promocode->PromoCode->id, 'price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => $code, 'parameterType' => "PROMO CODE", 'amount' => (string)$promoDiscount, 'type' => "DEBIT", 'code' => $code, 'freeValue' => $promocode->PromoCode->promo_code_value);
            $parameter = array('subTotal' => $promocode->PromoCode->id, 'price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => "promo_code", 'parameterType' => "PROMO CODE", 'amount' => (string)$promoDiscount, 'type' => "DEBIT", 'code' => $code, 'freeValue' => $promocode->PromoCode->promo_code_value);
            array_push($newArray, $parameter);
        }

        $taxes_array = $this->CalculateTaxes($price_card_id, ($amount - $outstanding_amount), $booking_id);

        if (!empty($taxes_array)):
            $newArray = array_merge($newArray, $taxes_array);
            $total_tax = array_sum(array_pluck($taxes_array, 'amount'));
            $total_tax = sprintf('%0.2f', $total_tax);
            $amount += $total_tax;
        endif;

        if (!empty($promocode) && $promocode->insurnce == 1) {
            $insurnce_amount = $priceCard->insurnce_type == 1 ? $priceCard->insurnce_value : ($priceCard->insurnce_value * $amount) / 100;
            $amount += $insurnce_amount;
            $parameter = array('subTotal' => $amount, 'price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => 'Insurance', 'parameterType' => "insurance", 'amount' => (string)$insurnce_amount, 'type' => "DEBIT", 'code' => "", 'freeValue' => "");
            array_push($newArray, $parameter);
        }
        $hotel_amount = 0;
        if(!empty($hotel_id)){
            $price_card_commission = PriceCardCommission::where('price_card_id',$priceCard->id)->first();
            // if Extra Hotel charges added
            if($price_card_commission->hotel_commission_type == 1){
                $hotel_amount = $price_card_commission->hotel_commission;
                $amount += $hotel_amount;
                $parameter = array('subTotal' => round($amount,2), 'price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => 'Hotel Charges', 'parameterType' => "hotel_charge", 'amount' => (string)$hotel_amount, 'type' => "DEBIT", 'code' => "", 'freeValue' => "");
                array_push($newArray, $parameter);
            }
        }

        if (!empty($merchant->Configuration->toll_api) && array_key_exists('from', $data) && array_key_exists('to', $data)) {
            if($merchant->Configuration->toll_api ==1){
                $newTool = new Toll();
                $coordinates = array_key_exists('coordinates', $data) ? $data['coordinates'] : "";
                $toolPrice = $newTool->checkToll($merchant->Configuration->toll_api, $data['from'], $data['to'], $coordinates, $merchant->Configuration->toll_key);
                if (is_array($toolPrice) && array_key_exists('cost', $toolPrice)) {
                    if ($toolPrice['cost'] > 0) {
                        $toolCharge = $toolPrice['cost'];
                        $parameter = array('price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => "TollCharges", 'amount' => $toolCharge, 'type' => "CREDIT", 'code' => "");
                        array_push($newArray, $parameter);
                        $amount += $toolCharge;
                    }
                }
            }else if($merchant->Configuration->toll_api == 2 || $merchant->Configuration->toll_api == 3){
                $manual_toll_charge = array_key_exists("manual_toll_charge", $data) ? (!empty($data['manual_toll_charge']) ? $data['manual_toll_charge'] : 0) : 0;
                $toolCharge = $manual_toll_charge;
                $parameter = array('price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => "TollCharges", 'amount' => $toolCharge, 'type' => "CREDIT", 'code' => "");
                array_push($newArray, $parameter);
                $amount += $toolCharge;
            }
        }
        $cancellation_array = array_filter($newArray, function ($e) {
            return ($e['parameter'] == "Cancellation fee");
        });
        $cancellation_amount_received = '0.0';
        if (!empty($cancellation_array)):
            $cancellation_amount_received = array_sum(array_pluck($cancellation_array, 'amount'));
            // $amount += $cancellation_amount_received;
        endif;

        return [
            'bill_details' => $newArray,
            'amount' => $amount,
            'promo' => $promoDiscount,
            'cancellation_amount_received' => $cancellation_amount_received,
            'subTotalWithoutSpecial' => $AmountWithOutDiscountAndSpecial,
            'subTotalWithoutDiscount' => ($AmountWithOutDiscount-$cancellation_amount_received),
            'toolCharge' => $toolCharge,
            'surge' => $surge,
            'extracharge' => $Extracharge,
            'insurnce_amount' => $insurnce_amount,
            'total_tax' => $total_tax,
            'booking_fee' => $bookingFee,
            'hotel_amount' => $hotel_amount,
        ];
    }

    public static function CalculateBill($price_card_id, $distance, $time, $booking_id, $waitmin = 0, $dead_milage_distance = 0, $outstand = 0, $units = 1)
    {
        $merchant = new Merchant();
        $distance = $units == 1 ? ($distance / 1000) : ($distance / 1609.34);
        $hour = $time / 60;
        $price_card = PriceCard::with(['PriceCardValues' => function ($query) {
            $query->with(['PricingParameter' => function ($q) {
                $q->orderBy('parameterType', 'ASC');
            }]);
        }])->find($price_card_id);
        $pricing_type = $price_card->pricing_type;
        $base_fare = $price_card->base_fare;
        $free_distance = $price_card->free_distance;
        $free_time = $price_card->free_time;
        $parameter = [];
        // @Bhuvanesh
        // pricing type 3  is INPUT BY DRIVER
        $parameter_price = 0;
        if ($pricing_type == 1 || $pricing_type == 2 || $pricing_type == 3) {
            $price_card_values = $price_card->PriceCardValues;
            $subTotal = 0;
            if (isset($base_fare)) {
                $newArray = PricingParameter::whereHas('PricingType', function ($query) use ($pricing_type) {
                    $query->where('price_type', $pricing_type);
                })->whereHas('Segment', function($query) use($price_card){
                    $query->whereIn('segment_id', [$price_card->segment_id]);
                })->where([['parameterType', '=', 10], ['merchant_id', '=', $price_card->merchant_id]])->first();
                if (!empty($newArray)) {
                    $subTotal += $base_fare;
                    $subTotal = $merchant->TripCalculation($subTotal, $price_card->merchant_id);
                    $parameter[] = array('simply_amount' => "amount_without_spl_discount", 'subTotal' => $subTotal, 'price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => $newArray->id, 'parameterType' => $newArray->parameterType, 'amount' => $base_fare, 'type' => "CREDIT", 'code' => "");
                }
            }
            foreach ($price_card_values as $value) {
                $disatnceValue = 1;
                $code = "";
                $parameterAmount = 0.0;
                $parameter_price = (float)$value->parameter_price;
                $pricing_parameter = $value->PricingParameter;
                $parameterType = $pricing_parameter->parameterType;
                $parameterName = $pricing_parameter->id;
                $free_value = $value->free_value;
                $type = "CREDIT";
                $parameterAmount = 0.0;
                if ($parameterType == 13 || $parameterType == 12 || $parameterType == 16) {
                    continue;
                }
                switch ($parameterType) {
                    case "1":
                        if ($distance > $free_distance) {
                            $extra = $distance - $free_distance;
                            $parameterAmount = $extra * $parameter_price;
                        } else {
                            $parameterAmount = "0.00";
                        }
                        $subTotal += $parameterAmount;
                        break;
                    case "2":
                        if ($hour > $free_time) {
                            $extra = $hour - $free_time;
                            $parameterAmount = $parameter_price * $extra;
                        } else {
                            $parameterAmount = "0.00";
                        }
                        $subTotal += $parameterAmount;
                        break;
                    case "6":
                        if ($dead_milage_distance > $free_value) {
                            $extra = $dead_milage_distance - $free_value;
                            $parameterAmount = $extra * $parameter_price;
                        } else {
                            $parameterAmount = "0.00";
                        }
                        $subTotal += $parameterAmount;
                        break;
                    case "8":
                        if ($time > $free_time) {
                            $extra = $time - $free_time;
                            $parameterAmount = $parameter_price * $extra;
                        } else {
                            $parameterAmount = "0.00";
                        }
                        $subTotal += $parameterAmount;
                        break;
                    case "9":
                        if ($waitmin > $free_value) {
                            $extra = $waitmin - $free_value;
                            $parameterAmount = $parameter_price * $extra;
                        } else {
                            $parameterAmount = "0.00";
                        }
                        $subTotal += $parameterAmount;
                        break;
                    case "14":
                        $parameterAmount = $free_value == 1 ? $parameter_price : $distance * $parameter_price;
                        $subTotal += $parameterAmount;
                        break;
                    case "15":
                        $days = $hour > 0 ? ceil($hour / 24) : 1;
                        $totalDistance = $days * $free_value;
                        if ($totalDistance > $distance) {
                            $parameterAmount = $days * $parameter_price;
                        } else {
                            $extra = $distance - $totalDistance;
                            $parameterAmount = ($days * $parameter_price) + ($disatnceValue * $extra);
                        }
                        $subTotal += $parameterAmount;
                        break;
                    case "17":
                        $parameterAmount = $parameter_price;
                        $subTotal += $parameterAmount;
                        break;
                    case "19":
                        $parameterAmount = $parameter_price;
                        $subTotal += $parameterAmount;
                        break;
                    case "18":
                        if(!empty($booking_id)){
                            $booking = Booking::find($booking_id);
                            $configuration = BookingConfiguration::where([['merchant_id', '=', $booking->merchant_id]])->first();
                            if($configuration->final_bill_calculation == 1){ //If Bill mathod equals to actual
                                if($booking->onride_waiting_time > 0){
                                    $parameterAmount = $parameter_price * $booking->onride_waiting_time;
                                } else {
                                    $parameterAmount = "0.00";
                                }
                                $subTotal += round($parameterAmount,2);
                            }
                        }
                        break;
                    case "20":
                        if ($waitmin > $free_value) {
                            $parameterAmount = $parameter_price;
                        } else {
                            $parameterAmount = "0.00";
                        }
                        $subTotal += $parameterAmount;
                        break;
                    case "21":
                        if(!empty($booking_id)){
                            $booking = BookingCheckout::where("merchant_id",$price_card->merchant_id)->find($booking_id);
                            if(empty($booking)){
                                $booking = Booking::where("merchant_id",$price_card->merchant_id)->find($booking_id);
                            }
                            $drop_point = $booking->total_drop_location-1;
                            if($drop_point > 0){
                                $parameterAmount = $parameter_price * $drop_point;
                            }else{
                                $parameterAmount = "0.00";
                            }
                            $subTotal += round($parameterAmount,2);
                        }
                        break;
                    default:
                        $parameterAmount = $parameter_price;
                        if ($parameterType == 11) {
                            $type = "CREDIT";
                        }
                        $subTotal += $parameterAmount;
                }
                // pricing type 3  is INPUT BY DRIVER
                if($pricing_type == 3){
                    $parameterAmount = 0;
                    $subTotal = 0;
                }else{
                    if($parameterType != 17){
                        $parameterAmount = $merchant->TripCalculation($parameterAmount, $price_card->merchant_id);
                    }
                }
                $outstand = $merchant->TripCalculation($outstand, $price_card->merchant_id);
                $parameter[] = array('subTotal' => $subTotal, 'price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => $parameterName, 'parameterType' => $parameterType, 'amount' => (string)$parameterAmount, 'type' => $type, 'code' => $code, 'freeValue' => $parameter_price);
            }
            if ($outstand > 0) {
                $subTotal += $outstand;
                $parameter[] = array('subTotal' => $subTotal, 'price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => "Cancellation fee", 'amount' => $outstand, 'type' => "CREDIT", 'code' => "", 'freeValue' => $parameter_price);
            }
            return $parameter;
        } else {
            return trans('api.message62');
        }
    }

    public function CalculateTaxes($price_card_id, $amount, $booking_id = 0)
    {
        $price_card = PriceCard::with(['PriceCardValues' => function ($query) {
            $query->whereHas('PricingParameter', function ($param) {
                $param->where('parameterType', 13);
            })->with(['PricingParameter' => function ($q) {
                $q->orderBy('parameterType', 'ASC');
            }]);
        }])->find($price_card_id);

        if (!empty($price_card)):
            $pricing_type = $price_card->pricing_type;
            $parameter = array();
            if ($pricing_type == 1 || $pricing_type == 2) {
                $price_card_values = $price_card->PriceCardValues;
                $subTotal = $amount;
                foreach ($price_card_values as $value) {
                    $code = "";
                    $parameter_price = $value->parameter_price;
                    $pricing_parameter = $value->PricingParameter;
                    $parameterType = $pricing_parameter->parameterType;
                    $parameterName = $pricing_parameter->id;
                    if ($subTotal > 0 && $parameter_price > 0) {
                        $parameterAmount = ($subTotal * $parameter_price) / 100;
                    } else {
                        $parameterAmount = "0.00";
                    }
                    $subTotal = $subTotal + $parameterAmount;
                    $parameter[] = array('subTotal' => $subTotal, 'price_card_id' => $price_card_id, 'booking_id' => $booking_id, 'parameter' => $parameterName, 'parameterType' => $parameterType, 'amount' => (string)$parameterAmount, 'type' => 'TAXES', 'code' => $code, 'freeValue' => $parameter_price);
                }
            }
            return $parameter;
        endif;
        return [];
    }

//    public function Refer($id, $type)
//    {
//        $refer = ReferralDiscount::where([['receiver_id', '=', $id], ['receiver_type', '=', $type], ['referral_available', '=', 1]])->first();
//        if (!empty($refer)) {
//            return $refer;
//        }
//    }
//
//    public function getSenderRefer($id,$type){
//        $refer = ReferralDiscount::where([['sender_id', '=', $id], ['sender_type', '=', $type],['offer_type','=',4],['sender_get_ride','!=', 0],['referral_available', '=', 1]])->first();
//        if (!empty($refer)) {
//            return $refer;
//        }
//    }
//
//    public function getReferCalculation($data, $amount,$booking_id)
//    {
//        switch ($data->offer_type) {
//            case "1":
//                $referAmount = $data->offer_value;
//                $amount = $this->getReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                return $amount;
//                break;
//            case "2":
//                $referAmount = ($amount * $data->offer_value) / 100;
//                $amount = $this->getReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                return $amount;
//                break;
//            case "3":
//                $commission_slabs = json_decode($data->offer_value, true);
//                $referAmount = 0;
//                foreach ($commission_slabs as $commission_slab){
//                    if (($commission_slab['start_range'] <= $amount) && ($amount <= $commission_slab['end_range'])){
//                        $referAmount = $commission_slab['commission'];
//                        break;
//                    }
//                }
//                if ($data->limit == 0){
//                    // For unlimited discount
//                    $amount = $this->ReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                }
//                if ($data->limit == 1){
//                    if (!empty($data->limit_usage) && $data->limit_usage > 0){
//                        $limit = $data->limit_usage;
//                        $no_of_day = $data->no_of_day;
//                        // Day count after signup
//                        if (!empty($data->day_count) && $data->day_count == 1){
//                            if ($data->receiver_type == 1){
//                                $getData = User::find($data->receiver_id);
//                            }else{
//                                $getData = Driver::find($data->receiver_id);
//                            }
//                            $first_date = $getData->created_at;
//                            $last_date = date('Y-m-d',strtotime($first_date. '+'.$no_of_day.' days'));
//                            if (date('Y-m-d') < $last_date){
//                                $amount = $this->ReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                                $limit--;
//                                if($limit == 0){
//                                    $data->referral_available = 2;
//                                }
//                            }else{
//                                $limit = 0;
//                                $data->referral_available = 2;
//                            }
//                            $data->limit_usage = $limit;
//                            $data->save();
//                        }elseif (!empty($data->day_count) && $data->day_count == 2){
//                            // Day count after first ride
//                            $keyWord = $data->receiver_type == 1 ? 'user_id' : 'driver_id';
//                            $getData = Booking::where([[$keyWord,'=',$data->receiver_id],['booking_status','=',1005]])->oldest()->first();
//                            if (!empty($getData)){
//                                $first_date = $getData->updated_at;
//                                $last_date = date('Y-m-d',strtotime($first_date. '+'.$no_of_day.' days'));
//                                if (date('Y-m-d') < $last_date){
//                                    $amount = $this->ReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                                    $limit--;
//                                    if($limit == 0){
//                                        $data->referral_available = 2;
//                                    }
//                                }else{
//                                    $limit = 0;
//                                    $data->referral_available = 2;
//                                }
//                                $data->limit_usage = $limit;
//                                $data->save();
//                            }
//                        }elseif(empty($data->day_count) && $data->day_count == NULL){
//                            $amount = $this->ReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                            $limit--;
//                            if($limit == 0){
//                                $data->referral_available = 2;
//                            }
//                            $data->limit_usage = $limit;
//                            $data->save();
//                        }
//                    }
//                    else{
//                        $no_of_day = $data->no_of_day;
//                        if (!empty($data->day_count) && $data->day_count == 1){
//                            if ($data->receiver_type == 1){
//                                $getData = User::find($data->receiver_id);
//                            }else{
//                                $getData = Driver::find($data->receiver_id);
//                            }
//                            $first_date = $getData->created_at;
//                            $last_date = date('Y-m-d',strtotime($first_date. '+'.$no_of_day.' days'));
//                            if (date('Y-m-d') < $last_date){
//                                $amount = $this->ReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                            }else{
//                                $data->referral_available = 2;
//                                $data->save();
//                            }
//                        }
//                        if (!empty($data->day_count) && $data->day_count == 2){
//                            $keyWord = $data->receiver_type == 1 ? 'user_id' : 'driver_id';
//                            $getData = Booking::where([[$keyWord,'=',$data->receiver_id],['booking_status','=',1005]])->oldest()->first();
//                            if (!empty($getData)){
//                                $first_date = $getData->updated_at;
//                                $last_date = date('Y-m-d',strtotime($first_date. '+'.$no_of_day.' days'));
//                                if (date('Y-m-d') < $last_date){
//                                    $amount = $this->ReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                                }else{
//                                    $data->referral_available = 2;
//                                    $data->save();
//                                }
//                            }
//                        }
//                    }
//                }
//                return $amount;
//                break;
//            case "4":
//                $referAmount = $amount;
//                $amount = $this->getReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                return $amount;
//                break;
//        }
//    }
//
//    public function getReferralOfferCalculation($data,$referAmount,$amount,$booking_id){
//        if ($data->limit == 0){
//            $amount = $this->ReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//        }
//        if ($data->limit == 1){
//            if (!empty($data->limit_usage) && $data->limit_usage > 0){
//                $limit = $data->limit_usage;
//                $no_of_day = $data->no_of_day;
//                if (!empty($data->day_count) && $data->day_count == 1){
//                    if ($data->receiver_type == 1){
//                        $getData = User::find($data->receiver_id);
//                    }else{
//                        $getData = Driver::find($data->receiver_id);
//                    }
//                    $first_date = $getData->created_at;
//                    $last_date = date('Y-m-d',strtotime($first_date. '+'.$no_of_day.' days'));
//                    if (date('Y-m-d') <= $last_date){
//                        $amount = $this->ReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                        $limit--;
//                        if($limit == 0){
//                            $data->referral_available = 2;
//                        }
//                    }else{
//                        $limit = 0;
//                        $data->referral_available = 2;
//                    }
//                    $data->limit_usage = $limit;
//                    $data->save();
//                }elseif (!empty($data->day_count) && $data->day_count == 2){
//                    $keyWord = $data->receiver_type == 1 ? 'user_id' : 'driver_id';
//                    $getData = Booking::where([[$keyWord,'=',$data->receiver_id],['booking_status','=',1005]])->oldest()->first();
//                    if (!empty($getData)){
//                        $first_date = $getData->updated_at;
//                        $last_date = date('Y-m-d',strtotime($first_date. '+'.$no_of_day.' days'));
//                        if (date('Y-m-d') < $last_date){
//                            $amount = $this->ReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                            $limit--;
//                            if($limit == 0){
//                                $data->referral_available = 2;
//                            }
//                        }else{
//                            $limit = 0;
//                            $data->referral_available = 2;
//                        }
//                        $data->limit_usage = $limit;
//                        $data->save();
//                    }
//                }elseif(empty($data->day_count) && $data->day_count == NULL){
//                    $amount = $this->ReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                    $limit--;
//                    if($limit == 0){
//                        $data->referral_available = 2;
//                    }
//                    $data->limit_usage = $limit;
//                    $data->save();
//                }
//            }else{
//                $no_of_day = $data->no_of_day;
//                if (!empty($data->day_count) && $data->day_count == 1){
//                    if ($data->receiver_type == 1){
//                        $getData = User::find($data->receiver_id);
//                    }else{
//                        $getData = Driver::find($data->receiver_id);
//                    }
//                    $first_date = $getData->created_at;
//                    $last_date = date('Y-m-d',strtotime($first_date. '+'.$no_of_day.' days'));
//                    if (date('Y-m-d') < $last_date){
//                        $amount = $this->ReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                    }else{
//                        $data->referral_available = 2;
//                        $data->save();
//                    }
//                }
//                if (!empty($data->day_count) && $data->day_count == 2){
//                    $keyWord = $data->receiver_type == 1 ? 'user_id' : 'driver_id';
//                    $getData = Booking::where([[$keyWord,'=',$data->receiver_id],['booking_status','=',1005]])->oldest()->first();
//                    if (!empty($getData)){
//                        $first_date = $getData->updated_at;
//                        $last_date = date('Y-m-d',strtotime($first_date. '+'.$no_of_day.' days'));
//                        if (date('Y-m-d') < $last_date){
//                            $amount = $this->ReferralOfferCalculation($data,$referAmount,$amount,$booking_id);
//                        }else{
//                            $data->referral_available = 2;
//                            $data->save();
//                        }
//                    }
//                }
//            }
//        }
//        return $amount;
//    }
//
//    public function ReferralOfferCalculation($data,$referAmount,$amount,$booking_id = 0){
//        $isReferralNull = false;
//        $walletTransaction = new WalletTransaction();
//        if ($data->offer_applicable == 1 || $data->offer_applicable == 3) {
//            if ($data->sender_type == 1) {
//                if ($data->offer_type == 4){
//                    $amount = $amount - $referAmount;
//                    $isReferralNull = false;
//                }else{
//                    $senderData = User::find($data->sender_id);
//                    //                    $balance = $senderData->wallet_balance + $referAmount;
//                    //                    $senderData->wallet_balance = $balance;
//                    //                    $senderData->save();
//                    //                    $walletTransaction->userWallet($senderData, $referAmount, 1,$booking_id);
//                    $paramArray = array(
//                        'user_id' => $senderData->id,
//                        'booking_id' => $booking_id,
//                        'amount' => $referAmount,
//                        'narration' => 1,
//                        'platform' => 2,
//                        'payment_method' => 1,
//                    );
//                    WalletTransaction::UserWalletCredit($paramArray);
////                    CommonController::UserWalletCredit($senderData->id,$booking_id,$referAmount,1,2,1);
//
//                    $isReferralNull = true;
//                }
//                $this->UserReferDiscount($data->sender_id,$referAmount,$booking_id);
//            }elseif ($data->sender_type == 2) {
//                if ($data->offer_type != 4){
//                    $this->DriverReferDiscount($data->sender_id, $referAmount, $booking_id);
//                    $isReferralNull = true;
//                }
//            }elseif ($data->sender_type == 0 && $data->sender_id == 0){
//                if ($data->offer_type != 4){
//                    $default_code = $data->getReferralSystem->default_code;
//                    $merchant_id = $data->getReferralSystem->merchant_id;
//                    if ($default_code == 1){
//                        $this->CompanyReferDiscount($data->id,$referAmount,$booking_id,$merchant_id);
//                    }
//                    $isReferralNull = true;
//                }
//            }
//        }
//        // Offer for  Receiver and Both
//        if ($data->offer_applicable == 2 || $data->offer_applicable == 3) {
//            if ($data->receiver_type == 1) {
//                if($data->offer_type == 1){
//                    if ($amount < $referAmount){
//                        $referAmount = $amount;
//                        $amount = 0;
//                    }else{
//                        $amount = $amount - $referAmount;
//                    }
//                }else if($data->offer_type != 4){
//                    $amount = $amount - $referAmount;
//                }
//                $this->UserReferDiscount($data->receiver_id,$referAmount,$booking_id);
//                $isReferralNull = false;
//            } elseif ($data->receiver_type == 2) {
//                if ($data->offer_type != 4){
//                    $this->DriverReferDiscount($data->receiver_id, $referAmount, $booking_id);
//                    $isReferralNull = true;
//                }
//            }
//        }
//        $refer_amount = $isReferralNull ? NULL : $referAmount ;
//        return array('amount'=> $amount,'refer_amount'=> $refer_amount);
//    }
//
//    public function DriverReferDiscount($id, $amount, $booking_id)
//    {
//        $senderData = Driver::find($id);
//        ReferralDriverDiscount::create([
//            'merchant_id' => $senderData->merchant_id,
//            'booking_id' => $booking_id,
//            'driver_id' => $id,
//            'amount' => $amount,
//            'payment_status' => 0,
//            'expire_status' => 0
//        ]);
//    }
//
//    public function CompanyReferDiscount($referral_discount_id, $amount,$booking_id,$merchant_id)
//    {
//        ReferralCompanyDiscount::create([
//            'merchant_id' => $merchant_id,
//            'referral_discount_id' => $referral_discount_id,
//            'booking_id' => $booking_id,
//            'amount' => $amount
//        ]);
//    }
//
//    public function UserReferDiscount($id,$amount,$booking_id){
//        $userData = User::select('merchant_id')->find($id);
//        if (!empty($userData)){
//            ReferralUserDiscount::create([
//                'merchant_id' => $userData->merchant_id,
//                'booking_id' => $booking_id,
//                'user_id' => $id,
//                'amount' => $amount,
//            ]);
//        }
//    }
//
//    public function DriverRefer($driver_id)
//    {
//        $refer = DriverReferralDiscount::where([['driver_id', '=', $driver_id], ['referral_available', '=', 1]])->oldest()->first();
//        if (!empty($refer)) {
//            return $refer;
//        }
//    }

//    public function getDriverReferEarning($merchant_id,$driver_id,$from,$to){
//        $data = ReferralDriverDiscount::where([['merchant_id','=',$merchant_id],['driver_id','=',$driver_id]])->whereBetween('created_at',array($from,$to))->sum('amount');
//        return $data;
//    }
}
