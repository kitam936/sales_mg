<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnalysisController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\SalesProductController;
use App\Http\Controllers\Api\SalesDigestController;

// Route::middleware('web', 'auth')->get('/users', [UserApiController::class, 'index']);

// Route::middleware('web', 'auth')->get('/items', [ItemApiController::class, 'index']);

// Route::middleware('web', 'auth')->group(function () {
//     Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/analysis', [AnalysisController::class, 'index'])->name('api.analysis');
    Route::get('/shops', [ShopController::class, 'index'])->name('api.shops');
    // 商品別売上データ（ページネーション付き）
    Route::get('/sales-products', [SalesProductController::class, 'index']);
    // 会社 → 店舗リレーション
    Route::get('/companies/{companyId}/shops', [SalesProductController::class, 'shops']);
    // シーズン → Unit リレーション
    Route::get('/seasons/{seasonId}/units', [SalesProductController::class, 'units']);
    // 消化率
    Route::get('/sales-digest', [SalesDigestController::class, 'index']);

});



