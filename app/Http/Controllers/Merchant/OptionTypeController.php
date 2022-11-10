<?php

namespace App\Http\Controllers\Merchant;
use App\Models\InfoSetting;
use App\Models\LanguageOptionType;
use Illuminate\Validation\Rule;
use validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OptionType;
use DB;
use App;
use App\Traits\MerchantTrait;
class OptionTypeController extends Controller
{
    use MerchantTrait;
    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','OPTION_TYPE')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $checkPermission = check_permission(1, 'FOOD');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $option['data'] = OptionType::where('merchant_id', '=', $merchant_id)
            ->where('delete',NULL)
            ->get();
        return view('merchant.option-type.index')->with($option);

    }

    public function add(Request $request, $id = NULL)
    {
        $checkPermission = check_permission(1, 'FOOD');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
       $merchant = get_merchant_id(false);
        $is_demo = false;
        $string_file = $this->getStringFile(NULL,$merchant);
        $option = NULL;
        if (!empty($id)) {
            $option = OptionType::Find($id);
            if (empty($option->id)) {
                return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
            }
            $is_demo = $merchant->demo == 1 ? true : false;
        }
        $data['data'] = [
            'save_url' =>  route('merchant.option-type.save', $id),
            'option' => $option,
            'status' => get_active_status("web",$string_file),
            'charges_type' => get_free_paid($string_file),
            'select_type' => get_optional_mandatory($string_file),
        ];
        $data['is_demo'] = $is_demo;
        return view('merchant.option-type.form')->with($data);
    }


    public function save(Request $request, $id = NULL)
    {
        $checkPermission = check_permission(1, 'FOOD');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $locale = \App::getLocale();
        $validator = Validator::make($request->all(), [
//        'type'=>'required',
//        'sequence'=>'required',
        'status'=>'required',
        'charges_type'=>'required',
        'select_type'=>'required',
        'max_option_on_app'=>'required',
        'type' => ['required',
//                Rule::unique('language_option_types', 'type')->where(function ($query) use ($merchant_id,$locale,$id) {
//                    return $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', $locale]])
//                        ->where('option_type_id','!=',$id);
//                })
            ],
        ]);

        $option_type_name = DB::table('language_option_types as lot')->where(function ($query) use ($merchant_id,$locale,$id,$request) {
            return $query->where([['lot.merchant_id', '=', $merchant_id], ['lot.locale', '=', $locale], ['lot.type', '=', $request->type]])
                ->where('lot.option_type_id','!=',$id);
        })->join("option_types as ot","lot.option_type_id","=","ot.id")
            ->where('ot.id','!=',$id)
            ->where('ot.merchant_id','=',$merchant_id)
            ->where('ot.delete',NULL)->first();

        if (!empty($option_type_name->id)) {

            return redirect()->back()->withInput($request->input())->withErrors(trans("$string_file.option_type_already_exist"));
        }

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        // Begin Transaction
        DB::beginTransaction();

        try {
            if (!empty($id)) {
                $option = OptionType::Find($id);
            } else {
                $option = new OptionType;
                $option->merchant_id = $merchant_id;
            }
            $option->status = $request->status;
            $option->sequence = $request->sequence;
            $option->charges_type = $request->charges_type;
            $option->select_type = $request->select_type;
            $option->max_option_on_app = $request->max_option_on_app;
//            p($option);
            $option->save();
            $this->saveLanguageData($request,$merchant_id,$option);
        }

        catch (\Exception $e) {
            $message = $e->getMessage();
//            p($message);
            DB::rollback();
            // Rollback Transaction
            return redirect()->route('merchant.option-type.index')->withErrors($message);
        }
        // Commit Transaction
        DB::commit();
        return redirect()->route('merchant.option-type.index')->withSuccess( trans("$string_file.added_successfully"));
    }

    public function saveLanguageData($request,$merchant_id,$option)
    {
        LanguageOptionType::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'option_type_id' => $option->id
        ], [
            'type' => $request->type,
        ]);
    }
    public function ChangeStatus($id, $status)
    {
        $validator = Validator::make(
            [
                'id'=>$id,
                'status'=>$status,
            ],
            [
                'id' => ['required'],
                'status' => ['required', 'integer', 'between:1,2'],
            ]);
        if($validator->fails()) {
            return redirect()->back();
        }
        $option = OptionType::find($id);
        $option->status = $status;
        $option->save();
        $string_file = $this->getStringFile(NULL,$option->Merchant);
        return redirect()->back()->withSuccess(trans("$string_file.status_updated"));
    }

    public function destroy($id)
    {
        $option = OptionType::Find($id);
        if (!empty($option->id)) {
            $option->delete = 1;
            $option->save();
        }
        $string_file = $this->getStringFile(NULL,$option->Merchant);
        return redirect()->back()->withSuccess(trans("$string_file.deleted"));
    }

    public function optionType($merchant_id)
    {
        $arr_type = OptionType::where('merchant_id', '=', $merchant_id)
            ->where('delete',NULL)
            ->where('status',1)
            ->get();
        $return = [];
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        foreach ($arr_type as $type)
        {
            $return[$type->id] = $type->Type($merchant_id);
        }
        return add_blank_option($return,trans("$string_file.select"));
    }
}
