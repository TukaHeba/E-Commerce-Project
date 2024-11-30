<?php


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


Route::get('users/showDeleted', [UserController::class, 'showDeleted']);
Route::apiResource('users',UserController::class);
Route::post('users/{user}/restoreDeleted', [UserController::class, 'restoreDeleted']);
Route::delete('users/{user}/forceDeleted', [UserController::class, 'forceDeleted']);


// Product Routes
Route::prefix('products')->group(function () {


    Route::get('trashed', [ProductController::class, 'showDeleted']); // List trashed products
    Route::post('{id}/restore', [ProductController::class, 'restoreDeleted']); // Restore a trashed product
    Route::delete('{id}/force-delete', [ProductController::class, 'forceDeleted']); // Force delete a product

});
Route::apiResource('products', ProductController::class); // CRUD operations


