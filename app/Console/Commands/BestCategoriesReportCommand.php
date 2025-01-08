<?php

namespace App\Console\Commands;

use App\Models\User\User;
use Illuminate\Console\Command;
use App\Jobs\BestCategoriesReportJob;
use App\Services\Export\ExportService;

class BestCategoriesReportCommand extends Command
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
        $user = User::firstWhere('email', 'nzeer1234aldrweesh@gmail.com');
        $file = $this->ExportService->bestCategoriesExportStorage();
        BestCategoriesReportJob::dispatch($user, $file);
    }
}
