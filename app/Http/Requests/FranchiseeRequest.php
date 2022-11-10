<?php

namespace App\Http\Requests;


use Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class FranchiseeRequest extends FormRequest
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
            'phone' => 'required|regex:/^[0-9]+$/',
            'email' => ['required', 'email',
                Rule::unique('franchisees', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', $merchant_id);
                })],
            'alias' => ['required',
                Rule::unique('franchisees', 'alias')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', $merchant_id);
                })],
            'password' => "required|min:6",
            'area' => 'required',
            'contact' => 'required',
            'commission' => 'required'
        ];
    }

    protected function getValidatorInstance()
    {
        $data = $this->all();
        $data['alias'] = str_slug($data['name']);
        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
