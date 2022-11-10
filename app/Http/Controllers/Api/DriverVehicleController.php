<?php

namespace App\Http\Controllers\Api;

use App;
use App\Models\CountryArea;
use App\Models\Driver;
use App\Models\DriverVehicle;
use App\Models\LanguageVehicleMake;
use App\Models\LanguageVehicleModel;
use App\Models\Merchant;
use App\Models\Onesignal;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleRequest;
use App\Models\VehicleOwnerDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Traits\ImageTrait;
use App\Traits\ApiResponseTrait;
use App\Traits\DriverVehicleTrait;
use App\Traits\MerchantTrait;
use function foo\func;
use function PHPUnit\Framework\returnArgument;

class DriverVehicleController extends Controller
{
    use ImageTrait, DriverVehicleTrait, ApiResponseTrait,MerchantTrait;

    public function AttechVehicle(Request $request)
    {
        $details = $request->user('vehicle_owner');
        $merchant_id = $details->merchant_id;
        $vehicle_type_id = $request->vehicle_type_id;
        $vehicle_make_id = $request->vehicle_make_id;
        $validator = Validator::make($request->all(), [
            'vehicle_type_id' => ['required',
                Rule::exists('vehicle_types', 'id')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })],
            'vehicle_make_id' => ['required',
                Rule::exists('vehicle_makes', 'id')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })],
            'vehicle_model_id' => ['required',
                Rule::exists('vehicle_models', 'id')->where(function ($query) use ($vehicle_type_id, $vehicle_make_id) {
                    return $query->where([['vehicle_type_id', '=', $vehicle_type_id], ['vehicle_make_id', '=', $vehicle_make_id]]);
                })],
            'vehicle_number' => ['required',
                Rule::unique('driver_vehicles', 'vehicle_number')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['vehicle_delete', '=', NULL]]);
                })],
            'vehicle_color' => 'required',
            'vehicle_image' => 'required|base64image',
            'vehicle_number_plate_image' => 'required|base64image',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }

        if (!empty($request->vehicle_image)) {
            list($format, $image) = explode(',', $request->vehicle_image);
            $temp = explode('/', $format);
            list($ext,) = explode(';', $temp[1]);
            $file_name = time() . "driverVehicle." . $ext;
            \File::put(public_path() . '/driverVehicle/' . $file_name, base64_decode($image));
            $image = "driverVehicle/$file_name";
        }

        if (!empty($request->vehicle_number_plate_image)) {
            list($format1, $image1) = explode(',', $request->vehicle_number_plate_image);
            $temp1 = explode('/', $format);
            list($ext,) = explode(';', $temp1[1]);
            $file_name = time() . "driverVehicle." . $ext;
            \File::put(public_path() . '/driverVehicle/' . $file_name, base64_decode($image));
            $image1 = "driverVehicle/$file_name";
        }
        $vehicle = DriverVehicle::create([
            'owner_id' => $details->id,
            'ownerType' => 2,
            'merchant_id' => $merchant_id,
            'vehicle_type_id' => $request->vehicle_type_id,
            'shareCode' => getRandomCode(10),//str_slug($request->input('vehicle_number')),
            'vehicle_make_id' => $request->vehicle_make_id,
            'vehicle_model_id' => $request->vehicle_model_id,
            'vehicle_number' => $request->vehicle_number,
            'vehicle_color' => $request->vehicle_color,
            'vehicle_image' => $image,
            'vehicle_number_plate_image' => $image1,
        ]);
        return response()->json(['result' => "1", 'message' => trans('api.vehicleadded'), 'data' => $vehicle]);
    }

    public function ChangeVehicle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_vehicle' => ['required', 'exists:driver_vehicles,id'],
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $driver = $request->user('api-driver');

        $Vehicle = DriverVehicle::with(['Drivers' => function ($q) use ($driver) {
            $q->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 2]]);
        }])->whereHas('Drivers', function ($query) use ($driver) {
            $query->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 2]]);
        })->where([['id', '=', $request->driver_vehicle]])->first();

        if (empty($Vehicle)) {
            return response()->json(['result' => "1", 'message' => trans('api.NotEligble'), 'data' => []]);
        } else {
            $oldVehicle = DriverVehicle::with(['Drivers' => function ($p) use ($driver) {
                $p->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 1]]);
            }])->whereHas('Drivers', function ($q) use ($driver) {
                $q->where([['driver_id', '=', $driver->id], ['vehicle_active_status', '=', 1]]);
            })->first();

            if (!empty($oldVehicle->id)) {
                $oldVehicle->Drivers()->updateExistingPivot($driver->id, ['vehicle_active_status' => 2]);
            }
            $Vehicle->Drivers()->updateExistingPivot($driver->id, ['vehicle_active_status' => 1]);

            $request->user('api-driver')->pool_ride_active = 2;
            $request->user('api-driver')->save();
            return response()->json(['result' => "1", 'message' => trans('api.vehicleActivate'), 'data' => $Vehicle]);
        }
    }

    public function getVehicleList(Request $request)
    {
        try {
            $driver = $request->user('api-driver');
            $driver_id = $driver->id;
            $string_file = $this->getStringFile(NULL, $driver->Merchant);
            $driverVechile = DriverVehicle::select('id', 'driver_id', 'merchant_id', 'vehicle_model_id', 'vehicle_type_id', 'vehicle_number', 'vehicle_make_id', 'vehicle_color', 'vehicle_image', 'vehicle_number_plate_image', 'vehicle_verification_status','shareCode')
                ->whereHas('Drivers', function ($q) use ($driver_id) {
                    $q->where([['driver_id', '=', $driver_id]]);
                })->with('VehicleType')->where('vehicle_delete', NULL)
                ->latest()->get();

            if (empty($driverVechile->toArray())) {
                return $this->failedResponse(trans("$string_file.data_not_found"));
//                return response()->json(['result' => "0", 'message' => trans('api.vehiclelistdriver'), 'data' => []]);
            }
//        $readylive = 2;
            foreach ($driverVechile as $key => $value) {
                $drivers = $value->Drivers;
                $driversDetails = array();
                $Datakey = '';
                foreach ($drivers as $driverData) {
                    $driversDetails[$driverData->id] = $driverData->online_offline == 1 ? 1 : 2;
                }
                if (in_array(1, $driversDetails)) {
                    $Datakey = array_search(1, $driversDetails);
                }
                if (isset($Datakey)) {
                    $driverName = Driver::select('first_name', 'last_name')->find($Datakey);
                }

                $activeDriverName = isset($driverName) ? $driverName->first_name . ' ' . $driverName->last_name : $value->Driver->fullName;

                $merchant_id = $value->merchant_id;

                //  Check if any driver is online with this vehicle
                $active_vehicle_details = [];
                foreach ($value->Drivers as $driver) {
                    if ($driver->pivot->vehicle_active_status == 1) {
                        $activeDriverName = isset($driver) ? $driver->first_name . ' ' . $driver->last_name : $driver->fullName;
                        $activeDriverID = $driver->id;
                        $active_vehicle_details = array('activeDriverName' => $activeDriverName, 'activeDriverID' => $activeDriverID, 'driver_vehicle_id' => $driver->pivot->driver_vehicle_id);
                    }
                }
                $active_status = 2;
                $status_message = "";
                $message_background_color = "";
                $pending = 0;
                switch ($value->vehicle_verification_status) {
                    case "1":
                        $status_message = trans("$string_file.pending");
                        $message_background_color = '0091FF';
                        $document_not_pending = check_driver_document($driver_id, 'vehicle', $value->id);
                        if ($document_not_pending == true) {
                            //p($value->ServiceTypes->count());
                            if ($value->ServiceTypes->count() == 0) {
                                $pending = 2;
                                $message_background_color = '6D7278';
                                $status_message = trans('api.pending_segment_config');
                                //p($status_message);
                            }
                        } else {
                            $pending = 1;
                            $message_background_color = 'F7B500';
                            $status_message = trans("$string_file.document_pending");
                        }

                        break;
                    case "2":
                        if (empty($active_vehicle_details)) {
//                        $status = trans('api.message183');
                            $message_background_color = '7DBB3A';
                            $status_message = trans("$string_file.verified");
                            $pending = 2;
                        } else {
                            $pending = 0;
                            $message_background_color = 'B620E0';
                            $activated_by_string = trans("$string_file.activated_by");
                            if (!empty($active_vehicle_details) && $active_vehicle_details['activeDriverID'] == $driver_id) {
                                $status_message = $activated_by_string.' '.$active_vehicle_details['activeDriverName'];
//                                    trans('api.message185', ['name' => $active_vehicle_details['activeDriverName']]);
                                $active_status = 1;
                            } else {
                                $status_message = $activated_by_string.' '.$active_vehicle_details['activeDriverName'];
//                                    trans('api.message185', ['name' => $active_vehicle_details['activeDriverName']]);
                            }
                        }
                        break;
                    case "3":
                        $pending = 1;
                        $status_message = trans("$string_file.document_rejected");
                        $message_background_color = 'E02020';
                        $document_not_pending = check_driver_document($driver_id, 'vehicle', $value->id, 3);

                        break;
                    case "4":
                        $pending = 1;
                        $message_background_color = 'E02020';
                        $status_message =trans("$string_file.document_expired");
                        $document_not_pending = check_driver_document($driver_id, 'vehicle', $value->id, 4);
                        break;
                }

                //$service_types = array_pluck($value->ServiceTypes, 'serviceName');
                $value->vehicle_type = $value->VehicleType->VehicleTypeName;
                $value->vehicle_type_image = get_image($value->VehicleType->vehicleTypeImage, 'vehicle', $merchant_id);
                $value->pool_enable = $value->VehicleType->pool_enable;
                $value->vehicle_make = $value->VehicleMake->VehicleMakeName;
                // $value->vehicle_make_logo = get_image($value->VehicleMake->vehicleMakeLogo,'vehicle',$merchant_id);
                $value->vehicle_model = $value->VehicleModel->VehicleModelName;
                $value->active_status = $active_status;
                $value->show_message = $status_message;
                $value->message_background_color = $message_background_color;
                $value->other_pending = $pending; // 0 : nothing 1: document, 2 segment config,:
                //$value->service_types = implode(',', $service_types);
                $activeStatus[] = $active_status;
                unset($value->Drivers);
                unset($value->merchant_id);
                unset($value->driver_id);
                unset($value->vehicle_make_id);
                unset($value->vehicle_model_id);
                unset($value->vehicle_type_id);
            }
            $driverVechile = $driverVechile->toArray();
            array_multisort($activeStatus, SORT_ASC, $driverVechile);
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans("$string_file.success"),$driverVechile);
//        return response()->json(['result' => "1", 'message' => trans('api.vehiclelistdriver'), 'data' => $driverVechile]);
    }

    public function PoolOnOff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pool_status' => 'required|integer|min:1|max:2',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $driverdata = $request->user('api-driver');
        if ($request->pool_status == 1):
            $servives = array_pluck($driverdata->CountryArea->ServiceTypes, 'id');
            $Vehicle = DriverVehicle::with('VehicleType')
                ->whereHas('Drivers', function ($q) use ($driverdata) {
                    $q->where([['driver_id', '=', $driverdata->id], ['vehicle_active_status', '=', 1]]);
                })->with(['Drivers' => function ($q) use ($driverdata) {
                    $q->where([['driver_id', '=', $driverdata->id], ['vehicle_active_status', '=', 1]]);
                }])->first();
            if (empty($Vehicle) || $Vehicle->VehicleType->pool_enable != 1 || !in_array(5, $servives)) {
                return response()->json(['result' => "0", 'message' => trans('api.message145'), 'data' => []]);
            }
            $driverdata->pool_ride_active = 1;
            $driverdata->avail_seats = $Vehicle->VehicleModel->vehicle_seat;
            $driverdata->occupied_seats = 0;
            $driverdata->status_for_pool = 1;
            $driverdata->pick_exceed = NULL;
            $driverdata->pool_user_id = NULL;
        else:
            if ($request->user('api-driver')->occupied_seats != 0):
                return response()->json(['result' => "0", 'message' => 'You Have Riders', 'data' => []]);
            else:
                $driverdata->pool_ride_active = $request->pool_status;
            endif;
        endif;
        $driverdata->save();
        $message = $request->pool_status == 1 ? trans('api.poolon') : trans('api.pooloff');
        return response()->json(['result' => "1", 'message' => $message, 'data' => []]);
    }



    public function VehicleOtpVerifiy(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $driver_vehicle_id = $request->driver_vehicle_id;
        $validator = Validator::make($request->all(), [
            'driver_vehicle_id' => ['required',
                Rule::exists('driver_vehicles', 'id')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })],
            'driver_id' => ['required',
                Rule::exists('vehicle_requests', 'driver_id')->where(function ($query) use ($driver_vehicle_id) {
                    return $query->where([['driver_vehicle_id', '=', $driver_vehicle_id]]);
                })],
            'otp' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        try {
            $vehicleRequest = VehicleRequest::where([['driver_vehicle_id', '=', $driver_vehicle_id], ['driver_id', '=', $request->driver_id]])->first();
            if ($request->otp != $vehicleRequest->otp) {
                return response()->json(['result' => "0", 'message' => trans('api.message179'), 'data' => []]);
            }
            $vehicle = DriverVehicle::find($driver_vehicle_id);
            $vehicle->Drivers()->attach($request->driver_id, ['vehicle_active_status' => 2]);

            $driver = Driver::find($request->driver_id);
            if ($driver == 1) {
                $driver->signupStep = 2;
                $driver->save();
            }
            return response()->json(['result' => "1", 'message' => trans('api.message180'), 'data' => []]);
        } catch (\Exception $e) {
            return response()->json(['result' => "0", 'message' => $e->getMessage(), 'data' => []]);
        }
    }

    // vehicle request by shared code
    public function vehicleRequest(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $driver_id = $request->driver_id;
        $validator = Validator::make($request->all(), [
            'driver_id' => ['required',
                Rule::exists('drivers', 'id')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })],
            'code' => ['required',
                Rule::exists('driver_vehicles', 'shareCode')->where(function ($query) use ($merchant_id, &$driver_id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['owner_id', '!=', $driver_id]]);
                })],

        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        try {
            $string_file = $this->getStringFile($merchant_id);


            $vehicle = DriverVehicle::where([['vehicle_verification_status','=', 2], ['shareCode', '=', $request->code], ['merchant_id', '=', $merchant_id]])->first();
            if (empty($vehicle)) {
                return $this->failedResponse(trans("$string_file.vehicle_get_warning"));
            }
            $otp = rand(999, 9999);
            $vehiclerequest = VehicleRequest::updateOrCreate(
                ['driver_vehicle_id' => $vehicle->id, 'driver_id' => $request->driver_id],
                [
                    'otp' => $otp,
                    'status' => 1,
                ]
            );
            setLocal($vehicle->Driver->language);
            $title = trans("$string_file.share_vehicle_otp");
            $message = trans("$string_file.otp_for_verification");
            $message = $message . ': ' . $otp;
            $data = [
                'notification_type'=>'SHARE_VEHICLE_OTP',
                'segment_type'=>'SHARE_VEHICLE_OTP',
                'segment_data'=>[
                    'otp'=>$otp
                ],
            ];
            // send otp to vehicle owner
            $arr_param = ['driver_id' => $vehicle->driver_id, 'data' => $data, 'message' => $message, 'merchant_id' => $merchant_id, 'title' => $title, 'large_icon' => ""];
            Onesignal::DriverPushMessage($arr_param);
            setLocal();
//            Onesignal::DriverPushMessage([$vehicle->OwnerDriver->id], $vehiclerequest, $message, 10, $merchant_id);
            $vehicle->vehicle_image = get_image($vehicle->vehicle_image, 'vehicle_document', $merchant_id);
            $vehicle->vehicle_number_plate_image = get_image($vehicle->vehicle_number_plate_image, 'vehicle_document', $merchant_id);
            $vehicle->VehicleTypeName = $vehicle->VehicleType->VehicleTypeName;
            $return_data = [
                'driver_vehicle_id'=>$vehicle->id,
                'vehicle_number'=>$vehicle->vehicle_number,
                'driver_id'=>$vehicle->driver_id,
                'owner_id'=>$vehicle->owner_id,
                'vehicle_type_id'=>$vehicle->vehicle_type_id,
                'shareCode'=>$vehicle->shareCode,
                'vehicle_make_id'=>$vehicle->vehicle_make_id,
                'vehicle_model_id'=>$vehicle->vehicle_model_id,
                'vehicle_color'=>$vehicle->vehicle_color,
                'vehicle_image'=>get_image($vehicle->vehicle_image, 'vehicle_document', $merchant_id),
                'vehicle_number_plate_image'=>get_image($vehicle->vehicle_number_plate_image, 'vehicle_document', $merchant_id),
                'vehicle_type_name'=>$vehicle->VehicleType->VehicleTypeName,
                'vehicle_make_name'=>$vehicle->VehicleMake->VehicleMakeName,
                'vehicle_model_name'=>$vehicle->VehicleModel->VehicleModelName,
                'ac_nonac'=>!empty($vehicle->ac_nonac) ? $vehicle->ac_nonac : 0,
                'baby_seat'=>!empty($vehicle->baby_seat) ? $vehicle->baby_seat : 0,
                'wheel_chair'=>!empty($vehicle->wheel_chair) ? $vehicle->wheel_chair : 0,
            ];
            return $this->successResponse(trans("$string_file.success"),$return_data);

        } catch (\Exception $e) {
            return response()->json(['result' => "0", 'message' => $e->getMessage()]);
        }
    }



    // add vehicle
    public function  addVehicle(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'vehicle_type_id' => 'required',
            'vehicle_make_id' => 'required',
            'vehicle_model_id' => 'required',
            'vehicle_number' => ['required',
                Rule::unique('driver_vehicles', 'vehicle_number')->where(function ($query) use ($merchant_id) {
                    return $query->whereNull('vehicle_delete')->where([['merchant_id', '=', $merchant_id]]);
                })],
            'vehicle_color' => 'required',
            'vehicle_image' => 'required',
            'vehicle_number_plate_image' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $driver_id = $request->driver_id;
            $driver = Driver::find($driver_id);
            $string_file = $this->getStringFile(NULL, $driver->Merchant);
            $signupStep = $driver->signupStep;
            if ($signupStep == 4) {
                $driver->signupStep = 5; // adding vehicle
                $driver->save();
            }
            $image = $this->uploadBase64Image('vehicle_image', 'vehicle_document', $merchant_id);
            $image1 = $this->uploadBase64Image('vehicle_number_plate_image', 'vehicle_document', $merchant_id);
//            $active_status = get_driver_multi_existing_vehicle_status($driver_id);
            $verification_status = get_driver_auto_verify_status($driver_id, $status = 'final_status', $for = 'vehicle');
            $vehicle_seat = $request->vehicle_seat ? $request->vehicle_seat : 3;
            
            
             $vehicle_make_id = $this->vehicleMake($request->vehicle_make_id, $merchant_id);
            $vehicle_model_id = $this->vehicleModel($request->vehicle_model_id, $merchant_id, $vehicle_make_id, $request->vehicle_type_id, $vehicle_seat);
            
            $vehicle = DriverVehicle::create([
                'driver_id' => $request->driver_id,
                'owner_id' => $request->driver_id,
                'merchant_id' => $merchant_id,
                'vehicle_type_id' => $request->vehicle_type_id,
                'shareCode' => getRandomCode(10),
                 'vehicle_make_id' => $vehicle_make_id,
                'vehicle_model_id' => $vehicle_model_id,
                'vehicle_number' => $request->vehicle_number,
                'vehicle_color' => $request->vehicle_color,
                'vehicle_image' => $image,
                'vehicle_number_plate_image' => $image1,
                'ac_nonac' => $request->ac_nonac,
                'baby_seat' => $request->baby_seat,
                'wheel_chair' => $request->wheel_chair,
                'vehicle_additional_data' => $request->vehicle_additional_data,
                'vehicle_verification_status' => $verification_status,
                'vehicle_register_date' => $request->vehicle_register_date,
                'vehicle_expire_date' => $request->vehicle_expire_date,
            ]);

            //sync pivot table for vehicle
            $vehicle->Drivers()->attach($request->driver_id, ['vehicle_active_status' => 2]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            DB::rollback();
            return $this->failedResponse($message);
        }
        DB::commit();
        $return_data = array('driver_vehicle_id' => $vehicle->id);
        return $this->successResponse(trans("$string_file.success"), $return_data);
    }



    public function getVehicleModel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'required',
            'vehicle_make' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $string_file = $this->getStringFile($request->merchant_id);
        $vehicleModels = VehicleModel::where([['merchant_id', '=', $request->merchant_id], ['vehicle_type_id', '=', $request->vehicle_type], ['vehicle_make_id', '=', $request->vehicle_make],['admin_delete','=',NULL]])->get();
        $vehicleModels = $vehicleModels->map(function ($value) {
            return [
                'id' => $value->id,
                'vehicleTypeName' => $value->vehicleModelName,

            ];
        });
        return $this->successResponse(trans("$string_file.success"), $vehicleModels);
    }

    // get vehicle type
    public function vehicleConfiguration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_area_id' => 'required|exists:country_areas,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $merchant = Merchant::Find($merchant_id);
        $merchant_segment = array_pluck($merchant->Segment, 'id');
        $merchant_service = array_pluck($merchant->ServiceType, 'id');
        // p($merchant_service);

        $area_vehicle = CountryArea::select('id')->with(['VehicleType' => function ($q) use ($merchant_segment, $merchant_service, $merchant_id) {
            $q->addSelect('id', 'vehicle_type_id', 'vehicleTypeImage', 'vehicleTypeMapImage');
            $q->where('admin_delete',NULL);
            $q->where('merchant_id', $merchant_id);
            $q->whereIn('segment_id', $merchant_segment);
            $q->whereIn('service_type_id', $merchant_service);
            $q->orderBy('vehicleTypeRank');
        }])->whereHas('VehicleType',function($qq){
            $qq->where('admin_delete',NULL);
        })
            ->find($request->country_area_id);

        $return_data = (object)[];
        if (!empty($area_vehicle)) {
            $vehicleTypes = $area_vehicle->VehicleType->unique();
            $vehicleTypes = $vehicleTypes->map(function ($value) use ($merchant_id) {
                return [
                    'id' => $value->id,
                    'vehicleTypeName' => $value->VehicleTypeName,
                    // 'vehicleTypeImage' => get_image($value->vehicleTypeImage,'vehicle',$merchant_id),
                    // 'vehicleTypeMapImage' => view_config_image($value['vehicleTypeMapImage'])
                ];
            });

            $vehicleMake = VehicleMake::select('id')->where('merchant_id', $merchant_id)->where('admin_delete',NULL)->get();
            $vehicleMake = $vehicleMake->map(function ($value) {
                return [
                    'id' => $value->id,
                    'vehicleMakeName' => $value->VehicleMakeName,
                ];
            });
            $return_data = array('vehicle_type' => $vehicleTypes, 'vehicle_make' => $vehicleMake);
        }
        return $this->successResponse(trans("$string_file.success"), $return_data);
//        return response()->json(['result' => "1", 'message' => trans('api.vehicleconfig'), 'data' => ['vehicle_type' => $vehicleArray, 'vehicle_make' => $vehicleMake]]);
    }
}
