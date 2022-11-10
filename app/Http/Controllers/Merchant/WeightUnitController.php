<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WeightUnit;
use App\Models\WeightUnitTranslation;
use Illuminate\Validation\Rule;
use Auth;
use App;
use View;
use App\Traits\MerchantTrait;

class WeightUnitController extends Controller
{
    use MerchantTrait;
    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','WEIGHT_UNIT')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
        $all_segments = array_merge(['DELIVERY'],$all_food_grocery_clone);
        $checkPermission = check_permission(1, $all_segments, true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $weightunits = WeightUnit::where([['merchant_id',$merchant_id]])->get();
        return view('merchant.weightunit.index',compact('weightunits'));
    }


    public function add(Request $request, $id = NULL)
    {
        $merchant = get_merchant_id(false);
        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
        $all_segments = array_merge(['DELIVERY'],$all_food_grocery_clone);
        $checkPermission = check_permission(1, $all_segments, true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        try {
            $weightunit = NULL;
            $arr_selected_segment = [];
            if(!empty($id))
            {
                $weightunit = WeightUnit::find($id);
                $arr_selected_segment = array_pluck($weightunit->Segment,'id');
            }
            $arr_business = get_merchant_segment($with_taxi = true, null,$segment_group_id = 1);
            $arr_business = get_permission_segments(1, false, $arr_business);
            $segment_data['arr_segment'] = $arr_business;
            $segment_data['selected'] = $arr_selected_segment;
            $segment_html = View::make('segment')->with($segment_data)->render();
            $is_demo = $merchant->demo == 1 ? true : false;
            return view('merchant.weightunit.edit', compact('weightunit','segment_html','is_demo'));
        }catch(\Exception $e)
        {
            return redirect()->back()->withErrors($e->getMessage());
        }

    }

    public function save(Request $request, $id = NULL)
    {
        $locale = App::getLocale();
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $request->validate([
            'name' => ['required', 'max:255',
                Rule::unique('weight_unit_translations', 'name')->where(function ($query) use ($merchant_id, &$locale, &$id) {
                    $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', $locale], ['weight_unit_id', '!=', $id]]);
                })],
            'description' => 'required',
            'segment' => 'required',
        ]);

          $weight_unit =  WeightUnit::updateOrCreate([
                'id' => $id,
                'merchant_id' => $merchant_id,
                'status' => 1,
            ]);
        $id = $weight_unit->id;

        // sync segment
        $weight_unit->Segment()->sync($request->segment);

        $this->SaveLanguageWeightunit($merchant_id, $id, $request->name, $request->description);
        return redirect()->route('weightunit.index')->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function SaveLanguageWeightunit($merchant_id, $weight_unit_id, $name, $description)
    {
        App\Models\WeightUnitTranslation::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'weight_unit_id' => $weight_unit_id
        ], [
            'name' => $name,
            'description' => $description,
        ]);
    }
}
