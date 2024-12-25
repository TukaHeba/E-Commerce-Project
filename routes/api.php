<?php

use App\Http\Controllers\Favorite\FavoriteController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Http\Request;
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


// Route::get('users/showDeleted', [UserController::class, 'showDeleted']);
// Route::apiResource('users',UserController::class);
// Route::post('users/{user}/restoreDeleted', [UserController::class, 'restoreDeleted']);
// Route::delete('users/{user}/forceDeleted', [UserController::class, 'forceDeleted']);

// User Routes
Route::prefix('users')->group(function () {


    Route::get('trashed', [UserController::class, 'showDeleted']); // List trashed users
    Route::post('{id}/restore', [UserController::class, 'restoreDeleted']); // Restore a trashed user
    Route::delete('{id}/force-delete', [UserController::class, 'forceDeleted']); // Force delete a user

});
Route::apiResource('users', UserController::class); // CRUD operations

// Product Routes
Route::prefix('products')->group(function () {


    Route::get('trashed', [ProductController::class, 'showDeleted']); // List trashed products
    Route::post('{id}/restore', [ProductController::class, 'restoreDeleted']); // Restore a trashed product
    Route::delete('{id}/force-delete', [ProductController::class, 'forceDeleted']); // Force delete a product

});
Route::apiResource('products', ProductController::class); // CRUD operations

// Favorite Routes

Route::prefix('favorites')->group(function () {
    Route::get('/', [FavoriteController::class, 'index']);
    Route::post('/', [FavoriteController::class, 'store']);
    Route::get('/users/{user}', [FavoriteController::class, 'show']);
    Route::get('/products/{product}', [FavoriteController::class, 'usersFavoringProduct']);
    Route::delete('/', [FavoriteController::class, 'destroy']);
});
