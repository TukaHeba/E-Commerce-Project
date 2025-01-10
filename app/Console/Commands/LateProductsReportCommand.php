<?php

namespace App\Console\Commands;

use App\Jobs\LateProductsReportJob;
use App\Models\User\User;
use Illuminate\Console\Command;

class LateProductsReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:late-products-report-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a daily notification to the sales_manager regarding products that are overdue for delivery';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        LateProductsReportJob::dispatch();
    }
}
