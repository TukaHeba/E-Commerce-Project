<?php

namespace App\Jobs;

use App\Models\User\User;
use App\Services\Export\ExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Notifications\ProductsRemainingNotification;

class ProductsRemainingReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('Before execution job');
        $exportFile = app()->make(ExportService::class);
        $admins = User::role(['admin', 'store manager'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new ProductsRemainingNotification($exportFile->productsRemainingInCartsExport()));
        }
        Log::info('After execution job');
    }
}
