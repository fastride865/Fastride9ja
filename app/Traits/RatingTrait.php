<?php

namespace App\Traits;

use App\Models\BookingRating;
use App\Models\Merchant;
use Auth;

trait RatingTrait
{
    public function getAllRating($pagination = true, $type = 'MERCHANT')
    {
        $taxi_company_id = '';
        $merchant_id = '';
        $taxi_company = '';
        $merchant = '';
        if($type == 'MERCHANT'){
            $merchant = Auth::user('merchant')->load('CountryArea');
            $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
        }else if($type == 'TAXICOMPANY'){
            $taxi_company = get_taxicompany();
            $taxi_company_id = $taxi_company->id;
            $merchant = Merchant::with('CountryArea')->find($taxi_company->merchant_id);
            $merchant_id = $taxi_company->merchant_id;
        }

        $query = BookingRating::with(['Booking' => function ($query) {
            $query->with('Driver', 'User');
        }])->whereHas('Booking', function ($q) use ($merchant_id, $merchant, $taxi_company_id, $type) {
            if (!empty($merchant->CountryArea->toArray())) {
                $area_ids = array_pluck($merchant->CountryArea, 'id');
                $q->where('merchant_id', $merchant_id);
                if($type == 'TAXICOMPANY'){
                    $q->where('taxi_company_id', $taxi_company_id);
                }
                $q->whereIn('country_area_id', $area_ids);
            } else {
                $q->where('merchant_id', $merchant_id);
                if($type == 'TAXICOMPANY'){
                    $q->where('taxi_company_id', $taxi_company_id);
                }
            }
        })->latest();
        $result = $pagination == true ? $query->paginate(25) : $query;
        return $result;
    }

  public function getAllRatingDelivery($pagination = true)
  {
    $merchant = Auth::user('merchant')->load('CountryArea');
    $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
    $query = BookingRating::with(['Booking' => function ($query) {
      $query->with('Driver', 'User');
    }])->whereHas('Booking', function ($q) use ($merchant_id, &$merchant) {
      if (!empty($merchant->CountryArea->toArray())) {
        $area_ids = array_pluck($merchant->CountryArea, 'id');
        $q->where([
          ['merchant_id', '=' , $merchant_id],
          ['delivery_type_id' , '!=' , null],
          ['delivery_type_id' , '!=' , 0]
        ])
          ->whereIn('country_area_id', $area_ids);
      } else {
        $q->where([
          ['merchant_id' , '=', $merchant_id],
          ['delivery_type_id' , '!=' , null],
          ['delivery_type_id' , '!=' , 0]
        ]);
      }
    })->latest();
    $result = $pagination == true ? $query->paginate(25) : $query;
    return $result;
  }
}