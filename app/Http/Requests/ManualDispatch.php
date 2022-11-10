<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManualDispatch extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'manual_area' => 'required|integer',
            'user_id' => 'required|integer',
            'service' => 'required|integer',
            'booking_type' => 'required|integer|between:1,2',
            'pickup_location' => 'required|string',
            'pickup_latitude' => 'required|string',
            'pickup_longitude' => 'required|string',
            'drop_latitude' => 'required_if:service,1,4,5',
            'drop_longitude' => 'required_if:service,1,4,5',
            'drop_location' =>'required_if:service,1,4,5',
            'estimate_distance' => 'required_if:service,1,4,5',
            'estimate_time' => 'required_if:service,1,4,5',
            'estimate_fare' => 'required',
            'vehicle_type' => 'required|integer',
            'driver_request' => 'required|integer|between:1,3',
            'payment_method_id' => 'required|integer',
            'package' => 'required_if:service,2|required_if:service,3',
        ];
    }
}
