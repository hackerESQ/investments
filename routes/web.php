<?php

use App\Models\Dividend;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HoldingController;
use App\Http\Controllers\TransactionController;
use App\Interfaces\MarketData\MarketDataInterface;

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

    // portfolio resource
    Route::resource('portfolio', PortfolioController::class);

    // transaction resource
    Route::get('transaction', [TransactionController::class, 'index'])->name('transaction.index');
    Route::get('portfolio/{portfolio}/transaction/create', [TransactionController::class, 'create'])->name('transaction.create');
    Route::post('portfolio/{portfolio}/transaction/create', [TransactionController::class, 'store'])->name('transaction.store');
    Route::get('portfolio/{portfolio}/transaction/import', [TransactionController::class, 'importForm'])->name('transaction.importForm');
    Route::post('portfolio/{portfolio}/transaction/import', [TransactionController::class, 'import'])->name('transaction.import');
    Route::get('portfolio/{portfolio}/transaction/{transaction}', [TransactionController::class, 'show'])->name('transaction.show');
    Route::put('portfolio/{portfolio}/transaction/{transaction}', [TransactionController::class, 'update'])->name('transaction.update');

    // holding view
    Route::get('portfolio/{portfolio}/holding/{holding}', [HoldingController::class, 'show'])->name('holding.show');

    Route::get('test', function() {
        // return Transaction::where('symbol', 'TSLA')->first()->calculateTotalOwnedOnDate(Carbon::parse('2020-01-01'));
        // return app(MarketDataInterface::class)->dividendHistory('AAPL', Carbon::parse('2020-01-01'), now());
        return Dividend::getDividendData('HDV');
    });

});