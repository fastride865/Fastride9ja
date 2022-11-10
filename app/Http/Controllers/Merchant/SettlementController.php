<?php

namespace App\Http\Controllers\Merchant;

use App\Models\DriverSettlement;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class SettlementController extends Controller
{
    public function index(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $driverAccounts = DriverSettlement::whereHas('Driver', function ($query) use ($merchant_id) {
            $query->where('merchant_id', '=', $merchant_id);
        })->paginate(20);
        return view('merchant.accounts.newindex', compact('driverAccounts'));
    }

    public function Settle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bill_id' => [
                'required',
                Rule::exists('driver_settlements', 'id')
            ],
            'settle_type' => 'required',
            'referance_number' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $driver = DriverSettlement::find($request->bill_id);
        $driver->settle_type = $request->settle_type;
        $driver->referance_number = $request->referance_number;
        $driver->status = 2;
        $driver->save();
        return redirect()->back()->with('settled', trans('admin.billSettle'));
    }
}
