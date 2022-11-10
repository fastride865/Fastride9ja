<?php

namespace App\Http\Controllers\Merchant;


use App\Models\InfoSetting;
use App\Traits\BookingTrait;
use Auth;
use App\Http\Controllers\Controller;
use App\Traits\MerchantTrait;

class MapController extends Controller
{
    use BookingTrait,MerchantTrait;

    public function HeatMap()
    {
        $checkPermission =  check_permission(1,'view_heat_map');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_segments = get_merchant_segment();
        if(count($merchant_segments) > 1){
            $merchant_segments = add_blank_option(get_merchant_segment(),trans("$string_file.all")." ".trans("$string_file.segment"));
        }
        $booking = $this->allBookings(false);
        $bookings = $booking->get(['pickup_latitude', 'pickup_longitude']);
        // p($bookings);
        $info_setting = InfoSetting::where('slug', 'HEAT_MAP')->first();
        return view('merchant.map.heat', compact('bookings','info_setting','merchant_segments'));
    }

    public function DriverMap()
    {
        $checkPermission =  check_permission(1,'view_driver_map');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_segments = get_merchant_segment();
        if(count($merchant_segments) > 1){
            $merchant_segments = add_blank_option(get_merchant_segment(),trans("$string_file.all")." ".trans("$string_file.segment"));
        }
        $info_setting = InfoSetting::where('slug', 'DRIVER_MAP')->first();
        return view('merchant.map.driver',compact('merchant_segments','info_setting'));
    }

}
