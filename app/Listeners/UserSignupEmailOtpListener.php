<?php

namespace App\Listeners;

use App\Events\UserSignupEmailOtpEvent;
use App\Http\Controllers\Merchant\emailTemplateController;
use App\Jobs\UserSignupEmailOtpJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserSignupEmailOtpListener
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
     * @param  UserSignupEmailOtpEvent  $event
     * @return void
     */
    public function handle($event)
    {
        dispatch(new UserSignupEmailOtpJob($event->merchant_id, $event->user_email, $event->otp));
    }
}
