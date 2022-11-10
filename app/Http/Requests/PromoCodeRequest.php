<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PromoCodeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        $merchant_id = Auth::user()->id;
        $promo_code_id = $request->id;
        $segment_id = $request->segment_id;
        return [
//            'price_card_ids' => 'required_without:id',
            'area' => 'required_without:id',
            'segment_id' => 'required_without:id',
            'promocode' => ['required',
                Rule::unique('promo_codes', 'promoCode')->where(function ($query) use ($merchant_id,$request,$promo_code_id, $segment_id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['segment_id','=',$segment_id], ['country_area_id', '=', $request->area], ['deleted', '=', NULL]]); // ,['id','!=',$promo_code_id]
                })->ignore($promo_code_id)],
            'promo_code_description' => 'required',
            'promo_code_value' => "required",
            'promo_code_value_type' => "required|integer",
            'promo_code_validity' => "required|integer",
            'promo_code_limit' => "required|integer",
            'promo_code_limit_per_user' => "required|integer|lte:promo_code_limit",
            'applicable_for' => 'required'
        ];
    }
}
