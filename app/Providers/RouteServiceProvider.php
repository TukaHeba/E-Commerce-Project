<?php

namespace App\Providers;

use App\Models\User\User;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use App\Models\Category\SubCategory;
use App\Models\Category\MainCategory;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        Route::model('user', User::class);
        Route::model('product', Product::class);
        Route::model('subCategory', SubCategory::class);
        Route::model('mainCategory', MainCategory::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Define a stricter rate limiter for authentication-related routes.
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Define dynamic route model bindings
        // Route::bind('user', function ($value) {
        //     return User::findOrFail($value);
        // });

        // Route::bind('product', function ($value) {
        //     return Product::findOrFail($value);
        // });

        // Route::bind('mainCategory', function ($value) {
        //     return MainCategory::findOrFail($value);
        // });

        // Route::bind('subCategory', function ($value) {
        //     return SubCategory::findOrFail($value);
        // });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
