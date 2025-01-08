<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ProductsRemainingNotification extends Notification
{
    use Queueable;

    protected $filePath;

    /**
     * Create a new notification instance.
     */
    public function __construct($file)
    {
        $this->filePath = $file;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('Products Remaining in Carts Report')
            ->greeting('Hello Admin,');

        if (empty($this->filePath)) {
            $mailMessage->line('No products remaining in carts older than 2 months.');
        } else {
            $mailMessage->line('The file following contain products that have been left for over 2 months:')
                ->attach(Storage::disk('public')->path($this->filePath), [
                    'as' => 'products_remaining.xlsx',
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
        }
        $mailMessage->line('Thank you for reviewing this report.');
        return $mailMessage;
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
