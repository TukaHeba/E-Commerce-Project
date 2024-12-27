<?php

namespace App\Observers;

use App\Models\Order\Order;
use App\Models\OrderTracking\OrderTracking;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Create a new record in the OrderTracking table

        OrderTracking::create([
            'order_id'=>$order->id,
            'old_status'=>null,
            'new_status'=>$order->status
        ]);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if the 'status' field has been modified
        if($order->isDirty('status')){

            // Create a new record in the OrderTracking table
            // This record logs the order ID, the previous status, and the updated status
            OrderTracking::create([
                'order_id'=>$order->id,
                'old_status'=>$order->getOriginal('status'),
                'new_status'=>$order->status
            ]);
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
