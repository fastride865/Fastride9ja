<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\BookingDetail;
use App\Models\Configuration;
use App\Models\Merchant;
use App\Models\PricingParameter;
use App\Models\User;
use App\Models\Driver;
use App\Models\Onesignal;
use App\Models\DriverWalletTransaction;
use App\Models\UserWalletTransaction;
use App\Models\UserDevice;
use Auth;
use App\Traits\BookingTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    use BookingTrait;

    public function index()
    {
        $corporate = Auth::user('corporate');
        $merchant = $corporate->Merchant;
        $transactions = $this->getAllTransaction(true,'CORPORATE');
        /*echo"<pre>";
        print_r($transactions->toArray());
        die();*/
        return view('corporate.transaction.index', compact('transactions','merchant'));
    }

    public function Search(Request $request)
    {
        $merchant = Merchant::find(Auth::user('corporate')->merchant_id);
        $query = $this->getAllTransaction(false,'CORPORATE');
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
                $q->where(\DB::raw('concat(`first_name`, `last_name`)'), 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where(\DB::raw('concat(`first_name`, `last_name`)'), 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $transactions = $query->paginate(25);
        return view('corporate.transaction.index', compact('transactions', 'merchant'));
    }

    public function GetBillDetails(Request $request)
    {
        $bookingDetails = BookingDetail::where([['booking_id', '=', $request->booking_id]])->first();
        $newArray = [];
        if(!empty($bookingDetails)):
            $bill_details = json_decode($bookingDetails->bill_details, true);
            if(!empty($bill_details)) {
                foreach ($bill_details as $value) {
                    $parameter = $value['parameter'];
                    $parameterDetails = pricing::find($parameter);
                    if (!empty($parameterDetails)):
                        $parameterName = $parameterDetails['ParameterApplication'];
                    else:
                        $parameterName = $value['parameter'];
                    endif;
                    $newArray[] = array('name' => $parameterName, 'amount' => $value['amount']);
                }
            }
        endif;
        echo json_encode($newArray, true);
    }
    
    public function WalletRechargeView(){
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        return view('merchant.random.wallet_recharge',compact('config'));
    }
    
    public function getDetails(Request $request){
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        if($request->application == 1){
            if($request->searchby == 1){
                $details = Driver::where([['merchant_id', '=', $merchant_id],['email', '=', $request->valu]])->first();
            }else{
                $details = Driver::where([['merchant_id', '=', $merchant_id],['phoneNumber', '=', $request->valu]])->first();
            }
        }else{
            if($request->searchby == 1){
                $details = User::where([['merchant_id', '=', $merchant_id],['email', '=', $request->valu]])->first();
            }else{
                $details = User::where([['merchant_id', '=', $merchant_id],['UserPhone', '=', $request->valu]])->first();
            }
        }
        return $details->id;
    }
    
    public function WalletRecharge(Request $request){
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'payment_method' => 'required|integer|between:1,2',
            'receipt_number' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'phone' => 'required',
        ]);
        if($request->application == 1){
            $parameter = $request->searchby == 1 ? "email":"phoneNumber";
            $driver = Driver::where([['merchant_id', '=', $merchant_id],[$parameter, '=', $request->phone]])->first();
            if(empty($driver)){
                return redirect()->back()->with('moneyAdded', trans('admin.notfound'));
            }
            //            $newAmount = new \App\Http\Controllers\Helper\Merchant();
            $paramArray = array(
                'driver_id' => $driver->id,
                'booking_id' => NULL,
                'amount' => $request->amount,
                'narration' => 1,
                'platform' => 1,
                'payment_method' => $request->payment_method,
                'receipt' => $request->receipt_number,
            );
            WalletTransaction::WalletCredit($paramArray);
//            CommonController::WalletCredit($driver->id,null,$request->amount,1,1,$request->payment_method,$request->receipt_number);
//            DriverWalletTransaction::create([
//                'merchant_id' => $merchant_id,
//                'driver_id' => $driver->id,
//                'transaction_type' => 1,
//                'payment_method' => $request->payment_method,
//                'receipt_number' => $request->receipt_number,
//                'amount' =>  $newAmount->TripCalculation($request->amount, $merchant_id),
//                'platform' => 1,
//                'description' => $request->description,
//                'narration' => 1,
//            ]);
//            $wallet_money = $driver->wallet_money + $request->amount;
//            $driver->wallet_money = $newAmount->TripCalculation($wallet_money, $merchant_id);
//            $driver->save();
////            $playerids = array($driver->player_id);
//            $message = trans('api.money');
//            $data = ['message' => $message];
//            Onesignal::DriverPushMessage($driver->id, $data, $message, 3, $merchant_id);
        }else{
            $parameter = $request->searchby == 1 ? "email":"UserPhone";
            $user = User::where([['merchant_id', '=', $merchant_id],[$parameter, '=', $request->phone]])->first();
            if(empty($user)){
                return redirect()->back()->with('moneyAdded', trans('admin.notfound'));
            }
//            $newAmount = new \App\Http\Controllers\Helper\Merchant();
            $paramArray = array(
                'user_id' => $user->id,
                'booking_id' => NULL,
                'amount' => $request->amount,
                'narration' => 1,
                'platform' => 1,
                'payment_method' => $request->payment_method,
                'receipt' => $request->receipt_number
            );
            WalletTransaction::UserWalletCredit($paramArray);
//            CommonController::UserWalletCredit($user->id,NULL,$request->amount,1,1,$request->payment_method,$request->receipt_number);
//            UserWalletTransaction::create([
//                'merchant_id' => $merchant_id,
//                'user_id' => $user->id,
//                'platfrom' => 1,
//                'amount' => $newAmount->TripCalculation($request->amount, $merchant_id),
//                'payment_method' => $request->payment_method,
//                'receipt_number' => $request->receipt_number,
//                'description' => $request->description,
//                'type' => 1,
//            ]);
//            $wallet_money = $user->wallet_balance + $request->amount;
//            $user->wallet_balance = $newAmount->TripCalculation($wallet_money, $merchant_id);
//            $user->save();
////            $userdevices = UserDevice::where([['user_id', '=', $user->id]])->get();
////            $playerids = array_pluck($userdevices, 'player_id');
//            $message = trans('api.money');
//            $data = ['message' => $message];
//            Onesignal::UserPushMessage($user->id, $data, $message, 3, $merchant_id);
        }
        return redirect()->back()->with('moneyAdded', trans('admin.message207'));
    }
}
