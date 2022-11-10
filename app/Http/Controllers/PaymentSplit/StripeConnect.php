<?php
namespace App\Http\Controllers\PaymentSplit;

use App\Models\Driver;
use App\Models\Merchant;
use App\Models\Onesignal;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use App\Http\Controllers\PaymentSplit\StripeConnectHelper;

class StripeConnect {
    /* Set Stripe Connect API */
    private static function set_api_key($merchant_id) {
        $stripe_api_key = null;
        $merchant = Merchant::findOrFail($merchant_id);
        if(isset($merchant->Configuration->stripe_connect_enable) && $merchant->Configuration->stripe_connect_enable == 1){
            $payment_option = PaymentOption::where('slug', 'STRIPE')->first();
            $paymentoption = PaymentOptionsConfiguration::where([['merchant_id', '=', $merchant->id],['payment_option_id','=',$payment_option->id]])->first();
            if(!$paymentoption){
                throw new \Exception(trans("$string_file.configuration_not_found"));
            }
            $stripe_api_key = $paymentoption->api_secret_key;
        }
        \Stripe\Stripe :: setApiKey($stripe_api_key);
    }

    /* create driver account  */
    public static function create_driver_account($driver , $verification_details) {
        self :: set_api_key($driver->merchant_id);
        $user_details = self :: get_driver_details($driver , $verification_details);
        $account = self :: create_account($user_details);
        $driver = self::save_details($driver , $account);
        // self::sync_account($driver);
        return $driver;
    }

    //    update driver account
    public static function update_driver_account($driver , $verification_details) {

        if (!$driver->sc_account_id) {
            throw new \Exception('Account not created');
        }

        self :: set_api_key($driver->merchant_id);
        $user_details = self :: get_driver_details($driver , $verification_details);

        $account = self :: update_account($driver->sc_account_id, $user_details);

        $driver = self::save_details($driver , $account);

        // self::sync_account($driver , $driver->player_id);

        return $driver;
    }

    /*
     * save driver account details
     */
    public static function save_details($user_driver , $account) {
        $user_driver->sc_account_id = $account->id;
        $user_driver->sc_account_status = 'pending';
        $user_driver->save();
        return $user_driver;
    }

    /*
     * update driver account details
     */
    public static function update_details($user_driver) {
        $user_driver->sc_account_status = 'pending';
        $user_driver->save();
        return $user_driver;
    }

    /*  generate details from driver instance.
        This function return details specific to
        account creation only for transfers mode + country USA.  */
    private static function get_driver_details(Driver $driver , $verification_details) {
        $short_code = strtoupper($driver->CountryArea->Country->short_code);
        switch ($short_code){
            case 'AU':
                return StripeConnectHelper::AustraliaValidator($driver , $verification_details);
                break;
            case 'US':
                return StripeConnectHelper::UnitedStateValidator($driver , $verification_details);
                break;
            Default:
                throw new \Exception('Sorry stripe connect is not in your country');
                break;
        }
    }

    private static function create_account($user_details) {
        $short_code = strtoupper($user_details['short_code']);
        switch ($short_code){
            case 'AU':
                return StripeConnectHelper::AustraliaCreateAccount($user_details);
                break;
            case 'US':
                return StripeConnectHelper::UnitedStateCreateAccount($user_details);
                break;
            Default:
                throw new \Exception('Sorry stripe connect in not in your country');
                break;
        }
    }

    private static function update_account($account_id, $user_details) {
        try {
            $update = \Stripe\Account::update(
                $account_id,
                [
                    'individual' => [
                        'first_name' => $user_details['first_name'],
                        'last_name' => $user_details['last_name'],
                        'dob' => [
                            'day' => $user_details['dob_day'],
                            'month' => $user_details['dob_month'],
                            'year' => $user_details['dob_year']
                        ],
//                        'ssn_last_4' => substr($user_details['ssn'] , -4),
                        'id_number' => $user_details['ssn'],
                        'phone' => $user_details['phone'],
                        'email' => $user_details['email'],
                        'address' => [
                            'line1' => $user_details['line1'],
//                        'line2' => $user_details['line2'],
                            'city' => $user_details['city'],
                            'state' => $user_details['state'],
                            'postal_code' => $user_details['postal_code']
                        ],
                        'verification' => [
//                        'customer_signature' => $user_details['personal'],
                            'document' => $user_details['document'],
                            'additional_document' => $user_details['additional_document']
                        ],
                    ],
                    'external_account' => $user_details['external_account']
                ]
            );

            return $update;
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    /*
     * bank details
     */
    private static function get_driver_bank_details(Driver $driver) {

        return [
            'object' => 'bank_account',
            'country' => $driver->CountryArea->Country->short_code,
            'currency' => $driver->CountryArea->Country->isoCode,
            'account_number' => $driver->account_number,
            'routing_number' => $driver->routing_number
        ];
    }

    /*
     * update bank details
     */
    private static function update_bank_details($account_id , $user_details) {
        try {

            $update = \Stripe\Account::update(
                $account_id,
                [
                    'external_account' => $user_details
                ]
            );

            return $update;
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /*
     * update driver bank details
     */
    public static function update_driver_bank_details(Driver $driver) {
        if (!$driver->sc_account_id) {
            throw new \Exception('No scripe account added');
        }
        self::set_api_key($driver->merchant_id);

        $driver_bank_details = self::get_driver_bank_details($driver);
        self::update_bank_details($driver->sc_account_id , $driver_bank_details);
        $driver = self::update_details($driver);
//        $driver = self::sync_account($driver , $driver->player_id);
        return $driver;
    }

    /*
     * tranfer amount to driver
     */
    public static function charge_amount($driver_payable_amount , $total_amount , $sc_account_id , $token , $currency, $merchant_id) {
        self::set_api_key($merchant_id);
        try {
            $charge = \Stripe\Charge::create([
                "amount" => (int)($total_amount * 100),
                "currency" => $currency,
                "customer" => $token,
                "transfer_data" => [
                    "amount" => (int)($driver_payable_amount * 100),
                    "destination" => $sc_account_id,
                ],
            ]);
            return $charge;
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /*
     * Retrieve Account Details
     */
    public static function retrieve_account_details($sc_account_id,$merchant_id) {
        self :: set_api_key($merchant_id);
        $account_details = \Stripe\Account::retrieve(
            $sc_account_id
        );
        return $account_details;
    }

    public static function upload_file($file, $merchant_id, $purpose) {
        self::set_api_key($merchant_id);
        try {
            $fp = fopen($file, 'r');
            $file = \Stripe\File::create([
                'purpose' => $purpose,
                'file' => $fp
            ]);
            return $file;
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function sync_drivers_bulk() {
        $drivers = Driver::where('sc_account_status' , 'pending')->get();
        foreach ($drivers as $driver) {
            self :: set_api_key($driver->merchant_id);
            self :: sync_account($driver , $driver->merchant_id);
        }
        return $drivers;
    }

    public static function sync_account($user_driver) {
        if (!$user_driver->sc_account_id) {
            throw new \Exception('Driver is not registered to stripe connect');
        }
        $error_array = [];
        $account = self::retrieve_account_details($user_driver->sc_account_id, $user_driver->merchant_id);
        $charges_enabled = $account->charges_enabled;
        $payouts_enabled = $account->payouts_enabled;
        $due_list = $account->requirements->currently_due;
        $error_list = $account->requirements->errors;
        $document_verification_status = $account->individual->verification->status;
        if ($charges_enabled && $payouts_enabled) {
            $user_driver->sc_account_status = 'active';
            $user_driver->sc_address_status = 'verified';
            $user_driver->sc_identity_photo_status = 'verified';
            $user_driver->save();
            // send notification to user driver
            $message = __('api.your_bank_account_active');
            $type = 23;
            Onesignal::DriverPushMessage($user_driver->id, [], $message, $type, $user_driver->merchant_id);
        }
        else {
            $change = false;
            if ($document_verification_status == 'verified') {
                $user_driver->sc_identity_photo_status = 'verified';
                $user_driver->sc_due_list = NULL;
                $change = true;
            }
            if (!empty($error_list)) {
                $user_driver->sc_account_status = 'rejected';
                $user_driver->sc_due_list = self::refactor_error_list($error_list);
//                $user_driver->sc_due_list = self::refactor_due_list($due_list);
                $change = true;
            }
            if ($change) {
                $user_driver->save();
            }
        }
        return $user_driver;
    }

//    public static function StripeConnectPendingAction()
//    {
//        $drivers = Driver::where('sc_account_status','pending')->get();
//        if(!empty($drivers)){
//            foreach ($drivers as $driver)
//                self::sync_account($driver);
//        }
//    }

//    public static function StripeConnectPending()
//    {
//        $drivers = Driver::where('sc_account_status','pending')->get();
//        if(!empty($drivers)){
//            return true;
//        }else{
//            return false;
//        }
//    }

    private static function refactor_due_list($due_list) {
        $data = [];
        foreach ($due_list as $due_item) {
            $item = explode('.' , $due_item);
            $data[] = end($item);
        }

        return json_encode($data);
    }

    private static function refactor_error_list($error_list) {
        $data = [];
        foreach ((array)$error_list as $error) {
            $message = explode('.',$error->requirement)[2] .' - '.$error->reason;
            array_push($data,$message);
        }
        return json_encode($data);
    }

    public static function check_stripe_status($status) {
        switch ($status) {
            case 'active' :
                $return = [
                    'result' => '1',
                    'message' => __('api.account_active')
                ];
                break;

            case 'pending' :
                $return = [
                    'result' => '2',
                    'message' => __('api.account_pending')
                ];
                break;

            case 'rejected' :
                $return = [
                    'result' => '3',
                    'message' => __('api.account_rejected')
                ];
                break;

            default :
                $return = [
                    'result' => '4',
                    'message' => __('api.not_registered')
                ];
                break;
        }
        return $return;
    }
    
    public static function delete_account($driver)
    {
        try {
            self::set_api_key($driver->merchant_id);
            $account = \Stripe\Account::retrieve(
                $driver->sc_account_id
            );
            $result = $account->delete();
            if (isset($result->deleted) && $result->deleted == 1) {
                $driver->sc_account_id = NULL;
                $driver->sc_account_status = NULL;
                $driver->sc_due_list = NULL;
                $driver->save();
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}