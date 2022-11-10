<?php
namespace App\Traits;

use Auth;
use App\Models\SosRequest;

trait SosTrait
{
  public function getAllSosRequest($pagination = true)
  {
      $merchant = Auth::user('merchant')->load('CountryArea');
      $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
      $query = SosRequest::where([['merchant_id', '=', $merchant_id]])->latest();
      if (!empty($merchant->CountryArea->toArray())) {
          $area_ids = array_pluck($merchant->CountryArea, 'id');
          $query->whereIn('country_area_id', $area_ids);
      }
      $sosrequest = $pagination == true ? $query->paginate(25) : $query;
      return $sosrequest;
  }
}
