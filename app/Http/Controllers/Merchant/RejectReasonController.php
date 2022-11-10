<?php

namespace App\Http\Controllers\Merchant;


use App;
use App\Models\RejectTranslation;
use Auth;
use Illuminate\Http\Request;
use App\Models\RejectReason;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class RejectReasonController extends Controller
{
    public function index()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $rejectreasons = RejectReason::where([['merchant_id', '=', $merchant_id]])->paginate(25);
        return view('merchant.rejectreasons.index', compact('rejectreasons'));
    }

    public function store(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'title' => ['required', 'max:255',
                Rule::unique('reject_translations', 'title')->where(function ($query) use ($merchant_id, &$id) {
                    $query->where([['merchant_id', '=', $merchant_id]]);
                })],
            'action' => 'required',
        ]);
        $reason = new RejectReason();
        $reason->merchant_id = $merchant_id;
        $reason->status = 1;
        $reason->save();
        $this->SaveLanguage($merchant_id, $reason->id, $request->title,$request->action);
        return redirect()->back()->with('reject', trans('admin.message705'));
    }

    public function SaveLanguage($merchant_id, $reason_id, $title,$action)
    {
        RejectTranslation::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'reject_reason_id' => $reason_id
        ], [
            'title' => $title,
            'action' => $action,
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
        $reason = RejectReason::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $reason->status = $status;
        $reason->save();
        return redirect()->route('rejectreason.index');
    }

    public function edit($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $reason = RejectReason::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        return view('merchant.rejectreasons.edit', compact('reason'));
    }

    public function update(Request $request, $id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'title' => ['required', 'max:255',
                Rule::unique('reject_translations', 'title')->where(function ($query) use ($merchant_id, &$id) {
                    $query->where([['merchant_id', '=', $merchant_id]]);
                })->ignore($id)],
            'action' => 'required',
        ]);
        $reason = RejectReason::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $this->SaveLanguage($merchant_id, $reason->id, $request->title,$request->action);
        return redirect()->back()->with('reject', trans('admin.message707'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
