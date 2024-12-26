<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Favorite\FavoriteController;
use App\Http\Controllers\User\PasswordResetController;
use App\Http\Controllers\Category\SubCategoryController;

use App\Http\Controllers\Category\MainCategoryController;
use App\Http\Controllers\Permission\PermissionController;


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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
});
//Oauth
Route::get('/auth/{provider}', [AuthController::class,'redirectToProvider']);
Route::get('/auth/{provider}/callback', [AuthController::class,'handleProviderCallback']);

//reset password
Route::post('/password/forgot', [PasswordResetController::class, 'sendResetLink']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
});

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
    Route::get('category/{categoryID}', [ProductController::class, 'getProductsByCategory']); // List products by category
    Route::get('you-may-like', [ProductController::class, 'getProductsUserMayLike']); // List products user may like
    Route::get('trashed', [ProductController::class, 'showDeleted']); // List trashed products
    Route::post('{id}/restore', [ProductController::class, 'restoreDeleted']); // Restore a trashed product
    Route::delete('{id}/force-delete', [ProductController::class, 'forceDeleted']); // Force delete a product
});
Route::apiResource('products', ProductController::class); // CRUD operations


Route::get('category/{categoryID}/products', [ProductController::class, 'getProductsByCategory']);


Route::apiResource('roles', RoleController::class); // CRUD Roles

Route::apiResource('permissions', PermissionController::class); // CRUD Permissions




//Main Category--------------------------------------------------------------------------------------------------------------------
Route::apiResource('maincategory',MainCategoryController::class);
Route::get('showDeleted_MainCategory', [MainCategoryController::class, 'showDeleted']);
Route::get('restoreDeleted_MainCategory/{main_category_id}', [MainCategoryController::class, 'restoreDeleted']);
Route::delete('forceDeleted_MainCategory/{main_category_id}', [MainCategoryController::class, 'forceDeleted']);

//Sub Category--------------------------------------------------------------------------------------------------------------------
Route::apiResource('subcategory',SubCategoryController::class);
Route::get('showDeleted_SubCategory', [SubCategoryController::class, 'showDeleted']);
Route::get('restoreDeleted_SubCategory/{sub_category_id}', [SubCategoryController::class, 'restoreDeleted']);
Route::delete('forceDeleted_SubCategory/{sub_category_id}', [SubCategoryController::class, 'forceDeleted']);

//favorites
Route::post('products/{product}/addToFavorite', [FavoriteController::class, 'store']);

Route::delete('products/{product}/removeFromFavorite', [FavoriteController::class, 'destroy']);
