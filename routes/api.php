<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Public campaign routes
Route::get('/campaigns', [CampaignController::class, 'index']);
Route::get('/campaigns/{campaign}', [CampaignController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    
    // Campaign routes
    Route::post('/campaigns', [CampaignController::class, 'store']);
    Route::put('/campaigns/{campaign}', [CampaignController::class, 'update']);
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy']);
    
    // Donation routes
    Route::post('/campaigns/{campaign}/donate', [DonationController::class, 'store']);
    Route::get('/donations/user', [DonationController::class, 'userDonations']);
    
    // Stats routes
    Route::get('/stats/dashboard', [StatsController::class, 'dashboard']);
    Route::get('/stats/user-contributions', [StatsController::class, 'userContributions']);
    
    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/stats', [StatsController::class, 'adminStats']);
    });
});