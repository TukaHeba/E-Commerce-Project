<?php

namespace App\Jobs;

use App\Services\Report\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProductsRemainingReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle()
    {
        \Log::info('Before execution job');
        $reportService = app()->make(ReportService::class);
        $reportService->getProductsRemaining();
        \Log::info('After execution job');
    }
}
