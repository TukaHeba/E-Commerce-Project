<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Telegram\TelegramChannel;

class OrderConfirmationNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'telegram'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Confirmation')
            ->greeting('Hello dear ' . $notifiable->first_name  . '!')
            ->line('Your order has been confirmed!')
            ->line('Here is your order details to follow it up:')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Total Price: $' . number_format($this->order->total_price, 2))
            ->line('Order Status: ' . $this->order->status)
            ->line('We will keep you updated on any changes to your order status.')
            ->line('Thank you for shopping with us!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'order_number' => $this->order->order_number,
            'total_price' => $this->order->total_price,
            'status' => $this->order->status,
            'message' => 'Your order has been confirmed!',
        ];
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram($notifiable)
    {
        $telegramMessage = TelegramMessage::create()
            ->content(
                "*Hello dear {$notifiable->first_name}!*" . "\n\n" .
                    "Your order has been confirmed!\n\n" .
                    "Here is your order details to follow it up:\n" .
                    "*Order Number:* {$this->order->order_number}\n" .
                    "*Total Price:* \$" . number_format($this->order->total_price, 2) . "\n" .
                    "*Order Status:* {$this->order->status}\n\n" .
                    "We will keep you updated on any changes to your order status.\n" .
                    "Thank you for shopping with us!"
            )
            ->options(['parse_mode' => 'Markdown']);
        return $telegramMessage;
    }
}
