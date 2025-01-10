<?php

namespace App\Console\Commands;

use App\Models\User\User;
use Illuminate\Console\Command;
use App\Jobs\BestProductsReportJob;
use App\Services\Export\ExportService;

class BestProductsReportCommand extends Command
{
    protected ExportService $ExportService;

    public function __construct(ExportService $ExportService)
    {
        parent::__construct();
        $this->ExportService = $ExportService;
    }
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
