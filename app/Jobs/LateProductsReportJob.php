<?php

namespace App\Jobs;

use App\Models\User\User;
use App\Notifications\LateProductsNotification;
use App\Services\Export\ExportService;
use App\Services\Report\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class LateProductsReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Generate the file path for the report export
        $response = (new ExportService(new ReportService()))->ordersLateToDeliverExport();

        // Save the file to a temporary storage location
        $filePath = 'reports/orders_Late_To_Deliver.xlsx';
        Storage::disk('local')->put($filePath, $response->getContent());

        // Get all sales managers
        $sales_managers = User::role('sales manager')->get();

        // Send notification to each sales manager with the file attachment
        foreach ($sales_managers as $sales_manager) {
            Notification::send($sales_manager, new LateProductsNotification($filePath));
        }
    }
}
