<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest
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
            'name' => 'required|string',
            'phone' => ['required',
                Rule::unique('hotels', 'phone')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })],
            'email' => ['required', 'email',
                Rule::unique('hotels', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', $merchant_id);
                })],
            'alias' => ['required',
                Rule::unique('hotels', 'alias')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', $merchant_id);
                })],
            'password' => "required|min:6",
            'address' => 'required',
            'hotel_logo' => 'required'
        ];
    }

    protected function getValidatorInstance()
    {
        $data = $this->all();
        $data['alias'] = str_slug($data['name']);
        $country = explode("|", $data['country']);
        $data['phone'] = $country[1] . $data['phone'];
        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
