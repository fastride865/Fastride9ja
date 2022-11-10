<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $taxi_company = get_taxicompany();
        if(!empty($taxi_company)){
            $merchant_id = $taxi_company->merchant_id;
            $rider_type = '';
        }else{
            $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
            $rider_type = 'required';
        }
        return [
            'rider_type' => $rider_type,
            'country' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => ['required',
                Rule::unique('users', 'UserPhone')->where(function ($query) use ($merchant_id) {
                    return $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
                })],
            'user_email' => ['required', 'email',
                Rule::unique('users', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where([['user_delete', '=', NULL], ['merchant_id', '=', $merchant_id]]);
                })],
            'password' => "required|min:6|max:8",
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
        $country = explode("|", $data['country']);
        $data['phone'] = $country[1] . $data['user_phone'];
        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }
}
