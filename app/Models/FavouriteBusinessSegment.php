<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BusinessSegment\BusinessSegment;

class FavouriteBusinessSegment extends Model
{
    protected $guarded = [];
    protected $hidden = ['Driver'];

    public function BusinessSegment()
    {
        return $this->belongsTo(BusinessSegment::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
