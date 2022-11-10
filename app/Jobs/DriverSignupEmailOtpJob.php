<?php

namespace App\Jobs;

use App\Http\Controllers\Merchant\emailTemplateController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DriverSignupEmailOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $otp;
    public $driver_email;
    public $merchant_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($merchant_id, $driver_email, $otp)
    {
        $this->merchant_id = $merchant_id;
        $this->driver_email = $driver_email;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email_listener = new emailTemplateController();
        $email_listener->DriverSignupEmailOtp($this->merchant_id, $this->driver_email, $this->otp);
    }
}
