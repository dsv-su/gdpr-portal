<?php

namespace App\Listeners;

use App\Events\FinishedJobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendFinishedNotification implements ShouldQueue
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
     * @param  FinishedJobs  $event
     * @return void
     */
    public function handle(FinishedJobs $event)
    {
        if($event->case->download == 2)
        {
            dd('Done');
        }
    }
}
