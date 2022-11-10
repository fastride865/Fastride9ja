<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Document;
use App\Models\Driver;
use App\Models\InfoSetting;
use Auth;
use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use DB;
use App\Traits\MerchantTrait;

class DocumentController extends Controller
{
    use MerchantTrait;
    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','DOCUMENTS')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $checkPermission =  check_permission(1,'view_documents');
        if ($checkPermission['isRedirect']){
            return  $checkPermission['redirectBack'];
        }

        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $documents = Document::where('merchant_id', $merchant_id)->latest()->paginate(25);
        $status =get_status(true,$string_file);// \Config::get('custom.document_status');
        return view('merchant.document.index', compact('documents','status'));
    }

    public function SaveLanguageDoc($merchant_id, $id, $documentname)
    {
        App\Models\LanguageDocument::updateOrCreate([
            'merchant_id' => $merchant_id, 'locale' => App::getLocale(), 'document_id' => $id
        ], [
            'documentname' => $documentname,
        ]);
    }

    public function ChangeStatus($id, $status)
    {
        $validator = Validator::make(
            [
                'id' => $id,
                'status' => $status,
            ],
            [
                'id' => ['required'],
                'status' => ['required', 'integer', 'between:1,2'],
            ]);
        if ($validator->fails()) {
            return redirect()->back();
        }
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        if($merchant->demo == 1)
        {
            return redirect()->back()->withErrors(trans("$string_file.demo_warning_message"));
        }
        $merchant_id = $merchant->id;

        $document = Document::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        if($status == 2 && isset($document->CountryAreas)) {
            $area_name = '';
            foreach($document->CountryAreas as $area) {
                $area_name .= $area->CountryAreaName . ' , ';
            }
            return redirect()->back()->withErrors(trans("$string_file.document_attached_to_service_area"));
        }
        $document->documentStatus = $status;
        $document->save();
        return redirect()->back()->withSuccess(trans("$string_file.status_updated"));
    }

    /**
     * Add Edit form of duration
     */
    public function add(Request $request, $id = null)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $is_demo = $merchant->demo == 1 ? true : false;
        $string_file = $this->getStringFile(NULL,$merchant);
        $data = [];
        if(!empty($id))
        {
            $data = Document::findorfail($id);
            $pre_title = trans("$string_file.edit") ;
            $submit_button = trans("$string_file.update");
        }
        else
        {
            $pre_title = trans("$string_file.add") ;
            $submit_button = trans("$string_file.save");
        }
        $title = $pre_title.' '.trans("$string_file.document");
        $return['document'] = [
            'data'=>$data,
            'submit_url'=>url('merchant/admin/document/save/'.$id),
            'title'=>$title,
            'document_status'=>add_blank_option(get_status(true,$string_file),trans("$string_file.select")),
            'submit_button'=>$submit_button,
        ];
        $return['is_demo'] = $is_demo;
        return view('merchant.document.form')->with($return);
    }
    /***
     * Save/update function of duration
     */
    public function save(Request $request, $id = NULL)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $validator = Validator::make($request->all(), [
            'documentname' => ['required','max:80',
                Rule::unique('language_documents', 'documentname')->where(function ($query) use ($merchant_id,$id) {
                    return $query->where([['merchant_id', '=', $merchant_id], ['locale', '=', App::getLocale()]])->where(function($qq) use ($id){
                        if(!empty($id))
                        {
                          $qq->where('document_id','!=',$id);
                        }
                    });
                })],
            'expire_date' => 'required|between:1,2',
            'documentNeed' => 'required|between:1,2',
            'document_number_required' => 'required|between:1,2'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        DB::beginTransaction();
        try {
            if(!empty($id))
            {
                $doc = Document::Find($id);
                //  mandatory to non mandatory case
//                if($doc->documentNeed == 1 && $request->documentNeed == 2 || $doc->expire_date == 1 && $request->expire_date == 2)
//                {
//                    return redirect()->back()->withErrors(trans('admin.not_possible'));
//                }
                if($doc->documentNeed == 2 && $request->documentNeed == 1)
                {
                    $online_driver = get_online_and_busy_drivers($merchant_id);
                    if($online_driver > 0)
                    {
                        return redirect()->back()->withErrors(trans("$string_file.driver_online_message"));
                    }
                    else{
                          Driver::where('driver_delete', NULL)->where('merchant_id',$merchant_id)->update(array('free_busy' => 2,'login_logout' => 2,'online_offline' => 2));
                          $status = has_driver_multiple_or_existing_vehicle(null,$merchant_id,'merchant');
                           if($status == true)
                           {
                               // make all vehicle activates
                               DB::table('driver_driver_vehicle as ddv')->join('driver_vehicles as dv')->where('dv.merchant_id',$merchant_id)->update(['dvv.vehicle_active_status'=>2]);
                           }
                        }
                }
                // non expiry date to expiry date
                if($doc->expire_date == 2 && $request->expire_date == 1)
                {
                     $expire_date =  $request->expire_date_value;
                     if(!empty($expire_date))
                     {
                         // update expire date of personal document
                        DB::table('driver_documents as dd')
                            ->join('drivers as d','dd.driver_id','=','d.id')
                            ->where('d.merchant_id',$merchant_id)
                            ->where('dd.status',1)
                            ->where('dd.document_id',$id)->update(['dd.expire_date'=>$expire_date]);

                        // update expire date of vehicle document
                        DB::table('driver_vehicle_documents as dvd')
                            ->join('driver_vehicles as dv','dvd.driver_vehicle_id','=','dv.id')
                            ->where('dv.merchant_id',$merchant_id)
                            ->where('dvd.status',1)
                            ->where('dvd.document_id',$id)->update(['dvd.expire_date'=>$expire_date]);
                     }
                }
            }
            else{
                $doc = new Document;
            }
            $doc->expire_date = $request->expire_date;
            $doc->documentNeed = $request->documentNeed;
            $doc->merchant_id = $merchant_id;
            $doc->document_number_required = $request->document_number_required;
            $doc->save();
            $this->SaveLanguageDoc($merchant_id, $doc->id, $request->documentname);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->route('documents.index')->withSuccess(trans("$string_file.saved_successfully"));
    }

}
