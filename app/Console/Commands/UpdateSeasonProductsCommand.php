<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateSeasonProductsJob;

class UpdateSeasonProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-season-products-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Cache key => season_products season products from the same mounth of last year in the background';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UpdateSeasonProductsJob::dispatch();
        $this->info('Job dispatched to update offers products.');
    }
}
