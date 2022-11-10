<?php

namespace App\Http\Resources;

use App\Http\Controllers\Helper\Merchant;
use App\Models\Booking;
use App\Models\DriverRideConfig;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\DriverTrait;

class DriverLoginResource extends JsonResource
{
    use DriverTrait;

    public function toArray($data)
    {
        $online_config = $this->getDriverOnlineConfig($this, 'online_details');
        $driver_address = $this->ActiveAddress;
        if (!empty($driver_address->id)) {
            $address = [
                'id' => $driver_address->id,
                'name' => $driver_address->address_name,
                'location' => $driver_address->location,
                'latitude' => $driver_address->latitude,
                'longitude' => $driver_address->longitude,
                'radius' => $driver_address->radius,
            ];
        } else {
            $address = [
                'id' => NULL,
                'name' => "",
                'location' => "",
                'latitude' => "",
                'longitude' => "",
                'radius' => NULL,
            ];
        }
        $bank_details = [];
        if (isset($this->Merchant->Configuration->bank_details_enable) && $this->Merchant->Configuration->bank_details_enable == 1) {
            $account_type = "";
            if (!empty($this->account_type_id)) {
                $account_type = $this->AccountType->Name;
            }
            $bank_details = array(
                'bank_name' => isset($this->bank_name) ? $this->bank_name : "",
                'account_type' => $account_type,
                'account_type_id' => isset($this->account_type_id) ? $this->account_type_id : "",
                'online_code' => isset($this->online_code) ? $this->online_code : "",
                'account_holder_name' => isset($this->account_holder_name) ? $this->account_holder_name : "",
                'account_number' => isset($this->account_number) ? $this->account_number : "",
                'transaction_code_text' => isset($this->Country) ? $this->Country->transaction_code : "Transaction Code"
            );
        } else {
            $bank_details = array(
                'bank_name' => "",
                'account_type' => "",
                'account_type_id' => "",
                'online_code' => "",
                'account_holder_name' => "",
                'account_number' => "",
                'transaction_code_text' => ""
            );
        }

        $segment_group_id = $this->segment_group_id;
        $home_address = false;
        if ($segment_group_id == 1) {
            $segments = array_pluck($this->Segment, 'slag');
            $home_address = in_array('TAXI', $segments) && $this->Merchant->BookingConfiguration->home_address_enable == 1 ? true : false;
        }
        $driver_radius = new \ArrayObject();
        $driver_radius["min_radius"] = 0;
        $driver_radius["max_radius"] = 0;
        $driver_radius["enable"] = false;
        $driver_radius["default_radius"] = 0;
        if (isset($this->Merchant->Configuration->driver_limit) && $this->Merchant->Configuration->driver_limit == 1 && $segment_group_id == 1) {
            $configuration = isset($this->Merchant->BookingConfiguration) ? $this->Merchant->BookingConfiguration : $this->Merchant->BookingConfiguration;
            $max_radius = isset($configuration->normal_ride_now_radius) ? $configuration->normal_ride_now_radius : 10;
            if (!empty($configuration->driver_ride_radius_request)) {
                $remain_ride_radius_slot = json_decode($configuration->driver_ride_radius_request, true);
                $max_radius_slot = isset($remain_ride_radius_slot[2]) ? $remain_ride_radius_slot[2] : $remain_ride_radius_slot[0];
                $max_radius = !empty($max_radius_slot) ? $max_radius_slot : $max_radius;
            }
            $driver_radius["max_radius"] = $max_radius;
            $driver_ride_config = DriverRideConfig::where('driver_id',$this->id)->first();
            $default_radius = !empty($driver_ride_config) ? $driver_ride_config->radius : 0;
            $driver_radius["enable"] = true;
            $driver_radius["default_radius"] = ($default_radius > 0) ? $default_radius : $max_radius;
        }
        return [
            'id' => $this->id,
            'signup_step' => $this->signupStep,
            'country_code' => $this->Country->country_code,
            'country_area_id' => $this->country_area_id,
            'country_id' => $this->country_id,
            'segment_group_id' => (int)$segment_group_id,
            'online_enable' => $this->signupStep == 9 ? true : false,
            'profile_image' => get_image($this->profile_image, 'driver', $this->merchant_id, true, false, 'driver'),
            'first_name' => $this->first_name,
            'last_name' => !empty($this->last_name) ? $this->last_name : "",
            'email' => !empty($this->email) ? $this->email : "",
            'phone_number' => !empty($this->phoneNumber) ? $this->phoneNumber : "",
            'online_config_status' => $online_config['status'],
            'work_set' => $online_config['detail'],
            'driver_address' => $address,
            'bank_details' => $bank_details,
            'home_address_enable' => $home_address,
            'home_location_active' => $this->home_location_active, // 1 active, 2 inactive
            'driver_radius' => $driver_radius,
            'socket_data' => $online_config['socket_data']
        ];
    }
}
