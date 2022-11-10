<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\PaymentSplit\StripeConnect;
use App\Models\Configuration;
use App\Models\Driver;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    use ImageTrait;
    public function RegisterToStripeConnect(Request $request) {
        $validator_array = array(
            'driver_id' => 'required|exists:drivers,id',
            'ip_address' => 'required',
            'dob' => 'required',
            'ssn' => 'required|unique:drivers,ssn',
            'identity_document' => 'required|file',
            'account_number' => 'required',
            'postal_code' => 'required',
            'address_line_1' => 'required',
            'city' => 'required',
        );
        $driver = Driver::find($request->driver_id);
        $short_code = $driver->CountryArea->Country->short_code;
        switch ($short_code){
            case 'US':
                $validator_array = array_merge($validator_array,array(
                    'routing_number' => 'required',
                    'state' => 'required|alpha|size:2',
                    'address_line_2' => 'required',
                ));
                break;
            case 'AU': // If contry is Australia
                $validator_array = array_merge($validator_array,array(
                    // 'routing_number' => 'required',
                    'account_holder_name' => 'required',
                    'bsb_number' => 'required',
                    'abn' => 'required',
                    'state' => 'required|alpha',
                ));
                break;
        }
        $valid = validator($request->all(),$validator_array);
        if ($valid->fails()) {
            return error_response($valid->errors()->first());
        }

        if ($driver->sc_account_id) {
            return error_response('Driver already registered to stripe connect');
        }

        DB::beginTransaction();
        $driver_additional_data = array(
            "pincode" => $request->postal_code,
            "address_line_1" => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            "province" => $request->state,
            'city_name' => $request->city,
        );
        $driver_additional_data = json_encode($driver_additional_data, true);
        $photo = $this->uploadImage('identity_document' , 'driver' , $driver->merchant_id);
        try {
            $driver->ssn = $request->ssn; // For Australia it will be unique id number
            $driver->device_ip = $request->ip_address;
            $driver->dob = formatted_date($request->dob);
            $driver->sc_identity_photo = $photo;
            $driver->sc_identity_photo_status = 'pending';
            $driver->account_number = $request->account_number;
            $driver->routing_number = $request->routing_number;
            $driver->driver_additional_data = $driver_additional_data;
            $driver->account_holder_name = isset($request->account_holder_name) ? $request->account_holder_name : null;
            $driver->bsb_number = isset($request->bsb_number) ? $request->bsb_number : null;
            $driver->abn_number = isset($request->abn) ? $request->abn : null;
            $driver->save();
            // upload image to stripe connect
            $stripe_file = StripeConnect::upload_file($request->identity_document, $driver->merchant_id, 'identity_document');
            $verification_details = [
                'photo_id_front' => $stripe_file->id,
                'additional_document_front' => $stripe_file->id
            ];
            $driver = StripeConnect::create_driver_account($driver , $verification_details);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return error_response($e->getMessage() , $e->getTrace());
        }
        DB::commit();
        return success_response('' , $driver);
    }

    public function CheckStripeConnect(Request $request)
    {
        $valid = validator($request->all(),[
            'driver_id' => 'required|exists:drivers,id',
        ]);
        if ($valid->fails()) {
            return error_response($valid->errors()->first());
        }
        try{
            $driver = Driver::findOrFail($request->driver_id);
            $config = Configuration::where('merchant_id',$driver->merchant_id)->first();
            if(isset($config->stripe_connect_enable) && $config->stripe_connect_enable != 1){
                return error_response(trans("$string_file.configuration_not_found"));
            }
            $sc_account_status = $driver->sc_account_status == 'active' ? '1' : ($driver->sc_account_status == null ? '3' : '2');
            $sc_account_text = $driver->sc_account_status == 'active' ? '' : ($driver->sc_account_status == null ? '3' : __('api.stripe_account_pending'));
            if ($driver->sc_account_id) {
                return response()->json(['result' => $sc_account_status, 'message' => $sc_account_text]);
            }else{
                return response()->json(['result' => "4", 'message' => "Driver not registered for stripe connect"]);
            }
        }catch (\Exception $e){
            return error_response($e->getMessage() , $e->getTrace());
        }
    }
}