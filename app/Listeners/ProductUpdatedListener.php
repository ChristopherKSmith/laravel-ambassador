<?php

namespace App\Listeners;

use App\Events\ProductUpdatedEvent;

class ProductUpdatedListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(ProductUpdatedEvent $event)
    {
        \Cache::forget('products_frontend');
        \Cache::forget('products_backend');
    }
}
