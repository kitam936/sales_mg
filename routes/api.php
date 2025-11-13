<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnalysisController;
use App\Http\Controllers\Api\ShopController;

// Route::middleware('web', 'auth')->get('/users', [UserApiController::class, 'index']);

// Route::middleware('web', 'auth')->get('/items', [ItemApiController::class, 'index']);

// Route::middleware('web', 'auth')->group(function () {
//     Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/analysis', [AnalysisController::class, 'index'])->name('api.analysis');
    Route::get('/shops', [ShopController::class, 'index'])->name('api.shops');

});



