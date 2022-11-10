<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSingleBookingHistoryDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
                'holder_map_image' =>[
                        'visibility' => true,
                        'data' =>[
                                'map_image' => 'https:maps.googleapis.com/maps/api/staticmap?center=&maptype=roadmap&path=color:0x000000%7Cweight:10%7Cenc:ifllDwsfuMArADf@`@fDFlABRnFgAnFiAdMaC`AKxQiDzMyB`@I@R?f@DhAGbCCjBPfNLdG|@rRX|G_@KaA?{CAm@?M@EDAJ@rAGFKB_F@&markers=color:green%7Clabel:P%7C28.412160104253168,77.04396016895771&markers=color:red%7Clabel:D%7C28.403537721034702,77.03378152102232&key=AIzaSyD_M5AzRM8trqR3qKj2LdUF5WNI2y5bbQg',
                            ],
                        ],
                'holder_family_member' => [
                                            'name' => '',
                                            'phoneNumber' => '',
                                            'age' => '',
                                        ],
                'holder_booking_description' => [
                                        'visibility' => true,
                                        'data' =>[
                                                'highlighted_left_text' => '2019-04-17 12:11:49',
                                                'highlighted_left_text_style' => 'BOLD',
                                                'highlighted_left_text_color' => '#333333',
                                                'small_left_text' => 'Economy',
                                                'small_left_text_style' => '',
                                                'small_left_text_color' => 'bbbbbb',
                                                'highlighted_right_text' => 'INR 33.31',
                                                'highlighted_right_text_style' => '',
                                                'highlighted_right_text_color' => '#333333',
                                                'small_right_text' => '',
                                                'small_right_text_style' => 'BOL',
                                                'small_right_text_color' => 'e74c3c',
                                            ],
                                    ],
                'holder_pickdrop_location' => [
                                        'visibility' => true,
                                        'data' =>[
                                                'pick_text_visibility' => true,
                                                'pick_text' => 'IRIS Tech Park, Sector 49, Gurugram, Haryana 122018, India',
                                                'drop_text_visibility' => true,
                                                'drop_text' => 'IRIS Tech Park, Sector 49, Gurugram, Haryana 122018, India',
                                        ],
                                    ],
                'holder_metering' =>[
                                'visibility' => true,
                                'data' =>[
                                        'text_one' => '33.31',
                                        'text_two' => '0 Km',
                                        'text_three' => '00 H 00 M',
                                    ],
                                ],
                'holder_driver' =>[
                                'visibility' => true,
                                'data' =>[
                                        'circular_image' => 'driver/N9shkaiXg3VIRIKwgXBmwUcxuPvJOflxn0GuoKIM.jpeg',
                                        'highlighted_text' => 'Natasha Chhibber',
                                        'small_text' => 'natasha@apporio.com',
                                        'rating_visibility' => false,
                                        'rating' => '0',
                                        'rating_button_visibility' => false,
                                        'rating_button_enable' => false,
                                        'rating_button_text' => 'Rate Driver',
                                        'rating_button_text_color' => '0',
                                        'rating_button_text_style' => 'BOLD',
                                ],
                            ],
                'holder_receipt' =>[
                        'visibility' => true,
                        'data' =>[
                                0 => [
                                        'highlighted_text' => 'Bill Details',
                                        'highlighted_text_color' => '333333',
                                        'highlighted_style' => 'BOLD',
                                        'highlighted_visibility' => true,
                                        'small_text' => 'eee',
                                        'small_text_color' => '333333',
                                        'small_text_style' => '',
                                        'small_text_visibility' => false,
                                        'value_text' => 'Bill Details',
                                        'value_text_color' => '333333',
                                        'value_text_style' => '',
                                        'value_textvisibility' => false,
                                    ],
                                1 =>[
                                        'highlighted_text' => 'Base Fare',
                                        'highlighted_text_color' => '333333',
                                        'highlighted_style' => 'NORMAL',
                                        'highlighted_visibility' => true,
                                        'small_text' => 'eee',
                                        'small_texot_clor' => '333333',
                                        'small_text_style' => '',
                                        'small_text_visibility' => false,
                                        'value_text' => 'INR 30.00',
                                        'value_text_color' => '333333',
                                        'value_text_style' => '',
                                        'value_textvisibility' => true,
                                    ],
                                2 =>[
                                        'highlighted_text' => 'Distance Charges',
                                        'highlighted_text_color' => '333333',
                                        'highlighted_style' => 'NORMAL',
                                        'highlighted_visibility' => true,
                                        'small_text' => 'eee',
                                        'small_texot_clor' => '333333',
                                        'small_text_style' => '',
                                        'small_text_visibility' => false,
                                        'value_text' => 'INR 0.01',
                                        'value_text_color' => '333333',
                                        'value_text_style' => '',
                                        'value_textvisibility' => true,
                                    ],
                                3 =>[
                                        'highlighted_text' => 'Waiting Time Charges',
                                        'highlighted_text_color' => '333333',
                                        'highlighted_style' => 'NORMAL',
                                        'highlighted_visibility' => true,
                                        'small_text' => 'eee',
                                        'small_texot_clor' => '333333',
                                        'small_text_style' => '',
                                        'small_text_visibility' => false,
                                        'value_text' => 'INR 1.80',
                                        'value_text_color' => '333333',
                                        'value_text_style' => '',
                                        'value_textvisibility' => true,
                                    ],
                                4 =>[
                                        'highlighted_text' => 'Ride Time Charges',
                                        'highlighted_text_color' => '333333',
                                        'highlighted_style' => 'NORMAL',
                                        'highlighted_visibility' => true,
                                        'small_text' => 'eee',
                                        'small_texot_clor' => '333333',
                                        'small_text_style' => '',
                                        'small_text_visibility' => false,
                                        'value_text' => 'INR 1.50',
                                        'value_text_color' => '333333',
                                        'value_text_style' => '',
                                        'value_textvisibility' => true,
                                    ],
                                5 =>[
                                        'highlighted_text' => 'Promo code',
                                        'highlighted_text_color' => '333333',
                                        'highlighted_style' => 'NORMAL',
                                        'highlighted_visibility' => true,
                                        'small_text' => 'eee',
                                        'small_texot_clor' => '333333',
                                        'small_text_style' => '',
                                        'small_text_visibility' => false,
                                        'value_text' => 'INR 0.00',
                                        'value_text_color' => '333333',
                                        'value_text_style' => '',
                                        'value_textvisibility' => true,
                                    ],
                            ],
                    ],
                'holder_driver_vehicle_rating' =>[
                                    'visibility' => false,
                                    'vehicle_data' =>[
                                            'booking_id' => '8455',
                                            'text' => 'Economy( black )',
                                            'image' => 'vehicle/u5T8E2cMf73bPQaP4R1xVv10LfJPpuHAoiyKVvUN.png',
                                        ],
                                ],
                'button_visibility' =>[
                                'track' => false,
                                'cancel' => false,
                                'mail_invoice' => true,
                                'support' => true,
                                'coupon' => false,
                            ],
        ];
    }

    public function with($data)
    {
        return [
            'result' => "1",
            'message' => trans('api.message16'),
            'bookable' => true,
        ];
    }
}
