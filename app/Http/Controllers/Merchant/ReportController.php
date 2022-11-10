<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Booking;
use App\Models\CountryArea;
use App\Models\Driver;
use App\Models\DriverOnlineTime;
use App\Models\DriverVehicle;
use App\Models\DriverWalletTransaction;
use App\Models\PromoCode;
use App\Models\ReferralDiscount;
use App\Models\User;
use App\Models\UserWalletTransaction;
use App\Traits\AreaTrait;
use Auth;
use App\Traits\BookingTrait;
use Illuminate\Http\Request;
use Khill\Lavacharts\Lavacharts;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Traits\DriverTrait;
use App\Traits\MerchantTrait;


class ReportController extends Controller
{
    use BookingTrait, AreaTrait,DriverTrait,MerchantTrait;

    public function index()
    {
        $bookings = $this->bookings(true, [1005]);
        $data = [];
        return view('merchant.report.booking', compact('bookings','data'));
    }

    public function BookingVariance()
    {
        $bookings = $this->bookings(true, [1005]);
        $data = [];
        return view('merchant.report.booking_verinace', compact('bookings','data'));
    }

    public function SearchBookingVariance(Request $request)
    {
        $query = $this->bookings(false, [1005]);
        if ($request->booking_id) {
            $query->where('merchant_booking_id', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }
        $bookings = $query->paginate(25);
        $data = $request->all();
        return view('merchant.report.booking_verinace', compact('bookings','data'));
    }

    public function SearchBooking(Request $request)
    {
        $query = $this->bookings(false, [1005]);
        if ($request->booking_id) {
            $query->where('merchant_booking_id', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->whereRaw("CONCAT(`first_name`, `last_name`) LIKE ? ", "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->whereRaw("CONCAT(`first_name`, `last_name`) LIKE ? ", "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }
        $bookings = $query->paginate(25);
        $data = $request->all();
        return view('merchant.report.booking', compact('bookings','data'));
    }

    public function DriverAcceptance(Request $request)
    {
        $drivers = $this->getDriverBookingRequestData($request);
        $data = [];
        return view('merchant.report.driver_acceptance', compact('drivers','data'));
    }

    public function SearchDriverAcceptance(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        //$daterange = $request->daterange;
        $from = $request->from;
        $to = $request->to;
//        if(isset($daterange) && $daterange != ''){
//            $daterange = explode(' - ', $daterange);
//            $from = $daterange[0];
//            $to = $daterange[1];
//        }
        $keyword = '';
        if($request->parameter != ''){
            switch ($request->parameter) {
                case "1":
                    $parameter = "drivers.first_name";
                    break;
                case "2":
                    $parameter = "email";
                    break;
                case "3":
                    $parameter = "phoneNumber";
                    break;
            }
            $keyword = $request->keyword;
        }
        $query = Driver::with(['BookingRequestDriver' => function($q) use($from, $to){
                            $q->select('*'
                                ,\DB::raw('COUNT(*)AS total_trip')
                                ,\DB::raw('SUM(CASE WHEN request_status = 1 THEN 1 ELSE 0 END )AS no_response')
                                ,\DB::raw('SUM(CASE WHEN request_status = 2 THEN 1 ELSE 0 END )AS accepted')
                                ,\DB::raw('SUM(CASE WHEN request_status = 3 THEN 1 ELSE 0 END )AS reject')
                            );
                            if(!empty($from) && !empty($to)){
                                $q->whereBetween(DB::raw('DATE(created_at)'), array($from, $to));
                            }
                            $q->groupBy('driver_id');
                        }])->whereHas('BookingRequestDriver', function($query) use($from, $to){
                            if(!empty($from) && !empty($to)){
                                $query->whereBetween(DB::raw('DATE(created_at)'), array($from, $to));
                            }
                        })->where([['merchant_id', '=', $merchant_id]]);

        if($keyword != ''){
            $query->where($parameter, 'like', "%$keyword%");
        }
        $drivers = $query->paginate(25);
        $data = $request->all();
        return view('merchant.report.driver_acceptance', compact('drivers','data'));
    }

    public function UserWallet()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $wallet_transactions = UserWalletTransaction::where([['merchant_id', '=', $merchant_id]])->latest()->paginate(25);
        $data = [];
        return view('merchant.report.user_wallet', compact('wallet_transactions','data'));
    }

    public function SearchUserWallet(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'keyword' => "required",
            'parameter' => "required|integer|between:1,3",
        ]);
        switch ($request->parameter) {
            case "1":
                $parameter = \DB::raw('concat(`first_name`, `last_name`)');
                break;
            case "2":
                $parameter = "email";
                break;
            case "3":
                $parameter = "UserPhone";
                break;
        }
        $keyword = $request->keyword;
        $query = UserWalletTransaction::where([['merchant_id', '=', $merchant_id]]);
        $query->WhereHas('User', function ($q) use ($keyword, $parameter) {
            $q->where($parameter, 'LIKE', '%'. $keyword.'%');
        });
        $wallet_transactions = $query->paginate(25);
        $data = $request->all();
        return view('merchant.report.user_wallet', compact('wallet_transactions','data'));
    }

    public function DriverWallet()
    {
        $authMerchant = Auth::user('merchant')->load('CountryArea');
        $merchant_id = $authMerchant->parent_id != 0 ? $authMerchant->parent_id : $authMerchant->id;
        $wallet_transactions = DriverWalletTransaction::where([['merchant_id', '=', $merchant_id]])->latest();
        if (!empty($authMerchant->CountryArea->toArray())) {
            $area_ids = array_pluck($authMerchant->CountryArea, 'id');
            $wallet_transactions->whereHas('Driver',function ($query) use ($area_ids){
                $query->whereIn('country_area_id', $area_ids);
            });
        }
        $wallet_transactions = $wallet_transactions->paginate(25);
        $data = [];
        return view('merchant.report.driver_wallet', compact('wallet_transactions','data'));
    }

    public function SerachDriverWallet(Request $request)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $request->validate([
            'keyword' => "required",
            'parameter' => "required|integer|between:1,3",
        ]);
        switch ($request->parameter) {
            case "1":
                // $parameter = \DB::raw('concat("first_name", "last_name")');
                $parameter = "first_name";
                break;
            case "2":
                $parameter = "email";
                break;
            case "3":
                $parameter = "phoneNumber";
                break;
        }
        $keyword = $request->keyword;
        $query = DriverWalletTransaction::where([['merchant_id', '=', $merchant_id]]);
        $query->WhereHas('Driver', function ($q) use ($keyword, $parameter) {
            $q->where($parameter, 'LIKE', "%$keyword%");
        });
        $wallet_transactions = $query->paginate(25);
        $data = $request->all();
        return view('merchant.report.driver_wallet', compact('wallet_transactions','data'));
    }

    public function DriverCharts(Request $request)
    {
        $lava = new Lavacharts;
        $population = $lava->DataTable();
        $drivers = $this->DriverGrowth();
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        if(count($drivers) >0)
        {
        $population->addDateColumn(trans("$string_file.month"))
            ->addNumberColumn(trans("$string_file.no_of_drivers"))
            ->addRows($drivers);
        }
        $lava->AreaChart('Population', $population, [
            'title' => trans($string_file.".drivers").' '.trans($string_file.".growth"),
            'legend' => [
                'position' => 'in'
            ]
        ]);

        $reasons = $lava->DataTable();
        $driversArea = $this->DriverAreaWise();
        if(count($driversArea) >0) {
            $reasons->addStringColumn(trans("$string_file.service_area"))
                ->addNumberColumn('number')
                ->addRows($driversArea);
        }
        $lava->PieChart('IMDB', $reasons, [
            'title' => trans($string_file.".driver_comparision"),
            'is3D' => true,
        ]);


        $rate = $lava->DataTable();
        $ratings = $this->TopDriver();
        if(count($ratings) >0) {
            $rate->addStringColumn(trans($string_file.".rating_wise"))
                ->addNumberColumn(trans("$string_file.reviews").' & '.trans("$string_file.ratings"))
                ->addRows($ratings);
        }

        $lava->BarChart('Rating', $rate,[
            'series' => [0,1,2,3,4,5],
            'hAxis.gridlines.interval' => 1
        ]);

        $votes = $lava->DataTable();
        $topDrivers = $this->TopDriverByRevanue();
        if(count($topDrivers) >0) {
            $votes->addStringColumn(trans($string_file.".driver_revenue"))
                ->addNumberColumn(trans("$string_file.earning"))
                ->addRows($topDrivers);
        }
        $lava->BarChart('Votes', $votes, [
            'colors' => ['red', '#004411']
        ]);

        $services = $lava->DataTable();
        $driverServices = $this->DriverServices();
        if(count($driverServices) >0) {
            $services->addStringColumn(trans("$string_file.service_type"))
                ->addNumberColumn('Value')
                ->addRows($driverServices);
        }
        $lava->GaugeChart('Temps', $services, [
            'width' => 400,
            'greenFrom' => 0,
            'greenTo' => 1000,
            'yellowFrom' => 1001,
            'yellowTo' => 4999,
            'redFrom' => 5000,
            'redTo' => 10000,
            'majorTicks' => [
                'Start',
                'Excellent'
            ]
        ]);
        $vehicle = $lava->DataTable();
        $dricevehilces = $this->DriverVehicle();
        if(count($dricevehilces) >0) {
            $vehicle->addStringColumn(trans($string_file.".vehicle_type"))
                ->addNumberColumn('Number')
                ->addRows($dricevehilces);
        }
        $lava->DonutChart('Vehicle', $vehicle, [
            'title' => trans($string_file.".vehicle_comparision")
        ]);


        $finances = $lava->DataTable();
        $drivers = $this->DriverGrowth();
        if(count($drivers) >0) {
            $finances->addDateColumn('Month')
                ->addNumberColumn('Signup')
                ->setDateTimeFormat('F')
                ->addRows($drivers);
        }
        $lava->ColumnChart('Finances', $finances, [
            'title' => trans($string_file.".driver_signup_performance"),
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14
            ]
        ]);
        return view('merchant.report.driver', compact('lava'));
    }

    public function DriverVehicle()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $drivervehicles = DriverVehicle::selectRaw('vehicle_type_id,count(*) as total')->whereHas('Driver', function ($query) use ($merchant_id) {
            $query->where([['merchant_id', '=', $merchant_id]]);
        })->groupBy('vehicle_type_id')->get();
        $newArray = array();
        if (!empty($drivervehicles->toArray())) {
            foreach ($drivervehicles as $value) {
                $vehicleTypeName = $value->VehicleType->LanguageVehicleTypeSingle == "" ? $value->VehicleType->LanguageVehicleTypeAny->vehicleTypeName : $value->VehicleType->LanguageVehicleTypeSingle->vehicleTypeName;
                $a = array(0 => $vehicleTypeName, 1 => (int)$value->total);
                $newArray [] = $a;
            }
        } else {
            $newArray[] = array();
        }
        return $newArray;
    }

    public function DriverServices()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $driverVehicles = \DB::table('driver_vehicle_service_type')
            ->join('service_types', 'driver_vehicle_service_type.service_type_id', '=', 'service_types.id')
            ->join('driver_vehicles', 'driver_vehicle_service_type.driver_vehicle_id', '=', 'driver_vehicles.id')
            ->join('drivers', 'driver_vehicles.driver_id', '=', 'drivers.id')
            ->select('service_types.serviceName', 'service_type_id', \DB::raw('count(*) as total'))
            ->where('drivers.merchant_id', $merchant_id)
            ->groupBy('service_type_id')
            ->get();
        $newArray = array();
        if (!empty($driverVehicles)) {
            foreach ($driverVehicles as $value) {
                $a = array(0 => $value->serviceName, 1 => $value->total);
                $newArray [] = $a;
            }
        } else {
            $newArray[] = array();
        }
        return $newArray;
    }

    public function TopDriverByRevanue()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $drivers = Driver::where([['merchant_id', '=', $merchant_id], ['total_earnings', '!=', NULL]])->orderBy('total_earnings', 'desc')->take(10)->get();
        $newArray = array();
        if (!empty($drivers->toArray())) {
            foreach ($drivers as $value) {
                $a = array(0 => $value->fullName, 1 => $value->total_earnings);
                $newArray [] = $a;
            }
        } else {
            $newArray[] = array();
        }
        return $newArray;
    }

    public function TopDriver()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $drivers = Driver::where([['merchant_id', '=', $merchant_id]])->orderBy('rating', 'desc')->take(10)->get();
        $newArray = array();
        if (!empty($drivers->toArray())) {
            foreach ($drivers as $value) {
                $a = array(0 => $value->fullName, 1 => $value->rating);
                $newArray [] = $a;
            }
        } else {
            $newArray[] = array();
        }
        return $newArray;
    }

    public function DriverGrowth()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $driver = Driver::selectRaw('monthname(created_at) as month,count(*) as number')
            ->whereYear('created_at', '=', date('Y'))
            ->where('merchant_id', '=', $merchant_id)
            ->groupBy('month')
            ->orderByRaw('min(created_at) desc')
            ->get();
        $newArray = array();
        if (!empty($driver->toArray())) {
            foreach ($driver as $value) {
                $a = array(0 => $value->month, 1 => $value->number);
                $newArray [] = $a;
            }
        } else {
            $newArray[] = array();
        }
        return $newArray;
    }

    public function DriverAreaWise()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $drivers = Driver::selectRaw('country_area_id,count(*) as number')
            ->where([['merchant_id', '=', $merchant_id],['country_area_id','!=',NULL]])
            ->groupBy('country_area_id')
            ->get();
        $newArray = array();
        if (!empty($drivers->toArray())) {
            foreach ($drivers as $value) {
                $name = $value->CountryArea->LanguageSingle == "" ? $value->CountryArea->LanguageAny->AreaName : $value->CountryArea->LanguageSingle->AreaName;
                $a = array(0 => $name, 1 => (int)$value->number);
                $newArray[] = $a;
            }
        } else {
            $newArray[] = array();
        }
        return $newArray;
    }

    public function DriverOnlineTime()
    {
        $authMerchant = Auth::user('merchant')->load('CountryArea');
        $merchant_id = $authMerchant->parent_id != 0 ? $authMerchant->parent_id : $authMerchant->id;
        $driver_times = DriverOnlineTime::has('Driver')->where([['merchant_id', '=', $merchant_id]])->latest();
        if (!empty($authMerchant->CountryArea->toArray())) {
            $area_ids = array_pluck($authMerchant->CountryArea, 'id');
            $driver_times->whereHas('Driver',function ($query) use ($area_ids){
                $query->whereIn('country_area_id', $area_ids);
            });
        }
        $driver_times = $driver_times->paginate(25);
        $data = [];
        return view('merchant.report.driver_online_time', compact('driver_times','data'));
    }

    public function SearchDriverOnlineTime(Request $request)
    {
        $dName = $request->driver_name;
        $dEmail = $request->email;
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;

        $query = DriverOnlineTime::where([['merchant_id', '=', $merchant_id]])->latest();
        if (!empty($request->driver_name)) {
            $query->WhereHas('Driver', function ($q) use ($dName) {
                $q->where("first_name", 'LIKE', "%".$dName."%");
            });
        }
        if (!empty($request->email)) {
            $query->WhereHas('Driver', function ($q) use ($dEmail) {
                $q->where('email', 'LIKE', "%$dEmail%");
            });
        }
        $driver_times = $query->paginate(25);
        $data = $request->all();
        return view('merchant.report.driver_online_time', compact('driver_times','data'));
    }

//    public function CompanyReferral(){
//        $merchant_id = get_merchant_id();
//        $referral_details = ReferralDiscount::where([['merchant_id','=',$merchant_id],['sender_id','=',0],['sender_type','=',0]])->latest()->paginate(15);
//        $receiverBasic = array();
//        foreach ($referral_details as $referral_detail){
//            $receiverDetails = $referral_detail->receiver_type == "USER" ? User::find($referral_detail->receiver_id) : Driver::find($referral_detail->receiver_id);
//            if(!empty($receiverDetails)){
//                $phone = $referral_detail->receiver_type == "USER" ? $receiverDetails->UserPhone : $receiverDetails->phoneNumber;
//                $receiverBasic[] = array(
//                    'id' => $receiverDetails->id,
//                    'name' => $receiverDetails->first_name.' '.$receiverDetails->last_name,
//                    'phone' => $phone,
//                    'email' => $receiverDetails->email,
//                );
//                $referral_detail->receiver_details = $receiverBasic;
//            }
//        }
//        return view('merchant.report.company_referral',compact('referral_details'));
//    }

    public function AreaReport(){
        $merchant_id = get_merchant_id();
        $areas_list = $this->getAreaList();
        $area = CountryArea::where('merchant_id',$merchant_id)->first();
        $driver_count = Driver::where('country_area_id', $area->id)->count();
        $booking_count = Booking::where('country_area_id', $area->id)->count();
        return view('merchant.report.area_report',compact('areas_list','area','booking_count','driver_count'));
    }

    public function AreaReportSearch(Request $request)
    {
        $areas_list = $this->getAreaList();
        $area = CountryArea::find($request->area_id);
        $driver_count = Driver::where('country_area_id', $area->id)->count();
        $booking_count = Booking::where('country_area_id', $area->id)->count();
        return view('merchant.report.area_report',compact('areas_list','area','booking_count','driver_count'));
    }

    public function AreaReportData(Request $request){
        $area_id = $request->area_id;
        $merchant_id = get_merchant_id();
        $dataArray = [];
        $rides = Booking::selectRaw('date(created_at) as date,count(*) as number')
            ->whereMonth('created_at', '=', date('m'))
            ->where([['merchant_id', '=', $merchant_id],['country_area_id', '=', $area_id]])
            ->groupBy('date')
            ->orderByRaw('min(created_at) desc')
            ->get();
        $dailyArray = array();
        $labels = [];
        $data = [];
        if (!empty($rides->toArray())) {
            foreach ($rides as $value) {
                array_push($labels, $value->date);
                array_push($data, $value->number);
//                $a = array(0 => $value->date, 1 => $value->number);
//                $dailyArray [] = $a;
            }
        } else {
            $dailyArray[] = array();
        }
        $dataArray['daily']['labels'] = $labels;
        $dataArray['daily']['data'] = $data;

        $labels = [];
        $data = [];
        $rides = Booking::selectRaw('monthname(created_at) as month,count(*) as number')
            ->whereYear('created_at', '=', date('Y'))
            ->where([['merchant_id', '=', $merchant_id],['country_area_id', '=', $area_id]])
            ->groupBy('month')
            ->orderByRaw('min(created_at) desc')
            ->get();
        $dailyArray = array();
        if (!empty($rides->toArray())) {
            foreach ($rides as $value) {
                array_push($labels, $value->month);
                array_push($data, $value->number);
//                $a = array(0 => $value->month, 1 => $value->number);
//                $dailyArray [] = $a;
            }
        } else {
            $dailyArray[] = array();
        }
        $dataArray['monthly']['labels'] = $labels;
        $dataArray['monthly']['data'] = $data;

        $labels = [];
        $data = [];
        $rides = Booking::selectRaw('year(created_at) as year,count(*) as number')
//            ->whereYear('created_at', '=', date('Y'))
            ->where([['merchant_id', '=', $merchant_id],['country_area_id', '=', $area_id]])
            ->groupBy('year')
            ->orderByRaw('min(created_at) desc')
            ->get();
        $dailyArray = array();
        if (!empty($rides->toArray())) {
            foreach ($rides as $value) {
                array_push($labels, $value->year);
                array_push($data, $value->number);
//                $a = array(0 => $value->year, 1 => $value->number);
//                $dailyArray [] = $a;
            }
        } else {
            $dailyArray[] = array();
        }
        $dataArray['yearly']['labels'] = $labels;
        $dataArray['yearly']['data'] = $data;
        print_r(json_encode($dataArray));
    }

    public function PromoCodeReport(Request $request){
        $merchant_id = get_merchant_id();
        $promo_codes = PromoCode::with('Booking')->where([['merchant_id','=',$merchant_id],['deleted', '!=', 1]])->paginate(25);
        return view('merchant.report.promocode_report',compact('promo_codes','merchant_id'));
    }

    public function PromoCodeDetails(Request $request, $id = null){
        if($id != null){
            $merchant_id = get_merchant_id();
            $promo_code = PromoCode::find($id);
            $bookings = Booking::where([['promo_code','=',$id],['merchant_id','=',$merchant_id]])->paginate(25);
            return view('merchant.report.promocode_details',compact('bookings','promo_code'));
        }else{
            return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
        }
    }
}