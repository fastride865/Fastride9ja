<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MailProcessedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $template_name;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Booking $booking, $template_name = null)
    {
        $this->booking = $booking;
        $this->template_name = $template_name;
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
