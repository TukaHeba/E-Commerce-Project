<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class CountriesWithHighestOrdersNotification extends Notification
{
    use Queueable;

    protected $file_path;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $file_path)
    {
        $this->file_path = $file_path;
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
            ->greeting('Dear sirs,')
            ->subject('Top 5 Countries by Orders Report for the Past 4 Months')
            ->attach(Storage::disk('public')->path($this->file_path), [
                'as' => 'CountryWithHighestOrder-'.Carbon::now()->format('Y-m-d').'.xlsx',
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->line('Here is the attached Excel table of the top selling countries over the past four months.')
            ->line('Best Regards,');
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
