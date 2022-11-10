<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Configuration;
use App\Models\InfoSetting;
use App\Models\Onesignal;
use App\Traits\AreaTrait;
use App\Traits\DriverTrait;
use App\Traits\ExpireDocument;
use App\Traits\ImageTrait;
use App\Traits\MerchantTrait;
use Auth;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ExpireDocumentController extends Controller
{
    use DriverTrait, AreaTrait, ExpireDocument,ImageTrait, MerchantTrait;


    public function index(Request $request)
    {
        $checkPermission = check_permission(1, 'expired_driver_documents');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $drivers = $this->getAllExpireDriversDocument($merchant->id);
        $merchant_type = $this->merchantType($merchant);
        $info_setting = InfoSetting::where('slug', 'DRIVER')->first();
        return view('merchant.driver.expired_document', compact('drivers','merchant_type','info_setting'));
    }

    function getDocumentGoingToExpire($currentDate, $reminder_last_date, $merchant_id, $driver_id = NULL)
    {
        $where = [['temp_document_file', '=', null], ['temp_expire_date', '=', null]];
        $dateBetween = array($currentDate, $reminder_last_date);
        $driverVehicleDocumentWith = ['DriverVehicleDocument' => function ($o) use ($where, $dateBetween) {
            $o->whereHas('Document', function($q) use($where)
            {
                $q->where('expire_date',1);
            });
            $o->where($where);
            $o->where('status',1);
            $o->whereBetween('expire_date', $dateBetween);
        }];
        $permission_area_ids = [];
        if(Auth::user() && isset(Auth::user()->role_areas) && Auth::user()->role_areas != ""){
            $permission_area_ids = explode(",",Auth::user()->role_areas);
        }
        $raw_query = Driver::select('id','first_name','last_name','email','phoneNumber','country_area_id')->where([['merchant_id', '=', $merchant_id], ['driver_delete', '=', NULL]])
            ->with(['DriverDocument' => function ($query) use ($where, $dateBetween) {
                $query->whereHas('Document', function($q) use($where)
                {
                    $q->where('expire_date',1);
                });
                $query->where($where);
                $query->where('status',1);
                $query->whereBetween('expire_date', $dateBetween);
            },
             'DriverSegmentDocument' => function ($query) use ($where, $dateBetween) {
                $query->whereHas('Document', function($q) use($where)
                {
                    $q->where('expire_date',1);
                });
                $query->where($where);
                $query->where('status',1);
                $query->whereBetween('expire_date', $dateBetween);
            },
                'DriverVehicles' => function ($d_v) use ($where, $dateBetween, $driverVehicleDocumentWith) {
                $d_v->with($driverVehicleDocumentWith)
                    ->whereHas('DriverVehicleDocument', function ($p) use ($where, $dateBetween) {
                        $p->whereHas('Document', function($q) use($where)
                        {
                            $q->where('expire_date',1);
                        });
                        $p->where($where);
                        $p->where('status',1);
                        $p->whereBetween('expire_date', $dateBetween);
                    });
            },
            ])
            ->where(function ($q) use ($where, $dateBetween, $driverVehicleDocumentWith) {
                $q->whereHas('DriverDocument', function ($q) use ($where, $dateBetween) {
                    $q->whereHas('Document', function($q) use($where)
                    {
                        $q->where('expire_date',1);
                    });
                    $q->where($where);
                    $q->where('status',1);
                    $q->whereBetween('expire_date', $dateBetween);
                })
                    ->orWhereHas('DriverVehicles', function ($r) use ($where, $dateBetween, $driverVehicleDocumentWith) {
                    $r->with($driverVehicleDocumentWith)
                        ->whereHas('DriverVehicleDocument', function ($s) use ($where, $dateBetween) {
                            $s->whereHas('Document', function($q) use($where)
                            {
                                $q->where('expire_date',1);
                            });
                            $s->where($where);
                            $s->where('status',1);
                            $s->whereBetween('expire_date', $dateBetween);
                        });
                    })
                    ->orWhereHas('DriverSegmentDocument', function ($s) use ($where, $dateBetween) {
                    $s->whereHas('Document', function($q) use($where)
                    {
                        $q->where('expire_date',1);
                    });
                    $s->where($where);
                    $s->where('status',1);
                    $s->whereBetween('expire_date', $dateBetween);
                    });
                    if(!empty($permission_area_ids))
                    {
                        $q->whereIn('country_area_id',$permission_area_ids);
                    }
            });
            if(!empty($permission_area_ids))
            {
                $raw_query->whereIn('country_area_id',$permission_area_ids);
            }
            if(!empty($driver_id))
            {
              $raw_query->where('id',$driver_id);
              $drivers = $raw_query->first();
            }
            else
            {
            $drivers = $raw_query->latest();
            }
        return $drivers;
    }

    public function GoingToExpireDocs(){
        $currentDate = date('Y-m-d');
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $reminder_days = Configuration::where('merchant_id','=',$merchant_id)->select('reminder_doc_expire')->first();
        $reminder_days = $reminder_days->reminder_doc_expire == null ? 0 : $reminder_days->reminder_doc_expire;
        $reminder_last_date = date('Y-m-d',strtotime('+'.$reminder_days.' days'));
        $drivers = $this->getDocumentGoingToExpire($currentDate,$reminder_last_date,$merchant_id);
        $drivers = $drivers->paginate(10);
        $merchant_type = $this->merchantType($merchant);
        $info_setting = InfoSetting::where('slug', 'DRIVER')->first();
        return view('merchant.driver.going_to_expire_doc', compact('drivers','currentDate','reminder_last_date','merchant_id','merchant_type','info_setting'));
    }

    public function SendNotification($id){
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        if (!empty($id)){
            $data = [];
            $data['notification_type'] = "DOCUMENT_EXPIRE_REMINDER";
            $data['segment_type'] = "";
            $data['segment_data'] = [];
            $arr_param = ['driver_id' => $id, 'data' => $data, 'message' => trans("$string_file.document_expire_warning"), 'merchant_id' => $merchant_id, 'title' => trans("$string_file.docs_going_expire")];
            Onesignal::DriverPushMessage($arr_param);
            setLocal();
            return redirect()->back()->withSuccess(trans("$string_file.notification_sent_successfully"));
        }
    }

    public function sendNotificationToAll(Request $request){
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors(trans('admin.select_any_driver'));
        }
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $driver_ids = $request->driver_id;
//        $data['notification_type'] = "DOCUMENT_EXPIRED";
        $data['segment_type'] = "";
        $data['segment_data'] = [];
        $data['notification_type'] = "DOCUMENT_EXPIRE_REMINDER";
        $arr_param = ['driver_id' => $driver_ids, 'data' => $data, 'message' => trans("$string_file.document_expire_warning"), 'merchant_id' => $merchant_id, 'title' => trans("$string_file.docs_going_expire")];
        Onesignal::DriverPushMessage($arr_param);
        return redirect()->back()->withSuccess(trans("$string_file.notification_sent_successfully"));
    }

//    public function sendNotificationToAll(Request $request){
//        $validator = Validator::make($request->all(), [
//            'driver_id' => 'required',
//        ]);
//        if ($validator->fails()) {
//            return redirect()->back()->withErrors(trans('admin.select_any_driver'));
//        }
//        $merchant_id = get_merchant_id();
//        $string_file = $this->getStringFile($merchant_id);
//        $driver_ids = $request->driver_id;
//        if(!is_array($driver_ids)){
//            $driver_ids = [$driver_ids];
//        }
////        $data['notification_type'] = "DOCUMENT_EXPIRED";
//        foreach($driver_ids as $driver_id){
//            $driver = Driver::find($driver_id);
//            setLocal($driver->language);
//            $arr_param = ['driver_id' => $driver_ids, 'data' => $data, 'message' => trans("$string_file.document_expire_warning"), 'merchant_id' => $merchant_id, 'title' => trans("$string_file.docs_going_expire")];
//            Onesignal::DriverPushMessage($arr_param);
//        }
//        setLocal();
//        return redirect()->back()->withSuccess(trans("$string_file.notification_sent_successfully"));
//    }

//    public function ShowPersonalDocs(Request $request)
//    {
//        $driver = Driver::findorfail($request->input('driver_id'));
//        $merchant_id = get_merchant_id();
//        $merchant = Merchant::findorfail($merchant_id);
//        $driver_document = DriverDocument::with(['Document'])->where([['driver_id', '=', $driver->id], ['document_verification_status', '=', 4]],['status', '=', 1])->get();
//        $driver_document_name = $driver_document->map(function ($item, $key) use ($merchant) {
//            return array(
//                'document_id' => $item->Document->id,
//                'document_name' => $item->Document->getDocumentAttribute->documentname,
//                'document_file' => get_image($item->document_file, 'driver_document', $merchant->id),
//                'document_verification_status' => $item->document_verification_status,
//                'document_expiry_date' => $item->expire_date
//            );
//        });
//        $html = '';
//        foreach ($driver_document_name as $key => $value) {
//            $html .= '<div class="row">';
//            $html .= '<div class="col-md-4">';
//            $html .= '<div class="form-group">' . $value['document_name'];
//            $html .= '</div>';
//            $html .= '</div>';
//
//            $html .= '<div class="col-md-4">';
//            $html .= '<div class="form-group">' . $value['document_expiry_date'];
//            $html .= '</div>';
//            $html .= '</div>';
//
//            $html .= '<div class="col-md-4">';
//            $html .= '<div class="form-group"><a target="_blank" href="' . $value['document_file'] . '"><center class="m-t-10"><img src="' . $value['document_file'] . '" height="50px" width="50px"></a>';
//            $html .= '</div>';
//            $html .= '</div>';
//        }
//        echo $html;
//
//    }
//
//    public function ShowVehicleDocs(Request $request)
//    {
//        $driver = Driver::findorfail($request->input('driver_id'));
//        $merchant_id = get_merchant_id();
//        $driver_vehicle_documentss = DriverVehicle::with(['DriverVehicleDocument' => function ($query) {
//            $query->where([['document_verification_status', '=', '4'],['status', '=', 1]])->select('id', 'driver_vehicle_id', 'document', 'document_id', 'expire_date', 'document_verification_status');
//        }])->where([['owner_id', '=', $driver->id], ['vehicle_verification_status', '=', '4']])->get();
//        foreach ($driver_vehicle_documentss as $doc) {
//            foreach ($doc->DriverVehicleDocument as $value) {
//                $document_name[] = $value->Document->DocumentName;
//            }
//            $doc->VehicleTypeName = $doc->VehicleType->VehicleTypeName;
//            $doc->vehicleTypeImage = $doc->VehicleType->vehicleTypeImage;
//            $doc->VehicleMakeName = $doc->VehicleMake->VehicleMakeName;
//            $doc->VehicleModelName = $doc->VehicleModel->VehicleModelName;
//        }
//        $html = '';
//        foreach ($driver_vehicle_documentss as $key => $value) {
//            $i = 0;
//            foreach ($value->DriverVehicleDocument as $docs) {
//                $html .= '<div class="row">';
//
//                $html .= '<div class="col-md-3">';
//                $html .= '<div class="form-group">' . $value['vehicle_number'];
//                $html .= '</div>';
//                $html .= '</div>';
//
//                $html .= '<div class="col-md-3">';
//                $html .= '<div class="form-group">' . $document_name[$i];
//                $html .= '</div>';
//                $html .= '</div>';
//
//                $html .= '<div class="col-md-3">';
//                $html .= '<div class="form-group">' . $docs['expire_date'];
//                $html .= '</div>';
//                $html .= '</div>';
//
//                $html .= '<div class="col-md-3">';
//                $html .= '<div class="form-group"><a target="_blank" href="' . get_image($docs['document'], 'vehicle', $merchant_id) . '"><center class="m-t-10"><img src="'. get_image($docs['document'], 'vehicle', $merchant_id) . '" height="50px" width="50px"></a>';
//                $html .= '</div>';
//                $html .= '</div>';
//                $html .= '</div>';
//                $i++;
//            }
//        }
//        echo $html;
//    }

//    public function UploadVehicleDocs(Request $request){
//        $validator = Validator::make($request->all(), [
//            'driver_vehicle_id' => 'required',
//            'uploadDocs' => 'required',
//            'uploadDocs.*' => 'image|mimes:jpeg,jpg,png',
//            'expireDate' => 'required',
//            'doc_type' => 'required'
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return redirect()->back()->with('error',$errors[0]);
//        }
//        $merchant_id = Auth::user('merchant')->paranet_id != 0 ?  Auth::user('merchant')->paranet_id : Auth::user('merchant')->id;
//        $driverDetail = DriverVehicle::select('driver_id')->where('id',$request->driver_vehicle_id)->first();
//        $allDocs = $request->uploadDocs;
//        $allExpireDate = $request->expireDate;
//        foreach ($allDocs as $key => $value){
//            if (!empty($value)){
//                $image = $this->uploadImage($value,'vehicle_document',$merchant_id,'multiple');
//                if ($request->doc_type == 1){
//                    DriverVehicleDocument::where([['driver_vehicle_id','=',$request->driver_vehicle_id],['document_id','=',$key]])
//                        ->update([
//                            'temp_document_file' => $image,
//                            'temp_expire_date' => $allExpireDate[$key],
//                            'temp_doc_verification_status' => 2
//                        ]);
//                }elseif ($request->doc_type == 2){
//                    DriverVehicle::where('driver_id',$driverDetail->driver_id)->update(['vehicle_verification_status' => 1]);
//                    DriverVehicleDocument::where([['driver_vehicle_id','=',$request->driver_vehicle_id],['document_id','=',$key]])
//                        ->update([
//                            'document' => $image,
//                            'expire_date' => $allExpireDate[$key],
//                            'document_verification_status' => 2
//                        ]);
//                }
//            }
//        }
//        return redirect()->back()->withSuccess(trans('admin.editDocSucess'));
//    }
//
//    public function uploadHandymanDocs(Request $request){
//        $validator = Validator::make($request->all(), [
//            'driver_vehicle_id' => 'required',
//            'uploadDocs' => 'required',
//            'uploadDocs.*' => 'image|mimes:jpeg,jpg,png',
//            'expireDate' => 'required',
//            'doc_type' => 'required'
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return redirect()->back()->with('error',$errors[0]);
//        }
//        $merchant_id = Auth::user('merchant')->paranet_id != 0 ?  Auth::user('merchant')->paranet_id : Auth::user('merchant')->id;
//        $driverDetail = DriverVehicle::select('driver_id')->where('id',$request->driver_vehicle_id)->first();
//        $allDocs = $request->uploadDocs;
//        $allExpireDate = $request->expireDate;
//        foreach ($allDocs as $key => $value){
//            if (!empty($value)){
//                $image = $this->uploadImage($value,'vehicle_document',$merchant_id,'multiple');
//                if ($request->doc_type == 1){
//                    DriverVehicleDocument::where([['driver_vehicle_id','=',$request->driver_vehicle_id],['document_id','=',$key]])
//                        ->update([
//                            'temp_document_file' => $image,
//                            'temp_expire_date' => $allExpireDate[$key],
//                            'temp_doc_verification_status' => 2
//                        ]);
//                }elseif ($request->doc_type == 2){
//                    DriverVehicle::where('driver_id',$driverDetail->driver_id)->update(['vehicle_verification_status' => 1]);
//                    DriverVehicleDocument::where([['driver_vehicle_id','=',$request->driver_vehicle_id],['document_id','=',$key]])
//                        ->update([
//                            'document' => $image,
//                            'expire_date' => $allExpireDate[$key],
//                            'document_verification_status' => 2
//                        ]);
//                }
//            }
//        }
//        return redirect()->back()->with('success',trans('admin.editDocSucess'));
//    }
//
//    public function UploadDriverDocs(Request $request){
//        $validator = Validator::make($request->all(), [
//            'driver_id' => 'required',
//            'uploadDocs' => 'required',
//            'uploadDocs.*' => 'image|mimes:jpeg,jpg,png',
//            'expireDate' => 'required',
//            'doc_type' => 'required'
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->messages()->all();
//            return redirect()->back()->with('error',$errors[0]);
//        }
//        $merchant_id = Auth::user('merchant')->paranet_id != 0 ?  Auth::user('merchant')->paranet_id : Auth::user('merchant')->id;
//        $allDocs = $request->uploadDocs;
//        $allExpireDate = $request->expireDate;
//        foreach ($allDocs as $key => $value){
//            if (!empty($value)){
//                $image = $this->uploadImage($value,'driver_document',$merchant_id,'multiple');
//                if ($request->doc_type == 1){
//                    DriverDocument::where([['driver_id','=',$request->driver_id],['document_id','=',$key]])
//                        ->update([
//                            'temp_document_file' => $image,
//                            'temp_expire_date' => $allExpireDate[$key],
//                            'temp_doc_verification_status' => 2
//                        ]);
//                }elseif ($request->doc_type == 2){
//                    DriverDocument::where([['driver_id','=',$request->driver_id],['document_id','=',$key]])
//                        ->update([
//                            'document_file' => $image,
//                            'expire_date' => $allExpireDate[$key],
//                            'document_verification_status' => 2
//                        ]);
//                }
//            }
//        }
//        return redirect()->back()->with('success',trans('admin.editDocSucess'));
//    }
}
