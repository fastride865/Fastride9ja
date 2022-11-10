<?php

namespace App\Http\Controllers\Corporate;

use App\Models\Corporate;
use App\Models\Country;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Validation\Rule;

class CorporateHomeController extends Controller
{
    use ImageTrait;

    public function dashboard(){
        $corporate = Auth::user('corporate');
        $users = User::where([['corporate_id','=',$corporate->id],['merchant_id','=',$corporate->merchant_id],['user_delete','=',NULL]])->count();
        return view('corporate.dashboard',compact('users'));
    }

    public function Profile()
    {
        $countries = Country::where([['merchant_id', '=', Auth::user('corporate')->merchant_id]])->get();
        return view('corporate.random.profile',compact('countries'));
    }

    public function UpdateProfile(Request $request){
        $merchant_id = Auth::user('corporate')->merchant_id;
        //p($merchant_id);
        $this->validate($request, [
            'country' => 'required|integer',
            'corporate_name' => 'required',
            'email' => ['required','email',
                Rule::unique('corporates', 'email')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })->ignore(Auth::user('corporate')->id)],
            'phone' => ['required','regex:/^[0-9]+$/',
                Rule::unique('corporates', 'corporate_phone')->where(function ($query) use ($merchant_id) {
                    return $query->where([['merchant_id', '=', $merchant_id]]);
                })->ignore(Auth::user('corporate')->id)],
            'address' => 'required',
        ]);

        DB::beginTransaction();
        try{
            $country = Country::find($request->country);
            $corporate = Corporate::findOrFail(Auth::user('corporate')->id);
            $corporate->corporate_name = $request->corporate_name;
            $corporate->country_id = $request->country;
            $corporate->corporate_phone = $country->phonecode . $request->phone;
            $corporate->email = $request->email;
            $corporate->corporate_address = $request->address;
            if($request->hasFile('corporate_logo')){
                $corporate->corporate_logo = $this->uploadImage('corporate_logo','corporate_logo',$merchant_id);
            }
            $corporate->save();
        }catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        // Commit Transaction
        DB::commit();
        return redirect()->route('corporate.dashboard')->with('success', trans('admin.message181'));
    }

}
