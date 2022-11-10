<?php

namespace App\Http\Controllers\BusinessSegment\Api;

use App\Models\BusinessSegment\BusinessSegment;
use App\Models\StyleManagement;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Passport\Client;
use DB;
use App\Traits\ImageTrait;
use App\Traits\AreaTrait;
use App\Traits\ApiResponseTrait;
use App\Models\Segment;
use Lcobucci\JWT\Parser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use App\Events\BusinessSegmentForgotPasswordEmailOtpEvent;

class AccountController extends Controller
{
    use ImageTrait, AreaTrait, ApiResponseTrait, MerchantTrait;

    public function resetPassword(Request $request){
        $merchant_id = $request->merchant_id;   
        $validator = Validator::make($request->all(), [
           'email' => 'required',

        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $business_segment = BusinessSegment::where([['merchant_id', $merchant_id], ['email', $request->email]])->first();
        $string_file = $this->getStringFile($merchant_id);
        $otp = mt_rand(1111, 9999);
        $email_message='The OTP to reset Password is '.$otp;

           if (!empty($business_segment)) {
               if (($business_segment->email != null)) {
                   
                   event(new BusinessSegmentForgotPasswordEmailOtpEvent($business_segment, $otp));
                   $auto_fill = false;
               }
               return $this->successResponse($email_message,array('auto_fill' => $auto_fill, 'otp' => (string)$otp));

           } else {
               return $this->failedResponse(trans("$string_file.data_not_found"));
           }
    }

    public function ForgotPassword(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $fields = [
            'password' => 'required|string',
            'for' => 'required|string',
        ];
        if ($request->for == 'PHONE') {
            $fields['phone'] = ['required', 'regex:/^[0-9+]+$/',
                Rule::exists('business_segments', 'phone_number')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['status', '=', 1]]);
                })];
        } else {
            $fields['phone'] = ['required', 'email',
                Rule::exists('business_segments', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['status', '=', 1]]);
                })];
        }

        $validator = Validator::make($request->all(), $fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
//                response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $keyword = $request->for == 'PHONE' ? 'phone_number' : 'email';
        $bs = BusinessSegment::where([['merchant_id', '=', $merchant_id], [$keyword, '=', $request->phone], ['status', '=', 1]])->first();
        $bs->password = Hash::make($request->password);
        $bs->save();
        $string_file = $this->getStringFile(null,$bs->Merchant);
        return $this->successResponse(trans("$string_file.password_changed"));
//            response()->json(['result' => "1", 'message' => trans("password").' '.trans("updated").' '.trans("successfully"), 'data' => []]);
    }

    public function editProfile(Request $request)
    {
        $bs = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        $validator = Validator::make($request->all(), [
           'password' => 'required_if:edit_password,==,1',
           'old_password'=>'required_if:edit_password,==,1',

        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            if(isset($request->full_name)){
                $bs->full_name = $request->full_name;
            }
          
            if(isset($request->email)){
                $bs->email = $request->email;
            }
            if(isset($request->phone_number)){
                $bs->phone_number = $request->phone_number;
            }
            if (isset($request->business_logo)) {
                $profile_image = $this->uploadBase64Image('business_logo', 'business_logo', $bs->merchant_id);
                $bs->business_logo = $profile_image;
            }
            if($request->edit_password == 1)
            {
                if(isset($request->old_password) && isset($request->new_password))
                {
                    if (Hash::check($request->old_password, $bs->password)) {
                        $bs->password = Hash::make($request->new_password);
                    } else {
                        return $this->failedResponse(trans("$string_file.invalid_old_password"));
                    }
                }
            }
            $bs->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        $return_data = [
            'full_name'=>$bs->full_name,
            'email'=>$bs->email,
            'phone_number'=>$bs->phone_number,
            'business_logo'=>get_image($bs->business_logo,'business_logo',$bs->merchant_id,true,true,"bs"),
        ];
        return $this->successResponse(trans("$string_file.success"), $return_data);
    }

    public function logout(Request $request)
    {
        $bs = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        DB::beginTransaction();
        try {

            $bs->login = 2; // means logout
            $bs->save();
            $access_token_id = $bs->access_token_id;
            \DB::table('oauth_access_tokens')->where('id', '=', $access_token_id)->delete();
            \DB::table('oauth_refresh_tokens')->where('access_token_id', '=', $access_token_id)->delete();
            $bs->token()->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.logout_successfully"));
    }

    // bs login
    public function login(Request $request)
    {
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $request_fields = [
            'password' => 'required',
            'email' => 'required',
            'player_id' => 'required_without:website|string|min:32'
        ];
        $validator = Validator::make($request->all(), $request_fields,
            [
                'email.exists' => trans("$string_file.email_is_not_registered"),
                'player_id.required' => trans("$string_file.invalid_player_id"),
                'player_id.min' => trans("$string_file.invalid_player_id")
            ]
        );
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $bs_detail = BusinessSegment::where([['email', '=', $request->email],['merchant_id', '=', $merchant_id],['status', '=', 1]])->first();
        if (empty($bs_detail)) {
            return $this->failedResponse(trans("$string_file.invalid_credentials"));
        }

        $client = Client::where([['user_id', '=', $merchant_id], ['password_client', '=', 1]])->first();
        Config::set('auth.guards.api.provider', 'business-segment-api');
        $request->request->add([
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $request->email,
            'password' => $request->password,
            'scope' => '',
        ]);

        $token_generation_after_login = Request::create(
            'oauth/token',
            'POST'
        );
        $collect_response = \Route::dispatch($token_generation_after_login)->getContent();
        $collectArray = json_decode($collect_response);
        if (empty($collectArray) || isset($collectArray->error)) {
            return $this->failedResponse(trans('auth.failed'));
        }
        
          // make previous tokens invalid
        if(!empty($bs_detail->access_token_id))
        {
            $access_token_id = $bs_detail->access_token_id;
            \DB::table('oauth_access_tokens')->where('id', '=', $access_token_id)->delete();
            \DB::table('oauth_refresh_tokens')->where('access_token_id', '=', $access_token_id)->delete();
        }
        
        $access_token_id = (new Parser())->parse($collectArray->access_token)->claims()->get('jti');
        $bs_detail->access_token_id = $access_token_id;
        $bs_detail->player_id = $request->player_id;
        $bs_detail->login = 1;
        $bs_detail->save();
        $return_data['access_token'] = $collectArray->access_token;

        $arr_days = get_days();
        $arr_open_time = json_decode($bs_detail->open_time,true);
        $arr_close_time = json_decode($bs_detail->close_time,true);

        $arr_time = [];
        foreach ($arr_days as $day =>$day_name)
        {
          $arr_time[] =  [
             'day_id'=>$day,
             'day'=>$day_name,
             'open_time'=>$open_time = isset($arr_open_time[$day]) ? $arr_open_time[$day] : "",
             'close_time'=>$close_time = isset($arr_close_time[$day]) ? $arr_close_time[$day] : ""
            ];
        }
        $return_data['other_data'] = [
            'id'=>$bs_detail->id,
            'name'=>$bs_detail->full_name,
            'email'=>$bs_detail->email,
            'phone'=>$bs_detail->phone_number,
            'address'=>$bs_detail->address,
            'arr_time'=>$arr_time,
            'merchant_details'=>[
                'id'=>$bs_detail->merchant_id,
                'name'=>$bs_detail->Merchant->BusinessName,
                'email'=>$bs_detail->Merchant->email,
                'phone'=>$bs_detail->Merchant->merchantPhone,
            ],
            'joined_date'=>date('d M, Y',strtotime($bs_detail->created_at)),
            'profile_image'=> get_image($bs_detail->business_profile_image,'business_profile_image',$merchant_id,true,true,"bs"),
        ];
        return $this->successResponse(trans("$string_file.success"), $return_data);
    }

    public function getStyle(Request $request)
    {
        $bs = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        try {
            $id = $bs->id;
            $merchant_id = $bs->merchant_id;
            $arr_style =  StyleManagement::with('BusinessSegment')
                 ->select('id')->where('delete',NULL)
                ->where('merchant_id',$merchant_id)->get();
                
            $arr_selected_style = DB::table('business_segment_style_management')->where('business_segment_id',$id)->get();
            if(count($arr_selected_style) > 0)
            {
                $selected_style = array_pluck($arr_selected_style,'style_management_id');
            }
            $style_data = $arr_style->map(function($item) use ($merchant_id,$selected_style) {
                return [
                    'id'=>$item->id,
                    'name'=>$item->Name($merchant_id),
                    'checked'=>in_array($item->id,$selected_style) ? true : false ,
                ];
            });
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }

        return $this->successResponse(trans("$string_file.success"),$style_data);
    }
    public function saveStyle(Request $request)
    {
        $bs = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        DB::beginTransaction();
        try {
            $arr_style = $request->input('arr_style');
            $arr_style = json_decode($arr_style,true);
            $bs->StyleManagement()->sync(array_column($arr_style,'id'));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.success"));
    }
    public function getConfigurations(Request $request){

        $bs = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        try {
            $merchant_id = $bs->merchant_id;
            $id = $bs->id;
            if(!empty($bs->login_background_image))
                $login_image=get_image($bs->login_background_image,'business_login_background_image',$merchant_id,true);
            else
                $login_image=asset('theme/examples/images/login2.png');
            $data=array(
                    'login_screen_image'=>$login_image,
                    'email'=>$bs->email,
                    'phone_number'=>$bs->phone_number,
                    
                );
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }

        return $this->successResponse(trans("$string_file.success"),$data);
    }
}
