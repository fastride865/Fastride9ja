<?php

namespace App\Models\BusinessSegment;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    public function ProductInventoryLog()
    {
        return $this->hasMany(ProductInventoryLog::class);
    }
    public function ProductVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
