<?php

namespace App\Jobs;

use App\Http\Controllers\Merchant\emailTemplateController;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MailProcessedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $booking;
    public $template_name;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Booking $booking, $template_name = null)
    {
        $this->booking = $booking;
        $this->template_name = $template_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        $email_listener = new emailTemplateController();
//        $email_listener->SendInvoiceEmail($this->template_name, $this->booking);
    }
}
