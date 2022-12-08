<?php

namespace App\Http\Controllers\DriverAgency;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\PricingParameter;
use Auth;
use App\Traits\BookingTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    use BookingTrait;

    public function index()
    {
        $taxicompany = get_taxicompany();
        $transactions = Booking::where([['taxi_company_id', '=', $taxicompany->id], ['booking_closure', '=', 1]])->latest()->paginate(25);
        return view('taxicompany.transaction.index', compact('transactions'));
    }

    public function Search(Request $request)
    {
        $taxicompany = get_taxicompany();
        $query = Booking::where([['taxi_company_id', '=', $taxicompany->id], ['booking_closure', '=', 1]])->latest();
        if ($request->date) {
            $query->whereDate('created_at', '>=', $request->date);
        }
        if ($request->date1) {
            $query->whereDate('created_at', '<=', $request->date1);
        }
        if ($request->booking_id) {
            $query->where('id', '=', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('UserName', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('fullName', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $transactions = $query->paginate(25);
        return view('taxicompany.transaction.index', compact('transactions'));
    }

    public function GetBillDetails(Request $request)
    {
        $bookingDetails = BookingDetail::where([['booking_id', '=', $request->booking_id]])->first();
        $bill_details = json_decode($bookingDetails->bill_details, true);
        $newArray = [];
        foreach ($bill_details as $value) {
            $parameter = $value['parameter'];
            $parameterDetails = PricingParameter::find($parameter);
            if(!empty($parameterDetails)){
                $parameterName = $parameterDetails->ParameterApplication;
            }else{
                $parameterName = $parameter;
            }
            $newArray[] = array('name' => $parameterName, 'amount' => $value['amount']);
        }
        echo json_encode($newArray, true);
    }
}
