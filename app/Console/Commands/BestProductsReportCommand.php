<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\BestProductsReportJob;

class BestProductsReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:best-products-report-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to get best products sold report for leatest 3 mounth';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        BestProductsReportJob::dispatch();
        $this->info('The command products-with-highest-sellings-command is done');
    }
}
