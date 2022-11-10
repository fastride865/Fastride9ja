<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    protected $guarded = [];

    public function Document()
    {
        return $this->belongsTo(Document::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
