<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ColController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\DeptController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HinbanController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SkuController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\DataDownloadController;
use App\Http\Controllers\ImageController;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('menu', [MenuController::class, 'menu'])->name('menu');
    // Data管理
    Route::get('/data/data_menu', [DataController::class, 'menu'])->name('admin.data.data_menu');
    Route::get('/data', [DataController::class, 'create'])->name('admin.data.create');
    Route::get('data/data_index', [DataController::class, 'index'])->name('admin.data.data_index');
    Route::get('data/brand_index', [DataController::class, 'brand_index'])->name('admin.data.brand_index');
    Route::get('data/unit_index', [DataController::class, 'unit_index'])->name('admin.data.unit_index');
    Route::get('data/hinban_index', [DataController::class, 'hinban_index'])->name('admin.data.hinban_index');
    Route::get('data/sku_index', [DataController::class, 'sku_index'])->name('admin.data.sku_index');
    Route::get('data/col_index', [DataController::class, 'col_index'])->name('admin.data.col_index');
    Route::get('data/size_index', [DataController::class, 'size_index'])->name('admin.data.size_index');
    Route::get('data/area_index', [DataController::class, 'area_index'])->name('admin.data.area_index');
    Route::get('data/company_index', [DataController::class, 'company_index'])->name('admin.data.company_index');
    Route::get('data/shop_index', [DataController::class, 'shop_index'])->name('admin.data.shop_index');
    Route::get('data/sales_index', [DataController::class, 'sales_index'])->name('admin.data.sales_index');
    Route::get('data/deliv_index', [DataController::class, 'delivery_index'])->name('admin.data.deliv_index');
    Route::get('data/stock_index', [DataController::class, 'stock_index'])->name('admin.data.stock_index');
    Route::get('data/yosan_index', [DataController::class, 'yosan_index'])->name('admin.data.yosan_index');
    Route::get('data/ym_index', [DataController::class, 'ym_index'])->name('admin.data.ym_index');
    Route::get('data/yw_index', [DataController::class, 'yw_index'])->name('admin.data.yw_index');
    Route::get('data/ymd_index', [DataController::class, 'ymd_index'])->name('admin.data.ymd_index');
    Route::get('data/y_index', [DataController::class, 'y_index'])->name('admin.data.y_index');
    Route::POST('data/stock_upload', [DataController::class, 'stock_upload'])->name('admin.data.stock_upload');
    Route::POST('data/yosan_upload', [DataController::class, 'yosan_upload'])->name('admin.data.yosan_upload');
    Route::POST('data/shop_upsert', [DataController::class, 'shop_upsert'])->name('admin.data.shop_upsert');
    Route::POST('data/sales_upload', [DataController::class, 'sales_upload'])->name('admin.data.sales_upload');
    Route::POST('data/deliv_upload', [DataController::class, 'delivery_upload'])->name('admin.data.deliv_upload');
    Route::POST('data/hinban_upsert', [DataController::class, 'hinban_upsert'])->name('admin.data.hinban_upsert');
    Route::POST('data/sku_upsert', [DataController::class, 'sku_upsert'])->name('admin.data.sku_upsert');
    Route::POST('data/ym_upsert', [DataController::class, 'ym_upsert'])->name('admin.data.ym_upsert');
    Route::POST('data/yw_upsert', [DataController::class, 'yw_upsert'])->name('admin.data.yw_upsert');
    Route::POST('data/ymd_upsert', [DataController::class, 'ymd_upsert'])->name('admin.data.ymd_upsert');
    Route::POST('data/y_upsert', [DataController::class, 'y_upsert'])->name('admin.data.y_upsert');
    Route::POST('data/col_upsert', [DataController::class, 'col_upsert'])->name('admin.data.col_upsert');
    Route::POST('data/size_upsert', [DataController::class, 'size_upsert'])->name('admin.data.size_upsert');
    Route::POST('data/company_upsert', [DataController::class, 'company_upsert'])->name('admin.data.company_upsert');
    Route::POST('data/area_upsert', [DataController::class, 'area_upsert'])->name('admin.data.area_upsert');
    Route::POST('data/unit_upsert', [DataController::class, 'unit_upsert'])->name('admin.data.unit_upsert');
    Route::POST('data/brand_upsert', [DataController::class, 'brand_upsert'])->name('admin.data.brand_upsert');
    Route::get('data/shop_edit/{shop}', [DataController::class, 'shop_edit'])->name('admin.data.shop_edit');
    Route::get('report_update/{shop}', [DataController::class, 'shop_update'])->name('admin.data.shop_update');
    Route::post('report_update/{shop}', [DataController::class, 'shop_update'])->name('admin.data.shop_update');
    Route::delete('report_destroy/{shop}', [DataController::class, 'shop_destroy'])->name('admin.data.shop_destroy');
    Route::get('data/delete_index', [DataController::class, 'delete_index'])->name('admin.data.delete_index');
    Route::delete('sales_destroy', [DataController::class, 'sales_destroy'])->name('admin.data.sales_destroy');
    Route::delete('deliv_destroy', [DataController::class, 'deliv_destroy'])->name('admin.data.deliv_destroy');
    Route::delete('stock_destroy', [DataController::class, 'stock_destroy'])->name('admin.data.stock_destroy');
    Route::delete('yosan_destroy', [DataController::class, 'yosan_destroy'])->name('admin.data.yosan_destroy');
    Route::delete('sku_destroy', [DataController::class, 'sku_destroy'])->name('admin.data.sku_destroy');
    Route::delete('hinban_destroy', [DataController::class, 'hinban_destroy'])->name('admin.data.hinban_destroy');
    Route::delete('shop_destroy', [DataController::class, 'shop_destroy'])->name('admin.data.shop_destroy');
    Route::delete('shop_destroy_all', [DataController::class, 'shop_destroy_all'])->name('admin.data.shop_destroy_all');
    Route::delete('company_destroy', [DataController::class, 'company_destroy'])->name('admin.data.company_destroy');
    Route::delete('area_destroy', [DataController::class, 'area_destroy'])->name('admin.data.area_destroy');
    Route::delete('unit_destroy', [DataController::class, 'unit_destroy'])->name('admin.data.unit_destroy');
    Route::delete('brand_destroy', [DataController::class, 'brand_destroy'])->name('admin.data.brand_destroy');
    Route::delete('col_destroy', [DataController::class, 'col_destroy'])->name('admin.data.col_destroy');
    Route::delete('size_destroy', [DataController::class, 'size_destroy'])->name('admin.data.size_destroy');
    Route::get('user_create', [UserController::class, 'create'])->name('admin.user_create');
    Route::POST('user_store', [UserController::class, 'store'])->name('admin.user_store');
    Route::get('user_edit/{user}', [UserController::class, 'edit'])->name('admin.user_edit');
    Route::delete('user_destroy/{user}', [UserController::class, 'user_destroy'])->name('admin.user_destroy');
    Route::get('image_create', [ImageController::class, 'image_create'])->name('admin.image_create');
    Route::get('image_edit/{hinban}', [ImageController::class, 'image_edit'])->name('admin.image_edit');
    Route::POST('image_store', [ImageController::class, 'store'])->name('admin.image_store');
    Route::delete('image_destroy/{hinban}', [ImageController::class, 'image_destroy'])->name('admin.image_destroy');
    Route::get('sku_image_edit/{sku}', [ImageController::class, 'sku_image_edit'])->name('admin.sku_image_edit');
    Route::POST('sku_image_store', [ImageController::class, 'sku_store'])->name('admin.sku_image_store');
    Route::delete('sku_image_destroy/{sku}', [ImageController::class, 'sku_image_destroy'])->name('admin.sku_image_destroy');
    Route::get('hinban_image_check', [ImageController::class, 'hinban_image_check'])->name('admin.hinban_image_check');
    Route::get('sku_image_check', [ImageController::class, 'sku_image_check'])->name('admin.sku_image_check');
    Route::get('hinban_image_csv', [DataDownloadController::class, 'HinbanImageCheck_CSV_download'])->name('admin.hinban_image_csv');
    Route::get('sku_image_csv', [DataDownloadController::class, 'SkuImageCheck_CSV_download'])->name('admin.sku_image_csv');
    Route::get('image_show/{hinban}', [ImageController::class, 'image_show'])->name('image_show');
    Route::get('sku_image_show/{sku}', [ImageController::class, 'sku_image_show'])->name('sku_image_show');
    // Report管理
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports/store', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/show/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/edit/{report}', [ReportController::class, 'edit'])->name('reports.edit');
    Route::get('/comments/create/{id}', [CommentController::class, 'comment_create'])->name('comments.create');
    Route::delete('del_image1', [ReportController::class, 'del_image1'])->name('reports.del_image1');
    Route::delete('del_image2', [ReportController::class, 'del_image2'])->name('reports.del_image2');
    Route::delete('del_image3', [ReportController::class, 'del_image3'])->name('reports.del_image3');
    Route::delete('del_image4', [ReportController::class, 'del_image4'])->name('reports.del_image4');
    // csv管理
    Route::get('/csv-import', [CsvImportController::class, 'index'])->name('csv.import.index');
    Route::post('/csv-import', [CsvImportController::class, 'store'])->name('csv.import.store');
    Route::get('/csv-progress', [CsvImportController::class, 'progress'])->name('csv.import.progress');
    // Menu, Analysis
    Route::get('analysis', [AnalysisController::class, 'index'])->name('analysis');
    Route::get('analysis/test', [AnalysisController::class, 'test'])->name('analysis.test');
    Route::get('menu', [MenuController::class, 'menu'])->name('menu');
});

Route::resource('roles', RoleController::class) ->middleware(['auth', 'verified']);

Route::resource('shops', ShopController::class) ->middleware(['auth', 'verified']);

Route::resource('users', UserController::class) ->middleware(['auth', 'verified']);

Route::resource('company', CompanyController::class) ->middleware(['auth', 'verified']);

Route::resource('hinbans', HinbanController::class) ->middleware(['auth', 'verified']);

Route::resource('skus', SkuController::class) ->middleware(['auth', 'verified']);




Route::resource('reports', ReportController::class) ->middleware(['auth', 'verified']);

Route::resource('comments', CommentController::class) ->middleware(['auth', 'verified']);



Route::resource('sales', SaleController::class) ->middleware(['auth', 'verified']);



Route::resource('stocks', StockController::class) ->middleware(['auth', 'verified']);






Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
