<?php

use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\CartItem\CartItemController;
use App\Http\Controllers\Category\MainCategoryController;
use App\Http\Controllers\Category\SubCategoryController;
use App\Http\Controllers\Favorite\FavoriteController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\Photo\PhotoController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Rate\RateController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\PasswordResetController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// 1- Apply throttling (10 requests per minute) for authentication-related routes.
Route::middleware(['throttle:auth', 'security'])->group(function () {

    // ----------------------------------- Authentication Routes ----------------------------------- //
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register',  'register');
        Route::post('logout',  'logout')->middleware('auth:api');;
        Route::post('refresh-token',  'refresh')->middleware('auth:api');;
        // OAuth Routes
        Route::get('auth/{provider}', 'redirectToProvider');
        Route::get('auth/{provider}/callback', 'handleProviderCallback');
    });


    // ----------------------------------- Reset Password Routes ----------------------------------- //
    Route::controller(PasswordResetController::class)->group(function () {
        Route::post('password/reset', 'resetPassword');
        Route::post('password/forgot', 'sendResetLink');
    });
});

// 2- Apply throttling (60 requests per minute) for general API routes.
Route::middleware(['throttle:api', 'security'])->group(function () {

    // ----------------------------------------- User Routes ----------------------------------------- //
    Route::controller(UserController::class)->middleware('auth:api')->group(function () {
        Route::get('users/show-deleted', 'showDeleted');
        Route::delete('users/{user}/force-deleted', 'forceDeleted');
        Route::post('users/{user}/restore-deleted', 'restoreDeleted');
        Route::get('users/{user}/most-expensive-order', 'showmostExpensiveOrder');
        Route::apiResource('users', UserController::class);
    });


    // ---------------------------------- Roles & Permissuin Routes ---------------------------------- //
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);


    // ------------------------------------ Main Category Routes ------------------------------------ //
    Route::controller(MainCategoryController::class)->middleware('auth:api')->group(function () {
        Route::get('main-categories/{main-category}/show-deleted', 'showDeleted');
        Route::delete('main-categories/{main-category}/force-deleted', 'forceDeleted');
        Route::post('main-categories/{main-category}/restore-deleted', 'restoreDeleted');
        Route::apiResource('main-categories', MainCategoryController::class)->except(['index', 'show']);
    });
    Route::apiResource('main-categories', MainCategoryController::class)->only(['index', 'show']);


    // ------------------------------------ Sub Category Routes ------------------------------------ //
    Route::controller(SubCategoryController::class)->middleware('auth:api')->group(function () {
        Route::get('sub-categories/{sub-category}/show-deleted', 'showDeleted');
        Route::delete('sub-categories/{sub-category}/force-deleted', 'forceDeleted');
        Route::post('sub-categories/{sub-category}/restore-deleted', 'restoreDeleted');
        Route::apiResource('sub-categories', SubCategoryController::class)->except(['index', 'show']);
    });
    Route::apiResource('sub-categories', SubCategoryController::class)->only(['index', 'show']);


    // -------------------------------------- Favorite Routes -------------------------------------- //
    Route::controller(FavoriteController::class)->middleware('auth:api')->group(function () {
        Route::get('users/my-favorite-products', 'show');
        Route::post('products/{product}/add-to-favorite', 'store');
        Route::delete('products/{product}/remove-from-favorite', 'destroy');
    });


    // --------------------------------------- Rate Routes --------------------------------------- //
    Route::controller(RateController::class)->middleware('auth:api')->group(function () {
        Route::get('rates/{rate}/show-deleted', 'showDeleted');
        Route::delete('rates/{rate}/force-deleted', 'forceDeleted');
        Route::post('rates/{rate}/restore-deleted', 'restoreDeleted');
        Route::apiResource('rates', RateController::class)->except(['index', 'show']);
    });
    Route::apiResource('rates', RateController::class)->only(['index', 'show']);


    // ------------------------------------- Cart Routes ------------------------------------- //
    Route::controller(CartController::class)->middleware('auth:api')->group(function () {
        Route::get('carts/user-cart', 'userCart');
        Route::get('carts/checkout', 'checkout');
        Route::post('carts/place-order', 'placeOrder');
        Route::apiResource('carts', CartController::class)->only(['index', 'show']);
    });


    // ---------------------------------- Cart Items Routes ---------------------------------- //
    Route::controller(CartItemController::class)->middleware('auth:api')->group(function () {
        Route::post('cart-items', 'store');
        Route::put('cart-items/{cart-item}', 'update');
        Route::delete('cart-items/{cart-item}', 'destroy');
    });

    #FIXME Re-check showDeleted-user
    // ------------------------------------- Order Routes ------------------------------------- //
    Route::controller(OrderController::class)->middleware('auth:api')->group(function () {
        Route::get('orders', 'indexAdmin');
        Route::get('orders/user-orders', 'indexUser');
        Route::get('orders/oldest-order', 'showOldestOrder');
        Route::get('orders/latest-order', 'showLatestOrder');
        Route::get('orders/{order}/tracking', 'orderTracking');
        Route::delete('orders/{order}/force-deleted', 'forceDeleted');
        Route::post('orders/{order}/restore-deleted', 'restoreDeleted');
        Route::get('orders/{order}/show-deleted-user', 'showDeletedUser');
        Route::get('orders/{order}/show-deleted-admin', 'showDeletedAdmin');
        Route::apiResource('orders', OrderController::class)->except(['index', 'store']);
    });


    #FIXME Re-check authenticated operations
    // ------------------------------------- Photo Routes ------------------------------------- //
    Route::controller(PhotoController::class)->middleware('auth:api')->group(function () {
        Route::delete('photos/{photo}', 'destroy');
        Route::post('users/{user}/photos', 'storePhoto');
        Route::post('products/{product}/photos', 'storePhoto');
        Route::post('sub-category/{sub-category}/photos', 'storePhoto');
        Route::post('main-category/{main-category}/photos', 'storePhoto');
    });


    // ------------------------------------- Product Routes ------------------------------------- //
    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'index');
        Route::get('products/{product}', 'show');
        Route::get('products/top-rated', 'topRatedProducts');
        Route::get('products/filter', 'getProductsWithFilter');
        Route::get('products/category', 'getProductsByCategory');
        Route::get('products/latest-arrivals', 'getLatestProducts');
        Route::get('products/hot-selling', 'getBestSellingProducts');

        Route::middleware('auth:api')->group(function () {
            Route::get('products/you-may-like', 'getProductsUserMayLike');
            Route::get('products/{product}/show-deleted', 'showDeleted');
            Route::delete('products/{product}/force-deleted', 'forceDeleted');
            Route::post('products/{product}/restore-deleted', 'restoreDeleted');
            Route::get('products/{name}/largest-quantity-sold', 'showLargestQuantitySold');
            Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        });
    });


    // -------------------------------------- Report Routes -------------------------------------- //
    Route::controller(ReportController::class)->middleware('auth:api')->group(function () {
        Route::get('reports/best-categories',  'bestCategoriesReport');
        Route::get('reports/best-selling-products', 'bestSellingProductsReport');
        Route::get('reports/products-low-on-stocks',  'productsLowOnStockReport');
        Route::get('reports/orders-late-to-deliver', 'ordersLateToDeliverReport');
        Route::post('reports/products-never-been-sold',  'productsNeverBeenSoldReport');
        Route::get('reports/products-remaining-in-carts', 'productsRemainingInCartsReport');
        Route::get('reports/countries-with-highest-orders',  'countriesWithHighestOrdersReport');
    });
});

