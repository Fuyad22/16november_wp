<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\EmailVerificationController;

// Post Routes
Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::post('/', [PostController::class, 'store']);
    Route::get('/{id}', [PostController::class, 'show']);
    Route::put('/{id}', [PostController::class, 'update']);
    Route::delete('/{id}', [PostController::class, 'destroy']);
});

// Email Verification Routes
Route::post('/send-verification-code', [EmailVerificationController::class, 'sendVerificationCode']);
Route::post('/verify-email', [EmailVerificationController::class, 'verifyEmail']);
