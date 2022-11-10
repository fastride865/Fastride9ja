<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeofenceAreaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $merchant_id = get_merchant_id();
        return [
            'country_area_id' => 'required',
            'name' => ['required',
                Rule::unique('language_country_areas', 'AreaName')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', \Config::get('app.locale')]]);
                })],
            'lat' => 'required',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lat.required' => 'Draw Area On Map',
        ];
    }
}
