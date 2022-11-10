<?php

namespace App\Http\Controllers\Merchant;

use App\Models\InfoSetting;
use App\Models\LangAppNavDrawer;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\MerchantNavDrawer;
use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use App\Traits\MerchantTrait;

class NavigationController extends Controller
{
    use ImageTrait,MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug', 'NAVIGATION_DRAWER')->first();
        view()->share('info_setting', $info_setting);
    }

    public function index()
    {
        $merchant_id = \Auth::user('merchant')->parent_id != 0 ? \Auth::user('merchant')->parent_id : \Auth::user('merchant')->id;
        $index_list = MerchantNavDrawer::where([['merchant_id', $merchant_id]])->orderBy('sequence')->paginate(15);
        return view('merchant.navigation.index', compact('index_list'));
    }

    public function create()
    {
        //
    }

    public function ChangeStatus($id, $status)
    {
        $validator = Validator::make(
            [
                'id' => $id,
                'status' => $status,
            ],
            [
                'id' => ['required', 'integer'],
                'status' => ['required', 'integer', 'between:0,1'],
            ]);
        if ($validator->fails()) {
            return redirect()->back();
        }
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        if($merchant->demo == 1)
        {
            return redirect()->back()->withErrors(trans("$string_file.demo_warning_message"));
        }
        $navigation = MerchantNavDrawer::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $navigation->status = $status;
        $navigation->save();
        return redirect()->back()->withSuccess(trans("$string_file.status_updated"));
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $edit = MerchantNavDrawer::findorfail($id);
        $merchant = get_merchant_id(false);
        $is_demo = $merchant->demo == 1 ? true : false;
        return view('merchant.navigation.edit', compact('edit','is_demo'));
        //
    }

    public function update(Request $request, $id)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|file|image|mimes:jpeg,bmp,png',
            'sequence' => 'required|integer|gte:1',
        ], [
            'sequence.gte' => 'Check Sequence number',
        ]);
        DB::beginTransaction();
        try {
            $update_lang_data = $request->only(['name']);
            $update = MerchantNavDrawer::findorfail($id);
            $update->sequence = $request->sequence;
            if ($request->hasFile('image') && $request->file('image') instanceof UploadedFile):
                $update->image = $this->uploadImage('image', 'drawericons');
            endif;
            $update->save();
            $this->saveLangNavigations(collect($update_lang_data), $update);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->back()->withSuccess(trans("$string_file.saved_successfully"));
    }

    private function saveLangNavigations(Collection $coming_collection, MerchantNavDrawer $coming_lang_data)
    {
        $collect_coming_data = $coming_collection->toArray();
        $merchant_id = get_merchant_id();
        $update_lang_coming_data = LangAppNavDrawer::where([['merchant_nav_drawer_id', '=', $coming_lang_data->id], ['locale', '=', \App::getLocale()]])->first();
        if (!empty($update_lang_coming_data)) {
            $update_lang_coming_data['name'] = $collect_coming_data['name'];
            $update_lang_coming_data->save();
        } else {
            $language_coming_data = new LangAppNavDrawer([
                'merchant_id' => $merchant_id,
                'merchant_nav_drawer_id' => $coming_lang_data->id,
                'locale' => \App::getLocale(),
                'name' => $collect_coming_data['name'],
            ]);
            $coming_lang_data->LanguageAppNavigationDrawers()->save($language_coming_data);
        }
    }

    public function destroy($id)
    {
        //
    }
}
