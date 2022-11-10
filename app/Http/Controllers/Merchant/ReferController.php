<?php

namespace App\Http\Controllers\Merchant;

use App\Models\ReferCommissionFare;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class ReferController extends Controller
{
  public function index()
  {
    $merchant_id = get_merchant_id();
    $commissions = ReferCommissionFare :: where('merchant_id' , $merchant_id)->get();
    return view('merchant.commissionfare.index' , ['commissions' => $commissions]);
  }

  public function create($id = NULL)
  {
      if($id != NULL ){
          $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
          $commissionfare = ReferCommissionFare :: where('id' , $id)->where('merchant_id' , $merchant_id)->first();
          if (!$commissionfare) {
              return Redirect::back()->withErrors(['msg', 'The Message']);
          }
          return view('merchant.commissionfare.create' , ['commissionfare' => $commissionfare]);
      }
      return view('merchant.commissionfare.create');
  }

  public function store(Request $request,  $id = NULL)
  {
    $merchant_id = get_merchant_id();
    $request->validate([
      'name' => 'required|unique:refer_commission_fare,name,'.$id.',id,merchant_id,'.$merchant_id,
      'start_range' => 'required|numeric|unique:refer_commission_fare,start_range,'.$id.',id,merchant_id,'.$merchant_id.'|unique:refer_commission_fare,end_range,'.$id.',id,merchant_id,'.$merchant_id,
      'end_range' => 'required|numeric|unique:refer_commission_fare,start_range,'.$id.',id,merchant_id,'.$merchant_id.'|unique:refer_commission_fare,end_range,'.$id.',id,merchant_id,'.$merchant_id,
      'commission' => 'required|numeric'
    ]);

    $query = DB::table('refer_commission_fare')
          ->where('merchant_id', $merchant_id)
          ->whereBetween('start_range', array($request->start_range, $request->end_range))
          ->orWhereBetween('end_range', array($request->start_range, $request->end_range));
     if($id != NULL){
         $query = $query->where('id','!=' ,$id);
//         $message = trans('admin.commissionfare.updated');
     }
     $existing_range_record = $query->get();
    if($existing_range_record->count() >= 1){
        return redirect()->back()->withErrors(trans("$string_file.something_went_wrong"))->withInput();
    }
    $commission = ReferCommissionFare::updateOrCreate(['id' => $id, 'merchant_id' => $merchant_id])->update([
      'name' => $request->name,
      'start_range' => $request->start_range,
      'end_range' => $request->end_range,
      'commission' => $request->commission
    ]);
    if ($commission){
      return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }
    return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"))->withInput();
  }

//  public function edit($id)
//  {
//    $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
//    $commissionfare = ReferCommissionFare :: where('id' , $id)->where('merchant_id' , $merchant_id)->first();
//
//    if (!$commissionfare) {
//      return redirect('/404');
//    }
//
//    return view('merchant.commissionfare.edit' , ['commissionfare' => $commissionfare]);
//  }

//  public function update(Request $request , $id)
//  {
//    $request->validate([
//      'start_range' => 'required|numeric|unique:refer_commission_fare,start_range,'.$id.'|unique:refer_commission_fare,end_range,'.$id,
//      'end_range' => 'required|numeric|unique:refer_commission_fare,start_range,'.$id.'|unique:refer_commission_fare,end_range,'.$id,
//      'commission' => 'required|numeric'
//    ]);
//
//    $commission = ReferCommissionFare :: where('id' , $id)->update([
//      'start_range' => $request->start_range,
//      'end_range' => $request->end_range,
//      'commission' => $request->commission
//    ]);
//
//    if ($commission) {
//      return redirect()->back()->with('driverCommission' , __('admin.commissionfare.updated'));
//    }
//
//    return redirect()->back()->with('driverCommission' , __('admin.swr'))->withInput();
//  }

    /*
      * merged by @Amba for remaining points of delivery
      * */
    public function destroy ($id) {
        $refer = ReferCommissionFare::find($id);
        if ($refer && $refer->delete()) {
            return redirect()->back()->with('driverCommission' , __('admin.deleted.successfully'));
        }
        return redirect()->back()->with('driverCommission' , __('admin.swr'));
    }
}