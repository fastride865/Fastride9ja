<?php
/**
 * Created by PhpStorm.
 * User: aamirbrar
 * Date: 2019-02-04
 * Time: 11:30
 */

namespace App\Http\Controllers\Holders;


class DriverHolder
{
    public function homelocation()
    {
        return
            array(
                'text_home_destination_info' => 'Home Destination',
                'text_home_adddress' => 'Shubash Chowk sector 47 islamabad,Gurgaon',
                'home_address_status' => 'Activated',
                'home_address_status_color' => '#4A90E2',
                'background_color' => '#FFEDEC',
            );
    }

    public function currentRide()
    {
        return array(
            'visibility' => true,
            'ride_status' => 'Riding Now',
            'user_name' => 'Manish sharma',
            'user_image' => '',
            'heading_to' => 'Heading to :Rajiv Chowk sector 30,Shivaji naga near petrol pump,Gurgaon',
            'background_color' => '#d1a5f7',
        );
    }

    public function activeVehicle($value)
    {
        return array(
            'vehicle_image' => 'https://i.ibb.co/80vjRs5/suv.png',
            'text_vehicle_name' => 'Honda Amaze',
            'text_vehicle_number' => 'DL-09 3232',
            'text_vehicle_category_name' => 'Luxury',
            'background_color' => '#FFFDE5',
        );
    }

    public function action($value)
    {
        return
            array(
                'current_status' => $value->driver_admin_status,
                'send_notifications_visibility' => true,
                'delete_driver_visiblity' => true,
                'activate_deactivate_visibility' => true,
            );
    }

    public function working()
    {
        return array(
            'text_working_status' => 'Working Status',
            'text_online_status' => 'On Duty',
            'text_online_time' => '32 Hours 5 min ',
            'background_color' => '#D0FFE4',
        );
    }

    public function vehicleType($vehicles)
    {
        $vehicleList = [];
        foreach ($vehicles as $value) {
            $vehicleList[] = array(
                "number" => $value->vehicle_number,
                "category" => $value->VehicleType->VehicleTypeName . "({$value->VehicleModel->vehicle_seat})",
                "image" => $value->vehicle_image
            );
        }
        return $vehicleList;
    }

    public function services($value)
    {
        return array(
            0 =>
                array(
                    'service_name' => 'Outstation',
                    'service_color' => '#00000',
                ),
            1 =>
                array(
                    'service_name' => 'Normal',
                    'service_color' => '#00000',
                ),
            2 =>
                array(
                    'service_name' => 'Pool',
                    'service_color' => '#00000',
                ),
        );
    }

    public function todayEarning($value)
    {
        return array(
            'text_earning' => '$233.8',
            'text_earning_color' => '#2B90F7',
            'txt_success_trips' => '23',
            'text_success_color' => '#2ECC71',
            'text_cancelled_trips' => '23',
            'text_cancelled_color' => '#E74C3C',
        );
    }

    public function DriverTrips($value)
    {
        return array(
            'text_rides_info' => 'Total Rides',
            'completed_rides' => '32',
            'completed_rides_color' => '#4A90E2',
            'cancelled_rides' => '3',
            'cancelled_rides_color' => '#E74C3C',
        );
    }

    public function info($value)
    {
        return array(
            'id' => $value->id,
            'name' => $value->fullName,
            'email' => $value->email,
            'phone' => $value->phoneNumber,
            'image' => $value->profile_image ? asset($value->profile_image) : "",
            'area_name' => $value->CountryArea->CountryAreaName,
            'info_btn_vsibility' => true,
            'edit_btn_visibility' => true,
            'ongoing_btn_text' => 'on going',
            'ongoing_visibility' => true,
            'ongoing_color' => '#2ECC71',
            'text_rating' => $value->rating,
            'rating_color' => '#FFDF8E',
        );
    }

    public function CurrentLocation($value)
    {
        return array(
            "text_current_address_info" => "Current Address",
            'lat' => $value->current_latitude,
            'long' => $value->current_longitude,
            'text_address' => '----',
            "current_status_image" => "https://maps.googleapis.com/maps/api/staticmap?center={$value->current_latitude},{$value->current_longitude}&zoom=15&size=400x200&key=AIzaSyDf3mBCvB1nZ7e2REiiG9cPtWYv_OExn6Y&markers=color:green%7Clabel:G%7C28.592140,77.046051",
            "background_color" => "#FFEDEC"
        );
    }

    public function wallet($value)
    {
        return array(
            'text_amount' => $value->wallet_money,
            'text_amount_coolor' => '#ffffff',
            'wallet_background_color' => '#2B90F7',
        );
    }

//    public function Status($value)
//    {
//        if ($value->driver_admin_status == 1):
//            if ($value->login_logout == 1):
//                if ($value->online_offline == 1):
//                    if ($value->free_busy == 1):
//                        $status = trans("$string_file.busy");
//                    else :
//                        $status = trans("$string_file.free");
//                    endif;
//                else :
//                    $status = trans("$string_file.offline");
//                endif;
//            else:
//                $status = trans("admin.logout");
//            endif;
//        else:
//            $status = trans("$string_file.inactive");
//        endif;
//        return array(
//            'status_background_color' => '#2ECC71',
//            'text_status' => $status,
//            'text_status_color' => '#ffffff',
//        );
//    }
    
     public function userInfo($value)
    {
        return array(
            'id' => $value->id,
            'user_name' => $value->fullName,
            'user_email' => $value->email,
            'user_phone' => $value->phoneNumber,
            'user_image' => $value->profile_image ? asset($value->profile_image) : "",
            'user_rating' => $value->rating,
            'created_via' => $value->signupFrom,
            "created_on" =>  $value->created_at->format('d-m-Y')
        );
    }

    public function personalDocuments($docs){

        $docList = [];
        foreach ($docs as $doc) {
            $docList[] = array(
                "status" => $doc->document_verification_status,
                "status_color" =>  '#4A90E2',
                "image" => $doc->document_file,
                "document_name" => $doc->DocumentName,
                "submitted_on" =>  $doc->created_at->format('d-m-Y'),
                "expiry_date" => $doc->expire_date,
            );
        }
        return $docList;
    }

        public function vehicleDetails($vehicleDetails,$area){

                $vehicleImgArray = $vehicleArray= $vehicleList= [];
                $vehicleColor=$noofseats='';
                foreach ($vehicleDetails as $vehicle) {
                    $vehicleImgArray[] = array(
                        array(
                            'vehicle_id' => $vehicle->id,
                            'vehicle_image' => $vehicle->vehicle_image
                        ),
                        array(
                            'vehicle_id' => $vehicle->id,
                            'vehicle_image' => $vehicle->vehicle_number_plate_image,
                        )
                    );
                    $vehicleArray[] = array(
                        "city_name"=> $area,
                        "vehicle_number"=> $vehicle->vehicle_number,
                        "vehicle_name" =>$vehicle->VehicleTypeName,
                    );
                    $vehicleColor=$vehicle->vehicle_color;
                    $noofseats=$vehicle->VehicleModel->vehicle_seat;
                    $vehicleList[] = array(
                        "category_id" => $vehicle->VehicleType->id,
                        "category_name" => $vehicle->VehicleType->VehicleTypeName,
                        "category_image" => $vehicle->vehicle_image
                    );
                }
        
        
                $vehicles[] = array(
                    'vehicle_number_details' => $vehicleArray,
                    'vehicle_image'=>$vehicleImgArray,
                    'vehicle_color'=>$vehicleColor,
                    "category_info" => $vehicleList,
                    "number_of_seats" =>$noofseats,
                    'services_info' => $this->services(1),
        
                );
                return $vehicles;
            }

}