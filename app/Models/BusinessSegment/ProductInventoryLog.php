<?php

namespace App\Models\BusinessSegment;

use Illuminate\Database\Eloquent\Model;

class ProductInventoryLog extends Model
{
    public function ProductInventory()
    {
        return $this->belongsTo(ProductInventory::class);
    }
}
