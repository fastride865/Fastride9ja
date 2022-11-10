<?php

namespace App\Http\Controllers\BusinessSegment;
use App\Models\BusinessSegment\LanguageOption;
use Illuminate\Validation\Rule;
use validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusinessSegment\BusinessSegmentConfigurations;
use DB;
use App;
use App\Traits\MerchantTrait;

class ConfigurationController extends Controller
{
    use MerchantTrait;
    public function index()
    {
        $bs = get_business_segment(false);
        $merchant_id = $bs->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $is_demo = $bs->Merchant->demo ==  1 ? true : false;
        $data['config'] = BusinessSegmentConfigurations::where('business_segment_id',  $bs->id)->first();
        $data['is_demo'] = $is_demo;
        $data['is_open'] = get_status(true,$string_file);
        return view('business-segment.configurations')->with($data);
    }

    public function save(Request $request){
        $bs = get_business_segment(false);
        // Begin Transaction
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        DB::beginTransaction();
        
        try {
            $config=BusinessSegmentConfigurations::where('business_segment_id',  $bs->id)->first();
            if(empty($config)){
                $config = new BusinessSegmentConfigurations;
                $config->business_segment_id=$bs->id;
            }
            $config->order_expire_time = $request->order_expire_time;
            $config->is_open=$request->is_open;
            $config->save();
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            p($message);
            // Rollback Transaction
            return redirect()->route('business-segment.configurations')->withErrors($message);
        }
        // Commit Transaction
        DB::commit();
        return redirect()->route('business-segment.configurations')->with('success', trans("$string_file.added_successfully"));
    }
}
