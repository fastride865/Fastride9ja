<?php

namespace App\Http\Controllers\BusinessSegment;

use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\BusinessSegment\BusinessSegmentCashout;
use App\Models\BusinessSegment\BusinessSegmentWalletTransaction;
use App\Models\Configuration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use View;
use App\Traits\MerchantTrait;

class WalletTransactionController extends Controller
{
    use MerchantTrait;
    public function walletSearchView($request)
    {
        $data['arr_search'] = $request->all();
        $wallet_search = View::make('business-segment.wallet.wallet-search')->with($data)->render();
        return $wallet_search;
    }
    public function index(Request $request){
        $data = [];
        $merchant_id = get_merchant_id();

        $request->request->add(['search_route'=>route('business-segment.wallet')]);
        $order_con = new WalletTransactionController;
        
        $request->request->add(['calling_view'=>"wallet-transaction-list"]);
        $search_view = $order_con->walletSearchView($request);
        $data['arr_search'] = $request->all();
        $business_segment = get_business_segment(false);
        $wallet_transactions = BusinessSegmentWalletTransaction::where('business_segment_id',$business_segment->id)
                                ->where(function ($query) use ($request) {
                                    if($request->start) {
                                        $start_date = date('Y-m-d',strtotime($request->start));
                                        $end_date = date('Y-m-d ',strtotime($request->end));
                                        $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                                    }
                                })
                                ->latest()->paginate(25);
//        p($wallet_transactions);
        return view('business-segment.wallet.index',compact('business_segment','wallet_transactions','search_view'));
    }

    public function cashouts(Request $request){
        $data = [];
        $merchant_id = get_merchant_id();

        $request->request->add(['search_route'=>route('business-segment.cashouts')]);
        $order_con = new WalletTransactionController;
        
        $request->request->add(['calling_view'=>"cashout-list"]);
        $search_view = $order_con->walletSearchView($request);
        $data['arr_search'] = $request->all();
        $business_segment = get_business_segment(false);
        $cashout_requests = BusinessSegmentCashout::where('business_segment_id',$business_segment->id)
                            ->where(function ($query) use ($request) {
                                    if($request->start) {
                                        $start_date = date('Y-m-d',strtotime($request->start));
                                        $end_date = date('Y-m-d ',strtotime($request->end));
                                        $query->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                                    }
                                })
                            ->latest()->paginate(25);
        return view('business-segment.wallet.cashout',compact('business_segment','cashout_requests','search_view'));
    }

    public function cashoutRequest(Request $request){
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try{
            $business_segment = get_business_segment(false);
            $merchant_id = $business_segment->merchant_id;
            $string_file = $this->getStringFile($merchant_id);
            if($business_segment->wallet_amount < $request->amount){
                return redirect()->back()->withErrors(trans('admin.wallet_balance_low'));
            }
            $paramArray = array(
                'business_segment_id' => $business_segment->id,
                'booking_id' => null,
                'amount' => $request->amount,
                'narration' => 4,
            );
            WalletTransaction::BusinessSegmntWalletDebit($paramArray);
            BusinessSegmentCashout::create([
                'business_segment_id' => $business_segment->id,
                'merchant_id' => $business_segment->merchant_id,
                'amount' => $request->amount,
            ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            p($e->getMessage());
            return redirect()->back()->withErrors($e->getMessage());
        }
        return redirect()->back()->with('success',trans("$string_file.cashout_request_registered_successfully"));
    }
}
