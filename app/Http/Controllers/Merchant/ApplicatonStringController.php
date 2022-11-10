<?php

namespace App\Http\Controllers\Merchant;

use App\Models\ApplicationMerchantString;
use App\Models\ApplicationModule;
use App\Models\ApplicationString;
use App\Models\ApplicationStringLanguage;
use App\Traits\ContentTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\MerchantTrait;
use App;
use Illuminate\Support\Facades\Session;
ini_set('max_execution_time', 300);

class ApplicatonStringController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use MerchantTrait, ContentTrait;
    public function index()
    {
        $checkPermission = check_permission(1, 'view_language_strings');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $application_string = [];
        $locale = app()->getLocale();
        $merchant = get_merchant_id(false);
        $app_string_group = $merchant->app_string_group;
        $app_string_group = !empty($app_string_group) ? json_decode($app_string_group) : [];
        $merchant_id = get_merchant_id();
//        $application_string = ApplicationString::whereIn('string_group_name',$app_string_group)->paginate(30);
//        $application_merchant_string = ApplicationMerchantString::where([['merchant_id','=',$merchant_id],['locale','=',$locale]])->paginate(30);
//        $application_string_language = ApplicationStringLanguage::where([['locale','=',$locale]])->paginate(30);
        $application_string = ApplicationString::whereIn('string_group_name',$app_string_group)
            ->with(['ApplicationStringLanguage'=>function($q) use($locale){
                $q->where('locale','en');
            }])
            ->whereHas('ApplicationStringLanguage',function($q) use($locale){
                $q->where('locale','en');
            })
            ->with(['ApplicationMerchantString'=>function($q) use($locale){
                $q->whereIn('locale',['en',$locale]);
            }])
            ->whereHas('ApplicationMerchantString',function($q) use($locale){
                $q->whereIn('locale',[$locale]);
            })
            ->paginate(30);
//        p($application_string);
//        $application_merchant_string = ApplicationMerchantString::where([['merchant_id','=',$merchant_id],['locale','=',$locale]])->paginate(30);
//        $application_string_language = ApplicationStringLanguage::where([['locale','=','en']])->paginate(30);
        return view('merchant.application_string.index',compact('application_string'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getStringVal(Request $request){
        $merchant = get_merchant_id(false);
        $app_string_group = $merchant->app_string_group;
        $app_string_group = !empty($app_string_group) ? json_decode($app_string_group) : [];
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
//        $keyword = $request->loc;
        $keyword = \App::getLocale(); // get locale
        $string_keys = ApplicationString::
        with(['ApplicationStringLanguage'=>function ($q) use ($keyword){
            $q->where('locale', 'en');
        }])->whereHas('ApplicationStringLanguage',function($q) use($keyword) {
            $q->where('locale', 'en');
        })
            ->where([['platform', '=', $request->platform],['application', '=', $request->app]])
            ->whereIn('string_group_name',$app_string_group)
            ->get();
//        p($string_keys);
        $text_row ="<div class='row'>";
        $row_end_text = "</div><br>";
        $column_text = "";
        $num = 1;
        $empty_string = trans("$string_file.data_not_found");
        $empty_text = "<div class='col-md-12 text-center'><div class='form-group'>".$empty_string."</div></div>";
        $final_text =$text_row.$empty_text.$row_end_text;
        $result = false;
        if($string_keys->count() > 0)
        {
        foreach ($string_keys as $string_key){
            $merchant_string = ApplicationMerchantString::where([['merchant_id','=',$merchant_id],['application_string_id','=',$string_key->id],['locale','=',$keyword]])->first();
            $str = $string_key->ApplicationStringLanguage;
            $string_name_loc = isset($str[0]->string_value) ? $str[0]->string_value : '----';
            if(!empty($merchant_string->string_value))
             {
                 $status = '<span class="green-500"> <i class="fa fa-check" title="'.trans("$string_file.translation_done").'"></i> </span>';
             }
             else
             {
                 $status = '<span class="red-500"> <i class="fa-info" title="'.trans("$string_file.translation_pending").'"></i> </span>';
             }
             if(!empty($merchant_string))
             {
                 $string_val_name =  $merchant_string->string_value;
             }
             else
             {
                 $string_in_locale = ApplicationStringLanguage::where([['application_string_id','=',$string_key->id],['locale','=',$keyword]])->first();
                 if(!empty($string_in_locale))
                 {
                 $string_val_name =   $string_in_locale->string_value;
                 }
                 else
                 {
                     $string_val_name  = $string_name_loc;
                 }
             }
            if ($keyword == 'zh' || $keyword == 'ko' || $keyword == 'ja' || $keyword == 'ar' || $keyword == 'lo'){
                $stringName = $string_val_name;
            }else{
                $stringName = $string_val_name;
//                $stringName = utf8_encode($string_val_name);
            }
            $column_text .= '<div class="col-md-4">
                              <div class="form-group">
                              <label>'.$num.')'.$string_name_loc.$status.'
                              </label>
                             <input type="text" name="name['.$string_key->id.']" value="'.$stringName.'" class="form-control">
                            </div>
                            </div>';


            //faced single quatation issue in string

            // "<div class='col-md-4'>
            //                   <div class='form-group'>
            //                   <label>".$num.') '.$string_name_loc.". $status
            //                   </label>
            //                  <input type='text' name='name[$string_key->id]' value='".$stringName."' class='form-control'>
            //                 </div>
            //                 </div>";
            $num++;
        }
        $final_text = $text_row.$column_text.$row_end_text;
        $result = true;
        }
        $searched_param = $request->all();
        return view('merchant.application_string.edit',compact('final_text','searched_param','result'));
    }

    public function customSave(Request $request){
        $request->validate([
            'name' => 'required',
            'loc' => 'required'
        ]);

        $merchant_id = get_merchant_id();
        $string_keys = $request->name;
        $var = [];
        $last_string_version = ApplicationMerchantString::select('version')->where([['merchant_id','=',$merchant_id],['locale','=',$request->loc]])->latest('version')->first();
        foreach ($string_keys as $key => $value){
            if ($value){
                $string_version = ApplicationMerchantString::where([['merchant_id','=',$merchant_id],['application_string_id','=',$key],['locale','=',$request->loc]])->latest()->first();
                if ($string_version){
                    $string_version->string_value = $value;
                    $string_version->version = sprintf("%.1f", $last_string_version->version+0.1);
                    $string_version->save();
                }else{
                    $ver = $last_string_version ? $last_string_version->version+0.1 : 1.0;
                    $ver = sprintf("%.1f", $ver);
                    $merchant_string = new ApplicationMerchantString;
                    $merchant_string->merchant_id = $merchant_id;
                    $merchant_string->application_string_id = $key;
                    $merchant_string->string_value = $value;
                    $merchant_string->locale = $request->loc;
                    $merchant_string->version = $ver;
                    $merchant_string->save();
                }
                $var[0] = "String Updated Successfully !!";
            }else{
                if (empty($var)){
                    $var[0] = "No String Updated !!";
                }
            }
        }
        return redirect()->back()->with('success',$var[0]);
    }

//    public function custom(){
//        $strings = ApplicationModule::with('ApplicationString')->where([['name','=','General']])->first();
//        return view('merchant.application_string.custom',compact('strings'));
//    }

    public function customEdit(){
//        $strings = ApplicationModule::with('ApplicationString')->where([['name','=','General']])->first();
        $final_text = "";
        $searched_param = [];
        $result = false;
        return view('merchant.application_string.edit',compact('final_text','searched_param','result'));
    }

    public function checkModule($merchant_id= null, $module_name = null){
        $merchant = Merchant::find($merchant_id);

        switch ($module_name):
            case 'corporate':
                return ($merchant->Configuration->corporate_admin == 1) ? true : false;
                break;
            case 'package':
                return (in_array(2,$merchant->Service) || in_array(3,$merchant->Service) || in_array(4,$merchant->Service)) ? true : false;
                break;
            case 'taxi_company':
                break;
            case 'franchisee':
                return ($merchant->franchisees_active == 1) ? true : false;
                break;
            case 'hotel':
                return ($merchant->hotel_active == 1) ? true : false;
                break;
            case 'cashback':
                return ($merchant->Configuration->cashback_module == 1) ? true : false;
                break;
            case 'email_configuration':
                return ($merchant->Configuration->email_functionality == 1) ? true : false;
                break;
            case 'security_question':
                return ($merchant->ApplicationConfiguration->security_question == 1) ? true : false;
                break;
            case 'subscription_package':
                return ($merchant->Configuration->subscription_package == 1) ? true : false;
                break;
            case 'surcharge':
                return ($merchant->ApplicationConfiguration->sub_charge == 1) ? true : false;
                break;
            case 'wallet_recharge':
                return ($merchant->Configuration->user_wallet_status == 1 || $merchant->Configuration->driver_wallet_status == 1) ? true : false;
                break;

            case 'child_terms_condition':
                return ($merchant->Configuration->family_member_enable == 1) ? true : false;
                break;

            case 'driver_commission_choices':
                return ($merchant->Configuration->subscription_package == 1 && $merchant->ApplicationConfiguration->driver_commission_choice == 1) ? true : false;
                break;

            case 'driver-account-types':
                return ($merchant->Configuration->bank_details_enable == 1) ? true : false;
                break;

            /*Corporate, Packages, Taxi Company, Franchisee, Hotel, Cashback, Email Configurations, Security Question,
            Subscription Package, SurCharge, Wallet_recharge, Child Terms*/
            default:
        endswitch;
    }

    public function ExportString(Request $request){
        $request->validate([
            'platform' => 'required',
            'app' => 'required'
        ]);
        $keyword = app()->getLocale();
        $merchant_id = get_merchant_id();

        $string_keys = ApplicationString::with(['ApplicationStringLanguage'=>function ($q) use ($keyword){
            $q->where('locale', '=', $keyword);
        }])->where([['platform', '=', $request->platform],['application', '=', $request->app]])->get();


        $fileName = $request->app == 'USER' ? $request->app:'DRIVER';
        if ($request->platform == "android"){
            $string_version = ApplicationMerchantString::where([['merchant_id','=',$merchant_id],['locale','=',$keyword]])->latest()->first();
            $version = $string_version ? $string_version->version : 1.0;
            header('Content-type: text/xml');
            header('Content-Disposition: attachment; filename="'.$fileName.'_version_'.$version.'.xml"');
            $xmlString = '<resources>';
            foreach ($string_keys as $string_key){
                $merchant_string = ApplicationMerchantString::where([['merchant_id','=',$merchant_id],['application_string_id','=',$string_key->id],['locale','=',$keyword]])->first();
                $string_name_loc = $string_key->ApplicationStringLanguage->toArray() ? $string_key->ApplicationStringLanguage[0]->string_value : '----';
                $string_val_name = $merchant_string ? $merchant_string->string_value : $string_name_loc;
                $xmlString .= '
                <string name="'.$string_key->string_key.'">'.htmlspecialchars($string_val_name).'</string>';
            }
            $xmlString .= '</resources>';

            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->preserveWhiteSpace = FALSE;
            $dom->loadXML($xmlString);
            libxml_use_internal_errors(false);
            echo $xmlString;

        }elseif ($request->platform == "ios"){
            $string_version = ApplicationMerchantString::where([['merchant_id','=',$merchant_id],['locale','=',$keyword]])->latest()->first();
            $version = $string_version ? $string_version->version : 1.0;
            $xmlString = "String Version :".$version;
            header("Content-Type: text/plain");
            header('Content-Disposition: attachment; filename="'.$fileName.'.strings"');
            foreach ($string_keys as $string_key){
                $merchant_string = ApplicationMerchantString::where([['merchant_id','=',$merchant_id],['application_string_id','=',$string_key->id],['locale','=',$keyword]])->first();

                $string_name_loc = $string_key->ApplicationStringLanguage->toArray() ? $string_key->ApplicationStringLanguage[0]->string_value : '----';
                $string_val_name = $merchant_string ? $merchant_string->string_value : $string_name_loc;
                $xmlString .= '
                "'.$string_key->string_key.'" = "'.$string_val_name.'";
                ';
            }
            $dom = new \DOMDocument();
            $dom->preserveWhiteSpace = FALSE;
            $dom->loadHTML($xmlString);
            echo $xmlString;
        }
    }

    // get module language file
    public function moduleLanguageStrings()
    {
        $checkPermission = check_permission(1, 'view_language_strings');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant = get_merchant_id(false);
        $locale = App::getLocale();
        // var_dump(Session::has('locale'));
        // p(Session::get('locale'),0);
        // p('end');
        // p($locale);
        // www-data
        //xcx
        // it will return only returned data of file
        $module_file = $this->getStringFile(NULL,$merchant);
        $module_file = $module_file.'.php';
        $string_group = $merchant->string_group;
        $merchant_lang_file = "";
        $merchant_file_exist = false;
        try {
            // check merchant file in selected locale
            $file = base_path().'/resources/lang/'.$locale.'/'.$module_file;
           // p($file);
            if(file_exists($file))
            {
                $merchant_lang_file = require($file);
                $merchant_file_exist = true;
            }
            else
            {
                // check merchant file in english locale
                $file = base_path().'/resources/lang/en/'.$module_file;
                if(file_exists($file))
                {

                    $merchant_lang_file = require($file);
                }
                else
                {
                    // check all_in_one file in english locale
                    $file = base_path().'/resources/lang/en/all_in_one.php';
                    $merchant_lang_file = require($file);
                }
            }
        }catch (\Exception $e)
        {
            p($e->getMessage());
            return redirect()->back()->withErrors($e->getMessage());
        }
        $language_strings = [];
        $project_strings =  $this->langaugeString($string_group);
        $info_setting = App\Models\InfoSetting::where('slug', 'LANGUAGE_STRING')->first();
        return view('merchant.language-file.module-strings', compact('language_strings','merchant_lang_file','info_setting','project_strings','merchant_file_exist'));
    }
    // save module string files
    public function submitModuleLanguageStrings(Request $request)
    {
        try {
            $merchant = get_merchant_id(false);
            $locale = App::getLocale();
            $module_file = $this->getStringFile(NULL,$merchant);
            $string_file = $module_file;
            $module_file = $module_file.'.php';

//            p($module_file);
            $file = base_path().'/resources/lang/'.$locale.'/'.$module_file;

            if(file_exists($file))
            {
                /*
                 * Original file for string storage
                */
                $string_file_data = fopen($file, "w+") or die("Unable to open file!");
                // add php tag
                $content = "<?php\n\n";
                fwrite($string_file_data, $content);
                // add return
                $content = "return ";
                fwrite($string_file_data, $content);

                // submitted key by merchant
                $dummyArr = $request->all()['name'];
                // add key array
                fwrite($string_file_data, var_export($dummyArr,true));
                // add semi colon
                $content = ";";
                $a = fwrite($string_file_data, $content);
                fclose($string_file_data);

                /*
                 * Backup file for string storage
                */
                $backup_file_name = $string_file.'_'.$locale.'_'.date("Y-m-d").'.php';
                $backup_file = public_path('locale-files/'.$backup_file_name);
                $backup_string_file_data = fopen($backup_file, "w+") or die("Unable to open file!");
                // add php tag
                $content = "<?php\n\n";
                fwrite($backup_string_file_data, $content);
                // add return
                $content = "return ";
                fwrite($backup_string_file_data, $content);
                // submitted key by merchant
                $dummyArr = $request->all()['name'];
                // add key array
                fwrite($backup_string_file_data, var_export($dummyArr,true));
                // add semi colon
                $content = ";";
                $a = fwrite($backup_string_file_data, $content);
                fclose($backup_string_file_data);

                return redirect()->route('merchant.module-strings')->withSuccess(trans("$string_file.string_file_updated"));
            }
            else
            {
                return redirect()->route('merchant.module-strings')->withErrors(trans("$string_file.string_file_not_found"));
            }
        }catch (\Exception $e)
        {
            p($e->getMessage());
            return redirect()->back()->withErrors($e->getMessage());

        }

    }
}
