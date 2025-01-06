<?php

namespace App\Console\Commands;

use App\Models\User\User;
use App\Models\Product\Product;
use Illuminate\Console\Command;
use App\Jobs\SendUnsoldProductEmail;

class UnsoldProductsEmailCommand extends Command
{
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
        $users = User::role('sales manager')->get();
       foreach ($users as $user) {
        SendUnsoldProductEmail::dispatch($user , Product::generateProductsNeverBeenSoldReport());

    }
    }

}

