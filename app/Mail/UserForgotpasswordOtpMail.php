<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserForgotpasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $otp_message;

    public function __construct(User $user, $otpMessage)
    {
        //
        $this->user = $user;
        $this->otp_message = $otpMessage;
    }

    public function build()
    {
        return $this->subject('Forgot Password')->from($this->user->Merchant->email, $this->user->Merchant->BusinessName)->markdown('emails.user-forgot-password-otp');
    }
}
