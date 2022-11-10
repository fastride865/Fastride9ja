<?php

namespace App\Listeners;

use App\Events\DriverSignupEmailOtpEvent;
use App\Http\Controllers\Merchant\emailTemplateController;
use App\Jobs\DriverSignupEmailOtpJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DriverSignupEmailOtpListener
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
     * @param  DriverSignupEmailOtpEvent  $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new DriverSignupEmailOtpJob($event->merchant_id, $event->driver_email, $event->otp));
//        $email_listener = new emailTemplateController();
//        $email_listener->DriverSignupEmailOtp($event->driver_email, $event->otp);
    }
}
