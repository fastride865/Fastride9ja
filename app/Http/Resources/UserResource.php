<?php

namespace App\Http\Resources;

use App\Http\Controllers\Helper\Merchant;
use App\Models\Configuration;
use App\Models\UserCard;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($data)
    {
        $countryCode = "";
        $phonenumber = "";
        $isoCode = "";
        if ($this->Country) {
            $phonenumber = substr($this->UserPhone, strlen($this->Country->phonecode));
            $countryCode = $this->Country->phonecode;
            $isoCode = $this->Country->isoCode;
        }
        $user_card = true;
        $user_signup_card_store = true;
        $config = Configuration::where('merchant_id', $this->merchant_id)->first();
        if(isset($config->user_signup_card_store_enable) && $config->user_signup_card_store_enable == 1){
            $user_signup_card_store = true;
            $cardList = UserCard::where([['user_id', '=', $this->id]])->get();
            if(count($cardList) > 0){
                $user_card = false;
            }
        }
        // need country id in case of demo user
//        p($this->id);
        $home = $this->UserAddress->where('category',1);
        $work = $this->UserAddress->where('category',2);

        $newMerchant = new Merchant();
        $country_areas = $newMerchant->CountrywithAreaList($this->Merchant,$this->country_id);
        return [
            'country_id' => !empty($this->country_id) ? (string)$this->country_id : $this->CountryArea->Country->id,
            'phone_code' => (string)$countryCode,
            'isoCode' => $isoCode,
            'first_name' => (string)$this->first_name,
            'last_name' => (string)$this->last_name ? $this->last_name : "",
            'email' => (string)$this->email,
            'rating' => (string)$this->rating,
            'merchant_id' => (string)$this->merchant_id,
            'PhoneVerified' => (string)$this->PhoneVerified,
            'id' => (string)$this->id,
            'ReferralCode' => (string)$this->ReferralCode,
            'password' => (string)$this->password,
            'UserPhone' => (string)$phonenumber,
            'wallet_balance' => (string)$this->wallet_balance,
            'UserProfileImage' => (string)$this->UserProfileImage ? get_image($this->UserProfileImage,'user',$this->merchant_id,true,false) :
                get_image(),
            'user_gender' => (string)$this->user_gender,
            'signup_status' => (string)$this->signup_status,
            'outstanding_amount' => (string)$this->outstanding_amount ? $this->outstanding_amount : "",
            'smoker_type' => (string)$this->smoker_type,
            'allow_other_smoker' => (string)$this->allow_other_smoker,
            'UserSignupType' => (string)$this->UserSignupType,
            'login_via' => (string)$this->login_via,
            'user_card' => $user_card,
            'user_signup_card_store' => $user_signup_card_store,
            'no_of_favorite_drivers' => !empty($this->FavouriteDriver) ? $this->FavouriteDriver->count(): 0,
            'home_location' => isset($home[0]) ? $home[0]->address : '',
            'work_location' => isset($work[0]) ? $work[0]->address : '',
            'no_of_bookings_done' => !empty($this->Booking) ? $this->Booking->count(): 0,
            'no_of_emergency_contacts' => !empty($this->Sos) ? $this->Sos->count(): 0,
            "country_areas"=>$country_areas
        ];
    }

    public function with($data)
    {
        return [
            'result' => "1",
            'message' => "success",
        ];
    }
}