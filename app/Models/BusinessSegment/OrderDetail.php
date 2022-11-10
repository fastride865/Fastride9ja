<?php

namespace App\Models\BusinessSegment;

use Illuminate\Database\Eloquent\Model;
use App\Models\WeightUnit;

class OrderDetail extends Model
{
    function Product()
    {
        return $this->belongsTo(Product::class);
    }
    function WeightUnit()
    {
        return $this->belongsTo(WeightUnit::class);
    }

    function ProductVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
