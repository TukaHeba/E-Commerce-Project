<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductsRemainingNotification extends Notification
{
    use Queueable;

    protected $productsRemaining;

    /**
     * Create a new notification instance.
     */
    public function __construct($products)
    {
        $this->productsRemaining = $products;
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

        if (empty($this->productsRemaining)) {
            $mailMessage->line('No products remaining in carts older than 2 months.');
        } else {
            $mailMessage->line('The following carts contain products that have been left for over 2 months:');

            foreach ($this->productsRemaining as $cartIndex => $cart) {
                $mailMessage->line("**Cart " . $cart['id'] . ":**");
                $mailMessage->line("- **User ID:** {$cart['user_id']}");
                $mailMessage->line("- **Products:**");

                if (!empty($cart['cart_items'])) {
                    foreach ($cart['cart_items'] as $itemIndex => $item) {
                        $mailMessage->line("  " . ($itemIndex + 1) . ". **Product ID:** {$item['product']['id']}, **Name:** {$item['product']['name']}, **Added On:** {$item['created_at']}");
                    }
                } else {
                    $mailMessage->line("  No products found in this cart.");
                }

                $mailMessage->line('');
            }
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
