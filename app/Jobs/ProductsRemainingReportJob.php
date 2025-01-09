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
    protected $file, $user;

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
        $exportFile = app()->make(ExportService::class);
        $file = $exportFile->productsRemainingInCartsExport();
        $this->user->notify(new ProductsRemainingNotification($file));
        Log::info('After execution job');
    }
}
