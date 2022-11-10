<?php

namespace App\Http\Controllers\Merchant;

use App\Models\CountryArea;
use App\Models\Driver;
use App\Models\InfoSetting;
use App\Models\Onesignal;
use App\Models\PromotionNotification;
use App\Models\UserDevice;
use App\Traits\DriverTrait;
use App\Traits\ImageTrait;
use App\Traits\UserTrait;
use App\Traits\MerchantTrait;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Validator;

class PromotionNotificationController extends Controller
{
    use ImageTrait, DriverTrait, UserTrait,MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'PROMOTIONAL_NOTIFICATION')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index(Request  $request)
    {
        $search_param = $request->all();
        $checkPermission =  check_permission(1,'view_promotion');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
//        $authMerchant = Auth::user('merchant')->load('CountryArea');
        $merchant_id = get_merchant_id();
        $promotions = PromotionNotification::where([['merchant_id', '=', $merchant_id]])->latest();
        $promotions = $promotions->paginate(25);
        $data = [];
        $data['merchant_id'] = $merchant_id;
        return view('merchant.promotion.index', compact('promotions','data','search_param'));
    }

    public function create()
    {
        $checkPermission =  check_permission(1,'create_promotion');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $areas = CountryArea::where([['merchant_id', '=', $merchant_id]])->get();
        return view('merchant.promotion.create', ['areas' => $areas]);
    }

    public function store(Request $request)
    {
        $merchant_id = get_merchant_id();
        $validator = Validator::make($request->all(),[
            'application' => 'required|integer|between:1,2',
            'title' => 'required|string',
            'message' => 'required|string',
//            'image' => 'nullable|mimes:jpeg,jpg,png,gif',
//            'date' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            $promotion = new PromotionNotification();
            $promotion->merchant_id = $merchant_id;
            $promotion->title = $request->title;
            $promotion->application = $request->application;
            $promotion->message = $request->message;
            $promotion->url = $request->url;
            $promotion->show_promotion = 1; // make show promotion default and put 1 always Added by @amba
            $promotion->expiry_date = $request->date;
            if ($request->hasFile('image')) {
                $promotion->image = $this->uploadImage('image', 'promotions');
            }
            $promotion->save();
            $promotion->notification_type = 1;    // type 1 for promotion notification
            $promotion->image = get_image($promotion->image,'promotions',$merchant_id);
            if ($request->application == 1) {
                $data = array(
                    'notification_type' => "NOTIFICATION",
                    'segment_type' => "NOTIFICATION",
                    'segment_data' => $promotion,
                );
                $large_icon = NULL;
                $arr_param = ['driver_id'=> 'all','data'=>$data,'message'=>$request->message,'merchant_id'=>$merchant_id,'title'=>$request->title,'large_icon'=>$large_icon];
                Onesignal::DriverPushMessage($arr_param);
//                Onesignal::DriverPushMessage('all', $promotion->toArray(), $request->title, 2, $merchant_id);
            } else {
                $data = array(
                    'notification_type' => "PROMOTION_NOTIFICATION",
                    'segment_type' => "NOTIFICATION",
                    'segment_data' => $promotion,
                );
                $arr_param = ['user_id' => "all", 'data' => $data, 'message' => $request->title, 'merchant_id' => $merchant_id, 'title' => $request->title, 'large_icon' => NULL];
                Onesignal::UserPushMessage($arr_param);
//                Onesignal::UserPushMessage('all', $promotion->toArray(), $request->title, 2, $merchant_id);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        $string_file = $this->getStringFile(NULL,$promotion->Merchant);
        return redirect()->back()->withSuccess(trans("$string_file.notification_sent_successfully"));
    }

    public function SendNotificationDriver(Request $request)
    {
        $merchant_id = get_merchant_id();
        $validation_array = [
            'persion_id' => ['required',
                Rule::exists('drivers', 'id')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', $merchant_id);
                })],
            'title' => 'required|string',
            'message' => 'required|string',
            'image' => 'nullable|mimes:jpeg,jpg,png',
        ];
        if(isset($request->expery_check) == 1){
            $validation_array = array_merge($validation_array,['date' => 'required']);
        }
        $validator = Validator::make($request->all(),$validation_array);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withErrors($errors[0]);
        }
        DB::beginTransaction();
        try {
            $promotion = new PromotionNotification();
            $promotion->merchant_id = $merchant_id;
            $promotion->title = $request->title;
            $promotion->application = 1;
            $promotion->message = $request->message;
            $promotion->driver_id = $request->persion_id;
            $promotion->url = $request->url;
            $promotion->show_promotion = (isset($request->expery_check) == 1) ? $request->expery_check : null;
            $promotion->expiry_date = $request->date;
            if ($request->hasFile('image')) {
                $promotion->image = $this->uploadImage('image', 'promotions');
                $promotion->save();
            }
            $promotion->save();
            $promotion_data = array(
                'url' => isset($promotion->url) ? $promotion->url : "",
                'image' => isset($promotion->image) ? get_image($promotion->image,'promotions',$merchant_id) : ""
                );
//            $driver = Driver::find($request->persion_id);
//            $playerids = array($driver->player_id);
//            Onesignal::DriverPushMessage($request->persion_id, $promotion->toArray(), $request->title, 2, $merchant_id, 1);
            $data = array(
                'notification_type' => "NOTIFICATION",
                'segment_type' => "NOTIFICATION",
                'segment_data' => $promotion_data,
            );
            $large_icon = NULL;
            $arr_param = ['driver_id'=>$request->persion_id,'data'=>$data,'message'=>$request->message,'merchant_id'=>$merchant_id,'title'=>$request->title,'large_icon'=>$large_icon];
            Onesignal::DriverPushMessage($arr_param);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        $string_file = $this->getStringFile(NULL, $promotion->Merchant);
        return redirect()->back()->withSuccess(trans("$string_file.notification_sent_successfully"));
    }

    public function SendNotificationUser(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $validation_array = [
            'persion_id' => ['required',
                Rule::exists('users', 'id')->where(function ($query) use ($merchant_id) {
                    return $query->where('merchant_id', $merchant_id);
                })],
            'title' => 'required|string',
            'message' => 'required|string',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif',
        ];
        if(isset($request->expery_check) && $request->expery_check == 1){
            $validation_array = array_merge($validation_array,['date' => 'required']);
        }
        $validator = Validator::make($request->all(),$validation_array);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withErrors($errors[0]);
        }

        DB::beginTransaction();
        try {
            $promotion = new PromotionNotification();
            $promotion->merchant_id = $merchant_id;
            $promotion->title = $request->title;
            $promotion->application = 2;
            $promotion->message = $request->message;
            $promotion->user_id = $request->persion_id;
            $promotion->url = $request->url;
            $promotion->show_promotion = 1;
            $promotion->expiry_date = $request->date;
            if ($request->hasFile('image')) {
                $promotion->image = $this->uploadImage('image', 'promotions');;

            }
            $promotion->save();
//            $userdevices = UserDevice::where([['user_id', '=', $request->persion_id]])->get();
//            $playerids = array_pluck($userdevices, 'player_id');
            $data = array(
                'notification_type' => "PROMOTION_NOTIFICATION",
                'segment_type' => "NOTIFICATION",
                'segment_data' => $promotion,
            );
            $arr_param = ['user_id' => $request->persion_id, 'data' => $data, 'message' => $request->title, 'merchant_id' => $merchant_id, 'title' => $request->title, 'large_icon' => NULL];
            Onesignal::UserPushMessage($arr_param);
//            Onesignal::UserPushMessage($request->persion_id, $promotion->toArray(), $request->title, 2, $merchant_id, 1);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        $string_file = $this->getStringFile(NULL,$promotion->Merchant);
        return redirect()->route('users.index')->withSuccess(trans("$string_file.notification_sent_successfully"));
    }

    public function SendNotificationAreaWise(Request $request)
    {
        $merchant_id = get_merchant_id();
        $validator = Validator::make($request->all(),[
            'area' => 'required|exists:country_areas,id',
            'title' => 'required|string',
            'message' => 'required|string',
//            'image' => 'nullable|mimes:jpeg,jpg,png,gif',
//            'date' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        $drivers = Driver::where([['country_area_id', '=', $request->area]])->get();
        if (empty($drivers->toArray())) {
            return redirect()->back()->with('Drivers not found in selected area');
        }
        DB::beginTransaction();
        try {
            $promotion = new PromotionNotification();
            $promotion->merchant_id = $merchant_id;
            $promotion->title = $request->title;
            $promotion->country_area_id = $request->area;
            $promotion->application = 1;
            $promotion->message = $request->message;
            $promotion->user_id = $request->persion_id;
            $promotion->url = $request->url;
            $promotion->show_promotion = 1;
            $promotion->expiry_date = $request->date;
            if ($request->hasFile('image')) {
                $promotion->image = $this->uploadImage('image', 'promotions');;
                $promotion->save();
            }
            $promotion->save();
            $promotion->image = get_image($promotion->image,'promotions',$merchant_id);
            $ids = array_pluck($drivers, 'id');
            // Onesignal::DriverPushMessage($ids, $promotion->toArray(), $request->title, 2, $merchant_id, 1);

            $data = array(
                'notification_type' => "NOTIFICATION",
                'segment_type' => "NOTIFICATION",
                'segment_data' => $promotion,
            );
            $large_icon = NULL;
            $arr_param = ['driver_id'=>$ids,'data'=>$data,'message'=>$request->message,'merchant_id'=>$merchant_id,'title'=>$request->title,'large_icon'=>$large_icon];
            Onesignal::DriverPushMessage($arr_param);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        $string_file = $this->getStringFile(NULL,$promotion->Merchant);
        return redirect()->back()->withSuccess(trans("$string_file.notification_sent_successfully"));
    }

    public function Search(Request $request)
    {
        $merchant_id = get_merchant_id();
        $search_param = $request->all();
//        p($search_param);
        $query = PromotionNotification::where([['merchant_id', '=', $merchant_id]]);
        if ($request->title) {
            $query->where('title', $request->title);
        }
        if ($request->application) {
            $query->where('application', $request->application);
        }
        if ($request->date) {
            $query->whereDate('created_at', '=', $request->date);
        }
        $promotions = $query->paginate(25);
        $data = $request->all();
        $data['merchant_id'] = $merchant_id;
        return view('merchant.promotion.index', compact('promotions','data','search_param'));
    }

    public function edit($id)
    {
        $checkPermission =  check_permission(1,'edit_promotion');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
//        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $promotion = PromotionNotification::findOrFail($id);
        return view('merchant.promotion.edit', compact('promotion'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
        ]);
        DB::beginTransaction();
        try {
//            $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
            $promotion = PromotionNotification::findOrFail($id);
            $promotion->title = $request->title;
            $promotion->message = $request->message;
            $promotion->url = $request->url;
            if ($request->hasFile('image')) {
                $promotion->image = $this->uploadImage('image', 'promotions');
            }
            $promotion->save();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        $string_file = $this->getStringFile(NULL,$promotion->Merchant);
        return redirect()->back()->withSuccess(trans("$string_file.notification_sent_successfully"));
    }

    public function destroy($id)
    {
        $checkPermission =  check_permission(1,'delete_promotion');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $promotions = PromotionNotification::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $promotions->delete();
        return redirect()->route('promotions.index')->with('success', trans('admin.promotion_deleted'));
    }
}
