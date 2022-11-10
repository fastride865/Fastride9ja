<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNavigationDrawer extends Model
{
    protected $fillable = [
        'name',
        'image',
        'status'
    ];

    public function Merchant()
    {
        return $this->belongsToMany(Merchant::class, 'merchant_nav_drawers');
    }
}
