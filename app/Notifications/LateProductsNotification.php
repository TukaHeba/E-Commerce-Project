<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class LateProductsNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $filePath ;
    public function __construct($filePath)
    {
        $this->filePath = $filePath ;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = Storage::path($this->filePath);
        return (new MailMessage)
            ->greeting('Hello Sales Manager')
            ->subject('Lating Orders to delivered')
            ->attach($url,[
                'as'=>'orders_Late_To_Deliver.xlsx',
                'mime'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ])
            ->line('Here is excel sheet in the attachment for lating orders for today');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
