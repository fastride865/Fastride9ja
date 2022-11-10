<?php

namespace App\Listeners;

use App\Events\MailProcessedEvent;
use App\Jobs\MailProcessedJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MailProcessedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MailProcessedEvent  $event
     * @return void
     */
    public function handle(MailProcessedEvent $event)
    {
        dispatch(new MailProcessedJob($event->booking, $event->template_name));
    }
}
