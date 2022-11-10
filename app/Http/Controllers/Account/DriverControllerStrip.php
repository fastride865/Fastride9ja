<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\PaymentSplit\StripeConnect;
use App\Models\Configuration;
use App\Models\Driver;
use App\Models\DriverConfiguration;
use App\Models\DriverDocument;
use App\Models\DriverVehicleDocument;
use App\Models\MerchantStripeConnect;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use App\Traits\DriverTrait;

class DriverControllerStrip extends Controller
{
    use ImageTrait, DriverTrait;

    public function RegisterToStripeConnect(Request $request)
    {
        $driver = Driver::findOrFail($request->driver_id);
        $validator_array = array(
            'driver_id' => 'required|exists:drivers,id',
            'ip_address' => 'required',
            // 'ssn' => 'required|unique:drivers,ssn,'.$driver->id,
            //'identity_document' => 'required|file',
            'account_number' => 'required',
            'postal_code' => 'required',
            'address_line_1' => 'required',
            'city' => 'required',
        );
        if (empty($driver->dob)) {
            $validator_array = array_merge($validator_array, array('dob' => 'required'));
        }
        $short_code = $driver->CountryArea->Country->short_code;
        switch ($short_code) {
            case 'US':
                $validator_array = array_merge($validator_array, array(
                    'routing_number' => 'required',
                    'state' => 'required|alpha|size:2',
                    'address_line_2' => 'required',
                ));
                break;
            case 'AU': // If contry is Australia
                $validator_array = array_merge($validator_array, array(
                    // 'routing_number' => 'required',
                    'account_holder_name' => 'required',
                    'bsb_number' => 'required',
                    'abn' => 'required',
                    'state' => 'required|alpha',
                ));
                break;
        }
        $valid = validator($request->all(), $validator_array);
        if ($valid->fails()) {
            return error_response($valid->errors()->first());
        }
        $stripe_connect_config = MerchantStripeConnect::where('merchant_id', $driver->merchant_id)->first();
        if (empty($stripe_connect_config)) {
            return error_response(trans("$string_file.configuration_not_found"));
        }

        $stripe_documents = self::getStripeRelatedDocuments($stripe_connect_config, $driver);

        $driver_additional_data = array(
            "pincode" => $request->postal_code,
            "address_line_1" => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            "province" => $request->state,
            'city_name' => $request->city,
        );
        $driver_additional_data = json_encode($driver_additional_data, true);
        DB::beginTransaction();
        try {
            $driver->ssn = $stripe_documents['personal_document']['doc_number']; // For Australia it will be unique id number
            $driver->device_ip = $request->ip_address;
            if (isset($request->dob) && $request->dob != '') {
                $driver->dob = formatted_date($request->dob);
            }
            $driver->sc_identity_photo = $stripe_documents['personal_document']['image_name'];
            $driver->sc_identity_photo_status = 'pending';
            $driver->account_number = $request->account_number;
            $driver->routing_number = $request->routing_number;
            $driver->driver_additional_data = $driver_additional_data;
            $driver->account_holder_name = isset($request->account_holder_name) ? $request->account_holder_name : null;
            $driver->bsb_number = isset($request->bsb_number) ? $request->bsb_number : null;
            $driver->abn_number = isset($request->abn) ? $request->abn : null;
            $driver->save();

            //$personal_id = StripeConnect::upload_file($stripe_documents['personal_document']['image'], $driver->merchant_id, 'customer_signature');
            $photo_front_id = StripeConnect::upload_file($stripe_documents['photo_front_document']['image'], $driver->merchant_id, 'identity_document');
            $photo_back_id = StripeConnect::upload_file($stripe_documents['photo_back_document']['image'], $driver->merchant_id, 'identity_document');
            $additional_id = StripeConnect::upload_file($stripe_documents['additional_document']['image'], $driver->merchant_id, 'additional_verification');
            $verification_details = [
                'personal_id' => NULL,
                'photo_front_id' => $photo_front_id->id,
                'photo_back_id' => $photo_back_id->id,
                'additional_id' => $additional_id->id
            ];
            if (!empty($driver->sc_account_id)) {
                $driver = StripeConnect::update_driver_account($driver, $verification_details);
            } else {
                $driver = StripeConnect::create_driver_account($driver, $verification_details);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return error_response($e->getMessage(), $e->getTrace());
        }
        DB::commit();
        return success_response(trans('api.stripe_register_success'), $driver);
    }

    public function CheckStripeConnect(Request $request)
    {
        $valid = validator($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
        ]);
        if ($valid->fails()) {
            return error_response($valid->errors()->first());
        }
        try {
            $driver = Driver::findOrFail($request->driver_id);
            $config = Configuration::where('merchant_id', $driver->merchant_id)->first();
            if (isset($config->stripe_connect_enable) && $config->stripe_connect_enable != 1) {
                return error_response(trans("$string_file.configuration_not_found"));
            }
            $sc_account_status = $driver->sc_account_status == 'active' ? '1' : ($driver->sc_account_status == null ? '3' : '2');
            $sc_account_text = $driver->sc_account_status == 'active' ? '' : ($driver->sc_account_status == null ? '3' : __('api.stripe_account_pending'));
            if ($driver->sc_account_id) {
                return response()->json(['result' => $sc_account_status, 'message' => $sc_account_text]);
            } else {
                return response()->json(['result' => "4", 'message' => "Driver not registered for stripe connect"]);
            }
        } catch (\Exception $e) {
            return error_response($e->getMessage(), $e->getTrace());
        }
    }
}