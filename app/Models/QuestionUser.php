<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionUser extends Model
{
    protected $guarded = [];
    protected $hidden = array('Question');

    public function Question()
    {
        return $this->belongsTo(Question::class);
    }
}
