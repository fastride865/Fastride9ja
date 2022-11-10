<?php

namespace App\Http\Controllers\Helper;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\DriverCancelBooking;
use App\Models\DriverConfiguration;
use Carbon\Carbon;
use DateTime;
use App\Models\DriverOnlineTime;

class DriverRecords
{
    public function OnlineTimeRecord($driver_id = null, $merchant_id = null)
    {
        $driver_online_today = DriverOnlineTime::where([['driver_id', $driver_id]])->whereBetween('created_at', [Carbon::now()->setTime(0, 0, 0)->format('Y-m-d H:i:s'), Carbon::now()->setTime(23, 59, 59)->format('Y-m-d H:i:s')])->first();
        if (empty($driver_online_today)):
            $driver_online_data['merchant_id'] = $merchant_id;
            $driver_online_data['driver_id'] = $driver_id;
            $driver_online_data['time_intervals'] = [['online_time' => Carbon::now()->format('d-m-Y H:i'), 'offline_time' => '', 'hours' => '00', 'minutes' => '00']];
            $driver_online_data_save = new DriverOnlineTime($driver_online_data);
            $driver_online_data_save->save();
        else:
            $modify = array();
            $modify = $driver_online_today->time_intervals;
            $new_online_time = array('online_time' => Carbon::now()->format('d-m-Y H:i'), 'offline_time' => '', 'hours' => '00', 'minutes' => '00');
            array_push($modify, $new_online_time);
            $driver_online_today->time_intervals = $modify;
            $driver_online_today->save();
        endif;
    }

    public function OfflineTimeRecord($driver_id = null, $merchant_id = null)
    {

        $driver_online_today = DriverOnlineTime::where([['driver_id', $driver_id]])->whereBetween('created_at', [Carbon::now()->setTime(0, 0, 0)->format('Y-m-d H:i:s'), Carbon::now()->setTime(23, 59, 59)->format('Y-m-d H:i:s')])->first();
        if (!empty($driver_online_today)):
            $modify = array();
            $modify = $driver_online_today->time_intervals;
            $last_one = end($modify);
            $last_one['offline_time'] = Carbon::now()->format('d-m-Y H:i');
            $last_one['hours'] = (new DateTime($last_one['offline_time']))->diff((new DateTime($last_one['online_time'])))->format('%H');
            $last_one['minutes'] = (new DateTime($last_one['offline_time']))->diff((new DateTime($last_one['online_time'])))->format('%i');

            array_pop($modify);
            array_push($modify, $last_one);

            $driver_online_today->time_intervals = $modify;
            $driver_online_today->hours = array_sum(array_column($modify, 'hours'));
            $driver_online_today->minutes = array_sum(array_column($modify, 'minutes'));
            if($driver_online_today->minutes > 60):
                ++$driver_online_today->hours;
                $driver_online_today->minutes = ($driver_online_today->minutes - 60);
            endif;
            $driver_online_today->save();
        else:
            $driver_online_yesterday = DriverOnlineTime::where([['driver_id', $driver_id]])->whereBetween('created_at', [Carbon::yesterday()->setTime(0, 0, 0)->format('Y-m-d H:i:s'), Carbon::yesterday()->setTime(23, 59, 59)->format('Y-m-d H:i:s')])->first();
            if (!empty($driver_online_yesterday)):
                $modify = array();
                $modify = $driver_online_yesterday->time_intervals;
                $last_one = end($modify);
                $last_one['offline_time'] = Carbon::yesterday()->setTime(23, 59, 59)->format('d-m-Y H:i');
                $last_one['hours'] = (new DateTime($last_one['offline_time']))->diff((new DateTime($last_one['online_time'])))->format('%H');
                $last_one['minutes'] = (new DateTime($last_one['offline_time']))->diff((new DateTime($last_one['online_time'])))->format('%i');
                array_pop($modify);
                array_push($modify, $last_one);
                $driver_online_yesterday->time_intervals = $modify;
                $driver_online_yesterday->hours = array_sum(array_column($modify, 'hours'));
                $driver_online_yesterday->minutes = array_sum(array_column($modify, 'minutes'));
                if($driver_online_yesterday->minutes > 60):
                    ++$driver_online_yesterday->hours;
                    $driver_online_yesterday->minutes = ($driver_online_yesterday->minutes - 60);
                endif;
                $driver_online_yesterday->save();
                $driver_online_data['merchant_id'] = $merchant_id;
                $driver_online_data['driver_id'] = $driver_id;
                $driver_online_data['time_intervals'] = [['online_time' => Carbon::now()->setTime(0, 0, 0)->format('d-m-Y H:i'), 'offline_time' => '', 'hours' => '00', 'minutes' => '00']];
                $driver_online_data_save = new DriverOnlineTime($driver_online_data);
                $driver_online_data_save->save();
                self::OfflineTimeRecord($driver_id, $merchant_id);
            endif;
        endif;
    }

    public function DriverSingleDayRecord($driver_id = null, $date_detail = null)
    {
        $date_detail = date('Y-m-d', strtotime($date_detail));
        $year = explode('-', $date_detail)[0];
        $month = explode('-', $date_detail)[1];
        $date = explode('-', $date_detail)[2];
        $fetched_date = DriverOnlineTime::where([['driver_id', $driver_id]])
            ->whereBetween('created_at', [Carbon::now()->setDate($year, $month, $date)->setTime(0, 0, 0)->format('Y-m-d H:i:s'), Carbon::now()->setDate($year, $month, $date)->setTime(23, 59, 59)->format('Y-m-d H:i:s')])
            ->first();
        $send = ":0 hr :0 mins";
        if (!empty($fetched_date)):
            $send = ":".$fetched_date->hours." hr :".$fetched_date->minutes." mins";
            $modify = array();
            $modify = $fetched_date->time_intervals;
            $last_one = end($modify);
            if($last_one['offline_time'] == ''):
                $last_one['offline_time'] = Carbon::now()->format('d-m-Y H:i');
                $last_one['hours'] = (new DateTime($last_one['offline_time']))->diff((new DateTime($last_one['online_time'])))->format('%H');
                $last_one['minutes'] = (new DateTime($last_one['offline_time']))->diff((new DateTime($last_one['online_time'])))->format('%i');
                array_pop($modify);
                array_push($modify, $last_one);
                $total_hours = array_sum(array_column($modify, 'hours'));
                $total_minutes = array_sum(array_column($modify, 'minutes'));
                if($total_minutes > 60):
                    ++$total_hours;
                    $total_minutes = ($total_minutes - 60);
                endif;
                $send = ":".$total_hours." hr :".$total_minutes." mins";
            endif;
        endif;
        return $send;
    }

    public static function penaltyDriver ($driver_id) {
     $driver = Driver::find($driver_id);
     $driver_config = DriverConfiguration::where('merchant_id' , $driver->merchant_id)->first();
     $merchant = \App\Models\Merchant::with('Configuration')->find($driver->merchant_id);
     if ($merchant->Configuration && $merchant->Configuration->driver_suspend_penalty_enable != 1) {
         return ;
     }
     $today_timestamp = date('Y-m-d').' 00:00:00';
     if ($driver_config->driver_penalty_enable == 1) {

       $cancelled_rides_now = Booking::where([
         ['driver_id' , '=' , $driver_id],
         ['booking_status' , '=' , 1007],
         ['updated_at' , '>' , $today_timestamp]
       ])->count();

       $cancelled_rides_later = DriverCancelBooking::where([
         ['driver_id' , '=' , $driver_id],
         ['created_at' , '>' , $today_timestamp]
       ])->count();

       $count = $cancelled_rides_later + $cancelled_rides_now;

       if ($count == $driver_config->driver_cancel_count) {
         $extended_time = date('Y-m-d H:i:s' , strtotime('+'. $driver_config->driver_penalty_period .' minutes' , strtotime(date('Y-m-d H:i:s'))));
         $driver->is_suspended = $extended_time;
       }
       if ($count > $driver_config->driver_cancel_count) {
         $extended_time = date('Y-m-d H:i:s' , strtotime('+'. $driver_config->driver_penalty_period_next .' minutes' , strtotime(date('Y-m-d H:i:s'))));
         $driver->is_suspended = $extended_time;
       }
       $driver->save();
     }
   }
}
