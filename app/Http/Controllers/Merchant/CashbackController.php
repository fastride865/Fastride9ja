<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Support\Collection;
use App\Models\Cashback;
use App\Models\LangCashback;
use Auth;
use App\Models\ApplicationConfiguration;
use App\Models\Configuration;
use App\Models\CountryArea;
use App\Models\ExtraCharge;
use App\Models\Merchant;
use App\Traits\AreaTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CashbackController extends Controller
{
    use AreaTrait;

    public function index()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $merchant = Merchant::find($merchant_id);
        $cashbacks = Cashback::where([['merchant_id',$merchant_id],['admin_delete',0]])->paginate(10);
        return view('merchant.cashback.index', compact('cashbacks', 'merchant'));
    }

    public function create()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $merchant = Merchant::find($merchant_id);
        $area = $this->getAreaList(false);
        $areas = $area->get();
        return view('merchant.cashback.create', compact('areas', 'merchant'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    protected function validator(array $data)
    {
        $merchant = Auth::user('merchant');
        $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
         return Validator::make($data, [
            'area' => ['required','integer','exists:country_areas,id',
                /*Rule::unique('cashbacks','country_area_id')->where(function ($query) use($merchant_id){
                    return $query->where([['merchant_id',$merchant_id]]);
                })*/
            ],
            'services.*' => 'required|exists:service_types,id',
            'bill_amount' => 'required',
            'Normal.*' => 'exists:vehicle_types,id',
            'Rental.*' => 'exists:vehicle_types,id',
            'Transfer.*' => 'exists:vehicle_types,id',
            'Outstation.*' => 'exists:vehicle_types,id',
            'Pool.*' => 'exists:vehicle_types,id',
            'user_cashback_enable_checkbox' => 'required_without:driver_cashback_enable_checkbox',
            'driver_cashback_enable_checkbox' => 'required_without:user_cashback_enable_checkbox',
            'user_cashback_from' => 'required_if:user_cashback_enable_checkbox,1',
            'user_cashback_text' => 'required_if:user_cashback_enable_checkbox,1',
            'user_cashback_upto' => 'sometimes|required_without:user_cashback_max',
            'user_cashback_max' => 'sometimes|required_without:user_cashback_upto',
            'driver_cashback_from' => 'required_if:driver_cashback_enable_checkbox,1',
            'driver_cashback_text' => 'required_if:driver_cashback_enable_checkbox,1',
            'driver_cashback_upto' => 'sometimes|required_without:driver_cashback_max',
            'driver_cashback_max' => 'sometimes|required_without:driver_cashback_upto',
            'tax_number' => 'required_with:tax',
        ], [
             'user_cashback_enable_checkbox.required_without' => trans('admin.at_least_user_driver_cashback'),
             'driver_cashback_enable_checkbox.required_without' => trans('admin.at_least_user_driver_cashback'),
        ]);
    }

    public function store(Request $request)
    {
        $merchant = Auth::user('merchant');
        $merchant_id = $merchant->parent_id != 0 ? $merchant->parent_id : $merchant->id;
        $data = $request->except('_token', '_method');
        $this->validator($data)->validate();

        DB::beginTransaction();
        try {
            $updateOrcreate = Cashback::updateOrCreate(
                ['country_area_id' => $data['area'], 'merchant_id' => $merchant_id],
                [   'min_bill_amount' => $request->bill_amount,
                    'status' => 1,
                    'admin_delete' => 0,
                    'users_cashback_enable' => $request->user_cashback_enable_checkbox,
                    'drivers_cashback_enable' => $request->driver_cashback_enable_checkbox,
                    'users_percentage' => $request->user_cashback_from,
                    'users_upto_amount' => $request->user_cashback_upto,
                    'users_max' => $request->user_cashback_max,
                    'drivers_percentage' => $request->driver_cashback_from,
                    'drivers_upto_amount' => $request->driver_cashback_upto,
                    'drivers_max' => $request->driver_cashback_max,
                ]
            );
            $updateOrcreate->CashBackVehicles()->detach();
            if ($request->has('Normal')) {
                $attach_data = $updateOrcreate->CashBackVehicles()->attach($request->input('Normal'),['service_type_id'=>1]);
            }
            if ($request->has('Rental')) {
                $attach_data = $updateOrcreate->CashBackVehicles()->attach($request->input('Rental'),['service_type_id'=>2]);
            }
            if ($request->has('Transfer')) {
                $attach_data = $updateOrcreate->CashBackVehicles()->attach($request->input('Transfer'),['service_type_id'=>3]);
            }
            if ($request->has('Outstation')) {
                $attach_data = $updateOrcreate->CashBackVehicles()->attach($request->input('Outstation'),['service_type_id'=>4]);
            }
            if ($request->has('Pool')) {
                $attach_data = $updateOrcreate->CashBackVehicles()->attach($request->input('Pool'),['service_type_id'=>5]);
            }

            if ($request->has('user_cashback_text')) {
                $update_lang_data_user = $request->only(['user_cashback_text']);
                $this->saveLangCashbackUsers(collect($update_lang_data_user), $updateOrcreate);
            }

            if ($request->has('driver_cashback_text')) {
                $update_lang_data_driver = $request->only(['driver_cashback_text']);
                $this->saveLangCashbackDrivers(collect($update_lang_data_driver), $updateOrcreate);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();

        $request->session()->flash('message', trans('admin.cashback_saved'));
        return redirect()->route('cashback.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $edit = Cashback::where([['admin_delete',0],['merchant_id',$merchant_id]])->FindorFail($id);
        $areaServices = $edit->CountryArea->ServiceTypes->groupBy('id')->toArray();
        $selected_services = $edit->CashBackServices->pluck('pivot.service_type_id')->toArray();
        $area_vehicles = $edit->CountryArea->VehicleType->sortBy('pivot.service_type_id')->groupBy('pivot.service_type_id');
        $selected_vehicles = $edit->CashBackVehicles->sortBy('pivot.service_type_id')->groupBy('pivot.service_type_id');
        /*echo"<pre>";
        print_r($areaServices->toArray());
        echo"<pre>";
        print_r($selected_services);*/
        /*echo"<pre>";
        print_r($area_vehicles->toArray());
        echo"<pre>";
        print_r($selected_vehicles);
        die();*/
        return view('merchant.cashback.edit', compact('edit','areaServices','selected_services','area_vehicles','selected_vehicles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'area' => ['required','integer','exists:country_areas,id',
                /*Rule::unique('cashbacks','country_area_id')->where(function ($query) use($merchant_id){
                    return $query->where([['merchant_id',$merchant_id]]);
                })*/
            ],
            //'services.*' => 'required|exists:service_types,id',
            'bill_amount' => 'required',
            'Normal.*' => 'exists:vehicle_types,id',
            'Rental.*' => 'exists:vehicle_types,id',
            'Transfer.*' => 'exists:vehicle_types,id',
            'Outstation.*' => 'exists:vehicle_types,id',
            'Pool.*' => 'exists:vehicle_types,id',
            'user_cashback_enable_checkbox' => 'required_without:driver_cashback_enable_checkbox',
            'driver_cashback_enable_checkbox' => 'required_without:user_cashback_enable_checkbox',
            'user_cashback_from' => 'required_if:user_cashback_enable_checkbox,1',
            'user_cashback_text' => 'required_if:user_cashback_enable_checkbox,1',
            'user_cashback_upto' => 'sometimes|required_without:user_cashback_max',
            'user_cashback_max' => 'sometimes|required_without:user_cashback_upto',
            'driver_cashback_from' => 'required_if:driver_cashback_enable_checkbox,1',
            'driver_cashback_text' => 'required_if:driver_cashback_enable_checkbox,1',
            'driver_cashback_upto' => 'sometimes|required_without:driver_cashback_max',
            'driver_cashback_max' => 'sometimes|required_without:driver_cashback_upto',
        ], [
            'user_cashback_enable_checkbox.required_without' => trans('admin.at_least_user_driver_cashback'),
            'driver_cashback_enable_checkbox.required_without' => trans('admin.at_least_user_driver_cashback'),
        ])->validate();

        $data = $request->except('_token', '_method');

        DB::beginTransaction();
                try {
                    $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
                    $updateOrcreate = Cashback::updateOrCreate(
                        ['country_area_id' => $data['area'], 'merchant_id' => $merchant_id],
                        [   'min_bill_amount' => $request->bill_amount,
                            'users_cashback_enable' => $request->user_cashback_enable_checkbox,
                            'drivers_cashback_enable' => $request->driver_cashback_enable_checkbox,
                            'users_percentage' => $request->user_cashback_from,
                            'users_upto_amount' => $request->user_cashback_upto,
                            'users_max' => $request->user_cashback_max,
                            'drivers_percentage' => $request->driver_cashback_from,
                            'drivers_upto_amount' => $request->driver_cashback_upto,
                            'drivers_max' => $request->driver_cashback_max,
                        ]
                    );
                    $updateOrcreate->CashBackVehicles()->detach();
                    if ($request->has('Normal')) {
                        $attach_data = $updateOrcreate->CashBackVehicles()->attach($request->input('Normal'),['service_type_id'=>1]);
                    }
                    if ($request->has('Rental')) {
                        $attach_data = $updateOrcreate->CashBackVehicles()->attach($request->input('Rental'),['service_type_id'=>2]);
                    }
                    if ($request->has('Transfer')) {
                        $attach_data = $updateOrcreate->CashBackVehicles()->attach($request->input('Transfer'),['service_type_id'=>3]);
                    }
                    if ($request->has('Outstation')) {
                        $attach_data = $updateOrcreate->CashBackVehicles()->attach($request->input('Outstation'),['service_type_id'=>4]);
                    }
                    if ($request->has('Pool')) {
                        $attach_data = $updateOrcreate->CashBackVehicles()->attach($request->input('Pool'),['service_type_id'=>5]);
                    }
                    if ($request->has('user_cashback_text')) {
                        $update_lang_data_user = $request->only(['user_cashback_text']);
                        $this->saveLangCashbackUsers(collect($update_lang_data_user), $updateOrcreate);
                    }

                    if ($request->has('driver_cashback_text')) {
                        $update_lang_data_driver = $request->only(['driver_cashback_text']);
                        $this->saveLangCashbackDrivers(collect($update_lang_data_driver), $updateOrcreate);
                    }

                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    p($message);
                    // Rollback Transaction
                    DB::rollback();
                }
                DB::commit();


        $request->session()->flash('message', trans('admin.cashback_updated'));
        return redirect()->route('cashback.index');
    }

    private function saveLangCashbackUsers(Collection $collection, Cashback $model_data)
    {
        $collect_lang_data = $collection->toArray();
        $update_lang_pro = LangCashback::where([['cashback_id','=',$model_data->id],['locale', '=', \App::getLocale()],['type',1]])->first();
        if(!empty($update_lang_pro)){
            //print_r($update_lang_pro->toArray());
            $update_lang_pro['app_message'] = $collect_lang_data['user_cashback_text'];
            $update_lang_pro->save();
        }else{
            $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
            $language_pro = new LangCashback([
                'merchant_id' => $merchant_id,
                'locale' => \App::getLocale(),
                'app_message' => $collect_lang_data['user_cashback_text'],
                'type' => 1,
            ]);

            $model_data->LangCashbacks()->save($language_pro);
        }

    }

    private function saveLangCashbackDrivers(Collection $collection, Cashback $model_data)
    {
        $collect_lang_data = $collection->toArray();
        $update_lang_pro = LangCashback::where([['cashback_id','=',$model_data->id],['locale', '=', \App::getLocale()],['type',2]])->first();
        if(!empty($update_lang_pro)){
            //print_r($update_lang_pro->toArray());
            $update_lang_pro['app_message'] = $collect_lang_data['driver_cashback_text'];
            $update_lang_pro->save();
        }else{
            $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
            $language_pro = new LangCashback([
                'merchant_id' => $merchant_id,
                'locale' => \App::getLocale(),
                'app_message' => $collect_lang_data['driver_cashback_text'],
                'type' => 2,
            ]);
            $model_data->LangCashbacks()->save($language_pro);
        }

    }

    public function Change_Status(Request $request, $id = null , $status = null)
    {
        $request->request->add(['status'=>$status,'id'=>$id]);
        Validator::make($request->all(),[
            'id'=>'required|exists:cashbacks,id',
            'status' => 'integer|required|between:0,1'
        ],[
            'status.between' => trans('admin.invalid_status'),
            'id.exists' => trans('admin.cashback_addederror'),
        ])->validate();
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $change = Cashback::where([['admin_delete',0],['merchant_id',$merchant_id]])->FindorFail($id);
        $change->status = $status;
        $change->save();
        if ($status == 1)
        {
            request()->session()->flash('message', trans('admin.cashback_activated'));
        } else {
            request()->session()->flash('error', trans('admin.cashback_deactivated'));
        }
        return redirect()->route('cashback.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $delete = Cashback::where([['merchant_id',$merchant_id],['admin_delete',0]])->findorfail($id);
        $delete->status = 0;
        $delete->admin_delete = 1;
        $delete->save();
        request()->session()->flash('error', trans('admin.cashback_deleted'));
        echo trans('admin.cashback_deleted');
        //return redirect()->route('subscription.index');
    }
}
