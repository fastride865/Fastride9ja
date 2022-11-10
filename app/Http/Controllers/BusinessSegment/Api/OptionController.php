<?php

namespace App\Http\Controllers\BusinessSegment\Api;
use App\Models\BusinessSegment\LanguageOption;
use Illuminate\Validation\Rule;
use validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusinessSegment\Option;
use App\Traits\ApiResponseTrait;
use App\Models\OptionType;
use DB;
use App;
use App\Traits\MerchantTrait;

class OptionController extends Controller
{
    use ApiResponseTrait,MerchantTrait;
	public function getOptionTypes(Request $request){
        $merchant_id = $request->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $option_type = new App\Http\Controllers\Merchant\OptionTypeController();
        $data=OptionType::where('merchant_id', '=', $merchant_id)
            ->where('delete',NULL)
            ->where('status',1)
            ->get();
        $return = [];
        foreach ($data as $key=>$type)
        {
            $return[$key]['type'] = $type->Type($merchant_id);
            $return[$key]['id']=$type->id;
        }
        $response =[
            'response_data'=>$return
        ];
        return $this->successResponse(trans("$string_file.success"),$response);
    }
	
	public function addOption(Request $request){
		$bs = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        $bs_id = $bs->id;
        $locale = \App::getLocale();
        $id=$request->id;

		$validator = Validator::make($request->all(), [
            'name' => 'required',
        	'option_type_id'=>'required',
        	'status'=>'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $merchant_id = $request->merchant_id;
        $option_name = DB::table('language_options as lot')->where(function ($query) use ($bs_id,$locale,$id,$request) {
            return $query->where([['lot.business_segment_id', '=', $bs_id], ['lot.locale', '=', $locale], ['lot.name', '=', $request->name]])
                ->where('lot.option_id','!=',$id);
        })->join("options as ot","lot.option_id","=","ot.id")
            ->where('ot.id','!=',$id)
            ->where('ot.delete',NULL)->first();

        if (!empty($option_name->id)) {
            $message=trans("$string_file.option_name_already_exist");
            return $this->failedResponse($message);
        }

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors);
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
           	return $this->failedResponse($message);
        }
        // Commit Transaction
        DB::commit();
        return $this->successResponse(trans("$string_file.success"),$option);
	}

	public function getOptions(Request $request){
		$bs = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        $merchant_id = $request->merchant_id;
        // $arr_options = Option::with('BusinessSegment')->where('business_segment_id',  $bs->id)
        //     ->where('delete',NULL)
        //     ->get();
        $arr_options = Option::where('business_segment_id',  $bs->id)
            ->where('delete',NULL)
            ->paginate(25);
        $data=[];
        foreach($arr_options as $option){
            $data[] = [
                'id'=>$option->id,
                'option_name'=> $option->Name($option->business_segment_id),
                'option_type'=> $option->OptionType->Type($merchant_id),
                'option_type_id'=>$option->option_type_id,
                'status'=> $option->status,
            ];
        }
        $options = $arr_options->toArray();
        $next_page_url = isset($options['next_page_url']) && !empty($options['next_page_url']) ? $options['next_page_url'] : "";
        $current_page = isset($options['current_page']) && !empty($options['current_page']) ? $options['current_page'] : 0;

        $response =[
            'current_page'=>$current_page,
            'next_page_url'=>$next_page_url,
            'response_data'=>$data
        ];
        return $this->successResponse(trans("$string_file.success"),$response);
	}

	public function deleteOption(Request $request){
		$validator = Validator::make($request->all(), [
            'option_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

		$option = Option::Find($request->option_id);
        $bs = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        if (!empty($option->id)) {
            $option->delete = 1;
            $option->save();
        }
        return $this->successResponse(trans("$string_file.deleted"));
	} 
	
	public function saveLanguageData($request,$bs_id,$option)
    {
        LanguageOption::updateOrCreate([
            'business_segment_id' => $bs_id, 'locale' => App::getLocale(), 'option_id' => $option->id
        ], [
            'name' => $request->name,
        ]);
    }	
}
?>