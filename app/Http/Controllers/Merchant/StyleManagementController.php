<?php

namespace App\Http\Controllers\Merchant;
use App\Models\InfoSetting;
use App\Models\LangName;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StyleManagement;
use Illuminate\Validation\Rule;
use validator;
use DB;
use App\Traits\MerchantTrait;

class StyleManagementController extends Controller
{
    use MerchantTrait;
    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','STYLE_MANAGEMENT')->first();
        view()->share('info_setting', $info_setting);
    }

    public  function index(){
        $checkPermission = check_permission(1, 'FOOD');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $style_management['data'] = StyleManagement::where('delete','=',NULL)->where('merchant_id',$merchant_id)->paginate(15);
        return view('merchant.style-management.index')->with($style_management);
    }
    public function add(Request $request, $id = NULL)
    {
        $checkPermission = check_permission(1, 'FOOD');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        /*declaration part*/
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $title_pre = trans("$string_file.add");
        $style_management = NULL;
        $save_url = route('merchant.style-management.save');
        if(!empty($id))
        {
            $style_management = StyleManagement::Find($id);
            if(empty($style_management->id))
            {
                return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
            }
            $title_pre = trans("$string_file.edit");
            $save_url = route('merchant.style-management.save',$id);
        }
        $title = $title_pre.' '. trans("$string_file.style");
        $data['data']= [
            'title'=>$title,
            'save_url'=>$save_url,
            'style_management'=> $style_management,
            'arr_status'=>get_active_status("web",$string_file),
        ];
        $data['is_demo'] = $merchant->demo == 1 ? true : false;
        return view('merchant.style-management.form')->with($data);
    }

    /*Save or Update*/
    public function save(Request $request, $id = NULL)
    {
        $checkPermission = check_permission(1,'FOOD');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $locale = \App::getLocale();
        $validator = Validator::make($request->all(), [
//            'style_name' => 'required|unique:style_managements,style_name,'.$id.',id,merchant_id,'.$merchant_id,
            'status' => 'required',
            'style_name' => 'required',
//            'style_name' => ['required',
//                Rule::unique('lang_names', 'name')->where(function ($query) use ($merchant_id,$locale,$id) {
//                    return $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', $locale]])
//                        ->where('dependable_id','!=',$id);
//                })
//            ],
        ]);
        $style_name = DB::table('lang_names')->where(function ($query) use ($merchant_id,$locale,$id,$request) {
            return $query->where([['lang_names.merchant_id', '=', $merchant_id], ['lang_names.locale', '=', $locale], ['lang_names.name', '=', $request->category_name]])
                ->where('lang_names.dependable_id','!=',$id);
        })->join("style_managements","lang_names.dependable_id","=","style_managements.id")
            ->where('style_managements.id','!=',$id)
            ->where('style_managements.merchant_id','=',$merchant_id)
            ->where('style_managements.delete',NULL)->first();

        if (!empty($style_name->id)) {

            return redirect()->back()->withErrors(trans("$string_file.style_name_already_exist"));
        }

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        DB::beginTransaction();

        try
        {
            if(!empty($id))
            {
                $style_management = StyleManagement::Find($id);
            }
            else
            {
                $style_management = new StyleManagement();
            }

//            $style_management->style_name = $request->style_name;
            $style_management->merchant_id = $merchant_id;
            $style_management->status = $request->status;
            $style_management->save();
            // sync language of style
            $style_locale =  $style_management->LangStyleSingle;
            if(!empty($style_locale->id))
            {
                $style_locale->name = $request->style_name;
                $style_locale->save();
            }
            else
            {
                $language_data = new LangName([
                    'merchant_id' => $style_management->merchant_id,
                    'locale' => $locale,
                    'name' => $request->style_name]);

                $style_management->LangStyle()->save($language_data);
//
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        // Commit Transaction
        DB::commit();
        return redirect()->route('merchant.style-management')->withSuccess(trans("$string_file.added_successfully"));
    }

    public  function destroy(Request $request ){
        $id = $request->id;
        $delete = StyleManagement::Find($id);
        $delete->delete = 1;
        $delete->save();
    }
}
