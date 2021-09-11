<?php

namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use Illuminate\Mail\Message;

class NotifyAdminListener
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

        \Mail::send('emails.admin', ['order' => $order], function (Message $message) {
            $message->subject('An Order has been completed.');
            $message->to('admin@admin.com');
        });
    }
}
