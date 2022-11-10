<?php

namespace App\Listeners;

use App\Events\CustomerSupportEvent;
use App\Http\Controllers\Merchant\emailTemplateController;
use App\Mail\CustomerSupportQueryMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerSupportListener
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
     * @param  CustomerSupportEvent $data
     * @return void
     */
    public function handle(CustomerSupportEvent $data)
    {
        $email_listener = new emailTemplateController();
        $email_listener->CustomerSupportSendEmail($data->customer_support);
        
    }
}
