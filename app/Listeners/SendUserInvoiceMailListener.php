<?php

namespace App\Listeners;

use App\Events\SendUserInvoiceMailEvent;
use App\Http\Controllers\Merchant\emailTemplateController;
use App\Jobs\SendUserInvoiceMailJob;

class SendUserInvoiceMailListener
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
     * @param  SendUserInvoiceMailEvent  $data
     * @return void
     */
    public function handle(SendUserInvoiceMailEvent $data)
    {
        dispatch(new SendUserInvoiceMailJob($data->booking_data));
//        $email_listener = new emailTemplateController();
//        $email_listener->SendInvoiceEmail($data->template_name, $data->booking_data);
    }
}
