<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicationMerchantString;
use App\Models\ApplicationString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\MerchantTrait;
use App\Traits\ApiResponseTrait;

class StringLanguageController extends Controller
{
    use ApiResponseTrait,MerchantTrait;
    public function getLatestString(Request $request){

        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'app' => 'required',
            'loc' => 'required',
            'version' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse([$errors[0]]);
        }

        $baseVersion = $request->base_version ? $request->base_version : 0.0;
        $platform = $request->platform;
        $app = $request->app;
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $loc = !empty($request->loc) ? $request->loc: 'en';
        $version = $request->version;
        $datas = $this->getStringFromVersion($merchant_id,$loc,$version,$platform,$app)->get();

        if (!empty($datas->toArray())){
            $getAllMerchantString = $datas;
//                $this->getStringFromVersion($merchant_id,$loc,$baseVersion,$platform,$app)->get();
            $string_latest_version = ApplicationMerchantString::select('version')->where([['merchant_id','=',$merchant_id],['locale','=',$loc]])->orderBy('updated_at', 'DESC')->first();
            $a = array();
            foreach ($getAllMerchantString as $data){
                $a[$data->string_key] = $data->ApplicationMerchantString[0]['string_value'];
            }
            $return_data = [
                'locale' => $request->loc,
                'latest_version' => $string_latest_version->version,
                'string' => $a,
            ];
            return $this->successResponse(trans("$string_file.success"),$return_data);
//            return response()->json(['result' => "1",'message'=> trans('api.update_string'),'locale' => $request->loc, 'data' => $a,'latest_version' => $string_latest_version->version]);
        }else{
            $return_data = [
                'locale' => $request->loc,
                'latest_version' => "0.0",
                'string' => [],
            ];
            return $this->failedResponse(trans("$string_file.success"),$return_data);
//            return response()->json(['result' => "0",'message'=> trans('api.uptodate'),'data' => [],'latest_version' => 0.0]);
        }
    }

    public function getStringFromVersion($merchant_id,$locale,$version,$platform,$application){
        $data = ApplicationString::with(['ApplicationMerchantString'=>function($q) use ($merchant_id,$locale,$version){
            $q->where([['merchant_id','=',$merchant_id],['locale','=',$locale],['version','>',$version]]);
        }])
            ->whereHas('ApplicationMerchantString', function ($query) use ($merchant_id,$locale,$version){
            $query->where([['merchant_id','=',$merchant_id],['locale','=',$locale],['version','>',$version]]);
        })
            ->where([['platform','=',$platform],['application','=',$application]])->latest();
        return $data;
    }
}
