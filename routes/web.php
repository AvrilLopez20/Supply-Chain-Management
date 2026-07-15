<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\ForecastingController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Debug Route
Route::get('/_debug-php', function () {
    return response()->json([
        'php_ini' => php_ini_loaded_file(),
        'extensions' => [
            'openssl' => extension_loaded('openssl'),
            'pdo_sqlite' => extension_loaded('pdo_sqlite'),
            'sqlite3' => extension_loaded('sqlite3'),
            'fileinfo' => extension_loaded('fileinfo'),
        ],
        'functions' => [
            'openssl_cipher_iv_length' => function_exists('openssl_cipher_iv_length'),
        ],
        'sapi' => PHP_SAPI,
        'php_version' => PHP_VERSION,
    ]);
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Dashboard Route
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Inventory Routes
Route::prefix('inventory')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('/', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/{id}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
    Route::put('/{id}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
});

// Procurement Routes
Route::prefix('procurement')->group(function () {
    Route::get('/', [ProcurementController::class, 'index'])->name('procurement.index');
    Route::get('/create', [ProcurementController::class, 'create'])->name('procurement.create');
    Route::post('/', [ProcurementController::class, 'store'])->name('procurement.store');
    Route::get('/{id}', [ProcurementController::class, 'show'])->name('procurement.show');
    Route::get('/{id}/edit', [ProcurementController::class, 'edit'])->name('procurement.edit');
    Route::put('/{id}', [ProcurementController::class, 'update'])->name('procurement.update');
    Route::delete('/{id}', [ProcurementController::class, 'destroy'])->name('procurement.destroy');
});

// Logistics Routes
Route::prefix('logistics')->group(function () {
    Route::get('/', [LogisticsController::class, 'index'])->name('logistics.index');
    Route::get('/create', [LogisticsController::class, 'create'])->name('logistics.create');
    Route::post('/', [LogisticsController::class, 'store'])->name('logistics.store');
    Route::get('/{id}', [LogisticsController::class, 'show'])->name('logistics.show');
    Route::get('/{id}/edit', [LogisticsController::class, 'edit'])->name('logistics.edit');
    Route::put('/{id}', [LogisticsController::class, 'update'])->name('logistics.update');
    Route::delete('/{id}', [LogisticsController::class, 'destroy'])->name('logistics.destroy');
});

// Forecasting Routes
Route::prefix('forecasting')->group(function () {
    Route::get('/', [ForecastingController::class, 'index'])->name('forecasting.index');
    Route::get('/analytics', [ForecastingController::class, 'analytics'])->name('forecasting.analytics');

    // Functional endpoints for forecasting page
    Route::post('/records', [ForecastingController::class, 'storeRecord'])->name('forecasting.storeRecord');
    Route::put('/records/{sale}', [ForecastingController::class, 'updateRecord'])->name('forecasting.updateRecord');
    Route::delete('/records/{sale}', [ForecastingController::class, 'destroyRecord'])->name('forecasting.destroyRecord');
    Route::post('/generate', [ForecastingController::class, 'generateForecast'])->name('forecasting.generate');
});

// Reports Routes
Route::prefix('reports')->group(function () {
    Route::get('/', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/inventory', [ReportsController::class, 'inventory'])->name('reports.inventory');
    Route::get('/procurement', [ReportsController::class, 'procurement'])->name('reports.procurement');
    Route::get('/logistics', [ReportsController::class, 'logistics'])->name('reports.logistics');
    Route::get('/export/{type}', [ReportsController::class, 'export'])->name('reports.export');
});

// JSON API Routes
Route::get('/api/inventory', [InventoryController::class, 'apiIndex']);
Route::post('/api/transfer', [InventoryController::class, 'apiTransfer']);
