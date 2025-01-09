<?php

namespace App\Jobs;

use App\Models\User\User;
use App\Notifications\CountriesWithHighestOrdersNotification;
use App\Services\Export\ExportService;
use App\Services\Report\ReportService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class CountriesWithHighestOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     * Send report for sales managers and store managers
     * The report includes the top five countries with the most orders during the past four months.
     */
    public function handle(): void
    {
        $users = User::role(['sales manager', 'store manager'])->get();
        $file_path = (new ExportService(new ReportService()))
            ->countriesWithHighestOrdersExportStorage(['start_date' => Carbon::now()->subMonths(4)->format('Y-m-d')]);

        Notification::send($users, new CountriesWithHighestOrdersNotification($file_path));

    }
}
