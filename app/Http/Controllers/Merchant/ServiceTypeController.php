<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use DB;
use App\Models\Merchant;
use App\Models\ServiceType;
use App;
use Auth;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\MerchantTrait;


class ServiceTypeController extends Controller
{
    use ImageTrait,MerchantTrait;
    public function index()
    {
        $checkPermission =  check_permission(1,"view_service_types");
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $segment_services = $this->getMerchantSegmentServices($merchant_id,'service_type_screen');
       // p($segment_services);
        $info_setting = InfoSetting::where('slug', 'SERVICE_TYPE_SETTINGS')->first();
        return view('merchant.service_types.index', compact('segment_services','info_setting'));
    }

    public function add(Request $request, $segment_id,$id = NULL)
    {
        $checkPermission =  check_permission(1,"view_service_types");
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $service = '';
        if(empty($id) && !in_array($segment_id,array_keys(get_merchant_segment(false))))
        {
            return redirect()->route('merchant.serviceType.index')->withErrors(trans('admin.trying_to_add_invalid_segment'));
        }
        $merchant = get_merchant_id(false);

        if(!empty($id))
        {
            $locale = App::getLocale();
            $service = $merchant->ServiceType->where('id',$id)->first();
            $service_locale = '';
            $merchant_id = $merchant->id;
            $service_description = "";

            if(!empty($service->ServiceName($merchant_id)))
            {
            $service_locale = $service->ServiceName($merchant_id);
            }
            if(!empty($service->ServiceDescription($merchant_id)))
            {
            $service_description = $service->ServiceDescription($merchant_id);
            }
            $service->service_locale_name = !empty($service_locale) ? $service_locale :$service->serviceName;
            $service->service_locale_description = $service_description;
        }
        $arr_segment = get_merchant_segment();
        $segment = $arr_segment[$segment_id];
        return view('merchant.service_types.form', compact('service','segment_id','segment'));
    }

    public function update(Request $request, $id = NULL)
    {
        $checkPermission =  check_permission(1,"edit_service_types");
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }
        $request->validate([
            'service' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $merchant = get_merchant_id(false);
            $merchant_id = $merchant->id;

            $string_file = $this->getStringFile(NULL,$merchant);
            $exist_service =   DB::table('service_types')->select('id')->where([
                ['serviceName','=',$request->service],['segment_id','=',$request->segment_id]
            ])->where(function($q) use($id){
              if(!empty($id))
              {
                $q->where('id','!=',$id);
              }
            })
            ->first();

            if(!empty($exist_service->id))
            {
                return redirect()->route('merchant.serviceType.index')->withErrors(trans("$string_file.service_type_duplicate"));
            }

            if(empty($id))
            {
                $service = new ServiceType;
                $service->serviceName = $request->service;
                $service->segment_id = $request->segment_id;
                $service->serviceStatus = 1;
                $service->owner_id = $merchant_id;
                $service->owner = 2; // service added by merchant
                $service->save();
                // insert row in pivot table
                DB::table('merchant_service_type')->insert([
                    ['service_type_id'=>$service->id,'merchant_id'=>$merchant_id,'segment_id'=>$request->segment_id,'sequence'=>$request->sequence]
                ]);
//                $merchant->ServiceType()->attach($service->id,['segment_id'=>$request->segment_id,'sequence'=>$request->sequence]);
//                p($service);
            }
            App\Models\ServiceTranslation::updateOrCreate([
                'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'service_type_id' => $id
            ], [
                'name' => $request->service,
                'description' => $request->description,
            ]);
            $insert_param['sequence'] = $request->sequence;
            if ($request->hasFile('icon')) {
                $insert_param['service_icon'] = $this->uploadImage('icon', 'service');
            }
            DB::table('merchant_service_type')->where([['merchant_id','=',$merchant_id],['service_type_id','=',$id],['segment_id','=',$request->segment_id]])->update($insert_param);

        } catch (\Exception $e) {
            $message = $e->getMessage();
          //  p($message);
            return redirect()->route('merchant.serviceType.index')->withErrors($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();

        return redirect()->route('merchant.serviceType.index')->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function editSegment($id)
    {
        $locale = App::getLocale();
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        if($merchant->demo == 1)
        {
            return redirect()->back()->withErrors(trans("$string_file.demo_user_cant_edited"));
        }
        $segment = $merchant->Segment->where('id',$id)->first();
//        p($segment);
        $segment->segment_locale_name = $segment->slag;
        $segment_locale = $segment->Name($merchant->id);
        if(!empty($segment_locale))
        {
            $segment->segment_locale_name = $segment_locale;
        }
        return view('merchant.service_types.segment', compact('segment'));
    }

    public function updateSegment(Request $request, $id)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        if($merchant->demo == 1)
        {
            return redirect()->back()->withErrors(trans("$string_file.demo_user_cant_edited"));
        }
        $merchant_id = $merchant->id;
        $request->validate([
            'segment' => 'required'
        ]);
        DB::beginTransaction();
        try {
            App\Models\SegmentTranslation::updateOrCreate([
                'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'segment_id' => $id
            ],[
                'name' => $request->segment,
            ]);
           $arr_update = ['sequence'=>$request->sequence,'is_coming_soon'=>$request->is_coming_soon];
            if ($request->hasFile('icon')) {
                $icon = $this->uploadImage('icon', 'segment',$merchant_id);
                $arr_update['segment_icon']= $icon;
            }
            DB::table('merchant_segment')->where('merchant_id',$merchant_id)
                ->where('segment_id',$id)->update($arr_update);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();

        return redirect()->route('merchant.serviceType.index')->withSuccess(trans("$string_file.saved_successfully"));
    }
}
