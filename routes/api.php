<?php

use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\CartItem\CartItemController;
use App\Http\Controllers\Category\MainCategoryController;
use App\Http\Controllers\Category\SubCategoryController;
use App\Http\Controllers\Export\ExportController;
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
Route::middleware('throttle:auth')->group(function () {

    // ----------------------------------- Authentication Routes ----------------------------------- //
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('logout', 'logout')->middleware('auth:api');
        Route::post('refresh-token', 'refresh')->middleware('auth:api');

        // -------------- OAuth Routes (these links need to be tested in the browser) -------------- //
        Route::get('auth/{provider}', 'redirectToProvider');
        Route::get('auth/{provider}/callback', 'handleProviderCallback');
    });


    // ----------------------------------- Reset Password Routes ----------------------------------- //
    Route::controller(PasswordResetController::class)->group(function () {
        Route::post('password/send-email', 'sendResetLink');
        Route::post('password/reset', 'resetPassword');
    });
});

// 2- Apply throttling (60 requests per minute) for general API routes.
Route::middleware('throttle:api')->group(function () {

    // ----------------------------------------- User Routes ----------------------------------------- //
    Route::controller(UserController::class)->middleware('auth:api')->group(function () {
        Route::get('users/show-deleted', 'showDeleted');
        Route::delete('users/{userId}/force-deleted', 'forceDeleted');
        Route::post('users/{userId}/restore-deleted', 'restoreDeleted');
        Route::get('users/{user}/average-purchases', 'getAveragePurchases')->middleware('role:admin');
        Route::apiResource('users', UserController::class);
    });


    // ---------------------------------- Roles & Permission Routes ---------------------------------- //
    Route::middleware(['throttle:60,1', 'auth:api', 'role:admin'])->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);

        Route::post('users/{user}/assign-role/{role}', [UserController::class, 'assignRole']);
        Route::delete('users/{user}/remove-role/{role}', [UserController::class, 'removeRole']);

        Route::post('roles/{role}/give-permission', [RoleController::class, 'givePermission']);
        Route::delete('roles/{role}/revoke-permission/{permission}', [RoleController::class, 'revokePermission']);
    });


    // ------------------------------------ Main Category Routes ------------------------------------ //
    Route::controller(MainCategoryController::class)->middleware('auth:api')->group(function () {
        Route::get('main-categories/show-deleted', 'showDeleted');
        Route::delete('main-categories/{mainCategoryId}/force-deleted', 'forceDeleted');
        Route::get('main-categories/{mainCategoryId}/restore-deleted', 'restoreDeleted');
        Route::apiResource('main-categories', MainCategoryController::class)->except(['index', 'show']);
    });
    Route::apiResource('main-categories', MainCategoryController::class)->only(['index', 'show']);


    // ------------------------------------ Sub Category Routes ------------------------------------ //
    Route::controller(SubCategoryController::class)->middleware('auth:api')->group(function () {
        Route::get('sub-categories/show-deleted', 'showDeleted');
        Route::delete('sub-categories/{subCategoryId}/force-deleted', 'forceDeleted');
        Route::get('sub-categories/{subCategoryId}/restore-deleted', 'restoreDeleted');
        Route::apiResource('sub-categories', SubCategoryController::class)->except(['index', 'show']);
    });
    Route::apiResource('sub-categories', SubCategoryController::class)->only(['index', 'show']);


    // -------------------------------------- Favorite Routes -------------------------------------- //
    Route::controller(FavoriteController::class)->middleware('auth:api')->group(function () {
        Route::get('my-favorite-products', 'show');
        Route::post('products/{product}/add-to-favorite', 'store');
        Route::delete('remove-from-favorite/product/{product}', 'destroy');
    });


    // --------------------------------------- Rate Routes --------------------------------------- //
    Route::apiResource('rates', RateController::class)->except(['index', 'show'])->middleware('auth:api');
    Route::apiResource('rates', RateController::class)->only(['index', 'show']);


    // ------------------------------------- Cart Routes ------------------------------------- //
    Route::controller(CartController::class)->middleware('auth:api')->group(function () {
        Route::get('carts/user-cart', 'userCart');
        Route::post('carts/place-order', 'placeOrder');
        Route::apiResource('carts', CartController::class)->only(['index', 'show']);
    });


    // ---------------------------------- Cart Items Routes ---------------------------------- //
    Route::apiResource('/cart-items', CartItemController::class)
        ->only(['store', 'update', 'destroy'])->middleware(['auth:api', 'role:customer']);


    // ------------------------------------- Order Routes ------------------------------------- //
    Route::controller(OrderController::class)->middleware('auth:api')->group(function () {
        Route::get('orders', 'indexAdmin');
        Route::get('orders/user-orders', 'indexUser');
        Route::get('orders/show-deleted', 'showDeleted');
        Route::get('orders/{order}/tracking', 'orderTracking');
        Route::delete('orders/{orderId}/force-deleted', 'forceDeleted');
        Route::post('orders/{orderId}/restore-deleted', 'restoreDeleted');
        Route::get('orders/{orderId}/show-deleted-admin', 'getDeletedOrdersAdmin');
        Route::apiResource('orders', OrderController::class)->except(['index', 'store']);
    });

    // ------------------------------------- Product Routes ------------------------------------- //
    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'index');
        Route::get('products/{product}', 'show');
        Route::get('products/top-rated', 'topRatedProducts');
        Route::get('products/category', 'getProductsByCategory');
        Route::get('products/latest-arrivals', 'getLatestProducts');
        Route::get('products/season-products', 'getSeasonProducts');
        Route::get('products/hot-selling', 'getBestSellingProducts');

        Route::middleware('auth:api')->group(function () {
            Route::get('products/you-may-like', 'getProductsUserMayLike');
            Route::get('products/show-deleted', 'showDeleted');
            Route::delete('products/{productId}/force-deleted', 'forceDeleted');
            Route::post('products/{productId}/restore-deleted', 'restoreDeleted');
            Route::get('products/{name}/largest-quantity-sold', 'showLargestQuantitySold');
            Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        });
    });


    // -------------------------------------- Report Routes -------------------------------------- //
    Route::controller(ReportController::class)->prefix('reports')->middleware('auth:api')->group(function () {
        Route::get('best-categories', 'bestCategoriesReport')->middleware('permission:BestSellingCategories');
        Route::get('best-selling-products', 'bestSellingProductsReport')->middleware('permission:BestSellingProducts');
        Route::get('products-low-on-stocks', 'productsLowOnStockReport')->middleware('permission:ProductsLowOnStock');
        Route::get('orders-late-to-deliver', 'ordersLateToDeliverReport')->middleware('permission:OrdersLateToDeliver');
        Route::get('products-never-been-sold', 'productsNeverBeenSoldReport')->middleware('permission:ProductsNeverBeenSold');
        Route::get('products-remaining-in-carts', 'productsRemainingInCartsReport')->middleware('permission:ProductsRemainingInCarts');
        Route::get('countries-with-highest-orders/{country?}', 'countriesWithHighestOrdersReport')->middleware('permission:CountriesWithHighestOrders');
    });


    // -------------------------------------- Export Routes -------------------------------------- //
    Route::controller(ExportController::class)->prefix('export')->middleware('auth:api')->group(function () {
        Route::get('best-categories', 'bestCategoriesExport')->middleware('permission:BestSellingCategories');
        Route::get('best-selling-products', 'bestSellingProductsExport')->middleware('permission:BestSellingProducts');
        Route::get('products-low-on-stocks', 'productsLowOnStockExport')->middleware('permission:ProductsLowOnStock');
        Route::get('orders-late-to-deliver', 'ordersLateToDeliverExport')->middleware('permission:OrdersLateToDeliver');
        Route::get('products-never-been-sold', 'productsNeverBeenSoldExport')->middleware('permission:ProductsNeverBeenSold');
        Route::get('products-remaining-in-carts', 'productsRemainingInCartsExport')->middleware('permission:ProductsRemainingInCarts');
        Route::get('countries-with-highest-orders/{country?}', 'countriesWithHighestOrdersExport')->middleware('permission:CountriesWithHighestOrders');
    });
});
