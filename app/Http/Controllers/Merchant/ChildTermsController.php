<?php

namespace App\Http\Controllers\Merchant;

use App\Models\ChildTerm;
use App\Models\Country;
use App\Models\LangName;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ChildTermsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*if (!Auth::user('merchant')->can('view_child_terms')) {
            abort(404, 'Unauthorized action.');
        }*/
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $child_termpages = ChildTerm::where([['merchant_id', '=', $merchant_id],['slug','child_terms']])->latest()->paginate(5);
        return view('merchant.child_terms.index', compact('child_termpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /*if (!\Auth::user('merchant')->can('create_child_terms')) {
            abort(404, 'Unauthorized action.');
        }*/
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $countries = Country::where([['country_status', '=', 1], ['merchant_id', '=', $merchant_id]])->get();
        return view('merchant.child_terms.create',compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->request->add(['application' => 1]);
        Validator::make($request->all(),[
            'title'=>'required',
            'application' => 'required|integer|between:1,2',
            'description'=> 'required|max:200',
            'country' => ['required',
                    Rule::exists('countries','id')->where('merchant_id',$merchant_id),
                    Rule::unique('child_terms','country_id')->where('merchant_id',$merchant_id),
                ],
        ],[
            'country.unique' => trans('admin.data_already_added'),
        ])->validate();
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $lang_data = $request->only(['title', 'description']);
        $store = ChildTerm::updateOrCreate(
            ['merchant_id' => $merchant_id, 'country_id' => $request->country, 'application' => $request->application, 'slug' => 'child_terms'],
            ['status' => 1]
        );
        $this->SaveLanguageChildTerms(collect($lang_data), $store);
        request()->session()->flash('message', trans('admin.child_terms_added'));
        return redirect()->route('child-terms-conditions.index')->withSuccess (trans("$string_file.saved_successfully"));
    }

    public function SaveLanguageChildTerms(Collection $collection, ChildTerm $model_data)
    {
        $collect_lang_data = $collection->toArray();
        $update_lang_pro = $model_data->LangTermsConditionSingle;
        if(!empty($update_lang_pro)){
            $update_lang_pro['name'] = $collect_lang_data['title'];
            $update_lang_pro['field_three'] = $collect_lang_data['description'];
            $update_lang_pro->save();
        }else{
            $language_data = new LangName([
                'merchant_id' => $model_data->merchant_id,
                'locale' => \App::getLocale(),
                'name' => $collect_lang_data['title'],
                'field_three' => $collect_lang_data['description'],
            ]);
            $saved_lang_data = $model_data->LangTermsConditions()->save($language_data);
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
        /*if(!Auth::user('merchant')->can('edit_child_terms')) {
            abort(404, 'Unauthorized action.');
        }*/
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $page_data = ChildTerm::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
        return view('merchant.child_terms.edit', compact('page_data'));
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
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
        $lang_data = $request->only(['title', 'description']);
        $update = ChildTerm::where([['merchant_id', '=', $merchant_id]])->findorFail($id);
        $this->SaveLanguageChildTerms(collect($lang_data), $update);
        request()->session()->flash('message', trans('admin.child_terms_updated'));
        return redirect()->route('child-terms-conditions.index')->withSuccess (trans("$string_file.saved_successfully"));
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
