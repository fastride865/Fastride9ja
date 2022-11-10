<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrackingScreenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($data)
    {
        return [
                'stil_marker' => [
                                    'marker_type' => 'DROP',
                                    'marker_lat' => '28.43169838985892',
                                    'marker_long' => '77.05788552761078',
                                ],
                'tip_status' => true,
                'movable_marker_type' => [
                                            'driver_marker_name' => 'car.png',
                                            'driver_marker_type' => 'CAR_ONE',
                                            'driver_marker_lat' => '28.4124418',
                                            'driver_marker_long' => '77.0440898',
                                            'driver_marker_bearing' => '0.0',
                                        ],
                'polydata' =>[
                        'polyline_width' => '8',
                        'polyline_color' => '333333',
                        'polyline' => '_hllDqtfuM@y@\\AV?A[CqAAo@@o@C}CAyHAcBCoL?yDuF@cCByEAaB@mBA}GJO??yB?}@I}@Ou@i@iBwAj@_KvDwAh@OJQLs@m@u@s@wBeCiCqCwG_FaBsAmDeDiBiBaBaByF}FsBwBiA_AgFiF}FkFkDgD_BzBwDpF{@z@i@^pH`H`DzC',
                    ],
                'location' =>[
                        'estimate_driver_time' => '',
                        'estimate_driver_distnace' => '',
                        'trip_status_text' => 'End Ride',
                        'location_headline' => 'Drop',
                        'location_text' => '253, Huda Colony, Sector 46, Gurugram, Haryana 122018, India',
                        'location_color' => 'e74c3c',
                        'location_editable' => false,
                    ],
                'cancelable' => false,
        ];

    }

    public function with($data)
    {
        return [
            'result' => "1",
            'message' => trans('api.HHHHHH'),
        ];
    }
}
