<?php

namespace App\Listeners;

use App\Events\RideBookingDriverNotificationEvent;
use App\Models\Onesignal;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RideBookingDriverNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RideBookingDriverNotificationEvent  $event
     * @return void
     */
    public function handle(RideBookingDriverNotificationEvent $notification_data)
    {
        Onesignal::DriverPushMessage($notification_data->playerids, $notification_data->sent_data, $notification_data->message, $notification_data->type, $notification_data->merchant_id);
    }
}
