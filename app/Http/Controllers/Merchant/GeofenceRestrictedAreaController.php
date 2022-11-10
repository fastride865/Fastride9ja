<?php


namespace App\Http\Controllers\Merchant;


use App\Http\Controllers\Controller;
use App\Models\CountryArea;
use App\Models\GeofenceAreaQueue;
use App\Models\InfoSetting;
use App\Models\Merchant;
use App\Models\RestrictedArea;
use App\Traits\AreaTrait;
use App\Traits\MerchantTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GeofenceRestrictedAreaController extends Controller
{
    use AreaTrait, MerchantTrait;

    public function __construct()
    {
        $info_setting = InfoSetting::where('slug','GEOFENCE_RESTRICT_AREA')->first();
        view()->share('info_setting', $info_setting);
    }

    public function RestrictedArea()
    {
        $checkPermission = check_permission(1, 'TAXI');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $areas = $this->getGeofenceAreaList(true);
        $service_areas = $this->getAreaList(false);
        $service_areas = $service_areas->get();
//        $service_areas = CountryArea::where([['is_geofence','=',2],['merchant_id', '=', $merchant_id]])->get();
        $area_list = [];
        if (!empty($service_areas)) {
            foreach ($service_areas as $service_area) {
                $area_list[$service_area->id] = $service_area->CountryAreaName;
            }
        }
        return view('merchant.geofence-restrict.index', compact('areas', 'area_list'));
    }

    public function EditRestrictedArea($id = NULL)
    {
        $checkPermission = check_permission(1, 'TAXI');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
//        $merchant = Merchant::find($merchant_id);
        $area = CountryArea::with('RestrictedArea')->where([['merchant_id', '=', $merchant_id]])->find($id);
        $areas = $this->getAreaList(false);
        $areas = $areas->get();
        $area_list = [];
        if (!empty($areas)) {
            foreach ($areas as $serviceArea) {
                $area_list[$serviceArea->id] = $serviceArea->CountryAreaName;
            }
        }
        if (isset($area->RestrictedArea) && empty($area->RestrictedArea)) {
            redirect()->back()->withErrors(trans('admin.restrict_area_not_found'));
        }
        return view('merchant.geofence-restrict.edit', compact('area', 'area_list'));
    }

    public function SaveRestrictedArea(Request $request, $id = NULL)
    {
        $checkPermission = check_permission(1, 'TAXI');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $request->validate([
            'restrict_area' => 'required|integer|between:1,3',
            'restrict_type' => 'required|integer|between:1,2',
            'base_areas' => 'required'
        ]);
        if ($id == NULL) {
            return redirect()->route('geofence.restrict.index')->withErrors(trans('admin.geofence_area_not_found'));
        }
        DB::beginTransaction();
        try {
            $area = CountryArea::findOrFail($id);
            RestrictedArea::updateOrCreate(
                ['country_area_id' => $area->id, 'merchant_id' => $area->merchant_id],
                ['restrict_area' => $request->restrict_area,
                    'restrict_type' => $request->restrict_type,
                    'status' => 1,
                    'base_areas' => implode(',', $request->base_areas),
                    'queue_system' => (isset($request->queue_system) && $request->queue_system == 'on') ? 1 : 0]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            DB::rollback();
            return redirect()->route('geofence.restrict.index')->withErrors($message);
        }
        DB::commit();
        return redirect()->route('geofence.restrict.index')->with('success', trans('admin.geo_fence_update'));
    }

    public function ViewGeofenceQueue($id)
    {
        $checkPermission = check_permission(1, 'TAXI');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $area = CountryArea::Where('merchant_id', $merchant_id)->findOrFail($id);
        $queue_managements = GeofenceAreaQueue::where([['geofence_area_id', '=', $id]])->paginate(25);
        return view('merchant.geofence-restrict.viewgeofencequeue', compact('queue_managements', 'area'));
    }

    public function SearchViewGeofenceQueue(Request $request, $id = NULL)
    {
        $checkPermission = check_permission(1, 'TAXI');
        if ($checkPermission['isRedirect']) {
            return $checkPermission['redirectBack'];
        }
        $merchant_id = get_merchant_id();
        $area = CountryArea::Where([['merchant_id', '=', $merchant_id], ['is_geofence', '=', 1]])->findOrFail($id);
        $query = GeofenceAreaQueue::where([['merchant_id', $merchant_id], ['geofence_area_id', '=', $id]]);
        if ($request->active_queue == 'on') {
            $query->where([['queue_status', '=', 1], ['exit_time', '=', null]]);
        } else {
            if ($request->date) {
                $query->whereDate('created_at', $request->date);
            }
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orwhere('last_name', 'LIKE', "%$keyword%");
            });
        }
        $queue_managements = $query->orderBy('created_at')->paginate(25);
        return view('merchant.geofence-restrict.viewgeofencequeue', compact('queue_managements', 'area'));
    }
}