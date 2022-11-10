<?php
/**
 * Created by PhpStorm.
 * User: aamirbrar
 * Date: 2019-01-30
 * Time: 11:02
 */

namespace App\Http\Controllers\Holders;


class UserHolder
{
    public function basicInfo($value)
    {
        return array(
            'user_name' => $value->UserName,
            'user_phone_number' => $value->UserPhone,
            'user_email' => $value->email,
            'user_image' => $value->UserProfileImage ? asset($value->UserProfileImage) : "",
        );
    }

    public function userTrips($value)
    {
        return array(
            0 =>
                array(
                    'text_trip_name' => 'Scheduled Trip',
                    'text_trip_value' => '23',
                    'strip_color' => '#f06f32',
                    'strip_max_height' => '20%',
                ),
            1 =>
                array(
                    'text_trip_name' => 'Total Trips',
                    'text_trip_value' => '908',
                    'strip_color' => '#711EB9',
                    'strip_max_height' => '80%',
                ),
            2 =>
                array(
                    'text_trip_name' => 'Failed Trips',
                    'text_trip_value' => '88',
                    'strip_color' => '#C96B18',
                    'strip_max_height' => '43%',
                ),
            3 =>
                array(
                    'text_trip_name' => 'Cancelled Trips',
                    'text_trip_value' => '18',
                    'strip_color' => '#408ab4',
                    'strip_max_height' => '65%',
                ),
            4 =>
                array(
                    'text_trip_name' => 'Auto Cancelled Trips',
                    'text_trip_value' => '18',
                    'strip_color' => '#408ab4',
                    'strip_max_height' => '22%',
                ),
        );
    }


    public function currentRide($value)
    {
        return array(
            'visibility' => true,
            'text_ride_status' => 'Riding Now',
            'vehicle_image' => '',
            'vehicle_category' => 'Mini',
            'vehicle_name' => '(Honda Amaze) DL-8C 7878',
        );
    }

    public function signUpDetails($value)
    {
        return array(
            'referal_code' => $value->ReferralCode,
            'from' => 'Normal',
            'registratoin_date_text' => 'Registration Date:',
            'registratoin_date_value' => $value->created_at->toformatteddatestring(),
            'last_date_text' => 'Last Date ',
            'last_date_value' => $value->updated_at->toformatteddatestring(),
        );
    }

    public function Location($value)
    {
        switch ($value->category) {
            case "1":
                $catName = trans('admin.message9');
                break;
            case "2":
                $catName = trans('admin.message10');
                break;
            case "3":
                $catName = $value->other_name;
                break;
        }
        return array(
            "Locationtype" => $catName,
            "location_type_color" => "#F5A623",
            "image" => "https://maps.googleapis.com/maps/api/staticmap?center={$value->latitude},{$value->longitude}&zoom=15&size=300x200&key=AIzaSyDf3mBCvB1nZ7e2REiiG9cPtWYv_OExn6Y&markers=color:green%7Clabel:G%7C24.860734290733696,67.00113628059624",
            "name" => $value->location,
            "location_type_image" => ""
        );
    }

    public function UserObject($user)
    {
        return array(
            'id' => $user->id,
            'user_type' => $user->user_type == 1 ? trans('admin.Corporate') : trans('admin.Retail'),
            "user" => array(
                'image' => asset($user->UserProfileImage ? $user->UserProfileImage : 'user/default.png'),
                "name" => $user->UserName,
                "email" => $user->email,
                "mobile" => $user->UserPhone,
                "info_icon_visibility" => true,
                "edit_icon_visibility" => true
            ),
            "rating" => $user->rating,
            "created_on" => $user->created_at->toformatteddatestring(),
            "wallet" => array(
                "amount" => $user->wallet_balance,
                "bg_color" => "#2ECC71"
            ),
            "last_active" => "2 hours ago",
            "actions" => array(
                "send_notification_visibility" => true,
                "add_money_visibilty" => true
            ),
            "status" => array(
                "color" => "#729d39",
                'list_status' => [
                    "deactive",
                    "active",
                    "sleep",
                    "other"
                ],
                "current_status" => $user->UserStatus,
            ),
            "trips" => array(
                "left_bg_color" => "#F5A623",
                "right_bg_color" => "#FCE9C8",
                "total_trips_name" => $user->total_trips,
                'total_trips_value' => [
                    "Total Trips: 45",
                    "Active Trips : 5",
                    "Past Trips : 45",
                    "Schedule Trips: 89"
                ]
            ),
            "created_via" => array(
                "name" => "Android",
                "image" => "data:image/png;base64,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"
            )
        );
    }

    public function FavDriver($value)
    {
        $newDriver = new DriverHolder();
        $newBooking = new BookingHolder();
        return array(
            'ride_mode' =>
                array(
                    0 => 'Rental',
                    1 => 'Outstation',
                ),
            'vehicle_info' => $newDriver->vehicleType($value->Driver->DriverVehicles),
            'driver_info' => $newBooking->driver($value),
            'actions' => array('delete_driver_visibility' => true)
        );
    }
}