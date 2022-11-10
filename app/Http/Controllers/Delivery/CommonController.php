<?php

namespace App\Http\Controllers\Delivery;

use App;
use Auth;
use App\Models\DeliveryConfiguration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommonController extends Controller
{

    /* Note we are not using these function**/
    public function index()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $configuration = DeliveryConfiguration::firstOrCreate(['merchant_id' => $merchant_id]);
        return view('delivery.configuration.index', compact('configuration'));
    }

    public function store(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'radius' => 'required',
            'request_drivers' => 'required',
            'later_request_type' => 'required',
            'later_radius' => 'required',
            'later_request_drivers' => 'required',
        ]);
        $category = DeliveryConfiguration::updateOrcreate(['merchant_id' => $merchant_id,],[
            'radius' => $request->radius,
            'request_drivers' => $request->request_drivers,
            'later_request_type' => $request->later_request_type,
            'later_radius' => $request->later_radius,
            'later_request_drivers'=> $request->later_request_drivers,
        ]);
        return redirect()->back()->with('configurationadded', 'Configuration Added');

    }
}
