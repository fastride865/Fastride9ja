<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserSignup extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $template_name;
    public $email_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $template_name, $data)
    {
        $this->user = $user;
        $this->template_name = $template_name;
        $this->email_data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to '.$this->user->Merchant->BusinessName)
            ->from($this->user->Merchant->email,$this->user->Merchant->BusinessName)
            ->view('emails.'.$this->template_name);
    }
}
