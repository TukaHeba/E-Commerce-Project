<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\BestCategoriesReportJob;

class BestCategoriesReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:best_category_report_command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        BestCategoriesReportJob::dispatch();
        $this->info('The command category-with-highest-sellings-command is done');
    }
}
