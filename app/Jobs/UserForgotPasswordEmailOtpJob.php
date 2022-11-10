<?php

namespace App\Jobs;

use App\Http\Controllers\Merchant\emailTemplateController;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserForgotPasswordEmailOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $Customer;
    public $otp;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $Customer, $otp)
    {
        $this->Customer = $Customer;
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
        $email_send->ForgotPasswordEmail($this->Customer,$this->otp);
    }
}
