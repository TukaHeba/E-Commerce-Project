<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChangeStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $order, $user;

    /**
     * Create a new event instance.
     * @param \Illuminate\Database\Eloquent\Model $order
     * @param \Illuminate\Database\Eloquent\Model $user
     */
    public function __construct(Model $order, Model $user)
    {
        $this->order = $order;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user_' . $this->user->id)
        ];
    }

    /**
     * Specify the event's broadcast name.
     * @return string
     */
    public function broadcastAs()
    {
        return 'orderStatus.changed';
    }

    /**
     * Details related to event
     * @return array{cause: mixed, message: string[]}
     */
    public function broadcastWith()
    {
        return [
            'message' => "New update, " . $this->user->first_name . "! The status of your order " . $this->order->order_number . " has changed to " . $this->order->status,
        ];
    }
}
