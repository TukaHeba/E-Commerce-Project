<?php

namespace App\Services\User;

use App\Models\User\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $botToken;
    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
    }

    /**
     * Fetches and processes new updates from the Telegram Bot API.
     *
     * This method retrieves updates, processes each to extract user information, 
     * and updates the cache with the ID of the last processed update to avoid re-processing.
     *
     * @return void
     */
    public function fetchAndProcessTelegramUpdates()
    {
        $lastUpdateId = cache('last_processed_update_id', 0);
        $url = "https://api.telegram.org/bot{$this->botToken}/getUpdates?offset=" . ($lastUpdateId + 1);
        $response = Http::get($url);

        if ($response->successful()) {
            $updates = $response->json();

            foreach ($updates['result'] as $update) {
                $this->handleTelegramUpdate($update);
                $lastUpdateId = $update['update_id'];
            }

            cache(['last_processed_update_id' => $lastUpdateId]);
        }
    }

    /**
     * Processes incoming Telegram updates to handle user interaction.
     *
     * This method extracts the user's Telegram ID and message. 
     * It checks if the message starts with "/start" to request the user's email.
     *
     * @param array $update The update data from the Telegram Bot API
     * @return void
     */
    private function handleTelegramUpdate($update)
    {
        $telegramId = $update['message']['chat']['id'] ?? null;
        $text = trim($update['message']['text'] ?? '');

        if ($telegramId) {
            if (strpos($text, '/start') === 0) {
                $this->requestUserEmail($telegramId);
            } else {
                $this->handleUserResponse($telegramId, $text);
            }
        }
    }

    /**
     * Sends a request to the user to provide their email address.
     *
     * This method sends a message to the user asking them to reply with their email address 
     * that is registered on the website in order to link their Telegram account.
     *
     * @param int $telegramId The Telegram ID of the user
     * @return void
     */
    private function requestUserEmail($telegramId)
    {
        $message = "Welcome! Please reply with your email address registered on our website.";
        $this->sendMessage($telegramId, $message);
    }

    /**
     * Processes the user's email response to link their Telegram account.
     *
     * This method checks if the email provided by the user matches an existing account. 
     * If a matching account is found, it links the user's Telegram account to the profile.
     * If no account is found, it informs the user that the email is incorrect.
     *
     * @param int $telegramId The Telegram ID of the user
     * @param string $text The email address provided by the user
     * @return void
     */
    private function handleUserResponse($telegramId, $text)
    {
        $user = User::where('email', $text)->first();

        if ($user) {
            $this->linkTelegramAccount($telegramId, $user);
        } else {
            $this->noAccountFound($telegramId);
        }
    }

    /**
     * Links the Telegram account to the user's account in the database.
     *
     * This method updates the user's data in database and add his telegram_user_id.
     *
     * @param int $telegramId The Telegram ID of the user
     * @param User $user The user model instance to link the Telegram account
     * @return void
     */
    private function linkTelegramAccount($telegramId, $user)
    {
        $user->telegram_user_id = $telegramId;
        $user->save();

        $message = "Your Telegram account has been successfully linked. Thank you!";
        $this->sendMessage($telegramId, $message);
    }

    /**
     * Informs the user that no account was found with the provided email.
     *
     * @param int $telegramId The Telegram ID of the user
     * @return void
     */
    private function noAccountFound($telegramId)
    {
        $message = "We couldn't find an account with the email you provided. Try again!";
        $this->sendMessage($telegramId, $message);
    }

    /**
     * Sends a message to the user via the Telegram Bot API.
     *
     * This method sends a specified message to the user with the provided Telegram ID 
     * through the Telegram Bot API.
     *
     * @param int $telegramId The Telegram ID of the user
     * @param string $message The message to send to the user
     * @return void
     */
    private function sendMessage($telegramId, $message)
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        Http::post($url, ['chat_id' => $telegramId, 'text' => $message]);
    }
}
