<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserSigupOtpEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $template_data;
    public $otp;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $template_data, $otp)
    {
        $this->user = $user;
        $this->template_data = $template_data;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Signup Otp')
            ->from($this->user->Merchant->email,$this->user->Merchant->BusinessName)
            ->view('emails.'.$this->template_data->template_name);
    }
}
