<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverDocument extends Model
{

    protected $guarded = [];

    public function Document()
    {
        return $this->belongsTo(Document::class);
    }

    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }

}
