<?php

namespace App\Http\Controllers\BusinessSegment;

use App\Models\BusinessSegment\LanguageProductVariant;
use App\Models\BusinessSegment\ProductInventory;
use App\Models\BusinessSegment\ProductInventoryLog;
use App\Models\BusinessSegment\ProductVariant;
use App\Models\Category;
use App\Models\BusinessSegment\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ImageTrait;
use App\Traits\ProductTrait;
use App\Traits\MerchantTrait;
use DB;
use Illuminate\Validation\Rule;
use validator;
use App\Models\BusinessSegment\ProductImage;
use View;
use App\Models\BusinessSegment\LanguageProduct;
use App;
//use Maatwebsite\Excel\Excel;
use App\Imports\ProductImport;
use App\Imports\ProductVariantImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\InfoSetting;

class ProductController extends Controller
{
    use ImageTrait,ProductTrait,MerchantTrait;

    public function searchView($request)
    {
        $data['arr_search'] = $request->all();
        $data['arr_search']['search_route'] = route('business-segment.product.index');
        $search = View::make('business-segment.product.search')->with($data)->render();
        return $search;
    }
    public function index(Request $request)
    {
        $category = $request->category;
        $segment = get_business_segment(false);
        $string_file = $this->getStringFile(NULL,$segment->Merchant);
        $business_segment_id = $segment->id;
        $merchant_id = $segment->merchant_id;
        $query = Product::with('ProductImage', 'Segment','ProductVariant')
            ->with(['Category'=>function($q) use ($merchant_id,$category) {
                $q->where([['delete', '=', NULL]]);
                if(!empty($category))
                {
                    $q->with(['LangCategorySingle'=>function($q) use($category,$merchant_id){
                        $q->where('name',$category)->where('merchant_id',$merchant_id);
                    }]);
                }
            }])
            ->whereHas('Category',function($q) use($merchant_id,$category) {
                $q->where([['delete', '=', NULL]]);
                if(!empty($category))
                {
                    $q->whereHas('LangCategorySingle',function($q) use($category,$merchant_id){
                        $q->where('name',$category)->where('merchant_id',$merchant_id);
                    });
                }
            })
            ->where([['delete', '=', NULL], ['business_segment_id', '=', $business_segment_id]]);

            if(!empty($request->name))
            {
              $query->with(['LanguageProduct'=>function($q) use($request,$merchant_id){
                  $q->where('name','like',"%".$request->name."%")->where('merchant_id',$merchant_id);
              }])
              ->whereHas('LanguageProduct',function($q) use($request,$merchant_id){
                  $q->where('name','like',"%".$request->name."%")->where('merchant_id',$merchant_id);
              });
            }
            if(!empty($request->sku_id))
            {
                $query->where('sku_id',$request->sku_id);
            }
            if(!empty($request->status))
            {
                $query->where('status',$request->status);
            }
            if(!empty($request->manage_inventory))
            {
                $query->where('manage_inventory',$request->manage_inventory);
            }

        $info_setting = InfoSetting::where('slug','PRODUCT')->first();
//            p($info_setting);
        $product['data'] =   $query->latest()->paginate(10);
        $product['segment'] = $segment;
        $product['product_status'] = get_product_status("web",$string_file);
        $product['product_inventory_status'] = product_inventory_status($string_file);
        $product['inventory_status'] = inventory_status($string_file);
        $product['arr_search']= $request->all();
        $product['search_view']= $this->searchView($request);
        $product['info_setting']= $info_setting;
        $product['arr_category_search'] = [
            'category' => $request->category,
            'merchant_id'=>$merchant_id,
            'segment_slug'=>[$segment->Segment->slag],
        ];
        return view('business-segment.product.index')->with($product);
    }

    public function basicStep(Request $request, $id = NULL)
    {
        $product = null;
        $segment = get_business_segment(false);
        $string_file = $this->getStringFile(NULL,$segment->Merchant);
        $merchant_id = $segment->merchant_id;
        $arr_category = $this->getCategory($merchant_id, $segment->segment_id);
        $sub_category = [];
        $is_demo = false;//$segment->Merchant->demo == 1 && $segment->country_area_id == 3 ? true : false;
        if (!empty($id)) {
            $product = Product::Find($id);
            if (empty($product->id)) {
                return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
            }

            $category = Category::select('id', 'category_parent_id')->find($product->category_id);
            if ($category->category_parent_id != 0) {
                $parent_category = Category::select('id', 'category_parent_id')->find($category->category_parent_id);
                $product->category_id = $parent_category->id;
                $product->sub_category_id = $category->id;
                $sub_category = $this->getCategory($merchant_id, $segment->segment_id, 'child', $parent_category->id);
            } else {
                $product->category_id = $category->id;
                $product->sub_category_id = null;
            }
        }
//        $arr_weight_unit = $this->getWeightUnit($merchant_id);
//        p($segment->Segment->slag);
        $string_file = $this->getStringFile($merchant_id);
        $data['data'] = [
            'save_url' => route('business-segment.product.basic.save', $id),
            'product' => $product,
            'arr_category' => $arr_category,
            'arr_food_type' => get_food_type($string_file),
            'arr_status' => get_active_status("web",$string_file),
            'product_status' => get_product_status("web",$string_file),
            'segment' => $segment,
            'sub_category' => $sub_category,
        ];
        $data['is_demo'] = $is_demo;
        return view('business-segment.product.form')->with($data);
    }

    /*Save or Update*/
    public function basicStepSave(Request $request, $id = NULL)
    {
        $width = Config('custom.image_size.product.width');
        $height = Config('custom.image_size.product.height');

        $business_segment = get_business_segment(false);
        $string_file = $this->getStringFile(NULL,$business_segment->Merchant);
        $business_segment_id = $business_segment->id;
        $merchant_id = $business_segment->merchant_id;
        $locale = \App::getLocale();
        $validator = Validator::make($request->all(), [
            'sku_id' => ['required',
                Rule::unique('products', 'sku_id')->where(function ($query) use ($merchant_id,$business_segment_id) {
                    return $query->where([['business_segment_id', '=', $business_segment_id], ['merchant_id', '=', $merchant_id], ['delete', '=', NULL]]);
                })->ignore($id)],

            'status' => 'required',
            'product_description' => 'required',
//            'product_cover_image'=>'required_without:id|mimes:jpg,jpeg,png',
            'product_cover_image' => 'required_if:id,!=,null|sometimes|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=' . $width . ',min_height=' . $height,
//            'product_image.*'=>'required_without:id|mimes:jpg,jpeg,png',
//            'product_image.*' => 'required_if:id,!=,null|image|mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=' . $width . ',min_height=' . $height,
            'category_id' => 'required',
//            'food_type' => 'required',
        ],['product_image.dimensions' => 'Please upload correct dimensions image']);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput()->withErrors($errors);
        }
        // product name
        $product_name = DB::table('language_products')->where(function ($query) use ($merchant_id,$locale,$id,$request) {
            return $query->where([['language_products.merchant_id', '=', $merchant_id], ['language_products.locale', '=', $locale], ['language_products.name', '=', $request->product_name]])
                ->where('language_products.product_id','!=',$id);
        })->join("products","language_products.product_id","=","products.id")
            ->where('products.id','!=',$id)
            ->where('products.merchant_id','=',$merchant_id)
            ->where('products.business_segment_id','=',$business_segment_id)
            ->where('products.delete',NULL)->first();

        if (!empty($product_name->id)) {
            $string_file = $this->getStringFile($merchant_id);
            return redirect()->back()->withErrors(trans("$string_file.product_name_already_exist"));
        }
        // Begin Transaction
        DB::beginTransaction();

        try {
            $business_segment = get_business_segment(false);
            $is_update_product_status = false;
            if (!empty($id)) {
                $product = Product::Find($id);
                if($product->status != $request->status){
                    $is_update_product_status = true;
                }
            } else {
                $product = new Product ();
                $product->business_segment_id = $business_segment->id;
                $product->merchant_id = $business_segment->merchant_id;
                $product->segment_id = $business_segment->segment_id;
            }

            $product->sku_id = $request->sku_id;
            $product->product_preparation_time = $request->product_preparation_time;
            $product->sequence = $request->sequence;
            $product->status = $request->status;
            $product->category_id = !empty($request->sub_category_id) ? $request->sub_category_id : $request->category_id;
            if(isset($request->food_type)){
                $product->food_type = $request->food_type;
            }
            $product->display_type = $request->display_type;
            $product->manage_inventory = $request->manage_inventory;
            if (!empty($request->hasFile('product_cover_image'))) {
                $additional_req = ['compress' => true, 'custom_key' => 'product'];
                $product->product_cover_image = $this->uploadImage('product_cover_image', 'product_cover_image', $merchant_id, 'single', $additional_req);
            }
            $product->save();
            // save language data
            $this->saveLanguageData($request,$product->merchant_id,$product);

            if($is_update_product_status){
                ProductVariant::where('product_id', $product->id)->update(array('status' => $request->status));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            DB::rollback();
            // Rollback Transaction
        }
        // Commit Transaction
        DB::commit();
        return redirect()->route('business-segment.product.variant.index', ['id' => $product->id])->with('success', trans("$string_file.added_successfully"));
    }

    public function saveLanguageData($request,$merchant_id,$product)
    {
        LanguageProduct::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'product_id' => $product->id
        ], [
            'business_segment_id' => $product->business_segment_id,
            'name' => $request->product_name,
            'description' => $request->product_description,
            'ingredients' => $request->product_ingredients,
        ]);
    }

    public function productVariant(Request $request, $id)
    {
        $segment = get_business_segment(false);
        $string_file = $this->getStringFile(NULL,$segment->Merchant);
        $business_segment_id = $segment->id;
        $product['data'] = Product::where([['delete', '=', NULL], ['business_segment_id', '=', $business_segment_id]])
            ->findOrFail($id);
        $product['product_variants'] = ProductVariant::with('ProductInventory')->where([['delete', '=', NULL], ['product_id', '=', $id]])->get();
        $arr_weight_unit = $this->getWeightUnit($segment->merchant_id,"web",$segment->segment_id);
        $product['arr_weight_unit'] = add_blank_option($arr_weight_unit, trans("$string_file.select"));
        $product['product_status'] = get_product_status("web",$string_file);
        $product['merchant_id'] = $segment->merchant_id;
        $product['bs_id'] = $business_segment_id;
        $product['arr_option_type'] = [];
        $product['is_demo'] = $segment->Merchant->demo == 1 && $segment->country_area_id == 3 ? true : false;
        $step_value = $this->stepValue($segment->merchant_id);
        $product['step_value'] = $step_value;
        $product['trip_calculation_method'] = $segment->Merchant->Configuration->trip_calculation_method;
        if($segment->Segment->slag == "FOOD")
        {
            $product['arr_option_type'] =
                App\Models\OptionType:: select('id','charges_type')
                    ->with(["Option"=>function($q) use($segment,$id){
                        $q->addSelect('id','option_type_id')
                            ->where([['status','=',1],['business_segment_id','=',$segment->id],['delete','=',NULL]]);
//                     $q->with(["Product"=>function($qq) use($segment,$id){
//                         $qq->where('product_id','=',$id);
//                     }]);
                    }])
                    ->whereHas("Option",function($q) use($segment,$id){
                        $q->where([['status','=',1],['business_segment_id','=',$segment->id],['delete','=',NULL]]);
//                $q->whereHas("Product",function($qq) use($segment,$id){
//                    $qq->where('product_id',$id);
//                });
                    })->where([['status','=',1],['merchant_id','=',$segment->merchant_id],['delete','=',NULL]])
                    ->get();
        }
        return view('business-segment.product.product_variant')->with($product);
    }

    public function saveVariantData($request,$merchant_id,$product_variant,$product)
    {
        LanguageProductVariant::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'product_variant_id' => $product_variant->id
        ], [
            'business_segment_id' => $product->business_segment_id,
            'name' => $request->title,
        ]);
    }

    /*Save or Update*/
    public function variantStepSave(Request $request)
    {
        $id = isset($request->id) ? $request->id : NULL;
        $product_id = isset($request->product_id) ? $request->product_id : NULL;
        $validator = Validator::make($request->all(), [
//            'sku_id' => 'required|max:255|unique:product_variants,sku_id,' . $id . ',id',
            'sku_id' => 'required|max:255',
//            'title' => 'required|max:255|unique:product_variants,product_title,' . $id . ',id,product_id,' . $product_id,
            'title' => 'required|max:255',
            'status' => 'required',
            'price' => 'required|between:0,100000',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return array('error' => $errors);
        }
        // Begin Transaction
        DB::beginTransaction();
        try {
            $business_segment = get_business_segment(false);
            $string_file = $this->getStringFile(NULL,$business_segment->Merchant);
            $product_sku_diff = Product::whereHas('ProductVariant',function($q)use($request,$id){
                return $q->where([['sku_id','=',$request->sku_id],['id','!=',$id]]);
            })->where([['merchant_id','=',$business_segment->merchant_id],['business_segment_id','=',$business_segment->id],['segment_id','=',$business_segment->segment_id],['id','=',$product_id]])->count();
            if($product_sku_diff > 0){
                return array('error' => 'The sku id has already been taken.');
            }
            if (!empty($id)) {
                $product_variant = ProductVariant::where('product_id', $product_id)->Find($id);
            } else {
                $product_variant = new ProductVariant ();
                $product_variant->product_id = $product_id;
            }
            $product = Product::find($product_id);
            if($product->status == 2 && $request->status == 1){
                return array('error' => trans('admin.product_variant_status_error'));
            }
            $is_title_show = 0;
            if($request->is_title_show == 'on'){
                $is_title_show = 1;
            }
            if ($id != null && $product_variant->product_price != $request->price) {
                if (isset($product->manage_inventory) && $product->manage_inventory == 1) {
                    $product_inventory = ProductInventory::where('product_variant_id', $id)->first();
                    if (!empty($product_inventory)) {
                        $product_inventory_log = new ProductInventoryLog();
                        $product_inventory_log->product_inventory_id = $product_inventory->id;
                        $product_inventory_log->last_current_stock = $product_inventory->current_stock;
                        $product_inventory_log->last_product_cost = $product_inventory->product_cost;
                        $product_inventory_log->last_product_selling_price = $product_inventory->product_selling_price;
                        $product_inventory_log->new_stock = 0;
                        $product_inventory_log->current_stock = $product_inventory->current_stock;
                        $product_inventory_log->product_cost = $product_inventory->product_cost;
                        $product_inventory_log->product_selling_price = $request->price;
                        $product_inventory_log->save();
                        $product_inventory->product_selling_price = $request->price;
                        $product_inventory->save();
                    }
                }
            }
            $product_variant->sku_id = $request->sku_id;
           $product_variant->product_title = $request->title;
            $product_variant->product_price = $request->price;
            $product_variant->discount = $request->discount;
            $product_variant->weight_unit_id = $request->weight_unit;
            $product_variant->weight = $request->weight;
            $product_variant->status = $request->status;
            $product_variant->is_title_show = $is_title_show;
            $product_variant->save();

            // sync language of category
            if($is_title_show == 1)
            {
              $this->saveVariantData($request,$product->merchant_id,$product_variant,$product);
            }
        } catch (\Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
            return array('error' => $message);
        }
        DB::commit();
        return array('success' => trans("$string_file.added_successfully"), 'route' => route('business-segment.product.variant.index', ['id' => $product_id]));
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $delete = Product::Find($id);
        $delete->delete = 1;
        $delete->save();
    }

    public function productImageRemove(Request $request, $id)
    {
        $product_id = NULL;
        $remove = ProductImage::find($id);
        if(!empty($remove))
        {
            $product_id = $remove->product_id;
            // first delete image from s3
            delete_image($remove->product_image, 'product_image',$remove->Product->merchant_id);
            $remove->delete();
        }
        return redirect()->route('business-segment.product.basic.add', $product_id)->with('success', trans('admin.product_image_deleted'));
    }



    public function getSubCategory(Request $request)
    {
        $result = [];
        $segment = get_business_segment(false);
        $string_file = $this->getStringFile(NULL,$segment->Merchant);
        if (isset($request->id) && $request->id != '') {
            $merchant_id = $segment->merchant_id;
            $sub_category = $this->getCategory($merchant_id, $segment->segment_id, 'child', $request->id);
            if (!empty($sub_category)) {
                $result = $sub_category;
            } else {
                $result = array('' => trans("$string_file.select"));
            }
        } else {
            $result = array('' => trans("$string_file.select"));
        }
        return json_encode($result, true);
    }

    public function variantDestroy(Request $request)
    {
        try {
            if (isset($request->id) && $request->id != '') {
                $id = $request->id;
                $delete = ProductVariant::Find($id);
                $delete->delete = 1;
                $delete->save();
                return array('result' => 'success');
            } else {
                return array('result' => 'error', 'data' => 'Invalid ID');
            }
        } catch (\Exception $e) {
            return array('result' => 'error', 'data' => $e->getMessage());
        }
    }

    /*Save or Update products option*/
    public function optionStepSave(Request $request, $id = NULL)
    {
        $business_segment = get_business_segment(false);
        $string_file = $this->getStringFile(NULL,$business_segment->Merchant);
        $validator = Validator::make($request->all(), [
                'arr_option' => 'required',
                'product_id' => 'required',
            ]
            ,['arr_option.required' => trans_choice("$string_file.have_to_choose", 3, ['NUM' => trans("$string_file.one"),'OBJECT' => trans("$string_file.option")])]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput()->withErrors($errors);
        }
        // Begin Transaction
        DB::beginTransaction();

        try {
            $id = $request->product_id;
            $arr_option = $request->arr_option;
            $arr_option_amount = $request->option_amount;
            $product = Product::select('id','manage_inventory')->Find($id);
//            p($arr_option,0);
//            p($arr_option_amount);
            $product->Option()->detach();
            foreach ($arr_option as $option_id =>$option)
            {
                $amount =  isset($arr_option_amount[$option_id]) ? $arr_option_amount[$option_id] : NULL;
                $product->Option()->attach($option_id,['option_amount'=>$amount]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            DB::rollback();
            return redirect()->back()->withErrors($message);
            // Rollback Transaction
        }
        // Commit Transaction
        DB::commit();
        if($product->manage_inventory == 1)
        {
            return redirect()->route('business-segment.product.inventory.index',['id' => $product->id])->withSuccess(trans("$string_file.added_successfully"));
        }
        else
        {
            return redirect()->route('business-segment.product.index')->withSuccess(trans("$string_file.added_successfully"));
        }
    }


    // import products from excel sheet
//    public function importProducts(Request $request)
//    {
//
////        p($request->all());
//        if($request->hasFile('product_import_sheet')){
//            Excel::load($request->file('product_import_sheet')->getRealPath(), function ($reader) {
//                p($reader->toArray());
//                foreach ($reader->toArray() as $key => $row) {
//                    $data['title'] = $row['title'];
//                    $data['description'] = $row['description'];
//
//                    if(!empty($data)) {
//                        DB::table('post')->insert($data);
//                    }
//                }
//            });
//        }
//        else
//        {
//            // invalid file name
//        }
//    }


     // import Products
    public function importProducts(Request $request)
    {
        $validator = Validator::make($request->all(),
            ['product_import_sheet'  => 'required|mimes:xls,xlsx']
        );

        if ($validator->fails()){
            $msg = $validator->messages()->all();
            return redirect()->back()->with('error',$msg[0]);
        }
        DB::beginTransaction();
        try
        {
            $path1 = $request->file('product_import_sheet')->store('temp');
            $path = storage_path('app').'/'.$path1;
            Excel::import(new ProductImport,$path);
        }catch (\Exception $e)
        {
            DB::rollBack();
            $message = $e->getMessage().',File : '.$e->getFile().',Line : '.$e->getFile();
            return redirect()->back()->withErrors($message);
        }

        DB::commit();
        return redirect()->back()->withSuccess("done");
    }


    // import Product variant
    public function importProductVariants(Request $request)
    {
        $validator = Validator::make($request->all(),
            ['product_variant_import_sheet'  => 'required|mimes:xls,xlsx']
        );

        if ($validator->fails()){
            $msg = $validator->messages()->all();
//            p($msg);
            return redirect()->back()->with('error',$msg[0]);
        }
        DB::beginTransaction();
        try
        {
            $path1 = $request->file('product_variant_import_sheet')->store('temp');
            $path = storage_path('app').'/'.$path1;
            Excel::import(new ProductVariantImport,$path);
        }catch (\Exception $e)
        {
            DB::rollBack();
            $message = $e->getMessage().',File : '.$e->getFile().',Line : '.$e->getLine();
            return redirect()->back()->withErrors($message);
        }

        DB::commit();
        return redirect()->back()->withSuccess("done");
    }
}