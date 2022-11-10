<?php

namespace App\Http\Requests;


use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CountryAreaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        return [
            'country' => 'required',
            'name' => ['required',
                Rule::unique('language_country_areas', 'AreaName')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', \Config::get('app.locale')]]);
                })],
            'lat' => 'required',
            'normal_service' => 'required_without_all:rental_service,transfer_service,outstion_service,pool_enable',
            'rental_vehicle_type' => 'required_with:rental_service',
            'transfer_vehicle_type' => 'required_with:transfer_service',
            'document' => 'required',
            'vehicledocuments' => 'required',
            'bill_period_id' => 'required',
            'timezone' => 'required|in:' . implode(',', \DateTimeZone::listIdentifiers()),
        ];
    }

    public function messages()
    {
        return [
            'lat.required' => 'Draw Area On Map',
        ];
    }
}
