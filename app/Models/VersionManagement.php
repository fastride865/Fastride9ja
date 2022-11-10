<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VersionManagement extends Model
{
    protected $guarded = [];
    protected $table = "version_managements";

    public function Merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public static function updateVersion($merchant_id)
    {
        $api_version = "0.10";
        $version_management = VersionManagement::where('merchant_id',$merchant_id)->first();
        if(!empty($version_management)){
            $api_version = $version_management->api_version + 0.1;
        }
        VersionManagement::updateOrCreate(['merchant_id' => $merchant_id],['api_version' => round_number($api_version)]);
    }
}
