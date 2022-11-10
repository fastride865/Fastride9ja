<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\Merchant;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\DriverVehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Passport\Client;
use DB;
use App\Traits\ImageTrait;

class VehicleOwner extends Controller
{
    use ImageTrait;
    public function TrackingDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driverId' => 'required|exists:drivers,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $driver = Driver::find($request->driverId);
        $carIcon = "";
        $driver_vehicle = DriverVehicle::with('VehicleType')->where([['driver_id', '=', $request->driverId], ['vehicle_active_status', '=', 1]])->first();
        if (!empty($driver_vehicle)) {
            $carIcon = $driver_vehicle->vehicleTypeMapImage;
        }
        $data = [
            "latitude" => $driver->current_latitude,
            "longitude" => $driver->current_longitude,
            "bearing_factor" => $driver->bearing,
            "accuracy" => $driver->accuracy,
            "car_icon" => $carIcon,
        ];
        return [
            'result' => "1",
            'message' => trans('api.earning'),
            'data' => $data
        ];
    }

    public function Earnings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $area = $request->user('vehicle_owner')->CountryArea;
        switch ($area->driver_earning_duration) {
            case "1":
                $from = $request->date;
                $to = $request->date;
                break;
            default:
                $from = "";
                $to = "";
        }
        $vehicleList = $request->user('vehicle_owner')->VehicleList;
        if (empty($vehicleList->toArray())) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }
        $driver_vehicle_id = array_pluck($vehicleList, 'id');
        $data = Booking::where([['booking_closure', '=', 1]])
            ->whereDate('created_at', '=', $request->date)
            ->whereIn('driver_vehicle_id', $driver_vehicle_id)
            ->get();
        if (empty($data->toArray())) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }
        $currency = $area->Country->isoCode;
        $merchant_id = $area->merchant_id;
        return [
            'result' => "1",
            'message' => trans('api.earning'),
            'data' => [
                $this->MainHolder($data, $from, $to, $merchant_id, $currency)
            ]
        ];
    }

    public function MainHolder($data, $from, $to, $merchant_id, $currency)
    {
        $newFormat = new Merchant();
        $amount = $currency . " " . $newFormat->TripCalculation(array_sum(array_pluck($data, 'driver_cut')), $merchant_id);
        return [
            "id" => $from,
            "left_text" => $from == $to ? date('m/d', strtotime($from)) : date('m/d', strtotime($from)) . "-" . date('m/d', strtotime($to)),
            "left_text_color" => "#A9A9A9",
            "left_text_bold" => false,
            "right_text" => $amount,
            "right_text_color" => "#181818",
            "right_text_bold" => true,
            'details' => [
                "heading" => $from == $to ? date('l,d', strtotime($from)) : date('l,d', strtotime($from)) . "-" . date('l,d', strtotime($to)),
                "amount" => $amount,
                "holders" => $this->DetailHolder($data, $merchant_id, $currency),
                'daily_earings' => $this->daily_earings($data, $merchant_id, $currency),
            ]
        ];
    }

    public function DetailHolder($data, $merchant_id, $currency)
    {
        $newFormat = new Merchant();
        $totalAmount = $newFormat->TripCalculation(array_sum(array_pluck($data, 'final_amount_paid')), $merchant_id);
        $DriverAmount = $currency . " " . $newFormat->TripCalculation(array_sum(array_pluck($data, 'driver_cut')), $merchant_id);
        $cashCollected = $totalAmount - array_sum(array_pluck($data, 'company_cut'));
        $cashCollected = $newFormat->TripCalculation($cashCollected, $merchant_id);
        return [
            $this->Holder(trans('api.totalearning'), $DriverAmount),
            $this->Holder(trans('api.cashCollected'), $cashCollected),
        ];
    }

    public function daily_earings($data, $merchant_id, $currency)
    {
        $newFormat = new Merchant();
        $daily = [];
        foreach ($data as $values) {
            $daily[] = [
                "left_text" => date('l,d', strtotime($values->created_at)),
                "left_text_color" => "#A9A9A9",
                "left_text_bold" => true,
                "right_text" => $currency . " " . $newFormat->TripCalculation($values->driver_cut, $merchant_id),
                "right_text_color" => "#181818",
                "right_text_bold" => false
            ];
        }
        return $daily;
    }

    public function Holder($message, $totalAmount)
    {
        return [
            "left_text" => $message,
            "left_text_color" => "#A9A9A9",
            "left_text_bold" => false,
            "right_text" => $totalAmount,
            "right_text_color" => "#181818",
            "right_text_bold" => false
        ];
    }

    public function HomeScreen(Request $request)
    {
        $merchant_id = null; //@added by amba
        $vehicleList = $request->user('vehicle_owner')->VehicleList;
        $onlineDriver = $vehicleList->filter(function ($vehicle) {
            return $vehicle->vehicle_active_status == 1;
        });
        $online_drivers = [];
        foreach ($onlineDriver as $value) {
            $online_drivers[] = [
                'id' => $value->id,
                'name' => $value->Driver->fullName,
                'image' => get_image($value->Driver->profile_image,'driver',$merchant_id),
                'contact_number' => $value->Driver->phoneNumber,
                'VehicleTypeName' => $value->VehicleType->VehicleTypeName,
                'vehicleTypeImage' => get_image($value->VehicleType->vehicleTypeImage,'vehicle',$merchant_id),
                'VehicleMakeName' => $value->VehicleMake->VehicleMakeName,
                'VehicleModelName' => $value->VehicleModel->VehicleModelName,
                'shareCode' => $value->shareCode,
                'vehicle_number' => $value->vehicle_number,
                'vehicle_color' => $value->vehicle_color,
                'vehicle_image' => get_image($value->vehicle_image,'vehicle_document',$merchant_id),
                'vehicle_number_plate_image' => get_image($value->vehicle_number_plate_image,'vehicle_document',$merchant_id),
            ];
        }
        $offlineDrivers = DriverVehicle::whereHas('Drivers', function ($q) {
            $q->where('online_offline', '=', 2);
        })->with(['Drivers' => function ($query) {
            $query->where('online_offline', '=', 2);
        }])->where([['ownerType', '=', 2], ['owner_id', '=', $request->user('vehicle_owner')->id]])->get();
        $offline_drivers = [];
        if (!empty($offlineDrivers->toArray())) {
            foreach ($offlineDrivers as $value) {
                $offline_drivers[] = [
                    'id' => $value->id,
                    'name' => $value->Driver->fullName,
                    'image' => $value->Driver->profile_image,
                    'contact_number' => $value->Driver->phoneNumber,
                    'VehicleTypeName' => $value->VehicleType->VehicleTypeName,
                    'vehicleTypeImage' => $value->VehicleType->vehicleTypeImage,
                    'VehicleMakeName' => $value->VehicleMake->VehicleMakeName,
                    'VehicleModelName' => $value->VehicleModel->VehicleModelName,
                    'shareCode' => $value->shareCode,
                    'vehicle_number' => $value->vehicle_number,
                    'vehicle_color' => $value->vehicle_color,
                    'vehicle_image' => $value->vehicle_image,
                    'vehicle_number_plate_image' => $value->vehicle_number_plate_image,
                ];
            }
        }
        return response()->json(['result' => "1", 'message' => trans("$string_file.data_not_found"), 'data' => ['total_earnings' => '$ 1400', 'online_drivers' => $online_drivers, 'offline_drivers' => $offline_drivers]]);
    }

    public function VehicleList(Request $request)
    {
        $vehicleList = $request->user('vehicle_owner')->VehicleList;
        if (empty($vehicleList->toArray())) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }
        $list = [];
        foreach ($vehicleList as $value) {
            $list[] = [
                'id' => $value->id,
                'VehicleTypeName' => $value->VehicleType->VehicleTypeName,
                'vehicleTypeImage' => $value->VehicleType->vehicleTypeImage,
                'VehicleMakeName' => $value->VehicleMake->VehicleMakeName,
                'VehicleModelName' => $value->VehicleModel->VehicleModelName,
                'shareCode' => $value->shareCode,
                'vehicle_number' => $value->vehicle_number,
                'vehicle_color' => $value->vehicle_color,
                'vehicle_image' => $value->vehicle_image,
                'vehicle_number_plate_image' => $value->vehicle_number_plate_image,
                'vehicle_active_status' => $value->vehicle_active_status,
                'vehicle_verification_status' => $value->vehicle_verification_status,
            ];
        }
        return response()->json(['result' => "1", 'message' => trans('api.vehiclelistdriver'), 'data' => $list]);
    }

    public function VehicleConfiguration(Request $request)
    {
        $vehicleTypes = $request->user('vehicle_owner')->CountryArea->VehicleType;
        foreach ($vehicleTypes as $k => $value) {
            $oldArray[$value['id']]['id'] = $value['id'];
            $oldArray[$value['id']]['vehicleTypeName'] = $value->VehicleTypeName;
            $oldArray[$value['id']]['vehicleTypeImage'] = $value['vehicleTypeImage'];
            $oldArray[$value['id']]['vehicleTypeMapImage'] = $value['vehicleTypeMapImage'];
        }
        foreach ($oldArray as $item) {
            $vehicleArray[] = $item;
        }
        $merchant_id = $request->user('vehicle_owner')->merchant_id;
        $vehicleMake = VehicleMake::where('merchant_id', $merchant_id)->get();
        foreach ($vehicleMake as $value) {
            $value->vehicleMakeName = $value->VehicleMakeName;
        }
        return response()->json(['result' => "1", 'message' => trans('api.vehicleconfig'), 'data' => ['vehicle_type' => $vehicleArray, 'vehicle_make' => $vehicleMake]]);
    }

    public function VehicleModel(Request $request)
    {
        $details = $request->user('vehicle_owner');
        $merchant_id = $details->merchant_id;
        $validator = Validator::make($request->all(), [
            'vehicle_type_id' => ['required',
                Rule::exists('vehicle_types', 'id')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })],
            'vehicle_make_id' => ['required',
                Rule::exists('vehicle_makes', 'id')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $vehicleModels = VehicleModel::where([['vehicle_type_id', '=', $request->vehicle_type_id], ['vehicle_make_id', '=', $request->vehicle_make_id]])->get();
        if (!empty($vehicleModels->toArray())) {
            foreach ($vehicleModels as $vehicle) {
                $vehicle->vehicleModelName = $vehicle->VehicleModelName;
            }
            return response()->json(['result' => "1", 'message' => trans('api.vehiclemodel'), 'data' => $vehicleModels]);
        } else {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }
    }

    public function detail(Request $request)
    {
        return response()->json(['result' => "1", 'message' => trans('api.details'), 'data' => $request->user('vehicle_owner')]);
    }

    public function login(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'phone' => ['required',
                Rule::exists('vehicle_owners', 'phone')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $client = Client::where([['user_id', '=', $merchant_id], ['password_client', '=', 1]])->first();
        Config::set('auth.guards.api.provider', 'vehicleOwner');
        $request->request->add([
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $request->phone,
            'password' => $request->password,
            'scope' => '',
        ]);
        $token_generation_after_login = Request::create(
            'oauth/token',
            'POST'
        );
        $collect_response = \Route::dispatch($token_generation_after_login)->getContent();
        $collectArray = json_decode($collect_response);
        if (isset($collectArray->error)) {
            return response()->json(['result' => "0", 'message' => $collectArray->message, 'data' => []]);
        }
        return response()->json(['result' => "1", 'message' => trans("$string_file.signup_done"), 'data' => ['access_token' => $collectArray->access_token, 'refresh_token' => $collectArray->refresh_token]]);
    }

    public function Signup(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $country_id = $request->country_id;
        $validator = Validator::make($request->all(), [
            'country_id' => ['required',
                Rule::exists('countries', 'id')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })],
            'area_id' => ['required',
                Rule::exists('country_areas', 'id')->where(function ($query) use ($merchant_id, &$country_id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['country_id', '=', $country_id]]);
                })],
            'first_name' => 'required',
            'email' => ['required',
                Rule::unique('vehicle_owners', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })],
            'phone' => ['required',
                Rule::unique('vehicle_owners', 'phone')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })],
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        DB::beginTransaction();
                try {
                    $vehicleOwner = \App\Models\VehicleOwner::create([
                        'merchant_id' => $merchant_id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'country_area_id' => $request->area_id,
                        'password' => Hash::make($request->password),
                    ]);
                    if ($request->hasFile('profile_image')) {
                        $vehicleOwner->profile_image = $this->uploadImage('profile_image','owner',$merchant_id);
                        $vehicleOwner->save();
                    }
                    $client = Client::where([['user_id', '=', $merchant_id], ['password_client', '=', 1]])->first();
                    Config::set('auth.guards.api.provider', 'vehicleOwner');
                    $request->request->add([
                        'grant_type' => 'password',
                        'client_id' => $client->id,
                        'client_secret' => $client->secret,
                        'username' => $request->phone,
                        'password' => $request->password,
                        'scope' => '',
                    ]);
                    $token_generation_after_login = Request::create(
                        'oauth/token',
                        'POST'
                    );
                    $collect_response = \Route::dispatch($token_generation_after_login)->getContent();
                    $collectArray = json_decode($collect_response);
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    p($message);
                    // Rollback Transaction
                    DB::rollback();
                }
                DB::commit();

        if (isset($collectArray->error)) {
            return response()->json(['result' => "0", 'message' => $collectArray->message, 'data' => []]);
        }
        return response()->json(['result' => "1", 'message' => trans("$string_file.signup_done"), 'data' => ['access_token' => $collectArray->access_token, 'refresh_token' => $collectArray->refresh_token]]);
    }

    public function EditProfile(Request $request)
    {
        $details = $request->user('vehicle_owner');
        $id = $details->id;
        $merchant_id = $details->merchant_id;
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'email' => ['required',
                Rule::unique('vehicle_owners', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })->ignore($id)],
            'phone' => ['required',
                Rule::unique('vehicle_owners', 'phone')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })->ignore($id)],
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $details->first_name = $request->first_name;
        $details->last_name = $request->last_name;
        $details->phone = $request->phone;
        $details->email = $request->email;
        if ($request->hasFile('profile_image')) {
            $profile_image = $this->uploadImage('profile_image','owner',$merchant_id);
            $details->profile_image = $profile_image;
        }
        $details->save();
        return response()->json(['result' => "1", 'message' => trans('api.message77'), 'data' => $details]);
    }
}
