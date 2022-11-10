<?php

namespace App\Mail;

use App\Models\CustomerSupport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerSupportQueryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customerSupportData;

    public function __construct(CustomerSupport $customerSupport)
    {
        $this->customerSupportData = $customerSupport;

    }

    public function build()
    {
        return $this->subject('Customer Support Query of ' . $this->customerSupportData->phone)
            ->from($this->customerSupportData->Merchant->email, $this->customerSupportData->Merchant->BusinessName)
            ->view('emails.customer_support_query');
    }
}
