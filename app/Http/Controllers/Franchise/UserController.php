<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Requests\FranchiseUserRequest;
use App\Models\Country;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use DB;
use App\Traits\ImageTrait;

class UserController extends Controller
{
    use ImageTrait;
    public function index()
    {
        $franchise = Auth::user('franchise');
        $id = $franchise->id;
        $users = User::WhereHas('Franchisee', function ($query) use ($id) {
            $query->where('franchisee_id', $id);
        })->latest()->paginate(25);
        return view('franchise.user.index', compact('users'));
    }

    public function create()
    {
        $merchant_id = Auth::user('franchise')->merchant_id;
        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
        return view('franchise.user.create', compact('countries'));
    }

    public function store(FranchiseUserRequest $request)
    {
        $merchant_id = Auth::user('franchise')->merchant_id;
        $password = Hash::make($request->password);
        $profile_image = $this->uploadImage('profile','user',$merchant_id);
        $user = new User();
        $user = User::create([
            'merchant_id' => $merchant_id,
            'UserName' => $request->user_name,
            'UserPhone' => $request->phone,
            'email' => $request->user_email,
            'password' => $password,
            'UserSignupType' => 1,
            'UserSignupFrom' => 2,
            'ReferralCode' => $user->GenrateReferCode(),
            'UserProfileImage' => $profile_image,
            'user_type' => 2,
        ]);
        $user->Franchisee()->sync(Auth::user('franchise')->id);
        return redirect()->back()->with('rideradded', 'Rider Added');
    }

    public function Serach(Request $request)
    {
        $request->validate([
            'keyword' => "required",
            'parameter' => "required|integer|between:1,3",
        ]);
        switch ($request->parameter) {
            case "1":
                $parameter = "UserName";
                break;
            case "2":
                $parameter = "email";
                break;
            case "3":
                $parameter = "UserPhone";
                break;
        }
        $franchise = Auth::user('franchise');
        $id = $franchise->id;
        $query = User::WhereHas('Franchisee', function ($query) use ($id) {
            $query->where('franchisee_id', $id);
        });
        if ($request->keyword) {
            $query->where($parameter, 'like', '%' . $request->keyword . '%');
        }
        $users = $query->paginate(25);
        return view('franchise.user.index', compact('users'));
    }

}
