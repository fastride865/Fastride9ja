<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CountryRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }
 
    public function rules()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        return [
            'name' => ['required',
                Rule::unique('language_countries')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', \Config::get('app.locale')]]);
                })],
            'isocode' => 'required',
            'phonecode' => 'required|integer',
            'maxNumPhone' => 'required|integer|gte:minNumPhone',
            'distance_unit' => 'required|integer|between:1,2',
//            'currency' => 'required',
            'minNumPhone' => 'required|integer|lte:maxNumPhone',
//            'additional_details'=>'integer|between:0,1',
//            'parameter_name' => 'required_if:additional_details,1',
//            'placeholder' => 'required_if:additional_details,1'
        ];
    }
    public function messages()
    {
// use trans instead on Lang
        return [
            'name.unique' => 'This country already exists in database.',
        ];
    }
}
