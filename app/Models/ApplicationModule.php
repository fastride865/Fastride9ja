<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationModule extends Model
{
    protected $guarded = [];

    public function ApplicationString()
    {
        return $this->hasMany(ApplicationString::class);
    }
}
