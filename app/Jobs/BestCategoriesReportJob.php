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
use App\Notifications\BestCAtegoriesNotification;

class BestCategoriesReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $file = (new ExportService(new ReportService()))->bestCategoriesExportStorage();
        $users = User::role(['sales manager', 'store manager'])->get();
        foreach ($users as $user) {
            Notification::send($user, new BestCategoriesNotification($file));
        }
    }
}
