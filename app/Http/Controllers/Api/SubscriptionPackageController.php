<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\Driver;
//use App\Models\DriverActivePack;
use App\Models\DriverSubscriptionRecord;
use App\Models\Onesignal;
use App\Models\SubscriptionPackage;
use App\Models\PackageDuration;
use App\Models\DriverWalletTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Merchant\DriverController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DB;

class SubscriptionPackageController extends Controller
{
    public function ViewPackages(Request $request)
    {
        $driver = $request->user('api-driver');
        $free_packages = DriverSubscriptionRecord::select('subscription_pack_id')->where([['package_type',1],['driver_id',$driver->id],['status',1],['end_date_time',NULL]])
                ->whereHas('SubscriptionPackage', function ($q) {
                    $q->where('expire_date','>=',date('Y-m-d'));
                    $q->orWhere('expire_date',NULL);
                })
            ->get()->toArray();
        $arr_assigned_fee_package = array_column($free_packages,'subscription_pack_id');
        $packages = SubscriptionPackage::with('ServiceType','PackageDuration')
            ->where([['merchant_id',$driver->merchant_id],['status',true]])
            ->whereHas('CountryArea', function ($query) use(&$driver){
                $query->where('country_area_id', '=', $driver->CountryArea->id);
            })
            ->where(function($q) use($arr_assigned_fee_package){
                $q->where('package_type',2); // paid
                if(!empty($arr_assigned_fee_package))
                {
                    $q->orWhereIn('id',$arr_assigned_fee_package); // free assigned package of driver
                }
            })
            ->where(function($q){
                $q->where('expire_date','>=',date('Y-m-d'));
                $q->orWhere('expire_date',NULL);
            })
            ->get(['id','max_trip','image','price','package_type','merchant_id','package_duration_id','expire_date']);
        if ($driver->merchant_id == 82){
            //for ultra taxi only
            $all_payment_methods = $driver->Merchant->PaymentMethod->where('id','=',3);
        }else{
            $all_payment_methods = $driver->Merchant->PaymentMethod->where('id','!=',1);
        }
        $payment_methods = $all_payment_methods->map(function ($item, $key) {
            return $item->only(['id', 'payment_method', 'payment_icon']);
        })->values();

        foreach($packages as $key=>$package):
            $package->name = $package->name;
            $package->expire_date = !empty($package->expire_date) ? $package->expire_date : '';
            $package->package_type = $package->package_type;
            $package->description = $package->description;
            $package->show_price = $driver->CountryArea->Country->isoCode.' '.$package->price;
//            $package->package_duration_name = $package->PackageDuration->name;
            $package->package_duration_name = '';
            $duration = $package->PackageDuration->getNameAccMerchantAttribute($driver->merchant_id,$package->package_duration_id);
            if(!empty($duration))
            {
                $package->package_duration_name = $duration;
            }
            $package->image = get_image($package->image,'package',$package->merchant_id);
            $package->service_type = $package->ServiceType->transform(function ($item, $key) use($driver){
                $ServiceTypeConfig = $item->ServiceTypeConfiguratoin($driver->merchant_id);
                $item->colour = (!empty($ServiceTypeConfig)) ? $ServiceTypeConfig['colour'] : '';
                $item->icon = (!empty($ServiceTypeConfig)) ? get_image($ServiceTypeConfig['icon'],'icon',$driver->merchant_id) : '';
                unset($item['serviceStatus']);
                unset($item['package']);
                unset($item['type']);
                unset($item['created_at']);
                unset($item['updated_at']);
                unset($item['type']);
                return $item;
            });
//            unset($packages[$key]['package_duration_id']);
        endforeach;
        $active_packages = DriverSubscriptionRecord::select('subscription_pack_id','payment_method_id','package_duration_id','package_total_trips','price','used_trips','start_date_time','end_date_time','status','package_type')
            ->with('SubscriptionPackage.ServiceType')
            ->where([['driver_id',$driver->id],['status',2],['end_date_time','>=',date('Y-m-d H:i:s')]])
            ->get();
        foreach($active_packages as $key=>$active_package):
            $active_package->name = $active_package->SubscriptionPackage->name;
            $active_package->package_type = $active_package->package_type;
            $active_package->description = $active_package->SubscriptionPackage->description;
            $active_package->show_price = $driver->CountryArea->Country->isoCode.' '.$active_package->price;
            $active_package->active = true;
            $active_package->rides_left = $active_package->package_total_trips - $active_package->used_trips;
//            $active_package->package_duration_name = $active_package->PackageDuration->name;
            $active_package->package_duration_name = '';
            $duration = $active_package->PackageDuration->getNameAccMerchantAttribute($driver->merchant_id,$active_package->package_duration_id);
            if(!empty($duration))
            {
                $active_package->package_duration_name = $duration;
            }
            $active_package->image = get_image($active_package->SubscriptionPackage->image,'package',$driver->merchant_id);
            $active_package->service_type = $active_package->SubscriptionPackage->ServiceType->transform(function ($item, $key) use($driver){
                $ServiceTypeConfig = $item->ServiceTypeConfiguratoin($driver->merchant_id);
                $item->colour = (!empty($ServiceTypeConfig)) ? $ServiceTypeConfig['colour'] : '';
                $item->icon = (!empty($ServiceTypeConfig)) ? get_image($ServiceTypeConfig['icon'],'icon',$driver->merchant_id) : '';
                unset($item['serviceStatus']);
                unset($item['package']);
                unset($item['type']);
                unset($item['created_at']);
                unset($item['updated_at']);
                unset($item['type']);
                return $item;
            });
//            unset($packages[$key]['package_duration_id']);
        endforeach;
        $activated_any_pack = (!empty($active_packages->toArray()) && count($active_packages->toArray()) > 0) ? true : false;
        return response()->json(['result'=>"1", 'message'=>trans('api.driver'), 'activated_pack'=>$activated_any_pack, 'active_pack_detail'=>$active_packages, 'data'=>$packages, 'payment_method'=>$payment_methods]);
    }
    
    public function ActivatePackage(Request $request)
    {
        $driver = $request->user('api-driver');
        $validator = Validator::make($request->all(), [
            'subscription_package_id' => [
                'required',
                Rule::exists('subscription_packages', 'id')->where(function ($query) use(&$driver) {
                    $query->where([['merchant_id',$driver->merchant_id],['status',true], ['admin_delete',0], ['deleted_at',null]]);
                }),
            ],
            'payment_method_id' => 'required_if:package_type,==,2',
            'payment_status' => [
                'required_unless:payment_method_id,3','string',
                Rule::in(['SUCCESS','FAIL']),
            ],
        ]);
        $request->request->add(['package' => $request->subscription_package_id,'driver_id'=>$driver->id]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $package = SubscriptionPackage::where([['merchant_id',$driver->merchant_id]])->findorfail($request->subscription_package_id);
        ($request->payment_method_id == 3) ? $this->CheckWalletActivation($driver,$package) : '';
        ($request->payment_status == 'SUCCESS') ? $this->SavePackageDetails($request) : '';

        $respose_data = ($request->payment_status == 'SUCCESS') ?
            ['result'=>"1",'message'=>trans('api.subscription_activated')]:
            (($request->payment_method_id == 3) ? ['result'=>"0",'message'=>trans('api.message35')]:
                ['result'=>"0",'message'=>trans('api.subscription_activated_failed')]);
        return response()->json($respose_data);
    }
    
    public function CheckWalletActivation(Driver $driver, SubscriptionPackage $package)
    {
        if($driver->wallet_money >= $package->price):
            $paramArray = array(
                'driver_id' => $driver->id,
                'booking_id' => null,
                'amount' => $package->price,
                'narration' => 4,
                'platform' => 1,
                'payment_method' => 3,
                'receipt' => rand(1111, 983939),
            );
            WalletTransaction::WalletDeduct($paramArray);
//            \App\Http\Controllers\Helper\CommonController::WalletDeduct($driver->id,null,$package->price,4,1,3,rand(1111, 983939));
//            $driver->wallet_money = ($driver->wallet_money - $package->price);
//            $driver->save();
//            DriverWalletTransaction::create([
//                'merchant_id' => $driver->merchant_id,
//                'driver_id' => $driver->id,
//                'transaction_type' => 2,
//                'payment_method' => 3,
//                'receipt_number' => rand(1111, 983939),
//                'amount' => $package->price,
//                'platform' => 1,
//                'subscription_package_id' => $package->id,
//                'description' => 'On Activation of Subscription Pack',
//                'narration' => 4,
//            ]);
            request()->request->add(['payment_status' => 'SUCCESS']);
            return true;
        else:
            request()->request->add(['payment_status' => 'FAIL']);
            return false;
        endif;

    }

    public function SubscriptionPackageDuration($package_duration_id)
    {
        $package_duration = PackageDuration::find($package_duration_id);
        $days = $package_duration->sequence; // number of days
        return ['start_date_time'=>date('Y-m-d H:i:s'), 'end_date_time'=>(new \DateTime(date('Y-m-d H:i:s')))->modify("+$days day")->format('Y-m-d H:i:s')];
    }

    public function SavePackageDetails(Request $request, $isWeb = false)
    {
        DB::beginTransaction();
        $driver_id = $request->driver_id;
        try {
        $driver_record = new DriverSubscriptionRecord;
        $package = SubscriptionPackage::findorfail($request->package);
        $duration = new SubscriptionPackageController();
        $duration_data = $duration->SubscriptionPackageDuration($package->package_duration_id);
            // for paid package
            $driver_record->package_type = $package->package_type;
            if($package->package_type == 2)
            {
                // get active package of driver
                $active_pack = DriverSubscriptionRecord::where('driver_id',$request->driver_id)->where('package_type',2)->where('status',2)->where('end_date_time',">=",date('Y-m-d H:i:s'))->orderBy('id','DESC')->first();
                $driver_record->driver_id = $request->driver_id;
                $driver_record->payment_method_id = $request->payment_method_id;
                $driver_record->subscription_pack_id =$package->id;
                $driver_record->package_duration_id = $package->package_duration_id;
                $driver_record->package_total_trips = $package->max_trip;
                $driver_record->price = $package->price;
                $driver_record->start_date_time = $duration_data['start_date_time'];
                $driver_record->end_date_time = $duration_data['end_date_time'];
                $driver_record->used_trips = 0;
                $driver_record->status = 2; // activate package
                if (!empty($active_pack->id) && (strtotime($active_pack->end_date_time) >= strtotime("now"))):
                    $driver_record->package_total_trips = $package->max_trip + ($active_pack->package_total_trips - $active_pack->used_trips);
                    $left_time = (strtotime($active_pack->end_date_time) - strtotime(date('Y-m-d H:i:s')));
                    $total_time = strtotime($duration_data['end_date_time']) + $left_time;
                    $end_date = date('Y-m-d H:i:s',$total_time);
                    $driver_record->end_date_time = $end_date;
                    $active_pack->status = 4; //carry forward to current package //
                    $active_pack->save();
                endif;
                $driver_record->save();
            }
            elseif ($package->package_type == 1)
            {
                $activated_package = DriverSubscriptionRecord::where([['driver_id',$request->driver_id],['package_type',$package->package_type],['subscription_pack_id',$request->package],['status',2]])->first();
                if(!empty($activated_package->id))
                {
                  return  ['result'=>"0",'message'=>trans('api.already_activated')];
                }

                $driver_record = DriverSubscriptionRecord::where([['driver_id',$request->driver_id],['package_type',$package->package_type],['subscription_pack_id',$request->package],['status',1],['end_date_time',NULL],['start_date_time',NULL]])->first();
                $driver_record->start_date_time = $duration_data['start_date_time'];
                $driver_record->end_date_time = $duration_data['end_date_time'];
                $driver_record->status = 2;
                $driver_record->save();
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();

        if($isWeb){
            $merchant_id = get_merchant_id();
            $msg = trans('api.subscription_activated');
            $type = 17;
            Onesignal::DriverPushMessage($driver_id, [], $msg, $type, $merchant_id, 1);
        }
    }
}
