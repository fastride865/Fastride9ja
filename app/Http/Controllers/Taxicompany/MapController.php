<?php

namespace App\Http\Controllers\Taxicompany;


use App\Models\Booking;
use App\Models\CountryArea;
use App\Models\Driver;
use App\Traits\BookingTrait;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MapController extends Controller
{
    use BookingTrait;

    public function HeatMap()
    {
        $taxicompany = get_taxicompany();
        $booking = Booking::where([['taxi_company_id', '=', $taxicompany->id]])->latest();
        $bookings = $booking->get(['pickup_latitude', 'pickup_longitude']);
        return view('taxicompany.map.heat', compact('bookings'));
    }

    public function DriverMap()
    {
        return view('taxicompany.map.driver');
    }

    public function getDriverOnMap(Request $request)
    {
//        $this->SetTimeZone($request->manual_area);
        $taxi_company = get_taxicompany();
        $merchant_id = $taxi_company->merchant_id;
        $config = get_merchant_configuration($taxi_company->merchant_id);
        $drivers = Driver::GetNearestDriver([
            'area'=>$request->manual_area,
            'latitude'=>$request->pickup_latitude,
            'longitude'=>$request->pickup_longitude,
            'limit'=>$config->BookingConfiguration->number_of_driver_user_map,
            'service_type'=>$request->service,
            'vehicle_type'=>$request->vehicle_type,
            'distance_unit'=>$request->distance_unit,
            'distance'=>$request->radius,
            'user_gender'=>$config->ApplicationConfiguration->gender == 1 && $request->driver_gender == 2 ? 2 : null,
            'type' => $request->type,
            'riders_num' => isset($request->riders_num)? $request->riders_num : null,
            'taxi_company' => $taxi_company->id
        ]);
        $mapMarkers = array();
        foreach ($drivers as $values) {
            $online_offline = $values->online_offline;
            if ($online_offline == 2) {
                $marker_icon = asset("marker/offline.png");
            } else {
                if ($values->free_busy == 1) {
                    $lastRide = Booking::where([['driver_id', '=', $values->id]])->whereIn('booking_status', array(1002, 1003, 1004))->first();
                    $booking_status = $lastRide->booking_status;
                    switch ($booking_status) {
                        case "1002":
                            $marker_icon = view_config_image("marker/Enroute-to-Pickup.png");
                            break;
                        case "1003":
                            $marker_icon = view_config_image("marker/Reached-Pickup.png");
                            break;
                        case "1004":
                            $marker_icon = view_config_image("marker/Journey-Started.png");
                            break;
                        default:
                            $marker_icon = view_config_image("marker/available.png");
                    }
                } else {
                    $marker_icon = view_config_image("marker/available.png");
                }
            }
            if (Auth::user()->demo == 1) {
                $mapMarkers[] = array(
                    'marker_id' => $values->id,
                    'marker_name' => "********" . substr($values->first_name . $values->last_name, -2),
                    'marker_address' => "",
                    'marker_number' => "********" . substr($values->phoneNumber, -2),
                    'marker_email' => "********" . substr($values->email, -2),
                    'marker_latitude' => $values->current_latitude,
                    'marker_longitude' => $values->current_longitude,
                    'marker_image' => get_image($values->profile_image, 'driver'),
                    'marker_icon' => $marker_icon,
                );
            } else {
                $mapMarkers[] = array(
                    'marker_id' => $values->id,
                    'marker_name' => $values->first_name . $values->last_name,
                    'marker_address' => "",
                    'marker_number' => $values->phoneNumber,
                    'marker_email' => $values->email,
                    'marker_latitude' => $values->current_latitude,
                    'marker_longitude' => $values->current_longitude,
                    'marker_image' => get_image($values->profile_image, 'driver'),
                    'marker_icon' => $marker_icon,
                );
            }
        }
        echo json_encode($mapMarkers, true);
    }

    public function SetTimeZone($areaID)
    {
        $area = CountryArea::find($areaID);
        if (!empty($area)) {
//            date_default_timezone_set($area->timezone);
        }
    }

}
