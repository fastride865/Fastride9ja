<?php

namespace App\Listeners;

use App\Events\DriverSignupWelcome;
use App\Http\Controllers\Merchant\emailTemplateController;
use App\Jobs\DriverSignupWelcomeJob;

class DriverSignupWelcomeListener
{
    public function __construct()
    {
    }

    public function handle(DriverSignupWelcome $data)
    {
        dispatch(new DriverSignupWelcomeJob($data->driver_id));
//        $email_listener = new emailTemplateController();
//        $email_listener->WelcomeOnSignupDriver($data->driver_id, $data->template_name);
    }
}
