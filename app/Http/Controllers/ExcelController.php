<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helper\ReferralController;
use App\Models\Booking;
use App\Models\BusinessSegment\Order;
use App\Models\Country;
use App\Models\CustomerSupport;
use App\Models\Driver;
use App\Models\Configuration;
use App\Models\DriverAccount;
use App\Models\DriverOnlineTime;
use App\Models\DriverWalletTransaction;
use App\Models\HandymanOrder;
use App\Models\Merchant;
use App\Models\PricingParameter;
use App\Models\PromotionNotification;
use App\Models\ReferralCompanyDiscount;
use App\Models\ReferralDriverDiscount;
use App\Models\ReferralUserDiscount;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWalletTransaction;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use App\Models\WeightUnit;
use App\Traits\BookingTrait;
use App\Traits\DriverTrait;
use App\Traits\RatingTrait;
use App\Traits\SosTrait;
use App\Traits\PromoTrait;
use App\Traits\AreaTrait;
use App\Traits\PriceTrait;
use App\Traits\MerchantTrait;
use App\Traits\HandymanTrait;
use Auth;
use Illuminate\Http\Request;
use App\Traits\OrderTrait;
use App\Models\Category;
use App\Models\BusinessSegment\Product;

class ExcelController extends Controller
{
    use DriverTrait, BookingTrait, SosTrait, RatingTrait,PromoTrait,AreaTrait,PriceTrait,OrderTrait,HandymanTrait;

    public function UserExport(Request $request)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        switch ($request->parameter) {
            case "1":
                $parameter = "first_name";
                break;
            case "2":
                $parameter = "email";
                break;
            case "3":
                $parameter = "UserPhone";
                break;
        }
        // $users = User::where([['merchant_id', '=', $merchant_id]])->get();
        $query = User::where([['merchant_id', '=', $merchant_id]]);
        if ($request->keyword) {
            $query->where($parameter, 'like', '%' . $request->keyword . '%');
        }
        if ($request->country_id) {
            $query->where('country_id', '=', $request->country_id);
        }
        $users = $query->get();
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($users) use($string_file){
            if ($users->user_type == 1) {
                $users->user_type = trans('admin.Corporate');
            } else {
                $users->user_type = trans('admin.Retail');
            }

            if ($users->UserSignupType == 1) {
                $users->UserSignupType = trans('admin.normal');
            } elseif ($users->UserSignupType == 2) {
                $users->UserSignupType = trans('admin.Google');
            } elseif ($users->UserSignupType == 3) {
                $users->UserSignupType = trans('admin.Facebook');
            }

            switch ($users->UserSignupFrom) {
                case 1:
                    $users->UserSignupFrom = trans("$string_file.application");
                    break;
                case 2:
                    $users->UserSignupFrom = trans("$string_file.admin");
                    break;
                case 3:
                    $users->UserSignupFrom = trans("$string_file.web");
                    break;
            }

            if ($users->UserStatus == 1) {
                $users->UserStatus = trans("$string_file.active");
            } else {
                $users->UserStatus = trans("$string_file.inactive");
            }
        });
        $csvExporter->build($users,
            [
                'user_merchant_id' => trans("$string_file.user_id"),
                'email' => trans("$string_file.email"),
                'UserName' => trans("$string_file.name"),
                'UserPhone' => trans("$string_file.phone"),
                'wallet_balance' => trans("$string_file.wallet_money"),
                'ReferralCode' => trans("$string_file.referral_code"),
                'rating' => trans("$string_file.rating"),
                'created_at' => trans("$string_file.registered_date"),
                'user_type' => trans("$string_file.signup_details"),
                'UserSignupType' => trans("$string_file.signup_type"),
                'UserSignupFrom' => trans("$string_file.signup_from"),
                'UserStatus' => trans("$string_file.status")
            ]
        )->download('riders' . time() . '.csv');
    }

    public function userWalletTransaction($id)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $userwallettransactions = UserWalletTransaction::where([['user_id', '=', $id]])->get();
        if ($userwallettransactions->isEmpty()):
            return redirect()->back()->with('nowallettransactionexport', 'No Ride data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($userwallettransactions) use($string_file) {
            $userwallettransactions->UserName = $userwallettransactions->User->UserName;
            $userwallettransactions->email = $userwallettransactions->User->email;
            $userwallettransactions->amount = $userwallettransactions->amount;

            if ($userwallettransactions->type == 1) {
                $userwallettransactions->type = trans("$string_file.credit");
            } else {
                $userwallettransactions->type = trans("$string_file.debit");
            }

            if ($userwallettransactions->payment_method == 1) {
                $userwallettransactions->payment_method = trans("$string_file.cash");
            } else {
                $userwallettransactions->payment_method = trans("$string_file.non_cash");
            }


//            if ($userwallettransactions->platfrom == 1) {
//                $userwallettransactions->platfrom = trans("$string_file.non_cash");
//                    trans('admin.message531');
//            } else {
//                $userwallettransactions->platfrom = trans('admin.message267');
//            }
        });
        $csvExporter->build($userwallettransactions,
            [
                'email' => trans("$string_file.email"),
                'UserName' => trans("$string_file.name"),
                'wallet_balance' => trans("$string_file.wallet_money"),
                'type' => trans("$string_file.transaction_type"),
                'payment_method' => trans("$string_file.payment_method"),
                'amount' => trans("$string_file.amount"),
                'platfrom' => trans("$string_file.narration"),
                'receipt_number' => trans("$string_file.receipt_number"),
                'created_at' => trans("$string_file.registered_date"),
            ]
        )->download('UserWalletTransaction_' . time() . '.csv');

    }

    public function UserWalletReport(Request $request)
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $parameter = '';
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
        if(!empty($keyword) && !empty($parameter)){
            $query->WhereHas('User', function ($q) use ($keyword, $parameter) {
                $q->where($parameter, 'LIKE', '%'. $keyword.'%');
            });
        }
        $wallet_transactions = $query->get();
        if ($wallet_transactions->isEmpty()):
            return redirect()->back()->with('nowallettransectionsexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($wallet_transactions) use($string_file) {
            $wallet_transactions->rider = $wallet_transactions->User->UserName . " (" . $wallet_transactions->User->UserPhone . ") (" . $wallet_transactions->User->email . ")";
            if ($wallet_transactions->type == 1 || $wallet_transactions->type == 4):
                $wallet_transactions->type = trans("$string_file.credit").$cashback = ($wallet_transactions->type == 4) ? '( '.trans('api.cashback').' )':'';;
            else:
                $wallet_transactions->type = trans("$string_file.debit");
            endif;
            if ($wallet_transactions->platfrom == 1):
                $wallet_transactions->platfrom = trans('admin.sub-admin');
            else:
                $wallet_transactions->platfrom = trans("$string_file.application");
            endif;
            $wallet_transactions->wallet_bal = $wallet_transactions->User->wallet_balance;
        });
        $csvExporter->build($wallet_transactions,
            [
                'rider' => trans("$string_file.user_details"),
                'amount' => trans("$string_file.amount"),
                'type' => trans("$string_file.transaction_type"),
                'created_at' => trans('admin.message266'),
                'platfrom' => trans('admin.message272'),
                'receipt_number' => trans('admin.message478'),
                'description' => trans("$string_file.description"),
                'wallet_bal' => trans('admin.message513'),
            ])->download('User_Wallet_Report_' . time() . '.csv');
    }

    public function userRides($id)
    {
        $userrides = Booking::where([['user_id', '=', $id]])->get();
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $booking_status = $this->getBookingStatus($string_file);
        if ($userrides->isEmpty()):
            return redirect()->back()->withErrors('No Ride data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($userrides) use($booking_status) {
            $userrides->user_id = $userrides->User->UserName;
            $userrides->driver_id = $userrides->Driver ? $userrides->Driver->fullName : trans('admin.message273');
            $userrides->payment_method_id = $userrides->PaymentMethod->payment_method;
            $userrides->booking_status = isset($booking_status[$userrides->booking_status]) ? $booking_status[$userrides->booking_status] : "";
            $userrides->country_area_id = $userrides->CountryArea->CountryAreaName;
            $userrides->service_type_id = $userrides->ServiceType->serviceName;
            $userrides->vehicle_type_id = $userrides->VehicleType->VehicleTypeName;

        });
        $csvExporter->build($userrides,
            [
                'id' => trans("$string_file.ride_id"),
                'user_id' => trans("$string_file.user_name"),
                'driver_id' => trans("$string_file.driver"),
                'pickup_location' => trans("$string_file.pickup_location"),
                'drop_location' => trans("$string_file.drop_off_location"),
                'payment_method_id' => trans("$string_file.payment_method"),
                'booking_status' => trans("$string_file.ride_status"),
                'country_area_id' => trans("$string_file.service_area"),
                'service_type_id' => trans("$string_file.service_type"),
                'vehicle_type_id' => trans("$string_file.vehicle_type"),
                'created_at' => trans("$string_file.date"),

            ])->download('UserRides_' . time() . '.csv');


    }

    public function DriverExport(Request $request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $drivers = $this->getAllDriver(false,$request);
        $config = Configuration::where([['merchant_id', '=', $merchant_id]])->first();
        $gender_enable = $config->gender;
        if ($drivers->isEmpty()):
            return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($drivers) use($string_file) {
            $driver_vehicles = $drivers->DriverVehicles;
            $vehicle_type_name = [];
            foreach($driver_vehicles as $vehicle){
                $vehicle_type_name[] = $vehicle->VehicleType->VehicleTypeName;
            }
            $drivers->vehicle_types = implode(',',$vehicle_type_name);
            $drivers->country_area_id = $drivers->CountryArea->CountryAreaName;
            if (is_null($drivers->total_earnings)):
                $drivers->total_earnings = 0;
            endif;
            if (is_null($drivers->total_trips)):
                $drivers->total_trips = "None";
            endif;
            if (is_null($drivers->wallet_money)):
                $drivers->wallet_money = 0;
            endif;
            if ($drivers->driver_gender == 1) {
                $drivers->driver_gender = trans("$string_file.male");
            } elseif ($drivers->driver_gender = "") {
                $drivers->driver_gender = "---";
            } else {
                $drivers->driver_gender = trans("$string_file.female");
            }

            if($drivers->login_logout == 1){
                $drivers->login_logout = trans("$string_file.login");
            }
            else{
                $drivers->login_logout = trans("$string_file.logout");
            }

            if($drivers->online_offline == 1){
                $drivers->online_offline = trans("$string_file.online");
            }
            else{
                $drivers->online_offline = trans("$string_file.offline");
            }
            if($drivers->free_busy == 1){
                $drivers->free_busy = trans("$string_file.busy");
            }
            else{
                $drivers->free_busy = trans("$string_file.free");
            }
            if (is_null($drivers->bank_name)):trans("$string_file.free");
                $drivers->bank_name = "None";
            endif;
            if (is_null($drivers->account_holder_name)):
                $drivers->account_holder_name = "None";
            endif;
            if (is_null($drivers->account_number)):
                $drivers->account_number = "None";
            endif;

        });
        if($gender_enable == 1)
        {
            $csvExporter->build($drivers,
                ['fullName' => trans("$string_file.driver"),
                    'email' => trans("$string_file.email"),
                    'country_area_id' => trans("$string_file.service_area"),
                    'phoneNumber' => trans("$string_file.phone"),
                    'driver_gender' => trans("$string_file.gender"),
                    'wallet_money' => trans("$string_file.wallet_money"),
                    'driver_referralcode' => trans("$string_file.referral_code"),
                    'online_offline' => trans("$string_file.online_offline"),
                    'free_busy' => trans("$string_file.free_busy"),
                    'login_logout' => trans("$string_file.login_logout"),
                    'total_trips' => trans("$string_file.total_rides"),
                    'total_earnings' => trans("$string_file.total_earning"),
                    'bank_name' => trans("$string_file.bank_name"),
                    'account_holder_name' => trans("$string_file.account_holder_name"),
                    'account_number' => trans("$string_file.account_number"),
                    'last_location_update_time' => trans("$string_file.last").' '.trans("$string_file.location").' '.trans("$string_file.updated"),
                    'vehicle_types' => trans("$string_file.vehicle").' '.trans("$string_file.type"),
                    'created_at' => trans("$string_file.registered_date"),
                ])->download('drivers_' . time() . '.csv');
        }
        else
        {
            $csvExporter->build($drivers,
                ['fullName' => trans("$string_file.driver"),
                    'email' => trans("$string_file.email"),
                    'country_area_id' => trans("$string_file.service_area"),
                    'phoneNumber' => trans("$string_file.phone"),
                    'wallet_money' => trans("$string_file.wallet_money"),
                    'driver_referralcode' => trans("$string_file.referral_code"),
                    'online_offline' => trans("$string_file.online_offline"),
                    'free_busy' => trans("$string_file.free_busy"),
                    'login_logout' => trans("$string_file.login_logout"),
                    'total_trips' => trans("$string_file.total_rides"),
                    'total_earnings' => trans("$string_file.total_earning"),
                    'bank_name' => trans("$string_file.bank_name"),
                    'account_holder_name' => trans("$string_file.account_holder_name"),
                    'account_number' => trans("$string_file.account_number"),
                    'created_at' => trans("$string_file.registered_date"),
                    'last_location_update_time' => trans("$string_file.last").' '.trans("$string_file.location").' '.trans("$string_file.updated"),
                    'vehicle_types' => trans("$string_file.vehicle").' '.trans("$string_file.type"),
                ])->download('drivers_' . time() . '.csv');
        }

    }

    public function basicSignupDriver(Request $request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $request->request->add(['request_from'=>"basic_signup"]);
        $basicdrivers = $this->getAllDriver(false,$request);

        if ($basicdrivers->isEmpty()):
            return redirect()->back()->withErrors('No basic drivers');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($basicdrivers) use($string_file) {
            if(!empty($basicdrivers->country_area_id))
            {
                $basicdrivers->country_area_id = $basicdrivers->CountryArea->CountryAreaName;
            }
            else
            {
                $basicdrivers->country_area_id = "";
            }

            if (is_null($basicdrivers->total_earnings)):
                $basicdrivers->total_earnings = 0;
            endif;
            if (is_null($basicdrivers->total_trips)):
                $basicdrivers->total_trips = "None";
            endif;

            if ($basicdrivers->driver_gender == 1) {
                $basicdrivers->driver_gender = trans("$string_file.male");
            } elseif ($basicdrivers->driver_gender = "") {
                $basicdrivers->driver_gender = "---";
            } else {
                $basicdrivers->driver_gender = trans("$string_file.female");
            }


        });
        $csvExporter->build($basicdrivers,
            [
                'fullName' => trans("$string_file.name"),
                'email' => trans("$string_file.email"),
                'country_area_id' => trans("$string_file.service_area"),
                'phoneNumber' => trans("$string_file.phone"),
                'driver_gender' => trans("$string_file.gender"),
                'total_trips' => trans("$string_file.total_rides"),
                'total_earnings' => trans("$string_file.total_earning"),
                'created_at' => trans("$string_file.registered_date"),
                'updated_at' => trans("$string_file.updated_at"),
            ])->download('basicdrivers_' . time() . '.csv');
    }

    public function pendingDrivers(Request $request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $request->request->add(['request_from'=>"pending_approval"]);
        $pendingdrivers = $this->getAllDriver(false,$request);
//        $pendingdrivers = $this->getAllPendingDriver(false)->get();
        if ($pendingdrivers->count() == 0):
            return redirect()->back()->withErrors( 'No pending drivers');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($pendingdrivers) use($string_file) {
            $pendingdrivers->country_area_id = $pendingdrivers->CountryArea->CountryAreaName;
            if (is_null($pendingdrivers->total_earnings)):
                $pendingdrivers->total_earnings = 0;
            endif;
            if (is_null($pendingdrivers->total_trips)):
                $pendingdrivers->total_trips = "None";
            endif;

            if ($pendingdrivers->driver_gender == 1) {
                $pendingdrivers->driver_gender = trans("$string_file.male");
            } elseif ($pendingdrivers->driver_gender = "") {
                $pendingdrivers->driver_gender = "---";
            } else {
                $pendingdrivers->driver_gender = trans("$string_file.female");
            }


        });
        $csvExporter->build($pendingdrivers,
            [
                'fullName' => trans("$string_file.name"),
                'email' => trans("$string_file.email"),
                'country_area_id' => trans("$string_file.service_area"),
                'phoneNumber' => trans("$string_file.phone"),
                'driver_gender' => trans("$string_file.gender"),
                'total_trips' => trans("$string_file.total_rides"),
                'total_earnings' => trans("$string_file.total_earning"),
                'created_at' => trans("$string_file.registered_date"),
                'updated_at' => trans("$string_file.updated_at"),
            ])->download('pendingdrivers_' . time() . '.csv');
    }

    public function rejectedDriver(Request  $request)
    {
//        $rejecteddrivers = $this->getAllRejectedDrivers(false)->get();
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $request->request->add(['request_from'=>"rejected_driver"]);
        $rejecteddrivers = $this->getAllDriver(false,$request);
        if ($rejecteddrivers->isEmpty()):
            return redirect()->back()->with('rejecteddriversdownload', 'No rejected drivers');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($rejecteddrivers) use($string_file){
            $rejecteddrivers->country_area_id = $rejecteddrivers->CountryArea->CountryAreaName;
            if (is_null($rejecteddrivers->total_earnings)):
                $rejecteddrivers->total_earnings = 0;
            endif;
            if (is_null($rejecteddrivers->total_trips)):
                $rejecteddrivers->total_trips = "None";
            endif;

            if ($rejecteddrivers->driver_gender == 1) {
                $rejecteddrivers->driver_gender = trans("$string_file.male");
            } elseif ($rejecteddrivers->driver_gender = "") {
                $rejecteddrivers->driver_gender = "---";
            } else {
                $rejecteddrivers->driver_gender = trans("$string_file.female");
            }


        });
        $csvExporter->build($rejecteddrivers,
            [
                'fullName' => trans("$string_file.name"),
                'email' => trans("$string_file.email"),
                'country_area_id' => trans("$string_file.service_area"),
                'phoneNumber' => trans("$string_file.phone"),
                'driver_gender' => trans("$string_file.gender"),
                'total_trips' => trans("$string_file.total_rides"),
                'total_earnings' => trans("$string_file.total_earning"),
                'created_at' => trans("$string_file.registered_date"),
                'updated_at' => trans("$string_file.updated_at"),
            ])->download('rejecteddrivers_' . time() . '.csv');
    }

    public function DriverWalletReport(Request $request)
    {
        $parameter = '';
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
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
        if(!empty($keyword) && !empty($parameter)){
            $query->WhereHas('Driver', function ($q) use ($keyword, $parameter) {
                $q->where($parameter, 'LIKE', "%$keyword%");
            });
        }
        if(isset($request->driver_id) && $request->driver_id != ""){
            $query->where('driver_id', $request->driver_id);
        }
        $wallet_transactions = $query->get();
        if ($wallet_transactions->isEmpty()):
            return redirect()->back()->with('nowallettransectionsexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($wallet_transactions) use($string_file) {
            $wallet_transactions->driver = $wallet_transactions->Driver->fullName . " (" . $wallet_transactions->Driver->phoneNumber . ") (" . $wallet_transactions->Driver->email . ")";
            if ($wallet_transactions->transaction_type == 1):
                $wallet_transactions->transaction_type = trans("$string_file.credit").$cashback = ($wallet_transactions->narration == 5) ? '( '.trans('admin.cashback').' )':'';;
            else:
                $wallet_transactions->transaction_type = trans("$string_file.debit");
            endif;
            if ($wallet_transactions->payment_method == 1):
                $wallet_transactions->payment_method = trans("$string_file.cash");
            else:
                $wallet_transactions->payment_method = trans("$string_file.non_cash");
            endif;
            switch ($wallet_transactions->platform):
                case 1:
                    $wallet_transactions->platform = trans("$string_file.admin");
                    break;
                case 2:
                    $wallet_transactions->platform = trans("$string_file.application");
                    break;
                case 3:
                    $wallet_transactions->platform = trans("$string_file.web");
                    break;
            endswitch;
            $wallet_transactions->wallet_bal = $wallet_transactions->Driver->wallet_money;
        });
        $csvExporter->build($wallet_transactions,
            [
                'driver' => trans("$string_file.driver_details"),
                'transaction_type' => trans("$string_file.transaction_type"),
                'payment_method' => trans("$string_file.payment"),
                'receipt_number' => trans("$string_file.receipt_no"),
                'platform' => trans("$string_file.plateform"),
                'amount' => trans("$string_file.amount"),
                'created_at' => trans("$string_file.created"),
                'description' => trans("$string_file.description"),
                'wallet_bal' => trans("$string_file.wallet_money"),
            ])->download('Driver_Wallet_Report_' . time() . '.csv');
    }

    public function DriverAcceptanceReport(Request $request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $drivers = $this->getDriverBookingRequestData($request,false);
        if ($drivers->isEmpty()):
            return redirect()->back()->with('nodriverexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($drivers) {
            $drivers->driver = $drivers->fullName . " (" . $drivers->phoneNumber . ") (" . $drivers->email . ")";
            $drivers->tot_ride = $drivers->BookingRequestDriver[0]->total_trip;
            $drivers->accepted_ride = $drivers->BookingRequestDriver[0]->accepted;
            $drivers->no_res = $drivers->BookingRequestDriver[0]->no_response;
            $drivers->rej_ride = $drivers->BookingRequestDriver[0]->reject;
            $drivers->acceptance_rate = round( $drivers->accepted_ride/$drivers->tot_ride* 100) . " %";
        });
        $csvExporter->build($drivers,
            [
                'driver' => trans("$string_file.driver_details"),
                'tot_ride' => trans('admin.message581'),
                'accepted_ride' => trans('admin.message582'),
                'no_res' => trans('admin.message583'),
                'rej_ride' => trans('admin.message584'),
                'acceptance_rate' => trans('admin.message585'),
            ])->download('Driver-Request-Acceptance-Report-' . time() . '.csv');
    }

    public function DriverOnlineTimeReport(Request $request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $query = DriverOnlineTime::has('Driver')->where([['merchant_id', '=', $merchant_id]]);
        if (!empty($request->driver_name)) {
            $query->WhereHas('Driver', function ($q) use ($request) {
                $q->where("first_name", 'LIKE', "%".$request->driver_name."%");
            });
        }
        if (!empty($request->email)) {
            $query->WhereHas('Driver', function ($q) use ($request) {
                $q->where('email', 'LIKE', "%$request->driver_name%");
            });
        }
        $driver_times = $query->latest()->get();
        if ($driver_times->isEmpty()):
            return redirect()->back()->with('nodriveronlineexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($driver_times) {
           $driver_times->driver_id = $driver_times->Driver->first_name." ".$driver_times->Driver->last_name;
            $driver_times->email = $driver_times->Driver->email;
            $driver_times->online_time = $driver_times->time_intervals[0]['online_time'];
            $driver_times->offline_time = $driver_times->time_intervals[0]['offline_time'];
            $driver_times->tot_time = $driver_times->hours . " Hours " . $driver_times->minutes . " Minutes";
        });
        // echo"<pre>";
        // print_r($driver_times);
        //die();
        $csvExporter->build($driver_times,
            [
                'driver_id' => trans("$string_file.name"),
                'email' => trans("$string_file.email"),
                'online_time' => trans('admin.message772'),
                'offline_time' => trans('admin.message773'),
                'tot_time' => trans('admin.message774'),
            ])->download('Driver_Online_Time_Report_' . time() . '.csv');
    }

    public function DriverAccounts()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);

        $drivers = Driver::with(['DriverAccount' => function ($query) {
            $query->where([['status', '=', 1]]);
        }])->where([['merchant_id', '=', $merchant_id], ['total_earnings', '!=', NULL]])->get();
        if ($drivers->isEmpty()):
            return redirect()->back()->withErrors('No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($drivers) {
            $drivers->driver = $drivers->fullName . " (" . $drivers->phoneNumber . ") (" . $drivers->email . ")";
            if ($drivers->CountryArea->LanguageSingle):
                $drivers->area = $drivers->CountryArea->LanguageSingle->AreaName;
            else:
                $drivers->area = $drivers->CountryArea->LanguageAny->AreaName;
            endif;
            $drivers->out_bill = sprintf("%0.2f", array_sum(array_pluck($drivers->DriverAccount, 'amount')));
            if ($drivers->outstand_amount):
                $drivers->unbill_amount = sprintf("%0.2f", $drivers->outstand_amount);
            else:
                $drivers->unbill_amount = trans('admin.message470');
            endif;
            $drivers->tot_outstand = sprintf("%0.2f", array_sum(array_pluck($drivers->DriverAccount, 'amount')) + $drivers->outstand_amount);
            $drivers->total_earnings = sprintf("%0.2f", $drivers->total_earnings);
            $drivers->total_comany_earning = sprintf("%0.2f", $drivers->total_comany_earning);
        });
        $csvExporter->build($drivers,
            [
                'driver' => trans("$string_file.driver_details"),
                'area' => trans("$string_file.area"),
                'out_bill' => trans('admin.message463'),
                'unbill_amount' => trans('admin.message464'),
                'tot_outstand' => trans('admin.message465'),
                'total_trips' => trans('admin.message277'),
                'total_earnings' => trans("$string_file.earning"),
                'total_comany_earning' => trans('admin.message283'),
                'wallet_money' => trans("$string_file.wallet_money"),
                'created_at' => trans("$string_file.registered_date"),
            ])->download('Driver_Accounts_' . time() . '.csv');
    }

    public function DriverBills($id)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $driver = Driver::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $bills = DriverAccount::where([['merchant_id', '=', $merchant_id], ['driver_id', '=', $id]])->oldest()->get();
        if ($bills->isEmpty()):
            return redirect()->back()->withErrors('No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($bills,$string_file) {
            $bills->bill_period = $bills->from_date . " To " . $bills->to_date;
            if ($bills->status == 1):
                $bills->status = trans("$string_file.un_settled");
            else:
                $bills->status = trans("$string_file.settled");
            endif;
            $bills->created_by = $bills->CreateBy->merchantFirstName . " (" . $bills->CreateBy->merchantPhone . ") (" . $bills->CreateBy->email . ")";
            if ($bills->settle_type):
                if ($bills->settle_type == 1):
                    $bills->settle_type = trans("$string_file.cash");
                else:
                    $bills->settle_type = trans("$string_file.non_cash");
                endif;
            else:
                $bills->settle_type = "----";
            endif;
            if ($bills->settle_by):
                $bills->settle_by = $bills->SettleBy->merchantFirstName . " (" . $bills->SettleBy->merchantPhone . ") (" . $bills->SettleBy->email . ")";
            else:
                $bills->settle_by = "----";
            endif;
        });
        $csvExporter->build($bills,
            [
                'created_at' => trans('admin.message471'),
                'bill_period' => trans('admin.message472'),
                'amount' => trans('admin.message275'),
                'status' => trans('admin.message474'),
                'created_by' => trans('admin.message473'),
                'referance_number' => trans('admin.message478'),
                'settle_type' => trans('admin.message477'),
                'settle_by' => trans('admin.message475'),
                'settle_date' => trans('admin.message476'),
            ])->download($driver->fullName . '_Bill_' . time() . '.csv');
    }

    public function RideNow()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $ridenow = $this->ActiveBookingNow(false)->get();
        $booking_status = $this->getBookingStatus($string_file);
        if ($ridenow->isEmpty()):
            return redirect()->back()->withErrors('noridenowexport', 'No Ride data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($ridenow) use ($booking_status) {
            $ridenow->user_id = $ridenow->User->UserName;
            $ridenow->driver_id = $ridenow->Driver ? $ridenow->Driver->fullName : trans('admin.message273');
            $ridenow->payment_method_id = $ridenow->PaymentMethod->payment_method;
//            if ($ridenow->booking_status == 1001) {
                $ridenow->booking_status = isset($booking_status[$ridenow->booking_status]) ? $booking_status[$ridenow->booking_status] : "";
//                    trans('admin.new_booking');
//            } elseif ($ridenow->booking_status == 1012) {
//                $ridenow->booking_status = trans("$string_file.partial_accepted");
//            } elseif ($ridenow->booking_status == 1002) {
//                $ridenow->booking_status = trans('admin.driver_accepted');
//            } elseif ($ridenow->booking_status == 1003) {
//                $ridenow->booking_status = trans('admin.driver_arrived');
//            } elseif ($ridenow->booking_status == 1004) {
//                $ridenow->booking_status = trans('admin.begin');
//            }

            $ridenow->country_area_id = $ridenow->CountryArea->CountryAreaName;
            $ridenow->service_type_id = $ridenow->ServiceType->serviceName;
            $ridenow->vehicle_type_id = $ridenow->VehicleType->VehicleTypeName;

        });
        $csvExporter->build($ridenow,
            [
                'id' => trans("$string_file.ride_id"),
                'user_id' => trans("$string_file.user_name"),
                'driver_id' => trans("$string_file.name"),
                'pickup_location' => trans("$string_file.pickup_location"),
                'drop_location' => trans("$string_file.drop_off_location"),
                'estimate_bill' => trans("$string_file.estimate_bill"),
                'estimate_distance' => trans('admin.message274'),
                'payment_method_id' => trans("$string_file.payment_method"),
                'booking_status' => trans("$string_file.ride_status"),
                'country_area_id' => trans("$string_file.service_area") ,
                'service_type_id' => trans("$string_file.service_type"),
                'vehicle_type_id' => trans("$string_file.vehicle_type"),
                'created_at' => trans("$string_file.date"),

            ])->download('ridenow_' . time() . '.csv');

    }

    public function RideLater()
    {
        $ridelater = $this->ActiveBookingLater(false)->get();
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        if ($ridelater->isEmpty()):
            return redirect()->back()->withErrors('No Ride data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($ridelater) use($string_file) {
            $ridelater->user_id = $ridelater->User->UserName;
            $ridelater->driver_id = $ridelater->Driver ? $ridelater->Driver->fullName : trans('admin.message273');
            $ridelater->payment_method_id = $ridelater->PaymentMethod->payment_method;
            if ($ridelater->booking_status == 1001) {
                $ridelater->booking_status = trans('admin.message38');
            } elseif ($ridelater->booking_status == 1012) {
                $ridelater->booking_status = trans("$string_file.partial_accepted");
            } elseif ($ridelater->booking_status == 1002) {
                $ridelater->booking_status = trans('admin.driver_accepted');
            } elseif ($ridelater->booking_status == 1003) {
                $ridelater->booking_status = trans('admin.driver_arrived');
            } elseif ($ridelater->booking_status == 1004) {
                $ridelater->booking_status = trans('admin.begin');
            }

            $ridelater->country_area_id = $ridelater->CountryArea->CountryAreaName;
            $ridelater->service_type_id = $ridelater->ServiceType->serviceName;
            $ridelater->vehicle_type_id = $ridelater->VehicleType->VehicleTypeName;
        });
        $csvExporter->build($ridelater,
            [
                'id' => trans("$string_file.ride_id"),
                'user_id' => trans("$string_file.user_name"),
                'driver_id' => trans("$string_file.driver"),
                'pickup_location' => trans("$string_file.pickup_location"),
                'drop_location' => trans("$string_file.drop_off_location"),
                'estimate_bill' => trans("$string_file.estimate_bill"),
                'estimate_distance' => trans('admin.message274'),
                'payment_method_id' => trans("$string_file.payment_method"),
                'created_at' => trans("$string_file.date"),
                'booking_status' => trans("$string_file.ride_status"),
                'country_area_id' => trans("$string_file.service_area") ,
                'service_type_id' => trans("$string_file.service_type"),
                'vehicle_type_id' => trans("$string_file.vehicle_type"),
            ])->download('ridelater_' . time() . '.csv');


    }

    public function RideComplete()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $ridecomplete = $this->bookings(false, [1005])->get();
        if ($ridecomplete->isEmpty()):
            return redirect()->back()->with('noridecompleteexport', 'No Ride data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($ridecomplete) {
            $ridecomplete->user_id = $ridecomplete->User->UserName;
            $ridecomplete->driver_id = $ridecomplete->Driver->fullName;
            $ridecomplete->country_area_id = $ridecomplete->CountryArea->CountryAreaName;
            $ridecomplete->service_type_id = $ridecomplete->ServiceType->serviceName;
            $ridecomplete->vehicle_type_id = $ridecomplete->VehicleType->VehicleTypeName;
            $ridecomplete->payment_method_id = $ridecomplete->PaymentMethod->payment_method;
            if ($ridecomplete->booking_type == 1) {
                $ridecomplete->booking_type = trans('admin.ride_now');
            } else {
                $ridecomplete->booking_type = trans('admin.ride_later');
            }
        });
        $csvExporter->build($ridecomplete,
            [
                'id' => trans("$string_file.ride_id"),
                'user_id' => trans("$string_file.user_name"),
                'driver_id' => trans("$string_file.name"),
                'pickup_location' => trans("$string_file.pickup_location"),
                'drop_location' => trans("$string_file.drop_off_location"),
                'booking_type' => trans("$string_file.ride_type"),
                'final_amount_paid' => trans('admin.message448'),
                'country_area_id' => trans("$string_file.service_area") ,
                'service_type_id' => trans("$string_file.service_type"),
                'vehicle_type_id' => trans("$string_file.vehicle_type"),
                'payment_method_id' => trans("$string_file.payment_method"),
                'created_at' => trans("$string_file.date"),
            ])->download('ridecomplete_' . time() . '.csv');

    }

    public function CancelledRide()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $ridecancel = $this->bookings(false, [1006, 1007, 1008])->get();
        if ($ridecancel->isEmpty()):
            return redirect()->back()->with('noridecancelexport', 'No Ride data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($ridecancel) use($string_file,$merchant_id) {
            $ridecancel->user_id = $ridecancel->User->UserName;
            $ridecancel->vehicle_type_id = $ridecancel->VehicleType->VehicleTypeName;
            $ridecancel->service_type_id = $ridecancel->ServiceType->ServiceName($merchant_id);
            $ridecancel->country_area_id = $ridecancel->CountryArea->CountryAreaName;
            $driver = "";
            if (!empty($ridecancel->Driver)) {
                $driver = $ridecancel->Driver->fullName;
            }
            $ridecancel->driver_id = $driver;
            switch ($ridecancel->booking_status) {
                case(1006):
                    $ridecancel->booking_status = trans("$string_file.ride_cancelled_by_user");
                    break;
                case(1007):
                    $ridecancel->booking_status = trans("$string_file.ride_cancelled_by_driver");
                    break;
                case(1008):
                    $ridecancel->booking_status =trans("$string_file.ride_cancelled_by_admin");
                    break;
            }
            if ($ridecancel->booking_type == 1) {
                $ridecancel->booking_type = trans("$string_file.now");
            } else {
                $ridecancel->booking_type = trans("$string_file.later");
            }
            $ridecancel->cancel_reason_id = $ridecancel->CancelReason->ReasonName;
        });
        $csvExporter->build($ridecancel,
            [
                'id' => trans("$string_file.ride_id"),
                'user_id' => trans("$string_file.user_name"),
                'driver_id' => trans("$string_file.name"),
                'pickup_location' => trans("$string_file.pickup_location"),
                'drop_location' => trans("$string_file.drop_off_location"),
                'booking_type' => trans("$string_file.ride_type"),
                'booking_status' => trans('admin.message450'),
                'country_area_id' => trans("$string_file.service_area") ,
                'service_type_id' => trans("$string_file.service_type"),
                'vehicle_type_id' => trans("$string_file.vehicle_type"),
                'cancel_reason_id' => trans('admin.message30'),
                'created_at' => trans("$string_file.date"),
            ])->download('ridecancelled_' . time() . '.csv');

    }

    public function FailedRide()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $ridefailed = $this->failsBookings(false)->get();
        if ($ridefailed->isEmpty()):
            return redirect()->back()->with('noridefailedexport', 'No Ride data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($ridefailed) {
            $ridefailed->user_id = $ridefailed->User->UserName;
            if ($ridefailed->booking_type == 1) {
                $ridefailed->booking_type = trans('admin.ride_now');
            } else {
                $ridefailed->booking_type = trans('admin.ride_later');
            }

            if ($ridefailed->failreason == 1) {
                $ridefailed->failreason = trans('admin.message363');
            } else {
                $ridefailed->failreason = trans('admin.message364');
            }
        });
        $csvExporter->build($ridefailed,
            [
                'id' => trans("$string_file.ride_id"),
                'user_id' => trans("$string_file.name"),
                'pickup_location' => trans("$string_file.pickup_location"),
                'drop_location' => trans("$string_file.drop_off_location"),
                'booking_type' => trans("$string_file.ride_type"),
                'failreason' => trans('admin.message451'),
                'country_area_id' => trans("$string_file.service_area") ,
                'service_type_id' => trans("$string_file.service_type"),
                'vehicle_type_id' => trans("$string_file.vehicle_type"),
                'created_at' => trans("$string_file.date"),
            ])->download('ridefailed_' . time() . '.csv');

    }

    public function autocancelrides()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $autocancelrides = $this->autoCancelRide(false, [1016])->get();
        if ($autocancelrides->isEmpty()):
            return redirect()->back()->with('noautocancelrideexport', 'No Ride data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($autocancelrides) {
            $autocancelrides->user_id = $autocancelrides->User->UserName;
            if ($autocancelrides->booking_type == 1) {
                $autocancelrides->booking_type = trans('admin.ride_now');
            } else {
                $autocancelrides->booking_type = trans('admin.ride_later');
            }

            $autocancelrides->country_area_id = $autocancelrides->CountryArea->CountryAreaName;
            $autocancelrides->service_type_id = $autocancelrides->ServiceType->serviceName;
            $autocancelrides->vehicle_type_id = $autocancelrides->VehicleType->VehicleTypeName;

        });
        $csvExporter->build($autocancelrides,
            [
                'id' => trans("$string_file.ride_id"),
                'user_id' => trans("$string_file.name"),
                'pickup_location' => trans("$string_file.pickup_location"),
                'drop_location' => trans("$string_file.drop_off_location"),
                'booking_type' => trans("$string_file.ride_type"),
                'created_at' => trans("$string_file.date"),
            ])->download('autocancelrides_' . time() . '.csv');
    }

    public function allRides(Request $request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $request->request->add(['request_from'=>"ALL",'arr_booking_status'=>$request->arr_booking_status,'url_slug'=>$request->url_slug]);
        $allrides = $this->getBookings($request,false, 'MERCHANT');
//        $allrides = $this->allBookings(false)->get();
        $booking_status = $this->getBookingStatus($string_file);
        if ($allrides->isEmpty()):
            return redirect()->back()->withErrors('No Ride data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($allrides) use($booking_status) {
            $allrides->user_id = $allrides->User->user_merchant_id;
            $allrides->driver_id = !empty($allrides->driver_id) ? $allrides->Driver->merchant_driver_id : NULL;
            $allrides->user_name = $allrides->User->UserName;
            $allrides->driver_name = $allrides->Driver ? $allrides->Driver->fullName : trans('admin.message273');
            $allrides->payment_method_id = $allrides->PaymentMethod->payment_method;
            $allrides->booking_status = isset($booking_status[$allrides->booking_status]) ? $booking_status[$allrides->booking_status] : "";
//            if ($allrides->booking_status == 1001) {
//                $allrides->booking_status = trans('admin.new_booking');
//            } elseif ($allrides->booking_status == 1012) {
//                $allrides->booking_status = trans("$string_file.partial_accepted");
//            } elseif ($allrides->booking_status == 1002) {
//                $allrides->booking_status = trans('admin.driver_accepted');
//            } elseif ($allrides->booking_status == 1003) {
//                $allrides->booking_status = trans('admin.driver_arrived');
//            } elseif ($allrides->booking_status == 1004) {
//                $allrides->booking_status = trans('admin.begin');
//            } elseif ($allrides->booking_status == 1005) {
//                $allrides->booking_status = trans('admin.message42');
//            } elseif ($allrides->booking_status == 1016) {
//                $allrides->booking_status = trans('admin.message779');
//            } elseif ($allrides->booking_status == 1006 || $allrides->booking_status == 1007 || $allrides->booking_status == 1008) {
//                $allrides->booking_status = trans('admin.message350');
//            } else {
//                $allrides->booking_status = "";
//            }

            $allrides->country_area_id = $allrides->CountryArea->CountryAreaName;
            $allrides->service_type_id = $allrides->ServiceType->serviceName;
            $allrides->vehicle_type_id = $allrides->VehicleType->VehicleTypeName;

        });
        $csvExporter->build($allrides,
            [
                'id' => trans("$string_file.ride_id"),
                'merchant_booking_id' => trans("$string_file.ride_id"),
                'user_id' => trans("$string_file.user_id"),
                'driver_id' => trans("$string_file.driver_id"),
                'user_name' => trans("$string_file.user_name"),
                'driver_name' => trans("$string_file.name"),
                'pickup_location' => trans("$string_file.pickup_location"),
                'drop_location' => trans("$string_file.drop_off_location"),
                'payment_method_id' => trans("$string_file.payment_method"),
                'booking_status' => trans("$string_file.ride_status"),
                'country_area_id' => trans("$string_file.service_area") ,
                'service_type_id' => trans("$string_file.service_type"),
                'vehicle_type_id' => trans("$string_file.vehicle_type"),
                'created_at' => trans("$string_file.date"),

            ])->download('allrides_' . time() . '.csv');
    }

    public function SubAdmin()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $subadmin = Merchant::where([['parent_id', '=', $merchant_id]])->get();

        if ($subadmin->isEmpty()):
            return redirect()->back()->with('nosubadminexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($subadmin) {
            $subadmin->merchantFirstName = $subadmin->merchantFirstName . ' ' . $subadmin->merchantLastName;
            $subadmin->role = $subadmin->roles->first()->display_name;
        });
        $csvExporter->build($subadmin,
            [
                'merchantFirstName' => trans("$string_file.name"),
                'email' => trans("$string_file.email"),
                'merchantPhone' => trans("$string_file.phone"),
                'role' => trans("$string_file.role"),
                'created_at' => trans("$string_file.created_at"),
            ])->download('Sub_Admins_' . time() . '.csv');
    }

    public function Transactions(Request $request)
    {
        $merchant = Merchant::find($request->merchant_id);
        $string_file = $this->getStringFile($request->merchant_id);
        $query = $this->getAllTransaction(false);
        if ($request->date) {
            $query->whereDate('created_at', '>=', $request->date);
        }
        if ($request->date1) {
            $query->whereDate('created_at', '<=', $request->date1);
        }
        if ($request->booking_id) {
            $query->where('id', '=', $request->booking_id);
        }
        if ($request->rider) {
            $keyword = $request->rider;
            $query->WhereHas('User', function ($q) use ($keyword) {
                $q->where(\DB::raw("concat(`first_name`,' ', `last_name`)"), 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('UserPhone', 'LIKE', "%$keyword%");
            });
        }
        if ($request->driver) {
            $keyword = $request->driver;
            $query->WhereHas('Driver', function ($q) use ($keyword) {
                $q->where(\DB::raw("concat(`first_name`,' ', `last_name`)"), 'LIKE', "%$keyword%")->orWhere('email', 'LIKE', "%$keyword%")->orWhere('phoneNumber', 'LIKE', "%$keyword%");
            });
        }
        $transactions = $query->get();
        if ($transactions->isEmpty()):
            return redirect()->back()->with('notransactionsexport', 'No data');
        endif;
        foreach ($transactions as $transaction){
            $referAmount = 0;
            $companyDiscount = ReferralCompanyDiscount::where('booking_id',$transaction->id)->first();
            if (!empty($companyDiscount)){
                $referAmount = $referAmount+$companyDiscount->amount;
            }

            $driverDiscount = ReferralDriverDiscount::where('booking_id',$transaction->id)->sum('amount');
            if (!empty($driverDiscount)){
                $referAmount = $referAmount+$driverDiscount;
            }

            $userDiscount = ReferralUserDiscount::where('booking_id',$transaction->id)->sum('amount');
            if (!empty($userDiscount)){
                $referAmount = $referAmount+$userDiscount;
            }
            $transaction->referral_discount = $referAmount;
            $transaction->merchant = $merchant;
        }
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($transactions) {
            $transactions->user_id = isset($transactions->User) ? $transactions->User->first_name." ".$transactions->User->last_name . " (" . $transactions->User->UserPhone . ") (" . $transactions->User->email . ")" : '';
            $transactions->driver_id = isset($transactions->Driver) ? $transactions->Driver->first_name." ".$transactions->Driver->last_name . " (" . $transactions->Driver->phoneNumber . ") (" . $transactions->Driver->email . ")" : '';
            if ($transactions->booking_type == 1):
                $transactions->booking_type = trans("$string_file.now");
            else:
                $transactions->booking_type = trans("$string_file.later");
            endif;
            $transactions->area = $transactions->CountryArea->CountryAreaName;
            $transactions->payment_method = $transactions->PaymentMethod->payment_method;
            $transactions->tot_fare = $transactions->CountryArea->Country->isoCode . " " . $transactions->final_amount_paid;
            $transactions->promo_dis = $transactions->CountryArea->Country->isoCode . " " . (isset($transactions['BookingTransaction']) ? ($transactions['BookingTransaction']['discount_amount']) : ($transactions['BookingDetail']['promo_discount']));
            $cutAfterReferral = ($transactions->company_cut ? $transactions->company_cut : 0) - ($transactions->referral_discount ? $transactions->referral_discount : 0);
            $transactions->company_cut_after_referral = $transactions->CountryArea->Country->isoCode . " " . $cutAfterReferral;
            $transactions->company_cut = $transactions->CountryArea->Country->isoCode . " " . ($transactions->company_cut ? $transactions->company_cut : 0);
            $transactions->driver_cut = $transactions->CountryArea->Country->isoCode . " " . $transactions->driver_cut;
            $transactions->estimate_bill = $transactions->CountryArea->Country->isoCode . " " . $transactions->estimate_bill;
            $transactions->surge_amount = $transactions->CountryArea->Country->isoCode . " " . ($transactions->surge_amount ? $transactions->surge_amount : 0);
            $transactions->extra_charges = $transactions->CountryArea->Country->isoCode . " " . ($transactions->extra_charges ? $transactions->extra_charges : 0);
            $transactions->tip = $transactions->CountryArea->Country->isoCode . " " . ($transactions->tip ? $transactions->tip : 0);
            $transactions->insurance_amount = $transactions->CountryArea->Country->isoCode . " " . ($transactions->insurance_amount ? $transactions->insurance_amount : 0);
            $transactions->toll_amount = $transactions->CountryArea->Country->isoCode . " " . ($transactions->toll_amount ? $transactions->toll_amount : 0);
            $transactions->cancellation_charge_applied = $transactions->CountryArea->Country->isoCode . " " . ($transactions->cancellation_charge_applied ? $transactions->cancellation_charge_applied : 0);
            $transactions->cancellation_charge_received = $transactions->CountryArea->Country->isoCode . " " . ($transactions->cancellation_charge_received ? $transactions->cancellation_charge_received : 0);
            $transactions->referral_discount = $transactions->CountryArea->Country->isoCode . " " . ($transactions->referral_discount ? $transactions->referral_discount : 0);
            $transactions->surge_amount = $transactions->CountryArea->Country->isoCode . " " . $transactions['BookingTransaction']['surge_amount'];
            $transactions->extra_charges = $transactions->CountryArea->Country->isoCode . " " . $transactions['BookingTransaction']['extra_charges'];
            $transactions->tip = $transactions->CountryArea->Country->isoCode . " " . $transactions['BookingTransaction']['tip'];
            $transactions->insurance_amount = $transactions->CountryArea->Country->isoCode . " " . $transactions['BookingTransaction']['insurance_amount'];
            $transactions->toll_amount = $transactions->CountryArea->Country->isoCode . " " . $transactions['BookingTransaction']['toll_amount'];
            $transactions->cancellation_charge_applied = $transactions->CountryArea->Country->isoCode . " " . $transactions['BookingTransaction']['cancellation_charge_applied'];
            $transactions->cancellation_charge_received = $transactions->CountryArea->Country->isoCode . " " . $transactions['BookingTransaction']['cancellation_charge_received'];

            if(isset($transactions->merchant->BookingConfiguration->final_amount_to_be_shown)){
                $rounded_amount = isset($transactions['BookingTransaction']['rounded_amount']) ? number_format($transactions['BookingTransaction']['rounded_amount'],2) : '0.00';
                $transactions->round_off = $transactions->CountryArea->Country->isoCode . " " . $rounded_amount;
            }
        });

        $basicArray = [
            'id' => trans("$string_file.ride_id"),
            'booking_type' => trans("$string_file.ride_type"),
            'area' => trans("$string_file.service_area"),
            'user_id' => trans("$string_file.user_details"),
            'driver_id' => trans("$string_file.driver_details"),
            'payment_method' => trans("$string_file.payment"),
            'tot_fare' => trans("$string_file.total_amount"),
            'promo_dis' => trans("$string_file.promo_discount"),
            'company_cut' => trans("$string_file.company_earning"),
            'driver_cut' => trans("$string_file.driver_earning"),
            'travel_distance' => trans("$string_file.travelled_distance"),
            'travel_time' => trans("$string_file.travelled_time"),
            'estimate_bill' => trans("$string_file.estimate_bill"),
            'referral_discount' => trans("$string_file.referral_discount"),
            'company_cut_after_referral' => trans('admin.company_cut_after_referral'),
            'created_at' => trans("$string_file.date"),
        ];

        if ($merchant->ApplicationConfiguration->sub_charge == 1){
            $basicArray['surge_amount'] = trans('admin.SubCharge');
        }
        if ($merchant->ApplicationConfiguration->time_charges == 1){
            $basicArray['extra_charges'] = trans('admin.message763');
        }
        if ($merchant->ApplicationConfiguration->tip_status == 1){
            $basicArray['tip'] = trans('admin.tip_charge');
        }
        if ($merchant->BookingConfiguration->insurance_enable == 1){
            $basicArray['insurance_amount'] = trans('admin.insurnce');
        }
        if ($merchant->Configuration->toll_api == 1){
            $basicArray['toll_amount'] = trans("$string_file.toll_charge");
        }
        if ($merchant->cancel_charges == 1){
            $basicArray['cancellation_charge_applied'] = trans('admin.message712');
            $basicArray['cancellation_charge_received'] = trans('admin.cancel_charges_receive');
        }
        if(isset($merchant->BookingConfiguration->final_amount_to_be_shown)){
            $basicArray['round_off'] = trans('admin.round_off');
        }

        $csvExporter->build($transactions,$basicArray)->download('Transactions_' . time() . '.csv');
    }

    public function PaymentTransactions()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $transactions = Transaction::where('merchant_id',$merchant_id)->get();
        if ($transactions->isEmpty()):
            return redirect()->back()->with('notransactionsexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($transactions) use ($string_file) {
            $transactions->payment_option_name = $transactions->PaymentOption->name ?? '-----';
            switch ($transactions->status):
                case 1:
                    $transactions->status = trans("$string_file.user");
                    break;
                case 2:
                    $transactions->status = trans("$string_file.driver");
                    break;
                case 3:
                    $transactions->status = trans("$string_file.booking");
                    break;
                default:
                    $transactions->status = '-----';
                    break;
            endswitch;
            $transactions->user_id = isset($transactions->User) ? $transactions->User->user_merchant_id : '-----';
            $transactions->user_details = isset($transactions->User) ? $transactions->User->first_name." ".$transactions->User->last_name . " (" . $transactions->User->UserPhone . ") (" . $transactions->User->email . ")" : '-----';
            $transactions->driver_id = isset($transactions->Driver) ? $transactions->Driver->merchant_driver_id : '-----';
            $transactions->driver_details = isset($transactions->Driver) ? $transactions->Driver->first_name." ".$transactions->Driver->last_name . " (" . $transactions->Driver->phoneNumber . ") (" . $transactions->Driver->email . ")" : '-----';
            $transactions->booking_details = isset($transactions->Booking) ? $transactions->Booking->merchant_booking_id : '-----';
            $transactions->amount =  $transactions->amount ?? '-----';
            $transactions->payment_mode = $transactions->payment_mode ?? '-----';

            switch ($transactions->request_status):
                case 1:
                    $transactions->request_status = trans("$string_file.pending");
                    break;
                case 2:
                    $transactions->request_status = trans("$string_file.successful");
                    break;
                case 3:
                    $transactions->request_status = trans("$string_file.failed");
                    break;
                case 4:
                    $transactions->request_status = trans("$string_file.unknown");
                    break;
                default:
                    $transactions->request_status = '-----';
                    break;
            endswitch;

            $transactions->status_message = $transactions->status_message ?? '-----';
        });

        $basicArray = [
            'id' => trans("$string_file.ride_id"),
            'payment_option_name' => trans("$string_file.payment_gateway"),
            'status' => trans("$string_file.type"),
            'user_id' => trans("$string_file.user_id"),
            'user_details' => trans("$string_file.user_details"),
            'driver_id' => trans("$string_file.driver_id"),
            'driver_details' => trans("$string_file.driver_details"),
            'booking_details' => trans("$string_file.booking_details"),
            'amount' => trans("$string_file.amount"),
            'payment_transaction_id' => trans("$string_file.transaction_id"),
            'reference_id' => trans("$string_file.gateway_reference_id"),
            'payment_mode' => trans("$string_file.payment_method"),
            'request_status' => trans("$string_file.payment_status"),
            'status_message' => trans("$string_file.gateway_message"),
        ];

        $csvExporter->build($transactions,$basicArray)->download('PaymentTransactions_' . time() . '.csv');
    }

    public function SosRequests()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $sosrequests = $this->getAllSosRequest(false)->get();
        if ($sosrequests->isEmpty()):
            return redirect()->back()->with('nososrequestsexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($sosrequests,$string_file) {
            if ($sosrequests->application == 1):
                $sosrequests->application = trans("$string_file.user");
            else:
                $sosrequests->application = trans("$string_file.driver");
            endif;
            $sosrequests->user_id = $sosrequests->Booking->User->UserName . ' ( ' . $sosrequests->Booking->User->UserPhone . ' )';
            $sosrequests->driver_id = $sosrequests->Booking->Driver->fullName . ' ( ' . $sosrequests->Booking->Driver->phoneNumber . ' )';
            $sosrequests->location = 'https://www.google.com/maps/place/' . $sosrequests->latitude . ',' . $sosrequests->longitude . ' ( ' . $sosrequests->latitude . ' ,' . $sosrequests->longitude . ' )';
            $sosrequests->area = $sosrequests->Booking->CountryArea->CountryAreaName;
            $sosrequests->service_type = $sosrequests->Booking->ServiceType->serviceName;
            $sosrequests->booking_time = $sosrequests->Booking->created_at;
        });
        $csvExporter->build($sosrequests,
            [
                'id' => trans("$string_file.ride_id"),
                'application' => trans("$string_file.application"),
                'user_id' => trans("$string_file.user_name"),
                'driver_id' => trans("$string_file.driver"),
                'area' => trans("$string_file.service_area"),
                'service_type' => trans("$string_file.service_type"),
                'location' => trans("$string_file.sos_location"),
                'number' => trans("$string_file.phone"),
                'created_at' => trans("$string_file.created_at"),
                'booking_time' => trans("$string_file.date"),
            ])->download('SOS_Requests_' . time() . '.csv');

    }

    public function Ratings()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $ratings = $this->getAllRating(false)->get();
        if ($ratings->isEmpty()):
            return redirect()->back()->with('noratingsexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($ratings) {
            if (is_null($ratings->user_rating_points)):
                $ratings->user_rating_points = "Not Yet";
            endif;
            if (is_null($ratings->driver_rating_points)):
                $ratings->driver_rating_points = "Not Yet";
            endif;
            if (is_null($ratings->user_comment)):
                $ratings->user_comment = "Not Yet";
            endif;
            if (is_null($ratings->driver_comment)):
                $ratings->driver_comment = "Not Yet";
            endif;
            $ratings->user = $ratings->Booking->User->UserName . " (" . $ratings->Booking->User->UserPhone . ") (" . $ratings->Booking->User->email . ")";
            $ratings->driver = $ratings->Booking->Driver->fullName . " (" . $ratings->Booking->Driver->phoneNumber . ") (" . $ratings->Booking->Driver->email . ")";
        });
        $csvExporter->build($ratings,
            [
                'id' => trans("$string_file.ride_id"),
                'user' => trans("$string_file.user_details"),
                'driver' => trans("$string_file.driver_details"),
                'user_rating_points' => trans("$string_file.rating_by_user"),
                'user_comment' => trans("$string_file.user_comments"),
                'driver_rating_points' => trans("$string_file.rating_by_driver"),
                'driver_comment' => trans("$string_file.driver_comments"),
            ])->download('Ratings_' . time() . '.csv');

    }

    public function CustomerSupports()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $customer_supports = CustomerSupport::where([['merchant_id', '=', $merchant_id]])->get();
        if ($customer_supports->isEmpty()):
            return redirect()->back()->with('nocustomersupportsexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($customer_supports) use($string_file) {
            if ($customer_supports->application == 1):
                $customer_supports->application = "Rider";
            endif;
            if ($customer_supports->application == 2):
                $customer_supports->application = "Driver";
            endif;
        });
        $csvExporter->build($customer_supports,
            [
                'application' => trans("$string_file.application"),
                'name' => trans("$string_file.name"),
                'email' => trans("$string_file.email"),
                'phone' => trans('admin.message306'),
                'query' => trans('admin.message380'),
                'created_at' => trans('admin.created_at'),
            ])->download('Customer_Supports_' . time() . '.csv');

    }

    public function PromotionNotifications(Request $request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile();
        $query= PromotionNotification::where([['merchant_id', '=', $merchant_id]]);
            if ($request->title) {
                $query->where('title', $request->title);
            }
            if ($request->application) {
                $query->where('application', $request->application);
            }
            if ($request->date) {
                $query->whereDate('created_at', '=', $request->date);
            }
       $promotions = $query->get();
//            p($promotions->toArray());
        if ($promotions->isEmpty()):
            return redirect()->back()->with('nopromotionnotificationsexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($promotions) {
            if ($promotions->application == 2):
                $promotions->application = "Rider";
                if ($promotions->user_id == 0):
                    $promotions->user_id = "All Rider";
                    $promotions->driver_id = "-----";
                else:
                    $promotions->user_id = $promotions->User->UserName . " (" . $promotions->User->UserPhone . ") (" . $promotions->User->email . ")";
                    $promotions->driver_id = "-----";
                endif;
            endif;
            if ($promotions->application == 1):
                $promotions->application = "Driver";
                if ($promotions->driver_id == 0):
                    $promotions->driver_id = "All Driver";
                    $promotions->user_id = "-----";
                else:
                    $promotions->driver_id = $promotions->Driver->fullName . " (" . $promotions->Driver->phoneNumber . ") (" . $promotions->Driver->email . ")";
                    $promotions->user_id = "-----";
                endif;
            endif;
            if ($promotions->country_area_id):
                $promotions->country_area_id = $promotions->CountryArea->CountryAreaName;
            endif;
//            if ($promotions->show_promotion == 1):
//                $promotions->show_promotion = "Yes";
//            else:
//                $promotions->show_promotion = "No";
//            endif;
        });
        $csvExporter->build($promotions,
            [
                'country_area_id' => trans("$string_file.service_area"),
                'title' => trans("$string_file.title"),
                'message' => trans("$string_file.message"),
                'url' => trans("$string_file.url"),
                'application' => trans("$string_file.application"),
                'user_id' => trans("$string_file.user_name"),
                'driver_id' => trans("$string_file.driver"),
                'created_at' => trans("$string_file.created_at"),
//                'show_promotion' => trans('admin.message688'),
                'expiry_date' => trans("$string_file.expiry_date"),
            ])->download('Promotion_Notifications_' . time() . '.csv');
    }

    public function countriesExport()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $countries = Country::where([['merchant_id', '=', $merchant_id]])->get();
        if ($countries->isEmpty()):
            return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($countries) use($string_file) {
            $countries->name = $countries->LanguageCountryAny->name;
            if ($countries->distance_unit == 1) {
                $countries->distance_unit = trans("$string_file.km");
            } elseif ($countries->distance_unit == 2) {
                $countries->distance_unit = trans("$string_file.miles");
            }
        });
        $csvExporter->build($countries,
            [
                'name' => trans("$string_file.country"),
                'phonecode' => trans("$string_file.isd_code"),
                'isoCode' => trans("$string_file.iso_code"),
                'distance_unit' => trans("$string_file.distance_unit"),

            ])->download('countries_' . time() . '.csv');
    }

    public function ServiceAreaManagement()
    {
        $areas = $this->getAreaList(false,true);
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $areas = $areas->get();
        if ($areas->isEmpty()):
            return redirect()->back()->withErrors(trans("$string_file.data_not_found"));
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($areas) use($string_file) {
            $areas->coun = $areas->country->CountryName;
            $areas->area = $areas->LanguageSingle->AreaName;
            $a = array();
            foreach ($areas->documents as $document):
                $a[] = $document->DocumentName;
            endforeach;
            $areas->doc_name = implode(',', $a);

//            $b = array();
//            foreach ($areas->vehicledocuments as $vehicledocument):
//                $b[] = $vehicledocument->DocumentName;
//            endforeach;
//            $areas->vehicl_doc_name = implode(',', $b);

//            $c = array();
//            foreach ($areas->servicetypes as $servicetype):
//                $c[] = $servicetype->serviceName;
//            endforeach;
//            $areas->avail_area = implode(',', $c);

            $arr_segment = array();
            foreach ($areas->Segment as $segment):
                $arr_segment[] = $segment->Name();
            endforeach;
            $areas->segment = implode(',', $arr_segment);
            $areas->area_type = $areas->is_geofence == 1 ? "Geofence Area" : trans("$string_file.service_area");

        });
        $csvExporter->build($areas,
            [
                'area' => trans("$string_file.service_area_name") ,
                'coun' => trans("$string_file.country_name") ,
                'segment' => trans("$string_file.segment"),
                'doc_name' => trans("$string_file.personal_document"),
                'area_type' => trans("$string_file.area_type"),
                'timezone' => trans("$string_file.timezone"),
                'minimum_wallet_amount' => trans("$string_file.minimum_wallet_amount"),
            ])->download('Service_Area_Management_' . time() . '.csv');
    }

    public function BookingReport(Request $request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $query = $this->bookings(false, ['1005']);
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
        $bookings = $query->get();
        if ($bookings->isEmpty()):
            return redirect()->back()->with('nobookingsexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($bookings) {
            $bookings->rider = $bookings->User->UserName . " (" . $bookings->User->UserPhone . ") (" . $bookings->User->email . ")";
            $bookings->driver = $bookings->Driver->fullName . " (" . $bookings->Driver->phoneNumber . ") (" . $bookings->Driver->email . ")";
            $bookings->loc = $bookings->BookingDetail->start_location . " To " . $bookings->BookingDetail->end_location;
        });
        $csvExporter->build($bookings,
            [
                'id' => trans("$string_file.id"),
                'rider' => trans("$string_file.user_details"),
                'driver' => trans("$string_file.driver_details"),
                'loc' => trans("$string_file.ride_location"),
                'created_at' => trans("$string_file.date"),
            ])->download('Booking_Report_' . time() . '.csv');
    }

    public function BookingVarianceReport(Request $request)
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $query = $this->bookings(false, ['1005']);
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
        $bookings = $query->get();
        if ($bookings->isEmpty()):
            return redirect()->back()->with('nobookingsexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($bookings) {
            $bookings->rider = $bookings->User->UserName . " (" . $bookings->User->UserPhone . ") (" . $bookings->User->email . ")";
            $bookings->driver = $bookings->Driver->fullName . " (" . $bookings->Driver->phoneNumber . ") (" . $bookings->Driver->email . ")";
            $bookings->loc = $bookings->BookingDetail->start_location . "  -----------  " . $bookings->BookingDetail->end_location;
            $bookings->travel_time_min = $bookings->travel_time_min . " " . trans("$string_file.min");
            $bookings->estimate_bill = $bookings->CountryArea->Country->isoCode . " " . $bookings->estimate_bill;
            $bookings->final_amount_paid = $bookings->CountryArea->Country->isoCode . " " . $bookings->final_amount_paid;
        });
        $csvExporter->build($bookings,
            [
                'id' => trans("$string_file.id"),
                'rider' => trans("$string_file.user_details"),
                'driver' => trans("$string_file.driver_details"),
                'loc' => trans("$string_file.ride_location"),
                'created_at' => trans("$string_file.date"),
                'estimate_time' => trans("$string_file.estimate_time"),
                'travel_time_min' => trans("$string_file.travelled_time"),
                'estimate_distance' => trans("$string_file.estimated_distance"),
                'travel_distance' => trans("$string_file.travelled_distance"),
                'estimate_bill' => trans("$string_file.estimate_bill"),
                'final_amount_paid' => trans("$string_file.amount_paid"),
            ])->download('Booking_Variance_Report_' . time() . '.csv');
    }

    public function PromoCode()
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $promocodes = $this->getAllPromoCode(false);
        $promocodes = $promocodes->get();
        if ($promocodes->isEmpty()):
            return redirect()->back()->with('nopromocodeexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($promocodes) use($string_file) {
            $promocodes->area = $promocodes->CountryArea->CountryAreaName;
            $b = array();
            foreach ($promocodes->ServiceType as $servicetype):
                $b[] = $servicetype->serviceName;
            endforeach;
            $promocodes->service_type = implode(',', $b);
            if ($promocodes->promo_code_value_type == 1):
                $promocodes->value = $promocodes->CountryArea->Country->isoCode . " " . $promocodes->promo_code_value;
            else:
                $promocodes->value = $promocodes->promo_code_value . " %";
            endif;
            if ($promocodes->promo_code_validity == 1):
                $promocodes->promo_code_validity = trans("$string_file.permanent");
            else:
                $promocodes->promo_code_validity = trans("$string_file.custom");
            endif;
            if ($promocodes->applicable_for == 1):
                $promocodes->applicable_for = trans("$string_file.all_users");
            elseif ($promocodes->applicable_for == 2):
                $promocodes->applicable_for = trans("$string_file.new_users");
            else:
                $promocodes->applicable_for = trans("$string_file.corporate_users");
            endif;
            if ($promocodes->promo_code_status == 1):
                $promocodes->promo_code_status = trans("$string_file.active");
            else:
                $promocodes->promo_code_status = trans("$string_file.inactive");
            endif;
        });
        $csvExporter->build($promocodes,
            [
                'promoCode' => trans("$string_file.promo_code"),
                'area' => trans("$string_file.service_area"),
                'service_type' => trans("$string_file.service_type"),
                'promo_code_description' => trans("$string_file.description"),
                'value' => trans("$string_file.discount"),
                'promo_code_validity' => trans("$string_file.validity"),
                'start_date' => trans("$string_file.start_date"),
                'end_date' => trans("$string_file.end_date"),
                'promo_code_limit' => trans("$string_file.limit"),
                'promo_code_limit_per_user' => trans("$string_file.limit_per_user"),
                'applicable_for' => trans("$string_file.applicable"),
                'promo_code_status' => trans("$string_file.status"),
            ])->download('PromoCode_' . time() . '.csv');
    }

    public function PriceCard()
    {
        $merchant_id = get_merchant_id();
        $string_file = $this->getStringFile($merchant_id);
        $pricecards = $this->getPriceList(false);
        $pricecards = $pricecards->get();
        if ($pricecards->isEmpty()):
            return redirect()->back()->with('nopricecardexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($pricecards) use ($string_file) {
            $pricecards->area = $pricecards->CountryArea->CountryAreaName;
            $pricecards->service = $pricecards->ServiceType->serviceName;
            $pricecards->vehicle = $pricecards->VehicleType->VehicleTypeName;
            if (empty($pricecards->package_id)):
                $pricecards->service_type_id = "----";
            else:
                if ($pricecards->service_type_id == 4):
                    $pricecards->service_type_id = $pricecards->OutstationPackage->PackageName;
                else:
                    $pricecards->service_type_id = $pricecards->Package->PackageName;
                endif;
            endif;
            switch ($pricecards->pricing_type):
                case 1:
                    $pricecards->pricing_type = trans("$string_file.variable");
                    break;
                case 2:
                    $pricecards->pricing_type = trans("$string_file.fixed");
                    break;
                case 3:
                    $pricecards->pricing_type = trans("$string_file.input_by_driver");
                    break;
            endswitch;
            $pricecards->base_fare = $pricecards->CountryArea->Country->isoCode . " " . $pricecards->base_fare;
            if ($pricecards->PriceCardCommission):
                if ($pricecards->PriceCardCommission->commission_type == 1):
//                    $pricecards->commission = trans('admin.prepaid');
                else:
//                    $pricecards->commission = trans('admin.postpaid');
                endif;
            else:
                $pricecards->commission = "----";
            endif;
            if ($pricecards->PriceCardCommission):
                switch ($pricecards->PriceCardCommission->commission_method):
                    case 1:
                        $pricecards->commission_method = trans("$string_file.flat");
                        break;
                    case 2:
                        $pricecards->commission_method = trans("$string_file.percentage");
                        break;
                endswitch;
            else:
                $pricecards->commission_method = "----";
            endif;
            if ($pricecards->PriceCardCommission):
                switch ($pricecards->PriceCardCommission->commission_method):
                    case 1:
                        $pricecards->commission_val = $pricecards->CountryArea->Country->isoCode . " " . $pricecards->PriceCardCommission->commission;
                        break;
                    case 2:
                        $pricecards->commission_val = $pricecards->PriceCardCommission->commission . " %";
                        break;
                endswitch;
            else:
                $pricecards->commission_val = "----";
            endif;
            if ($pricecards->sub_charge_status == 1):
                $pricecards->sub_charge_status = trans("$string_file.on");
            else:
                $pricecards->sub_charge_status = trans("$string_file.off");
            endif;
            if ($pricecards->sub_charge_type == 1):
                $pricecards->sub_charge_type = trans("$string_file.nominal");
            else:
                $pricecards->sub_charge_type = trans("$string_file.multiplier");
            endif;
            $pricecards->sub_charge_value = $pricecards->CountryArea->Country->isoCode . " " . $pricecards->sub_charge_value;
        });
        $csvExporter->build($pricecards,
            [
                'area' => trans("$string_file.service_area"),
                'service' => trans("$string_file.service_type"),
                'vehicle' => trans("$string_file.vehicle_type"),
                'service_type_id' =>trans("$string_file.service_type"),
                'pricing_type' => trans('admin.price_type'),
                'base_fare' => trans("$string_file.base_fare"),
                'commission_method' => trans("$string_file.commission_method"),
                'commission_val' => trans("$string_file.commission_value"),
                'sub_charge_status' => trans("$string_file.sub_charge_status"),
                'sub_charge_type' => trans("$string_file.sub_charge_type"),
                'sub_charge_value' => trans("$string_file.sub_charge_value"),
            ])->download('PriceCard_' . time() . '.csv');
    }

    public function vehicleTypes(Request $request) {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id = $merchant->id;
        $vehicle_type = $request->vehicle_type;
        $query = VehicleType::where([['merchant_id', '=', $merchant_id]]);
        if(!empty($vehicle_type))
        {
            $query->with(['LanguageVehicleTypeSingle'=>function($q) use($vehicle_type,$merchant_id){
                $q->where('vehicleTypeName',$vehicle_type)->where('merchant_id',$merchant_id);
            }])->whereHas('LanguageVehicleTypeSingle',function($q) use($vehicle_type,$merchant_id){
                $q->where('vehicleTypeName',$vehicle_type)->where('merchant_id',$merchant_id);
            });
        }
        $vehicle_types =   $query->get();
        if ($vehicle_types->isEmpty()):
            return redirect()->back()->with('novehicletypesexport', 'No Vehicle Types');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($vehicle_types) use($string_file) {
            $vehicle_types->name = $vehicle_types->LanguageVehicleTypeAny->vehicleTypeName;
            $vehicle_types->description = $vehicle_types->LanguageVehicleTypeSingle->vehicleTypeDescription;
            $vehicle_types->serviceType = ($vehicle_types->DeliveryType) ? $vehicle_types->DeliveryType->name : ' - - - ';
            $vehicle_types->pool_enable =  ($vehicle_types->pool_enable == 1) ? trans("$string_file.yes") : trans("$string_file.no");
        });
        $csvExporter->build($vehicle_types,
            [
                'name' => trans("$string_file.name"),
                'description' => trans("$string_file.description"),
                'pool_enable' => trans("$string_file.pool_availability"),

            ])->download('vehicle_types_' . time() . '.csv');
    }

    public function Referral()
    {
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $string_file = $this->getStringFile(NULL,$merchant);
        $ref = new ReferralController();
        $referral_details = $ref->getReferralDiscountExcelData($merchant_id);
//        $referral_details = ReferralDiscount::where([['merchant_id','=',$merchant_id],['sender_id','!=',0],['sender_type','!=',0]])->groupBy('sender_id')->latest()->get();
//        foreach ($referral_details as $referral_detail){
//            $senderDetails = $referral_detail->sender_type == 1 ? User::find($referral_detail->sender_id) : Driver::find($referral_detail->sender_id);
//            if (!empty($senderDetails)){
//                $phone = $referral_detail->sender_type == 1 ? $senderDetails->UserPhone : $senderDetails->phoneNumber;
//                $senderType = $referral_detail->sender_type == 1 ? 'User' : 'Driver';
//                $referral_detail->sender_details =  $senderDetails->first_name.' '.$senderDetails->last_name.' ('.$phone.') ('. $senderDetails->email.') (Type : '.$senderType.')';
//                $referReceivers = ReferralDiscount::where([['merchant_id','=',$merchant_id],['sender_id','=',$referral_detail->sender_id]])->latest()->get();
//                $receiverBasic = array();
//                foreach ($referReceivers as $referReceiver){
//                    $receiverDetails = $referReceiver->receiver_type == 1 ? User::find($referReceiver->receiver_id) : Driver::find($referReceiver->receiver_id);
//                    if (!empty($receiverDetails)){
//                        $phone = $referReceiver->receiver_type == 1 ? $receiverDetails->UserPhone : $receiverDetails->phoneNumber;
//                        $receiverType = $referReceiver->receiver_type == 1 ? 'User' : 'Driver';
//                        $receiverBasic[] =  $receiverDetails->first_name.' '.$receiverDetails->last_name.' ('.$phone.') ('.$receiverDetails->email.') (Type : '.$receiverType.')';
//                    }
//                }
//                $referral_detail->total_refer = count($receiverBasic);
//                $referral_detail->receiver_details = implode(',',$receiverBasic);
//            }
//        }

        if ($referral_details->isEmpty()):
            return redirect()->back()->with('notransactionsexport', 'No data');
        endif;
        $csvExporter = new \Laracsv\Export();
        $csvExporter->build($referral_details,[
            'sender_details' => trans("$string_file.sender"),
            'receiver_details' => trans("$string_file.receiver"),
            'total_refer' => trans('admin.total_refer'),
            'created_at' => trans("$string_file.date")
        ])->download('ReferralReports_' . time() . '.csv');

    }

    // export earning of bs
    public function businessSegmentEarningExport(Request $request)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $request->request->add(['status'=>'DELIVERED']);
        $order = new Order;
        $all_orders = $order->getOrders($request);
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($all_orders) use($string_file) {
            $additional_amount = "";
            if(!empty($all_orders->tip_amount))
            {
                $additional_amount.=trans("$string_file.tip").' : '.$all_orders->tip_amount;
            }
            $all_orders->business_segment_earning = $all_orders->OrderTransaction->business_segment_earning;
            $all_orders->company_earning = $all_orders->OrderTransaction->company_earning;
            $all_orders->order_date =  trans("$string_file.at").' '.date('H:i',strtotime($all_orders->created_at)).', '.date_format($all_orders->created_at,'D, M d, Y');
            $all_orders->additional_charges =  $additional_amount;
        });

        $csvExporter->build($all_orders,
            [
                'merchant_order_id' => trans("$string_file.id"),
                'business_segment_earning' => trans("$string_file.business_segment_earning"),
                'company_earning' => trans("$string_file.merchant_earning"),
                'final_amount_paid' => trans("$string_file.order_amount"),
                'cart_amount' => trans("$string_file.cart_amount"),
                'tax' => trans("$string_file.tax"),
                'delivery_amount' => trans("$string_file.delivery_amount"),
                'order_date' => trans("$string_file.order_date"),
            ]
        )->download('business-segment-earning' . time() . '.csv');
    }   // export earning of bs

    public function taxiServicesEarningExport(Request $request)
    {
        $request->request->add(['request_from'=>'COMPLETE']);
        $arr_rides = $this->getBookings($request,false, 'MERCHANT');
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($arr_rides)  use($string_file){
            $arr_rides->driver_earning = $arr_rides->BookingTransaction->driver_earning;
            $arr_rides->company_earning = $arr_rides->BookingTransaction->company_earning;
            $arr_rides->discount_amount = $arr_rides->BookingTransaction->discount_amount;
            $arr_rides->sub_total_before_discount = $arr_rides->final_amount_paid + $arr_rides->BookingTransaction->discount_amount;
            $arr_rides->driver_name = $arr_rides->Driver->fullName ?? $arr_rides->Driver->first_name.' '.$arr_rides->Driver->last_name;
            $arr_rides->service_area = $arr_rides->CountryArea->CountryAreaName;
            $arr_rides->ride_date =  trans("$string_file.at").' '.date('H:i',strtotime($arr_rides->created_at)).', '.date_format($arr_rides->created_at,'D, M d, Y');
            $arr_rides->payment_method =  $arr_rides->PaymentMethod->MethodName($arr_rides->merchant_id);
            $arr_rides->user_detail =  $arr_rides->User->first_name.' '.$arr_rides->User->last_name;
        });

        $csvExporter->build($arr_rides,
            [
                'merchant_booking_id' => trans("$string_file.ride_id"),
                'payment_method' => trans("$string_file.payment_method"),
                'user_detail' => trans("$string_file.user_details"),
                'driver_name' => trans("$string_file.driver_details"),
                'driver_earning' => trans("$string_file.driver_earning"),
                'company_earning' => trans("$string_file.merchant_earning"),
                'sub_total_before_discount' => trans("$string_file.sub_total_before_discount"),
                'discount_amount' => trans("$string_file.discount_amount"),
                'final_amount_paid' => trans("$string_file.ride_amount"),
                'service_area' => trans("$string_file.service_area"),
                'ride_date' => trans("$string_file.date"),
            ]
        )->download('taxi-services-earning' . time() . '.csv');
    }

    // handyman services
    public function handymanServicesEarningExport(Request $request)
    {

        $handyman = new HandymanOrder;
        $arr_bookings = $handyman->getSegmentOrders($request,false);
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($arr_bookings) use($string_file) {
            if(!empty($arr_bookings->HandymanOrderTransaction))
            {
            $arr_bookings->driver_earning = $arr_bookings->HandymanOrderTransaction->driver_earning;
            $arr_bookings->company_earning = $arr_bookings->HandymanOrderTransaction->company_earning;
            $arr_bookings->total_booking_amount =  $arr_bookings->final_amount_paid - $arr_bookings->tax;
            $arr_bookings->booking_date =  trans("$string_file.at").' '.date('H:i',strtotime($arr_bookings->created_at)).', '.date_format($arr_bookings->created_at,'D, M d, Y');
            $arr_bookings->sub_total_before_discount =$arr_bookings->final_amount_paid + $arr_bookings->HandymanOrderTransaction->discount_amount;
            $arr_bookings->discount_amount = $arr_bookings->HandymanOrderTransaction->discount_amount;
            $arr_bookings->driver_name = $arr_rides->Driver->fullName ?? $arr_bookings->Driver->first_name.' '.$arr_bookings->Driver->last_name;
            $arr_bookings->payment_method =  $arr_bookings->PaymentMethod->MethodName($arr_bookings->merchant_id);
            $arr_bookings->user_detail =  $arr_bookings->User->first_name.' '.$arr_bookings->User->last_name;
            }
        });

        $csvExporter->build($arr_bookings,
            [
                'merchant_order_id' => trans("$string_file.booking_id"),
                'payment_method' => trans("$string_file.payment_method"),
                'user_detail' => trans("$string_file.user_details"),
                'driver_name' => trans("$string_file.driver_details"),
                'driver_earning' => trans("$string_file.driver_earning"),
                'company_earning' => trans("$string_file.merchant_earning"),
                'total_booking_amount' => trans("$string_file.booking_amount"),
                'tax' => trans("$string_file.tax"),
                'sub_total_before_discount' => trans("$string_file.sub_total_before_discount"),
                'discount_amount' => trans("$string_file.discount_amount"),
                'final_amount_paid' => trans("$string_file.total_amount"),
                'booking_date' => trans("$string_file.booking_date"),

            ]
        )->download('handyman-services-earning' . time() . '.csv');
    }


    // export earning of merchant
    public function orderEarningSummary(Request $request)
    {
        $request->request->add(['status'=>'COMPLETED']);
        $order = new Order;
        $all_orders = $order->getOrders($request);
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($all_orders) use($string_file) {
            $additional_amount = "";
            if(!empty($all_orders->tip_amount))
            {
                $additional_amount.=trans("$string_file.tip").' : '.$all_orders->tip_amount;
            }
            $all_orders->business_segment_earning = $all_orders->OrderTransaction->business_segment_earning;
            $all_orders->company_earning = $all_orders->OrderTransaction->company_earning;
            $all_orders->order_date =  trans("$string_file.at").' '.date('H:i',strtotime($all_orders->created_at)).', '.date_format($all_orders->created_at,'D, M d, Y');
            $all_orders->additional_charges =  $additional_amount;
            $all_orders->sub_total_before_discount =$all_orders->final_amount_paid + $all_orders->OrderTransaction->discount_amount;
            $all_orders->discount_amount = $all_orders->OrderTransaction->discount_amount;
            $all_orders->driver_name = $arr_rides->Driver->fullName ?? $all_orders->Driver->first_name.' '.$all_orders->Driver->last_name;
            $all_orders->payment_method =  $all_orders->PaymentMethod->MethodName($all_orders->merchant_id);
            $all_orders->user_detail =  $all_orders->User->first_name.' '.$all_orders->User->last_name;
        });


        $csvExporter->build($all_orders,
            [
                'merchant_order_id' => trans("$string_file.id"),
                'payment_method' => trans("$string_file.payment_method"),
                'user_detail' => trans("$string_file.user_details"),
                'driver_name' => trans("$string_file.driver_details"),
                'business_segment_earning' => trans("$string_file.store_earning"),
                'company_earning' => trans("$string_file.merchant_earning"),
                'cart_amount' => trans("$string_file.cart_amount"),
                'tax' => trans("$string_file.tax"),
                'delivery_amount' => trans("$string_file.delivery_charge"),
                'sub_total_before_discount' => trans("$string_file.sub_total_before_discount"),
                'discount_amount' => trans("$string_file.discount_amount"),
                'final_amount_paid' => trans("$string_file.order_amount"),
                'order_date' => trans("$string_file.order_date"),
            ]
        )->download('merchant-order-earning' . time() . '.csv');
    }

    public function VehicleMake(Request $request){
        $merchant = get_merchant_id(false);
        $vehicle_make = $request->vehicle_make;
        $merchant_id = $merchant->id;
        $query = VehicleMake::where([['merchant_id', '=', $merchant->id]]);
            if(!empty($vehicle_make))
            {
                $query->with(['LanguageVehicleMakeSingle'=>function($q) use($vehicle_make,$merchant_id){
                    $q->where('vehicleMakeName',$vehicle_make)->where('merchant_id',$merchant_id);
                }])->whereHas('LanguageVehicleMakeSingle',function($q) use($vehicle_make,$merchant_id){
                    $q->where('vehicleMakeName',$vehicle_make)->where('merchant_id',$merchant_id);
                });
            }
        $vehiclemakes =  $query->get();
        $string_file = $this->getStringFile(NULL,$merchant);
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($vehiclemakes) use($string_file) {
            $vehiclemakes->vehicle_make = $vehiclemakes->VehicleMakeName;
            $vehiclemakes->vehicle_make_desc = $vehiclemakes->VehicleMakeDescription;
            $vehiclemakes->vehicle_make_logo = get_image($vehiclemakes->vehicleMakeLogo,'vehicle');
            $vehiclemakes->status = $vehiclemakes->vehicleMakeStatus == 1 ? trans("$string_file.active") : trans("$string_file.inactive");
        });

        $csvExporter->build($vehiclemakes,
            [
                'vehicle_make' => trans("$string_file.vehicle_make"),
                'vehicle_make_logo' => trans("$string_file.logo"),
                'vehicle_make_desc' => trans("$string_file.description"),
                'status' => trans("$string_file.status"),
            ]
        )->download('vehicle_make' . time() . '.csv');
    }

    public function VehicleModel(Request $request){
        $merchant = get_merchant_id(false);
        $merchant_id = $merchant->id;
        $vehicle_model = $request->vehicle_model;
        $query = VehicleModel::where([['merchant_id', '=', $merchant_id]]);
        if(!empty($vehicle_model))
        {
            $query->with(['LanguageVehicleModelSingle'=>function($q) use($vehicle_model,$merchant_id){
                $q->where('vehicleModelName',$vehicle_model)->where('merchant_id',$merchant_id);
            }])->whereHas('LanguageVehicleModelSingle',function($q) use($vehicle_model,$merchant_id){
                $q->where('vehicleModelName',$vehicle_model)->where('merchant_id',$merchant_id);
            });
        }
        $vehicleModels = $query->get();
        $string_file = $this->getStringFile(NULL,$merchant);
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($vehicleModels) use($string_file) {
            $vehicleModels->vehicle_type = $vehicleModels->VehicleType->VehicleTypeName;
            $vehicleModels->vehicle_make = $vehicleModels->VehicleMake->VehicleMakeName;
            $vehicleModels->vehicle_model = $vehicleModels->VehicleModelName;
            $vehicleModels->vehicle_model_desc = $vehicleModels->VehicleModelDescription;
            $vehicleModels->seat = $vehicleModels->vehicle_seat;
            $vehicleModels->status = $vehicleModels->vehicleModelStatus == 1 ? trans("$string_file.active") : trans("$string_file.inactive");
        });

        $csvExporter->build($vehicleModels,
            [
                'vehicle_type' => trans("$string_file.vehicle_make"),
                'vehicle_make' => trans("$string_file.vehicle_make"),
                'vehicle_model' => trans("$string_file.vehicle_make"),
                'vehicle_model_desc' => trans("$string_file.description"),
                'seat' => trans("$string_file.no_of_seat"),
                'status' => trans("$string_file.status"),
            ]
        )->download('vehicle_model' . time() . '.csv');
    }

    public function OrderManagement(Request $request)
    {
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $merchant_id =  $merchant->id;
        $request->request->add(['merchant_id'=>$merchant_id]);

        $order = new Order;
        $all_orders = $order->getOrders($request);
        $req_param['merchant_id'] = $merchant_id;
        $arr_status = $this->getOrderStatus($req_param);
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($all_orders) use($string_file,$arr_status) {

            $all_orders->merchant_earning = NULL;
            $all_orders->store_earning = NULL;
            $all_orders->driver_earning = NULL;
            if(!empty($all_orders->OrderTransaction))
            {
                $all_orders->merchant_earning = $all_orders->OrderTransaction->company_earning;
                $all_orders->store_earning = $all_orders->OrderTransaction->business_segment_earning;
                $all_orders->driver_earning = $all_orders->OrderTransaction->driver_earning;
            }

            $all_orders->payment_mode = $all_orders->PaymentMethod->payment_method;
            $all_orders->user_name = $all_orders->User->first_name.' '.$all_orders->User->last_name;
            $all_orders->user_contact = $all_orders->User->UserPhone.', '.$all_orders->User->email;

            $all_orders->drive_name = "";
            $all_orders->driver_contact = "";
            if(!empty($all_orders->driver_id))
            {
                $all_orders->drive_name = $all_orders->Driver->first_name.' '.$all_orders->Driver->last_name;
                $all_orders->driver_contact = $all_orders->Driver->PhoneNumber.', '.$all_orders->Driver->email;
            }

            $all_orders->store_name = $all_orders->BusinessSegment->full_name;
            $all_orders->store_contact = $all_orders->BusinessSegment->phone_number.', '.$all_orders->BusinessSegment->email;
            $product_details = "";
            foreach($all_orders->OrderDetail as $product)
            {
                if(!empty($product))
                {
                    $weight =  isset($product->ProductVariant->weight) ? $product->ProductVariant->weight : "";
                    $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
                    $unit = !empty($weight)  ? $product->quantity.' x '.$weight.' '.$unit : $product->quantity.$unit;
                    $product_details .=$product->quantity.' '.$unit.' '.$product->Product->Name($all_orders->merchant_id).',';
                }
            }
            $all_orders->product_details = $product_details;
            $all_orders->order_status = $arr_status[$all_orders->order_status];
        });

        $csvExporter->build($all_orders,
            [
                'merchant_order_id' => trans("$string_file.order_id") ,
                'final_amount_paid' => trans("$string_file.final_amount") ,
                'merchant_earning' => trans("$string_file.merchant_earning"),
                'store_earning' => trans("$string_file.store_earning"),
                'driver_earning' => trans("$string_file.driver_earning"),
                'payment_mode' => trans("$string_file.payment_method"),
                'cart_amount' => trans("$string_file.cart_amount"),
                'tax' => trans("$string_file.tax"),
                'tip' => trans("$string_file.tip"),
                'discount' => trans("$string_file.discount"),
                'user_name' => trans("$string_file.user_name"),
                'user_contact' => trans("$string_file.user_contact"),
                'drive_name' => trans("$string_file.driver"),
                'driver_contact' => trans("$string_file.driver_contact"),
                'product_details' => trans("$string_file.product_details"),
                'store_name' => trans("$string_file.store_name"),
                'store_contact' => trans("$string_file.store_contact"),
                'order_date' => trans("$string_file.order_date"),
                'order_status' => trans("$string_file.order_status"),
                'created_at' => trans("$string_file.created_at"),
            ])->download('merchant-orders-list-' . time() . '.csv');
    }


    // export categories
    public function categories(Request $request)
    {
        $category_name = $request->category;
        $merchant_id = $request->merchant_id;
        $permission_segments = $request->segment_slug;
        $query = Category::with(['Segment' => function($query) use($permission_segments){
            $query->whereIn('slag',$permission_segments);
        }])->whereHas("Segment",function($query) use($permission_segments){
            $query->whereIn('slag',$permission_segments);
        })
            ->where('merchant_id',$merchant_id)->where('delete','=',NULL);
        if(!empty($category_name))
        {
            $query->with(['LangCategorySingle'=>function($q) use($category_name,$merchant_id){
                $q->where('name',$category_name)->where('merchant_id',$merchant_id);
            }])->whereHas('LangCategorySingle',function($q) use($category_name,$merchant_id){
                $q->where('name',$category_name)->where('merchant_id',$merchant_id);
            });
        }
        $arr_categories = $query->get();
        $string_file = $this->getStringFile($merchant_id);
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($arr_categories) use($string_file) {
            $parent_category_name = "";
            if (!empty($arr_categories->category_parent_id)) {
            $parent_category = Category::where('id', $arr_categories->category_parent_id)->first();
            if (!empty($parent_category->id))
                {
                      $parent_category_name =  $parent_category->Name($arr_categories->merchant_id) ;
                }
            }
           else
            {
                $parent_category_name =  trans("$string_file.none");
            }
            $arr_categories->category_name = $arr_categories->Name($arr_categories->merchant_id);
            $arr_categories->parent_category_name = $parent_category_name;
        });

        $csvExporter->build($arr_categories,
            [
                'category_parent_id' => trans("$string_file.parent_category_id"),
                'parent_category_name' => trans("$string_file.parent_category"),
                'id' => trans("$string_file.category_id"),
                'category_name' => trans("$string_file.category"),

            ])->download('merchant-category-list-' . time() . '.csv');
    }


    // export weight unit
    public function weightUnit(Request $request)
    {
        $business_segment = get_business_segment(false);
        $unit_name = $request->unit_name;
        $merchant_id = $business_segment->merchant_id;
        $permission_segments[] = $business_segment->Segment->slag;
        $query = WeightUnit::with(['Segment' => function($query) use($permission_segments){
            $query->whereIn('slag',$permission_segments);
        }])->whereHas("Segment",function($query) use($permission_segments){
            $query->whereIn('slag',$permission_segments);
        })
            ->where('merchant_id',$merchant_id)
//            ->where('delete','=',NULL)
        ;
        if(!empty($category_name))
        {
            $query->with(['LanguageSingle'=>function($q) use($unit_name,$merchant_id){
                $q->where('name',$unit_name)->where('merchant_id',$merchant_id);
            }])->whereHas('LanguageSingle',function($q) use($unit_name,$merchant_id){
                $q->where('name',$unit_name)->where('merchant_id',$merchant_id);
            });
        }
        $arr_units = $query->get();
        $string_file = $this->getStringFile($merchant_id);
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($arr_units) use($string_file) {
            $arr_units->unit_name = $arr_units->WeightUnitName;
        });
        $csvExporter->build($arr_units,
            [
                'id' => trans("$string_file.weight_unit_id"),
                'unit_name' => trans("$string_file.weight_unit"),
            ])->download('merchant-weight_unit-list-' . time() . '.csv');
    }


    // export product to import variant
    public function productForVariant(Request $request)
    {
        $business_segment = get_business_segment(false);
        $query = Product::where('business_segment_id',$business_segment->id);
        $arr_products = $query->get();
        $merchant_id = $business_segment->merchant_id;
        $string_file = $this->getStringFile($merchant_id);
        $csvExporter = new \Laracsv\Export();
        $csvExporter->beforeEach(function ($arr_products) use($string_file,$merchant_id) {
            $arr_products->product_name = $arr_products->Name($merchant_id);
        });

        $csvExporter->build($arr_products,
            [
                'id' => trans("$string_file.id"),
                'sku_id' => trans("$string_file.product_sku"),
                'product_name' => trans("$string_file.product_title"),
                'c1' => trans("$string_file.variant_sku"),
                'c2' => trans("$string_file.variant_title"),
                'c3' => trans("$string_file.product_price"),
                'c4' => trans("$string_file.weight_unit"),
                'c5' => trans("$string_file.weight"),
                'c6' => trans("$string_file.is_title_show"),
                'c7' => trans("$string_file.status"),
                'c8' => trans("$string_file.stock"),
            ])->download('product_variant_import_sheet.xlsx');
    }

    // export handyman bookings
    public function exportHandymanBookings(Request $request)
    {
        $handyman = new HandymanOrder;
        $arr_bookings = $handyman->getSegmentOrders($request,false);
//        p($arr_bookings);
        $merchant = get_merchant_id(false);
        $string_file = $this->getStringFile(NULL,$merchant);
        $csvExporter = new \Laracsv\Export();
        $req_param['merchant_id'] = $merchant->id;
        $arr_status = $this->getHandymanBookingStatus($req_param,$string_file);
        $arr_price_type = get_price_card_type("web","BOTH",$string_file);

        $csvExporter->beforeEach(function ($arr_bookings) use($string_file,$arr_status,$arr_price_type) {

            $arr_bookings->driver_name = "";
            $arr_bookings->driver_phone = "";
            if(!empty($arr_bookings->driver_id))
            {
            $arr_bookings->driver_name = $arr_bookings->Driver->first_name.' '.$arr_bookings->Driver->last_name;
            $arr_bookings->driver_phone = $arr_bookings->Driver->phoneNumber;
            }
            $arr_bookings->user_name = $arr_bookings->User->first_name.' '.$arr_bookings->User->last_name;
            $arr_bookings->user_phone = $arr_bookings->User->UserPhone;
            $arr_bookings->segment = $arr_bookings->Segment->Name($arr_bookings->merchant_id);
            $arr_bookings->service_date = $arr_bookings->booking_date; // on that day service will be done
            $arr_bookings->booking_date = $arr_bookings->created_at;
            $arr_bookings->payment_method = $arr_bookings->PaymentMethod->MethodName($arr_bookings->merchant_id);
            $arr_bookings->cart_amount = $arr_bookings->cart_amount;
            $arr_bookings->tax = $arr_bookings->tax;
            $arr_bookings->minimum_booking_amount = $arr_bookings->minimum_booking_amount;
            $arr_bookings->final_amount_paid = $arr_bookings->final_amount_paid;
            $arr_bookings->request_status = isset($arr_status[$arr_bookings->order_status])?$arr_status[$arr_bookings->order_status]: "";
            $arr_bookings->service_location = $arr_bookings->drop_location;
            $arr_bookings->service_area = $arr_bookings->CountryArea->CountryAreaName;
            $arr_bookings->price_type = isset($arr_price_type[$arr_bookings->price_type]) ?  $arr_price_type[$arr_bookings->price_type] : "";
            $arr_services = ""; $order_details = $arr_bookings->HandymanOrderDetail;
            foreach($order_details as $details){

                $service_name =   $details->ServiceType->serviceName;
                $arr_services.= $service_name.',';
            }
            $arr_bookings->services = $arr_services;

            if(!empty($arr_bookings->HandymanOrderTransaction))
            {
                $arr_bookings->driver_earning = $arr_bookings->HandymanOrderTransaction->driver_earning;
                $arr_bookings->merchant_earning = $arr_bookings->HandymanOrderTransaction->company_earning;
            }

        });

        $csvExporter->build($arr_bookings,
            [
                'merchant_order_id' => trans("$string_file.id"),
                'driver_name' => trans("$string_file.driver").' '.trans("$string_file.name"),
                'driver_phone' =>  trans("$string_file.driver").' '.trans("$string_file.phone"),
                'user_name' => trans("$string_file.user").' '.trans("$string_file.name"),
                'user_phone' => trans("$string_file.user").' '.trans("$string_file.phone"),
                'services' => trans("$string_file.services"),
                'segment' => trans("$string_file.segment"),
                'price_type' => trans("$string_file.price_type"),
                'service_date' => trans("$string_file.service_date"),
                'booking_date' => trans("$string_file.booking_date"),
                'payment_method' => trans("$string_file.payment_method"),
                'cart_amount' => trans("$string_file.cart_amount"),
                'tax' => trans("$string_file.tax"),
                'minimum_booking_amount' => trans("$string_file.minimum_booking_amount"),
                'final_amount_paid' => trans("$string_file.final_amount_paid"),
                'driver_earning' => trans("$string_file.driver_earning"),
                'merchant_earning' => trans("$string_file.merchant_earning"),
                'service_area' => trans("$string_file.service_area"),
                'request_status' => trans("$string_file.status"),
                'drop_location' => trans("$string_file.drop_location"),
            ]
        )->download('export-handyman-bookings' . time() . '.csv');
    }

}
