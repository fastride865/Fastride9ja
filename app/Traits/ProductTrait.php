<?php

namespace App\Traits;

use App\Models\BusinessSegment\ProductInventory;
use App\Models\BusinessSegment\ProductInventoryLog;
use App\Models\BusinessSegment\ProductVariant;
use App\Models\Category;
use App\Models\WeightUnit;
use Auth;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;

trait ProductTrait{

    public function getProducts($request)
    {
        //p($request->all());
        $user = $request->user('api');
        $currency = $user->Country->isoCode;
        $merchant_id = $request->merchant_id;
        $segment_id = $request->segment_id;
        $sub_category_id = $request->sub_category_id;
        $category_id = !empty($sub_category_id) ? $sub_category_id : $request->category_id;
        $display_type = !empty($request->display_type) ? $request->display_type : NULL;
        $business_segment_id = !empty($request->business_segment_id) ? $request->business_segment_id : NULL;

        $products = ProductVariant::select('id','weight','product_id','weight_unit_id','discount','product_title','product_price','status','is_title_show')
            ->with(['Product'=>function($q) use($merchant_id,$segment_id,$category_id,$business_segment_id,$display_type){
                $q->select('id','category_id','food_type','product_cover_image','manage_inventory','sequence')
                    ->where([['merchant_id','=',$merchant_id],['segment_id','=',$segment_id],['delete','=',NULL]]);
                $q->where(function ($qq) use ($business_segment_id) {
                    if(!empty($business_segment_id))
                    {
//                        $qq->where('business_segment_id', $business_segment_id);
                        if(is_array($business_segment_id))
                        {
                            $qq->whereIn('business_segment_id', $business_segment_id);
                        }
                        else
                        {
                            $qq->where('business_segment_id', $business_segment_id);
                        }
                    }
                });
                $q->where(function ($qq) use ($display_type) {
                    if(!empty($display_type))
                    {
                        $qq->where('display_type', $display_type);
                    }
                });
                $q->where(function ($qq) use ($category_id) {
                    if(!empty($category_id))
                    {
                        $qq->where('category_id', $category_id);
                    }
                });
                $q->orderBy('sequence','ASC');
                // $q->orderBy('updated_at','DESC');
                $q->with(['Category'=>function($qq) use($merchant_id,$category_id) {
                    $qq->select('id', 'category_parent_id')
                        ->where(function ($qqq) use ($category_id) {
                            $qqq->where('id', $category_id);
                            $qqq->orWhere('category_parent_id', $category_id);
                        })
                        ->where([['merchant_id', '=', $merchant_id], ['status', '=', 1], ['delete', '=', NULL]]);
                }]);
            }])
            ->whereHas('Product',function($q) use($merchant_id,$segment_id,$category_id,$business_segment_id,$display_type) {
                $q->select('id','category_id','food_type','product_cover_image')
                    ->where([['merchant_id','=',$merchant_id],['segment_id','=',$segment_id],['status','=',1],['delete','=',NULL]]);
                $q->where(function ($qq) use ($business_segment_id) {
                    if(!empty($business_segment_id))
                    {
                        $qq->where('business_segment_id', $business_segment_id);
                    }
                });
                $q->where(function ($qq) use ($display_type) {
                    if(!empty($display_type))
                    {
                        $qq->where('display_type', $display_type);
                    }
                });
                $q->where(function ($qq) use ($category_id) {
                    if(!empty($category_id))
                    {
                        $qq->where('category_id', $category_id);
                    }
                });
                $q->orderBy('sequence');
                $q->orderBy('updated_at','DESC');
                if(!empty($category_id))
                {
                    $q->whereHas('Category',function($qq) use($merchant_id,$category_id) {
                        $qq->select('id', 'category_parent_id')
                            ->where(function ($qqq) use ($category_id) {
                                $qqq->where('id', $category_id);
                                $qqq->orWhere('category_parent_id', $category_id);
                            })
                            ->where([['merchant_id', '=', $merchant_id], ['status', '=', 1], ['delete', '=', NULL]]);

                    });
                }
                 else
                {

                    $q->whereHas('Category',function($qq) use($merchant_id) {
                        $qq->where([['merchant_id', '=', $merchant_id], ['status', '=', 1], ['delete', '=', NULL]]);

                    });
                }
//                $q->with(['Category'=>function($qq) use($merchant_id,$category_id) {
//                    $qq->select('id', 'category_parent_id')
//                        ->where(function ($qqq) use ($category_id) {
//                            $qqq->where('id', $category_id);
//                            $qqq->orWhere('category_parent_id', $category_id);
//                        })
//                        ->where([['merchant_id', '=', $merchant_id], ['status', '=', 1], ['delete', '=', NULL]]);
//                }]);
            })
            ->with(['WeightUnit'=>function($q){
                $q->select('id');
            }])
            ->with(['ProductInventory'=>function($q) use($merchant_id,$segment_id) {
                $q->select('id', 'product_variant_id', 'current_stock');
                $q->where([['merchant_id', '=', $merchant_id], ['segment_id', '=', $segment_id]]);
            }])
            ->where([['status', '=', 1]])
            ->orderBy(DB::raw('RAND()'))
            ->get();
        // p($products);
        if($request->return_type == "modified_array")
        {
            foreach($products as $key => $product){
                if($product->Product->manage_inventory == 1 && empty($product->ProductInventory)){
                    $products->forget($key);
                }
            }
            $products = $products->values();
            $products = $products->map(function ($item, $key) use($merchant_id,$currency)
            {
                $unit = isset($item->WeightUnit->id) ? $item->WeightUnit->WeightUnitName : "";
                $product_lang = $item->Product->langData($merchant_id);
                return array(
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'weight_unit_id' => $item->weight_unit_id,
                    'stock_quantity' => isset($item->ProductInventory->id) ? $item->ProductInventory->current_stock : NULL,
                    'price' =>$item->product_price,
                    'discount' => !empty($item->discount) && $item->discount> 0 ? $item->discount : "",
                    'discounted_price' =>!empty($item->discount) && $item->discount> 0 ? (string)($item->product_price - $item->discount) : "",
                    'title' => $item->is_title_show == 1 ? $item->Name($merchant_id) : $product_lang->name,
                    'product_description' => !empty($product_lang->description) ? $product_lang->description : "",
                    'ingredients' => !empty($product_lang->ingredients) ? $product_lang->ingredients : "",
                    'currency' => "$currency",
                    'manage_inventory' => $item->Product->manage_inventory,
                    'weight_unit' => $item->weight.' '.$unit,
                    'image' => !empty($item->Product->product_cover_image) ? get_image($item->Product->product_cover_image,'product_cover_image',$merchant_id) : "",
                    'product_availability' => ($item->status == 1) ? true :false,
                    'sequence' =>$item->Product->sequence
                );
            });
            if(!empty($products)){
                $products = $products->sortBy('sequence')->values();
            }
            // p($products);
        }
        return $products;
    }

    public function manageProductVariantInventory($request)
    {
        $product_variant_id = isset($request->id) ? $request->id : NULL;
        // inventory object
        $product_inventory = ProductInventory::where('product_variant_id',$product_variant_id)->first();
        // inventory log object
        $product_inventory_log = new ProductInventoryLog();
        // current stock of variant
        $current_stock = $product_inventory->current_stock;

        $updated_current_stock = 0;
        if($request->stock_type == 1) // stock in
        {
            $updated_current_stock = ($current_stock + $request->new_stock);
        }
        else
        {
            $updated_current_stock = ($current_stock - $request->new_stock);
            $product_inventory_log->stock_out_id = $request->stock_out_id;
        }

        $product_inventory->current_stock = $updated_current_stock;
        $product_inventory->save();

        $product_inventory_log->product_inventory_id = $product_inventory->id;
        $product_inventory_log->last_current_stock = $current_stock;
        $product_inventory_log->stock_type = $request->stock_type;
        $product_inventory_log->new_stock = $request->new_stock;
        $product_inventory_log->current_stock = $updated_current_stock;
        $product_inventory_log->product_cost = $product_inventory->product_cost;
        $product_inventory_log->product_selling_price = $product_inventory->product_selling_price;
        $product_inventory_log->save();
        return true;
    }


    public function getCategory($merchant_id, $segment_id = null, $type = "parent", $parent_id = NULL,$calling_from = "web")
    {
        $categories = Category::select('id')
            ->where(function ($query) use ($type, $parent_id) {
                if ($type == 'parent') {
                    $query->where('category_parent_id', '=', 0);
                } elseif ($type == 'child') {
                    $query->where('category_parent_id', '=', $parent_id);
                }
            })->with('Segment')->whereHas('Segment', function ($q) use ($segment_id) {
               if(!empty($segment_id))
               {
                $q->where('segment_id', $segment_id);
               }
            })
            ->where('merchant_id', $merchant_id)
            ->where('delete', NULL)
            ->get();
        $arr_category = [];
        if($calling_from =="app")
        {
            foreach ($categories as $category) {
                $arr_category[] =
                    ["key"=>$category->id,"value"=>$category->Name($merchant_id)];
            }
        }
        else
        {
            foreach ($categories as $category) {
                $arr_category[$category->id] = $category->Name($merchant_id);
            }
        }
        return $arr_category;
    }


    public function getWeightUnit($merchant_id,$calling_from = "web",$segment_id = NULL)
    {
        $list = [];
        $arr_weight_unit = WeightUnit::whereHas('Segment',function($q) use($segment_id){
             $q->where('segment_id',$segment_id);
        })->where([['merchant_id', '=', $merchant_id]])->get();
        if($calling_from == "app")
        {
            foreach ($arr_weight_unit as $weight_unit) {
                $list[] = ["key"=>$weight_unit->id,"value"=>$weight_unit->WeightUnitName];
            }
        }
        else
        {
            foreach ($arr_weight_unit as $weight_unit) {
                $list[$weight_unit->id] = $weight_unit->WeightUnitName;
            }
        }

        return $list;
    }

}
