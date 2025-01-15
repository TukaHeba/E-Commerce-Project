<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LowProductQuantityEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $admin;

    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $product
     * @param \Illuminate\Database\Eloquent\Model $admin
     */
    public function __construct(Model $product, Model $admin)
    {
        $this->product = $product;
        $this->admin = $admin;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin_' . $this->admin->id),
        ];
    }

    /**
     * Specify the event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'productQuantity.low';
    }

    /**
     * Specify the data to be broadcast with the event.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'message' => "Attention, " . $this->admin->first_name . "! The stock for product '" . $this->product->name . "' is critically low. Only " . $this->product->product_quantity . " items left!",
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'quantity' => $this->product->product_quantity,
            ],
        ];
    }
}
