<?php

namespace App\Http\Controllers\Holders;

class CustomHolder
{
    public function Parameter($value)
    {
        $new = [];
        foreach ($value as $item) {
            $new[] = [
                'parameterName' => $item->ParameterName,
                'parameterNameApplication' => $item->ParameterApplication,
                'sequence_number' => $item->sequence_number,
                'parameterStatus' => $item->parameterStatus,
            ];
        }
        return $new;
    }

    public function Support($value)
    {
        $new = [];
        foreach ($value as $item) {
            $new[] = [
                'id' => $item->id,
                'application' => $item->application == 1 ? trans('admin.message70') : trans('admin.message69'),
                'name' => $item->name,
                'email' => $item->email,
                'phone' => $item->phone,
                'query' => $item->query,
                'date' => $item->created_at->toformatteddatestring(),
            ];
        }
        return $new;
    }

    public function Cms($value)
    {
        return array(
            'id' => $value->id,
            'application' => $value->application == 1 ? trans('api.user') : trans('api.driver'),
            'title' => $value->CmsPageTitle,
            'description' => $value->CmsPageDescription,
            'action' =>
                array(
                    'action_one' => true,
                    'action_two' => true,
                ),
        );
    }

    public function Refer($value)
    {
        switch ($value->offer_type) {
            case "1":
                $offer = trans('admin.message324');
                $offerValue = $value->offer_value;
                break;
            case "2":
                $offer = trans("$string_file.discount");
                $offerValue = $value->offer_value . "%";
                break;
            case "3":
                $offer = trans('admin.message326');
                $offerValue = $value->offer_value;
                break;
        }
        return array(
            'id' => $value->id,
            'country' => $value->Country->CountryName,
            'senderDiscount' => $value->sender_discount == 1 ? true : false,
            'receiverDiscount' => $value->receiver_discount == 1 ? true : false,
            'startDate' => $value->start_date,
            'endDate' => $value->end_date,
            'offerType' =>
                array(
                    'id' => $value->offer_type,
                    'type' => $offer,
                ),
            'offerValue' => $offerValue,
            'status' => $value->status,
        );
    }

    public function PaymentOption($payment)
    {
        $options = [];
        foreach ($payment as $value) {
            $options[] = array(
                "id" => $value->id,
                "mode" => $value->payment_method,
                "icon" => $value->payment_icon,
            );
        }
        return $options;
    }

    public function Document($value)
    {
        return array(
            'id' => "$value->id",
            'actions' =>
                array(
                    'active_status' => $value->documentStatus,
                    'active_visibility' => true,
                    'active_status_color' => '#232322',
                    'delete_visibility' => false,
                    'edit_visibility' => true,
                ),
            'description' => '',
            'document_name' => $value->DocumentName,
            'expiry_date' => $value->expire_date,
        );
    }

    public function Service($services, $package)
    {
        $servicesList = [];
        foreach ($services as $value) {
            $packages = [];
            if (in_array($value->id, [2, 3])) {
                $service_id = $value->id;
                $packageList = $package->filter(function ($q) use ($service_id) {
                    return $q->service_type_id == $service_id;
                });
                $packages = $this->PackageList($packageList);
            }
            $servicesList[] = array(
                "service_id" => $value->id,
                "service_name" => $value->serviceName,
                "vehicle_types" => $value->id != 5 ? true : false,
                "selectPackageList" => [],
                "selectVehicleType" => [],
                "pool_type" => false,
                "packages" => $packages
            );
        }
        return $servicesList;
    }

    public function Packages($value)
    {
        return array(
            'id' => $value->id,
            'actions' =>
                array(
                    'active_status' => $value->packageStatus,
                    'active_visibility' => true,
                    'active_status_color' => '#232322',
                    'delete_visibility' => false,
                    'edit_visibility' => true,
                ),
            'package_name' => $value->PackageName,
            'description' => $value->PackageDescription,
            'termcondition' => $value->PackageTermsConditions,
        );
    }

    public function Outstation($value)
    {
        return array(
            'id' => $value->id,
            'actions' =>
                array(
                    'active_status' => $value->status,
                    'active_visibility' => true,
                    'active_status_color' => '#232322',
                    'delete_visibility' => false,
                    'edit_visibility' => true,
                ),
            'package_name' => $value->PackageName,
            'description' => $value->PackageDescription,
            'termcondition' => "",
        );
    }

    public function PackageList($packages)
    {
        $packageList = [];
        foreach ($packages as $value) {
            $packageList = array(
                "package_id" => $value->id,
                "package_name" => $value->PackageName
            );
        }
        return $packageList;
    }

    public function countryList($countries)
    {
        $countrylist = [];
        foreach ($countries as $value) {
            $countrylist[] = array(
                "id" => $value->id,
                "name" => $value->CountryName,
                "flag" => "https://www.worldatlas.com/upload/ad/a6/68/ar-flag-min.jpg",
                "ISO" => $value->phonecode,
                "phone_length" => $value->maxNumPhone
            );
        }
        return $countrylist;
    }

    public function VehicleMakeDropDown($value)
    {
        return array(
            'id' => $value->id,
            'make_name' => $value->VehicleMakeName,
        );
    }

    public function allvehicle($value)
    {
        $vehicleList = [];
        foreach ($value as $v) {
            $vehicleList[] = $this->vehicleTypeDropDown($v);
        }
        return $vehicleList;
    }

    public function vehicleTypeDropDown($value)
    {
        return array(
            "id" => $value->id,
            "category_name" => $value->VehicleTypeName,
            "category_icon" => get_image($value->vehicleTypeImage,'vehicle',true,false),
        );
    }

    public function vehicleModel($value)
    {
        return array(
            'id' => $value->id,
            'vehicle_make' => $value->VehicleMake->VehicleMakeName,
            'vehicle_model_name' => $value->VehicleModelName,
            'vehicle_make_icon' => get_image($value->VehicleMake->vehicleMakeLogo,'vehicle',true,false),
            'vehicle_type' => $value->VehicleType->VehicleTypeName,
            'vehicle_type_icon' => get_image($value->VehicleType->vehicleTypeImage,'vehicle',true,false),
            'no_of_seats' => $value->vehicle_seat,
            'description' => $value->VehicleModelDescription,
            'actions' =>
                array(
                    'active_deactive' =>
                        array(
                            'visibility' => true,
                            'status' => $value->vehicleModelStatus,
                        ),
                    'delete' =>
                        array(
                            'visibility' => true,
                        ),
                    'edit' =>
                        array(
                            'visibility' => true,
                        ),
                ),
        );
    }

    public function VehicleMake($value)
    {
        return array(
            'id' => $value->id,
            'vehicle_make_icon' => asset($value->vehicle_make_logo),
            'vehicle_make_name' => $value->VehicleMakeName,
            'description' => $value->VehicleMakeDescription,
            'vehicles_linked' => $value->VehicleLink,
            'actions' =>
                array(
                    'active_deactive' =>
                        array(
                            'visibility' => true,
                            'status' => $value->vehicleMakeStatus,
                        ),
                    'delete' =>
                        array(
                            'visibility' => true,
                        ),
                    'edit' =>
                        array(
                            'visibility' => true,
                        ),
                ),
        );
    }

    public function VehicleType($value)
    {
        return array(
            "id" => $value->id,
            "vehicle_type_icon" => get_image($value->vehicleTypeImage,'vehicle',true,false),
            "vehicle_type_name" => $value->VehicleTypeName,
            "description" => $value->VehicleTypeDescription,
            "vehicle_linked" => $value->VehicleLink,
            "rank" => $value->vehicleTypeRank,
            "vehicleTypeMapImage" => get_image($value->vehicleTypeMapImage,'vehicle',true,false),
            "actions" => array(
                "active_deactive" =>
                    array(
                        "visibility" => true,
                        "status" => $value->vehicleTypeStatus
                    ),
                "delete" => array(
                    "visibility" => true
                ),
                "edit" => array(
                    "visibility" => true
                )
            )
        );
    }

    public function Areas($areas)
    {
        $areaList = [];
        foreach ($areas as $value) {
            $areaList[] = $this->AreaList($value);
        }
        return $areaList;
    }

    public function AreaList($value)
    {
        return array(
            "id" => "$value->id",
            "name" => "{$value->CountryAreaName}",
            'serviceTypes' => $this->ServicesList($value->ServiceTypes)
        );
    }

    public function AreaDropdown($value)
    {
        return array(
            "id" => "$value->id",
            "name" => "{$value->CountryAreaName}",
        );
    }

    public function ServicesList($ServiceTypes)
    {
        $serviceTypes = [];
        foreach ($ServiceTypes as $v) {
            $serviceTypes[] = array('id' => $v->id, 'serviceName' => $v->serviceName);
        }
        return $serviceTypes;
    }

    public function Area($value)
    {
        return array(
            "id" => "#{$value->id}",
            "area_data" => array(
                "id" => "#{$value->id}",
                "name" => "{$value->CountryAreaName}",
                "drivers" => "42 Drivers"
            ),
            "counry_data" => array(
                "flag" => "https://upload.wikimedia.org/wikipedia/en/4/41/Flag_of_India.svg",
                "phone_code" => $value->country->phonecode,
                "name" => $value->country->CountryName,
                "code" => "IN",
                "currency" => $value->country->isoCode
            ),
            "time_zone_data" => array(
                "time_zone_name" => $value->timezone
            ),
            "services" => $this->Services($value->servicetypes),
            "documents" => [
                array(
                    "name" => "Vehicle Doc",
                    "value" => $this->doc($value->documents)
                ),
                array(
                    "name" => "Personal Doc",
                    "value" => $this->doc($value->vehicledocuments)
                )
            ],
            "actions" =>
                array(
                    'active_deactive' => array("visibility" => true, "status" => $value->status),
                    "delete" => array("visibility" => true),
                    "edit" => array("visibility" => true)
                ),

        );
    }

    public function doc($document)
    {
        $docNames = [];
        foreach ($document as $value) {
            $docNames[] = $value->DocumentName;
        }
        return implode(",", $docNames);
    }

    public function docList($document)
    {
        $doclist = [];
        foreach ($document as $value) {
            $doclist[] = array('id' => $value->id, 'document_name' => $value->DocumentName);
        }
        return $doclist;
    }

    public function Services($services)
    {
        $serviceArray = [];
        foreach ($services as $val) {
            $serviceArray[] = array('id' => $val->id, 'name' => $val->serviceName);
        }
        return $serviceArray;
    }

    public function Promotion($value)
    {
        return array(
            "id" => $value->id,
            "image" => asset($value->image),
            "title" => $value->title,
            "content" => $value->message,
            "recievers" => array(
                "total" => 56,
                "driver_count" => 23,
                "user_count" => 33
            ),
            "timings" => array(
                "date" => $value->created_at->toformatteddatestring(),
                "time" => $value->created_at->toTimeString()
            ),
            "action" => array(
                "action_one" => true,
                "action_two" => false
            )
        );
    }

    public function CancelReason($value)
    {
        switch ($value->reason_type) {
            case "1":
                $type = trans('admin.Rider');
                break;
            case "2":
                $type = trans('admin.message35');
                break;
            case "3":
                $type = trans('admin.message36');
                break;
        }
        return array(
            "id" => $value->id,
            "title" => $value->ReasonName,
            "reason_type" => $type,
            "reason_type_id" => $value->reason_type,
            "description" => $value->ReasonDescription,
            "actions" => array(
                'active_deactive' =>
                    array(
                        'visibility' => true,
                        'status' => $value->reason_status,
                    ),
                'delete' =>
                    array(
                        'visibility' => false,
                    ),
                'edit' =>
                    array(
                        'visibility' => true,
                    ),
            )
        );
    }

    public function RejectReason($value)
    {
        return array(
            "id" => $value->id,
            "a_icon_one" => "https://i.ibb.co/T454psh/icons8-invisible-96.png",
            "a_icon_two" => "https://i.ibb.co/6mMcyMD/icons8-compose-96.png",
            "reject_reason" => $value->ReasonName,
            "selected_action" => $value->reason_status == 1 ? "Active" : "Deactive",
            "action_color" => "#00D669",
            "actions" => [
                array(
                    "id" => "1",
                    "action_name" => "Active",
                    "selected" => $value->reason_status == 1 ? true : false,
                ),
                array(
                    "id" => "2",
                    "action_name" => "Deactive",
                    "selected" => $value->reason_status == 2 ? true : false
                )
            ]
        );
    }
}