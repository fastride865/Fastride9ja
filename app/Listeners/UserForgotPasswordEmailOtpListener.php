<?php

namespace App\Listeners;

use App\Events\UserForgotPasswordEmailOtpEvent;
use App\Http\Controllers\Merchant\emailTemplateController;
use App\Jobs\UserForgotPasswordEmailOtpJob;
use App\Mail\CustomerSupportQueryMail;
use App\Mail\UserForgotpasswordOtpMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserForgotPasswordEmailOtpListener
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
        dispatch(new UserForgotPasswordEmailOtpJob($event->Customer,$event->otp));
//        $email_send = new emailTemplateController();
//        $email_send->ForgotPasswordEmail($event->Customer,$event->otp);
    }
}
