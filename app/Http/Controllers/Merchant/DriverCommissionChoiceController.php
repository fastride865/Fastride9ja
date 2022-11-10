<?php

namespace App\Http\Controllers\Merchant;

use App\Models\DriverCommissionChoice;
use App\Models\InfoSetting;
use App\Models\LangName;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class DriverCommissionChoiceController extends Controller
{
    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'DRIVER_COMMISSION_CHOICE')->first();
        view()->share('info_setting', $info_setting);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $commission_options = DriverCommissionChoice::where([['status',true]])->paginate(10);
        return view('merchant.drivercommissionchoices.index', compact('commission_options'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = DriverCommissionChoice::where([['status',true]])->findorfail($id);
        return view('merchant.drivercommissionchoices.edit', compact('edit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $update = DriverCommissionChoice::where([['status',true]])->findorfail($id);
        $lang_data = $request->only(['name']);
        $this->saveLangCommissionChoices(collect($lang_data), $update);
        request()->session()->flash('message', trans('admin.driver_commission_choices_name_updated'));
        return redirect()->route('driver-commission-choices.index');
    }

    private function saveLangCommissionChoices(Collection $collection, DriverCommissionChoice $model_data)
    {
        $collect_lang_data = $collection->toArray();
        $update_lang_pro = $model_data->LangCommissionChoiceAccMerchantSingle;
        if(!empty($update_lang_pro)){
            $update_lang_pro['name'] = $collect_lang_data['name'];
            $update_lang_pro->save();
        }else{
            $language_data = new LangName([
                'merchant_id' => \Auth::user('merchant')->parent_id != 0 ? \Auth::user('merchant')->parent_id : \Auth::user('merchant')->id,
                'locale' => \App::getLocale(),
                'name' => $collect_lang_data['name'],
            ]);
            $saved_lang_data = $model_data->LangCommissionChoices()->save($language_data);
        }
    }

}
