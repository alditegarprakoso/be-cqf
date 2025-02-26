<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonationCategoryController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    // User Routes
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::post('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Donation Categories Routes
    Route::get('/donation-categories', [DonationCategoryController::class, 'index']);
    Route::get('/donation-categories/{id}', [DonationCategoryController::class, 'show']);
    Route::post('/donation-categories', [DonationCategoryController::class, 'store']);
    Route::put('/donation-categories/{id}', [DonationCategoryController::class, 'update']);
    Route::delete('/donation-categories/{id}', [DonationCategoryController::class, 'destroy']);

    // Donation Routes
    Route::get('/donations', [DonationController::class, 'index']);
    Route::get('/donations/{id}', [DonationController::class, 'show']);
    Route::post('/donations', [DonationController::class, 'store']);
    Route::post('/donations/{id}', [DonationController::class, 'update']);
    Route::delete('/donations/{id}', [DonationController::class, 'destroy']);
});
