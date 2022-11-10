<?php

namespace App\Http\Controllers\BusinessSegment;

use App\Models\BusinessSegment\Product;
use App\Models\BusinessSegment\ProductInventory;
use App\Models\BusinessSegment\ProductInventoryLog;
use App\Models\BusinessSegment\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use validator;
use DB;
use App\Traits\MerchantTrait;

class ProductInventoryController extends Controller
{
    use MerchantTrait;
    public function index(Request $request)
    {
        $segment = get_business_segment(false);
        $string_file = $this->getStringFile(NULL,$segment->Merchant);
        $business_segment_id = $segment->id;
        $merchant_id = $segment->merchant_id;
        $product = null;
        $product_variants = null;
        $id = isset($request->id) ? $request->id : null;
        $product_variant_id = isset($request->product_variant_id) ? $request->product_variant_id : null;
        if ($id != null) {
            $product = Product::whereNull('delete')->findOrFail($id);
        }
        if($product_variant_id != null || $product != null){
            $product_variants = ProductVariant::with('ProductInventory','Product')->whereNull('delete')->where(function($q) use($product,$product_variant_id){
                if($product != null){
                    $q->where('product_id',$product->id);
                }
                if($product_variant_id != null){
                    $q->where('id',$product_variant_id);
                }
            })->latest()->paginate(20);
        }

        $product_list = [];
        $product_list_data = Product::where([['delete', '=', NULL], ['business_segment_id', '=', $business_segment_id]])->select('id')->get();
        foreach ($product_list_data as $product)
        {
            $product_list[$product->id] = $product->Name($merchant_id);
        }
        $product_variant_list = ProductVariant::whereHas('Product',function($query) use($business_segment_id){
            $query->where([['delete', '=', NULL], ['business_segment_id', '=', $business_segment_id]]);
        })->where([['delete', '=', NULL]])->orderBy('product_title')->pluck('product_title','id')->toArray();
        $data = array(
            'product' => $product,
            'product_variants' => $product_variants,
            'product_list' => $product_list,
            'product_variant_list' => add_blank_option($product_variant_list,trans("$string_file.select")),
            'segment' => $segment,
        );
        $data['is_demo'] = false;//$segment->Merchant->demo == 1 ? true : false;
        return view('business-segment.product-inventory.index')->with($data);
    }

    /*Save or Update*/
    public function save(Request $request)
    {
        $segment = get_business_segment(false);
        $string_file = $this->getStringFile(NULL,$segment->Merchant);
        $product_variant_id = isset($request->id) ? $request->id : NULL;
        $validator = Validator::make($request->all(), [
            'new_stock'=>'required|between:1,100000',
            'product_cost'=>'between:0,100000',
            'product_selling_price'=>'required|between:1,100000',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return array('error' =>  $errors);
        }
        // Begin Transaction
        DB::beginTransaction();
        try {
            $product_variant = ProductVariant::findOrFail($product_variant_id);
            $product_inventory = ProductInventory::where('product_variant_id',$product_variant_id)->first();
            if (empty($product_inventory)) {
                $product_inventory = new ProductInventory();
                $product_inventory->product_variant_id = $product_variant_id;
                $product_inventory->merchant_id = $segment->merchant_id;
                $product_inventory->segment_id = $segment->segment_id;
                $product_inventory->business_segment_id = $segment->id;
            }
            $last_current_stock = $product_inventory->current_stock;
            $last_product_cost = ($product_inventory->last_product_cost != null) ? $product_inventory->last_product_cost : 0;
            $last_product_selling_price = ($product_inventory->last_product_selling_price != null) ? $product_inventory->last_product_selling_price : 0;
            $product_inventory->current_stock = ($request->current_stock + $request->new_stock);
            $product_inventory->product_cost = $request->product_cost;
            $product_inventory->product_selling_price = $request->product_selling_price;
            $product_inventory->save();

            $product_inventory_log = new ProductInventoryLog();
            $product_inventory_log->product_inventory_id = $product_inventory->id;
            $product_inventory_log->last_current_stock = $last_current_stock;
            $product_inventory_log->last_product_cost = $last_product_cost;
            $product_inventory_log->last_product_selling_price = $last_product_selling_price;
            $product_inventory_log->new_stock = $request->new_stock;
            $product_inventory_log->current_stock = ($request->current_stock + $request->new_stock);
            $product_inventory_log->product_cost = $request->product_cost;
            $product_inventory_log->product_selling_price = $request->product_selling_price;
            $product_inventory_log->save();

            $product_variant->product_price = $request->product_selling_price;
            $product_variant->save();
        }
        catch (\Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
            return array('error' => $message);
        }
        DB::commit();
        return array('success' => trans("$string_file.added_successfully"), 'route' => route('business-segment.product.inventory.index',['id'=>$product_variant->product_id]));
    }
}
