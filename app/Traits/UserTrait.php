<?php

namespace App\Traits;

use App\Models\User;
use App\Models\UserDevice;
use Auth;

trait UserTrait
{
    public function getAllActiveUserPlayerIds()
    {
        $merchant = Auth::user('merchant')->load('CountryArea');
        $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
        $users = UserDevice::whereHas('User', function ($q) use($merchant_id){
            $q->where([['merchant_id', '=', $merchant_id], ['user_delete', '=', NULL]]);
        })->where('player_id','!=', '')->pluck('player_id')->toArray();
        return $users;
    }
}
