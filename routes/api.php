<?php

use App\Http\Controllers\User\UserController;
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



