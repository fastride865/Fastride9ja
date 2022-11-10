<?php

namespace App\Jobs;

use App\Http\Controllers\Merchant\emailTemplateController;
use App\Models\Driver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DriverForgotPasswordEmailOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $driver;
    public $otp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Driver $driver, $otp)
    {
        $this->driver = $driver;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email_send = new emailTemplateController();
        $email_send->ForgotPasswordEmailDriver($this->driver,$this->otp);
    }
}
