<?php

namespace App\Console\Commands;

use App\Models\User\User;
use App\Models\Product\Product;
use Illuminate\Console\Command;
use App\Jobs\LowOnStockReportJob;

class LowOnStockReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:low-on-stock-report-command';

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
        $user = User::firstWhere('email', 'moon_2060@gmail.com');
        LowOnStockReportJob::dispatch($user , Product::generateLowStockReport());  
    }
}
