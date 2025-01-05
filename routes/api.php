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
        Route::get('reports/lating-orders-report', 'repor1');
        Route::get('reports/top-countries',  'topCountries');
        Route::get('reports/best-categories-report',  'BestCategories');
        Route::get('reports/products-remaining', 'productsRemainingReport');
        Route::get('reports/products-low-on-stocks',  'ProductsLowOnStockReport');
        Route::post('reports/send-unsold-products-email',  'sendUnsoldProductsEmail');
    });
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//Oauth
Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);

    //Cart & Cart item-----------------------------------------------------------
    Route::apiResource('/cart-items', CartItemController::class)->except(['index', 'show']);
    Route::apiResource('/carts', CartController::class)->only(['index', 'show']);
    Route::get('/user-cart', [CartController::class, 'userCart']);

});

//Oauth
Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);

//reset password
Route::post('/password/forgot', [PasswordResetController::class, 'sendResetLink']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);


Route::get('users/myFavoriteProducts', [FavoriteController::class, 'show']);
Route::get('users/showDeleted', [UserController::class, 'showDeleted']);
Route::apiResource('users', UserController::class);
Route::post('users/{user}/restoreDeleted', [UserController::class, 'restoreDeleted']);
Route::delete('users/{user}/forceDeleted', [UserController::class, 'forceDeleted']);

// Product Routes
Route::prefix('products')->group(function () {
    Route::get('latest-arrivals', [ProductController::class, 'getLatestProducts']); // List latest products added
    Route::get('filter', [ProductController::class, 'getProductsWithFilter']); // List products with filters (price, name, category_id, latest)
    Route::get('hotSelling', [ProductController::class, 'getBestSellingProducts']); // List best-selling products
    Route::get('category', [ProductController::class, 'getProductsByCategory']); // List products by category
    Route::middleware('auth:api')->get('you-may-like', [ProductController::class, 'getProductsUserMayLike']); // List products user may like
    Route::get('trashed', [ProductController::class, 'showDeleted']); // List trashed products
    Route::post('{id}/restore', [ProductController::class, 'restoreDeleted']); // Restore a trashed product
    Route::delete('{id}/force-delete', [ProductController::class, 'forceDeleted']); // Force delete a product
    Route::get('top-rated', [ProductController::class, 'topRatedProducts']); // Top rated products
});
Route::apiResource('products', ProductController::class); // CRUD operations

Route::apiResource('roles', RoleController::class); // CRUD Roles

Route::apiResource('permissions', PermissionController::class); // CRUD Permissions

//Main Category--------------------------------------------------------------------------------------------------------------------

Route::apiResource('maincategory', MainCategoryController::class);


Route::get('showDeleted_MainCategory', [MainCategoryController::class, 'showDeleted']);
Route::get('restoreDeleted_MainCategory/{main_category_id}', [MainCategoryController::class, 'restoreDeleted']);
Route::delete('forceDeleted_MainCategory/{main_category_id}', [MainCategoryController::class, 'forceDeleted']);

//Sub Category--------------------------------------------------------------------------------------------------------------------
Route::apiResource('subcategory', SubCategoryController::class);


Route::get('showDeleted_SubCategory', [SubCategoryController::class, 'showDeleted']);
Route::get('restoreDeleted_SubCategory/{sub_category_id}', [SubCategoryController::class, 'restoreDeleted']);
Route::delete('forceDeleted_SubCategory/{sub_category_id}', [SubCategoryController::class, 'forceDeleted']);

// Order
Route::middleware('auth')->controller(OrderController::class)->group(function () {
    Route::get('user-orders', 'indexUser');
    Route::get('admin-orders', 'indexAdmin');
    Route::get('user-orders/show-deleted', 'showDeletedUser');
    Route::get('admin-orders/show-deleted', 'showDeletedAdmin');
    Route::post('orders/{id}/restore-deleted', 'restoreDeleted');
    Route::delete('orders/{id}/force-deleted', 'forceDeleted');
});
Route::apiResource('orders', OrderController::class)->except(['index', 'store'])->middleware('auth');


//photo --------------------------------------------------------------------------
Route::post('users/{user}/photos', [PhotoController::class, 'storePhoto']);
Route::post('products/{product}/photos', [PhotoController::class, 'storePhoto']);
Route::post('maincategory/{mainCategory}/photos', [PhotoController::class, 'storePhoto']);
Route::post('subcategory/{subCategory}/photos', [PhotoController::class, 'storePhoto']);

Route::delete('photos/{photo}', [PhotoController::class, 'destroy']);

//Rate

Route::middleware('auth:api')->prefix('rate')->group(function () {
    Route::put('restore/{rate}', [RateController::class, 'restoreDeleted']);
    Route::get('deleted-rates', [RateController::class, 'showDeleted']);
    Route::delete('force-deleted-rate/{rate}', [RateController::class, 'forceDeleted']);
    Route::put('{rate}', [RateController::class, 'update']);
    Route::delete('{rate}', [RateController::class, 'destroy']);
    Route::post('', [RateController::class, 'store']);
});
Route::apiResource('rate', RateController::class)->only(['index', 'show']);

//favorites
Route::post('products/{product}/addToFavorite', [FavoriteController::class, 'store']);
Route::delete('products/{product}/removeFromFavorite', [FavoriteController::class, 'destroy']);
Route::get('users/myFavoriteProducts', [FavoriteController::class, 'show']);

// Checkout Routes
Route::middleware(['auth:api'])->group(function () {
    Route::get('/cart/checkout', [CartController::class, 'checkout']);
    Route::post('/cart/place-order', [CartController::class, 'placeOrder']);
});

Route::get('reports/bestSellingProductsReport',[ReportController::class,'generateBestSellingProductsReport']);
