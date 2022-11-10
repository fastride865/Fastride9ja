<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class FranchiseUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $merchant_id =  Auth::user('franchise')->merchant_id;
        return [
            'country' => 'required',
            'user_name' => 'required|regex:/^[a-zA-Z]+$/u',
            'user_phone' => 'required|regex:/^[0-9]+$/',
            'phone' => ['required', 'integer',
                Rule::unique('users', 'UserPhone')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', $merchant_id);
                })],
            'user_email' => ['required', 'email',
                Rule::unique('users', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', $merchant_id);
                })],
            'password' => "required|min:6",
            'profile' => 'required|file'
        ];
    }

    public function messages()
    {
        return [
            'rider_type.required' => trans('admin.RiderTypeNeed'),
            'user_name.required' => trans('admin.usernameRequire'),
            'user_phone.required' => trans('admin.userphoneRequire'),
            'user_email.required' => trans('admin.useremailRequire'),
            'password.required' => trans('admin.passwordRequire'),
            'profile.required' => trans('admin.profileRequire'),
        ];
    }

    protected function getValidatorInstance()
    {
        $data = $this->all();
        $data['phone'] = $data['country'] . $data['user_phone'];
        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
