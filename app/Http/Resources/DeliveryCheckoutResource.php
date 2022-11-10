<?php

namespace App\Http\Resources;

use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\HolderController;
use App\Models\AdvertisementBanner;
use App\Models\DeliveryCheckoutDetail;
use App\Models\DeliveryProduct;
use App\Models\MerchantFarePolicy;
use App\Models\PaymentOptionsConfiguration;
use App\Models\SmsConfiguration;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\Helper\PolygenController;
use App\Models\CountryArea;
use Illuminate\Support\Facades\App;
use App\Traits\MerchantTrait;

class DeliveryCheckoutResource extends JsonResource
{
    use MerchantTrait;
    public function toArray($data)
    {
        $currency = $this->CountryArea->Country->isoCode;
        $estimate_bill = $currency . " " . $this->estimate_bill;
        $SelectedPaymentMethod = $this->SelectedPaymentMethod = $this->PaymentMethod($this->id);
        $string_file = $this->getStringFile(NULL,$this->Merchant);
        $additional_mover = $this->Merchant->BookingConfiguration->additional_mover;
        $promo_heading = trans("$string_file.apply_coupon");
        $promo_code = "";
        $discounted_amount = "";
        $bill_details = $this->bill_details;
        if (!empty($this->promo_code)) {
            $promo_code = $this->PromoCode->promoCode;
            $promo_heading = trans("$string_file.coupon_applied");
            $discounted_amount = isset($this->discounted_amount) ? $this->discounted_amount : "";
            $estimate_bill = $discounted_amount = $currency . " " . $discounted_amount;
        }

        $estimate_receipt = [];
        if (!empty($bill_details)) {
            $price = json_decode($bill_details, true);
            $estimate_receipt = HolderController::PriceDetailHolder($price, null, $currency,'user',$this->segment_id,"delivery_checkout");
        }

        $return_array = [];
        $return_array['id'] = $this->id;
        $return_array['estimate_bill'] = $estimate_bill;
        $return_array['estimate_receipt'] = $estimate_receipt;
        $return_array['SelectedPaymentMethod'] = $SelectedPaymentMethod;
        $return_array['vehicle_details']['id'] = $this->VehicleType->id;
        $return_array['vehicle_details']['name'] = $this->VehicleType->VehicleTypeName;
        $return_array['vehicle_details']['weight'] = '';
        $return_array['vehicle_details']['icon'] = get_image($this->VehicleType->vehicleTypeImage, 'vehicle', $this->merchant_id,true,false);

        $return_array['request_type']['type'] = ((int)$this->booking_type == 1) ? trans("$string_file.request_normal") : trans("$string_file.request_later");
        $return_array['request_type']['time'] = ($this->booking_type == 1) ? '' : $this->later_booking_time;
        $return_array['request_type']['date'] = ($this->booking_type == 1) ? '' : $this->later_booking_date;

        $return_array['location']['pickup']['visible'] = true;
        $return_array['location']['pickup']['address']['name'] = $this->pickup_location;
        $return_array['location']['pickup']['address']['latitude'] = $this->pickup_latitude;
        $return_array['location']['pickup']['address']['longitude'] = $this->pickup_longitude;

        $return_array['location']['drop']['visible'] = ($this->drop_latitude) ? true : false;
        $return_array['location']['drop']['address']['name'] = $this->drop_location;
        $return_array['location']['drop']['address']['latitude'] = (string)$this->drop_latitude;
        $return_array['location']['drop']['address']['longitude'] = (string)$this->drop_longitude;

        $return_array['packages'] = [];
        $return_array['additional_mover_charge'] = !empty($this->PriceCard->additional_mover_charge) ? $this->PriceCard->additional_mover_charge : 0;

        $products = DeliveryProduct::where([['merchant_id','=',$this->merchant_id],['status','=',1]])->get();
        $product_list = [];
        foreach ($products as $product){
            $product_list[] = array(
                'id' => $product->id,
                'segment_id' => $product->segment_id,
                'merchant_id' => $product->merchant_id,
                'product_name' => $product->ProductName,
                'weight_unit' => $product->WeightUnit->WeightUnitName
            );
        }
        $return_array['product_list'] = $product_list;

        $delivery_drop_details = [];
        $delivery_checkout_details = DeliveryCheckoutDetail::where([['booking_checkout_id','=',$this->id]])->orderBy('stop_no')->get();
        $delivery_checkout_detail_pending = DeliveryCheckoutDetail::where([['booking_checkout_id','=',$this->id],['details_fill_status','=',0]])->get()->count();
        if(count($delivery_checkout_details) > 0){
            foreach($delivery_checkout_details as $delivery_checkout_detail){
                array_push($delivery_drop_details, array(
                    'id' => $delivery_checkout_detail->id,
                    'stop_no' => $delivery_checkout_detail->stop_no,
                    'drop_location' => $delivery_checkout_detail->drop_location,
                    'drop_latitude' => $delivery_checkout_detail->drop_latitude,
                    'drop_longitude' => $delivery_checkout_detail->drop_longitude,
                    'receiver_name' => ($delivery_checkout_detail->receiver_name != null) ? $delivery_checkout_detail->receiver_name : "",
                    'receiver_phone' => ($delivery_checkout_detail->receiver_phone != null) ? $delivery_checkout_detail->receiver_phone : "",
                    'receiver_image' => ($delivery_checkout_detail->receiver_image != null) ? $delivery_checkout_detail->receiver_image : "",
                    'additional_notes' => ($delivery_checkout_detail->additional_notes != null) ? $delivery_checkout_detail->additional_notes : "",
                    'product_data' => ($delivery_checkout_detail->product_data != null) ? json_decode($delivery_checkout_detail->product_data,true) : [],
                    'product_image_one' => ($delivery_checkout_detail->product_image_one != null) ? get_image($delivery_checkout_detail->product_image_one, 'product_image', $this->merchant_id,true,false) : "",
                    'product_image_two' => ($delivery_checkout_detail->product_image_two != null) ? get_image($delivery_checkout_detail->product_image_two, 'product_image', $this->merchant_id,true,false) : "",
                    'details_fill_status' => ($delivery_checkout_detail->details_fill_status == 1),
                ));
            }
        }
        $return_array['delivery_drop_details_pending'] = ($delivery_checkout_detail_pending > 0) ? true : false;
        $return_array['delivery_drop_details'] = $delivery_drop_details;
        $return_array['promo_code'] = $promo_code;
        $return_array['discounted_amount'] = $discounted_amount;
        $return_array['promo_heading'] = $promo_heading;
        $return_array['additional_mover_enable'] = $additional_mover == 1 ? true : false;
        return $return_array;
    }
}
