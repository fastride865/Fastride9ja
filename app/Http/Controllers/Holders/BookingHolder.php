<?php
/**
 * Created by PhpStorm.
 * User: aamirbrar
 * Date: 2019-01-30
 * Time: 14:58
 */

namespace App\Http\Controllers\Holders;


class BookingHolder
{
    public function Track($value)
    {
        return array(
            "driver_marker" => array(
                "latitude" => $value->Driver->current_latitude,
                "longitude" => $value->Driver->current_longitude,
                "bearing" => "",
                "icon" => "https://static.thenounproject.com/png/331565-200.png"
            ),
            "ride_status" => 3,
            "location_details" => array(
                'visible' => true,
                'pick_location' =>
                    array(
                        'visible' => true,
                        'location_name' => $value->pickup_location,
                        'latitude' => $value->pickup_latitude,
                        'longitude' => $value->pickup_longitude,
                        'icon' => 'https://cdn4.iconfinder.com/data/icons/user-icons-5/100/user-17-512.png',
                    ),
                'drop_location' =>
                    array(
                        'visible' => $value->drop_location ? true : false,
                        'location_name' => $value->drop_location,
                        'latitude' => $value->drop_latitude,
                        'longitude' => $value->drop_longitude,
                        'icon' => 'https://cdn4.iconfinder.com/data/icons/user-icons-5/100/user-17-512.png',
                    ),
            ),
            "polyline_info" => array(
                "encode_string" => $value->ploy_points
            )
        );
    }

    public function DispatchDriver($value)
    {
        $drivers = [];
        foreach ($value as $item) {
            $drivers[] = array(
                'id' => $item->Driver->id,
                'driver_name' => $item->Driver->fullName,
                'driver_phone' => $item->Driver->phoneNumber,
                'driver_email' => $item->Driver->email,
                'driver_image' => asset($item->Driver->profile_image),
                'driver_last_location' =>
                    array(
                        'lat' => $item->Driver->current_latitude,
                        'lng' => $item->Driver->current_longitude,
                    ),
                'player_id' => $item->Driver->player_id,
                'notification_id' => "",
            );
        }
        return $drivers;

    }

    public function CancelBooking($value)
    {
        return array(
            "id" => "#{$value->id}",
            'ride_type' => $value->booking_type,
            "ride_mode" => $value->ServiceType->serviceName,
            "vehicle_info" => $this->vehicleInfo($value),
            "rider_info" => $this->user($value),
            "driver_info" => $this->driver($value),
            'device_info' =>
                array(
                    'name' => 'Android',
                    'image' => 'https://cdn4.iconfinder.com/data/icons/social-papercut/512/android.png',
                ),
            'location_info' =>
                array(
                    "pick_location" => array(
                        "visible" => true,
                        "location" => $value->pickup_location
                    ),
                    "drop_location" => array(
                        "visible" => true,
                        "location" => $value->drop_location
                    ),
                ),
            'cancelled_data' =>
                array(
                    'cancelled_by' => $this->bookingStatus($value->booking_status),
                    'cancel_reasontittle' => $value->CancelReason->ReasonName,
                ),
            'actions' => $this->action($value),
            'dispatch' => $this->DispatchDriver($value->BookingRequestDriver),
        );
    }

    public function action($value)
    {
        $visibility = false;
        if(in_array($value->booking_status,[1001, 1002, 1003, 1004])){
            $visibility = true;
        }
        return array(
            'driver_dispatches' =>
                array(
                    'visibility' => true,
                ),
            'delete' =>
                array(
                    'visibility' => false,
                ),
            'info' =>
                array(
                    'visibility' => true,
                ),
            'cancel' => array(
                'visibility' => $visibility,
            )
        );
    }

    public function bookingStatus($status)
    {
        switch ($status) {
            case "1006":
                return trans('admin.message48');
                break;
            case "1007":
                return trans('admin.message49');
                break;
            case "1008":
                return trans('admin.message50');
                break;
        }
    }

    public function CompleteRide($value)
    {
        return array(
            "id" => "#{$value->id}",
            "ride_mode" => $value->ServiceType->serviceName,
            "vehicle_info" => $this->vehicleInfo($value),
            "rider_info" => $this->user($value),
            "driver_info" => $this->driver($value),
            'device_info' =>
                array(
                    'name' => 'Android',
                    'image' => 'https://cdn4.iconfinder.com/data/icons/social-papercut/512/android.png',
                ),
            'location_info' =>
                array(
                    'pick_location' =>
                        array(
                            'visible' => true,
                            'location' => $value->BookingDetail->start_location,
                        ),
                    'drop_location' =>
                        array(
                            'visible' => true,
                            'location' => $value->BookingDetail->end_location,
                        ),
                ),
            'ride_type' => $value->booking_type,
            'highlights' =>
                array(
                    'distance' => $value->travel_distance,
                    'time' => $value->travel_time,
                    'amount' => $value->final_amount_paid,
                    'amount_color' => '3323',
                ),
            'actions' => $this->action($value),
            'dispatch' => $this->DispatchDriver($value->BookingRequestDriver),
        );
    }

    public function Estimate($value)
    {
        return array(
            "distance" => array(
                "visible" => true,
                "value" => "ETA Distance - {$value->estimate_distance}"
            ),
            "time" => array(
                "visible" => true,
                "value" => "ETA Time - {$value->estimate_time}"
            ),
            "amount" => array(
                "visible" => true,
                "value" => "{$value->estimate_bill}"
            )
        );
    }

    public function Ride($value)
    {
        return array(
            "id" => "#{$value->id}",
            'ride_type' => $value->booking_type,
            "ride_mode" => $value->ServiceType->serviceName,
            "vehicle_info" => $this->vehicleInfo($value),
            "rider_info" => $this->user($value),
            "driver_info" => $this->driver($value),
            "device_info" => array(
                "name" => "Android",
                "image" => "https://cdn4.iconfinder.com/data/icons/social-papercut/512/android.png"
            ),
            'location_info' => array(
                "pick_location" => array(
                    "visible" => true,
                    "location" => $value->pickup_location
                ),
                "drop_location" => array(
                    "visible" => true,
                    "location" => $value->drop_location
                ),
            ),
            'actions' => $this->action($value),
            'dispatch' => $this->DispatchDriver($value->BookingRequestDriver),
            'estimated_info' => $this->Estimate($value),
        );
    }

    public function driver($value)
    {
        return array(
            "visble" => $value->Driver ? true : false,
            "id" => $value->Driver ? $value->Driver->id : "",
            "image" => $value->Driver ? asset($value->Driver->profile_image) : "",
            "name" => $value->Driver ? $value->Driver->fullName : "",
            "email" => $value->Driver ? $value->Driver->email : "",
            "phone" => $value->Driver ? $value->Driver->phoneNumber : "",
        );
    }

    public function vehicleInfo($value)
    {
        return array(
            "name" => $value->DriverVehicle ? $value->DriverVehicle->VehicleModel->VehicleModelName : "",
            "number" => $value->DriverVehicle ? $value->DriverVehicle->vehicle_number : "",
            "category" => $value->VehicleType->VehicleTypeName,
            "image" => get_image($value->VehicleType->vehicleTypeImage,'vehicle',true,false),
            "status" => "Riding Now",
            "status_color" => "#000000"
        );
    }

    public function user($value)
    {
        return array(
            "visble" => true,
            "image" => asset($value->User->UserProfileImage),
            "name" => $value->User->UserName,
            "email" => $value->User->email,
            "phone" => $value->User->UserPhone
        );
    }
}
