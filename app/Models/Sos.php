<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class Sos extends Model
{
    protected $guarded = [];
    protected $hidden = ['LanguageAny', 'LanguageSingle'];

    public function LanguageAny()
    {
        return $this->hasOne(LanguageSos::class);
    }

    public function LanguageSingle()
    {
        return $this->hasOne(LanguageSos::class)->where([['locale', '=', App::getLocale()]]);
    }

    public static function SosNumbers($merchant_id)
    {
        $sosnumber = Sos::where([['merchant_id', '=', $merchant_id], ['sosStatus', '=', 1]])->whereNull('user_id')->get();
        foreach ($sosnumber as $value) {
            if (empty($value->LanguageSingle)) {
                $name = $value->LanguageAny->name;
                unset($value->LanguageSingle);
                unset($value->LanguageAny);
            } else {
                $name = $value->LanguageSingle->name;
                unset($value->LanguageSingle);
            }
            $value->name = $name;
        }
        return $sosnumber;
    }

    public static function AllSosList($merchant_id, $application, $id)
    {
        /*$sosnumber = Sos::where([['merchant_id', '=', $merchant_id], ['sosStatus', '=', 1], ['application', '=', $application]])->orWhere([['application', '=', $application], ['user_id', '=', $id], ['sosStatus', '=', 1]])->get();
        $without_country_code_sos = Configuration::where([['merchant_id', '=', $merchant_id]])->first()->without_country_code_sos;
        foreach ($sosnumber as $value) {
            $value->name = $value->SosName;
            if($without_country_code_sos == 1 && !empty($value->country_id)){
                $phoneCode = Country::find($value->country_id)->phonecode;
                $value->number = str_replace($phoneCode,'', $value->number);
            }
        }
        return $sosnumber;*/
//        $sosnumber = Sos::where([['merchant_id', '=', $merchant_id],['sosStatus', '=', 1],['application', '=', $application], ['user_id', '=', $id]])->get();
        $sosnumber = get_sos_list($merchant_id,$application,$id);
        $without_country_code_sos = Configuration::where([['merchant_id', '=', $merchant_id]])->first()->without_country_code_sos;
        if($without_country_code_sos == 1){
            foreach ($sosnumber as $value) {
                if (empty($value->LanguageSingle)) {
                    $name = $value->LanguageAny->name;
                    unset($value->LanguageSingle);
                    unset($value->LanguageAny);
                } else {
                    $name = $value->LanguageSingle->name;
                    unset($value->LanguageSingle);
                }
                if(!empty($value->country_id)){
                    $phoneCode = Country::find($value->country_id)->phonecode;
                    $value->number = str_replace($phoneCode,'', $value->number);
                }
                $value->name = $name;
            }
        }
        else{
            foreach ($sosnumber as $value) {
                if (empty($value->LanguageSingle)) {
                    $name = $value->LanguageAny->name;
                    unset($value->LanguageSingle);
                    unset($value->LanguageAny);
                } else {
                    $name = $value->LanguageSingle->name;
                    unset($value->LanguageSingle);
                }
                $value->name = $name;
            }
        }

        return $sosnumber;
    }

    public function getSosNameAttribute()
    {
        if (empty($this->LanguageSingle)) {
            return $this->LanguageAny->name;
        }
        return $this->LanguageSingle->name;
    }

    public function User()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function Driver()
    {
        return $this->belongsTo(Driver::class,'user_id');
    }
}
