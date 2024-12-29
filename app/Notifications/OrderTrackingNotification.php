<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderTrackingNotification extends Notification
{
    use Queueable;

    protected $user_email;
    protected $user_first_name;
    protected $order_id;
    protected $order_status;

    public function __construct($user_email, $user_first_name, $order_id, $order_status)
    {
        $this->user_email = $user_email;
        $this->user_first_name = $user_first_name;
        $this->order_id = $order_id;
        $this->order_status = $order_status;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('api/orders/'.$this->order_id);
        Log::info('User order passed to Job: ' . $url);

        return (new MailMessage)
            ->greeting('Hello ' . $this->user_first_name . '!')
            ->line('The status of your order has been updated to ' . $this->order_status . '.')
            ->action('View Order', $url)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
