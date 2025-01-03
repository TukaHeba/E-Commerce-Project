<?php

namespace App\Jobs;

use App\Models\User\User;
use Illuminate\Bus\Queueable;
use App\Models\Product\Product;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\UnsoldProductNotification;

class SendUnsoldProductEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    public $unsoldProducts; // Add a public property to store the result

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */public function handle(): void
    {
        // Fetch products that haven't been sold
        $this->unsoldProducts = Product::whereDoesntHave('orderItems')->get();

        // Notify the user about unsold products
        foreach ($this->unsoldProducts as $product) {
            $this->user->notify(new UnsoldProductNotification($product));
        }
    }

    /**
     * Get the unsold products.
     */
    public function getUnsoldProducts()
    {
        return $this->unsoldProducts;

    }
}

