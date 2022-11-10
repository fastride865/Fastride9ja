<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper\ButtlerTaxi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ButtlerController extends Controller
{
    public function index(Request $request, $alias_name)
    {
        $see = $request->all();
        $class = new ButtlerTaxi();
        return $class->checkout($alias_name);
    }

    public function tripstatus(Request $request, $alias_name, $tripId = null)
    {
        $ride_id = $tripId;
        $request->request->add(['ride_id' => $ride_id]);
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0]]);
        }

        $class = new ButtlerTaxi();
        return $class->get_status($ride_id, $alias_name);
    }

    public function trips_delete(Request $request, $alias_name, $tripId = null)
    {
        $ride_id = $tripId;
        $request->request->add(['ride_id' => $ride_id]);
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0]]);
        }
        $class = new ButtlerTaxi();
        return $class->delete_ride($ride_id, $alias_name);
    }
}
