<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public function Child()
    {
        return $this->hasMany(Permission::class, 'parent_id', 'id');
    }
}
