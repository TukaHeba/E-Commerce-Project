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
    protected $description = 'Update offer products from the same month of last year in the background';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UpdateOfferProductsJob::dispatch();
        $this->info('Job dispatched to update offers products.');
    }
}
