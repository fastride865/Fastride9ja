<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\Configuration;
use App\Models\Driver;
use App\Models\DriverCashout;
use App\Models\InfoSetting;
use App\Models\Onesignal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\MerchantTrait;

class DriverCashoutController extends Controller
{
    use MerchantTrait;
    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'DRIVER_CASHOUT')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index(Request $request){
        try{
            $merchant_id = get_merchant_id();
            $permission_area_ids = [];
            if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
                $permission_area_ids = explode(",",Auth::user()->role_areas);
            }
            $config = Configuration::where('merchant_id',$merchant_id)->first();
//            if(isset($config->driver_cashout_module) && $config->driver_cashout_module == 1){
            $driver_cashout_requests = DriverCashout::with('Driver')->whereHas('Driver', function($q) use($permission_area_ids){
                if(!empty($permission_area_ids)){
                    $q->whereIn("country_area_id",$permission_area_ids);
                }
            })->where('merchant_id',$merchant_id)->latest()->paginate(20);
            return view('merchant.cashout.index',compact('driver_cashout_requests'));
//            }else{
//                return redirect()->route('merchant.dashboard')->withErrors(trans("$string_file.enable_cashout"));
//            }
        }catch (\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function search(Request $request){

    }

    public function changeStatus(Request $request, $id){
        try{

            $merchant = get_merchant_id(false);
            $merchant_id = $merchant->id;
            $driver_cashout_request = DriverCashout::with('Driver')->where('merchant_id',$merchant_id)->find($id);
            $bank_details_enable = $merchant->Configuration->bank_details_enable;
            return view('merchant.cashout.edit',compact('driver_cashout_request','bank_details_enable'));
        }catch (\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

//    public function changeStatusUpdate(Request $request, $id){
//        $validator = Validator::make($request->all(), [
//            'cashout_status' => 'required',
//            'action_by' => 'required',
//            'transaction_id' => 'required',
//            'comment' => 'required',
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return redirect()->back()->withErrors($errors[0]);
//        }
//        DB::beginTransaction();
//        try{
//            $merchant_id = get_merchant_id();
//            $string_file = $this->getStringFile($merchant_id);
//            $driver_cashout_request = DriverCashout::find($id);
//            $driver = Driver::find($driver_cashout_request->driver_id);
//            if($request->cashout_status == 2){
//                $paramArray = array(
//                    'driver_id' => $driver->id,
//                    'booking_id' => NULL,
//                    'amount' => $driver_cashout_request->amount,
//                    'narration' => 15,
//                );
//                WalletTransaction::WalletCredit($paramArray);
////                CommonController::WalletCredit($driver->id,null,$driver_cashout_request->amount,10);
//            }
//            $driver_cashout_request->cashout_status = $request->cashout_status;
//            $driver_cashout_request->action_by = $request->action_by;
//            $driver_cashout_request->transaction_id = $request->transaction_id;
//            $driver_cashout_request->comment = $request->comment;
//            $driver_cashout_request->save();
//            if($request->cashout_status == 1){
//                $data = array(
//                    'notification_type' => "WALLET_UPDATE",
//                    'segment_type' => "WALLET_UPDATE",
//                    'segment_data' => time(),
//                    'notification_gen_time' => time(),
//                );
//                $large_icon = "";
//                $title = "Cashout Request Successfull";
//                $message = "Cashout request successfully completed by admin, You can review that.";
//                $arr_param = ['driver_id'=>$driver->id,'data'=>$data,'message'=>$message,'merchant_id'=>$driver->merchant_id,'title'=>$title,'large_icon'=>$large_icon];
//                Onesignal::DriverPushMessage($arr_param);
//            }
//            DB::commit();
//            $return_message = "";
//            if($request->cashout_status == 0)
//            {
//                $return_message = trans("$string_file.cashout_request_pending");
//            }
//            elseif($request->cashout_status == 1)
//            {
//                $return_message = trans("$string_file.cashout_request_successfully");
//            }
//            elseif($request->cashout_status == 2)
//            {
//                $return_message = trans("$string_file.cashout_request_rejected_refund_amount");
//            }
//            return redirect()->route('merchant.driver.cashout_request')->withSuccess($return_message);
//        }catch (\Exception $e){
//            DB::rollBack();
//            return redirect()->back()->withErrors($e->getMessage());
//        }
//    }

    public function changeStatusUpdate(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'cashout_status' => 'required',
            'action_by' => 'required',
            'transaction_id' => 'required',
            'comment' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withErrors($errors[0]);
        }
        DB::beginTransaction();
        try{
//            $merchant_id = get_merchant_id();
            $driver_cashout_request = DriverCashout::find($id);
            $driver = Driver::find($driver_cashout_request->driver_id);
            $string_file = $this->getStringFile(NULL,$driver_cashout_request->Merchant);
            if($request->cashout_status == 2){
                $paramArray = array(
                    'driver_id' => $driver->id,
                    'booking_id' => NULL,
                    'amount' => $driver_cashout_request->amount,
                    'narration' => 15,
                );
                WalletTransaction::WalletCredit($paramArray);
//                CommonController::WalletCredit($driver->id,null,$driver_cashout_request->amount,10);
                $message = trans("$string_file.cashout_request_rejected_refund_amount");
            }
            $driver_cashout_request->cashout_status = $request->cashout_status;
            $driver_cashout_request->action_by = $request->action_by;
            $driver_cashout_request->transaction_id = $request->transaction_id;
            $driver_cashout_request->comment = $request->comment;
            $driver_cashout_request->save();
            if($request->cashout_status == 1){
                $data = array(
                    'notification_type' => "WALLET_UPDATE",
                    'segment_type' => "WALLET_UPDATE",
                    'segment_data' => [],
//                    'notification_gen_time' => time(),
                );

                $large_icon = "";
                $title = trans("$string_file.cashout_request_accepted");
                $message = trans("$string_file.cashout_request_accepted_message");
                $arr_param = ['driver_id'=>$driver->id,'data'=>$data,'message'=>$message,'merchant_id'=>$driver->merchant_id,'title'=>$title,'large_icon'=>$large_icon];
                Onesignal::DriverPushMessage($arr_param);
            }
            DB::commit();
            return redirect()->route('merchant.driver.cashout_request')->withSuccess($message);
        }catch (\Exception $e){
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
