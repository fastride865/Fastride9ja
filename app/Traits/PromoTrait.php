<?php

namespace App\Traits;

use App\Models\PromoCode;
use Auth;

trait PromoTrait
{
    public function getAllPromoCode($pagination = true)
    {
        $merchant_id = get_merchant_id();
        $permission_segments = get_permission_segments(1, true);
        $permission_area_ids = [];
        if (Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != "") {
            $permission_area_ids = explode(",", Auth::user()->role_areas);
        }
        $query = PromoCode::whereHas('Segment', function ($query) use ($permission_segments,$merchant_id) {
                    $query->whereIn('slag', $permission_segments);
                    $query->whereHas('Merchant', function ($query1) use ($merchant_id) {
                        $query1->where('merchant_id', $merchant_id);
                    });
                 })
            ->where([['merchant_id', '=', $merchant_id], ['deleted', '=', NULL]])
            ->whereHas('CountryArea', function ($q) use ($permission_area_ids) {
                $q->where('status', 1);
                if (!empty($permission_area_ids)) {
                    $q->whereIn("id", $permission_area_ids);
                }
            })->latest();
        $result = $pagination == true ? $query->paginate(25) : $query;
        return $result;
    }
}
