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
        Route::get('users/showDeleted', 'showDeleted');
        Route::delete('users/{user}/forceDeleted', 'forceDeleted');
        Route::post('users/{user}/restoreDeleted', 'restoreDeleted');
        Route::apiResource('users', UserController::class);
    });


    // ---------------------------------- Roles & Permissuin Routes ---------------------------------- //
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);


    // ------------------------------------ Main Category Routes ------------------------------------ //
    Route::controller(MainCategoryController::class)->middleware('auth:api')->group(function () {
        Route::get('mainCategories/{mainCategory}/showDeleted', 'showDeleted');
        Route::delete('mainCategories/{mainCategory}/forceDeleted', 'forceDeleted');
        Route::post('mainCategories/{mainCategory}/restoreDeleted', 'restoreDeleted');
        Route::apiResource('mainCategories', MainCategoryController::class)->except(['index', 'show']);
    });
    Route::apiResource('mainCategories', MainCategoryController::class)->only(['index', 'show']);


    // ------------------------------------ Sub Category Routes ------------------------------------ //
    Route::controller(SubCategoryController::class)->middleware('auth:api')->group(function () {
        Route::get('subCategories/{subCategory}/showDeleted', 'showDeleted');
        Route::delete('subCategories/{subCategory}/forceDeleted', 'forceDeleted');
        Route::post('subCategories/{subCategory}/restoreDeleted', 'restoreDeleted');
        Route::apiResource('subCategories', SubCategoryController::class)->except(['index', 'show']);
    });
    Route::apiResource('subCategories', SubCategoryController::class)->only(['index', 'show']);


    // -------------------------------------- Favorite Routes -------------------------------------- //
    Route::controller(FavoriteController::class)->middleware('auth:api')->group(function () {
        Route::get('users/myFavoriteProducts', 'show');
        Route::post('products/{product}/addToFavorite', 'store');
        Route::delete('products/{product}/removeFromFavorite', 'destroy');
    });


    // --------------------------------------- Rate Routes --------------------------------------- //
    Route::controller(RateController::class)->middleware('auth:api')->group(function () {
        Route::get('rates/{rate}/showDeleted', 'showDeleted');
        Route::delete('rates/{rate}/forceDeleted', 'forceDeleted');
        Route::post('rates/{rate}/restoreDeleted', 'restoreDeleted');
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
        Route::post('cartItems', 'store');
        Route::put('cartItems/{cartItem}', 'update');
        Route::delete('cartItems/{cartItem}', 'destroy');
    });

    #FIXME Re-check showDeleted-user
    // ------------------------------------- Order Routes ------------------------------------- //
    Route::controller(OrderController::class)->middleware('auth:api')->group(function () {
        Route::get('orders', 'indexAdmin');
        Route::get('orders/user-orders', 'indexUser');
        Route::get('orders/{order}/tracking', 'orderTracking');
        Route::delete('orders/{order}/forceDeleted', 'forceDeleted');
        Route::post('orders/{order}/restoreDeleted', 'restoreDeleted');
        Route::get('orders/{order}/showDeleted-user', 'showDeletedUser');
        Route::get('orders/{order}/showDeleted-admin', 'showDeletedAdmin');
        Route::apiResource('orders', OrderController::class)->except(['index', 'store']);
    });


    #FIXME Re-check authenticated operations
    // ------------------------------------- Photo Routes ------------------------------------- //
    Route::controller(PhotoController::class)->middleware('auth:api')->group(function () {
        Route::delete('photos/{photo}', 'destroy');
        Route::post('users/{user}/photos', 'storePhoto');
        Route::post('products/{product}/photos', 'storePhoto');
        Route::post('subcategory/{subCategory}/photos', 'storePhoto');
        Route::post('maincategory/{mainCategory}/photos', 'storePhoto');
    });


    // ------------------------------------- Product Routes ------------------------------------- //
    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'index');
        Route::get('products/{product}', 'show');
        Route::get('products/top-rated', 'topRatedProducts');
        Route::get('products/filter', 'getProductsWithFilter');
        Route::get('products/category', 'getProductsByCategory');
        Route::get('products/latest-arrivals', 'getLatestProducts');
        Route::get('products/hotSelling', 'getBestSellingProducts');

        Route::middleware('auth:api')->group(function () {
            Route::get('products/you-may-like', 'getProductsUserMayLike');
            Route::get('products/{product}/showDeleted', 'showDeleted');
            Route::delete('products/{product}/forceDeleted', 'forceDeleted');
            Route::post('products/{product}/restoreDeleted', 'restoreDeleted');
            Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        });
    });


    // -------------------------------------- Report Routes -------------------------------------- //
    Route::controller(ReportController::class)->middleware('auth:api')->group(function () {
        Route::get('admin/products-remaining-report', 'repor2');
    });
});
