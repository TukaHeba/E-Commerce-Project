<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendUnsoldProductEmail;
use App\Services\Export\ExportService;

class UnsoldProductsEmailCommand extends Command
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
    protected $signature = 'app:unsold-products-report-command';

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
        SendUnsoldProductEmail::dispatch();
    }
}
