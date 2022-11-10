<?php

namespace App\Events;

use App\Models\BusinessSegment\BusinessSegment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class BusinessSegmentForgotPasswordEmailOtpEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $business_segment;
    public $otp;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BusinessSegment $business_segment, $otp)
    {
        $this->business_segment = $business_segment;
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
