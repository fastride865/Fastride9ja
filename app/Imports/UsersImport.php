<?php

namespace App\Imports;

use App\Models\ImportUserFail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Auth;

class UsersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $corporate = Auth::user('corporate');
        $phone = '+'.$row[2];
        $password = (string)$row[4];
        array_push($row,$phone);
        $errorMsg = array();
        $userPhone = User::where('UserPhone',$phone)->exists();
        $userPhone == true ? array_push($errorMsg,'Phone Number Already Exist') : '';

        $userEmail = User::where('email',$row[3])->exists();
        $userEmail == true ? array_push($errorMsg,'Email Already Exist') : '';

        if ($userPhone || $userEmail){
            return new ImportUserFail([
                'merchant_id' =>  $corporate->merchant_id,
                'corporate_id' => $corporate->id,
                'name' => $row[0].' '.$row[1],
                'phone' => $phone,
                'email' => $row[3],
                'error_message' => json_encode($errorMsg,true),
            ]);
        }else{
            return new User([
                'merchant_id' => $corporate->merchant_id,
                'country_id' => $corporate->country_id,
                'user_type'=> 1,
                'corporate_id' => $corporate->id,
                'UserSignupFrom' => 2,
                'first_name' => $row[0],
                'last_name' => $row[1],
                'UserPhone' => $phone,
                'email' => $row[3],
                'password' => Hash::make($password),
                'designation_id' => $row[5],
            ]);
        }
    }
}
