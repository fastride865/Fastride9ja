<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Country;
use App\Models\LangName;
use App\Models\State;
use App\Models\Town;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TownController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*if (!Auth::user('merchant')->can('view_cities')) {
            abort(404, 'Unauthorized action.');
        }*/
        return redirect()->back();
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $cities = Town::where([['admin_delete',0], ['merchant_id', $merchant_id]])->latest()->paginate(25);
        return view('merchant.towns.index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /*if (!Auth::user('merchant')->can('create_towns')) {
            abort(404, 'Unauthorized action.');
        }*/
        return redirect()->back();
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $states = State::where([['merchant_id', $merchant_id],['admin_delete',0]])->latest()->get();
        return view('merchant.towns.create', compact('states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back();
        Validator::make($request->all(),[
            'name'=>'required',
            'city_description'=> 'nullable|max:200',
            'state_id' => 'required|exists:states,id',
            'status' => 'integer|required|between:0,1'
        ],[
            'status.between' => trans('admin.invalid_status'),
            'state_id.exists' => trans('admin.invalid_state_id'),
        ])->validate();

        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->request->add(['merchant_id'=>$merchant_id]);
        $data = $request->except('_token','_method','name','city_description');
        $lang_data = $request->only(['name', 'city_description']);
        $store = new Town($data);
        if($store->save()):
            $this->saveLangTowns(collect($lang_data), $store);
            request()->session()->flash('message', trans('admin.town_added'));
            return redirect()->route('cities.index');
        else:
            request()->session()->flash('error', trans('admin.town_addederror'));
            return redirect()->route('cities.index');
        endif;
    }

    private function saveLangTowns(Collection $collection, Town $model_data)
    {
        $collect_lang_data = $collection->toArray();
        $update_lang_pro = $model_data->LangTownSingle;
        if(!empty($update_lang_pro)){
            $update_lang_pro['name'] = $collect_lang_data['name'];
            $update_lang_pro['field_one'] = $collect_lang_data['city_description'];
            $update_lang_pro->save();
        }else{
            $language_data = new LangName([
                'merchant_id' => $model_data->merchant_id,
                'locale' => \App::getLocale(),
                'name' => $collect_lang_data['name'],
                'field_one' => $collect_lang_data['city_description'],
            ]);
            $saved_lang_data = $model_data->LangTowns()->save($language_data);
        }
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
        return redirect()->back();
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $edit = Town::where([['admin_delete',0],['merchant_id',$merchant_id]])->FindorFail($id);
        $states = State::where([['merchant_id', $merchant_id],['admin_delete',0]])->latest()->get();
        return view('merchant.towns.edit', compact('edit','states'));

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
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->request->add(['id'=>$id]);
        Validator::make($request->all(),[
            'id'=>['required',
                Rule::exists('towns', 'id')->where(function ($query) use($merchant_id){
                    $query->where([['admin_delete',0]]);
                })],
            'name'=>'required',
            'city_description'=> 'nullable|max:200',
            'state_id' => 'required|exists:states,id',
            'status' => 'integer|required|between:0,1'
        ],[
            'status.between' => trans('admin.invalid_status'),
            'id.exists' => trans('admin.invalid_town_id'),
            'state_id.exists' => trans('admin.invalid_state_id'),
        ])->validate();

        $update = Town::where([['merchant_id',$merchant_id],['admin_delete',0]])->findorfail($id);
        $data = $request->except('_token','_method','id','name','city_description');
        $update->state_id = $data['state_id'];
        $update->status = $data['status'];
        $lang_data = $request->only(['name', 'city_description']);
        if($update->save()):
            $this->saveLangTowns(collect($lang_data), $update);
            request()->session()->flash('message', trans('admin.town_updated'));
            return redirect()->route('cities.index');
        else:
            request()->session()->flash('error', trans('admin.town_addederror'));
            return redirect()->route('cities.index');
        endif;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function Change_Status(Request $request, $id = null , $status = null)
    {
        return redirect()->back();
        $request->request->add(['status'=>$status,'id'=>$id]);
        Validator::make($request->all(),[
            'id'=>'required|exists:towns,id',
            'status' => 'integer|required|between:0,1'
        ],[
            'status.between' => trans('admin.invalid_status'),
            'id.exists' => trans('admin.invalid_town_id'),
        ])->validate();
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $change = Town::where([['admin_delete',0],['merchant_id',$merchant_id]])->FindorFail($id);
        $change->status = $status;
        $change->save();
        if ($status == 1)
        {
            request()->session()->flash('message', trans('admin.town_activated'));
        } else {
            request()->session()->flash('error', trans('admin.town_deactivated'));
        }
        return redirect()->route('cities.index');
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
        $delete = Town::where([['merchant_id',$merchant_id],['admin_delete',0]])->findorfail($id);
        $delete->status = 0;
        $delete->admin_delete = 1;
        $delete->save();
        request()->session()->flash('error', trans('admin.town_deleted'));
        echo trans('admin.town_deleted');
        //return redirect()->route('subscription.index');
    }
}
