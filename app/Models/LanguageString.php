<?php

namespace App\Models;

use Auth;
use App;
use Illuminate\Database\Eloquent\Model;

class LanguageString extends Model
{
    public function LanguageSingleMessage()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        return $this->hasOne(LanguageStringTranslation::class)->where([['locale', '=', App::getLocale()], ['merchant_id', '=', $merchant_id]]);
    }
}
