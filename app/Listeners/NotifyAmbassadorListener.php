<?php

namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use Illuminate\Mail\Message;

class NotifyAmbassadorListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(OrderCompletedEvent $event)
    {
        $order = $event->order;

        \Mail::send('emails.ambassador', ['order' => $order], function (Message $message) use ($order) {
            $message->subject('An Order has been completed.');
            $message->to($order->ambassador_email);
        });
    }
}
