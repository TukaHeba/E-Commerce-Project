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
use App\Notifications\UnsoldProductNotification;

class SendUnsoldProductEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::role('store manager')->get();
        $file = (new ExportService(new ReportService()))
            ->productsNeverBeenSoldExportStorage();
        foreach ($users as $user) {
            $user->notify(new UnsoldProductNotification($file));
        }
    }
}
