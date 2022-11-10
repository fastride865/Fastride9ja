<?php

namespace App\Http\Controllers\Api;

use App\Models\Sos;
use App;
use App\Models\LanguageSos;
use App\Models\ApplicationConfiguration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;

class SosController extends Controller
{
    use ApiResponseTrait,MerchantTrait;
    public function SosUser(Request $request)
    {
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        try {
            $sos = get_sos_list($user->merchant_id,1,$user->id);
            if(!empty($sos))
            {
                foreach ($sos as $obj) {
                    $obj->name = $obj->SosName;
                }
            }
            return $this->successResponse(trans("$string_file.success"),$sos);
        }catch(\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }
    }

    public function addSosUser(Request $request)
    {
        $user = $request->user('api');
        $appConfig = ApplicationConfiguration::where('merchant_id', $user->merchant_id)->first();
        if ($appConfig->sos_user_driver == 0) {
            return response()->json(['result' => "0", 'message' => 'Deactivated from master', 'data' => []]);
        }
        $validate = Validator:: make($request->all(), [
            'number' => ['required', 'max:255',
                Rule::unique('sos', 'number')->where(function ($query) use ($user, &$locale) {
                    $query->where([['application', '=', 1], ['user_id', '=', $user->id]]);
                })],
            'name' => 'required|string',
        ]);
        if ($validate->fails()) {
            $errors = $validate->messages()->all();
            return $this->failedResponse($errors[0], []);
//            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $sos = Sos::create([
            'country_id' => $request->country_id,
            'merchant_id' => $user->merchant_id,
            'number' => $request->number,
            'application' => 1,
            'user_id' => $user->id
        ]);
        $this->SaveLanguageSos($user->merchant_id, $sos->id, $request->name);
        $sos->name = $sos->SosName;
        return $this->successResponse(trans('api.sosadd'), $sos);
    }

    public function delete(Request $request)
    {
        $user = $request->user('api');
        $validate = Validator:: make($request->all(), [
            'id' => ['required', 'max:255',
                Rule::exists('sos', 'id')->where(function ($query) use ($user) {
                    $query->where([['application', '=', 1], ['user_id', '=', $user->id]]);
                })],
        ]);
        if ($validate->fails()) {
            $errors = $validate->messages()->all();
            return $this->failedResponse($errors[0], []);
        }
        Sos::where([['id', '=', $request->id]])->delete();
        return $this->successResponse(trans('api.sosdelete'), []);
    }

    public function SaveLanguageSos($merchant_id, $sos_id, $name)
    {
        LanguageSos::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'sos_id' => $sos_id
        ], [
            'name' => $name,
        ]);
    }

    public function SosDriver(Request $request)
    {
        $driver = $request->user('api-driver');
        $string_file = $this->getStringFile(NULL,$driver->Merchant);
        $sos = get_sos_list($driver->merchant_id,2,$driver->id);
        if (empty($sos->toArray())) {
            return $this->failedResponse(trans("$string_file.data_not_found"));
        }
        foreach ($sos as $obj) {
            $obj->name = $obj->SosName;
        }
        return $this->successResponse(trans('success'),$sos);
    }

    public function addSosDriver(Request $request)
    {
        $user = $request->user('api-driver');
        $appConfig = ApplicationConfiguration::where('merchant_id', $user->merchant_id)->first();
        if ($appConfig->sos_user_driver == 0) {
            return $this->failedResponse('Deactivated from master', []);
        }
        $validate = Validator:: make($request->all(), [
            'number' => ['required', 'max:255',
                Rule::unique('sos', 'number')->where(function ($query) use ($user, &$locale) {
                    $query->where([['application', '=', 2], ['user_id', '=', $user->id]]);
                })],
            'name' => 'required|string',
        ]);
        if ($validate->fails()) {
            $errors = $validate->messages()->all();
            return $this->failedResponse($errors[0], []);
//            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $sos = Sos::create([
            'merchant_id' => $user->merchant_id,
            'number' => $request->number,
            'application' => 2,
            'user_id' => $user->id
        ]);
        $this->SaveLanguageSos($user->merchant_id, $sos->id, $request->name);
        $sos->name = $sos->SosName;
        return $this->successResponse(trans('api.sosadd'),$sos);
    }

    public function deleteDriverSos(Request $request)
    {
        $user = $request->user('api-driver');
        $validate = Validator:: make($request->all(), [
            'id' => ['required', 'max:255',
                Rule::exists('sos', 'id')->where(function ($query) use ($user) {
                    $query->where([['application', '=', 2], ['user_id', '=', $user->id]]);
                })],
        ]);
        if ($validate->fails()) {
            $errors = $validate->messages()->all();
            return $this->failedResponse($errors[0], []);
        }
        Sos::where([['id', '=', $request->id]])->delete();
        return $this->successResponse(trans('api.sosdelete'), []);
    }
}
