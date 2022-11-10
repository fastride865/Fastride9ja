<?php

namespace App\Http\Controllers\DriverAgency;

use App\Models\DriverAgency\DriverAgencyWalletTransaction;
use App\Models\DriverAgency\DriverAgency;
use App\Traits\RatingTrait;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class DriverAgencyController extends Controller
{
    use RatingTrait;

    public function __construct()
    {
        //$this->middleware('auth:admin');
    }

    public function dashboard()
    {
        $driver_agency = get_driver_agency(false);
        $driver_agency_id = $driver_agency->id;
        $curreny = isset($driver_agency->Country->isoCode) ? $driver_agency->Country->isoCode : 'INR';
        $drivers = Driver::where([['driver_agency_id', $driver_agency_id], ['driver_delete', '=', NULL]])->count();
        $wallet_money = $curreny.' '.$driver_agency->wallet_money;
        return view('driver-agency.dashboard', compact( 'drivers', 'wallet_money'));
    }
    
    public function Profile()
    {
        $driver_agency = get_driver_agency();
        return view('driver-agency.random.profile', compact('driver_agency'));
    }

    public function UpdateProfile(Request $request)
    {
        $driver_agency = get_driver_agency();
        $request->validate([
            'name' => 'required|alpha',
            'email' => ['required',
                Rule::unique('driver_agencies')->where(function($query)use($driver_agency){
                    $query->where([['merchant_id','=',$driver_agency->merchant_id]]);
                })->ignore($driver_agency->id)
            ],
            'phone' => ['numeric','required',
                Rule::unique('driver_agencies')->where(function($query)use($driver_agency){
                    $query->where([['merchant_id','=',$driver_agency->merchant_id]]);
                })->ignore($driver_agency->id)
            ],
            'contact_person' =>'required',
            'address' => 'required',
            'password' => 'required_if:edit_password,1'
        ]);
        DB::beginTransaction();
        try {
            $driver_agency->name = $request->name;
            $driver_agency->email = $request->email;
            $driver_agency->phone = $request->phone;
            $driver_agency->address = $request->address;
            if ($request->edit_password == 1) {
                $password = Hash::make($request->password);
                $driver_agency->password = $password;
            }
            $driver_agency->save();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->back()->with('message181', trans('admin.message181'));
    }

    public function Ratings()
    {
        $ratings = $this->getAllRating(true, 'driver_agency');
        return view('driver_agency.random.ratings', compact('ratings'));
    }

    public function SearchRating(Request $request)
    {
        $query = $this->getAllRating(false);
        if ($request->booking_id) {
            $keyword = $request->booking_id;
            $query->WhereHas('Booking', function ($q) use ($keyword) {
                $q->where('merchant_booking_id',$keyword);
            });
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('Booking', function ($q) use ($keyword) {
                $q->WhereHas('User', function ($qu) use ($keyword) {
                    $qu->where('UserName', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
                });
            });
        }
        if ($request->driver) {
            $driverKeyword = $request->driver;
            $query->WhereHas('Booking', function ($q) use ($driverKeyword) {
                $q->WhereHas('Driver', function ($qu) use ($driverKeyword) {
                    $qu->where('fullName', 'LIKE', "%$driverKeyword%")->orWhere('email', 'LIKE', "%$driverKeyword%")->orWhere('phoneNumber', 'LIKE', "%$driverKeyword%");
                });
            });
        }
        $ratings = $query->paginate(25);
        return view('driver_agency.random.ratings', compact('ratings'));
    }


    // driver wallet transaction
    public function wallet()
    {
        $driver_agency = get_driver_agency(false);
        $wallet_transactions = DriverAgencyWalletTransaction::where([['driver_agency_id', '=', $driver_agency->id]])->paginate(25);
        return view('driver-agency.random.wallet', compact('wallet_transactions', 'driver_agency'));
    }


}
