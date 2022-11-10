<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\Configuration;
use App\Models\DriverCashout;
use App\Models\DriverConfiguration;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DriverCashoutController extends Controller
{
    use ApiResponseTrait,MerchantTrait;
    public function index(Request $request){
        try{
            $driver = $request->user('api-driver');
            $string_file = $this->getStringFile(NULL,$driver->Merchant);
            $configuration = Configuration::where('merchant_id',$driver->merchant_id)->first();
            $driver_config = DriverConfiguration::where('merchant_id',$driver->merchant_id)->first();
            if(isset($configuration->driver_cashout_module) && $configuration->driver_cashout_module != 1 && isset($driver_config->driver_cashout_min_amount) && $driver_config->driver_cashout_min_amount != 1){
                return $this->failedResponse(trans("$string_file.configuration_not_found"));
            }else{
                $data = [];
                $cashout_requests = DriverCashout::where([['merchant_id','=',$driver->merchant_id],['driver_id',$driver->id]])->orderBy('created_at','DESC')->get();
                if($cashout_requests->count() > 0){
                    foreach ($cashout_requests as $cashout_request){
                        // p($cashout_request->cashout_status);
                        $cashout_status = '';
                        switch ($cashout_request->cashout_status){
                            case "0":
                                // p($cashout_status);
                                $cashout_status = trans("$string_file.pending");
                                // p($cashout_status);
                                break;
                            case "1":
                                $cashout_status = trans("$string_file.success");
                                break;
                            case "2":
                                $cashout_status = trans("$string_file.rejected");
                                break;
                            default:
                                $cashout_status ="";

                        }
                        // p($cashout_status);
                        array_push($data,array(
                            'id' => $cashout_request->id,
                            'amount' => $driver->CountryArea->Country->isoCode .' '.$cashout_request->amount,
                            'cashout_status' => $cashout_status,
                            'action_by' => $cashout_request->action_by,
                            'transaction_id' => $cashout_request->transaction_id,
                            'comment' => $cashout_request->comment,
                            'created_at' =>  strtotime($cashout_request->created_at),
                            'updated_at' =>  strtotime($cashout_request->updated_at),
                        ));
                    }
                }
                return $this->successResponse(trans("$string_file.cash_out_request_driver"),$data);
            }
        }catch (\Exception $e){
            return $this->failedResponse($e->getMessage());
        }
    }

    public function request(Request $request){
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try{
            $driver = $request->user('api-driver');
            $string_file = $this->getStringFile(NULL,$driver->Merchant);
            $configuration = Configuration::where('merchant_id',$driver->merchant_id)->first();
            $driver_config = DriverConfiguration::where('merchant_id',$driver->merchant_id)->first();
            if(isset($configuration->driver_cashout_module) && $configuration->driver_cashout_module != 1 && isset($driver_config->driver_cashout_min_amount) && $driver_config->driver_cashout_min_amount != 1){
                return $this->failedResponse(trans("$string_file.configuration_not_found"));
            }else{
                if($request->amount < $driver_config->driver_cashout_min_amount){
                    $amount = $driver->CountryArea->Country->isoCode .' '.$driver_config->driver_cashout_min_amount;
                    return $this->failedResponse(trans("$string_file.cash_out_min_amount_requested").' '.$amount);
                }
                if($driver->wallet_money < $request->amount){
                    return $this->failedResponse(trans("$string_file.low_wallet_warning"));
                }
                $paramArray = array(
                    'driver_id' => $driver->id,
                    'booking_id' => null,
                    'amount' => $request->amount,
                    'narration' => 10,
                );
                WalletTransaction::WalletDeduct($paramArray);
//                \App\Http\Controllers\Helper\CommonController::WalletDeduct($driver->id,null,$request->amount,10,2,2);
                DriverCashout::create([
                    'driver_id' => $driver->id,
                    'merchant_id' => $driver->merchant_id,
                    'amount' => $request->amount,
                ]);
                DB::commit();
                return $this->successResponse(trans("$string_file.cash_out_request_driver_successfully"));
            }
        }catch (\Exception $e){
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
    }
}
