<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;


class UnsoldProductNotification extends Notification
{
    use Queueable;

    protected $filePath;


    /**
     * Create a new notification instance.
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
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
        return (new MailMessage)
            ->greeting('Hello dear ')
            ->subject('The Products Has Not Been Sold report')
            ->attach(Storage::disk('public')->path($this->filePath), [
                'as' => 'unsold_Products.xlsx',
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->line('Here is excel sheet in the attachment for the products has not been sold ')
            ->line('Best Regrads !');
    }
}
