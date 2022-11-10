<?php

namespace App\Events;

use App\Models\CustomerSupport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;


class CustomerSupportEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(CustomerSupport $customerSupport)
    {
        $this->customer_support = $customerSupport;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
