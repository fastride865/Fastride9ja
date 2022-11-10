<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeofenceAreaQueue extends Model
{
    protected $guarded = [];
    protected $table = 'geofence_area_queue';

    public function CountryArea()
    {
        return $this->belongsTo(CountryArea::class);
    }

    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public  function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
