<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardPoint extends Model
{
  protected $guarded = [];

    public function countryArea () {
      return $this->belongsTo(CountryArea::class);
    }
}
