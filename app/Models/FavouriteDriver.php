<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavouriteDriver extends Model
{
    protected $guarded = [];
    protected $hidden = ['Driver'];

    public function Driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
