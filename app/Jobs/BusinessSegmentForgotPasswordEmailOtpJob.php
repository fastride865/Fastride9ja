<?php

namespace App\Jobs;

use App\Http\Controllers\Merchant\emailTemplateController;
use App\Models\BusinessSegment\BusinessSegment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BusinessSegmentForgotPasswordEmailOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $business_segment;
    public $otp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BusinessSegment $business_segment, $otp)
    {
        $this->business_segment = $business_segment;
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
        $email_send->ForgotPasswordEmailBusinessSegment($this->business_segment,$this->otp);
    }
}
