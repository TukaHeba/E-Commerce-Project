<?php

namespace App\Console;

use App\Jobs\SendDelayedOrderEmail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Sends an email with remaining products report every two months on the first day at midnight.
        $schedule->command('report:products-remaining-command')->cron('0 0 1 */2 *');

        // Sends an email with unsold products report once a month.
        $schedule->command('app:unsold-products-report-command')->monthly();

        // Sends an email with update seasonal product report monthly, defaults to running on the 1st at midnight.
        $schedule->command('app:update-season-products-command')->monthlyOn();

        // Sends an email with late-products report of products that are overdue.
        $schedule->command('app:late-products-report-command')->daily();

        // Sends an email with best-performing product categories report monthly, defaults to the 1st at midnight.
        $schedule->command('app:best_category_report_command')->monthlyOn();

        // Sends an email with best-performing products report quarterly on the 1st day of the quarter at 8:00 AM.
        $schedule->command('app:best-products-report-command')->quarterlyOn(1, '08:00');

        // Sends an email with highest orders report on January, May, and September 1st at 10:00 AM.
        $schedule->command('app:countries-with-highest-orders-command')->cron('0 10 1 1,5,9 *');

        // Fetches the latest Telegram bot users every ten minutes.
        $schedule->command('app:get-telegram-bot-users-command')->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
