<?php

namespace App\Http\Controllers\BusinessSegment;
use App\Models\BusinessSegment\LanguageOption;
use Illuminate\Validation\Rule;
use validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusinessSegment\Option;
use DB;
use App;
use App\Traits\MerchantTrait;

class OptionController extends Controller
{
    use MerchantTrait;
    public function index()
    {
        $bs = get_business_segment(false);
        $merchant_id = $bs->merchant_id;
        $option['data'] = Option::with('BusinessSegment')->where('business_segment_id',  $bs->id)
            ->where('delete',NULL)
            ->get();
        $option['merchant_id'] = $merchant_id;
        return view('business-segment.option.index')->with($option);
    }

    public function add(Request $request, $id = NULL)
    {
        $option = NULL;
        $is_demo = false;
        $business_segment = get_business_segment(false);
        $string_file = $this->getStringFile(NULL,$business_segment->Merchant);
        if (!empty($id)) {
            $option = Option::Find($id);
            if (empty($option->id)) {
                return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
            }
//            $is_demo = $business_segment->Merchant->demo == 1 ? true : false;
        }
        $option_type = new App\Http\Controllers\Merchant\OptionTypeController();

        $data['data'] = [
            'save_url' => route('business-segment.option.save', $id),
            'option' => $option,
            'arr_option_type' => $option_type->optionType($business_segment->merchant_id),
            'status' => get_active_status("web",$string_file),
        ];
        $data['is_demo'] = $is_demo;
        return view('business-segment.option.form')->with($data);
    }
    public function save(Request $request, $id = NULL)
    {
        $bs = get_business_segment(false);
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        $bs_id = $bs->id;
        $locale = \App::getLocale();
        $validator = Validator::make($request->all(), [
//        'name'=>'required',
            'name' => ['required',
//                Rule::unique('language_options', 'name')->where(function ($query) use ($locale,$bs_id,$id) {
//                    return $query->where([['business_segment_id', '=', $bs_id], ['locale', '=', $locale]])
//                        ->where('option_id','!=',$id)
//                        ;
//                })
            ],
        'option_type_id'=>'required',
//        'sequence'=>'required',
        'status'=>'required',
        ]);

        $option_name = DB::table('language_options as lot')->where(function ($query) use ($bs_id,$locale,$id,$request) {
            return $query->where([['lot.business_segment_id', '=', $bs_id], ['lot.locale', '=', $locale], ['lot.name', '=', $request->name]])
                ->where('lot.option_id','!=',$id);
        })->join("options as ot","lot.option_id","=","ot.id")
            ->where('ot.id','!=',$id)
            ->where('ot.delete',NULL)->first();

        if (!empty($option_name->id)) {

            return redirect()->back()->withInput($request->input())->withErrors(trans("$string_file.option_name_already_exist"));
        }

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        // Begin Transaction
        DB::beginTransaction();
        
        try {
            if (!empty($id)) {
                $option = Option::Find($id);
            } else {
                $option = new Option;
                $option->business_segment_id = $bs_id;
            }

            $option->option_type_id = $request->option_type_id;
            $option->sequence = $request->sequence;
            $option->status = $request->status;

            $option->save();
            $this->saveLanguageData($request,$bs_id,$option);
        }

        catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            // Rollback Transaction
            return redirect()->route('business-segment.option.index')->withErrors($message);
        }
        // Commit Transaction
        DB::commit();
        return redirect()->route('business-segment.option.index')->with('success', trans("$string_file.added_successfully"));
    }

    public function saveLanguageData($request,$bs_id,$option)
    {
        LanguageOption::updateOrCreate([
            'business_segment_id' => $bs_id, 'locale' => App::getLocale(), 'option_id' => $option->id
        ], [
            'name' => $request->name,
        ]);
    }

    public function ChangeStatus($id, $status)
    {
        $bs = get_business_segment(false);
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
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
        $option = Option::find($id);
        $option->status = $status;
        $option->save();

        return redirect()->back()->with('success',trans("$string_file.status_updated"));
    }

    public function destroy($id)
    {
        $bs = get_business_segment(false);
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        $option = Option::Find($id);
        if (!empty($option->id)) {
            $option->delete = 1;
            $option->save();
        }
        return redirect()->back()->with('success',trans("$string_file.deleted"));
    }

}
