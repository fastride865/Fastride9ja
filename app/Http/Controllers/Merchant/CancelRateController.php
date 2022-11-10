<?php

namespace App\Http\Controllers\Merchant;


use App\Models\CancelRate;
use App\Models\PaymentConfiguration;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class CancelRateController extends Controller
{

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $merchant_id = $request->user('merchant')->parent_id != 0 ? $request->user('merchant')->parent_id : $request->user('merchant')->id;
            $payment_config = PaymentConfiguration::select('cancel_rate_table_enable')->where('merchant_id', $merchant_id)->first();
            if (!$payment_config || $payment_config->cancel_rate_table_enable != 1) {
                die('not found');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;

        $cancel_rates = CancelRate:: where('merchant_id', $merchant_id)->get();

        return view('merchant.cancelrate.index', [
            'cancel_rates' => $cancel_rates
        ]);
    }

    public function create()
    {
        return view('merchant.cancelrate.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_range' => 'required|numeric|unique:cancel_rates,start_range|unique:cancel_rates,end_range',
            'end_range' => 'required|numeric|unique:cancel_rates,start_range|unique:cancel_rates,end_range',
            'charge' => 'required|numeric'
        ]);

        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;

        $cancelrate = CancelRate:: create([
            'merchant_id' => $merchant_id,
            'start_range' => $request->start_range,
            'end_range' => $request->end_range,
            'charge' => $request->charge,
            'charge_type' => $request->charge_type
        ]);

        if ($cancelrate) {
            return redirect()->back()->with('cancelrate', __('admin.cancelrate.added'));
        }

        return redirect()->back()->with('cancelrate', __('admin.swr'))->withInput();

    }

    public function edit($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $cancelrate = CancelRate:: where('id', $id)->where('merchant_id', $merchant_id)->first();


        return view('merchant.cancelrate.edit', ['cancelrate' => $cancelrate]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'start_range' => 'required|numeric|unique:cancel_rates,start_range,' . $id . '|unique:cancel_rates,end_range,' . $id,
            'end_range' => 'required|numeric|unique:cancel_rates,start_range,' . $id . '|unique:cancel_rates,end_range,' . $id,
            'charge' => 'required|numeric'
        ]);

        $cancelrate = CancelRate:: where('id', $id)->update([
            'start_range' => $request->start_range,
            'end_range' => $request->end_range,
            'charge' => $request->charge,
            'charge_type' => $request->charge_type
        ]);

        if ($cancelrate) {
            return redirect()->back()->with('cancelrate', __('admin.cancelrate.updated'));
        }

        return redirect()->back()->with('cancelrate', __('admin.swr'))->withInput();
    }

    /*
     * merged by @Amba for remaining points of delivery
     * */
    public function destroy($id)
    {
        $rate = CancelRate::find($id);
        if ($rate && $rate->delete()) {
            return redirect()->back()->with('cancelrate', __('admin.deleted.successfully'));
        }
        return redirect()->back()->with('cancelrate', __('admin.swr'));
    }
}
