<?php

namespace App\Http\Controllers\PaymentSplit;

class StripeConnectHelper {

    public static function AustraliaValidator($driver, $verification_details){
        $valid = \validator($driver->toArray() , [
            'first_name' => 'required',
            'last_name' => 'required',
            'device_ip' => 'required',
            'dob' => 'required',
           // 'ssn' => 'required',
            'email' => 'required|email',
            'phoneNumber' => 'required',
            'account_number' => 'required',
            'bsb_number' => 'required',
            'driver_additional_data' => 'required'
        ]);
        $valid = \validator($verification_details , [
            //'personal_id' => 'required',
            'photo_front_id' => 'required',
            'photo_back_id' => 'required',
            'additional_id' => 'required',
        ]);

        if ($valid->fails()) {
            throw new \Exception($valid->errors()->first());
        }
        $driver_additional_data = json_decode($driver->driver_additional_data , true);
        if (!$driver_additional_data) {
            throw new \Exception('Driver address not added');
        }
        return [
            'first_name' => $driver->first_name,
            'last_name' => $driver->last_name,
            'tos_acceptance_date' => time(),
            'tos_acceptance_ip' => $driver->device_ip,
            'dob_day' => date('d' , strtotime($driver->dob)),
            'dob_month' => date('m' , strtotime($driver->dob)),
            'dob_year' => date('Y' , strtotime($driver->dob)),
            'ssn' => $driver->ssn,
            'email' => $driver->email,
            'short_code' => $driver->CountryArea->Country->short_code,
            'phone' => $driver->phoneNumber,
            'line1' => $driver_additional_data['address_line_1'],
            'line2' => $driver_additional_data['address_line_2'],
            'city' => $driver_additional_data['city_name'],
            'state' => $driver_additional_data['province'],
            'postal_code' => $driver_additional_data['pincode'],
            'external_account' => [
                'object' => 'bank_account',
                'country' => $driver->CountryArea->Country->short_code,
                'currency' => $driver->CountryArea->Country->isoCode,
                'account_number' => $driver->account_number,
                'routing_number' => $driver->bsb_number
            ],
            'personal' => [
                'front' => $verification_details['personal_id'],
            ],
            'document' => [
                'front' => $verification_details['photo_front_id'],
                'back' => $verification_details['photo_back_id'],
            ],
            'additional_document' => [
                'front' => $verification_details['additional_id']
            ]
        ];
    }

    public static function AustraliaCreateAccount($user_details){
        try {
            $account = \Stripe\Account::create([
                'type' => 'custom',
                'country' => $user_details['short_code'],
                'requested_capabilities' => [
                    'transfers',
                    'card_payments'
                ],
                'business_type' => 'individual',
                'business_profile' => [
                    'url' => 'https://www.apporio.com',
                    'mcc' => '5045'
                ],
                'tos_acceptance' => [
                    'date' => $user_details['tos_acceptance_date'],
                    'ip' => $user_details['tos_acceptance_ip']
                ],
                'individual' => [
                    'first_name' => $user_details['first_name'],
                    'last_name' => $user_details['last_name'],
                    'dob' => [
                        'day' => $user_details['dob_day'],
                        'month' => $user_details['dob_month'],
                        'year' => $user_details['dob_year']
                    ],
                    //  'ssn_last_4' => substr($user_details['ssn'] , -4),
                    'id_number' => $user_details['ssn'],
                    'phone' => $user_details['phone'],
                    'email' => $user_details['email'],
                    'address' => [
                        'line1' => $user_details['line1'],
                        'line2' => $user_details['line2'],
                        'city' => $user_details['city'],
                        'state' => $user_details['state'],
                        'postal_code' => $user_details['postal_code']
                    ],
                    'verification' => [
//                        'customer_signature' => $user_details['personal'],
                        'document' => $user_details['document'],
                        'additional_document' => $user_details['additional_document']
                    ]
                ],
                'external_account' => $user_details['external_account']
            ]);
            return $account;
        }
        catch (\Exception $e) {
//            p($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public static function UnitedStateValidator($driver, $verification_details){
        $valid = \validator($driver->toArray() , [
            'first_name' => 'required',
            'last_name' => 'required',
            'device_ip' => 'required',
            'dob' => 'required',
            'ssn' => 'required',
            'email' => 'required|email',
            'phoneNumber' => 'required',
            'account_number' => 'required',
            'routing_number' => 'required',
            'driver_additional_data' => 'required'
        ]);
        $valid = \validator($verification_details , [
            'photo_id_front' => 'required',
        ]);

        if ($valid->fails()) {
            throw new \Exception($valid->errors()->first());
        }
        $driver_additional_data = json_decode($driver->driver_additional_data , true);
        if (!$driver_additional_data) {
            throw new \Exception('Driver address not added');
        }
        return [
            'first_name' => $driver->first_name,
            'last_name' => $driver->last_name,
            'tos_acceptance_date' => time(),
            'tos_acceptance_ip' => $driver->device_ip,
            'dob_day' => date('d' , strtotime($driver->dob)),
            'dob_month' => date('m' , strtotime($driver->dob)),
            'dob_year' => date('Y' , strtotime($driver->dob)),
            'ssn' => $driver->ssn,
            'email' => $driver->email,
            'short_code' => $driver->CountryArea->Country->short_code,
            'phone' => $driver->phoneNumber,
            'line1' => $driver_additional_data['address_line_1'],
            'line2' => $driver_additional_data['address_line_2'],
            'city' => $driver_additional_data['city_name'],
            'state' => $driver_additional_data['province'],
            'postal_code' => $driver_additional_data['pincode'],
            'external_account' => [
                'object' => 'bank_account',
                'country' => $driver->CountryArea->Country->short_code,
                'currency' => $driver->CountryArea->Country->isoCode,
                'account_number' => $driver->account_number,
                'routing_number' => $driver->routing_number
            ],
            'document' => [
//                'back' => $verification_details['photo_id_back'],
                'front' => $verification_details['photo_id_front']
            ]
        ];
    }

    public static function UnitedStateCreateAccount($user_details){
        try {
            $account = \Stripe\Account::create([
                'type' => 'custom',
                'country' => $user_details['short_code'],
                'requested_capabilities' => [
                    'transfers',
                    'card_payments'
                ],
                'business_type' => 'individual',
                'business_profile' => [
                    'url' => 'https://www.apporio.com',
                    'mcc' => '5045'
                ],
                'tos_acceptance' => [
                    'date' => $user_details['tos_acceptance_date'],
                    'ip' => $user_details['tos_acceptance_ip']
                ],
                'individual' => [
                    'first_name' => $user_details['first_name'],
                    'last_name' => $user_details['last_name'],
                    'dob' => [
                        'day' => $user_details['dob_day'],
                        'month' => $user_details['dob_month'],
                        'year' => $user_details['dob_year']
                    ],
                    'ssn_last_4' => substr($user_details['ssn'] , -4),
                    'id_number' => $user_details['ssn'],
                    'phone' => $user_details['phone'],
                    'email' => $user_details['email'],
                    'address' => [
                        'line1' => $user_details['line1'],
                        'line2' => $user_details['line2'],
                        'city' => $user_details['city'],
                        'state' => $user_details['state'],
                        'postal_code' => $user_details['postal_code']
                    ],
                    'verification' => [
                        'document' => $user_details['document']
                    ]
                ],
                'external_account' => $user_details['external_account']
            ]);
            return $account;
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}