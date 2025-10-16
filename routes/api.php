<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\ItemApiController;
use App\Http\Controllers\Api\AnalysisController;

// Route::middleware('web', 'auth')->get('/users', [UserApiController::class, 'index']);

// Route::middleware('web', 'auth')->get('/items', [ItemApiController::class, 'index']);

Route::middleware('web', 'auth')->group(function () {
    Route::get('/users', [UserApiController::class, 'index'])->name('api.users.index');
    // Route::get('/items', [ItemApiController::class, 'index'])->name('api.items.index');
    Route::get('/analysis', [AnalysisController::class, 'index'])->name('api.analysis');
});

