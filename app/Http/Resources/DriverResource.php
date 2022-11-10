<?php

namespace App\Http\Resources;

use App\Http\Controllers\Helper\Merchant;
use App\Models\Booking;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray($data)
    {
        $countryCode = "";
        $phonenumber = $this->phoneNumber;
        if ($this->CountryArea->Country) {
            $phonenumber = substr($this->phoneNumber, strlen($this->CountryArea->Country->phonecode));
            $countryCode = $this->CountryArea->Country->phonecode;
        }
        $address = $this->ActiveAddress ? $this->ActiveAddress->location : "";
        $totalEarning = Booking::select('driver_cut')->Where([['driver_id', '=', $this->id], ['booking_closure', '=', 1]])->get();
        $totalRides = count($totalEarning);
        $earning = array(
            'total_ride' => $totalRides,
            'total_earning' => sprintf("%0.2f", array_sum(array_pluck($totalEarning, 'driver_cut'))),
            'rating' => $this->rating . "/" . $totalRides . " Users"
        );
        $newMerchant = new Merchant();

        // overwrite vehicle images for s3
        $arr_vehicle_data = [];
        if(!empty($this->vehicleId))
        {
            foreach ($this->DriverVehicle as $driver_vehicle)
            {
                $driver_vehicle['vehicle_id'] = $driver_vehicle['id'];
                $driver_vehicle['vehicle_image'] = get_image($driver_vehicle['vehicle_image'],'vehicle_document',$this->merchant_id);
                $driver_vehicle['vehicle_number_plate_image'] = get_image($driver_vehicle['vehicle_number_plate_image'],'vehicle_document',$this->merchant_id);
                $arr_vehicle_data[] = $driver_vehicle;
            }
        }

        $listCommissionOptions = $this->Merchant->ApplicationConfiguration->driver_commission_choice ==1 ? $newMerchant->DriverCommissionChoices($this->Merchant) : [];

        $online_transaction_code = array(
            'name' => $this->CountryArea->Country->transaction_code,
            'placeholder' => "Please enter " . $this->CountryArea->Country->transaction_code . " Code",
        );

        $stripe_connect_enable = false;
        if(isset($this->Merchant->Configuration->stripe_connect_enable) && $this->Merchant->Configuration->stripe_connect_enable == 1){
            $payment_option = PaymentOption::where('slug', 'STRIPE')->first();
            $paymentoption = PaymentOptionsConfiguration::where([['merchant_id','=', $this->id],['payment_option_id','=', $payment_option->id]])->first();
            if(!empty($paymentoption)){
                $stripe_connect_enable = true;
            }
        }

        return [
            'country_id' => (string)$this->CountryArea->Country->id,
            'phone_code' => (string)$countryCode,
            'first_name' => (string)$this->first_name,
            'last_name' => (string)$this->last_name,
            'country_area_id' => (int)$this->country_area_id,
            'driver_address' => $this->driver_address ? $this->driver_address : "",
            'login_logout' => (int)$this->login_logout,
            'account_holder_name' => (string)$this->account_holder_name,
            'rating' => (string)$this->rating,
            'driver_gender' => (int)$this->driver_gender,
            'driver_admin_status' => (string)$this->driver_admin_status,
            'signupFrom' => (string)$this->signupFrom,
            'current_latitude' => (string)$this->current_latitude,
            'phoneNumber' => (string)$phonenumber,
            'email' => (string)$this->email,
            'account_type_id' => (string)(is_null($this->account_type_id)) ? '' : $this->account_type_id,
            'signupStep' => (string)$this->signupStep,
            'account_number' => (string)$this->account_number,
            'wallet_money' => (string)$this->wallet_money,
            'player_id' => (string)$this->player_id,
            'password' => (string)$this->password,
            'id' => (int)$this->id,
            'current_longitude' => (string)$this->current_longitude,
            'free_busy' => (int)$this->free_busy,
            'profile_image' => (string)get_image($this->profile_image,'driver',$this->merchant_id),
            'bank_name' => (string)$this->bank_name,
            'online_code' => (string)$this->online_code,
            'online_offline' => (int)$this->online_offline,
            'term_status' => (int)$this->term_status,
            'created_at' => (string)$this->created_at,
            'smoker_type' => $this->DriverRideConfig ? (string)$this->DriverRideConfig->smoker_type : "",
            'auto_accept_enable' => $this->DriverRideConfig ? (string)$this->DriverRideConfig->auto_accept_enable : "",
            'allow_other_smoker' => $this->DriverRideConfig ? (string)$this->DriverRideConfig->allow_other_smoker : "",
            'home_location_active' => (string)$this->home_location_active,
            'selected_address' => $address,
//            'any_document_expire' => $this->expire_personal_document == 1 ? true : false,
            'any_document_expire' => get_driver_document_details($this->id,'status','any', 4),
            'totalEarning' => $earning,
            'DriverVehicle' => $arr_vehicle_data,
            'driver_commission_choices' => $listCommissionOptions,
            'subscription_wise_commission' =>(string)$this->pay_mode,
            'driver_limit' => $this->DriverRideConfig ? array('radius' => $this->DriverRideConfig->radius, 'latitude' => $this->DriverRideConfig->latitude, 'longitude' => $this->DriverRideConfig->longitude ) : "",
            'driver_additional_data' => $this->driver_additional_data,
            'online_transaction_code' => $online_transaction_code,
            'app_debug_mode' => isset($this->app_debug_mode) && $this->app_debug_mode == 1 ? true : false,
            'stripe_connect_enable' => $stripe_connect_enable,
            'sc_account_status' => $this->sc_account_status == 'active' ? '1' : ($this->sc_account_status == null ? '3' : '2'),
            'sc_account_text' => $this->sc_account_status == 'active' ? '' : ($this->sc_account_status == null ? '3' : __('api.stripe_account_pending'))
        ];
    }
}
