<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateOfferProductsJob;

class UpdateOfferProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-offer-products-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Cache key => best_selling_products_last_year offer products from the same mounth of last year in the background';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UpdateOfferProductsJob::dispatch();
        $this->info('Job dispatched to update offers products.');
    }
}
