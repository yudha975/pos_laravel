<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Master Data Routes
    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('products', ProductController::class);
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

    // Inventory
    Route::get('inventory', [\App\Http\Controllers\StockTransactionController::class, 'index'])->name('inventory.index');
    Route::get('inventory/create', [\App\Http\Controllers\StockTransactionController::class, 'create'])->name('inventory.create');
    Route::post('inventory', [\App\Http\Controllers\StockTransactionController::class, 'store'])->name('inventory.store');

    // POS
    Route::get('pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
    Route::post('pos', [\App\Http\Controllers\PosController::class, 'store'])->name('pos.store');
    Route::get('pos/{sale}/receipt', [\App\Http\Controllers\PosController::class, 'receipt'])->name('pos.receipt');

    // Purchasing
    Route::get('purchases', [\App\Http\Controllers\PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('purchases/create', [\App\Http\Controllers\PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('purchases', [\App\Http\Controllers\PurchaseController::class, 'store'])->name('purchases.store');
    Route::patch('purchases/{purchase}/payment', [\App\Http\Controllers\PurchaseController::class, 'updatePayment'])->name('purchases.payment');

    // Sales History
    Route::get('sales', [\App\Http\Controllers\SaleController::class, 'index'])->name('sales.index');
    Route::patch('sales/{sale}/payment', [\App\Http\Controllers\SaleController::class, 'updatePayment'])->name('sales.payment');

    // Finance & Laba Rugi
    Route::get('finance/cash', [\App\Http\Controllers\FinanceController::class, 'cashIndex'])->name('finance.cash');
    Route::post('finance/cash', [\App\Http\Controllers\FinanceController::class, 'cashStore'])->name('finance.cash.store');
    Route::get('finance/profit-loss', [\App\Http\Controllers\FinanceController::class, 'profitLoss'])->name('finance.profit_loss');

    // Reports & Analytics
    Route::get('reports/sales', [\App\Http\Controllers\ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('reports/purchases', [\App\Http\Controllers\ReportController::class, 'purchasesReport'])->name('reports.purchases');
    Route::get('reports/stocks', [\App\Http\Controllers\ReportController::class, 'stockReport'])->name('reports.stocks');

    // Settings & Users (Superadmin Only)
    Route::group(['middleware' => ['role:superadmin']], function () {
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['create', 'show', 'edit']);
        Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\SettingController::class, 'store'])->name('settings.store');
        Route::get('audit-log', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');
    });
});

require __DIR__.'/auth.php';
