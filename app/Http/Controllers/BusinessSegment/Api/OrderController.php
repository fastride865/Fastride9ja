<?php

namespace App\Http\Controllers\BusinessSegment\Api;

use App\Models\BusinessSegment\LanguageProduct;
use App\Models\BusinessSegment\Order;
use App\Models\BusinessSegment\Product;
use App\Models\BusinessSegment\ProductImage;
use App\Models\BusinessSegment\ProductInventory;
use App\Models\BusinessSegment\ProductInventoryLog;
use App\Models\BusinessSegment\ProductVariant;
use App\Models\BookingTransaction;
use App\Models\CancelReason;
use App\Models\Category;
use App\Models\Driver;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Passport\Client;
use DB;
use App\Traits\ImageTrait;
use App\Traits\OrderTrait;
use App\Traits\ProductTrait;
use App\Traits\ApiResponseTrait;
use Lcobucci\JWT\Parser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use App;
use App\Http\Controllers\BusinessSegment\OrderController as CommonOrder;

class OrderController extends Controller
{
    use ImageTrait, OrderTrait, ApiResponseTrait,ProductTrait;

    public function getOrders(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:TODAY,UPCOMING,ONGOING,PENDING_PROCESSING,COMPLETED,CANCELLED,REJECTED,PICKUP_VERIFICATION,ONTHEWAY',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $merchant_id = $request->merchant_id;
        $bs = $request->user('business-segment-api');
        $request->request->add(['business_segment_id'=>$bs->id]);

        $string_file = $this->getStringFile($merchant_id);
        $req_param['string_file'] = $string_file;
        $arr_status = $this->getOrderStatus($req_param);
        $order = new Order;
        $arr_orders = $order->getOrders($request,true);
        $data = [];
        foreach ($arr_orders as $order)
        {
            $arr_completed_order = [];
            if(!empty($order->order_status_history))
            {
                $status_completed = json_decode($order->order_status_history,true);
                $status_completed =  array_column($status_completed,NULL,'order_status');
                $arr_completed_order =array_keys($status_completed);
            }

            $order_created = convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone,null,$order->Merchant);
            $order_info = [
                'id'=>$order->id,
                'status'=>$order->order_status,
                'items'=>$order->quantity.' '.trans("$string_file.items"),
                'status_text'=>$arr_status[$order->order_status],
                'number'=>$order->merchant_order_id,
                'time'=>date("H:i a,d M",strtotime($order_created)),
            ];
//            $user_info = [
//                'user_name'=>$order->User->first_name .' '.$order->User->last_name,
//                'user_image'=>get_image($order->User->UserProfileImage,'user',$order->merchant_id),
//                'user_phone'=>$order->User->UserPhone,
//                'user_rating'=>"4.5",
//            ];
//            $pickup = $order->BusinessSegment;
//            $pick_details = [
//                'lat'=>$pickup->latitude,
//                'lng'=>$pickup->longitude,
//                'address'=>$pickup->address,
//                'icon'=>view_config_image("static-images/pick-icon.png"),
//            ];
//            $drop_details = [
//                'lat'=>$order->drop_latitude,
//                'lng'=>$order->drop_longitude,
//                'address'=>$order->drop_location,
//                'icon'=>view_config_image("static-images/drop-icon.png"),
//            ];
            $payment_details = [
                'payment_mode'=>$order->PaymentMethod->payment_method,
                'amount'=>$order->CountryArea->Country->isoCode .' '.$order->final_amount_paid,
                'paid'=>$order->payment_status == 1 ? trans("$string_file.paid") :trans("$string_file.not_paid"),
            ];

            $button_text = "";
            $api_to_call = "";
            $action_buttons = [];
            if($order->order_status == 1)
            {
                if($order->BusinessSegment->order_request_receiver == 1)
                {
                 $assign =   [
                    'button_text'=> trans("$string_file.assign"),
                    'button_text_colour'=>"FFFFFF",
                    'button_action'=>"ASSIGN",
                ];
                 array_push($action_buttons,$assign);
                }
                $reject = [
                        'button_text'=> trans("$string_file.reject"),
                        'button_text_colour'=>"FFFFFF",
                        'button_action'=>"REJECT",
                        ];
                array_push($action_buttons,$reject);

            }
            elseif($order->order_status == 6)
            {
                $action_buttons = [
                    [
                        'button_text'=> trans("$string_file.process"),
                        'button_text_colour'=>"FFFFFF",
                        'button_action'=>"PROCESS",
                    ],
                ];
            }
            elseif($order->order_status == 7 && !in_array(9,$arr_completed_order))
            {
                $action_buttons = [
                    [
                        'button_text'=> trans("$string_file.process"),
                        'button_text_colour'=>"FFFFFF",
                        'button_action'=>"PROCESS",
                    ],
                ];
            }
            elseif($order->order_status == 9 && $order->otp_for_pickup != NULL && $order->confirmed_otp_for_pickup == 2)
            {
                $action_buttons = [
                    [
                        'button_text'=> trans("$string_file.pickup_verification"),
                        'button_text_colour'=>"FFFFFF",
                        'button_action'=>"PICKUP_VERIFICATION",
                    ],
                ];
            }

            $data[] = [
                'info'=> $order_info,
//                'user_info'=> $user_info,
//                'pick_details'=> $pick_details,
//                'drop_details'=> $drop_details,
                'payment_details'=> $payment_details,
                'action_buttons'=> $action_buttons,
            ];
        }

        $orders = $arr_orders->toArray();
        $next_page_url = isset($orders['next_page_url']) && !empty($orders['next_page_url']) ? $orders['next_page_url'] : "";
        $current_page = isset($orders['current_page']) && !empty($orders['current_page']) ? $orders['current_page'] : 0;

        $response =[
            'current_page'=>$current_page,
            'next_page_url'=>$next_page_url,
            'response_data'=>$data
        ];
        return $this->successResponse(trans("$string_file.success"),$response);
    }
    public function getOrderDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', Rule::exists('orders', 'id')->where(function ($query) {
            }),]
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        $data = [];
        try {
            $order_obj = new Order;
            $order = $order_obj->getOrderInfo($request);
            $string_file = $this->getStringFile($order->merchant_id);
            $req_param = ['string_file'=>$string_file];
            $config_status = $this->getOrderStatus($req_param);


            $arr_completed_order = [];
            if(!empty($order->order_status_history))
            {
                $status_completed = json_decode($order->order_status_history,true);
                $status_completed =  array_column($status_completed,NULL,'order_status');
                $arr_completed_order =array_keys($status_completed);
            }

            $currency = $order->CountryArea->Country->isoCode;
            
            
            
            $deliver_time = !empty($order->service_time_slot_detail_id) ? $order->ServiceTimeSlotDetail->slot_time_text." " : "";
            $deliver_date = convertTimeToUSERzone($order->order_date, $order->CountryArea->timezone, null, $order->Merchant);
            
            $order_details = [
                'id'=>$order->id,
                'number'=>$order->merchant_order_id,
                'segment_slug'=>$order->Segment->slag,
                'segment_id'=>$order->segment_id,
                'status'=>$order->order_status,
                'total_items'=>$order->quantity,
                'total_products'=>$order->OrderDetail->count(),
                'order_time'=>date('d M Y, H:i a',$order->order_timestamp),
                'status_text'=>$config_status[$order->order_status],
                'deliver_on'=>$deliver_time.$deliver_date,
            ];

            $product_list = $order->OrderDetail;
            $bill_details =[];
            $product_details =[];
            foreach($product_list as $product)
            {
                $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
                $product_details[] =[
                    'name'=> $product->quantity .' '.$unit.' '.$product->Product->Name($order->merchant_id),
                    'product_variant' => $product->ProductVariant->Name($order->merchant_id),
                    'value'=> $product->price,
                    'bold'=> false,
                    'items'=>$product->quantity
                ];
//                    p($bill_details);
            }
            $delivery_charges = [
                'name'=>trans("$string_file.delivery_charge"),
                'value'=> $order->delivery_amount,
                'bold'=> false,
            ];
            array_push($bill_details,$delivery_charges);
            $tax = [
                'name'=>trans("$string_file.tax"),
                'value'=> $order->tax,
                'bold'=> false,
            ];
            array_push($bill_details,$tax);
            $cart_amount = [
                'name'=> trans("$string_file.cart_amount"),
                'value'=> $order->cart_amount,
                'bold'=> false,
            ];
            array_push($bill_details,$cart_amount);
             $discount_amount = [
                'name'=> trans("$string_file.discount_amount"),
                'value'=> $order->discount_amount,
                'bold'=> false,
            ];
            array_push($bill_details,$discount_amount);
            $final_amount = [
                'name'=> trans("$string_file.grand_total"),
                'value'=> $currency.$order->final_amount_paid,
                'bold'=> true,
            ];
            array_push($bill_details,$final_amount);

            $payment_details = [
                'paid_status'=>$order->payment_status == 1 ? trans("$string_file.paid") :trans("$string_file.not_paid"),
                'payment_mode'=>$order->PaymentMethod->payment_method,
                'amount'=>$currency.$order->final_amount_paid,
                'currency'=>$currency,
            ];
            $customer_details = [
                'customer_name'=>$order->User->first_name .' '.$order->User->last_name,
                'customer_phone'=>$order->User->UserPhone,
                'drop_location'=>$order->drop_location,
            ];
            $driver_details = [
                'driver_name'=> !empty($order->driver_id) ? $order->Driver->first_name .' '.$order->Driver->last_name :"",
                'driver_phone'=>!empty($order->driver_id) ? $order->Driver->phoneNumber : "",
            ];

            $action =  $order->is_order_completed == 1 || in_array($order->order_status,[2,3,5,8]) ? false : true;
            $action_buttons= [];

//                if($order->order_status == 1)
//                {
//                    $arr_action = [
//                        [
//                        'action'=>"ASSIGN",
//                        'text'=>trans("$string_file.assign"),
//                        'color'=>"3498DB",
//                        ],
//                        [
//                         'action'=>"REJECT",
//                         'text'=>trans("$string_file.reject"),
//                         'color'=>"3498DB",
//                        ]
//                   ];
//                }

            $call_customer = [
                'button_text'=> trans("$string_file.call_customer"),
                'button_text_colour'=>"FFFFFF",
                'button_action'=>"CALL_CUSTOMER",
            ];

            $call_driver =[
                'button_text'=> trans("$string_file.call_driver"),
                'button_text_colour'=>"FFFFFF",
                'button_action'=>"CALL_DRIVER",
            ];

            if($order->order_status == 1)
            {
                $action_buttons = [
                    [
                        'button_text'=> trans("$string_file.assign"),
                        'button_text_colour'=>"FFFFFF",
                        'button_action'=>"ASSIGN",
                    ],
                    [
                        'button_text'=> trans("$string_file.reject"),
                        'button_text_colour'=>"FFFFFF",
                        'button_action'=>"REJECT",
                    ],
                ];
            }
            elseif($order->order_status == 6)
            {
                $action_buttons = [
                    [
                        'button_text'=> trans("$string_file.process"),
                        'button_text_colour'=>"FFFFFF",
                        'button_action'=>"PROCESS",
                    ],$call_driver,
                ];
            }
            elseif($order->order_status == 7 && !in_array(9,$arr_completed_order))
            {
                $action_buttons = [
                    [
                        'button_text'=> trans("$string_file.process"),
                        'button_text_colour'=>"FFFFFF",
                        'button_action'=>"PROCESS",
                    ],$call_driver,
                ];
            }
            elseif($order->order_status == 7 && in_array(9,$arr_completed_order) && $order->otp_for_pickup == NULL)
            {
                $action_buttons = [$call_driver];
            }
            elseif(in_array(9,$arr_completed_order) && $order->otp_for_pickup != NULL && $order->confirmed_otp_for_pickup == 2)
            {
                $action_buttons = [
                    [
                        'button_text'=> trans("$string_file.pickup_verification"),
                        'button_text_colour'=>"FFFFFF",
                        'button_action'=>"PICKUP_VERIFICATION",
                    ],$call_driver,
                ];
            }
            array_push($action_buttons,$call_customer);
            $data = [
                'details'=> $order_details,
                'product_details'=> $product_details,
                'bill_details'=> $bill_details,
                'payment_details'=> $payment_details,
                'customer_details'=> $customer_details,
                'driver_details'=> $driver_details,
                'action_buttons'=>$action_buttons,
                'invoice_url'=>($order->is_order_completed==1)?route('business-segment.generate-invoice',['business_segment'=>$order->business_segment_id,'id'=>$order->id]):'',
            ];

        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \Exception($message);
        }
        return $data;
    }

    // get all products
    public function getProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:ALL,OUTOFSTOCK', // business_segment_id
        ]);
        $request_type = $request->type;
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try
        {
            $locale = \App::getLocale();
            $search_text = $request->search_text;
            $merchant_id = $request->merchant_id;
            $bs = $request->user('business-segment-api');
            $string_file = $this->getStringFile(NULL,$bs->Merchant);
            $id = $bs->id;
            $currency = $bs->CountryArea->Country->isoCode;

            $query = Product::select('id', 'category_id', 'business_segment_id', 'manage_inventory', 'food_type', 'product_preparation_time', 'product_cover_image','status')
                ->where([['business_segment_id', '=', $id], ['delete', '=', NULL]]);
            $query->with(['ProductVariant' => function ($qq) use ($id,$request_type) {
                $qq->select('id', 'product_id', 'weight_unit_id', 'product_price', 'weight', 'status','product_title');
                $qq->with(['ProductInventory' => function ($qq) use ($id,$request_type) {
                    $qq->select('id', 'product_variant_id', 'current_stock');
                    $qq->where('current_stock',0);
                }]);
            }]);

            if($request_type == "OUTOFSTOCK")
            {
                $query->whereHas('ProductVariant', function ($qq) use ($id, $request_type) {
                    $qq->select('id', 'product_id', 'weight_unit_id', 'product_price', 'weight', 'status');
                    $qq->whereHas('ProductInventory', function ($qq) use ($id) {
                        $qq->where('current_stock', 0);
                    });
                });
            }

            if(!empty($search_text))
            {
                $query->whereHas('LanguageProduct', function ($qq) use ($search_text,$locale,$merchant_id,$id) {
                    // $qq->where('name','like',"%$search_text%")
                    //     ->where('description','like',"%$search_text%")
                        $qq->where(function ($qqq) use ($search_text,$merchant_id,$id){
                            $qqq->where('name','like',"%$search_text%")->where('merchant_id',$merchant_id)->where('business_segment_id',$id);
                            $qqq->orWhere('sku_id','like',"%$search_text%")->where('merchant_id',$merchant_id)->where('business_segment_id',$id);
                        })
                        ->where(function ($q) use ($locale,$merchant_id,$id) {
                            $q->where('locale', $locale)->where('merchant_id',$merchant_id)->where('business_segment_id',$id);
                            $q->orWhere('locale', '!=', NULL)->where('merchant_id',$merchant_id)->where('business_segment_id',$id);
                        });
                })->orWhereHas('Category',function($q) use($search_text,$merchant_id,$id) {
                    $q->where([['delete', '=', NULL]]);
                    $q->whereHas('LangCategorySingle',function($q) use($search_text,$merchant_id,$id){
                        $q->where('name','like',"%$search_text%")->where('merchant_id',$merchant_id)->where('business_segment_id',$id);
                    });
                    
                });
            }
            $arr_products = $query->latest()->paginate(10);

            $return_data['items'] = $arr_products->map(function ($product, $key) use ($currency, $merchant_id) {
                $lang_data = $product->langData($merchant_id);
                // p($lang_data);
                $arr_sub_items = $product->ProductVariant->map(function ($first_variant, $key) use ($lang_data) {
                    $unit = !empty($first_variant->weight_unit_id) ? $first_variant->WeightUnit->WeightUnitName : "";
                    return [
                        'product_variant_id' => $first_variant->id,
                        'name' => !empty($first_variant->product_title) ? $first_variant->product_title : $lang_data->name ,
                        'product_price' => $first_variant->product_price,
                        'weight_unit' => $first_variant->weight . ' ' . $unit,
                        'stock_quantity' => isset($first_variant->ProductInventory->id) ? $first_variant->ProductInventory->current_stock : NULL,
                        'product_availability' => ($first_variant->status == 1) ? true : false,
                    ];
                });

                return [
                    'product_id' => $product->id,
                    'product_name' => $lang_data->name,
                    'product_cover_image' => get_image($product->product_cover_image, 'product_cover_image', $merchant_id),
                    'currency' => "$currency",
                    'food_type' => $product->food_type,
                    'product_description' => $lang_data->description,
                    'ingredients' => $lang_data->ingredients,
                    'manage_inventory' => $product->manage_inventory,
                    'product_availability' => ($product->status == 1) ? true : false,
                    'sub_items'=> $arr_sub_items
                ];
            });

            $products = $arr_products->toArray();
            $next_page_url = isset($products['next_page_url']) && !empty($products['next_page_url']) ? $products['next_page_url'] : "";
            $current_page = isset($products['current_page']) && !empty($products['current_page']) ? $products['current_page'] : 0;

            $return_data['current_page'] =$current_page;
            $return_data['next_page_url'] =$next_page_url;
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse(trans("$string_file.success"),$return_data);

    }

    // item add first step
    public function productBasicStep(Request $request)
    {
        $validator = Validator::make($request->all(), [

        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $id = $request->product_id;
            $product = null;
            $return_product = null;
            $bs = $request->user('business-segment-api');
            $merchant_id = $bs->merchant_id;
            // p($merchant_id);
            $segment_id = $bs->segment_id;
            $arr_category = $this->getCategory($merchant_id, $segment_id,"parent",NULL,"app");
            $sub_category = [];
            if (!empty($id)) {
                $product = Product::Find($id);
                $category = Category::select('id', 'category_parent_id')->find($product->category_id);

                if ($category->category_parent_id != 0) {

                    $parent_category = Category::select('id', 'category_parent_id')->find($category->category_parent_id);
                    $product->category_id = $parent_category->id;
                    $product->sub_category_id = $category->id;
                    $sub_category = $this->getCategory($merchant_id, $segment_id, 'child', $parent_category->id,"app");
                    // p($sub_category);
                } else {
                    $product->category_id = $category->id;
                    $product->sub_category_id = null;
                }

                $lang_data = $product->langData($product->merchant_id);
                $arr_images = [];
                if(isset($product->ProductImage) && !empty($product->ProductImage))
                {
                    foreach($product->ProductImage as $arr_product_image)
                    {
                        $arr_images [] = [
                            'id'=>$arr_product_image->id,
                            'image'=>get_image($arr_product_image->product_image,'product_image',$product->merchant_id,true,false)
                        ];
                    }
                }
                $order_created = convertTimeToUSERzone($product->created_at, $bs->CountryArea->timezone,null,$bs->Merchant,2);
                $return_product = [
                    'id'=>$product->id,
                    'sku_id'=>$product->sku_id,
                    'name'=>$lang_data->name,
                    'description'=>$lang_data->description,
                    'product_ingredients'=>$lang_data->ingredients,
                    'food_type'=>$product->food_type,
                    'category_id'=>$product->category_id,
                    'product_cover_image'=>get_image($product->product_cover_image,'product_cover_image',$product->merchant_id,true,false),
                    'product_preparation_time'=>$product->product_preparation_time,
                    'sequence'=>$product->sequence,
                    'status'=>$product->status,
                    'manage_inventory'=>$product->manage_inventory,
                    'sub_category_id'=>$product->sub_category_id,
                    'created_at'=> $order_created,
                    'arr_images'=>$arr_images,
                ];
            }

            $string_file = $this->getStringFile($merchant_id);
            $return_data = [
                'product' => $return_product,
                'arr_category' => $arr_category,
                'arr_food_type' => get_food_type($string_file,$calling_from = "app"),
                'arr_status' => get_active_status("app",$string_file),
                'product_status' => get_product_status("app",$string_file),
                'sub_category' => $sub_category,
            ];
        }
        catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }

        return $this->successResponse(trans("$string_file.success"),$return_data);
    }


    /*Save or Update*/
    public function saveProductBasicStep(Request $request)
    {
        $width = Config('custom.image_size.product.width');
        $height = Config('custom.image_size.product.height');

        $business_segment = $request->user('business-segment-api');
        $id = $request->id;
        $business_segment_id = $business_segment->id;
        $string_file = $this->getStringFile(NULL,$business_segment->Merchant);
        $merchant_id = $business_segment->merchant_id;
        $locale = \App::getLocale();
        $validator = Validator::make($request->all(), [
            'sku_id' => ['required',
                Rule::unique('products', 'sku_id')->where(function ($query) use ($merchant_id,$business_segment_id) {
                    return $query->where([['business_segment_id', '=', $business_segment_id], ['merchant_id', '=', $merchant_id]]);
                })->ignore($id)],
            'product_name' => ['required',
                Rule::unique('language_products', 'name')->where(function ($query) use ($merchant_id,$locale,$business_segment_id,$id) {
                    return $query->where([['merchant_id', '=', $merchant_id],['business_segment_id', '=', $business_segment_id], ['locale', '=', $locale]])
                        ->where('product_id','!=',$id)
                        ;
                })
            ],
            'status' => 'required',
            'product_description' => 'required',
            'product_cover_image' => 'required_if:id,!=,null',
            // 'product_image.*' => 'required_if:id,!=,null',
            'sequence' => 'required',
            'manage_inventory' => 'required',
        ],['product_image.dimensions' => 'Please upload correct dimensions image']);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        // Begin Transaction
        DB::beginTransaction();
        try {
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
//            $product->product_name = $request->product_name;
//            $product->product_description = $request->product_description;
//            $product->product_ingredients = $request->product_ingredients;
            $product->product_preparation_time = $request->product_preparation_time;
            $product->sequence = $request->sequence;
            $product->status = $request->status;
            $product->category_id = !empty($request->sub_category_id) ? $request->sub_category_id : $request->category_id;
            if(isset($request->food_type)){
                $product->food_type = $request->food_type;
            }
            $product->display_type = $request->display_type;
            $product->manage_inventory = $request->manage_inventory;
            // p($request->all());
            if (!empty($request->product_cover_image)) {
                $additional_req = ['compress' => true, 'custom_key' => 'product'];
                $product->product_cover_image = $this->uploadBase64Image('product_cover_image', 'product_cover_image', $merchant_id, 'single', $additional_req);
            }
            $product->save();
            // save language data
            $this->saveLanguageData($request,$product->merchant_id,$product);

// p($request->all());
            // if (!empty($request->product_image)) {
            //     $arr_list = json_decode($request->product_image);
            //     foreach ($arr_list as $image) {
            //         // p($image);
            //         $additional_req = ['compress' => true, 'custom_key' => 'product'];
            //         $product_image = new ProductImage;
            //         $product_image->product_id = $product->id;
            //         $product_image->product_image = $this->uploadBase64Image($image, 'product_image', $merchant_id, 'multiple', $additional_req);
            //         $product_image->save();
            //     }
            // }
            if($is_update_product_status){
                ProductVariant::where('product_id', $product->id)->update(array('status' => $request->status));
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            return $this->failedResponse($message);
            // Rollback Transaction
        }
        // Commit Transaction
        DB::commit();
        return $this->successResponse(trans("$string_file.success"),$product);
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

    /**** get product variant data ******/
    public function productVariantStep(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required', // product id
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        try {
            $id = $request->product_id;
            $business_segment = $request->user('business-segment-api');
            $string_file = $this->getStringFile(NULL,$business_segment->Merchant);
            $product['data'] = Product::select('id','sku_id','segment_id','business_segment_id','manage_inventory')->where([['delete', '=', NULL]])
                ->findOrFail($id);
            $product['data']->name = $product['data']->Name($business_segment->merchant_id);
            $product['product_variants'] = ProductVariant::select('id','product_id','sku_id','product_title','product_price','discount','weight_unit_id','weight','is_title_show','status')
//                ->with('ProductInventory')
                ->where([['delete', '=', NULL], ['product_id', '=', $id]])->get();
            $arr_weight_unit = $this->getWeightUnit($business_segment->merchant_id,"app",$business_segment->segment_id);
            $product['arr_weight_unit'] = $arr_weight_unit;
            $product['product_status'] = get_product_status("app",$string_file);

        }catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans("$string_file.success"),$product);
    }

    /**** save product variant data ******/
    public function saveProductVariantStep(Request $request)
    {
        $id = isset($request->product_variant_id) ? $request->product_variant_id : NULL; // variant id
        $product_id = isset($request->product_id) ? $request->product_id : NULL;
        $validator = Validator::make($request->all(), [
            'sku_id' => 'required|max:255',
            'title' => 'required|max:255',
            'status' => 'required',
            'price' => 'required|between:0,100000',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        // Begin Transaction
        DB::beginTransaction();
        try {
            $business_segment = $request->user('business-segment-api');
            $string_file = $this->getStringFile($business_segment->merchant_id);

            // $product_sku_diff = Product::whereHas('ProductVariant',function($q)use($request,$id){
            //     return $q->where([['sku_id','=',$request->sku_id],['id','!=',$id]]);
            // })->where([['merchant_id','=',$business_segment->merchant_id],['business_segment_id','=',$business_segment->id],['segment_id','=',$business_segment->segment_id],['id','=',$product_id]])->count();

            // if($product_sku_diff > 0){
            //     return $this->failedResponse(trans("$string_file.product_sku_error"));
            // }
            if (!empty($id)) {
                $product_variant = ProductVariant::where('product_id', $product_id)->Find($id);
            } else {
                $product_variant = new ProductVariant ();
                $product_variant->product_id = $product_id;
            }
            $product = Product::find($product_id);
            if($product->status == 2 && $request->status == 1){
//                return array('error' => trans('admin.product_variant_status_error'));
                return $this->failedResponse(trans("$string_file.product_availability_error"));
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
            if($request->weight_unit_id=='' || $request->weight_unit_id=="0"){
                $request->weight_unit_id=NULL;
            }
            $product_variant->sku_id = $request->sku_id;
            $product_variant->product_title = $request->title;
            $product_variant->product_price = $request->price;
            $product_variant->discount = $request->discount;
            $product_variant->weight_unit_id = ($request->weight_unit_id=='' || $request->weight_unit_id=="0")?NULL:$request->weight_unit_id;
            $product_variant->weight = $request->weight;
            $product_variant->status = $request->status;
            $product_variant->is_title_show = $is_title_show;
            $product_variant->save();
        } catch (\Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.success"),[]);
    }

    /**** get product inventory data ******/
    public function productInventoryStep(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required', // product variant id
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        try {
            $business_segment = $request->user('business-segment-api');
            $string_file = $this->getStringFile(NULL,$business_segment->Merchant);
            $id = $request->product_id;
//            $business_segment = $request->user('business-segment-api');
            $arr_variant = ProductVariant::with('ProductInventory')->where([['delete', '=', NULL], ['product_id', '=', $id]])->get();
            $product['product_variants'] = $arr_variant->map(function($item) {
                return [
                    'id'=>$item->id,
                    'product_id'=>$item->product_id,
                    'sku_id'=>$item->sku_id,
                    'product_title'=>$item->product_title,
                    'product_price'=>$item->product_price,
                    'discount'=>$item->discount,
                    'weight_unit_id'=>$item->weight_unit_id,
                    'weight'=>$item->weight,
                    'is_title_show'=>$item->is_title_show,
                    'status'=>$item->status,
                    'product_inventory'=> [
                        'id'=>!empty($item->ProductInventory) ? $item->ProductInventory->id : NULL,
                        'current_stock'=>!empty($item->ProductInventory) ? $item->ProductInventory->current_stock : 0,
                        'product_cost'=>!empty($item->ProductInventory) ? $item->ProductInventory->product_cost : 0,
                        'product_selling_price'=>$item->product_price,
                    ],
                ];
            });

        }catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans("$string_file.success"),$product);
    }

    /**** save product variant data ******/
    public function saveProductInventoryStep(Request $request)
    {
        $bs = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        $product_variant_id = isset($request->product_variant_id) ? $request->product_variant_id : NULL;
        $validator = Validator::make($request->all(), [
            'product_variant_id'=>'required',
            'new_stock'=>'required|between:1,100000',
            'product_cost'=>'between:0,100000',
            'product_selling_price'=>'required|between:1,100000',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        // Begin Transaction
        DB::beginTransaction();
        try {
            $product_variant = ProductVariant::findOrFail($product_variant_id);
            $product_inventory = ProductInventory::where('product_variant_id',$product_variant_id)->first();
            if (empty($product_inventory)) {
                $product_inventory = new ProductInventory();
                $product_inventory->product_variant_id = $product_variant_id;
                $product_inventory->merchant_id = $bs->merchant_id;
                $product_inventory->segment_id = $bs->segment_id;
                $product_inventory->business_segment_id = $bs->id;
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
        return $this->successResponse(trans("$string_file.success"),[]);
    }

    public function getDrivers(Request $request)
    {
        $bs = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$bs->Merchant);
        $merchant_id = $bs->merchant_id;
        $bs_id = $bs->id;
        $validator = Validator::make($request->all(), [
            'order_id' => ['required',
                Rule::exists('orders', 'id')->where(function ($query) use ($merchant_id,$bs_id) {
                    return $query->where([['order_status', '=', 1], ['merchant_id', '=', $merchant_id], ['business_segment_id', '=', $bs_id]]);
                })],
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $order_id = $request->order_id;
            $order = Order::Find($order_id);
            $request->request->add(['segment_id' => $order->segment_id,'user_id' => $order->user_id, 'service_type_id' => $order->service_type_id,'driver_vehicle_id' => $order->driver_vehicle_id,'latitude' => $bs->latitude,'longitude'=>$bs->longitude]);
            $arr_driver = Driver::getDeliveryCandidate($request);
            $arr_return['drivers'] = $arr_driver->map(function($driver) use ($merchant_id,$string_file) {
                return [
                    'id'=>$driver->id,
                    'name'=>$driver->first_name.' '.$driver->last_name,
                    'rating'=>$driver->rating,
                    'distance'=>number_format($driver->distance,3).trans("$string_file.km"),
                    'profile_image'=>get_image($driver->profile_image,'driver',$merchant_id),
                ];
            });

            $drivers = $arr_driver->toArray();
            $next_page_url = isset($drivers['next_page_url']) && !empty($drivers['next_page_url']) ? $drivers['next_page_url'] : "";
            $current_page = isset($drivers['current_page']) && !empty($drivers['current_page']) ? $drivers['current_page'] : 0;

            $arr_return['next_url'] = $next_page_url;
            $arr_return['current_page'] = $current_page;

        }catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans("$string_file.success"),$arr_return);
    }

    //send request to manual selected  drivers
    public function sendOrderRequestToDriver(Request $request)
    {
        $business_seg = $request->user('business-segment-api');
        $merchant_id = $business_seg->merchant_id;
        $string_file=$this->getStringFile($merchant_id);
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'request_type'=>'required|in:AUTO,MANUAL',
            'driver_id' => 'required_if|request_type,==,MANUAL',
        ],[
            'driver_id.required' => trans_choice("$string_file.have_to_choose", 3, ['NUM' => trans("$string_file.one"), 'OBJECT' => trans("$string_file.driver")]),
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try{
            $arr_driver_id = [];
            if($request->request_type == "MANUAL")
            {
                $arr_driver_id = $request->driver_id;
            }

            $order_id = $request->order_id;
            $order_obj  = new Order;
            $request->request->add(['id'=>$order_id]);
            $order = $order_obj->getOrderInfo($request);

            $request->request->add(['latitude'=>$business_seg->latitude,'longitude'=>$business_seg->longitude,
                'merchant_id'=>$merchant_id,'segment_id'=>$business_seg->segment_id,'arr_id'=>$arr_driver_id]);
            $message = $this->orderAcceptNotification($request,$order);

        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            return $this->failedResponse($message);
//                redirect()->back()->withErrors($message);
        }

        return $this->successResponse($message,[]);
    }

    // cancel order
    public function cancelOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $business_seg = $request->user('business-segment-api');
            $message = $this->cancelOrderByBusinessSegment($request, $business_seg);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse($message,[]);
    }

    // reject order
    public function rejectOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $business_seg = $request->user('business-segment-api');

            $merchant_id = $business_seg->merchant_id;
            $string_file = $this->getStringFile($merchant_id);

            $this->rejectOrderByBusinessSegment($request, $business_seg);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse(trans("$string_file.order_rejected_successfully"),[]);
    }

    public function orderPickupOTPVerification(Request $request)
    {
        try {
            $message = $this->orderOTPVerification($request);
        }catch (\Exception $e)
        {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse($message,[]);
    }

    public function orderCancelReason(Request $request)
    {
        $validator = Validator::make($request->all(), [
//            'segment_id' => 'required|exists:segments,id',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $bs = $request->user('business-segment-api');
            $string_file = $this->getStringFile(NULL,$bs->Merchant);
            $merchant_id = $bs->merchant_id;
            $cancelReasons = CancelReason::Reason($merchant_id, 4, $bs->segment_id);
            if (empty($cancelReasons->toArray())) {
                return $this->failedResponse(trans("$string_file.data_not_found"));
            }
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
        return $this->successResponse(trans("$string_file.success"), $cancelReasons);
    }

    public function processOrder(Request $request)
    {
        try {
            $business_seg = $request->user('business-segment-api');
            $message =  $this->orderProcessing($request,$business_seg);
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
        return $this->successResponse($message, []);
    }
    // assign order to driver
    public function autoAssignDriver(Request $request)
    {
        try {
            $business_seg = $request->user('business-segment-api');
            $order_con = new CommonOrder;
            $request->request->add(['latitude'=>$business_seg->latitude,'longitude'=>$business_seg->longitude,
                'merchant_id'=>$business_seg->merchant_id,'segment_id'=>$business_seg->segment_id]);
            $message =  $order_con->autoAssign($request,$business_seg);
            return $this->successResponse($message, []);
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
    }

    // assign order to driver
    public function manualAssignDriver(Request $request)
    {
        try {
//            $arr_driver_id = $request->driver_id;
//            $arr_driver_id = json_decode($request->driver_id,true);
            $arr_driver_id = json_decode($request->driver_id,true);
            $arr_driver_id = array_column($arr_driver_id,'id');
            $business_seg = $request->user('business-segment-api');
            $request->request->add(['latitude'=>$business_seg->latitude,'longitude'=>$business_seg->longitude,
                'merchant_id'=>$business_seg->merchant_id,'segment_id'=>$business_seg->segment_id,'arr_id'=>$arr_driver_id]);
            $order_con = new CommonOrder;
            $message =  $order_con->manualAssign($request,$business_seg);
            return $this->successResponse($message, []);
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
    }

    // get sub-category of category
    public function getSubCategories(Request $request)
    {
        try {
            $business_seg = $request->user('business-segment-api');
            $string_file = $this->getStringFile(NULL,$business_seg->Merchant);
            $sub_category = $this->getCategory($business_seg->merchant_id, $business_seg->segment_id, 'child', $request->category_id,"app");
            return $this->successResponse(trans("$string_file.success"), $sub_category);
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->failedResponse($message);
        }
    }

    // update product status
    public function updateProductStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status_for' => 'required|in:PRODUCT,VARIANT',
            'product_id' => 'required_if:status_for,==,PRODUCT',
            'product_variant_id' => 'required_if:status_for,==,VARIANT',
            'status' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $business_seg = $request->user('business-segment-api');
            $string_file = $this->getStringFile(NULL,$business_seg->Merchant);
            if($request->status_for == "PRODUCT")
            {
                $product = Product::select('status','id')->Find($request->product_id);
                $product->status = $request->status;
                $product->save();
            }
            elseif($request->status_for == "VARIANT")
            {
                $product_v = ProductVariant::select('status','id')->Find($request->product_variant_id);
                $product_v->status = $request->status;
                $product_v->save();
            }
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();
            DB::rollBack();
            return $this->failedResponse($message);
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.status_updated_successfully"),[]);
    }

    public function getOrderStatistics(Request $request){
        $business_seg = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$business_seg->Merchant);
        $id=$business_seg->id;
        $merchant_id = $business_seg->merchant_id;
        $data['business_summary'] = [];
        $segment_id = $business_seg->segment_id;
        $request->request->add(['merchant_id'=>$merchant_id,'segment_id'=>$segment_id,'id'=>$id]);
            $business_income = BookingTransaction::select(DB::raw('SUM(customer_paid_amount) as order_amount'),DB::raw('SUM(company_earning) as merchant_earning'),DB::raw('SUM(driver_earning) as driver_earning'),DB::raw('SUM(business_segment_earning) as store_earning'))
                ->with(['Order'=>function($q) use($merchant_id,$segment_id,$id) {
                    $q->where([['merchant_id','=',$merchant_id],['segment_id','=',$segment_id],['business_segment_id','=',$id]])->get();
                }])
                ->whereHas('Order',function($q) use($merchant_id,$segment_id,$id) {
                    $q->where([['merchant_id','=',$merchant_id],['segment_id','=',$segment_id],['business_segment_id','=',$id]]);
                })->where('order_id','!=',NULL)
                ->first();
            $business_orders = Order::where([['business_segment_id','=',$id]])->count();
            $currency = $business_seg->Country->isoCode;
            $data['business_summary'] = [
                'products'=> !empty($business_seg) ? $business_seg->Product->count() : '---',
                'orders'=> !empty($business_orders) ? $business_orders: '---',
                'order_amount'=> !empty($business_income)?$currency.' '.$business_income->order_amount:$currency.' '.'0',
                'merchant_earning'=>!empty($business_income)?$currency.' '.$business_income->merchant_earning:$currency.' '.'0',
                'store_earning'=>!empty($business_income)?$currency.' '.$business_income->store_earning:$currency.' '.'0'
            ];
            

        $response =[
            'response_data'=>$data
        ];
        return $this->successResponse(trans("$string_file.success"),$response);
    }
    
    public function getEarnings(Request $request){
        $business_seg = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$business_seg->Merchant);
        $id=$business_seg->id;
        $merchant_id = $business_seg->merchant_id;
        $data['business_summary'] = [];
        $segment_id = $business_seg->segment_id;
        $request->request->add(['status'=>'COMPLETED','merchant_id'=>$merchant_id,'segment_id'=>$segment_id,'business_segment_id'=>$id]);
        $order = new Order;
        $all_orders = $order->getOrders($request,true);
        
        $order_data=[];
        foreach($all_orders as $order){
            if(!empty($order->OrderTransaction))
                $transaction = $order->OrderTransaction;
             $created_at =   convertTimeToUSERzone($order->order_date, $order->CountryArea->timezone,null,$order->Merchant, 2);
            $order_data[] = [
                'order_id'=>$order->merchant_order_id,
                'store_earning'=> (!empty($transaction))?$transaction->business_segment_earning:'',
                'merchant_earning'=> (!empty($transaction))?$transaction->company_earning:'',
                'order_amount'=>$order->final_amount_paid,
                'cart_amount'=> $order->cart_amount,
                'tax'=>$order->tax,
                'delivery_charges'=>$order->delivery_amount,
                'other_charges'=>(!empty($order->tip_amount))?trans("$string_file.tip").': '.$order->tip_amount:'',
                'created_at'=>$created_at
            ];
        }
        $all_orders_arr = $all_orders->toArray();
        $next_page_url = isset($all_orders_arr['next_page_url']) && !empty($all_orders_arr['next_page_url']) ? $all_orders_arr['next_page_url'] : "";
        $current_page = isset($all_orders_arr['current_page']) && !empty($all_orders_arr['current_page']) ? $all_orders_arr['current_page'] : 0;
        
        $query = BookingTransaction::select(DB::raw('SUM(customer_paid_amount) as order_amount'),DB::raw('SUM(company_earning) as merchant_earning'),DB::raw('SUM(driver_earning) as driver_earning'),DB::raw('SUM(business_segment_earning) as store_earning'))
            ->with(['Order'=>function($q) use($request,$id){
                $q->where([['order_status','=',11],['business_segment_id','=',$id]]);
                if (!empty($request->booking_id) && $request->booking_id) {
                    $q->where('merchant_booking_id', $request->booking_id);
                }
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
                if ($request->start) {
                    $start_date = date('Y-m-d',strtotime($request->start));
                    $end_date = date('Y-m-d ',strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                }
            }])
            ->whereHas('Order',function($q) use($request,$id){
                $q->where([['order_status','=',11],['business_segment_id','=',$id]]);
                if (!empty($request->order_id) && $request->order_id) {
                    $q->where('merchant_order_id', $request->order_id);
                }
                if (!empty($request->segment_id)) {
                    $q->where('segment_id', $request->segment_id);
                }
                if ($request->start) {
                    $start_date = date('Y-m-d',strtotime($request->start));
                    $end_date = date('Y-m-d ',strtotime($request->end));
                    $q->whereBetween(DB::raw('DATE(created_at)'), [$start_date,$end_date]);
                }
            });
        $business_income = $query->first();
        
        $currency = $business_seg->Country->isoCode;
        $data['business_summary'] = [
            'total_orders'=> $all_orders->total(),
            'order_amount'=> !empty($business_income)?$currency.' '.$business_income->order_amount:$currency.' '.'0',
            'merchant_earning'=>!empty($business_income)?$currency.' '.$business_income->merchant_earning:$currency.' '.'0',
            'store_earning'=>!empty($business_income)?$currency.' '.$business_income->store_earning:$currency.' '.'0',
            'orders'=>$order_data,
            'current_page'=>$current_page,
            'next_page_url'=>$next_page_url,
            'currency'=>$currency
        ];
        $response =[
            'response_data'=>$data
        ];
        return $this->successResponse(trans("$string_file.success"),$response);
    }

    /*Save or Update products option*/
    public function optionStepSave(Request $request)
    {
        $business_seg = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$business_seg->Merchant);
        $validator = Validator::make($request->all(), [
                'arr_option' => 'required',
                'product_id' => 'required',
            ]
            ,['arr_option.required' => trans_choice("$string_file.have_to_choose", 3, ['NUM' => trans("$string_file.one"),'OBJECT' => trans("$string_file.option")])]);

        if ($validator->fails()) {
           $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        // Begin Transaction
        DB::beginTransaction();

        try {
            $id = $request->product_id;
            $arr_option = json_decode($request->arr_option,true);
            $product = Product::select('id','manage_inventory')->Find($id);
//            p($arr_option,0);
//            p($arr_option_amount);
            $product->Option()->detach();
            foreach ($arr_option as $option)
            {
                $amount =  isset($option['amount']) ? $option['amount'] : NULL;
                $product->Option()->attach($option['id'],['option_amount'=>$amount]);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            return $this->failedResponse($message);
            // Rollback Transaction
        }
        // Commit Transaction
        DB::commit();
        return $this->successResponse(trans("$string_file.added_successfully"));
    }

    /*Get product Options*/
    public function getProductOptions(Request $request){
        // p($request->all());
        $business_seg = $request->user('business-segment-api');
        $string_file = $this->getStringFile(NULL,$business_seg->Merchant);
        $id=$business_seg->id;
        $merchant_id = $business_seg->merchant_id;
        $validator = Validator::make($request->all(), [
                'product_id' => 'required',
            ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $arr_option_type = [];
        $arr_option_type =
                App\Models\OptionType:: select('id','charges_type')
                 ->with(["Option"=>function($q) use($business_seg,$id){
                     $q->addSelect('id','option_type_id')
                     ->where([['status','=',1],['business_segment_id','=',$business_seg->id],['delete','=',NULL]]);
//                     $q->with(["Product"=>function($qq) use($segment,$id){
//                         $qq->where('product_id','=',$id);
//                     }]);
                 }])
            ->whereHas("Option",function($q) use($business_seg,$id){
                $q->where([['status','=',1],['business_segment_id','=',$business_seg->id],['delete','=',NULL]]);
//                $q->whereHas("Product",function($qq) use($segment,$id){
//                    $qq->where('product_id',$id);
//                });
            })->where([['status','=',1],['merchant_id','=',$merchant_id],['delete','=',NULL]])
            ->get();
        $options=[];    
        if(!empty($arr_option_type)){
            foreach($arr_option_type as $key=>$option_type){
                $form_field_type = $option_type->charges_type == 2 ? true : false;
                $options[$key]['type']=$option_type->Type($merchant_id);
                foreach($option_type->Option as $k=>$option){
                    $amount='';
                    $checked=false;
                    foreach($option->Product as $product_pivot){
                        if(!empty($product_pivot->pivot->product_id) && ($product_pivot->pivot->product_id == $request->product_id)){
                            $amount=$product_pivot->pivot->option_amount;
                            $checked=true;
                        }
                        
                    }
                    $options[$key]['options'][$k]['id']=$option->id;
                    $options[$key]['options'][$k]['name']=$option->Name($id);
                    $options[$key]['options'][$k]['amount']=$amount;
                    $options[$key]['options'][$k]['checked']=$checked;
                    $options[$key]['options'][$k]['show_amount_field']=$form_field_type;
                }
            }
        }
        $response =[
            'response_data'=>$options
        ];
        return $this->successResponse(trans("$string_file.success"),$response);
    }
}
