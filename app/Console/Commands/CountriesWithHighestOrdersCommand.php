<?php

namespace App\Console\Commands;

use App\Jobs\CountriesWithHighestOrdersJob;
use App\Models\User\User;
use App\Services\Export\ExportService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CountriesWithHighestOrdersCommand extends Command
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
    protected $signature = 'app:countries-with-highest-orders-command';

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
        CountriesWithHighestOrdersJob::dispatch();
        $this->info('The command countries-with-highest-orders-command is done');
    }
}
