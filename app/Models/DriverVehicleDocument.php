<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverVehicleDocument extends Model
{
    protected $hidden = ['Document'];
     
    protected $guarded = [];

    public function Document()
    {
        return $this->belongsTo(Document::class);
    }
    public function DriverVehicle()
    {
        return $this->belongsTo(DriverVehicle::class);
    }
}
