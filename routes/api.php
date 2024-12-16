<?php


use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\Role\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Product\ProductController;


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


Route::get('users/showDeleted', [UserController::class, 'showDeleted']);
Route::apiResource('users', UserController::class);
Route::post('users/{user}/restoreDeleted', [UserController::class, 'restoreDeleted']);
Route::delete('users/{user}/forceDeleted', [UserController::class, 'forceDeleted']);


// Product Routes
Route::prefix('products')->group(function () {
    Route::get('latest-arrivals', [ProductController::class, 'getLatestProducts']);                       // list latest products added
    Route::get('hotSelling', [ProductController::class, 'getBestSellingProducts']);                       // list best selling products
    Route::get('category/{categoryID}', [ProductController::class, 'getProductsByCategory']);             // list products by category
    Route::get('you-may-like', [ProductController::class, 'getProductsUserMayLike']);                    // List products usr may like
    Route::get('trashed', [ProductController::class, 'showDeleted']);                                     // List trashed products
    Route::post('{id}/restore', [ProductController::class, 'restoreDeleted']);                            // Restore a trashed product
    Route::delete('{id}/force-delete', [ProductController::class, 'forceDeleted']);                       // Force delete a product

});
Route::apiResource('products', ProductController::class); // CRUD operations


Route::get('category/{categoryID}/products', [ProductController::class, 'getProductsByCategory']);


Route::apiResource('roles', RoleController::class); // CRUD Roles

Route::apiResource('permissions', PermissionController::class); // CRUD Permissions
