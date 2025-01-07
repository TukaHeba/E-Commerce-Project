<?php

namespace App\Jobs;

use App\Models\User\User;
use App\Services\Report\ReportService;
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
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('Before execution job');
        $reportService = app()->make(ReportService::class);
        $productsRemaining = $reportService->getProductsRemainingInCarts();
        $this->user->notify(new ProductsRemainingNotification($productsRemaining));
        Log::info('After execution job');
    }
}
