<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnsoldProductNotification extends Notification
{
    use Queueable;

    protected $unsoldProducts;

    /**
     * Create a new notification instance.
     */
    public function __construct($unsoldProducts)
    {
        $this->unsoldProducts = $unsoldProducts;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('The Products Has Not Been Sold');

        foreach ($this->unsoldProducts as $product) {
            $mailMessage->line('Product Name: ' . $product->name)
                ->line('Main Category Id: ' . $product->category->main_category_id)
                ->line('Sub Category Id:' . $product->category->sub_category_id)
                ->action('View Product', url('/products/' . $product->id));
        }

        return $mailMessage;
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {

        foreach ($this->unsoldProducts as $product) {
            $data[] = [
                'Product Id' => $product->id,
                'Main Category Id' => $product->category->main_category_id,
                'Sub Category Id' => $product->category->sub_category_id,
            ];
        }
        return
            [
                'message' => 'The Products has not been sold',
                'products' => $data
            ];
    }
}
