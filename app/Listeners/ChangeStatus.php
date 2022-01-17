<?php

namespace App\Listeners;

use App\Events\ChangeStatusEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ChangeStatus
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
     * @param object $event
     * @return void
     */
    public function handle(ChangeStatusEvent $event)
    {
        $this->updateStatus($event->order);
    }

    public function updateStatus($order)
    {
        $order->order_status = 'confirmed';
        $order->save();
    }
}
