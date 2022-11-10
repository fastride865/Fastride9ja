<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use Auth;
use App;
use App\Models\CancelReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Traits\MerchantTrait;
class CancelReasonController extends Controller
{
    use MerchantTrait;
    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'CANCEL_REASON')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
        $all_segments = array_merge(['DELIVERY','TAXI','HANDYMAN'],$all_food_grocery_clone);
        $checkPermission = check_permission(1, $all_segments, true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $permission_segments = get_permission_segments(1,true);
        $cancelreasons = CancelReason::whereHas('Segment',function($query) use($permission_segments){
            $query->whereIn('slag',$permission_segments);
        })->where([['merchant_id', '=', $merchant_id]])->paginate(15);
        $merchant_segments = get_permission_segments(1,false,get_merchant_segment());
        return view('merchant.cancelreason.index', compact('cancelreasons','merchant_segments'));
    }

    public function store(Request $request)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
        $all_segments = array_merge(['DELIVERY','TAXI','HANDYMAN'],$all_food_grocery_clone);
        $checkPermission = check_permission(1, $all_segments, true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $request->validate([
            'reason_for' => 'required|integer',
            'reason' => 'required',
        ]);
        $cancel_reason = CancelReason::create([
            'merchant_id' => $merchant_id,
            'segment_id' => $request->segment_id,
            'reason_type' => $request->reason_for,
            'reason_status' => 1,
        ]);
        $this->SaveLanguageCancel($merchant_id, $cancel_reason->id, $request->reason);
        return redirect()->back()->withSuccess(trans("$string_file.added_successfully"));
    }

    public function SaveLanguageCancel($merchant_id, $cancel_reason_id, $reason)
    {
        App\Models\LanguageCancelReason::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'cancel_reason_id' => $cancel_reason_id
        ], [
            'reason' => $reason,
        ]);
    }

    public function ChangeStatus($id, $status)
    {
        $validator = Validator::make(
            [
                'id' => $id,
                'status' => $status,
            ],
            [
                'id' => ['required'],
                'status' => ['required', 'integer', 'between:1,2'],
            ]);
        if ($validator->fails()) {
            return redirect()->back();
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $cancelreason = CancelReason::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $cancelreason->reason_status = $status;
        $cancelreason->save();
        return redirect()->route('cancelreason.index')->with('success', "Status Updated");
    }

    public function edit($id)
    {
        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
        $all_segments = array_merge(['DELIVERY','TAXI','HANDYMAN'],$all_food_grocery_clone);
        $checkPermission = check_permission(1, $all_segments, true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $cancelreason = CancelReason::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
        $merchant_segments = get_merchant_segment();
        return view('merchant.cancelreason.edit', compact('cancelreason','merchant_segments'));
    }

    public function update(Request $request, $id)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
        $all_segments = array_merge(['DELIVERY','TAXI','HANDYMAN'],$all_food_grocery_clone);
        $checkPermission = check_permission(1, $all_segments, true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $request->validate([
            'reason' => 'required',
        ]);
        $cancelreasons = CancelReason::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
        $cancelreasons->segment_id = $request->segment_id;
        $cancelreasons->save();
        $this->SaveLanguageCancel($merchant_id, $cancelreasons->id, $request->reason);
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function Search(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $query = CancelReason::where([['merchant_id', '=', $merchant_id]]);
        if ($request->reason) {
            $keyword = $request->reason;
            $query->WhereHas('LanguageSingle', function ($q) use ($keyword) {
                $q->where('reason', 'LIKE', "%$keyword%");
            });
        }
        if ($request->reason_for) {
            $query->where('reason_type', $request->reason_for);
        }
        $cancelreasons = $query->paginate(25);
        return view('merchant.cancelreason.index', compact('cancelreasons'));
    }

    public function destroy($id)
    {
        //
    }
}
