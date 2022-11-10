<?php

namespace App\Jobs;

use App\Http\Controllers\Merchant\emailTemplateController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendUserInvoiceMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $booking_data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($booking_data)
    {
        $this->booking_data = $booking_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email_listener = new emailTemplateController();
        $email_listener->SendTaxiInvoiceEmail($this->booking_data);
    }
}
