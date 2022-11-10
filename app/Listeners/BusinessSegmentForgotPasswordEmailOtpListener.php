<?php

namespace App\Listeners;

use App\Events\BusinessSegmentForgotPasswordEmailOtpEvent;
use App\Http\Controllers\Merchant\emailTemplateController;
use App\Jobs\BusinessSegmentForgotPasswordEmailOtpJob;
use App\Mail\CustomerSupportQueryMail;
use App\Mail\UserForgotpasswordOtpMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BusinessSegmentForgotPasswordEmailOtpListener
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
        dispatch(new BusinessSegmentForgotPasswordEmailOtpJob($event->business_segment, $event->otp));
//        $email_send = new emailTemplateController();
//        $email_send->ForgotPasswordEmailDriver($data->Customer,$data->otp);
    }
}
