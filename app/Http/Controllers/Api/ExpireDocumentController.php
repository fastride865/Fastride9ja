<?php

namespace App\Http\Controllers\Api;

use App\Models\Configuration;
use App\Models\DriverDocument;
use App\Models\DriverVehicle;
use App\Models\DriverVehicleDocument;
use App\Models\Merchant;
use App\Models\Onesignal;
use App\Models\VehicleMake;
use App\Models\VehicleType;
use App\Traits\AreaTrait;
use App\Traits\DriverTrait;
use App\Traits\ExpireDocument;
use Auth;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class ExpireDocumentController extends Controller
{
    use DriverTrait, AreaTrait, ExpireDocument;

    public function index(Request $request)
    {
        $driver = $request->user('api-driver');

        $current_date = date('Y-m-d');
        $reminder_days = Configuration::where('merchant_id','=',$driver->merchant_id)->select('reminder_doc_expire')->first();
        $reminder_days = $reminder_days->reminder_doc_expire == null ? 0 : $reminder_days->reminder_doc_expire;
        $reminder_last_date = date('Y-m-d',strtotime('+'.$reminder_days.' days'));
        $driver_document = DriverDocument::with(['Document'])->where([['driver_id', '=', $driver->id]])->whereIn('document_verification_status', [4, 3, 1])->get();
        $driver_document_name = $driver_document->map(function ($item, $key) use($current_date,$reminder_last_date) {
            $temp_doc_upload = getTempDocUpload($item['expire_date'],$current_date,$reminder_last_date);
            return array(
                'document_id' => $item->Document->id,
                'document_name' => $item->Document->getDocumentAttribute->documentname,
                'document_file' => get_image($item->document_file,'driver_document',$item->Driver->merchant_id),
                'document_verification_status' => $item->document_verification_status,
                'document_expiry_date' => $item->expire_date,
                'temp_doc_upload' => $temp_doc_upload
            );
        });

        $driver_vehicle_document = DriverVehicle::with(['DriverVehicleDocument' => function ($query) {
            $query->whereIn([['document_verification_status', [4, 1, 3]],['status', '=', 1]])->select('id', 'driver_vehicle_id', 'document', 'document_id', 'expire_date', 'document_verification_status');
        }])->where([['owner_id', '=', $driver->id], ['vehicle_verification_status', '=', 4]])->get();

        foreach ($driver_vehicle_document as $doc) {
            foreach ($doc->DriverVehicleDocument as $value) {
                $temp_doc_upload = getTempDocUpload($value['expire_date'],$current_date,$reminder_last_date);
                $value->document_name = $value->Document->DocumentName;
                $value->temp_doc_upload = $temp_doc_upload;
            }
            $doc->VehicleTypeName = $doc->VehicleType->VehicleTypeName;
            $doc->vehicleTypeImage = get_image($doc->VehicleType->vehicleTypeImage,'vehicle_document',$driver->merchant_id);
            $doc->VehicleMakeName = $doc->VehicleMake->VehicleMakeName;
            $doc->VehicleModelName = $doc->VehicleModel->VehicleModelName;
        }

        //if ((!empty($driver_document_name)) || (!empty($driver_vehicle_document->toArray()[0]))):  // IF ANY OF BOTH, HAVE DATA IN THEM
        if (($driver_document_name->isNotEmpty()) || ($driver_vehicle_document->isNotEmpty())):
            $driver_expired_documents = [
                'result' => '1',
                'expiry_visibility' => true,
                'expiry_message' => trans('api.expiry_document_message'),
                'expired_personal_documents' => $driver_document_name,
                'expired_vehicle_documents' => $driver_vehicle_document,
            ];
        else:
            $driver_expired_documents = [
                'result' => '0',
                'expiry_visibility' => false,
                'expiry_message' => trans('api.not_expiry_document_message'),
                'expired_personal_documents' => [],
                'expired_vehicle_documents' => [],
            ];
        endif;
        return $driver_expired_documents;
    }
}
