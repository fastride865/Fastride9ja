<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Helper\Merchant;
use App\Http\Controllers\Helper\PriceController;
use App\Http\Controllers\Helper\ReferralController;
use App\Models\BillPeriodCountryArea;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\DriverAccount;
use App\Models\DriverConfiguration;
use App\Models\DriverSettlement;
use DateTime;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DriverAccountController extends Controller
{
    public function index()
    {
        $authMerchant = Auth::user('merchant')->load('CountryArea');
        $merchant_id = $authMerchant->parent_id != 0 ? $authMerchant->parent_id : $authMerchant->id;
        $drivers = Driver::where([['merchant_id', '=', $merchant_id], ['total_earnings', '!=', NULL], ['driver_delete', '=', NULL]]);
        if (!empty($authMerchant->CountryArea->toArray())) {
            $area_ids = array_pluck($authMerchant->CountryArea, 'id');
            $drivers->whereIn('country_area_id', $area_ids);
        }
        $drivers = $drivers->paginate(25);
        return view('merchant.accounts.index', compact('drivers'));
    }

    public function Serach(Request $request)
    {
        $request->validate([
            'parameter' => "required|integer|between:1,3",
        ]);
        switch ($request->parameter) {
            case "1":
                $parameter = "first_name";
                break;
            case "2":
                $parameter = "email";
                break;
            case "3":
                $parameter = "phoneNumber";
                break;
        }
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $query = Driver::with(['DriverAccount' => function ($query){
            $query->where([['status', '=', 1]]);
        }])->whereHas('DriverAccount',function($q) use($request){
            if(isset($request->settle_type) && $request->settle_type != ''){
                if($request->settle_type == 1){
                    $q->where([['settle_type', '=', 1]]);
                }else{
                    $q->where('settle_type', null);
                }
            }
        })->where([['merchant_id', '=', $merchant_id], ['total_earnings', '!=', NULL]]);
        if ($request->keyword) {
            $query->where($parameter, 'like', '%' . $request->keyword . '%');
        }
        $drivers = $query->paginate(25);
        return view('merchant.accounts.index', compact('drivers'));
    }

    /*  public function create()
      {
          $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
          $drivers = Driver::where([['merchant_id', '=', $merchant_id], ['outstand_amount', '!=', NULL]])->get();
          if (empty($drivers->toArray())) {
              return redirect()->back()->with('accounts', trans('admin.message468'));
          }
          foreach ($drivers as $value) {
              $driver_id = $value->id;
              $lastBill = DriverAccount::where([['driver_id', '=', $driver_id]])->orderBy('id', 'desc')->first();
              if (!empty($lastBill)) {
                  $trips = $value->total_trips - $lastBill->total_trips;
                  $fromDate = $lastBill->to_date;
                  $fromDate = date('Y-m-d H:i:s', strtotime('+1 days', strtotime($fromDate)));
              } else {
                  $trips = $value->total_trips;
                  $fromDate = $value->created_at;
              }
              DriverAccount::create([
                  'merchant_id' => $merchant_id,
                  'driver_id' => $driver_id,
                  'from_date' => $fromDate,
                  'to_date' => date('Y-m-d H:i:s'),
                  'amount' => sprintf("%0.2f", $value->outstand_amount),
                  'total_trips' => $trips,
                  'create_by' => Auth::user('merchant')->id,
              ]);
              $value->outstand_amount = NULL;
              $value->save();
          }
          return redirect()->back()->with('accounts', trans('admin.message469'));
      } */

    public function DriverBillGenerationForSingleDriver(Request $request, $driver_id = 0)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $driver_object = Driver::where([['merchant_id','=',$merchant_id]])->find($driver_id);
        $driver_config = $driver_object->Merchant->DriverConfiguration;
        if (empty($driver_config)) {
            request()->session()->flash('error', trans('admin.driver_config_not_set'));
            return redirect()->back(); // FOR MERCHANT PANEL
        }
        $count = $this->DriverBillGenerationEntryPoint($request,$driver_object);
        request()->session()->flash('message', trans('admin.message469'));
        return redirect()->back();
    }

    public function create(Request $request)
    {
        $count = 0;
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $drivers = Driver::where([['merchant_id', '=', $merchant_id], ['outstand_amount', '!=', NULL]])->get(); // outstand_amount NOT NULL, those who have done atleast one ride
        if (empty($drivers->toArray())) {
            return redirect()->back()->with('accounts', trans('admin.message468'));
        }
        foreach ($drivers as $key => $value):
            $count += $this->DriverBillGenerationEntryPoint($request, $value);
        endforeach;
        if($count > 0){
            $message = $count.' '.trans('admin.message469');
            request()->session()->flash('message', $message);
        }else{
            request()->session()->flash('message', trans('admin.message468'));
        }
        return redirect()->back();
    }

    public function DriverBillGenerationEntryPoint(Request $request, Driver $driver_object)
    {
        $count = 0;
        $driver_config = $driver_object->Merchant->DriverConfiguration;
        if (empty($driver_config)) {
            request()->session()->flash('error', trans('admin.driver_config_not_set'));
            return redirect()->back(); // FOR MERCHANT PANEL
        }
        if(!empty($driver_object) && !empty($driver_object->CountryArea->BillPeriod)):  //Data From BillPeriodCountryArea Model
            $timeZone = $driver_object->CountryArea->timezone;
//            if (date_default_timezone_get() != $timeZone) {
//                date_default_timezone_set($timeZone);
//            }
            $period = $driver_object->CountryArea->BillPeriod; //Data From BillPeriodCountryArea Model
            switch ($period->bill_period_id) {
                case "1":
                    $count = $this->DailyBillGenerationLogic($period, $driver_object);
                    break;
                case "2":
                    $count = $this->WeeklyBillGenerationLogic($period, $driver_object);
                    break;
                case "3":
                    $count = $this->MonthlyBillGenerationLogic($period, $driver_object);
                    break;
            }
        endif;
        return $count;
    }

    public function DailyBillGenerationLogic(BillPeriodCountryArea $period, Driver $driver_obj)
    {
        $count = 0;
        $time = $period->bill_period_start;
        $time = date('H:i:s', strtotime($time));
        $bill_generation_date = ( new \DateTime(date('Y-m-d H:i:s')) > new \DateTime(date('Y-m-d '.$time)) ) ?
            date('Y-m-d '.$time):
            (new DateTime(date('Y-m-d '.$time)))->modify('-1 day')->format('Y-m-d H:i:s');

        $bill_last_generated = (!empty($driver_obj['last_bill_generated']) ? $driver_obj['last_bill_generated']:$driver_obj->created_at->format('Y-m-d H:i:s'));
        if(new \DateTime($bill_generation_date) > new \DateTime($bill_last_generated)):
            $temp = '2018-10-24 '.$time;
            $first = 1;
            while (new \DateTime($temp) != new \DateTime($bill_generation_date)):
                $from_date = ($first == 1) ? $bill_last_generated : $temp;
                $to_date = (new \DateTime(date('Y-m-d '.$time, strtotime($from_date))) > new \DateTime($from_date)) ?
                    date('Y-m-d '.$time, strtotime($from_date)):
                    (new \DateTime(date('Y-m-d '.$time, strtotime($from_date))))->modify('+1 day')->format('Y-m-d H:i:s');
                $timePeriod['from'] = $from_date;
                $timePeriod['to'] = $to_date;
                $count += $this->GenerateBill($driver_obj, $timePeriod);
                $first = 2;
                $temp = $to_date;
            endwhile;
            $driver_obj->last_bill_generated = $temp;
            $driver_obj->save();
        endif;
        return $count;
    }

    public function WeeklyBillGenerationLogic(BillPeriodCountryArea $period, Driver $driver_obj)
    {
        $count = 0;
        $time = '23:59:59';
        $day = $period->bill_period_start;
        $server_date_time = date('Y-m-d '.$time);
        $week_bill_generation_date = date('Y-m-d H:i:s', strtotime(($day - date('N', strtotime($server_date_time))).' day', strtotime($server_date_time)));
        $bill_generation_date = (new \DateTime($server_date_time) > new \DateTime($week_bill_generation_date)) ?
            $week_bill_generation_date:
            (new \DateTime($week_bill_generation_date))->modify('-7 day')->format('Y-m-d H:i:s');

        $bill_last_generated = (!empty($driver_obj['last_bill_generated']) ? $driver_obj['last_bill_generated'] : $driver_obj->created_at->format('Y-m-d H:i:s'));
        if(new \DateTime($bill_generation_date) > new \DateTime($bill_last_generated)):
            $temp = '2018-10-24 '.$time;
            $first = 1;
            while (new \DateTime($temp) != new \DateTime($bill_generation_date)):
                $from_date = ($first == 1) ? $bill_last_generated : $temp;
                $calculate_to_date = date('Y-m-d '.$time, strtotime($from_date));
                $to_date = date('Y-m-d H:i:s', strtotime(($day - date('N', strtotime($calculate_to_date))).' day', strtotime($calculate_to_date)));
                $to_date = (new \DateTime($to_date) > new \DateTime($from_date)) ?
                    $to_date:
                    (new \DateTime($to_date))->modify('+7 day')->format('Y-m-d H:i:s');
                $timePeriod['from'] = $from_date;
                $timePeriod['to'] = $to_date;

                $this->GenerateBill($driver_obj, $timePeriod);
                $first = 2;
                $temp = $to_date;
                $count += 1;
            endwhile;
            $driver_obj->last_bill_generated = $temp;
            $driver_obj->save();
        endif;
        return $count;
    }

    public function MonthlyBillGenerationLogic(BillPeriodCountryArea $period, Driver $driver_obj)
    {
        $count = 0;
        $month_date = sprintf("%'02u", $period->bill_period_start);
        $month_date_time = $month_date . ' 23:59:59';
        $time = ' 23:59:59';
        $bill_generation_date = (new \DateTime(date('Y-m-' . $month_date_time)) > new \DateTime(date('Y-m-d' . $time))) ?
            (new \DateTime(date('Y-m-' . $month_date_time)))->modify('-1 month')->format('Y-m-d H:i:s') :
            date('Y-m-' . $month_date_time);

        $bill_last_generated = (!empty($driver_obj['last_bill_generated']) ? $driver_obj['last_bill_generated'] : $driver_obj->created_at->format('Y-m-d H:i:s'));

        if (new \DateTime($bill_generation_date) > new \DateTime($bill_last_generated)):
            $temp = '2018-10-24' . $time;
            $first = 1;
            while (new \DateTime($temp) != new \DateTime($bill_generation_date)):
                $from_date = ($first == 1) ? $bill_last_generated : $temp;
                $to_date = date('Y-m-' . $month_date_time, strtotime($from_date));
                $to_date = (new \DateTime($to_date) > new \DateTime($from_date)) ?
                    $to_date :
                    (new \DateTime($to_date))->modify('+1 month')->format('Y-m-d H:i:s');
                $timePeriod['from'] = $from_date;
                $timePeriod['to'] = $to_date;
                if ($this->GenerateBill($driver_obj, $timePeriod)){
                    $count += 1;
                }
                $first = 2;
                $temp = $to_date;
            endwhile;
            $driver_obj->last_bill_generated = $temp;
            $driver_obj->save();
        endif;
        return $count;
    }

//    public function StartAndEndDateTime(Driver $driver)
//    {
//        $driver->CountryArea->BillPeriod;
//        switch ($id) {
//            case "1":
//                $start = strtotime($time);
//                $end = strtotime('+1 day', $start);
//                break;
//            case "2":
//                $start = strtotime($time . ' this week');
//                $end = strtotime('+7 day', $start);
//                break;
//            case "3":
//                $start = strtotime(date('Y-m-' . $time));
//                $end = strtotime('+1 month', $start);
//                break;
//        }
//    }
    public function GenerateBill(Driver $driver_obj, $timePeriod)
    {
        $bookings = Booking::where([['booking_status','=','1006']])->whereBetween('updated_at', [$timePeriod['from'], $timePeriod['to']])->where([['driver_id','=',$driver_obj->id]])
            ->orWhere([['booking_closure','=','1']])->whereBetween('updated_at', [$timePeriod['from'], $timePeriod['to']])->where([['driver_id','=',$driver_obj->id]])
            ->with(['BookingTransaction' => function ($booking_transaction) {
                $booking_transaction->where('instant_settlement' , '=' , 0);
            }])->get();

        if($bookings->isEmpty()):
            return false;
        endif;
        $transaction_collection = $bookings->map(function ($item, $key) {
            return $item['BookingTransaction'];
        })->filter()->values();
        if($transaction_collection->isEmpty()):
            return false;
        endif;
        $merchant = new Merchant();
        $driver_config = $driver_obj->Merchant->DriverConfiguration;
        $trips = $bookings->filter(function ($item, $key) {
            return $item['booking_status'] != 1006 && !empty($item['BookingTransaction']);
        })->count();

        /*$lastBill = DriverAccount::where([['driver_id', '=', $driver_obj->id]])->orderBy('id', 'desc')->first();
        if (!empty($lastBill)) {
            $trips = $driver_obj->total_trips - $lastBill->total_trips_till_now;
        } else {
            $trips = $driver_obj->total_trips;
        }*/

        $referralController = new ReferralController();
        $total_refer_amount = $referralController->getDriverReferEarning($driver_obj->merchant_id,$driver_obj->id,$timePeriod['from'],$timePeriod['to']);

        $fare_amount_sum = $transaction_collection->sum('sub_total_before_discount');
        $company_commission_sum = $transaction_collection->sum('company_earning');
        $toll_amount_sum = $transaction_collection->sum('toll_amount');
        $tip_amount_sum = $transaction_collection->sum('tip');
        $cancellation_charges_sum = $transaction_collection->sum('cancellation_charge_applied');
        //$referral_amount_sum = $transaction_collection->sum('referral_amount');

        $referral_amount_sum = $total_refer_amount;

        $cash_payment_sum = $transaction_collection->sum('cash_payment');
        $trips_outstanding_sum = $transaction_collection->sum('trip_outstanding_amount');
        $total_trips_till_now = $driver_obj->total_trips;
        $amount = $fare_amount_sum + $toll_amount_sum + $tip_amount_sum + $cancellation_charges_sum + $referral_amount_sum - $company_commission_sum - $cash_payment_sum;

        DriverAccount::create([
            'merchant_id' => $driver_obj->merchant_id,
            'driver_id' => $driver_obj->id,
            'from_date' => $timePeriod['from'],
            'to_date' => $timePeriod['to'],
            'amount' => sprintf("%0.2f", $amount),
            'create_by' => (\Auth::guard('merchant')->check()) ? \Auth::user('merchant')->id : $driver_obj->merchant_id,
            'total_trips' => $trips,
            'total_trips_till_now' => $total_trips_till_now,
            'fare_amount' => $fare_amount_sum,
            'company_commission' => $company_commission_sum,
            'toll_amount' => $toll_amount_sum,
            'tip_amount' => $tip_amount_sum,
            'cancellation_charges' => $cancellation_charges_sum,
            'referral_amount' => $referral_amount_sum,
            'cash_payment_received' => $cash_payment_sum,
            'trips_outstanding_sum' => $trips_outstanding_sum,
            'status' => '1', //1:Generated Only
            //'fee_after_grace_period' => $fee_after_grace_period,
            //'block_date' => $block_date,
            //'due_date' => $due_date,
        ]);
        return true;
        /*$fee_after_grace_period = $driver_config->fee_after_grace_period;
        $bill_due_period = $driver_config->bill_due_period;
        $due_days = "+$bill_due_period days";
        $due_date = date('Y-m-d H:i:s', strtotime($due_days));

        $bill_grace_period = $driver_config->bill_grace_period;
        $add_days = $bill_due_period + $bill_grace_period;
        $days = "+$add_days days";
        $block_date = date('Y-m-d H:i:s', strtotime($days));*/
    }

    public function createOLD()
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $booking = DriverConfiguration::where([['merchant_id', '=', $merchant_id]])->first();
        if (empty($booking)) {
            return redirect()->back()->with('accounts', trans('admin.message469'));
        }
        $drivers = Driver::where([['merchant_id', '=', $merchant_id], ['outstand_amount', '!=', NULL]])->get();
        if (empty($drivers->toArray())) {
            return redirect()->back()->with('accounts', trans('admin.message468'));
        }

        $fee_after_grace_period = $booking->fee_after_grace_period;
        $bill_due_period = $booking->bill_due_period;
        $due_days = "+$bill_due_period days";
        $due_date = date('Y-m-d H:i:s', strtotime($due_days));

        $bill_grace_period = $booking->bill_grace_period;
        $add_days = $bill_due_period + $bill_grace_period;
        $days = "+$add_days days";
        $block_date = date('Y-m-d H:i:s', strtotime($days));
        foreach ($drivers as $value) {
            $driver_id = $value->id;
            $lastBill = DriverAccount::where([['driver_id', '=', $driver_id]])->orderBy('id', 'desc')->first();
            if (!empty($lastBill)) {
                $trips = $value->total_trips - $lastBill->total_trips; //Doubt
                $fromDate = $lastBill->to_date;                         //Doubt
                $fromDate = date('Y-m-d H:i:s', strtotime('+1 days', strtotime($fromDate)));
            } else {
                $trips = $value->total_trips;
                $fromDate = $value->created_at;
            }
            DriverAccount::create([
                'merchant_id' => $merchant_id,
                'driver_id' => $driver_id,
                'from_date' => $fromDate,
                'to_date' => date('Y-m-d H:i:s'),
                'fee_after_grace_period' => $fee_after_grace_period,
                'block_date' => $block_date,
                'due_date' => $due_date,
                'amount' => sprintf("%0.2f", $value->outstand_amount),
                'total_trips' => $trips,
                'create_by' => Auth::user('merchant')->id,
            ]);
            $value->outstand_amount = NULL;
            $value->save();
        }
        return redirect()->back()->with('accounts', trans('admin.message469'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'referance_number' => "required",
            'settle_type' => 'required|integer|between:1,3',
            'bill_id' => 'required',
        ]);
        $bill = DriverAccount::findOrFail($request->bill_id);
        $bill->settle_by = Auth::user('merchant')->id;
        $bill->settle_date = date('Y-m-d H:i:s');
        $bill->referance_number = $request->referance_number;
        $bill->settle_type = $request->settle_type;
        $bill->status = 2;
        $bill->save();

        $sum_of_outstandings = $bill->trips_outstanding_sum;
        $driver_object = Driver::findOrFail($bill->driver_id);
        $driver_object->outstand_amount = $driver_object->outstand_amount - $sum_of_outstandings;
        $driver_object->save();

        return redirect()->back()->with('accounts', trans('admin.message482'));
    }

    public function DriverBillEmail(Request $request)
    {

        $BillData = DriverAccount::findorfail($request->param);
        $send_email = new emailTemplateController();
        $send_email->DriverBillEmail($BillData);
        request()->session()->flash('accounts', 'Email Sent to Driver');
    }

    public function show($id)
    {
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $driver = Driver::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);
        $bills = DriverAccount::where([['merchant_id', '=', $merchant_id], ['driver_id', '=', $id]])->oldest()->paginate(25);
        return view('merchant.accounts.bills', compact('bills', 'driver'));
    }

    public function edit(Request $request, $id)
    {
        $count = 0;
        $merchant_id = Auth::user('merchant')->parent_id != 0 ? Auth::user('merchant')->parent_id : Auth::user('merchant')->id;
        $driver = Driver::where([['merchant_id', '=', $merchant_id]])->findOrFail($id);

        $driver_config = $driver->Merchant->DriverConfiguration;
        if (empty($driver_config)) {
            request()->session()->flash('error', trans('admin.driver_config_not_set'));
            return redirect()->back(); // FOR MERCHANT PANEL
        }

        if ($driver->outstand_amount == NULL) {
            return redirect()->back()->with('accounts', trans('admin.message468'));
        }
        $count = $this->DriverBillGenerationEntryPoint($request,$driver);
        if($count > 0){
            request()->session()->flash('message', trans('admin.message469'));
        }else{
            request()->session()->flash('message', trans('admin.message468'));
        }
        return redirect()->back();
    }

    public function DriverBill($id)
    {
        $BillData = DriverAccount::with('Driver')->findorfail($id);

        return view('merchant.driver.DriverAccountBill', compact('BillData'));
    }

    public function DriverBillGenerationCronJob(Driver $driver_object)
    {
        $count = 0;
        $driver_config = $driver_object->Merchant->DriverConfiguration;
        if (empty($driver_config)) {
            exit(); // FOR CRONJOB
        }
        if(!empty($driver_object) && !empty($driver_object->CountryArea->BillPeriod)):  //Data From BillPeriodCountryArea Model
            $timeZone = $driver_object->CountryArea->timezone;
//            if (date_default_timezone_get() != $timeZone) {
//                date_default_timezone_set($timeZone);
//            }
            $period = $driver_object->CountryArea->BillPeriod; //Data From BillPeriodCountryArea Model
            switch ($period->bill_period_id) {
                case "1":
                    $count = $this->DailyBillGenerationLogic($period, $driver_object);
                    break;
                case "2":
                    $count = $this->WeeklyBillGenerationLogic($period, $driver_object);
                    break;
                case "3":
                    $count = $this->MonthlyBillGenerationLogic($period, $driver_object);
                    break;
            }
//            $only_generated_bills = $driver_object->DriverAccount()->where([['status','=','1']])->get();
//            $sum_of_outstandings = $only_generated_bills->sum('trips_outstanding_sum');
//            $merchant_helper = new Merchant();
//            $driver_object->outstand_amount = $merchant_helper->TripCalculation($driver_object->outstand_amount, $driver_object->merchant_id) - $merchant_helper->TripCalculation($sum_of_outstandings, $driver_object->merchant_id);
//            $driver_object->save();
        endif;
        return $count;
    }
}