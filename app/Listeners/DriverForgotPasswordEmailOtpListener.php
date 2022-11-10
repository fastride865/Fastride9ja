<?php

namespace App\Listeners;

use App\Events\DriverForgotPasswordEmailOtpEvent;
use App\Http\Controllers\Merchant\emailTemplateController;
use App\Jobs\DriverForgotPasswordEmailOtpJob;
use App\Mail\CustomerSupportQueryMail;
use App\Mail\UserForgotpasswordOtpMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DriverForgotPasswordEmailOtpListener
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

    public function handle($event)
    {
        dispatch(new DriverForgotPasswordEmailOtpJob($event->driver, $event->otp));
//        $email_send = new emailTemplateController();
//        $email_send->ForgotPasswordEmailDriver($data->Customer,$data->otp);
    }
}
