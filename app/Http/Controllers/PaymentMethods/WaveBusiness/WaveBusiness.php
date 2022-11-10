<?php
namespace App\Http\Controllers\PaymentMethods\WaveBusiness;

use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\Driver;
use App\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Validator;

class WaveBusiness extends Controller
{
    use ApiResponseTrait;

    public function handlerCallBack(Request $request){
        try {
            $settled = "";
            if(isset($request['data'])){
                $data = $request['data'];
                $existRecord = DB::table('transactions')->where(['payment_transaction_id' => $data['client_reference']])->first();
                if(!empty($existRecord)){
                    if($existRecord->request_status == 1 && $data['payment_status'] == "succeeded"){
                        if ($existRecord->status == 1) {
                            $user = User::find($existRecord->user_id);
                            $transaction = DB::table('transactions')
                                ->where('payment_transaction_id', $data['client_reference'])
                                ->update([
                                    'request_status' => 2,
                                    'payment_transaction' => $request->all(),
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                            $paramArray = array(
                                'user_id' => $user->id,
                                'booking_id' => NULL,
                                'amount' => $existRecord->amount,
                                'narration' => 2,
                                'platform' => 2,
                                'payment_method' => 2,
                                'receipt' => "WaveBusiness",
                            );
                            WalletTransaction::UserWalletCredit($paramArray);
                        } else if ($existRecord->status == 2) {
                            $driver = Driver::find($existRecord->driver_id);
                            $transaction = DB::table('transactions')
                                ->where('payment_transaction_id', $data['client_reference'])
                                ->update([
                                    'request_status' => 2,
                                    'payment_transaction' => $request->all(),
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                            $paramArray = array(
                                'driver_id' => $driver->id,
                                'booking_id' => NULL,
                                'amount' => $existRecord->amount,
                                'narration' => 2,
                                'platform' => 2,
                                'payment_method' => 2,
                                'receipt' => "WaveBusiness",
                            );
                            WalletTransaction::WalletCredit($paramArray);
                        }
                    }else{
                        $settled = $existRecord->request_status;
                    }
                }else{
                    $settled = "Not Found";
                }
            }
            $log_data = array(
                'request_type' => "Wave Business Webhook Call",
                'request_data' => $request->all(),
                'additional_notes' => array(
                    "settled" => $settled
                ),
                'hit_time' => date('Y-m-d H:i:s')
            );
            \Log::channel('wave_business')->info($log_data);
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse("Success");
    }

    public function request(Request $request, $status){
        $log_data = array(
            'request_type' => "Wave Business Webhook Request - ".$status,
            'request_data' => $request->all(),
            'additional_notes' => "",
            'hit_time' => date('Y-m-d H:i:s')
        );
        \Log::channel('wave_business')->info($log_data);
        if(strtoupper($status) == "ERROR"){
            return $this->failedResponse($status, $request->all());
        }else{
            return $this->successResponse($status, $request->all());
        }
    }

    public function createTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
            'amount' => 'required',
            'type' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        if ($request->type == 1) {
            $user = $request->user('api');
        } elseif ($request->type == 2) {
            $user = $request->user('api-driver');
        } else {
            return $this->failedResponse("Invalid Type");
        }
        DB::beginTransaction();
        try {
            $existRecord = DB::table('transactions')->where(['payment_transaction_id' => $request->transaction_id])->get();
            if ($existRecord->count() > 0) {
                return $this->failedResponse("Payment Request Already Recorded");
            } else {
                DB::table('transactions')->insert([
                        'merchant_id' => $user->merchant_id,
                        'status' => $request->type,
                        'amount' => $request->amount,
                        'user_id' => $request->type == 1 ? $user->id : NULL,
                        'driver_id' => $request->type == 2 ? $user->id : NULL,
                        'payment_transaction_id' => $request->transaction_id,
                        'request_status' => 1,
                    ]
                );
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse("Success");
    }
}