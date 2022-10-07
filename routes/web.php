<?php

use App\Exports\BackupExport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DailyChangeController,
    HoldingController,
    ImportExportController,
    PortfolioController,
    TransactionController
};
use App\Interfaces\MarketData\MarketDataInterface;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/portfolio', 301);

Route::middleware(['auth:sanctum', 'verified'])->group(function() {

    // backup
    Route::post('import', [ImportExportController::class, 'import'])->name('backup.import');
    Route::view('import', 'pages.backup')->name('backup.importForm');
    Route::get('export', [ImportExportController::class, 'export'])->name('backup.export');

    // portfolios resource
    Route::resource('portfolio', PortfolioController::class);

    // dailychange
    Route::get('daily-change', [DailyChangeController::class, 'index'])->name('daily_change.index');

    // transactions resource
    Route::get('transaction', [TransactionController::class, 'index'])->name('transaction.index');
    Route::get('portfolio/{portfolio}/transaction/create', [TransactionController::class, 'create'])->name('portfolio.transaction.create');
    Route::post('portfolio/{portfolio}/transaction/create', [TransactionController::class, 'store'])->name('portfolio.transaction.store');
    // Route::get('portfolio/{portfolio}/transaction/{transaction}', [TransactionController::class, 'show'])->name('transaction.show');
    // Route::put('portfolio/{portfolio}/transaction/{transaction}', [TransactionController::class, 'update'])->name('transaction.update');

    // holdings view
    Route::get('portfolio/{portfolio}/symbol/{holding:symbol}', [HoldingController::class, 'show'])->name('holding.show');
});