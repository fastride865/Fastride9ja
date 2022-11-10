<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    //
    protected $fillable = ['user_id','latitude','longitude','address_title','category','house_name','floor','building','land_mark','address'];
    public function User()
    {
        return $this->belongsTo(User::class);
    }

}
