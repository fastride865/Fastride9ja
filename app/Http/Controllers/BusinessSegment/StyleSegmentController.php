<?php

namespace App\Http\Controllers\BusinessSegment;

use App\Models\StyleManagement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use validator;
use DB;
use App\Traits\MerchantTrait;


class StyleSegmentController extends Controller
{
    use MerchantTrait;
    public  function index(){
        $selected_style = [];
        $segment = get_business_segment(false);
        $arr_data['data'] = StyleManagement::with('BusinessSegment')->select('id','merchant_id')->where('delete','=',NULL)->where('merchant_id',$segment->merchant_id)->get();
        $arr_selected_style = DB::table('business_segment_style_management')->where('business_segment_id',$segment->id)->get();
        if(count($arr_selected_style) > 0)
        {
            $selected_style = array_pluck($arr_selected_style,'style_management_id');
        }
        $arr_data['selected_style'] = $selected_style;
        $arr_data['is_demo'] = false; //$segment->Merchant->demo == 1 ? true : false;
        return view('business-segment.style-segment.index')->with($arr_data);
    }

  public  function save(Request $request,$id=NULL){
        $validator = Validator::make($request->all(),[
         'arr_style' => 'required',
          ]);
      if($validator->fails()) {
      $errors = $validator->messages()->all();
      return redirect()->back()->withInput($request->input())->withErrors($errors);
  }
      $arr_style = $request->input('arr_style');
      $business_segment = get_business_segment(false);
      $string_file = $this->getStringFile(NULL,$business_segment->Merchant);
      $business_segment->save();
      $business_segment->StyleManagement()->sync($arr_style);
      return redirect()->route('business-segment.style-segment.index')->with('success', trans("$string_file.added_successfully"));
  }
}
