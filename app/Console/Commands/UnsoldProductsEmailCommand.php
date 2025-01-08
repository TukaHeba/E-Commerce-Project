<?php

namespace App\Console\Commands;

use App\Models\User\User;
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
    protected $signature = 'app:unsold-products-email-command';

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
        $users = User::role('store manager')->get();
        $file = $this->ExportService->productsNeverBeenSoldExportStorage();
       foreach ($users as $user) {
        SendUnsoldProductEmail::dispatch($user , $file);
    }
    }

}

