<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnsoldProductNotification extends Notification
{
    use Queueable;

    protected $product;

    /**
     * Create a new notification instance.
     */
    public function __construct($product)
    {
        $this->product = $product;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [ 'email','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Unsold Product Alert: ' . $this->product->id)
            ->line('The product has not been sold')
            ->line('Product Name: ' . $this->product->name)
            ->line('Category: ' . $this->product->category)
            ->action('View Product', url('/products/' . $this->product->id));
    }
    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'Product Id:' => $this->product->id,
            'Category: ' => $this->product->category,
            'message' => 'The product has not been sold',
        ];
    }
}
