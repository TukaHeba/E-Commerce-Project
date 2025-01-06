<?php

namespace App\Console\Commands;

use App\Jobs\ProductsRemainingReportJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProductsRemainingCommand extends Command
{
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
        ProductsRemainingReportJob::dispatch();
        $this->info('Products remaining report job dispatched successfully.');
        Log::info('After execution command');
    }
}
