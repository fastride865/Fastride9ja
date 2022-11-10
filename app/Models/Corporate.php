<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
use App;

class Corporate extends Authenticatable
{
    protected $table = 'corporates';
    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token', 'pivot',
    ];

    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function Country()
    {
        return $this->belongsTo(Country::class);
    }

}
