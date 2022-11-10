<?php

namespace App\Events;

use App\Models\Driver;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class DriverForgotPasswordEmailOtpEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $driver;
    public $otp;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Driver $driver, $otp)
    {
        $this->driver = $driver;
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
