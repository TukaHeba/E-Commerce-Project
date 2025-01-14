<?php

namespace App\Jobs;

use App\Models\User\User;
use Illuminate\Bus\Queueable;
use App\Services\Export\ExportService;
use App\Services\Report\ReportService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BestProductsNotification;

class BestProductsReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     * 
     * Send report for sales managers and store managers
     * The report includes the best sold products.
     */
    public function handle(): void
    {
        $file = (new ExportService(new ReportService()))->bestSellingProductsExportStorage();
        $users = User::role(['sales manager', 'store manager'])->get();
        foreach ($users as $user) {
            Notification::send($user, new BestProductsNotification($file));
        }
    }
}
