<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Helper\BookingDataController;
use App\Http\Controllers\Helper\GoogleController;
use App\Http\Controllers\Helper\HolderController;
use App\Http\Controllers\Helper\Merchant;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Http\Controllers\Merchant\PriceCardController;
use App\Http\Controllers\PaymentMethods\Payment;
use App\Models\BusinessSegment\OrderDetail;
use App\Models\BusinessSegment\ProductVariant;
use App\Models\CountryArea;
use App\Models\PriceCard;
use App\Models\ProductCart;
use App\Models\UserAddress;
use App\Traits\BannerTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BusinessSegment\BusinessSegment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\BusinessSegment\Product;
use App\Models\Category;
use App\Models\PromoCode;
use App\Models\BusinessSegment\Order;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Traits\OrderTrait;
use App\Traits\ProductTrait;
use App\Traits\AreaTrait;
use App\Models\OptionType;
use DateTime;
use DateTimeZone;
use App\Models\Onesignal;
use App\Models\FavouriteBusinessSegment;

class FoodController extends Controller
{
    // get home screen data of food app
    use BannerTrait, ApiResponseTrait, OrderTrait, ProductTrait, AreaTrait;

    public function homeScreen(Request $request)
    {
        // call area trait to get id of area
        $merchant_id = $request->merchant_id;
        $segment_id = $request->segment_id;
        $is_search = $request->is_search;
        $validator = Validator::make($request->all(), [
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {
            }),],
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
            // return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        try {
            $user = $request->user('api');
            $string_file = $this->getStringFile(NULL,$user->Merchant);
            $this->getAreaByLatLong($request,$string_file);
            $distance = $user->Merchant->BookingConfiguration->store_radius_from_user;
            $google_key = $user->Merchant->BookingConfiguration->google_key;
            $request->request->add(['distance'=>$distance]);
            if ((!$request->has('page') || $request->page == 1) && $is_search != 1) {
                $request->request->add(['merchant_id' => $merchant_id, 'home_screen' => NULL, 'segment_id' => $segment_id, 'banner_for' => 1]);
                $arr_banner = $this->getMerchantBanner($request);
                $banner_res['cell_title'] = 'BANNER_CELL';
                $banner_res['cell_contents'] = $arr_banner->map(function ($item, $key) use ($merchant_id,$string_file) {

                    $return = array(
                        'id' => $item->id,
                        'business_segment_id' => $item->business_segment_id,
                        'title' => $item->banner_name,
                        'image' => get_image($item->banner_images, 'banners', $merchant_id,true,false),
                        'redirect_url' => !empty($item->redirect_url) ? $item->redirect_url : "",
                    );
                    if (!empty($item->BusinessSegment)) {
                        $m_amount = $item->BusinessSegment->minimum_amount;
                        $m_amount_for = $item->BusinessSegment->minimum_amount_for;
                        $return['name'] = $item->BusinessSegment->full_name;
                        $return['time'] = $item->BusinessSegment->delivery_time . ' ' . trans("$string_file.minute");
                        $return['amount'] = !empty($item->BusinessSegment->minimum_amount) ? "$m_amount" : "";
                        $return['amount_for'] = !empty($item->BusinessSegment->minimum_amount_for) ? "$m_amount_for" : "";
                        $return['currency'] = "₹";
                    }
                    return $return;
                });
                $request->request->add(['is_popular'=>'YES']);
                $arr_popular_restaurant = $this->getMerchantBusinessSegment($request);
//                    $this->getPopularBusinessSegment($request);
                $popular_restaurant_heading['cell_title'] = 'TITLE';
                $popular_restaurant_heading['cell_contents'][0] = ['title' => trans("$string_file.popular_brands")];

                $popular_restaurant_res['cell_title'] = 'POPULAR_BRAND_CELL';

                $popular_restaurant_res['cell_contents'] = $arr_popular_restaurant->map(function ($item, $key) use ($merchant_id,$string_file) {
                    // only setting timezone
                     date_default_timezone_set($item->CountryArea['timezone']);
                    $current_time = date('H:i');
                    $is_business_segment_open = false;
                    $current_day = date('w');
                    $arr_open_time = json_decode($item->open_time,true);
                    $arr_close_time = json_decode($item->close_time,true);
                    $open_time = isset($arr_open_time[$current_day]) ? $arr_open_time[$current_day] : NULL;
                    $close_time = isset($arr_close_time[$current_day]) ? $arr_close_time[$current_day] : NULL;
                    if ($open_time < $current_time && $close_time > $current_time) {
                        $is_business_segment_open = true;
                    } else {
                        $is_business_segment_open = false;
                    }
                    $arr_style =   $item->StyleManagement->map(function ($style) use ($merchant_id,$string_file){
                        return ['style_name' => $style->Name($merchant_id)];
                    });
                    $m_amount = $item->minimum_amount;
                    $m_amount_for = $item->minimum_amount_for;
                    return array(
                        'business_segment_id' => $item->id,
                        'title' => $item->full_name,
                        'time' => "$item->delivery_time " . trans("$string_file.minute"),
                        'amount' => !empty($item->minimum_amount) ? "$m_amount" : "",
                        'amount_for' => !empty($item->minimum_amount_for) ? "$m_amount_for" : "",
                        'currency' => $item->Country->isoCode,
                        'style' => array_pluck($arr_style,'style_name'),//array_pluck($item->StyleManagement, 'style_name'),
                        'open_time' => $open_time,
                        'close_time' => $close_time,
                        'rating' => !empty($item->rating) ? $item->rating : "2.5",
                        'image' => get_image($item->business_logo, 'business_logo', $merchant_id),
                        'is_business_segment_open' => $is_business_segment_open,
                    );
                });

                $restaurant_heading['cell_title'] = 'TITLE';
                $restaurant_heading['cell_contents'][0] = ['title' => trans("$string_file.all_restaurants")];
            }

            $request->request->add(['is_popular'=>NULL]);
            $arr_restaurant = $this->getMerchantBusinessSegment($request);
            $arr_restaurant_pg = $arr_restaurant;
            $google = new GoogleController;
            $user_lat = $request->latitude;
            $user_long = $request->longitude;
            $user = $request->user('api');
            $unit = $user->Country->distance_unit;

            $restaurant_res['cell_title'] = 'RESTRAURANT_CELL';
            $restaurant_res['cell_contents'] = $arr_restaurant->map(function ($item, $key) use ($merchant_id,$google,
                $user_lat,$user_long,$unit,$string_file,$google_key) {
                // only setting timezone
                 date_default_timezone_set($item->CountryArea['timezone']);
                $current_time = date('H:i');
                $is_business_segment_open = false;
                $current_day = date('w');
                $arr_open_time = json_decode($item->open_time,true);
                $arr_close_time = json_decode($item->close_time,true);
                $open_time = isset($arr_open_time[$current_day]) ? $arr_open_time[$current_day] : NULL;
                $close_time = isset($arr_close_time[$current_day]) ? $arr_close_time[$current_day] : NULL;

                //  Changes for midnight store time
                if($open_time > $close_time){
                    $close_time_n = date('Y-m-d H:i:s', strtotime($close_time . ' +1 day'));
                }else{
                    $close_time_n = date('Y-m-d H:i:s', strtotime($close_time));
                }
                $open_time_n = date('Y-m-d H:i:s', strtotime($open_time));
                $current_time_n = date("Y-m-d H:i:s");
                if ($open_time_n < $current_time_n && $close_time_n > $current_time_n) {
                    $is_business_segment_open = true;
                }

                $arr_style =   $item->StyleManagement->map(function ($style) use ($merchant_id,$unit,$string_file){
                    return ['style_name' => $style->Name($merchant_id)];
                });

                $store_lat = $item->latitude;
                $store_long = $item->longitude;

                // $distance_from_user = $google->arialDistance($user_lat, $user_long, $store_lat, $store_long,$unit,$string_file);
                // calculate distance from google direction api
                $user_drop_location[0] = [
                    'drop_latitude'=>$user_lat,
                    'drop_longitude'=>$user_long,
                    'drop_location'=>""
                ];
                $distance_from_user = GoogleController::GoogleStaticImageAndDistance($store_lat, $store_long, $user_drop_location, $google_key,"",$string_file);
                $distance_from_user = isset($distance_from_user['total_distance_text']) ? $distance_from_user['total_distance_text'] : "";
                return array(
                    'business_segment_id' => $item->id,
                    'title' => $item->full_name,
                    'time' => "$item->delivery_time " . trans("$string_file.minute"),
                    'amount' => !empty($item->minimum_amount) ? "$item->minimum_amount" : "",
                    'amount_for' => !empty($item->minimum_amount_for) ? "$item->minimum_amount_for" : "",
                    'currency' => $item->Country->isoCode,
                    'style' => array_pluck($arr_style,'style_name'),//array_pluck($item->StyleManagement, 'style_name'),
                    'open_time' => $open_time,
                    'close_time' => $close_time,
                    'rating' => !empty($item->rating) ? $item->rating : "2.5",
                    'distance' => trans("$string_file.distance").' '.$distance_from_user,
                    'image' => get_image($item->business_logo, 'business_logo', $merchant_id),
                    'is_business_segment_open' => $is_business_segment_open,
                );
            });

            if ((!$request->has('page') || $request->page == 1) && $is_search != 1) {
                $return_data[0] = $banner_res;
                $return_data[1] = $popular_restaurant_heading;
                $return_data[2] = $popular_restaurant_res;
                $return_data[3] = $restaurant_heading;
                $return_data[4] = $restaurant_res;

            } else {
//            $return_data[0] = $restaurant_heading;
                $return_data[0] = $restaurant_res;
            }
            $restaurant_res = $arr_restaurant_pg->toArray();
            $next_page_url = $restaurant_res['next_page_url'];
            $next_page_url = $next_page_url == "" ? "" : $next_page_url;
        } catch (\Exception $e) {
            return $this->failedResponse($e->getMessage());
        }
        return response()->json(['result' => "1", 'message' => trans("$string_file.data_found"), 'next_page_url' => $next_page_url, 'total_pages' => $restaurant_res['last_page'], 'current_page' => $restaurant_res['current_page'], 'data' => $return_data]);
    }

//    public function getPopularBusinessSegment(Request $request)
//    {
//        // call area trait to get id of area
//        // $this->getAreaByLatLong($request);
//
//        $merchant_id = $request->merchant_id;
//        $segment_id = $request->segment_id;
//        $country_area_id = $request->area;
//        $distance_unit = 1;
//        $radius = $distance_unit == 2 ? 3958.756 : 6367;
//        $distance = 50;
//        $latitude = $request->latitude;
//        $longitude = $request->longitude;
//        $arr_popular_restro = BusinessSegment::select('id', 'country_id', 'full_name', 'business_logo', 'delivery_time', 'minimum_amount', 'minimum_amount_for', 'open_time', 'close_time')
//            ->addSelect(DB::raw('( ' . $radius . ' * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( latitude ) ) ) ) AS distance'))
//            ->where([['merchant_id', '=', $merchant_id], ['segment_id', '=', $segment_id]])
////            ->having('distance', '<', $distance)
//            ->where('country_area_id', $country_area_id)
//            ->orderBy('distance')
//            ->get();
//        return $arr_popular_restro;
//    }

    // get products list of restaurant
    public function foodProducts(Request $request)
    {
        // its business segment id
        $id = $request->id;
        $merchant_id = $request->merchant_id;
        $user = $request->user('api');
        $user_id = $user->id;
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $validator = Validator::make($request->all(), [
            'id'=>'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }

        // products restaurant

        $business_segment = BusinessSegment::select('id', 'full_name','country_area_id', 'delivery_time', 'minimum_amount', 'minimum_amount_for','rating')
            ->with(['FavouriteBusinessSegment'=>function($q) use($user_id){
                $q->where([['user_id','=',$user_id]]);
            }])
            ->Find($request->id);
        $arr_style =   $business_segment->StyleManagement->map(function ($style) use ($merchant_id){
            return ['style_name' => $style->Name($merchant_id)];
        });
        $currency = $business_segment->CountryArea->Country->isoCode;

        $arr_products = $this->getProducts($request,$currency);

        $request->request->add(['business_segment_id' => $business_segment->id,'merchant_id' => $merchant_id, 'home_screen' => NULL, 'segment_id' => $business_segment->segment_id, 'banner_for' => 1]);
        $arr_banner = $this->getBusinessSegmentBanner($request);
        // Add Business segment logo if banner not exist.
        if(empty($arr_banner)){
            array_push($arr_banner,array(
                'id' => null,
                'business_segment_id' => $business_segment->id,
                'title' => $business_segment->full_name,
                'image' => get_image($business_segment->business_logo, 'business_logo', $merchant_id,true,false),
                'redirect_url' => "",

            ));
        }
        $restaurant = [
            'business_segment_id' => $business_segment->id,
            'currency' => $currency,
            'name' => $business_segment->full_name,
            'time' => $business_segment->delivery_time . " " . trans("$string_file.minute"),
            'amount' => !empty($business_segment->minimum_amount) ? "$business_segment->minimum_amount" : "",
            'amount_for' => !empty($business_segment->minimum_amount_for) ? "$business_segment->minimum_amount_for" : NULL,
            'style' => array_pluck($arr_style,'style_name'),//array_pluck($business_segment->StyleManagement, 'style_name'),
            'rating' => !empty($business_segment->rating) ? $business_segment->rating : "2.5",
            "banners" => $arr_banner,
            "is_favourite" =>!empty($business_segment->FavouriteBusinessSegment->id) ? true : false,
        ];
        $return_data['restaurant'] = $restaurant;
        $return_data['arr_product'] = $arr_products;
        return $this->successResponse(trans("$string_file.data_found"), $return_data);
    }

    public function getProducts($request,$currency = NULL)
    {
        //p('in');
        $id = $request->id;
        $merchant_id = $request->merchant_id;
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $food_type = $request->food_type; // for veg & non-veg
        $trip_calculation_method =$user->Merchant->Configuration->trip_calculation_method;
        $arr_products = Category::
        with(['Product' => function ($q) use ($id,$food_type) {
            $q->select('id', 'category_id', 'business_segment_id', 'manage_inventory', 'food_type', 'product_preparation_time', 'product_cover_image');
            $q->where([['business_segment_id', '=', $id], ['delete', '=', NULL]]);
                if(!empty($food_type))
                {
                    $q->where('food_type',$food_type);
                }
            // products_variant
            $q->with(['ProductVariant' => function ($qq) use ($id) {
                $qq->select('id', 'product_id', 'weight_unit_id', 'product_price', 'weight', 'status','is_title_show','discount');
                $qq->where([['status','=',1],['delete', '=', NULL]]);

                $qq->with(['ProductInventory' => function ($qq) use ($id) {
                    $qq->select('id', 'product_variant_id', 'current_stock');
                }]);
            }]);

            $q->whereHas('ProductVariant', function ($qq) use ($id) {
                $qq->select('id', 'product_id', 'weight_unit_id', 'product_price', 'weight', 'status','is_title_show');
                $qq->where([['status','=',1],['delete', '=', NULL]]);
            });
            $q->with(['Option'=> function ($qq) use ($id) {
            }]);
        }])
            ->whereHas('Product', function ($q) use ($id,$food_type) {
                $q->where([['business_segment_id', '=', $id], ['delete', '=', NULL],['status','=',1]]);
                if(!empty($food_type))
                {
                    $q->where('food_type',$food_type);
                }
            })
            ->select('id')
            ->where('merchant_id', $merchant_id)
            ->where('delete', NULL)
            ->where('status', 1)
            ->get();
            // p($arr_products);
        $return_data = [];
        $arr_product_variant = [];
        $arr_product_option = [];
        $merchant_helper = new Merchant();
        foreach($arr_products as $category){
            $product_data = [];
            if ($category->Product->count() > 0) {
                foreach ($category->Product as $product) {
                    $product_variants = $product->ProductVariant;
                    $arr_product_variant = [];
                    $arr_product_option = [];
                    $product_lang = $product->langData($merchant_id);
                    if ($product_variants->count() > 0) {
                        // product variant as option
                        $product_variant_data = [];
                        foreach($product_variants as $key => $product_variant){
                            if ($product->manage_inventory == 1 && empty($product_variant->ProductInventory->id)) {
                                continue;
                            }else{
                                $selected = $key == 0 ? true : false;
                                $unit = !empty($product_variant->weight_unit_id) ? $product_variant->WeightUnit->WeightUnitName : "";
                                $discounted_price = !empty($product_variant->discount) && $product_variant->discount > 0 ? ($product_variant->product_price - $product_variant->discount) : "";
                                $product_variant_data[] = [
                                    'id' => $product_variant->id, // first variant id
                                    'title' => $product_variant->is_title_show == 1 ? $product_variant->Name($merchant_id) : $product_lang->name,
                                    'price' => $merchant_helper->TripCalculation($product_variant->product_price, $merchant_id,$trip_calculation_method),
                                    'discount' => !empty($discounted_price) && $product_variant->discount > 0 ? $merchant_helper->TripCalculation($product_variant->discount,$merchant_id,$trip_calculation_method) : "",
                                    'discounted_price' =>!empty($discounted_price) ? $merchant_helper->TripCalculation($discounted_price,$merchant_id,$trip_calculation_method) : "",
                                    'product_id' => $product->id,
                                    'selected' => $selected,
                                    'weight_unit' => $product_variant->weight . ' ' . $unit,
                                    'stock_quantity' => isset($product_variant->ProductInventory->id) ? $product_variant->ProductInventory->current_stock : NULL,
                                    'product_availability' => $product_variant->status == 1 ? true : false,
                                ];
                            }
                        }
                        if(count($product_variant_data) > 1)
                        {
                            $variant_heading = [
                                'cell_title'=>trans("$string_file.size"),
                                'message'=>"",//trans_choice("$string_file.can_select_max_option", 3,  ['NUM' => trans("$string_file.one"),'OBJECT' => trans("$string_file.option")]),
                                'mandatory'=>true,
                                'minSelection'=>1,
                                'max_selection'=>10000, // unlimited
                                'cell_contents'=>$product_variant_data,
                            ];

                            array_push($arr_product_variant,$variant_heading);
                        }

                        // product option from option table
                        if(!empty($product->Option[0]) && $product->Option[0]->pivot->product_id == $product->id)
                        {
                            $product_option_type = OptionType:: select('id','charges_type','max_option_on_app','select_type')
                                ->with(["Option"=>function($q) use($id, $product){
                                    $q->addSelect('id','option_type_id')
                                        ->where([['status','=',1],['business_segment_id','=',$id]]);
                                    $q->with(["Product"=>function($qq) use($product){
                                        $qq->where('product_id',$product->id);
                                    }]);

                                }])
                                ->whereHas("Option",function($q) use($id,$product){
                                    $q->where([['status','=',1],['business_segment_id','=',$id]]);
                                    $q->whereHas("Product",function($qqq) use($product){
                                        $qqq->where('product_id',$product->id);
                                    });
                                })
                                ->where([['status','=',1],['merchant_id','=',$merchant_id]])
                                ->get();

                            $select_id = NULL;
                            foreach($product_option_type as $option_type)
                            {
                                $arr_option = [];
                                foreach($option_type->Option as $key=> $option)
                                {
                                    if(isset($option->Product[0]) && $option->Product[0]->pivot->product_id ==$product->id )
                                    {
                                    $arr_option[] = [
                                        'id'=>$option->id,
                                        'title'=>$option->Name($id),
//                                        'selected' => $option_type->select_type == 2 && $key == 0 ? true : false,
                                        'selected' => false,
                                        'price'=>isset($option->Product[0]) && !empty($option->Product[0]->pivot->option_amount) ? $option->Product[0]->pivot->option_amount : "",
                                    ];
                                    }
                                }
                                if(count($arr_option) > 0)
                                {
                                    $option_heading = [
                                        'cell_title'=>$option_type->Type($merchant_id),
                                        'message'=>trans_choice("$string_file.can_select_max_option", 3,  ['NUM' => $option_type->max_option_on_app,'OBJECT' => trans("$string_file.option")]),
                                        'mandatory'=>$option_type->select_type == 2 ? true : false,
                                        'max_selection'=>!empty($option_type->max_option_on_app) ? $option_type->max_option_on_app : 0,
                                        'cell_contents'=>$arr_option,
                                    ];
                                    array_push($arr_product_option,$option_heading);
                                }
                            }
                        }
                        // product details
                        $first_variant = isset($product_variants[0]) ? $product_variants[0]  : NULL;
                        // p($first_variant);
                        if ($product->manage_inventory == 1 && empty($first_variant->ProductInventory->id)) {
                            continue;
                        }else{
                            $unit = !empty($first_variant->weight_unit_id) ? $first_variant->WeightUnit->WeightUnitName : "";
                            $discounted_price = !empty($first_variant->discount) && $first_variant->discount > 0 ? ($first_variant->product_price - $first_variant->discount) : "";
                            $product_data[] = [
                                'id' => $first_variant->id, // first variant id
                                'product_id' => $product->id,
                                'product_name' => $product_lang->name,
//                                'product_name' => $product_variant->is_title_show == 1 ? $product_variant->Name($merchant_id) : $product_lang->name,
                                'product_cover_image' => !empty($product->product_cover_image) ? get_image($product->product_cover_image, 'product_cover_image', $merchant_id) : "",
                                'currency' => "$currency",
                                'product_price' => (string)$merchant_helper->TripCalculation($first_variant->product_price, $merchant_id,$trip_calculation_method),
                                'discount' =>!empty($first_variant->discount) && $first_variant->discount > 0 ? $merchant_helper->TripCalculation($first_variant->discount,$merchant_id,$trip_calculation_method) : "",
                                'discounted_price' => !empty($discounted_price) ? (string)$merchant_helper->TripCalculation($discounted_price,$merchant_id,$trip_calculation_method): "",
                                'food_type' => $product->food_type,
                                'product_description' => !empty($product_lang->description) ? $product_lang->description : "",
                                'ingredients' => !empty($product_lang->ingredients) ? $product_lang->ingredients : "",
                                'weight_unit' => $first_variant->weight . ' ' . $unit,
                                'manage_inventory' => $product->manage_inventory,
                                'stock_quantity' => isset($first_variant->ProductInventory->id) ? $first_variant->ProductInventory->current_stock : NULL,
                                'product_availability' => $first_variant->status == 1 ? true : false,
                                'arr_variant' => $arr_product_variant,
                                'arr_option' =>$arr_product_option
                            ];
                        }

                    }
                }
            }
            $return_data[] = [
                'id' => $category->id,
                'category_name' => $category->Name($merchant_id),
                'product' => $product_data,
            ];
        }
//
        return $return_data;
        // return $arr_products;
    }

    public function saveProductCart(Request $request)
    {
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $request_fields = [
            'segment_id' => "required_if:product_update,==,NO",
            'longitude' => 'required_if:product_update,==,NO',
            'latitude' => 'required_if:product_update,==,NO',
            'product_update' => 'required|in:YES,NO',
            'product_details' => 'required_if:product_update,==,NO',
            'cart_id' => 'required_if:product_update,==,YES',
            'product_variant_id' => 'required_if:product_update,==,YES',
            'quantity' => 'required_if:product_update,==,YES',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            $merchant_id = $request->merchant_id;
            // call area trait to get id of area
            $this->getAreaByLatLong($request,$string_file);
            $id = $request->cart_id; // product cart/checkout table
            if ($request->product_update == "YES" && !empty($id)) {
                $product_variant_id = $request->product_variant_id;
                $product_quantity = $request->quantity;
                $product_cart = ProductCart::where('id', $id)->first();
                if (empty($product_cart->id)) {
                    $string_file = $this->getStringFile($merchant_id);
                    throw new \Exception(trans("$string_file.cart_not_found"));
                }
                $product_details = $product_cart->product_details;
                $product_details = json_decode($product_details, true);
                $updated_products = [];
                foreach ($product_details as $product) {
                    // p($product);
                    $quantity = $product['product_variant_id'] == $product_variant_id ? $product_quantity : $product['quantity'];
                    // $updated_products[] = ['product_variant_id' => $product['product_variant_id'], 'quantity' => $quantity];
                    $updated_list =  ['product_variant_id' => $product['product_variant_id'], 'quantity' => $quantity];
                    if(isset($product['options']) && !empty($product['options']))
                    {
                        $updated_list['options'] =$product['options'];
                    }
                    $updated_products[] = $updated_list;
                }
                // p($updated_products);
                $product_cart->product_details = json_encode($updated_products);
            } else {

                $segment_id = $request->segment_id;
                $service_type_id = $this->getSegmentService($segment_id, $merchant_id, 'id');
                $country_area_id = $request->area;
                // price card to check delivery charges of user
                $price_card = PriceCard::where([['status', '=', 1], ['country_area_id', '=', $country_area_id], ['merchant_id', '=', $merchant_id], ['service_type_id', '=', $service_type_id], ['segment_id', '=', $segment_id],['price_card_for','=',2]])->first();
                if (empty($price_card)) {
                    return $this->failedResponse(trans("$string_file.no_price_card_for_area"));
                }
                $segment_id = $request->segment_id;
                $merchant_id = $request->merchant_id;
                $user_id = $user->id;
                $product_cart = ProductCart::where('id', $id)->orWhere(function ($q) use ($user_id, $segment_id, $merchant_id) {
                    $q->where([['user_id', '=', $user_id], ['merchant_id', '=', $merchant_id], ['segment_id', '=', $segment_id]]);
                })->first();

                if (empty($product_cart->id)) {
                    $product_cart = new ProductCart;
                    $product_cart->user_id = $user_id;
                    $product_cart->merchant_id = $request->merchant_id;
                    $product_cart->segment_id = $request->segment_id;
                }
                // save cart data
                $product_cart->product_details = $request->product_details;
                $product_cart->price_card_id = $price_card->id;
            }
            // return cart data
            $calling_from = "save_cart";
            $product_cart->save();
            $product_cart->area = $request->area;
            $return_cart = $this->getCartData($product_cart, false, "", $calling_from,$request,$string_file);


        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            DB::rollback();
            return $this->failedResponse($message);
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.success"), $return_cart);
    }

    public function getProductCart(Request $request)
    {
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $request_fields = [
            'cart_id' => ['required', 'integer', Rule::exists('product_carts', 'id')->where(function ($query) {
            }),],
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $id = $request->cart_id; // product cart/checkout table
        // return cart data
        $return_cart = $this->getCartData($id, true,NULL,"",$request,$string_file);
        return $this->successResponse(trans("$string_file.data_found"), $return_cart);
    }

    public function deleteCart(Request $request)
    {
        $request_fields = [
            'delete_type' => 'required|in:CART,PRODUCT',
            'cart_id' => 'required',
            'product_variant_id' => 'required_if:delete_type,==,PRODUCT',
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $merchant_id = $request->merchant_id;
            $string_file=$this->getStringFile($merchant_id);
            $id = $request->cart_id; // product cart/checkout table
            $product_cart = ProductCart::where('id', $id)->first();
            // p($product_cart);
            if ($request->delete_type == 'CART') {
                $product_cart->delete();
                return $this->successResponse(trans("$string_file.cart_deleted"));
            } else {
                $product_id = $request->product_variant_id;
                $product_details = $product_cart->product_details;
                $product_details = json_decode($product_details, true);
                $updated_products = [];
                foreach ($product_details as $product) {
                    if ($product['product_variant_id'] != $product_id) {

                        $updated_list =  ['product_variant_id' => $product['product_variant_id'], 'quantity' => $product['quantity']];
                        if(isset($product['options']) && !empty($product['options']))
                        {
                            $updated_list['options'] =$product['options'];
                        }
                        $updated_products[] = $updated_list;
                        // $updated_products[] = ['product_variant_id' => $product['product_variant_id'], 'quantity' => $product['quantity'],'options'=>$product['options']];
                        // $updated_products[] = ['product_variant_id' => $product['product_variant_id'], 'quantity' => $product['quantity']];
                    }
                }
                if(count($updated_products) == 0)
                {
                     $product_cart->delete();
                     return $this->successResponse(trans("$string_file.cart_deleted"));
                }
                $product_cart->product_details = json_encode($updated_products);
                $product_cart->save();
            }
           
            // return cart data
            $return_cart = $this->getCartData($product_cart, false,"","delete_cart",$request,$string_file);
            return $this->successResponse(trans("$string_file.cart_product_deleted"), $return_cart);
        }catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }

    }

    public function getCartData($product_cart, $find_by_cart_id = true, $promo_code = null, $calling_from = "",$request = NULL,$string_file = "")
    {
        try {
            $area_id = isset($product_cart->area) ? $product_cart->area : NULL;
            $area_id = ($area_id == NULL && $request->area) ? $request->area : $area_id;
            if(isset($product_cart['area'])){
                unset($product_cart['area']);
            }
            if ($find_by_cart_id == true) {
                $product_cart = ProductCart::Find($product_cart);
            }
            $merchant_helper = new Merchant();
            $trip_calculation_method =$product_cart->Merchant->Configuration->trip_calculation_method;
            $arr_cart_product = json_decode($product_cart->product_details, true);
            // p($arr_cart_product);
            $arr_variant = array_column($arr_cart_product,NULL,'product_variant_id');
            $arr_product_id = array_column($arr_cart_product, 'product_variant_id');
            //p($arr_cart_product);
            $arr_cart_product_list = array_column($arr_cart_product, NULL, 'product_variant_id');
            $arr_product_list = ProductVariant::select('id', 'product_id', 'product_price', 'weight', 'weight_unit_id','is_title_show','discount')
                ->whereIn('id', $arr_product_id)->where([['status', '=', 1], ['delete', '=', NULL]])
                ->with(['Product' => function ($q) {
                    $q->select('id', 'category_id','merchant_id', 'manage_inventory', 'tax', 'product_cover_image', 'food_type', 'business_segment_id');
                    $q->with(['Option'=>function($q){
                    }]);
                }])
                ->with(['WeightUnit' => function ($q) {
                    $q->select('id');
                }])
                ->with(['ProductInventory' => function ($q) {
                    $q->select('id', 'product_variant_id', 'current_stock');
                }])
                ->get();

            $total_cart_quantity = 0;
            $total_cart_amount = 0;
            $total_tax_amount = 0;
            $business_segment_id = NULL;
            $arr_cart_product = [];
            $product_data = [];
            if(isset($area_id) && !empty($area_id)){
                $country_area = CountryArea::find($area_id);
                $currency = $country_area->Country->isoCode;
            }else{
                $currency = $product_cart->User->Country->isoCode;
            }
            // p($arr_product_list);
            $cart_out_of_stock = false; // overall cart status like out of stock or not
            $total_product_discount = 0;
            foreach ($arr_product_list as $key => $product) {
                $merchant_id = $product->Product->merchant_id;
                $arr_return_option = [];
                $arr_option_amount = [];
                $business_segment_id = $product->Product->business_segment_id;
                $arr_options = isset($arr_variant[$product->id]['options']) ? $arr_variant[$product->id]['options'] : [];
                if(!empty($arr_options))
                {
                    $arr_cart_option = $product->Product->Option;
                    foreach($arr_cart_option as $option)
                    {
                        if(in_array($option->id,$arr_options))
                        {
                            $arr_return_option [] = [
                                'id'=> $option->id,
                                'option_name'=> $option->Name($business_segment_id),
                                'amount'=> $option->pivot->option_amount,
                            ];
                            $arr_option_amount[] = $option->pivot->option_amount;
                        }
                    }
                }
                $product_option_amount = array_sum($arr_option_amount);
                $product_price = $product->product_price;
                $product_discount = (!empty($product->discount)  && $product->discount > 0) ? $product->discount : NULL;
//            $product_tax = $product->Product->tax;

                $quantity = (int)$arr_cart_product_list[$product->id]['quantity'];

                $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";

                // check item stock
                $manage_inventory = $product->Product->manage_inventory;

                $item_out_of_stock = false;
                $current_stock = 0;
                if ($manage_inventory == 1 && !empty($product->ProductInventory->id)) {
                    $current_stock = $product->ProductInventory->current_stock;
                    $item_out_of_stock = $current_stock < $quantity ? true : false;
                    if ($item_out_of_stock == true) {
                        $cart_out_of_stock = $item_out_of_stock;
                    }
                }
                $total_product_discount = $total_product_discount + $product_discount;
                $product_total_price = (($product_price - $product_discount) + $product_option_amount) * $quantity;
                $product_data['product_id'] = $product->product_id;
                $product_data['weight_unit_id'] = $product->weight_unit_id;
                $product_data['product_variant_id'] = $product->id;
                $product_data['food_type'] = $product->Product->food_type;
                $product_data['quantity'] = $quantity;
                $product_data['current_stock'] = $current_stock;
                $product_data['manage_inventory'] = $manage_inventory;
                $product_data['currency'] = "$currency";
                $product_data['item_out_of_stock'] = $item_out_of_stock;
                $product_data['product_price'] = $merchant_helper->TripCalculation($product_price,$merchant_id,$trip_calculation_method);
                $product_data['discount'] = !empty($product_discount) ? $merchant_helper->TripCalculation($product_discount,$merchant_id,$trip_calculation_method) : "";
//              $product_data['tax'] = $product_tax;
                $discounted_price =!empty($product_discount) && $product_discount > 0 ? ($product_price -$product_discount) : "";
                $product_data['discounted_price'] = !empty($discounted_price) ? $merchant_helper->TripCalculation($discounted_price,$merchant_id,$trip_calculation_method) : "";
                $product_data['total_price'] = $merchant_helper->TripCalculation($product_total_price,$merchant_id,$trip_calculation_method);
//            $product_tax_amount = $product_total_price * $product_tax / 100;
//            $product_data['tax_amount'] = $product_tax_amount;
                $product_data['weight_unit'] = $product->weight . ' ' . $unit;
                $product_data['product_name'] = $product->Product->Name($merchant_id);

                $product_data['variant_title_heading'] = $product->is_title_show == 1 ? trans("$string_file.size") : "";
                $product_data['variant_title'] = $product->is_title_show == 1 ? $product->Name($merchant_id) : "";

                $product_data['product_cover_image'] = !empty($product->Product->product_cover_image) ? get_image($product->Product->product_cover_image, 'product_cover_image', $product_cart->merchant_id, true) : "";
                $product_data['arr_option'] = $arr_return_option;
                $arr_cart_product[] = $product_data;
                $total_cart_quantity += $quantity;
                $total_cart_amount += $product_total_price;
//            $total_tax_amount += $product_tax_amount;
                //p($product_data);
            }

            $discount_amount = 0;
            $promocode = "";
            if (!empty($promo_code->id)) {
                $promo_details = $promo_code;
                // flat discount promo_code_value_type ==1
                $discount_amount = $promo_details->promo_code_value;
                if ($promo_details->promo_code_value_type == 2) {
                    // percentage discount promo_code_value_type == 2
                    $promoMaxAmount = $promo_details->promo_percentage_maximum_discount;
                    $discount_amount = ($total_cart_amount * $discount_amount) / 100;
                    $discount_amount = !empty($promoMaxAmount) && ($discount_amount > $promoMaxAmount) ? $promoMaxAmount : $discount_amount;
                }
                $promocode = $promo_code->promoCode;
            }
            // p($business_segment_id);
            $product_cart->business_segment_id = $business_segment_id;
            $product_cart->cart_amount = $total_cart_amount;
//            if ($calling_from == "save_cart") {
                // just update the business segment id
                $product_cart->save();
//            }

            //drop_distance_from_restaurant
            $delivery_charge = 0;
            $price_card_detail_id = NULL;
            if($calling_from !="delete_cart")
            {
                // in case of demo user order_pickup point will be same as user current location
                if($product_cart->User->login_type == 1)
                {
                    $from = $request->latitude . ',' . $request->longitude;
                }
                else{
                    $pickup_lat = $product_cart->BusinessSegment->latitude;
                    $pickup_long = $product_cart->BusinessSegment->longitude;
                    $from = $pickup_lat . ',' . $pickup_long;
                }

                $to = $request->latitude . ',' . $request->longitude;
                $google_key = $product_cart->Merchant->BookingConfiguration->google_key;
                $units = ($product_cart->BusinessSegment->CountryArea->Country['distance_unit'] == 1) ? 'metric' : 'imperial';
                $google_result = GoogleController::GoogleDistanceAndTime($from, $to, $google_key, $units,false,'foodCart',$string_file);
                // distance in meter
                $user_distance = isset($google_result['distance_in_meter']) ? $google_result['distance_in_meter'] : NULL;
                // distance in km
                $product_cart->estimate_distance = isset($google_result['distance']) ? $google_result['distance'] : NULL;
                $delivery_charge_slabs = $product_cart->PriceCard->PriceCardDetail->where('status',1)->toArray();
                if(!empty($user_distance))
                {
                    $request->request->add(['for'=>2,'distance'=>$user_distance,'cart_amount'=>$total_cart_amount]);
                    $slab = $this->getDistanceSlab($request,$delivery_charge_slabs);
                    if(isset($slab['id']) && isset($slab['slab_amount']))
                    {
                        $delivery_charge = $slab['slab_amount'];
                        $price_card_detail_id = $slab['id'];
                    }
                }
                $product_cart->delivery_charge = $delivery_charge;
                $product_cart->save();
                // just for bill details
                $product_cart->price_card_detail_id = $price_card_detail_id;
            }
            else
            {
                $delivery_charge = $product_cart->delivery_charge;
            }

            $product_cart->tip_status = $product_cart->Merchant->ApplicationConfiguration->tip_status == 1 ? true : false;
            $product_cart->cart_out_of_stock = $cart_out_of_stock;
            $product_cart->promoCode = $promocode;

            $tax_per = $product_cart->PriceCard->tax;
            if($tax_per > 0)
            {
                $total_tax_amount = ($total_cart_amount * $tax_per/100);
            }

            
            $final = ($total_cart_amount - $discount_amount) + $total_tax_amount + $delivery_charge;


            $merchant_cart_commission_amount = 0;
            $business_segment = BusinessSegment ::select('commission_method','commission')->find($business_segment_id);
            $commission = $business_segment->commission;
            if($business_segment->commission_method == 1){
                if($total_cart_amount >= $commission){
                    $merchant_cart_commission_amount = $commission;
                }else{
                    $merchant_cart_commission_amount = $total_cart_amount;
                }
            }elseif($business_segment->commission_method == 2){
                // Percentage Commission
                $merchant_cart_commission_amount = ($commission * $total_cart_amount) / 100;
            }

            $product_cart->receipt = [
                'currency' => $currency,
                'quantity' => $total_cart_quantity,
                'total_amount' =>$merchant_helper->TripCalculation($total_cart_amount,$merchant_id,$trip_calculation_method),
                'discount_amount' => $merchant_helper->TripCalculation($discount_amount,$merchant_id,$trip_calculation_method),
                'tax_amount' => $merchant_helper->TripCalculation($total_tax_amount,$merchant_id,$trip_calculation_method),
                'delivery_charge' =>$merchant_helper->TripCalculation($delivery_charge,$merchant_id,$trip_calculation_method),
                'final_amount' => $merchant_helper->TripCalculation($final,$merchant_id,$trip_calculation_method),
                'application_fee' => $merchant_helper->TripCalculation($merchant_cart_commission_amount,$merchant_id,$trip_calculation_method), // commission of merchant for
                // mercado
            ];
            $cancel_minutes = 0;
            $cancel_charges = 0;
            $order_cancel_status = false;
            if(!empty($product_cart->price_card_id) && $product_cart->PriceCard->cancel_charges == 1){
                $order_cancel_status = true;
                $cancel_minutes = $product_cart->PriceCard->cancel_time;
                $cancel_charges = $product_cart->PriceCard->cancel_amount;
            }
            $product_cart->cancel_text = [
                'cancel_order' => $order_cancel_status,
                'header' => trans("$string_file.cancel_text_header"),
                'body' => trans("$string_file.order_cancel_warning",["time" =>$cancel_minutes, "amount" => $cancel_charges])
            ];
            $product_cart->product_details = $arr_cart_product;
            $paymentMethods = $product_cart->PriceCard->CountryArea->PaymentMethod;
            $bookingData = new BookingDataController();
            $options = $bookingData->PaymentOption($paymentMethods, $product_cart->user_id, null, $product_cart->PriceCard->minimum_wallet_amount);
            $product_cart->payment_method = $options;
            unset($product_cart->PriceCard);
            unset($product_cart->User);
            unset($product_cart->BusinessSegment);
            unset($product_cart->Merchant);
            unset($product_cart->created_at);
            unset($product_cart->updated_at);
            unset($product_cart->Segment);
            //p($product_cart);
            return $product_cart;

        }catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    public function placeOrder(Request $request)
    {
        $user = $request->user('api');
        $request_fields = [
            'cart_id' => ['required', 'integer', Rule::exists('product_carts', 'id')->where(function ($query) {
            }),],
            'payment_method_id' => ['required', 'integer', Rule::exists('payment_methods', 'id')->where(function ($query) {
            }),],
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {
            }),],
            'address' => 'required',
            'card_id' => 'required_if:payment_method_id,=,2',
//            'payment_status' => 'required',
        ];

        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            // call area trait to get id of area
            $string_file = $this->getStringFile($user->merchant_id);
            $this->getAreaByLatLong($request,$string_file);
            $promocode = $request->promo_code;
            $promo_code_id = NULL;
            if (!empty($request->promo_code)) {
                $commont_controller = new CommonController();
                $check_promo_code = $commont_controller->checkPromoCode($request);
                if (isset($check_promo_code['status']) && $check_promo_code['status'] == true) {
                    $promocode = $check_promo_code['promo_code'];
                    $promo_code_id = $promocode->id;
                } else {
                    return $check_promo_code;
                }
            }
            //$return_cart = $this->getCartData($request->cart_id, true, "", $promocode,$request);
            $return_cart = $this->getCartData($request->cart_id, true, $promocode, "",$request,$string_file);
            // if cart status is out of stock then return cart response in place order api
            if ($return_cart->cart_out_of_stock == true) {

                return $this->successResponse(trans("$string_file.cart_out_of_stock"), $return_cart);
            }

            $service_type_id = $this->getSegmentService($return_cart->segment_id, $request->merchant_id, 'id');

            // Check driver pricecard is exist or not
            $price_card_object = new PriceCardController();
            $check_for_driver = $price_card_object->checkFoodGroceryPriceCard("DRIVER",$request->merchant_id, $return_cart->segment_id, $request->area,$service_type_id);
            if(!$check_for_driver){
                throw new \Exception(trans("$string_file.driver")." ".trans("$string_file.price_card")." ".trans("$string_file.data_not_found"));
            }

            $order = new Order;
            $order->merchant_id = $request->merchant_id;
            $order->card_id = $request->card_id;
            $order->user_id = $user->id;
            $order->segment_id = $return_cart->segment_id;
            $order->order_status = 1; //order placed
            $order->country_area_id = $request->area;
            $order->payment_option_id = $request->payment_option_id;
            $order->payment_status = !empty($request->payment_status) ? $request->payment_status : 2;
            $order->service_type_id = $service_type_id;
            $cart_amount = $return_cart['receipt'];

            $final_amount = $cart_amount['final_amount'];
            if($request->payment_method_id == 3)
            {
                $common_controller = new CommonController;
                $common_controller->checkUserWallet($user,$final_amount);
            }

            // Set Promocode in order table
            $order->promo_code_id = $promo_code_id;
            $order->cart_amount = $cart_amount['total_amount'];
            $order->discount_amount = $cart_amount['discount_amount'];
            $order->tax = $cart_amount['tax_amount'];
            $order->tip_amount = $request->tip_amount;
            // tip will be added in total amount
            $order->final_amount_paid = $final_amount + $request->tip_amount;
            $order->delivery_amount = $cart_amount['delivery_charge'];

            $order->payment_method_id = $request->payment_method_id;
            $order->business_segment_id = $return_cart->business_segment_id;

            // for user card
            $order->price_card_id = $return_cart->price_card_id;
            $arr['user'] = ['price_card_detail_id'=>$return_cart->price_card_detail_id,'slab_amount'=>$cart_amount['final_amount'],'distance'=>$return_cart->estimate_distance];
            $arr['driver'] = [];
            $order->bill_details = json_encode($arr);

            $order->drop_latitude = $request->latitude;
            $order->drop_longitude = $request->longitude;
            $order->user_address_id = $request->user_address_id;
            // we should store drop location if get address id
            $drop_location = $request->address;
            if (empty($request->address)) {
                $drop_location = "";
                if (!empty($request->user_address_id)) {
                    $user_address = UserAddress::Find($request->user_address_id);
                    $drop_location = $user_address->house_name . ',' . $user_address->building . ',' . $user_address->address;
                }
            }
            $order->drop_location = $drop_location;
            $order->additional_notes = $request->additional_notes;
            $order->estimate_amount = 0;
            $order->order_timestamp = time();
            $order->order_date = date('Y-m-d');
            $order->quantity = $cart_amount['quantity'];

            // will do later, its bill details
            //$parameter[] = array('parameter' => "", 'parameterType' => "", 'amount' => "");
            $order->save();
            $this->saveOrderStatusHistory($request,$order);
            $arr_ordered_product = $return_cart->product_details;
            foreach ($arr_ordered_product as $product) {
                $product_obj = new OrderDetail;
                $product_obj->order_id = $order->id;
                $product_obj->product_id = $product['product_id'];
                $product_obj->weight_unit_id = $product['weight_unit_id'];
                $product_obj->product_variant_id = $product['product_variant_id'];
                $product_obj->options = isset($product['arr_option']) && !empty($product['arr_option']) ? json_encode($product['arr_option']) : NULL;
                $product_obj->quantity = $product['quantity'];
                $product_obj->price = $product['product_price'];
                $product_obj->discount = 0;
//                $product_obj->tax = $product['tax'];
//                $product_obj->tax_amount = $product['tax_amount'];
                $product_obj->total_amount = $product['total_price'];
                $product_obj->save();

                // manage product inventory
                if ($product['manage_inventory'] == 1) {
                    $request->request->add([
                        'order_id' => $product_obj->order_id,
                        'product_id' => $product_obj->product_id,
                        'id' => $product_obj->product_variant_id,
                        'new_stock' => $product_obj->quantity,
                        'stock_type' => 2,
                        'stock_out_id' => $product_obj->id,
                    ]);
                    $this->manageProductVariantInventory($request);
                }
            }

            // In case of Non-Cash payment method, do payment first
            if($request->payment_method_id != 1){
                $payment = new Payment();
                $array_param = array(
                    'order_id' => $order->id,
                    'payment_option_id' => $order->payment_option_id,
                    'payment_method_id' => $order->payment_method_id,
                    'amount' => $order->final_amount_paid,
                    'user_id' => $order->user_id,
                    'card_id' => $order->card_id,
                    'currency' => $order->User->Country->isoCode,
                    'quantity' => $order->quantity,
                    'order_name' => $order->merchant_order_id,
                    'ewallet_user_otp_pin' => $request->otp_pin, // for amole payment gateway
                    'ewallet_pin_expire' => $request->pin_expire_date,
                    'phone_card_no' => $request->phone_card_no,
                );
              $payment_status = $payment->MakePayment($array_param);
                // payment done successfully
                if($payment_status){
                    $order->payment_status = 1; // means payment done while order place
                    $order->save();
                }
            }

            $success_message = "";
            // send notification to driver is configuration is set to direct driver
            $product_cart = ProductCart::Find($request->cart_id);
            $business_seg = BusinessSegment::select('id', 'order_request_receiver', 'segment_id', 'merchant_id', 'latitude', 'longitude','delivery_service')->Find($product_cart->business_segment_id);
            $arr_agency_id = []; // we can check
            $delivery_service = $business_seg->delivery_service;
            if($delivery_service == 2)
            {
                $arr_agency_id = $business_seg->DriverAgency->pluck('id')->toArray();
            }
            if (!empty($business_seg->order_request_receiver) && $business_seg->order_request_receiver == 2) {
                $request->request->add(['latitude' => $business_seg->latitude, 'longitude' => $business_seg->longitude,
                    'merchant_id' => $business_seg->merchant_id, 'segment_id' => $business_seg->segment_id,'arr_agency_id'=>$arr_agency_id]);
                $this->orderAcceptNotification($request, $order);

                $success_message = trans("$string_file.order_placed");
            }
            else
            {
                $success_message = trans("$string_file.later_order_placed");
            }
//            else
//            {
                $this->sendPushNotificationToWeb($request,$order);
//            }
            // delete cart
            $product_cart->delete();

            // Send mail to merchant as well as to restro
            $this->sendNewOrderMail($order);

            //send onesignal message to restro
            $data = array('order_id' => $order->id,'order_number' => $order->merchant_order_id, 'notification_type' => 'ORDER_PLACED', 'segment_type' => $order->Segment->slag,'order_number'=>$order->merchant_order_id);
            $arr_param = array(
                'business_segment_id' => $order->business_segment_id,
                'data'=>$data,
                'message'=>trans("$string_file.new_order_driver_message"),
                'merchant_id'=>$order->merchant_id,
                'title' => trans("$string_file.order_placed_title")
            );
            Onesignal::BusinessSegmentPushMessage($arr_param);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            DB::rollback();
            return $this->failedResponse($message);
        }
        DB::commit();
        $data = ['order_id' => $order->id, 'order_status' => $order->order_status];
        return $this->successResponse($success_message, $data);
    }

    public function getOrders(Request $request)
    {
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $merchant_id = $user->merchant_id;
        $req_param['merchant_id'] = $merchant_id;
        $config_status = $this->getOrderStatus($req_param);
        $currency = $user->Country->isoCode;
        $request_fields = [
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {
            }),],
            'type' => 'required', // 1 for schedule 2 ongoing 3 for past and rejected
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $segment_id = $request->segment_id;
            $user_id = $user->id;
            $order_status = [];
            if ($request->type == 2) {
                $order_status = [1, 6, 7, 9, 10];
            } elseif ($request->type == 3) {
                $order_status = [11, 3, 2];
            }
            $orders = Order::select('business_segment_id','country_area_id', 'merchant_id', 'id', 'merchant_order_id', 'payment_method_id', 'final_amount_paid', 'created_at', 'order_status', 'quantity')
                ->with(['BusinessSegment' => function ($q) {
                    $q->addSelect('id', 'full_name', 'business_logo', 'address');
                }])
                ->with(['PaymentMethod' => function ($q) {
                    $q->addSelect('id', 'payment_method');
                }])
                ->whereIn('order_status', $order_status)
                ->where([['segment_id', '=', $segment_id], ['user_id', '=', $user_id]])
                ->orderBy('created_at','DESC')
                ->get();

            $orders = $orders->map(function ($order, $key) use ($currency,$config_status) {
                $merchant_id = $order->merchant_id;
                $date = new DateTime($order->created_at);
                $date->setTimezone(new DateTimeZone($order->CountryArea->timezone));
                return [
                    'order_id' => $order->id,
                    'restaurant_name' => $order->BusinessSegment->full_name,
                    'restaurant_address' => $order->BusinessSegment->address,
                    'restaurant_logo' => get_image($order->BusinessSegment->business_logo,'business_logo',$merchant_id),
                    'order_date' => $date->format('H:i D, d-m-Y'),
                    'total_items' => $order->quantity,
                    'currency' => "$currency",
                    'total_amount' => $order->final_amount_paid,
                    'order_status' => $config_status[$order->order_status],
//                    'product_data' => $product_data,
                ];
            });
            return $this->successResponse(trans("$string_file.data_found"), $orders);
        }catch (\Exception $e)
        {
            return $this->failedResponse($e->getMessage());
        }
    }

    public function getOrderDetails(Request $request)
    {
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL,$user->Merchant);
        $merchant_id = $user->merchant_id;
        $req_param['merchant_id'] = $merchant_id;
        $config_status = $this->getOrderStatus($req_param);
        $currency = $user->Country->isoCode;
        $request_fields = [
            'order_id' => ['required', 'integer', Rule::exists('orders', 'id')->where(function ($query) {
            })],
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        try {
            $order = Order::select('business_segment_id','country_area_id','price_card_id', 'merchant_id', 'id', 'merchant_order_id', 'drop_location', 'user_address_id', 'payment_method_id', 'promo_code_id', 'final_amount_paid', 'tax', 'discount_amount', 'cart_amount','delivery_amount', 'created_at', 'order_status', 'quantity','tip_amount', 'order_status_history')
                ->with(['BusinessSegment' => function ($q) {
                    $q->addSelect('id', 'address');
                }])
                ->with(['OrderDetail' => function ($q) {
                    $q->addSelect('id', 'order_id', 'product_id', 'product_variant_id', 'weight_unit_id', 'quantity', 'price', 'discount', 'total_amount');
                }])
                ->with(['OrderDetail.Product' => function ($q) {
                }])
                ->with(['PaymentMethod' => function ($q) {
                    $q->addSelect('id', 'payment_method');
                }])
                ->where('id',$request->order_id)
                ->first();
               $arr_option_amount = [];

                $merchant_id = $order->merchant_id;
                $product_data = $order->OrderDetail->map(function ($product, $key) use ($merchant_id, $currency) {
                    $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
                    $arr_option = !empty($product->options) ? json_decode($product->options,true) : [];
                    $product_option =  !empty($arr_option) ? array_sum(array_column($arr_option,'amount')) : 0;
                    $arr_option_amount[] =$product_option;
                    return [
                        'title' => $product->Product->Name($merchant_id),
                        'variant_title' => $product->ProductVariant->Name($merchant_id),
                        'image' => get_image($product->Product->product_cover_image, 'product_cover_image', $merchant_id),
                        'quantity' => $product->quantity,
                        'weight_unit' => $product->ProductVariant->weight .' '.$unit,
                        'food_type' => $product->Product->food_type,
                        'total_price' => round_number($product->price + $product_option),
                        'product_price' => round_number($product->ProductVariant->product_price),
                        'total_product_price' => round_number($product->quantity * round_number($product->ProductVariant->product_price)),
                        'arr_option' => $arr_option,

                    ];
                });

            $order_cancel_status = false;
            $order_eligible_for_cancel = [1,6,7];
            $cancel_minutes = 0;
            $cancel_charges = 0;
            $status_history = json_decode($order->order_status_history, true);

            $is_order_status_nine = false;
            foreach($status_history as $status_hst){
                if($status_hst['order_status'] == 9){
                    $is_order_status_nine = true;
                    break;
                }
            }
            if(in_array($order->order_status, $order_eligible_for_cancel) && !$is_order_status_nine && $order->order_status != 2){
                if(isset($order->PriceCard) && $order->PriceCard->cancel_charges == 1 && $order->payment_method_id == 1){
                    $order_cancel_status = true;
                    $cancel_minutes = $order->PriceCard->cancel_time;
                    $cancel_charges = $order->PriceCard->cancel_amount;
                }
            }
            // p($order_cancel_status);
            $date = new DateTime($order->created_at);
            $date->setTimezone(new DateTimeZone($order->CountryArea->timezone));
                $option_amount = array_sum($arr_option_amount);
                $cart_amount = round_number($order->cart_amount + $option_amount);
                $order_details =  [
                    'order_id' => $order->id,
                    'order_no' => $order->merchant_order_id,
                    'pickup' => $order->BusinessSegment->address,
                    'drop_off' => $order->drop_location,
                    'order_date' => $date->format('H:i, d-m-Y'),
                    'total_items' => $order->quantity,
                    'currency' => "$currency",
                    'option_amount' => "$option_amount",
                    'order_status' => $config_status[$order->order_status],
                    'product_data' => $product_data,
                    'time_charges_enable' => false,
                    'time_charges_placeholder' => "",
                    'receipt'=>[
                        'cart_amount' => (int)$cart_amount,
                        'total_amount' => "$order->final_amount_paid",
                        'delivery_amount' => !empty($order->delivery_amount) ? "$order->delivery_amount" : "0.00",
                        'tax' => !empty($order->tax) ? "$order->tax" : "",
                        'tip_amount' => !empty($order->tip_amount) ? "$order->tip_amount" : "",
                        'time_charges' => "",
                        'discount_amount' => "$order->discount_amount",
                    ],
                    'cancel_receipt' => HolderController::userOrderCancelHolder($order, $string_file),
                    'arr_action'=>[
                        'tracking'=>in_array($order->order_status,[6,7,9,10]) ? true : false,
                        'cancel_order'=> $order_cancel_status,
                        'cancel_text' => trans("$string_file.order_cancel_warning",["time" =>$cancel_minutes, "amount" => $order->CountryArea->Country->isoCode." ".$cancel_charges])
                    ],
                ];
            return $this->successResponse(trans("$string_file.data_found"), $order_details);
        }catch (\Exception $e)
        {
            // p($e->getTraceAsString());
            return $this->failedResponse($e->getMessage());
        }
    }

    public function applyRemovePromoCode(Request $request){
        $user = $request->user('api');
        $request_fields = [
            'cart_id' => ['required', 'integer', Rule::exists('product_carts', 'id')->where(function ($query) {
            }),],
            'segment_id' => ['required', 'integer', Rule::exists('segments', 'id')->where(function ($query) {
            }),],
        ];
        $validator = Validator::make($request->all(), $request_fields);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        DB::beginTransaction();
        try {
            // call area trait to get id of area
            $string_file = $this->getStringFile($user->merchant_id);
            $this->getAreaByLatLong($request,$string_file);


            $promocode = NULL;
            if (isset($request->promo_code) && !empty($request->promo_code)) {
                $cart = ProductCart::select('cart_amount','id')->find($request->cart_id);
                $request->request->add(['order_amount'=>$cart->cart_amount]);
                $common_controller = new CommonController();
                $check_promo_code = $common_controller->checkPromoCode($request);
                if (isset($check_promo_code['status']) && $check_promo_code['status'] == true) {
                    $promocode = $check_promo_code['promo_code'];
                } else {
                    return $check_promo_code;
                }
            }
            $return_cart = $this->getCartData($request->cart_id, true, $promocode, "",$request,$string_file);
            // if cart status is out of stock then return cart response in place order api
            if ($return_cart->cart_out_of_stock == true) {
                return $this->successResponse(trans("$string_file.cart_out_of_stock"), $return_cart);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Rollback Transaction
            DB::rollback();
            return $this->failedResponse($message);
        }
        DB::commit();
        return $this->successResponse(trans("$string_file.promo_code_applied"), $return_cart);
    }


    // Favourite Business Segments of user
    public function favouriteBusinessSegment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_segment_id' => 'required|integer',
            'segment_id' => 'required|integer',
            'action' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $user = $request->user('api');
        $user_id = $user->id;
        $string_file = $this->getStringFile();
        if ($request->action == 1) // add / update
        {
            FavouriteBusinessSegment::updateOrCreate(
                ['user_id' => $user_id, 'business_segment_id' => $request->business_segment_id],
                ['segment_id' => $request->segment_id,'merchant_id' => $request->merchant_id]
            );
        } elseif ($request->action == 2) // delete
        {
//            $driver = (object)[];
            FavouriteBusinessSegment::where([['user_id', '=', $user_id], ['business_segment_id', '=', $request->business_segment_id], ['segment_id', '=', $request->segment_id]])->delete();
        }
        return response()->json(['result' => "1", 'message' => trans("$string_file.favourite"), 'data' => []]);
    }

    public function getFavouriteBusinessSegment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'segment_id' => 'required',
//            'area_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return $this->failedResponse($errors[0]);
        }
        $user = $request->user('api');
        $string_file = $this->getStringFile(NULL, $user->Merchant);
        $user_id = $user->id;
        $merchant_id = $user->merchant_id;


        try{
            // call area trait to get id of area
            $this->getAreaByLatLong($request,$string_file);
        }catch(\Exception $e)
        {

            return $this->failedResponse($e->getMessage(),[]);
        }

//        $arr_driver = FavouriteBusinessSegment::select('id', 'business_segment_id')->where([['user_id', '=', $user_id], ['segment_id', '=', $request->segment_id]
//        ])->with(['BusinessSegment' => function ($q) {
//            $q->select("id", "first_name", "last_name", "phoneNumber", "email", "profile_image", "rating");
//        }])
//            ->get();


        $user = $request->user('api');
        $distance = $user->Merchant->BookingConfiguration->store_radius_from_user;
        $google_key = $user->Merchant->BookingConfiguration->google_key;
        $request->request->add(['user_id'=>$user_id,'is_favourite'=>"YES",'distance'=>$distance]);
        $arr_restaurant = $this->getMerchantBusinessSegment($request);
        $google = new GoogleController;
        $user_lat = $request->latitude;
        $user_long = $request->longitude;

        $unit = $user->Country->distance_unit;




        $fav_restaurant_res = $arr_restaurant->map(function ($item, $key) use ($merchant_id,$google,
            $user_lat,$user_long,$unit,$string_file,$google_key) {
            // only setting timezone
            date_default_timezone_set($item->CountryArea['timezone']);
            $current_time = date('H:i');
            $is_business_segment_open = false;
            $current_day = date('w');
            $arr_open_time = json_decode($item->open_time,true);
            $arr_close_time = json_decode($item->close_time,true);
            $open_time = isset($arr_open_time[$current_day]) ? $arr_open_time[$current_day] : NULL;
            $close_time = isset($arr_close_time[$current_day]) ? $arr_close_time[$current_day] : NULL;

            //  Changes for midnight store time
            if($open_time > $close_time){
                $close_time_n = date('Y-m-d H:i:s', strtotime($close_time . ' +1 day'));
            }else{
                $close_time_n = date('Y-m-d H:i:s', strtotime($close_time));
            }
            $open_time_n = date('Y-m-d H:i:s', strtotime($open_time));
            $current_time_n = date("Y-m-d H:i:s");
            if ($open_time_n < $current_time_n && $close_time_n > $current_time_n) {
                $is_business_segment_open = true;
            }

            $arr_style =   $item->StyleManagement->map(function ($style) use ($merchant_id,$unit,$string_file){
                return ['style_name' => $style->Name($merchant_id)];
            });

            $store_lat = $item->latitude;
            $store_long = $item->longitude;

            $user_drop_location[0] = [
                'drop_latitude'=>$user_lat,
                'drop_longitude'=>$user_long,
                'drop_location'=>""
            ];
            $distance_from_user = GoogleController::GoogleStaticImageAndDistance($store_lat, $store_long, $user_drop_location, $google_key,"",$string_file);
            $distance_from_user = isset($distance_from_user['total_distance_text']) ? $distance_from_user['total_distance_text'] : "";
            return array(
                'business_segment_id' => $item->id,
                'title' => $item->full_name,
                'time' => "$item->delivery_time " . trans("$string_file.minute"),
                'amount' => !empty($item->minimum_amount) ? "$item->minimum_amount" : "",
                'amount_for' => !empty($item->minimum_amount_for) ? "$item->minimum_amount_for" : "",
                'currency' => $item->Country->isoCode,
                'style' => array_pluck($arr_style,'style_name'),//array_pluck($item->StyleManagement, 'style_name'),
                'open_time' => $open_time,
                'close_time' => $close_time,
                'rating' => !empty($item->rating) ? $item->rating : "2.5",
                'distance' => trans("$string_file.distance").' '.$distance_from_user,
                'image' => get_image($item->business_logo, 'business_logo', $merchant_id),
                'is_business_segment_open' => $is_business_segment_open,
            );
        });
        return $this->successResponse(trans("$string_file.data_found"), $fav_restaurant_res);
    }
}
