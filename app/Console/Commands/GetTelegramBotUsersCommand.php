<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\User\TelegramService;

class GetTelegramBotUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-telegram-bot-users-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command aims to get the telegram_user_id of Telegram bot users';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegramService)
    {
        Log::info('Start GetTelegramBotUsersCommand command');
        $telegramService->fetchAndProcessTelegramUpdates();
        Log::info('End GetTelegramBotUsersCommand command');
    }
}
