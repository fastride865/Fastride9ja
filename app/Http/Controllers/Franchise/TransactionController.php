<?php

namespace App\Http\Controllers\Franchise;

use Auth;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function index()
    {
        $franchise_id = Auth::user('franchise')->id;
        $transactions = Booking::where([['franchise_id', '=', $franchise_id], ['booking_type', '=', 1]])->paginate(25);
        return view('franchise.transaction.index', compact('transactions'));
    }

    public function Search(Request $request)
    {
        $franchise_id = Auth::user('franchise')->id;
        $query = Booking::where([['franchise_id', '=', $franchise_id], ['booking_type', '=', 1]]);
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
        return view('franchise.transaction.index', compact('transactions'));
    }
}
