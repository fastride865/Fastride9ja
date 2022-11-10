<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserSignupEmailOtpEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $otp;
    public $user_email;
    public $merchant_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($merchant_id, $user_email, $otp)
    {
        $this->otp = $otp;
        $this->user_email = $user_email;
        $this->merchant_id = $merchant_id;
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
