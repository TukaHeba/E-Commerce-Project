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
        $schedule->command('report:products-remaining-command')->cron('0 0 1 */2 *');

        $schedule->command('app:unsold-products-email-command')->monthly();

        $schedule->command('app:update-season-products-command')->monthlyOn(); // default on 1 of month and 00:000

        $schedule->command('app:late-products-report-command')->daily();

        $schedule->command('app:best_category_report_command')->monthlyOn(); // default on 1 of month and 00:000

        $schedule->command('app:best-products-report-command')->quarterlyOn(1, '08:00');

        $schedule->command('app:countries-with-highest-orders-command')->cron('0 10 1 1,5,9 *');

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
