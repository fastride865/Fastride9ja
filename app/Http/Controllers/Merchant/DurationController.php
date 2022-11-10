<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use App\Models\LangName;
use App\Models\PackageDuration;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Collection;
use DB;
use Validator;

class DurationController extends Controller
{
    use MerchantTrait;
    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'SUBSCRIPTION_DURATION')->first();
        view()->share('info_setting', $info_setting);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $durations = PackageDuration::where([['status', true], ['merchant_id', get_merchant_id()]])->paginate(10);
//        $durations['period'] = \Config::get('custom.package_duration');
        return view('merchant.packageduration.index', compact('durations'));
    }

    /**
     * Add Edit form of duration
     */
    public function add(Request $request, $id = null)
    {
        $data = [];
        $string_file = $this->getStringFile($request->merchant_id);
        if (!empty($id)) {
            $data = PackageDuration::where([['status', true]])->findorfail($id);
            $pre_title = trans("$string_file.edit");
            $submit_button = trans("$string_file.update");
        } else {
            $pre_title = trans("$string_file.add");
            $submit_button = trans("$string_file.save");
        }
        $title = $pre_title . ' ' . trans("$string_file.duration");
        $return['duration'] = [
            'data' => $data,
            'submit_url' => url('merchant/admin/duration/save/' . $id),
            'title' => $title,
//            'duration_period'=>add_blank_option(\Config::get('custom.package_duration'),'-- Select period--'),
            'submit_button' => $submit_button,
        ];
        return view('merchant.packageduration.form')->with($return);
    }

    /***
     * Save/update function of duration
     */
    public function save(Request $request, $id = NULL)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($request->merchant_id);
        $validator = Validator::make($request->all(), [
//            'sequence' =>'required|unique:package_durations,sequence,'.$id.',id,merchant_id,'.$merchant_id,
            'sequence' => 'required',
            'name' => 'required',

        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            if (!empty($id)) {
                $update = PackageDuration::Find($id);
            } else {
                $update = new PackageDuration;
            }
            $this->saveLangDurations($request, $update, $merchant_id);
//            request()->session()->flash('message', trans('admin.duration_name_updated'));

        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->route('duration.index')->withSuccess(trans("$string_file.duration_saved_successfully"));
    }

    private function saveLangDurations(Request $request, PackageDuration $model_data, $merchant_id)
    {
        $update_lang_pro = $model_data->LangPackageDurationAccMerchantSingle;
        $name = $request->input('name');
        $duration_period = $request->input('sequence');
        if (!empty($update_lang_pro)) {
            $model_data->sequence = $duration_period; // duration period
            $model_data->save();
            $update_lang_pro['name'] = $name;
            $update_lang_pro->save();
        } else {
            $model_data->merchant_id = $merchant_id;
            $model_data->status = 1;
            $model_data->sequence = $duration_period; // duration period
            $model_data->save();
            $language_data = new LangName([
                'merchant_id' => $merchant_id,
                'locale' => \App::getLocale(),
                'dependable_id' => $model_data->id,
                'name' => $name,
            ]);
            $model_data->LangPackageDurations()->save($language_data);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id)
//    {
//        $edit = PackageDuration::where([['status',true]])->findorfail($id);
//        return view('merchant.packageduration.edit', compact('edit'));
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
//    public function update(Request $request, $id)
//    {
//        $update = PackageDuration::where([['status',true]])->findorfail($id);
//        $lang_data = $request->only(['name']);
//        $this->saveLangDurations(collect($lang_data), $update);
//        request()->session()->flash('message', trans('admin.duration_name_updated'));
//        return redirect()->route('duration.index');
//    }
}
