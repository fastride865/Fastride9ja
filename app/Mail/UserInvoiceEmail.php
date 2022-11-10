<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserInvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $template;
    public $event;

    public function __construct(Booking $template)
    {
        $this->template = $template;
        $this->merchantname = 'ApporioTaxi';
        $this->merchantemail = 'apporio@apporio.com';
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->subject('Welcome to '.$this->merchantname)->from($this->merchantemail,$this->merchantname)->view('emails.'.$this->event);
        return $this->subject('Invoice For Booking: #'.$this->template->id)
            ->from($this->template->Merchant->email,$this->template->Merchant->BusinessName)
            ->markdown('emails.invoice');
    }
}
