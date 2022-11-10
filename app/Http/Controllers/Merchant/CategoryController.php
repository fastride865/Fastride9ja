<?php

namespace App\Http\Controllers\Merchant;
use App\Models\ApplicationConfiguration;
use App\Models\Category;
use App\Models\InfoSetting;
use App\Models\LangName;
use App\Traits\ImageTrait;
use App\Traits\ProductTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use validator;
use View;
use App\Traits\MerchantTrait;

class CategoryController extends Controller
{
    use ImageTrait, ProductTrait,MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','CATEGORY')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index(Request  $request)
    {

        $merchant_id = get_merchant_id();
        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
        $all_segments = array_merge(['TAXI','DELIVERY'],$all_food_grocery_clone);
        $checkPermission = check_permission(1, $all_segments, true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }


        $permission_segments = get_permission_segments(1,true);

        $category_name = $request->category;
        $query = Category::with(['Segment' => function($query) use($permission_segments){
            $query->whereIn('slag',$permission_segments);
        }])->whereHas("Segment",function($query) use($permission_segments){
            $query->whereIn('slag',$permission_segments);
        })
        ->where('merchant_id',$merchant_id)->where('delete','=',NULL);
        if(!empty($category_name))
        {
            $query->with(['LangCategorySingle'=>function($q) use($category_name,$merchant_id){
            $q->where('name',$category_name)->where('merchant_id',$merchant_id);
            }])->whereHas('LangCategorySingle',function($q) use($category_name,$merchant_id){
                $q->where('name',$category_name)->where('merchant_id',$merchant_id);
            });
        }

        $all_category = $query->paginate(15);
        $category['data'] =$all_category;
        $category['data']['parent_category'] = Category::select('id','category_parent_id')->where('merchant_id',get_merchant_id())->where('category_parent_id',0)->where('delete','=',NULL)->get();
        $category['search_route'] = route('merchant.category');
        $category['arr_search'] = $request->all();
        $category['arr_search']['merchant_id'] = $merchant_id;
        $category['arr_search']['segment_slug'] = $permission_segments;
        return view('merchant.category.index')->with($category);
    }

    public function add(Request $request, $id = NULL)
    {
        $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
        $all_segments = array_merge(['TAXI','DELIVERY'],$all_food_grocery_clone);
        $checkPermission = check_permission(1, $all_segments, true);
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $category = NULL;
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $pre_title = trans("$string_file.add");
        $save_url = route('business-segment.category.save');
        $arr_selected_segment = [];
        $is_demo = false;
        if (!empty($id)) {
            $category = Category::Find($id);
            $arr_selected_segment = array_pluck($category->Segment,'id');
            if (empty($category->id)) {
                return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
            }
            $pre_title = trans("$string_file.edit");
            $save_url = route('business-segment.category.save', $id);
            $is_demo = $merchant->demo == 1 ? true : false;
        }
        $title = $pre_title.' '.trans("$string_file.category");
        $arr_category = add_blank_option($this->getCategory($merchant_id),trans("$string_file.none"));
        $arr_businesss = get_merchant_segment($with_taxi = true, null,$segment_group_id = 1);
        // If there is no category view then remove taxi and delivery segment
        $app_config = ApplicationConfiguration::where("merchant_id",$merchant_id)->first();
        if(isset($app_config->home_screen_view) && $app_config->home_screen_view != 1){
            if(isset($arr_businesss[1])){
                unset($arr_businesss[1]);
            }
            if(isset($arr_businesss[2])){
                unset($arr_businesss[2]);
            }
        }
        $arr_businesss = get_permission_segments(1, false, $arr_businesss);
        $segment_data['arr_segment'] = $arr_businesss;
        $segment_data['selected'] = $arr_selected_segment;
        $segment_html = View::make('segment')->with($segment_data)->render();
        $data['data'] = [
            'title' => $title,
            'save_url' => $save_url,
            'category' => $category,
            'arr_category'=>$arr_category,
            'segment_html'=>$segment_html,
            'arr_status'=>get_active_status("web",$string_file),
        ];
        $data['is_demo'] = $is_demo;
        return view('merchant.category.form')->with($data);
    }

    /*Save or Update*/
    public function save(Request $request, $id = NULL)
    {
        $width = Config('custom.image_size.category.width');
        $height = Config('custom.image_size.category.height');
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $locale = \App::getLocale();
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
            'category_image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width='.$width.',min_height='.$height,
            'sequence' => 'required|integer',
            'status' => 'required',
            'segment'=>'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

      $category_name = DB::table('lang_names')->where(function ($query) use ($merchant_id,$locale,$id,$request) {
            return $query->where([['lang_names.merchant_id', '=', $merchant_id], ['lang_names.locale', '=', $locale], ['lang_names.name', '=', $request->category_name]])
                ->where('lang_names.dependable_id','!=',$id);
        })->join("categories","lang_names.dependable_id","=","categories.id")
            ->where('categories.id','!=',$id)
            ->where('categories.merchant_id','=',$merchant_id)
            ->where('categories.delete',NULL)->first();

        if (!empty($category_name->id)) {

            return redirect()->back()->withErrors(trans("$string_file.category_name_already_exist"));
        }
        // Begin Transaction
        DB::beginTransaction();

        try {
            if (!empty($id)) {
                $category = Category::Find($id);
            } else {
                $category = new Category ();
            }

            $merchant_id = get_merchant_id();
            $category->category_parent_id = !empty($request->category_parent_id) ? $request->category_parent_id : 0;
            $category->sequence = $request->sequence;
            $category->status = $request->status;
            $category->merchant_id = $merchant_id;
            if (!empty($request->hasFile('category_image'))) {
                $additional_req = ['compress'=>true,'custom_key'=>'category'];
                $category->category_image = $this->uploadImage('category_image', 'category',$merchant_id,'single',$additional_req);
            }

            $category->save();

            // sync segment
            $category->Segment()->sync($request->segment);

            // sync language of category
            $category_locale =  $category->LangCategorySingle;
            if(!empty($category_locale->id))
            {
                $category_locale->name = $request->category_name;
                $category_locale->save();
            }
            else
            {
                $language_data = new LangName([
                    'merchant_id' => $category->merchant_id,
                    'locale' => $locale,
                    'name' => $request->category_name]);

            $category->LangCategory()->save($language_data);
//
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            DB::rollback();
            return redirect()->route('merchant.category')->withErrors($message);
            // Rollback Transaction
        }
        // Commit Transaction
        DB::commit();
        return redirect()->route('merchant.category')->withSuccess(trans("$string_file.category_saved_successfully"));
    }
    public function destroy(Request $request)
    {
        $id = $request->id;
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        if($merchant->demo == 1)
        {
            echo trans("$string_file.demo_warning_message");
        }
        if(is_array($id)){
            $delete = Category::whereIn('id',$id)->update(['delete' => 1]);
        }else{
            $delete = Category::where('id',$id)->update(['delete' => 1]);
        }
    }
}