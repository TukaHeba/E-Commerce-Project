<?php

namespace App\Console\Commands;

use App\Jobs\ProductsRemainingReportJob;
use App\Models\User\User;
use App\Services\Export\ExportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProductsRemainingCommand extends Command
{
    protected $exportFile;
    public function __construct(ExportService $exportService)
    {
        parent::__construct();
        $this->exportFile = $exportService;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:products-remaining-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a report for products that older than 2 months inside cart';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Before execution command');
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            ProductsRemainingReportJob::dispatch($admin);
        }
        $this->info('Products remaining report job dispatched successfully.');
        Log::info('After execution command');
    }
}
