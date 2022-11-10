<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDesignation extends Model
{
    protected $guarded = [];
    public function users()
    {
        $this->belongsToMany(User::class);
    }
}
