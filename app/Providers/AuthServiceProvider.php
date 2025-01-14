<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Cart\Cart;
use App\Models\Rate\Rate;
use App\Models\User\User;
use App\Models\Order\Order;
use App\Models\Product\Product;
use App\Policies\Cart\CartPolicy;
use App\Policies\Rate\RatePolicy;
use App\Policies\User\UserPolicy;
use App\Policies\Order\OrderPolicy;
use App\Models\Category\SubCategory;
use App\Models\Category\MainCategory;
use App\Policies\Product\ProductPolicy;
use App\Policies\SubCategory\SubCategoryPolicy;
use App\Policies\MainCategory\MainCategoryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Order::class => OrderPolicy::class,
        User::class => UserPolicy::class,
        Cart::class => CartPolicy::class,
        MainCategory::class => MainCategoryPolicy::class,
        SubCategory::class => SubCategoryPolicy::class,
        Product::class => ProductPolicy::class,
        Rate::class => RatePolicy::class,
        
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
