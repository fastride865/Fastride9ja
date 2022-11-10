<?php

namespace App\Traits;
use App\Models\CountryArea;
use DB;
use App\Models\BannerManagement;
use App\Models\ServiceType;
use App;

trait ApiResponseTrait{

    protected function failedResponse($message, $data = [])
    {
        $api_version = "1.5";
        if(isset(request()->merchant_id)){
            $merchant_id = request()->merchant_id;
            $version_management = App\Models\VersionManagement::where('merchant_id',$merchant_id)->first();
            $api_version = !empty($version_management->id) ? "$version_management->api_version" : $api_version;
        }
        return response()->json(['version' => $api_version,"result" => "0", 'message' => $message]);
    }
    protected function successResponse($message, $data = [])
    {
        $api_version = "1.5";
        if(isset(request()->merchant_id)){
            $merchant_id = request()->merchant_id;
            $version_management = App\Models\VersionManagement::where('merchant_id',$merchant_id)->first();
            $api_version = !empty($version_management->id) ? "$version_management->api_version" : $api_version;
        }
        return response()->json(['version' => $api_version,"result" => "1", 'message' => $message, 'data' => $data]);
    }
}
