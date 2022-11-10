<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserForgotPasswordEmailOtpEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $Customer;
    public $otp;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user_data, $otp)
    {
        $this->Customer = $user_data;
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
