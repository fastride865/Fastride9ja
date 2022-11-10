<?php

namespace App\Listeners;

use App\Events\UserSignupWelcome;
use App\Http\Controllers\Merchant\emailTemplateController;
use App\Jobs\UserSignupWelcomeJob;

class UserSignupWelcomeListener
{
    public function __construct()
    {
    }

    public function handle(UserSignupWelcome $data)
    {
        dispatch(new UserSignupWelcomeJob($data->user_id));
//        $email_listener = new emailTemplateController();
//        $email_listener->WelcomeOnSignup($data->user_id, $data->template_name);
    }
}
