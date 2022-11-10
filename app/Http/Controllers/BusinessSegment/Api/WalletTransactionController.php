<?php

namespace App\Http\Controllers\BusinessSegment\Api;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\BusinessSegment\BusinessSegmentCashout;
use App\Models\BusinessSegment\BusinessSegmentWalletTransaction;
use App\Models\Configuration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;

class WalletTransactionController extends Controller
{
    use ApiResponseTrait,MerchantTrait;

    public function getTransactions(Request $request){
        // Transaction_type
        // 1. credit
        // 2. Debit

        // Payment_method
        // 1. Cash
        // 2. NonCash
        // 3. CashBack
        $bs = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        $merchant_id = $request->merchant_id;
        
        $arr_wallet_transactions = BusinessSegmentWalletTransaction::where('business_segment_id',$bs->id)
                                    ->where(function ($query) use ($request) {
                                        if($request->start) {
                                            $start_date = date('Y-m-d',strtotime($request->start));
                                            $end_date = date('Y-m-d ',strtotime($request->end));
                                            $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                                        }
                                    })    
                                    ->latest()->paginate(25);
        $data=[];
        foreach($arr_wallet_transactions as $transaction){
            $narration='------';
            if($transaction->narration==1){
                $narration=trans("$string_file.message44");
            }
            elseif($transaction->narration==2){
                $narration=trans("$string_file.order_amount_added_by_admin");
            }
            elseif($transaction->narration==3){
                $narration=trans("$string_file.order_commission_deducted");
            }
            elseif($transaction->narration==4){
                $narration=trans("$string_file.cashout_amount_deducted");
            }
            elseif($transaction->narration==5){
                $narration=trans("$string_file.cashout_request_rejected_refund_amount");
            }

            $data[] = [
                'transaction_type'=>($transaction->transaction_type == 1)?trans("$string_file.credit"):trans("$string_file.debit"),
                'payment_mode'=> ($transaction->payment_method == 1)?trans("$string_file.cash"):trans("$string_file.non_cash"),
                'amount'=> $transaction->BusinessSegment->Country->isoCode.' '.$transaction->amount,
                'narration'=>$narration,
                'transaction_on'=> convertTimeToUSERzone($transaction->created_at,null, null, $transaction->BusinessSegment->Merchant),
            ];
        }
        $wallet_transactions = $arr_wallet_transactions->toArray();
        $next_page_url = isset($wallet_transactions['next_page_url']) && !empty($wallet_transactions['next_page_url']) ? $wallet_transactions['next_page_url'] : "";
        $current_page = isset($wallet_transactions['current_page']) && !empty($wallet_transactions['current_page']) ? $wallet_transactions['current_page'] : 0;

        $response =[
            'current_page'=>$current_page,
            'next_page_url'=>$next_page_url,
            'total_amount'=>$bs->Country->isoCode.' '.$bs->wallet_amount,
            'response_data'=>$data
        ];
        return $this->successResponse(trans("$string_file.success"),$response);
    }
    
    public function getCashoutTransactions(Request $request){
        $bs = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        $arr_cashout_requests = BusinessSegmentCashout::where('business_segment_id',$bs->id)->latest()->paginate(25);
        $data=[];
        foreach($arr_cashout_requests as $transaction){
            if($transaction->cashout_status==0){
                $status=trans("$string_file.pending");
            }
            elseif($transaction->cashout_status==1){
                $status=trans("$string_file.success");
            }
            elseif($transaction->cashout_status==2){
                $status=trans("$string_file.rejected");
            }
           

            $data[] = [
                'amount'=>$transaction->BusinessSegment->Country->isoCode.' '.$transaction->amount,
                'status'=> $status,
                'action_by'=> ($transaction->action_by != '') ? $transaction->action_by : '---',
                'transaction_id'=>($transaction->transaction_id) ? $transaction->transaction_id : '---',
                'comment'=> ($transaction->comment != '') ? $transaction->comment : '---',
            ];
        }
        $cashout_requests = $arr_cashout_requests->toArray();
        $next_page_url = isset($cashout_requests['next_page_url']) && !empty($cashout_requests['next_page_url']) ? $cashout_requests['next_page_url'] : "";
        $current_page = isset($cashout_requests['current_page']) && !empty($cashout_requests['current_page']) ? $cashout_requests['current_page'] : 0;

        $response =[
            'current_page'=>$current_page,
            'next_page_url'=>$next_page_url,
            'total_amount'=>$bs->Country->isoCode.' '.$bs->wallet_amount,
            'response_data'=>$data
        ];
        return $this->successResponse(trans("$string_file.success"),$response);
    }

    public function requestCashout(Request $request){
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try{
            $bs = $request->user('business-segment-api');
            $string_file = $this->getStringFile(NULL,$bs->Merchant);
            if($bs->wallet_amount < $request->amount){
                return $this->failedResponse(trans('admin.wallet_balance_low'));
            }
            $paramArray = array(
                'business_segment_id' => $bs->id,
                'booking_id' => null,
                'amount' => $request->amount,
                'narration' => 4,
            );
            WalletTransaction::BusinessSegmntWalletDebit($paramArray);
            BusinessSegmentCashout::create([
                'business_segment_id' => $bs->id,
                'merchant_id' => $bs->merchant_id,
                'amount' => $request->amount,
            ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans("$string_file.cashout_request_registered_successfully"));
    }
}
?>