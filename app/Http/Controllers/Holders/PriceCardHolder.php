<?php

namespace App\Http\Controllers\Holders;


class PriceCardHolder
{
    public function priceCard($value)
    {
        return array(
            'id' => $value->id,
            "service_id" => $value->service_type_id,
            "service_name" => $value->ServiceType->serviceName,
        );
    }

    public function PriceList($priceObj)
    {
        $list = [];
        foreach ($priceObj as $value) {

            if (empty($value->package_id)):
                $package = "--";
            else:
                if ($value->service_type_id == 4):
                    $package = $value->OutstationPackage->PackageName;
                else:
                    $package = $value->Package->PackageName;
                endif;
            endif;

            switch ($value->pricing_type):
                case "1":
                    $bill_calc_type = trans('admin.Variable');
                    break;
                case "2":
                    $bill_calc_type = trans('admin.fixed_price');
                    break;
                case "3":
                    $bill_calc_type = trans('admin.inputDriver');
                    break;
            endswitch;
            $a = [];
            foreach ($value->paymentmethod as $payment):
                $a[] = $payment->payment_method;
            endforeach;
            $commission_type = "";
            $commission_method = "";
            $commission_methodname = "";
            if (!empty($value->PriceCardCommission)) {
                $commission_type = $value->PriceCardCommission->commission_type == 1 ? trans('admin.Prepaid') : trans('admin.Postpaid');
                $commission_method = $value->PriceCardCommission->commission_method == 1 ? $value->CountryArea->Country->isoCode . " " . $value->PriceCardCommission->commission : $value->PriceCardCommission->commission;
                $commission_methodname = $value->PriceCardCommission->commission_method == 1 ? trans('admin.Flatcomission') : trans('admin.percentage_bill');
            }
            $list[] = array(
                'vehicle_info' => array(
                    'vehicle_name' => $value->VehicleType->VehicleTypeName,
                    'vehicle_image' => get_image($value->VehicleType->vehicleTypeImage,'vehicle',true,false),
                ),
                'bill_calc_info' => array(
                    'bill_calc_type' => $bill_calc_type,
                    'bill_calc_type_color' => '#e23w2ew',
                ),
                'comission_info' => array(
                    'comission_type' => $commission_type,
                    'comission_value' => $commission_method,
                    'comission_type_color' => '#23123',
                    'comission_calc_method' => $commission_methodname,
                    'comission_calc_type_color' => '#dwe231',
                ),
                'payment_modes' => $a,
                'package_info' => array(
                    'package_name' => $package,
                ),
                'fare_info' => array(
                    'base_fare' => $value->CountryArea->Country->isoCode . " " . $value->base_fare,
                ),
                'actions' => array(
                    'active_deactive' =>
                        array(
                            'visibility' => true,
                            'status' => $value->status,
                        ),
                    'delete' =>
                        array(
                            'visibility' => false,
                        ),
                    'edit' =>
                        array(
                            'visibility' => true,
                        ),
                    'more_info' =>
                        array(
                            'visibility' => true,
                        ),
                )
            );
        }
        return $list;
    }
}