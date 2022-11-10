<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DriverSignupEmailOtpEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $driver_email;
    public $merchant_id;
    public $otp;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($merchant_id, $driver_email, $otp)
    {
        $this->merchant_id = $merchant_id;
        $this->driver_email = $driver_email; // Email Of Driver like check@gmail.com
        $this->otp = $otp;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
