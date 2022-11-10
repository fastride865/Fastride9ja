<?php

namespace App\Http\Requests;

use Auth;
use App;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;


class VehicleTypeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        return [
            'vehicle_name' => ['required',
                Rule::unique('language_vehicle_types', 'vehicleTypeName')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', App::getLocale()]]);
                })],
            'vehicle_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'vehicle_map_image' => 'required',
            'description' => 'required',
            'vehicle_rank' => 'required|integer',
        ];
    }
}
