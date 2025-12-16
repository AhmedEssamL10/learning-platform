<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Middleware\AdminMiddleware;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/plans', [SubscriptionPlanController::class, 'index']);
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/deactivate-subscription/{id}', [AdminController::class, 'deactivate']);
    });
});

Route::post("/payment-callback", [SubscriptionController::class, 'paymentCallback']);