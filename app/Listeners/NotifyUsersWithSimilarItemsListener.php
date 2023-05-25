<?php

namespace App\Listeners;

use App\Events\NewItemEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyUsersWithSimilarItemsListener
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
     * @param  NewItemEvent  $event
     * @return void
     */
    public function handle(NewItemEvent $event)
    {

        $item  = $event->item;

    }
}
