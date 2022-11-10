<?php

namespace App\Http\Controllers\Taxicompany;

use App\Models\Driver;
use App\Models\DriverAccount;
use App\Models\DriverConfiguration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverAccountController extends Controller
{
    public function index()
    {
        $taxicompany_id = get_taxicompany(true);
        $drivers = Driver::where([['drivers.driver_delete', '=', NULL], ['drivers.taxi_company_id', '=', $taxicompany_id]])
            ->join('bookings', 'drivers.id', '=', 'bookings.driver_id')
            ->join('booking_transactions', 'bookings.id', '=', 'booking_transactions.booking_id')
            ->select('drivers.*',DB::raw('SUM(booking_transactions.cash_payment) AS cash_received'))->paginate(25);
//        $drivers = Driver::with(['DriverAccount' => function ($query) {
//            $query->where([['status', '=', 1]]);
//        }])->where([['total_earnings', '!=', NULL], ['taxi_company_id', '=', $taxicompany_id]])->paginate(25);
        return view('taxicompany.accounts.index', compact('drivers'));
    }

    public function create()
    {
//        $taxicompany = get_taxicompany();
//        $merchant_id = $taxicompany->merchant_id;
//        if(empty($booking))
//        {
//            return redirect()->back()->with('accounts', trans('admin.message469'));
//        }
//        $drivers = Driver::where([['outstand_amount', '!=', NULL], ['taxi_company_id', '=', $taxicompany->id]])->get();
//        if (empty($drivers->toArray())) {
//            return redirect()->back()->with('accounts', trans('admin.message468'));
//        }
//
//        $fee_after_grace_period = 2;
//        $bill_due_period = 5;
//        $due_days = "+$bill_due_period days";
//        $due_date = date('Y-m-d H:i:s', strtotime($due_days));
//
//        $bill_grace_period  = $booking->bill_grace_period;
//        $add_days = $bill_due_period + $bill_grace_period;
//        $days = "+$add_days days";
//        $block_date = date('Y-m-d H:i:s', strtotime($days));
//        foreach ($drivers as $value) {
//            $driver_id = $value->id;
//            $lastBill = DriverAccount::where([['driver_id', '=', $driver_id]])->orderBy('id', 'desc')->first();
//            if (!empty($lastBill)) {
//                $trips = $value->total_trips - $lastBill->total_trips;
//                $fromDate = $lastBill->to_date;
//                $fromDate = date('Y-m-d H:i:s', strtotime('+1 days', strtotime($fromDate)));
//            } else {
//                $trips = $value->total_trips;
//                $fromDate = $value->created_at;
//            }
//            DriverAccount::create([
//                'merchant_id' => $merchant_id,
//                'driver_id' => $driver_id,
//                'from_date' => $fromDate,
//                'to_date' => date('Y-m-d H:i:s'),
//                'fee_after_grace_period'=>$fee_after_grace_period,
//                'block_date' => $block_date,
//                'due_date' => $due_date,
//                'amount' => sprintf("%0.2f", $value->outstand_amount),
//                'total_trips' => $trips,
//                'create_by' => Auth::user('merchant')->id,
//            ]);
//            $value->outstand_amount = NULL;
//            $value->save();
//        }
//        return redirect()->back()->with('account', trans('admin.message469'));
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
//        $taxicompany = Auth::user('taxicompany')->parent_id != 0 ? Auth::user('taxicompany')->parent_id : Auth::user('taxicompany');
//        $merchant_id = $taxicompany->merchant_id;
//        $driver = Driver::where([['taxi_company_id', '=', $taxicompany->id]])->findOrFail($id);
//        $bills = DriverAccount::where([['merchant_id', '=', $merchant_id], ['driver_id', '=', $id]])->oldest()->paginate(25);
//        return view('taxicompany.accounts.bills', compact('bills', 'driver'));
    }

    public function Search(Request $request)
    {
        $request->validate([
            'keyword' => "required",
            'parameter' => "required|integer|between:1,3",
        ]);
        switch ($request->parameter) {
            case "1":
                $parameter = "first_name";
                break;
            case "2":
                $parameter = "email";
                break;
            case "3":
                $parameter = "phoneNumber";
                break;
        }
        $taxicompany = get_taxicompany();
        $merchant_id = $taxicompany->merchant_id;
        $query = Driver::with(['DriverAccount' => function ($query) {
            $query->where([['status', '=', 1]]);
        }])->where([['total_earnings', '!=', NULL] ,['taxi_company_id', '=', $taxicompany->id]]);
        if ($request->keyword) {
            $query->where($parameter, 'like', '%' . $request->keyword . '%');
        }
        $drivers = $query->paginate(25);
        return view('taxicompany.accounts.index', compact('drivers'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
