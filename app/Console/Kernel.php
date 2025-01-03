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
        $schedule->command('report:products-remaining-command')
            ->cron('0 0 1 */2 *') // تشغيل كل شهرين في اليوم الأول عند منتصف الليل
            ->emailOutputTo('admin@gmail.com');
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:unsold-products-email-command')->monthly();
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
